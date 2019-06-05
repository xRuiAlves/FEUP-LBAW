const addEventListeners = () => {
    document.querySelector("#disable_events_btn").addEventListener("click", () => {
        const raw_event_ids = document.querySelectorAll("#events-table .content-table tbody tr input[type='checkbox']:checked");
        const event_ids = Array.from(raw_event_ids).map(item => {
            return Number.parseInt(item.parentElement.parentElement.getAttribute('data-event-id'));
        });

        disableEvents(event_ids);
    });

    document.querySelector("#enable_events_btn").addEventListener("click", () => {
        const raw_event_ids = document.querySelectorAll("#events-table .content-table tbody tr input[type='checkbox']:checked");
        const event_ids = Array.from(raw_event_ids).map(item => {
            return Number.parseInt(item.parentElement.parentElement.getAttribute('data-event-id'));
        });

        enableEvents(event_ids);
    });

    document.querySelectorAll("#issue-table .issue-header").forEach((elem) => {
        console.log(elem);
    })
};

const disableEvents = (event_ids) => {
    fetch('/api/event/disable', {
        method: 'PUT',
        body: JSON.stringify({
            event_ids,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            // Clearing previous successes and errors
            document.querySelectorAll("#events-table .status-messages > .alert").forEach(el => el.style.display = "none");
            // Displaying success with the desired message
            const success_alert = document.querySelector("#events-table .status-messages > .alert-success");
            success_alert.style.display = "";
            success_alert.textContent = `${event_ids.length} ${event_ids.length > 1 ? "events" : "event"} disabled successfully!`;

            // Update DOM elements
            event_ids.forEach(event_id => {
                document.querySelector(`#events-table .content-table tbody tr[data-event-id="${event_id}"] > td.event-abling`).textContent = "Yes";
            });
        } else {
            res.json()
            .then(res_json => {
                // Clearing previous successes and errors
                document.querySelectorAll("#events-table .status-messages > .alert").forEach(el => el.style.display = "none");
                // Displaying error with the given message
                const error_alert = document.querySelector("#events-table .status-messages > .alert-danger");
                error_alert.style.display = "";
                let error_str = `Error: ${res_json.message}\n`;
                if (res_json.errors) {
                    for (const error_key of Object.keys(res_json.errors)) {
                        error_str += `${error_key}: ${res_json.errors[error_key]}\n`;
                    }
                }
                
                error_alert.innerHTML = error_str;
            });
        }
    })
};

const enableEvents = (event_ids) => {
    fetch('/api/event/enable', {
        method: 'PUT',
        body: JSON.stringify({
            event_ids,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            // Clearing previous successes and errors
            document.querySelectorAll("#events-table .status-messages > .alert").forEach(el => el.style.display = "none");
            // Displaying success with the desired message
            const success_alert = document.querySelector("#events-table .status-messages > .alert-success");
            success_alert.style.display = "";
            success_alert.textContent = `${event_ids.length} ${event_ids.length > 1 ? "events" : "event"} enabled successfully!`;

            // Update DOM elements
            event_ids.forEach(event_id => {
                document.querySelector(`#events-table .content-table tbody tr[data-event-id="${event_id}"] > td.event-abling`).textContent = "No";
            });
        } else {
            res.json()
            .then(res_json => {
                // Clearing previous successes and errors
                document.querySelectorAll("#events-table .status-messages > .alert").forEach(el => el.style.display = "none");
                // Displaying error with the given message
                const error_alert = document.querySelector("#events-table .status-messages > .alert-danger");
                error_alert.style.display = "";
                let error_str = `Error: ${res_json.message}\n`;
                if (res_json.errors) {
                    for (const error_key of Object.keys(res_json.errors)) {
                        error_str += `${error_key}: ${res_json.errors[error_key]}\n`;
                    }
                }
                
                error_alert.innerHTML = error_str;
            });
        }
    });
};

addEventListeners();