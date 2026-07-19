<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function edit(Site $site)
    {
        return view('admin.site.edit', [
            'site' => $site,
            'faq' => SiteContent::get('faq', []),
            'testimonials' => SiteContent::get('testimonials', []),
            'howItWorks' => SiteContent::get('how_it_works', []),
        ]);
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'whatsapp_group' => 'nullable|url|max:500',
            'bank' => 'required|string|max:100',
            'type' => 'nullable|string|max:50',
            'acc' => 'required|string|max:30',
            'ag' => 'nullable|string|max:20',
            'fullName' => 'required|string|max:255',
            'cpf' => 'required|cpf',
            'faq_json' => 'nullable|string',
            'testimonials_json' => 'nullable|string',
            'how_it_works_json' => 'nullable|string',
        ]);

        if (! $site->update(collect($validated)->except(['faq_json', 'testimonials_json', 'how_it_works_json'])->toArray())) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Erro ao atualizar dados do site.']);
        }

        $this->saveJsonContent('faq', $request->faq_json);
        $this->saveJsonContent('testimonials', $request->testimonials_json);
        $this->saveJsonContent('how_it_works', $request->how_it_works_json);

        return redirect()->route('admin.site.edit', ['site' => $site->id])
            ->with(['message' => 'Dados atualizados com sucesso!']);
    }

    private function saveJsonContent(string $key, ?string $json): void
    {
        if (empty($json)) {
            return;
        }

        $decoded = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            SiteContent::updateOrCreate(['key' => $key], ['value' => $decoded]);
        }
    }
}
