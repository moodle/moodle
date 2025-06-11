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

define(['jquery', 'core/templates', 'core/str'], function($, templates, str) {

    var staticSupportsSessionStorage = null;

    /**
     * General utilities library.
     */
    return {
        /**
         * On function evaluating true.
         *
         * @param {function} func
         * @param {function} callBack
         * @param {boolean} forceCallBack
         * @param {number} maxIterations
         * @param {number} i
         */
        whenTrue: function(func, callBack, forceCallBack, maxIterations, i) {
            maxIterations = !maxIterations ? 10 : maxIterations;
            i = !i ? 0 : i + 1;
            if (i > maxIterations) {
                // Error, too long waiting for function to evaluate true.
                if (forceCallBack) {
                    callBack();
                }
                return;
            }
            if (func()) {
                callBack();
            } else {
                var self = this;
                window.setTimeout(function() {
                    self.whenTrue(func, callBack, forceCallBack, maxIterations, i);
                }, 200);
            }
        },

        /**
         * Scroll a specific dom element into the viewport.
         * @param {Object} el
         */
        scrollToElement: function(el) {
            var navheight = $('#mr-nav').outerHeight();

            if (!el.length) {
                // Element does not exist so exit.
                return;
            }
            if (el.length > 1) {
                // If collection has more than one element then exit - we can't scroll to more than one element!
                return;
            }
            var scrtop = el.offset().top - navheight;
            $('html, body').animate({
                scrollTop: scrtop
            }, 600);
        },

        /**
         * Does the browser support session storage?
         * @returns {null|bool}
         */
        supportsSessionStorage: function() {
            if (staticSupportsSessionStorage !== null) {
                return staticSupportsSessionStorage;
            }
            if (typeof window.sessionStorage === 'object') {
                try {
                    window.sessionStorage.setItem('sessionStorage', 1);
                    window.sessionStorage.removeItem('sessionStorage');
                    staticSupportsSessionStorage = true;
                } catch (e) {
                    staticSupportsSessionStorage = false;
                }
            }
            return staticSupportsSessionStorage;
        },

        /**
         * Process all animated images (GIFs).
         */
        processAnimatedImages: function() {
            // Put animated images in a wrap if necessary.
            let gifs = $('img[src$=".gif"]:not(.texrender):not(.snap-feature-image)');
            // Use main page and course page.
            const indexPage = $('#page-site-index');
            const coursePage = $('.path-course-view');
            if (indexPage.length || coursePage.length) {
                gifs.each(function() {
                    if (!$(this).parent().hasClass('snap-animated-image')) {
                        $(this).wrap('<div class="snap-animated-image"></div>');
                        let animImage = $(this).parent();
                        (function() {
                            return str.get_strings([
                                {key: 'pausegraphicsanim', component: 'theme_snap'},
                                {key: 'resumegraphicsanim', component: 'theme_snap'},
                            ]);
                        })()
                            .then(function(localizedstrings) {
                                return templates.render('theme_snap/animated_graphics_pause', {
                                    pausegraphicsanim: localizedstrings[0],
                                    resumegraphicsanim: localizedstrings[1],
                                });
                            })
                            .then(function(html) {
                                animImage.append(html);
                                // Add events to hide/show the buttons to control if is GIF paused or not.
                                var playButtons = animImage.find('.anim-play-resume-buttons');
                                // Buttons are hidden by default.
                                playButtons.css('display', 'none');
                                animImage.mouseover(function() {
                                    playButtons.css('display', 'inline-block');
                                });
                                animImage.mouseout(function() {
                                    playButtons.css('display', 'none');
                                });
                                animImage.focusin(function() {
                                    playButtons.css('display', 'inline-block');
                                });
                            });
                    }
                });
            }
        },

        /**
         * Adds the correct section return to the modchooser, so use it only where it makes sense.
         */
        modchooserSectionReturn: function() {
            if (document.querySelector('body.path-course') !== null) {
                let choosers = document.querySelectorAll('button.section-modchooser-link');
                if (choosers.length !== 0) {
                    choosers.forEach(el => {
                        el.addEventListener('click', e => {
                            let sectionNum = e.target.closest('[data-sectionid]').getAttribute('data-sectionid');
                            let modchooserObserver = new MutationObserver(function() {
                                let loadedModules = document.querySelectorAll('[role="menuitem"]' +
                                    ' [data-region="chooser-option-info-container"]' +
                                    ' a[data-action="add-chooser-option"]');
                                if (loadedModules.length > 0) {
                                    loadedModules.forEach(el => {
                                        let link = el.href + '&sr=' + sectionNum;
                                        el.setAttribute('href', link);
                                    });
                                }
                            });
                            let modchooserObserverConfig = {
                                childList: true,
                                subtree: true,
                            };
                            modchooserObserver.observe(document.body, modchooserObserverConfig);
                        });
                    });
                }
            }
        }
    };
});
