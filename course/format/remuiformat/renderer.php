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
 * Cards Format - A topics based format that uses card layout to diaply the content.
 *
 * @package format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/mod_stats.php');

/**
 * Remui format renderer class
 */
class format_remuiformat_renderer extends format_section_renderer_base {

    /**
     * Our course format object as defined in lib.php.
     * @var format_remuiformat
     */
    protected $courseformat;
    /**
     * Our custom course module renderer.
     * @var course_renderer
     */
    protected $coursemodulerenderer;
    /**
     * Our setting controller.
     * @var object
     */
    protected $settingcontroller;
    /**
     * Our mod stats controller.
     * @var \format_remuiformat\ModStats
     */
    protected $modstats;
    /**
     * Course format settings.
     * @var array
     */
    protected $settings;

    /**
     * Constructor method, calls the parent constructor
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course);
        $this->settings = $this->courseformat->get_settings();
        $this->modstats = \format_remuiformat\ModStats::getinstance();
        // Since format_remuiformat_renderer::section_edit_controls()
        // only displays the 'Set current section' control when editing mode is on
        // we need to be sure that the link 'Turn editing mode on' is available
        // for a user who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Get curserenderer object
     * @return object courserenderer
     */
    public function get_base_renderer() {
        return $this->courserenderer;
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'cards'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_remuiformat');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate next/previous section links for naviation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    public function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('class' => 'btn btn-inverse btn-sm');
                $prevsectionname = get_section_name($course, $sections[$back]);
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text btn btn-inverse btn-sm');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .=
                (core_text::strlen($prevsectionname) > 15) ? core_text::substr($prevsectionname, 0, 15)."..." : $prevsectionname;
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($forward <= $numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('class' => 'btn btn-inverse btn-sm');
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text btn btn-inverse btn-sm');
                }
                $nextsectionname = get_section_name($course, $sections[$forward]);
                $nextlink =
                (core_text::strlen($nextsectionname) > 15) ? core_text::substr($nextsectionname, 0, 15)."..." : $nextsectionname;
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Generate a summary of the activites in a section
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    public function section_activity_summary($section, $course, $mods) {
        unset($mods);
        $modinfo = get_fast_modinfo($course);
        $output = array(
            "activityinfo" => "",
            "progressinfo" => ""
        );
        if (empty($modinfo->sections[$section->section])) {
            return $output;
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

        if (empty($sectionmods)) {
            // No sections.
            return $output;
        }

        // Output section activities summary.
        $o = '';
        $o .= html_writer::start_tag('div', array('class' => 'section-summary-activities pb-10'));
        foreach ($sectionmods as $mod) {
            $o .= html_writer::start_tag('p', array('class' => 'section-mod-details'));
            $o .= $mod['name'].': '.$mod['count'];
            $o .= html_writer::end_tag('p');
        }
        $o .= html_writer::end_tag('div');
        $output['activityinfo'] = $o;
        $o = '';
        // Output section completion data.
        if ($total > 0) {
            $a = new stdClass;
            $a->complete = $complete;
            $a->total = $total;
            $completed = "";
            $percentage = round(($a->complete / $a->total) * 100 , 0);
            if ($a->complete == $a->total) {
                $completed = 'completed';
            }
            $o .= html_writer::start_tag('div', array('class' => 'd-flex'));
            $o .= html_writer::start_tag('div', array('class' => 'section-summary-percentage px-10'));
            $o .= html_writer::tag(
                'p', get_string('progress', 'format_remuiformat'), array('class' => 'progress-title m-0 text-muted')
            );
            $o .= html_writer::tag('p', $a->complete.' / '.$a->total, array('class' => 'activity-count m-0 text-right'));
            $o .= html_writer::end_tag('div');
            $o .= html_writer::start_tag('div', array('class' => 'pchart', 'data-percent' => $percentage));
            $o .= html_writer::tag(
                'span', ' <i class="fa fa-check" aria-hidden="true"></i>', array('class' => 'activity-check '.$completed)
            );
            $o .= html_writer::end_tag('div');
            $o .= html_writer::end_tag('div');
        }
        $output['progressinfo'] = $o;
        return $output;
    }

    /**
     * Generate the html for a hidden section
     *
     * @param int $sectionno The section number in the course which is being displayed
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
     * @return string HTML to output.
     */
    public function section_hidden($sectionno, $courseorid = null) {
        if ($courseorid) {
            $sectionname = get_section_name($courseorid, $sectionno);
            $strnotavailable = get_string('notavailablecourse', '', $sectionname);
        } else {
            $strnotavailable = get_string('notavailable');
        }

        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$sectionno, 'class' => 'section main clearfix hidden'));
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= html_writer::tag('div', $strnotavailable);
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Generate html for a section summary text
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    public function format_summary_text($section) {
        $context = context_course::instance($section->course);
        $summarytext = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php',
            $context->id, 'course', 'section', $section->id);

        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        return format_text($summarytext, $section->summaryformat, $options);
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    public function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
        $o .= $this->section_edit_control_menu($controls, $course, $section);

        return $o;
    }

    /**
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        if (!$this->page->user_is_editing()) {
            return array();
        }
        $coursecontext = context_course::instance($course->id);
        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if ($section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthistopic = get_string('markedthistopic');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                                               'name' => $highlightoff,
                                               'pixattr' => array('class' => '', 'alt' => $markedthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markedthistopic,
                                                   'data-action' => 'removemarker'));
            } else {
                $url->param('marker', $section->section);
                $markthistopic = get_string('markthistopic');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                                               'name' => $highlight,
                                               'pixattr' => array('class' => '', 'alt' => $markthistopic),
                                               'attr' => array('class' => 'editing_highlight', 'title' => $markthistopic,
                                                   'data-action' => 'setmarker'));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    public function section_availability_message($section, $canviewhidden) {
        global $CFG;
        $o = '';
        if (!$section->visible) {
            if ($canviewhidden) {
                $o .= $this->courserenderer->availability_info(get_string('hiddenfromstudents'), 'ishidden');
            } else {
                // We are here because of the setting "Hidden sections are shown in collapsed form".
                // Student can not see the section contents but can see its name.
                $o .= $this->courserenderer->availability_info(get_string('notavailable'), 'ishidden');
            }
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $formattedinfo = \core_availability\info::format_info(
                        $section->availableinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo, 'isrestricted');
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $formattedinfo = \core_availability\info::format_info(
                        $fullinfo, $section->course);
                $o .= $this->courserenderer->availability_info($formattedinfo, 'isrestricted isfullinfo');
            }
        }
        return $o;
    }

    /**
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     * @return string
     */
    public function change_number_sections($course, $sectionreturn = null) {
        $coursecontext = context_course::instance($course->id);
        if (!has_capability('moodle/course:update', $coursecontext)) {
            return '';
        }

        $options = course_get_format($course)->get_format_options();
        $supportsnumsections = array_key_exists('numsections', $options);

        if ($supportsnumsections) {
            // Current course format has 'numsections' option, which is very confusing and we suggest course format
            // developers to get rid of it (see MDL-57769 on how to do it).
            // Display "Increase section" / "Decrease section" links.
            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                      'increase' => true,
                      'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon.get_accesshide($straddsection), array('class' => 'increase-sections'));

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                          'increase' => false,
                          'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon.get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }

            echo html_writer::end_tag('div');

        } else if (course_get_format($course)->uses_sections()) {
            // Current course format does not have 'numsections' option but it has multiple sections suppport.
            // Display the "Add section" link that will insert a section in the end.
            // Note to course format developers: inserting sections in the other positions should check both
            // capabilities 'moodle/course:update' and 'moodle/course:movesections'.
            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));
            if (get_string_manager()->string_exists('addsections', 'format_'.$course->format)) {
                $straddsections = get_string('addsections', 'format_'.$course->format);
            } else {
                $straddsections = get_string('addsections');
            }
            $url = new moodle_url('/course/changenumsections.php',
                ['courseid' => $course->id, 'insertsection' => 0, 'sesskey' => sesskey()]);
            if ($sectionreturn !== null) {
                $url->param('sectionreturn', $sectionreturn);
            }
            $icon = $this->output->pix_icon('t/add', $straddsections);
            echo html_writer::link($url, $icon . $straddsections,
                array('class' => 'add-sections', 'data-add-sections' => $straddsections));
            echo html_writer::end_tag('div');
        }
    }


    /**
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     * @return string
     */
    public function change_number_sections_context($course, $sectionreturn = null) {
        $coursecontext = context_course::instance($course->id);
        if (!has_capability('moodle/course:update', $coursecontext)) {
            return '';
        }

        $options = course_get_format($course)->get_format_options();
        $supportsnumsections = array_key_exists('numsections', $options);

        $addnewsection = new stdClass;
        $addnewsection->addsection = 1;
        $addnewsection->divid = 'changenumsections';

        if ($supportsnumsections) {

            $addnewsection->numsections = 1;
            // Current course format has 'numsections' option, which is very confusing and we suggest course format.
            // developers to get rid of it (see MDL-57769 on how to do it).
            // Display "Increase section" / "Decrease section" links.
            $straddsection = get_string('increasesections', 'moodle');

            // Increase number of sections.
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                      'increase' => true,
                      'sesskey' => sesskey()));

            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);

            $addnewsection->straddsection = $straddsection;
            $addnewsection->addurl = $url;
            $addnewsection->addicon = str_replace('icon', 'fa-4x d-block', $icon);
            $addnewsection->addurlclass = 'increase-sections';

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                          'increase' => false,
                          'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);

                $addnewsection->strremovesection = $strremovesection;
                $addnewsection->removeurl = $url;
                $addnewsection->removeicon = str_replace('icon', 'fa-4x d-block', $icon);
                $addnewsection->removeurlclass = 'reduce-sections';

            }

        } else if (course_get_format($course)->uses_sections()) {
            // Current course format does not have 'numsections' option but it has multiple sections suppport.
            // Display the "Add section" link that will insert a section in the end.
            // Note to course format developers: inserting sections in the other positions should check both
            // capabilities 'moodle/course:update' and 'moodle/course:movesections'.
            $addnewsection->numsections = 0;

            if (get_string_manager()->string_exists('addsections', 'format_'.$course->format)) {
                $straddsections = get_string('addsections', 'format_'.$course->format);
            } else {
                $straddsections = get_string('addsections');
            }
            $url = new moodle_url('/course/changenumsections.php',
                ['courseid' => $course->id, 'insertsection' => 0, 'sesskey' => sesskey()]);
            if ($sectionreturn !== null) {
                $url->param('sectionreturn', $sectionreturn);
            }
            $icon = $this->output->pix_icon('t/add', $straddsections);

            $addnewsection->straddsections = $straddsections;
            $addnewsection->url = $url;
            $addnewsection->icon = str_replace('icon', 'fa-4x d-block', $icon);
        }

        return $addnewsection;
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    public function section_left_content($section, $course, $onsectionpage) {
        $o = '';
        unset($onsectionpage);
        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o = get_accesshide(get_string('currentsection', 'format_'.$course->format));
            }
        }

        return $o;
    }

    /**
     * Renders the mutiple section page.
     * @param \format_remuiformat\output\format_remuiformat_card_all_sections_summary $section Object of the Section renderable.
     */
    public function render_card_all_sections_summary(
        \format_remuiformat\output\format_remuiformat_card_all_sections_summary $section) {
        $templatecontext = $section->export_for_template($this);
        if (isset($templatecontext->error)) {
            throw new \moodle_exception($templatecontext->error);
        } else {
            echo $this->render_from_template('format_remuiformat/card_all_sections_summary', $templatecontext);
        }
    }

    /**
     * Renders the card one section page.
     * @param \format_remuiformat\output\format_remuiformat_card_one_section $activity Object of Activity renderable
     */
    public function render_card_one_section(
        \format_remuiformat\output\format_remuiformat_card_one_section $activity) {
        $templatecontext = $activity->export_for_template($this);
        echo $this->render_from_template('format_remuiformat/card_one_section', $templatecontext);
    }

    /**
     * Renders the list all sections page with all activities.
     * @param \format_remuiformat\output\format_remuiformat_list_all_sections $section Object of the Section renderable.
     */
    public function render_list_all_sections(
        \format_remuiformat\output\format_remuiformat_list_all_sections $section) {
        $templatecontext = $section->export_for_template($this);
        if (isset($templatecontext->error)) {
            throw new \moodle_exception($templatecontext->error);
        } else {
            echo $this->render_from_template('format_remuiformat/list_all_sections', $templatecontext);
        }
    }

    /**
     * Renders the list all sections page without activities.
     * List Format -> All Section Page.
     * @param \format_remuiformat\output\format_remuiformat_list_all_sections_summary $section Object of the Section renderable.
     */
    public function render_list_all_sections_summary(
        \format_remuiformat\output\format_remuiformat_list_all_sections_summary $section) {
        $templatecontext = $section->export_for_template($this);
        if (isset($templatecontext->error)) {
            throw new \moodle_exception($templatecontext->error);
        } else {
            echo $this->render_from_template('format_remuiformat/list_all_sections_summary', $templatecontext);
        }
    }

    /**
     * Renders the list one section page.
     * @param \format_remuiformat\output\format_remuiformat_list_one_section $activity Object of Activity renderable
     */
    public function render_list_one_section(
        \format_remuiformat\output\format_remuiformat_list_one_section $activity) {
        $templatecontext = $activity->export_for_template($this);
        echo $this->render_from_template('format_remuiformat/list_one_section', $templatecontext);
    }

    /**
     * Limit string without break html tags.
     * Supports UTF8
     *
     * @param string $value
     * @param int $limit Default 100
     */
    public function abstract_html_contents($value, $limit = 100) {
        $value = preg_replace("/<img[^>]+\>/i", "", $value);

        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        // Strip text with HTML tags, sum html len tags too.
        // Is there another way to do it?
        do {
            $len = mb_strwidth( $value, 'UTF-8' );
            $lenstripped = mb_strwidth( strip_tags($value), 'UTF-8' );
            $lentags = $len - $lenstripped;

            $value = mb_strimwidth($value, 0, $limit + $lentags, '', 'UTF-8');
        } while ($lenstripped > $limit);

        // Append ...
        $value .= '...';

        // Load as HTML ignoring errors.
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $value, LIBXML_HTML_NODEFDTD);

        // Fix the html errors.
        $value = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));

        // Remove body tag.
        $value = mb_strimwidth($value, 6, mb_strwidth($value, 'UTF-8') - 13, '', 'UTF-8');

        // Remove empty tags.
        if (stripos("<source", $value) !== false) {
            $value = preg_replace(
                '/<(\w+)\b(?:\s+[\w\-.:]+(?:\s*=\s*(?:"[^"]*"|"[^"]*"|[\w\-.:]+))?)*\s*\/?>\s*<\/\1\s*>/',
                '',
                $value
            );
        }

        // Return.
        return $value;
    }
}
