const addEventListeners = () => {
    document.querySelector("#disable_events_btn").addEventListener("click", () => {
        console.log("Disable clicked");
    });

    document.querySelector("#enable_events_btn").addEventListener("click", () => {
        console.log("Enable clicked");
    });
};

const disableEvent = (event_id) => {
    fetch('/api/event/disable', {
        method: 'PUT',
        body: JSON.stringify({
            'id': event_id,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(res => {
        console.log(res);
        return res.text();
    })
    .then(text => console.log("thetext:", text));
};

const enableEvent = (event_id) => {
    fetch('/api/event/enable', {
        method: 'PUT',
        body: JSON.stringify({
            'id': event_id,
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(res => {
        console.log(res);
        return res.text();
    })
    .then(text => console.log("thetext:", text));
};

addEventListeners();