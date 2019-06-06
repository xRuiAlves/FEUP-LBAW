let navbar = document.getElementById("navbar");
navbar.style.transform = "scale(1,0)";

window.addEventListener('scroll', () => {
    if(navbar.getBoundingClientRect().top <= 0){
        navbar.style.transform = "scale(1,1)";
    }else{
        navbar.style.transform = "scale(1,0)";
    }
});

const search_events_home_form = document.querySelector("#search-events-home-form");
document.querySelector("#search-events-home-form i.fa-search").addEventListener("click", (e) => {
    search_events_home_form.submit();
});