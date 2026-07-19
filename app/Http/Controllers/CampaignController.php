<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaign as CampaignRequest;
use App\Models\Campaign;
use App\Models\Image;
use App\Models\ImageCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Campaign::with(['category', 'user']);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $campaigns = $query->orderByDesc('created_at')->get();
        $categories = \App\Models\Category::orderBy('sort_order')->get();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        if ($user->is_admin) {
            return view('admin.campaigns.index', compact('campaigns', 'categories', 'users'));
        }

        $campaigns = Campaign::where('user_id', $user->id)->orderByDesc('created_at')->get();

        return view('users.campaigns.index')->with('campaigns', $campaigns);
    }

    public function show($slug)
    {
        $campaign = Campaign::where('slug', $slug)->get();
        $user = auth()->user();

        if ($user->is_admin) {
            if (! empty($campaign)) {
                return view('admin.campaigns.show')->with('campaigns', $campaign);
            } else {
                return view('admin.campaigns.index');
            }
        }

        if (! empty($campaign)) {
            return view('users.campaigns.show')->with('campaigns', $campaign);
        } else {
            return view('users.campaigns.index');
        }

    }

    public function create()
    {
        $user = auth()->user();
        $categories = \App\Models\Category::active()->get();

        if ($user->is_admin) {
            return view('admin.campaigns.create', compact('categories'));
        }

        return view('users.campaigns.create', compact('categories'));
    }

    public function store(CampaignRequest $request)
    {

        $user = auth()->user();
        $request->request->add(['user_id' => $user->id]);
        if ($request->request->get('goal') != null) {
            $request->request->set('goal', str_replace(',', '.', str_replace('.', '', $request->request->get('goal'))));
        }
        $campaign = Campaign::create($request->all());
        $request->request->add(['campaign_id' => $campaign->id]);
        $campaign->setSlug();

        if ($request->allFiles()) {
            $request->validate([
                'files.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            foreach ($request->allFiles()['files'] as $image) {
                $ext = $image->getClientOriginalExtension();
                $filename = uniqid().'.'.$ext;
                $image->storeAs('public/campaigns/', $filename);
                Storage::delete("public/campaigns/{$campaign->image}");
                $newImage = Image::create([
                    'original_name' => $image->getClientOriginalName(),
                    'path' => 'campaigns/'.$filename,
                ]);
                ImageCampaign::create([
                    'image_id' => $newImage->id,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        if ($user->is_admin) {
            return redirect()->route('admin.campaigns.index', [
                'campaigns' => $campaign->id,
            ])->with(['message' => 'Campaign cadastrada com sucesso!']);
        }

        return redirect()->route('usuario.campanhas.index', [
            'campaigns' => $campaign->id,
        ])->with(['message' => 'Campaign cadastrada com sucesso!']);
    }

    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        $this->authorizeCampaignAccess($campaign);
        $donations = $campaign->donations()->get();
        $user = auth()->user();
        $categories = \App\Models\Category::active()->get();
        $updates = $campaign->updates()->with('user')->get();

        if ($user->is_admin) {
            return view('admin.campaigns.edit', [
                'campaign' => $campaign,
                'donations' => $donations,
                'categories' => $categories,
                'updates' => $updates,
            ]);
        }

        return view('users.campaigns.edit', [
            'campaign' => $campaign,
            'donations' => $donations,
            'categories' => $categories,
            'updates' => $updates,
        ]);
    }

    public function update(Request $request, $campaign)
    {
        $campaign = Campaign::findOrFail($campaign);
        $this->authorizeCampaignAccess($campaign);
        $user = auth()->user();

        if ($request->allFiles()) {
            $request->validate([
                'files.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            foreach ($request->allFiles()['files'] as $image) {
                $ext = $image->getClientOriginalExtension();
                $filename = uniqid().'.'.$ext;
                $image->storeAs('public/campaigns/', $filename);
                Storage::delete("public/campaigns/{$campaign->image}");
                $newImage = Image::create([
                    'original_name' => $image->getClientOriginalName(),
                    'path' => 'campaigns/'.$filename,
                ]);
                ImageCampaign::create([
                    'image_id' => $newImage->id,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        try {
            if ($request->request->get('goal') != null) {
                $request->request->set('goal', str_replace(',', '.', str_replace('.', '', $request->request->get('goal'))));
            }

            $campaign->update($request->all());
        } catch (\Exception $e) {
            if ($user->is_admin) {
                return redirect()->route('admin.campaigns.edit', [
                    'campanha' => $campaign->id,
                ])->with(['error' => 'Erro ao atualizar campanha.']);
            }

            return redirect()->route('usuario.campanhas.edit', [
                'campanha' => $campaign->id,
            ])->with(['error' => 'Erro ao atualizar campanha.']);
        }

        $campaign->setSlug();

        if ($user->is_admin) {
            return redirect()->route('admin.campaigns.index', [
                'campaigns' => $campaign->id,
            ])->with(['message' => 'Campanha atualizada com sucesso!']);
        }

        return redirect()->route('usuario.campanhas.index', [
            'campaigns' => $campaign->id,
        ])->with(['message' => 'Campanha atualizada com sucesso!']);
    }

    public function destroy($campaign)
    {
        $campaign = Campaign::findOrFail($campaign);
        $this->authorizeCampaignAccess($campaign);
        $campaign->update([
            'status' => $campaign->status == 1 ? 2 : 1,
        ]);
        $user = auth()->user();

        if ($user->is_admin) {
            return redirect()->route('admin.campaigns.index')->with(['message' => 'Campaign deletada com sucesso!']);
        }

        return redirect()->route('usuario.campanhas.index')->with(['message' => 'Campaign deletada com sucesso!']);

    }

    public function imageSetCover(Request $request)
    {
        $request->validate(['image' => 'required|integer']);

        $imageSetCover = ImageCampaign::findOrFail($request->image);
        $this->authorizeCampaignAccess(Campaign::findOrFail($imageSetCover->campaign_id));
        $allImage = ImageCampaign::where('campaign_id', $imageSetCover->campaign_id)->get();

        foreach ($allImage as $image) {
            $image->cover = false;
            $image->save();
        }

        $imageSetCover->cover = true;
        $imageSetCover->save();

        return response()->json(['success' => true]);
    }

    public function removeImage(Request $request)
    {
        $request->validate(['image' => 'required|integer']);

        $imageDelete = ImageCampaign::findOrFail($request->image);
        $this->authorizeCampaignAccess(Campaign::findOrFail($imageDelete->campaign_id));
        Storage::delete($imageDelete->image->path);
        $imageDelete->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeCampaignAccess(Campaign $campaign): void
    {
        $user = auth()->user();

        if (! $user->is_admin && (int) $campaign->user_id !== (int) $user->id) {
            abort(403, 'Acesso não autorizado a esta campanha.');
        }
    }
}
