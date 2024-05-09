<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaign as RifaRequest;
use App\Models\Donation;
use App\Models\Image;
use App\Models\ImageCampaign;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index(){
        $user = auth()->user();
        if($user->is_admin) {
            $campaigns = Campaign::all();
            return view('admin.campaigns.index')->with('campaigns', $campaigns);
        }

        $campaigns = Campaign::where('user_id', $user->id)->get();
        return view('users.campaigns.index')->with('campaigns', $campaigns);
    }

    public function show($slug){
        $campaign = Campaign::where('slug', $slug)->get();
        $user = auth()->user();

        if($user->is_admin) {
            if (!empty($campaign)) {
                return view('admin.campaigns.show')->with('campaigns', $campaign);
            } else {
                return view('admin.campaigns.index');
            }
        }

        if (!empty($campaign)) {
            return view('users.campaigns.show')->with('campaigns', $campaign);
        } else {
            return view('users.campaigns.index');
        }

    }

    public function create(){
        return view('admin.campaigns.create');
    }

    public function store(RifaRequest $request){

        $user = auth()->user();
        $request->request->add(['user_id' => $user->id]);
        $campaign = Campaign::create($request->all());
        $request->request->add(['campaign_id' => $campaign->id]);
        $campaign->setSlug();

        if($request->allFiles()) {
            foreach ($request->allFiles()['files'] as $image) {
                $ext = $image->getClientOriginalExtension();
                $filename = uniqid().'.'.$ext;
                $image->storeAs("public/campaigns/", $filename);
                Storage::delete("public/campaigns/{$campaign->image}");
                $newImage = Image::create([
                    'original_name' => $image->getClientOriginalName(),
                    'path' => "campaigns/" . $filename
                ]);
                ImageCampaign::create([
                    'image_id' => $newImage->id,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        return redirect()->route('admin.campaigns.index', [
            'campaigns' => $campaign->id
        ])->with(['message' => 'Campaign cadastrada com sucesso!']);
    }

    public function edit($id){
        $campaign = Campaign::where('id', $id)->first();
        $donations = $campaign->donations()->get();
        $user = auth()->user();
        if($user->is_admin) {
            if (!empty($campaign)) {
                return view('admin.campaigns.edit', [
                    'campaign' => $campaign,
                    'donations' => $donations
                ]);
            } else {
                return view('admin.campaigns.index');
            }
        }

        if (!empty($campaign)) {
            return view('users.campaigns.edit', [
                'campaign' => $campaign,
                'donations' => $donations
            ]);
        } else {
            return view('users.campaigns.index');
        }
    }

    public function cancelReserve() {
        $donations = Donation::where('status', 2);
        foreach ($donations as $number){
            $dataReserva = date_create($number->updated_at);
            $dataHoje = date_create();
            $resultado = date_diff($dataReserva, $dataHoje);
            if ($number->status === 2 && $resultado === 5) {
                $number->status = 1;
            }
        }
    }

    public function update(Request $request, $campaign){
        $campaign = Campaign::where('id', $campaign)->first();
        if($request->allFiles()) {
            foreach ($request->allFiles()['files'] as $image) {
                $ext = $image->getClientOriginalExtension();
                $filename = uniqid().'.'.$ext;
                $image->storeAs("public/campaigns/", $filename);
                Storage::delete("public/campaigns/{$campaign->image}");
                $newImage = Image::create([
                    'original_name' => $image->getClientOriginalName(),
                    'path' => "campaigns/" . $filename
                ]);
                ImageCampaign::create([
                    'image_id' => $newImage->id,
                    'campaign_id' => $campaign->id,
                ]);
            }
        }

        try {
            $request->request->set('goal', str_replace(',', '.', str_replace('.', '', $request->request->get('goal'))));
            $campaign->update($request->all());
        } catch (\Exception $e) {
            return redirect()->route('usuario.campanhas.edit', [
                'campanha' => $campaign->id
            ])->with(['error' => 'Erro ao atualizar campanha.']);
        }

        $campaign->setSlug();

        return redirect()->route('usuario.campanhas.edit', [
            'campanha' => $campaign->id
        ])->with(['message' => 'Campanha atualizada com sucesso!']);
    }

    public function imageSetCover(Request $request)
    {
        $imageSetCover = ImageCampaign::where('id', $request->image)->first();
        $allImage = ImageCampaign::where('campaign_id', $imageSetCover->campaign_id)->get();

        foreach ($allImage as $image) {
            $image->cover = false;
            $image->save();
        }

        $imageSetCover->cover = true;
        $imageSetCover->save();

        $json = [
            'success' => true,
        ];

        return response()->json($json);
    }

    public function removeImage(Request $request){
        $imageDelete = ImageCampaign::where('id', $request->image)->first();
        Storage::delete($imageDelete->path);
        $imageDelete->delete();
        $json = [
            'success' => true
        ];
        return response()->json($json);
    }

    public function destroy(Campaign $campaign){
        $campaign->delete();
        return redirect()-> route('admin.campaigns.index')->with(['message' => 'Campaign deletada com sucesso!']);

    }
}
