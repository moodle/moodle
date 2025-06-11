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
 * Personal menu course cards.
 */
define(['jquery', 'core/log', 'core/templates', 'theme_snap/pm_course_favorites',
    'theme_snap/model_view', 'theme_snap/ajax_notification', 'theme_snap/util',
    'theme_snap/appear'],
    function($, log, templates, courseFavorites, mview, ajaxNotify, util, appear) {

        var CourseCards = function() {

            var self = this;

            /**
             * Apply course information to courses in personal menu.
             *
             * @param {Array} crsinfo
             */
            this.applyCourseInfo = function(crsinfo) {
                // Pre-load template or it will get loaded multiple times with a detriment on performance.
                templates.render('theme_snap/course_cards', [])
                    .done(function() {
                        for (var i in crsinfo) {
                            var info = crsinfo[i];
                            log.debug('applying course data for courseid ' + info.course);
                            var cardEl = $('.coursecard[data-courseid="' + info.course + '"]');
                            mview(cardEl, 'theme_snap/course_cards');
                            $(cardEl).trigger('modelUpdate', info);
                        }
                    });
            };

            /**
             * Request courseids.
             * @param {number[]} courseids
             */
            this.reqCourseInfo = function(courseids) {
                if (courseids.length === 0) {
                    return;
                }
                // Get course info via ajax.
                var courseiddata = 'courseids=' + courseids.join(',');
                var courseInfoKey = M.cfg.sesskey + 'coursecard';
                log.debug("fetching course data");
                $.ajax({
                    type: "GET",
                    async: true,
                    url: M.cfg.wwwroot + '/theme/snap/rest.php?action=get_courseinfo&contextid=' + M.cfg.context,
                    data: courseiddata,
                    success: function(data) {
                        ajaxNotify.ifErrorShowBestMsg(data).done(function(errorShown) {
                            if (errorShown) {
                                return;
                            } else {
                                // No errors, apply course info.
                                if (data.info) {
                                    log.debug('fetched coursedata', data.info);
                                    if (util.supportsSessionStorage()) {
                                        window.sessionStorage[courseInfoKey] = JSON.stringify(data.info);
                                    }
                                    self.applyCourseInfo(data.info);
                                } else {
                                    log.warn('fetched coursedata with error: JSON data object is missing info property', data);
                                }
                            }
                        });
                    }
                });
            };

            /**
             * Get course ids from cards.
             * @returns {Array}
             */
            this.getCourseIds = function() {
                var courseIds = [];
                $('.coursecard').each(function() {
                    courseIds.push($(this).attr('data-courseid'));
                });
                return courseIds;
            };

            this.applyAppearForImages = function() {
                appear(document.body).on('appear', '.coursecard', function(e, appeared) {
                    appeared.each(function() {
                        var imgurl = $(this).data('image-url');
                        if (imgurl !== undefined) {
                            var card = $(this);
                            // We use a fake image element to track the loading of the image so we can insert the
                            // background image once the image is loaded. If we didn't do this then we'd see a flicker
                            // from the course card gradient to the main brand colour before the image loaded.
                            $('<img src="' + imgurl.trim() + '" />').on('load', function() {
                                $(card).css('background-image', 'url(' + imgurl.trim() + ')');
                            });
                        }
                    });
                });

                /**
                 * Detect when animation is completed.
                 * https://davidwalsh.name/css-animation-callback
                 * @returns {string}
                 */
                function whichAnimationEvent() {
                    var t;
                    var el = document.createElement('fakeelement');
                    var res = 'Animationend';
                    var animations = {
                        'Animation': 'Animationend',
                        'OAnimation': 'oAnimationEnd',
                        'MozAnimation': 'Animationend',
                        'WebkitAnimation': 'webkitAnimationEnd'
                    };

                    for (t in animations) {
                        if (el.style[t] !== undefined) {
                            res = animations[t];
                            break;
                        }
                    }
                    return res; // Adding a default return value.
                }

                // When the course archive navigation elements are clicked we need to force appear to check for
                // newly visible course cards.
                $('#snap-pm-courses .nav-tabs .nav-link').on('click', function() {
                    var selector = $(this).attr('href');
                    // Note, appear does not play nicely with CSS animations so we are waiting for the animation to
                    // complete before we force appear to check for newly visible cards.
                    $(selector)[0].addEventListener(whichAnimationEvent(), function() {
                        $.force_appear();
                    }, false);
                });

                // Appear configuration - start loading images when they are out of the view port by 100px.
                var appearConf = {appeartopoffset: 100, appearleftoffset: 100};
                $('.coursecard').appear(appearConf);
                appear.force_appear();
            };

            /**
             * Initialising function.
             */
            this.init = function() {
                $(document).ready(function() {
                    courseFavorites();

                    // Load course information via ajax.
                    var courseIds = self.getCourseIds();
                    var courseInfoKey = M.cfg.sesskey + 'coursecard';
                    if (courseIds.length > 0) {
                        self.applyAppearForImages();
                        // OK - lets see if we have grades/progress in session storage.
                        if (util.supportsSessionStorage() && window.sessionStorage[courseInfoKey]) {
                            var courseinfo = JSON.parse(window.sessionStorage[courseInfoKey]);
                            self.applyCourseInfo(courseinfo);
                        } else {
                            // Only make AJAX request on document ready if the session storage isn't populated.
                            self.reqCourseInfo(courseIds);
                        }
                    }
                });

                // Personal menu course card clickable.
                $(document).on('click', '.coursecard[data-href]', function(e) {
                    var trigger = $(e.target),
                        hreftarget = '_self';
                    // Excludes any clicks in the card deeplinks.
                    if (!$(trigger).closest('a').length) {
                        window.open($(this).data('href'), hreftarget);
                        e.preventDefault();
                    }
                });
            };

        };

        return new CourseCards();
    }
);
