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
 * Customized output for the attempt administration chart data.
 *
 * @module     mod_adaptivequiz/attempt_administration_chart_output_htmltable
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/chart_output_htmltable',
    'mod_adaptivequiz/attempt_administration_chart_dataset_config'
], function (
    $,
    OutputTable,
    DatasetConfig
) {

    /**
     * Output for the attempt administration chart data.
     *
     * @class
     * @extends {module:core/chart_output_htmltable}
     */
    function AttemptAdministrationChartOutputTable() {
        OutputTable.apply(this, arguments);
    }
    AttemptAdministrationChartOutputTable.prototype = Object.create(OutputTable.prototype);

    /**
     * Overrides building the table.
     *
     * @override
     * @protected
     * @return {Object} Modified table node.
     */
    AttemptAdministrationChartOutputTable.prototype._makeTable = function() {
        let tbl = OutputTable.prototype._makeTable.apply(this, arguments);

        // Remove columns with standard error min/max.
        const dataIndicesToRemove = [DatasetConfig.indices.STANDARD_ERROR_MAX, DatasetConfig.indices.STANDARD_ERROR_MIN];

        const selectorsToRemove = dataIndicesToRemove.flatMap(
            (dataIndexToRemove) => [`th:nth-child(${dataIndexToRemove + 2})`, `td:nth-child(${dataIndexToRemove + 2})`]
        );

        tbl.find(selectorsToRemove.join()).remove();

        return tbl;
    };

    return AttemptAdministrationChartOutputTable;
});
