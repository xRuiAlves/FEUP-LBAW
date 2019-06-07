const attendEventClick = () => {
    const attend_btn = document.getElementById('attend-btn');

    attend_btn.addEventListener('click', () => {
        window.location.href = `/event/${attend_btn.getAttribute('data-event-id')}/attend`;
    })
}

const addFavoriteClickEvent = () => {
    const favorite_btn = document.getElementById("favorite-marker");
    
    favorite_btn.addEventListener("click", (e) => {
        const new_val = favorite_btn.classList.contains('active') ? false : true;

        const event_id = favorite_btn.getAttribute('data-event-id');
        
        if(new_val) { // favorited event - must query to mark favorite
            sendFavoriteRequest('/api/event/favorite', 'POST', {event_id})
            .then(res => {
                
                if (res.status === 200) {
                    res.json()
                    .then(json => {
                        favorite_btn.classList.add('active');
                        favorite_btn.setAttribute('title', 'Unmark as Favorite')
                        
                    });
                }
            });
        } else { //unfavorited -- remove favorite

            sendFavoriteRequest('/api/event/favorite', 'DELETE', {event_id})
            .then(res => {
                
                if (res.status === 200) {
                    res.json()
                    .then(json => {
                        favorite_btn.classList.remove('active');
                        favorite_btn.setAttribute('title', 'Mark as Favorite')

                    });
                }
            });
        }


    });
}

const sendFavoriteRequest = async (url, method, body) => {
    
    return fetch(url, {
        method: method,
        body: JSON.stringify(body),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    
}

addFavoriteClickEvent();
attendEventClick();