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
 * A javascript module to handle question ajax actions.
 *
 * @module     core_question/qbankmanager
 * @class      qbankmanager
 * @package    core_question
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/pubsub', 'core/checkbox-toggleall'], function($, PubSub, ToggleAll) {

    var registerListeners = function() {
        PubSub.subscribe(ToggleAll.events.checkboxToggled, toggleButtonStates);
    };

    var toggleButtonStates = function(data) {
        if ('qbank' !== data.toggleGroupName) {
            return;
        }

        setButtonState(data.anyChecked);
    };

    var setButtonState = function(state) {
        var buttons = $('.modulespecificbuttonscontainer').find('input, select, link');
        buttons.attr('disabled', !state);
    };

    return {
        /**
         * Set up the Question Bank Manager.
         *
         * @method init
         */
        init: function() {
            setButtonState(false);
            registerListeners();
        },
    };
});
