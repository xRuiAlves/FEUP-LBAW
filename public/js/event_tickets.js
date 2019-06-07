document.querySelectorAll('.btn.cancel-ticket').forEach((elem) => {
    elem.addEventListener('click', () => {

        let ticket_id = elem.dataset.ticketId;
        console.log(ticket_id);
        let event_id = elem.dataset.eventId;

        confirmModal("Are you sure you wish to delete this attendee's ticket?").then(() => 
            fetch(`/api/event/${event_id}/attendee`, {
                method: 'DELETE',
                body: JSON.stringify({
                    ticket_id
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
            .then((res)=>{
                if (res.status === 200) {
                    let row = document.querySelector(`.ticket[data-ticket-id="${ticket_id}"]`);
                    row.parentElement.removeChild(row);
                
                    const success_alert = document.querySelector("#event-tickets-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-tickets-status-messages .alert-danger");
                    success_alert.style.display = "";
                    danger_alert.style.display = "none";
                    success_alert.innerHTML = `Successfully removed ticket <strong>${ticket_id}</strong>`;
                } else {
                    const success_alert = document.querySelector("#event-tickets-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-tickets-status-messages .alert-danger");
                    success_alert.style.display = "none";
                    danger_alert.style.display = "";
                    danger_alert.innerHTML = `Failed to remove ticket <strong>${ticket_id}</strong> from the event`;
                }
            })
        );
    });
});