const event_id = document.getElementById('page-card').dataset.event_id;

document.querySelectorAll('.btn.check-in').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;

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
            }else{
                elem.classList.remove('btn-success');
                elem.classList.add('btn-danger');
                elem.setAttribute('disabled', true);
                elem.innerHTML = 'ERROR'
            }
        });
    });
});

document.querySelectorAll('.btn.remove-attendee').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;

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
            }
        });
    });
});

document.querySelectorAll('.btn.remove-organizer').forEach((elem) => {
    elem.addEventListener('click', () => {
        let user_id = elem.dataset.user_id;

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
            }
        });
    });
});

document.querySelector('#generate-vouchers').addEventListener('click', () => window.location.href = "./generate-vouchers");