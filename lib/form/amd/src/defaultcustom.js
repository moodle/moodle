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
 * Functionality for the form element defaultcustom
 *
 * @module     core_form/defaultcustom
 * @package    core_form
 * @class      defaultcustom
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
define(['jquery'], function($) {
    var onChangeSelect = function(event) {
        var element = $(event.target),
            defaultvalue = JSON.parse(element.attr('data-defaultvalue')),
            customvalue = JSON.parse(element.attr('data-customvalue')),
            type = element.attr('data-type'),
            form = element.closest('form'),
            elementName = element.attr('name').replace(/\[customize\]$/, '[value]'),
            newvalue = element.prop('checked') ? customvalue : defaultvalue;

        if (type === 'text') {
            form.find('[name="' + elementName + '"]').val(newvalue);
        } else if (type === 'date_selector') {
            form.find('[name="' + elementName + '[day]"]').val(newvalue.day);
            form.find('[name="' + elementName + '[month]"]').val(newvalue.month);
            form.find('[name="' + elementName + '[year]"]').val(newvalue.year);
        } else if (type === 'date_time_selector') {
            form.find('[name="' + elementName + '[day]"]').val(newvalue.day);
            form.find('[name="' + elementName + '[month]"]').val(newvalue.month);
            form.find('[name="' + elementName + '[year]"]').val(newvalue.year);
            form.find('[name="' + elementName + '[hour]"]').val(newvalue.hour);
            form.find('[name="' + elementName + '[minute]"]').val(newvalue.minute);
        }
    };

    var selector = 'input[data-defaultcustom=true]';
    $('body').on('change', selector, onChangeSelect);
});
