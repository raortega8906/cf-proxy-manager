<?php

namespace App\Console\Commands;

use App\Mail\ScheduleAutomaticLaLiga;
use App\Models\ProxySchedule;
use App\Models\ProxySite;
use App\Services\LaligaService;
use App\Services\ProxyScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AddAutomaticScheduleMatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-automatic-schedule-match-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(LaligaService $laLiga, ProxyScheduleService $proxySchedule)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando fechas de partidos para la creación del schedule...');

        $schedule_ids = ProxySite::where('affected_by_laliga', true)->pluck('id')->toArray();
        $dateFrom = Carbon::today();
        $dateTo = Carbon::today();

        // Pruebas para la Hackatón:
        // $dateFrom = Carbon::parse('2026-03-21');
        // $dateTo = Carbon::parse('2026-03-21');

//         $dateFrom = Carbon::parse('2026-04-17');
//         $dateTo = Carbon::parse('2026-04-17');

        $matches = $laLiga->getMatches($dateFrom, $dateTo);

        if (empty($matches)) {
            $this->line('  → Sin schedule automatico para crear de partidos.');
            Log::error('  → Sin schedule automatico para crear de partidos.');

            return;
        }

        $matchesFormatted = collect($matches)->map(function ($match) {
            return [
                'home'     => $match['homeTeam']['name'],
                'away'     => $match['awayTeam']['name'],
                'datetime' => Carbon::parse($match['utcDate'])
                                ->timezone('Europe/Madrid')
                                ->format('Y-m-d H:i'),
            ];
        })->toArray();

        $firstMatch  = Carbon::parse($matchesFormatted[0]['datetime'], 'Europe/Madrid');
        $lastMatch  = Carbon::parse(end($matchesFormatted)['datetime'], 'Europe/Madrid');
        $date       = $firstMatch->format('d/m/Y');

        $email = config('mail.email');
        $domains = ProxySite::where('affected_by_laliga', true)->pluck('domain')->toArray();

        // Un solo partido
        if (count($matchesFormatted) === 1) {

            $dataSchedule = [
                'description' => "Schedule automático por partidos de liga el día {$date}",
                'disable_at'  => $firstMatch->clone()->subHour(),
                'enable_at'   => $lastMatch->clone()->addHours(3),
            ];

            // Verificar si ya existe un schedule con esa descripción
            $exists = ProxySchedule::where('description', $dataSchedule['description'])->exists();

            if ($exists) {
                $this->line('  → Ya existe un schedule para hoy, se omite la creación.');
                Log::error('  → Ya existe un schedule para hoy, se omite la creación.');

                return;
            }

            $proxySchedule->writeAutomaticSchedule(
                'laliga_match',
                $dataSchedule['description'],
                $dataSchedule['disable_at'],
                $dataSchedule['enable_at'],
                'pending',
                $schedule_ids
            );

            $this->line("  → Schedule creado para: {$matchesFormatted['home']} vs {$matchesFormatted['away']} ({$matchesFormatted['datetime']})");

        } else {  // Varios partidos

            // Agrupar partidos por horario
            $matchesByTime = collect($matchesFormatted)->groupBy('datetime');
            $groups        = $matchesByTime->values(); // re-indexar
            $totalGroups   = $groups->count();

            foreach ($groups as $index => $groupMatches) {
                $matchDatetime = Carbon::parse($groupMatches->first()['datetime'], 'Europe/Madrid');
                $groupNumber   = $index + 1;
                $isLast        = $index === $totalGroups - 1;

                // Si hay varios partidos en el mismo horario, listarlos todos en la descripción
                $matchNames = $groupMatches->map(fn($m) => "{$m['home']} vs {$m['away']}")->implode(' | ');

                $dataSchedule = [
                    'description' => "Schedule automático por partidos de liga el día {$date} - Partido {$groupNumber} ({$matchNames})",
                    'disable_at'  => $matchDatetime->clone(),
                    'enable_at'   => $matchDatetime->clone()->addHours($isLast ? 3 : 2),
                ];

                $exists = ProxySchedule::where('description', $dataSchedule['description'])->exists();

                if ($exists) {
                    $this->line("  → Ya existe el schedule para el grupo {$groupNumber} ({$matchDatetime->format('H:i')}), se omite.");
                    Log::info("  → Ya existe el schedule para el grupo {$groupNumber}, se omite.");
                    continue;
                }

                $proxySchedule->writeAutomaticSchedule(
                    'laliga_match',
                    $dataSchedule['description'],
                    $dataSchedule['disable_at'],
                    $dataSchedule['enable_at'],
                    'pending',
                    $schedule_ids
                );

                $matchLog = $groupMatches->map(fn($m) => "{$m['home']} vs {$m['away']} ({$m['datetime']})")->implode(', ');
                $this->line("  → Schedule creado para: {$matchLog}");
            }
        }

        Mail::to($email)->send(new ScheduleAutomaticLaLiga($email, $domains, $matchesFormatted));

        $this->info('Listo.');

        return self::SUCCESS;
    }
}
