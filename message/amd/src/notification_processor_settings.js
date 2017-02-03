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
 * Load the settings for a message processor.
 *
 * @module     core_message/notification_processor_settings
 * @class      notification_processor_settings
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/fragment', 'core/templates', 'core/str', 'tool_lp/dialogue'],
        function($, Ajax, Notification, Fragment, Templates, Str, Dialogue) {

    var SELECTORS = {
        PROCESSOR: '[data-processor-name]',
        PREFERENCE_ROW: '[data-region="preference-row"]',
    };

    /**
     * Constructor for the notification processor settings.
     *
     * @param {object} element jQuery object root element of the processor
     */
    var NotificationProcessorSettings = function(element) {
        this.root = $(element);
        this.name = this.root.attr('data-name');
        this.userId = this.root.attr('data-user-id');
        this.contextId = this.root.attr('data-context-id');
    };

    /**
     * Show the notification processor settings dialogue.
     *
     * @method show
     */
    NotificationProcessorSettings.prototype.show = function() {
        Fragment.loadFragment('message', 'processor_settings', this.contextId, {
            userid: this.userId,
            type: this.name,
        })
        .done(function(html, js) {
            Str.get_string('processorsettings', 'message').done(function(string) {
                var dialogue = new Dialogue(
                    string,
                    html,
                    function() {
                        Templates.runTemplateJS(js);
                    },
                    function() {
                        // Removed dialogue from the DOM after close.
                        dialogue.close();
                    }
                );

                $(document).on('mpp:formsubmitted', function() {
                    dialogue.close();
                    this.updateConfiguredStatus();
                }.bind(this));

                $(document).on('mpp:formcancelled', function() {
                    dialogue.close();
                });
            }.bind(this));
        }.bind(this));
    };

    /**
     * Checks if the processor has been configured. If so then remove the unconfigured
     * status from the interface.
     *
     * @method updateConfiguredStatus
     * @return {Promise|boolean}
     */
    NotificationProcessorSettings.prototype.updateConfiguredStatus = function() {
        var processorHeader = this.root.closest(SELECTORS.PROCESSOR);

        if (!processorHeader.hasClass('unconfigured')) {
            return false;
        }

        var processorName = processorHeader.attr('data-processor-name');
        var request = {
            methodname: 'core_message_get_message_processor',
            args: {
                name: processorName,
                userid: this.userId,
            },
        };

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .done(function(result) {
                // Check if the user has figured configuring the processor.
                if (result.userconfigured) {
                    // If they have then we can enable the settings.
                    var notifications = $(SELECTORS.PREFERENCE_ROW + ' [data-processor-name="' + processorName + '"]');
                    processorHeader.removeClass('unconfigured');
                    notifications.removeClass('disabled');
                }
            });
    };

    return NotificationProcessorSettings;
});
