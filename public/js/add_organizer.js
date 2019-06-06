let event_id = document.getElementById('user-table').dataset.event_id;

document.querySelectorAll('#user-table .btn.action').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;

        fetch(`/api/event/${event_id}/organizer`, {
            method: 'PUT',
            body: JSON.stringify({
                user_id
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        })
        .then((res)=>{
            if (res.status === 200) {
                elem.classList.add('btn-success');
                elem.setAttribute('disabled', true);
                elem.innerHTML = '<i class="fas fa-check"></i>'
                
                const success_alert = document.querySelector("#event-add-organizer-status-messages .alert-success");
                const danger_alert = document.querySelector("#event-add-organizer-status-messages .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.innerHTML = `Successfully added <strong>user ${user_id}</strong> to the organization team`;
            }else{
                elem.classList.add('btn-danger');
                elem.setAttribute('disabled', true);
                elem.innerHTML = 'ERROR'

                const success_alert = document.querySelector("#event-add-organizer-status-messages .alert-success");
                const danger_alert = document.querySelector("#event-add-organizer-status-messages .alert-danger");
                success_alert.style.display = "none";
                danger_alert.style.display = "";
                danger_alert.innerHTML = `Failed to add <strong>user ${user_id}</strong> to the organization team`;
            }
        });
    });
});