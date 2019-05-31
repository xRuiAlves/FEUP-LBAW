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
    });
};

const solveIssue = (issue_id, creator_id, solver_id, content) => {
    $('#solve-issue-modal').modal('hide');

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
            location.reload();
        } else {
            // TODO
        }
    });
}

addEventListeners();