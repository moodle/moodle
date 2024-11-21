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
 * Block XP log table.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use stdClass;
use table_sql;
use block_xp\local\course_world;
use block_xp\local\utils\user_utils;
use html_writer;
use moodle_url;
use pix_icon;

/**
 * Block XP log table class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_table extends table_sql {

    /** @var string The key of the user ID column. */
    public $useridfield = 'userid';
    /** @var \moodle_database DB. */
    protected $db;
    /** @var course_world The world. */
    protected $world;
    /** @var renderer_base The renderer. */
    protected $renderer;
    /** @var int Filter by user ID, falsy means not filtering. */
    protected $filterbyuserid;
    /** @var int The group ID. */
    protected $groupid;

    /**
     * Constructor.
     *
     * @param course_world $world The world.
     * @param int $groupid The group ID.
     * @param int|null $userid The user ID.
     */
    public function __construct(course_world $world, $groupid, $userid = null) {
        $userid = max(0, (int) $userid);
        parent::__construct('block_xp_log_' . $userid);

        $this->world = $world;
        $this->renderer = \block_xp\di::get('renderer');
        $this->db = \block_xp\di::get('db');
        $this->filterbyuserid = $userid;

        // Define columns.
        $this->define_columns([
            'time',
            'fullname',
            'xp',
            'eventname',
        ]);
        $this->define_headers([
            get_string('eventtime', 'block_xp'),
            get_string('fullname'),
            get_string('reward', 'block_xp'),
            get_string('eventname', 'block_xp'),
        ]);

        // Define various table settings.
        $this->sortable(true, 'time', SORT_DESC);
        $this->collapsible(false);
    }

    /**
     * Init SQL.
     */
    protected function init_sql() {
        $groupid = $this->groupid;
        $courseid = $this->world->get_courseid();

        // Define SQL.
        $sqlfrom = '';
        $sqlparams = [];
        if ($groupid) {
            $sqlfrom = '{block_xp_log} x
                     JOIN {groups_members} gm
                       ON gm.groupid = :groupid
                      AND gm.userid = x.userid
                LEFT JOIN {user} u
                       ON x.userid = u.id';
            $sqlparams = ['groupid' => $groupid];
        } else {
            $sqlfrom = '{block_xp_log} x LEFT JOIN {user} u ON x.userid = u.id';
        }

        // User filter.
        [$usersql, $userparams] = $this->generate_user_filter_sql();

        // Define SQL.
        $this->sql = new stdClass();
        $this->sql->fields = 'x.*, ' . user_utils::name_fields('u') . ', u.suspended';
        $this->sql->from = $sqlfrom;
        $this->sql->where = "u.deleted = 0 AND x.courseid = :courseid AND $usersql";
        $this->sql->params = array_merge(['courseid' => $courseid], $userparams, $sqlparams);
        if ($this->filterbyuserid) {
            $this->sql->where .= ' AND x.userid = :userid';
            $this->sql->params = array_merge($this->sql->params, ['userid' => $this->filterbyuserid]);
        }
    }

    /**
     * Formats the column time.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_fullname($row) {
        $o = parent::col_fullname($row);
        if ($row->suspended) {
            $o .= ' (' . get_string('suspended', 'core') . ')';
        }
        if (!$this->filterbyuserid) {
            $o .= ' ' . $this->renderer->action_icon(
                new moodle_url($this->baseurl, ['userid' => $row->userid]),
                new pix_icon('i/search', get_string('filterbyuser', 'block_xp'))
            );
        }
        return $o;
    }

    /**
     * Formats the column time.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_time($row) {
        return userdate($row->time);
    }

    /**
     * XP.
     *
     * @param stdClass $row The row.
     * @return string
     */
    protected function col_xp($row) {
        return $this->renderer->xp($row->xp);
    }

    /**
     * Generate the user filter SQL.
     *
     * @return array
     */
    protected function generate_user_filter_sql() {
        $filterset = $this->get_filterset();
        if (!$filterset || !$filterset->has_filter('term')) {
            return ['1=1', []];
        }

        $term = trim($filterset->get_filter('term')->current());
        if (empty($term)) {
            return ['1=1', []];
        }

        $wheres = [];
        $params = [];

        $nameoptions = [
            ['firstname' => $term],
            ['lastname' => $term],
        ];
        $nameparts = explode(' ', $term);
        if (count($nameparts) > 1) {
            for ($i = 0; $i < count($nameparts) - 1; $i++) {
                $nameoptions[] = [
                    'firstname' => implode(' ', array_slice($nameparts, 0, $i + 1)),
                    'lastname' => implode(' ', array_slice($nameparts, $i + 1)),
                ];
            }
        }
        foreach ($nameoptions as $i => $option) {
            $subparams = [];
            $subsql = [];
            if (!empty($option['firstname'])) {
                $paramname = 'usertermfn' . $i;
                $subsql[] = $this->db->sql_like("u.firstname", ':' . $paramname, false, false);
                $subparams[$paramname] = $this->db->sql_like_escape($option['firstname']) . '%';
            }
            if (!empty($option['lastname'])) {
                $paramname = 'usertermln' . $i;
                $subsql[] = $this->db->sql_like("u.lastname", ':' . $paramname, false, false);
                $subparams[$paramname] = $this->db->sql_like_escape($option['lastname']) . '%';
            }
            if (!empty($subsql)) {
                $wheres[] = '(' . implode(' AND ', $subsql) . ')';
                $params = array_merge($params, $subparams);
            }
        }

        if (empty($wheres)) {
            return ['1=1', []];
        }

        return ['((' . implode(') OR (', $wheres) . '))', $params];
    }

    /**
     * Out.
     *
     * @param int $pagesize The page size.
     * @param bool $initialbars Whether to use initial bars.
     * @param string $downloadhelpbutton What is this?
     */
    public function out($pagesize, $initialbars, $downloadhelpbutton = '') {
        $this->init_sql();
        return parent::out($pagesize, $initialbars, $downloadhelpbutton);
    }

    /**
     * Override to rephrase.
     *
     * @return void
     */
    public function print_nothing_to_display() {
        $hasfilters = false;
        $showfilters = false;

        if ($this->can_be_reset()) {
            $hasfilters = true;
            $showfilters = true;
        }

        // Render button to allow user to reset table preferences, and the initial bars if some filters
        // are used. If none of the filters are used and there is nothing to display it just means that
        // the course is empty and thus we do not show anything but a message.
        echo $this->render_reset_button();
        if ($showfilters) {
            $this->print_initials_bar();
        }

        $message = get_string('nologsrecordedyet', 'block_xp');
        if ($hasfilters) {
            $message = get_string('nothingtodisplay', 'core');
        }

        echo \html_writer::div(
            \block_xp\di::get('renderer')->notification_without_close($message, 'info'),
            '',
            ['style' => 'margin: 1em 0']
        );
    }
}
