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
 * A javascript module that handles the change of the user's visibility in the
 * online users block.
 *
 * @module     block_online_users/change_user_visibility
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/str', 'core/notification'],
        function($, Ajax, Str, Notification) {

    /**
     * Selectors.
     *
     * @access private
     * @type {Object}
     */
    var SELECTORS = {
        CHANGE_VISIBILITY_LINK: '#change-user-visibility',
        CHANGE_VISIBILITY_ICON: '#change-user-visibility .icon'
    };

    /**
     * Change user visibility in the online users block.
     *
     * @method changeVisibility
     * @param {String} action
     * @param {String} userid
     * @private
     */
    var changeVisibility = function(action, userid) {

        var value = action == "show" ? 1 : 0;
        var preferences = [{
            'name': 'block_online_users_uservisibility',
            'value': value,
            'userid': userid
        }];

        var request = {
            methodname: 'core_user_set_user_preferences',
            args: {
                preferences: preferences
            }
        };
        Ajax.call([request])[0].then(function(data) {
            if (data.saved) {
                var newAction = oppositeAction(action);
                changeVisibilityLinkAttr(newAction);
                changeVisibilityIconAttr(newAction);
            }
            return;
        }).catch(Notification.exception);
    };

    /**
     * Get the opposite action.
     *
     * @method oppositeAction
     * @param {String} action
     * @return {String}
     * @private
     */
    var oppositeAction = function(action) {
        return action == 'show' ? 'hide' : 'show';
    };

    /**
     * Change the attribute values of the user visibility link in the online users block.
     *
     * @method changeVisibilityLinkAttr
     * @param {String} action
     * @private
     */
    var changeVisibilityLinkAttr = function(action) {
        getTitle(action).then(function(title) {
            $(SELECTORS.CHANGE_VISIBILITY_LINK).attr({
                'data-action': action,
                'title': title
            });
            return;
        }).catch(Notification.exception);
    };

    /**
     * Change the attribute values of the user visibility icon in the online users block.
     *
     * @method changeVisibilityIconAttr
     * @param {String} action
     * @private
     */
    var changeVisibilityIconAttr = function(action) {
        var icon = $(SELECTORS.CHANGE_VISIBILITY_ICON);
        getTitle(action).then(function(title) {
            // Add the proper title to the icon.
            $(icon).attr({
                'title': title,
                'aria-label': title
            });
            // If the icon is an image.
            if (icon.is("img")) {
                $(icon).attr({
                    'src': M.util.image_url('t/' + action),
                    'alt': title
                });
            } else {
                // Add the new icon class and remove the old one.
                $(icon).addClass(getIconClass(action));
                $(icon).removeClass(getIconClass(oppositeAction(action)));
            }
            return;
        }).catch(Notification.exception);
    };

    /**
     * Get the proper class for the user visibility icon in the online users block.
     *
     * @method getIconClass
     * @param {String} action
     * @return {String}
     * @private
     */
    var getIconClass = function(action) {
        return action == 'show' ? 'fa-eye-slash' : 'fa-eye';
    };

    /**
     * Get the title description of the user visibility link in the online users block.
     *
     * @method getTitle
     * @param {String} action
     * @return {object} jQuery promise
     * @private
     */
    var getTitle = function(action) {
        return Str.get_string('online_status:' + action, 'block_online_users');
    };

    return {
        // Public variables and functions.
        /**
         * Initialise change user visibility function.
         *
         * @method init
         */
        init: function() {
            $(SELECTORS.CHANGE_VISIBILITY_LINK).on('click', function(e) {
                e.preventDefault();
                var action = ($(this).attr('data-action'));
                var userid = ($(this).attr('data-userid'));
                changeVisibility(action, userid);
            });
        }
    };
});
