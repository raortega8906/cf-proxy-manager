<?php

namespace App\Console\Commands;

use App\Models\ProxySite;
use App\Services\LaligaService;
use App\Services\ProxyScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Procesando schedules...');

        $schedule_ids = ProxySite::where('affected_by_laliga', true)->pluck('id')->toArray();
        $dateFrom = Carbon::today();
        $dateTo = Carbon::today();

        // Pruebas:
        // $dateFrom = Carbon::parse('2026-03-21');
        // $dateTo = Carbon::parse('2026-03-21');

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

        $dataSchedule = [
            'description' => "Schedule automático por partidos de liga el día {$date}",
            'disable_at'  => $firstMatch->clone()->subHour(),
            'enable_at'   => $lastMatch->clone()->addHours(3),
        ];

        $proxySchedule->writeAutomaticSchedule(
            'laliga_match', 
            $dataSchedule['description'],
            $dataSchedule['disable_at'],
            $dataSchedule['enable_at'],
            'pending',
            $schedule_ids
        );

        $this->info('Listo.');

        return self::SUCCESS;
    }
}
