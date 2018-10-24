(function() {
    window.addEventListener('message', function (e) {
        try {
            var postMessageData = e.data;
            if (postMessageData.subject === "lti.frameResize") {
                var height = postMessageData.height;
                var lti = document.querySelector("#contentframe");
                lti.style.height = height + "px";
            }
        }
        catch (ex) {
            console.error("encountered error in kms communication", ex);
        }
    });
}());