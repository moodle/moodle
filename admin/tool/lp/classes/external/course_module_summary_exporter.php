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
 * Class for exporting a course module summary from an stdClass.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;


/**
 * Class for exporting a course module summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_summary_exporter extends exporter {

    protected function get_other_values(renderer_base $output) {
        global $CFG;

        require_once($CFG->libdir . '/modinfolib.php');

        $cm = get_coursemodule_from_id(null, $this->data->id);
        $fastmodinfo = get_fast_modinfo($cm->course)->cms[$cm->id];

        return array(
            'name' => $fastmodinfo->name,
            'url' => $fastmodinfo->url->out(),
            'iconurl' => $fastmodinfo->get_icon_url()->out()
        );
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'visible' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT
            ),
            'url' => array(
                'type' => PARAM_URL
            ),
            'iconurl' => array(
                'type' => PARAM_URL
            )
        );
    }
}
