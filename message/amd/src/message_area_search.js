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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/custom_interaction_events'],
    function($, ajax, templates, notification, str, customEvents) {

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

    /** @type {String} The number of people displayed. */
    Search.prototype._numPeopleDisplayed = 0;

    /** @type {String} The number of people to retrieve. */
    Search.prototype._numPeopleToRetrieve = 20;

    /** @type {Array} The type of available search areas. **/
    Search.prototype._searchAreas = {
        MESSAGES: 'messages',
        PEOPLE: 'people',
        PEOPLEINCOURSE: 'peopleincourse'
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
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHTEXTAREA).on('input', this._searchRequest.bind(this));

        // Handle clicking on a course in the list of people.
        this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.SEARCHPEOPLEINCOURSE, function(e) {
            this._setFilter($(e.currentTarget).html());
            this._setPlaceholderText('searchforperson');
            this._clearSearchArea();
            this._searchArea = this._searchAreas.PEOPLEINCOURSE;
            this._courseid = $(e.currentTarget).data('courseid');
            this._searchPeopleInCourse();
        }.bind(this));

        // Handle deleting the search filter.
        this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.DELETESEARCHFILTER, function() {
            this._hideSearchResults();
            // Filter has been removed, so we don't want to be searching in a course anymore.
            this._searchArea = this._searchAreas.PEOPLE;
            this._setPlaceholderText('searchforpersonorcourse');
            // Go back the contacts.
            this.messageArea.trigger(this.messageArea.EVENTS.PEOPLESEARCHCANCELED);
        }.bind(this));

        // Handle events that occur outside this module.
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONVERSATIONSSELECTED, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.MESSAGES;
            this._setPlaceholderText('searchmessages');
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTSSELECTED, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.PEOPLE;
            this._setPlaceholderText('searchforpersonorcourse');
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESENT, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.MESSAGES;
            this._setPlaceholderText('searchmessages');
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CHOOSEMESSAGESTODELETE, function() {
            this._hideSearchResults();
            this._searchArea = this._searchAreas.MESSAGES;
            this._setPlaceholderText('searchmessages');
            this._disableSearching();
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CANCELDELETEMESSAGES, function() {
            this._enableSearching();
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESDELETED, function() {
            this._enableSearching();
        }.bind(this));

        // Event listeners for scrolling through messages and people in courses.
        customEvents.define(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), [
            customEvents.events.scrollBottom
        ]);
        this.messageArea.onDelegateEvent(customEvents.events.scrollBottom, this.messageArea.SELECTORS.SEARCHRESULTSAREA,
            function() {
                if (this._searchArea == this._searchAreas.MESSAGES) {
                    this._searchMessages();
                } else if (this._searchArea == this._searchAreas.PEOPLEINCOURSE) {
                    this._searchPeopleInCourse();
                }
            }.bind(this)
        );

        // Set the initial search area.
        this._searchArea = this._searchAreas.MESSAGES;
    };

    /**
     * Handles when search requests are sent.
     *
     * @private
     */
    Search.prototype._searchRequest = function() {
        var str = this.messageArea.find(this.messageArea.SELECTORS.SEARCHTEXTAREA + ' input').val();

        if (this._requestTimeout) {
            clearTimeout(this._requestTimeout);
        }

        if (str.trim() === '') {
            // If nothing we being searched then we need to display the usual data.
            if (this._searchArea == this._searchAreas.MESSAGES) {
                this._hideSearchResults();
                this.messageArea.trigger(this.messageArea.EVENTS.MESSAGESEARCHCANCELED);
            } else if (this._searchArea == this._searchAreas.PEOPLE) {
                this._hideSearchResults();
                this.messageArea.trigger(this.messageArea.EVENTS.PEOPLESEARCHCANCELED);
            } else if (this._searchArea == this._searchAreas.PEOPLEINCOURSE) {
                // We are still searching in a course, so need to list all the people again.
                this._clearSearchArea();
                this._searchPeopleInCourse();
            }
            return;
        }

        this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS).hide();
        this.messageArea.find(this.messageArea.SELECTORS.CONTACTS).hide();
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA).show();

        if (this._searchArea == this._searchAreas.MESSAGES) {
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numMessagesDisplayed = 0;
                this._searchMessages();
            }.bind(this), 300);
        } else if (this._searchArea == this._searchAreas.PEOPLEINCOURSE) {
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numPeopleDisplayed = 0;
                this._searchPeopleInCourse();
            }.bind(this), 300);
        } else { // Must be searching for people and courses
            this._requestTimeout = setTimeout(function() {
                this._clearSearchArea();
                this._numPeopleDisplayed = 0;
                this._searchPeople();
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

        var str = this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).val();

        // Tell the user we are loading items.
        this._isLoading = true;

        // Call the web service to get our data.
        var promises = ajax.call([{
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
        return templates.render('core/loading', {}).then(function(html, js) {
            templates.appendNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            numberreceived = data.contacts.length;
            return templates.render('core_message/message_area_message_search_results', data);
        }).then(function(html, js) {
            // Remove the loading icon.
            this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA + " " +
                this.messageArea.SELECTORS.LOADINGICON).remove();
            // Only append data if we got data back.
            if (numberreceived > 0) {
                // Show the new content.
                templates.appendNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), html, js);
                // Increment the number of contacts displayed.
                this._numMessagesDisplayed += numberreceived;
            } else if (this._numMessagesDisplayed == 0) { // Must have nothing to begin with.
                // Replace the new content.
                templates.replaceNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), html, js);
            }
            // Mark that we are no longer busy loading data.
            this._isLoading = false;
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Handles searching for people.
     *
     * @private
     * @return {Promise} The promise resolved when the search area has been rendered
     */
    Search.prototype._searchPeople = function() {
        var str = this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).val();

        // Call the web service to get our data.
        var promises = ajax.call([{
            methodname: 'core_message_data_for_messagearea_search_people',
            args: {
                userid: this.messageArea.getCurrentUserId(),
                search: str,
                limitnum: this._numPeopleToRetrieve
            }
        }]);

        // Perform the search and replace the content.
        return templates.render('core/loading', {}).then(function(html, js) {
            templates.replaceNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            return templates.render('core_message/message_area_people_search_results', data);
        }).then(function(html, js) {
            templates.replaceNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), html, js);
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Handles searching for people in a course.
     *
     * @private
     * @return {Promise|boolean} The promise resolved when the search area has been rendered
     */
    Search.prototype._searchPeopleInCourse = function() {
        if (this._isLoading) {
            return false;
        }

        var str = this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).val();

        // Tell the user we are loading items.
        this._isLoading = true;

        // Call the web service to get our data.
        var promises = ajax.call([{
            methodname: 'core_message_data_for_messagearea_search_people_in_course',
            args: {
                userid: this.messageArea.getCurrentUserId(),
                courseid: this._courseid,
                search: str,
                limitfrom: this._numPeopleDisplayed,
                limitnum: this._numPeopleToRetrieve
            }
        }]);

        // Keep track of the number of contacts
        var numberreceived = 0;
        // Add loading icon to the end of the list.
        return templates.render('core/loading', {}).then(function(html, js) {
            templates.appendNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA),
                "<div style='text-align:center'>" + html + "</div>", js);
            return promises[0];
        }.bind(this)).then(function(data) {
            numberreceived = data.contacts.length;
            return templates.render('core_message/message_area_people_search_results', data);
        }).then(function(html, js) {
            // Remove the loading icon.
            this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA + " " +
                this.messageArea.SELECTORS.LOADINGICON).remove();
            // Only append data if we got data back.
            if (numberreceived > 0) {
                // Show the new content.
                templates.appendNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), html, js);
                // Increment the number of contacts displayed.
                this._numPeopleDisplayed += numberreceived;
            } else if (this._numPeopleDisplayed == 0) { // Must have nothing to begin with.
                // Replace the new content.
                templates.replaceNodeContents(this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA), html, js);
            }
            // Mark that we are no longer busy loading data.
            this._isLoading = false;
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Sets placeholder text for search input.
     *
     * @private
     * @param {String} text The placeholder text
     * @return {Promise} The promise resolved when the placeholder text has been set
     */
    Search.prototype._setPlaceholderText = function(text) {
        return str.get_string(text, 'message').then(function(s) {
            this.messageArea.find(this.messageArea.SELECTORS.SEARCHTEXTAREA + ' input').attr('placeholder', s);
        }.bind(this));
    };

    /**
     * Sets filter for search input.
     *
     * @private
     * @param {String} text The filter text
     */
    Search.prototype._setFilter = function(text) {
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).val('');
        this.messageArea.find(this.messageArea.SELECTORS.CONTACTSAREA).addClass('searchfilter');
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHFILTERAREA).show();
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHFILTER).html(text);
    };

    /**
     * Hides filter for search input.
     *
     * @private
     */
    Search.prototype._clearFilters = function() {
        this.messageArea.find(this.messageArea.SELECTORS.CONTACTSAREA).removeClass('searchfilter');
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHFILTER).empty();
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHFILTERAREA).hide();
    };

    /**
     * Handles clearing the search area.
     *
     * @private
     */
    Search.prototype._clearSearchArea = function() {
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA).empty();
    };

    /**
     * Handles hiding the search area.
     *
     * @private
     */
    Search.prototype._hideSearchResults = function() {
        this._clearFilters();
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHTEXTAREA + ' input').val('');
        this._clearSearchArea();
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHRESULTSAREA).hide();
    };

    /**
     * Disable search.
     *
     * @private
     */
    Search.prototype._disableSearching = function() {
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).prop('disabled', true);

        if (this._searchArea == this._searchAreas.MESSAGES) {
            this.messageArea.trigger(this.messageArea.EVENTS.MESSAGESEARCHCANCELED);
        } else if (this._searchArea == this._searchAreas.PEOPLE) {
            this.messageArea.trigger(this.messageArea.EVENTS.PEOPLESEARCHCANCELED);
        }
    };

    /**
     * Enable search.
     *
     * @private
     */
    Search.prototype._enableSearching = function() {
        this.messageArea.find(this.messageArea.SELECTORS.SEARCHBOX).prop('disabled', false);
    };

    return Search;
});
