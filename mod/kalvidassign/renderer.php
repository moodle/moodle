<?php
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
 * Kaltura video assignment renderer script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(__FILE__))).'/lib/tablelib.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/moodlelib.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

/**
 * Table class for displaying video submissions for grading
 */
class submissions_table extends table_sql {
    /* @var bool Set to true if a quick grade form needs to be rendered. */
    public $quickgrade;
    /* @var object An object returned from @see grade_get_grades(). */
    public $gradinginfo;
    /* @var int The course module instnace id. */
    public $cminstance;
    /* @var int The maximum grade point set for the activity instance. */
    public $grademax;
    /* @var int The number of columns of the quick grade textarea element. */
    public $cols = 20;
    /* @var int The number of rows of the quick grade textarea element. */
    public $rows = 4;
    /* @var string The first initial of the first name filter. */
    public $tifirst;
    /* @var string The first initial of the last name filter. */
    public $tilast;
    /* @var int The current page number. */
    public $page;

    /**
     * Constructor function for the submissions table class.
     * @param int $uniqueid Unique id.
     * @param int $cm Course module id.
     * @param object $gradinginfo An object returned from @see grade_get_grades().
     * @param bool $quickgrade Set to true if a quick grade form needs to be rendered.
     * @param string $tifirst The first initial of the first name filter.
     * @param string $tilast The first initial of the first name filter.
     * @param int $page The current page number.
     */
    public function __construct($uniqueid, $cm, $gradinginfo, $quickgrade = false, $tifirst = '', $tilast = '', $page = 0) {
        global $DB;

        parent::__construct($uniqueid);

        $this->quickgrade = $quickgrade;
        $this->gradinginfo = $gradinginfo;

        $instance = $DB->get_record('kalvidassign', array('id' => $cm->instance), 'id,grade');

        $instance->cmid = $cm->id;

        $this->cminstance = $instance;

        $this->grademax = $this->gradinginfo->items[0]->grademax;

        $this->tifirst      = $tifirst;
        $this->tilast       = $tilast;
        $this->page         = $page;
    }

    /**
     * The function renders the picture column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_picture($data) {
        global $OUTPUT;

        $user = new stdClass();
        $user->id = $data->id;
        $user->picture = $data->picture;
        $user->imagealt = $data->imagealt;
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;

        $output = $OUTPUT->user_picture($user);

        $attr = array('type' => 'hidden', 'name' => 'users['.$data->id.']', 'value' => $data->id);
        $output .= html_writer::empty_tag('input', $attr);

        return $output;
    }

    /**
     * The function renders the select grade column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_selectgrade($data) {
        global $CFG;

        $output      = '';
        $finalgrade = false;

        if (array_key_exists($data->id, $this->gradinginfo->items[0]->grades)) {

            $finalgrade = $this->gradinginfo->items[0]->grades[$data->id];

            if ($CFG->enableoutcomes) {

                $finalgrade->formatted_grade = $this->gradinginfo->items[0]->grades[$data->id]->str_grade;
            } else {

                // Equation taken from mod/assignment/lib.php display_submissions()
                $finalgrade->formatted_grade = round($finalgrade->grade, 2).' / '.round($this->grademax, 2);
            }
        }

        if (!is_bool($finalgrade) && ($finalgrade->locked || $finalgrade->overridden) ) {

            $locked_overridden = 'locked';

            if ($finalgrade->overridden) {
                $locked_overridden = 'overridden';
            }
            $attr = array('id' => 'g'.$data->id, 'class' => $locked_overridden);

            $output = html_writer::tag('div', $finalgrade->formatted_grade, $attr);

        } else if (!empty($this->quickgrade)) {

            $attributes = array();

            $grades_menu = make_grades_menu($this->cminstance->grade);

            $default = array(-1 => get_string('nograde'));

            $grade = null;

            if (!empty($data->timemarked)) {
                $grade = $data->grade;
            }

            $output = html_writer::select($grades_menu, 'menu['.$data->id.']', $grade, $default, $attributes);

        } else {

            $output = get_string('nograde');

            if (!empty($data->timemarked)) {
                $output = $this->display_grade($data->grade);
            }
        }

        return $output;
    }

    /**
     * The function renders the submissions comment column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_submissioncomment($data) {
        global $OUTPUT;

        $output     = '';
        $finalgrade = false;

        if (array_key_exists($data->id, $this->gradinginfo->items[0]->grades)) {
            $finalgrade = $this->gradinginfo->items[0]->grades[$data->id];
        }

        if ( (!is_bool($finalgrade) && ($finalgrade->locked || $finalgrade->overridden)) ) {

            $output = shorten_text(strip_tags($data->submissioncomment), 15);

        } else if (!empty($this->quickgrade)) {

            $param = array(
                'id' => 'comments_'.$data->submitid,
                'rows' => $this->rows,
                'cols' => $this->cols,
                'name' => 'submissioncomment['.$data->id.']');

            $output .= html_writer::start_tag('textarea', $param);
            $output .= $data->submissioncomment;
            $output .= html_writer::end_tag('textarea');

        } else {
            $output = shorten_text(strip_tags($data->submissioncomment), 15);
        }

        return $output;
    }

    /**
     * The function renders the grade marked column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_grademarked($data) {

        $output = '';

        if (!empty($data->timemarked)) {
            $output = userdate($data->timemarked);
        }

        return $output;
    }

    /**
     * The function renders the time modified column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_timemodified($data) {

        $data->source = local_kaltura_add_kaf_uri_token($data->source);
        $attr = array('name' => 'media_submission');
        $output = html_writer::start_tag('div', $attr);

        $attr = array('id' => 'ts'.$data->id);

        $date_modified = $data->timemodified;
        $date_modified = is_null($date_modified) || empty($data->timemodified) ? '' : userdate($date_modified);

        $output .= html_writer::tag('div', $date_modified, $attr);

        $output .= html_writer::empty_tag('br');

        // If the metadata property is empty only display an anchor tag.  Otherwise display a thumbnail image.
        if (!empty($data->entry_id)) {

            // Decode the additional video metadata.
            $metadata = local_kaltura_decode_object_for_storage($data->metadata);

            // Check if the metadata thumbnailurl property is empty.  If not then display the thumbnail.  Otherwise display a text link.
            if (!empty($metadata->thumbnailurl) && !is_null($metadata->thumbnailurl)) {

                $output .= html_writer::start_tag('center');
                $metadata = local_kaltura_decode_object_for_storage($data->metadata);

                $attr = array('src' => $metadata->thumbnailurl, 'class' => 'kalsubthumb');
                $thumbnail = html_writer::empty_tag('img', $attr);

                $attr = array('name' => 'submission_source', 'href' => local_kaltura_add_kaf_uri_token($data->source), 'class' => 'kalsubthumbanchor');
                $output .= html_writer::tag('a', $thumbnail, $attr);
                $output .= html_writer::end_tag('center');

            } else {

                $output .= html_writer::start_tag('center');
                $attr = array('name' => 'submission_source', 'href' => local_kaltura_add_kaf_uri_token($data->source), 'class' => 'kalsubanchor');
                $output .= html_writer::tag('a', get_string('viewsubmission', 'kalvidassign'), $attr);
                $output .= html_writer::end_tag('center');
            }
        }

        // Display hidden elements.
        if (!empty($data->entry_id)) {
            $attr = array('type' => 'hidden', 'name' => 'width', 'value' => $data->width);
            $output .= html_writer::empty_tag('input', $attr);

            $attr = array('type' => 'hidden', 'name' => 'height', 'value' => $data->height);
            $output .= html_writer::empty_tag('input', $attr);
        }

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * The function renders the grade column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_grade($data) {
        $finalgrade = false;

        if (array_key_exists($data->id, $this->gradinginfo->items[0]->grades)) {
            $finalgrade = $this->gradinginfo->items[0]->grades[$data->id];
        }

        $finalgrade = (!is_bool($finalgrade)) ? $finalgrade->str_grade : '-';

        $attr = array('id' => 'finalgrade_'.$data->id);
        $output = html_writer::tag('span', $finalgrade, $attr);

        return $output;
    }

    /**
     * The function renders the time marked column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_timemarked($data) {
        $output = '-';

        if (0 < $data->timemarked) {

                $attr = array('id' => 'tt'.$data->id);
                $output = html_writer::tag('div', userdate($data->timemarked), $attr);

        } else {
            $otuput = '-';
        }

        return $output;
    }

    /**
     * The function renders the submission status column.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function col_status($data) {
        global $OUTPUT, $CFG;

        require_once(dirname(dirname(dirname(__FILE__))).'/lib/weblib.php');

        $url = new moodle_url('/mod/kalvidassign/single_submission.php', array('cmid' => $this->cminstance->cmid, 'userid' => $data->id, 'sesskey' => sesskey()));

        if (!empty($this->tifirst)) {
            $url->param('tifirst', $this->tifirst);
        }

        if (!empty($this->tilast)) {
            $url->param('tilast', $this->tilast);
        }

        if (!empty($this->page)) {
            $url->param('page', $this->page);
        }

        $buttontext = '';
        if ($data->timemarked > 0) {
            $class = 's1';
            $buttontext = get_string('update');
        } else {
            $class = 's0';
            $buttontext  = get_string('grade');
        }

        $attr = array('id' => 'up'.$data->id,
                      'class' => $class);

        $output = html_writer::link($url, $buttontext, $attr);

        return $output;
    }

    /**
     *  Return a grade in user-friendly form, whether it's a scale or not
     *
     * @global object
     * @param mixed $grade
     * @return string User-friendly representation of grade
     *
     * TODO: Move this to locallib.php
     */
    public function display_grade($grade) {
        global $DB;

        // Cache scales for each assignment - they might have different scales!!
        static $kalscalegrades = array();

        // Normal number
        if ($this->cminstance->grade >= 0) {
            if ($grade == -1) {
                return '-';
            } else {
                return $grade.' / '.$this->cminstance->grade;
            }

        } else {
            // Scale
            if (empty($kalscalegrades[$this->cminstance->id])) {

                if ($scale = $DB->get_record('scale', array('id'=>-($this->cminstance->grade)))) {

                    $kalscalegrades[$this->cminstance->id] = make_menu_from_list($scale->scale);
                } else {

                    return '-';
                }
            }

            if (isset($kalscalegrades[$this->cminstance->id][$grade])) {
                return $kalscalegrades[$this->cminstance->id][$grade];
            }
            return '-';
        }
    }
}

/**
 * This class renders the submission pages.
 */
class mod_kalvidassign_renderer extends plugin_renderer_base {
    /**
     * The function displays information about the assignment settings.
     * @param object $data information about the current row being rendered.
     * @return string HTML markup.
     */
    public function display_mod_info($kalvideoobj, $context) {
        global $DB;
        $html = '';

        if (!empty($kalvideoobj->timeavailable)) {
            $html .= html_writer::start_tag('p');
            $html .= html_writer::tag('b', get_string('availabledate', 'kalvidassign').': ');
            $html .= userdate($kalvideoobj->timeavailable);
            $html .= html_writer::end_tag('p');
        }

        if (!empty($kalvideoobj->timedue)) {
            $html .= html_writer::start_tag('p');
            $html .= html_writer::tag('b', get_string('duedate', 'kalvidassign').': ');
            $html .= userdate($kalvideoobj->timedue);
            $html .= html_writer::end_tag('p');
        }

        // Display a count of the numuber of submissions
        if (has_capability('mod/kalvidassign:gradesubmission', $context)) {

            $param = array('vidassignid' => $kalvideoobj->id, 'timecreated' => 0, 'timemodified' => 0);

            $csql = "SELECT COUNT(*)
                      FROM {kalvidassign_submission}
                     WHERE vidassignid = :vidassignid
                           AND (timecreated > :timecreated OR timemodified > :timemodified) ";

            $count = $DB->count_records_sql($csql, $param);

            if ($count) {
                $html .= html_writer::start_tag('p');
                $html .= get_string('numberofsubmissions', 'kalvidassign', $count);
                $html .= html_writer::end_tag('p');
            }

        }

        return $html;
    }

    /**
     * This function returns HTML markup to render a form and submission buttons.
     * @param object $cm A course module object.
     * @param int $userid The current user id.
     * @param bool $disablesubmit Set to true to disable the submit button.
     * @return string Returns HTML markup.
     */
    public function display_student_submit_buttons($cm, $userid, $disablesubmit = false) {
        $html = '';

        $target = new moodle_url('/mod/kalvidassign/submission.php');

        $attr = array('method' => 'POST', 'action' => $target);

        $html .= html_writer::start_tag('form', $attr);

        $attr = array(
            'type' => 'hidden',
            'name' => 'entry_id',
            'id' => 'entry_id',
            'value' => ''
        );

        $html .= html_writer::empty_tag('input', $attr);

        $attr = array(
            'type' => 'hidden',
            'name' => 'cmid',
            'value' => $cm->id
        );
        $html .= html_writer::empty_tag('input', $attr);

        $attr = array(
            'type' => 'hidden',
            'name' => 'sesskey',
            'value' => sesskey()
        );
        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'width', 'name' => 'width', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'height', 'name' => 'height', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'source', 'name' => 'source', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'metadata', 'name' => 'metadata', 'value' => 0));

        $html .= html_writer::start_tag('center');

        $attr = array(
            'type' => 'button',
            'id' => 'id_add_video',
            'name' => 'add_video',
            'value' => get_string('addvideo', 'kalvidassign')
        );

        if ($disablesubmit) {
            $attr['disabled'] = 'disabled';
        }

        $html .= html_writer::empty_tag('input', $attr);

        $html .= '&nbsp;';

        $attr = array(
            'type' => 'submit',
            'name' => 'submit_video',
            'id' => 'submit_video',
            'disabled' => 'disabled',
            'value' => get_string('submitvideo', 'kalvidassign'));

        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::end_tag('center');

        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * This function returns HTML markup to render a form and submission buttons.
     * @param object $cm A course module object.
     * @param int $userid The current user id.
     * @param bool $disablesubmit Set to true to disable the submit button.
     * @return string Returns HTML markup.
     */
    public function display_student_resubmit_buttons($cm, $userid, $disablesubmit = false) {
        global $DB;

        $param = array('vidassignid' => $cm->instance, 'userid' => $userid);
        $submissionrec = $DB->get_record('kalvidassign_submission', $param);

        $html = '';

        $target = new moodle_url('/mod/kalvidassign/submission.php');

        $attr = array('method' => 'POST', 'action' => $target);

        $html .= html_writer::start_tag('form', $attr);

        $attr = array(
            'type' => 'hidden',
            'name'  => 'cmid',
            'value' => $cm->id
        );

        $html .= html_writer::empty_tag('input', $attr);

        $attr = array(
            'type' => 'hidden',
            'name'  => 'entry_id',
            'id'    => 'entry_id',
            'value' => $submissionrec->entry_id
        );

        $html .= html_writer::empty_tag('input', $attr);

        $attr = array(
            'type' => 'hidden',
            'name'  => 'sesskey',
            'value' => sesskey()
        );

        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'width', 'name' => 'width', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'height', 'name' => 'height', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'source', 'name' => 'source', 'value' => 0));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'metadata', 'name' => 'metadata', 'value' => 0));

        $html .= html_writer::start_tag('center');

        // Add submit and review buttons.
        $attr = array(
            'type' => 'button',
            'name' => 'add_video',
            'id' => 'id_add_video',
            'value' => get_string('replacevideo', 'kalvidassign')
        );

        if ($disablesubmit) {
            $attr['disabled'] = 'disabled';
        }

        $html .= html_writer::empty_tag('input', $attr);

        $html .= '&nbsp;&nbsp;';

        $attr = array(
            'type' => 'submit',
            'id'   => 'submit_video',
            'name' => 'submit_video',
            'disabled' => 'disabled',
            'value' => get_string('submitvideo', 'kalvidassign')
        );

        if ($disablesubmit) {
            $attr['disabled'] = 'disabled';
        }

        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::end_tag('center');

        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * This function returns HTML markup to render a form and submission buttons.
     * @param object $cm A course module object.
     * @param int $userid The current user id.
     * @param bool $disablesubmit Set to true to disable the submit button.
     * @return string Returns HTML markup.
     */
    public function display_instructor_buttons($cm,  $userid) {
        $html = '';

        $target = new moodle_url('/mod/kalvidassign/grade_submissions.php');

        $attr = array('method' => 'POST', 'action' => $target);

        $html .= html_writer::start_tag('form', $attr);

        $html .= html_writer::start_tag('center');

        $attr = array('type' => 'hidden',
                     'name' => 'sesskey',
                     'value' => sesskey());
        $html .= html_writer::empty_tag('input', $attr);

        $attr = array('type' => 'hidden',
                     'name' => 'cmid',
                     'value' => $cm->id);
        $html .= html_writer::empty_tag('input', $attr);

        $attr = array('type' => 'submit',
                     'name' => 'grade_submissions',
                     'value' => get_string('gradesubmission', 'kalvidassign'));

        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::end_tag('center');

        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * This function returns HTML markup to render a the submissions table
     * @param object $cm A course module object.
     * @param int $groupfilter The group id to filter against.
     * @param string $filter Filter users who have submitted, submitted and graded or everyone.
     * @param int $perpage The number of submissions to display on a page.
     * @param bool $quickgrade True if the quick grade table needs to be rendered, otherwsie false.
     * @param string $tifirst The first initial of the first name.
     * @param string $tilast The first initial of the last name.
     * @param int $page The current page to render.
     * @return string Returns HTML markup.
     */
    public function display_submissions_table($cm, $groupfilter = 0, $filter = 'all', $perpage, $quickgrade = false, $tifirst = '', $tilast = '', $page = 0) {

        global $DB, $OUTPUT, $COURSE, $USER;

        // Get a list of users who have submissions and retrieve grade data for those users.
        $users = kalvidassign_get_submissions($cm->instance, $filter);

        $define_columns = array('picture', 'fullname', 'selectgrade', 'submissioncomment', 'timemodified', 'timemarked', 'status', 'grade');

        if (empty($users)) {
            $users = array();
        }

        $entryids = array();

        foreach ($users as $usersubmission) {
            $entryids[$usersubmission->entry_id] = $usersubmission->entry_id;
        }

        // Compare student who have submitted to the assignment with students who are
        // currently enrolled in the course
        $students = kalvidassign_get_assignment_students($cm);
        $users = array_intersect(array_keys($users), array_keys($students));

        if (empty($users)) {
            echo html_writer::tag('p', get_string('noenrolledstudents', 'kalvidassign'));
            return;
        }

        $gradinginfo = grade_get_grades($cm->course, 'mod', 'kalvidassign', $cm->instance, $users);

        $where = '';
        switch ($filter) {
            case KALASSIGN_SUBMITTED:
                $where = ' kvs.timemodified > 0 AND ';
                break;
            case KALASSIGN_REQ_GRADING:
                $where = ' kvs.timemarked < kvs.timemodified AND ';
                break;
        }

        // Determine logic needed for groups mode
        $param        = array();
        $groupswhere  = '';
        $groupscolumn = '';
        $groupsjoin   = '';
        $groups       = array();
        $groupids     = '';
        $context      = context_course::instance($COURSE->id);

        // Get all groups that the user belongs to, check if the user has capability to access all groups
        if (!has_capability('moodle/site:accessallgroups', $context, $USER->id)) {
            $groups = groups_get_all_groups($COURSE->id, $USER->id);

            if (empty($groups)) {
                $message = get_string('nosubmissions', 'kalvidassign');
                echo html_writer::tag('center', $message);
                return;
            }
        } else {
            $groups = groups_get_all_groups($COURSE->id);
        }

        // Create a comma separated list of group ids
        foreach ($groups as $group) {
            $groupids .= $group->id.',';
        }

        $groupids = rtrim($groupids, ',');

        switch (groups_get_activity_groupmode($cm)) {
            case NOGROUPS:
                // No groups, do nothing
                break;
            case SEPARATEGROUPS:
                // If separate groups, but displaying all users then we must display only users
                // who are in the same group as the current user
                if (0 == $groupfilter) {
                    $groupscolumn = ', gm.groupid ';
                    $groupsjoin   = ' RIGHT JOIN {groups_members} gm ON gm.userid = u.id RIGHT JOIN {groups} g ON g.id = gm.groupid ';

                    $param['courseid'] = $cm->course;
                    $groupswhere  .= ' AND g.courseid = :courseid ';

                    $param['groupid'] = $groupfilter;
                    $groupswhere .= ' AND g.id IN ('.$groupids.') ';

                }

            case VISIBLEGROUPS:
                // if visible groups but displaying a specific group then we must display users within
                // that group, if displaying all groups then display all users in the course
                if (0 != $groupfilter) {

                    $groupscolumn = ', gm.groupid ';
                    $groupsjoin   = ' RIGHT JOIN {groups_members} gm ON gm.userid = u.id RIGHT JOIN {groups} g ON g.id = gm.groupid ';

                    $param['courseid'] = $cm->course;
                    $groupswhere  .= ' AND g.courseid = :courseid ';

                    $param['groupid'] = $groupfilter;
                    $groupswhere .= ' AND gm.groupid = :groupid ';

                }
                break;
        }

        $table = new submissions_table('kal_vid_submit_table', $cm, $gradinginfo, $quickgrade, $tifirst, $tilast, $page);

        // In order for the sortable first and last names to work.  User ID has to be the first column returned and must be
        // returned as id.  Otherwise the table will display links to user profiles that are incorrect or do not exist
        $columns = user_picture::fields('u').', kvs.id AS submitid, ';
        $columns .= ' kvs.grade, kvs.submissioncomment, kvs.timemodified, kvs.entry_id, kvs.source, kvs.width, kvs.height, kvs.timemarked, ';
        $columns .= 'kvs.metadata, 1 AS status, 1 AS selectgrade'.$groupscolumn;
        $where .= ' u.deleted = 0 AND u.id IN ('.implode(',', $users).') '.$groupswhere;

        $param['instanceid'] = $cm->instance;
        $from = "{user} u LEFT JOIN {kalvidassign_submission} kvs ON kvs.userid = u.id AND kvs.vidassignid = :instanceid ".$groupsjoin;

        $baseurl = new moodle_url('/mod/kalvidassign/grade_submissions.php', array('cmid' => $cm->id));

        $col1 = get_string('fullname', 'kalvidassign');
        $col2 = get_string('grade', 'kalvidassign');
        $col3 = get_string('submissioncomment', 'kalvidassign');
        $col4 = get_string('timemodified', 'kalvidassign');
        $col5 = get_string('grademodified', 'kalvidassign');
        $col6 = get_string('status', 'kalvidassign');
        $col7 = get_string('finalgrade', 'kalvidassign');

        $table->set_sql($columns, $from, $where, $param);
        $table->define_baseurl($baseurl);
        $table->collapsible(true);

        $table->define_columns($define_columns);
        $table->define_headers(array('', $col1, $col2, $col3, $col4, $col5, $col6, $col7));

        echo html_writer::start_tag('center');

        $attributes = array('action' => new moodle_url('grade_submissions.php'), 'id' => 'fastgrade', 'method' => 'post');
        echo html_writer::start_tag('form', $attributes);

        $attributes = array('type' => 'hidden', 'name' => 'cmid', 'value' => $cm->id);
        echo html_writer::empty_tag('input', $attributes);

        $attributes['name'] = 'mode';
        $attributes['value'] = 'fastgrade';

        echo html_writer::empty_tag('input', $attributes);

        $attributes['name'] = 'sesskey';
        $attributes['value'] = sesskey();

        echo html_writer::empty_tag('input', $attributes);

        $table->out($perpage, true);

        if ($quickgrade) {
            $attributes = array('type' => 'submit', 'name' => 'save_feedback', 'value' => get_string('savefeedback', 'kalvidassign'));

            echo html_writer::empty_tag('input', $attributes);
        }

        echo html_writer::end_tag('form');

        echo html_writer::end_tag('center');

        echo html_writer::empty_tag('input', array('id' => 'closeltipanel', 'type' => 'hidden'));
    }

    /**
     * Displays the assignments listing table.
     *
     * @param object $course The course odject.
     */
    public function display_kalvidassignments_table($course) {
        global $CFG, $DB, $PAGE, $OUTPUT, $USER;

        echo html_writer::start_tag('center');

        $strplural = get_string('modulenameplural', 'kalvidassign');

        if (!$cms = get_coursemodules_in_course('kalvidassign', $course->id, 'm.timedue')) {
            echo get_string('noassignments', 'mod_kalvidassign');
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }

        $strsectionname  = get_string('sectionname', 'format_'.$course->format);
        $usesections = course_format_uses_sections($course->format);
        $modinfo = get_fast_modinfo($course);

        if ($usesections) {
            $sections = $modinfo->get_section_info_all();
        }
        $courseindexsummary = new kalvidassign_course_index_summary($usesections, $strsectionname);

        $timenow = time();
        $currentsection = '';
        $assignmentcount = 0;

        foreach ($modinfo->instances['kalvidassign'] as $cm) {
            if (!$cm->uservisible) {
                continue;
            }

            $assignmentcount++;
            $timedue = $cms[$cm->id]->timedue;

            $sectionname = '';
            if ($usesections && $cm->sectionnum) {
                $sectionname = get_section_name($course, $sections[$cm->sectionnum]);
            }

            $submitted = '';
            $context = context_module::instance($cm->id);

            if (has_capability('mod/kalvidassign:gradesubmission', $context)) {
                $submitted = $DB->count_records('kalvidassign_submission', array('vidassignid' => $cm->instance));
            } else if (has_capability('mod/kalvidassign:submit', $context)) {
                if ($DB->count_records('kalvidassign_submission', array('vidassignid' => $cm->instance, 'userid' => $USER->id)) > 0) {
                    $submitted = get_string('submitted', 'mod_kalvidassign');
                } else {
                    $submitted = get_string('nosubmission', 'mod_kalvidassign');
                }
            }

            $gradinginfo = grade_get_grades($course->id, 'mod', 'kalvidassign', $cm->instance, $USER->id);
            if (isset($gradinginfo->items[0]->grades[$USER->id]) && !$gradinginfo->items[0]->grades[$USER->id]->hidden ) {
                $grade = $gradinginfo->items[0]->grades[$USER->id]->str_grade;
            } else {
                $grade = '-';
            }

            $courseindexsummary->add_assign_info($cm->id, $cm->name, $sectionname, $timedue, $submitted, $grade);
        }

        if ($assignmentcount > 0) {
            $pagerenderer = $PAGE->get_renderer('mod_kalvidassign');
            echo $pagerenderer->render($courseindexsummary);
        }

        echo html_writer::end_tag('center');
    }

    /**
     * This function displays HTML markup needed by the ltipanel YUI module to display a popup window containing the LTI launch.
     * @param object $submission A Kaltura video assignment video submission table object.
     * @param int $courseid The course id.
     * @param int $cmid The ccourse module id.
     * @return string HTML markup.
     */
    public function display_video_container_markup($submission, $courseid, $cmid) {
        $source = new moodle_url('/local/kaltura/pix/vidThumb.png');
        $alt    = get_string('video_thumbnail', 'mod_kalvidassign');
        $title  = get_string('video_thumbnail', 'mod_kalvidassign');
        $iframe = '';
        $url = null;

        $attr = array(
            'id' => 'video_thumbnail',
            'src' => $source->out(),
            'alt' => $alt,
            'title' => $title
        );

        // If the submission object contains a source URL then display the video as part of an LTI launch.
        if (!empty($submission->source)) {
            $attr['style'] = 'display:none';

            $params = array(
                'courseid' => $courseid,
                'height' => $submission->height,
                'width' => $submission->width,
                'withblocks' => 0,
                'source' => local_kaltura_add_kaf_uri_token($submission->source),
                'cmid' => $cmid
            );
            $url = new moodle_url('/mod/kalvidassign/lti_launch.php', $params);
        }

        $output = html_writer::empty_tag('img', $attr);

        $params = array(
            'id' => 'contentframe',
            'src' => ($url instanceof moodle_url) ? $url->out(false) : '',
            'allowfullscreen' => "true",
            'webkitallowfullscreen' => "true",
            'mozallowfullscreen' => "true",
            'height' => '100%',
            'width' => !empty($submission->width) ? $submission->width : ''
        );

        if (empty($submission->source)) {
            $params['style'] = 'display:none';
        }

        $iframe = html_writer::tag('iframe', '', $params);

        $output .= html_writer::tag('center', $iframe);
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'closeltipanel', 'value' => 0));

        return $output;
    }

    /**
     * Display the feedback to the student
     *
     * This default method prints the teacher picture and name, date when marked,
     * grade and teacher submissioncomment.
     *
     * @global object
     * @global object
     * @global object
     * @param object $submission The submission object or NULL in which case it will be loaded
     *
     * TODO: correct documentation for this function
     */
    public function display_grade_feedback($kalvidassign, $context) {
        global $USER, $CFG, $DB, $OUTPUT;

        require_once($CFG->libdir.'/gradelib.php');

        // Check if the user is enrolled to the coruse and can submit to the assignment
        if (!is_enrolled($context, $USER, 'mod/kalvidassign:submit')) {
            // can not submit assignments -> no feedback
            return;
        }

        // Get the user's submission obj
        $gradinginfo = grade_get_grades($kalvidassign->course, 'mod', 'kalvidassign', $kalvidassign->id, $USER->id);

        $item = $gradinginfo->items[0];
        $grade = $item->grades[$USER->id];

        // Hidden or error.
        if ($grade->hidden or $grade->grade === false) {
            return;
        }

        // Nothing to show yet.
        if ($grade->grade === null and empty($grade->str_feedback)) {
            return;
        }

        $gradedate = $grade->dategraded;
        $gradeby   = $grade->usermodified;

        // We need the teacher info
        if (!$teacher = $DB->get_record('user', array('id'=>$gradeby))) {
            print_error('cannotfindteacher');
        }

        // Print the feedback
        echo $OUTPUT->heading(get_string('feedbackfromteacher', 'assignment', fullname($teacher)));

        echo '<table cellspacing="0" class="feedback">';

        echo '<tr>';
        echo '<td class="left picture">';
        if ($teacher) {
            echo $OUTPUT->user_picture($teacher);
        }
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        if ($teacher) {
            echo '<div class="fullname">'.fullname($teacher).'</div>';
        }
        echo '<div class="time">'.userdate($gradedate).'</div>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        echo '<div class="grade">';
        echo get_string("grade").': '.$grade->str_long_grade;
        echo '</div>';
        echo '<div class="clearer"></div>';

        echo '<div class="comment">';
        echo $grade->str_feedback;
        echo '</div>';
        echo '</tr>';

        echo '</table>';
    }

    /**
     * Render a course index summary.
     *
     * @param kalvidassign_course_index_summary $indexsummary Structure for index summary.
     * @return string HTML for assignments summary table
     */
    public function render_kalvidassign_course_index_summary(kalvidassign_course_index_summary $indexsummary) {
        $strplural = get_string('modulenameplural', 'kalvidassign');
        $strsectionname  = $indexsummary->courseformatname;
        $strduedate = get_string('duedate', 'kalvidassign');
        $strsubmission = get_string('submission', 'kalvidassign');
        $strgrade = get_string('grade');

        $table = new html_table();
        if ($indexsummary->usesections) {
            $table->head  = array ($strsectionname, $strplural, $strduedate, $strsubmission, $strgrade);
            $table->align = array ('left', 'left', 'center', 'right', 'right');
        } else {
            $table->head  = array ($strplural, $strduedate, $strsubmission, $strgrade);
            $table->align = array ('left', 'left', 'center', 'right');
        }
        $table->data = array();

        $currentsection = '';
        foreach ($indexsummary->assignments as $info) {
            $params = array('id' => $info['cmid']);
            $link = html_writer::link(new moodle_url('/mod/kalvidassign/view.php', $params), $info['cmname']);
            $due = $info['timedue'] ? userdate($info['timedue']) : '-';

            $printsection = '';
            if ($indexsummary->usesections) {
                if ($info['sectionname'] !== $currentsection) {
                    if ($info['sectionname']) {
                        $printsection = $info['sectionname'];
                    }
                    if ($currentsection !== '') {
                        $table->data[] = 'hr';
                    }
                    $currentsection = $info['sectionname'];
                }
            }

            if ($indexsummary->usesections) {
                $row = array($printsection, $link, $due, $info['submissioninfo'], $info['gradeinfo']);
            } else {
                $row = array($link, $due, $info['submissioninfo'], $info['gradeinfo']);
            }
            $table->data[] = $row;
        }

        return html_writer::table($table);
    }
}
