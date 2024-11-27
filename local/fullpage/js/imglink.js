!function() {
    "use strict";
    var imgnewlink = document.getElementById("imgnewlink").innerText;
    var images;
    images = document.querySelectorAll("#fullpage-advanced img");
    for (var i = 0; i < images.length; i++) {
        var oldVal = images[i].getAttribute('src');
        images[i].setAttribute('src', imgnewlink + oldVal);
    }
}()
