import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { enquiryForm, quizForm, enquiryModal } from './forms';

Alpine.plugin(focus);
Alpine.data('enquiryForm', enquiryForm);
Alpine.data('quizForm', quizForm);
Alpine.data('enquiryModal', enquiryModal);

window.Alpine = Alpine;
window.dataLayer = window.dataLayer || [];
Alpine.start();
