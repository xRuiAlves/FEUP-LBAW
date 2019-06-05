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
        tickets_container.appendChild(new_ticket);


    })
}

const submitAttendClickEvent = () => {
    const form = document.getElementById('ticket-form');

    console.log(form);


    form.addEventListener('submit', (e) => {

        e.preventDefault();

        const tickets_container = document.getElementById('tickets-container');
        console.log('====================================');
        console.dir($(tickets_container).children('.ticket').map(function () {
            return {
                nif: $($(this).find('input[name="nif"]')[0]).val(),
                address: $($(this).find('input[name="address"]')[0]).val(),
                billing_name: $($(this).find('input[name="billing_name"]')[0]).val(),
                voucher_code: $($(this).find('input[name="voucher_code"]')[0]).val()
            }
        }).toArray());
        console.log('====================================');
        return false;
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

addTicketClickEvent();
submitAttendClickEvent();