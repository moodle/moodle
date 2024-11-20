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
 * An adhoc task for local Iomad track
 *
 * @package    local_iomad_track
 * @copyright  2020 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_iomad_company_admin\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

class companyenableshop extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('companyenableshoptask', 'block_iomad_company_admin');
    }

    /**
     * Run companyenableshop
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/blocks/iomad_commerce/locallib.php');

        $data = $this->get_custom_data();
        $company = new \company($data->companyid);
        $companyrecord = $DB->get_record('company', array('id' => $data->companyid));
        \iomad_commerce::update_company($companyrecord, $companyrecord);

        // get the company user ids.
        //$userids = array();
        $userids = $company->get_all_user_ids();

        // fire the user update.
        foreach (array_keys($userids) as $userid) {
            if ($user = $DB->get_record('user', array('id' => $userid, 'suspended' => 0, 'deleted' => 0))) {
                $user->company = $companyrecord->name;
                $compuser = $DB->get_record('company_users', array('userid' => $userid, 'companyid' => $data->companyid));
                if ($compuser->managertype == 1 ) {
                    $user->manager = 'yes';
                } else {
                    $user->manager = 'no';
                }
                \iomad_commerce::update_user($user, $data->companyid);
            }
        }

        return true;
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task($companyid) {
        global $USER;

        // Let's set up the adhoc task.
        $task = new \block_iomad_company_admin\task\companyenableshop();
        $task->set_custom_data(array('companyid' => $companyid));
        $task->set_userid($USER->id);
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
