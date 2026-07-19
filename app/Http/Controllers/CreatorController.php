<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CreatorController extends Controller
{
    public function show(string $slug): View
    {
        $creator = User::where('public_slug', $slug)->firstOrFail();

        $activeCampaigns = $creator->campaigns()
            ->with(['category', 'donations'])
            ->active()
            ->orderByDesc('created_at')
            ->get();

        $finishedCampaigns = $creator->campaigns()
            ->with(['category', 'donations'])
            ->finished()
            ->orderByDesc('created_at')
            ->get();

        $totalRaised = Donation::whereIn('campaign_id', $creator->campaigns()->pluck('id'))
            ->where('status', 3)
            ->sum('amount');

        return view('web.creator', [
            'creator' => $creator,
            'activeCampaigns' => $activeCampaigns,
            'finishedCampaigns' => $finishedCampaigns,
            'totalRaised' => $totalRaised,
        ]);
    }
}
