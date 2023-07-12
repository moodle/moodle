// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/* eslint space-before-function-paren: 0 */

/**
 * Load the format_tiles JavaScript for the course edit settings page /course/edit.php?id=xxx
 *
 * @module      format_tiles
 * @package     course/format
 * @subpackage  tiles
 * @copyright   2018 David Watson {@link http://evolutioncode.uk}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/templates", "core/config", "core/ajax", "format_tiles/completion"],
    function ($, Templates, config, ajax) {
        "use strict";

        var courseId;
        var strings = {};
        var dataKeys = {
            cmid: "data-cmid",
            numberComplete: "data-numcomplete",
            numberOutOf: "data-numoutof",
            section: "data-section"
        };
        var Selector = {
            launchModuleModal: '[data-action="launch-tiles-module-modal"]',
            launchResourceModal: '[data-action="launch-tiles-resource-modal"]',
            pageContent: "#page-content",
            regionMain: "#region-main",
            resourceModule: '.activity.resource',
            completeonevent: ".completeonevent",
            completeonview: ".completeonview",
            activity: "li.activity",
            section: "li.section.main",
            togglecompletion: '[data-action="change-completion-status"]',
            tileId: "#tile-",
            progressIndicatorId: '#tileprogress-',
            tile: '.tile',
            spacer: '.spacer',
            availabilityinfo: '.availabilityinfo',
            sectionId: '#section-'
        };

        var Icon = {
            completionYes: 'completion-icon-y',
            completionNo: 'completion-icon-n'
        };

        /**
         * When toggleCompletionTiles() makes an AJAX call it needs to send some data
         * and this helps assemble the data
         * @param {number} tileId which tile is this for
         * @param {number} numComplete how many items has the user completed
         * @param {number} outOf how many items are there to complete
         * @param {boolean} asPercent should we show this as a percentage
         * @returns {{}}
         */
        var progressTemplateData = function (tileId, numComplete, outOf, asPercent) {
            var data = {
                tileid: tileId,
                numComplete: numComplete,
                numOutOf: outOf,
                showAsPercent: asPercent,
                percent: outOf > 0 ? Math.round(numComplete / outOf * 100) : 0,
                percentCircumf: 106.8,
                percentOffset: outOf > 0 ? Math.round(((outOf - numComplete) / outOf) * 106.8) : 0,
                isComplete: false,
                isSingleDigit: false,
                hastilephoto: $(Selector.tileId + tileId).hasClass("phototile"),
            };
            if (tileId === 0) {
                data.isOverall = 1;
            } else {
                data.isOverall = 0;
            }
            if (outOf > 0 && numComplete >= outOf) {
                data.isComplete = true;
            }
            if (data.percent < 10) {
                data.isSingleDigit = true;
            }
            return data;
        };

        /**
         * When a progress change happens, e.g. an item is marked as complete or not, this fires.
         * It changes the current tile's progress up or down by 1 according to the progressChange arg.
         * @param {int} sectionNum the number of this tile/section.
         * @param {object} tileProgressIndicator the indicator for this tile
         * @param {int} newTileProgressValue the new value
         */
        var changeProgressIndicatorSection = function(sectionNum, tileProgressIndicator, newTileProgressValue) {
            if (newTileProgressValue < 0 || newTileProgressValue > tileProgressIndicator.attr(dataKeys.numberOutOf)) {
                // If we are already at zero, do not reduce.  May happen rarely if user presses repeatedly.
                // Will not cause a long term issue as will be resolved when user refreshes page.
                return;
            }

            if (!sectionNum) {
                // Section zero doesn't have a section progress indicator.
                return;
            }

            // Render and replace the progress indicator for *this tile*.
            Templates.render("format_tiles/progress", progressTemplateData(
                sectionNum,
                newTileProgressValue,
                parseInt(tileProgressIndicator.attr(dataKeys.numberOutOf)),
                tileProgressIndicator.hasClass("percent")
            )).done(function (html) {
                // Need to repeat jquery selector as it is being replaced (replacwith).
                tileProgressIndicator.replaceWith(html);

            });
        };

        const setOverallProgressIndicator = function(newValue, outOf) {
            // Render and replace the *overall* progress indicator for the *whole course*.
            Templates.render("format_tiles/progress", progressTemplateData(
                0, newValue, outOf, true
            )).done(function (html) {
                $("#tileprogress-0").replaceWith(html).fadeOut(0).animate({opacity: 1}, 500);
            });
        };
        /**
         * When a user clicks a completion tracking checkbox in this format, pass the click through to core
         * This is partly based on the core functionality in completion.js but is included here as otherwise clicks on
         * check boxes added dynamically after page load are not detected
         * @param {number} cmid the course module id
         * @param {bool} completed the new completion status
         */
        var setCompletionState = function (cmid, completed) {
            ajax.call([{
                methodname: "format_tiles_update_activity_completion_status_manually",
                args: {
                    cmid: cmid,
                    completed: completed
                }
            }])[0]
                .done((res) => {
                    if (res.status) {
                        const section = $('#module-' + cmid).closest(Selector.section).attr('data-section');
                        if (section) {
                            triggerCompletionChangedEvent(section);
                        }
                        // Change the modal icon if appropriate.
                        const checkBox = $(Selector.togglecompletion + '[data-cmid="' + cmid + '"]');
                        if (checkBox) {
                            checkBox.attr('data-state', completed ? "1" : "0");
                            if (completed) {
                                checkBox.find('.completion_img_' + cmid)
                                    .addClass(Icon.completionYes).removeClass(Icon.completionNo);
                            } else {
                                checkBox.find('.completion_img_' + cmid)
                                    .addClass(Icon.completionNo).removeClass(Icon.completionYes);
                            }
                        }
                    }
                })
                .fail((err) => {
                    require(["core/log"], function(log) {
                        log.debug("Failed to set completion state");
                        log.debug(err);
                    });
                });
        };

        /**
         * When automatic completion tracking is being used, on modal launch we need to:
         * - change the completion icon to complete.
         * - recalculate the % complete for this tile and overall.
         * We do not need to notify the server that the item is complete.
         * This is because that is already covered when course_mod_modal calls log_mod_view().
         * I.e. we just update the UI here because the data is handled elsewhere.
         * @param {object} activity the activity which contains the completion icon
         */
        var markAsAutoCompleteInUI = function(activity) {
            var sectionNum = activity.closest(Selector.section).attr('data-section');
            if (activity.hasClass("completeonview")) {
                var completionIcon = activity.find('.completion-icon');
                var parent = completionIcon.closest(".completioncheckbox");
                if (parent.attr('data-ismanual') === "0" && parent.attr('data-completionstate') === "0") {
                    completionIcon.addClass(Icon.completionYes).removeClass(Icon.completionNo);
                    parent.attr('data-completionstate', 1);
                    parent.attr('data-original-title', strings.completeauto);

                    const tileProgressIndicator = $(Selector.progressIndicatorId + sectionNum);
                    // Get the tile's new progress value.
                    var newTileProgressValue = Math.min(
                        parseInt(tileProgressIndicator.attr(dataKeys.numberComplete)) + 1,
                        tileProgressIndicator.attr(dataKeys.numberOutOf)
                    );
                    changeProgressIndicatorSection(sectionNum, tileProgressIndicator, newTileProgressValue);

                    // Get the new overall progress value.
                    const overallProgressIndicator = $("#tileprogress-0");
                    const newOverallProgressValue = Math.min(
                        parseInt(overallProgressIndicator.attr(dataKeys.numberComplete)) + 1,
                        overallProgressIndicator.attr(dataKeys.numberOutOf)
                    );
                    setOverallProgressIndicator(
                        newOverallProgressValue, parseInt(overallProgressIndicator.attr(dataKeys.numberOutOf))
                    );
                }
            }
        };

        /**
         * Trigger an event so that other JS modules can be notified to check completion status.
         * Used to refresh section contents when completion is checked.
         * Can also be used by other components e.g. blocks that show completion.
         * @param {number} sectionNum the number of the section where completion changed.
         */
        const triggerCompletionChangedEvent = function (sectionNum) {
            $(document).trigger('format-tiles-completion-changed', {
                section: parseInt(sectionNum)
            });
        };

        /**
         * If we have called format_tiles_get_section_information then we need to add the result to the DOM.
         * @param {array} sections the section in
         * @param {number} overallcomplete how many activities complete in the section overall
         * @param {number}overalloutof how many activities in the section overall
         */
        const updateSectionsInfo = function(sections, overallcomplete, overalloutof) {
            sections.forEach(sec => {
                const tile = $(Selector.tileId + sec.sectionnum);
                // If this tile is now unrestricted / visible, give it the right classes.
                if (sec.isavailable && tile.hasClass('tile-restricted')) {
                    tile.removeClass('tile-restricted');
                } else if (!sec.isavailable) {
                    tile.addClass('tile-restricted');
                }
                if (sec.isclickable && !tile.hasClass('tile-clickable')) {
                    tile.addClass('tile-clickable');
                } else if (!sec.isclickable && tile.hasClass('tile-clickable')) {
                    tile.removeClass('tile-clickable');
                }

                // Now re-render the progress indicator if necessary with correct data.
                const progressIndicator = $(Selector.progressIndicatorId + sec.sectionnum);
                changeProgressIndicatorSection(sec.sectionnum, progressIndicator, sec.numcomplete);
                setOverallProgressIndicator(overallcomplete, overalloutof);

                // Finally change or re-render the availability message if necessary.
                const availabilityInfoDiv = tile.find(Selector.availabilityinfo);
                if (availabilityInfoDiv.length > 0 && sec.isavailable && !sec.availabilitymessage) {
                    // Display no message any more.
                    availabilityInfoDiv.fadeOut();
                } else if (!sec.isavailable && sec.availabilitymessage) {
                    // Sec is not available and we have a message to display.
                    if (availabilityInfoDiv.length > 0) {
                        availabilityInfoDiv.html = 'NEW' + sec.availabilitymessage;
                        availabilityInfoDiv.fadeIn();
                    } else {
                        Templates.render("format_tiles/availability_info", {
                            availabilitymessage: sec.availabilitymessage,
                            visible: true
                        }).done(function (html) {
                            // Need to repeat jquery selector as it is being replaced (replacwith).
                            progressIndicator.replaceWith(html);

                        });
                    }
                }
            });
        };

        /**
         * Sometimes we must check the availability and completion status of/some all tiles using AJAX.
         * This might happen if for example a tile expands and some embedded activities are then complete.
         * Other tiles might use the completion of a previous tile for their availability.
         * This especially applies if teh H5P filter is being used to display embedded H5P in labels.
         * @param {Number[]} sectionNums
         */
        var updateTileInformation = function (sectionNums) {
            if (sectionNums === undefined) {
                // Use all sections if no arg.
                sectionNums = $(Selector.tile).not(Selector.spacer).map((i, t) => {
                    return parseInt($(t).attr('data-section'));
                }).toArray();
            }
            ajax.call([{
                methodname: "format_tiles_get_section_information",
                args: {
                    courseid: courseId,
                    sectionnums: sectionNums
                }
            }])[0].done((res) => {
                    updateSectionsInfo(res.sections, res.overall.complete, res.overall.outof);
                })
                .fail(err => {
                    require(["core/log"], function(log) {
                        log.debug(
                            "Failed to get section information to check completion status of section"
                        );
                        log.debug(err);
                    });
                });
        };

        return {
            init: function (courseIdInit, strCompleteAuto) {
                courseId = courseIdInit;
                $(document).ready(function () {
                    strings.completeauto = strCompleteAuto;
                    // Trigger toggle completion event if check box is clicked.
                    // Included like this so that later dynamically added boxes are covered.
                    $("body").on("click", Selector.togglecompletion, function (e) {
                        // Send the toggle to the database and change the displayed icon.
                        e.preventDefault();
                        const target = $(e.currentTarget);
                        setCompletionState(
                            target.attr('data-cmid'),
                            target.attr('data-state') !== "1",
                        );
                    });

                    var pageContent = $("#page-content");
                    if (pageContent.length === 0) {
                        // Some themes e.g. RemUI do not have a #page-content div, so use #region-main.
                        pageContent = $("#region-main");
                    }
                    pageContent
                        .on("click", Selector.launchModuleModal + ", " + Selector.launchResourceModal, function (e) {
                            var clickedActivity = $(e.currentTarget).closest(Selector.activity);
                            if (clickedActivity.hasClass("completeonview")) {
                                markAsAutoCompleteInUI(clickedActivity);
                            }
                        });
                });
            },
            // Allow this to be accessed from elsewhere e.g. format_tiles module.
            markAsAutoCompleteInUI: function(courseIdInit, activity) {
                courseId = courseIdInit;
                markAsAutoCompleteInUI(activity);
            },
            triggerCompletionChangedEvent: function(sectionNum) {
                triggerCompletionChangedEvent(sectionNum);
            },
            updateTileInformation: function(sectionNumbers) {
                try {
                    updateTileInformation(sectionNumbers);
                } catch (err) {
                    require(["core/log"], function(log) {
                        log.debug(err);
                    });
                }
            },
            updateSectionsInfo: function(sections, overallcomplete, overalloutof) {
                updateSectionsInfo(sections, overallcomplete, overalloutof);
            }
        };
    }
);
