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
 * Base renderer for outputting course formats.
 *
 * @package core
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */

defined('MOODLE_INTERNAL') || die();


/**
 * This is a convenience renderer which can be used by section based formats
 * to reduce code duplication. It is not necessary for all course formats to
 * use this and its likely to change in future releases.
 *
 * @package core
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */
abstract class format_section_renderer_base extends plugin_renderer_base {

    /** @var core_course_renderer contains instance of core course renderer */
    protected $courserenderer;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    abstract protected function start_section_list();

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    abstract protected function end_section_list();

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    abstract protected function page_title();

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        $title = get_section_name($course, $section);
        $url = course_get_url($course, $section->section, array('navigation' => true));
        if ($url) {
            $title = html_writer::link($url, $title);
        }
        return $title;
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return get_section_name($course, $section);
    }

    /**
     * Generate the edit control action menu
     *
     * @param array $controls The edit control items from section_edit_control_items
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function section_edit_control_menu($controls, $course, $section) {
        $o = "";
        if (!empty($controls)) {
            $menu = new action_menu();
            $menu->set_menu_trigger(get_string('edit'));
            $menu->attributes['class'] .= ' section-actions';
            foreach ($controls as $value) {
                $url = empty($value['url']) ? '' : $value['url'];
                $icon = empty($value['icon']) ? '' : $value['icon'];
                $name = empty($value['name']) ? '' : $value['name'];
                $attr = empty($value['attr']) ? array() : $value['attr'];
                $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                $alt = empty($value['pixattr']['alt']) ? '' : $value['pixattr']['alt'];
                $al = new action_menu_link_secondary(
                    new moodle_url($url),
                    new pix_icon($icon, $alt, null, array('class' => "smallicon " . $class)),
                    $name,
                    $attr
                );
                $menu->add($al);
            }

            $o .= html_writer::div($this->render($menu), 'section_action_menu',
                array('data-sectionid' => $section->id));
        }

        return $o;
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
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
        $o .= $this->section_edit_control_menu($controls, $course, $section);

        return $o;
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
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o = get_accesshide(get_string('currentsection', 'format_'.$course->format));
            }
        }

        return $o;
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
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     * @deprecated since Moodle 3.0 MDL-48947 - please do not use this function any more.
     * @see format_section_renderer_base::section_edit_control_items()
     */
    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $controls = array();
        $items = $this->section_edit_control_items($course, $section, $onsectionpage);

        foreach ($items as $key => $item) {
                $url = empty($item['url']) ? '' : $item['url'];
                $icon = empty($item['icon']) ? '' : $item['icon'];
                $name = empty($item['name']) ? '' : $item['name'];
                $attr = empty($item['attr']) ? '' : $item['attr'];
                $class = empty($item['pixattr']['class']) ? '' : $item['pixattr']['class'];
                $alt = empty($item['pixattr']['alt']) ? '' : $item['pixattr']['alt'];
                $controls[$key] = html_writer::link(
                    new moodle_url($url), $this->output->pix_icon($icon, $alt),
                    $attr);
        }

        debugging('section_edit_controls() is deprecated, please use section_edit_control_items() instead.', DEBUG_DEVELOPER);
        return $controls;
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

        $sectionreturn = $onsectionpage ? $section->section : null;

        $coursecontext = context_course::instance($course->id);
        $numsections = course_get_format($course)->get_last_section_number();
        $isstealth = $section->section > $numsections;

        $baseurl = course_get_url($course, $sectionreturn);
        $baseurl->param('sesskey', sesskey());

        $controls = array();

        if (!$isstealth && has_capability('moodle/course:update', $coursecontext)) {
            if ($section->section > 0
                && get_string_manager()->string_exists('editsection', 'format_'.$course->format)) {
                $streditsection = get_string('editsection', 'format_'.$course->format);
            } else {
                $streditsection = get_string('editsection');
            }

            $controls['edit'] = array(
                'url'   => new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn)),
                'icon' => 'i/settings',
                'name' => $streditsection,
                'pixattr' => array('class' => '', 'alt' => $streditsection),
                'attr' => array('class' => 'icon edit', 'title' => $streditsection));
        }

        if ($section->section) {
            $url = clone($baseurl);
            if (!$isstealth) {
                if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
                    if ($section->visible) { // Show the hide/show eye.
                        $strhidefromothers = get_string('hidefromothers', 'format_'.$course->format);
                        $url->param('hide', $section->section);
                        $controls['visiblity'] = array(
                            'url' => $url,
                            'icon' => 'i/hide',
                            'name' => $strhidefromothers,
                            'pixattr' => array('class' => '', 'alt' => $strhidefromothers),
                            'attr' => array('class' => 'icon editing_showhide', 'title' => $strhidefromothers,
                                'data-sectionreturn' => $sectionreturn, 'data-action' => 'hide'));
                    } else {
                        $strshowfromothers = get_string('showfromothers', 'format_'.$course->format);
                        $url->param('show',  $section->section);
                        $controls['visiblity'] = array(
                            'url' => $url,
                            'icon' => 'i/show',
                            'name' => $strshowfromothers,
                            'pixattr' => array('class' => '', 'alt' => $strshowfromothers),
                            'attr' => array('class' => 'icon editing_showhide', 'title' => $strshowfromothers,
                                'data-sectionreturn' => $sectionreturn, 'data-action' => 'show'));
                    }
                }

                if (!$onsectionpage) {
                    if (has_capability('moodle/course:movesections', $coursecontext)) {
                        $url = clone($baseurl);
                        if ($section->section > 1) { // Add a arrow to move section up.
                            $url->param('section', $section->section);
                            $url->param('move', -1);
                            $strmoveup = get_string('moveup');
                            $controls['moveup'] = array(
                                'url' => $url,
                                'icon' => 'i/up',
                                'name' => $strmoveup,
                                'pixattr' => array('class' => '', 'alt' => $strmoveup),
                                'attr' => array('class' => 'icon moveup', 'title' => $strmoveup));
                        }

                        $url = clone($baseurl);
                        if ($section->section < $numsections) { // Add a arrow to move section down.
                            $url->param('section', $section->section);
                            $url->param('move', 1);
                            $strmovedown = get_string('movedown');
                            $controls['movedown'] = array(
                                'url' => $url,
                                'icon' => 'i/down',
                                'name' => $strmovedown,
                                'pixattr' => array('class' => '', 'alt' => $strmovedown),
                                'attr' => array('class' => 'icon movedown', 'title' => $strmovedown));
                        }
                    }
                }
            }

            if (course_can_delete_section($course, $section)) {
                if (get_string_manager()->string_exists('deletesection', 'format_'.$course->format)) {
                    $strdelete = get_string('deletesection', 'format_'.$course->format);
                } else {
                    $strdelete = get_string('deletesection');
                }
                $url = new moodle_url('/course/editsection.php', array(
                    'id' => $section->id,
                    'sr' => $sectionreturn,
                    'delete' => 1,
                    'sesskey' => sesskey()));
                $controls['delete'] = array(
                    'url' => $url,
                    'icon' => 'i/delete',
                    'name' => $strdelete,
                    'pixattr' => array('class' => '', 'alt' => $strdelete),
                    'attr' => array('class' => 'icon editing_delete', 'title' => $strdelete));
            }
        }

        return $controls;
    }

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

        // If section is hidden then display grey section link
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => $classattr, 'role'=>'region', 'aria-label'=> $title));

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o .= $this->section_availability($section);
        $o.= html_writer::start_tag('div', array('class' => 'summarytext'));

        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o.= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);

        $o .= html_writer::end_tag('div');
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
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return '';
        }

        // Generate array with count of activities in this section:
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
            // No sections
            return '';
        }

        // Output section activities summary:
        $o = '';
        $o.= html_writer::start_tag('div', array('class' => 'section-summary-activities mdl-right'));
        foreach ($sectionmods as $mod) {
            $o.= html_writer::start_tag('span', array('class' => 'activity-count'));
            $o.= $mod['name'].': '.$mod['count'];
            $o.= html_writer::end_tag('span');
        }
        $o.= html_writer::end_tag('div');

        // Output section completion data
        if ($total > 0) {
            $a = new stdClass;
            $a->complete = $complete;
            $a->total = $total;

            $o.= html_writer::start_tag('div', array('class' => 'section-summary-activities mdl-right'));
            $o.= html_writer::tag('span', get_string('progresstotal', 'completion', $a), array('class' => 'activity-count'));
            $o.= html_writer::end_tag('div');
        }

        return $o;
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
    protected function section_availability_message($section, $canviewhidden) {
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
     * Displays availability information for the section (hidden, not available unles, etc.)
     *
     * @param section_info $section
     * @return string
     */
    public function section_availability($section) {
        $context = context_course::instance($section->course);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        return html_writer::div($this->section_availability_message($section, $canviewhidden), 'section_availability');
    }

    /**
     * Show if something is on on the course clipboard (moving around)
     *
     * @param stdClass $course The course entry from DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return string HTML to output.
     */
    protected function course_activity_clipboard($course, $sectionno = null) {
        global $USER;

        $o = '';
        // If currently moving a file then show the current clipboard.
        if (ismoving($course->id)) {
            $url = new moodle_url('/course/mod.php',
                array('sesskey' => sesskey(),
                      'cancelcopy' => true,
                      'sr' => $sectionno,
                )
            );

            $o.= html_writer::start_tag('div', array('class' => 'clipboard'));
            $o.= strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
            $o.= ' ('.html_writer::link($url, get_string('cancel')).')';
            $o.= html_writer::end_tag('div');
        }

        return $o;
    }

    /**
     * Generate next/previous section links for naviation
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
        while ($back > 0 and empty($links['previous'])) {
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
        $numsections = course_get_format($course)->get_last_section_number();
        while ($forward <= $numsections and empty($links['next'])) {
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
     * Generate the header html of a stealth section
     *
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return string HTML to output.
     */
    protected function stealth_section_header($sectionno) {
        $o = '';
        $o.= html_writer::start_tag('li', array('id' => 'section-'.$sectionno, 'class' => 'section main clearfix orphaned hidden'));
        $o.= html_writer::tag('div', '', array('class' => 'left side'));
        $course = course_get_format($this->page->course)->get_course();
        $section = course_get_format($this->page->course)->get_section($sectionno);
        $rightcontent = $this->section_right_content($section, $course, false);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));
        $o.= $this->output->heading(get_string('orphanedactivitiesinsectionno', '', $sectionno), 3, 'sectionname');
        return $o;
    }

    /**
     * Generate footer html of a stealth section
     *
     * @return string HTML to output.
     */
    protected function stealth_section_footer() {
        $o = html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Generate the html for a hidden section
     *
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
     * @return string HTML to output.
     */
    protected function section_hidden($sectionno, $courseorid = null) {
        if ($courseorid) {
            $sectionname = get_section_name($courseorid, $sectionno);
            $strnotavailable = get_string('notavailablecourse', '', $sectionname);
        } else {
            $strnotavailable = get_string('notavailable');
        }

        $o = '';
        $o.= html_writer::start_tag('li', array('id' => 'section-'.$sectionno, 'class' => 'section main clearfix hidden'));
        $o.= html_writer::tag('div', '', array('class' => 'left side'));
        $o.= html_writer::tag('div', '', array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));
        $o.= html_writer::tag('div', $strnotavailable);
        $o.= html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        global $CFG;
        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            // This section doesn't exist or is not available for the user.
            // We actually already check this in course/view.php but just in case exit from this function as well.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

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
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display,
            // OR it is hidden but the course has a setting to display hidden sections as unavilable.
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo)) ||
                    (!$thissection->visible && !$course->hiddensections);
            if (!$showsection) {
                continue;
            }

            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
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
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     * @return string
     */
    protected function change_number_sections($course, $sectionreturn = null) {
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
     * Generate html for a section summary text
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {
        $context = context_course::instance($section->course);
        $summarytext = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php',
            $context->id, 'course', 'section', $section->id);

        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        return format_text($summarytext, $section->summaryformat, $options);
    }
}
