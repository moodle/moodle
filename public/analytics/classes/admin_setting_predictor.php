<?php
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
 * Extension to show an error message if the selected predictor is not available.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib/adminlib.php');

/**
 * Extension to show an error message if the selected predictor is not available.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_predictor extends \admin_setting_configselect {

    /**
     * Save a setting
     *
     * @param string $data
     * @return string empty of error string
     */
    public function write_setting($data) {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        if (!array_key_exists($data, $this->choices)) {
            return '';
        }

        // Calling it here without checking if it is ready because we check it below and show it as a controlled case.
        $selectedprocessor = \core_analytics\manager::get_predictions_processor($data, false);

        if (!during_initial_install() && !moodle_needs_upgrading()) {
            // TODO: Do not check if the processor is ready during installation or upgrade. See MDL-84481.
            $isready = $selectedprocessor->is_ready();
            if ($isready !== true) {
                return get_string('errorprocessornotready', 'analytics', $isready);
            }
        }

        $currentvalue = get_config('analytics', 'predictionsprocessor');
        if (!empty($currentvalue) && $currentvalue != str_replace('\\\\', '\\', $data)) {
            // Clear all models data.
            $models = \core_analytics\manager::get_all_models();
            foreach ($models as $model) {
                $model->clear();
            }
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}
