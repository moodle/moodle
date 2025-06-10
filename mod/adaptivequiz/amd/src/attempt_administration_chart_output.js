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
 * Customized output for the attempt administration chart.
 *
 * The class overrides some definitions from core/chart_output_chartjs for custom output.
 *
 * @module     mod_adaptivequiz/attempt_administration_chart_output
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/chart_output_chartjs',
    'core/chartjs',
    'mod_adaptivequiz/attempt_administration_chart_dataset_config'
], function(
    Output,
    Chartjs,
    DatasetConfig
) {

    /**
     * Used to set the color as a hex code to avoid inconsistencies from Chartjs when displaying the color with opacity specified.
     */
    const STANDARD_ERROR_LABEL_COLOR = '#ffcce0';

    /**
     * A filter callback for tooltip items.
     *
     * @param {Object} tooltipItem
     * @return {Boolean}
     */
    const tooltipItemsFilter = function (tooltipItem) {
        return !(tooltipItem.datasetIndex === DatasetConfig.indices.STANDARD_ERROR_MAX
            || tooltipItem.datasetIndex === DatasetConfig.indices.STANDARD_ERROR_MIN);
    };

    /**
     * A callback to add text after a tooltip item.
     *
     * @param {Object} tooltipItem
     * @return {String}
     */
    const afterTooltipItemLabel = function (tooltipItem) {
        // Show extra text only after the ability measure and administered difficulty items.
        if (!(tooltipItem.datasetIndex === DatasetConfig.indices.ABILITY_MEASURE
            || tooltipItem.datasetIndex === DatasetConfig.indices.ADMINISTERED_DIFFICULTY)) {

            return '';
        }

        // If this is the ability measure item.
        if (tooltipItem.datasetIndex === DatasetConfig.indices.ABILITY_MEASURE) {
            // Reach out to the standard error data.
            const stdErrorSeries = this._chart.getSeries()[DatasetConfig.indices.STANDARD_ERROR_PERCENT];
            const stdErrorValue = stdErrorSeries.getValues()[tooltipItem.dataIndex];

            return `${stdErrorSeries.getLabel()}: ${stdErrorValue}`;
        }

        // The rest case - administered difficulty item.

        // Reach out to the right/wrong data.
        const rightWrongSeries = this._chart.getSeries()[DatasetConfig.indices.CORRECT_WRONG_FLAG];
        const rightWrongValue = rightWrongSeries.getValues()[tooltipItem.dataIndex];

        return `${rightWrongSeries.getLabel()}: ${rightWrongValue}`;
    };

    /**
     * Returns part of the config to set up the legend.
     *
     * @return {Object}
     */
    const legendConfig = function () {
        return {
            labels: {
                generateLabels(chart) {
                    let labels = Chartjs.defaults.plugins.legend.labels.generateLabels(chart);

                    // Store the standard error label for future use.
                    const stdErrorLabelText = labels.find((label) =>
                        label.datasetIndex === DatasetConfig.indices.STANDARD_ERROR_PERCENT).text;

                    // Remove everything hidden and one of the standard error labels.
                    labels = labels.filter((label) =>
                        !label.hidden && label.datasetIndex !== DatasetConfig.indices.STANDARD_ERROR_MIN);

                    // Order by dataset index.
                    labels.sort((label1, label2) => label1.datasetIndex - label2.datasetIndex);

                    // Convert one of the standard error labels to a proper one.
                    const stdErrorMaxIndex = labels.findIndex((label) =>
                        label.datasetIndex === DatasetConfig.indices.STANDARD_ERROR_MAX);
                    labels[stdErrorMaxIndex].text = stdErrorLabelText;
                    labels[stdErrorMaxIndex].strokeStyle = STANDARD_ERROR_LABEL_COLOR;
                    labels[stdErrorMaxIndex].fillStyle = STANDARD_ERROR_LABEL_COLOR;

                    return labels;
                }
            },
            onClick: function () {
                return false;
            }
        };
    };

    /**
     * Output for the attempt administration chart.
     *
     * @class
     * @extends {module:core/chart_output_chartjs}
     */
    function AttemptAdministrationChartOutput() {
        Output.apply(this, arguments);
    }
    AttemptAdministrationChartOutput.prototype = Object.create(Output.prototype);

    /**
     * Overrides config definition to add more custom features.
     *
     * @protected
     * @override
     * @return {Object}
     */
    AttemptAdministrationChartOutput.prototype._makeConfig = function () {
        let config = Output.prototype._makeConfig.apply(this, arguments);

        // Define draw order.
        config.data.datasets[DatasetConfig.indices.ABILITY_MEASURE].order = DatasetConfig.orderWeights.ABILITY_MEASURE;
        config.data.datasets[DatasetConfig.indices.TARGET_DIFFICULTY].order = DatasetConfig.orderWeights.TARGET_DIFFICULTY;
        config.data.datasets[DatasetConfig.indices.ADMINISTERED_DIFFICULTY].order =
            DatasetConfig.orderWeights.ADMINISTERED_DIFFICULTY;
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MAX].order = DatasetConfig.orderWeights.STANDARD_ERROR_BORDER;
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MIN].order = DatasetConfig.orderWeights.STANDARD_ERROR_BORDER;

        // Hide lines and points for standard error min/max datasets.

        // In Chart.js version prior to 4.2.1 points visibility can be controlled with the 'pointRadius' property.
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MAX].pointRadius = 0;
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MIN].pointRadius = 0;
        // For Chart.js since 4.2.1.
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MAX].pointStyle = false;
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MIN].pointStyle = false;

        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MAX].showLine = false;
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_MIN].showLine = false;

        // Hide entire datasets with standard error percentages and right/wrong flags.
        config.data.datasets[DatasetConfig.indices.STANDARD_ERROR_PERCENT].hidden = true;
        config.data.datasets[DatasetConfig.indices.CORRECT_WRONG_FLAG].hidden = true;

        // Tooltip.
        config.options.plugins.tooltip.filter = tooltipItemsFilter;
        config.options.plugins.tooltip.callbacks.afterLabel = afterTooltipItemLabel.bind(this);
        config.options.plugins.tooltip.itemSort = function (tooltip1, tooltip2) {
            return tooltip1.datasetIndex - tooltip2.datasetIndex;
        };

        // Legend.
        config.options.plugins.legend = legendConfig();

        return config;
    };

    return AttemptAdministrationChartOutput;
});
