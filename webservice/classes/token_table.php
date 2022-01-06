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
 * Contains the class used for the displaying the tokens table.
 *
 * @package    core_webservice
 * @copyright  2017 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace webservice;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the participants table.
 *
 * @package    core_webservice
 * @copyright  2017 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_table extends \table_sql {

    /**
     * @var bool $showalltokens Whether or not the user is able to see all tokens.
     */
    protected $showalltokens;

    /**
     * Sets up the table.
     * @param int $id The id of the table
     */
    public function __construct($id) {
        parent::__construct($id);

        // Get the context.
        $context = \context_system::instance();

        // Can we see tokens created by all users?
        $this->showalltokens = has_capability('moodle/webservice:managealltokens', $context);

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('token', 'webservice');
        $columns[] = 'token';
        $headers[] = get_string('user');
        $columns[] = 'fullname';
        $headers[] = get_string('service', 'webservice');
        $columns[] = 'name';
        $headers[] = get_string('iprestriction', 'webservice');
        $columns[] = 'iprestriction';
        $headers[] = get_string('validuntil', 'webservice');
        $columns[] = 'validuntil';
        if ($this->showalltokens) {
            // Only need to show creator if you can see tokens created by other people.
            $headers[] = get_string('tokencreator', 'webservice');
            $columns[] = 'creatorlastname'; // So we can have semi-useful sorting. Table SQL doesn't two fullname collumns.
        }
        $headers[] = get_string('operation', 'webservice');
        $columns[] = 'operation';

        $this->define_columns($columns);
        $this->define_headers($headers);

        $this->no_sorting('operation');
        $this->no_sorting('token');
        $this->no_sorting('iprestriction');

        $this->set_attribute('id', $id);
    }

    /**
     * Generate the operation column.
     *
     * @param \stdClass $data Data for the current row
     * @return string Content for the column
     */
    public function col_operation($data) {
        $tokenpageurl = new \moodle_url(
            "/admin/webservice/tokens.php",
            [
                "sesskey" => sesskey(),
                "action" => "delete",
                "tokenid" => $data->id
            ]
        );
        return \html_writer::link($tokenpageurl, get_string("delete"));
    }

    /**
     * Generate the validuntil column.
     *
     * @param \stdClass $data Data for the current row
     * @return string Content for the column
     */
    public function col_validuntil($data) {
        if (empty($data->validuntil)) {
            return '';
        } else {
            return userdate($data->validuntil, get_string('strftimedatetime', 'langconfig'));
        }
    }

    /**
     * Generate the fullname column. Also includes capabilities the user is missing for the webservice (if any)
     *
     * @param \stdClass $data Data for the current row
     * @return string Content for the column
     */
    public function col_fullname($data) {
        global $OUTPUT;

        $userprofilurl = new \moodle_url('/user/profile.php', ['id' => $data->userid]);
        $content = \html_writer::link($userprofilurl, fullname($data));

        // Make up list of capabilities that the user is missing for the given webservice.
        $webservicemanager = new \webservice();
        $usermissingcaps = $webservicemanager->get_missing_capabilities_by_users([['id' => $data->userid]], $data->serviceid);

        if (!is_siteadmin($data->userid) && array_key_exists($data->userid, $usermissingcaps)) {
            $missingcapabilities = implode(', ', $usermissingcaps[$data->userid]);
            if (!empty($missingcapabilities)) {
                $capabilitiesstring = get_string('usermissingcaps', 'webservice', $missingcapabilities) . '&nbsp;' .
                        $OUTPUT->help_icon('missingcaps', 'webservice');
                $content .= \html_writer::div($capabilitiesstring, 'missingcaps');
            }
        }

        return $content;
    }

    /**
     * Generate the token column.
     *
     * @param \stdClass $data Data for the current row
     * @return string Content for the column
     */
    public function col_token($data) {
        global $USER;
        // Hide the token if it wasn't created by the current user.
        if ($data->creatorid != $USER->id) {
            return '-';
        }

        return $data->token;
    }

    /**
     * Generate the creator column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_creatorlastname($data) {
        // We have loaded all the name fields for the creator, with the 'creator' prefix.
        // So just remove the prefix and make up a user object.
        $user = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'creator') !== false) {
                $newkey = str_replace('creator', '', $key);
                $user[$newkey] = $value;
            }
        }

        $creatorprofileurl = new \moodle_url('/user/profile.php', ['id' => $data->creatorid]);
        return \html_writer::link($creatorprofileurl, fullname((object)$user));
    }

    /**
     * This function is used for the extra user fields.
     *
     * These are being dynamically added to the table so there are no functions 'col_<userfieldname>' as
     * the list has the potential to increase in the future and we don't want to have to remember to add
     * a new method to this class. We also don't want to pollute this class with unnecessary methods.
     *
     * @param string $colname The column name
     * @param \stdClass $data
     * @return string
     */
    public function other_cols($colname, $data) {
        return s($data->{$colname});
    }

    /**
     * Query the database for results to display in the table.
     *
     * Note: Initial bars are not implemented for this table because it includes user details twice and the initial bars do not work
     * when the user table is included more than once.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar Not implemented. Please pass false.
     */
    public function query_db($pagesize, $useinitialsbar = false) {
        global $DB, $USER;

        if ($useinitialsbar) {
            debugging('Initial bar not implemented yet. Call out($pagesize, false)');
        }

        $usernamefields = get_all_user_name_fields(true, 'u');
        $creatorfields = get_all_user_name_fields(true, 'c', null, 'creator');

        $params = ["tokenmode" => EXTERNAL_TOKEN_PERMANENT];

        // TODO: in order to let the administrator delete obsolete token, split the request in multiple request or use LEFT JOIN.

        if ($this->showalltokens) {
            // Show all tokens.
            $sql = "SELECT t.id, t.token, u.id AS userid, $usernamefields, s.name, t.iprestriction, t.validuntil, s.id AS serviceid,
                           t.creatorid, $creatorfields
                      FROM {external_tokens} t, {user} u, {external_services} s, {user} c
                     WHERE t.tokentype = :tokenmode AND s.id = t.externalserviceid AND t.userid = u.id AND c.id = t.creatorid";
            $countsql = "SELECT COUNT(t.id)
                           FROM {external_tokens} t, {user} u, {external_services} s, {user} c
                          WHERE t.tokentype = :tokenmode AND s.id = t.externalserviceid AND t.userid = u.id AND c.id = t.creatorid";
        } else {
            // Only show tokens created by the current user.
            $sql = "SELECT t.id, t.token, u.id AS userid, $usernamefields, s.name, t.iprestriction, t.validuntil, s.id AS serviceid,
                           t.creatorid, $creatorfields
                      FROM {external_tokens} t, {user} u, {external_services} s, {user} c
                     WHERE t.creatorid=:userid AND t.tokentype = :tokenmode AND s.id = t.externalserviceid AND t.userid = u.id AND
                           c.id = t.creatorid";
            $countsql = "SELECT COUNT(t.id)
                           FROM {external_tokens} t, {user} u, {external_services} s, {user} c
                          WHERE t.creatorid=:userid AND t.tokentype = :tokenmode AND s.id = t.externalserviceid AND
                                t.userid = u.id AND c.id = t.creatorid";
            $params["userid"] = $USER->id;
        }

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql = $sql . ' ORDER BY ' . $sort;
        }

        $total = $DB->count_records_sql($countsql, $params);
        $this->pagesize($pagesize, $total);

        $this->rawdata = $DB->get_recordset_sql($sql, $params, $this->get_page_start(), $this->get_page_size());
    }
}
