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
define(['jquery', 'core/str', 'core/notification'], function($, str, notification) {

    return {
        /**
         * A reference to the header checkbox.
         *
         * @property _strings
         * @type Node
         * @private
         */
        _strings: null,

        /**
         * A reference to the add to quiz button.
         *
         * @property _buttons
         * @type Node
         * @private
         */
        _buttons: null,

        /**
         * Set up the Question Bank Manager.
         *
         * @method init
         */
        init: function() {
            // Find the header checkbox, and set the initial values.
            var header = $('#qbheadercheckbox');
            if (header.length == 0) {
                return;
            }
            var self = this;
            str.get_strings([
                {key: 'selectall', component: 'moodle'},
                {key: 'deselectall', component: 'moodle'},
            ]).then(function(strings) {
                self._strings = strings;
                header.attr({
                    disabled: false,
                    checked: self._getSizeChecked() != 0,
                    title: strings[0]
                });
                header.click(self, self._headerClick);

                self._buttons = $(".modulespecificbuttonscontainer input, .modulespecificbuttonscontainer select," +
                    " .modulespecificbuttonscontainer link, .modulespecificbuttonscontainer link");

                self._buttons.attr('disabled', self._getSizeChecked() == 0);

                if (self._buttons.length > 0) {
                    $('.categoryquestionscontainer')
                        .delegate('td.checkbox input[type="checkbox"]', 'change', self, self._questionClick);
                }
                return;
            }).fail(notification.exception);
        },

        /**
         * Handle toggling of the header checkbox.
         *
         * @method _headerClick
         * @param {Event} event of element.
         * @private
         */
        _headerClick: function(event) {
            var self = event.data;
            var header = $('#qbheadercheckbox');
            var isCheckedHeader = header.is(':checked');
            var indexStringTitle = isCheckedHeader ? 1 : 0;

            $("#categoryquestions tbody [type=checkbox]").prop("checked", isCheckedHeader);
            self._buttons.attr('disabled', self._getSizeChecked() === 0);
            header.attr('title', self._strings[indexStringTitle]);
        },

        /**
         * Handle toggling of a question checkbox.
         *
         * @method _questionClick
         * @param {Event} event of element.
         * @private
         */
        _questionClick: function(event) {
            var self = event.data;
            var header = $('#qbheadercheckbox');
            var areChecked = self._getSizeChecked();
            var lengthCheckbox = $("#categoryquestions tbody [type=checkbox]").length;
            var ischeckboxHeader = (areChecked != 0) && areChecked == lengthCheckbox;

            header.prop('checked', ischeckboxHeader);
            self._buttons.attr('disabled', (areChecked === 0));
        },
        /**
         * Get size all row checked of table.
         * @method _getSizeChecked
         * @return {Number}
         * @private
         */
        _getSizeChecked: function() {
            return $('#categoryquestions td.checkbox input[type="checkbox"]:checked').length;
        }
    };
});
