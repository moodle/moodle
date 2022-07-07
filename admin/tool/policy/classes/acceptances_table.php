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
 * View user acceptances to the policies
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy;

use tool_policy\output\acceptances_filter;
use tool_policy\output\renderer;
use tool_policy\output\user_agreement;
use core_user;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/tablelib.php');

/**
 * Class acceptances_table
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class acceptances_table extends \table_sql {

    /** @var array */
    protected $versionids;

    /** @var acceptances_filter */
    protected $acceptancesfilter;

    /** @var renderer */
    protected $output;

    /**
     * @var string[] The list of countries.
     */
    protected $countries;

    /** @var bool are there any users that this user can agree on behalf of */
    protected $canagreeany = false;

    /**
     * Constructor.
     *
     * @param string $uniqueid Table identifier.
     * @param acceptances_filter $acceptancesfilter
     * @param renderer $output
     */
    public function __construct($uniqueid, acceptances_filter $acceptancesfilter, renderer $output) {
        global $CFG;
        parent::__construct($uniqueid);
        $this->set_attribute('id', 'acceptancetable');
        $this->acceptancesfilter = $acceptancesfilter;
        $this->is_downloading(optional_param('download', 0, PARAM_ALPHA), 'user_acceptances');
        $this->baseurl = $acceptancesfilter->get_url();
        $this->output = $output;

        $this->versionids = [];
        $versions = $acceptancesfilter->get_versions();
        if (count($versions) > 1) {
            foreach ($versions as $version) {
                $this->versionids[$version->id] = $version->name;
            }
        } else {
            $version = reset($versions);
            $this->versionids[$version->id] = $version->name;
            if ($version->status != policy_version::STATUS_ACTIVE) {
                $this->versionids[$version->id] .= '<br>' . $version->revision;
            }
        }

        $extrafields = get_extra_user_fields(\context_system::instance());
        $userfields = \user_picture::fields('u', $extrafields);

        $this->set_sql("$userfields",
            "{user} u",
            'u.id <> :siteguestid AND u.deleted = 0',
            ['siteguestid' => $CFG->siteguest]);
        if (!$this->is_downloading()) {
            $this->add_column_header('select', get_string('select'), false, 'colselect');
        }
        $this->add_column_header('fullname', get_string('fullnameuser', 'core'));
        foreach ($extrafields as $field) {
            $this->add_column_header($field, get_user_field_name($field));
        }

        if (!$this->is_downloading() && !has_capability('tool/policy:acceptbehalf', \context_system::instance())) {
            // We will need to check capability to accept on behalf in each user's context, preload users contexts.
            $this->sql->fields .= ',' . \context_helper::get_preload_record_columns_sql('ctx');
            $this->sql->from .= ' JOIN {context} ctx ON ctx.contextlevel = :usercontextlevel AND ctx.instanceid = u.id';
            $this->sql->params['usercontextlevel'] = CONTEXT_USER;
        }

        if ($this->acceptancesfilter->get_single_version()) {
            $this->configure_for_single_version();
        } else {
            $this->configure_for_multiple_versions();
        }

        $this->build_sql_for_search_string($extrafields);
        $this->build_sql_for_capability_filter();
        $this->build_sql_for_roles_filter();

        $this->sortable(true, 'firstname');
    }

    /**
     * Remove randomness from the list by always sorting by user id in the end
     *
     * @return array
     */
    public function get_sort_columns() {
        $c = parent::get_sort_columns();
        $c['u.id'] = SORT_ASC;
        return $c;
    }

    /**
     * Allows to add only one column name and header to the table (parent class methods only allow to set all).
     *
     * @param string $key
     * @param string $label
     * @param bool $sortable
     * @param string $columnclass
     */
    protected function add_column_header($key, $label, $sortable = true, $columnclass = '') {
        if (empty($this->columns)) {
            $this->define_columns([$key]);
            $this->define_headers([$label]);
        } else {
            $this->columns[$key] = count($this->columns);
            $this->column_style[$key] = array();
            $this->column_class[$key] = $columnclass;
            $this->column_suppress[$key] = false;
            $this->headers[] = $label;
        }
        if ($columnclass !== null) {
            $this->column_class($key, $columnclass);
        }
        if (!$sortable) {
            $this->no_sorting($key);
        }
    }

    /**
     * Helper configuration method.
     */
    protected function configure_for_single_version() {
        $userfieldsmod = get_all_user_name_fields(true, 'm', null, 'mod');
        $v = key($this->versionids);
        $this->sql->fields .= ", $userfieldsmod, a{$v}.status AS status{$v}, a{$v}.note, ".
           "a{$v}.timemodified, a{$v}.usermodified AS usermodified{$v}";

        $join = "JOIN {tool_policy_acceptances} a{$v} ON a{$v}.userid = u.id AND a{$v}.policyversionid=:versionid{$v}";
        $filterstatus = $this->acceptancesfilter->get_status_filter();
        if ($filterstatus == 1) {
            $this->sql->from .= " $join AND a{$v}.status=1";
        } else if ($filterstatus == 2) {
            $this->sql->from .= " $join AND a{$v}.status=0";
        } else {
            $this->sql->from .= " LEFT $join";
        }

        $this->sql->from .= " LEFT JOIN {user} m ON m.id = a{$v}.usermodified AND m.id <> u.id AND a{$v}.status IS NOT NULL";

        $this->sql->params['versionid' . $v] = $v;

        if ($filterstatus === 0) {
            $this->sql->where .= " AND a{$v}.status IS NULL";
        }

        $this->add_column_header('status' . $v, get_string('response', 'tool_policy'));
        $this->add_column_header('timemodified', get_string('responseon', 'tool_policy'));
        $this->add_column_header('usermodified' . $v, get_string('responseby', 'tool_policy'));
        $this->add_column_header('note', get_string('acceptancenote', 'tool_policy'), false);
    }

    /**
     * Helper configuration method.
     */
    protected function configure_for_multiple_versions() {
        $this->add_column_header('statusall', get_string('acceptancestatusoverall', 'tool_policy'));
        $filterstatus = $this->acceptancesfilter->get_status_filter();
        $statusall = [];
        foreach ($this->versionids as $v => $versionname) {
            $this->sql->fields .= ", a{$v}.status AS status{$v}, a{$v}.usermodified AS usermodified{$v}";
            $join = "JOIN {tool_policy_acceptances} a{$v} ON a{$v}.userid = u.id AND a{$v}.policyversionid=:versionid{$v}";
            if ($filterstatus == 1) {
                $this->sql->from .= " {$join} AND a{$v}.status=1";
            } else if ($filterstatus == 2) {
                $this->sql->from .= " {$join} AND a{$v}.status=0";
            } else {
                $this->sql->from .= " LEFT {$join}";
            }
            $this->sql->params['versionid' . $v] = $v;
            $this->add_column_header('status' . $v, $versionname);
            $statusall[] = "COALESCE(a{$v}.status, 0)";
        }
        $this->sql->fields .= ",".join('+', $statusall)." AS statusall";

        if ($filterstatus === 0) {
            $statussql = [];
            foreach ($this->versionids as $v => $versionname) {
                $statussql[] = "a{$v}.status IS NULL";
            }
            $this->sql->where .= " AND (u.policyagreed = 0 OR ".join(" OR ", $statussql).")";
        }
    }

    /**
     * Download the data.
     */
    public function download() {
        \core\session\manager::write_close();
        $this->out(0, false);
        exit;
    }

    /**
     * Get sql to add to where statement.
     *
     * @return string
     */
    public function get_sql_where() {
        list($where, $params) = parent::get_sql_where();
        $where = preg_replace('/firstname/', 'u.firstname', $where);
        $where = preg_replace('/lastname/', 'u.lastname', $where);
        return [$where, $params];
    }

    /**
     * Helper SQL query builder.
     *
     * @param array $userfields
     */
    protected function build_sql_for_search_string($userfields) {
        global $DB, $USER;

        $search = $this->acceptancesfilter->get_search_strings();
        if (empty($search)) {
            return;
        }

        $wheres = [];
        $params = [];
        foreach ($search as $index => $keyword) {
            $searchkey1 = 'search' . $index . '1';
            $searchkey2 = 'search' . $index . '2';
            $searchkey3 = 'search' . $index . '3';
            $searchkey4 = 'search' . $index . '4';
            $searchkey5 = 'search' . $index . '5';
            $searchkey6 = 'search' . $index . '6';
            $searchkey7 = 'search' . $index . '7';

            $conditions = array();
            // Search by fullname.
            $fullname = $DB->sql_fullname('u.firstname', 'u.lastname');
            $conditions[] = $DB->sql_like($fullname, ':' . $searchkey1, false, false);

            // Search by email.
            $email = $DB->sql_like('u.email', ':' . $searchkey2, false, false);
            if (!in_array('email', $userfields)) {
                $maildisplay = 'maildisplay' . $index;
                $userid1 = 'userid' . $index . '1';
                // Prevent users who hide their email address from being found by others
                // who aren't allowed to see hidden email addresses.
                $email = "(". $email ." AND (" .
                    "u.maildisplay <> :$maildisplay " .
                    "OR u.id = :$userid1". // User can always find himself.
                    "))";
                $params[$maildisplay] = core_user::MAILDISPLAY_HIDE;
                $params[$userid1] = $USER->id;
            }
            $conditions[] = $email;

            // Search by idnumber.
            $idnumber = $DB->sql_like('u.idnumber', ':' . $searchkey3, false, false);
            if (!in_array('idnumber', $userfields)) {
                $userid2 = 'userid' . $index . '2';
                // Users who aren't allowed to see idnumbers should at most find themselves
                // when searching for an idnumber.
                $idnumber = "(". $idnumber . " AND u.id = :$userid2)";
                $params[$userid2] = $USER->id;
            }
            $conditions[] = $idnumber;

            // Search by middlename.
            $middlename = $DB->sql_like('u.middlename', ':' . $searchkey4, false, false);
            $conditions[] = $middlename;

            // Search by alternatename.
            $alternatename = $DB->sql_like('u.alternatename', ':' . $searchkey5, false, false);
            $conditions[] = $alternatename;

            // Search by firstnamephonetic.
            $firstnamephonetic = $DB->sql_like('u.firstnamephonetic', ':' . $searchkey6, false, false);
            $conditions[] = $firstnamephonetic;

            // Search by lastnamephonetic.
            $lastnamephonetic = $DB->sql_like('u.lastnamephonetic', ':' . $searchkey7, false, false);
            $conditions[] = $lastnamephonetic;

            $wheres[] = "(". implode(" OR ", $conditions) .") ";
            $params[$searchkey1] = "%$keyword%";
            $params[$searchkey2] = "%$keyword%";
            $params[$searchkey3] = "%$keyword%";
            $params[$searchkey4] = "%$keyword%";
            $params[$searchkey5] = "%$keyword%";
            $params[$searchkey6] = "%$keyword%";
            $params[$searchkey7] = "%$keyword%";
        }

        $this->sql->where .= ' AND '.join(' AND ', $wheres);
        $this->sql->params += $params;
    }

    /**
     * If there is a filter to find users who can/cannot accept on their own behalf add it to the SQL query
     */
    protected function build_sql_for_capability_filter() {
        global $CFG;
        $hascapability = $this->acceptancesfilter->get_capability_accept_filter();
        if ($hascapability === null) {
            return;
        }

        list($neededroles, $forbiddenroles) = get_roles_with_cap_in_context(\context_system::instance(), 'tool/policy:accept');

        if (empty($neededroles)) {
            // There are no roles that allow to accept agreement on one own's behalf.
            $this->sql->where .= $hascapability ? ' AND 1=0' : '';
            return;
        }

        if (empty($forbiddenroles)) {
            // There are no roles that prohibit to accept agreement on one own's behalf.
            $this->sql->where .= ' AND ' . $this->sql_has_role($neededroles, $hascapability);
            return;
        }

        $defaultuserroleid = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
        if (!empty($neededroles[$defaultuserroleid])) {
            // Default role allows to accept agreement. Make sure user has/does not have one of the roles prohibiting it.
            $this->sql->where .= ' AND ' . $this->sql_has_role($forbiddenroles, !$hascapability);
            return;
        }

        if ($hascapability) {
            // User has at least one role allowing to accept and no roles prohibiting.
            $this->sql->where .= ' AND ' . $this->sql_has_role($neededroles);
            $this->sql->where .= ' AND ' . $this->sql_has_role($forbiddenroles, false);
        } else {
            // Option 1: User has one of the roles prohibiting to accept.
            $this->sql->where .= ' AND (' . $this->sql_has_role($forbiddenroles);
            // Option 2: User has none of the roles allowing to accept.
            $this->sql->where .= ' OR ' . $this->sql_has_role($neededroles, false) . ")";
        }
    }

    /**
     * Returns SQL snippet for users that have (do not have) one of the given roles in the system context
     *
     * @param array $roles list of roles indexed by role id
     * @param bool $positive true: return users who HAVE roles; false: return users who DO NOT HAVE roles
     * @return string
     */
    protected function sql_has_role($roles, $positive = true) {
        global $CFG;
        if (empty($roles)) {
            return $positive ? '1=0' : '1=1';
        }
        $defaultuserroleid = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
        if (!empty($roles[$defaultuserroleid])) {
            // No need to query, everybody has the default role.
            return $positive ? '1=1' : '1=0';
        }
        return "u.id " . ($positive ? "" : "NOT") . " IN (
                SELECT userid
                FROM {role_assignments}
                WHERE contextid = " . SYSCONTEXTID . " AND roleid IN (" . implode(',', array_keys($roles)) . ")
            )";
    }

    /**
     * If there is a filter by user roles add it to the SQL query.
     */
    protected function build_sql_for_roles_filter() {
        foreach ($this->acceptancesfilter->get_role_filters() as $roleid) {
            $this->sql->where .= ' AND ' . $this->sql_has_role([$roleid => $roleid]);
        }
    }

    /**
     * Hook that can be overridden in child classes to wrap a table in a form
     * for example. Called only when there is data to display and not
     * downloading.
     */
    public function wrap_html_start() {
        echo \html_writer::start_tag('form',
            ['action' => new \moodle_url('/admin/tool/policy/accept.php')]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'returnurl',
            'value' => $this->get_return_url()]);
        foreach (array_keys($this->versionids) as $versionid) {
            echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'versionids[]',
                'value' => $versionid]);
        }
    }

    /**
     * Hook that can be overridden in child classes to wrap a table in a form
     * for example. Called only when there is data to display and not
     * downloading.
     */
    public function wrap_html_finish() {
        global $PAGE;
        if ($this->canagreeany) {
            echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'accept']);
            echo \html_writer::empty_tag('input', ['type' => 'submit', 'data-action' => 'acceptmodal',
                'value' => get_string('consentbulk', 'tool_policy'), 'class' => 'btn btn-primary mt-1']);
            $PAGE->requires->js_call_amd('tool_policy/acceptmodal', 'getInstance', [\context_system::instance()->id]);
        }
        echo "</form>\n";
    }

    /**
     * Render the table.
     */
    public function display() {
        $this->out(100, true);
    }

    /**
     * Call appropriate methods on this table class to perform any processing on values before displaying in table.
     * Takes raw data from the database and process it into human readable format, perhaps also adding html linking when
     * displaying table as html, adding a div wrap, etc.
     *
     * See for example col_fullname below which will be called for a column whose name is 'fullname'.
     *
     * @param array|object $row row of data from db used to make one row of the table.
     * @return array one row for the table, added using add_data_keyed method.
     */
    public function format_row($row) {
        \context_helper::preload_from_record($row);
        $row->canaccept = false;
        $row->user = \user_picture::unalias($row, [], $this->useridfield);
        $row->select = null;
        if (!$this->is_downloading()) {
            if (has_capability('tool/policy:acceptbehalf', \context_system::instance()) ||
                has_capability('tool/policy:acceptbehalf', \context_user::instance($row->id))) {
                $row->canaccept = true;
                $row->select = \html_writer::empty_tag('input',
                    ['type' => 'checkbox', 'name' => 'userids[]', 'value' => $row->id, 'class' => 'usercheckbox',
                    'id' => 'selectuser' . $row->id]) .
                \html_writer::tag('label', get_string('selectuser', 'tool_policy', $this->username($row->user, false)),
                    ['for' => 'selectuser' . $row->id, 'class' => 'accesshide']);
                $this->canagreeany = true;
            }
        }
        return parent::format_row($row);
    }

    /**
     * Get the column fullname value.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_fullname($row) {
        global $OUTPUT;
        $userpic = $this->is_downloading() ? '' : $OUTPUT->user_picture($row->user);
        return $userpic . $this->username($row->user, true);
    }

    /**
     * User name with a link to profile
     *
     * @param stdClass $user
     * @param bool $profilelink show link to profile (when we are downloading never show links)
     * @return string
     */
    protected function username($user, $profilelink = true) {
        $canviewfullnames = has_capability('moodle/site:viewfullnames', \context_system::instance()) ||
            has_capability('moodle/site:viewfullnames', \context_user::instance($user->id));
        $name = fullname($user, $canviewfullnames);
        if (!$this->is_downloading() && $profilelink) {
            $profileurl = new \moodle_url('/user/profile.php', array('id' => $user->id));
            return \html_writer::link($profileurl, $name);
        }
        return $name;
    }

    /**
     * Helper.
     */
    protected function get_return_url() {
        $pageurl = $this->baseurl;
        if ($this->currpage) {
            $pageurl = new \moodle_url($pageurl, [$this->request[TABLE_VAR_PAGE] => $this->currpage]);
        }
        return $pageurl;
    }

    /**
     * Return agreement status
     *
     * @param int $versionid either id of an individual version or empty for overall status
     * @param stdClass $row
     * @return string
     */
    protected function status($versionid, $row) {
        $onbehalf = false;
        $versions = $versionid ? [$versionid => $this->versionids[$versionid]] : $this->versionids; // List of versions.
        $accepted = []; // List of versionids that user has accepted.
        $declined = [];

        foreach ($versions as $v => $name) {
            if ($row->{'status' . $v} !== null) {
                if (empty($row->{'status' . $v})) {
                    $declined[] = $v;
                } else {
                    $accepted[] = $v;
                }
                $agreedby = $row->{'usermodified' . $v};
                if ($agreedby && $agreedby != $row->id) {
                    $onbehalf = true;
                }
            }
        }

        $ua = new user_agreement($row->id, $accepted, $declined, $this->get_return_url(), $versions, $onbehalf, $row->canaccept);

        if ($this->is_downloading()) {
            return $ua->export_for_download();

        } else {
            return $this->output->render($ua);
        }
    }

    /**
     * Get the column timemodified value.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_timemodified($row) {
        if ($row->timemodified) {
            if ($this->is_downloading()) {
                // Use timestamp format readable for both machines and humans.
                return date_format_string($row->timemodified, '%Y-%m-%d %H:%M:%S %Z');
            } else {
                // Use localised calendar format.
                return userdate($row->timemodified, get_string('strftimedatetime'));
            }
        } else {
            return null;
        }
    }

    /**
     * Get the column note value.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_note($row) {
        if ($this->is_downloading()) {
            return $row->note;
        } else {
            return format_text($row->note, FORMAT_MOODLE);
        }
    }

    /**
     * Get the column statusall value.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_statusall($row) {
        return $this->status(0, $row);
    }

    /**
     * Generate the country column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_country($data) {
        if ($data->country && $this->countries === null) {
            $this->countries = get_string_manager()->get_list_of_countries();
        }
        if (!empty($this->countries[$data->country])) {
            return $this->countries[$data->country];
        }
        return '';
    }

    /**
     * You can override this method in a child class. See the description of
     * build_table which calls this method.
     *
     * @param string $column
     * @param stdClass $row
     * @return string
     */
    public function other_cols($column, $row) {
        if (preg_match('/^status([\d]+)$/', $column, $matches)) {
            $versionid = $matches[1];
            return $this->status($versionid, $row);
        }
        if (preg_match('/^usermodified([\d]+)$/', $column, $matches)) {
            if ($row->$column && $row->$column != $row->id) {
                $user = (object)['id' => $row->$column];
                username_load_fields_from_object($user, $row, 'mod');
                return $this->username($user, true);
            }
            return ''; // User agreed by themselves.
        }
        return null;
    }
}