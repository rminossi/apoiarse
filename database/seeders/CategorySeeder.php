<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\SiteContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Saúde', 'slug' => 'saude', 'icon' => 'heart', 'color' => '#ef4444', 'sort_order' => 1],
            ['name' => 'Educação', 'slug' => 'educacao', 'icon' => 'book', 'color' => '#3b82f6', 'sort_order' => 2],
            ['name' => 'Emergência', 'slug' => 'emergencia', 'icon' => 'alert', 'color' => '#f97316', 'sort_order' => 3],
            ['name' => 'Animais', 'slug' => 'animais', 'icon' => 'paw', 'color' => '#a855f7', 'sort_order' => 4],
            ['name' => 'Comunidade', 'slug' => 'comunidade', 'icon' => 'users', 'color' => '#059669', 'sort_order' => 5],
            ['name' => 'Projetos', 'slug' => 'projetos', 'icon' => 'rocket', 'color' => '#6366f1', 'sort_order' => 6],
            ['name' => 'Outros', 'slug' => 'outros', 'icon' => 'star', 'color' => '#78716c', 'sort_order' => 7],
        ];

        $typeMap = [
            'sick' => 'saude',
            'Saúde' => 'saude',
            'residential-accident' => 'emergencia',
            'public-calamity' => 'emergencia',
            'Educação' => 'educacao',
            'Emergência' => 'emergencia',
            'Outros' => 'outros',
            'other' => 'outros',
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $defaultCategory = Category::where('slug', 'outros')->first();

        Campaign::whereNull('category_id')->each(function (Campaign $campaign) use ($typeMap, $defaultCategory) {
            $slug = $typeMap[$campaign->type] ?? 'outros';
            $category = Category::where('slug', $slug)->first() ?? $defaultCategory;
            if ($category) {
                $campaign->update(['category_id' => $category->id]);
            }
        });

        SiteContent::updateOrCreate(
            ['key' => 'faq'],
            ['value' => [
                ['question' => 'Como funciona o Apoiar-se?', 'answer' => 'Você cria uma campanha, compartilha com sua rede e recebe doações via PIX ou cartão de forma segura.'],
                ['question' => 'Quanto tempo leva para receber?', 'answer' => 'Doações via PIX são confirmadas em segundos. Cartão pode levar alguns minutos para confirmação.'],
                ['question' => 'A plataforma cobra taxas?', 'answer' => 'Consulte nossos termos de uso para informações sobre taxas de processamento de pagamento.'],
            ]]
        );

        SiteContent::updateOrCreate(
            ['key' => 'testimonials'],
            ['value' => [
                ['name' => 'Maria S.', 'text' => 'Consegui arrecadar para o tratamento do meu filho em poucos dias. Plataforma simples e confiável!'],
                ['name' => 'João P.', 'text' => 'Criei uma campanha para a comunidade e o compartilhamento foi muito fácil.'],
            ]]
        );

        SiteContent::updateOrCreate(
            ['key' => 'how_it_works'],
            ['value' => [
                ['step' => 1, 'title' => 'Crie sua campanha', 'description' => 'Conte sua história, defina a meta e adicione fotos.'],
                ['step' => 2, 'title' => 'Compartilhe', 'description' => 'Envie para amigos, família e redes sociais.'],
                ['step' => 3, 'title' => 'Arrecade', 'description' => 'Receba doações via PIX ou cartão com segurança.'],
            ]]
        );
    }
}
