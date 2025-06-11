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
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* exported snapInit */
/* eslint no-invalid-this: "warn"*/

/**
 * Main snap initialising function.
 */
define(['jquery', 'core/log', 'core/aria', 'theme_snap/headroom', 'theme_snap/util', 'theme_snap/cover_image',
        'theme_snap/progressbar', 'core/templates', 'core/str', 'core/ajax', 'theme_snap/accessibility',
        'theme_snap/messages', 'theme_snap/scroll'],
    function($, log, Aria, Headroom, util, coverImage, ProgressBar, templates, str, ajax, accessibility, messages, Scroll) {

        'use strict';

        /* eslint-disable camelcase */
        M.theme_snap = M.theme_snap || {};
        /* eslint-enable camelcase */

        /**
         * master switch for logging
         * @type {boolean}
         */
        var loggingenabled = false;

        if (!loggingenabled) {
            log.disableAll(true);
        } else {
            log.enableAll(true);
        }

        /**
         * Initialize pre SCSS and grading constants.
         * New variables can be initialized if necessary.
         * These variables are being passed from classes/output/shared.php,
         * and being updated from php constants in snapInit.
         */
        var brandColorSuccess = '';
        var brandColorWarning = '';
        var GRADE_DISPLAY_TYPE_PERCENTAGE = '';
        var GRADE_DISPLAY_TYPE_PERCENTAGE_REAL = '';
        var GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER = '';
        var GRADE_DISPLAY_TYPE_REAL = '';
        var GRADE_DISPLAY_TYPE_REAL_PERCENTAGE = '';
        var GRADE_DISPLAY_TYPE_REAL_LETTER = '';

        /**
         * Get all url parameters from href
         * @param {string} href
         * @returns {Array}
         */
        var getURLParams = function(href) {
            // Create temporary array from href.
            var ta = href.split('?');
            if (ta.length < 2) {
                return false; // No url params
            }
            // Get url params full string from href.
            var urlparams = ta[1];

            // Strip out hash component
            urlparams = urlparams.split('#')[0];

            // Get urlparam items.
            var items = urlparams.split('&');

            // Create params array of values hashed by key.
            var params = [];
            for (var i = 0; i < items.length; i++) {
                var item = items[i].split('=');
                var key = item[0];
                var val = item[1];
                params[key] = val;
            }
            return (params);
        };

        /**
         * Change save and cancel buttons from forms to the bottom on mobile mode.
         */
        $(window).on('resize', function() {
            mobileFormChecker();
            updateGraderHeadersTop();
        });

        var mobileFormChecker = function() {
            var savebuttonsformrequired = $('div[role=main] .mform div.snap-form-required fieldset > div.form-group.fitem');
            var savebuttonsformadvanced = $('div[role=main] .mform div.snap-form-advanced > div:nth-of-type(3)');
            var width = $(window).width();
            if (width < 992) {
                $('.snap-form-advanced').append(savebuttonsformrequired);
            } else if (width > 992) {
                $('.snap-form-required fieldset#id_general').append(savebuttonsformadvanced);
            }
        };

        const updateGraderHeadersTop = function() {
            const graderHeader = $('.path-grade-report-grader .gradeparent tr.heading');
            if (graderHeader.length) {
                graderHeader.css('top', $('#mr-nav').height() + 'px');
            }
        };

        const regionMain = $('.path-grade-report-grader #region-main div[role="main"]');
        if (regionMain.length > 0) {
            const gradeParent = regionMain[0].querySelector('.gradeparent');
            if (gradeParent) {
                regionMain.addClass('snap-grade-reporter');
            }
        }

        updateGraderHeadersTop();

        /**
         * Move PHP errors into header
         *
         * @author Guy Thomas
         * @date 2014-05-19
         */
        var movePHPErrorsToHeader = function() {
            // Remove <br> tags inserted before xdebug-error.
            var xdebugs = $('.xdebug-error');
            if (xdebugs.length) {
                for (var x = 0; x < xdebugs.length; x++) {
                    var el = xdebugs[x];
                    var fontel = el.parentNode;
                    var br = $(fontel).prev('br');
                    $(br).remove();
                }
            }

            // Get messages using the different classes we want to use to target debug messages.
            var msgs = $('.xdebug-error, .php-debug, .debuggingmessage');

            if (msgs.length) {
                // OK we have some errors - lets shove them in the footer.
                $(msgs).addClass('php-debug-footer');
                var errorcont = $('<div id="footer-error-cont"><h3>' +
                    M.util.get_string('debugerrors', 'theme_snap') +
                    '</h3><hr></div>');
                $('#page-footer').append(errorcont);
                $('#footer-error-cont').append(msgs);
                // Add rulers
                $('.php-debug-footer').after($('<hr>'));
                // Lets also add the error class to the header so we know there are some errors.
                $('#mr-nav').addClass('errors-found');
                // Lets add an error link to the header.
                var errorlink = $('<a class="footer-error-link btn btn-danger" href="#footer-error-cont">' +
                    M.util.get_string('problemsfound', 'theme_snap') + ' <span class="badge">' + (msgs.length) + '</span></a>');
                $('#page-header').append(errorlink);
            }
        };

        /**
         * Are we on the course page?
         * Note: This doesn't mean that we are in a course - Being in a course could mean that you are on a module page.
         * This means that you are actually on the course page.
         * @returns {boolean}
         */
        var onCoursePage = function() {
            return $('body').attr('id').indexOf('page-course-view-') === 0;
        };

        /**
         * Apply block hash to form actions etc if necessary.
         */
        /* eslint-disable no-invalid-this */
        var applyBlockHash = function() {

            if (location.hash !== '') {
                return;
            }

            var urlParams = getURLParams(location.href);

            // If calendar navigation has been clicked then go back to calendar.
            if (onCoursePage() && typeof (urlParams.time) !== 'undefined') {
                location.hash = 'coursetools';
                if ($('.block_calendar_month')) {
                    util.scrollToElement($('.block_calendar_month'));
                }
            }

            // Form selectors for applying blocks hash.
            var formselectors = [
                'body.path-blocks-collect #notice form'
            ];

            // There is no decent selector for block deletion so we have to add the selector if the current url has the
            // appropriate parameters.
            var paramchecks = ['bui_deleteid', 'bui_editid'];
            for (var p in paramchecks) {
                var param = paramchecks[p];
                if (typeof (urlParams[param]) !== 'undefined') {
                    formselectors.push('#notice form');
                    break;
                }
            }

            // If required, apply #coursetools hash to form action - this is so that on submitting form it returns to course
            // page on blocks tab.
            $(formselectors.join(', ')).each(function() {
                // Only apply the blocks hash if a hash is not already present in url.
                var formurl = $(this).attr('action');
                if (formurl.indexOf('#') === -1
                    && (formurl.indexOf('/course/view.php') > -1)
                ) {
                    $(this).attr('action', $(this).attr('action') + '#coursetools');
                }
            });

            // Additional coursetools manipulations.
            document.querySelectorAll('#coursetools li div.snap-participant-icons span.userinitials').forEach(function(el) {
                el.setAttribute('aria-hidden', 'true');
            });
        };

        /**
         * Set forum strings because there isn't a decent renderer for mod/forum
         * It would be great if the official moodle forum module used a renderer for all output
         *
         * @author Guy Thomas
         * @date 2014-05-20
         */
        var setForumStrings = function() {
            $('.path-mod-forum tr.discussion td.topic.starter').attr('data-cellname',
                M.util.get_string('forumtopic', 'theme_snap'));
            $('.path-mod-forum tr.discussion td.picture:not(\'.group\')').attr('data-cellname',
                M.util.get_string('forumauthor', 'theme_snap'));
            $('.path-mod-forum tr.discussion td.picture.group').attr('data-cellname',
                M.util.get_string('forumpicturegroup', 'theme_snap'));
            $('.path-mod-forum tr.discussion td.replies').attr('data-cellname',
                M.util.get_string('forumreplies', 'theme_snap'));
            $('.path-mod-forum tr.discussion td.lastpost').attr('data-cellname',
                M.util.get_string('forumlastpost', 'theme_snap'));
        };

        /**
         * Process toc search string - trim, remove case sensitivity etc.
         *
         * @author Guy Thomas
         * @param {string} searchString
         * @returns {string}
         */
        var processSearchString = function(searchString) {
            searchString = searchString.trim().toLowerCase();
            return (searchString);
        };

        /**
         * Search course modules
         *
         * @author Stuart Lamour
         * @param {array} dataList
         */
        var tocSearchCourse = function(dataList) {
            // Keep search input open
            var i;
            var ua = window.navigator.userAgent;
            if (ua.indexOf('MSIE ') || ua.indexOf('Trident/')) {
                // We have reclone datalist over again for IE, or the same search fails the second time round.
                dataList = $("#toc-searchables").find('li').clone(true);
            }

            // TODO - for 2.7 process search string called too many times?
            var searchString = $("#toc-search-input").val();
            searchString = processSearchString(searchString);

            if (searchString.length === 0) {
                $('#toc-search-results').html('');
                $("#toc-search-input").removeClass('state-active');

            } else {
                $("#toc-search-input").addClass('state-active');
                var matches = [];
                for (i = 0; i < dataList.length; i++) {
                    var dataItem = dataList[i];
                    if (processSearchString($(dataItem).text()).indexOf(searchString) > -1) {
                        matches.push(dataItem);
                    }
                }
                $('#toc-search-results').html(matches);
            }
        };

        /**
         * Apply body classes which could not be set by renderers - e.g. when a notice was outputted.
         * We could do this in plain CSS if there was such a think as a parent selector.
         */
        var bodyClasses = function() {
            var extraclasses = [];
            if ($('#notice.snap-continue-cancel').length) {
                extraclasses.push('hascontinuecancel');
            }
            $('body').addClass(extraclasses.join(' '));
        };

        /**
         * Listen for hash changes / popstates.
         * @param {CourseLibAmd} courseLib
         */
        var listenHashChange = function(courseLib) {
            var lastHash = location.hash;
            $(window).on('popstate hashchange', function(e) {
                var newHash = location.hash;
                log.info('hashchange');
                if (newHash !== lastHash) {
                    $('#page, #moodle-footer, #logo, .skiplinks').css('display', '');
                    if (onCoursePage()) {
                        log.info('show section', e.target);
                        courseLib.showSection();
                    }
                }
                lastHash = newHash;
            });
        };

        /**
         * Course footer recent activity dom re-order.
         */
        var recentUpdatesFix = function() {
            $('#snap-course-footer-recent-activity .info').each(function() {
                $(this).appendTo($(this).prev());
            });
            $('#snap-course-footer-recent-activity .head .name').each(function() {
                $(this).prependTo($(this).closest(".head"));
            });
        };

        /**
         * Apply progressbar.js for circular progress displays.
         * @param {node} nodePointer
         * @param {function} dataCallback
         * @param {function} valueCallback
         */
        var createColoredDataCircle = function(nodePointer, dataCallback, valueCallback = null) {
            var circle = new ProgressBar.Circle(nodePointer, {
                color: 'inherit', // @gray.
                easing: 'linear',
                strokeWidth: 6,
                trailWidth: 3,
                duration: 1400,
                text: {
                    value: '0'
                }
            });
            var value = ($(nodePointer).attr('value') / 100);
            var endColor = brandColorSuccess; // Green @brand-success.
            if (value === 0 || $(nodePointer).attr('value') === '-') {
                circle.setText('-');
            } else {
                if ($(nodePointer).attr('value') < 50) {
                    endColor = brandColorWarning; // Orange @brand-warning.
                }
                circle.setText(dataCallback(nodePointer));
            }
            var valueAnimate = 0;

            if (valueCallback === null) {
                valueAnimate = value;
            } else {
                valueAnimate = valueCallback(nodePointer);
            }
            circle.animate(valueAnimate, {
                from: {
                    color: '#999' // @gray-light.
                },
                to: {
                    color: endColor
                },
                step: function(state, circle) {
                    circle.path.setAttribute('stroke', state.color);
                }
            });
        };

        var progressbarcircle = function() {
            $('.snap-student-dashboard-progress .js-progressbar-circle').each(function() {
                createColoredDataCircle(this, function(nodePointer) {
                    return $(nodePointer).attr('value') + '<small>%</small>';
                });
            });

            $('.snap-student-dashboard-grade .js-progressbar-circle').each(function() {
                createColoredDataCircle(this, function(nodePointer) {
                    var nodeValue = $(nodePointer).attr('value');
                    var gradeFormat = $(nodePointer).attr('gradeformat');

                    /**
                     * Definitions for gradebook.
                     *
                     * We need to display the % for all the grade formats which contains a % in the value.
                     */
                    if (gradeFormat == GRADE_DISPLAY_TYPE_PERCENTAGE
                        || gradeFormat == GRADE_DISPLAY_TYPE_PERCENTAGE_REAL
                        || gradeFormat == GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER) {
                        nodeValue = nodeValue + '<small>%</small>';
                    }
                    return nodeValue;
                }, function(nodePointer) {
                    var valueAnimate = $(nodePointer).attr('value');
                    var gradeFormat = $(nodePointer).attr('gradeformat');

                    if (gradeFormat == GRADE_DISPLAY_TYPE_REAL
                        || gradeFormat == GRADE_DISPLAY_TYPE_REAL_PERCENTAGE
                        || gradeFormat == GRADE_DISPLAY_TYPE_REAL_LETTER) {
                        valueAnimate = 0;
                    } else {
                        valueAnimate = ($(nodePointer).attr('value') / 100);
                    }
                    return valueAnimate;
                });
            });
        };

        /**
         * Add listeners.
         *
         * just a wrapper for various snippets that add listeners
         */
        var addListeners = function() {
            var selectors = [
                '.chapters a',
                '.section_footer a',
                ' #toc-search-results a'
            ];

            $(document).on('click', selectors.join(', '), function(e) {
                var href = this.getAttribute('href');
                if (window.history && window.history.pushState) {
                    history.pushState(null, null, href);
                    // Force hashchange fix for FF & IE9.
                    $(window).trigger('hashchange');
                    // Prevent scrolling to section.
                    e.preventDefault();
                } else {
                    location.hash = href;
                }
            });

            // Show fixed header on scroll down
            // using headroom js - http://wicky.nillia.ms/headroom.js/
            var myElement = document.querySelector("#mr-nav");
            // Functions added to trigger on pin and unpin actions for the nav bar
            var onPin = () => {
                $('.snap-drawer-no-headroom').addClass('snap-drawer-headroom');
                $('.snap-drawer-headroom').removeClass('snap-drawer-no-headroom');
            };
            var onUnpin = () => {
                $('.snap-drawer-headroom').addClass('snap-drawer-no-headroom');
                $('.snap-drawer-no-headroom').removeClass('snap-drawer-headroom');
            };
            // Construct an instance of Headroom, passing the element.
            var headroom = new Headroom(myElement, {
                "tolerance": 5,
                "offset": 100,
                "classes": {
                    // When element is initialised
                    initial: "headroom",
                    // When scrolling up
                    pinned: "headroom--pinned",
                    // When scrolling down
                    unpinned: "headroom--unpinned",
                    // When above offset
                    top: "headroom--top",
                    // When below offset
                    notTop: "headroom--not-top"
                },
                "onPin": onPin,
                "onUnpin": onUnpin
            });
            // When not signed in always show mr-nav?
            if (!$('.notloggedin').length) {
                headroom.init();
            }

            // Listener for toc search.
            var dataList = $("#toc-searchables").find('li').clone(true);
            $('#course-toc').on('keyup', '#toc-search-input', function() {
                tocSearchCourse(dataList);
            });

            // Handle keyboard navigation of search items.
            $('#course-toc').on('keydown', '#toc-search-input', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 9) {
                    // 9 tab
                    // 13 enter
                    // 40 down arrow
                    // Register listener for exiting search result.
                    $('#toc-search-results a').last().blur(function() {
                        $(this).off('blur'); // Unregister listener
                        $("#toc-search-input").val('');
                        $('#toc-search-results').html('');
                        $("#toc-search-input").removeClass('state-active');
                    });

                }
            });

            $('#course-toc').on("click", '#toc-search-results a', function() {
                $("#toc-search-input").val('');
                $('#toc-search-results').html('');
                $("#toc-search-input").removeClass('state-active');
            });

            /**
             * When the document is clicked, if the closest object that was clicked was not the search input then close
             * the search results.
             * Note that this is triggered also when you click on a search result as the results should no longer be
             * required at that point.
             */
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#toc-search-input').length) {
                    $("#toc-search-input").val('');
                    $('#toc-search-results').html('');
                    $("#toc-search-input").removeClass('state-active');
                }
            });

            // Admin drawer: Onclick for toggle of state-visible of admin block and mobile menu.
            $(document).on("click", "#admin-menu-trigger, #toc-mobile-menu-toggle", function(e) {
                var href = this.getAttribute('href');
                // Make this only happen for settings button.
                if (this.getAttribute('id') === 'admin-menu-trigger') {
                    $(this).toggleClass('active');
                    $('#page').toggleClass('offcanvas');
                    if ($(this).attr('aria-expanded') === 'true') {
                        $(this).attr('aria-expanded', false);
                    } else {
                        $(this).attr('aria-expanded', true);
                    }
                }
                $(href).attr('tabindex', '0');
                $(href).toggleClass('state-visible').focus();
                e.preventDefault();

                // Toggle accessibility visibility for screen readers using aria-hidden.
                if ($(href).hasClass('state-visible')) {
                    Aria.unhide(document.querySelector('#settingsnav'));
                } else {
                    Aria.hide(document.querySelector('#settingsnav'));
                }

                if ($('.message-app.main').length === 0) {
                    document.dispatchEvent(new Event("messages-drawer:toggle"));
                }

                // Code for mod_data sticky footer.
                if ($('#sticky-footer').length != 0) {
                    $('#sticky-footer').toggleClass('snap-mod-data-sticky-footer');
                }
            });

            // Snap feeds drawer: Onclick for toggle of state-visible of Snap feeds side menu.
            $(document).on("click", "#snap_feeds_side_menu_trigger", function(e) {
                var href = this.getAttribute('href');
                if (this.getAttribute('id') === 'snap_feeds_side_menu_trigger') {
                    $(this).toggleClass('active');
                    $('#page').toggleClass('offcanvas');
                    if ($(this).attr('aria-expanded') === 'true') {
                        $(this).attr('aria-expanded', false);
                    } else {
                        $(this).attr('aria-expanded', true);
                    }
                }
                $(href).toggleClass('state-visible').focus();
                e.preventDefault();

                // Code for mod_data sticky footer.
                if ($('#sticky-footer').length != 0) {
                    $('#sticky-footer').toggleClass('snap-mod-data-sticky-footer');
                }
            });

            // Messages Drawer: Onclick for Snap sidebar menu to adjust sticky footer.
            $(document).on("click", "[data-region=\"popover-region-messages\"] a", function() {
                // Code for mod_data sticky footer.
                if ($('#sticky-footer').length != 0) {
                    $('#sticky-footer').toggleClass('snap-mod-data-sticky-footer');
                }
            });

            // Mobile menu button.
            $(document).on("click", "#course-toc.state-visible a", function() {
                $('#course-toc').removeClass('state-visible');
            });

            // Check compatibility Mode in Snap.
            var isQuirksMode = document.compatMode !== 'CSS1Compat';
            if (isQuirksMode) {
                log.error('The document is rendering in "quirks mode". This may cause issues with the site\'s' +
                    ' functionality. Please ensure that the DOCTYPE declaration is present and correctly placed ' +
                    'at the very start of the HTML document.');
            }

            // Reset videos, when changing section (INT-18208).
            $(document).on("click", ".section_footer a, .chapter-title, .toc-footer a", function() {
                const videos = $('[title="watch"], .video-js, iframe:not([id])');
                for (let i = 0; i < videos.length; i++) {
                    if (videos[i].classList.contains('video-js')) {
                        if (videos[i].classList.contains('vjs-playing')) {
                            let videoButton = videos[i].querySelector('.vjs-play-control.vjs-control.vjs-button');
                            videoButton.click(); // Stop for videos using video-js Plugin.
                        }
                    } else if (videos[i].nodeName === 'IFRAME') {
                        if (videos[i].src.includes("vimeo")) {
                            videos[i].src += ""; // Stop for Vimeo embedded videos.
                        }
                    } else {
                        videos[i].querySelector('iframe').src += ""; // Stop for Youtube embedded videos.
                    }
                }
            });

            $(document).on('click', '.news-article .toggle', function(e) {
                var $news = $(this).closest('.news-article');
                var $newstoggle = $(this);
                util.scrollToElement($news);
                $('.news-article').not($news).removeClass('state-expanded');
                $('.news-article a.toggle:not(.snap-icon-close), .news-article h3.toggle a')
                    .not($newstoggle).attr('aria-expanded', 'false');
                $('.news-article-message').css('display', 'none');

                $news.toggleClass('state-expanded');
                if (!$news.attr('state-expanded')) {
                    $news.focus();
                    if (!$newstoggle.hasClass( "news-article-image")
                        && !$newstoggle.hasClass( "snap-icon-close")) {
                        $newstoggle.find( "a" ).attr('aria-expanded', 'false');
                    }
                }
                $('.state-expanded').find('.news-article-message').slideDown("fast", function() {
                    // Animation complete.
                    if ($news.is('.state-expanded')) {
                        $news.find('.news-article-message').focus();
                        if (!$newstoggle.hasClass( "news-article-image")
                            && !$newstoggle.hasClass( "snap-icon-close")) {
                            $newstoggle.find( "a" ).attr('aria-expanded', 'true');
                        }
                    } else {
                        $news.focus();
                        if (!$newstoggle.hasClass( "news-article-image")
                            && !$newstoggle.hasClass( "snap-icon-close")) {
                            $newstoggle.find( "a" ).attr('aria-expanded', 'false');
                        }
                    }
                    $(document).trigger('snapContentRevealed');
                });
                e.preventDefault();
            });

            // Add listeners for pausing animated images.
            $(document).on('click', '.anim-play-button', function() {
                $(this).parent().prev().css('visibility', 'visible');
            });
            $(document).on('click', '.anim-pause-button', function() {
                $(this).parent().prev().css('visibility', 'hidden');
            });

            // Initialise the scroll event listener.
            (new Scroll()).init();

            // Bootstrap js elements.

            // Initialise core bootstrap tooltip js.
            $(function() {
                var supportsTouch = false;
                if ('ontouchstart' in window) {
                    // IOS & android
                    supportsTouch = true;
                } else if (window.navigator.msPointerEnabled) {
                    // Win8
                    supportsTouch = true;
                }
                if (!supportsTouch) {
                    var tooltipNode = $('[data-toggle="tooltip"]');
                    if ($.isFunction(tooltipNode.tooltip)) {
                        tooltipNode.tooltip();
                    }
                }
            });
        };

        /**
         * Edit url for edit toggle in course page.
         * @param {string} courseFormat
         */
        var editToggleURL = function(courseFormat) {

            // We use this MutationObserver because modifying the URL with a hash for navigation does not trigger a page
            // reload. Specifically, the `window.location` statement in `lib/amd/src/edit_switch.js` does not cause a
            // reload. To work around this, we need to manually reload the page, but only after confirming that the
            // edit mode was successfully enabled. To do this, we observe the `aria-checked` attribute, which is added
            // by the `toggleEditSwitch` function in `lib/amd/src/edit_switch.js` when the edit mode was changed.
            const editModeToggleObserver = function(mutationsList, observer) {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'attributes') {
                        const editToggle = document.querySelector('.editmode-switch-form .custom-control-input');
                        if (editToggle.hasAttribute('aria-checked')) {
                            observer.disconnect();
                            location.reload();
                        }
                    }
                }
            };
            var courseEditSwitch = document.querySelector(
                '#page-course-view-' + courseFormat + ' .editmode-switch-form .custom-control-input');
            if (courseEditSwitch) {
                var urlHash = window.location.hash;
                var originalUrl = courseEditSwitch.getAttribute('data-pageurl');
                var modifiedURL = originalUrl+urlHash;
                courseEditSwitch.setAttribute('data-pageurl', modifiedURL);
                window.onhashchange = function() {
                    urlHash = window.location.hash;
                    courseEditSwitch.setAttribute('data-pageurl', originalUrl+urlHash);
                };
                const observer = new MutationObserver(editModeToggleObserver);
                observer.observe(document.body, {attributes: true});
            }
        };

        /**
         * Set a course as favourite from the enrolled courses in the home page.
         * @param {string} selector
         */
        var setHomeCourseFavourite = function(selector) {
            // Buttons to unset the course as favourite.
            const favouriteButtons = document.querySelectorAll('.snap-home-course .favouriteicon .' + selector);
            favouriteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const course = button.dataset.courseid;
                    var favouriteValue;
                    if (selector === 'unset-favourite') {
                        favouriteValue = false;
                    } else {
                        favouriteValue = true;
                    }
                    const args = {
                        courses: [
                            {
                                'id': course,
                                'favourite': favouriteValue
                            }
                        ]
                    };
                    const request = {
                        methodname: 'core_course_set_favourite_courses',
                        args: args,
                        done: function(response) {
                            if (!response.warnings.length) {
                                // We need to hide the current button and show the opposite one.
                                const relatedUnsetButton = document.querySelectorAll(
                                    '.favouriteicon .' + selector + '[data-courseid="' + course + '"]');
                                relatedUnsetButton.forEach(function(relatedButton) {
                                    relatedButton.classList.add('d-none');
                                });
                                if (selector === 'unset-favourite') {
                                    const relatedSetButton = document.querySelectorAll(
                                        '.favouriteicon .set-favourite[data-courseid="' + course + '"]');
                                    relatedSetButton.forEach(function(setButton) {
                                        setButton.classList.remove('d-none');
                                    });
                                } else {
                                    const relatedUnsetButton = document.querySelectorAll(
                                        '.favouriteicon .unset-favourite[data-courseid="' + course + '"]');
                                    relatedUnsetButton.forEach(function(setButton) {
                                        setButton.classList.remove('d-none');
                                    });
                                }

                            }
                        }
                    };

                    return ajax.call([request])[0];
                });
            });
        };

        /**
         * Function to fix the styles when fullscreen is used with Atto Editor.
         */
        function waitForFullScreenButton() {
            var maxIterations = 15;
            var i = 0;
            var checker = setInterval(function() {
                i = i + 1;
                if (i > maxIterations) {
                    clearInterval(checker);
                } else {
                    if ($('button.atto_fullscreen_button').length != 0 && $('div.editor_atto').length != 0) {
                        $('button.atto_fullscreen_button').click(function() {
                            $('div.editor_atto').css('background-color', '#eee');
                            $('div.editor_atto').css('z-index', '1');
                        });
                        $('button.atto_html_button').click(function() {
                            $('#id_introeditor').css('z-index', '1');
                        });
                        clearInterval(checker);
                    }
                }
            }, 2000);
        }

        /**
         * AMD return object.
         */
        return {
            /**
             * Snap initialise function.
             * @param {object} courseConfig
             * @param {bool} pageHasCourseContent
             * @param {int} siteMaxBytes
             * @param {bool} forcePassChange
             * @param {bool} messageBadgeCountEnabled
             * @param {int} userId
             * @param {bool} inAlternativeRole
             * @param {string} brandColors
             * @param {int} gradingConstants
             */
            snapInit: function(courseConfig, pageHasCourseContent, siteMaxBytes, forcePassChange,
                               messageBadgeCountEnabled, userId, inAlternativeRole,
                               brandColors, gradingConstants) {

                // Set up.

                // Branding colors. New colors can be set up if necessary.
                brandColorSuccess = brandColors.success;
                brandColorWarning = brandColors.warning;
                // Grading constants for percentage.
                GRADE_DISPLAY_TYPE_PERCENTAGE = gradingConstants.gradepercentage;
                GRADE_DISPLAY_TYPE_PERCENTAGE_REAL = gradingConstants.gradepercentagereal;
                GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER = gradingConstants.gradepercentageletter;
                GRADE_DISPLAY_TYPE_REAL = gradingConstants.gradereal;
                GRADE_DISPLAY_TYPE_REAL_PERCENTAGE = gradingConstants.graderealpercentage;
                GRADE_DISPLAY_TYPE_REAL_LETTER = gradingConstants.graderealletter;

                M.cfg.context = courseConfig.contextid;
                M.snapTheme = {forcePassChange: forcePassChange};

                // Course related AMD modules (note, site page can technically have course content too).
                if (pageHasCourseContent) {
                    require(
                        [
                            'theme_snap/course-lazy'
                        ], function(CourseLibAmd) {
                            // Instantiate course lib.
                            var courseLib = new CourseLibAmd(courseConfig);

                            // Hash change listener goes here because it requires courseLib.
                            listenHashChange(courseLib);
                        }
                    );
                }

                // When document has loaded.
                /* eslint-disable complexity */
                $(document).ready(function() {
                    movePHPErrorsToHeader(); // Boring
                    setForumStrings(); // Whatever
                    addListeners(); // Essential
                    applyBlockHash(); // Change location hash if necessary
                    bodyClasses(); // Add body classes
                    mobileFormChecker();
                    util.processAnimatedImages();

                    // Make sure that the blocks are always within page-content for assig view page.
                    $('#page-mod-assign-view #page-content').append($('#moodle-blocks'));

                    // Remove from Dom the completion tracking when it is disabled for an activity.
                    $('.snap-header-card .snap-header-card-icons .disabled-snap-asset-completion-tracking').remove();

                    // Prepend asset type when activity is a folder to appear in the card header instead of the content.
                    var folders = $('li.snap-activity.modtype_folder');
                    $.each(folders, function(index, folder) {
                        var content = $(folder).find('div.contentwithoutlink div.snap-assettype');
                        if (content.length > 0) {
                            if ($(folder).find('div.activityinstance div.snap-header-card .asset-type').length == 0) {
                                var folderAssetTypeHeader = $(folder).find('div.activityinstance div.snap-header-card');
                                content.prependTo(folderAssetTypeHeader);
                            }
                        }
                    });

                    // Add a class to the body to show js is loaded.
                    $('body').addClass('snap-js-loaded');

                    // Regroup activity naming to the correct placing in the DOM.
                    if (document.querySelector('[id^="page-mod-"]')) {
                        let contextHeader = document.querySelector('div.page-context-header');
                        let regionMain = document.querySelector('#region-main');
                        if (contextHeader !== null && regionMain !== null) {
                            regionMain.insertBefore(contextHeader, regionMain.firstChild);
                        }
                    }

                    // Apply progressbar.js for circular progress display.
                    progressbarcircle();
                    // Course footer recent updates dom fixes.
                    recentUpdatesFix();

                    if (!$('.notloggedin').length) {
                        if ($('body').hasClass('pagelayout-course') || $('body').hasClass('pagelayout-frontpage')) {
                            coverImage.courseImage(courseConfig.shortname, siteMaxBytes);
                        } else if ($('body').hasClass('pagelayout-coursecategory')) {
                            if (courseConfig.categoryid) {
                                coverImage.categoryImage(courseConfig.categoryid, siteMaxBytes);
                            }
                        }
                    }
                    // Allow deeplinking to bs tabs on snap settings page.
                    if ($('#page-admin-setting-themesettingsnap').length) {
                        var tabHash = location.hash;
                        // Check link is to a tab hash.
                        if (tabHash && $('.nav-link[href="' + tabHash + '"]').length) {
                            $('.nav-link[href="' + tabHash + '"]').tab('show');
                            $(window).scrollTop(0);
                        }
                    }

                    // Add extra padding when the error validation message appears at the moment of enter a not valid
                    // URL for feature spots.
                    var firstlinkerror = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_one_title_link span.error');
                    var secondlinkerror = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_two_title_link span.error');
                    var thirdlinkerror = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_three_title_link span.error');
                    var titlelinksettingone = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_one_title_link .form-label');
                    var titlelinksettingtwo = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_two_title_link .form-label');
                    var titlelinksettingthree = $('#page-admin-setting-themesettingsnap #themesnapfeaturespots' +
                        ' #admin-fs_three_title_link .form-label');
                    // Create an extra Div to wrap title links settings to avoid line break.
                    $('#page-admin-setting-themesettingsnap #themesnapfeaturespots ' +
                        '#admin-fs_three_title').nextUntil('#page-admin-setting-themesettingsnap #themesnapfeaturespots ' +
                        '#admin-fs_one_title_link_cb').wrapAll("<div class=fs-title-links></div>");
                    var linktitlestyle = {'padding-bottom': '2.1em'};

                    // We need to modify the padding of these elements depending on the case, because when validating
                    // the link and throwing an error, this will create an extra height to the parent and can break
                    // the visualization of the settings page for Feature spots.
                    if ((firstlinkerror).length) {
                        titlelinksettingtwo.css(linktitlestyle);
                        titlelinksettingthree.css(linktitlestyle);
                    }
                    if ((secondlinkerror).length) {
                        titlelinksettingone.css(linktitlestyle);
                        titlelinksettingthree.css(linktitlestyle);
                    }
                    if ((thirdlinkerror).length) {
                        titlelinksettingone.css(linktitlestyle);
                        titlelinksettingtwo.css(linktitlestyle);
                    }

                    // SHAME - make section name creation mandatory
                    const sname = document.getElementById('id_name');
                    if ($('#page-course-editsection.format-topics').length) {
                        sname.required = "required";
                        // Make sure that section does have at least one character.
                        $(sname).attr("pattern", ".*\\S+.*");

                        // Enable the cancel button.
                        $('#id_cancel').on('click', function() {
                            $(sname).removeAttr('required');
                            $(sname).removeAttr('pattern');
                            return true;
                        });
                    // Make sure that in other formats, "only spaces" name is not available.
                    } else {
                        $('#id_name_value').attr("pattern", ".*\\S+.*");
                        $('#id_cancel').on('click', function() {
                            $(sname).removeAttr('pattern');
                            return true;
                        });
                    }

                    // Book mod print button, only show if print link already present.
                    if ($('#page-mod-book-view a[href*="mod/book/tool/print/index.php"]').length) {
                        var urlParams = getURLParams(location.href);
                        if (urlParams) {
                            $('[data-block="_fake"]').append('<p>' +
                                '<hr><a target="_blank" href="/mod/book/tool/print/index.php?id=' + urlParams.id + '">' +
                                M.util.get_string('printbook', 'booktool_print') +
                                '</a></p>');
                        }
                    }

                    var modSettingsIdRe = /^page-mod-.*-mod$/; // E.g. #page-mod-resource-mod or #page-mod-forum-mod
                    var onModSettings = modSettingsIdRe.test($('body').attr('id')) && location.href.indexOf("modedit") > -1;
                    if (!onModSettings) {
                        modSettingsIdRe = /^page-mod-.*-general$/;
                        onModSettings = modSettingsIdRe.test($('body').attr('id')) && location.href.indexOf("modedit") > -1;
                    }
                    var onCourseSettings = $('body').attr('id') === 'page-course-edit';
                    var onSectionSettings = $('body').attr('id') === 'page-course-editsection';
                    $('#page-mod-hvp-mod .h5p-editor-iframe').parent().css({"display": "block"});
                    var pageBlacklist = ['page-mod-hvp-mod'];
                    var pageNotInBlacklist = pageBlacklist.indexOf($('body').attr('id')) === -1;

                    if ((onModSettings || onCourseSettings || onSectionSettings) && pageNotInBlacklist) {
                        // Wrap advanced options in a div
                        var vital = [
                            ':first',
                            '#page-course-edit #id_descriptionhdr',
                            '#id_contentsection',
                            '#id_general + #id_general', // Turnitin duplicate ID bug.
                            '#id_content',
                            '#page-mod-choice-mod #id_optionhdr',
                            '#page-mod-workshop-mod #id_gradingsettings',
                            '#page-mod-choicegroup-mod #id_miscellaneoussettingshdr',
                            '#page-mod-choicegroup-mod #id_groups',
                            '#page-mod-scorm-mod #id_packagehdr'
                        ];
                        vital = vital.join();

                        $('form[id^="mform1"] > fieldset').not(vital).wrapAll('<div class="snap-form-advanced col-md-4" />');

                        // Add expand all to advanced column.
                        $(".snap-form-advanced").append($(".collapsible-actions"));

                        // Adding additional events to handle collapse/expanse same as lib/form/amd/src/collapsesections.js
                        const formContainers = $('.snap-form-advanced > fieldset > .fcontainer');
                        const collapsemenu = $(".collapsible-actions > .collapsemenu")[0];
                        $('.snap-form-advanced > fieldset > .fcontainer').on('hidden.bs.collapse', () => {
                            const allCollapsed = [...formContainers].every(container => !container.classList.contains('show'));
                            if (allCollapsed) {
                                collapsemenu.classList.add('collapsed');
                                collapsemenu.setAttribute('aria-expanded', false);
                            }
                        });
                        $('.snap-form-advanced > fieldset > .fcontainer').on('shown.bs.collapse', () => {
                            const allExpanded = [...formContainers].every(container => container.classList.contains('show'));
                            if (allExpanded) {
                                collapsemenu.classList.remove('collapsed');
                                collapsemenu.setAttribute('aria-expanded', true);
                            }
                        });

                        // Add collapsed to all fieldsets in advanced, except on course edit page.
                        if (!$('#page-course-edit').length) {
                            $(".snap-form-advanced fieldset").addClass('collapsed');
                        }

                        // Sanitize required input into a single fieldset
                        var mainForm = $('form[id^="mform1"] fieldset:first');
                        var appendTo = $('form[id^="mform1"] fieldset:first .fcontainer');

                        var required = $('form[id^="mform1"] > fieldset').not('form[id^="mform1"] > fieldset:first');
                        for (var i = 0; i < required.length; i++) {
                            var content = $(required[i]).find('.fcontainer');
                            $(appendTo).append(content);
                            $(required[i]).remove();
                        }
                        $(mainForm).wrap('<div class="snap-form-required col-md-8" />');

                        // Show the form buttons when adding multiple LTI activities.
                        if ($('body#page-mod-lti-mod').length) {
                            var multipleLTIActivities =
                                document.querySelector('section#region-main form.mform > div[data-attribute="dynamic-import"]');
                            var LTIObserver = new MutationObserver(function() {
                                $('fieldset#id_general > :nth-child(5)').detach()
                                    .appendTo('section#region-main > div[role="main"] > form.mform');
                            });
                            var LTIObserverConfig = {childList: true};
                            LTIObserver.observe(multipleLTIActivities, LTIObserverConfig);
                        }

                        var description = $('form[id^="mform1"] fieldset:first .fitem_feditor:not(.required)');

                        if (onModSettings && description) {
                            var noNeedDescSelectors = [
                                'body#page-mod-assign-mod',
                                'body#page-mod-choice-mod',
                                'body#page-mod-turnitintool-mod',
                                'body#page-mod-workshop-mod',
                            ];
                            var addMultiMessageSelectors = [
                                'body#page-mod-url-mod',
                                'body#page-mod-resource-mod',
                                'body#page-mod-folder-mod',
                                'body#page-mod-imscp-mod',
                                'body#page-mod-lightboxgallery-mod',
                                'body#page-mod-scorm-mod',
                            ];
                            if ($(noNeedDescSelectors.join()).length === 0) {
                                $(appendTo).append(description);
                                $(appendTo).append($('#fitem_id_showdescription'));
                            }
                            // Resource cards - add a message to this type of activities, these activities will not display
                            // any multimedia.
                            if ($(addMultiMessageSelectors.join()).length > 0) {
                                str.get_strings([
                                    {key: 'multimediacard', component: 'theme_snap'}
                                ]).done(function(stringsjs) {
                                    var activityCards = stringsjs[0];
                                    var cardmultimedia = $("[id='id_showdescription']").closest('.form-group');
                                    $(cardmultimedia).append(activityCards);
                                });
                            }
                            str.get_strings([
                                {key: 'pageactivitywithnodescription', component: 'theme_snap'}
                            ]).done(function (stringsjs) {
                                let stringmsg = stringsjs[0];
                                let modpagelocation = $("#page-mod-page-mod")
                                    .find("#id_coursecontentnotification")
                                    .closest('.form-group');
                                $(modpagelocation).append(stringmsg);
                            });
                        }

                        // Resources - put description in common mod settings.
                        description = $("#page-mod-resource-mod [data-fieldtype='editor']").closest('.form-group');
                        var showdescription = $("#page-mod-resource-mod [id='id_showdescription']").closest('.form-group');
                        $("#page-mod-resource-mod .snap-form-advanced #id_modstandardelshdr .fcontainer").append(description);
                        $("#page-mod-resource-mod .snap-form-advanced #id_modstandardelshdr .fcontainer").append(showdescription);

                        // Assignment - put due date in required.
                        var duedate = $("#page-mod-assign-mod [for='id_duedate']").closest('.form-group');
                        $("#page-mod-assign-mod .snap-form-required .fcontainer").append(duedate);

                        // Move availablity at the top of advanced settings.
                        var availablity = $('#id_visible').closest('.form-group').addClass('snap-form-visibility');
                        var label = $(availablity).find('label');
                        var select = $(availablity).find('select');
                        $(label).insertBefore(select);

                        // SHAME - rewrite visibility form lang string to be more user friendly.
                        $(label).text(M.util.get_string('visibility', 'theme_snap') + ' ');

                        if ($("#page-course-edit").length) {
                            // We are in course editing form.
                            // Removing the "Show all sections in one page" from the course format form.
                            var strDisabled = "";
                            (function() {
                                return str.get_strings([
                                    {key: 'showallsectionsdisabled', component: 'theme_snap'},
                                    {key: 'disabled', component: 'theme_snap'}
                                ]);
                            })()
                                .then(function(strings) {
                                    var strMessage = strings[0];
                                    strDisabled = strings[1];
                                    return templates.render('theme_snap/form_alert', {
                                        type: 'warning',
                                        classes: '',
                                        message: strMessage
                                    });
                                })
                                .then(function(html) {
                                    var op0 = $('[name="coursedisplay"] > option[value="0"]');
                                    var op1 = $('[name="coursedisplay"] > option[value="1"]');
                                    var selectNode = $('[name="coursedisplay"]');
                                    // Disable option 0
                                    op0.attr('disabled', 'disabled');
                                    // Add "(Disabled)" to option text
                                    op0.append(' (' + strDisabled + ')');
                                    // Remove selection attribute
                                    op0.removeAttr("selected");
                                    // Select option 1
                                    op1.attr('selected', 'selected');
                                    // Add warning
                                    selectNode.parent().append(html);
                                });
                        }

                        $('.snap-form-advanced').prepend(availablity);

                        // Add save buttons.
                        var savebuttons = $('form[id^="mform1"] > .form-group:last');
                        $(mainForm).append(savebuttons);

                        // Expand collapsed fieldsets when editing a mod that has errors in it.
                        var errorElements = $('.form-group.has-danger');
                        if (onModSettings && errorElements.length) {
                            errorElements.closest('.collapsible').removeClass('collapsed');
                        }

                        // Hide appearance menu from interface when editing a page-resource.
                        if ($("#page-mod-page-mod").length) {
                            // Chaining promises to get localized strings and render warning message.
                            (function() {
                                return str.get_strings([
                                    {key: 'showappearancedisabled', component: 'theme_snap'}
                                ]);
                            })()
                                .then(function(localizedstring) {
                                    return templates.render('theme_snap/form_alert', {
                                        type: 'warning',
                                        classes: '',
                                        message: localizedstring
                                    });
                                })
                                // eslint-disable-next-line promise/always-return
                                .then(function(html) {
                                    // Disable checkboxes.
                                    // Colors for disabling the divs.
                                    var layoverbkcolor = "#f1f1f1";
                                    var layovercolor = "#d5d5d5";
                                    var pageInputs = $('[id="id_printheading"], [id="id_printintro"],' +
                                        ' [id="id_printlastmodified"], [id="id_display"],' +
                                        ' [id="id_popupwidth"], [id="id_popupheight"]');

                                    // This will help with disable the multiple options for the select, and let the one by default.
                                    // Allowing to submit the form.
                                    $('#id_display option:not(:selected)').attr('disabled', true);

                                    // Note we can't use 'disabled' for settings or they don't get submitted.
                                    pageInputs.attr('readonly', true);
                                    $('#id_display').attr('disabled', true);
                                    pageInputs.attr('tabindex', -1); // Prevent tabbing to change val.
                                    pageInputs.click(function(e) {
                                        e.preventDefault();
                                        return false;
                                    });
                                    pageInputs.parent().parent().parent().css('background-color', layoverbkcolor);
                                    pageInputs.parent().parent().parent().css('color', layovercolor);

                                    // Add warning.
                                    var selectNode = $('#id_appearancehdrcontainer');
                                    selectNode.append(html);
                                });
                            $('#id_showdescription').parent().parent().parent().hide();
                        }
                    }
                    // Remove disabled attribute for section name for topics format.
                    if (onSectionSettings) {
                        var sectionName = $("#page-course-editsection.format-topics .form-group #id_name_value");
                        if (sectionName.length) {
                            let sectionNameIsDiabled = document.getElementById('id_name_value').hasAttribute("disabled");
                            if (sectionNameIsDiabled) {
                                document.getElementById('id_name_value').removeAttribute("disabled");
                            }
                        }
                    }

                    // Conversation counter for user badge.
                    if (messageBadgeCountEnabled) {
                        require(
                            [
                                'theme_snap/conversation_badge_count-lazy'
                            ], function(conversationBadgeCount) {
                                conversationBadgeCount.init(userId);
                            }
                        );
                    }

                    // Update Messages badge without reloading the site.
                    $('.message-app .list-group').on('click', '.list-group-item.list-group-item-action', function(e) {
                        require(
                            [
                                'theme_snap/conversation_badge_count-lazy'
                            ], function(conversationBadgeCount) {
                                let conversationId = e.currentTarget.attributes['data-conversation-id'].value;
                                conversationBadgeCount.init(userId, conversationId);
                            }
                        );
                    });

                    // Listen to cover image label key press for accessible usage.
                    var focustarget = $('#snap-coverimagecontrol label');
                    if (focustarget && focustarget.length) {
                        focustarget.keypress(function(e) {
                            if (e.which === 13) {
                                $('#snap-coverfiles').trigger('click');
                            }
                        });
                    }

                    // Review if settings block is missing.
                    if (!$('.block_settings').length) {
                        // Hide admin icon.
                        $('#admin-menu-trigger').hide();
                        if (inAlternativeRole) {
                            // Handle possible alternative role.
                            require(
                                [
                                    'theme_snap/alternative_role_handler-lazy'
                                ], function(alternativeRoleHandler) {
                                    alternativeRoleHandler.init(courseConfig.id);
                                }
                            );
                        }
                    }

                    // Add settings tab show behaviour to classes which want to do that.
                    $('.snap-settings-tab-link').on('click', function() {
                        var tab = $('a[href="' + $(this).attr('href') + '"].nav-link');
                        if (tab.length) {
                            tab.tab('show');
                        }
                    });

                    // Unpin headroom when url has #course-detail-title or #mod_book-chapter.
                    if (window.location.hash === '#course-detail-title' || window.location.hash === '#mod_book-chapter') {
                        $('#mr-nav').removeClass('headroom--pinned').addClass('headroom--unpinned');
                    }

                    // Re position submit buttons for forms when using mobile mode at the bottom of the form.
                    var savebuttonsformrequired = $('div[role=main] .mform div.snap-form-required fieldset > div.form-group.fitem');
                    var width = $(window).width();
                    if (width < 767) {
                        $('.snap-form-advanced').append(savebuttonsformrequired);
                    }

                    // Fix a position for the new 'Send content change notification' setting.
                    if ( $('.path-mod.theme-snap #id_coursecontentnotification').length ) {
                        const notificationCheck = document.getElementById('id_coursecontentnotification')
                            .closest(".form-group.fitem");
                        const submitButtons = $('.snap-form-required [data-groupname="buttonar"]');
                        if (notificationCheck !== null && submitButtons.length) {
                            notificationCheck.classList.add('snap_content_notification_check');
                            submitButtons.before(notificationCheck);
                        }
                    }

                    // Checking if the snap form required fieldset is not being displayed.
                    const snapFormFsRequired = $('.snap-form-required > fieldset');
                    if(snapFormFsRequired && snapFormFsRequired.hasClass('d-none')){
                        // Now its safe to remove  the columns class from the form so the visible fieldset takes the full space.
                        const visibleFieldset = $('.snap-form-advanced > fieldset').not('.d-none');
                        $(visibleFieldset).parent().removeClass('col-md-4');

                        // Making sure that the save buttons are displayed.
                        const notificationCheck = document.getElementById('id_coursecontentnotification')
                            .closest(".form-group.fitem");
                        $('.snap-form-advanced').append(notificationCheck);
                        $('.snap-form-advanced').append(savebuttonsformrequired);
                    }

                    // Hide Blocks editing on button from the Intelliboard Dashboard page in Snap.
                    if ( $('#page-home.theme-snap .intelliboard-page').length && $('.snap-page-heading-button').length) {
                        const blocksEditingOnButton = document.getElementsByClassName('snap-page-heading-button')[0];
                        blocksEditingOnButton.classList.add("hidden");
                    }

                    // Code for Tiles particular loading, needed before other scripts but after the document is ready.
                    var targetTilesSect = document.querySelector('section#tiles-section');
                    if (targetTilesSect) {
                        var configTilesSect = {childList: true, subtree: true};
                        var observerTilesSect = new MutationObserver(function() {
                            util.processAnimatedImages();
                        });
                        observerTilesSect.observe(targetTilesSect, configTilesSect);
                    }

                    // Listener to set the favourite courses from the homepage enrolled courses.
                    const snapHomeCourses = document.querySelector('#page-site-index #frontpage-course-list .snap-home-course');
                    if (snapHomeCourses) {
                        setHomeCourseFavourite('unset-favourite');
                        setHomeCourseFavourite('set-favourite');
                    }

                    // Snapify format site on the front page if needed.
                    // TODO: Maybe remove this whole piece if MDL-82188 ever gets resolved in our favor.
                    if ($('body#page-site-index.format-site').length) {
                        var frontPageActivities = document.querySelector('div[role="main"] ul.section');
                        var frontPageActObserver = new MutationObserver(function() {
                            $('div[role="main"] ul.section > li[id^="module"]').each(function() {
                                if (!$(this).hasClass('snap-activity') && !$(this).hasClass('snap-asset')) {
                                    let id = $(this).attr('id');
                                    let moduleid = id.match(/\d+$/)[0];
                                    $(this).hide(); // Hide it while we finish.

                                    ajax.call([
                                        {
                                            methodname: 'theme_snap_course_module',
                                            args: {cmid: moduleid},
                                            done: function(response) {
                                                let html = $.parseHTML(response.html);
                                                $('#' + id).replaceWith(html[0]);
                                            }
                                        }
                                    ]);
                                }
                            });
                        });
                        var frontPageActConfig = {childList: true};
                        frontPageActObserver.observe(frontPageActivities, frontPageActConfig);
                    }

                    // Move My courses button to be centered in the home page.
                    if ($('body#page-site-index.theme-snap .frontpage-course-list-enrolled .paging-morelink').length) {
                        var moreCoursesButton = document.querySelector('.frontpage-course-list-enrolled .paging-morelink');
                        const newParentElement = moreCoursesButton.parentNode.parentNode;
                        newParentElement.appendChild(moreCoursesButton);
                    }
                    // Move All courses button to be centered in the home page.
                    if ($('body#page-site-index.theme-snap .frontpage-course-list-all .paging-morelink').length) {
                        var moreCoursesButton = document.querySelector('.frontpage-course-list-all .paging-morelink');
                        const newParentElement = moreCoursesButton.parentNode.parentNode;
                        newParentElement.appendChild(moreCoursesButton);
                    }

                    waitForFullScreenButton();

                    // Reassess the competency report user table.
                    if ($('body#page-report-competency-index').length > 0) {
                        const userCompetency = $('.user-competency-course-navigation');
                        if (userCompetency.length > 0) {
                            userCompetency.parent().addClass('ms-4');
                        }
                    }

                    // To update the edit toggle URL in course page.
                    editToggleURL('topics');
                    editToggleURL('weeks');

                    // Modify Hide / Show / Delete actions for blocks in coursetools section to be redirected to the
                    // course dashboard instead of course main page.
                    const snapEditingCourse = document.querySelector('body.theme-snap.pagelayout-course.editing');
                    if (snapEditingCourse) {
                        require(
                            [
                                'theme_snap/coursetools_blocks_management'
                            ], function(CoursetoolsBlocksManagementAmd) {
                                CoursetoolsBlocksManagementAmd.init(courseConfig);
                            }
                        );
                    }
                    // Modify the edit mod link in settings block to add section return parameter.
                    const linkWithSection = document.querySelector('.breadcrumb-nav .breadcrumb-item [href*="#section-"]');
                    const blockSettingsLink = document.querySelector('.block_settings [href*="course/modedit.php?update"]');
                    if (linkWithSection && blockSettingsLink) {
                        const sectionMatch = linkWithSection.href.match(/section-(\d+)/);
                        if (sectionMatch) {
                            const sectionNumber = sectionMatch[1];
                            blockSettingsLink.href += `&sr=${sectionNumber}`;
                        }
                    }

                    // Add the correct section return to the modchooser.
                    util.modchooserSectionReturn();
                });
                accessibility.snapAxInit();
                messages.init();

                // Smooth scroll for go to top button.
                $("div#goto-top-link > a").click(function() {
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    $('body').find('a, [tabindex=0]').first().focus();
                });

                // Blocks selectors to remove 'editing' class because is not necessary to access their settings.
                var noneditingblocks = {};
                noneditingblocks.blockxp = '#page-blocks-xp-index';

                // Remove 'editing' class actions.
                for (var block in noneditingblocks) {
                    var blockisediting = $(noneditingblocks[block]).hasClass('editing');
                    if (blockisediting === true) {
                        $(noneditingblocks[block]).removeClass('editing');
                    }
                }
            }
        };
    }
);
