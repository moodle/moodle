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
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Snap Personal menu.
 */
define(['jquery', 'core/log', 'theme_snap/pm_course_cards', 'theme_snap/util', 'theme_snap/ajax_notification'],
    function($, log, courseCards, util, ajaxNotify) {

        /**
         * Personal Menu (courses menu).
         * @constructor
         */
        var PersonalMenu = function() {

            var self = this;

            var redirectToSitePolicy = false;

            var snapFeedsEnabled = false;

            /**
             * Add deadlines, messages, grades & grading,  async'ly to the personal menu
             *
             */
            this.update = function() {

                // If site policy needs acceptance, then don't update, just redirect to site policy!
                if (redirectToSitePolicy) {
                    var redirect = M.cfg.wwwroot + '/user/policy.php';
                    window.location = redirect;
                    return;
                }

                // Update course cards with info.
                courseCards.reqCourseInfo(courseCards.getCourseIds());


                $('#snap-pm').focus();

                /**
                 * Load ajax info into personal menu.
                 * @param {string} type
                 */
                var loadAjaxInfo = function(type) {
                    // Target for data to be displayed on screen.
                    var container = $('#snap-personal-menu-' + type);
                    if ($(container).length) {
                        var cacheKey = M.cfg.sesskey + 'personal-menu-' + type;
                        try {
                            // Display old content while waiting
                            if (util.supportsSessionStorage() && window.sessionStorage[cacheKey]) {
                                log.info('using locally stored ' + type);
                                var html = window.sessionStorage[cacheKey];
                                $(container).html(html);
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
                                            $(container).html(data.html);
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
                if (!snapFeedsEnabled) {
                    loadAjaxInfo('deadlines');
                    loadAjaxInfo('graded');
                    loadAjaxInfo('grading');
                    loadAjaxInfo('messages');
                    loadAjaxInfo('forumposts');
                    $(document).trigger('snapUpdatePersonalMenu');

                    // Triggering event for external PM open listeners.
                    // Try/catch added to ensure browser compatibility for webcomponents.
                    try {
                        document.dispatchEvent(new Event('snapPersonalMenuOpen'));
                    } catch (error) {
                        log.error(error.message);
                        try {
                            var evt = document.createEvent("Event");
                            evt.initEvent('snapPersonalMenuOpen', true, true);
                            document.dispatchEvent(evt);
                        } catch (err) {
                            log.error(err.message);
                        }
                    }
                }
            };

            /**
             * Apply listeners for personal menu in mobile mode.
             */
            var mobilePersonalMenuListeners = function() {
                /**
                 * Get section left position and height.
                 * @param {string} href
                 * @returns {Object.<string, number>}
                 */
                var getSectionCoords = function(href) {
                    var sections = $("#snap-pm-content section");
                    var sectionWidth = $(sections).outerWidth();
                    var section = $(href);
                    var selector = "#snap-pm-updates section > div, #snap-pm-updates section > snap-feed > div";
                    var targetSection = $(selector).index(section) + 1;
                    var position = sectionWidth * targetSection;
                    var sectionHeight = $(href).outerHeight() + 200;

                    // Course lists is at position 0.
                    if (href == '#snap-pm-courses') {
                        position = 0;
                    }

                    // Set the window height.
                    var winHeight = $(window).height();
                    if (sectionHeight < winHeight) {
                        sectionHeight = winHeight;
                    }
                    return {left: position, height: sectionHeight};
                };
                // Personal menu small screen behaviour corrections on resize.
                $(window).on('resize', function() {
                    if (window.innerWidth >= 992) {
                        // If equal or larger than Bootstrap 992 large breakpoint, clear left positions of sections.
                        $('#snap-pm-content').removeAttr('style');

                        if ($("#snap-pm-courses div#snap-personal-menu-intelliboard").length > 0) {
                            var section = $('#snap-pm div#snap-personal-menu-intelliboard').closest('section');
                            section.prependTo('#snap-pm-updates');
                        }
                        if ($("#snap-pm-courses div#snap-personal-menu-intellicart").length > 0) {
                            var section = $('#snap-pm div#snap-personal-menu-intellicart').closest('section');
                            section.prependTo('#snap-pm-updates');
                        }
                        return;
                    }
                    var activeLink = $('#snap-pm-mobilemenu a.state-active');
                    if (!activeLink || !activeLink.length) {
                        return;
                    }

                    if ($("#snap-pm-updates div#snap-personal-menu-intelliboard").length > 0) {
                        var section = $('#snap-pm div#snap-personal-menu-intelliboard').closest('section');
                        section.appendTo('section#snap-pm-courses');
                    }
                    if ($("#snap-pm-updates div#snap-personal-menu-intellicart").length > 0) {
                        var section = $('#snap-pm div#snap-personal-menu-intellicart').closest('section');
                        section.appendTo('section#snap-pm-courses');
                    }

                    var href = activeLink.attr('href');
                    var posHeight = getSectionCoords(href);

                    $('#snap-pm-content').css('left', '-' + posHeight.left + 'px');
                    $('#snap-pm-content').css('height', posHeight.height + 'px');
                });
                // Personal menu small screen behaviour.
                $(document).on("click", '#snap-pm-mobilemenu a', function(e) {
                    var href = this.getAttribute('href');
                    var posHeight = getSectionCoords(href);

                    $("html, body").animate({scrollTop: 0}, 0);
                    $('#snap-pm-content').animate({
                            left: '-' + posHeight.left + 'px',
                            height: posHeight.height + 'px'
                        }, "700", "swing",
                        function() {
                            // Animation complete.
                        });
                    $('#snap-pm-mobilemenu a').removeClass('state-active');
                    $(this).addClass('state-active');
                    e.preventDefault();
                });
            };

            /**
             * Apply personal menu listeners.
             */
            var applyListeners = function() {
                // On clicking personal menu trigger.
                $(document).on("click", ".js-snap-pm-trigger", function(event) {
                    $("html, body").animate({scrollTop: 0}, 0);
                    $('body').toggleClass('snap-pm-open');
                    if ($('.snap-pm-open #snap-pm').is(':visible')) {
                        self.update();
                    }
                    event.preventDefault();

                    // Intelliboard and intellicart should be displayed under the courses on a small screen.
                    if (window.innerWidth < 992) {
                        if ($("#snap-pm-updates div#snap-personal-menu-intelliboard").length > 0) {
                            var section = $('#snap-pm div#snap-personal-menu-intelliboard').closest('section');
                            section.appendTo('section#snap-pm-courses');
                        }
                        if ($("#snap-pm-updates div#snap-personal-menu-intellicart").length > 0) {
                            var section = $('#snap-pm div#snap-personal-menu-intellicart').closest('section');
                            section.appendTo('section#snap-pm-courses');
                        }
                    }

                    // If there is a message drawer, dispatch event to avoid opening both elements.
                    if ($('.message-app.main').length === 0) {
                        document.dispatchEvent(new Event("messages-drawer:pm-toggle"));
                    }
                });

                mobilePersonalMenuListeners();
            };

            /**
             * Initialising function.
             * @param {boolean} sitePolicyAcceptReqd
             * @param {boolean} snapFeedsFlag
             */
            this.init = function(sitePolicyAcceptReqd, snapFeedsFlag) {
                redirectToSitePolicy = sitePolicyAcceptReqd;
                snapFeedsEnabled = snapFeedsFlag;
                applyListeners();
                if (!redirectToSitePolicy) {
                    courseCards.init();
                }
            };
        };

        return new PersonalMenu();
    }
);
