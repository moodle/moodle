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
 * Javascript Module to handle browser storage for format_tiles for student view.
 * (Can also be used by staff when they view the student view).
 * Stores and retrieves course content and settings
 * e.g. which filter button do I have pressed
 *
 * @module browser_storage
 * @package course/format
 * @subpackage tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery", "format_tiles/browser_storage_set_up"], function ($, storageSetUp) {
    "use strict";

    var courseId;
    var userId;

    var localStorageKeyElements = {
        prefix: "mdl-tiles-",
        course: "mdl-tiles-course-",
        lastSection: "-lastSecId",
        content: "-content",
        lastUpdated: "-lastUpdated",
        collapseSecZero: "-collapsesec0",
        user: "-user-",
        section: "-sec-",
        userChoicePrefix: "mdl-tiles-userPrefStorage-"
    };

    /**
     * The last visited section number will be stored with a key in the format
     * mdl-tiles-course-[courseid]-lastSecId
     * @returns {string} the key to use for this course
     */
    var encodeLastVistedSectionKeyName = function() {
        return localStorageKeyElements.course + courseId
            + localStorageKeyElements.user + userId
            + localStorageKeyElements.lastSection;
    };

    /**
     * Whether or not section zero is collapsed for this course/user
     * will be stored with a key in this format
     * @returns {string} the key to use
     */
    var collapseSecZeroKey = function() {
        return localStorageKeyElements.course + courseId
            + localStorageKeyElements.user + userId
            + localStorageKeyElements.collapseSecZero;
    };

    /**
     * Set the last visited section for the user for this course
     * Used to reload that section on next visit
     * Data is just an integer for section if
     * Uses local storage not session storage so that it persists
     * @param {number} sectionNum the section number last visited
     */
    var setLastVisitedSection = function (sectionNum) {
        if (sectionNum && storageSetUp.Enabled.local) {
            localStorage.setItem(encodeLastVistedSectionKeyName(), sectionNum.toString());
        } else {
            localStorage.removeItem(encodeLastVistedSectionKeyName());
        }
    };

    return {

        init: function (
            course,
            isEditing,
            sectionNum,
            assumeDataStoreConsent,
            user
        ) {
            courseId = course.toString();
            userId = user.toString();
            storageSetUp.init(userId, assumeDataStoreConsent);

            $(document).ready(function () {
                if (isEditing) {
                    // Teacher is editing now so not using JS nav but set their current section for when they stop editing.
                    setLastVisitedSection(sectionNum);
                }
            });
        },

        storageEnabledSession: function () {
            return storageSetUp.Enabled.session;
        },
        storageEnabledLocal: function () {
            return storageSetUp.Enabled.local;
        },

        /**
         * Get the user's last visited section id for this course
         * @return {string|null} the section ID or null if none stored
         */
        getLastVisitedSection: function () {
            return storageSetUp.Enabled.local && localStorage.getItem(encodeLastVistedSectionKeyName());
        },

        /**
         * When user collapsed or expands section zero, record their choice in localStorage so
         * that it can be applied next time they visit
         * @param {string} status to be applied
         */
        setSecZeroCollapseStatus: function (status) {
            if (storageSetUp.Enabled.local && storageSetUp.storageAllowed()) {
                if (status === "collapsed") {
                    localStorage.removeItem(collapseSecZeroKey());
                } else {
                    localStorage.setItem(collapseSecZeroKey(), "1");
                }
            }
        },
        /**
         * Get the last status of section zero for the present course from localStorage
         * @returns {boolean} whether collapsed or not
         */
        getSecZeroCollapseStatus: function () {
            return !!localStorage.getItem(collapseSecZeroKey());
        },

        setLastVisitedSection: function (sectionNum) {
            // Return object ("public") access to the "private" method above.
            if (storageSetUp.storageAllowed()) {
                setLastVisitedSection(sectionNum);
            }
        }
    };
});