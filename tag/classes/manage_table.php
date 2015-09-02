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
 * Contains class core_tag_manage_table
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class core_tag_manage_table
 *
 * @package   core
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_manage_table extends table_sql {

    /** @var int stores the total number of found tags */
    public $totalcount = null;

    /**
     * Constructor
     */
    public function __construct() {
        global $USER, $CFG, $PAGE;
        parent::__construct('tag-management-list-'.$USER->id);

        $perpage = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/tag/manage.php', array('perpage' => $perpage, 'page' => $page));

        $tablecolumns = array('select', 'name', 'fullname', 'count', 'flag', 'timemodified', 'tagtype', 'controls');
        $tableheaders = array(get_string('select', 'tag'),
                              get_string('name', 'tag'),
                              get_string('owner', 'tag'),
                              get_string('count', 'tag'),
                              get_string('flag', 'tag'),
                              get_string('timemodified', 'tag'),
                              get_string('officialtag', 'tag'),
                              '');

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);
        $this->define_baseurl($baseurl);

        $this->column_class('select', 'mdl-align col-select');
        $this->column_class('name', 'col-name');
        $this->column_class('owner', 'col-owner');
        $this->column_class('count', 'mdl-align col-count');
        $this->column_class('flag', 'mdl-align col-flag');
        $this->column_class('timemodified', 'col-timemodified');
        $this->column_class('tagtype', 'mdl-align col-tagtype');
        $this->column_class('controls', 'mdl-align col-controls');

        $this->sortable(true, 'flag', SORT_DESC);
        $this->no_sorting('select');
        $this->no_sorting('controls');

        $this->set_attribute('cellspacing', '0');
        $this->set_attribute('id', 'tag-management-list');
        $this->set_attribute('class', 'admintable generaltable tag-management-table');

        $totalcount = "SELECT COUNT(id) FROM {tag}";
        $params = array();

        $this->set_count_sql($totalcount, $params);

        $this->set_sql('', '', '', $params);

        $this->collapsible(true);

        $PAGE->requires->js_call_amd('core/tag', 'init_manage_page', array());

    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;
        $where = '';
        if (!$this->is_downloading()) {
            $grandtotal = $DB->count_records_sql($this->countsql, $this->countparams);

            list($wsql, $wparams) = $this->get_sql_where();
            if ($wsql) {
                $this->countsql .= ' AND '.$wsql;
                $this->countparams = array_merge($this->countparams, $wparams);

                $where .= ' AND '.$wsql;
                $this->sql->params = array_merge($this->sql->params, $wparams);

                $total  = $DB->count_records_sql($this->countsql, $this->countparams);
            } else {
                $total = $grandtotal;
            }

            $this->pagesize(min($pagesize, $total), $total);
            $this->totalcount = $total;
        }

        // Fetch the attempts.
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort .= ", tg.name";
        } else {
            $sort = "tg.name";
        }

        $allusernames = get_all_user_name_fields(true, 'u');
        $sql = "
            SELECT tg.id, tg.name, tg.rawname, tg.tagtype, tg.flag, tg.timemodified,
                       u.id AS owner, $allusernames,
                       COUNT(ti.id) AS count
            FROM {tag} tg
            LEFT JOIN {tag_instance} ti ON ti.tagid = tg.id
            LEFT JOIN {user} u ON u.id = tg.userid
                       WHERE 1 = 1 $where
            GROUP BY tg.id, tg.name, tg.rawname, tg.tagtype, tg.flag, tg.timemodified,
                       u.id, $allusernames
            ORDER BY $sort";

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }

    /**
     * Get any extra classes names to add to this row in the HTML
     *
     * @param stdClass $row array the data for this row.
     * @return string added to the class="" attribute of the tr.
     */
    public function get_row_class($row) {
        return $row->flag ? 'flagged-tag' : '';
    }

    /**
     * Column name
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_name($tag) {
        global $OUTPUT;
        $tagoutput = new core_tag\output\tag($tag);
        return $OUTPUT->render_from_template('core_tag/tagname', $tagoutput->export_for_template($OUTPUT));
    }

    /**
     * Column flag
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_flag($tag) {
        global $OUTPUT;
        $tagoutput = new core_tag\output\tag($tag);
        return $OUTPUT->render_from_template('core_tag/tagflag', $tagoutput->export_for_template($OUTPUT));
    }

    /**
     * Column fullname (user name)
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_fullname($tag) {
        $params         = array('id' => $tag->owner);
        $ownerlink      = new moodle_url('/user/view.php', $params);
        $owner          = html_writer::link($ownerlink, fullname($tag));
        return $owner;
    }

    /**
     * Column time modified
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_timemodified($tag) {
        return format_time(time() - $tag->timemodified);
    }

    /**
     * Column tag type
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_tagtype($tag) {
        global $OUTPUT;
        $tagoutput = new core_tag\output\tag($tag);
        return $OUTPUT->render_from_template('core_tag/tagtype', $tagoutput->export_for_template($OUTPUT));
    }

    /**
     * Column select
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_select($tag) {
        $id = "tagselect" . $tag->id;
        return html_writer::label(get_string('selecttag', 'tag', $tag->rawname), $id,
                false, array('class' => 'accesshide')).
                html_writer::empty_tag('input', array('type' => 'checkbox',
                'name' => 'tagschecked[]', 'value' => $tag->id, 'id' => $id));
    }

    /**
     * Column controls
     *
     * @param stdClass $tag
     * @return string
     */
    public function col_controls($tag) {
        global $OUTPUT, $PAGE;
        $o = '';
        // Edit.
        $url = new moodle_url('/tag/edit.php', array('id' => $tag->id, 'returnurl' => $PAGE->url->out_as_local_url()));
        $o .= $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('edittag', 'tag')));
        // Delete.
        $url = new moodle_url($this->baseurl, array('action' => 'delete',
            'tagid' => $tag->id, 'sesskey' => sesskey()));
        $o .= $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete', 'tag')),
                null, array('class' => 'action-icon tagdelete'));
        return $o;
    }
}
