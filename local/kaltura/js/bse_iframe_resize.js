(function() {
    window.addEventListener('message', function (e) {
        try {
            var postMessageData = e.data;
            if (postMessageData.subject === "lti.frameResize") {
                var height = postMessageData.height;
                if (height.toString().indexOf("%") === -1) {
                    height += "px";
                }
                $('.kaltura-player-iframe').height(height);
            }
        }
        catch (ex) {
            console.error("encountered error in kms communication", ex);
        }
    });
}());
