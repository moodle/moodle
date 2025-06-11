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
 * Course main functions.
 */
define(
    [
        'jquery',
        'theme_snap/util',
        'theme_snap/section_asset_management',
        'theme_snap/course_modules',
        'core/str'
    ],
    function($, util, sectionAssetManagement, courseModules, str) {

    /**
     * Return class(has private and public methods).
     * @param {object} courseConfig
     */
    return function(courseConfig) {

        var self = this;

        self.courseConfig = courseConfig;

        /**
         * Are we on the main course page - i.e. TOC is visible.
         * @returns {boolean}
         */
        var onCoursePage = function() {
            return $('body').attr('id').indexOf('page-course-view-') === 0;
        };

        /**
         * Scroll to a mod via search
         * @param {string} modid
         */
        var scrollToModule = function(modid) {
            // Sometimes we have a hash, sometimes we don't
            // strip hash then add just in case
            $('#toc-search-results').html('');
            var targmod = $("#" + modid.replace('#', ''));
            // http://stackoverflow.com/questions/6677035/jquery-scroll-to-element
            util.scrollToElement(targmod);

            var searchpin = $("#searchpin");
            if (!searchpin.length) {
                searchpin = $('<i id="searchpin"></i>');
            }

            $(targmod).find('.instancename').prepend(searchpin);
            $(targmod).attr('tabindex', '-1').focus();
            $('#course-toc').removeClass('state-visible');
        };

        /**
         * Mark the section shown to user with a class in the TOC.
         */
        this.setTOCVisibleSection = function() {
            var sectionIdSel = '.section.main.state-visible, #coursetools.state-visible, #snap-add-new-section.state-visible';
            var currentSectionId = $(sectionIdSel).attr('id');

            // Remove snap-visible-section class and reset aria-current to false for all chapters
            $('#chapters li').removeClass('snap-visible-section');
            $('#chapters li a').attr('aria-current', 'false');

            // Find the correct chapter link and update class and aria-current
            var visibleSectionLink = $('#chapters a[href$="' + currentSectionId + '"]');
            visibleSectionLink.parent('li').addClass('snap-visible-section');
            visibleSectionLink.attr('aria-current', 'true');
        };

        /**
         * Check if current url is having specific parameter on it.
         * @param {string} checkParameter
         */
        var checkToolParameter = function(checkParameter) {
            return window.location.href.indexOf(checkParameter) != -1;
        };

        /**
         * When on course page, show the section currently referenced in the location hash.
         */
        this.showSection = function() {
            if (!onCoursePage()) {
                // Only relevant for main course page.
                return;
            }

            // We know the params at 0 is a section id.
            // Params will be in the format: #section-[number]&module-[cmid], e.g: #section-1&module-7255.
            var urlParams = location.hash.split("&"),
                section = urlParams[0],
                mod = urlParams[1] || null;

            // Redirect to the correct section when doing /course/section.php.
            if (section === '' && location.pathname === '/course/section.php' && self.courseConfig.sectionnum) {
                section = '#section-' + self.courseConfig.sectionnum;
            }

            var sectionId = false;
            if (section.indexOf('#sectionid') != -1) {
                sectionId = section.match(/#sectionid-(\d+)-title/)[1];
            }

            // Check if we are using permalinks like #sectionid-{id}-title.
            if (sectionId) {
                // Search element with section-id.
                var $chapter = $('.chapters .chapter-title[section-id="' + sectionId + '"]');
                if ($chapter.length > 0) {
                    // Get the section-number associated.
                    section = $chapter.attr('section-number');
                    section = '#section-' + section;
                }
            }

            // We are done here. H5P will handle the section shown within its iframe.
            if (section.startsWith('#h5pbook')) {
                return;
            }

            var sectionSetByServer = '';

            if ($('.section.main.state-visible.set-by-server').length) {
                sectionSetByServer = '#' + $('.section.main.state-visible.set-by-server').attr('id');
                $('.section.main.state-visible.set-by-server').removeClass('set-by-server');
            } else {
                $('.course-content .section.main, #moodle-blocks,#coursetools, #snap-add-new-section,' +
                    '#tiles-section').removeClass('state-visible');
            }

            if (section == '') {
                var qs = location.search.substring(1);
                var sparameters = qs.split('&');
                sparameters.forEach(function(param) {
                    if (param.indexOf('section=') >= 0) {
                        param.replace(param);
                        section = '#' + param.replace('=', '-');
                    }
                });
            }

            if (section !== '' && section !== sectionSetByServer) {
                $(sectionSetByServer).removeClass('state-visible');
            }

            // Dashboard in Tiles should be hidden except in #coursetools section.
            let btnEditing = '.btn-editing';
            let courseTools = '#coursetools';
            let tilesEditing = $(courseTools).hasClass('editing-tiles');
            if (tilesEditing) {
                $(courseTools).removeClass('state-visible');
                $(courseTools).addClass('d-none');

                // Change duplicate data-action in label activities for Tiles.
                let labelDuplicateButton = $('.launch-tiles-standard.modtype_label .actions .editing_duplicate');
                if (labelDuplicateButton) {
                    $(labelDuplicateButton).attr("data-action", "tiles-duplicate");
                }
            }
            let sectionParameter = checkToolParameter('section-');
            let dashboardParameter = checkToolParameter('coursetools');
            if (sectionParameter && !dashboardParameter) {
                $('#tiles-section').addClass('state-visible');
                $(courseTools).removeClass('state-visible');
                $(courseTools).addClass('d-none');
            }
            if (!sectionParameter && dashboardParameter) {
                let tilesDashboard = $('#snap-course-tools').hasClass('tiles-dashboard');
                if (tilesDashboard) {
                    $('#tiles-section').removeClass('state-visible');
                    $(courseTools).addClass('state-visible');
                    $(courseTools).removeClass('d-none');
                    if ($(btnEditing).length) {
                        let urlEditing = document.querySelector(btnEditing).href;
                        let existToolParameter = urlEditing.includes('#coursetools');
                        if (!existToolParameter) {
                            str.get_strings([
                                {key: 'editcoursecontent', component: 'theme_snap'},
                                {key: 'editmodetiles', component: 'theme_snap'},
                                {key: 'turneditingoff', component: 'moodle'},
                            ]).done(function(stringsjs) {
                                let btnEditText = document.querySelector(btnEditing).text;
                                if (btnEditText == stringsjs[1]) {
                                    document.querySelector(btnEditing).innerHTML = stringsjs[0];
                                } else {
                                    document.querySelector(btnEditing).innerHTML = stringsjs[2];
                                }
                            });
                            document.querySelector(btnEditing).href = urlEditing + '#coursetools';
                        }
                    }
                }
                // Remove class d-none to show Course Dashboard after clicking in a section first.
                let snapCourseDashboard = $('#snap-course-tools').hasClass('snap-course-dashboard');
                if (snapCourseDashboard) {
                    $(courseTools).removeClass('d-none');
                }
            }

            // Course tools special section.
            if (section == '#coursetools') {
                $('#moodle-blocks').addClass('state-visible');
            }

            // If a modlue was in the hash then scroll to it.
            if (mod !== null) {
                $(section).addClass('state-visible');
                scrollToModule(mod);
            } else {
                $(section).addClass('state-visible').focus();
                // Faux link click behaviour - scroll to page top.
                scrollBack();
            }

            // Default niceties to perform.
            var visibleChapters = $(
                '.section.main.state-visible,' +
                '#coursetools.state-visible,' +
                '#snap-add-new-section.state-visible'
            );
            if (!visibleChapters.length) {
                if (section !== '') {
                    $(section).addClass('state-visible').focus();
                } else if ($('.section.main.current').length) {
                    $('.section.main.current').addClass('state-visible').focus();
                } else {
                    $('#section-0').addClass('state-visible').focus();
                }
                scrollBack();
            }
            if (section == '' && self.courseConfig.format == 'tiles') {
                $('#tiles-section').addClass('state-visible').focus();
            }

            // When usejsnavforsinglesection is enabled, tiles-section will be shown instead of single-section.
            // We need to ensure that tiles-section is visible when course tools is not.
            if (self.courseConfig.format == 'tiles') {
                if (!$(courseTools).hasClass('state-visible')
                    && !$('#tiles-section').hasClass('state-visible')) {
                    $('#tiles-section').addClass('state-visible');
                }
                if ($('#page-course-view-tiles .tiles[data-for="course_sectionlist"]').length) {
                    if (!$(courseTools).hasClass('state-visible')
                        && !$('#page-course-view-tiles .tiles').hasClass('state-visible')) {
                        $('#page-course-view-tiles .tiles').addClass('state-visible');
                    }
                }
                if (!dashboardParameter) {
                    $('#snap-course-dashboard').addClass('state-visible');
                }
            }

            // Store last activity/resource accessed on sessionStorage
            $('li.snap-activity:visible, li.snap-resource:visible').on('click', 'a.mod-link', function() {
                sessionStorage.setItem('lastMod', $(this).parents('[id^=module]').attr('id'));
            });


            this.setTOCVisibleSection();
        };

        /**
         * Scroll to the last activity or resource accessed,
         * if there is nothing stored in session go to page top.
         */
        var scrollBack = function() {
            var storedmod = sessionStorage.getItem('lastMod');
            if (storedmod === null) {
                window.scrollTo(0, 0);
            } else {
                util.scrollToElement($('#' + storedmod + ''));
                sessionStorage.removeItem('lastMod');
            }
        };

        /**
         * Captures hash parameters and triggers the render method.
         */
        var renderFromHash = function() {
            var hash = $(location).attr('hash');
            var params = hash.replace('#', '').split('&');
            var section = false;
            var sectionId = false;
            var mod = 0;

            $.each(params, function(idx, param) {
                if (param.indexOf('sectionid') != -1) {
                    sectionId = param.match(/sectionid-(\d+)-title/)[1];
                } else if (param.indexOf('section') != -1) {
                    section = param.split('section-')[1];
                } else if (param.indexOf('module') != -1) {
                    mod = param.split('module-')[1];
                }
            });

            // Check if we are using permalinks like #sectionid-{id}-title.
            if (sectionId) {
                // Search element with section-id.
                var $chapter = $('.chapters .chapter-title[section-id="' + sectionId + '"]');
                if ($chapter.length > 0) {
                    // Get the section-number associated.
                    section = $chapter.attr('section-number');
                }
            }

            if (!section) {
                var qs = location.search.substring(1);
                var sparameters = qs.split('&');
                sparameters.forEach(function(param) {
                    if (param.indexOf('section=') >= 0) {
                        param.replace(param);
                        section = param.replace('section=', '');
                    }
                });
            }
            if (section && $('.chapters .chapter-title[href="#section-' + section + '"]').length > 0) {
                sectionAssetManagement.renderAndFocusSection(section, mod);
            }
        };

        /**
         * Initialise course JS.
         */
        var init = function() {
            sectionAssetManagement.init(self);
            courseModules.init(courseConfig);

            // Only load the conditionals library if it's enabled for the course, viva la HTTP2!
            if (self.courseConfig.enablecompletion) {
                require(
                    [
                        'theme_snap/course_conditionals-lazy'
                    ], function(conditionals) {
                        conditionals(courseConfig);
                    }
                );
            }

            // SL - 19th aug 2014 - check we are in a course and if so, show current section.
            if (onCoursePage()) {
                self.showSection();
                $(document).on('snapTOCReplaced', function() {
                    self.setTOCVisibleSection();
                    if (self.courseConfig.partialrender) {
                        sectionAssetManagement.setTocObserver();
                    }
                });
                // Sets the observers for rendering sections on demand.
                if (self.courseConfig.partialrender) {
                    renderFromHash();
                    $(window).on('hashchange', function() {
                        renderFromHash();
                    });
                    // Current section might be hidden, at this point should be visible.
                    var sections = $('.course-content li[id^="section-"]');
                    var urlParams = location.hash.split("&"),
                        sectionParam = urlParams[0];
                    if (sections.length == 1 &&
                        sectionParam != '#coursetools' &&
                        sectionParam != '#snap-add-new-section') {
                        sections.addClass('state-visible');
                        var section = sections.attr('id').split('section-')[1];
                        if (self.courseConfig.toctype == 'top' && self.courseConfig.format == 'topics' && section > 0) {
                            var title = sections.find('.sectionname').html();
                            var elements = $('.chapter-title');
                            var tmpid = 0;
                            $.each(elements, function(key, element) {
                                if ($(element).attr('section-number') == section) {
                                    tmpid = key;
                                }
                            });
                            sections.find('.sectionname').html(title);
                            sections.find('.sectionnumber').html(tmpid + '.');
                        }
                    }


                }
            }
        };

        /**
         * Snap modchooser listener to add current section to urls.
         */
        var modchooserSectionLinks = function() {
            $('.section-modchooser-link').click(function() {
                // Grab the section number from the button.
                var sectionNum = $(this).attr('data-sectionid');
                $('.snap-modchooser-addlink').each(function() {
                    // Update section in mod link to current section.
                    var newLink = this.href.replace(/(section=)[0-9]+/ig, '$1' + sectionNum);
                    $(this).attr('href', newLink);
                });
            });
        };

        // Intialise course lib.
        init();
        modchooserSectionLinks();
    };
});
