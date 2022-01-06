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
 * Main Javascript module for format_tiles for when user is *NOT* editing.
 * See course_edit for if they are editing.
 * Handles the UI changes when tiles are selected and anything else not
 * covered by the specific modules
 *
 * @module      format_tiles/course
 * @package     course/format
 * @subpackage  tiles
 * @copyright   2018 David Watson {@link http://evolutioncode.uk}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/templates", "core/ajax", "format_tiles/browser_storage",
        "core/notification", "core/str", "format_tiles/tile_fitter"],
    function ($, Templates, ajax, browserStorage, Notification, str, tileFitter) {
        "use strict";

        var body = $("body");
        var bodyHtml = $("body, html");
        var isMobile;
        var loadingIconHtml;
        var stringStore = [];
        var windowOverlay;
        var scrollFuncLock = false;
        var sectionIsOpen = false;
        var HEADER_BAR_HEIGHT = 60; // This varies by theme and version so will be reset once pages loads below.
        var reopenLastVisitedSection = false;
        var backDropZIndex = 0;
        var courseId;
        var resizeLocked = false;
        var enableCompletion;

         // Keep a record of which tile is currently open.
        var openTile = 0;

        var Selector = {
            PAGE: "#page",
            TILE: ".tile",
            TILEID: "#tile-",
            MOVEABLE_SECTION: ".moveablesection",
            FILTER_BUTTON: ".filterbutton",
            TILE_LOADING_ICON: ".tile-loading-icon",
            TILE_LOADING_ICON_ID: "#loading-icon-",
            TILE_COLLAPSED: ".tile-collapsed",
            TILE_CLICKABLE: ".tile-clickable",
            TILES: "ul.tiles",
            ACTIVITY: ".activity",
            SPACER: ".spacer",
            SECTION_MOVEABLE: ".moveablesection",
            SECTION_ID: "#section-",
            SECTION_TITLE: ".sectiontitle",
            SECTION_MAIN: ".section.main",
            SECTION_BUTTONS: ".sectionbuttons",
            CLOSE_SEC_BTN: ".closesectionbtn",
            HIDE_SEC0_BTN: "#buttonhidesec0",
            SECTION_ZERO: "#section-0",
            MOODLE_VIDEO: ".mediaplugin.mediaplugin_videojs",
            LAUNCH_STANDARD: '[data-action="launch-tiles-standard"]',
            TOOLTIP: "[data-toggle=tooltip]",
            HEADER_BAR: ["header.navbar", "nav.fixed-top.navbar", "#essentialnavbar.moodle-has-zindex", "#navwrap",
                "nav.navbar-fixed-top", "#adaptable-page-header-wrapper"],
            // We try several different selectors for header bar as it varies between theme.
            // (Boost based, clean based, essential etc).
            MATHJAX_EQUATION: ".filter_mathjaxloader_equation"
        };
        var ClassNames = {
            SELECTED: "selected",
            OPEN: "open",
            CLOSED: "closed",
            LAUNCH_CM_MODAL: "launch-tiles-cm-modal",
            STATE_VISIBLE: 'state-visible' // This is a Snap theme class and was added to make this format cooperate better with it.
        };

        var Event = {
            CLICK: "click",
            KEYDOWN: "keydown",
            SCROLL: "scroll"
        };

        var CSS = {
            DISPLAY: "display",
            Z_INDEX: "z-index",
            HEIGHT: "height",
            BG_COLOUR: "background-color"
        };
        var Keyboard = {
            ESCAPE: 27,
            TAB: 9,
            RETURN: 13
        };

        /**
         * If we have embedded video in section, stop it.
         * Runs when section is closed.
         * @param {number} section where the video is.
         */
        var stopVideoPlaying = function(section) {
            var contentSection = $(Selector.SECTION_ID + section);

            // First iframes (e.g. embedded YouTube).
            contentSection.find("iframe").each(function (index, iframe) {
                iframe = $(iframe);
                // Remove the src from the iframe but keep it in case the section is re-opened.
                if (iframe.attr('src')) {
                    iframe.attr('data-src', iframe.attr("src"));
                    iframe.attr('src', "");
                }
            });

            // Then Moodle media player.
            var mediaPlayers = contentSection.find(Selector.MOODLE_VIDEO);
            if (mediaPlayers.length > 0) {
                contentSection.html("");
            }
        };

        /**
         * When JS navigation is being used, when a user un-selects a tile, we have to move the tile's z-index back so that it is
         * no longer on top of the overlay, as well as removing its "selected" class, and hiding the overlay
         * @param {number} sectionToFocus if we want to focus a tile after closing, which one
         */
        var cancelTileSelections = function (sectionToFocus) {
            $(Selector.MOVEABLE_SECTION).each(function (index, sec) {
                sec = $(sec);
                if (sec.is(":visible")) {
                    stopVideoPlaying(sec.attr("data-section"));
                    sec.slideUp().removeClass(ClassNames.STATE_VISIBLE); // Excludes section 0.
                }
            });
            $(Selector.TILE).removeClass(ClassNames.SELECTED).css(CSS.Z_INDEX, "").css(CSS.BG_COLOUR, "");
            $(".section " + ClassNames.SELECTED).removeClass(ClassNames.SELECTED).css(CSS.Z_INDEX, "");
            windowOverlay.fadeOut(300);

            if (sectionToFocus !== undefined && sectionToFocus !== 0) {
                $(Selector.TILEID + sectionToFocus).focus();
            }
            $(Selector.TILE_LOADING_ICON).fadeOut(300, function () {
                $(Selector.TILE_LOADING_ICON).html("");
            });
            sectionIsOpen = false;
            openTile = 0;
        };

        /**
         * Set the HTML for a course section to the correct div in the page
         * @param {Object} contentArea the jquery object for the content area
         * @param {String} content the HTML
         * @returns {boolean} success
         */
        var setCourseContentHTML = function (contentArea, content) {
            if (content) {
                contentArea.html(content);
                $(Selector.TILE_LOADING_ICON).fadeOut(300, function () {
                    $(Selector.TILE_LOADING_ICON).html("");
                });

                if (contentArea.attr("id") !== Selector.SECTION_ZERO) {
                    // Trap the tab key navigation in the content bearing section.
                    // Until the user clicks the close button.
                    // When user reaches last item, send them back to first.
                    // And vice versa if going backwards.

                    var activities = contentArea.find(Selector.ACTIVITY).not(Selector.SPACER);
                    contentArea.on(Event.KEYDOWN, function (e) {
                        if (e.keyCode === Keyboard.ESCAPE) {
                            // Close open tile, and return focus to closed tile, for screen reader user.
                            browserStorage.setLastVisitedSection(0);
                            cancelTileSelections(0);
                            $(Selector.TILEID + contentArea.attr('data-section')).focus();
                        }
                    });
                    activities.on(Event.KEYDOWN, function (e) {
                        if (e.keyCode === Keyboard.RETURN) {
                            var toClick = $(e.currentTarget).find("a");
                            if (toClick.hasClass(ClassNames.LAUNCH_CM_MODAL)) {
                                toClick.click();
                            } else if (toClick.attr("href") !== undefined) {
                                window.location = toClick.attr("href");
                            }
                        }
                    });
                    if (!isMobile) {
                        activities.last().on(Event.KEYDOWN, function (e) {
                            if (e.keyCode === Keyboard.TAB && !e.shiftKey
                                    && $(e.relatedTarget).closest(Selector.SECTION_MAIN).attr("id") !== contentArea.attr("id")) {
                                // RelatedTarget is the item we tabbed to.
                                // If we reached here, the item we are on is not a member of the section we were in.
                                // (I.e. we are trying to tab out of the bottom of section) so move tab back to first item instead.
                                setTimeout(function () {
                                    // Allow very short delay so we dont skip forward on the basis of our last key press.
                                    contentArea.find(Selector.SECTION_TITLE).focus();
                                    bodyHtml.animate({scrollTop: contentArea.offset().top - HEADER_BAR_HEIGHT}, "slow");
                                    contentArea.find(Selector.SECTION_BUTTONS).css("top", "");
                                }, 200);
                            }
                        });
                        contentArea.find(Selector.SECTION_TITLE).on(Event.KEYDOWN, function (e) {
                            if (e.keyCode === Keyboard.TAB && e.shiftKey
                                    && $(e.relatedTarget).closest(Selector.SECTION_MAIN).attr("id") !== contentArea.attr("id")) {
                                // See explanation previous block.
                                // Here we are trying to tab backwards out of the top of our section.
                                // So take us to last item instead.
                                setTimeout(function () {
                                    activities.last().focus();
                                }, 200);
                            }
                        });
                    }
                }

                if (!isMobile) {
                    // Activate tooltips for completion toggle and any "restricted" items in this content.
                    setTimeout(function () {
                        // Manual forms, auto icons and "Restricted until ..." etc.
                        try {
                            const tooltipItems = contentArea.find(".badge-info");
                            if (tooltipItems.length > 0 && typeof tooltipItems.tooltip == 'function') {
                                tooltipItems.tooltip();
                            }
                        } catch (err) {
                            require(["core/log"], function(log) {
                                log.debug(err);
                            });
                        }
                    }, 500);
                }
                // As we have just loaded new content, ensure that we initialise videoJS media player if required.
                if (contentArea.find(Selector.MOODLE_VIDEO).length !== 0) {
                    require(["media_videojs/loader"], function(videoJS) {
                        videoJS.setUp();
                    });
                }

                applyMathJax(contentArea);
                return true;
            }
            return false;
        };

        /**
         * Find Mathjax equations in a content area and queue them for processing.
         * @param {Object} contentArea the jquery object for the content area
         */
        const applyMathJax = function(contentArea) {
            if (typeof window.MathJax !== "undefined") {
                try {
                    const mathJaxElems = contentArea.find(Selector.MATHJAX_EQUATION);
                    if (mathJaxElems.length) {
                        mathJaxElems.each((i, node) => {
                            window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, node]);
                        });
                    }
                } catch (err) {
                    require(["core/log"], function(log) {
                        log.debug(err);
                    });
                }
            }
        };

        /**
         * Expand a content containing section (e.g. on tile click)
         * @param {object} contentArea
         * @param {number} tileId to expand
         */
        var expandSection = function (contentArea, tileId) {
            var expandAndScroll = function () {
                // Scroll to the top of content bearing section
                // we have to wait until possible reOrg and slide down totally before calling this, else co-ords are wrong.
                var scrollTo = $("#tileText-" + tileId).offset().top - HEADER_BAR_HEIGHT;
                if (scrollTo === $(window).scrollTop) {
                    // Scroll by at least one pixel otherwise z-index on selected tile is not changed.
                    // Until mouse moves.
                    scrollTo += 1;
                }
                contentArea.find(Selector.SECTION_TITLE).focus();
                // If user tries to scroll during animation, stop animation.
                var events = "scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove";
                body.on(events, function () {
                    body.stop();
                });

                bodyHtml.animate({scrollTop: scrollTo}, "slow", function () {
                    // Animation complete, remove stop handler.
                    bodyHtml.off(events, function () {
                        bodyHtml.stop();
                    });
                });
                sectionIsOpen = true;
                openTile = tileId;

                // For users with screen readers, move focus to the first item within the tile.
                contentArea.find(Selector.ACTIVITY).first().focus();

                // If we have any iframes in the section which were previous emptied out, re-populate.
                // This will happen if we have previously closed a section with videos in, and they were muted.
                const iframes = contentArea.find("iframe");
                if (iframes.length > 0) {
                    iframes.each(function (index, iframe) {
                        iframe = $(iframe);
                        // If iframe has no src, add it from data-src.
                        if (iframe.attr('src') === '' && iframe.attr('data-src') !== undefined) {
                            iframe.attr('src', iframe.attr("data-src"));
                        }
                    });

                    if (enableCompletion) {
                        // Some iframes may load content set to mark as complete on view.
                        // So maybe need to update tile completion info. E.g. applies with H5P filter.
                        require(["format_tiles/completion"], function (completion) {
                            setTimeout(() => {
                                completion.updateTileInformation();
                            }, 1000);
                        });
                    }
                }
            };

            /**
             * Make sure that the section close and edit buttons always appear at the top of the section on scroll
             */
            var holdSectionButtonPosition = function () {
                var buttons = contentArea.find(Selector.SECTION_BUTTONS);
                $(window).on(Event.SCROLL, function () {
                    if (!scrollFuncLock && sectionIsOpen) {
                        scrollFuncLock = true;
                        buttons.fadeOut(300);
                        setTimeout(function () {
                            var windowTop = $(window).scrollTop();
                            var desiredNewPositionInSection = (windowTop - contentArea.offset().top + 50);
                            if (desiredNewPositionInSection > 0
                                    && desiredNewPositionInSection < contentArea.outerHeight() - 100) {
                                desiredNewPositionInSection = (windowTop - contentArea.offset().top + 50);
                                buttons.css("top", desiredNewPositionInSection);
                                if (windowOverlay.css(CSS.DISPLAY) === "none") {
                                    windowOverlay.fadeIn(300);
                                }
                            } else if (desiredNewPositionInSection < 0) {
                                buttons.css("top", 0);
                            }
                            if (windowTop > contentArea.offset().top + contentArea.outerHeight() - 50) {
                                // We have scrolled down and content bottom has gone out of the top of window.
                                if (windowOverlay.css(CSS.DISPLAY) === "block") {
                                    windowOverlay.fadeOut(300);
                                }
                                buttons.css("top", 0);
                            } else if (contentArea.offset().top > windowTop + $(window).outerHeight()) {
                                // We have scrolled up and  content bottom has gone out of the bottom of window.
                                if (windowOverlay.css(CSS.DISPLAY) === "block") {
                                    windowOverlay.fadeOut(300);
                                }
                                buttons.css("top", 0);
                            } else if (windowOverlay.css(CSS.DISPLAY) === "none") {
                                windowOverlay.fadeIn(300);
                            }
                            buttons.fadeIn(300, function () {
                                // Release lock on this function.
                                scrollFuncLock = false;
                            });
                        }, 500);
                    }
                });
                if (!scrollFuncLock && !sectionIsOpen && windowOverlay.is(":visible")) {
                    windowOverlay.fadeOut(300);
                }
            };

            contentArea.addClass(ClassNames.STATE_VISIBLE);
            contentArea.slideDown(350, function () {
                // Wait until we have finished sliding down before we work out where the top is for scroll.
                expandAndScroll();
                holdSectionButtonPosition();
            });
            openTile = tileId;
        };

        /**
         * We find out what section is open, collapse them all, then run the re-org.
         * Finally we re-open the section.
         * This is to ensure that the content bearing section is on the row under the tile clicked.
         * It is run at page load and again if window is re-sized etc.
         * @param {boolean} delayBefore do we want a delay before we re-org.  This allows e.g. browser resizing to complete.
         * @param {boolean} fitTilesToScreenWidth whether we need to resize the tiles window while tiles are closed.
         * @returns {Promise}
         */
        var reOrgSections = function (delayBefore, fitTilesToScreenWidth) {
            var dfd = new $.Deferred();
            var openedSection = $(".moveablesection:visible");
            var openedSectionNum = 0;
            if (openedSection.length > 0) {
                openedSectionNum = openedSection.attr("data-section");
                cancelTileSelections();
            }
            var reOrgFunc = function(delayBefore) {
                tileFitter.runReOrg(delayBefore)
                    .done(function(result) {
                        if (openedSectionNum !== 0) {
                            expandSection(openedSection, openedSectionNum);
                        }
                        dfd.resolve(result);
                    })
                    .fail(function(result) {
                        if (openedSectionNum !== 0) {
                            expandSection(openedSection, openedSectionNum);
                        }
                        dfd.reject(result);
                    });
            };

            if (fitTilesToScreenWidth) {
                setTimeout(function() {
                    tileFitter.resizeTilesDivWidth(courseId).done(function() {
                        reOrgFunc(false);
                    }, delayBefore);
                });

            } else {
                reOrgFunc(delayBefore);
            }
            return dfd.promise();
        };

        var failedLoadSectionNotify = function(sectionNum, failResult, contentArea) {
            if (failResult) {
                // Notify the user and invite them to refresh.  We did get a "failResult" from server,
                // So it looks like we do have a connection and can launch this.
                Notification.confirm(
                    stringStore.sectionerrortitle,
                    stringStore.sectionerrorstring,
                    stringStore.refresh,
                    stringStore.cancel,
                    function () {
                        window.location.reload();
                    },
                    null
                );
                contentArea.html(""); // Clear loading icon.
            } else {
                // It looks like we may not have a connection so we can't launch notifications.
                // We can warn the user like this instead.
                setCourseContentHTML(contentArea, "<p>" + stringStore.noconnectionerror + "</p>");
                setTimeout(function () {
                    expandSection(contentArea, sectionNum);
                }, 500);
            }
            require(["core/log"], function(log) {
                log.debug(failResult);
            });
            throw new Error("Not successful retrieving tile content by AJAX for section " + sectionNum);
        };

        /**
         * For a given section, get the content from the server, add it to the store and maybe UI and maybe show it
         * @param {number} courseId the id for the affected course
         * @param {number} sectionNum the section number we are wanting to populate
         * @return {Promise} promise to resolve when the ajax call returns.
         */
        var getSectionContentFromServer = function (courseId, sectionNum) {
            return ajax.call([{
                methodname: "format_tiles_get_single_section_page_html",
                args: {
                    courseid: courseId,
                    sectionid: sectionNum,
                    setjsusedsession: true
                }
            }])[0];
        };

        /**
         * Temporary function to adjust shade of RGB colour
         * (used for shading tiles to get around transparent background on overlay issue)
         * @param {number} R
         * @param {number} G
         * @param {number} B
         * @param {number} percent
         * @returns {string}
         */
        var shadeRGBColor = function (R, G, B, percent) {
            var t = percent < 0 ? 0 : 255;
            var p = percent < 0 ? percent * -1 : percent;
            var r = Math.round((t - R) * p) + R;
            var g = Math.round((t - G) * p) + G;
            var b = Math.round((t - B) * p) + B;
            return "rgb(" + r + "," + g + "," + b + ")";
        };

        /**
         * Add an opaque modal backdrop like div to obscure all other tiles and bring specified tile and content to front
         * @param {number} secNumOnTop the section number which should be displayed on top of the overlay
         */
        var setOverlay = function (secNumOnTop) {
            windowOverlay.fadeIn(300);
            backDropZIndex = parseInt(windowOverlay.css(CSS.Z_INDEX));
            var tile = $(Selector.TILEID + secNumOnTop);
            tile.css(CSS.Z_INDEX, (backDropZIndex + 1));
            $(Selector.SECTION_ID + secNumOnTop).css(CSS.Z_INDEX, (backDropZIndex + 1));
            if (tile.css(CSS.BG_COLOUR) && tile.css(CSS.BG_COLOUR).substr(0, 4) === "rgba") {
                // Tile may have transparent background from theme - needs to be solid otherwise modal shows through.
                var existingColour = tile.css(CSS.BG_COLOUR).replace("rgba(", "").replace(")", "").replace(" ", "").split(",");
                tile.css(CSS.BG_COLOUR, shadeRGBColor(
                    parseInt(existingColour[0]),
                    parseInt(existingColour[1]),
                    parseInt(existingColour[2]),
                    0.95
                ));
            }
        };

        /**
         * Used where the user clicks the window overlay but we want the active click to be behind the
         * overlay e.g. the tile or custom menu item behind it.  So we get the co-ordinates of the click
         * on the overlay and then repeat the click at that spot ignoring the overlay
         * @param {object} e the click event object
         */
        var clickItemBehind = function (e) {
            var clickedItem = $(e.currentTarget);
            if (clickedItem.attr("id") === "window-overlay") {
                // We need to know what is behind the modal, so hide it for an instant to find out.
                clickedItem.hide();
                var BottomElement = $(document.elementFromPoint(e.clientX, e.clientY));
                clickedItem.show();
                if (clickedItem.attr("id") === "window-overlay") {
                    if (BottomElement.hasClass("filterbutton") || BottomElement.hasClass("list-group-item")) {
                        // Must ba a filter button clicked or a nav drawer item.
                        BottomElement.click();
                    } else {
                        // Must be a tile clicked.
                        var clickedTile = BottomElement.closest(Selector.TILE);
                        if (clickedTile) {
                            clickedTile.click();
                        }
                    }
                } else {
                    // Must be a click on the header bar.
                    cancelTileSelections(0);
                    BottomElement.click();
                }
            }
        };

        /**
         * If the user had section zero collapsed in this course previously, collapse it now
         */
        var setSectionZeroFromUserPref = function () {
            var buttonHideSecZero = $(Selector.HIDE_SEC0_BTN);
            var sectionZero = $(Selector.SECTION_ZERO);
            if (browserStorage.storageEnabledLocal()) {
                // Collapse section zero if user had it collapsed before - relies on local storage so only if enabled.
                if (browserStorage.getSecZeroCollapseStatus() === true) {
                    sectionZero.slideUp(0);
                    buttonHideSecZero.addClass(ClassNames.CLOSED).removeClass(ClassNames.OPEN); // Button image.
                } else {
                    sectionZero.slideDown(300);
                    buttonHideSecZero.addClass(ClassNames.OPEN).removeClass(ClassNames.CLOSED); // Button image.
                }
            } else {
                // Storage not available so we dont know if sec zero was previously collapsed - expand it.
                buttonHideSecZero.addClass(ClassNames.OPEN).removeClass(ClassNames.CLOSED);
                sectionZero.slideDown(300);
            }
        };

        /**
         * To be called when a tile is clicked. Get content from server or storage and display or store it.
         * @param {number} courseId courseId the id of this course.
         * @param {object} thisTile jquery the tile object clicked.
         * @param {number} dataSection the id number of the tile.
         */
        var populateAndExpandSection = function(courseId, thisTile, dataSection) {
            setOverlay(dataSection);
            $(Selector.TILE).removeClass(ClassNames.SELECTED);
            thisTile.addClass(ClassNames.SELECTED);
            openTile = dataSection;
            // Then close all open secs.
            // Timed to finish in 200 so that it completes well before the opening next.
            $(Selector.MOVEABLE_SECTION).each(function (index, sec) {
                sec = $(sec);
                if (sec.is(":visible")) {
                    stopVideoPlaying(sec.attr("data-section"));
                    sec.slideUp(200).removeClass(ClassNames.STATE_VISIBLE); // Excludes section 0.
                }
            });
            // Log the fact we viewed the section.
            ajax.call([{
                methodname: "format_tiles_log_tile_click", args: {
                    courseid: courseId,
                    sectionid: dataSection
                }
            }])[0].fail(Notification.exception);
            // Get the content - use locally stored content first if available.
            var relatedContentArea = $(Selector.SECTION_ID + dataSection);
            if (relatedContentArea.find(Selector.ACTIVITY).length > 0) {
                // There is already some content on the screen so display immediately.
                expandSection(relatedContentArea, dataSection);

                // Still contact the server in case content has changed (e.g. restrictions now satisfied).
                getSectionContentFromServer(courseId, dataSection).done(function (response) {
                    setCourseContentHTML(relatedContentArea, $(response.html).html());
                });
            } else {
                relatedContentArea.html(loadingIconHtml);
                // Get from server.
                getSectionContentFromServer(courseId, dataSection).done(function (response) {
                    setCourseContentHTML(relatedContentArea, $(response.html).html());
                    expandSection(relatedContentArea, dataSection);
                }).fail(function (failResult) {
                    failedLoadSectionNotify(dataSection, failResult, relatedContentArea);
                    cancelTileSelections(dataSection);
                });
            }
            browserStorage.setLastVisitedSection(dataSection);
        };

        return {
            init: function (
                courseIdInit,
                useJavascriptNav, // Set by site admin see settings.php.
                isMobileInit,
                sectionNum,
                useFilterButtons,
                assumeDataStoreConsent, // Set by site admin see settings.php.
                reopenLastSectionInit, // Set by site admin see settings.php.
                userId,
                fitTilesToWidth,
                enableCompletionInit
            ) {
                courseId = courseIdInit;
                isMobile = isMobileInit;
                // Some args are strings or ints but we prefer bool.  Change to bool now as they are passed on elsewhere.
                reopenLastVisitedSection = reopenLastSectionInit === "1";
                useFilterButtons = useFilterButtons === 1;
                assumeDataStoreConsent = assumeDataStoreConsent === "1";
                enableCompletion = enableCompletionInit === "1";
                 // We want to initialise the browser storage JS module for storing user settings.
                browserStorage.init(
                    courseId,
                    false,
                    sectionNum,
                    assumeDataStoreConsent,
                    userId
                );
                $(document).ready(function () {
                    var pageContent = $("#page-content");
                    if (pageContent.length === 0) {
                        // Some themes e.g. RemUI do not have a #page-content div, so use #region-main.
                        pageContent = $("#region-main");
                    }

                    // If we are being told to launch a section number from the URL, use that.
                    if (sectionNum !== 0) {
                        openTile = sectionNum;
                    } else {
                        // Don't use the URL param - check local storage instead.
                        if (reopenLastVisitedSection && browserStorage.storageEnabledLocal) {
                            openTile = browserStorage.getLastVisitedSection();
                            // If user is not on mobile, retrieve last visited section id from browser storage (if present).
                            // And click it.
                        }
                    }
                    if (openTile !== 0) {
                        tileFitter.init(courseId, openTile, fitTilesToWidth, false);
                    } else {
                        // Set focus to the first tile (not section zero).
                        $(Selector.TILEID + "1").focus();
                        tileFitter.init(courseId, null, fitTilesToWidth, false);
                    }
                    var windowWidth = $(window).outerWidth();

                    if (useJavascriptNav) {
                        // User is not editing but is usingJS nav to view.

                        // When a tile is clicked we add an overlay to grey out the rest of the tiles on the page, so prepare it.
                        windowOverlay = $("<div></div>").addClass("modal-backdrop fade in").hide()
                            .attr("id", "window-overlay").appendTo(body);

                        // If user clicks the window overlay behind the visible tile content, deselect tile.
                        // (They want to remove the overlay).
                        windowOverlay.click(function (e) {
                            cancelTileSelections(0);
                            clickItemBehind(e);
                        });

                         // On a tile click, decide what to do an do it.
                         // (Collapse if already expanded, or expand it and fill with content).
                        pageContent.on(Event.CLICK, Selector.TILE_CLICKABLE, function (e) {
                            // Prevent the link being followed to reload the PHP page as we are using JS instead.
                            if (!useJavascriptNav) {
                                return;
                            }
                            e.preventDefault();
                            $(window).off(Event.SCROLL); // Stop listening for scroll events on ay previously opened tiles.
                            // if other tiles have loading icons, fade them out (on the tile not the content sec).
                            $(Selector.TILE_LOADING_ICON).fadeOut(300, function () {
                                $(Selector.TILE_LOADING_ICON).html();
                            });
                            var thisTile = $(e.currentTarget).closest(Selector.TILE);
                            var dataSection = parseInt(thisTile.attr("data-section"));
                            if (thisTile.hasClass(ClassNames.SELECTED)) {
                                // This tile is already expanded so collapse it.
                                cancelTileSelections(dataSection);
                                browserStorage.setLastVisitedSection(0);
                            } else {
                                populateAndExpandSection(courseId, thisTile, dataSection);
                            }
                            // Silently set the *next* section's content to if it exists and if user is not on mobile.
                            // short delay as more important to get current section content first (above).
                            var nextSecIfExists = $(Selector.SECTION_ID + (dataSection + 1));
                            const usingH5pFilter = $('.filters-config[data-filter="h5p"]').length === 1;
                            if (!isMobile && !usingH5pFilter && nextSecIfExists.length && dataSection > 0) {
                                getSectionContentFromServer(courseId, dataSection + 1).done(function(response) {
                                    setCourseContentHTML(
                                        nextSecIfExists,
                                        $(response.html).html()
                                    );
                                });
                            }
                        });

                        // When window is re-sized, content sections under the tiles may be in wrong place.
                        // So remove them and re-initialise them.
                        // Collapse the selected section before doing this.
                        // Otherwise the re-organisation won't work as the tiles' flow will be out when they are analysed.
                        $(window).on("resize", function () {
                            // On iOS resize events are triggered often on scroll because the address bar hides itself.
                            // Avoid this using windowWidth here.
                            if (resizeLocked || windowWidth === $(window).outerWidth()) {
                                return;
                            }
                            resizeLocked = true;

                            // We wait for a short time before doing anything, as user may still be dragging window size change.
                            // We don't want to react to say 20 resize events happening over a single drag.
                            setTimeout(function() {
                                // First assume that we are going to resize, but we have checks to make below.
                                var resizeRequired = true;

                                // If we have a Moodle media player div in the section in fullscreen, ignore this resize event.
                                // It was probably caused when user pressed the full screen button.
                                var openContentSection = $(".moveablesection:visible");
                                if (openContentSection.length !== 0) {
                                    var mediaPlayers = openContentSection.find(".mediaplugin iframe");
                                    if (mediaPlayers.length !== 0) {
                                        mediaPlayers.each(function (index, player) {
                                            player = $(player);
                                            if (player.outerWidth() > openContentSection.outerWidth()) {
                                                // Video is present and playing full screen so don't react to resize event.
                                                resizeRequired = false;
                                            }
                                        });
                                    }
                                }
                                if (resizeRequired) {
                                    // Set global for comparison next time.
                                    windowWidth = $(window).outerWidth();
                                    reOrgSections(true, fitTilesToWidth);
                                }
                                resizeLocked = false;
                            }, 600);
                        });

                        // We want the main menu at the top to be on top of the tiles.
                        // Even the ones which we have brought to the front on top of the window overlay.
                        // But we also want it to still be greyed out.  So add an opaque overlay.
                        // We can show this when the main overlay is active.
                        // When a user clicks this overlay, they want to close the overlay and click menu item behind.
                        // So provide for that here.
                        // Z-INDICES: active tile will be at overlayZindex + 1.
                        // Header bar will be at overlayZindex + 2 so that it is higher than tiles.
                        // Header bar overlay will be at overlayZindex + 3 so that it is highest of all.

                        var overlayZindex = parseInt(windowOverlay.css(CSS.Z_INDEX));
                        var headerBar = $(Selector.HEADER_BAR.find(function(selector) {
                            return $(selector).length > 0;
                        }));
                        if (headerBar !== undefined && headerBar.length !== 0) {
                            headerBar.css(CSS.Z_INDEX, overlayZindex + 3);
                        }

                        // When user clicks to close a section using cross at top right in section.
                        pageContent.on(Event.CLICK, Selector.CLOSE_SEC_BTN, function (e) {
                            cancelTileSelections($(e.currentTarget).attr("data-section"));
                        });

                        // If user clicks a sub tile body below the link, treat it as a click on the link itself.
                        pageContent.on(Event.CLICK, Selector.LAUNCH_STANDARD, function(e) {
                            var clickedLk = $(e.currentTarget);
                            if (clickedLk.attr("href") !== undefined) {
                                window.location = clickedLk.attr("href");
                            } else if (clickedLk.find('a').attr("href") !== undefined) {
                                window.location = clickedLk.find('a').attr("href");
                            }
                        });

                        setSectionZeroFromUserPref();
                        // Most filter button related JS is in filter_buttons.js module which is required below.

                    }

                    // If this event is triggered, user has updated a completion check box.
                    // We need to retrieve section content from server in case availability of items has changed.
                    $(document).on('format-tiles-completion-changed', function(e, data) {
                        const allSectionNums = $(Selector.TILE).not(Selector.SPACER).map((i, t) => {
                            return parseInt($(t).attr('data-section'));
                        }).toArray();
                        // Need to include sec zero as may have completion tracked items.
                        allSectionNums.push(0);
                        const requests = ajax.call([
                            {
                                methodname: "format_tiles_get_single_section_page_html",
                                args: {
                                    courseid: courseId,
                                    sectionid: data.section,
                                    setjsusedsession: true
                                }
                            },
                            {
                                methodname: "format_tiles_get_section_information",
                                args: {
                                    courseid: courseId,
                                    sectionnums: allSectionNums
                                }
                            }
                        ]);
                        requests[0]
                            .done((response) => {
                                setCourseContentHTML($(Selector.SECTION_ID + data.section), $(response.html).html());
                            })
                            .catch(err => {
                                require(["core/log"], function(log) {
                                    log.debug(err);
                                });
                            });
                        requests[1]
                            .done((response) => {
                                require(["format_tiles/completion"], function (completion) {
                                    completion.updateSectionsInfo(
                                        response.sections, response.overall.complete, response.overall.outof
                                    );
                                });

                            })
                            .catch(err => {
                                require(["core/log"], function(log) {
                                    log.debug(err);
                                });
                            });
                    });

                    // When the user presses the button to collapse or expand Section zero (section at the top of the course).
                    pageContent.on(Event.CLICK, Selector.HIDE_SEC0_BTN, function (e) {
                        var sectionZero = $(Selector.SECTION_ZERO);
                        if (sectionZero.css(CSS.DISPLAY) === "none") {
                            // Sec zero is collapsed so expand it on user click.
                            sectionZero.slideDown(250);
                            $(e.currentTarget).addClass(ClassNames.OPEN).removeClass(ClassNames.CLOSED);
                            browserStorage.setSecZeroCollapseStatus("collapsed");
                        } else {
                            // Sec zero is expanded so collapse it on user click.
                            sectionZero.slideUp(250);
                            $(e.currentTarget).addClass(ClassNames.CLOSED).removeClass(ClassNames.OPEN);
                            browserStorage.setSecZeroCollapseStatus("expanded");
                        }
                    });

                    if (useFilterButtons) {
                        require(["format_tiles/filter_buttons"], function (filterButtons) {
                            filterButtons.init(courseId, browserStorage.storageEnabledLocal);
                        });
                        if (useJavascriptNav) {
                            pageContent.on(Event.CLICK, Selector.FILTER_BUTTON, function () {
                                cancelTileSelections(0);
                                reOrgSections(true, false);
                            });
                        }

                    }
                    // If theme is displaying the .tiles_coursenav class items, show items with this class.
                    // They will be hidden otherwise.
                    // They are hidden when initially rendered from PHP as we only want them shown if browser supports JS.
                    // See lib.php extend_course_navigation.
                    $(".tiles_coursenav").removeClass("hidden");

                    // Render the loading icon and store its HTML globally so that we can use it where needed later.
                    Templates.render("format_tiles/loading", {}).done(function (html) {
                        loadingIconHtml = html;
                    });

                     // Get these strings now, in case we need them.
                    // E.g. after we lose connection and cannot display content on a user tile click.
                    var stringKeys = [
                        {key: "sectionerrortitle", component: "format_tiles"},
                        {key: "sectionerrorstring", component: "format_tiles"},
                        {key: "refresh"},
                        {key: "cancel"},
                        {key: "noconnectionerror", component: "format_tiles"},
                        {key: "show"},
                        {key: "hide"},
                        {key: "other", component: "format_tiles"},
                        {key: "blockedpopuptitle", component: "format_tiles"}
                    ];
                    str.get_strings(stringKeys).done(function (s) {
                        s.forEach(function(str, index) {
                            if (str) {
                                stringStore[stringKeys[index].key] = str;
                            } else {
                                stringStore[stringKeys[index].key] = 'Error.';
                                require(["core/log"], function(log) {
                                    log.debug(`Format tiles get_strings error ${index}`);
                                    log.debug(s);
                                });
                            }
                        });
                    })
                    .fail(function(err) {
                        require(["core/log"], function(log) {
                            log.debug(err);
                        });
                    });

                    // If a mobile user clicks an embedded video activity, we don't show them a modal.
                    // It won't work well. Instead we direct them to the original site e.g. YouTube.
                    if (isMobile) {
                        pageContent.on(Event.CLICK, Selector.ACTIVITY + ".video a", function(e) {
                            var target = $(e.currentTarget);
                            var url = target.closest(Selector.ACTIVITY).attr("data-url-secondary");
                            if (url !== undefined) {
                                e.preventDefault();
                                e.stopPropagation();
                                var cm = target.closest(Selector.ACTIVITY);
                                ajax.call([{
                                    methodname: "format_tiles_log_mod_view", args: {
                                        courseid: courseId,
                                        cmid: cm.attr("data-cmid")
                                    }
                                }])[0].done(function () {
                                    // Because we intercepted the normal event for the click, process auto completion.
                                    require(["format_tiles/completion"], function (completion) {
                                        completion.markAsAutoCompleteInUI(courseId, cm);
                                    });
                                });
                                window.location = url;
                            }
                        });
                    } else {
                        // If user is NOT on mobile device.

                        // If return is pressed while an item is in focus, click the item.
                        // This is to make the tiles keyboard navigable for users using screen readers.
                        // User tabbing between tiles is handled by tabindex in the HTML.
                        // Once the tile is clicked, the expand tile function will move focus to the first content item.
                        // On escape key, we clear all selections and collapse tiles (handled above not here).
                        $(Selector.TILE).on(Event.KEYDOWN, function (e) {
                            if (e.keyCode === Keyboard.RETURN) { // Return key pressed.
                                $(e.currentTarget).click();
                            }
                        });

                        // Move focus to the first tile in the course (not sec zero contents if present).
                        $("ul.tiles .tile").first().focus();
                    }

                    // If Adaptable theme is being used, and Glossary auto link filter is on, we need this.
                    // Otherwise when the auto link is clicked, the resulting dialogue is under the main overlay.
                    // Don't need this when Boost or Clean themes are used as they handle it themselves.
                    $(document).on("filter-content-updated", function (event, msg) {
                        if (msg.length > 0) {
                            var elem = $(msg[0]);
                            if (elem.hasClass("moodle-dialogue") && elem.css("z-index") < backDropZIndex) {
                                    elem.css("z-index", backDropZIndex + 1);
                            }
                        }
                    });
                    const mathJaxConfigDiv = $('.filters-config[data-filter="mathjaxloader"]');
                    if (mathJaxConfigDiv.length) {
                        if (typeof window.MathJax === 'undefined') {
                            // If mathjax is in use and undefined, we try to initialise it.
                            const script = $('<script/>');
                            script.attr('src', mathJaxConfigDiv.attr('data-url'))
                                .attr('type', 'text/javascript')
                                .html(mathJaxConfigDiv.attr('data-config'));
                            $('head').append(script);
                            setTimeout(() => {
                                applyMathJax($('#multi_section_tiles'));
                            }, 2000);
                        }
                    }
                });
            }
        };
    }
);