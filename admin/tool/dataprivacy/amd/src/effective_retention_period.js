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
 * Module to update the displayed retention period.
 *
 * @module     tool_dataprivacy/effective_retention_period
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'],
    function($) {

        var SELECTORS = {
            PURPOSE_SELECT: '#id_purposeid',
            RETENTION_FIELD: '#fitem_id_retention_current [data-fieldtype=static]',
        };

        /**
         * Constructor for the retention period display.
         *
         * @param {Array} purposeRetentionPeriods Associative array of purposeids with effective retention period at this context
         */
        var EffectiveRetentionPeriod = function(purposeRetentionPeriods) {
            this.purposeRetentionPeriods = purposeRetentionPeriods;
            this.registerEventListeners();
        };

        /**
         * Removes the current 'change' listeners.
         *
         * Useful when a new form is loaded.
         */
        var removeListeners = function() {
            $(SELECTORS.PURPOSE_SELECT).off('change');
        };

        /**
         * @var {Array} purposeRetentionPeriods
         * @private
         */
        EffectiveRetentionPeriod.prototype.purposeRetentionPeriods = [];

        /**
         * Add purpose change listeners.
         *
         * @method registerEventListeners
         */
        EffectiveRetentionPeriod.prototype.registerEventListeners = function() {

            $(SELECTORS.PURPOSE_SELECT).on('change', function(ev) {
                var selected = $(ev.currentTarget).val();
                var selectedPurpose = this.purposeRetentionPeriods[selected];
                $(SELECTORS.RETENTION_FIELD).text(selectedPurpose);
            }.bind(this));
        };

        return /** @alias module:tool_dataprivacy/effective_retention_period */ {
            init: function(purposeRetentionPeriods) {
                // Remove previously attached listeners.
                removeListeners();
                return new EffectiveRetentionPeriod(purposeRetentionPeriods);
            }
        };
    }
);

