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
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Snap feeds side menu.
 */
define(['jquery', 'core/log','theme_snap/util', 'theme_snap/ajax_notification'],
    function($, log, util, ajaxNotify) {
        /**
         * Load Snap feeds content when advanced feeds are disabled.
         * @param {boolean} urlParameter
         *
         */
        var update = function(urlParameter) {
            $(document).ready(function() {
                var loadAjaxInfo = function(type) {
                    // Target for data to be displayed on screen.
                    var container = $('#snap-feeds-menu-' + type);
                    var mobileContainer = $('#snap-feeds-section-' + type);
                    if ($(container).length) {
                        var cacheKey = M.cfg.sesskey + 'snap-feeds-menu-' + type;
                        try {
                            // Display old content while waiting
                            if (util.supportsSessionStorage() && window.sessionStorage[cacheKey]) {
                                log.info('using locally stored ' + type);
                                var html = window.sessionStorage[cacheKey];
                                $(container).html(html);
                                $(mobileContainer).html(html);
                            }
                            log.info('fetching ' + type);
                            $.ajax({
                                type: "GET",
                                async: true,
                                url: M.cfg.wwwroot + '/theme/snap/rest.php?action=get_' + type + '&contextid=' + M.cfg.context,
                                success: function(data) {
                                    ajaxNotify.ifErrorShowBestMsg(data).done(function(errorShown) {
                                        if (errorShown) {
                                            return;
                                        } else {
                                            // No errors, update sesion storage.
                                            log.info('fetched ' + type);
                                            if (util.supportsSessionStorage() && typeof (data.html) != 'undefined') {
                                                window.sessionStorage[cacheKey] = data.html;
                                            }
                                            // Note: we can't use .data because that does not manipulate the dom, we need the data
                                            // attribute populated immediately so things like behat can utilise it.
                                            // .data just sets the value in memory, not the dom.
                                            $(container).attr('data-content-loaded', '1');
                                            $(mobileContainer).attr('data-content-loaded', '1');
                                            if (urlParameter) {
                                                let modifiedHTML = modifyHTML(data.html);
                                                $(container).html(modifiedHTML);
                                                $(mobileContainer).html(modifiedHTML);
                                            } else {
                                                $(container).html(data.html);
                                                $(mobileContainer).html(data.html);
                                            }
                                        }
                                    });
                                }
                            });
                        } catch (err) {
                            sessionStorage.clear();
                            log.error(err);
                        }
                    }
                };

                loadAjaxInfo('deadlines');
                loadAjaxInfo('graded');
                loadAjaxInfo('grading');
                loadAjaxInfo('messages');
                loadAjaxInfo('forumposts');
            });
        };

        /**
         * Modify the HTML to add the snapfeedsclicked label in the URL.
         * @param {string} html
         */
        var modifyHTML = function(html) {
            const beforeURLPattern = "<div class=\"snap-media-body\">\n<a href=\"";
            const afterURLPattern = "\"><h3>";
            const regex = new RegExp(beforeURLPattern + "(.*)" + afterURLPattern, "g");
            const results = [...html.matchAll(regex)];
            let modifiedHTML = html;
            if (results.length > 0) {
                results.forEach(result => {
                    const originalURL = result[1];
                    const modifiedURL = originalURL + "&snapfeedsclicked=on";
                    modifiedHTML = modifiedHTML.replace(originalURL, modifiedURL);
                });
            } else {
                return html;
            }
            return modifiedHTML;
        };

        /**
         * Apply Snap feeds side menu listeners.
         * @param {boolean} urlParameter
         */
        var applyListeners = function(urlParameter) {
            // On clicking snap feeds side menu trigger.
            $(document).on("click", ".js-snap-feeds-side-menu-trigger", function(event) {
                if ($('#snap_feeds_side_menu').is(':visible')) {
                    update(urlParameter);
                }
                event.preventDefault();

            });

            // Mobile menu tabs
            $('.snap-feeds-mobile-menu .nav-item').on('click', function() {
                $('.snap-feeds-mobile-menu .nav-item').removeClass('active');
                $(this).addClass('active');

                $('.snap-feeds-mobile-menu .snap-feeds-mobile-sections > div').removeClass('active');
                let target = $(this).data('target');
                $('.snap-feeds-mobile-menu #snap-feeds-section-' + target).addClass('active');
            });
        };

        var init = function(urlParameter) {
            applyListeners(urlParameter);
        };

        return {
            init: init,
        };
    }
);