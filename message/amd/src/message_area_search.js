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
 * The module handles searching contacts.
 *
 * @module     core_message/message_area_search
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/custom_interaction_events',
        'core_message/message_area_events'],
    function($, Ajax, Templates, Notification, Str, CustomEvents, Events) {

    /** @type {Object} The list of selectors for the message area. */
    var SELECTORS = {
        CONTACTS: "[data-region='contacts'][data-region-content='contacts']",
        CONTACTSAREA: "[data-region='contacts-area']",
        CONVERSATIONS: "[data-region='contacts'][data-region-content='conversations']",
        DELETESEARCHFILTER: "[data-region='search-filter-area']",
        LOADINGICON: '.loading-icon',
        SEARCHBOX: "[data-region='search-box']",
        SEARCHFILTER: "[data-region='search-filter']",
        SEARCHFILTERAREA: "[data-region='search-filter-area']",
        SEARCHRESULTSAREA: "[data-region='search-results-area']",
        SEARCHTEXTAREA: "[data-region='search-text-area']",
        SEARCHUSERSINCOURSE: "[data-action='search-users-in-course']",
    };

    /**
     * Search class.
     *
     * @param {Messagearea} messageArea The messaging area object.
     */
    function Search(messageArea) {
        this.messageArea = messageArea;
        this._init();
    }

    /** @type {Messagearea} The messaging area object. */
    Search.prototype.messageArea = null;

    /** @type {String} The area we are searching in. */
    Search.prototype._searchArea = null;

    /** @type {String} The id of the course we are searching in (if any). */
    Search.prototype._courseid = null;

    /** @type {Boolean} checks if we are currently loading  */
    Search.prototype._isLoading = false;

    /** @type {String} The number of messages displayed. */
    Search.prototype._numMessagesDisplayed = 0;

    /** @type {String} The number of messages to retrieve. */
    Search.prototype._numMessagesToRetrieve = 20;

    /** @type {String} The number of users displayed. */
    Search.prototype._numUsersDisplayed = 0;

    /** @type {String} The number of users to retrieve. */
    Search.prototype._numUsersToRetrieve = 20;

    /** @type {Array} The type of available search areas. **/
    Search.prototype._searchAreas = {
        MESSAGES: 'messages',
        USERS: 'users',
        USERSINCOURSE: 'usersincourse'
    };

    /** @type {int} The timeout before performing an ajax search */
    Search.prototype._requestTimeout = null;

    /**
     * Initialise the event listeners.
     *
     * @private
     */
    Search.prototype._init = function() {
        // Handle searching for text.
        this.messageArea.find(SELECTORS.SEARCHTEXTAREA).on('input', this._searchRequest.bind(this));

        // Handle clicking on a course in the list of users.
        this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.SEARCHUSERSINCOURSE, function(e) {
            this._setFilter($(e.currentTarget).html());
            this._setPlaceholderText('searchforuser');
            this._clearSearchArea();
            this._searchArea = this._searchAreas.USERSINCOURSE;
            this._courseid = $(e.currentTarget).data('courseid');
            this._searchUsersInCourse();
            this.messageArea.find(SELECTORS.SEARCHBOX).focus();
        }.bind(this));

        // Handle deleting the search filter.
        this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.DELETESEARCHFILTER, function() {
            this._hideSearchResults();
            // Filter has been removed, so we don't want to be searching in a course anymore.
            this._searchArea = this._searchAreas.USERS;
            this._setPlaceholderText('searchforuserorcourse');
            // Go back the contacts.
            this.messageArea.trigger(Events.USERSSEARCHCANCELED);
            this.messageArea.find(SELECTORS.SEARCHBOX).focus();
        }.bind(this));

        // Handle events that occur outside this module.
        this.messageArea.onCustomEvent(Events.CONVERSATIONSSELECTED, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.MESSAGES;
            this._setPlaceholderText('searchmessages');
        }.bind(this));
        this.messageArea.onCustomEvent(Events.CONTACTSSELECTED, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.USERS;
            this._setPlaceholderText('searchforuserorcourse');
        }.bind(this));
        this.messageArea.onCustomEvent(Events.MESSAGESENT, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.MESSAGES;
            this._setPlaceholderText('searchmessages');
        }.bind(this));

        // Event listeners for scrolling through messages and users in courses.
        CustomEvents.define(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), [
            CustomEvents.events.scrollBottom
        ]);
        this.messageArea.onDelegateEvent(CustomEvents.events.scrollBottom, SELECTORS.SEARCHRESULTSAREA,
            function() {
                if (this._searchArea == this._searchAreas.MESSAGES) {
                    this._searchMessages();
                } else if (this._searchArea == this._searchAreas.USERSINCOURSE) {
                    this._searchUsersInCourse();
                }
            }.bind(this)
        );

        // Set the initial search area.
        this._searchArea = (this.messageArea.showContactsFirst()) ? this._searchAreas.USERS : this._searchAreas.MESSAGES;
    };

    /**
     * Handles when search requests are sent.
     *
     * @private
     */
    Search.prototype._searchRequest = function() {
        var str = this.messageArea.find(SELECTORS.SEARCHTEXTAREA + ' input').val();

        if (this._requestTimeout) {
            clearTimeout(this._requestTimeout);
        }

        if (str.trim() === '') {
            // If nothing we being searched then we need to display the usual data.
            if (this._searchArea == this._searchAreas.MESSAGES) {
                this._hideSearchResults();
                this.messageArea.trigger(Events.MESSAGESEARCHCANCELED);
            } else if (this._searchArea == this._searchAreas.USERS) {
                this._hideSearchResults();
                this.messageArea.trigger(Events.USERSSEARCHCANCELED);
            } else if (this._searchArea == this._searchAreas.USERSINCOURSE) {
                // We are still searching in a course, so need to list all the users again.
                this._clearSearchArea();
                this._searchUsersInCourse();
            }
            return;
        }

        this.messageArea.find(SELECTORS.CONVERSATIONS).hide();
        this.messageArea.find(SELECTORS.CONTACTS).hide();
        this.messageArea.find(SELECTORS.SEARCHRESULTSAREA).show();

        if (this._searchArea == this._searchAreas.MESSAGES) {
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numMessagesDisplayed = 0;
                this._searchMessages();
            }.bind(this), 300);
        } else if (this._searchArea == this._searchAreas.USERSINCOURSE) {
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numUsersDisplayed = 0;
                this._searchUsersInCourse();
            }.bind(this), 300);
        } else { // Must be searching for users and courses
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numUsersDisplayed = 0;
                this._searchUsers();
            }.bind(this), 300);
        }
    };

    /**
     * Handles searching for messages.
     *
     * @private
     * @return {Promise|boolean} The promise resolved when the search area has been rendered
     */
    Search.prototype._searchMessages = function() {
        if (this._isLoading) {
            return false;
        }

        var str = this.messageArea.find(SELECTORS.SEARCHBOX).val();

        // Tell the user we are loading items.
        this._isLoading = true;

        // Call the web service to get our data.
        var promises = Ajax.call([{
            methodname: 'core_message_data_for_messagearea_search_messages',
            args: {
                userid: this.messageArea.getCurrentUserId(),
                search: str,
                limitfrom: this._numMessagesDisplayed,
                limitnum: this._numMessagesToRetrieve
            }
        }]);

        // Keep track of the number of messages
        var numberreceived = 0;
        // Add loading icon to the end of the list.
        return Templates.render('core/loading', {}).then(function(html, js) {
            Templates.appendNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            numberreceived = data.contacts.length;
            return Templates.render('core_message/message_area_message_search_results', data);
        }).then(function(html, js) {
            // Remove the loading icon.
            this.messageArea.find(SELECTORS.SEARCHRESULTSAREA + " " +
                SELECTORS.LOADINGICON).remove();
            // Only append data if we got data back.
            if (numberreceived > 0) {
                // Show the new content.
                Templates.appendNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), html, js);
                // Increment the number of contacts displayed.
                this._numMessagesDisplayed += numberreceived;
            } else if (this._numMessagesDisplayed == 0) { // Must have nothing to begin with.
                // Replace the new content.
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), html, js);
            }
            // Mark that we are no longer busy loading data.
            this._isLoading = false;
        }.bind(this)).fail(Notification.exception);
    };

    /**
     * Handles searching for users.
     *
     * @private
     * @return {Promise} The promise resolved when the search area has been rendered
     */
    Search.prototype._searchUsers = function() {
        var str = this.messageArea.find(SELECTORS.SEARCHBOX).val();

        // Call the web service to get our data.
        var promises = Ajax.call([{
            methodname: 'core_message_data_for_messagearea_search_users',
            args: {
                userid: this.messageArea.getCurrentUserId(),
                search: str,
                limitnum: this._numUsersToRetrieve
            }
        }]);

        // Perform the search and replace the content.
        return Templates.render('core/loading', {}).then(function(html, js) {
            Templates.replaceNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            if (data.contacts.length > 0) {
                data.hascontacts = true;
            }
            if (data.courses.length > 0) {
                data.hascourses = true;
            }
            if (data.noncontacts.length > 0) {
                data.hasnoncontacts = true;
            }
            return Templates.render('core_message/message_area_user_search_results', data);
        }).then(function(html, js) {
            Templates.replaceNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), html, js);
        }.bind(this)).fail(Notification.exception);
    };

    /**
     * Handles searching for users in a course.
     *
     * @private
     * @return {Promise|boolean} The promise resolved when the search area has been rendered
     */
    Search.prototype._searchUsersInCourse = function() {
        if (this._isLoading) {
            return false;
        }

        var str = this.messageArea.find(SELECTORS.SEARCHBOX).val();

        // Tell the user we are loading items.
        this._isLoading = true;

        // Call the web service to get our data.
        var promises = Ajax.call([{
            methodname: 'core_message_data_for_messagearea_search_users_in_course',
            args: {
                userid: this.messageArea.getCurrentUserId(),
                courseid: this._courseid,
                search: str,
                limitfrom: this._numUsersDisplayed,
                limitnum: this._numUsersToRetrieve
            }
        }]);

        // Keep track of the number of contacts
        var numberreceived = 0;
        // Add loading icon to the end of the list.
        return Templates.render('core/loading', {}).then(function(html, js) {
            Templates.appendNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            numberreceived = data.contacts.length;
            if (numberreceived > 0) {
                data.hascontacts = true;
            }
            return Templates.render('core_message/message_area_user_search_results', data);
        }).then(function(html, js) {
            // Remove the loading icon.
            this.messageArea.find(SELECTORS.SEARCHRESULTSAREA + " " +
                SELECTORS.LOADINGICON).remove();
            // Only append data if we got data back.
            if (numberreceived > 0) {
                // Show the new content.
                Templates.appendNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), html, js);
                // Increment the number of contacts displayed.
                this._numUsersDisplayed += numberreceived;
            } else if (this._numUsersDisplayed == 0) { // Must have nothing to begin with.
                // Replace the new content.
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.SEARCHRESULTSAREA), html, js);
            }
            // Mark that we are no longer busy loading data.
            this._isLoading = false;
        }.bind(this)).fail(Notification.exception);
    };

    /**
     * Sets placeholder text for search input.
     *
     * @private
     * @param {String} text The placeholder text
     * @return {Promise} The promise resolved when the placeholder text has been set
     */
    Search.prototype._setPlaceholderText = function(text) {
        return Str.get_string(text, 'message').then(function(s) {
            this.messageArea.find(SELECTORS.SEARCHTEXTAREA + ' input').attr('placeholder', s);
        }.bind(this));
    };

    /**
     * Sets filter for search input.
     *
     * @private
     * @param {String} text The filter text
     */
    Search.prototype._setFilter = function(text) {
        this.messageArea.find(SELECTORS.SEARCHBOX).val('');
        this.messageArea.find(SELECTORS.CONTACTSAREA).addClass('searchfilter');
        this.messageArea.find(SELECTORS.SEARCHFILTERAREA).show();
        this.messageArea.find(SELECTORS.SEARCHFILTER).html(text);
        Str.get_string('removecoursefilter', 'message', text).then(function(languagestring) {
            this.messageArea.find(SELECTORS.SEARCHFILTERAREA).attr('aria-label', languagestring);
        }.bind(this));
    };

    /**
     * Hides filter for search input.
     *
     * @private
     */
    Search.prototype._clearFilters = function() {
        this.messageArea.find(SELECTORS.CONTACTSAREA).removeClass('searchfilter');
        this.messageArea.find(SELECTORS.SEARCHFILTER).empty();
        this.messageArea.find(SELECTORS.SEARCHFILTERAREA).hide();
        this.messageArea.find(SELECTORS.SEARCHFILTERAREA).removeAttr('aria-label');
    };

    /**
     * Handles clearing the search area.
     *
     * @private
     */
    Search.prototype._clearSearchArea = function() {
        this.messageArea.find(SELECTORS.SEARCHRESULTSAREA).empty();
    };

    /**
     * Handles hiding the search area.
     *
     * @private
     */
    Search.prototype._hideSearchResults = function() {
        this._clearFilters();
        this.messageArea.find(SELECTORS.SEARCHTEXTAREA + ' input').val('');
        this._clearSearchArea();
        this.messageArea.find(SELECTORS.SEARCHRESULTSAREA).hide();
    };

    return Search;
});
