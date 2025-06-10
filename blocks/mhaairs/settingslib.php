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
 * Block MHAAIRS Improved
 *
 * @package    block_mhaairs
 * @copyright  2013 Moodlerooms inc.
 * @author     Teresa Hardy <thardy@moodlerooms.com>
 * @author     Darko Miletic
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

require_once($CFG->dirroot.'/blocks/mhaairs/lib.php');

/**
 *
 */
class admin_setting_configmulticheckbox_mhaairs extends admin_setting_configmulticheckbox {

    public function __construct($name, $heading, $description) {
        parent::__construct($name, $heading, $description, null, null);
    }

    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $result = false;
        $services = block_mhaairs_connect::get_services(true);
        if (is_array($services) && isset($services['Tools'])) {
            foreach ($services['Tools'] as $item) {
                $choices[$item['ServiceID']] = '&nbsp;&nbsp;'.$item['ServiceName'];
            }
            asort($choices);
            $this->choices = $choices;
            $result = true;
        }
        return $result;
    }

    /**
     * Returns the current setting if it is set
     * or array otherwise so that it doesn't loop
     * on install/upgrade.
     *
     * @return array
     */
    public function get_setting() {
        $setting = parent::get_setting();

        if (is_null($setting)) {
            $setting = array();
        }

        return $setting;
    }

    public function output_html($data, $query='') {
        if ($this->load_choices()) {
            return parent::output_html($data, $query);
        }

        $visiblename = get_string('services_displaylabel', 'block_mhaairs');
        $description = get_string('service_down_msg'     , 'block_mhaairs');
        return format_admin_setting($this, $visiblename, '', $description, false, '', '');
    }
}
