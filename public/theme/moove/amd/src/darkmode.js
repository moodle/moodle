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
 * Contain the logic for dark mode.
 *
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/ajax'], function($, Str, Ajax) {

    var SELECTORS = {
        TRIGGER: '#toggle-darkmode-input',
        MODECLASS: 'moove-darkmode'
    };

    var ISACTIVATED = false;

    var DarkMode = function() {
        this.registerEventListeners();

        if (document.body.classList.contains(SELECTORS.MODECLASS)) {
            document.body.setAttribute('data-bs-theme', 'dark');

            ISACTIVATED = true;
        } else {
            document.body.setAttribute('data-bs-theme', 'light');
        }

        this.setTriggerStatus();
    };

    DarkMode.prototype.registerEventListeners = function() {
        $(SELECTORS.TRIGGER).click(function() {
            this.toggleDarkMode();
        }.bind(this));
    };

    DarkMode.prototype.toggleDarkMode = function() {
        $('body').toggleClass(SELECTORS.MODECLASS);

        if (ISACTIVATED) {
            document.body.setAttribute('data-bs-theme', 'light');
        } else {
            document.body.setAttribute('data-bs-theme', 'dark');
        }

        ISACTIVATED = !ISACTIVATED;

        var request = Ajax.call([{
            methodname: 'theme_moove_toggledarkmode',
            args: {}
        }]);

        request[0].done(function() {
            this.setTriggerStatus();
        }.bind(this));
    };

    DarkMode.prototype.setTriggerStatus = function() {
        if (ISACTIVATED) {
            return $(SELECTORS.TRIGGER).attr('checked', 'checked');
        }

        return $(SELECTORS.TRIGGER).removeAttr('checked');
    };

    return {
        'init': function() {
            return new DarkMode();
        }
    };
});