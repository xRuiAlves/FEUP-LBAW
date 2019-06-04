const addEventListeners = () => {
    const change_name_form = document.querySelector("#change-name-form");
    change_name_form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!change_name_form.checkValidity()) {
            return;
        }

        const name = change_name_form.querySelector("input[name=name]").value;

        changeName(name);
    });

    const change_password_form = document.querySelector("#change-password-form");
    change_password_form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!change_password_form.checkValidity()) {
            return;
        }

        const current_password = change_password_form.querySelector("input[name=current_password]").value;
        const new_password = change_password_form.querySelector("input[name=new_password]").value;
        const new_password_confirmation = change_password_form.querySelector("input[name=new_password_confirmation]").value;

        changePassword(current_password, new_password, new_password_confirmation, change_password_form);
    });
}

const changeName = (name) => {
    if (name.length < 3 || name.length > 20) {
        displayNameErrorMessage("Failed to change name. The chosen name must be between 3 and 20 characters long.");
        return;
    }

    fetch('/api/name/change', {
        method: 'PUT',
        body: JSON.stringify({
            name
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            displayNameSuccessMessage(name);
            document.querySelector("#nav_user_name").textContent = name;

            const change_name_form = document.querySelector("#change-name-form");
            change_name_form.reset();
            change_name_form.classList.remove("was-validated");
        } else {
            res.json()
            .then(json => {
                displayNameErrorMessage(`Failed to change name. ${json.message}`);
            });
        }
    });
} 

const changePassword = (current_password, new_password, new_password_confirmation, form) => {
    if (new_password.length < 6) {
        displayPasswordErrorMessage("Your new password must be, at least, 6 characters long.");
        return;
    } else if (new_password !== new_password_confirmation) {
        displayPasswordErrorMessage("Your new password does not match the new password confirmation.");
        return;
    }

    form.submit();
}

const displayNameErrorMessage = (error_message) => {
    const success_alert = document.querySelector("#change-name-form .status-messages > .alert-success");
    const danger_alert = document.querySelector("#change-name-form .status-messages > .alert-danger");
    success_alert.style.display = "none";
    danger_alert.style.display = "";
    danger_alert.textContent = error_message;
}

const displayNameSuccessMessage = (new_name) => {
    const success_alert = document.querySelector("#change-name-form .status-messages > .alert-success");
    const danger_alert = document.querySelector("#change-name-form .status-messages > .alert-danger");
    success_alert.style.display = "";
    danger_alert.style.display = "none";
    success_alert.textContent = `Your name was successfully changed to '${new_name}'!`;
}

const displayPasswordErrorMessage = (error_message) => {
    const success_alert = document.querySelector("#change-password-form .status-messages > .alert-success");
    const danger_alert = document.querySelector("#change-password-form .status-messages > .alert-danger");
    success_alert.style.display = "none";
    danger_alert.style.display = "";
    danger_alert.textContent = error_message;
}

const displayPasswordSuccessMessage = () => {
    const success_alert = document.querySelector("#change-password-form .status-messages > .alert-success");
    const danger_alert = document.querySelector("#change-password-form .status-messages > .alert-danger");
    success_alert.style.display = "";
    danger_alert.style.display = "none";
    success_alert.textContent = "Your password was successfully updated!";
}

addEventListeners();