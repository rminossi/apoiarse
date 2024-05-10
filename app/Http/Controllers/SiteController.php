<?php

namespace App\Http\Controllers;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function edit($id){
        dd(Auth::user());
        $site = Site::where('id', '1')->first();
        return view('admin.site.edit', [
            'site' => $site
        ]);

    }
    public function update(Request $request, Site $site){
        if(!$site->update($request->all())) {
            return redirect()->back()->withInput()->withErrors();
        }
        return redirect()->route('admin.site.edit', [
        'site' => $site
        ])->with(['message' => 'Dados atualizads com sucesso!']);
    }
}
