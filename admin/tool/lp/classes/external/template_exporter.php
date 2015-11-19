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

use renderer_base;
use tool_lp\plan;

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

    protected function get_values(renderer_base $output) {
        return array(
            'duedateformatted' => userdate($this->persistent->get_duedate()),
            'planscount' => plan::count_records(array('templateid' => $this->persistent->get_id()))
        );
    }

    protected static function define_properties() {
        return array(
            'duedateformatted' => array(
                'type' => PARAM_RAW
            ),
            'planscount' => array(
                'type' => PARAM_INT
            )
        );
    }
}
