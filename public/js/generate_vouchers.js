const btn = document.getElementById('btn-generator');
const numberInput = document.getElementById('number-vouchers');
const inputLabel = document.getElementById('vouchers-input-label');
const output = document.getElementById('vouchers-output');
const event_id = document.getElementById('page-card').dataset.event_id;

numberInput.addEventListener('input', () => {
    btn.innerText = "Generate " + numberInput.value + " vouchers";
})

btn.addEventListener('click', () => {
    btn.setAttribute('hidden', 'true');
    numberInput.setAttribute('hidden', 'true');
    inputLabel.setAttribute('hidden', 'true');

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
        }else{
            btn.setAttribute('hidden', 'false');
            numberInput.setAttribute('hidden', 'false');
            inputLabel.setAttribute('hidden', 'false');
            output.innerText = "Error generating vouchers. Please contact the platform administrator";
        }
    });
})