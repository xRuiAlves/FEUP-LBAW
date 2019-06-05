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

    document.querySelectorAll("#user-table .user-entry").forEach(entry => {
        const button_node = entry.querySelector("button.promote-admin-button");
        button_node.addEventListener("click", (e) => {
            const user_id = entry.getAttribute("data-user-id");
            const name = entry.getAttribute("data-user-name");
            promote_to_admin_modal.setAttribute("data-user-id", user_id);
            promote_to_admin_modal.setAttribute("data-user-name", name);

            $('#promote-to-admin-modal').modal();
            promote_to_admin_modal.querySelector(".modal-body").innerHTML = 
                `Are you sure you want to promote user <u><strong>${name}</strong></u> to a platform administrator?`;
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
            success_alert.textContent = `Successfully promoted user '${name}' to admin`;
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

addEventListeners();