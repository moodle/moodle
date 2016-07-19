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
 * Controls the general settings on the message preferences page
 *
 * @module     core_message/preferences_general_settings_controller
 * @class      preferences_processors_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core_message/user_preference', 'core_message/disable_all_preference'],
        function($, UserPreference, DisableAllPreference) {

    var SELECTORS = {
        SETTING: '[data-preference-key]',
    };

    /**
     * Constructor for the GeneralSettingsController.
     *
     * @param element jQuery object root element of the processor
     * @return object GeneralSettingsController
     */
    var GeneralSettingsController = function(element) {
        this.root = $(element);
        this.userId = this.root.attr('data-user-id');

        this.root.on('change', function(e) {
            var element = $(e.target).closest(SELECTORS.SETTING);
            var setting = this.createFromElement(element);
            setting.save();
        }.bind(this));
    };

    /**
     * Factory method to return the correct UserPreference instance
     * for the given jQuery element.
     *
     * @method save
     * @param object jQuery element
     * @return object UserPreference
     */
    GeneralSettingsController.prototype.createFromElement = function(element) {
        element = $(element);

        if (element.attr('data-preference-key') === "disableall") {
            return new DisableAllPreference(element, this.userId);
        } else {
            return new UserPreference(element, this.userId);
        }
    };

    return GeneralSettingsController;
});
