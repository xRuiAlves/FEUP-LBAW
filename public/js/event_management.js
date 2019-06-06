const event_id = document.getElementById('page-card').dataset.event_id;

document.querySelectorAll('.btn.check-in').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;
        let user_name = elem.dataset.user_name;

        fetch(`/api/event/${event_id}/check-in`, {
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
                elem.classList.remove('btn-success');
                elem.setAttribute('disabled', true);
                elem.innerHTML = 'Checked In  <i class="fas fa-check"></i>'
                
                const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.innerHTML = `Successfully checked-in user <strong>${user_name}</strong>`;
            }else{
                elem.classList.remove('btn-success');
                elem.classList.add('btn-danger');
                elem.setAttribute('disabled', true);
                elem.innerHTML = 'ERROR'

                const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                success_alert.style.display = "none";
                danger_alert.style.display = "";
                danger_alert.innerHTML = `Failed to check-in user <strong>${user_name}</strong>`;
            }
        });
    });
});

document.querySelectorAll('.btn.remove-attendee').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;
        let user_name = elem.dataset.user_name;

        confirmModal("Are you sure you wish to delete this attendee's ticket?").then(() => 
            fetch(`/api/event/${event_id}/attendee`, {
                method: 'DELETE',
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
                    let row = document.querySelector(`.attendee[data-user_id="${user_id}"]`);
                    row.parentElement.removeChild(row);
                
                    const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                    success_alert.style.display = "";
                    danger_alert.style.display = "none";
                    success_alert.innerHTML = `Successfully removed user <strong>${user_name}</strong> from the event`;
                } else {
                    const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                    success_alert.style.display = "none";
                    danger_alert.style.display = "";
                    danger_alert.innerHTML = `Failed to remove user <strong>${user_name}</strong> from the event`;
                }
            })
        );
    });
});

document.querySelectorAll('.btn.remove-organizer').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;
        let user_name = elem.dataset.user_name;

        confirmModal("Are you sure you wish to remove this organizer from the event?").then(() => 
            fetch(`/api/event/${event_id}/organizer`, {
                method: 'DELETE',
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
                    let row = document.querySelector(`.organizer[data-user_id="${user_id}"]`);
                    row.parentElement.removeChild(row);
                
                    const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                    success_alert.style.display = "";
                    danger_alert.style.display = "none";
                    success_alert.innerHTML = `Successfully removed organizer <strong>${user_name}</strong> from the organization team`;
                } else {
                    const success_alert = document.querySelector("#event-management-status-messages .alert-success");
                    const danger_alert = document.querySelector("#event-management-status-messages .alert-danger");
                    success_alert.style.display = "none";
                    danger_alert.style.display = "";
                    danger_alert.innerHTML = `Failed to remove organizer <strong>${user_name}</strong> from the organization team`;
                }
            })
        );
    });
});

document.querySelector('#generate-vouchers').addEventListener('click', () => window.location.href = "./generate-vouchers");

const quitOrganizationBtn = document.querySelector('#quit-organization-btn');
if(quitOrganizationBtn){
    quitOrganizationBtn.addEventListener('click', () => {
        confirmModal("Are you sure you wish to leave the organization of this event?").then(() => 
            fetch(`/api/event/${event_id}/quit-organization`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
            .then((res)=>{
                if (res.status === 200) {
                    window.location.href = ".";
                }else{
                    quitOrganizationBtn.setAttribute('disabled', 'true');
                    quitOrganizationBtn.innerText = 'ERROR. Could not quit organization';
                }
            })
        );
    });
}

const cancelEventBtn = document.querySelector('#cancel-event-btn');
if(cancelEventBtn){
    cancelEventBtn.addEventListener('click', async () => {
        confirmModal("Are you sure you wish to cancel this event?").then(() => 
            fetch(`/api/event/${event_id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
            .then((res)=>{
                if (res.status === 200) {
                    window.location.href = ".";
                }else{
                    cancelEventBtn.setAttribute('disabled', 'true');
                    cancelEventBtn.innerText = 'ERROR. Could not cancel event';
                }
            })
        );
    });
}