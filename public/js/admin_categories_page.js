const addEventListeners = () => {
    const create_category_button = document.querySelector("#create-category-modal button.create-category");
    create_category_button.addEventListener("click", () => {
        const name_node = document.querySelector("#create-category-modal input[name=name]");
        const name = name_node.value;
        createCategory(name);
        $('#create-category-modal').modal('hide');
        name_node.value = "";
    })
};

const createCategory = (name) => {
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
                const success_alert = document.querySelector("#status_messages > .alert-success");
                success_alert.style.display = "";
                success_alert.textContent = `Category '${name}' created successfully!`;

                const num_categories = document.querySelector("#categories-list").childElementCount;
                if (num_categories < 10) {
                    createCategoryDOMNode(name, json.element_id);
                }
            });
        } else {
            res.json()
            .then(json => {
                const success_alert = document.querySelector("#status_messages > .alert-danger");
                success_alert.style.display = "";
                success_alert.textContent = `Failed to create category. ${json.message}`;
            });
        }
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

addEventListeners();