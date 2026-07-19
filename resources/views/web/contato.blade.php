@extends('layouts.public')

@section('content')
<div class="page-container py-8 sm:py-10">
    <div class="mx-auto w-full max-w-xl">
    <h1 class="section-title">Contato</h1>
    <p class="section-subtitle">Envie sua mensagem e responderemos o mais breve possível.</p>

    <form id="contact-form" class="mt-8 space-y-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200">
        @csrf
        <div>
            <label for="fullname" class="block text-sm font-medium text-stone-700">Nome</label>
            <input type="text" id="fullname" name="fullname" required class="input-field mt-1">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-stone-700">E-mail</label>
            <input type="email" id="email" name="email" required class="input-field mt-1">
        </div>
        <div>
            <label for="message" class="block text-sm font-medium text-stone-700">Mensagem</label>
            <textarea id="message" name="message" rows="5" required class="input-field mt-1"></textarea>
        </div>
        <div id="contact-feedback" class="hidden rounded-lg p-3 text-sm"></div>
        <button type="submit" class="btn-primary w-full">Enviar mensagem</button>
    </form>

    @if(!empty($site))
    <div class="mt-8 text-center text-sm text-stone-500">
        @if($site->email)<p>E-mail: {{ $site->email }}</p>@endif
        @if($site->phone)<p>Telefone: {{ $site->phone }}</p>@endif
    </div>
    @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('contact-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const feedback = document.getElementById('contact-feedback');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const data = new FormData(form);

    try {
        const res = await fetch('{{ route('enviar-contato') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: data,
        });
        const json = await res.json();
        feedback.classList.remove('hidden', 'bg-red-50', 'text-red-800', 'bg-brand-50', 'text-brand-800');
        if (res.ok) {
            feedback.classList.add('bg-brand-50', 'text-brand-800');
            form.reset();
        } else {
            feedback.classList.add('bg-red-50', 'text-red-800');
        }
        feedback.textContent = json.message?.replace(/<[^>]*>/g, '') || 'Erro ao enviar.';
    } catch {
        feedback.classList.remove('hidden');
        feedback.classList.add('bg-red-50', 'text-red-800');
        feedback.textContent = 'Erro ao enviar mensagem.';
    }
});
</script>
@endpush
