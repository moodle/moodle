/**
* Stop video js.
* 
*/
document.addEventListener(
    "click",
    function (event) {
        "use strict";
        // If user either clicks X button OR clicks outside the modal window, then close modal by calling closeModal()
        if (event.target.closest(".block12modal")) {
            stopVideos();
        }
        if (event.target.closest(".block051modal")) {
            stopVideos();
        }
    },
    false
)
function stopVideos() {
    "use strict";
    var videos = document.querySelectorAll('iframe, video');
    Array.prototype.forEach.call(videos, function (video) {
        if (video.tagName.toLowerCase() === 'video') {
            video.pause();
        } else {
            var src = video.src;
            video.src = src;
        }
    });
};