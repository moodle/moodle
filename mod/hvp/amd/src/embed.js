// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/* eslint-disable no-undef, no-console, no-unused-vars, max-len */
define(['jquery', 'mod_hvp/communicator'], function($, H5PEmbedCommunicator) {

    // Wait for instances to be initialize.
    $(document).ready(function() {
        $('.h5p-iframe').ready(function() {
            initEmbedCommunicator = function() {
                var resizeDelay;
                var instance = H5P.instances[0];
                var parentIsFriendly = false;

                // Handle that the resizer is loaded after the iframe.
                H5PEmbedCommunicator.on('ready', function() {
                    H5PEmbedCommunicator.send('hello');
                });

                // Handle hello message from our parent window.
                H5PEmbedCommunicator.on('hello', function() {
                    // Initial setup/handshake is done.
                    parentIsFriendly = true;

                    // Hide scrollbars for correct size.
                    iFrame.contentDocument.body.style.overflow = 'hidden';

                    document.body.classList.add('h5p-resizing');

                    // Content need to be resized to fit the new iframe size.
                    H5P.trigger(instance, 'resize');
                });

                // When resize has been prepared tell parent window to resize.
                H5PEmbedCommunicator.on('resizePrepared', function() {
                    H5PEmbedCommunicator.send('resize', {
                        scrollHeight: iFrame.contentDocument.body.scrollHeight
                    });
                });

                H5PEmbedCommunicator.on('resize', function() {
                    H5P.trigger(instance, 'resize');
                });

                H5P.on(instance, 'resize', function() {
                    if (H5P.isFullscreen) {
                        return; // Skip iframe resize.
                    }

                    // Use a delay to make sure iframe is resized to the correct size.
                    clearTimeout(resizeDelay);
                    resizeDelay = setTimeout(function() {
                        // Only resize if the iframe can be resized.
                        if (parentIsFriendly) {
                            H5PEmbedCommunicator.send('prepareResize',
                                {
                                    scrollHeight: iFrame.contentDocument.body.scrollHeight,
                                    clientHeight: iFrame.contentDocument.body.clientHeight
                                }
                            );
                        } else {
                            H5PEmbedCommunicator.send('hello');
                        }
                    }, 0);
                });

                // Trigger initial resize for instance.
                H5P.trigger(instance, 'resize');
            };
            var iFrame = document.querySelector('.h5p-iframe');
            var H5P = iFrame.contentWindow.H5P;
            // Check for H5P instances.
            if (!H5P || !H5P.instances || !H5P.instances[0]) {
                console.warn("H5P embed.js: ACK! Embedded H5P.instances[0] in lowest iframe is not set up yet. Waiting for 'initialized' event");
                window.H5P.externalDispatcher.on('initialized', function(event) {
                    console.log("H5P embed.js: 'initialized' event received");
                    H5P = iFrame.contentWindow.H5P;
                    initEmbedCommunicator();
                });
            } else {
                initEmbedCommunicator();
            }
        });
    });

});