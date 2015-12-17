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
 * Class for exporting template data.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderer_base;
use tool_lp\plan;
use tool_lp\template_cohort;

/**
 * Class for exporting template data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'tool_lp\\template';
    }

    protected function get_other_values(renderer_base $output) {
        return array(
            'duedateformatted' => userdate($this->persistent->get_duedate()),
            'cohortscount' => template_cohort::count_records(array('templateid' => $this->persistent->get_id())),
            'planscount' => plan::count_records(array('templateid' => $this->persistent->get_id())),
            'canmanage' => $this->persistent->can_manage(),
            'canread' => $this->persistent->can_read(),
            'contextname' => $this->persistent->get_context()->get_context_name()
        );
    }

    protected static function define_other_properties() {
        return array(
            'duedateformatted' => array(
                'type' => PARAM_RAW
            ),
            'cohortscount' => array(
                'type' => PARAM_INT
            ),
            'planscount' => array(
                'type' => PARAM_INT
            ),
            'canmanage' => array(
                'type' => PARAM_BOOL
            ),
            'canread' => array(
                'type' => PARAM_BOOL
            ),
            'contextname' => array(
                'type' => PARAM_TEXT,
            )
        );
    }
}
