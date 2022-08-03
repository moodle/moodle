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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->dirroot.'/blocks/iomad_microlearning/lib.php');

/**
 *
 */

class block_iomad_microlearning extends block_base {
    public function init() {
        $this->title = get_string('blocktitle', 'block_iomad_microlearning');
    }

    public function hide_header() {
        return false;
    }

    public function get_content() {
        global $CFG, $USER, $DB;

        // Get any nuggets assigned and not completed.
        $mynuggets = $DB->get_records_sql("SELECT mtu.*, mn.name AS nuggetname, mn.cmid, mn.sectionid, mn.url as url, mt.name AS threadname 
                                           FROM {microlearning_thread_user} mtu
                                           JOIN {microlearning_nugget} mn ON (mtu.nuggetid = mn.id)
                                           JOIN {microlearning_thread} mt ON (mtu.threadid = mt.id)
                                           WHERE mtu.userid = :userid
                                           AND mtu.timecompleted IS NULL
                                           ORDER BY mn.name,mtu.schedule_date",
                                           array('userid' => $USER->id));
        if (empty($mynuggets)) {
            $nuggetout = get_string('nolearningthreads', 'block_microlearning');
        } else {
            $threadid = 0;
            $nuggetout = html_writer::start_tag('div', array('class' => 'microlearningthreads'));
            foreach ($mynuggets as $mynugget) {
                if ($threadid != $mynugget->threadid) {
                    // display the thread name.
                    $nuggetout .= html_writer::start_tag('div', array('class' => 'microlearningthreadhead'));
                    $nuggetout .= format_text($mynugget->threadname);
                    $nuggetout .= html_writer::end_tag('a');
                    $threadid = $mynugget->threadid;
                }
                $linkurl = microlearning::get_nugget_url($mynugget);
                $nuggetout .= html_writer::start_tag('div', array('class' => 'microlearningnugget'));
                $nuggetout .= html_writer::start_tag('a', array('class' => 'microlearningnugget_link', 'href' => $linkurl));
                $nuggetout .= format_string($mynugget->nuggetname);
                $nuggetout .= html_writer::end_tag('a');
                $nuggetout .= html_writer::end_tag('div');
            }
            $nuggetout .= html_writer::end_tag('div');
        }

        // Need to add in links to manage if we have caps.

        $this->content = new stdClass;
        $this->content->footer = '';

        $this->content->text = $nuggetout;

        return $this->content;
    }

    function has_config() {
        return true;
    }
}
