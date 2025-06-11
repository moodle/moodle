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
 * WDS Post Grades Block for Moodle 4.5.
 *
 * This block displays student final grades in a tabular format.
 * It uses the Moodle gradebook API to retrieve grades properly formatted.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/wds_postgrades/classes/period_settings.php');

/**
 * Block definition class for WDS Post Grades block.
 */
class block_wds_postgrades extends block_base {

    /**
     * Initialize the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_wds_postgrades');
    }

    /**
     * Indicates whether the block has settings.
     *
     * @return boolean True if the block has settings.
     */
    public function has_config() {
        return true;
    }

    /**
     * Returns the content object.
     *
     * @return stdClass The content object.
     */
    public function get_content() {
        global $CFG, $COURSE, $OUTPUT;

        require_once($CFG->dirroot . '/blocks/wds_postgrades/classes/period_settings.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $isections = \block_wds_postgrades\period_settings::get_interim_grading_sections($COURSE->id);

        $fsections = \block_wds_postgrades\period_settings::get_final_grading_sections($COURSE->id);

        // No sections available for posting.
        if (empty($isections) && empty($fsections)) {
            return null;
        }

        // Build out the block.
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Check capabilities.
        $context = context_course::instance($COURSE->id);
        if (!has_capability('block/wds_postgrades:view', $context)) {
            $this->content->text = get_string('nopermission', 'block_wds_postgrades');
            return $this->content;
        }

        // Create a link to the view page for all sections
        $viewurl = new moodle_url('/blocks/wds_postgrades/view.php', ['courseid' => $COURSE->id]);
        $linktext = get_string('postallgrades', 'block_wds_postgrades');

        if (!empty($isections)) {

            $this->content->text .= html_writer::start_div('individual-sections interim-sections');

            $this->content->text .= html_writer::tag(
                'h5',
                get_string('gradetype', 'block_wds_postgrades', 'Interim')
            );

            // Loop through nay interims.
            foreach ($isections as $isection) {
                $iparms = [
                    'courseid' => $COURSE->id,
                    'sectionid' => $isection->id,
                    'gradetype' => 'interim'
                ];
                $isectionurl = new moodle_url('/blocks/wds_postgrades/view.php', $iparms);
                $isectiontitle = $isection->course_subject_abbreviation . ' ' .
                    $isection->course_number . ' ' .
                    $isection->section_number;
                $isectionbutton = $OUTPUT->single_button(
                    $isectionurl,
                    $isectiontitle,
                    'get',
                    ['class' => 'wdspgradesbutton-section']
                );
                $this->content->text .= $isectionbutton;
            }

            $this->content->text .= html_writer::end_div();
        }

        if (!empty($fsections)) {

            $this->content->text .= html_writer::start_div('individual-sections final-sections');

            $this->content->text .= html_writer::tag(
                'h5',
                get_string('gradetype', 'block_wds_postgrades', 'Final')
            );

            // Loop through any finals.
            foreach ($fsections as $fsection) {
                $fparms = [
                    'courseid' => $COURSE->id,
                    'sectionid' => $fsection->id,
                    'gradetype' => 'final'
                ];
                $fsectionurl = new moodle_url('/blocks/wds_postgrades/view.php', $fparms);

                $fsectiontitle = $fsection->course_subject_abbreviation . ' ' .
                    $fsection->course_number . ' ' .
                    $fsection->section_number;
                $fsectionbutton = $OUTPUT->single_button(
                    $fsectionurl,
                    $fsectiontitle,
                    'get',
                    ['class' => 'wdspgradesbutton-section']
                );
                $this->content->text .= $fsectionbutton;
            }

            $this->content->text .= html_writer::end_div();
        }

        return $this->content;
    }

    /**
     * This block can be added to courses only.
     *
     * @return array The array of applicable formats.
     */
    public function applicable_formats() {
        return [
            'course-view' => true,
            'site' => false,
            'mod' => false,
            'my' => false,
        ];
    }
}
