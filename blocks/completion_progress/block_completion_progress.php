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
 * Completion Progress block definition
 *
 * @package    block_completion_progress
 * @copyright  2016 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/completion_progress/lib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Completion Progress block class
 *
 * @copyright 2016 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_completion_progress extends block_base {

    /**
     * Sets the block title
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('config_default_title', 'block_completion_progress');
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
        if (isset($this->config->progressTitle) && trim($this->config->progressTitle) != '') {
            $this->title = format_string($this->config->progressTitle);
        }
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return !block_completion_progress_on_site_page();
    }

    /**
     * Controls whether the block is configurable
     *
     * @return bool
     */
    public function instance_allow_config() {
        return !block_completion_progress_on_site_page();
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site'           => true,
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

        // Guests do not have any progress. Don't show them the block.
        if (!isloggedin() or isguestuser()) {
            return $this->content;
        }

        // Draw the multi-bar content for the Dashboard and Front page.
        if (block_completion_progress_on_site_page()) {

            if (!$CFG->enablecompletion) {
                $this->content->text .= get_string('completion_not_enabled', 'block_completion_progress');
                return $this->content;
            }

            // Show a message when the user is not enrolled in any courses.
            $courses = enrol_get_my_courses();
            if (($this->page->user_is_editing() || is_siteadmin()) && empty($courses)) {
                $this->content->text = get_string('no_courses', 'block_completion_progress');
                return $this->content;
            }

            $coursenametoshow = get_config('block_completion_progress', 'coursenametoshow') ?:
                DEFAULT_COMPLETIONPROGRESS_COURSENAMETOSHOW;
            $sql = "SELECT bi.id,
                           bp.id AS blockpositionid,
                           COALESCE(bp.region, bi.defaultregion) AS region,
                           COALESCE(bp.weight, bi.defaultweight) AS weight,
                           COALESCE(bp.visible, 1) AS visible,
                           bi.configdata
                      FROM {block_instances} bi
                 LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                                               AND ".$DB->sql_like('bp.pagetype', ':pagetype', false)."
                     WHERE bi.blockname = 'completion_progress'
                       AND bi.parentcontextid = :contextid
                  ORDER BY region, weight, bi.id";

            foreach ($courses as $courseid => $course) {

                // Get specific block config and context.
                $completion = new completion_info($course);
                if ($course->visible && $completion->is_enabled()) {
                    $context = CONTEXT_COURSE::instance($course->id);
                    $params = array('contextid' => $context->id, 'pagetype' => 'course-view-%');
                    $blockinstances = $DB->get_records_sql($sql, $params);
                    $exclusions = block_completion_progress_exclusions($course->id);
                    foreach ($blockinstances as $blockid => $blockinstance) {
                        $blockinstance->config = unserialize(base64_decode($blockinstance->configdata));
                        $blockinstance->activities = block_completion_progress_get_activities($course->id, $blockinstance->config);
                        $blockinstance->activities = block_completion_progress_filter_visibility($blockinstance->activities,
                                                         $USER->id, $course->id, $exclusions);
                        $blockcontext = CONTEXT_BLOCK::instance($blockid);
                        if (
                            !has_capability('block/completion_progress:showbar', $blockcontext) ||
                            $blockinstance->visible == 0 ||
                            empty($blockinstance->activities) ||
                            (
                                !empty($blockinstance->config->group) &&
                                !has_capability('moodle/site:accessallgroups', $context) &&
                                !groups_is_member($blockinstance->config->group, $USER->id)
                            )
                        ) {
                            unset($blockinstances[$blockid]);
                        }
                    }
                    $blockinstancesonpage = array_merge($blockinstancesonpage, array_keys($blockinstances));

                    // Output the Progress Bar.
                    if (!empty($blockinstances)) {
                        $courselink = new moodle_url('/course/view.php', array('id' => $course->id));
                        $linktext = HTML_WRITER::tag('h3', s(format_string($course->$coursenametoshow)));
                        $this->content->text .= HTML_WRITER::link($courselink, $linktext);
                    }
                    foreach ($blockinstances as $blockid => $blockinstance) {
                        if (
                            isset($blockinstance->config) &&
                            isset($blockinstance->config->progressTitle) &&
                            $blockinstance->config->progressTitle != ''
                        ) {
                            $this->content->text .= HTML_WRITER::tag('p', s(format_string($blockinstance->config->progressTitle)));
                        }
                        $submissions = block_completion_progress_student_submissions($course->id, $USER->id);
                        $completions = block_completion_progress_completions($blockinstance->activities, $USER->id, $course,
                            $submissions);
                        $this->content->text .= block_completion_progress_bar($blockinstance->activities,
                                                                    $completions,
                                                                    $blockinstance->config,
                                                                    $USER->id,
                                                                    $course->id,
                                                                    $blockinstance->id);
                    }
                }
            }

            // Show a message explaining lack of bars, but only while editing is on.
            if ($this->page->user_is_editing() && $this->content->text == '') {
                $this->content->text = get_string('no_blocks', 'block_completion_progress');
            }

        } else {
            // Gather content for block on regular course.

            // Check if user is in group for block.
            if (
                !empty($this->config->group) &&
                !has_capability('moodle/site:accessallgroups', $this->context) &&
                !groups_is_member($this->config->group, $USER->id)
            ) {
                return $this->content;
            }

            // Check if completion is enabled at site level.
            if (!$CFG->enablecompletion) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('completion_not_enabled', 'block_completion_progress');
                }
                return $this->content;
            }

            // Check if completion is enabled at course level.
            $completion = new completion_info($COURSE);
            if (!$completion->is_enabled()) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('completion_not_enabled_course', 'block_completion_progress');
                }
                return $this->content;
            }

            // Check if any activities/resources have been created.
            $exclusions = block_completion_progress_exclusions($COURSE->id);
            $activities = block_completion_progress_get_activities($COURSE->id, $this->config);
            $activities = block_completion_progress_filter_visibility($activities, $USER->id, $COURSE->id, $exclusions);
            if (empty($activities)) {
                if (has_capability('moodle/block:edit', $this->context)) {
                    $this->content->text .= get_string('no_activities_config_message', 'block_completion_progress');
                }
                return $this->content;
            }

            // Display progress bar.
            if (has_capability('block/completion_progress:showbar', $this->context)) {
                $submissions = block_completion_progress_student_submissions($COURSE->id, $USER->id);
                $completions = block_completion_progress_completions($activities, $USER->id, $COURSE, $submissions);
                $this->content->text .= block_completion_progress_bar(
                    $activities,
                    $completions,
                    $this->config,
                    $USER->id,
                    $COURSE->id,
                    $this->instance->id
                );
            }
            $blockinstancesonpage = array($this->instance->id);

            // Allow teachers to access the overview page.
            if (has_capability('block/completion_progress:overview', $this->context)) {
                $parameters = array('instanceid' => $this->instance->id, 'courseid' => $COURSE->id, 'sesskey' => sesskey());
                $url = new moodle_url('/blocks/completion_progress/overview.php', $parameters);
                $label = get_string('overview', 'block_completion_progress');
                $options = array('class' => 'overviewButton');
                $this->content->text .= $OUTPUT->single_button($url, $label, 'post', $options);
            }
        }

        // Organise access to JS.
        $jsmodule = array(
            'name' => 'block_completion_progress',
            'fullpath' => '/blocks/completion_progress/module.js',
            'requires' => array(),
            'strings' => array(),
        );
        $arguments = array($blockinstancesonpage, array($USER->id));
        $this->page->requires->js_init_call('M.block_completion_progress.setupScrolling', array(), false, $jsmodule);
        $this->page->requires->js_init_call('M.block_completion_progress.init', $arguments, false, $jsmodule);

        return $this->content;
    }
}
