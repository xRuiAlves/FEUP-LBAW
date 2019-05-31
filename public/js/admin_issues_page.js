const addEventListeners = () => {
    const issue_modal = document.querySelector("#solve-issue-modal");
    document.querySelectorAll("#issue-table .issue-header").forEach((elem) => {
        const is_solved = elem.getAttribute("data-issue-solved");
        if (is_solved == 0) {
            const issue_id = elem.getAttribute("data-issue-id");
            const creator_id = elem.getAttribute("data-issue-creator-id");
            const button = elem.querySelector("button.solve-issue-pop-modal");
    
            button.addEventListener("click", (e) => {
                e.stopPropagation();
                $('#solve-issue-modal').modal();
                const modal_title = issue_modal.querySelector(".custom-modal-title");
                modal_title.textContent = `Solve Issue #${issue_id}`;
                modal_title.setAttribute("data-issue-id", issue_id);
                modal_title.setAttribute("data-issue-creator-id", creator_id);
            });
        }
    })

    issue_modal.querySelector("button.solve-issue").addEventListener("click", (e) => {
        const issue_id = issue_modal.querySelector(".custom-modal-title").getAttribute("data-issue-id");
        const creator_id = issue_modal.querySelector(".custom-modal-title").getAttribute("data-issue-creator-id");
        const solver_id = document.querySelector("#admin-dashboard").getAttribute("data-admin-id");
        const content_field = issue_modal.querySelector("textarea[name=content]");
        const content = content_field.value;

        solveIssue(issue_id, creator_id, solver_id, content);
        content_field.value = "";
        $('#solve-issue-modal').modal('hide');
    });
};

const solveIssue = (issue_id, creator_id, solver_id, content) => {
    fetch('/api/issue/solve', {
        method: 'PUT',
        body: JSON.stringify({
            issue_id,
            creator_id,
            solver_id,
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
            const success_alert = document.querySelector("#status_messages > .alert-success");
            success_alert.style.display = "";
            success_alert.textContent = `Issue #${issue_id} was solved successfully!`;

            const issue_solved_text = document.querySelector(`#issue-table .issue-header[data-issue-id="${issue_id}"] button.solve-issue-pop-modal`);
            issue_solved_text.textContent = "Solved";
            issue_solved_text.classList.add("solved-issue");
        } else {
            const success_alert = document.querySelector("#status_messages > .alert-danger");
            success_alert.style.display = "";
            success_alert.textContent = `Failed to mark Issue #${issue_id} as solved.`;
        }
    });
}

addEventListeners();