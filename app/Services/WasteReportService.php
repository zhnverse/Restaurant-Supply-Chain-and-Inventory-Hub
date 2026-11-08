<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\WasteReport;
use Illuminate\Support\Facades\DB;

class WasteReportService
{
    /**
     * Create a new waste report.
     */
    public function createReport(array $data, int $reportedBy): WasteReport
    {
        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        // Auto-calculate estimated loss if not provided
        $estimatedLoss = isset($data['estimated_loss']) && $data['estimated_loss'] > 0
            ? $data['estimated_loss']
            : round((float)$data['quantity'] * (float)$ingredient->cost_per_unit, 2);

        return WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $data['ingredient_id'],
            'batch_id'       => $data['batch_id'] ?? null,
            'reported_by'    => $reportedBy,
            'quantity'       => $data['quantity'],
            'reason'         => $data['reason'],
            'description'    => $data['description'] ?? null,
            'estimated_loss' => $estimatedLoss,
            'status'         => 'pending',
        ]);
    }

    /**
     * Approve waste report: deduct quantity from ingredient stock.
     */
    public function approve(WasteReport $report, int $approverId, ?string $notes = null): void
    {
        if (!$report->isPending()) {
            throw new \RuntimeException('Only pending waste reports can be approved.');
        }

        DB::transaction(function () use ($report, $approverId, $notes) {
            $ingredient = $report->ingredient;

            // Deduct waste quantity from current stock
            $ingredient->current_stock = max(0, $ingredient->current_stock - $report->quantity);
            $ingredient->save();

            // Also deduct from batch if specified
            if ($report->batch_id && $report->batch) {
                $batch = $report->batch;
                $batch->remaining_quantity = max(0, $batch->remaining_quantity - $report->quantity);
                $batch->save();
            }

            $report->update([
                'status'        => 'approved',
                'approved_by'   => $approverId,
                'approved_at'   => now(),
                'manager_notes' => $notes,
            ]);
        });
    }

    /**
     * Reject waste report without touching stock.
     */
    public function reject(WasteReport $report, int $approverId, ?string $notes = null): void
    {
        if (!$report->isPending()) {
            throw new \RuntimeException('Only pending waste reports can be rejected.');
        }

        $report->update([
            'status'        => 'rejected',
            'approved_by'   => $approverId,
            'approved_at'   => now(),
            'manager_notes' => $notes,
        ]);
    }

    /**
     * Get summary stats for dashboard.
     */
    public function getStats(): array
    {
        return [
            'total'         => WasteReport::count(),
            'pending'       => WasteReport::where('status', 'pending')->count(),
            'approved'      => WasteReport::where('status', 'approved')->count(),
            'rejected'      => WasteReport::where('status', 'rejected')->count(),
            'total_loss'    => WasteReport::where('status', 'approved')->sum('estimated_loss'),
        ];
    }
}
