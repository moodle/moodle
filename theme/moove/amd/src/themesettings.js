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
 * Theme settings js logic.
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'theme_moove/themesettings_modal'],
    function($, ModalFactory, ThemeSettingsModal) {

        var SELECTORS = {
            TOGGLE_REGION: '#themesettings-control'
        };

        /**
         * Constructor for the ThemeSettings.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ThemeSettings = function() {
            this.registerEventListeners();
        };

        /**
         * Open / close the blocks drawer.
         *
         * @method toggleThemeSettings
         * @param {Event} e
         */
        ThemeSettings.prototype.openThemeSettings = function() {
            ModalFactory.create({
                type: ThemeSettingsModal.TYPE
            })
            .then(function(modal) {
                modal.show();
            });
        };

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ThemeSettings.prototype.registerEventListeners = function() {
            $(SELECTORS.TOGGLE_REGION).click(function(e) {
                this.openThemeSettings(e);
                e.preventDefault();
            }.bind(this));
        };

        return {
            'init': function() {
                return new ThemeSettings();
            }
        };
    }
);
