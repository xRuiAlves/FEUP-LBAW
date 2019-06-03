const addEventListeners = () => {
    const create_comment_forms = document.querySelectorAll(".create-comment-form");
    create_comment_forms.forEach((create_comment_form) => {
        create_comment_form.addEventListener("submit", (e) => {
            e.preventDefault();
            if (!create_comment_form.checkValidity()) {
                return;
            }

            const post_id = create_comment_form.getAttribute("data-post-id");
            const content = create_comment_form.querySelector("textarea[name=comment]").value;

            createComment(post_id, content);
        });
    });

    const posts = document.querySelectorAll(".post");
    posts.forEach((post) => {
        const post_id = post.getAttribute("data-post-id");
        const rating_node = post.querySelector(".rating");
        const upvote_node = rating_node.querySelector(".upvote");
        const downvote_node = rating_node.querySelector(".downvote");
        const rating_value_node = rating_node.querySelector(".rating-value");
        
        upvote_node.addEventListener("click", (e) => {
            upvotePost(post_id, rating_value_node);
        });
        
        downvote_node.addEventListener("click", (e) => {
            downvotePost(post_id, rating_value_node);
        });
    });
}

const upvotePost = (post_id, rating_value_node) => {
    fetch('/api/post/upvote', {
        method: 'PUT',
        body: JSON.stringify({
            post_id
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        res.json().then(json => {
            rating_value_node.textContent = json.rating
        });
    });
}

const downvotePost = (post_id, rating_value_node) => {
    fetch('/api/post/downvote', {
        method: 'PUT',
        body: JSON.stringify({
            post_id
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        res.json().then(json => {
            rating_value_node.textContent = json.rating
        });
    });
}

const createComment = (post_id, content) => {
    fetch('/api/comment', {
        method: 'POST',
        body: JSON.stringify({
            post_id,
            content
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        const form = document.querySelector(`.create-comment-form[data-post-id='${post_id}']`);
        res.json().then(json => {
            if (res.status === 200) {
                const success_alert = form.querySelector(".status-messages > .alert-success");
                const danger_alert = form.querySelector(".status-messages > .alert-danger");
                success_alert.style.display = "";
                danger_alert.style.display = "none";
                success_alert.textContent = `The issue was successfully submitted to the administration team.`;

                form.reset();
                form.classList.remove("was-validated");

                createCommentDOMNode(post_id, content, json.name, json.formatted_timestamp);
            } else {
                const success_alert = form.querySelector(".status-messages > .alert-success");
                const danger_alert = form.querySelector(".status-messages > .alert-danger");
                success_alert.style.display = "none";
                danger_alert.style.display = "";
                danger_alert.textContent = json.message;
            }
        });
    });
}

const createCommentDOMNode = (post_id, content, name, formatted_timestamp) => {
    const comment_node = document.createElement("div");
    comment_node.classList.add("comment");
    const name_node = document.createElement("div");
    name_node.classList.add("name");
    const content_node = document.createElement("div");
    content_node.classList.add("text");
    const timestamp_node = document.createElement("div");
    timestamp_node.classList.add("date");

    name_node.textContent = name;
    content_node.textContent = content;
    timestamp_node.textContent = `${formatted_timestamp}h`;

    comment_node.appendChild(name_node);
    comment_node.appendChild(timestamp_node);
    comment_node.appendChild(content_node);

    const post_node = document.querySelector(`div[data-post-id='${post_id}']`);
    const comments_list_node = post_node.querySelector(".comments-list");
    const num_comments_node = post_node.querySelector(".num-comments");
    num_comments_node.textContent = parseInt(num_comments_node.textContent) + 1;
    comments_list_node.prepend(comment_node);
}


addEventListeners();