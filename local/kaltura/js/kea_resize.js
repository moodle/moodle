(function() {
    window.addEventListener('message', function (e) {
        try {
            if (e.data && e.data.messageType && e.data.messageType === "kea-ready") {
                // listen to kea bootstrap event so we can
                // resize the iframe accordingly
                var lti = document.querySelector("#contentframe");
                lti.style.height = e.data.height + "px";
            }
        } catch(ex) {
            console.error("encountered error in kms communication", ex);
        }
    });
}());