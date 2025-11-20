document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.login-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            // Just submit the form normally without any CSRF handling
            // The form will submit to the route which bypasses CSRF
        });
    }
});