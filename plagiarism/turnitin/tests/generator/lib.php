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

defined('MOODLE_INTERNAL') || die();
global $CFG;
global $DB;

require_once($CFG->dirroot . '/plagiarism/turnitin/classes/turnitin_user.class.php');
include_once $CFG->dirroot.'/course/lib.php';
require_once $CFG->dirroot.'/webservice/tests/helpers.php';

/**
 * plagiarism_turnitin module data generator class
 * Usage:
 *   - Test class must extend this class.
 *   - Create a test function and call one of these functions from within it using (for example):
 *     <code>$this->make_test_users(5,"Learner");</code>
 *
 * @category  Test
 * @package  plagiarism_turnitin
 * @copyright  2017 Turnitin
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plagiarism_turnitin_test_lib extends advanced_testcase {

    /**
     * Creates a number of test plagiarism_turnitin users, creates an equivalent moodle user for each, and handles the database association work.
     *
     * @param int $number_of_users
     * @param array $roles - an array of strings, each of which should be 'Learner' or 'Instructor'.
     * @return object $return - object of two arrays of equal length, one full of plagiarism_turnitin_user types and the other with ids for dbtable plagiarism_turnitin_users. The indices of these arrays DO align.
     */
    public function make_test_users($number_of_users, $roles) {
        $return['plagiarism_turnitin_users'] = array();
        $return['joins'] = array();

        for ($i=0; $i < $number_of_users; $i++) {
            $role = isset($roles[$i]) ? $roles[$i] : 'Instructor';
            $new_user = new turnitin_user( $i+1, $role, false, 'site', false );
            array_push($return['plagiarism_turnitin_users'], $new_user);
            $joinid = $this->join_test_user($new_user);
            array_push($return['joins'], $joinid);
        }

        return $return;
    }

    /**
     * Creates a moodle user and a corresponding entry in the plagiarism_turnitin_users table
     * for the tii user specified
     * @param  object $plagiarism_turnitin_user - plagiarism_turnitin user object
     *
     * @return  int $plagiarism_turnitin_user_id id of plagiarism_turnitin user join (for use in get_record queries on plagiarism_turnitin_users table)
     */
    public function join_test_user($plagiarism_turnitin_user) {
        global $DB;

        $mdl_user = $this->getDataGenerator()->create_user();
        $tiiUserRecord = new stdClass();
        $tiiUserRecord->userid = $mdl_user->id;
        $tiiUserRecord->turnitin_uid = $plagiarism_turnitin_user->id;
        $tiiUserRecord->user_agreement_accepted = 1;
        $plagiarism_turnitin_user_id = $DB->insert_record('plagiarism_turnitin_users', $tiiUserRecord);

        return $plagiarism_turnitin_user_id;
    }
}
