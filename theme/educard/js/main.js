/**
* Main js
*/
(function() {
    "use strict";
    /* img link popup */
    // Modal html
    var popup = document.getElementById("educard-popup");
    if (popup) {
        var htmlcode = '<div id="educardmodal" class="educard-modal">';
            htmlcode += '<img class="educard-modal-content" id="educardmodalimg01">';
            htmlcode += '<div id="educardmodalcaption"></div>';
            htmlcode += '</div>';
        //document.write(htmlcode);
        popup.innerHTML = htmlcode;
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var img = document.getElementById("educardmodalimg");
        var modal = document.getElementById("educardmodal");
        var modalImg = document.getElementById("educardmodalimg01");
        var captionText = document.getElementById("educardmodalcaption");
        img.onclick = function(){
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }
        // Close the modal
        modal.onclick = function() { 
            modal.style.display = "none";
        }
    }
})();
