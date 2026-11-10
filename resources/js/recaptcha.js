/**
 * Lazy, explicit reCAPTCHA v2 loader. Supports many widgets per page (inline forms, quiz, modal).
 * The Google script is only fetched when a visitor first interacts with a form, which keeps the page fast.
 */
const state = { loading: false, ready: false, queue: [] };

export function loadRecaptcha() {
    if (state.loading) return;
    state.loading = true;
    window.gatRecaptchaReady = () => {
        state.ready = true;
        state.queue.splice(0).forEach((fn) => fn());
    };
    const s = document.createElement('script');
    s.src = 'https://www.google.com/recaptcha/api.js?onload=gatRecaptchaReady&render=explicit';
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
}

/** Render a widget into `el` (idempotent). `onChange` fires when solved/expired. */
export function renderWidget(el, onChange = () => {}) {
    if (!el || !el.dataset.sitekey) return;
    loadRecaptcha();
    const run = () => {
        if (el.dataset.widgetId !== undefined) return;
        el.dataset.widgetId = window.grecaptcha.render(el, {
            sitekey: el.dataset.sitekey,
            callback: () => onChange(true),
            'expired-callback': () => onChange(false),
            'error-callback': () => onChange(false),
        });
    };
    state.ready ? run() : state.queue.push(run);
}

export function recaptchaToken(el) {
    if (!el || el.dataset.widgetId === undefined || !window.grecaptcha) return '';
    return window.grecaptcha.getResponse(Number(el.dataset.widgetId)) || '';
}

export function resetWidget(el) {
    if (el && el.dataset.widgetId !== undefined && window.grecaptcha) {
        window.grecaptcha.reset(Number(el.dataset.widgetId));
    }
}
