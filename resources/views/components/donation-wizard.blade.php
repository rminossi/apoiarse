@props(['campaign'])

@php
    $loginUrl = route('sessao.login', ['redirect' => route('web.campaign', $campaign->slug)]);
@endphp

<div x-data="donationWizard({{ $campaign->id }}, {{ Auth::check() ? 'true' : 'false' }}, '{{ $loginUrl }}')"
     class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200">
    @if($campaign->status != 1)
        <p class="text-center text-stone-600">Esta campanha não está mais recebendo doações.</p>
    @else
        {{-- Step indicators --}}
        <div class="mb-6 flex items-center justify-between text-xs font-medium text-stone-500">
            <span :class="step >= 1 ? 'text-brand-600' : ''">1. Valor</span>
            <span :class="step >= 2 ? 'text-brand-600' : ''">2. Método</span>
            <span :class="step >= 3 ? 'text-brand-600' : ''">3. Confirmação</span>
        </div>

        {{-- Step 1: Amount --}}
        <div x-show="step === 1" x-cloak>
            <h3 class="text-lg font-semibold text-stone-900">Quanto deseja apoiar?</h3>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach([25, 50, 100, 200] as $preset)
                    <button type="button" @click="setAmount({{ $preset }})"
                            class="rounded-lg border border-stone-200 py-3 text-sm font-semibold hover:border-brand-500 hover:bg-brand-50"
                            :class="amount === {{ $preset }} ? 'border-brand-600 bg-brand-50 text-brand-700' : ''">
                        R$ {{ $preset }}
                    </button>
                @endforeach
            </div>
            <div class="mt-4">
                <label class="text-sm font-medium text-stone-700">Outro valor</label>
                <input type="text" x-model="amountDisplay" @input="parseAmount"
                       class="input-field mt-1" placeholder="R$ 0,00">
            </div>
            <button type="button" @click="nextStep()" class="btn-primary mt-6 w-full">Continuar</button>
        </div>

        {{-- Step 2: Method --}}
        <div x-show="step === 2" x-cloak>
            <h3 class="text-lg font-semibold text-stone-900">Como deseja pagar?</h3>
            <template x-if="!loggedIn">
                <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
                    <a :href="loginUrl" class="font-semibold underline">Entre na sua conta</a> para continuar a doação.
                </div>
            </template>
            <div class="mt-4 space-y-3">
                <button type="button" @click="selectMethod('pix')" :disabled="!loggedIn"
                        class="flex w-full items-center gap-3 rounded-lg border p-4 text-left hover:border-brand-500 disabled:opacity-50"
                        :class="method === 'pix' ? 'border-brand-600 bg-brand-50' : 'border-stone-200'">
                    <span class="text-2xl">⚡</span>
                    <div>
                        <p class="font-semibold">PIX</p>
                        <p class="text-xs text-stone-500">Confirmação em segundos</p>
                    </div>
                </button>
                <button type="button" @click="selectMethod('card')" :disabled="!loggedIn"
                        class="flex w-full items-center gap-3 rounded-lg border p-4 text-left hover:border-brand-500 disabled:opacity-50"
                        :class="method === 'card' ? 'border-brand-600 bg-brand-50' : 'border-stone-200'">
                    <span class="text-2xl">💳</span>
                    <div>
                        <p class="font-semibold">Cartão de crédito</p>
                        <p class="text-xs text-stone-500">Visa, Mastercard, Elo</p>
                    </div>
                </button>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="button" @click="step = 1" class="btn-secondary flex-1">Voltar</button>
                <button type="button" @click="processPayment()" :disabled="!method || loading"
                        class="btn-primary flex-1">
                    <span x-show="!loading">Confirmar</span>
                    <span x-show="loading">Processando...</span>
                </button>
            </div>
        </div>

        {{-- Step 3: Confirmation --}}
        <div x-show="step === 3" x-cloak>
            <template x-if="method === 'pix'">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-stone-900">Pague com PIX</h3>
                    <p class="mt-2 text-sm text-stone-500">Escaneie o QR Code ou copie o código</p>
                    <img :src="pixImage" alt="QR Code PIX" class="mx-auto mt-4 max-w-[240px] rounded-lg">
                    <button type="button" @click="copyPix()" class="btn-primary mt-4 w-full">Copiar código PIX</button>
                </div>
            </template>
            <template x-if="method === 'card'">
                <div>
                    <div x-show="cardStatus === 'pending'" id="card-form-container">
                        <h3 class="text-lg font-semibold text-stone-900">Dados do cartão</h3>
                        <div class="card-js mt-4">
                            <input class="card-number w-full rounded border p-2" name="card-number" placeholder="Número do Cartão" required>
                            <input class="name w-full rounded border p-2 mt-2" name="card-holders-name" placeholder="Nome no cartão" required>
                            <input class="expiry-month" name="expiry-month" required>
                            <input class="expiry-year" name="expiry-year" required>
                            <input class="cvc" name="cvc" required>
                        </div>
                        <div class="mt-4 space-y-2">
                            <input class="input-field" name="cardName" placeholder="Nome completo" required>
                            <input class="input-field" name="cardEmail" placeholder="E-mail" required>
                            <input class="input-field" name="cardCpf" placeholder="CPF" required>
                            <input class="input-field" name="cardPhone" placeholder="Celular" required>
                            <input class="input-field" name="cardAddressComplement" placeholder="Endereço" required>
                            <input class="input-field" name="cardAddressNumber" placeholder="Número" required>
                            <input class="input-field" name="cardPostalCode" placeholder="CEP" required>
                        </div>
                        <button type="button" @click="payWithCard()" class="btn-primary mt-4 w-full">Pagar</button>
                    </div>
                    <div x-show="cardStatus === 'confirmed'" class="rounded-lg bg-brand-50 p-4 text-center text-brand-800">
                        Pagamento aprovado! Obrigado pelo apoio.
                    </div>
                    <div x-show="cardStatus === 'failed'" class="rounded-lg bg-red-50 p-4 text-center text-red-800">
                        Pagamento não aprovado. Tente novamente.
                    </div>
                </div>
            </template>
        </div>

        <p class="mt-4 flex items-center gap-2 text-xs text-stone-500">
            <svg class="h-4 w-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Pagamento seguro via PIX ou cartão
        </p>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('donationWizard', (campaignId, loggedIn, loginUrl) => ({
        step: 1,
        amount: 50,
        amountDisplay: 'R$ 50,00',
        method: null,
        loading: false,
        loggedIn,
        loginUrl,
        pixImage: '',
        pixCode: '',
        cardStatus: 'pending',

        setAmount(val) {
            this.amount = val;
            this.amountDisplay = 'R$ ' + val.toFixed(2).replace('.', ',');
        },

        parseAmount() {
            let v = this.amountDisplay.replace(/\D/g, '');
            if (!v) { this.amount = 0; return; }
            this.amount = parseInt(v, 10) / 100;
        },

        nextStep() {
            if (this.amount <= 0) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Informe um valor válido.', type: 'error' } }));
                return;
            }
            this.step = 2;
        },

        selectMethod(m) {
            if (!this.loggedIn) return;
            this.method = m;
        },

        formatAmountForApi() {
            return this.amount.toFixed(2).replace('.', ',');
        },

        async processPayment() {
            if (!this.method) return;
            this.loading = true;
            const token = document.querySelector('meta[name="csrf-token"]').content;

            if (this.method === 'pix') {
                try {
                    const res = await fetch('{{ route('web.getPixQrCode') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify({ campaign_id: campaignId, amount: this.formatAmountForApi() }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Erro ao gerar PIX');
                    this.pixImage = 'data:image/png;base64,' + data.qrCode.encodedImage;
                    this.pixCode = data.qrCode.qrCode;
                    this.step = 3;
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: e.message, type: 'error' } }));
                }
            } else {
                this.step = 3;
            }
            this.loading = false;
        },

        copyPix() {
            copyToClipboard(this.pixCode);
        },

        async payWithCard() {
            this.loading = true;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const form = document.getElementById('card-form-container');
            const data = {
                campaign_id: campaignId,
                amount: this.formatAmountForApi(),
                cardNumber: form.querySelector('[name=card-number]').value,
                cardHoldersName: form.querySelector('[name=card-holders-name]').value,
                cardMonth: form.querySelector('[name=expiry-month]').value,
                cardYear: form.querySelector('[name=expiry-year]').value,
                cardCvv: form.querySelector('[name=cvc]').value,
                cardName: form.querySelector('[name=cardName]').value,
                cardEmail: form.querySelector('[name=cardEmail]').value,
                cardCpf: form.querySelector('[name=cardCpf]').value,
                cardPhone: form.querySelector('[name=cardPhone]').value,
                cardAddressComplement: form.querySelector('[name=cardAddressComplement]').value,
                cardAddressNumber: form.querySelector('[name=cardAddressNumber]').value,
                cardPostalCode: form.querySelector('[name=cardPostalCode]').value,
            };

            try {
                const res = await fetch('{{ route('web.payWithCard') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await res.json();
                this.cardStatus = result.status === 'CONFIRMED' ? 'confirmed' : 'failed';
            } catch {
                this.cardStatus = 'failed';
            }
            this.loading = false;
        },
    }));
});
</script>
@endpush
