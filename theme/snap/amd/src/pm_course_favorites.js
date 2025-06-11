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
 * Course card favoriting.
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/log', 'theme_snap/model_view', 'theme_snap/ajax_notification'],
    function($, ajax, notification, log, mview, ajaxNotify) {
        return function() {
            log.enableAll(true);

            /**
             * The ajax call has returned a new course_card renderable.
             *
             * @method reloadCourseCardTemplate
             * @param {object} renderable - coursecard renderable
             * @param {jQuery} cardEl - coursecard element
             * @returns {Promise}
             */
            var reloadCourseCardTemplate = function(renderable, cardEl) {
                var dfd = $.Deferred();
                mview(cardEl, 'theme_snap/course_cards');
                var callback = function() {
                    var button = $(cardEl).find('.favoritetoggle');
                    $(button).removeClass('ajaxing');
                    $(button).focus();
                };
                $(cardEl).trigger('modelUpdate', [renderable, callback]);
                $(cardEl).on('modelUpdated', function(e) {
                    dfd.resolve(e);
                });
                return dfd.promise();
            };

            /**
             * Get course card course id.
             * @param {jQuery} cardEl
             * @returns {int}
             */
            var getCardId = function(cardEl) {
                return parseInt($(cardEl).find('.coursecard-body').data('courseid'));
            };

            /**
             * Get course card full name.
             * @param {jQuery} cardEl
             * @param {null|bool} lowerCase
             * @returns {*|jQuery}
             */
            var getCardTitle = function(cardEl, lowerCase) {
                // The title comes back in lower case by default as it's used for case insensitive sorting.
                if (lowerCase === undefined) {
                    lowerCase = true;
                }
                var title = $(cardEl).find('.coursecard-coursename').html();
                if (lowerCase) {
                    title = title.toLowerCase();
                }
                return title;
            };

            /**
             * Get index of card within list.
             *
             * @param {jQuery} cardEl
             * @param {jQuery} cards
             * @returns {number}
             */
            var getCardIndex = function(cardEl, cards) {
                if (cards.length === 0) {
                    return -1;
                }
                // The sort variable is purely for sorting the cards by name.
                var sort = [],
                    sortItem = {};

                cards.each(function() {
                    sortItem = {
                        title: getCardTitle(this),
                        card: this
                    };
                    sort.push(sortItem);
                });
                // Add the item we are inserting to the list.
                sortItem = {
                    title: getCardTitle(cardEl),
                    card: cardEl
                };
                sort.push(sortItem);
                sort.sort(function(a, b) {
                    var aId = getCardId(a.card);
                    var bId = getCardId(b.card);
                    if (a.title === b.title) {
                        if (aId === bId) {
                            return 0;
                        }
                        return aId > bId ? 1 : -1;
                    }
                    return a.title > b.title ? 1 : -1;
                });
                return sort.indexOf(sortItem);
            };

            /**
             * Move card into alphabetical place in list.
             * @param {jQuery} cardEl
             * @param {string} listSelector
             * @param {string} listSelectorWhenEmpty
             * @param {bool} prependWhenEmpty
             * @param {function} onMoveComplete
             */
            var moveCard = function(cardEl, listSelector, listSelectorWhenEmpty, prependWhenEmpty, onMoveComplete) {

                var cardEls = $(listSelector);
                var idx = getCardIndex(cardEl, cardEls);
                var insIdx = idx + 1;

                log.debug('Moving card element into position ' + insIdx +
                    ' of list (size = ' + cardEls.length + ') : ' + listSelector);

                if (insIdx > 0) {
                    if (insIdx <= cardEls.length) {
                        log.debug('Moving card before position ' + insIdx + '  using selector ' + listSelector);
                        $(listSelector).eq(idx).before(cardEl);
                    } else {
                        log.debug('Moving card after position ' + cardEls.length + ' using selector ' + listSelector);
                        $(listSelector).eq(cardEls.length - 1).after(cardEl);
                    }
                } else {
                    log.debug('Destination ' + listSelector + ' empty');
                    if (prependWhenEmpty) {
                        log.debug('prepending to ' + listSelectorWhenEmpty);
                        $(listSelectorWhenEmpty).prepend(cardEl);
                    } else {
                        log.debug('appending to ' + listSelectorWhenEmpty);
                        $(listSelectorWhenEmpty).append(cardEl);
                    }
                }

                if (typeof (onMoveComplete) === 'function') {
                    onMoveComplete();
                }
            };

            /**
             * Move card element out of favorites.
             * @param {jQuery} cardEl
             * @param {function} onMoveComplete
             * @returns {void}
             */
            var moveOutOfFavorites = function(cardEl, onMoveComplete) {
                var container;
                // Check there are courses which are not hidden.
                // When this is 0 we only have hidden courses, so container is #snap-pm-courses-current-cards.
                var publishedcount = $('#snap-pm-courses-current .coursecard:not([data-hidden="true"])').length;
                // Special stuff for when moving a hidden course.
                if ($(cardEl).data('hidden') === true && publishedcount > 0) {
                    container = '#snap-pm-courses-hidden-cards';
                    // Open hidden courses section.
                    $('#snap-pm-courses-hidden').addClass('state-visible');
                    $('#snap-pm-courses-hidden-cards').collapse('show');
                } else {
                    window.console.log('not a hidden card');
                    container = '#snap-pm-courses-current-cards';
                }
                moveCard(cardEl, container + ' .coursecard:not(.favorited)', container, false, onMoveComplete);
            };

            /**
             * Favorite a course.
             * @param {jQuery} button - button clicked on to favorite the course.
             */
            var favoriteCourse = function(button) {
                if ($(button).hasClass('ajaxing')) {
                    return;
                }

                $(button).addClass('ajaxing');

                var favorited = $(button).attr('aria-pressed') === 'true' ? 0 : 1;
                var cardEl = $($(button).parents('.coursecard')[0]);
                var shortname = $(cardEl).data('shortname');

                var doAjax = function(jsid) {
                    return ajax.call([
                        {
                            methodname: 'theme_snap_course_card',
                            args: {courseshortname: shortname, favorited: favorited},
                            fail: function(response) {
                                $(button).removeClass('ajaxing');
                                ajaxNotify.ifErrorShowBestMsg(response);
                            }
                        }
                    ], true, true)[0].then(function(response) {
                        return reloadCourseCardTemplate(response, cardEl);
                    }).then(function() {
                        M.util.js_complete(jsid);
                    });
                };

                var jsid;
                if (favorited === 1) {
                    jsid = 'favourite_' + new Date().getTime().toString(16) + (Math.floor(Math.random() * 1000));
                    M.util.js_pending(jsid);
                    // Move to favorites.
                    moveCard(cardEl, '#snap-pm-courses-current-cards .coursecard.favorited', '#snap-pm-courses-current-cards', true,
                        function() {
                            doAjax(jsid);
                        }
                    );
                } else {
                    jsid = 'unfavourite_' + new Date().getTime().toString(16) + (Math.floor(Math.random() * 1000));
                    M.util.js_pending(jsid);
                    moveOutOfFavorites(cardEl,
                        function() {
                           doAjax(jsid);
                        }
                    );
                }
            };

            /**
             * On clicking favourite toggle. (Delegated).
             */
            $("#snap-pm").on("click", ".favoritetoggle", function(e) {
                e.preventDefault();
                e.stopPropagation();
                favoriteCourse(this);
            });
        };
    }
);
