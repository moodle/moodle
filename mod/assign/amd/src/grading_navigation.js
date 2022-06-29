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
 * Javascript to handle changing users via the user selector in the header.
 *
 * @module     mod_assign/grading_navigation
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/notification', 'core/str', 'core/form-autocomplete',
        'core/ajax', 'mod_assign/grading_form_change_checker'],
       function($, notification, str, autocomplete, ajax, checker) {

    /**
     * GradingNavigation class.
     *
     * @class mod_assign/grading_navigation
     * @param {String} selector The selector for the page region containing the user navigation.
     */
    var GradingNavigation = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);
        this._filters = [];
        this._users = [];
        this._filteredUsers = [];
        this._lastXofYUpdate = 0;
        this._firstLoadUsers = true;

        // Get the current user list from a webservice.
        this._loadAllUsers();

        // We do not allow navigation while ajax requests are pending.
        // Attach listeners to the select and arrow buttons.

        this._region.find('[data-action="previous-user"]').on('click', this._handlePreviousUser.bind(this));
        this._region.find('[data-action="next-user"]').on('click', this._handleNextUser.bind(this));
        this._region.find('[data-action="change-user"]').on('change', this._handleChangeUser.bind(this));
        this._region.find('[data-region="user-filters"]').on('click', this._toggleExpandFilters.bind(this));

        $(document).on('user-changed', this._refreshSelector.bind(this));
        $(document).on('done-saving-show-next', this._handleNextUser.bind(this));

        // Position the configure filters panel under the link that expands it.
        var toggleLink = this._region.find('[data-region="user-filters"]');
        var configPanel = $(document.getElementById(toggleLink.attr('aria-controls')));

        configPanel.on('change', 'select', this._filterChanged.bind(this));

        var userid = $('[data-region="grading-navigation-panel"]').data('first-userid');
        if (userid) {
            this._selectUserById(userid);
        }

        str.get_string('changeuser', 'mod_assign').done(function(s) {
                autocomplete.enhance('[data-action=change-user]', false, 'mod_assign/participant_selector', s);
            }
        ).fail(notification.exception);

        $(document).bind("start-loading-user", function() {
            this._isLoading = true;
        }.bind(this));
        $(document).bind("finish-loading-user", function() {
            this._isLoading = false;
        }.bind(this));
    };

    /** @property {Boolean} Boolean tracking active ajax requests. */
    GradingNavigation.prototype._isLoading = false;

    /** @property {String} Selector for the page region containing the user navigation. */
    GradingNavigation.prototype._regionSelector = null;

    /** @property {Array} The list of active filter keys */
    GradingNavigation.prototype._filters = null;

    /** @property {Array} The list of users */
    GradingNavigation.prototype._users = null;

    /** @property {JQuery} JQuery node for the page region containing the user navigation. */
    GradingNavigation.prototype._region = null;

    /** @property {String} Last active filters */
    GradingNavigation.prototype._lastFilters = '';

    /**
     * Load the list of all users for this assignment.
     *
     * @private
     * @method _loadAllUsers
     * @return {Boolean} True if the user list was fetched.
     */
    GradingNavigation.prototype._loadAllUsers = function() {
        var select = this._region.find('[data-action=change-user]');
        var assignmentid = select.attr('data-assignmentid');
        var groupid = select.attr('data-groupid');

        var filterPanel = this._region.find('[data-region="configure-filters"]');
        var filter = filterPanel.find('select[name="filter"]').val();
        var workflowFilter = filterPanel.find('select[name="workflowfilter"]');
        if (workflowFilter) {
            filter += ',' + workflowFilter.val();
        }
        var markerFilter = filterPanel.find('select[name="markerfilter"]');
        if (markerFilter) {
            filter += ',' + markerFilter.val();
        }

        if (this._lastFilters == filter) {
            return false;
        }
        this._lastFilters = filter;

        ajax.call([{
            methodname: 'mod_assign_list_participants',
            args: {assignid: assignmentid, groupid: groupid, filter: '', onlyids: true, tablesort: true},
            done: this._usersLoaded.bind(this),
            fail: notification.exception
        }]);
        return true;
    };

    /**
     * Call back to rebuild the user selector and x of y info when the user list is updated.
     *
     * @private
     * @method _usersLoaded
     * @param {Array} users
     */
    GradingNavigation.prototype._usersLoaded = function(users) {
        this._firstLoadUsers = false;
        this._filteredUsers = this._users = users;
        if (this._users.length) {
            // Position the configure filters panel under the link that expands it.
            var toggleLink = this._region.find('[data-region="user-filters"]');
            var configPanel = $(document.getElementById(toggleLink.attr('aria-controls')));

            configPanel.find('select[name="filter"]').trigger('change');
        } else {
            this._selectNoUser();
        }
        this._triggerNextUserEvent();
    };

    /**
     * Close the configure filters panel if a click is detected outside of it.
     *
     * @private
     * @method _checkClickOutsideConfigureFilters
     * @param {Event} event
     */
    GradingNavigation.prototype._checkClickOutsideConfigureFilters = function(event) {
        var configPanel = this._region.find('[data-region="configure-filters"]');

        if (!configPanel.is(event.target) && configPanel.has(event.target).length === 0) {
            var toggleLink = this._region.find('[data-region="user-filters"]');

            configPanel.hide();
            configPanel.attr('aria-hidden', 'true');
            toggleLink.attr('aria-expanded', 'false');
            $(document).unbind('click.mod_assign_grading_navigation');
        }
    };

    /**
     * Close the configure filters panel if a click is detected outside of it.
     *
     * @private
     * @method _updateFilterPreference
     * @param {Number} userId The current user id.
     * @param {Array} filterList The list of current filter values.
     * @param {Array} preferenceNames The names of the preferences to update
     * @return {Promise} Resolved when all the preferences are updated.
     */
    GradingNavigation.prototype._updateFilterPreferences = function(userId, filterList, preferenceNames) {
        var preferences = [],
            i = 0;

        if (filterList.length == 0 || this._firstLoadUsers) {
            // Nothing to update.
            var deferred = $.Deferred();
            deferred.resolve();
            return deferred;
        }
        // General filter.
        // Set the user preferences to the current filters.
        for (i = 0; i < filterList.length; i++) {
            var newValue = filterList[i];
            if (newValue == 'none') {
                newValue = '';
            }

            preferences.push({
                userid: userId,
                name: preferenceNames[i],
                value: newValue
            });
        }

        return ajax.call([{
            methodname: 'core_user_set_user_preferences',
            args: {
                preferences: preferences
            }
        }])[0];
    };
    /**
     * Turn a filter on or off.
     *
     * @private
     * @method _filterChanged
     */
    GradingNavigation.prototype._filterChanged = function() {
        // There are 3 types of filter right now.
        var filterPanel = this._region.find('[data-region="configure-filters"]');
        var filters = filterPanel.find('select');
        var preferenceNames = [];

        this._filters = [];
        filters.each(function(idx, ele) {
            var element = $(ele);
            this._filters.push(element.val());
            preferenceNames.push('assign_' + element.prop('name'));
        }.bind(this));

        // Update the active filter string.
        var filterlist = [];
        filterPanel.find('option:checked').each(function(idx, ele) {
            filterlist[filterlist.length] = $(ele).text();
        });
        if (filterlist.length) {
            this._region.find('[data-region="user-filters"] span').text(filterlist.join(', '));
        } else {
            str.get_string('nofilters', 'mod_assign').done(function(s) {
                this._region.find('[data-region="user-filters"] span').text(s);
            }.bind(this)).fail(notification.exception);
        }

        var select = this._region.find('[data-action=change-user]');
        var currentUserID = select.data('currentuserid');
        this._updateFilterPreferences(currentUserID, this._filters, preferenceNames).done(function() {
            // Reload the list of users to apply the new filters.
            if (!this._loadAllUsers()) {
                var userid = parseInt(select.attr('data-selected'));
                var foundIndex = 0;
                // Search the returned users for the current selection.
                $.each(this._filteredUsers, function(index, user) {
                    if (userid == user.id) {
                        foundIndex = index;
                    }
                });

                if (this._filteredUsers.length) {
                    this._selectUserById(this._filteredUsers[foundIndex].id);
                } else {
                    this._selectNoUser();
                }

            }
        }.bind(this)).fail(notification.exception);
        this._refreshCount();
    };

    /**
     * Select no users, because no users match the filters.
     *
     * @private
     * @method _selectNoUser
     */
    GradingNavigation.prototype._selectNoUser = function() {
        // Detect unsaved changes, and offer to save them - otherwise change user right now.
        if (this._isLoading) {
            return;
        }
        if (checker.checkFormForChanges('[data-region="grade-panel"] .gradeform')) {
            // Form has changes, so we need to confirm before switching users.
            str.get_strings([
                {key: 'unsavedchanges', component: 'mod_assign'},
                {key: 'unsavedchangesquestion', component: 'mod_assign'},
                {key: 'saveandcontinue', component: 'mod_assign'},
                {key: 'cancel', component: 'core'},
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', -1);
                });
            });
        } else {
            $(document).trigger('user-changed', -1);
        }
    };

    /**
     * Select the specified user by id.
     *
     * @private
     * @method _selectUserById
     * @param {Number} userid
     */
    GradingNavigation.prototype._selectUserById = function(userid) {
        var select = this._region.find('[data-action=change-user]');
        var useridnumber = parseInt(userid, 10);

        // Detect unsaved changes, and offer to save them - otherwise change user right now.
        if (this._isLoading) {
            return;
        }
        if (checker.checkFormForChanges('[data-region="grade-panel"] .gradeform')) {
            // Form has changes, so we need to confirm before switching users.
            str.get_strings([
                {key: 'unsavedchanges', component: 'mod_assign'},
                {key: 'unsavedchangesquestion', component: 'mod_assign'},
                {key: 'saveandcontinue', component: 'mod_assign'},
                {key: 'cancel', component: 'core'},
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', useridnumber);
                });
            });
        } else {
            select.attr('data-selected', userid);

            if (!isNaN(useridnumber) && useridnumber > 0) {
                $(document).trigger('user-changed', userid);
            }
        }
    };

    /**
     * Expand or collapse the filter config panel.
     *
     * @private
     * @method _toggleExpandFilters
     * @param {Event} event
     */
    GradingNavigation.prototype._toggleExpandFilters = function(event) {
        event.preventDefault();
        var toggleLink = $(event.target).closest('[data-region="user-filters"]');
        var expanded = toggleLink.attr('aria-expanded') == 'true';
        var configPanel = $(document.getElementById(toggleLink.attr('aria-controls')));

        if (expanded) {
            configPanel.hide();
            configPanel.attr('aria-hidden', 'true');
            toggleLink.attr('aria-expanded', 'false');
            $(document).unbind('click.mod_assign_grading_navigation');
        } else {
            configPanel.css('display', 'inline-block');
            configPanel.attr('aria-hidden', 'false');
            toggleLink.attr('aria-expanded', 'true');
            event.stopPropagation();
            $(document).on('click.mod_assign_grading_navigation', this._checkClickOutsideConfigureFilters.bind(this));
        }
    };

    /**
     * Change to the previous user in the grading list.
     *
     * @private
     * @method _handlePreviousUser
     * @param {Event} e
     */
    GradingNavigation.prototype._handlePreviousUser = function(e) {
        e.preventDefault();
        var select = this._region.find('[data-action=change-user]');
        var currentUserId = select.attr('data-selected');
        var i = 0;
        var currentIndex = 0;

        for (i = 0; i < this._filteredUsers.length; i++) {
            if (this._filteredUsers[i].id == currentUserId) {
                currentIndex = i;
                break;
            }
        }

        var count = this._filteredUsers.length;
        var newIndex = (currentIndex - 1);
        if (newIndex < 0) {
            newIndex = count - 1;
        }

        if (count) {
            this._selectUserById(this._filteredUsers[newIndex].id);
        }
    };

    /**
     * Change to the next user in the grading list.
     *
     * @param {Event} e
     * @param {Boolean} saved Has the form already been saved? Skips checking for changes if true.
     */
    GradingNavigation.prototype._handleNextUser = function(e, saved) {
        e.preventDefault();
        var select = this._region.find('[data-action=change-user]');
        var currentUserId = select.attr('data-selected');
        var i = 0;
        var currentIndex = 0;

        for (i = 0; i < this._filteredUsers.length; i++) {
            if (this._filteredUsers[i].id == currentUserId) {
                currentIndex = i;
                break;
            }
        }

        var count = this._filteredUsers.length;
        var newIndex = (currentIndex + 1) % count;

        if (saved && count) {
            // If we've already saved the grade, skip checking if we've made any changes.
            var userid = this._filteredUsers[newIndex].id;
            var useridnumber = parseInt(userid, 10);
            select.attr('data-selected', userid);
            if (!isNaN(useridnumber) && useridnumber > 0) {
                $(document).trigger('user-changed', userid);
            }
        } else if (count) {
            this._selectUserById(this._filteredUsers[newIndex].id);
        }
    };

    /**
     * Set count string. This method only sets the value for the last time it was ever called to deal
     * with promises that return in a non-predictable order.
     *
     * @private
     * @method _setCountString
     * @param {Number} x
     * @param {Number} y
     */
    GradingNavigation.prototype._setCountString = function(x, y) {
        var updateNumber = 0;
        this._lastXofYUpdate++;
        updateNumber = this._lastXofYUpdate;

        var param = {x: x, y: y};
        str.get_string('xofy', 'mod_assign', param).done(function(s) {
            if (updateNumber == this._lastXofYUpdate) {
                this._region.find('[data-region="user-count-summary"]').text(s);
            }
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Rebuild the x of y string.
     *
     * @private
     * @method _refreshCount
     */
    GradingNavigation.prototype._refreshCount = function() {
        var select = this._region.find('[data-action=change-user]');
        var userid = select.attr('data-selected');
        var i = 0;
        var currentIndex = 0;

        if (isNaN(userid) || userid <= 0) {
            this._region.find('[data-region="user-count"]').hide();
        } else {
            this._region.find('[data-region="user-count"]').show();

            for (i = 0; i < this._filteredUsers.length; i++) {
                if (this._filteredUsers[i].id == userid) {
                    currentIndex = i;
                    break;
                }
            }
            var count = this._filteredUsers.length;
            if (count) {
                currentIndex += 1;
            }
            this._setCountString(currentIndex, count);
            // Update window URL
            if (currentIndex > 0) {
                var url = new URL(window.location);
                if (parseInt(url.searchParams.get('blindid')) > 0) {
                    var newid = this._filteredUsers[currentIndex - 1].recordid;
                    url.searchParams.set('blindid', newid);
                } else {
                    url.searchParams.set('userid', userid);
                }
                // We do this so a browser refresh will return to the same user.
                window.history.replaceState({}, "", url);
            }
        }
    };

    /**
     * Respond to a user-changed event by updating the selector.
     *
     * @private
     * @method _refreshSelector
     * @param {Event} event
     * @param {String} userid
     */
    GradingNavigation.prototype._refreshSelector = function(event, userid) {
        var select = this._region.find('[data-action=change-user]');
        userid = parseInt(userid, 10);

        if (!isNaN(userid) && userid > 0) {
            select.attr('data-selected', userid);
        }
        this._refreshCount();
    };

    /**
     * Trigger the next user event depending on the number of filtered users
     *
     * @private
     * @method _triggerNextUserEvent
     */
    GradingNavigation.prototype._triggerNextUserEvent = function() {
        if (this._filteredUsers.length > 1) {
            $(document).trigger('next-user', {nextUserId: null, nextUser: true});
        } else {
            $(document).trigger('next-user', {nextUser: false});
        }
    };

    /**
     * Change to a different user in the grading list.
     *
     * @private
     * @method _handleChangeUser
     */
    GradingNavigation.prototype._handleChangeUser = function() {
        var select = this._region.find('[data-action=change-user]');
        var userid = parseInt(select.val(), 10);

        if (this._isLoading) {
            return;
        }
        if (checker.checkFormForChanges('[data-region="grade-panel"] .gradeform')) {
            // Form has changes, so we need to confirm before switching users.
            str.get_strings([
                {key: 'unsavedchanges', component: 'mod_assign'},
                {key: 'unsavedchangesquestion', component: 'mod_assign'},
                {key: 'saveandcontinue', component: 'mod_assign'},
                {key: 'cancel', component: 'core'},
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', userid);
                });
            });
        } else {
            if (!isNaN(userid) && userid > 0) {
                select.attr('data-selected', userid);

                $(document).trigger('user-changed', userid);
            }
        }
    };

    return GradingNavigation;
});
