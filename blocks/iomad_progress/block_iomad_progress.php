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
 * Progress Bar block definition
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/iomad_progress/lib.php');

/**
 * Progress Bar block class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_iomad_progress extends block_base {

    /**
     * Sets the block title
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('config_default_title', 'block_iomad_progress');
    }

    /**
     *  we have global config/settings data
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        if (isset($this->config->iomad_progressTitle) && trim($this->config->iomad_progressTitle) != '') {
            $this->title = format_string($this->config->iomad_progressTitle);
        }
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return !block_iomad_progress_on_my_page();
    }

    /**
     * Controls whether the block is configurable
     *
     * @return bool
     */
    public function instance_allow_config() {
        return !block_iomad_progress_on_my_page();
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site'           => false,
            'mod'            => false,
            'my'             => true
        );
    }

    /**
     * Creates the blocks main content
     *
     * @return string
     */
    public function get_content() {
        global $USER, $COURSE, $CFG, $OUTPUT, $DB;

        // If content has already been generated, don't waste time generating it again.
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        $blockinstancesonpage = array();

        // Guests do not have any iomad_progress. Don't show them the block.
        if (!isloggedin() or isguestuser()) {
            return $this->content;
        }

        // Draw the multi-bar content for the My home page.
        if (block_iomad_progress_on_my_page()) {
            $courses = enrol_get_my_courses();
            $coursenametoshow = get_config('block_iomad_progress', 'coursenametoshow') ?: 'shortname';
            $sql = "SELECT bi.id,
                           bp.id AS blockpositionid,
                           COALESCE(bp.region, bi.defaultregion) AS region,
                           COALESCE(bp.weight, bi.defaultweight) AS weight,
                           COALESCE(bp.visible, 1) AS visible,
                           bi.configdata
                      FROM {block_instances} bi
                 LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                                               AND ".$DB->sql_like('bp.pagetype', ':pagetype', false)."
                     WHERE bi.blockname = 'iomad_progress'
                       AND bi.parentcontextid = :contextid
                  ORDER BY region, weight, bi.id";

            foreach ($courses as $courseid => $course) {

                // Get specific block config and context.
                $modules = block_iomad_progress_modules_in_use($course->id);
                if ($course->visible && !empty($modules)) {
                    $context = block_iomad_progress_get_course_context($course->id);
                    $params = array('contextid' => $context->id, 'pagetype' => 'course-view-%');
                    $blockinstances = $DB->get_records_sql($sql, $params);
                    $blockinstancesonpage = array_merge($blockinstancesonpage, array_keys($blockinstances));
                    foreach ($blockinstances as $blockid => $blockinstance) {
                        $blockinstance->config = unserialize(base64_decode($blockinstance->configdata));
                        if (!empty($blockinstance->config)) {
                            $blockinstance->events = block_iomad_progress_event_information(
                                                         $blockinstance->config,
                                                         $modules,
                                                         $course->id);
                            $blockinstance->events = block_iomad_progress_filter_visibility($blockinstance->events,
                                                         $USER->id, $context, $course);
                        }
                        if (
                            $blockinstance->visible == 0 ||
                            empty($blockinstance->config) ||
                            $blockinstance->events == 0 ||
                            (
                                !empty($blockinstance->config->group) &&
                                !has_capability('moodle/site:accessallgroups', $context) &&
                                !groups_is_member($blockinstance->config->group, $USER->id)
                            )
                        ) {
                            unset($blockinstances[$blockid]);
                        }
                    }

                    // Output the Progress Bar.
                    if (!empty($blockinstances)) {
                        $courselink = new moodle_url('/course/view.php', array('id' => $course->id));
                        $linktext = HTML_WRITER::tag('h3', s($course->$coursenametoshow));
                        $this->content->text .= HTML_WRITER::link($courselink, $linktext);
                    }
                    foreach ($blockinstances as $blockid => $blockinstance) {
                        if ($blockinstance->config->iomad_progressTitle != '') {
                            $this->content->text .= HTML_WRITER::tag('p', s($blockinstance->config->iomad_progressTitle));
                        }
                        $attempts = block_iomad_progress_attempts($modules,
                                                            $blockinstance->config,
                                                            $blockinstance->events,
                                                            $USER->id,
                                                            $course->id);
                        $this->content->text .= block_iomad_progress_bar($modules,
                                                                   $blockinstance->config,
                                                                   $blockinstance->events,
                                                                   $USER->id,
                                                                   $blockinstance->id,
                                                                   $attempts,
                                                                   $course->id);
                    }
                }
            }

            // Show a message explaining lack of bars, but only while editing is on.
            if ($this->page->user_is_editing() && $this->content->text == '') {
                $this->content->text = get_string('no_blocks', 'block_iomad_progress');
            }
        }

        // Gather content for block on regular course.
        else {

            // Check if user is in group for block.
            if (
                !empty($this->config->group) &&
                !has_capability('moodle/site:accessallgroups', $this->context) &&
                !groups_is_member($this->config->group, $USER->id)
            ) {
                return $this->content;
            }

            // Check if any activities/resources have been created.
            $modules = block_iomad_progress_modules_in_use($COURSE->id);
            if (empty($modules)) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('no_events_config_message', 'block_iomad_progress');
                }
                return $this->content;
            }

            // Check if activities/resources have been selected in config.
            $events = block_iomad_progress_event_information($this->config, $modules, $COURSE->id);
            $context = block_iomad_progress_get_course_context($COURSE->id);
            $events = block_iomad_progress_filter_visibility($events, $USER->id, $context);
            if ($events === null || $events === 0) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('no_events_message', 'block_iomad_progress');
                    if ($USER->editing) {
                        $parameters = array('id' => $COURSE->id, 'sesskey' => sesskey(),
                                            'bui_editid' => $this->instance->id);
                        $url = new moodle_url('/course/view.php', $parameters);
                        $label = get_string('selectitemstobeadded', 'block_iomad_progress');
                        $this->content->text .= $OUTPUT->single_button($url, $label);
                        if ($events === 0) {
                            $url->param('turnallon', '1');
                            $label = get_string('addallcurrentitems', 'block_iomad_progress');
                            $this->content->text .= $OUTPUT->single_button($url, $label);
                        }
                    }
                }
                return $this->content;
            } else if (empty($events)) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('no_visible_events_message', 'block_iomad_progress');
                }
                return $this->content;
            }

            // Display iomad_progress bar.
            $attempts = block_iomad_progress_attempts($modules, $this->config, $events, $USER->id, $COURSE->id);
            $this->content->text = block_iomad_progress_bar($modules,
                                                      $this->config,
                                                      $events,
                                                      $USER->id,
                                                      $this->instance->id,
                                                      $attempts,
                                                      $COURSE->id);
            $blockinstancesonpage = array($this->instance->id);

            // Allow teachers to access the overview page.
            if (has_capability('block/iomad_progress:overview', $this->context)) {
                $parameters = array('iomad_progressbarid' => $this->instance->id, 'courseid' => $COURSE->id);
                $url = new moodle_url('/blocks/iomad_progress/overview.php', $parameters);
                $label = get_string('overview', 'block_iomad_progress');
                $options = array('class' => 'overviewButton');
                $this->content->text .= $OUTPUT->single_button($url, $label, 'post', $options);
            }
        }

        // Organise access to JS.
        $jsmodule = array(
            'name' => 'block_iomad_progress',
            'fullpath' => '/blocks/iomad_progress/module.js',
            'requires' => array(),
            'strings' => array(),
        );
        $arguments = array($blockinstancesonpage, array($USER->id));
        $this->page->requires->js_init_call('M.block_iomad_progress.init', $arguments, false, $jsmodule);

        return $this->content;
    }
}
