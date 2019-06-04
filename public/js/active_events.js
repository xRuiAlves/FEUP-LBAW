const addFavoriteClickEvent = () => {
    const favorite_btn = document.getElementById("favorite-marker");
    
    favorite_btn.addEventListener("click", (e) => {
        const new_val = favorite_btn.classList.contains('active') ? false : true;

        const event_id = favorite_btn.getAttribute('data-event-id');
        
        if(new_val) { // favorited event - must query to mark favorite
            console.log('marked as favorite');
            sendFavoriteRequest('/api/event/favorite', 'POST', {event_id})
            .then(res => {
                console.log(res);
                
                if (res.status === 200) {
                    res.json()
                    .then(json => {
                        favorite_btn.classList.add('active');
                        
                    });
                } else {
                    res.json()
                    .then(json => {
                        console.log('====================================');
                        console.dir("dont toggle the button here instead of bootstrap toggle");
                        console.log('====================================');
                    });
                }
            });
        } else { //unfavorited -- remove favorite
            console.log('unmarked as favorite');


            sendFavoriteRequest('/api/event/favorite', 'DELETE', {event_id})
            .then(res => {
                console.log(res);
                
                if (res.status === 200) {
                    res.json()
                    .then(json => {
                        favorite_btn.classList.remove('active');
                    });
                } else {
                    res.json()
                    .then(json => {
                        console.log('====================================');
                        console.dir("dont toggle the button here instead of bootstrap toggle");
                        console.log('====================================');
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