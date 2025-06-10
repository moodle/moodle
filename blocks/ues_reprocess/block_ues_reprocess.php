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
 *
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();

class block_ues_reprocess extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_ues_reprocess');
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return @bool
     */
    public function has_config() {
        return true;
    }
    
    public function applicable_formats() {
        return array('course' => true, 'my' => true, 'site' => false);
    }

    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        global $OUTPUT, $USER, $COURSE;

        $content = new stdClass;

        $content->items = array();
        $content->icons = array();
        $content->footer = '';

        ues::require_daos();

        $teacher = ues_teacher::get(array('userid' => $USER->id));

        if (!$teacher) {
            return $this->content;
        }

        $is = ues::gen_str('block_ues_reprocess');

        $urlgen = function($base, $params=null) use ($is) {
            $url = new moodle_url('/blocks/ues_reprocess/'.$base.'.php', $params);

            return html_writer::link($url, $is($base));
        };

        $issite = $COURSE->id == 1;

        if ($issite) {
            $content->items[] = $urlgen('reprocess', array(
                'id' => $USER->id, 'type' => 'user'
            ));
        } else {
            $sections = $teacher->sections(true);

            $inthiscourse = function($section) use ($COURSE) {
                return $section->idnumber == $COURSE->idnumber;
            };

            $found = array_filter($sections, $inthiscourse);

            if (empty($found)) {
                return $this->content;
            }

            $content->items[] = $urlgen('reprocess', array(
                'id' => $COURSE->id, 'type' => 'course'
            ));
        }

        $content->icons[] = $OUTPUT->pix_icon(
            'i/users', $is('reprocess'), 'moodle', array('class' => 'icon')
        );

        $this->content = $content;

        return $this->content;
    }

    public function cron() {
        $us = ues::gen_str('block_ues_reprocess');

        mtrace($us('cleanup'));

        ues_section::update_meta(array('section_reprocessed' => 0));

        return true;
    }
}
