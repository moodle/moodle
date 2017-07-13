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
 * Mentees block.
 *
 * @package    block_mentees
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_mentees extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_mentees');
    }

    function applicable_formats() {
        return array('all' => true, 'tag' => false);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? $this->config->title : get_string('newmenteesblock', 'block_mentees');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();

        // get all the mentees, i.e. users you have a direct assignment to
        $allusernames = get_all_user_name_fields(true, 'u');
        if ($usercontexts = $DB->get_records_sql("SELECT c.instanceid, c.instanceid, $allusernames
                                                    FROM {role_assignments} ra, {context} c, {user} u
                                                   WHERE ra.userid = ?
                                                         AND ra.contextid = c.id
                                                         AND c.instanceid = u.id
                                                         AND c.contextlevel = ".CONTEXT_USER, array($USER->id))) {

            $this->content->text = '<ul>';
            foreach ($usercontexts as $usercontext) {
                $this->content->text .= '<li><a href="'.$CFG->wwwroot.'/user/view.php?id='.$usercontext->instanceid.'&amp;course='.SITEID.'">'.fullname($usercontext).'</a></li>';
            }
            $this->content->text .= '</ul>';
        }

        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Returns true if the block can be docked.
     * The mentees block can only be docked if it has a non-empty title.
     * @return bool
     */
    public function instance_can_be_docked() {
        return parent::instance_can_be_docked() && isset($this->config->title) && !empty($this->config->title);
    }
}

