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
 * @author    Daniel Cifuentes <daniel.cifuentes@openlms.net>
 * @copyright Copyright (c) 2024 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module theme_snap/coursetools_blocks_management
 */
define(['core/ajax'],
    function(ajax) {
        var self = this;

        /**
         * Modifies block editing actions so that they are redirected to the Course Dashboard when triggered.
         * @param {object} courseConfig
         */
        self.init = function(courseConfig) {

            const blockActions = document.querySelectorAll(
                '.block-controls .editing_hide, ' +
                '.block-controls .editing_show, ' +
                '.block-controls .editing_delete'
            );
            blockActions.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = button.getAttribute('href');
                    const urlParams = new URLSearchParams(href.split('?')[1]);
                    const bui_hideid = urlParams.get('bui_hideid');
                    const bui_showid = urlParams.get('bui_showid');
                    const bui_deleteid = urlParams.get('bui_deleteid');
                    // The button to hide the block.
                    if (bui_hideid !== null) {
                        self.manageAjaxCall('bui_hideid', bui_hideid, courseConfig);
                    }
                    // The button to show the block.
                    if (bui_showid !== null) {
                        self.manageAjaxCall('bui_showid', bui_showid, courseConfig);
                    }
                    // The button to delete the block.
                    if (bui_deleteid !== null) {
                        // There is a confirmation modal that is displayed once the user clicks on the delete button.
                        // For this reason, we need to observe when that modal appears in order to modify the delete
                        // action.
                        const observer = new MutationObserver(
                            (mutationsList, observer) => {
                            for (const mutation of mutationsList) {
                                if (mutation.type === 'childList') {
                                    const confirmButton = document.querySelector('.modal [data-action="save"]');
                                    if (confirmButton) {
                                        observer.disconnect();
                                        confirmButton.addEventListener('click', function(event) {
                                            event.stopPropagation();
                                            confirmButton.removeAttribute('data-action');
                                            self.manageAjaxCall('bui_deleteid', bui_deleteid, courseConfig);
                                        });
                                    }
                                }
                            }
                        });
                        observer.observe(document.body, {childList: true, subtree: true});
                    }
                });
            });
        };
        /**
         * Manages the AJAX call responsible for modifying the block editing action.
         * @param {string} action
         * @param {int} id
         * @param {object} courseConfig
         */
        self.manageAjaxCall = function(action, id, courseConfig) {
            var ajaxParams = {};
            ajaxParams.action = action;
            ajaxParams.id = id;
            ajaxParams.courseid = courseConfig.id;
            ajax.call([{
                methodname: "theme_snap_coursetools_block_actions",
                args: {params: ajaxParams},
                done: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
            }]);
        };
        return {
            init: self.init
        };
    }
);
