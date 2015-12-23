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
 * Class for exporting plan data.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;

/**
 * Class for exporting plan data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'tool_lp\\plan';
    }

    protected static function define_related() {
        return array('template' => 'tool_lp\\template?');
    }

    protected function get_other_values(renderer_base $output) {
        $classname = static::define_class();
        $values = array(
            'statusname' => $this->persistent->get_statusname(),
            'isbasedontemplate' => $this->persistent->is_based_on_template(),
            'canmanage' => $this->persistent->can_manage(),
            'canbeedited' => $this->persistent->can_be_edited(),
            'usercanreopen' => $this->persistent->can_manage() &&
                $this->persistent->get_status() == $classname::STATUS_COMPLETE,
            'usercancomplete' => $this->persistent->can_manage() &&
                $this->persistent->get_status() == $classname::STATUS_ACTIVE,
            'usercanunlink' => $this->persistent->can_manage() &&
                $this->persistent->get_status() != $classname::STATUS_COMPLETE &&
                $this->persistent->is_based_on_template(),
            'iscompleted' => $this->persistent->get_status() == $classname::STATUS_COMPLETE,
            'duedateformatted' => userdate($this->persistent->get_duedate())
        );

        if ($this->persistent->is_based_on_template()) {
            $exporter = new template_exporter($this->related['template']);
            $values['template'] = $exporter->export($output);
        }

        return $values;
    }

    public static function define_other_properties() {
        return array(
            'statusname' => array(
                'type' => PARAM_RAW,
            ),
            'isbasedontemplate' => array(
                'type' => PARAM_BOOL,
            ),
            'canmanage' => array(
                'type' => PARAM_BOOL,
            ),
            'canbeedited' => array(
                'type' => PARAM_BOOL,
            ),
            'usercanreopen' => array(
                'type' => PARAM_BOOL,
            ),
            'usercancomplete' => array(
                'type' => PARAM_BOOL,
            ),
            'usercanunlink' => array(
                'type' => PARAM_BOOL,
            ),
            'iscompleted' => array(
                'type' => PARAM_BOOL
            ),
            'duedateformatted' => array(
                'type' => PARAM_TEXT
            ),
            'template' => array(
                'type' => template_exporter::read_properties_definition(),
                'optional' => true,
            ),
        );
    }
}
