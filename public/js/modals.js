async function confirmModal(msg) {
    return new Promise((resolve, reject) => {
        let modal = document.querySelector("#confirmation-modal");
        let yesBtn = modal.querySelector('#confirmation-modal-yes');
        let noBtn = modal.querySelector('#confirmation-modal-no');
        let text = modal.querySelector('#confirmation-modal-text');
        text.innerText = msg;

        $(modal).modal('show');

        let onYes = () => {
            resolve();
            yesBtn.removeEventListener('click', onYes);
            noBtn.removeEventListener('click', onNo);
            $(modal).modal('hide');
        }

        let onNo = () => {
            yesBtn.removeEventListener('click', onYes);
            noBtn.removeEventListener('click', onNo);
            $(modal).modal('hide');
        }

        yesBtn.addEventListener('click', onYes);
        noBtn.addEventListener('click', onNo);
    });
   
}