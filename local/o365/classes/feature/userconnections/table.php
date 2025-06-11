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
 * Search results table.
 *
 * @package local_o365
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 onwards Remote-Learner Inc (http://www.remote-learner.net)
 */

namespace local_o365\feature\userconnections;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/tablelib.php');

/**
 * Search results table.
 */
class table extends \table_sql {
    /**
     * @var object|null
     */
    protected $having = null;

    /**
     * Constructor.
     *
     * @param int $uniqueid This is a unique string used as a key when storing table properties in the session.
     */
    public function __construct($uniqueid) {
        global $USER, $DB;
        parent::__construct($uniqueid);
        $this->sql = new \stdClass;
        $this->set_columns();
        $this->having = (object)['sql' => '', 'params' => []];
    }

    /**
     * Set the table's columns.
     */
    public function set_columns() {
        $columns = [
            'userlastname' => get_string('acp_userconnections_column_muser', 'local_o365'),
            'o365username' => get_string('acp_userconnections_column_o365user', 'local_o365'),
            'usinglogin' => get_string('acp_userconnections_column_usinglogin', 'local_o365'),
            'status' => get_string('acp_userconnections_column_status', 'local_o365'),
            'actions' => get_string('acp_userconnections_column_actions', 'local_o365'),
        ];
        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));
        $this->sortable(true, 'userlastname', SORT_ASC);
        $this->no_sorting('status');
        $this->no_sorting('actions');
        $this->no_sorting('usinglogin');
    }

    /**
     * Set custom "where" Sql. Useful for filtering.
     *
     * @param string $sql The SQL snippet.
     * @param array $params Parameters used in the SQL snippet.
     */
    public function set_where($sql, $params) {
        $sql = preg_replace('#\:ex\_text[0-9]+#', '?', $sql);
        $this->where = (object)[
            'sql' => $sql,
            'params' => array_values($params),
        ];
    }

    /**
     * Set custom "having" Sql. Useful for filtering.
     *
     * @param string $sql The SQL snippet.
     * @param array $params Parameters used in the SQL snippet.
     */
    public function set_having($sql, $params) {
        $sql = preg_replace('#\:ex\_text[0-9]+#', '?', $sql);
        $this->having = (object)[
            'sql' => $sql,
            'params' => array_values($params),
        ];
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize The amount of results per page.
     * @param bool $useinitialsbar Whether to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar=true) {
        global $DB;

        $customsql = $this->where->sql;
        $customparams = $this->where->params;
        $customhaving = $this->having->sql;
        $customhavingparams = $this->having->params;

        $columns = [
            'u.id AS userid',
            'u.firstname AS userfirstname',
            'u.firstnamephonetic AS userfirstnamephonetic',
            'u.lastname AS userlastname',
            'u.lastnamephonetic AS userlastnamephonetic',
            'u.middlename AS usermiddlename',
            'u.alternatename AS useralternatename',
            'u.auth AS userauth',
            'aotok.oidcusername AS toko365username',
            'o365match.entraidupn AS matchedo365username',
            'o365match.uselogin AS matcheduselogin',
            'objects.o365name AS objectso365name',
            'COALESCE(aotok.oidcusername, o365match.entraidupn, objects.o365name) AS o365username',
        ];
        $sql = 'SELECT ' . implode(',', $columns) . '
                  FROM {user} u
             LEFT JOIN {auth_oidc_token} aotok ON aotok.userid = u.id
             LEFT JOIN {local_o365_connections} o365match ON o365match.muserid = u.id
             LEFT JOIN {local_o365_objects} objects ON objects.moodleid = u.id AND type = ?
                 WHERE u.deleted = 0 AND u.username != ?';
        $params = ['user', 'guest'];

        if (!empty($customsql)) {
            $sql .= ' AND '.$customsql;
            $params = array_merge($params, $customparams);
        }

        if (!empty($customhaving)) {
            // Move the "HAVING" part to a sub-query because it causes error in PostgreSQL.
            $sql = "SELECT org.* FROM (" . $sql . ") AS org WHERE " . $customhaving;
            $params = array_merge($params, $customhavingparams);
        }

        $totalresults = $DB->count_records_sql('SELECT count(1) from ('.$sql.') a', $params);
        if ($useinitialsbar) {
            $this->initialbars($totalresults > $pagesize);
        }

        $this->pagesize($pagesize, $totalresults);

        // Sorting.
        $sort = $this->get_sql_sort();
        if (!empty($sort)) {
            $sort = 'ORDER BY '.$sort;
            $sql .= ' '.$sort;
        }

        $start = $this->get_page_start();
        $limit = $this->get_page_size();
        $this->rawdata = $DB->get_records_sql($sql, $params, $start, $limit);
    }

    /**
     * Process the usinglogin column.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return column value.
     */
    public function col_usinglogin($values) {
        if (!empty($values->toko365username) || !empty($values->objectso365name)) {
            // Actively connected or synced users.
            if (isset($values->userauth) && $values->userauth === 'oidc') {
                return get_string('yes');
            } else {
                return get_string('no');
            }
        } else {
            if (!empty($values->matchedo365username)) {
                return (!empty($values->matcheduselogin)) ? get_string('yes') : get_string('no');
            }
        }
        return '';
    }

    /**
     * Process the userlastname column.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return column value.
     */
    public function col_userlastname($values) {
        $userdata = [
            'firstname' => $values->userfirstname,
            'firstnamephonetic' => $values->userfirstnamephonetic,
            'lastname' => $values->userlastname,
            'lastnamephonetic' => $values->userlastnamephonetic,
            'middlename' => $values->usermiddlename,
            'alternatename' => $values->useralternatename,
        ];
        $fullname = fullname((object)$userdata);
        $viewurl = new \moodle_url('/user/view.php', ['id' => $values->userid]);
        return \html_writer::link($viewurl, $fullname);
    }

    /**
     * Process the o365username column.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return column value.
     * @return $string Return formated certificate issue date.
     */
    public function col_o365username($values) {
        return $values->o365username;
    }

    /**
     * Process the status column.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return column value.
     * @return $string Return formated certificate issue date.
     */
    public function col_status($values) {
        $statuscss = 'padding:0.25rem;display:block;';
        if (!empty($values->toko365username)) {
            $statusparams = ['class' => 'alert-success', 'style' => $statuscss];
            $label = get_string('acp_userconnections_table_connected', 'local_o365');
            return \html_writer::tag('span', $label, $statusparams);
        } else {
            if (!empty($values->matchedo365username)) {
                $statusparams = [
                    'class' => 'alert-info',
                    'style' => 'padding:0.25rem;display:block;color:#960;background-color:#fed;',
                ];
                $label = get_string('acp_userconnections_table_matched', 'local_o365');
                return \html_writer::tag('span', $label, $statusparams);
            } else {
                if (!empty($values->objectso365name)) {
                    $statusparams = ['class' => 'alert-info', 'style' => $statuscss];
                    $label = get_string('acp_userconnections_table_synced', 'local_o365');
                    return \html_writer::tag('span', $label, $statusparams);
                } else {
                    $statusparams = ['style' => 'font-style:italic;opacity:0.5'];
                    $label = get_string('acp_userconnections_table_noconnection', 'local_o365');
                    return \html_writer::tag('span', $label, $statusparams);
                }
            }
        }
    }

    /**
     * Process the actions column.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return column value.
     */
    public function col_actions($values) {
        $urlparams = [
            'userid' => $values->userid,
            'sesskey' => sesskey(),
        ];
        $links = [];
        if (!empty($values->toko365username)) {
            // Connected user.
            $urlparams['mode'] = 'userconnections_disconnect';
            $url = new \moodle_url('/local/o365/acp.php', $urlparams);
            $label = get_string('acp_userconnections_table_disconnect', 'local_o365');
            $links[] = \html_writer::link($url, $label);

            $urlparams['mode'] = 'userconnections_resync';
            $url = new \moodle_url('/local/o365/acp.php', $urlparams);
            $label = get_string('acp_userconnections_table_resync', 'local_o365');
            $links[] = \html_writer::link($url, $label, ['target' => '_blank']);
        } else {
            if (!empty($values->matchedo365username)) {
                // Matched, unconfirmed user.
                $urlparams['mode'] = 'userconnections_unmatch';
                $url = new \moodle_url('/local/o365/acp.php', $urlparams);
                $label = get_string('acp_userconnections_table_unmatch', 'local_o365');
                $links[] = \html_writer::link($url, $label);
            } else {
                if (!empty($values->objectso365name)) {
                    // This is a synced, uninitialized user.
                    $urlparams['mode'] = 'userconnections_resync';
                    $url = new \moodle_url('/local/o365/acp.php', $urlparams);
                    $label = get_string('acp_userconnections_table_resync', 'local_o365');
                    $links[] = \html_writer::link($url, $label, ['target' => '_blank']);
                } else {
                    // Unconnected, unmatched user.
                    $urlparams['mode'] = 'userconnections_manualmatch';
                    $url = new \moodle_url('/local/o365/acp.php', $urlparams);
                    $label = get_string('acp_userconnections_table_match', 'local_o365');
                    $links[] = \html_writer::link($url, $label);
                }
            }
        }
        return implode('<br />', $links);
    }

}
