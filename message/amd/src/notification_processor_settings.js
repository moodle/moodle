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
 * @since      3.2
 */
define(['jquery', 'core/fragment', 'core/templates', 'core/str', 'tool_lp/dialogue'], function($, Fragment, Templates, Str, Dialogue) {
    /**
     * Constructor for the notification processor settings.
     *
     * @param element jQuery object root element of the processor
     * @return object NotificationProcessorSettings
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
                new Dialogue(string, html, function() {
                    Templates.runTemplateJS(js);
                });
            });
        });
    }

    return NotificationProcessorSettings;
});
