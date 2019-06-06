const addEventListeners = () => {
    const promote_to_admin_modal = document.querySelector("#promote-to-admin-modal");
    promote_to_admin_modal.querySelector("form").addEventListener("submit", (e) => {
        e.preventDefault();
        
        const user_id = promote_to_admin_modal.getAttribute("data-user-id");
        const name = promote_to_admin_modal.getAttribute("data-user-name");
        const button_node = document.querySelector(`#user-table .user-entry[data-user-id='${user_id}'] button.promote-admin-button`);

        promoteToAdmin(user_id, name, button_node);
        $('#promote-to-admin-modal').modal('hide');
    });

    const user_status_modal = document.querySelector("#user-status-modal");
    user_status_modal.querySelector("button.action-1").addEventListener("click", () => {        
        const user_id = user_status_modal.getAttribute("data-user-id");
        const name = user_status_modal.getAttribute("data-user-name");
        const is_disabled = user_status_modal.getAttribute("data-user-disabled");
        const button_node = document.querySelector(`#user-table .user-entry[data-user-id='${user_id}'] button.account-enable-toggle`);

        if (is_disabled == "true") {
            enableUserAccount(user_id, name, button_node);
        } else {
            disableUserAccount(user_id, name, button_node, false);
        }
        
        $('#user-status-modal').modal('hide');
    });
    user_status_modal.querySelector("button.action-2").addEventListener("click", () => {
        const is_disabled = user_status_modal.getAttribute("data-user-disabled");
        if (is_disabled == "false") {
            const user_id = user_status_modal.getAttribute("data-user-id");
            const name = user_status_modal.getAttribute("data-user-name");
            const button_node = document.querySelector(`#user-table .user-entry[data-user-id='${user_id}'] button.account-enable-toggle`);

            disableUserAccount(user_id, name, button_node, true);
            $('#user-status-modal').modal('hide');
        }
    });

    document.querySelectorAll("#user-table .user-entry").forEach(entry => {
        const promote_admin_button_node = entry.querySelector("button.promote-admin-button");
        const account_enable_toggle_button_node = entry.querySelector("button.account-enable-toggle");
        const user_id = entry.getAttribute("data-user-id");
        const name = entry.getAttribute("data-user-name");
        promote_admin_button_node.addEventListener("click", (e) => {
            const is_disabled = entry.getAttribute("data-user-disabled");
            promote_to_admin_modal.setAttribute("data-user-id", user_id);
            promote_to_admin_modal.setAttribute("data-user-name", name);
            promote_to_admin_modal.setAttribute("data-user-disabled", is_disabled);

            $('#promote-to-admin-modal').modal();
            promote_to_admin_modal.querySelector(".modal-body").innerHTML = 
                `Are you sure you want to promote user <u><strong>${name}</strong></u> to a platform administrator?`;
        });
        account_enable_toggle_button_node.addEventListener("click", (e) => {
            const is_disabled = entry.getAttribute("data-user-disabled");
            user_status_modal.setAttribute("data-user-id", user_id);
            user_status_modal.setAttribute("data-user-name", name);
            user_status_modal.setAttribute("data-user-disabled", is_disabled);

            $('#user-status-modal').modal();
            user_status_modal.querySelector(".modal-title").innerHTML = 
                `${is_disabled == "true" ? "Enabling" : "Disabling"} user account`
            user_status_modal.querySelector(".modal-body").innerHTML = 
                `Do you want to ${is_disabled == "true" ? "enable" : "disable"} user <u><strong>${name}</strong></u> account? 
                ${is_disabled == "true" ? "" : "This action will cause <u>all the events the user created to be <strong>disabled</strong></u>."}`;
            const action_button_1 = user_status_modal.querySelector(".modal-footer button.action-1");
            const action_button_2 = user_status_modal.querySelector(".modal-footer button.action-2");
            if (is_disabled == "true") {
                action_button_1.innerHTML = "Enable"
                action_button_2.style.display = "none;"
            } else {
                action_button_1.innerHTML = "Disable User";
                action_button_2.innerHTML = "Disable User and Events";
            }
        });
    });
}

const promoteToAdmin = (user_id, name, button_node) => {
    fetch('/api/admin/promote', {
        method: 'PUT',
        body: JSON.stringify({
            user_id,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
            const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
            success_alert.style.display = "";
            danger_alert.style.display = "none";
            success_alert.innerHTML = `Successfully promoted user <strong><u>${name}</u></strong> to admin`;
            button_node.setAttribute("disabled", "disabled");
        } else {
            const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
            const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
            success_alert.style.display = "none";
            danger_alert.style.display = "";
            danger_alert.textContent = "Failed to promote user to admin";
        }
    });
}

const enableUserAccount = (user_id, name, button_node) => {
    fetch('/api/user/enable', {
        method: 'PUT',
        body: JSON.stringify({
            user_id,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
            const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
            success_alert.style.display = "";
            danger_alert.style.display = "none";
            success_alert.innerHTML = `Successfully enabled user <strong><u>${name}</u></strong> account`;

            const icon_node = button_node.querySelector("i");
            const user_node = document.querySelector(`#user-table .user-entry[data-user-id='${user_id}']`);
            user_node.setAttribute("data-user-disabled", "false");
            user_node.querySelector(".status").textContent = "Active";
            button_node.querySelector(".text").textContent = "Disable";
            icon_node.classList.remove("fa-undo");
            icon_node.classList.add("fa-ban");
        } else {
            const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
            const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
            success_alert.style.display = "none";
            danger_alert.style.display = "";
            danger_alert.textContent = "Failed to enable user account";
        }
    });
}

const disableUserAccount = (user_id, name, button_node, disable_events) => {
    fetch('/api/user/disable', {
        method: 'PUT',
        body: JSON.stringify({
            user_id,
            disable_events
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            res.json().then(({ disabled_events }) => {
                console.log(disabled_events);
                let success_message = `Successfully disabled user <strong><u>${name}</u></strong> account. `;
                if (disabled_events.length === 0 && disable_events) {
                    success_message += "The user was not hosting any events that were active, thus no events were disabled."
                } else if (disabled_events.length === 1) {
                    success_message += `The event <i><u>${disabled_events[0]}</u></i> was disabled`;
                } else if (disabled_events.length > 1) {
                    success_message += "The events "
                    let prefix = "";
                    for (let i = 0; i < disabled_events.length - 1; i++) {
                        success_message += `${prefix}<i><u>${disabled_events[i]}</u></i>`;
                        prefix = ", ";
                    }
                    success_message += ` and <i><u>${disabled_events[disabled_events.length - 1]}</u></i> (a total of <strong>${disabled_events.length} events</strong>) were disabled.`
                }
                const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
                const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.innerHTML = success_message;
    
                const icon_node = button_node.querySelector("i");
                const user_node = document.querySelector(`#user-table .user-entry[data-user-id='${user_id}']`);
                user_node.setAttribute("data-user-disabled", "true");
                user_node.querySelector(".status").textContent = "Disabled";
                button_node.querySelector(".text").textContent = "Enable";
                icon_node.classList.remove("fa-ban");
                icon_node.classList.add("fa-undo");
            });
        } else {
            const success_alert = document.querySelector("#user-table .status-messages > .alert-success");
            const danger_alert = document.querySelector("#user-table .status-messages > .alert-danger");
            success_alert.style.display = "none";
            danger_alert.style.display = "";
            danger_alert.textContent = "Failed to disable user account";
        }
    });
}



addEventListeners();