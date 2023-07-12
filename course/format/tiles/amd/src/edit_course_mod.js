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
 * Javascript Module to handle edit actions on course modules.
 *
 * @module      edit_course_mod
 * @package     course/format
 * @subpackage  tiles
 * @copyright   2018 David Watson {@link http://evolutioncode.uk}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.3
 */

define(["jquery", "core/ajax", "core/templates", "core/notification", "core/str", "core/url", "core/config"],
    function ($, ajax, Templates, Notification, str, url, config) {
        "use strict";

        /**
         * Keep references for all modals we have already added to the page,
         * so that we can relaunch then if needed without asking server
         * @type {{}}
         */
        var stringStore = {};

        var body = $("html,body");
        var page = $("#page");

        var Selector = {
            MENU_ACTION: ".menu-action-text",
            SUBTILE: ".subtile",
            SPACER: ".spacer",
            AVAIL_INFO: ".availabilityinfo",
            ACTIVITY_INSTANCE: ".subtile-activityinstance",
            INSTANCE_NAME: ".instancename",
            SECTION_CM_EDIT_ACTIONS: ".section-cm-edit-actions",
            EDITING_MOVE: ".editing_move",
            LABEL_CONVERT: ".editing_labelconvert",
            CM_EDIT_ACTION: ".cm-edit-action",
            ACTIVITY_ICON: ".activityicon",
            EDITING_DELETE: ".editing_delete",
            SECTION_MAIN: ".section.main",
            STEALTH_UNAVAIL_LINK: '.editing_makeunavailable'
        };

        var ClassNames = {
            SHOW: "show",
            HIDE: "hide",
            FA_EYE_SLASH: "fa-eye-slash",
            FA_EYE: "fa-eye",
            DIMMED: "dimmed",
            EDITING: "editing_",
            ACTIVITY: "activity",
            LABEL: "label",
            SECTION_DRAGGABLE: "sectiondraggable"
        };

        var Event = {
            CLICK: "click",
            MODULE_ADDED: "filter-content-updated",
            MOUSEDOWN: "mousedown"
        };

        /**
         * This dates from before the change to using AJAX for editing teacher content.
         * Once the new functionality is no longer experimental, this can be removed.
         *
         *  If an editing user clicks a show/hide menu item on a course module
         * @param {object} clickEvent
         */
        var legacyToggleHideCourseMod = function (clickEvent) {
            var clickedLink = $(clickEvent.currentTarget);
            var actions;
            if (clickedLink.attr("data-action") === "tiles-hide") {
                actions = {changeTo: ClassNames.HIDE, old: ClassNames.SHOW};
                $(Selector.STEALTH_UNAVAIL_LINK).hide();
            } else if (clickedLink.attr("data-action") === "tiles-show") {
                actions = {changeTo: ClassNames.SHOW, old: ClassNames.HIDE};
            }
            if (actions) {
                clickEvent.preventDefault();
                clickedLink.attr("data-action", "tiles-" + actions.old);
                var promises = ajax.call([{
                    methodname: "core_course_edit_module",
                    args: {
                        id: clickedLink.attr("data-cmid"),
                        action: actions.changeTo
                    }
                }], true);
                promises[0].done(function (HTMLresult) {
                    // This core web service returns the new HTML for the course module.
                    // However we don't want to use it, as we are using different templates for the CM.
                    // To save re-implementing the core function, we replace elements in the old HTML instead of new.
                    // TODO - consider if it is preferable to re-implement the core function or re-render CM.

                    clickedLink.removeClass(ClassNames.EDITING + actions.old).addClass(ClassNames.EDITING + actions.changeTo);
                    clickedLink.attr(
                        "href", clickedLink.attr("href").replace("&" + actions.old + "=", "&" + actions.changeTo + "=")
                    );
                    // Replace the string on the menu item to "Hide" or "Show", and icon, as appropriate to reflect new state.
                    var menuActionText = clickedLink.find(Selector.MENU_ACTION);
                    var subtile = clickedLink.closest(Selector.SUBTILE);
                    if (actions.changeTo === ClassNames.SHOW) {
                        menuActionText.html(menuActionText.html().replace(stringStore.show, stringStore.hide));
                        clickedLink.find("i").removeClass(ClassNames.FA_EYE_SLASH).addClass(ClassNames.FA_EYE);
                        subtile.removeClass(ClassNames.DIMMED); // Dim the related sub-tile.
                    } else if (actions.changeTo === ClassNames.HIDE) {
                        menuActionText.html(menuActionText.html().replace(stringStore.hide, stringStore.show));
                        clickedLink.find("i").removeClass(ClassNames.FA_EYE).addClass(ClassNames.FA_EYE_SLASH); // Replace the icon.
                        subtile.addClass(ClassNames.DIMMED);
                    }
                    // Now replace the availability info displayed on the sub tile.
                    var newAvailabilityInfo = $(HTMLresult).find(Selector.AVAIL_INFO);
                    var oldAvailabilityInfo = subtile.find(Selector.AVAIL_INFO);
                    if (newAvailabilityInfo) {
                        if (oldAvailabilityInfo.length) {
                            oldAvailabilityInfo.replaceWith(newAvailabilityInfo);
                        } else {
                            newAvailabilityInfo.appendTo(subtile.find(Selector.ACTIVITY_INSTANCE));
                        }
                    } else {
                        oldAvailabilityInfo.hide();
                    }
                });
                promises[0].fail(function () {
                    // We failed to use JS to handle so refer user to the original PHP URL instead.
                    window.location.replace(clickedLink.attr("href"));
                });
            }
        };

        /**
         * This dates from before the change to using AJAX for editing teacher content.
         * Once the new functionality is no longer experimental, this can be removed.
         *
         * When a new item is added to course by drag drop, we have to convert it to subtile format
         * For this we need to know which class to add to it (e.g. ppt) so that it has the correct icon
         * and colouring on the sub tile.  The easiest way to work this out is to use the icon URL
         * from the standard activity
         * @param {string} iconURL the icon URL oe.g. [wwwroot]/theme/image.php/boost/core/1535409577/f/pdf-24
         * @returns {string} the type e.g. 'pdf'
         */
        var legacyFileTypeFromIconURL = function (iconURL) {
            var extensions = {
                powerpoint: "ppt",
                document: "doc",
                spreadsheet: "xls",
                archive: "zip",
                pdf: "pdf",
                mp3: "mp3",
                mpeg: "mp4",
                jpeg: "jpeg",
                text: "txt",
                html: "html"
            };
            // Return the type corresponding to the last item in the URL, excluding anything after '-'.
            return extensions[iconURL.split("/").slice(-1)[0].split("-")[0]];
        };

        /**
         * This dates from before the change to using AJAX for editing teacher content.
         * Once the new functionality is no longer experimental, this can be removed.
         *
         * When a new course module is dragged and dropped into the course
         * we need its attributes so that we can convert it into a sub tile if needed
         * This assembles them ready to be sent to the mustache template
         *
         * @param {object} cmObject the jquery object added
         * @param {string} modResourceType type of module e.g. 'page'
         * @param {string} displayname e.g. "Spreadsheet"
         * @returns {object} promise for the module attributes
         */
        var legacyCourseModGetSubtileAttributes = function (cmObject, modResourceType, displayname) {
            // TODO would be better to handle this whole function with a web service for cm data?
            if (displayname === undefined || displayname === "") {
                // Failed to get string for this type
                displayname = stringStore.other; // Use "other" instead of proper word.
            }
            // We take the course module edit menu from the new CM which core added to the course.
            // We remove the data-action attributes from the hide menu item, as we dont want it to call JS.
            // if it called JS, the result would not only hide the item (good) but re-render it using standard core template (bad).
            cmObject.find('a.cm-edit-action').each(function (index, editItem) {
                $(editItem).attr('data-action', '');
            });
            cmObject.find('a.editing_moveright').remove(); // We do not use this with subtiles (indent).
            var returnData = {
                cmid: cmObject.attr("id").split("-").slice(-1)[0], // Last item.
                modtitle: cmObject.find(Selector.INSTANCE_NAME).html().split("<")[0],
                cmeditmenu: cmObject.find(Selector.SECTION_CM_EDIT_ACTIONS)[0].outerHTML.replace(/\n/g, ""),
                cmmove: cmObject.find(Selector.EDITING_MOVE)[0].outerHTML,
                modname: "resource",
                modResourceType: modResourceType,
                modnameDisplay: displayname,
                useSubtiles: 1,
                isEmbeddedResource: 0,
                clickable: 1,
                isediting: 1,
                visible: 1
            };
            returnData.isPdf = returnData.modResourceType === "pdf";
            return returnData;
        };

        /**
         * This dates from before the change to using AJAX for editing teacher content.
         * Once the new functionality is no longer experimental, this can be removed.
         * @param {number} courseId
         * @param {object} addedCourseModule
         * @param {object} sectionAddedTo
         * @param {string} msg
         */
        var legacyHandleCmAddedToPage = function(courseId, addedCourseModule, sectionAddedTo, msg) {
            if (sectionAddedTo.hasClass(ClassNames.SECTION_DRAGGABLE)
                && window.location.href.indexOf('expand=') === -1) {
                // An item has been dragged into a section when we are on the multi tile screen.
                // However the section is not yet expanded.
                // Therefore expand the section it has been dragged into so teacher can see it.
                window.location = config.wwwroot + '/course/view.php?id=' + courseId
                    + "&expand=" + sectionAddedTo.attr("data-section")
                    + "#section-" + sectionAddedTo.attr("data-section");
            } else if (addedCourseModule.hasClass(ClassNames.LABEL)
                && addedCourseModule.closest('ul').hasClass('subtiles')) {
                // The mod type is probably an image (being dragged in to the course.
                // When this happens, core adds a label and puts it in.
                // So allow this to happen, then reload the page.
                // This ensures the image displays correctly (only bother if we are using subtiles).
                window.location.reload();
            } else if (
                addedCourseModule.hasClass(ClassNames.ACTIVITY) && addedCourseModule.closest('ul').hasClass('subtiles')
            ) {
                // Only course modules and not (for example) user tours modal.
                // If we are not using sub tiles in section zero, don't bother changing it there.
                addedCourseModule.children().hide();
                addedCourseModule.append($("<img/>")
                    .attr("src", url.imageUrl("loading", "format_tiles"))
                    .addClass("loading-subtile").attr('title', stringStore.loading));
                var previousNonSpacer = addedCourseModule.prevAll(Selector.SUBTILE).not(Selector.SPACER).first();
                addedCourseModule.prevUntil(previousNonSpacer, Selector.SPACER).hide();

                // Get cmid, modtitle, modnameDisplay, cmeditmenu of ite just added to course by AJAX.
                // Re-render it in the correct style for this format (as sub tile).
                var modResourceType = legacyFileTypeFromIconURL(addedCourseModule.find(Selector.ACTIVITY_ICON).attr("src"));
                if (modResourceType === undefined) {
                    // We have probably dragged an image into the course and chosen to add it as a file resource.
                    window.location.reload();
                }
                var stringKey = "displaytitle_mod_" + modResourceType;
                if (stringStore[stringKey] === undefined) {
                    str.get_string(stringKey, "format_tiles")
                        .done(function (string) {
                            if (string.substring(0, 2) !== "[[") {
                                // The [[ means unknown string.
                                stringStore[stringKey] = string;
                            } else {
                                string = "";
                            }
                            var cmAttributes = legacyCourseModGetSubtileAttributes(
                                addedCourseModule, modResourceType, string
                            );
                            Templates.render(
                                "format_tiles/course_module",
                                cmAttributes
                            ).done(function (html) {
                                addedCourseModule.replaceWith(html);
                                // Flash the new item to bring attention to it.
                                var newItem = $("#" + $(msg).attr("id"));
                                body.animate({scrollTop: newItem.offset().top - 130}, "fast");
                                for (var x = 0; x < 3; x++) {
                                    newItem.fadeOut(300).fadeIn(300);
                                }
                                // The move cm icon will not work, so if is clicked, we refresh page so it works.
                                $('#module-' + cmAttributes.cmid).find('.editing_move')
                                    .attr('data-action', '')
                                    .on(Event.MOUSEDOWN, function() {
                                        window.location.reload();
                                    });
                            });
                        });
                } else {
                    var cmAttributes = legacyCourseModGetSubtileAttributes(
                        addedCourseModule, modResourceType, stringStore[stringKey]
                    );
                    Templates.render("format_tiles/course_module", cmAttributes).done(function (html) {
                        addedCourseModule.replaceWith(html);
                        // The move cm icon will not work, so if is clicked, we refresh page so it works.
                        $('#module-' + cmAttributes.cmid).find('.editing_move')
                            .attr('data-action', '')
                            .on(Event.MOUSEDOWN, function() {
                                window.location.reload();
                            });
                    });
                }
            } else {
                // We are leaving the course module in the old style as added by core drag-drop.
                // Adjust the inner div styling slightly to make it consistent with the others.
                addedCourseModule.find('div.mod-indent-outer')
                    .css('position', 'absolute').css('top', '0')
                    .css('width', '100%').css('padding-left', '0');
            }
        };

        return {
            init: function (courseId, displaySection, convertedLabel) {
                $(document).ready(function () {
                    // This is to cover something which can happen when dragging and dropping a course module.
                    // In course/amd/src/action.js is a method called M.course.coursebase.register_module.
                    // This is called on each drag drop and results in tbe course module which is drag dropped,
                    // and refreshed afterwards, changing  it in the process to a non tiles format.  This stops that.
                    // Ugly but seems to work and can't see another easy way to stop it firing.
                    Y.use("moodle-course-coursebase", function () {
                        // Clear existing instance variable to stop it - delay is to ensure page is loaded.
                        if (typeof M.course !== "undefined") {
                            setTimeout(function () {
                                M.course.coursebase.registermodules = [];
                            }, 500);
                        }
                    });

                    // User wants to convert a label to a page using action menu on label.
                    $(Selector.LABEL_CONVERT).on(Event.CLICK, function (e) {
                        e.preventDefault();
                        Notification.confirm(
                            stringStore.areyousure,
                            stringStore.converttopage_confirm,
                            stringStore.yes,
                            stringStore.no,
                            function () {
                                window.location = $(e.currentTarget).attr("href");
                            },
                            null
                        );
                    });

                    if (convertedLabel !== 0) {
                        // User has just converted a label to a page, so highlight the new item they just converted.
                        var newPage = $("#module-" + convertedLabel);
                        body.animate({scrollTop: newPage.offset().top - 130}, "fast");
                        for (var x = 0; x < 3; x++) {
                            newPage.fadeOut(300).fadeIn(300);
                        }
                    }

                    // If user clicks show/hide on a course module.
                    page.on("click", Selector.CM_EDIT_ACTION, function (e) {
                        legacyToggleHideCourseMod(e);
                    });

                    // Listen for new items being added to course by drag drop and convert them to subtile format.
                    // Otherwise the core JS will add them to the page in standard "list" format.
                    // This can be moved to edit_course once legacy functions are removed.
                    $(document).on(Event.MODULE_ADDED, function (event, msg) {
                        var addedCourseModule = $("#" + $(msg).attr("id"));
                        var sectionAddedTo = addedCourseModule.closest(Selector.SECTION_MAIN);
                        if (sectionAddedTo.length >= 1) {
                            legacyHandleCmAddedToPage(courseId, addedCourseModule, sectionAddedTo, msg);
                        }
                    });

                    // If the teacher deletes a subtile, we need to hide any "spacer" subtiles directly before it.
                    // We want none between what was the previous activity to the one we just deleted, and the next.
                    page.on(Event.CLICK, Selector.EDITING_DELETE, function (e) {
                        var deletedItem = $(e.currentTarget).closest("li" + ClassNames.ACTIVITY);
                        var previousNonSpacer = deletedItem.prevAll(Selector.SUBTILE).not(Selector.SPACER).first();
                        if (deletedItem.hasClass(ClassNames.LABEL)) {
                            deletedItem.prevUntil(previousNonSpacer, Selector.SPACER).hide();
                        }
                    });

                    str.get_strings([
                        {key: "yes"},
                        {key: "no"},
                        {key: "show"},
                        {key: "hide"},
                        {key: "converttopage_confirm", component: "format_tiles"},
                        {key: "areyousure"},
                        {key: "complete", component: "format_tiles"},
                        {key: "fileaddedtobottom", component: "format_tiles"},
                        {key: "loading", component: "format_tiles"}
                    ]).done(function (s) {
                        stringStore = {
                            "yes": s[0],
                            "no": s[1],
                            "show": s[2],
                            "hide": s[3],
                            "converttopage_confirm": s[4],
                            "areyousure": s[5],
                            "complete": s[6],
                            "fileaddedtobottom": s[7],
                            "loading": s[8]
                        };
                    });
                });
            }
        };
    }
);