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
 * Class for exporting user competency data.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;

use renderer_base;
use context_user;
use tool_lp\user_competency;

/**
 * Class for exporting user competency data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_exporter extends persistent_exporter {

    protected function get_persistent_class() {
        return 'tool_lp\\user_competency';
    }

    protected function get_related() {
        // We cache the scale so it does not need to be retrieved from the framework every time.
        return array('scale' => 'grade_scale');
    }

    public function export_for_template(renderer_base $output) {
        $result = parent::export_for_template($output);
        $context = context_user::instance($result->userid);
        if ($result->grade === null) {
            $gradename = '-';
        } else {
            $gradename = external_format_string($this->related['scale']->scale_items[$result->grade - 1], $context->id);
        }
        $result->gradename = $gradename;

        if ($result->proficiency === null) {
            $proficiencyname = '-';
        } else {
            $proficiencyname = get_string($result->proficiency ? 'yes' : 'no');
        }
        $result->proficiencyname = $proficiencyname;

        $statusname = '-';
        if ($result->status != user_competency::STATUS_IDLE) {
            $statusname = (string) $this->persistent->get_status_name($result->status);
        }
        $result->statusname = $statusname;
        return $result;
    }
}
