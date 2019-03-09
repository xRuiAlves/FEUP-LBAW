let navbar = document.getElementById("navbar");

window.addEventListener('scroll', () => {
    if(navbar.getBoundingClientRect().top <= 0){
        navbar.style.transform = "scale(1,1)";
    }else{
        navbar.style.transform = "scale(1,0)";
    }
});