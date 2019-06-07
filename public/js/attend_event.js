const addTicketClickEvent = () => {
    const add_ticket_btn = document.getElementById('add-ticket');

    add_ticket_btn.addEventListener('click', e => {
        e.preventDefault();

        const tickets_container = document.getElementById('tickets-container');

        const new_separator = document.createElement('div');
        new_separator.innerHTML = `<div class="separator main-separator">
            <hr>
        </div>`
        tickets_container.appendChild(new_separator);

        const new_ticket = document.querySelector('.ticket').cloneNode(true);

        const new_ticket_number = $('.ticket').length + 1;

        $($(new_ticket).find('header h3')).html(`Ticket #${new_ticket_number}`);


        //clear each input
        $(new_ticket).find('input').each(function() {
            $(this).val("");
        });

        tickets_container.appendChild(new_ticket);


    })
}

const sendRequest = async (url, method, body) => {

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

const submitAttendClickEvent = () => {
    const form = document.getElementById('ticket-form');

    form.addEventListener('submit', (e) => {

        e.preventDefault();

        
        const tickets_container = document.getElementById('tickets-container');
        const tickets = $(tickets_container).children('.ticket').map(function () {
            return {
                nif: $($(this).find('input[name="nif"]')[0]).val(),
                address: $($(this).find('input[name="address"]')[0]).val(),
                billing_name: $($(this).find('input[name="billing_name"]')[0]).val(),
                voucher_code: $($(this).find('input[name="voucher_code"]')[0]).val()
            }
        }).toArray();


        sendRequest(`/event/${form.getAttribute('data-event-id')}/attend`, 'POST', {tickets})
        .then(res => {
            res.json()
                .then(data => {
                    if(res.status !== 200) {
                        const errors = parseErrors(data.errors);
                        
                        displayErrors(form, errors);
                    } else {
                        window.location.href = `/event/${form.getAttribute('data-event-id')}`;
                    }
                })
            }
        );

        return false;
    })
}

const parseErrors = (errors) => {
    let ret = {};

    Object.keys(errors).forEach(key => {

        if(key === 'global') { // special case for global error
            ret['global']= errors[key];
            return;
        }

        let reg = /.*\.(\d+)\.(.*)/g;
        let match = reg.exec(key);

        const ticket_num = match[1];
        const field = match[2];


        if(!ret[ticket_num]) {
            ret[ticket_num] = {};
        }

        ret[ticket_num][field] = errors[key].map(msg => parseErrorMessage(msg));
    })

    return ret;

}

const parseErrorMessage = msg => {
    let reg = /The .*\.(\d+)\.(.*)\s(.*)/g;
    let match = reg.exec(msg);

    const ticket_num = match[1];
    const field = match[2];

    return `The ${field} ${match[3]}`;
}

const displayErrors = (form, errors) => {
    $('input').each(function() {
        this.setCustomValidity(true);
    });
    $('.invalid-feedback').remove();
    $('.form-error').remove();

    Object.keys(errors).forEach(error_type => {
        if(error_type === 'global') { //special case for global form error

            const error_elem = document.createElement('div');
            error_elem.classList.add('form-error');
            errors['global'].forEach(msg => {
                const error_msg = document.createElement('p');
                $(error_msg).html(msg);
                error_elem.appendChild(error_msg);
            })
            $(form).append(error_elem)

        } else {//case where the error is for a single ticket
            const ticket_num = error_type;

            const ticket = $('.ticket')[ticket_num];
            Object.entries(errors[error_type]).forEach(([field, messages]) => {
                messages.forEach(msg => {
                    const error_elem = document.createElement('div');
                    error_elem.classList.add('invalid-feedback');
                    $(error_elem).html(msg)
                    $(ticket).find(`input[name="${field}"]`).each(function() {
                        this.setCustomValidity(false);
                    });
                    $(ticket).find(`input[name="${field}"]`).parent().append(error_elem);
                })
            })
        }
    })
}

addTicketClickEvent();
submitAttendClickEvent();