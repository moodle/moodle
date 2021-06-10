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
 * Overriden course topics format renderer.
 *
 * @package    theme_fordson
 * Special thanks to Willian Mono for course topic progress percentage code.  See comment for code.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/weeks/renderer.php');

class theme_fordson_format_weeks_renderer extends format_weeks_renderer {

    /**
     * Generate a summary of a section for display on the 'coruse index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        $total = 0;
        $complete = 0;
        $completioninfo = new completion_info($course);
        $cancomplete = isloggedin() && !isguestuser();
        $modinfo = get_fast_modinfo($course);
        
        $sectionmods = array();
        $completioninfo = new completion_info($course);
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $cmid) {
                
                $thismod = $modinfo->cms[$cmid];

                if ($thismod->modname == 'label') {
                    // Labels are special (not interesting for students)!
                    continue;
                }

                if ($thismod->uservisible) {
                    if (isset($sectionmods[$thismod->modname])) {
                        $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                        $sectionmods[$thismod->modname]['count']++;
                    }
                    else {
                        $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                        $sectionmods[$thismod->modname]['count'] = 1;
                    }
                    if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                        $total++;
                        $completiondata = $completioninfo->get_data($thismod, true);
                        if ($completiondata->completionstate == COMPLETION_COMPLETE || $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                            $complete++;
                        }
                    }
                }
            }
        }

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        }
        else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', array(
            'id' => 'section-' . $section->section,
            'class' => $classattr,
            'role' => 'region',
            'aria-label' => $title
        ));

        $o .= html_writer::tag('div', '', array(
            'class' => 'left side'
        ));
        $o .= html_writer::tag('div', '', array(
            'class' => 'right side'
        ));
        $o .= html_writer::start_tag('div', array(
            'class' => 'content'
        ));
        if ($total > 0) {
            $completion = new stdClass;
            $completion->complete = $complete;
            $completion->total = $total;
            $percenttext = get_string('coursecompletion', 'completion');
            $percent = 0;

            if ($complete > 0) {
                $percent = (int)(($complete / $total) * 100);
            }

            $o .= "<div class='progress fordsonsinglepage'>";
            $o .= "<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='{$percent}' ";
            $o .= " aria-valuemin='0' aria-valuemax='100' style='width: {$percent}%;'>";
            $o .= "<div class='fhsprogresstest'>";
            $o .= "<span class='sr-only'>$percenttext</span>";
            $o .= "</div>";
            $o .= "</div>";
            $o .= "</div>";
        }

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title, array(
                'href' => course_get_url($course, $section->section) ,
                'class' => $linkclasses
            ));
        }
        // Add .sectionname so that fontawesome icon can be applied to this page too.
        $o .= $this->output->heading($title, 3, 'section-title sectionname');
        $o .= html_writer::start_tag('div', array(
            'class' => 'summarytext'
        ));
        $o .= $this->format_summary_text($section);
        $o .= $this->section_activity_summary($section, $course, null);
        $o .= html_writer::end_tag('div');

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));

        $o .= html_writer::end_tag('div'); // Content.
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate a summary of the activites in a section
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_activity_summary($section, $course, $mods) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return '';
        }

        // Generate array with count of activities in this section.
        $sectionmods = array();
        $total = 0;
        $complete = 0;
        $cancomplete = isloggedin() && !isguestuser();
        $completioninfo = new completion_info($course);
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];

            if ($thismod->modname == 'label') {
                // Labels are special (not interesting for students)!
                continue;
            }

            if ($thismod->uservisible) {
                if (isset($sectionmods[$thismod->modname])) {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                    $sectionmods[$thismod->modname]['count']++;
                }
                else {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                    $sectionmods[$thismod->modname]['count'] = 1;
                }
                if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $total++;
                    $completiondata = $completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE || $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $complete++;
                    }
                }
            }
        }

        if (empty($sectionmods)) {
            // No sections.
            return '';
        }

        $output = '';
        // Output Link to Topic modules.
        // $title = get_section_name($course, $section);
        $linktitle = get_string('viewsectionmodules', 'theme_fordson');
        $output = html_writer::link(new moodle_url('/course/view.php', array('id' => $PAGE->course->id, 'section' => $section->section)) , $linktitle, array('class' => 'section-go-link btn btn-secondary'));

        // Output section activities summary
        $output .= html_writer::start_tag('div', array(
            'class' => 'section-summary-activities'
        ));

        $output .= html_writer::tag('span', get_string('section_mods', 'theme_fordson') , array(
            'class' => 'activity-count'
        ));
        foreach ($sectionmods as $mod) {
            $output .= html_writer::start_tag('span', array(
                'class' => 'activity-count'
            ));
            $output .= $mod['name'] . ': ' . $mod['count'];
            $output .= html_writer::end_tag('span');
        }

        // Output section completion data.
        if ($total > 0) {
            $a = new stdClass;
            $a->complete = $complete;
            $a->total = $total;
            $output .= '<br>';
            $output .= html_writer::tag('span', get_string('progresstotal', 'completion', $a) , array(
                'class' => 'activity-count'
            ));
        }

        $output .= html_writer::end_tag('div');

        return $output;
    }

}