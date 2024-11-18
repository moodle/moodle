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
 * Event observer for trainingevent activity plugin.
 *
 * @package    mod_trainingevent
 * @copyright  2022 Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/trainingevent/lib.php');

class mod_trainingevent_observer {

    /**
     * Triggered via mod_trainingevent::user_added event.
     *
     * @param \mod_trainingevent\event\user_added $event
     * @return bool true on success.
     */
    public static function user_attending($event) {
        trainingevent_user_attending($event);
        return true;
    }

    /**
     * Triggered via block_iomad_company_admin::company_created event.
     *
     * @param \mod_trainingevent\event\user_removed $event
     * @return bool true on success.
     */
    public static function user_removed($event) {
        trainingevent_user_removed($event);
        return true;
    }
}
