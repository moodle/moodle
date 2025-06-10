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
 * Defines order and indices for datasets.
 *
 * @module     mod_adaptivequiz/attempt_administration_chart_dataset_config
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    orderWeights: {
        ABILITY_MEASURE: 10,
        ADMINISTERED_DIFFICULTY: 20,
        TARGET_DIFFICULTY: 30,
        STANDARD_ERROR_BORDER: 40,
        STANDARD_ERROR_PERCENT: 50,
        CORRECT_WRONG_FLAG: 60,
    },
    indices: {
        TARGET_DIFFICULTY: 0,
        ADMINISTERED_DIFFICULTY: 1,
        CORRECT_WRONG_FLAG: 2,
        ABILITY_MEASURE: 3,
        STANDARD_ERROR_MAX: 4,
        STANDARD_ERROR_MIN: 5,
        STANDARD_ERROR_PERCENT: 6,
    },
};
