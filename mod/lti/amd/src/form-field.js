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
 * A module that enables the setting of form field values on the client side.
 *
 * @module     mod_lti/form-field
 * @class      form-field
 * @package    mod_lti
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery'],
    function($) {
        /**
         * Form field class.
         *
         * @param {string} name Field name.
         * @param {number} type The field type.
         * @param {boolean} resetIfUndefined Flag to reset the field to the default value if undefined in the return data.
         * @param {string|number|boolean} defaultValue The default value to use for the field.
         * @constructor
         */
        var FormField = function(name, type, resetIfUndefined, defaultValue) {
            this.name = name;
            this.id = 'id_' + this.name;
            this.selector = '#' + this.id;
            this.type = type;
            this.resetIfUndefined = resetIfUndefined;
            this.defaultValue = defaultValue;
        };

        /**
         * Form field types.
         *
         * @type {{TEXT: number, SELECT: number, CHECKBOX: number, EDITOR: number}}
         */
        FormField.TYPES = {
            TEXT: 1,
            SELECT: 2,
            CHECKBOX: 3,
            EDITOR: 4
        };

        /**
         * Sets the values for a form field.
         *
         * @param {string|boolean|number} value The value to be set into the field.
         */
        FormField.prototype.setFieldValue = function(value) {
            if (value === null) {
                if (this.resetIfUndefined) {
                    value = this.defaultValue;
                } else {
                    // No need set the field value if value is null and there's no need to reset the field.
                    return;
                }
            }

            switch (this.type) {
                case FormField.TYPES.CHECKBOX:
                    if (value) {
                        $(this.selector).prop('checked', true);
                    } else {
                        $(this.selector).prop('checked', false);
                    }
                    break;
                case FormField.TYPES.EDITOR:
                    if ($.type(value.text) !== 'undefined') {
                        /* global tinyMCE:false */

                        // Set text in editor's editable content, if applicable.
                        // Check if it is an Atto editor.
                        var attoEditor = $(this.selector + 'editable');
                        if (attoEditor.length) {
                            attoEditor.html(value.text);
                        } else if (typeof tinyMCE !== 'undefined') {
                            // If the editor is not Atto, try to fallback to TinyMCE.
                            tinyMCE.execInstanceCommand(this.id, 'mceInsertContent', false, value.text);
                        }

                        // Set text to actual editor text area.
                        $(this.selector).val(value.text);
                    }
                    break;
                default:
                    $(this.selector).val(value);
                    break;
            }
        };

        return FormField;
    }
);
