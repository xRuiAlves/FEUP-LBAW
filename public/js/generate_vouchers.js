const form = document.getElementById('voucher_gen_form');
const btn = document.getElementById('btn-generator');
const numberInput = document.getElementById('number-vouchers');
const inputLabel = document.getElementById('vouchers-input-label');
const output = document.getElementById('vouchers-output');
const event_id = document.getElementById('page-card').dataset.event_id;

numberInput.addEventListener('input', () => {
    btn.innerText = "Generate " + numberInput.value + " voucher" + (parseInt(numberInput.value) > 1 ? "s" : "");
})

form.addEventListener('submit', e => {
    // Request done via AJAX
    e.preventDefault();

    if (form.checkValidity() === false) {
        // If the form was not valid, don't try to submit
        return;
    }

    btn.setAttribute('hidden', 'true');
    numberInput.setAttribute('hidden', 'true');
    inputLabel.setAttribute('hidden', 'true');

    output.innerText = "Loading...";

    fetch(`/api/event/${event_id}/vouchers`, {
        method: 'POST',
        body: JSON.stringify({
            nVouchers: numberInput.value
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then((res)=>{
        if (res.status === 200) {
            output.innerHTML = `${moment()}<br><strong><i class="fas fa-check"></i> Successfully generated ${numberInput.value} vouchers:</strong><br><br>`;
            res.json().then(data => {
                let outputCodesElem = document.createElement('div');
                outputCodesElem.innerText += data.reduce((outputString, currentCode) => outputString + currentCode + "\n", "");
                output.appendChild(outputCodesElem);
            });
        } else if (res.status === 422) {
            // Invalid data sent to the back-end
            res.json().then(data => {
                let error_message = "\n" + data.message + "\n\nErrors:\n";
                for (const error_key in data.errors) {
                    error_message += error_key + ": " + data.errors[error_key] + "\n";
                }
                output.innerText = error_message;

                // Inputs "unhidden" to be able to reattempt request
                btn.removeAttribute('hidden');
                numberInput.removeAttribute('hidden');
                inputLabel.removeAttribute('hidden');
            })
        } else {
            btn.setAttribute('hidden', 'false');
            numberInput.setAttribute('hidden', 'false');
            inputLabel.setAttribute('hidden', 'false');
            output.innerText = "Error generating vouchers. Please contact the platform administrator";
        }
    });
})