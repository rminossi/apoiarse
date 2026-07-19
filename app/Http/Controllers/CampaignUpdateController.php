<?php

namespace App\Http\Controllers;

use App\Models\CampaignUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignUpdateController extends Controller
{
    public function store(Request $request, int $campaignId): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_pinned' => 'boolean',
        ]);

        $user = auth()->user();
        $campaign = \App\Models\Campaign::findOrFail($campaignId);

        if (! $user->is_admin && $campaign->user_id !== $user->id) {
            abort(403);
        }

        CampaignUpdate::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        $route = $user->is_admin ? 'admin.campaigns.edit' : 'usuario.campanhas.edit';

        return redirect()->route($route, ['campanha' => $campaign->id, 'updates' => true])
            ->with('message', 'Atualização publicada!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $update = CampaignUpdate::findOrFail($id);
        $user = auth()->user();
        $campaign = $update->campaign;

        if (! $user->is_admin && $campaign->user_id !== $user->id) {
            abort(403);
        }

        $update->delete();

        $route = $user->is_admin ? 'admin.campaigns.edit' : 'usuario.campanhas.edit';

        return redirect()->route($route, ['campanha' => $campaign->id, 'updates' => true])
            ->with('message', 'Atualização removida.');
    }
}
