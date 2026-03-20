<?php

namespace App\Exports;

use App\Models\ProxyLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProxyLogsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection(): Collection
    {
        return ProxyLog::with('site')
            ->latest()
            ->get()
            ->map(fn ($log) => [
                'id'         => $log->id,
                'dominio'    => $log->site?->domain ?? '—',
                'accion'     => $log->action,
                'razon'      => $log->reason,
                'estado'     => $log->status,
                'mensaje'    => $log->message ?? '—',
                'fecha'      => $log->created_at->format('d/m/Y H:i:s'),
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Dominio', 'Acción', 'Razón', 'Estado', 'Mensaje', 'Fecha'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0D1520']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}