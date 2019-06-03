const submit_issue_form = document.querySelector("#create-issue-form");
submit_issue_form.addEventListener("submit", (e) => {
    e.preventDefault();
    if (!submit_issue_form.checkValidity()) {
        return;
    }

    const title = submit_issue_form.querySelector("input[name=title]").value;
    const content = submit_issue_form.querySelector("textarea[name=content]").value;

    submitIssue(title, content);
});

const submitIssue = (title, content) => {
    if (title.length > 64) {
        displayIssueErrorMessage("The issue title must be, at most, 64 characters long.");
        return;
    } else if (content.length > 600) {
        displayIssueErrorMessage("The issue's content message must be, at most, 600 characters long.");
        return;
    }

    fetch('/issue/create', {
        method: 'POST',
        body: JSON.stringify({
            title,
            content
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            const success_alert = document.querySelector("#create-issue-form .status-messages > .alert-success");
            const danger_alert = document.querySelector("#create-issue-form .status-messages > .alert-danger");
            success_alert.style.display = "";
            danger_alert.style.display = "none";
            success_alert.textContent = `The issue was successfully submitted to the administration team.`;

            const submit_issue_form = document.querySelector("#create-issue-form");
            submit_issue_form.reset();
            submit_issue_form.classList.remove("was-validated");
        } else {
          res.json().then(json => {
              displayIssueErrorMessage(json.message);
          })
        }
    });
}   

const displayIssueErrorMessage = (error_message) => {
    const success_alert = document.querySelector("#create-issue-form .status-messages > .alert-success");
    const danger_alert = document.querySelector("#create-issue-form .status-messages > .alert-danger");
    success_alert.style.display = "none";
    danger_alert.style.display = "";
    danger_alert.textContent = error_message;
}

$('#submit-issue-modal').on('shown.bs.modal', () => {
    $('#submit-issue-modal input[name=title]').focus()
});
