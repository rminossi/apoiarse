<footer class="w-full border-t border-stone-200 bg-stone-900 text-stone-300">
    <div class="page-container py-10 sm:py-12">
        <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-3">
            <div class="sm:col-span-2 md:col-span-1">
                <img src="{{ asset('assets/frontend/images/apoiarse_logo.png') }}" alt="Apoiar-se" class="h-8 brightness-0 invert">
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-stone-400">Plataforma de crowdfunding para causas que importam. Arrecade com segurança via PIX ou cartão.</p>
            </div>
            <div>
                <h4 class="font-semibold text-white">Links</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('web.campaigns') }}" class="hover:text-white">Campanhas</a></li>
                    <li><a href="{{ route('web.sobre') }}" class="hover:text-white">Sobre</a></li>
                    <li><a href="{{ route('web.contato') }}" class="hover:text-white">Contato</a></li>
                    <li><a href="{{ route('usuario.campanhas.create') }}" class="hover:text-white">Criar campanha</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white">Redes</h4>
                <div class="mt-4 flex flex-wrap gap-4">
                    <a href="https://api.whatsapp.com/send?phone=5551980436739" target="_blank" rel="noopener" class="hover:text-white">WhatsApp</a>
                    <a href="https://www.instagram.com/apoiarse.online" target="_blank" rel="noopener" class="hover:text-white">Instagram</a>
                </div>
            </div>
        </div>
        <div class="mt-8 border-t border-stone-800 pt-6 text-center text-sm text-stone-500 sm:pt-8">
            &copy; {{ date('Y') }} Apoiar-se. Todos os direitos reservados.
        </div>
    </div>
</footer>
