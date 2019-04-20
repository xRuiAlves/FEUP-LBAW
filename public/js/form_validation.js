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
            }
            form.classList.add('was-validated');
        }, false);
    });
}, false);