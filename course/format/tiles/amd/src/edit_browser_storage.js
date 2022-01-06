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
 * Javascript Module to handle browser storage for editing interface.
 *
 * @module edit_browser_storage
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery", "format_tiles/browser_storage_set_up"], function ($, storageSetUp) {
    "use strict";
    var Selector = {
        PAGE: '#page',
        EDITING_COLLAPSE_SECID: "#collapse",
        EDITING_COLLAPSE_SEC: ".collapse-section",
        EDITING_EXPAND_SEC: ".expand-section",
        EDITING_EXPAND_SECID: "#expand",
        SECTION_CONTENT: "li.section.main .content",
        SECTION_MAIN: "li.section.main",
        SECTION_ID: "#section-"
    };

    var courseId;
    var userId;

    var StorageKeys = {
        COURSE_VISIT: "mdl-tiles-editing-last-visit-course-",
        EDITING_COURSE: "mdl-tiles-editing-course-",
        SECTION: "-section-",
        COURSE: "mdl-tiles-course-",
        LAST_SECTION: "-lastSecId",
        USER: "-user-",
        PREFIX: "mdl-tiles-",
        USER_CHOICE_PREFIX: "mdl-tiles-userPrefStorage-",
        CONTENT_SUFFIX: "-content",
        LAST_UPDATED_SUFFIX: "-lastUpdated"
    };

    var StorageValues = {
        OPEN: "1",
        CLOSED: "0"
    };

    var lastInteractedSection;

    var encodeSectionStatus = function(sectionNum) {
        return StorageKeys.EDITING_COURSE + courseId + StorageKeys.SECTION + sectionNum + StorageKeys.USER + userId;
    };

    /**
     * Find out which sections of this course the user has showing as open in their browser storage.
     * @param {number} maxSection
     * @returns {Array}
     */
    var getOpenSections = function(maxSection) {
        if (!maxSection || maxSection > 50) {
            maxSection = 50; // Do not check more than 50 sections.
        }
        var sections = [];
        var value;
        for (var secNum = 1; secNum <= maxSection; secNum++) {
            value = sessionStorage.getItem(encodeSectionStatus(secNum));
            if (value === StorageValues.OPEN) {
                sections.push(secNum);
            }
        }
        return sections;
    };

    /**
     * Storage key for the last section user was using.
     * This is the same key as when not editing.
     * @returns {string}
     */
    var lastSectionStorageKey = function () {
        return StorageKeys.COURSE + courseId +
        StorageKeys.USER + userId + StorageKeys.LAST_SECTION;
    };

    /**
     * Set the last section the user was using.  This is the same key as when not editing.
     * @param {number} sectionNum
     */
    var setLastSection = function(sectionNum) {
        if (!sectionNum) {
            localStorage.clear(lastSectionStorageKey());
        } else if (sectionNum !== lastInteractedSection && storageSetUp.storageAllowed) {
            lastInteractedSection = sectionNum;
            localStorage.setItem(lastSectionStorageKey(), sectionNum.toString());
        }
    };

    var getLastSection = function() {
        if (!lastInteractedSection) {
            lastInteractedSection = localStorage.getItem(lastSectionStorageKey());
        }
         return lastInteractedSection;
    };

    var setSectionStatus = function(secNum, open) {
        if (storageSetUp.storageAllowed && open) {
            sessionStorage.setItem(encodeSectionStatus(secNum), StorageValues.OPEN);
        } else {
            sessionStorage.removeItem(encodeSectionStatus(secNum));
        }
    };

    /**
     * If we are editing, we want to clear all stored content on initial page load.
     * This ensure that, when we go back to not editing, our new content is shown not the old.
     */
    var clearStoredContent = function() {
        Object.keys(sessionStorage).filter(function (key) {
            return key.indexOf(StorageKeys.PREFIX) === 0 &&
                (key.indexOf(StorageKeys.CONTENT_SUFFIX) === key.length - StorageKeys.CONTENT_SUFFIX.length
                    || key.indexOf(StorageKeys.LAST_UPDATED_SUFFIX) === key.length - StorageKeys.LAST_UPDATED_SUFFIX.length);
        }).forEach(function (item) {
            // Item does relate to this plugin.
            // It is not the user's preference about whether to use storage or not (keep that).
            sessionStorage.removeItem(item);
        });
    };

    /**
     * Clear all browser storage used by this plugin.
     */
    var clearStorage = function() {
        Object.keys(localStorage).filter(function (key) {
            return key.indexOf(StorageKeys.PREFIX) === 0 && key.indexOf(StorageKeys.USER_CHOICE_PREFIX) === -1;
        }).forEach(function (item) {
            // Item does relate to this plugin.
            // It is not the user's preference about whether to use storage or not (keep that).
            localStorage.removeItem(item);
        });
        Object.keys(sessionStorage).filter(function (key) {
            return key.indexOf(StorageKeys.PREFIX) === 0 && key.indexOf(StorageKeys.USER_CHOICE_PREFIX) === -1;
        }).forEach(function (item) {
            // Item does relate to this plugin.
            // It is not the user's preference about whether to use storage or not (keep that).
            sessionStorage.removeItem(item);
        });
    };

    return {
        getOpenSections: function(maxSection) {
            return getOpenSections(maxSection);
        },
        init: function(
            userIdInit,
            courseIdInit,
            assumeDataStoreConsent,
            lastSectionNum,
            collapsingAllFromURL
        ) {
            courseId = courseIdInit;
            userId = userIdInit;
            if (lastSectionNum) {
                setSectionStatus(lastSectionNum, true);
            }
            $(document).ready(function () {
                storageSetUp.init(userId, assumeDataStoreConsent, clearStorage);

                clearStoredContent();
                if (storageSetUp.storageAllowed()) {
                    sessionStorage.setItem(StorageKeys.COURSE_VISIT + courseId + StorageKeys.USER + userId, Date.now().toString());

                    if (collapsingAllFromURL) {
                        for (var i = 1; i <= lastSectionNum; i++) {
                            setSectionStatus(i, false);
                        }
                    }
                    // When section content is clicked, update the last visited section.
                    $(Selector.PAGE).on("click", Selector.SECTION_CONTENT, function(e) {
                        var currTar = $(e.currentTarget);
                        var sectionClicked = currTar.closest(Selector.SECTION_MAIN).attr("data-section");
                        setLastSection(sectionClicked);
                    });
                }
            });
        },
        getLastSection: function() {
            return getLastSection();
        },
        setSectionStatus: function(secNum, open) {
            return setSectionStatus(secNum, open);
        },
        setLastSection: function(secNum) {
            return setLastSection(secNum);
        }
    };
});