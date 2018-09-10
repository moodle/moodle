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
 * Onetopic renderer logic implementation.
 *
 * @since 2.0
 * @package format_onetopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

/**
 * Basic renderer for onetopic format.
 *
 * @copyright 2012 David Herney Bernal - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_onetopic_renderer extends format_section_renderer_base {


    /** @var stdClass Local format data */
    protected $_format_data;

    /** @var stdClass Reference to current course */
    protected $_course;

    /**
     * Course sections length.
     * @var int numsections
     */
    public $numsections;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        // Since format_topics_renderer::section_edit_controls() only displays the 'Set current section'
        // control when editing mode is on we need to be sure that the link 'Turn editing mode on' is
        // available for a user who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
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
        return get_string('topicoutline');
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
     * Generate next/previous section links for navigation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;

        while ((($back > 0 && $course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) ||
                ($back >= 0 && $course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE)) && empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $this->numsections && empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE, $OUTPUT;;

        $realcoursedisplay = $course->realcoursedisplay;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $course->realcoursedisplay = $realcoursedisplay;
        $sections = $modinfo->get_section_info_all();

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $formatdata = new stdClass();
        $formatdata->mods = $mods;
        $formatdata->modinfo = $modinfo;
        $this->_course = $course;
        $this->_format_data = $formatdata;

        // General section if non-empty and course_display is multiple.
        if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $thissection = $sections[0];
            if ((($thissection->visible && $thissection->available) || $canviewhidden) &&
                    ($thissection->summary || $thissection->sequence || $PAGE->user_is_editing() ||
                    (string)$thissection->name !== '')) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true);

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);

                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section onetopic'));

        // Move controls.
        $canmove = false;
        if ($PAGE->user_is_editing() && has_capability('moodle/course:movesections', $context) && $displaysection > 0) {
            $canmove = true;
        }
        $movelisthtml = '';

        // Init custom tabs.
        $section = 0;

        $sectionmenu = array();
        $tabs = array();
        $inactivetabs = array();

        $defaulttopic = -1;

        while ($section <= $this->numsections) {

            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

            $thissection = $sections[$section];

            $showsection = true;
            if (!$thissection->visible || !$thissection->available) {
                $showsection = false;
            } else if ($section == 0 && !($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())) {
                $showsection = false;
            }

            if (!$showsection) {
                $showsection = $canviewhidden || !$course->hiddensections;
            }

            if (isset($displaysection)) {
                if ($showsection) {

                    if ($defaulttopic < 0) {
                        $defaulttopic = $section;

                        if ($displaysection == 0) {
                            $displaysection = $defaulttopic;
                        }
                    }

                    $formatoptions = course_get_format($course)->get_format_options($thissection);

                    $sectionname = get_section_name($course, $thissection);

                    if ($displaysection != $section) {
                        $sectionmenu[$section] = $sectionname;
                    }

                    $customstyles = '';
                    $level = 0;
                    if (is_array($formatoptions)) {

                        if (!empty($formatoptions['fontcolor'])) {
                            $customstyles .= 'color: ' . $formatoptions['fontcolor'] . ';';
                        }

                        if (!empty($formatoptions['bgcolor'])) {
                            $customstyles .= 'background-color: ' . $formatoptions['bgcolor'] . ';';
                        }

                        if (!empty($formatoptions['cssstyles'])) {
                            $customstyles .= $formatoptions['cssstyles'] . ';';
                        }

                        if (isset($formatoptions['level'])) {
                            $level = $formatoptions['level'];
                        }
                    }

                    if ($section == 0) {
                        $url = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => 0));
                    } else {
                        $url = course_get_url($course, $section);
                    }

                    $specialstyle = 'tab_position_' . $section . ' tab_level_' . $level;
                    if ($course->marker == $section) {
                        $specialstyle = ' marker ';
                    }

                    if (!$thissection->visible || !$thissection->available) {
                        $specialstyle .= ' dimmed ';

                        if (!$canviewhidden) {
                            $inactivetabs[] = "tab_topic_" . $section;
                        }
                    }

                    $newtab = new tabobject("tab_topic_" . $section, $url,
                    '<div style="' . $customstyles . '" class="tab_content ' . $specialstyle . '">' .
                    '<span>' . $sectionname . "</span></div>", $sectionname);

                    if (is_array($formatoptions) && isset($formatoptions['level'])) {

                        if ($formatoptions['level'] == 0 || count($tabs) == 0) {
                            $tabs[] = $newtab;
                            $newtab->level = 1;
                        } else {
                            $parentindex = count($tabs) - 1;
                            if (!is_array($tabs[$parentindex]->subtree)) {
                                $tabs[$parentindex]->subtree = array();
                            } else if (count($tabs[$parentindex]->subtree) == 0) {
                                $tabs[$parentindex]->subtree[0] = clone($tabs[$parentindex]);
                                $tabs[$parentindex]->subtree[0]->id .= '_index';
                                $parentsection = $sections[$section - 1];
                                $parentformatoptions = course_get_format($course)->get_format_options($parentsection);
                                if ($parentformatoptions['firsttabtext']) {
                                    $firsttabtext = $parentformatoptions['firsttabtext'];
                                } else {
                                    $firsttabtext = get_string('index', 'format_onetopic');
                                }
                                $tabs[$parentindex]->subtree[0]->text = '<div class="tab_content tab_initial">' .
                                                                        $firsttabtext . "</div>";
                                $tabs[$parentindex]->subtree[0]->level = 2;

                                if ($displaysection == $section - 1) {
                                    $tabs[$parentindex]->subtree[0]->selected = true;
                                }
                            }
                            $newtab->level = 2;
                            $tabs[$parentindex]->subtree[] = $newtab;
                        }
                    } else {
                        $tabs[] = $newtab;
                    }

                    // Init move section list.
                    if ($canmove) {
                        if ($section > 0) { // Move section.
                            $baseurl = course_get_url($course, $displaysection);
                            $baseurl->param('sesskey', sesskey());

                            $url = clone($baseurl);

                            $url->param('move', $section - $displaysection);

                            // Define class from sublevels in order to move a margen in the left.
                            // Not apply if it is the first element (condition !empty($movelisthtml))
                            // because the first element can't be a sublevel.
                            $liclass = '';
                            if (is_array($formatoptions) && isset($formatoptions['level']) && $formatoptions['level'] > 0 &&
                                    !empty($movelisthtml)) {
                                $liclass = 'sublevel';
                            }

                            if ($displaysection != $section) {
                                $movelisthtml .= html_writer::tag('li', html_writer::link($url, $sectionname),
                                                array('class' => $liclass));
                            } else {
                                $movelisthtml .= html_writer::tag('li', $sectionname, array('class' => $liclass));
                            }
                        } else {
                            $movelisthtml .= html_writer::tag('li', $sectionname);
                        }
                    }
                    // End move section list.
                }
            }

            $section++;
        }

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);
        $sectiontitle = '';

        if (!$course->hidetabsbar && count($tabs[0]) > 0) {

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {

                // Increase number of sections.
                $straddsection = get_string('increasesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                        'increase' => true,
                        'sesskey' => sesskey(),
                        'insertsection' => 0));
                $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
                $tabs[] = new tabobject("tab_topic_add", $url, $icon, s($straddsection));

            }

            $sectiontitle .= $OUTPUT->tabtree($tabs, "tab_topic_" . $displaysection, $inactivetabs);
        }

        echo $sectiontitle;

        if ($sections[$displaysection]->uservisible || $canviewhidden) {

            if ($course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE || $displaysection !== 0) {
                // Now the list of sections.
                echo $this->start_section_list();

                // The requested section page.
                $thissection = $sections[$displaysection];
                echo $this->section_header($thissection, $course, true);
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $completioninfo->display_help_icon();

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {

            echo '<br class="utilities-separator" />';
            print_collapsible_region_start('move-list-box clearfix collapsible mform', 'course_format_onetopic_config_movesection',
                get_string('utilities', 'format_onetopic'), '', true);

            // Move controls.
            if ($canmove && !empty($movelisthtml)) {
                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('movesectionto', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo html_writer::tag('ul', $movelisthtml, array('class' => 'move-list'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('movesectionto_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            $baseurl = course_get_url($course, $displaysection);
            $baseurl->param('sesskey', sesskey());

            $url = clone($baseurl);

            global $USER, $OUTPUT;
            if (isset($USER->onetopic_da[$course->id]) && $USER->onetopic_da[$course->id]) {
                $url->param('onetopic_da', 0);
                $textbuttondisableajax = get_string('enable', 'format_onetopic');
            } else {
                $url->param('onetopic_da', 1);
                $textbuttondisableajax = get_string('disable', 'format_onetopic');
            }

            echo html_writer::start_div("form-item clearfix");
                echo html_writer::start_div("form-label");
                    echo html_writer::tag('label', get_string('disableajax', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-setting");
                    echo html_writer::link($url, $textbuttondisableajax);
                echo html_writer::end_div();
                echo html_writer::start_div("form-description");
                    echo html_writer::tag('p', get_string('disableajax_help', 'format_onetopic'));
                echo html_writer::end_div();
            echo html_writer::end_div();

            // Duplicate current section option.
            if (has_capability('moodle/course:manageactivities', $context)) {
                $urlduplicate = new moodle_url('/course/format/onetopic/duplicate.php',
                                array('courseid' => $course->id, 'section' => $displaysection, 'sesskey' => sesskey()));

                $link = new action_link($urlduplicate, get_string('duplicate', 'format_onetopic'));
                $link->add_action(new confirm_action(get_string('duplicate_confirm', 'format_onetopic'), null,
                    get_string('duplicate', 'format_onetopic')));

                echo html_writer::start_div("form-item clearfix");
                    echo html_writer::start_div("form-label");
                        echo html_writer::tag('label', get_string('duplicatesection', 'format_onetopic'));
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-setting");
                        echo $this->render($link);
                    echo html_writer::end_div();
                    echo html_writer::start_div("form-description");
                        echo html_writer::tag('p', get_string('duplicatesection_help', 'format_onetopic'));
                    echo html_writer::end_div();
                echo html_writer::end_div();
            }

            echo html_writer::start_div("form-item clearfix form-group row fitem");
                echo $this->change_number_sections($course, 0);
            echo html_writer::end_div();

            print_collapsible_region_end();
        }
    }

    /**
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
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
                'class' => 'section main clearfix'.$sectionstyle, 'role' => 'region',
                'aria-label' => get_section_name($course, $section)));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->section != 0 || $course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE || (string)$section->name == '') {
            $classes = ' accesshide';
            $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        } else {
            $classes = '';
            $sectionname = html_writer::tag('span', get_section_name($course, $section));
        }

        $o .= $this->output->heading($sectionname, 3, 'sectionname' . $classes);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
            has_capability('moodle/course:viewhiddensections', $context));

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
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $isstealth = $section->section > $this->numsections;
        $controls = array();
        if (!$isstealth && $section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
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
        if (array_key_exists("delete", $parentcontrols)) {
            $url = new moodle_url('/course/editsection.php', array(
                    'id' => $section->id,
                    'sr' => $section->section - 1,
                    'delete' => 1,
                    'sesskey' => sesskey()));
            $parentcontrols['delete']['url'] = $url;
        }

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
     * Generate html for a section summary text
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {

        if ($this->_course->templatetopic != format_onetopic::TEMPLATETOPIC_NOT) {
            $section->summary = $this->replace_resources($section);
        }

        return parent::format_summary_text($section);
    }

    /**
     * Process the template.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    private function replace_resources ($section) {

        global $CFG, $USER, $PAGE;

        static $initialised;

        static $groupbuttons;
        static $groupbuttonslink;
        static $strunreadpostsone;
        static $usetracking;
        static $groupings;

        $course = $this->_course;
        $completioninfo = new completion_info($course);

        if (!isset($initialised)) {
            $groupbuttons     = ($course->groupmode || (!$course->groupmodeforce));
            $groupbuttonslink = (!$course->groupmodeforce);
            include_once($CFG->dirroot.'/mod/forum/lib.php');
            if ($usetracking = forum_tp_can_track_forums()) {
                $strunreadpostsone = get_string('unreadpostsone', 'forum');
            }
            $initialised = true;
        }

        $labelformatoptions = new stdclass();
        $labelformatoptions->noclean = true;

        // Casting $course->modinfo to string prevents one notice when the field is null.
        $modinfo = $this->_format_data->modinfo;

        $summary = $section->summary;

        $htmlresource = '';
        $htmlmore     = '';

        if (!empty($section->sequence)) {
            $sectionmods = explode(",", $section->sequence);

            $objreplace = new format_onetopic_replace_regularexpression();

            $showyuidialogue = false;
            foreach ($sectionmods as $modnumber) {

                if (empty($this->_format_data->mods[$modnumber])) {
                    continue;
                }

                $mod = $this->_format_data->mods[$modnumber];

                if ($mod->modname == "label") {
                    continue;
                }

                $instancename = format_string($modinfo->cms[$modnumber]->name, true, $course->id);

                // Display the link to the module (or do nothing if module has no url).
                $cmname = $this->courserenderer->course_section_cm_name($mod);

                if (!empty($cmname)) {
                    $cmname = str_replace('<div ', '<span ', $cmname);
                    $cmname = str_replace('</div>', '</span>', $cmname);
                    $htmlresource = $cmname . $mod->afterlink;
                } else {
                    $htmlresource = '';
                }

                // If there is content but NO link (eg label), then display the
                // content here (BEFORE any icons). In this case cons must be
                // displayed after the content so that it makes more sense visually
                // and for accessibility reasons, e.g. if you have a one-line label
                // it should work similarly (at least in terms of ordering) to an
                // activity.
                $contentpart = $this->courserenderer->course_section_cm_text($mod);

                $url = $mod->url;
                if (!empty($url)) {
                    // If there is content AND a link, then display the content here
                    // (AFTER any icons). Otherwise it was displayed before.
                    $contentpart = str_replace('<div ', '<span ', $contentpart);
                    $contentpart = str_replace('</div>', '</span>', $contentpart);
                    $htmlresource .= $contentpart;
                }

                $availabilitytext = trim($this->courserenderer->course_section_cm_availability($mod));

                if (!empty($availabilitytext)) {
                    $uniqueid = 'format_onetopic_winfo_' . time() . '-' . rand(0, 1000);
                    $htmlresource .= '<span class="iconhelp" data-infoid="' . $uniqueid . '">' .
                                        $this->output->pix_icon('a/help', get_string('help')) .
                                     '</span>';

                    $htmlmore .= '<div id="' . $uniqueid . '" class="availability_info_box" style="display: none;">' .
                        $availabilitytext . '</div>';

                    $showyuidialogue = true;
                }

                // Replace the link in pattern: [[resource name]].
                $objreplace->_string_replace = $htmlresource;
                $objreplace->_string_search = $instancename;

                $newsummary = preg_replace_callback("/(\[\[)(([<][^>]*>)*)((" . preg_quote($objreplace->_string_search, '/') .
                    ")(:?))([^\]]*)\]\]/i", array($objreplace, "replace_tag_in_expresion"), $summary);

                if ($newsummary != $summary) {
                    unset($this->_format_data->mods[$modnumber]);
                }

                $summary = $newsummary;
            }

            if ($showyuidialogue) {
                $PAGE->requires->yui_module('moodle-core-notification-dialogue', 'M.course.format.dialogueinit');
            }

        }

        if ($this->_course->templatetopic_icons == 0) {
            $summary = '<span class="onetopic_hideicons">' . $summary . '</span>';
        }

        return $summary . $htmlmore;

    }


    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param array $displayoptions
     * @return void
     */
    public function custom_course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = $this->_format_data->modinfo;

        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // Check if we are currently in the process of moving a module with JavaScript disabled.
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one).
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {

                // Custom modification in order to hide resources if they are shown in summary.
                if (!$this->courserenderer->page->user_is_editing() && !isset($this->_format_data->mods[$modnumber])) {
                    continue;
                }
                // End of custom modification.

                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // Do not display moving mod.
                    continue;
                }

                if ($modulehtml = $this->courserenderer->course_section_cm_list_item($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->courserenderer->output->render($movingpix),
                            array('title' => $strmovefull)), array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->courserenderer->output->render($movingpix),
                        array('title' => $strmovefull)), array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }

}
