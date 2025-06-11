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
 * Adhoc task handling rating users in learning plan templates.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lpmonitoring\task;
use report_lpmonitoring\api;

/**
 * Adhoc task handling rating users in learning plan templates.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rate_users_in_templates extends \core\task\adhoc_task {

    /**
     * Run the rating task.
     */
    public function execute() {
        global $CFG;

        // Set the proper user.
        $user = \core_user::get_user($this->get_userid(), '*', MUST_EXIST);
        \core\cron::setup_user($user);

        // Rate users in template.
        api::rate_users_in_template_with_defaultvalues($this->get_custom_data());
    }
}
