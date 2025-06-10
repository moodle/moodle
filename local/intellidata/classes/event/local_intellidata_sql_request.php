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
 * IntelliData sql request event.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\event;

use core\event\base;
use local_intellidata\helpers\ParamsHelper;

/**
 * IntelliData sql request event.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_sql_request extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_sql_request', ParamsHelper::PLUGIN);
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $other = is_array($this->other) ? json_encode($this->other) : $this->other;
        return "The web service function 'local_intellidata_get_data' has been called with params: '{$other}'.";
    }

    /**
     * Get other values mapping.
     *
     * @return false
     */
    public static function get_other_mapping() {
        return false;
    }
}
