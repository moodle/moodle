var maxHeight = 550;
var maxListHeight = 120;

function init() {
    if (document.getElementById("singleimage")) single();
    /*else if (document.getElementById("pictures")) list();*/
}

function list() {
    imageDivs = document.getElementsByName("listimage");
    for (i=0; i < imageDivs.length; i++) {
        currentHeight = imageDivs[i].offsetHeight;
        currentWidth = imageDivs[i].offsetWidth;

        if (currentHeight > maxListHeight) {
            ratio = maxListHeight / currentHeight;
            imageDivs[i].style.width = (currentWidth*ratio) + 'px';
            imageDivs[i].style.height = (currentHeight*ratio) + 'px';
            imageDivs[i].firstChild.style.height = '100%';
            imageDivs[i].firstChild.style.width = '100%';
        }
    }
}

function single() {
    var imageDiv = document.getElementById("singleimage");

    if (imageDiv) {
        currentHeight = imageDiv.offsetHeight;
        currentWidth  = imageDiv.offsetWidth;

        if (currentHeight > maxHeight) {
            ratio = maxHeight / currentHeight;
            imageDiv.style.width = (currentWidth*ratio) + 'px';
            imageDiv.style.height = (currentHeight*ratio) + 'px';

        }
    }
}

window.onload = init;