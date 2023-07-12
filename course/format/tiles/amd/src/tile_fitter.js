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

/**
 * Javascript Module to handle fitting tiles to screen.
 * Called when in non editing mode.
 *
 * @module tile_fitter
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery", "core/ajax"], function ($, ajax) {
    "use strict";

    var reOrgLocked = false;
    var courseId;
    var Selector = {
        PAGE: "#page",
        TILE: ".tile",
        TILEID: "#tile-",
        TILE_COLLAPSED: ".tile-collapsed",
        TILES: ".format-tiles.jsenabled ul.tiles",
        ACTIVITY: ".activity",
        SPACER: ".spacer",
        SECTION_ID: "#section-",
        OPEN_SECTION: ".moveablesection:visible",
        SECTION_ZERO: "#section-0",
        CONTENT_SECTIONS: ".moveablesection"
    };

    // Used to store a delayed AJAX request so we can replace it if user sets again within one second or two.
    var timeoutBeforeResizeAjax = null;

    /**
     * If we have a single tile on the last row it looks odd.
     * We might want to shrink the tile window down a little to even it out.
     * So we work out how many per row would be optimal, and shrink the window accordingly.
     * @see format_tiles_width_template_data() in locallib.php for more information.
     * @return {Promise}
     */
    var resizeTilesDivWidth = function() {
        var winWidth = $(window).width();
        // Create a new Deferred.
        var dfd = new $.Deferred();
        var tiles = $(Selector.TILES);
        var TILE_WIDTHS = {
            standard: 260,
            min: 225,
            mobileMin: 160
        };
        try {
            var tilesParentWidth = tiles.parent().innerWidth();
            var firstTile = $(tiles.find(Selector.TILE)[0]);
            // Get the width of one tile.
            var oneTileWidth = firstTile.width()
                ? firstTile.width()
                : TILE_WIDTHS.standard; // Default standard as min tile width if we can't get the actual width.
            var tileMargin = 14; // Margin 7px either side.
            oneTileWidth = oneTileWidth + tileMargin;
            var resizeWidth = "inherit";
            // Skip if window is only 2 or smaller than 2 tiles wide already.
            // This ensures that we don't crush the tiles into the centre (i.e. we use at least x% of width).
            var maxPossibleTilesPerRow = Math.floor(tilesParentWidth / TILE_WIDTHS.min);
            var tileCount = ($(Selector.TILE).not(Selector.SPACER).length); // How many tiles in this course.
            if (tilesParentWidth < TILE_WIDTHS.mobileMin * 2) {
                // Only space for one tile - don't resize to save space.
                dfd.reject("Too narrow to resize");
            } else if (tileCount <= 3 && tilesParentWidth > tileCount * TILE_WIDTHS.mobileMin) {
                resizeWidth = TILE_WIDTHS.standard * 4;
            } else if (tilesParentWidth < TILE_WIDTHS.min * 3) {
                resizeWidth = (TILE_WIDTHS.standard - tileMargin) * 2;
            } else if (maxPossibleTilesPerRow < 4) {
                // Here we set the max width to the space we have available, so that tiles are centred.
                resizeWidth = TILE_WIDTHS.standard * maxPossibleTilesPerRow;
            } else {
                // Make a range of numbers in an array.  e.g. range(2,5) = [2, 3, 4, 5].
                var range = function (start, end) {
                    var res = [];
                    for (start; start <= end; start += 1) {
                        res.push(start);
                    }
                    return res;
                };

                // How many tiles per row could we fit on the screen, if we put in as many as possible?
                var rowMaxCount = Math.min(
                    Math.floor(tilesParentWidth / oneTileWidth),
                    tileCount
                );
                // If we'd have a single row of more than 5 tiles, split it into 2.
                if (tileCount <= rowMaxCount && tileCount > 5) {
                    resizeWidth = (Math.floor(tileCount / 2) + 1) * oneTileWidth;
                } else if (rowMaxCount > 3 && tileCount / rowMaxCount <= 3) {
                    // If we can fit 3 tiles per row max, we don't restrict to 2, as this makes content window unnecessarily small.
                    // Also if we have 3 or more rows (3 or more) we don't bother restricting as the last row is not so noticeable.

                    // How many tiles per row do we want as a minimum (in order to occupy a reasonable amount of width)?
                    var rowMinCount = Math.floor(tilesParentWidth / oneTileWidth);

                    // What are the possibilities for tiles per row?  Then we can look at which we want.
                    var possibleRowCounts = range(rowMinCount, rowMaxCount).reverse(); // Something like [6, 5, 4, 3].

                    // For each possibility, how many tiles would that leave on the last row?
                    var lastRowRemainderTiles = possibleRowCounts.map(function (num) {
                        return tileCount % num;
                    });
                    if (lastRowRemainderTiles.indexOf(0) !== -1) {
                        // We have the option of having a *full* last row so take that.
                        resizeWidth = oneTileWidth * possibleRowCounts[lastRowRemainderTiles.indexOf(0)];
                    } else if (tileCount < rowMinCount) {
                        resizeWidth = tileCount * oneTileWidth;
                    } else {
                        // Otherwise make the last row as full as possible (few tiles on last row looks worse).
                        resizeWidth = oneTileWidth * possibleRowCounts[lastRowRemainderTiles.indexOf(
                            Math.max.apply(null, lastRowRemainderTiles)
                        )];
                    }
                } else {
                    // In these cases, we don't artificially narrow the view, but we do put a max width on of the existing width.
                    // This is so that, when sections open under the tiles, they do not stick out with extra width beyond the tiles.
                    // It also allows the auto margin to centre the tiles.
                    resizeWidth = rowMaxCount * oneTileWidth;
                }
            }

            // If we already have the desired width, nothing to do here so skip it.
            var existingWidth = parseInt(tiles.css("max-width").replace("px", ""));
            if (Math.abs(resizeWidth - existingWidth) < 100) {
                dfd.resolve();
            } else {
                // We set session width at the server so that next time it is rendered with PHP, it has the correct width already.
                var resizeTime = 500;
                tiles.css("max-width", winWidth).animate({"max-width": resizeWidth}, resizeTime, "swing",
                    function() {
                        setTimeout(function() {
                            // Wait additional time before confirm resolved to allow resize to complete else re-org is too early.
                            dfd.resolve();
                        }, resizeTime + 100);
                        $(Selector.CONTENT_SECTIONS).animate({"max-width": resizeWidth}, resizeTime, "swing");
                    }
                );
            }

            // If we already have scheduled AJAX request to set width, cancel it and replace it with a more up to date one.
            // We need not set the width on the server very often - once every 3 seconds is plenty.
            if (timeoutBeforeResizeAjax) {
                clearTimeout(timeoutBeforeResizeAjax);
            }
            timeoutBeforeResizeAjax = setTimeout(function () {

                ajax.call([{
                    methodname: "format_tiles_set_session_width",
                    args: {courseid: courseId, width: Math.floor(resizeWidth)}
                }]);
            }, 3000);
        } catch (err) {
            // Unset widths as something went wrong.
            tiles.css("max-width", winWidth).animate({"max-width": "100%"}, 500, "swing");
            ajax.call([{
                methodname: "format_tiles_set_session_width",
                args: {courseid: courseId, width: 0}
            }]);
            dfd.reject("Failed to resize");
        }
        return dfd.promise();
    };

    var organiser = {
        /**
         * Content sections need to be displayed after the row in which the tile to which they relate appears
         * e.g. we have a row of tiles 1-3 and then after that we need to have the content divs which contain the
         * related content.  As this depends on device window size, we calcuate this on page load and after window changes
         * e.g. navbar button at side is pressed or browser window is resized
         * @returns {Array} of rows, with the tile they need to be displayed after, and the sections in each row
         */
        getContentSectionPositions: function () {
            var rows = [];
            var currentSectionId;
            var previousTile;
            var openSections = $(Selector.OPEN_SECTION);
            // Hide these for an instant while we do the calculations.
            openSections.css("display", "none");

            var maxTilesPerRow = 1;
            var thisRowCount = 0;
            var allTiles = $(Selector.TILES).children(Selector.TILE).not(Selector.TILE_COLLAPSED).not(".spacer");
            if (allTiles.length === 0) {
                // Course has no sections.
                return [];
            }
            allTiles.each(function (index, tile) {
                currentSectionId = $(tile).attr("data-section");
                var maxVerticalPositionDifference = 100;
                if (currentSectionId) {
                    if (index === 0) {
                        // We are on the first tile, so append a row and add tile ID to it.
                        thisRowCount = 1;
                    } else if (Math.abs($(tile).position().top - $(previousTile).position().top) <= maxVerticalPositionDifference) {
                        thisRowCount += 1;
                        // We are on the same row as the previous tile.
                        // maxVerticalPositionDifference is because tiles on same row may have different vertical positions.
                        // E.g. if one of the is in a hover state.  If they are within 100 px max they must be on same row.
                    } else {
                        // On a new row.
                        thisRowCount = 0;
                    }
                    if (thisRowCount > maxTilesPerRow) {
                        maxTilesPerRow = thisRowCount;
                    }
                    previousTile = tile;
                }
            });
            openSections.css("display", "block");

            // Now allocate rows of maxTilesPerRow each until we run out of tiles.
            allTiles.each(function (index, tile) {
                currentSectionId = $(tile).attr("data-section");
                if (rows.length === 0 || rows[rows.length - 1].sections.length >= maxTilesPerRow) {
                    if (rows.length >= 1) {
                        // Update the display after tag on previous row.
                        rows[rows.length - 1].displayAfterTile =
                            rows[rows.length - 1].sections[(rows[rows.length - 1].sections).length - 1];
                    }
                    // Start a new row.
                    rows.push({
                        displayAfterTile: "",
                        sections: [currentSectionId]
                    });
                } else {
                    rows[rows.length - 1].sections.push(currentSectionId);
                }
            });
            rows[rows.length - 1].displayAfterTile =
                rows[rows.length - 1].sections[(rows[rows.length - 1].sections).length - 1];
            return rows;
        },

        /**
         * Move content sections to appear under the correct tiles
         * so that when a tile is clicked, they expand under it
         * @param {Array} positionData
         * @param {[function]} callbacks
         */
        moveContentSectionsToPlaces: function (positionData, callbacks) {
            positionData.forEach(function (row) {
                row.sections.forEach(function (contentSection) {
                    if (row.displayAfterTile === positionData[positionData.length - 1].displayAfterTile) {
                        $(Selector.SECTION_ID + contentSection).detach().insertAfter($("ul.tiles .tile").last());
                    } else {
                        $(Selector.SECTION_ID + contentSection).detach().insertAfter($("#tile-" + row.displayAfterTile));
                    }
                });
            });
            callbacks.forEach(function(func) {
                if (typeof func === "function") {
                    func();
                }
            });
        },

        /**
         * Re-organise the sections so that they are in the correct order
         * e.g. content section 3 is on the row below tile 3, so that
         * when tile 3 is clicked, content section 3 opens directly under it
         * @param {boolean} delayBefore should we delay before doing the re-org?
         * @return {Promise}
         */
        runReOrg: function (delayBefore) {
            // Create a new Deferred.
            var dfd = new $.Deferred();
            if (reOrgLocked === true) {
                // Avoid repeated re-organisations - one at a time.
                dfd.reject("Re-org locked");
            }
            reOrgLocked = true;
            var action = function() {
                organiser.moveContentSectionsToPlaces(
                    organiser.getContentSectionPositions(),
                    [
                        function() {
                            $("body").removeClass("modal-open");
                            dfd.resolve("Finished organising tiles");
                            reOrgLocked = false;
                        }
                    ]
                );
            };

            if (delayBefore === true) {
                // We want to allow a delay before we start the re-org. This allows any page animation going on to end.
                setTimeout(function() {
                    action();
                    dfd.resolve("Re-org complete");
                }, 1000);
            } else {
                action();
                dfd.resolve("Re-org complete");
            }
            return dfd.promise();
        }
    };

    var setListeners = function () {
        // If theme uses docked blocks (e.g. more) then re-organise if they move.
        $(".block-hider-hide").click(function () {
            organiser.runReOrg(true);
        });

        $(".block-hider-show").click(function () {
            organiser.runReOrg(true);
        });

        // If nav drawer is opened or closed, this rezises the window so need to re-initialise content divs.
        $(".navbar button[data-action=\"toggle-drawer\"]").click(function () {
            setTimeout(function() {
                organiser.runReOrg(true);
                resizeTilesDivWidth();
            }, 600);

        });
    };

    /**
     * On initial page load, we need to unhide the tiles.  They will have been hidden from PHP if we are using JS.
     * This is to cover the initial setting up of div width (i.e. allow us time to get screen width and set up).
     */
    var unHideTiles = function() {
        $(Selector.TILES).animate({opacity: 1}, "fast");
        $(Selector.SECTION_ZERO).animate({opacity: 1}, "fast");
        $("#page-loading-icon").fadeOut(500).remove();
    };

    return {
        init: function(courseIdInit, sectionOpen, fitTilesToWidth, isEditing) {
            courseId = courseIdInit;
            $(document).ready(function() {
                setListeners();
                if ($(Selector.TILES).css("opacity") === "1") {
                    organiser.runReOrg().done(function() {
                        if (sectionOpen !== 0) {
                            // Tiles are already visible so open the tile user was on previously (if any).
                            $(Selector.TILEID + sectionOpen).click();
                        }
                    });
                }

                // When we first load the page we want to move the tile contents divs.
                // Put them in the correct rows according to which row of tiles they relate to.
                // Only then do we re-open the last section the user had open.
                var organiseAndRevealTiles = function () {
                    organiser.runReOrg().done(function() {
                        if (sectionOpen !== 0 && $(Selector.OPEN_SECTION).length === 0) {
                            // Now open the tile user was on previously (if any).
                            $(Selector.TILEID + sectionOpen).click();
                        }
                        unHideTiles();
                    });
                };
                if (fitTilesToWidth && !isEditing) {
                    // If we have a single tile on the last row it looks odd so resize window.
                    resizeTilesDivWidth().done(function() {
                        organiseAndRevealTiles();
                    }).fail(function() {
                        // If resize is rejected e.g. as screen is to narrow e.g. mobile.
                        organiseAndRevealTiles();
                    });
                } else {
                    organiseAndRevealTiles();
                }
            });
        },
        resizeTilesDivWidth: function() {
            return resizeTilesDivWidth();
        },
        runReOrg: function (delayBefore) {
            return organiser.runReOrg(delayBefore);
        }
    };
});