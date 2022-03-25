document.addEventListener('DOMContentLoaded', function(){
    const buttonOpenModal = document.getElementsByClassName("button");
    buttonOpenModal[0].onclick = function (e) {
        e.preventDefault();
        const modal = document.getElementsByClassName("modal-fade");
        if (modal[0].style.display === "" || modal[0].style.display === "none") {
            modal[0].style.display = "block";
        }
    };

    const buttonCloseModal = document.getElementsByClassName("modal-close");
    buttonCloseModal[0].onclick = function (e) {
        e.preventDefault();
        const modal = document.getElementsByClassName("modal-fade");
        if (modal[0].style.display === "block") {
            modal[0].style.display = "none";
        }
    };

    const modalFade = document.getElementsByClassName("modal-fade");
    modalFade[0].onclick = function (e) {
        e.preventDefault();
        const modal = document.getElementsByClassName("modal-fade");
        if (modal[0].style.display === "block") {
            modal[0].style.display = "none";
        }
    };

    const modalWindow = document.getElementsByClassName("modal-window");
    modalWindow[0].onclick = function (e) {
        e.stopPropagation();
    };
});