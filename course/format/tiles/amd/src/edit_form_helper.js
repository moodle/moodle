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
 * Javascript Module to handle changes which are made to the course > edit settings
 * form as the user changes various options
 * e.g. if user deselects one item, this deselects another linked one for them
 * if the user picks an invalid option it will be detected by format_tiles::edit_form_validation (lib.php)
 * but this is to help them avoid triggering that if they have JS enabled
 *
 * @module      edit_form_helper
 * @package     course/format
 * @subpackage  tiles
 * @copyright   2018 David Watson {@link http://evolutioncode.uk}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.3
 */

define(["jquery", "core/notification", "core/str", "core/templates"],
    function ($, Notification, str, Templates) {
        "use strict";
        return {
            init: function (pageType, courseDefaultIcon, courseId, sectionId, section, userId, allowphototiles, documentationUrl) {
                $(document).ready(function () {
                    const useSubTilesCheckBox = $("input#id_courseusesubtiles");
                    const useSubTilesSecZeroCheckBox = $("input#id_usesubtilesseczero");
                    if (!useSubTilesCheckBox.prop('checked')) {
                        // We cannot use sub tiles in top section if we are not using them at all.
                        useSubTilesSecZeroCheckBox.prop("checked", false);
                        useSubTilesSecZeroCheckBox.attr('disabled', true);
                    }
                    useSubTilesCheckBox.change(function () {
                        if (!useSubTilesCheckBox.prop('checked')) {
                            // We cannot use sub tiles in top section if we are not using them at all.
                            useSubTilesSecZeroCheckBox.prop("checked", false);
                            useSubTilesSecZeroCheckBox.attr('disabled', true);
                        } else {
                            // We are changing to use sub tiles.
                            // For convenience, uncheck the "Emphasise headings with coloured tab" box.
                            // User can change it back if they want.
                            $("input#id_courseusebarforheadings").prop("checked", false);
                            useSubTilesSecZeroCheckBox.attr('disabled', false);
                        }
                    });
                    $("select#id_courseshowtileprogress").change(function (e) {
                        if (e.currentTarget.value !== "0") {
                            var enableCompBox = $("select#id_enablecompletion");
                            if (enableCompBox.val() === "0") {
                                // We are changing to show progress on tiles
                                // For convenience, if completion tracking if off at course level, switch it on and tell the user.
                                // User can change it back if they want.  See under "completion tracking > enable.
                                enableCompBox.val("1");
                                str.get_strings([
                                    {key: "completion", component: "completion"},
                                    {key: "completionswitchhelp", component: "format_tiles"}
                                ]).done(function (s) {
                                    Notification.alert(
                                        s[0],
                                        s[1]
                                    );
                                });
                            }
                        }
                    });
                    $("select#id_enablecompletion").change(function (e) {
                        if (e.currentTarget.value === "0") {
                            // We are changing switch completion tracking off at course level too.
                            // See under "completion tracking > enable.
                            // It follows that we must be hiding progress on tiles too.
                            $("select#id_courseshowtileprogress").val("0");
                        }
                    });

                    // Create clickable colour swatch for each colour in the select drop down to help user choose.
                    var colourSelectMenu = $("select#id_basecolour");
                    Templates.render("format_tiles/colour_picker", {
                        colours: colourSelectMenu.find("option").map(
                            function (index, option) {
                                var optselector = $(option);
                                var colour = optselector.attr("value");
                                return {
                                    colour: colour,
                                    colourname: optselector.text(),
                                    selected: colour === colourSelectMenu.val(),
                                    id: colour.replace("#", "")
                                };
                            }
                        ).toArray()
                    }).done(function (html) {
                        // Add the newly created colour picker next to the standard select menu.
                        $(html).insertAfter(colourSelectMenu);
                        // Now that users are using the colour circles we can hide the text menu.
                        colourSelectMenu.hide();
                        // Watch for clicks on each circle and set select menu to correct colour on click.

                        var circles = $(".colourpickercircle");

                        circles.click(function (e) {
                            var clicked = $(e.currentTarget);
                            circles.removeClass("selected");
                            clicked.addClass("selected");
                            colourSelectMenu.val(clicked.attr("data-colour"));
                            $("#colourselectnotify").fadeIn(200).fadeOut(1200);
                        });

                        colourSelectMenu.change(function () {
                            circles.removeClass("selected");
                            $("#colourpick_" + colourSelectMenu.val().replace("#", "")).addClass("selected");
                        });

                        // If the course is being switched in to "Tiles", body will still have old format class e.g. format-topics.
                        // This comes from core.  We want body to have format-tiles class for our colour picker CSS, so we add it.
                        var body = $("body");
                        if (!body.hasClass("format-tiles")) {
                            body.addClass("format-tiles");
                        }
                    });

                    // If we are on the course edit settings form, render a button to be added to it.
                    // Put it next to the existing drop down select box for course default tile icon.
                    // Add it to the page.

                    var selectedIconName;
                    var selectBox;
                    if (pageType === "course-edit") {
                        selectBox = $("#id_defaulttileicon");
                        selectedIconName = $("#id_defaulttileicon option:selected").text();
                    } else if (pageType === "course-editsection") {
                        selectBox = $("#id_tileicon");
                        selectedIconName = $("#id_tileicon option:selected").text();
                    }
                    if (pageType === "course-edit" || (pageType === "course-editsection" && section !== "0")) {
                        var currentIcon;
                        if (selectBox.val() === "") {
                            currentIcon = courseDefaultIcon;
                        } else {
                            currentIcon = selectBox.val();
                        }
                        Templates.render("format_tiles/icon_picker_launch_btn", {
                            initialicon: currentIcon,
                            initialname: selectedIconName,
                            sectionId: sectionId,
                            allowphototiles: allowphototiles
                        }).done(function (html) {
                            $(html).insertAfter(selectBox);

                            // We can hide the original select box now as users will use the button instead.
                            selectBox.hide();
                            require(["format_tiles/edit_icon_picker"], function(iconPicker) {
                                iconPicker.init(courseId, pageType, allowphototiles, documentationUrl);
                            });
                        });
                    } else if (pageType === "course-editsection" && section === "0") {
                        selectBox.closest(".row").hide(); // Don't have an icon for section zero.
                    }

                    // Add a row to the page with link to plugin documentation.
                    Templates
                        .render("format_tiles/edit_form_helptext", {documentationurl: documentationUrl + 'teachers'})
                        .done(function (html) {
                            $(html).appendTo($("#id_courseformathdr .fcontainer"));
                        });
                });
            }
        };
    }
);