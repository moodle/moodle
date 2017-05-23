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
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class sitewide extends base {

    protected function get_site() {
        return new \core_analytics\site();
    }

    public function get_analysable_data($includetarget) {

        // Here there is a single analysable and it is the system.
        $analysable = $this->get_site();

        $return = array();

        $files = $this->process_analysable($analysable, $includetarget);

        // Copy to range files as there is just one analysable.
        // TODO Not abstracted as we should ideally directly store it as range-scope file.
        foreach ($files as $timesplittingid => $file) {

            if ($this->options['evaluation'] === true) {
                // Delete the previous copy. Only when evaluating.
                \core_analytics\dataset_manager::delete_previous_evaluation_file($this->modelid, $timesplittingid);
            }

            // We use merge but it is just a copy
            // TODO use copy or move if there are performance issues.
            $files[$timesplittingid] = \core_analytics\dataset_manager::merge_datasets(array($file), $this->modelid,
                $timesplittingid, $this->options['evaluation'], $includetarget);
        }

        return $files;
    }
}
