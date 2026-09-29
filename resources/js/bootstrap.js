import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Ziggy route helper - loaded from blade via @routes directive
// Falls back to a no-op if not available
if (typeof route === 'undefined') {
    window.route = (name, params) => {
        console.warn('Ziggy not loaded yet for route:', name);
        return '/';
    };
}
