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
            }else{
                elem.classList.add('btn-danger');
                elem.setAttribute('disabled', true);
                elem.innerHTML = 'ERROR'
            }
        });
    });
});