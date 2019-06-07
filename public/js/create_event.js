const MAPS_URL_FOR_REPLACING = "https://maps.google.com/?q=REPLACE_LAT,REPLACE_LONG&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed";
const DELAY_UNTIL_LAT_LONG_REQUEST_MS = 1250;
const map_iframe_wrapper = document.querySelector("#map_wrapper");
const latitude_input = document.querySelector("input[name='latitude']");
const longitude_input = document.querySelector("input[name='longitude']");

let timeout_to_request_lat_long = null;
let location_value = null;

const requestLatLong = () => {
    // console.log("Request called");

    const headers = new Headers({
        "Accept"       : "application/json",
        "Content-Type" : "application/json",
        "User-Agent"   : "LBAW1819/Eventually-debug"
    });

    fetch(`https://nominatim.openstreetmap.org/search/${location_value}?format=json&limit=1&addressdetails=0&extratags=0&namedetails=0`, {
        headers
    })
    .then(res => res.json())
    .then(data => {
        latitude_input.value = data[0].lat;
        longitude_input.value = data[0].lon;
        // console.log("Data", data);
        updateMapIframe(data[0].lat, data[0].lon);
    })
};

const updateMapIframe = (lat, long) => {
    const src = MAPS_URL_FOR_REPLACING.replace("REPLACE_LAT", lat).replace("REPLACE_LONG", long);

    map_iframe_wrapper.innerHTML = `
        <iframe class="event-map"
            src="${src}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
        </iframe>
    `;
}

document.querySelector("input[name='location']").addEventListener('input', e => {
    // Using timeouts to ensure that there is not an overload of requests to the service

    clearTimeout(timeout_to_request_lat_long);

    location_value = e.target.value;

    if (!location_value) {
        latitude_input.value = longitude_input.value = "";
        console.log("Location reset")
        return;
    }

    timeout_to_request_lat_long = setTimeout(requestLatLong, DELAY_UNTIL_LAT_LONG_REQUEST_MS);
});

const addedTagsElem = document.querySelector("#added-tags");
const tagsInput = document.querySelector("#add-tag-input");
const addTagBtn = document.querySelector("#add-tag-button");
const addedTagsStringElem = document.querySelector("#added-tags-string");

Array.from(addedTagsElem.children).forEach(tag => {
    tag.addEventListener('click', (e) => {
        addedTagsElem.removeChild(tag);
        e.preventDefault();
        refreshAddedTags();
    }, true);
});

const refreshAddedTags = () => {
    addedTagsStringElem.value = JSON.stringify(
        Array.from(addedTagsElem.children).reduce(
            (arr, tagElem) => tagElem ? [...arr, tagElem.value] : arr, []));
}

const addTag = () => {
    let newTag = document.createElement('BUTTON');
    let tag = tagsInput.value.replace(/^[\s,]+|[\s,]+$/gm,'');
    if(tag !== ""){
        newTag.innerText = tag + " ×";
        newTag.value = tag;
        newTag.classList.add("added-tag", "btn", "btn-light");
        newTag.addEventListener('click', ()=> addedTagsElem.removeChild(newTag) && refreshAddedTags());
        addedTagsElem.appendChild(newTag);
        refreshAddedTags();
    }
    tagsInput.value = "";
}

tagsInput.addEventListener("keyup", (e) =>  e.keyCode === 188 && addTag() && e.stopPropagation());
addTagBtn.addEventListener("click", () => addTag());    