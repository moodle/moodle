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
 * Javascript Module to help set up user needs browser storage.
 *
 * @module browser_storage_set_up
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery"], function ($) {
    "use strict";

    var storageUserConsent = {
        GIVEN: "yes", // What to store in local storage to indicate consent granted.
        DENIED: "no" // Or to indicate consent denied.
    };

    var localStorageKeyElements = {
        userPrefStorage: "mdl-tiles-userPrefStorage",
        user: "-user-"
    };

    var storageType = {
        local: "local",
        session: "session"
    };

    var Enabled = {
        local: false,
        session: false
    };
    var userId;

    var userChoice = null; // The user's current choice - initially null as we have not yet checked local storage or asked user.

    var encodeStorageKey = function() {
        return localStorageKeyElements.userPrefStorage + localStorageKeyElements.user + userId;
    };

    var storageAllowed = function() {
        if (userChoice !== null) {
            return userChoice;
        } else {
            var browserChoice = localStorage.getItem(encodeStorageKey());
            if (browserChoice) {
                userChoice = browserChoice === storageUserConsent.GIVEN;
                return userChoice;
            } else {
                // This shouldn't happen much as user asked for pref if not present.
                return null;
            }
        }
    };

    var setAllowed = function(allowedOrNot) {
        if (allowedOrNot) {
            userChoice = true;
            localStorage.setItem(encodeStorageKey(), storageUserConsent.GIVEN);
        } else {
            userChoice = false;
            localStorage.setItem(encodeStorageKey(), storageUserConsent.DENIED);
        }
    };

    /**
     * Launch the window enabling the user to select whether we want to store data locally or not
     */
    var obtainUserPreference = function () {
        require(["core/str", "core/notification"], function(str, Notification) {
            str.get_strings([
                {key: "datapref", component: "format_tiles"},
                {key: "dataprefquestion", component: "format_tiles"},
                {key: "yes"},
                {key: "cancel"}
            ]).done(function (s) {
                Notification.confirm(
                    s[0],
                    s[1],
                    s[2],
                    s[3],
                    function() {
                        setAllowed(true);
                    },
                    function() {
                        setAllowed(false);
                    }
                );
            });
        });
    };

    /**
     * Check if the user's browser supports localstorage or session storage
     * @param {String} localOrSession the type of storage we wish to check
     * @returns {boolean} whether or not storage is supported
     */
    var storageInitialCheck = function (localOrSession) {
        var storage;
        try {
            if (localOrSession === storageType.local) {
                storage = localStorage;
            } else if (localOrSession === storageType.session) {
                storage = sessionStorage;
            }
            if (typeof storage === "undefined") {
                return false;
            }
            storage.setItem("testItem", "testValue");
            if (storage.getItem("testItem") === "testValue") {
                storage.removeItem("testItem");
                return true;
            }
            return false;
        } catch (err) {
            require(["core/log"], function(log) {
                log.debug(err);
            });
            return false;
        }
    };

    return {

        setAllowed: function(allowedOrNot) {
            setAllowed(allowedOrNot);
        },
        storageAllowed: function() {
            return storageAllowed();
        },
        getStorageKey: function() {
            return encodeStorageKey();
        },
        Enabled: Enabled,

        init: function(userIdInit, assumeConsent) {
            userId = userIdInit;
            $(document).ready(function () {
                Enabled.local = storageInitialCheck(storageType.local);
                Enabled.session = storageInitialCheck(storageType.session);

                if (assumeConsent) {
                    userChoice = true;
                } else if (storageAllowed() === null && Enabled.local) {
                    // We wait 3 seconds before launching the dialog to ensure content finished loading.
                    setTimeout(function() {
                        obtainUserPreference();
                    }, 3000);
                }

                // If the user clicks the "Data preference" item in the navigation menu,
                // show them the dialogue box to re-enter their local storage choice.
                $('a[href*="datapref"]').click(function (e) {
                    e.preventDefault();
                    obtainUserPreference();
                });
            });
        }
    };

});