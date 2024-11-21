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
 * Various actions on sections like update image, delete image, ...
 *
 * @module     format_softcourse/actions
 * @copyright  2019 Pimenko <contact@pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'theme_boost/bootstrap/tooltip'], function($, ajax) {
    /**
     * Updating of an image of a section.
     */
    let updateImageSection = function() {
        $('.section-file').on('change', function(event) {
            let sectionid = event.target.dataset.sectionid;
            let courseid = event.target.dataset.courseid;
            if (courseid) {
                let file = event.target.files[0]; // Get only 1st file.
                let filedata = null;
                // Check if img.
                if (!file.type.match('image.*')) {
                    return;
                }
                let that = this;
                let reader = new FileReader();
                reader.onload = (function(file) {
                    return function(e) {
                        filedata = e.target.result;
                        $(that).parent().parent().css('background-image', 'url(' + filedata + ')');
                        $(that).parent().parent().addClass('not-empty');
                        let imagedata = filedata.split('base64,')[1];
                        let filename = file.name;
                        ajax.call([
                            {
                                methodname: 'format_softcourse_update_section_image',
                                args: {
                                    courseid: courseid,
                                    sectionid: sectionid,
                                    imagedata: imagedata,
                                    filename: filename
                                },
                                fail: function(response) {
                                    window.console.error(response);
                                }
                            }
                        ], true, true);
                    };
                })(file);
                // Read img.
                reader.readAsDataURL(file);
            }
        });
    };

    let deleteImageSection = function() {
        $('.section-delete-file').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            let sectionid = event.target.dataset.sectionid;
            let courseid = event.target.dataset.courseid;
            $(this).parent().parent().css('background-image', 'none');
            $(this).parent().parent().removeClass('not-empty');
            ajax.call([
                {
                    methodname: 'format_softcourse_delete_section_image',
                    args: {
                        courseid: courseid,
                        sectionid: sectionid
                    },
                    fail: function(response) {
                        window.console.error(response);
                    }
                }
            ], true, true);
        });
    };

    let toggle = function() {
        $('[data-toggle="tooltip"]').tooltip();
    };

    let courseformat = function() {
        M.course = M.course || {};

        M.course.format = M.course.format || {};

        /**
         * Get sections config for this format
         *
         * The section structure is:
         * <ul class="softcourse">
         *  <li class="section">...</li>
         *  <li class="section">...</li>
         *   ...
         * </ul>
         *
         * @return {object} section list configuration
         */
        M.course.format.get_config = function() {
            return {
                container_node : 'ul',
                container_class : 'softcourse',
                section_node : 'li',
                section_class : 'section'
            };
        };

        /**
         * Swap section
         *
         * @param {YUI} Y YUI3 instance
         * @param {string} node1 node to swap to
         * @param {string} node2 node to swap with
         */
        M.course.format.swap_sections = function(Y, node1, node2) {
            let CSS = {
                COURSECONTENT : 'course-content',
                SECTIONADDMENUS : 'section_add_menus'
            };

            let sectionlist = Y.Node.all('.' + CSS.COURSECONTENT + ' ' + M.course.format.get_section_selector(Y));
            // Swap menus.
            sectionlist.item(node1).one('.' + CSS.SECTIONADDMENUS).swap(sectionlist.item(node2).one('.' + CSS.SECTIONADDMENUS));
        };

        /**
         * Process sections after ajax response
         *
         * @param {YUI} Y YUI3 instance
         * @param {array} sectionlist
         * @param {array} response ajax response
         * @param {string} sectionfrom first affected section
         * @param {string} sectionto last affected section
         */
        M.course.format.process_sections = function(Y, sectionlist, response, sectionfrom, sectionto) {
            let CSS = {
                SECTIONNAME : 'sectionname'
            },
            SELECTORS = {
                SECTIONLEFTSIDE : '.left .section-handle .icon'
            };

            if (response.action === 'move') {
                // If moving up swap around 'sectionfrom' and 'sectionto' so the that loop operates.
                if (sectionfrom > sectionto) {
                    let temp = sectionto;
                    sectionto = sectionfrom;
                    sectionfrom = temp;
                }

                // Update titles and move icons in all affected sections.
                let ele, str, stridx, newstr;

                for (let i = sectionfrom; i <= sectionto; i++) {
                    // Update section title.
                    let content = Y.Node.create('<span>' + response.sectiontitles[i] + '</span>');
                    sectionlist.item(i).all('.' + CSS.SECTIONNAME).setHTML(content);
                    // Update move icon.
                    ele = sectionlist.item(i).one(SELECTORS.SECTIONLEFTSIDE);
                    str = ele.getAttribute('alt');
                    stridx = str.lastIndexOf(' ');
                    newstr = str.substr(0, stridx + 1) + i;
                    ele.setAttribute('alt', newstr);
                    ele.setAttribute('title', newstr); // For FireFox as 'alt' is not refreshed.
                }
            }
        };
    };

    return {
        init: function() {
            courseformat();
            updateImageSection();
            deleteImageSection();
            toggle();
        }
    };
});
