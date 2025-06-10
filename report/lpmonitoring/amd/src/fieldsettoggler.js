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
 * Fieldset toggler.
 *
 * @module     report_lpmonitoring/fieldsettoggler
 * @author     Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @copyright  2016 Université de Montréal
 */

define(['jquery'],
    function($) {

        /**
         * Fieldset toggler object.
         *
         * @param {String} containerSelector container selector.
         */
        var FieldsetToggler = function(containerSelector) {
            var container = containerSelector || '.competencyreport';
            // Collapse block panels.
            $(container).on('click', '.fheader', function(event) {
                event.preventDefault();
                var f = $(this).closest(".collapsible"),
                h = $(this);
                f.toggleClass("collapsed");
                if (h.attr('aria-expanded') === 'true') {
                    h.attr('aria-expanded', 'false');
                } else {
                    h.attr('aria-expanded', 'true');
                }
            });
        };

        return {
            /**
             * Main initialisation.
             *
             * @param {String} containerSelector container selector.
             * @return {FieldsetToggler} A new instance of FieldsetToggler.
             * @method init
             */
            init: function(containerSelector) {
                return new FieldsetToggler(containerSelector);
            }
        };

    });
