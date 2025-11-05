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
 * @package   tool_mergeusers
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author    Mike Holzer
 * @author    Forrest Gaston
 * @author    Juan Pablo Torres Herrera
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>, Universitat Rovira i Virgili
 * @author    John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @copyright University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use coding_exception;
use dml_exception;
use Exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/clilib.php');

/**
 * A class to perform user search and verification.
 *
 * @author John Hoopes <hoopes@wisc.edu>
 */
final class user_searcher {
    /**
     * Searches users matching a condition on a given field and text to match partially for that field.
     *
     * @param string $input Term to search by.
     * @param string $searchfield The user's field to search by. Empty string means searching by all fields.
     * @return array the results of the search.
     * @throws dml_exception
     */
    public function search_users(string $input, string $searchfield): array {
        global $DB;

        switch ($searchfield) {
            // Search on id field.
            case 'id':
                // The sql_cast_to_char() prevents PostgreSQL error when comparing id column when $input is not an integer.
                $where = $DB->sql_cast_to_char('id') . ' = :userid';
                $params = ['userid' => $input];
                break;
            // Searching by any of these fields.
            case 'username':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'idnumber':
                $where = $DB->sql_like($searchfield, ":$searchfield", false, false);
                $params = [$searchfield => '%' . $input . '%'];
                break;
            // Search on all fields by default.
            default:
                $where = '(' .
                         $DB->sql_cast_to_char('id') . ' = :userid OR ' .
                         $DB->sql_like('username', ':username', false, false)
                         . ' OR ' .
                         $DB->sql_like('firstname', ':firstname', false, false)
                         . ' OR ' .
                         $DB->sql_like('lastname', ':lastname', false, false)
                         . ' OR ' .
                         $DB->sql_like('email', ':email', false, false)
                         . ' OR ' .
                         $DB->sql_like('idnumber', ':idnumber', false, false)
                         . ')';
                $params['userid'] = $input;
                $params['username'] = '%' . $input . '%';
                $params['firstname'] = '%' . $input . '%';
                $params['lastname'] = '%' . $input . '%';
                $params['email'] = '%' . $input . '%';
                $params['idnumber'] = '%' . $input . '%';
                break;
        }

        $where .= ' AND deleted = :deleted';
        $params['deleted'] = 0;
        return $DB->get_records_select('user', $where, $params, 'lastname, firstname');
    }

    /**
     * Verifies whether a user exists based upon the user information
     * to verify and the column that matches that information.
     *
     * The result has this structure:
     *   [
     *       0 => Either NULL or the user object.  Will be NULL if not valid user or without actual selection,
     *       1 => Message for invalid user to display/log. Empty string for no actual selection.
     *   ]
     *
     * @param ?string $value The identifying information about the user. Null when no actual selection was done.
     * @param string $field The column name to verify against. (Should not be direct user input)
     *
     * @return array two positions with the results of the verification.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function verify_user(?string $value, string $field): array {
        global $DB;

        // Inform there is no actual selection this time.
        if (is_null($value)) {
            return [null, ''];
        }

        // Check for existing user matching the specified criteria.
        $message = '';
        try {
            $user = $DB->get_record('user', [$field => $value, 'deleted' => 0], '*', MUST_EXIST);
        } catch (Exception $e) {
            $message = get_string('invaliduser', 'tool_mergeusers', ['field' => $field, 'value' => $value]);
            $user = null;
        }

        return [$user, $message];
    }
}
