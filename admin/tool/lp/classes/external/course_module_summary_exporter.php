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


/**
 * Class for exporting a course module summary from a cm_info class.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_summary_exporter extends \core_competency\external\exporter {

    protected static function define_related() {
        return array('cm' => 'cm_info');
    }

    protected function get_other_values(renderer_base $output) {
        $cm = $this->related['cm'];
        $context = $cm->context;

        $values = array(
            'id' => $cm->id,
            'name' => external_format_string($cm->name, $context->id),
            'iconurl' => $cm->get_icon_url()->out()
        );
        if ($cm->url) {
            $values['url'] = $cm->url->out();
        }
        return $values;
    }


    public static function define_other_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'name' => array(
                'type' => PARAM_TEXT
            ),
            'url' => array(
                'type' => PARAM_URL,
                'optional' => true,
            ),
            'iconurl' => array(
                'type' => PARAM_URL
            )
        );
    }
}
