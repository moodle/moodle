/**
 * When embedded the communicator helps talk to the parent page.
 * This is a copy of the H5P.communicator, which we need to communicate in this context
 *
 * @type {H5PEmbedCommunicator}
 */
H5PEmbedCommunicator = (function () {
    /**
     * @class
     * @private
     */
    function Communicator() {
        var self = this;

        // Maps actions to functions.
        var actionHandlers = {};

        // Register message listener.
        window.addEventListener('message', function receiveMessage(event) {
            if (window.parent !== event.source || event.data.context !== 'h5p') {
                return; // Only handle messages from parent and in the correct context.
            }

            if (actionHandlers[event.data.action] !== undefined) {
                actionHandlers[event.data.action](event.data);
            }
        }, false);

        /**
         * Register action listener.
         *
         * @param {string} action What you are waiting for
         * @param {function} handler What you want done
         */
        self.on = function (action, handler) {
            actionHandlers[action] = handler;
        };

        /**
         * Send a message to the all mighty father.
         *
         * @param {string} action
         * @param {Object} [data] payload
         */
        self.send = function (action, data) {
            if (data === undefined) {
                data = {};
            }
            data.context = 'h5p';
            data.action = action;

            // Parent origin can be anything.
            window.parent.postMessage(data, '*');
        };
    }

    return (window.postMessage && window.addEventListener ? new Communicator() : undefined);
})();

document.onreadystatechange = function () {
    // Wait for instances to be initialize.
    if (document.readyState !== 'complete') {
        return;
    }

    // Check for H5P iFrame.
    var iFrame = document.querySelector('.h5p-iframe');
    if (!iFrame || !iFrame.contentWindow) {
        return;
    }
    var H5P = iFrame.contentWindow.H5P;

    // Check for H5P instances.
    if (!H5P || !H5P.instances || !H5P.instances[0]) {
        return;
    }

    var resizeDelay;
    var instance = H5P.instances[0];
    var parentIsFriendly = false;

    // Handle that the resizer is loaded after the iframe.
    H5PEmbedCommunicator.on('ready', function () {
        H5PEmbedCommunicator.send('hello');
    });

    // Handle hello message from our parent window.
    H5PEmbedCommunicator.on('hello', function () {
        // Initial setup/handshake is done.
        parentIsFriendly = true;

        // Make iframe responsive.
        iFrame.contentDocument.body.style.height = 'auto';

        // Hide scrollbars for correct size.
        iFrame.contentDocument.body.style.overflow = 'hidden';

        document.body.classList.add('h5p-resizing');

        // Content need to be resized to fit the new iframe size.
        H5P.trigger(instance, 'resize');
    });

    // When resize has been prepared tell parent window to resize.
    H5PEmbedCommunicator.on('resizePrepared', function (data) {
        H5PEmbedCommunicator.send('resize', {
            scrollHeight: iFrame.contentDocument.body.scrollHeight
        });
    });

    H5PEmbedCommunicator.on('resize', function () {
        H5P.trigger(instance, 'resize');
    });

    H5P.on(instance, 'resize', function () {
        if (H5P.isFullscreen) {
            return; // Skip iframe resize.
        }

        // Use a delay to make sure iframe is resized to the correct size.
        clearTimeout(resizeDelay);
        resizeDelay = setTimeout(function () {
            // Only resize if the iframe can be resized.
            if (parentIsFriendly) {
                H5PEmbedCommunicator.send('prepareResize', {
                    scrollHeight: iFrame.contentDocument.body.scrollHeight,
                    clientHeight: iFrame.contentDocument.body.clientHeight
                });
            }
            else {
                H5PEmbedCommunicator.send('hello');
            }
        }, 0);
    });

    // Trigger initial resize for instance.
    H5P.trigger(instance, 'resize');
};
