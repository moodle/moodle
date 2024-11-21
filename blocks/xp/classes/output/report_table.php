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
 * Block XP report table.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use action_menu_link;
use context_course;
use context_helper;
use moodle_database;
use moodle_url;
use pix_icon;
use renderer_base;
use stdClass;
use table_sql;
use block_xp\di;
use block_xp\local\course_world;
use block_xp\local\permission\access_logs_permissions;
use block_xp\local\routing\url_resolver;
use block_xp\local\utils\user_utils;
use block_xp\local\xp\course_user_state_store;
use block_xp\local\xp\state_with_subject;

/**
 * Block XP report table class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends table_sql {

    /** @var moodle_database The DB. */
    protected $db;
    /** @var \block_xp\local\course_world The world. */
    protected $world = null;
    /** @var \block_xp\local\xp\course_user_state_store The store. */
    protected $store = null;
    /** @var access_logs_permissions|null The log access permissions. */
    protected $logaccessperms = null;
    /** @var renderer_base The renderer. */
    protected $renderer = null;
    /** @var url_resolver The URL resolver. */
    protected $urlresolver;
    /** @var int The groupd ID. */
    protected $groupid = null;
    /** @var array The columns definition where keys are IDs, values are lang strings. */
    protected $columnsdefinition;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param course_world $world The world.
     * @param renderer_base $renderer The renderer.
     * @param course_user_state_store $store The store.
     * @param int $groupid The group ID.
     */
    public function __construct(
            moodle_database $db,
            course_world $world,
            renderer_base $renderer,
            course_user_state_store $store,
            $groupid
        ) {

        parent::__construct('block_xp_report');

        $this->db = $db;
        $this->groupid = $groupid;
        $this->world = $world;
        $this->renderer = $renderer;
        $this->store = $store;
        $this->urlresolver = di::get('url_resolver');

        $accessperms = $this->world->get_access_permissions();
        if ($accessperms instanceof access_logs_permissions) {
            $this->logaccessperms = $accessperms;
        }

        // Init the stuff.
        $this->init();
    }

    /**
     * Init function.
     *
     * @return void
     */
    protected function init() {
        $columnsdef = $this->get_columns_definition();
        $this->define_columns(array_keys($columnsdef));
        $this->define_headers(array_values($columnsdef));

        // Level sorting is a fake column sorting that uses the 'xp' column under the hood.
        $this->sortable(true, 'lvl', SORT_DESC);
        $this->no_sorting('userpic');
        $this->no_sorting('progress');
        $this->collapsible(false);
        $this->set_attribute('class', 'block_xp-report-table');
    }

    /**
     * Initialise the SQL bits.
     *
     * @return void
     */
    protected function init_sql() {
        $courseid = $this->world->get_courseid();
        $context = context_course::instance($courseid);
        $groupid = $this->groupid;

        // Get all the users that are enrolled and can earn XP.
        $ids = [];
        $users = get_enrolled_users($context, 'block/xp:earnxp', $groupid);
        foreach ($users as $user) {
            $ids[$user->id] = $user->id;
        }
        unset($users);

        // Get the users which might not be enrolled or are revoked the permission, but still should
        // be displayed in the report for the teachers' benefit. We need to filter out the users which
        // are not a member of the group though.
        if (empty($groupid)) {
            $sql = 'SELECT userid FROM {block_xp} WHERE courseid = :courseid';
            $params = ['courseid' => $courseid];
        } else {
            $sql = 'SELECT b.userid
                      FROM {block_xp} b
                      JOIN {groups_members} gm
                        ON b.userid = gm.userid
                       AND gm.groupid = :groupid
                     WHERE courseid = :courseid';
            $params = ['courseid' => $courseid, 'groupid' => $groupid];
        }

        $entries = $this->db->get_recordset_sql($sql, $params);
        foreach ($entries as $entry) {
            $ids[$entry->userid] = $entry->userid;
        }
        $entries->close();
        list($insql, $inparams) = $this->db->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'param', true, null);

        // User filter.
        [$usersql, $userparams] = $this->generate_user_filter_sql();

        // Define SQL.
        $this->sql = new stdClass();
        $this->sql->fields = user_utils::picture_fields('u') . ', u.idnumber, u.email, u.username, u.suspended, x.xp, ' .
            context_helper::get_preload_record_columns_sql('ctx');
        $this->sql->from = "{user} u
                       JOIN {context} ctx
                         ON ctx.instanceid = u.id
                        AND ctx.contextlevel = :contextlevel
                  LEFT JOIN {block_xp} x
                         ON (x.userid = u.id AND x.courseid = :courseid)";
        $this->sql->where = "u.deleted = 0 AND u.id $insql AND $usersql";
        $this->sql->params = array_merge($inparams, $userparams, [
            'courseid' => $courseid,
            'contextlevel' => CONTEXT_USER,
        ]);
    }

    /**
     * Generate the columns definition.
     *
     * @return array
     */
    protected function generate_columns_definition() {
        $cols = [
            'userpic' => '',
            'fullname' => get_string('fullname', 'core'),
            'lvl' => get_string('level', 'block_xp'),
            'xp' => get_string('total', 'block_xp'),
            'progress' => get_string('progress', 'block_xp'),
        ];
        if ($this->world->get_access_permissions()->can_manage()) {
            $cols['actions'] = '';
        }
        return $cols;
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
     * Get the columns definition.
     *
     * @return array
     */
    final protected function get_columns_definition() {
        if (!isset($this->columnsdefinition)) {
            $this->columnsdefinition = $this->generate_columns_definition();
        }
        return $this->columnsdefinition;
    }

    /**
     * Get the columns.
     *
     * @return array
     * @deprecated Since Level Up XP 3.12, please use self::get_columns_definition instead.
     */
    protected function get_columns() {
        return array_keys($this->get_columns_definition());
    }

    /**
     * Get the headers.
     *
     * @return void
     * @deprecated Since Level Up XP 3.12, please use self::get_columns_definition instead.
     */
    protected function get_headers() {
        return array_map(function($header) {
            return (string) $header;
        }, array_values($this->get_columns_definition()));
    }

    /**
     * Override to add states.
     *
     * @return void
     */
    public function build_table() {
        if (!$this->rawdata) {
            return;
        }

        foreach ($this->rawdata as $row) {
            $row->state = $this->make_state_from_record($row);
            $row->lvl = $row->state->get_level()->get_level();

            $formattedrow = $this->format_row($row);
            $this->add_data_keyed($formattedrow,
                $this->get_row_class($row));
        }
    }

    /**
     * Get the actions for row.
     *
     * @param stdClass $row Table row.
     * @return action_menu_link[] List of actions.
     */
    protected function get_row_actions($row) {
        $actions = [];

        $actions[] = new action_menu_link(
            $this->baseurl,
            new pix_icon('t/edit', get_string('edit', 'core')),
            get_string('edit', 'core'),
            false,
            [
                'data-action' => 'open-form',
                'data-form-class' => 'block_xp\form\user_xp',
                'data-form-args__contextid' => $this->world->get_context()->id,
                'data-form-args__userid' => $row->id,
                'data-modal-title' => get_string('edita', 'core', fullname($row)),
            ]
        );

        if ($this->logaccessperms && $this->logaccessperms->can_access_logs()) {
            $url = $this->urlresolver->reverse('log', ['courseid' => $this->world->get_courseid()]);
            $url->param('userid', $row->id);
            $actions[] = new action_menu_link($url, new pix_icon('t/log', get_string('logs', 'core')),
                get_string('viewlogs', 'block_xp'));
        }

        if (isset($row->xp)) {
            $url = new moodle_url($this->baseurl, ['action' => '', 'delete' => 1, 'userid' => $row->id]);
            $action = new action_menu_link($url, new pix_icon('t/delete', get_string('delete', 'core')),
                get_string('delete', 'core'));
            $action->add_class('text-danger');
            $actions[] = $action;
        }

        return $actions;
    }

    /**
     * Formats the column actions.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_actions($row) {
        $actions = $this->get_row_actions($row);
        if (empty($actions)) {
            return '';
        }
        return $this->renderer->control_menu($this->get_row_actions($row));
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_fullname($row) {
        $o = parent::col_fullname($row);
        if ($row->suspended) {
            $o .= ' (' . get_string('suspended', 'core') . ')';
        }
        return $o;
    }

    /**
     * Formats the column level.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_lvl($row) {
        return isset($row->xp) ? $row->lvl : '-';
    }

    /**
     * Formats the column progress.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_progress($row) {
        return $this->renderer->progress_bar($row->state);
    }

    /**
     * Formats the column XP.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_xp($row) {
        return isset($row->xp) ? $this->renderer->xp($row->xp) : '-';
    }

    /**
     * Formats the column userpic.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_userpic($row) {
        $picture = null;
        $link = null;
        if ($row->state instanceof state_with_subject) {
            $picture = $row->state->get_picture();
            $link = $row->state->get_link();
        }
        return $this->renderer->user_avatar($picture, $link);
    }

    /**
     * Make state from record.
     *
     * @param stdClass $row Table row.
     * @return user_state
     */
    protected function make_state_from_record($row) {
        return $this->store->make_state_from_record($row, 'id');
    }

    /**
     * Construct the ORDER BY clause.
     *
     * We override this to ensure that XP set to null appears at the bottom, not the top.
     *
     * @param array $cols The columns.
     * @param array $textsortcols The text columns.
     * @return string
     */
    public static function construct_order_by($cols, $textsortcols = []) {
        $newcols = [];

        // We use a foreach to maintain the order in which the fields were defined.
        foreach ($cols as $field => $sortorder) {
            if ($field === 'xp' || $field === 'lvl') {
                $field = 'COALESCE(xp, 0)';
            }
            $newcols[$field] = $sortorder;
        }

        return parent::construct_order_by($newcols, $textsortcols);
    }

    /**
     * Get the columns to sort by.
     *
     * @return array column name => SORT_... constant.
     */
    public function get_sort_columns() {
        $orderby = parent::get_sort_columns();

        // It should never be empty, but if it is then never mind...
        if (!empty($orderby)) {

            // If we are sorting by lvl, remove the xp column as we treat them as alises.
            if (array_key_exists('lvl', $orderby)) {
                unset($orderby['xp']);
            }

            // Always add the user ID, to avoid random ordering.
            if (!array_key_exists('id', $orderby)) {
                $orderby['id'] = SORT_ASC;
            }
        }

        return $orderby;
    }

    /**
     * Get SQL sort.
     *
     * Must be overridden because otherwise it calls the parent 'construct_order_by()'.
     *
     * @return string
     */
    public function get_sql_sort() {
        return static::construct_order_by($this->get_sort_columns(), []);
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
        $issite = di::get('config')->get('context') == CONTEXT_SYSTEM && $this->world->get_courseid() == SITEID;
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

        $message = get_string($issite ? 'reportisempty' : 'reportisemptyenrolstudents', 'block_xp');
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
