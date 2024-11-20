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
 * Utility file.
 *
 * The effort of all given authors below gives you this current version of the file.
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';
require_once __DIR__ . '/autoload.php';
require_once $CFG->dirroot . '/local/iomad/lib/company.php';

/**
 * A class to perform user search and lookup (verification)
 *
 * @author John Hoopes <hoopes@wisc.edu>
 */
class IomadMergeSearch{


    /**
     * Searches the user table based on the input.
     *
     * @param mixed $input input
     * @param string $searchfield The field to search on.  empty string means all fields
     * @return array $results the results of the search
     */
    public function search_users($input, $searchfield){
        global $DB, $USER;

        if (!iomad::has_capability('block/iomad_company_admin:editallusers', context_system::instance())) {
            // Get the user id's which the user can see.
            $companyid = iomad::get_my_companyid(context_system::instance());
            $company = new company($companyid);
            $userlevels = $company->get_userlevel($USER);
            $departmentusers = array();
            foreach ($userlevels as $userlevelid => $userlevel) {
                $departmentusers = $departmentusers + company::get_recursive_department_users($userlevelid);
            }
            if (!empty($departmentusers)) {
                $departmentids = "";
                foreach ($departmentusers as $departmentuser) {
                    if (!empty($departmentids)) {
                        $departmentids .= ",".$departmentuser->userid;
                    } else {
                        $departmentids .= $departmentuser->userid;
                    }
                }
                $departmentsql = " AND id IN (" . $departmentids . ")";
            } else {
                $departmentsql = " AND 1 = 2 ";
            }
        } else {
            $departmentsql = "";
        }

        switch($searchfield){
            case 'id': // search on id field

                $params = array(
                    'userid' => $input,
                );
                $sql = 'SELECT * FROM {user} WHERE id = :userid';

                break;
            case 'username': // search on username

                $params = array(
                    'username' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE username LIKE :username';

                break;
            case 'firstname': // search on firstname

                $params = array(
                    'firstname' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE firstname LIKE :firstname';

                break;
            case 'lastname': // search on lastname

                $params = array(
                    'lastname' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE lastname LIKE :lastname';

                break;
            case 'email': // search on email

                $params = array(
                    'email' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE email LIKE :email';

                break;
            case 'idnumber': // search on idnumber

                $params = array(
                    'idnumber' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE idnumber LIKE :idnumber';

                break;
            default: // search on all fields by default

                $params = array(
                    'userid'     =>  $input,
                    'username'   => '%' . $input . '%',
                    'firstname'  => '%' . $input . '%',
                    'lastname'   => '%' . $input . '%',
                    'email'      => '%' . $input . '%',
                    'idnumber'      => '%' . $input . '%'
                );

                $sql =
                   'SELECT *
                    FROM {user}
                    WHERE
                        id = :userid OR
                        username LIKE :username OR
                        firstname LIKE :firstname OR
                        lastname LIKE :lastname OR
                        email LIKE :email OR
                        idnumber LIKE :idnumber';

                break;
        }

        $ordering = ' $departmentsql ORDER BY lastname, firstname';

        $results = $DB->get_records_sql($sql . $ordering, $params);
        return $results;
    }

    /**
     * Verifies whether or not a user exists based upon the user information
     * to verify and the column that matches that information
     *
     * @param mixed $uinfo The identifying information about the user
     * @param string $column The column name to verify against.  (should not be direct user input)
     *
     * @return array
     *      (
     *          0 => Either NULL or the user object.  Will be NULL if not valid user,
     *          1 => Message for invalid user to display/log
     *      )
     */
    public function verify_user($uinfo, $column){
        global $DB;
        $message = '';
        try {
            $user = $DB->get_record('user', array($column => $uinfo), '*', MUST_EXIST);
        } catch (Exception $e) {
            $message = get_string('invaliduser', 'tool_iomadmerge'). '('.$column . '=>' . $uinfo .'): ' . $e->getMessage();
            $user = null;
        }

        return array($user, $message);
    }


}
