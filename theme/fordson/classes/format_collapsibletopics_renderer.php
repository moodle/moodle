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
 * Overridden collapsibletopics format renderer class
 *
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//namespace theme_fordson\output;

defined('MOODLE_INTERNAL') || die();
global $PAGE;

// So that theme users that do not want collapsibletopics format will not get errors.
if (file_exists($CFG->dirroot . '/course/format/collapsibletopics/renderer.php') && isset($PAGE->theme->settings->integrationcollapsibletopics) && $PAGE->theme->settings->integrationcollapsibletopics == 1) {


    require_once($CFG->dirroot . '/course/format/collapsibletopics/renderer.php');

    /**
     * Overridden collapsibletopics format renderer class definition
     *
     * @package    theme_receptic
     * @author     Jean-Roch Meurisse
     * @copyright  2016 - Cellule TICE - Unversite de Namur
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class theme_fordson_format_collapsibletopics_renderer extends format_collapsibletopics_renderer {


	// Fordson remove expand All button
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        if (!isset($course->coursedisplay)) {
            $course->coursedisplay = COURSE_DISPLAY_SINGLEPAGE;
        }

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list();
        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                $this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
                echo '<div class="collapsible-actions" >
    <a href="#" class="expandall" role="button">' . get_string('expandall') . '
    </a>
</div>';
                // 0-section is displayed a little different then the others.
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    $this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
                    $modules = $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $modules;
                    
                    echo $this->section_footer();
                   } 
                continue;
            }
            if ($section > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $thissection->uservisible ||
                ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo))
                || (!$thissection->visible && !$course->hiddensections);
            if (!$showsection) {
                continue;
            }

            $modules = $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->section_header($thissection, $course, false, 0);

            if ($thissection->uservisible) {
                echo $modules;
            }

            echo $this->section_footer();
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo $this->change_number_sections($course, 0);
        } else {
            echo $this->end_section_list();
        }
    }

        /**
         * Overrides format_section_renderer_base
         * Generate the display of the header part of a section before
         * course modules are included
         *
         * @param stdClass $section The course_section entry from DB
         * @param stdClass $course The course entry from DB
         * @param bool $onsectionpage true if being printed on a single-section page
         * @param int $sectionreturn The section to return to after an action
         * @return string HTML to output.
         */
       protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
            global $PAGE;

            $o = '';
            $sectionstyle = '';

            if ($course->sectionprogress) {
                $total = 0;
                $complete = 0;
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
                            } else {
                                $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                                $sectionmods[$thismod->modname]['count'] = 1;
                            }
                            if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                                $total++;
                                $completiondata = $completioninfo->get_data($thismod, true);
                                if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                                        $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                                    $complete++;
                                }
                            }
                        }
                    }
                }
            }

            if ($section->section != 0) {
                // Only in the non-general sections.
                if (!$section->visible) {
                    $sectionstyle = ' hidden';
                } else if (course_get_format($course)->is_section_current($section)) {
                    $sectionstyle = ' current';
                }
            }

            $o .= html_writer::start_tag('li', array('id' => 'section-' . $section->section,
                'class' => 'section main clearfix fordsoncourseformat' . $sectionstyle, 'role' => 'region',
                'aria-label' => get_section_name($course, $section)));

            // Create a span that contains the section title to be used to create the keyboard section move menu.
            $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

            $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
            $o .= html_writer::start_tag('div', array('class' => 'content'));

            // When not on a section page, we display the section titles except the general section if null.
            $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

            $classes = ' accesshide';
            if ($hasnamenotsecpg || $hasnamesecpg) {
                $classes = '';
            }
            if (!$PAGE->user_is_editing()) {
            $sectionname = html_writer::tag('span', $this->section_title_without_link($section, $course),
                array('class' => 'sectionname'));
            // Add collapse toggle.
            if (course_get_format($course)->is_section_current($section)) {
                if ($course->sectionprogress && $total > 0) {
                    $o .= $this->section_progressbar($total, $complete);
                }
                $o .= '<h3 class="sectionname">' . $sectionname . '</h3> ';
            } else if ($section->section != 0) {
                if ($course->sectionprogress && $total > 0) {
                    $o .= $this->section_progressbar($total, $complete);
                }
                $o .= '<h3 class="sectionname">' . $sectionname . '</h3> ';
            } else if ($section->section == 0 && !is_null($section->name)) {
                $o .= $this->output->heading($sectionname, 3, 'sectionname' . $classes);
            }
            // End collapse toggle.

            $o .= '<div class="clearfix">';
            $o .= $this->section_availability($section) . '</div>';
            if ($section->uservisible || $section->visible) {
                // Show summary if section is available or has availability restriction information.
                // Do not show summary if section is hidden but we still display it because of course setting
                // "Hidden sections are shown in collapsed form".
                $o .= $this->section_summary($section, $course, null);
            }

            } else {
                $sectionname = html_writer::tag('span', $this->section_title_without_link($section, $course));

                $o .= '<div class="clearfix">' . $this->output->heading($sectionname, 3, 'sectionname' . $classes);
                $o .= $this->section_availability($section) . '</div>';
                if ($section->uservisible || $section->visible) {
                    // Show summary if section is available or has availability restriction information.
                    // Do not show summary if section is hidden but we still display it because of course setting
                    // "Hidden sections are shown in collapsed form".
                    $o .= $this->section_summary($section, $course, null);
                }
            }
            if (course_get_format($course)->is_section_current($section)) {
                $classes = 'collapse show';
            } else if ($section->section != 0) {
                $classes = 'collapse';
            } else {
                $classes = '';
            }
            $o .= '<div id="collapse-' .
                $section->section .
                '" class="' .
                $classes .
                '" role="tabpanel" aria-labelledby="heading' .
                $section->section .
                '">' .
                '<span class="hidden">' . $sectionname . '</span>';

            return $o;
        }

        
        /**
         * Override to separate section summary from section name.
         * @param stdClass $section
         * @param stdClass $course
         * @param array $mods
         * @return string
         */
        protected function section_summary($section, $course, $mods) {
            $activities = $this->section_activity_summary($section, $course, null);
            $courserenderer = $this->page->get_renderer('core', 'course');
            $o = '';
            
            $o .= html_writer::start_tag('div', array('class' => 'summarytext'));
            $o .= $this->format_summary_text($section);
            if ($section->section != 0) {
	            $o .= '<div class="fhscoursebutton"><a class="sectiontoggle btn btn-primary' .
	                        '" data-toggle="collapse" data-parent=".accordion" href="#collapse-' .
	                        $section->section .
	                        '" aria-expanded="true" aria-controls="collapse-' .
	                        $section->section .
	                        '"> ' . get_string('viewfcfmodules', 'theme_fordson') . ' </a> </div>';

	        }
            $o .= '<div>' . $activities . '</div> ';
            $o .= $courserenderer->course_section_add_cm_control($course, $section->section, 0);
            $o .= html_writer::end_tag('div');
            

            return $o;
        }

    // End class    
    }
}