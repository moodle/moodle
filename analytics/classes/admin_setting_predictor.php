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
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib/adminlib.php');

class admin_setting_predictor extends \admin_setting_configselect {

    /**
     * Builds HTML to display the control.
     *
     * The main purpose of this is to display a warning if the selected predictions processor is not ready.

     * @param string $data Unused
     * @param string $query
     * @return string HTML
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $html = '';

        // Calling it here without checking if it is ready because we check it below and show it as a controlled case.
        $selectedprocessor = \core_analytics\manager::get_predictions_processor($data, false);

        $isready = $selectedprocessor->is_ready();
        if ($isready !== true) {
            $html .= $OUTPUT->notification(get_string('errorprocessornotready', 'analytics', $isready));
        }

        $html .= parent::output_html($data, $query);
        return $html;
    }
}
