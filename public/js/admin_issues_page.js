const addEventListeners = () => {
    const issue_modal = document.querySelector("#solve-issue-modal");
    document.querySelectorAll("#issue-table .issue-header").forEach((elem) => {
        const issue_id = elem.getAttribute("data-issue-id");
        const button = elem.querySelector("button.solve-issue-pop-modal");
        
        button.addEventListener("click", (e) => {
            e.stopPropagation();
            $('#solve-issue-modal').modal();
            const modal_title = issue_modal.querySelector(".custom-modal-title");
            modal_title.textContent = `Solve Issue #${issue_id}`;
            modal_title.setAttribute("data-issue-id", issue_id);
        });
    })

    issue_modal.querySelector("button.solve-issue").addEventListener("click", (e) => {
        const issue_id = issue_modal.querySelector(".custom-modal-title").getAttribute("data-issue-id");
        const admin_id = document.querySelector("#admin-dashboard").getAttribute("data-admin-id");
        const message = issue_modal.querySelector("textarea[name=content]").value;

        solveIssue(issue_id, admin_id, message);
    });
};

const solveIssue = (issue_id, admin_id, message) => {
    console.log("Issue ID: ", issue_id);
    console.log("Admin ID: ", admin_id);
    console.log("Message: ", message);
}

addEventListeners();