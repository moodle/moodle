/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module theme_snap/dndupload-lazy
 */
define(['jquery', 'core/yui', 'theme_snap/util'],
    function($, YUI, util) {
        var self = this;

        self.dndupload = null;

        /**
         * Initialising function.
         * @param {array} options The course dnd options
         */
        self.init = function(options) {
            util.whenTrue(
                function() {
                    return typeof (M.course_dndupload) !== 'undefined';
                },
                function() {
                    self.dndupload = M.course_dndupload;
                    // Adding YUI 3 requirements here.
                    YUI.use('node', 'event', 'json', 'anim', 'moodle-core-notification-alert', function(Y) {
                        self.decorateCourseDNDUpload(Y, options);
                    });

                }, true);
        };

        /**
         * Decorates the file handlers of course drag and drop widget.
         * @param {object} Y
         * @param {object} options
         */
        self.decorateCourseDNDUpload = function(Y, options) {
            // Add the statically added file handlers.
            /* global themeSnapCourseFileHandlers */
            if (typeof(themeSnapCourseFileHandlers) != "undefined" && themeSnapCourseFileHandlers) {
                options.handlers = themeSnapCourseFileHandlers;
            } else {
                options.handlers = {};
            }

            // Rebuild file handlers without annoying label handler.
            var extensions = ['gif', 'jpe', 'jpg', 'jpeg', 'png', 'svg', 'svgz', 'webp', 'mp3'];
            var newfilehandlers = [];
            for (var h in options.handlers.filehandlers) {
                var handler = options.handlers.filehandlers[h];
                if (handler && handler.module) {
                    // Prevent label img dialog from showing.
                    if (handler.module !== 'label' || extensions.indexOf(handler.extension.toLowerCase()) === -1) {
                        newfilehandlers.push(handler);
                    }
                }
            }
            options.handlers.filehandlers = newfilehandlers;

            self.dndupload.init(Y, options);

            $('.js-snap-drop-file').change(function() {
                var sectionnumber = $(this).attr('id').replace('snap-drop-file-', '');
                var section = Y.one('#section-' + sectionnumber);

                var file;
                for (var i = 0; i < this.files.length; i++) {
                    // Get file and trigger upload.
                    file = this.files.item(i);
                    self.dndupload.handle_file(file, section, sectionnumber);

                }
            });

            var targetAnimImgs = document.querySelectorAll('div.content');
            var configAnimImgs = {childList: true, subtree: true};
            var observerAnimImgs = new MutationObserver(function() {
                util.processAnimatedImages();
            });
            targetAnimImgs.forEach(target => observerAnimImgs.observe(target, configAnimImgs));
        };

        return {
            init: self.init
        };
    }
);
