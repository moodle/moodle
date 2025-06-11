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
 * along with Moodle. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @module    theme_snap/accessibility
 * @author    Oscar Nadjar oscar.nadjar@openlms.net
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JS code to assign attributes and expected behavior for elements in the Dom regarding accessibility.
 */
define(['jquery', 'core/str', 'core/event', 'theme_boost/bootstrap/tools/sanitizer', 'theme_boost/popover'],
    function($, str, Event, { DefaultWhitelist }) {
        return {
            snapAxInit: function(localJouleGrader, allyReport, blockReports, localCatalogue) {

                /**
                 * Module to get the strings from Snap to add the aria-label attribute to new accessibility features.
                 */
                str.get_strings([
                    {key: 'accessforumstringdis', component: 'theme_snap'},
                    {key: 'accessforumstringmov', component: 'theme_snap'},
                    {key: 'calendar', component: 'calendar'},
                    {key: 'accessglobalsearchstring', component: 'theme_snap'},
                    {key: 'viewcalendar', component: 'theme_snap'},
                    {key: 'viewmyfeedback', component: 'theme_snap'},
                    {key: 'viewmessaging', component: 'theme_snap'},
                    {key: 'viewforumposts', component: 'theme_snap'},
                    {key: 'editcoursesettings', component: 'theme_snap'},
                    {key: 'gradebook', component: 'local_joulegrader'},
                    {key: 'gradebook', component: 'core_grades'},
                    {key: 'numparticipants', component: 'core_message'},
                    {key: 'pld', component: 'theme_snap'},
                    {key: 'competencies', component: 'core_competency'},
                    {key: 'outcomes', component: 'core_outcome'},
                    {key: 'badges', component: 'core_badges'},
                    {key: 'coursereport', component: 'report_allylti'},
                    {key: 'pluginname', component: 'local_catalogue'},
                    {key: 'experimental', component: 'block_reports'}
                ]).done(function(stringsjs) {
                    if ($("#page-mod-forum-discuss")) {
                        $("div[data-content='forum-discussion'] select.custom-select.singleselect")
                        .attr("aria-label", stringsjs[0]);
                        $("div[data-content='forum-discussion'] div.movediscussionoption " +
                            "select.custom-select.urlselect").attr("aria-label", stringsjs[1]);
                    }
                    $("i.fa-calendar").parent().attr("aria-label", stringsjs[2]);
                    $("input[name='TimeEventSelector[calendar]']").attr('aria-label', stringsjs[2]);
                    var searchbutton = $("#mr-nav .simplesearchform a.btn.btn-open");
                    $(searchbutton).attr({
                        title: stringsjs[3],
                        'aria-label': stringsjs[3],
                    });

                    // Add needed ID's for course dashboard.
                    // These ID's were added for the most used elements in the course dashboard.

                    // There is not a lang string that contains {$a} participants with capital P, and this function helps with that.
                    // Taken from https://css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive/
                    $.expr[":"].contains = $.expr.createPseudo(function(arg) {
                        return function(elem) {
                            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
                        };
                    });
                    var ctparticipantsnumber = stringsjs[11].split(" ");
                    $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[8] + '")')
                        .attr("id", "ct-course-settings");
                    $('section#coursetools ul#coursetools-list a:contains("' + ctparticipantsnumber[1] + '")')
                        .attr("id", "ct-participants-number");
                    $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[13] + '")')
                        .attr("id", "ct-competencies");
                    $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[14] + '")')
                        .attr("id", "ct-outcomes");
                    $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[15] + '")')
                        .attr("id", "ct-badges");

                    // Check if the plugins are installed to pass the strings. These parameters are being passed from
                    // $initaxvars in snap/classes/output/shared.php. More validations can be added if needed.
                    if (localJouleGrader) {
                        $('section#coursetools ul#coursetools-list a:contains("' + 'Open Grader' + '")')
                            .attr("id", "ct-open-grader");
                        $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[9] + '")')
                            .attr("id", "ct-course-gradebook");
                    } else {
                        $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[10] + '")')
                            .attr("id", "ct-course-gradebook");
                    }
                    if (blockReports) {
                        $('section#coursetools ul#coursetools-list a:contains("' + 'Open Reports' + '")')
                            .attr("id", "ct-open-reports");
                        $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[18] + '")')
                            .attr("id", "ct-open-reports-experimental");
                    }
                    if (allyReport) {
                        $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[16] + '")')
                            .attr("id", "ct-ally");
                    }
                    if (localCatalogue) {
                        $('section#coursetools ul#coursetools-list a:contains("' + stringsjs[17] + '")')
                            .attr("id", "ct-open-catalogue");
                    }

                    // Add ARIA attributes.
                    $('div[role="main"] div.sitetopic ul.section.img-text').attr('role', 'presentation');
                });

                $(document).ready(function() {
                    // Add necessary attributes to needed DOM elements to new accessibility features.
                    $("#page-mod-data-edit input[id*='url']").attr("type", "url").attr("autocomplete", "url");
                    $("#moodle-blocks aside#block-region-side-pre a.sr-only.sr-only-focusable").attr("tabindex", "-1");

                    // Focus first invalid input after a submit is done.
                    $('.mform').submit(function() {
                        $('input.form-control.is-invalid:first').focus();
                    });

                    // Retrieve value from the input buttons from add/remove users in a group inside a course.
                    var addtext = $('.groupmanagementtable #buttonscell p.arrow_button input[name="add"]').attr('value');
                    var removetext = $(".groupmanagementtable #buttonscell p.arrow_button input[name='remove']").attr('value');

                    // Snap tab panels.
                    new Tabpanel("snap-pm-accessible-tab");
                    new Tabpanel("modchooser-accessible-tab");

                    // Wrapping for dropdown elements in the actionable elements in an activity for PLD.
                    if( $(".dropdown-item.editing_pld").closest(".pld-dropdown").length == 0 ) {
                        $(".dropdown-item.editing_pld").wrap("<li class='pld-dropdown'></li>");
                    }
                    $(".dropdown-item.editing_pld").attr("role","button");

                    /**
                     * Store the references outside the event handler.
                     * Window reload to change the inputs value for Add and Remove buttons when adding new
                     * members to a group.
                     */
                    var $window = $(window);

                    /**
                     * Modifies attributes depending on the window size.
                     */
                    function checkWidth() {
                        var windowsize = $window.width();
                        if (windowsize < 1220) {
                            $(".groupmanagementtable #buttonscell p.arrow_button input[name='add']").attr("value", "+");
                            $(".groupmanagementtable #buttonscell p.arrow_button input[name='remove']").attr("value", "-");
                        } else if (windowsize > 1220) {
                            $(".groupmanagementtable #buttonscell p.arrow_button input[name='add']").attr("value", addtext);
                            $(".groupmanagementtable #buttonscell p.arrow_button input[name='remove']").attr("value", removetext);
                        }
                    }
                    // Execute on load
                    checkWidth();
                    // Bind event listener
                    $(window).resize(checkWidth);

                    /**
                     * Change for the active carousel slide.
                     */
                    function carouselAriaCurrentValue() {
                        var carouselindicator = $('#snap-site-carousel .carousel-indicators button');
                        carouselindicator.click(function (e) {
                            var element = $(e.target);
                            carouselindicator.attr('aria-current', false);
                            element.attr('aria-current', true);
                        });

                        /**
                         * Listener to change aria-current value dynamically.
                         */
                        var targetNode = document.getElementById('snap-carousel-container');

                        // Options for the observer (which mutations to observe)
                        var config = { attributes: true, childList: true, subtree: true };

                        // Callback function to execute when mutations are observed
                        var callback = () => {
                            $('.carousel-indicators button').attr('aria-current', false);
                            $('.carousel-indicators button.active').attr('aria-current', true);
                        };

                        // Create an observer instance linked to the callback function
                        var observer = new MutationObserver(callback);

                        if (targetNode) {
                            // Start observing the target node for configured mutations.
                            observer.observe(targetNode, config);
                        }
                    }
                    carouselAriaCurrentValue();

                    /**
                     * Creates a pause and resume cycles for Snap's carousel.
                     */
                    function carouselPausePlay() {
                        $('#snap-site-carousel').carousel({
                            interval: 6000,
                            pause: "false"
                        });

                        $('#play-button').click(function () {
                            $('#snap-site-carousel').carousel('cycle');
                        });
                        $('#pause-button').click(function () {
                            $('#snap-site-carousel').carousel('pause');
                        });
                    }
                    carouselPausePlay();
                });

                /**
                 * Add needed accessibility for tabs inside Snap.
                 * This makes use of Bootstrap accessible tab panel with WAI-ARIA with the arrow keys binding codes.
                 * @param {string} id
                 */
                function Tabpanel(id) {
                    this._id = id;
                    this.$tpanel = $('#' + id);
                    this.$tabs = this.$tpanel.find('.tab');
                    this.$panels = this.$tpanel.find('.tab-pane');
                    this.bindHandlers();
                    this.init();
                }

                Tabpanel.prototype.keys = {
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40
                };

                Tabpanel.prototype.init = function() {
                    var $tab;
                    this.$panels.attr('aria-hidden', 'true');
                    this.$panels.removeClass('active in');
                    $tab = this.$tabs.filter('.active');
                    if ($tab === undefined) {
                        $tab = this.$tabs.first();
                        $tab.addClass('active');
                    }
                    this.$tpanel
                        .find('#' + $tab.find('a').attr('aria-controls'))
                        .addClass('active in').attr('aria-hidden', 'false');
                };

                Tabpanel.prototype.switchTabs = function($curTab, $newTab) {
                    var $curTabLink = $curTab.find('a'),
                        $newTabLink = $newTab.find('a');
                    $curTab.removeClass('active');
                    $curTabLink.attr('tabindex', '-1').attr('aria-selected', 'false');
                    $newTab.addClass('active');
                    $newTabLink.attr('aria-selected', 'true');
                    this.$tpanel
                        .find('#' + $curTabLink.attr('aria-controls'))
                        .removeClass('active in').attr('aria-hidden', 'true');
                    this.$tpanel
                        .find('#' + $newTabLink.attr('aria-controls'))
                        .addClass('active in').attr('aria-hidden', 'false');
                    $newTabLink.attr('tabindex', '0');
                    $newTabLink.focus();
                };

                Tabpanel.prototype.bindHandlers = function() {
                    var self = this;
                    this.$tabs.keydown(function(e) {
                        return self.handleTabKeyDown($(this), e);
                    });
                    this.$tabs.click(function(e) {
                        return self.handleTabClick($(this), e);
                    });
                };

                Tabpanel.prototype.handleTabKeyDown = function($tab, e) {
                    var $newTab, tabIndex;
                    switch (e.keyCode) {
                        case this.keys.left:
                        case this.keys.up: {
                            tabIndex = this.$tabs.index($tab);
                            if (tabIndex === 0) {
                                $newTab = this.$tabs.last();
                            } else {
                                $newTab = this.$tabs.eq(tabIndex - 1);
                            }
                            this.switchTabs($tab, $newTab);
                            e.preventDefault();
                            return false;
                        }
                        case this.keys.right:
                        case this.keys.down: {
                            tabIndex = this.$tabs.index($tab);
                            if (tabIndex === this.$tabs.length - 1) {
                                $newTab = this.$tabs.first();
                            } else {
                                $newTab = this.$tabs.eq(tabIndex + 1);
                            }
                            this.switchTabs($tab, $newTab);
                            e.preventDefault();
                            return false;
                        }
                    }
                };

                Tabpanel.prototype.handleTabClick = function($tab) {
                    var $oldTab = this.$tpanel.find('.tab.active');
                    this.switchTabs($oldTab, $tab);
                };
            },

            /**
             * Custom form error event handler to manipulate the bootstrap markup and show
             * nicely styled errors in an mform focusing the necessary elements in the form.
             * @param {string} elementid
             */
            enhanceform: function(elementid) {
                var element = document.getElementById(elementid);
                $(element).on(Event.Events.FORM_FIELD_VALIDATION, function(event, msg) {
                    event.preventDefault();
                    var parent = $(element).closest('.form-group');
                    var feedback = parent.find('.form-control-feedback');
                    var invalidinput = parent.find('input.form-control.is-invalid');

                    // Sometimes (atto) we have a hidden textarea backed by a real contenteditable div.
                    if (($(element).prop("tagName") == 'TEXTAREA') && parent.find('[contenteditable]')) {
                        element = parent.find('[contenteditable]');
                    }
                    if (msg !== '') {
                        parent.addClass('has-danger');
                        parent.data('client-validation-error', true);
                        $(element).addClass('is-invalid');
                        $(element).attr('aria-describedby', feedback.attr('id'));
                        $(element).attr('aria-invalid', true);
                        invalidinput.attr('tabindex', 0);
                        feedback.html(msg);

                        // Only display and focus when the error was not already visible.
                        if (!feedback.is(':visible')) {
                            feedback.show();
                            feedback.focus();
                        }
                    } else {
                        if (parent.data('client-validation-error') === true) {
                            parent.removeClass('has-danger');
                            parent.data('client-validation-error', false);
                            $(element).removeClass('is-invalid');
                            $(element).removeAttr('aria-describedby');
                            $(element).attr('aria-invalid', false);
                            feedback.hide();
                        }
                    }
                });
            },

            /**
             * Override the options from theme_boost/loader::enablePopovers to enhance accesibility (VPAT).
             */
            setManualPopovers: function() {
                const btnSelector = '.iconhelp.btn';

                $('body').popover({
                    selector: '[data-toggle="popover"]',
                    trigger: 'manual',
                    container: 'body',
                    whitelist: Object.assign(DefaultWhitelist, {
                        table: [],
                        thead: [],
                        tbody: [],
                        tr: [],
                        th: [],
                        td: [],
                    }),
                });

                // Prevent Bootstrap from automatically reacting to button focus
                $(btnSelector).on('focusin', function(e) {
                    e.stopImmediatePropagation();
                });

                $(btnSelector).on('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const $el = $(this);
                    const isOpen = $el.attr('aria-expanded') === 'true';

                    if (isOpen) {
                        $el.popover('hide');
                    } else {
                        $el.popover('show');
                    }
                });

                $(btnSelector).on('keydown', function(e) {
                    const $el = $(this);

                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        const isOpen = $el.attr('aria-expanded') === 'true';

                        if (isOpen) {
                            $el.popover('hide');
                        } else {
                            $el.popover('show');
                        }
                    }

                    if (e.key === 'Escape') {
                        $el.popover('hide');
                    }
                });

                $(btnSelector).on('shown.bs.popover', function () {
                    const popover = $(this).data('bs.popover').tip;
                    $(this).attr('aria-controls', popover.id);
                    $(this).attr('aria-expanded', true);
                    $(popover).insertAfter($(this));
                    $(popover).popover('update');
                });

                $(btnSelector).on('hidden.bs.popover', function () {
                    $(this).attr('aria-expanded', false);
                });
            }

        };
    }
);
