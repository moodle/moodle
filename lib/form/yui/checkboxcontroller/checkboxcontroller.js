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
 * Group of date and time input element
 *
 * Contains class for a group of elements used to input a date and time.
 *
 * @package   core_form
 * @copyright 2012 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
YUI.add('moodle-form-checkboxcontroller', function(Y) {
    var checkboxcontroller = function() {
        checkboxcontroller.superclass.constructor.apply(this, arguments);
    }

    Y.extend(checkboxcontroller, Y.Base, {
        _controllervaluenode : null,
        _checkboxclass : null,

        /*
         * Initialize script if all params passed.
         *
         * @param object params values passed while initalizing script
         */
        initializer : function(params) {
            if (params && params.checkboxcontroller &&
                params.controllerbutton &&
                params.checkboxclass) {
               // Id of controller node which keeps value in html.
               this._controllervaluenode = '#id_'+params.checkboxcontroller;

               // Checkboxes class name by which checkboxes will be selected
               this._checkboxclass = '.'+params.checkboxclass;

               // Replace submit button with link.
               this.replaceButton('#id_'+params.controllerbutton);
            }
        },

        /**
         * Replace controller button with link and add event.
         *
         * @param string controllerbutton id of the controller button which needs to be replaced
         */
        replaceButton : function(controllerbutton) {
            var controllerbutton = Y.one(controllerbutton);
            var linkname = controllerbutton.get('value');
            // Link node which will replace controller button
            var link = Y.Node.create('<a href="#">'+linkname+'</a>');

            // Attach onclick event to link
            link.on('click', this.onClick, this);
            // Hide controller button
            controllerbutton.hide();
            // Insert link node
            controllerbutton.get('parentNode').insert(link, controllerbutton.get('lastNode'));
        },

        /**
         * Onclick event will be handled.
         *
         * @param Event e
         */
        onClick : function(e) {
            e.preventDefault();
            this.switchGroupState();
        },

        /**
         * Toggles checkboxes status belong to a group
         */
        switchGroupState : function() {
            if (this._checkboxclass) {
                // Value which should be set on checkboxes
                var newvalue = '';
                // Get controller node which keeps value
                var controllervaluenode = Y.one(this._controllervaluenode);
                // Get all checkboxes with
                var checkboxes = Y.all(this._checkboxclass);

                // Toggle checkboxes in group, depending on conroller value
                if (controllervaluenode.get('value') == 1) {
                    controllervaluenode.set('value', '0');
                } else {
                    controllervaluenode.set('value', '1');
                    newvalue = 'checked';
                }
                checkboxes.each(function(checkbox){
                    if (!checkbox.get('disabled')) {
                        checkbox.set('checked', newvalue);
                    }
                });
            }
        }
    });

    M.form = M.form || {};

    M.form.checkboxcontroller = function(params) {
        return new checkboxcontroller(params);
    }
}, '@VERSION@', {requires:['base', 'node']});
