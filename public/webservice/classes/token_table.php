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

namespace core_webservice;

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
 *
 * @deprecated since 4.5 MDL-79496. Table replaced with a report builder system report.
 * @todo MDL-79909 This will be deleted in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: null,
    since: '4.5',
    reason: 'Table replaced with a report builder system report',
    mdl: 'MDL-79496',
)]
class token_table extends \table_sql {

    /**
     * @var bool $showalltokens Whether or not the user is able to see all tokens.
     */
    protected $showalltokens;

    /** @var bool $hasviewfullnames Does the user have the viewfullnames capability. */
    protected $hasviewfullnames;

    /** @var array */
    protected $userextrafields;

    /** @var object */
    protected $filterdata;

    /**
     * Sets up the table.
     *
     * @param int $id The id of the table
     * @param object $filterdata The data submitted by the {@see token_filter}.
     */
    public function __construct($id, $filterdata = null) {
        parent::__construct($id);

        // Get the context.
        $context = \context_system::instance();

        // Can we see tokens created by all users?
        $this->showalltokens = has_capability('moodle/webservice:managealltokens', $context);
        $this->hasviewfullnames = has_capability('moodle/site:viewfullnames', $context);

        // List of user identity fields.
        $this->userextrafields = \core_user\fields::get_identity_fields(\context_system::instance(), false);

        // Filter form values.
        $this->filterdata = $filterdata;

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('tokenname', 'webservice');
        $columns[] = 'name';
        $headers[] = get_string('user');
        $columns[] = 'fullname';
        $headers[] = get_string('service', 'webservice');
        $columns[] = 'servicename';
        $headers[] = get_string('iprestriction', 'webservice');
        $columns[] = 'iprestriction';
        $headers[] = get_string('validuntil', 'webservice');
        $columns[] = 'validuntil';
        $headers[] = get_string('lastaccess');
        $columns[] = 'lastaccess';
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
            return get_string('validuntil_empty', 'webservice');
        } else {
            return userdate($data->validuntil, get_string('strftimedatetime', 'langconfig'));
        }
    }

    /**
     * Generate the last access column
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_lastaccess(\stdClass $data): string {
        if (empty($data->lastaccess)) {
            return get_string('never');
        } else {
            return userdate($data->lastaccess, get_string('strftimedatetime', 'langconfig'));
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

        $identity = [];

        foreach ($this->userextrafields as $userextrafield) {
            $identity[] = s($data->$userextrafield);
        }

        $userprofilurl = new \moodle_url('/user/profile.php', ['id' => $data->userid]);
        $content = \html_writer::link($userprofilurl, fullname($data, $this->hasviewfullnames));

        if ($identity) {
            $content .= \html_writer::div('<small>' . implode(', ', $identity) . '</small>', 'useridentity text-muted');
        }

        // Make up list of capabilities that the user is missing for the given webservice.
        $webservicemanager = new \webservice();
        $usermissingcaps = $webservicemanager->get_missing_capabilities_by_users([['id' => $data->userid]], $data->serviceid);

        if ($data->serviceshortname <> MOODLE_OFFICIAL_MOBILE_SERVICE && !is_siteadmin($data->userid)
                && array_key_exists($data->userid, $usermissingcaps)) {
            $count = \html_writer::span(count($usermissingcaps[$data->userid]), 'badge bg-danger text-white');
            $links = array_map(function($capname) {
                return get_capability_docs_link((object)['name' => $capname]) . \html_writer::div($capname, 'text-muted');
            }, $usermissingcaps[$data->userid]);
            $list = \html_writer::alist($links);
            $help = $OUTPUT->help_icon('missingcaps', 'webservice');
            $content .= print_collapsible_region(\html_writer::div($list . $help, 'missingcaps'), 'small mt-2',
                \html_writer::random_id('usermissingcaps'), get_string('usermissingcaps', 'webservice', $count), '', true, true);
        }

        return $content;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $data Data for the current row
     * @return string Content for the column
     */
    public function col_name($data) {
        return $data->name;
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
        return \html_writer::link($creatorprofileurl, fullname((object)$user, $this->hasviewfullnames));
    }

    /**
     * Format the service name column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_servicename($data) {
        return \html_writer::div(s($data->servicename)) . \html_writer::div(s($data->serviceshortname), 'small text-muted');
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

        $userfieldsapi = \core_user\fields::for_name();
        $usernamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $creatorfields = $userfieldsapi->get_sql('c', false, 'creator', '', false)->selects;

        if (!empty($this->userextrafields)) {
            $usernamefields .= ',u.' . implode(',u.', $this->userextrafields);
        }

        $params = ['tokenmode' => EXTERNAL_TOKEN_PERMANENT];

        $selectfields = "SELECT t.id, t.name, t.iprestriction, t.validuntil, t.creatorid, t.lastaccess,
                                u.id AS userid, $usernamefields,
                                s.id AS serviceid, s.name AS servicename, s.shortname AS serviceshortname,
                                $creatorfields ";

        $selectcount = "SELECT COUNT(t.id) ";

        $sql = "  FROM {external_tokens} t
                  JOIN {user} u ON u.id = t.userid
                  JOIN {external_services} s ON s.id = t.externalserviceid
                  JOIN {user} c ON c.id = t.creatorid
                 WHERE t.tokentype = :tokenmode";

        if (!$this->showalltokens) {
            // Only show tokens created by the current user.
            $sql .= " AND t.creatorid = :userid";
            $params['userid'] = $USER->id;
        }

        if ($this->filterdata->name !== '') {
            $sql .= " AND " . $DB->sql_like("t.name", ":name", false, false);
            $params['name'] = "%" . $DB->sql_like_escape($this->filterdata->name) . "%";
        }

        if (!empty($this->filterdata->users)) {
            list($sqlusers, $paramsusers) = $DB->get_in_or_equal($this->filterdata->users, SQL_PARAMS_NAMED, 'user');
            $sql .= " AND t.userid {$sqlusers}";
            $params += $paramsusers;
        }

        if (!empty($this->filterdata->services)) {
            list($sqlservices, $paramsservices) = $DB->get_in_or_equal($this->filterdata->services, SQL_PARAMS_NAMED, 'service');
            $sql .= " AND s.id {$sqlservices}";
            $params += $paramsservices;
        }

        $sort = $this->get_sql_sort();
        $sortsql = '';

        if ($sort) {
            $sortsql = " ORDER BY {$sort}";
        }

        $total = $DB->count_records_sql($selectcount . $sql, $params);
        $this->pagesize($pagesize, $total);

        $this->rawdata = $DB->get_recordset_sql($selectfields . $sql . $sortsql, $params, $this->get_page_start(),
            $this->get_page_size());
    }
}
