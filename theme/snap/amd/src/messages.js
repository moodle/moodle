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
 * @author    Juan Ibarra juan.ibarra@openlms.net
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JS code to manage hide/show of full width messages drawer.
 */
define(['jquery'],
    function($) {
        // Array to control which popovers are open.
        var openedpopovers = [];
        // Maximum size in pixels to consider a mobile screen
        var maxWidth = 560;

        return {
            init: function() {
                // Listener for the admin block.
                if ($('.preferences-page-container').length === 0 && $('.message-app.main').length === 0 &&
                        ($('#page-message-edit').length != 0 || $('#page-message-index').length != 0)) {
                    var drawermessage = $('.drawer .message-app');
                    drawermessage.css('visibility', 'visible');
                    drawermessage.animate({width: '100%'}, 0);
                    document.addEventListener("messages-drawer:toggle", function () {
                        if ($('#page-message-edit').length || $('#page-message-index').length) {
                            if ($('.block_settings').hasClass('state-visible')) {
                                if ($(window).width() < maxWidth) {
                                    $('.drawer .message-app').hide();
                                } else {
                                    $('.drawer .message-app').animate({width: '50%'}, 0);
                                }
                            } else {
                                if ($(window).width() < maxWidth) {
                                    $('.drawer .message-app').show();
                                } else {
                                    $('.drawer .message-app').animate({width: '100%'}, 0);
                                }
                            }
                        }
                    });
                    // Listeners for popovers.
                    var popover = $('div.popover-region');
                    popover.on('popoverregion:menuopened', function () {
                        if ($('#page-message-edit').length || $('#page-message-index').length) {
                            var popovername = $(this).attr("id");
                            if (openedpopovers.indexOf(popovername) == -1) {
                                openedpopovers.push(popovername);
                            }
                            // If there are open popovers, hide message-drawer.
                            if (openedpopovers.length > 0) {
                                if ($(window).width() < maxWidth) {
                                    $('.drawer .message-app').hide();
                                } else {
                                    $('.drawer .message-app').animate({width: '50%'}, 0);
                                }
                            }
                        }
                    }).bind();
                    popover.on('popoverregion:menuclosed', function () {
                        if ($('#page-message-edit').length || $('#page-message-index').length) {
                            var popovername = $(this).attr("id");
                            var index = openedpopovers.indexOf(popovername);
                            openedpopovers.splice(index, 1);
                            // Only open drawer when there are no opened popovers.
                            if (openedpopovers.length == 0) {
                                if ($(window).width() < maxWidth) {
                                    $('.drawer .message-app').show();
                                } else {
                                    $('.drawer .message-app').animate({width: '100%'}, 0);
                                }
                            }
                        }
                    }).bind();
                }
            },
        };
    }
);
