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
 * Controls the message preference page.
 *
 * @module     core_message/notification_preference
 * @class      notification_preference
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification',
        'core_message/preferences_notifications_list_controller', 'core/custom_interaction_events'],
        function($, Ajax, Notification, ListController, CustomEvents) {

    var SELECTORS = {
        PREFERENCES_CONTAINER: '.preferences-container',
        BLOCK_NON_CONTACTS: '.block-non-contacts-container [data-block-non-contacts]',
        BLOCK_NON_CONTACTS_CONTAINER: '.block-non-contacts-container',
    };

    /**
     * Constructor for the MessagePreferences.
     *
     * @return object MessagePreferences
     */
    var MessagePreferences = function() {
        new ListController($(SELECTORS.PREFERENCES_CONTAINER));
        var blockContactsElement = $(SELECTORS.BLOCK_NON_CONTACTS);

        CustomEvents.define(blockContactsElement, [
            CustomEvents.events.activate
        ]);

        blockContactsElement.on(CustomEvents.events.activate, function(e) {
            this.saveBlockNonContactsStatus();
        }.bind(this));
    };

    /**
     * Update the block messages from non-contacts user preference in the DOM and
     * send a request to update on the server.
     *
     * @method saveBlockNonContactsStatus
     */
    MessagePreferences.prototype.saveBlockNonContactsStatus = function() {
        var checkbox = $(SELECTORS.BLOCK_NON_CONTACTS);
        var container = $(SELECTORS.BLOCK_NON_CONTACTS_CONTAINER);
        var ischecked = checkbox.prop('checked');

        if (container.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        container.addClass('loading');

        var request = {
            methodname: 'core_user_update_user',
            args: {
                user: {
                    preferences: [
                        {
                            type: checkbox.attr('data-preference-key'),
                            value: ischecked ? 1 : 0,
                        }
                    ]
                }
            }
        };

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .always(function() {
                container.removeClass('loading');
            });
    };

    return MessagePreferences;
});
