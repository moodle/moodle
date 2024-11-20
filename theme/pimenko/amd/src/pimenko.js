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

/**
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";
define(['jquery', 'core/ajax'],
    function($, ajax) {

        "use strict";

        let eventlistener = function() {
            $(function() {
                $("#completion-block").tooltip({trigger: "hover"});
            });

            $(".cover_file").on("change", function(event) {
                let courseid = $(this).data("id");
                let filedelete = false;
                if (courseid) {
                    let file = event.target.files[0]; // Get only 1st file.
                    let filedata = null;
                    // Check if img.
                    if (!file.type.match("image.*")) {
                        return;
                    }
                    let reader = new FileReader();

                    reader.onload = (function(file) {
                        return function(e) {

                            // Set page header to use local version for now.
                            filedata = e.target.result;

                            let imagedata = filedata.split("base64,")[1];
                            let filename = file.name;

                            let fileconverted = filedata;
                            // Convert base64 to raw binary data held in a string.
                            let byteString = atob(fileconverted.split(',')[1]);

                            // Separate out the mime component.
                            let mimeString = fileconverted.split(',')[0].split(':')[1].split(';')[0];

                            // Write the bytes of the string to an ArrayBuffer.
                            let arrayBuffer = new ArrayBuffer(byteString.length);
                            let _ia = new Uint8Array(arrayBuffer);
                            for (let i = 0; i < byteString.length; i++) {
                                _ia[i] = byteString.charCodeAt(i);
                            }

                            let dataView = new DataView(arrayBuffer);
                            fileconverted = new Blob([dataView], {type: mimeString});

                            ajax.call([
                                {
                                    methodname: "theme_pimenko_save_cover_file",
                                    args: {
                                        imagedata: imagedata,
                                        courseid: courseid,
                                        filename: filename,
                                        filedelete: filedelete
                                    },
                                    done: function(response) {
                                        if (response.success) {
                                            window.location.reload(true);
                                        }
                                    },
                                    fail: function(response) {
                                        // eslint-disable-next-line no-console
                                        console.log(response);
                                        // eslint-disable-next-line no-console
                                        console.log("fail");
                                    }
                                }
                            ], true, true);
                        };
                    })(file);

                    // Read img.
                    reader.readAsDataURL(file);
                }
            });

            $(".delete-cover").on("click", function() {
                let selector = $(".cover_file");
                let courseid = selector.data("id");
                let filename = selector.data("name");
                let filedelete = true;

                if (courseid) {
                    ajax.call([
                        {
                            methodname: "theme_pimenko_save_cover_file",
                            args: {
                                courseid: courseid,
                                filename: filename,
                                filedelete: filedelete
                            },
                            done: function(response) {
                                if (response.success) {
                                    window.location.reload(true);
                                }
                            },
                            fail: function(response) {
                                // eslint-disable-next-line no-console
                                console.log(response);
                                // eslint-disable-next-line no-console
                                console.log("fail");
                            }
                        }
                    ], true, true);
                }
            });
        };

        /**
         * Add special classes to body depending on scroll position.
         *
         * @method  update
         * @chainable
         */
        let scrollHandler = function() {
            const body = document.querySelector('body');
            const scrollY = getScrollPosition();
            if (scrollY >= window.innerHeight) {
                body.classList.add('scrolled');
            } else {
                body.classList.remove('scrolled');
            }
        };

        let getScrollPosition = function() {
            return window.pageYOffset || document.documentElement.scrollTop;
        };

        return {
            init: function() {
                eventlistener();
                this.scrollY = 0;
                window.addEventListener("scroll", scrollHandler.bind(this));
            }
        };
    });
