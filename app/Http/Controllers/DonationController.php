<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $donation = Donation::where('id', $id)->first();
        $campaign_id = $donation->campaign_id;
        $status = $request->status;
        if($status === 1) {
            $request->request->add(['user_id' => null]);
        }
        $request->request->add(['status' => $status]);
        if(!$donation->update($request->all())) {
            return redirect()->back()->withInput()->withErrors();
        }
        return redirect()->route('admin.campaigns.edit', [
            'campaign' => $campaign_id,
            'donations' => true
        ])->with(['message' => 'Campaign atualizada com sucesso!']);
    }

    public function myDonations()
    {
        $user = auth()->user();
        $donations = $user->donations()->paginate();
        return view('users.donations.index', [
            'donations' => $donations
        ]);
    }
}
