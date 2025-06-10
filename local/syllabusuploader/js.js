var sclicked = false;

document.getElementById("nodbl").addEventListener("click", function(event) {
});

function processClick() {
    if (sclicked == false) {
        sclicked = true;
    } else if (sclicked == true) {
        event.preventDefault()
    }
}

