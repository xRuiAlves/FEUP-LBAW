// From https://getbootstrap.com/docs/4.3/components/forms/#custom-styles
// Example starter JavaScript for disabling form submissions if there are invalid fields
'use strict';
window.addEventListener('load', () => {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    Array.prototype.filter.call(forms, (form) => {
        form.addEventListener('submit', (event) => {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            } else if (form.classList.contains("redirect-on-submit") && form.dataset.redirectTo) {
                // Temporary - using this in order to have working mockups
                // Correctly validated and wants to redirect (has redirect-on-submit class and redirect-to data attribute)
                event.preventDefault();
                event.stopPropagation();
                const redirect_target = form.dataset.redirectTo;
                window.location.href = redirect_target;
            }
            form.classList.add('was-validated');
        }, false);
    });
}, false);