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
 * Trait - format section
 * Code that is shared between course_format_topic_renderer.php and course_format_weeks_renderer.php
 * Used for section outputs.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS. (http://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;
use context_course;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content;
use renderable;
use html_writer;
use moodle_url;
use stdClass;
use theme_snap\output\core\course_renderer;
use theme_snap\renderables\course_action_section_duplicate;
use theme_snap\renderables\course_action_section_move;
use theme_snap\renderables\course_action_section_visibility;
use theme_snap\renderables\course_action_section_delete;
use theme_snap\renderables\course_action_section_highlight;
use theme_snap\renderables\course_action_section_permalink;
use theme_snap\renderables\course_section_navigation;

trait format_section_trait {

    use general_section_trait;

     static $SECTION_ACTIONS_BEFORE_MENU = 2;

    /**
     * Renders HTML to display one course module for display within a section.
     *
     * @deprecated since 4.0 - use core_course output components or course_format::course_section_updated_cm_item instead.
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course
     * @param \completion_info $completioninfo
     * @param \cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return String
     */
    public function course_section_updated_cm_item(
        course_format $format,
        \section_info $section,
        \cm_info $cm,
        array $displayoptions = []
    ) {
        global $PAGE;
        $course = $format->get_course();
        $completioninfo = new \completion_info($course);
        $render = new course_renderer($PAGE, null);
        return $render->course_section_cm_list_item_snap($course, $completioninfo, $cm, $format->get_sectionnum(),
            $displayoptions);
    }

    /**
     * Render the enable bulk editing button.
     * @param course_format $format the course format
     * @return string|null the enable bulk button HTML (or null if no bulk available).
     */
    public function bulk_editing_button(course_format $format): ?string {
        // Snap modifications to course formats do not support this feature.
        return '';
    }

    /**
     * New moodle 4.0 render_content function.
     * @param renderable $widget
     */

    public function render_content(renderable $widget) {
        // We need access to format and course to avoid more queries.
        $reflectionf = new \ReflectionClass($widget);
        $property = $reflectionf->getProperty('format');
        $property->setAccessible(true);
        $format = $property->getValue($widget);
        $reflectioncourse = new \ReflectionClass($format);
        $property = $reflectioncourse->getProperty('course');
        $property->setAccessible(true);
        $course = $property->getValue($format);

        if ($widget instanceof content) {
            $this->print_multiple_section_page($course, null, null, null, null);
        } else {
            return parent::render($widget);
        }
    }

    /**
     * Based on get_nav_links function in class format_section_renderer_base
     * This function has been modified to provide a link to section 0
     * Generate next/previous section links for navigation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the course which is being displayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        global $OUTPUT;
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
        || !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > -1 && empty($links['previous'])) {
            if ($canviewhidden
            || $sections[$back]->uservisible
            || $sections[$back]->availableinfo) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }

                $previouslink = html_writer::tag('span', $OUTPUT->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                if ($back > 0 ) {
                    $courseurl = course_get_url($course, $back);
                } else {
                    // We have to create the course section url manually if its 0.
                    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $back));
                }
                $links['previous'] = html_writer::link($courseurl, $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($forward <= $numsections && empty($links['next'])) {
            if ($canviewhidden
            || $sections[$forward]->uservisible
            || $sections[$forward]->availableinfo) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $OUTPUT->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Create target link content
     *
     * @param $name
     * @param $arrow
     * @param $string
     * @return string
     */
    private function target_link_content($name, $arrow, $string) {
        $html = html_writer::div($arrow, 'nav_icon');
        $html .= html_writer::start_span('text');
        $html .= html_writer::span($string, 'nav_guide');
        $html .= html_writer::empty_tag('br');
        $html .= $name;
        $html .= html_writer::end_tag('span');
        return $html;
    }


    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        if ($section->section === 0) {
            return [];
        }

        if ($onsectionpage) {
            $baseurl = course_get_url($course, $section->section);
        } else {
            $baseurl = course_get_url($course);
        }
        $baseurl->param('sesskey', sesskey());

        $controls = array();

        $moveaction = new course_action_section_move($course, $section, $onsectionpage);
        $visibilityaction = new course_action_section_visibility($course, $section, $onsectionpage);
        $deleteaction = new course_action_section_delete($course, $section, $onsectionpage);
        $highlightaction = new course_action_section_highlight($course, $section, $onsectionpage);
        $duplicateaction = new course_action_section_duplicate($course, $section, $onsectionpage);
        $permalinkaction = new course_action_section_permalink($course, $section, $onsectionpage);

        $actions = array(
            $moveaction,
            $visibilityaction,
            $deleteaction,
            $highlightaction,
            $duplicateaction,
            $permalinkaction,
        );

        foreach($actions as $action) {
            $controls[] = $this->render($action);
        }

        if(count($controls) > self::$SECTION_ACTIONS_BEFORE_MENU) {
            $newcontrols = array_slice($controls, 0, self::$SECTION_ACTIONS_BEFORE_MENU);
            $actionstomenu = array_slice($actions, self::$SECTION_ACTIONS_BEFORE_MENU);
            foreach ($actionstomenu as $action) {
                $action->isinmenu = true;
            }
            $menu = $this->section_edit_control_items_menued($actionstomenu, $section);
            $newcontrols[] = $menu;
            $controls = $newcontrols;
        }

        return $controls;
    }

    /**
     * Generate the dropdown to display the extra options.
     * @param $items
     */
    protected function section_edit_control_items_menued($actions, $section) {
        $data = [
            'actions' => $actions,
            'sectionid' => $section->section
        ];
        return $this->render_from_template('theme_snap/course_action_section_menu', $data);
    }

    /**
     *
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
        global $PAGE, $USER;

        $o = '';
        $sectionstyle = '';

        // We have to get the output renderer instead of using $this->output to ensure we get the non ajax version of
        // the renderer, even when via an AJAX request. The HTML returned has to be the same for all requests, even
        // ajax.
        $output = $PAGE->get_renderer('theme_snap', 'core', RENDERER_TARGET_GENERAL);
        $pagepath = $PAGE->url->get_path();
        $sectionid = optional_param('id', -1, PARAM_INT);

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current set-by-server';
                if ($pagepath !== '/course/section.php') {
                    $sectionstyle .= ' state-visible';
                }
            } else if ($course->format == 'weeks' && $sectionid == $section->id) {
                $sectionstyle .= ' state-visible set-by-server';
            }
        } else if ($course->format == "topics" && $course->marker == 0) {
            $sectionstyle = ' set-by-server';
            if ($pagepath !== '/course/section.php') {
                $sectionstyle .= ' state-visible';
            }
        }

        if ($this->is_section_conditional($section)) {
            $canviewhiddensections = has_capability(
                'moodle/course:viewhiddensections',
                context_course::instance($course->id)
            );
            if (!$section->uservisible || $canviewhiddensections) {
                $sectionstyle .= ' conditional';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle .= ' current set-by-server';
                if ($pagepath !== '/course/section.php') {
                    $sectionstyle .= ' state-visible';
                }
            }
        }

        if ($pagepath === '/course/section.php' && ($sectionid = optional_param('id', -1, PARAM_INT)) !== -1) {
            if ($sectionid == $section->id) {
                $sectionstyle .= ' state-visible';
            }
        }

        // SHAME - the tabindex is intefering with moodle js.
        // SHAME - Remove tabindex when editing menu is shown.
        $sectionarrayvars = array(
            'id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle,
            'aria-label' => get_section_name($course, $section),
            'data-id' => $section->id,
            );
        if (!$PAGE->user_is_editing()) {
            $sectionarrayvars['tabindex'] = '-1';
        }

        $o .= html_writer::start_tag('li', $sectionarrayvars);
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null.
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one.
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }

        $context = context_course::instance($course->id);

        $sectiontitle = get_section_name($course, $section);
        // Better first section title.
        if ($sectiontitle == get_string('general') && $section->section == 0) {
            $classes = '';
            $sectiontitle = get_string('introduction', 'theme_snap');
        }

        // Untitled topic title.
        $testemptytitle = get_string('section').' '.$section->section;
        $leftnav = get_config('theme_snap', 'leftnav');
        $leftnavtop = $leftnav === 'top';
        $sectionid = "sectionid-{$section->id}-title";
        if ($sectiontitle == $testemptytitle && has_capability('moodle/course:update', $context)) {
            $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
            $o .= "<h2 id='{$sectionid}' class='sectionname' data-id='{$section->id}'>";
            if ($section->section != 0 && $leftnavtop != 0 ) {
                $o .= "<span class='sectionnumber'></span>";
            }
            $o .= "<a href='$url' title='".s(get_string('editcoursetopic', 'theme_snap'))."'>";
            $o .= get_string('defaultsectiontitle', 'theme_snap')."</a></h2>";
        } else {
            if ($section->section != 0 && $leftnavtop != 0 ) {
                $sectiontitle = '<span class=\'sectionnumber\'></span>' . $sectiontitle;
            }
            $htmlheading = html_writer::tag(
                'h' . 2,
                $sectiontitle,
                array(
                    'id' => $sectionid,
                    'class' => 'sectionname',
                    'data-id' => $section->id
                ));
            $o .= "<div>" . $htmlheading . "</div>";
        }

        // Section drop zone.
        $caneditcourse = has_capability('moodle/course:update', $context);
        if ($caneditcourse && $section->section != 0) {
            $extradropclass = !empty(get_config('theme_snap', 'coursepartialrender')) ? 'partial-render' : '';
            $o .= "<a class='snap-drop section-drop " . $extradropclass . "' data-title='".
                    s($sectiontitle)."' href='#'>_</a>";
        }

        // Section editing commands.
        $sectiontoolsarray = $this->section_edit_control_items($course, $section, $sectionreturn);
        if (!empty($sectiontoolsarray)) {
            // Wrap into a list
            $sectiontoolsarray[0] = '<ul>' . $sectiontoolsarray[0];
            $sectiontoolsarray[count($sectiontoolsarray) - 1] .= '</ul>';
        }

        if (has_capability('moodle/course:update', $context) || has_capability('moodle/course:activityvisibility', $context)) {
            if (!empty($sectiontoolsarray)) {
                $sectiontools = implode(' ', $sectiontoolsarray);
                $o .= html_writer::tag('div', $sectiontools, array(
                    'class' => 'js-only snap-section-editing actions',
                    'role' => 'region',
                    'aria-label' => get_string('topicactions', 'theme_snap'),
                ));
            }
        }
        // Draft message.
        $drafticon = '<img aria-hidden="true" role="presentation" class="svg-icon" src="'.$output->image_url('/i/show').'" />';
        $o .= '<div class="snap-draft-tag snap-draft-section">'.$drafticon.' '.get_string('draft', 'theme_snap').'</div>';

        // Current section message.
        if ($course->format == "weeks") {
            $currentstring = get_string('currentsection', 'format_weeks');
        } else {
            $currentstring = get_string('highlightedsection', 'theme_snap');
        }
        $currenticon = '<img aria-hidden="true" role="presentation" class="svg-icon" src="'.$output->image_url('/i/marked').'" />';
        $o .= '<span class="snap-current-tag">' . $currenticon . ' ' . $currentstring . '</span>';

        // Availabiliy message.
        // Note - $canviewhiddensection is required so that teachers can see the availability info message permanently,
        // even if the teacher satisfies the conditions to make the section available.
        // section->availabeinfo will be empty when all conditions are met.
        $canviewhiddensections = has_capability('moodle/course:viewhiddensections', $context);
        $formattedinfo = '';
        if ($canviewhiddensections || !empty($section->availableinfo)) {
            $src = $output->image_url('conditional', 'theme');
            $conditionalicon = '<img aria-hidden="true" role="presentation" class="svg-icon" src="'.$src.'" />';
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            $formattedinfo = '';
            $displayedinfo = $canviewhiddensections ? $fullinfo : $section->availableinfo;
            if ($fullinfo) {
                $formattedinfo = \core_availability\info::format_info(
                    $displayedinfo, $section->course);
            }
        }

        if ($formattedinfo !== '') {
            $o .= '<div class="snap-conditional-tag">'.$conditionalicon.' '.$formattedinfo.'</div>';
        }

        // Section summary/body text.
        $summarylabel = get_string('summarylabel', 'theme_snap');
        $o .= "<div class='summary' role='group' aria-label='$summarylabel'>";

        $summarytext = $this->format_summary_text($section);

        $canupdatecourse = has_capability('moodle/course:update', $context);

        // Welcome message when no summary text.
        if (empty($summarytext) && $canupdatecourse) {
            $summarytext = "<p>".get_string('defaultsummary', 'theme_snap')."</p>";
            if ($section->section == 0) {
                $editorname = format_string(fullname($USER));
                $summarytext = "<p>".get_string('defaultintrosummary', 'theme_snap', $editorname)."</p>";
            }
        } else {
            $summarytext = "<div>" . $summarytext . "</div>";
        }

        $o .= $summarytext;
        if ($canupdatecourse) {
            $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
            $icon = '<img aria-hidden="true" role="presentation" class="svg-icon" alt="" src="';
            $icon .= $this->output->image_url('pencil', 'theme').'" /><br/>';
            $o .= '<a href="'.$url.'" class="edit-summary">'.$icon.get_string('editcoursetopic', 'theme_snap'). '</a>';
        }
        $o .= "</div>";

        return $o;
    }

    /**
     * @param course_section_navigation $navigation
     */
    public function render_course_section_navigation(course_section_navigation $navigation) {
        return $this->render_from_template('theme_snap/course_section_navigation', $navigation);
    }


    /**
     * Render an individual course section.
     * @param \stdClass $course
     * @param \section_info $section
     * @param \course_modinfo $modinfo
     * @return string
     */
    public function course_section($course, $section, $modinfo) {
        global $PAGE;

        $output = $this->section_header($section, $course, false, $section->sectionnum);
        $renderer = new course_renderer($PAGE, null);
        // GThomas 21st Dec 2015 - Only output assets inside section if the section is user visible.
        // Otherwise you can see them, click on them and it takes you to an error page complaining that they
        // are restricted!
        if ($section->uservisible) {
            $output .= $renderer->course_section_cm_list_snap($course, $section, 0);
            // SLamour Aug 2015 - make add asset visible without turning editing on
            // N.B. this function handles the can edit permissions.
            $output .= $this->course_section_add_cm_control_snap($course, $section->section, 0);
        }
        $output .= $this->render(new course_section_navigation($course, $modinfo->get_section_info_all(), $section->section));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('li');
        return $output;
    }



    // Basically unchanged from the core version adds some navigation with course_section_navigation renderable.
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $DB, $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $pagepath = $PAGE->url->get_path();
        $sectionid = optional_param('id', -1, PARAM_INT);

        if ($pagepath === '/course/section.php' && $sectionid !== -1) {
            $section = $DB->get_record('course_sections', ['id' => $sectionid], 'section', MUST_EXIST);
            $course->sectionreturn = $section->section ?? 0;
        }

        $context = context_course::instance($course->id);

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections.
        echo html_writer::start_tag('ul', ['class' => 'sections']);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id));
        $numsections = course_get_format($course)->get_last_section_number();
        if (get_config('theme_snap', 'coursepartialrender')) {
            echo $this->render_from_template('theme_snap/course_section_loading', []);
            $startsectionid = 0;
            if ($course->format == 'topics') {
                $startsectionid = !empty($course->marker) ? $course->marker : 0;
            } else if ($course->format == 'weeks') {
                if ($pagepath !== '/course/section.php') {
                    for ($i = 0; $i <= $numsections; $i++) {
                        if (course_get_format($course)->is_section_current($i)) {
                            $startsectionid = $i;
                            break;
                        }
                    }
                } elseif ($sectionid !== -1) {
                    $startsectionid = !empty($course->marker) ? $course->marker : 0;
                }
            }
            $startsectionid = !empty($course->sectionreturn) ? $course->sectionreturn : $startsectionid;
            $mainsection = $modinfo->get_section_info($startsectionid);
            // Marker can be set to a deleted section.
            if (!empty($mainsection) && (!empty($mainsection->visible) || $canviewhidden)) {
                $sections[$startsectionid] = $mainsection;
            } else {
                $sections[0] = $modinfo->get_section_info(0);
            }

        } else {
            $sections = $modinfo->get_section_info_all();
        }

        foreach ($sections as $section => $thissection) {

            if ($section > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                continue;
            }

            // Student check.
            if (!$canviewhidden) {
                $conditional = $this->is_section_conditional($thissection);
                // HIDDEN SECTION - If nothing in show hidden sections, and course section is not visible - don't print.
                if (!$conditional && $course->hiddensections && !$thissection->visible) {
                    continue;
                }
                // CONDITIONAL SECTIONS - If its not visible to the user and we have no info why - don't print.
                if ($conditional && !$thissection->uservisible && !$thissection->availableinfo) {
                    continue;
                }
                // If hidden sections are collapsed - print a fake li.
                if (!$conditional && !$course->hiddensections && !$thissection->visible) {
                    echo $this->section_hidden($section);
                    continue;
                }
            }

            // Output course section.
            echo $this->course_section($course, $thissection, $modinfo);
        }

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections || empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
//                echo $this->stealth_section_header($section); Call to deprecated function.
                $o = '';
                $o .= html_writer::start_tag('li', [
                    'id' => 'section-' . $section,
                    'class' => 'section main clearfix orphaned hidden',
                    'data-sectionid' => $section
                ]);
                $o .= html_writer::tag('div', '', array('class' => 'left side'));
                $course = course_get_format($this->page->course)->get_course();
                $section = course_get_format($this->page->course)->get_section($section);
//                $rightcontent = $this->section_right_content($section, $course, false); call to deprecated function.
                $rightcontent = $this->output->spacer();
                $controls = $this->section_edit_control_items($course, $section, false);

                if (!empty($controls)) {
                    $menu = new \action_menu();
                    $menu->set_menu_trigger(get_string('edit'));
                    $menu->attributes['class'] .= ' section-actions';
                    foreach ($controls as $value) {
                        $url = empty($value['url']) ? '' : $value['url'];
                        $icon = empty($value['icon']) ? '' : $value['icon'];
                        $name = empty($value['name']) ? '' : $value['name'];
                        $attr = empty($value['attr']) ? array() : $value['attr'];
                        $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                        $al = new \action_menu_link_secondary(
                            new moodle_url($url),
                            new \pix_icon($icon, '', null, array('class' => "smallicon " . $class)),
                            $name,
                            $attr
                        );
                        $menu->add($al);
                    }

                    $o .= html_writer::div(
                        $this->render($menu),
                        'section_action_menu',
                        array('data-sectionid' => $section->id)
                    );
                }

                $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
                $o .= html_writer::start_tag('div', array('class' => 'content'));
                $o .= $this->output->heading(
                    get_string('orphanedactivitiesinsectionno', '', $section),
                    3,
                    'sectionname'
                );
                echo $o;

                // Don't print add resources/activities of 'stealth' sections.
                //echo $this->stealth_section_footer();
                $o = html_writer::end_tag('div');
                $o .= html_writer::end_tag('li');
                echo $o;

            }
        }
        echo $this->end_section_list();
    }

    protected function end_section_list() {
        global $COURSE;

        $output = html_writer::end_tag('ul');
        $output .= $this->change_num_sections($COURSE);
        $output .= shared::course_tools(true);
        return $output;
    }


    /**
     * Render a form to create a new course section, prompting for basic info.
     *
     * @return string
     */
    private function change_num_sections($course) {

        $course = course_get_format($course)->get_course();
        $context = context_course::instance($course->id);
        if (!has_capability('moodle/course:update', $context)) {
            return '';
        }

        $url = new moodle_url('/theme/snap/index.php', array(
            'sesskey'  => sesskey(),
            'action' => 'addsection',
            'contextid' => $context->id,
        ));

        $required = '';
        $defaulttitle = get_string('title', 'theme_snap');
        $sectionnum = course_get_format($course)->get_last_section_number();
        if ($course->format === 'topics') {
            // Make sure that section does not have leading or trailing spaces and at least one character.
            $required = 'required pattern=".*\S+.*"';
        } else {
            // Take this part of code from /course/format/weeks/lib.php on functions
            // @codingStandardsIgnoreLine
            // get_section_name($section) and get_section_dates($section).
            $oneweekseconds = 60 * 60 * 24 * 7;
            // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
            // savings and the date changes.
            $startdate = $course->startdate + (60 * 60 * 2);
            $dates = new stdClass();
            $dates->start = $startdate + ($oneweekseconds * $sectionnum);
            $dates->end = $dates->start + $oneweekseconds;
            // We subtract 24 hours for display purposes.
            $dates->end = ($dates->end - (60 * 60 * 24));
            $dateformat = get_string('strftimedateshort');
            $weekday = userdate($dates->start, $dateformat);
            $endweekday = userdate($dates->end, $dateformat);
            $datesection = $weekday.' - '.$endweekday;
        }
        $heading = get_string('addanewsection', 'theme_snap');
        $output = "<section id='snap-add-new-section' class='clearfix' tabindex='-1'>
        <h3>$heading</h3>";
        $output .= html_writer::start_tag('form', array(
            'method' => 'post',
            'action' => $url->out_omit_querystring(),
        ));
        $output .= html_writer::input_hidden_params($url);
        $output .= '<div class="mb-3">';
        $output .= "<label for='newsection' class='sr-only'>".get_string('title', 'theme_snap')."</label>";
        if ($course->format === 'topics') {
            $output .= '<input id="newsection" type="text" maxlength="250" name="newsection" '.$required;
            $output .= ' placeholder="'.s(get_string('title', 'theme_snap')).'">';
        } else {
            $output .= '<h3>'.$defaulttitle.': '.$datesection.'</h3>';
        }
        $output .= '</div>';
        $output .= '<div class="mb-3">';
        $output .= '<label for="summary">'.get_string('contents', 'theme_snap').'</label>';

        $options = array(
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'context' => $context,
        );
        $draftitemid = file_get_submitted_draft_itemid('summary');
        $currenttext = file_prepare_draft_area($draftitemid, $context->id, 'course', 'section', null, $options);

        $output .= $this->print_editor('summary', 'summary-editor', $currenttext, $draftitemid, $options);
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden',
            'name' => 'draftitemid',
            'value' => $draftitemid,
        ));

        $output .= '</div>';
        $output .= html_writer::empty_tag('input', array(
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'name' => 'addtopic',
            'value' => get_string('createsection', 'theme_snap'),
        ));

        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $message = get_string('cancel');
        $attr = array('class' => 'btn btn-secondary', 'id' => 'cancel-new-section');
        $output .= html_writer::link($courseurl, $message, $attr);

        $output .= html_writer::end_tag('form');
        $output .= '</section>';
        return $output;
    }

    /**
     * Returns the HTML for an editor with file management
     *
     * @param string $id The id to use fort he textarea element
     * @param string $name Name to use for the textarea element
     * @param string $currenttext Initial content to display in the textarea
     * @param int $draftitemid the id of the draft area to use
     * @param array $options text and file options ('subdirs'=>false, 'forcehttps'=>false)
     * @return string
     */
    private function print_editor($name, $id, $currenttext, $draftitemid, $options) {
        global $OUTPUT;

        editors_head_setup();
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $editor->set_text($currenttext);

        $args = new stdClass();
        $args->accepted_types = array('image');
        $args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
        $args->context = $options['context'];
        $args->env = 'filepicker';

        $imageoptions = initialise_filepicker($args);
        $imageoptions->context = $options['context'];
        $imageoptions->client_id = uniqid();
        $imageoptions->maxbytes = $options['maxfiles'];
        $imageoptions->env = 'editor';
        $imageoptions->itemid = $draftitemid;

        $args->accepted_types = array('video', 'audio');
        $mediaoptions = initialise_filepicker($args);
        $mediaoptions->context = $options['context'];
        $mediaoptions->client_id = uniqid();
        $mediaoptions->maxbytes  = $options['maxfiles'];
        $mediaoptions->env = 'editor';
        $mediaoptions->itemid = $draftitemid;

        $args->accepted_types = '*';
        $linkoptions = initialise_filepicker($args);
        $linkoptions->context = $options['context'];
        $linkoptions->client_id = uniqid();
        $linkoptions->maxbytes  = $options['maxfiles'];
        $linkoptions->env = 'editor';
        $linkoptions->itemid = $draftitemid;

        $fpoptions['image'] = $imageoptions;
        $fpoptions['media'] = $mediaoptions;
        $fpoptions['link'] = $linkoptions;

        $editor->use_editor('summary-editor', $options, $fpoptions);

        $context = [
            'id' => $id,
            'name' => $name,
            'value' => $currenttext,
            'rows' => 15,
            'cols' => 65,
        ];

        return $OUTPUT->render_from_template('core_form/editor_textarea', $context);
    }

    /**
     * Renders HTML for the menus to add activities and resources to the current course
     *
     * Note, if theme overwrites this function and it does not use modchooser,
     * see also {@link core_course_renderer::add_modchoosertoggle()}
     *
     * @param stdClass $course
     * @param int $section relative section number (field course_sections.section)
     * @param int $sectionreturn The section to link back to
     * @param array $displayoptions additional display options, for example blocks add
     *     option 'inblock' => true, suggesting to display controls vertically
     * @return string
     */
    public function course_section_add_cm_control_snap($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $OUTPUT;
        // Check to see if user can add menus and there are modules to add.
        if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                || !($modnames = get_module_types_names()) || empty($modnames)) {
            return '';
        }

        $iconurl = $OUTPUT->image_url('move_here', 'theme');
        $icon = '<img src="'.$iconurl.'" class="svg-icon" role="presentation" alt=""><br>';
        // Slamour Aug 2017.
        $straddmod = get_string('addresourceoractivity', 'theme_snap');
        $mclinkcontent = $icon.$straddmod;
        $mcclass = 'js-only section-modchooser-link btn btn-link';

        // Render button for new core mod chooser.
        $modchoosercontent = html_writer::tag('button', $mclinkcontent, [
            'class' => $mcclass,
            'data-action' => 'open-chooser',
            'data-sectionid' => $section,
        ]);

        // We need to be sure not having the same ID for every mod chooser if multiple sections exists.
        $modchooserid = "snap-create-activity-$section";
        $modchooser = html_writer::tag('div', $modchoosercontent, [
            'class' => 'col-sm-6 snap-modchooser',
            'id' => $modchooserid,
        ]);

        // Add zone for quick uploading of files.
        $dropfileicon = $OUTPUT->image_url('i/folderdropzone', 'theme_snap');
        $dropzonelabel = get_string('dropzonelabel', 'theme_snap');
        $upload = '<div class="col-sm-6">';
        $upload .= '<form class="snap-dropzone js-only">';
        $upload .= '<label tabindex="0" for="snap-drop-file-'.$section.'" class="snap-dropzone-label">';
        $upload .= '<div>';
        $upload .= '<div class="activityiconcontainer">';
        $upload .= '<img src="'.$dropfileicon.'" alt="" class="iconlarge activityicon" role="presentation">';
        $upload .= '</div>';
        $upload .= '<div>'.$dropzonelabel.'</div>';
        $upload .= '</div>';
        $upload .= '</label>';
        $upload .= '<input class="js-snap-drop-file sr-only" type="file" multiple
         name="snap-drop-file-'.$section.'" id="snap-drop-file-'.$section.'">';
        $upload .= '</form>';
        $upload .= '</div>';

        return '<div class="row">'.$modchooser.$upload.'</div>';
    }

    /**
     * Always output the html for multiple sections, single section mode is not supported in Snap.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        return $this->print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
    }

    /**
     * @param course_action_section_move $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_move(course_action_section_move $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_visibility $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_visibility(course_action_section_visibility $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_highlight $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_highlight(course_action_section_highlight $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_delete $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_delete(course_action_section_delete $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_duplicate $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_duplicate(course_action_section_duplicate $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_extra_menu $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_extra_menu(course_action_section_extra_menu $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * @param course_action_section_permalink $action
     * @return mixed
     * @throws \moodle_exception
     */
    public function render_course_action_section_permalink(course_action_section_permalink $action) {
        $data = $action->export_for_template($this);
        return $this->render_from_template('theme_snap/course_action_section', $data);
    }

    /**
     * Show if something is on on the course clipboard (moving around)
     * Copied from course/format/classes/output/section_renderer.php
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * While the non ajax course eidtion is still supported, the old clipboard will be
     * emulated by core_courseformat\output\local\content\section\cmlist.
     *
     * @param stdClass $course The course entry from DB
     * @param int $sectionno The section number in the course which is being displayed
     * @return string HTML to output.
     */
    protected function course_activity_clipboard($course, $sectionno = null) {
        global $USER;
        $o = '';
        // If currently moving a file then show the current clipboard.
        if (ismoving($course->id)) {
            $url = new moodle_url(
                '/course/mod.php',
                array(
                    'sesskey' => sesskey(),
                    'cancelcopy' => true,
                    'sr' => $sectionno,
                )
            );

            $o .= html_writer::start_tag('div', array('class' => 'clipboard'));
            $o .= strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
            $o .= ' (' . html_writer::link($url, get_string('cancel')) . ')';
            $o .= html_writer::end_tag('div');
        }

        return $o;
    }

    /**
     * Generate html for a section summary text
     * Copied from course/format/classes/output/section_renderer.php
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {
        $format = course_get_format($section->course);
        if (!($section instanceof \section_info)) {
            $modinfo = $format->get_modinfo();
            $section = $modinfo->get_section_info($section->section);
        }
        $summaryclass = $format->get_output_classname('content\\section\\summary');
        $summary = new $summaryclass($format, $section);
        return $summary->format_summary_text();
    }
}
