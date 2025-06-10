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
 * Module to manage appearance of the answers distribution chart.
 *
 * @module     mod_adaptivequiz/attempt_answers_distribution_chart_manager
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Entry point of the module.
 *
 * @param {Object} chartOutput An instance of Output.
 * @param {Object} chartBar An instance of Bar.
 * @param {Number} userId Current user to set the preferences for.
 * @param {Number} adaptiveQuizId Adaptive quiz instance to set the preferences for.
 */
export const init = (chartOutput, chartBar, userId, adaptiveQuizId) => {
    let stackedFlagControl = document.querySelector('[data-action="set-answers-distribution-chart-stacked"]');

    stackedFlagControl.addEventListener('change', (e) => {
        const setStacked = e.target.checked;

        chartBar.setStacked(setStacked);
        chartOutput.update();

        Ajax.call([{
            methodname: 'core_user_set_user_preferences',
            args: {
                preferences: [{
                    name: `mod_adaptivequiz_answers_distribution_chart_settings_${adaptiveQuizId}`,
                    value: JSON.stringify({
                        showstacked: setStacked,
                    }),
                    userid: userId,
                }]
            }
        }])[0].catch(Notification.exception);
    });
};
