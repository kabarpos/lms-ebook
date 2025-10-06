import './bootstrap';
import './tiptap-extensions';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Enable CSP-compliant evaluator to avoid 'unsafe-eval' requirements
import '@alpinejs/csp';

Alpine.start();
