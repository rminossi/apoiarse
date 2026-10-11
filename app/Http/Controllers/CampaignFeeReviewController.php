<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignFeeReviewController extends Controller
{
    public function reviewFeeMode(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approve_gateway_only,keep_standard,reject',
            'fee_review_notes' => 'nullable|string|max:2000',
            'activate' => 'nullable|boolean',
        ]);

        $data = [
            'fee_reviewed_at' => now(),
            'fee_reviewed_by' => auth()->id(),
            'fee_review_notes' => $validated['fee_review_notes'] ?? null,
        ];

        match ($validated['decision']) {
            'approve_gateway_only' => $data += [
                'fee_mode' => 'gateway_only',
                'fee_mode_request' => 'approved',
            ],
            'keep_standard' => $data += [
                'fee_mode' => 'standard',
                'fee_mode_request' => $campaign->fee_mode_request === 'pending' ? 'rejected' : $campaign->fee_mode_request,
            ],
            'reject' => $data += [
                'fee_mode' => 'standard',
                'fee_mode_request' => 'rejected',
            ],
        };

        if ($request->boolean('activate') && (int) $campaign->status !== 3) {
            $data['status'] = 1;
        }

        $campaign->update($data);

        return redirect()->route('admin.campaigns.edit', $campaign)
            ->with('message', 'Modalidade de taxas atualizada.');
    }

    public function reviewSplitPayout(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approve,reject',
            'fee_review_notes' => 'nullable|string|max:2000',
        ]);

        $campaign->update([
            'split_payout_enabled' => $validated['decision'] === 'approve',
            'split_payout_request' => $validated['decision'] === 'approve' ? 'approved' : 'rejected',
            'fee_reviewed_at' => now(),
            'fee_reviewed_by' => auth()->id(),
            'fee_review_notes' => $validated['fee_review_notes'] ?? $campaign->fee_review_notes,
        ]);

        return redirect()->route('admin.campaigns.edit', $campaign)
            ->with('message', 'Repasse automático (split) atualizado.');
    }
}
