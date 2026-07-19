import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

window.copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Copiado!', type: 'success' } }));
    } catch {
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Não foi possível copiar.', type: 'error' } }));
    }
};

window.shareWhatsApp = (url, title) => {
    const text = encodeURIComponent(`${title} — ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank', 'noopener,noreferrer');
};

window.formatCurrency = (value) => {
    const num = typeof value === 'string'
        ? parseFloat(value.replace(/\./g, '').replace(',', '.'))
        : value;
    if (Number.isNaN(num)) return 'R$ 0,00';
    return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

document.addEventListener('alpine:init', () => {
    Alpine.data('toast', () => ({
        visible: false,
        message: '',
        type: 'success',
        show(message, type = 'success') {
            this.message = message;
            this.type = type;
            this.visible = true;
            setTimeout(() => { this.visible = false; }, 3000);
        },
        init() {
            window.addEventListener('toast', (e) => this.show(e.detail.message, e.detail.type));
        },
    }));

    Alpine.data('mobileNav', () => ({
        open: false,
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
    }));
});
