var sclicked = false;

var element = document.getElementById("nodbl");
if (typeof(element) != 'undefined' && element != null) {
    document.getElementById("nodbl").addEventListener("click", function(event) {
        // Does this even do anything???
    });
}

function processClick() {
    if (sclicked == false) {
        sclicked = true;
    } else if (sclicked == true) {
        event.preventDefault()
    }
}

