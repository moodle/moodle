<?PHP

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

/*
 * @package    blocks
 * @subpackage community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * The community block
 */

class block_community extends block_list {

    function init() {
        $this->title = get_string('pluginname', 'block_community');
    }

    function user_can_addto($page) {
        // Don't allow people to add the block if they can't even use it
        if (!has_capability('moodle/community:add', $page->context)) {
            return false;
        }

        return parent::user_can_addto($page);
    }

    function user_can_edit() {
        // Don't allow people to edit the block if they can't even use it
        if (!has_capability('moodle/community:add',
                        context::instance_by_id($this->instance->parentcontextid))) {
            return false;
        }
        return parent::user_can_edit();
    }

    function get_content() {
        global $CFG, $OUTPUT, $USER;

        $coursecontext = context::instance_by_id($this->instance->parentcontextid);

        if (!has_capability('moodle/community:add', $coursecontext)
                or $this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!isloggedin()) {
            return $this->content;
        }

        $icon = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/group'),
                    'class' => 'icon', 'alt' => ""));
        $addcourseurl = new moodle_url('/blocks/community/communitycourse.php',
                        array('add' => true, 'courseid' => $this->page->course->id));
        $searchlink = html_writer::tag('a', $icon . get_string('addcourse', 'block_community'),
                        array('href' => $addcourseurl->out(false)));
        $this->content->items[] = $searchlink;

        require_once($CFG->dirroot . '/blocks/community/locallib.php');
        $communitymanager = new block_community_manager();
        $courses = $communitymanager->block_community_get_courses($USER->id);
        if ($courses) {
            $this->content->items[] = html_writer::empty_tag('hr');
            $this->content->icons[] = '';
            $this->content->items[] = get_string('mycommunities', 'block_community');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                //delete link
                $deleteicon = html_writer::empty_tag('img',
                                array('src' => $OUTPUT->pix_url('t/delete'),
                                    'alt' => get_string('removecommunitycourse', 'block_community')));
                $deleteurl = new moodle_url('/blocks/community/communitycourse.php',
                                array('remove' => true,
                                    'courseid' => $this->page->course->id,
                                    'communityid' => $course->id, 'sesskey' => sesskey()));
                $deleteatag = html_writer::tag('a', $deleteicon, array('href' => $deleteurl));

                $courselink = html_writer::tag('a', $course->coursename,
                                array('href' => $course->courseurl));
                $this->content->items[] = $courselink . ' ' . $deleteatag;
                $this->content->icons[] = '';
            }
        }

        return $this->content;
    }

}

