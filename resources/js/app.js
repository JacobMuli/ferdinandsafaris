import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import bookingForm from './booking-form';

window.Alpine = Alpine;
Alpine.plugin(collapse);

// Register components
window.bookingForm = bookingForm;
Alpine.data('bookingForm', bookingForm);

Alpine.start();
