const addEventListeners = () => {
    const dismiss_notification_modal = document.querySelector("#dismiss-notification-modal");
    document.querySelectorAll("#notifications-list .notification-item").forEach((elem) => {
        const notification_id = elem.getAttribute("data-notification-id");
        const button = elem.querySelector(".dismiss-notification-button");
        button.addEventListener("click", () => {
            $('#dismiss-notification-modal').modal();    
            const modal_title = dismiss_notification_modal.querySelector(".custom-modal-title");
            modal_title.setAttribute("data-notification-id", notification_id);
        });
    })

    dismiss_notification_modal.querySelector("button.dismiss-notification").addEventListener("click", () => {
        const notification_id = dismiss_notification_modal.querySelector(".custom-modal-title").getAttribute("data-notification-id");
        dismiss_notification(notification_id);
        $('#dismiss-notification-modal').modal('hide');
    });
};

const dismiss_notification = (notification_id) => {
    fetch('api/notification/dismiss', {
        method: 'PUT',
        body: JSON.stringify({
            notification_id
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            const notification = document.querySelector(`#notifications-list .notification-item[data-notification-id="${notification_id}"]`);
            notification.remove();
        } else {
            // TODO
        }
    });
}

addEventListeners();