const addEventListeners = () => {
    const create_category_form = document.querySelector("#create-category-form");
    create_category_form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!create_category_form.checkValidity()) {
            return;
        }
        
        const name_node = document.querySelector("#create-category-modal input[name=name]");
        const name = name_node.value;
        createCategory(name);
        $('#create-category-modal').modal('hide');
    });

    const rename_category_modal = document.querySelector("#rename-category-modal");
    document.querySelectorAll("#categories-list tr").forEach((elem) => {
        const button = elem.querySelector("button.rename-category-button");
        const category_id = elem.querySelector("td:nth-child(1)").textContent;
        const category_name = elem.querySelector("td:nth-child(2)").textContent;

        button.addEventListener("click", () => {
            $('#rename-category-modal').modal();
            const modal_title = rename_category_modal.querySelector(".custom-modal-title");
            modal_title.textContent = `Rename '${category_name}' category`;
            modal_title.setAttribute("data-category-id", category_id);
            modal_title.setAttribute("data-category-name", category_name);
        })
    });

    const rename_category_form = document.querySelector("#rename-category-form");
    rename_category_form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!rename_category_form.checkValidity()) {
            return;
        }
        
        const category_id = rename_category_modal.querySelector(".custom-modal-title").getAttribute("data-category-id");
        const category_old_name = rename_category_modal.querySelector(".custom-modal-title").getAttribute("data-category-name");
        const category_name = rename_category_modal.querySelector("input[name=name]").value;

        renameCategory(category_id, category_name, category_old_name);
        document.querySelector("#rename-category-modal input[name=name]").value = "";
        $('#rename-category-modal').modal('hide');
    });
};

const createCategory = (name) => {
    if (name.length > 20) {
        displayCategoryErrorMessage("Failed to create category. The category name must be, at most, 20 characters long.");
        return;
    }

    fetch('/event/category', {
        method: 'POST',
        body: JSON.stringify({
            name
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            res.json()
            .then(json => {
                const success_alert = document.querySelector("#category-table .status-messages > .alert-success");
                const danger_alert = document.querySelector("#category-table .status-messages > .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.textContent = `Category '${name}' created successfully!`;

                const num_categories = document.querySelector("#categories-list").childElementCount;
                if (num_categories < 10) {
                    createCategoryDOMNode(name, json.category_id);
                }
            });
        } else {
            res.json()
            .then(json => {
                displayCategoryErrorMessage(`Failed to create category. ${json.message}`);
            });
        }

        const create_category_form = document.querySelector("#create-category-form");
        create_category_form.classList.remove("was-validated");
        create_category_form.reset();
    });
}

const createCategoryDOMNode = (name, id) => {
    const category_item = document.createElement("tr");
    const td1 = document.createElement("td");
    const td2 = document.createElement("td");
    const td3 = document.createElement("td");
    const td4 = document.querySelector("button.rename-category-button").parentNode.cloneNode(true);
    td1.textContent = id;
    td2.textContent = name;
    td3.textContent = "0";
    category_item.appendChild(td1);
    category_item.appendChild(td2);
    category_item.appendChild(td3);
    category_item.appendChild(td4);

    document.querySelector("#categories-list").appendChild(category_item);
}

const renameCategory = (id, name, old_name) => {
    if (name.length > 20) {
        displayCategoryErrorMessage("Failed to create category. The category name must be, at most, 20 characters long.");
        return;
    }

    fetch('/event/category/rename', {
        method: 'PUT',
        body: JSON.stringify({
            id,
            name
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 200) {
            res.json()
            .then(json => {
                const success_alert = document.querySelector("#category-table .status-messages > .alert-success");
                const danger_alert = document.querySelector("#category-table .status-messages > .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.textContent = `Category name updated from '${old_name}' to '${name}' successfully!`;
                const category_node = document.querySelector(`td[data-category-name='${old_name}']`);
                category_node.textContent = name;
            });
        } else {
            res.json()
            .then(json => {
                displayCategoryErrorMessage(`Failed to rename category. ${json.message}`);
            });
        }

        const rename_category_form = document.querySelector("#rename-category-form");
        rename_category_form.classList.remove("was-validated");
        rename_category_form.reset();
    });
}

const displayCategoryErrorMessage = (error_message) => {
    const success_alert = document.querySelector("#category-table .status-messages > .alert-success");
    const danger_alert = document.querySelector("#category-table .status-messages > .alert-danger");
    success_alert.style.display = "none";
    danger_alert.style.display = "";
    danger_alert.textContent = error_message;
}

$('#rename-category-modal').on('shown.bs.modal', () => {
    $('#rename-category-modal input[name=name]').focus()
});

$('#create-category-modal').on('shown.bs.modal', () => {
    $('#create-category-modal input[name=name]').focus()
});

addEventListeners();