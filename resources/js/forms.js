import { recaptchaToken, renderWidget, resetWidget } from './recaptcha';

const push = (payload) => {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(payload);
};

/**
 * Shared behaviour for EVERY enquiry form (plain forms, modal, quiz final step).
 * Server is the source of truth: it re-validates consent, reCAPTCHA, CSRF, rate limit.
 */
export function enquiryForm(cfg = {}) {
    return {
        errors: {},
        submitting: false,
        done: false,
        formError: '',
        captchaSolved: false,
        _bound: false,

        init() {
            const form = this.$root.tagName === 'FORM' ? this.$root : this.$root.querySelector('form');
            if (!form || this._bound) return;
            this._bound = true;
            // Load reCAPTCHA on first interaction, not on page load.
            form.addEventListener('focusin', () => this.renderCaptcha(), { once: true });
        },

        formEl() {
            return this.$root.tagName === 'FORM' ? this.$root : this.$root.querySelector('form');
        },

        slot() {
            return this.formEl()?.querySelector('.gat-recaptcha');
        },

        renderCaptcha() {
            renderWidget(this.slot(), (ok) => {
                this.captchaSolved = ok;
                if (ok) delete this.errors['g-recaptcha-response'];
            });
        },

        clear(name) {
            if (this.errors[name]) delete this.errors[name];
        },

        async submit() {
            const form = this.formEl();
            this.formError = '';
            this.errors = {};

            // Browser-side checks (the server re-checks everything)
            if (!form.reportValidity()) return;
            if (!form.querySelector('[name="consent"]').checked) {
                this.errors.consent = ['Please tick the box to agree to the terms and SMS consent before submitting.'];
                return;
            }
            this.renderCaptcha();
            const token = recaptchaToken(this.slot());
            if (!token) {
                this.errors['g-recaptcha-response'] = ['Please tick "I\'m not a robot".'];
                return;
            }

            this.submitting = true;
            const body = new FormData(form);
            push({ event: 'enquiry_form_submit', form_name: body.get('form_name') });

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body,
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));

                if (res.ok && data.redirect) {
                    this.done = true;
                    setTimeout(() => (window.location.href = data.redirect), 1200);
                    return;
                }
                if (res.status === 422) {
                    this.errors = data.errors || {};
                    this.formError = 'Please check the highlighted fields.';
                } else if (res.status === 429) {
                    this.formError = 'Too many attempts. Please wait a minute and try again.';
                } else if (res.status === 419) {
                    this.formError = 'Your session expired. Please refresh the page and try again.';
                } else {
                    this.formError = 'Something went wrong. Please try again, or call us.';
                }
                resetWidget(this.slot());
                this.captchaSolved = false;
            } catch (e) {
                this.formError = 'We could not reach the server. Please check your connection and try again.';
                resetWidget(this.slot());
            } finally {
                this.submitting = false;
            }
        },
    };
}

/** Multi-step hero quiz. Answers live in Alpine state and are submitted with the contact form in one request. */
export function quizForm(cfg = {}) {
    return {
        ...enquiryForm(cfg),
        total: cfg.total || 0,
        step: 0,
        answers: {},
        advancing: false,

        get progress() {
            return Math.round((Math.min(this.step, this.total) / (this.total + 1)) * 100);
        },
        get onContact() {
            return this.step >= this.total;
        },

        pick(key, value) {
            this.answers[key] = value;
            if (this.advancing) return;
            this.advancing = true;
            setTimeout(() => {
                this.step++;
                this.advancing = false;
                this.afterStep();
            }, 180); // brief pause so the selection is visible
        },
        next(key) {
            if (!this.answers[key]) return;
            this.step++;
            this.afterStep();
        },
        back() {
            if (this.step > 0) this.step--;
        },
        afterStep() {
            if (this.onContact) {
                this.$nextTick(() => {
                    this.renderCaptcha();
                    this.formEl()?.querySelector('input[name="first_name"]')?.focus({ preventScroll: true });
                });
            }
        },
        summary(steps) {
            return steps.filter((s) => this.answers[s.key]).map((s) => ({ label: s.label, value: this.answers[s.key] }));
        },
    };
}

/** Pop-up modal that hosts one shared enquiry form; buttons open it with context (package, form name). */
export function enquiryModal(defaults = {}) {
    return {
        isOpen: false,
        heading: defaults.heading || 'Get your free personalised quote',
        intro: defaults.intro || '',
        form_name: defaults.form_name || 'Header Modal',
        package_id: '',
        package_name: '',
        tour_type: '',
        _trigger: null,

        open(detail = {}) {
            this._trigger = document.activeElement;
            this.form_name = detail.form_name || defaults.form_name || 'Header Modal';
            this.heading = detail.heading || defaults.heading || 'Get your free personalised quote';
            this.intro = detail.intro || '';
            this.package_id = detail.package_id || '';
            this.package_name = detail.package_name || '';
            this.tour_type = detail.tour_type || '';
            this.isOpen = true;
            this.$nextTick(() => {
                const slot = this.$root.querySelector('.gat-recaptcha');
                renderWidget(slot);
                resetWidget(slot);
            });
        },
        close() {
            this.isOpen = false;
            this._trigger?.focus?.();
        },
    };
}
