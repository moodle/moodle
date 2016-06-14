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
 * @package    mod_assign
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
     * @class GradingNavigation
     * @param {String} selector The selector for the page region containing the user navigation.
     */
    var GradingNavigation = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);
        this._filters = [];
        this._users = [];
        this._filteredUsers = [];

        // Get the current user list from a webservice.
        this._loadAllUsers();

        // Attach listeners to the select and arrow buttons.

        this._region.find('[data-action="previous-user"]').on('click', this._handlePreviousUser.bind(this));
        this._region.find('[data-action="next-user"]').on('click', this._handleNextUser.bind(this));
        this._region.find('[data-action="change-user"]').on('change', this._handleChangeUser.bind(this));
        this._region.find('[data-region="user-filters"]').on('click', this._toggleExpandFilters.bind(this));

        $(document).on('user-changed', this._refreshSelector.bind(this));

        // Position the configure filters panel under the link that expands it.
        var toggleLink = this._region.find('[data-region="user-filters"]');
        var configPanel = $(document.getElementById(toggleLink.attr('aria-controls')));

        configPanel.on('change', '[type="checkbox"]', this._filterChanged.bind(this));

        var userid = $('[data-region="grading-navigation-panel"]').data('first-userid');
        if (userid) {
            this._selectUserById(userid);
        }

        str.get_string('changeuser', 'mod_assign').done(function(s) {
                autocomplete.enhance('[data-action=change-user]', false, 'mod_assign/participant_selector', s);
            }.bind(this)
        ).fail(notification.exception);

        // We do not allow navigation while ajax requests are pending.

        $(document).bind("start-loading-user", function(){
            this._isLoading = true;
        }.bind(this));
        $(document).bind("finish-loading-user", function(){
            this._isLoading = false;
        }.bind(this));
    };

    /** @type {Boolean} Boolean tracking active ajax requests. */
    GradingNavigation.prototype._isLoading = false;

    /** @type {String} Selector for the page region containing the user navigation. */
    GradingNavigation.prototype._regionSelector = null;

    /** @type {Array} The list of active filter keys */
    GradingNavigation.prototype._filters = null;

    /** @type {Array} The list of users */
    GradingNavigation.prototype._users = null;

    /** @type {JQuery} JQuery node for the page region containing the user navigation. */
    GradingNavigation.prototype._region = null;

    /**
     * Load the list of all users for this assignment.
     *
     * @private
     * @method _loadAllUsers
     */
    GradingNavigation.prototype._loadAllUsers = function() {
        var select = this._region.find('[data-action=change-user]');
        var assignmentid = select.attr('data-assignmentid');
        var groupid = select.attr('data-groupid');

        ajax.call([{
            methodname: 'mod_assign_list_participants',
            args: { assignid: assignmentid, groupid: groupid, filter: '', onlyids: true },
            done: this._usersLoaded.bind(this),
            fail: notification.exception
        }]);
    };

    /**
     * Call back to rebuild the user selector and x of y info when the user list is updated.
     *
     * @private
     * @method _usersLoaded
     * @param {Array} users
     */
    GradingNavigation.prototype._usersLoaded = function(users) {
        this._filteredUsers = this._users = users;
        if (this._users.length) {
            // Position the configure filters panel under the link that expands it.
            var toggleLink = this._region.find('[data-region="user-filters"]');
            var configPanel = $(document.getElementById(toggleLink.attr('aria-controls')));

            configPanel.find('[type="checkbox"]').trigger('change');
        } else {
            this._selectNoUser();
        }
    };

    /**
     * Close the configure filters panel if a click is detected outside of it.
     *
     * @private
     * @method _checkClickOutsideConfigureFilters
     * @param {Event}
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
     * Turn a filter on or off.
     *
     * @private
     * @method _filterChanged
     * @param {Event}
     */
    GradingNavigation.prototype._filterChanged = function(event) {
        var name = $(event.target).attr('name');
        var key = name.split('_').pop();
        var enabled = $(event.target).prop('checked');

        if (enabled) {
            if (this._filters.indexOf(key) == -1) {
                this._filters[this._filters.length] = key;
            }
        } else {
            var index = this._filters.indexOf(key);
            if (index != -1) {
                this._filters.splice(index, 1);
            }
        }

        // Update the active filter string.
        var filterlist = [];
        this._region.find('[data-region="configure-filters"]').find('[type="checkbox"]').each(function(idx, ele) {
            if ($(ele).prop('checked')) {
                filterlist[filterlist.length] = $(ele).closest('label').text();
            }
        }.bind(this));
        if (filterlist.length) {
            this._region.find('[data-region="user-filters"] span').text(filterlist.join(', '));
        } else {
            str.get_string('nofilters', 'mod_assign').done(function(s) {
                this._region.find('[data-region="user-filters"] span').text(s);
            }.bind(this)).fail(notification.exception);
        }

        // Filter the options in the select box that do not match the current filters.

        var select = this._region.find('[data-action=change-user]');
        var userid = select.attr('data-selected');
        var foundIndex = 0;

        this._filteredUsers = [];

        $.each(this._users, function(index, user) {
            var show = true;
            $.each(this._filters, function(filterindex, filter) {
                if (filter == "submitted") {
                    if (user.submitted == "0") {
                        show = false;
                    }
                } else if (filter == "notsubmitted") {
                    if (user.submitted == "1") {
                        show = false;
                    }
                } else if (filter == "requiregrading") {
                    if (user.requiregrading == "0") {
                        show = false;
                    }
                }
            }.bind(this));

            if (show) {
                this._filteredUsers[this._filteredUsers.length] = user;
                if (userid == user.id) {
                    foundIndex = index;
                }
            }
        }.bind(this));

        if (this._filteredUsers.length) {
            this._selectUserById(this._filteredUsers[foundIndex].id);
        } else {
            this._selectNoUser();
        }
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
                { key: 'unsavedchanges', component: 'mod_assign' },
                { key: 'unsavedchangesquestion', component: 'mod_assign' },
                { key: 'saveandcontinue', component: 'mod_assign' },
                { key: 'cancel', component: 'core' },
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', -1);
                });
            }.bind(this));
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
                { key: 'unsavedchanges', component: 'mod_assign' },
                { key: 'unsavedchangesquestion', component: 'mod_assign' },
                { key: 'saveandcontinue', component: 'mod_assign' },
                { key: 'cancel', component: 'core' },
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', useridnumber);
                });
            }.bind(this));
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
     * @param {Event}
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
        var i = 0, currentIndex = 0;

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
     */
    GradingNavigation.prototype._handleNextUser = function(e) {
        e.preventDefault();
        var select = this._region.find('[data-action=change-user]');
        var currentUserId = select.attr('data-selected');
        var i = 0, currentIndex = 0;

        for (i = 0; i < this._filteredUsers.length; i++) {
            if (this._filteredUsers[i].id == currentUserId) {
                currentIndex = i;
                break;
            }
        }

        var count = this._filteredUsers.length;
        var newIndex = (currentIndex + 1) % count;

        if (count) {
            this._selectUserById(this._filteredUsers[newIndex].id);
        }
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
            var param = { x: currentIndex, y: count };

            str.get_string('xofy', 'mod_assign', param).done(function(s) {
                this._region.find('[data-region="user-count-summary"]').text(s);
            }.bind(this)).fail(notification.exception);
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
     * Change to a different user in the grading list.
     *
     * @private
     * @method _handleChangeUser
     * @param {Event}
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
                { key: 'unsavedchanges', component: 'mod_assign' },
                { key: 'unsavedchangesquestion', component: 'mod_assign' },
                { key: 'saveandcontinue', component: 'mod_assign' },
                { key: 'cancel', component: 'core' },
            ]).done(function(strs) {
                notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                    $(document).trigger('save-changes', userid);
                });
            }.bind(this));
        } else {
            if (!isNaN(userid) && userid > 0) {
                select.attr('data-selected', userid);

                $(document).trigger('user-changed', userid);
            }
        }
    };

    return GradingNavigation;
});
