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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <rodrigo_brandao@me.com>
 * @copyright  2018 Rodrigo Brandão
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/format/topics/renderer.php');

/**
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão (rodrigobrandao.com.br)
 * @copyright  2017 Rodrigo Brandão
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_buttons_renderer extends format_topics_renderer
{

    /**
     * get_button_section
     *
     * @param stdclass $course
     * @param string $name
     * @return string
     */
    protected function get_color_config($course, $name)
    {
        $return = false;
        if (isset($course->{$name})) {
            $color = str_replace('#', '', $course->{$name});
            $color = substr($color, 0, 6);
            if (preg_match('/^#?[a-f0-9]{6}$/i', $color)) {
                $return = '#'.$color;
            }
        }
        return $return;
    }

    /**
     * get_button_section
     *
     * @param stdclass $course
     * @param string $sectionvisible
     * @return string
     */
    protected function get_button_section($course, $sectionvisible)
    {
        global $PAGE;
        $html = '';
        $css = '';
        if ($colorcurrent = $this->get_color_config($course, 'colorcurrent')) {
            $css .=
            '#buttonsectioncontainer .buttonsection.current {
                background: ' . $colorcurrent . ';
            }
            ';
        }
        if ($colorvisible = $this->get_color_config($course, 'colorvisible')) {
            $css .=
            '#buttonsectioncontainer .buttonsection.sectionvisible {
                background: ' . $colorvisible . ';
            }
            ';
        }
        if ($css) {
            $html .= html_writer::tag('style', $css);
        }
        $withoutdivisor = true;
        for ($k = 1; $k <= 12; $k++) {
            if ($course->{'divisor' . $k}) {
                $withoutdivisor = false;
            }
        }
        if ($withoutdivisor) {
            $course->divisor1 = 999;
        }
        $divisorshow = false;
        $count = 1;
        $currentdivisor = 1;
        $modinfo = get_fast_modinfo($course);
        $inline = '';
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            if ($course->hiddensections && !(int)$thissection->visible) {
                continue;
            }
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $count > $course->{'divisor' . $currentdivisor}) {
                $currentdivisor++;
                $count = 1;
            }
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} != 0 &&
                !isset($divisorshow[$currentdivisor])) {
                $currentdivisorhtml = $course->{'divisortext' . $currentdivisor};
                $currentdivisorhtml = str_replace('[br]', '<br>', $currentdivisorhtml);
                $currentdivisorhtml = html_writer::tag('div', $currentdivisorhtml, ['class' => 'divisortext']);
                if ($course->inlinesections) {
                    $inline = 'inlinebuttonsections';
                }
                $html .= html_writer::tag('div', $currentdivisorhtml, ['class' => "divisorsection $inline"]);
                $divisorshow[$currentdivisor] = true;
            }
            $id = 'buttonsection-' . $section;
            if ($course->sequential) {
                $name = $section;
            } else {
                if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} == 1) {
                    $name = '&bull;&bull;&bull;';
                } else {
                    $name = $count;
                }
            }
            if ($course->sectiontype == 'alphabet' && is_numeric($name)) {
                $name = $this->number_to_alphabet($name);
            }
            if ($course->sectiontype == 'roman' && is_numeric($name)) {
                $name = $this->number_to_roman($name);
            }
            $class = 'buttonsection';
            $onclick = 'M.format_buttons.show(' . $section . ',' . $course->id . ')';
            if (!$thissection->available &&
                !empty($thissection->availableinfo)) {
                $class .= ' sectionhidden';
            } elseif (!$thissection->uservisible || !$thissection->visible) {
                $class .= ' sectionhidden';
                $onclick = false;
            }
            if ($course->marker == $section) {
                $class .= ' current';
            }
            if ($sectionvisible == $section) {
                $class .= ' sectionvisible';
            }
            if ($PAGE->user_is_editing()) {
                $onclick = false;
            }
            $html .= html_writer::tag('div', $name, ['id' => $id, 'class' => $class, 'onclick' => $onclick]);
            $count++;
        }
        $html = html_writer::tag('div', $html, ['id' => 'buttonsectioncontainer', 'class' => $course->buttonstyle]);
        if ($PAGE->user_is_editing()) {
            $html .= html_writer::tag('div', get_string('editing', 'format_buttons'), ['class' => 'alert alert-warning alert-block fade in']);
        }
        return $html;
    }

    /**
     * get_button_section kadima
     *
     * @param stdclass $course
     * @param string $sectionvisible
     * @return string
     */
    protected function get_button_section_kadima($course, $sectionvisible)
    {
        global $PAGE;
        $html = '';
        $css = '';

        $modinfo = get_fast_modinfo($course);
        $inline = '';
        $count = 1;

        if (!$PAGE->user_is_editing()) {
        // start kadima container render
        $html .= html_writer::start_tag('div',['class' => 'container-fluid buttons']); // don't forget to close it later

        $html .= html_writer::start_tag('div',['class' => 'sections-wrapper justify-content-end']);
        $html .= html_writer::start_tag('ul',['id' => 'sections', 'role' => 'sections-list', 'class' => 'nav slider sections align-items-end align-content-end']);
        }

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            if ($course->hiddensections && !(int)$thissection->visible) {
                continue;
            }

            if ($course->sequential) {
                $name = $section;
            } else {
                    $name = $count;
            }
            if ($course->sectiontype == 'alphabet' && is_numeric($name)) {
                $name = $this->number_to_alphabet($name);
            }
            if ($course->sectiontype == 'roman' && is_numeric($name)) {
                $name = $this->number_to_roman($name);
            }

            $class = 'buttonsection';
            if (!$thissection->available &&
            !empty($thissection->availableinfo)) {
            $class .= ' sectionhidden';
            } elseif (!$thissection->uservisible || !$thissection->visible) {
                $class .= ' sectionhidden';
                $onclick = false;
            }
            if ($course->marker == $section) {
                $class .= ' current';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $class = ' active';
            }
            //TODO   open first section remove it after
            if ($section == 2) {
                $class = ' active show';
            }

            if ($sectionvisible == $section) {
                $class .= ' sectionvisible';
            }

            if ($PAGE->user_is_editing()) {
                $onclick = false;
            }

            $html .= html_writer::start_tag('li',['class' => 'nav-item mb-auto', 'data-section' => $section]);
            // $html .= html_writer::start_tag('a',['href' => "#section$section",'class' => "nav-link $class", 'aria-controls' => "section-$section"]);
            $html .= html_writer::start_tag('div',['class' => 'd-flex flex-row section-header justify-content-around align-items-center']);
            $html .= html_writer::tag('span', '', ['class' => 'section-icon d-inline-flex p-3 justify-content-center align-items-center', 'style' => "background: url({$this->courserenderer->image_url('label-default', 'format_buttons')}) no-repeat; background-size: cover;"]);
            $html .= html_writer::start_tag('div',['class' => 'd-flex flex-column section-header']);
            $html .= html_writer::tag('span', get_section_name($course, $section), ['class' => ' section-title']);
            $html .= html_writer::tag('p', "$thissection->summary", ['class' => 'section-description']);
            $html .= html_writer::end_tag('div');
            $html .= html_writer::tag('span', 'i', ['class' => 'section-tooltip d-inline-flex p-1 justify-content-center align-items-center', 'title'=>'section tooltip', 'data-info'=>'Tooltip content', 'data-section' => $section]);
            $html .= html_writer::end_tag('div');
            // $html .= html_writer::end_tag('a');
            $html .= html_writer::end_tag('li');

            $count++;
        }
        $html .= html_writer::end_tag('ul');
        // $html .= html_writer::tag('button', '', ['type' => 'button', 'name' => 'button', 'class' => 'slide-tabs slide-right']);
        $html .= html_writer::end_tag('div');

        if ($PAGE->user_is_editing()) {
            $html .= html_writer::tag('div', get_string('editing', 'format_buttons'), ['class' => 'alert alert-warning alert-block fade in']);
        }
        return $html;
    }

    /**
     * number_to_roman
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_roman($number)
    {
        $number = intval($number);
        $return = '';
        $romanarray = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        foreach ($romanarray as $roman => $value) {
            $matches = intval($number / $value);
            $return .= str_repeat($roman, $matches);
            $number = $number % $value;
        }
        return $return;
    }

    /**
     * number_to_alphabet
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_alphabet($number)
    {
        $number = $number - 1;
        $alphabet = range("A", "Z");
        if ($number <= 25) {
            return $alphabet[$number];
        } elseif ($number > 25) {
            $dividend = ($number + 1);
            $alpha = '';
            while ($dividend > 0) {
                $modulo = ($dividend - 1) % 26;
                $alpha = $alphabet[$modulo] . $alpha;
                $dividend = floor((($dividend - $modulo) / 26));
            }
            return $alpha;
        }
    }

    /**
     * start_section_list
     *
     * @return string
     */
    protected function start_section_list()
    {
        return html_writer::start_tag('ul', ['class' => 'buttons']);
    }

    /**
     * section_header
     *
     * @param stdclass $section
     * @param stdclass $course
     * @param bool $onsectionpage
     * @param int $sectionreturn
     * @return string
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null)
    {
        global $PAGE, $CFG;
        $o = '';
        $currenttext = '';
        $sectionstyle = '';
        if ($section->section != 0) {
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } elseif (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }
        $o .= html_writer::start_tag('li', ['id' => 'section-'.$section->section,
        'class' => 'section main clearfix'.$sectionstyle,
        'role' => 'region', 'aria-label' => get_section_name($course, $section)]);
        $o .= html_writer::tag('span', $this->section_title($section, $course), ['class' => 'sectionname']);  // by default - ['class' => 'hidden sectionname']
        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $leftcontent, ['class' => 'left side']);
        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $rightcontent, ['class' => 'right side']);
        $o .= html_writer::start_tag('div', ['class' => 'content']);
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));
        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        if ($course->showdefaultsectionname) {
            $o .= $this->output->heading($sectionname, 3, 'sectionname' . $classes);
        }
        $o .= html_writer::start_tag('div', ['class' => 'summary']);
        $o .= $this->format_summary_text($section);
        $context = context_course::instance($course->id);
        $o .= html_writer::end_tag('div');
        $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));
        return $o;
    }

    /**
     * print_multiple_section_page
     *
     * @param stdclass $course
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused)
    {
        global $PAGE;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $context = context_course::instance($course->id);
        $completioninfo = new completion_info($course);
        if (isset($_COOKIE['sectionvisible_'.$course->id])) {
            $sectionvisible = $_COOKIE['sectionvisible_'.$course->id];
        } elseif ($course->marker > 0) {
            $sectionvisible = $course->marker;
        } else {
            $sectionvisible = 1;
        }
        // old htmlsection
        // $htmlsection = false;
        // foreach ($modinfo->get_section_info_all() as $section => $thissection) {
        //     $htmlsection[$section] = '';
        //     if ($section == 0) {
        //         $section0 = $thissection;
        //         continue;
        //     }
        //     if ($section > $course->numsections) {
        //         continue;
        //     }
        //     /* if is not editing verify the rules to display the sections */
        //     if (!$PAGE->user_is_editing()) {
        //         if ($course->hiddensections && !(int)$thissection->visible) {
        //             continue;
        //         }
        //         if (!$thissection->available && !empty($thissection->availableinfo)) {
        //             $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
        //             continue;
        //         }
        //         if (!$thissection->uservisible || !$thissection->visible) {
        //             $htmlsection[$section] .= $this->section_hidden($section, $course->id);
        //             continue;
        //         }
        //     }
        //     $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
        //     if ($thissection->uservisible) {

        //         if (!$PAGE->user_is_editing()) {

        //             // our labels output into sections except 0
        //             $htmlsection[$section] .= html_writer::start_tag('div', array('class' => 'labels-wrap'));
        //             $labelscontent = $this->labels_content($course, $thissection);
        //             $htmlsection[$section] .= html_writer::tag('div', $labelscontent, array('class' => 'labels-content'));
        //             $labelslist = $this->labels_list($course, $thissection);
        //             //$htmlsection[$section] .= $this->get_section_labels($course, $thissection, 0);
        //             $htmlsection[$section] .= html_writer::tag('div', $labelslist, array('class' => 'labels-list'));
        //             $htmlsection[$section] .= html_writer::end_tag('div');

        //             //$htmlsection[$section] .= $this->course_section_cm_list($course, $thissection, 0); // first version render
        //         } else {
        //             $htmlsection[$section] .= $this->courserenderer->course_section_cm_list($course, $thissection, 0); // original render
        //             $htmlsection[$section] .= $this->courserenderer->course_section_add_cm_control($course, $section, 0);
        //         }
        //     }

        //     $htmlsection[$section] .= $this->section_footer();
        // }

        // kadima html section
        $htmlsection = false;
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            $htmlsection[$section] = '';
            $currentsectionclass = '';
            // TODO
            if ($section == 1) {
                $currentsectionclass = ' active';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $currentsectionclass = ' active';
            }
            if ($section == 0) {
                $section0 = $thissection;
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            /* if is not editing verify the rules to display the sections */
            if (!$PAGE->user_is_editing()) {
                if ($course->hiddensections && !(int)$thissection->visible) {
                    continue;
                }
                if (!$thissection->available && !empty($thissection->availableinfo)) {
                    $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
                    continue;
                }
                if (!$thissection->uservisible || !$thissection->visible) {
                    $htmlsection[$section] .= $this->section_hidden($section, $course->id);
                    continue;
                }
            }
            if ($PAGE->user_is_editing()) { // turn on section header only for editing mode
            $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
            }

            if ($thissection->uservisible) {

                if (!$PAGE->user_is_editing()) {
                    // our labels output into sections except 0

                    $htmlsection[$section] .=  html_writer::start_tag('div',['id' => "section$section",'class' => "section-content d-none  $currentsectionclass", 'role' => 'section content' ]);
                    $htmlsection[$section] .=  html_writer::start_tag('div',['class' => 'd-flex flex-column flex-md-row']);
                    $htmlsection[$section] .=  html_writer::start_tag('div',['class' => 'col-12 col-md-3 col-lg-2 labels-wrapper']);
                    $htmlsection[$section] .=  html_writer::start_tag('ul',['class' => 'nav flex-column flex-nowrap align-content-end justify-content-end slider labels', 'role' => 'labels list']);
                    $htmlsection[$section] .=  $this->labels_list($course, $thissection);
                    $htmlsection[$section] .=  html_writer::end_tag('ul');
                    $htmlsection[$section] .=  html_writer::end_tag('div');
                    $htmlsection[$section] .=  html_writer::start_tag('div',['class' => 'label-content-wrapper col-12 col-md-9 col-lg-10']);
                    $htmlsection[$section] .=  $this->labels_content($course, $thissection);
                    $htmlsection[$section] .=  html_writer::end_tag('div');
                    $htmlsection[$section] .=  html_writer::start_tag('div',['class' => ' label-content-controls d-flex d-md-none d-lg-none']);
                    $htmlsection[$section] .=  html_writer::tag('button', '', ['class' => ' p-2 col-4 label-prev']);
                    $htmlsection[$section] .=  html_writer::tag('div', '', ['class' => ' p-2 col-4 label-active']);
                    $htmlsection[$section] .=  html_writer::tag('button', '', ['class' => ' p-2 col-4 label-next']);
                    $htmlsection[$section] .=  html_writer::end_tag('div');
                    $htmlsection[$section] .=  html_writer::end_tag('div');
                    $htmlsection[$section] .=  html_writer::end_tag('div');

                    //first kadima render
                    // $htmlsection[$section] .= html_writer::start_tag('div', array('class' => 'labels-wrap'));
                    // $labelscontent = $this->labels_content($course, $thissection);
                    // $htmlsection[$section] .= html_writer::tag('div', $labelscontent, array('class' => 'labels-content'));
                    // $labelslist = $this->labels_list($course, $thissection);
                    // //$htmlsection[$section] .= $this->get_section_labels($course, $thissection, 0);
                    // $htmlsection[$section] .= html_writer::tag('div', $labelslist, array('class' => 'labels-list'));
                    // $htmlsection[$section] .= html_writer::end_tag('div');

                    //$htmlsection[$section] .= $this->course_section_cm_list($course, $thissection, 0); // first version render
                } else {
                    // render sections edit mode
                    $htmlsection[$section] .= $this->courserenderer->course_section_cm_list($course, $thissection, 0); // original render
                    $htmlsection[$section] .= $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
            }

            if ($PAGE->user_is_editing()) { // show section footer only in editing mode
                $htmlsection[$section] .= $this->section_footer(); //
            }
        }

        if ($section0->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
            $htmlsection0 = $this->section_header($section0, $course, false, 0);
            //$htmlsection0 .= $this->courserenderer->course_section_cm_list($course, $section0, 0); // original render
            $htmlsection0 .= $this->course_section_cm_list($course, $section0, 0); // first version render
            $htmlsection0 .= $this->courserenderer->course_section_add_cm_control($course, 0, 0);
            $htmlsection0 .= $this->section_footer();
        }
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');
        echo $this->course_activity_clipboard($course, 0);
        echo $this->start_section_list();
        if ($course->sectionposition == 0 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'above']);
        }

        // render section buttons here
        if (!$PAGE->user_is_editing()) {
          echo $this->get_button_section_kadima($course, $sectionvisible);
          echo html_writer::start_tag('div',['class' => 'sections-content-wrapper']);  //tab content starts here
        } else {
            echo $this->get_button_section($course, $sectionvisible);
        }

          // putput sections (except 0) - here
          foreach ($htmlsection as $current) {
              echo $current;
          }

          if (!$PAGE->user_is_editing()) {
            // end kadima reder here
            echo html_writer::end_tag('div'); // tab content ends here
            echo html_writer::end_tag('div'); // container-fluid buttons ends here (starts in get_button_section_kadima)
          }

        if ($course->sectionposition == 1 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'below']);
        }
        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0); // original render
                // echo $this->course_section_cm_list($course, $thissection, 0);  // first version render
                echo $this->stealth_section_footer();
            }
            echo $this->end_section_list();
            echo html_writer::start_tag('div', ['id' => 'changenumsections', 'class' => 'mdl-right']);
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                'increase' => true, 'sesskey' => sesskey()]);
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon.get_accesshide($straddsection), ['class' => 'increase-sections']);
            if ($course->numsections > 0) {
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                    'increase' => false, 'sesskey' => sesskey()]);
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link(
                    $url,
                    $icon.get_accesshide($strremovesection),
                ['class' => 'reduce-sections']
                );
            }
            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
        echo html_writer::tag('style', '.course-content ul.buttons #section-'.$sectionvisible.' { display: block; }');
        // if (!$PAGE->user_is_editing()) {
        //     $PAGE->requires->js_init_call('M.format_buttons.init', [$course->numsections]);
        // }
        // ==============================================================================
        // don't needed - implemented above
        //echo $this->course_format_buttons_design($course, $section);
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
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER, $PAGE;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }

                // show only 'labels' in sections
                if ($mod->modname == 'label') {
                    if ($modulehtml = $this->courserenderer->course_section_cm_list_item($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                        $moduleshtml[$modnumber] = $modulehtml;
                    }
                } else if ($PAGE->user_is_editing()) { // show other activities ONLY in editing mode, else comment here
                    if ($modulehtml = $this->courserenderer->course_section_cm_list_item($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                        $moduleshtml[$modnumber] = $modulehtml;
                    }
                } //and comment here

            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                        array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }

    /**
     * Function to get all labels for section befor render
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @return arr Content parsed for labels render
     */
    public function get_section_labels($course, $section) {
        global $USER, $PAGE;

        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }

        $lables = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($mod->modname == 'label') {
                    if (!$mod->is_visible_on_course_page()) {
                        // nothing to be displayed to the user
                        return $output;
                    }

                    // get and parse label content into header, icon and the rest of the text
                    if ($modulehtml =  $mod->get_formatted_content(array('noclean' => true))) {

                        $reg = '/<h\d>(.*)<\/h\d>.*?\s*(<pre>(.*)<\/pre>)?\s*(.*)<\/div>/sm'; // Regex for <h></h>, <pre></pre> and others. Last <div> is to close no-owerflow div
                        $reg = '/#name(.*)%name.*?\s*#icon(.*)%icon?\s*(.*)<\/div>/im';
                        preg_match($reg, $modulehtml, $content);

                        $lables[$modnumber] = $content;
                    }
                }
            }
        }
        return $lables;

    } // get_section_labels ends

    /**
     * Function to render labels list (menu) on the course page
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @return str Output of the labels list
     */
    public function labels_list($course, $section) {
        $labels = $this->get_section_labels($course, $section);
        $output = '';
        foreach ($labels as $modnum => $content) {

            // here we fetch icon url or set default one
            if (empty($content[2])) {
                $licon = $this->courserenderer->image_url('label-default', 'format_buttons');
            } else {
                $licon = $this->courserenderer->image_url($content[2], 'format_buttons');
            }

            // kadima render
            $output .=  html_writer::start_tag('li',['class' => 'nav-item label-item', 'data-label'=>$modnum]);
            $output .= html_writer::start_tag('div', ['class'=> 'd-flex flex-row label-header align-items-center']);
            // $output .= html_writer::start_tag('a',['href' => "#label{$modnum}",'class' => "nav-link label-link", 'aria-controls' => "label{$modnum}"]);
            $output .= html_writer::tag('span', '', ['class' => 'label-icon d-inline-flex justify-content-center align-items-center', 'style' => "background: url({$licon}) no-repeat; background-size: cover;"]);
            $output .= html_writer::start_tag('div', ['class'=> 'd-flex flex-column']);
            $output .= html_writer::tag('span', $content[1], ['class'=>'label-title']);
            $output .= html_writer::end_tag('div');
            // $output .= html_writer::end_tag('a');
            $output .= html_writer::end_tag('div');
            $output .= html_writer::end_tag('li');

            // first test render - for reference
            // $output .= "<div class = 'label_item' id='label_{$modnum}'>";
            // $output .= $content[1];
            // $output .= "&nbsp;<div class='licon' style='background: url({$licon}) no-repeat; background-size: contain;'></div>";
            // $output .= "</div>";
        }

        return $output;
    }

    /**
     * Function to render labels content on the course page
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @return str Output of the labels content
     */
    public function labels_content($course, $section) {
        $labels = $this->get_section_labels($course, $section);
        $output = '';
        foreach ($labels as $modnum => $content) {

            $output .= html_writer::tag('div', $content[3], ['id' => "label{$modnum}", 'class' => 'label-content d-none', 'role' => 'label content', 'data-label-content' => $modnum ]);

            // first test render - for reference
            // $output .= "<div class = 'label_content' id='label_content_{$modnum}'>";
            // $output .= $content[3];
            // $output .= "</div>";
        }

        return $output;
    }
    /*************************************************************************************/
    /**
     * Function for hardcode rendering tabs layout on the course page
     *
     * @param stdclass $course
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)

     * @return str Output layout markup
     */
    public function course_format_buttons_design($course, $section) {
        $output = '';
// section buttons
        $output .= '
        <div class="container-fluid buttons">
          <div class="sections-wrapper">
            <button type="button" name="button" class="slide-tabs slide-left"></button>
            <ul class="nav nav-tabs sections flex-nowrap" id="sections" role="tablist">
              <li class="nav-item">
                <a href="#section0" class="nav-link active" data-toggle="tab" aria-controls="section-0">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 0</span>
                    <p class="section-description">section 0 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section1" class="nav-link" data-toggle="tab" aria-controls="section-1">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 1</span>
                    <p class="section-description">section 1 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section2" class="nav-link" data-toggle="tab" aria-controls="section-2">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 2</span>
                    <p class="section-description">section 2 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section3" class="nav-link" data-toggle="tab" aria-controls="section-3">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 3</span>
                    <p class="section-description">section 3 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section4" class="nav-link" data-toggle="tab" aria-controls="section-4">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 4</span>
                    <p class="section-description">section 4 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section5" class="nav-link" data-toggle="tab" aria-controls="section-5">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 5</span>
                    <p class="section-description">section 5 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section6" class="nav-link" data-toggle="tab" aria-controls="section-6">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 6</span>
                    <p class="section-description">section 6 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section7" class="nav-link" data-toggle="tab" aria-controls="section-7">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 7</span>
                    <p class="section-description">section 7 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section8" class="nav-link" data-toggle="tab" aria-controls="section-8">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 8</span>
                    <p class="section-description">section 8 description</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a href="#section9" class="nav-link" data-toggle="tab" aria-controls="section-9">
                  <div class="d-flex flex-column section-header">
                    <span class="section-icon"></span>
                    <span class="lead section-title">Section 9</span>
                    <p class="section-description">section 9 description</p>
                  </div>
                </a>
              </li>
            </ul>
            <button type="button" name="button" class="slide-tabs slide-right"></button>
          </div>
          <div class="tab-content">
            <div id="section0" class="tab-pane active" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic00" class="nav-link topic-link active" data-toggle="tab" aria-controls="topic00"><span class="topic-icon"></span>Topic 0</a></li>
                    <li class="nav-item"><a href="#topic01" class="nav-link topic-link" data-toggle="tab" aria-control="topic-01"><span class="topic-icon"></span>Topic 01</a></li>
                    <li class="nav-item"><a href="#topic02" class="nav-link topic-link" data-toggle="tab" aria-control="topic-02"><span class="topic-icon"></span>Topic 02</a></li>
                    <li class="nav-item"><a href="#topic03" class="nav-link topic-link" data-toggle="tab" aria-control="topic-03"><span class="topic-icon"></span>Topic 03</a></li>
                    <li class="nav-item"><a href="#topic04" class="nav-link topic-link" data-toggle="tab" aria-control="topic-04"><span class="topic-icon"></span>Topic 04</a></li>
                    <li class="nav-item"><a href="#topic05" class="nav-link topic-link" data-toggle="tab" aria-control="topic-05"><span class="topic-icon"></span>Topic 05</a></li>
                    <li class="nav-item"><a href="#topic06" class="nav-link topic-link" data-toggle="tab" aria-control="topic-06"><span class="topic-icon"></span>Topic 06</a></li>
                    <li class="nav-item"><a href="#topic07" class="nav-link topic-link" data-toggle="tab" aria-control="topic-07"><span class="topic-icon"></span>Topic 07</a></li>
                    <li class="nav-item"><a href="#topic08" class="nav-link topic-link" data-toggle="tab" aria-control="topic-08"><span class="topic-icon"></span>Topic 08</a></li>
                    <li class="nav-item"><a href="#topic09" class="nav-link topic-link" data-toggle="tab" aria-control="topic-09"><span class="topic-icon"></span>Topic 09</a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic00" role="tabpanel">Topic 00 content</div>
                  <div class="tab-pane" id="topic01" role="tabpanel">Topic 01 content</div>
                  <div class="tab-pane" id="topic02" role="tabpanel">Topic 02 content</div>
                  <div class="tab-pane" id="topic03" role="tabpanel">Topic 03 content</div>
                  <div class="tab-pane" id="topic04" role="tabpanel">Topic 04 content</div>
                  <div class="tab-pane" id="topic05" role="tabpanel">Topic 05 content</div>
                  <div class="tab-pane" id="topic06" role="tabpanel">Topic 06 content</div>
                  <div class="tab-pane" id="topic07" role="tabpanel">Topic 07 content</div>
                  <div class="tab-pane" id="topic08" role="tabpanel">Topic 08 content</div>
                  <div class="tab-pane" id="topic09" role="tabpanel">Topic 09 content</div>
                </div>
              </div>
            </div>
            <div id="section1" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic10" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-10"><span class="topic-icon"></span><span class="lead topic-title">Topic 10</span></a></li>
                    <li class="nav-item"><a href="#topic11" class="nav-link topic-link" data-toggle="tab" aria-control="topic-11"><span class="topic-icon"></span><span class="lead topic-title">Topic 11</span></a></li>
                    <li class="nav-item"><a href="#topic12" class="nav-link topic-link" data-toggle="tab" aria-control="topic-12"><span class="topic-icon"></span><span class="lead topic-title">Topic 12</span></a></li>
                    <li class="nav-item"><a href="#topic13" class="nav-link topic-link" data-toggle="tab" aria-control="topic-13"><span class="topic-icon"></span><span class="lead topic-title">Topic 13</span></a></li>
                    <li class="nav-item"><a href="#topic14" class="nav-link topic-link" data-toggle="tab" aria-control="topic-14"><span class="topic-icon"></span><span class="lead topic-title">Topic 14</span></a></li>
                    <li class="nav-item"><a href="#topic15" class="nav-link topic-link" data-toggle="tab" aria-control="topic-15"><span class="topic-icon"></span><span class="lead topic-title">Topic 15</span></a></li>
                    <li class="nav-item"><a href="#topic16" class="nav-link topic-link" data-toggle="tab" aria-control="topic-16"><span class="topic-icon"></span><span class="lead topic-title">Topic 16</span></a></li>
                    <li class="nav-item"><a href="#topic17" class="nav-link topic-link" data-toggle="tab" aria-control="topic-17"><span class="topic-icon"></span><span class="lead topic-title">Topic 17</span></a></li>
                    <li class="nav-item"><a href="#topic18" class="nav-link topic-link" data-toggle="tab" aria-control="topic-18"><span class="topic-icon"></span><span class="lead topic-title">Topic 18</span></a></li>
                    <li class="nav-item"><a href="#topic19" class="nav-link topic-link" data-toggle="tab" aria-control="topic-19"><span class="topic-icon"></span><span class="lead topic-title">Topic 19</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic10" role="tabpanel">Topic 10 content</div>
                  <div class="tab-pane" id="topic11" role="tabpanel">Topic 11 content</div>
                  <div class="tab-pane" id="topic12" role="tabpanel">Topic 12 content</div>
                  <div class="tab-pane" id="topic13" role="tabpanel">Topic 13 content</div>
                  <div class="tab-pane" id="topic14" role="tabpanel">Topic 14 content</div>
                  <div class="tab-pane" id="topic15" role="tabpanel">Topic 15 content</div>
                  <div class="tab-pane" id="topic16" role="tabpanel">Topic 16 content</div>
                  <div class="tab-pane" id="topic17" role="tabpanel">Topic 17 content</div>
                  <div class="tab-pane" id="topic18" role="tabpanel">Topic 18 content</div>
                  <div class="tab-pane" id="topic19" role="tabpanel">Topic 19 content</div>
                </div>
              </div>
            </div>
            <div id="section2" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic20" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-20"><span class="topic-icon"></span><span class="lead topic-title">Topic 20</span></a></li>
                    <li class="nav-item"><a href="#topic21" class="nav-link topic-link" data-toggle="tab" aria-control="topic-21"><span class="topic-icon"></span><span class="lead topic-title">Topic 21</span></a></li>
                    <li class="nav-item"><a href="#topic22" class="nav-link topic-link" data-toggle="tab" aria-control="topic-22"><span class="topic-icon"></span><span class="lead topic-title">Topic 22</span></a></li>
                    <li class="nav-item"><a href="#topic23" class="nav-link topic-link" data-toggle="tab" aria-control="topic-23"><span class="topic-icon"></span><span class="lead topic-title">Topic 23</span></a></li>
                    <li class="nav-item"><a href="#topic24" class="nav-link topic-link" data-toggle="tab" aria-control="topic-24"><span class="topic-icon"></span><span class="lead topic-title">Topic 24</span></a></li>
                    <li class="nav-item"><a href="#topic25" class="nav-link topic-link" data-toggle="tab" aria-control="topic-25"><span class="topic-icon"></span><span class="lead topic-title">Topic 25</span></a></li>
                    <li class="nav-item"><a href="#topic26" class="nav-link topic-link" data-toggle="tab" aria-control="topic-26"><span class="topic-icon"></span><span class="lead topic-title">Topic 26</span></a></li>
                    <li class="nav-item"><a href="#topic27" class="nav-link topic-link" data-toggle="tab" aria-control="topic-27"><span class="topic-icon"></span><span class="lead topic-title">Topic 27</span></a></li>
                    <li class="nav-item"><a href="#topic28" class="nav-link topic-link" data-toggle="tab" aria-control="topic-28"><span class="topic-icon"></span><span class="lead topic-title">Topic 28</span></a></li>
                    <li class="nav-item"><a href="#topic29" class="nav-link topic-link" data-toggle="tab" aria-control="topic-29"><span class="topic-icon"></span><span class="lead topic-title">Topic 29</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic20" role="tabpanel">Topic 20 content</div>
                  <div class="tab-pane" id="topic21" role="tabpanel">Topic 21 content</div>
                  <div class="tab-pane" id="topic22" role="tabpanel">Topic 22 content</div>
                  <div class="tab-pane" id="topic23" role="tabpanel">Topic 23 content</div>
                  <div class="tab-pane" id="topic24" role="tabpanel">Topic 24 content</div>
                  <div class="tab-pane" id="topic25" role="tabpanel">Topic 25 content</div>
                  <div class="tab-pane" id="topic26" role="tabpanel">Topic 26 content</div>
                  <div class="tab-pane" id="topic27" role="tabpanel">Topic 27 content</div>
                  <div class="tab-pane" id="topic28" role="tabpanel">Topic 28 content</div>
                  <div class="tab-pane" id="topic29" role="tabpanel">Topic 29 content</div>
                </div>
              </div>
            </div>
            <div id="section3" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic30" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-30"><span class="topic-icon"></span><span class="lead topic-title">Topic 30</span></a></li>
                    <li class="nav-item"><a href="#topic31" class="nav-link topic-link" data-toggle="tab" aria-control="topic-31"><span class="topic-icon"></span><span class="lead topic-title">Topic 31</span></a></li>
                    <li class="nav-item"><a href="#topic32" class="nav-link topic-link" data-toggle="tab" aria-control="topic-32"><span class="topic-icon"></span><span class="lead topic-title">Topic 32</span></a></li>
                    <li class="nav-item"><a href="#topic33" class="nav-link topic-link" data-toggle="tab" aria-control="topic-33"><span class="topic-icon"></span><span class="lead topic-title">Topic 33</span></a></li>
                    <li class="nav-item"><a href="#topic34" class="nav-link topic-link" data-toggle="tab" aria-control="topic-34"><span class="topic-icon"></span><span class="lead topic-title">Topic 34</span></a></li>
                    <li class="nav-item"><a href="#topic35" class="nav-link topic-link" data-toggle="tab" aria-control="topic-35"><span class="topic-icon"></span><span class="lead topic-title">Topic 35</span></a></li>
                    <li class="nav-item"><a href="#topic36" class="nav-link topic-link" data-toggle="tab" aria-control="topic-36"><span class="topic-icon"></span><span class="lead topic-title">Topic 36</span></a></li>
                    <li class="nav-item"><a href="#topic37" class="nav-link topic-link" data-toggle="tab" aria-control="topic-37"><span class="topic-icon"></span><span class="lead topic-title">Topic 37</span></a></li>
                    <li class="nav-item"><a href="#topic38" class="nav-link topic-link" data-toggle="tab" aria-control="topic-38"><span class="topic-icon"></span><span class="lead topic-title">Topic 38</span></a></li>
                    <li class="nav-item"><a href="#topic39" class="nav-link topic-link" data-toggle="tab" aria-control="topic-39"><span class="topic-icon"></span><span class="lead topic-title">Topic 39</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic30" role="tabpanel">Topic 30 content</div>
                  <div class="tab-pane" id="topic31" role="tabpanel">Topic 31 content</div>
                  <div class="tab-pane" id="topic32" role="tabpanel">Topic 32 content</div>
                  <div class="tab-pane" id="topic33" role="tabpanel">Topic 33 content</div>
                  <div class="tab-pane" id="topic34" role="tabpanel">Topic 34 content</div>
                  <div class="tab-pane" id="topic35" role="tabpanel">Topic 35 content</div>
                  <div class="tab-pane" id="topic36" role="tabpanel">Topic 36 content</div>
                  <div class="tab-pane" id="topic37" role="tabpanel">Topic 37 content</div>
                  <div class="tab-pane" id="topic38" role="tabpanel">Topic 38 content</div>
                  <div class="tab-pane" id="topic39" role="tabpanel">Topic 39 content</div>
                </div>
              </div>
            </div>
            <div id="section4" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic40" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-40"><span class="topic-icon"></span><span class="lead topic-title">Topic 40</span></a></li>
                    <li class="nav-item"><a href="#topic41" class="nav-link topic-link" data-toggle="tab" aria-control="topic-41"><span class="topic-icon"></span><span class="lead topic-title">Topic 41</span></a></li>
                    <li class="nav-item"><a href="#topic42" class="nav-link topic-link" data-toggle="tab" aria-control="topic-42"><span class="topic-icon"></span><span class="lead topic-title">Topic 42</span></a></li>
                    <li class="nav-item"><a href="#topic43" class="nav-link topic-link" data-toggle="tab" aria-control="topic-43"><span class="topic-icon"></span><span class="lead topic-title">Topic 43</span></a></li>
                    <li class="nav-item"><a href="#topic44" class="nav-link topic-link" data-toggle="tab" aria-control="topic-44"><span class="topic-icon"></span><span class="lead topic-title">Topic 44</span></a></li>
                    <li class="nav-item"><a href="#topic45" class="nav-link topic-link" data-toggle="tab" aria-control="topic-45"><span class="topic-icon"></span><span class="lead topic-title">Topic 45</span></a></li>
                    <li class="nav-item"><a href="#topic46" class="nav-link topic-link" data-toggle="tab" aria-control="topic-46"><span class="topic-icon"></span><span class="lead topic-title">Topic 46</span></a></li>
                    <li class="nav-item"><a href="#topic47" class="nav-link topic-link" data-toggle="tab" aria-control="topic-47"><span class="topic-icon"></span><span class="lead topic-title">Topic 47</span></a></li>
                    <li class="nav-item"><a href="#topic48" class="nav-link topic-link" data-toggle="tab" aria-control="topic-48"><span class="topic-icon"></span><span class="lead topic-title">Topic 48</span></a></li>
                    <li class="nav-item"><a href="#topic49" class="nav-link topic-link" data-toggle="tab" aria-control="topic-49"><span class="topic-icon"></span><span class="lead topic-title">Topic 49</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic40" role="tabpanel">Topic 40 content</div>
                  <div class="tab-pane" id="topic41" role="tabpanel">Topic 41 content</div>
                  <div class="tab-pane" id="topic42" role="tabpanel">Topic 42 content</div>
                  <div class="tab-pane" id="topic43" role="tabpanel">Topic 43 content</div>
                  <div class="tab-pane" id="topic44" role="tabpanel">Topic 44 content</div>
                  <div class="tab-pane" id="topic45" role="tabpanel">Topic 45 content</div>
                  <div class="tab-pane" id="topic46" role="tabpanel">Topic 46 content</div>
                  <div class="tab-pane" id="topic47" role="tabpanel">Topic 47 content</div>
                  <div class="tab-pane" id="topic48" role="tabpanel">Topic 48 content</div>
                  <div class="tab-pane" id="topic49" role="tabpanel">Topic 49 content</div>
                </div>
              </div>
            </div>
            <div id="section5" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic50" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-50"><span class="topic-icon"></span><span class="lead topic-title">Topic 50</span></a></li>
                    <li class="nav-item"><a href="#topic51" class="nav-link topic-link" data-toggle="tab" aria-control="topic-51"><span class="topic-icon"></span><span class="lead topic-title">Topic 51</span></a></li>
                    <li class="nav-item"><a href="#topic52" class="nav-link topic-link" data-toggle="tab" aria-control="topic-52"><span class="topic-icon"></span><span class="lead topic-title">Topic 52</span></a></li>
                    <li class="nav-item"><a href="#topic53" class="nav-link topic-link" data-toggle="tab" aria-control="topic-53"><span class="topic-icon"></span><span class="lead topic-title">Topic 53</span></a></li>
                    <li class="nav-item"><a href="#topic54" class="nav-link topic-link" data-toggle="tab" aria-control="topic-54"><span class="topic-icon"></span><span class="lead topic-title">Topic 54</span></a></li>
                    <li class="nav-item"><a href="#topic55" class="nav-link topic-link" data-toggle="tab" aria-control="topic-55"><span class="topic-icon"></span><span class="lead topic-title">Topic 55</span></a></li>
                    <li class="nav-item"><a href="#topic56" class="nav-link topic-link" data-toggle="tab" aria-control="topic-56"><span class="topic-icon"></span><span class="lead topic-title">Topic 56</span></a></li>
                    <li class="nav-item"><a href="#topic57" class="nav-link topic-link" data-toggle="tab" aria-control="topic-57"><span class="topic-icon"></span><span class="lead topic-title">Topic 57</span></a></li>
                    <li class="nav-item"><a href="#topic58" class="nav-link topic-link" data-toggle="tab" aria-control="topic-58"><span class="topic-icon"></span><span class="lead topic-title">Topic 58</span></a></li>
                    <li class="nav-item"><a href="#topic59" class="nav-link topic-link" data-toggle="tab" aria-control="topic-59"><span class="topic-icon"></span><span class="lead topic-title">Topic 59</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic50" role="tabpanel">Topic 50 content</div>
                  <div class="tab-pane" id="topic51" role="tabpanel">Topic 51 content</div>
                  <div class="tab-pane" id="topic52" role="tabpanel">Topic 52 content</div>
                  <div class="tab-pane" id="topic53" role="tabpanel">Topic 53 content</div>
                  <div class="tab-pane" id="topic54" role="tabpanel">Topic 54 content</div>
                  <div class="tab-pane" id="topic55" role="tabpanel">Topic 55 content</div>
                  <div class="tab-pane" id="topic56" role="tabpanel">Topic 56 content</div>
                  <div class="tab-pane" id="topic57" role="tabpanel">Topic 57 content</div>
                  <div class="tab-pane" id="topic58" role="tabpanel">Topic 58 content</div>
                  <div class="tab-pane" id="topic59" role="tabpanel">Topic 59 content</div>
                </div>
              </div>
            </div>
            <div id="section6" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic60" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-60"><span class="topic-icon"></span><span class="lead topic-title">Topic 60</span></a></li>
                    <li class="nav-item"><a href="#topic61" class="nav-link topic-link" data-toggle="tab" aria-control="topic-61"><span class="topic-icon"></span><span class="lead topic-title">Topic 61</span></a></li>
                    <li class="nav-item"><a href="#topic62" class="nav-link topic-link" data-toggle="tab" aria-control="topic-62"><span class="topic-icon"></span><span class="lead topic-title">Topic 62</span></a></li>
                    <li class="nav-item"><a href="#topic63" class="nav-link topic-link" data-toggle="tab" aria-control="topic-63"><span class="topic-icon"></span><span class="lead topic-title">Topic 63</span></a></li>
                    <li class="nav-item"><a href="#topic64" class="nav-link topic-link" data-toggle="tab" aria-control="topic-64"><span class="topic-icon"></span><span class="lead topic-title">Topic 64</span></a></li>
                    <li class="nav-item"><a href="#topic65" class="nav-link topic-link" data-toggle="tab" aria-control="topic-65"><span class="topic-icon"></span><span class="lead topic-title">Topic 65</span></a></li>
                    <li class="nav-item"><a href="#topic66" class="nav-link topic-link" data-toggle="tab" aria-control="topic-66"><span class="topic-icon"></span><span class="lead topic-title">Topic 66</span></a></li>
                    <li class="nav-item"><a href="#topic67" class="nav-link topic-link" data-toggle="tab" aria-control="topic-67"><span class="topic-icon"></span><span class="lead topic-title">Topic 67</span></a></li>
                    <li class="nav-item"><a href="#topic68" class="nav-link topic-link" data-toggle="tab" aria-control="topic-68"><span class="topic-icon"></span><span class="lead topic-title">Topic 68</span></a></li>
                    <li class="nav-item"><a href="#topic69" class="nav-link topic-link" data-toggle="tab" aria-control="topic-69"><span class="topic-icon"></span><span class="lead topic-title">Topic 69</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic60" role="tabpanel">Topic 60 content</div>
                  <div class="tab-pane" id="topic61" role="tabpanel">Topic 61 content</div>
                  <div class="tab-pane" id="topic62" role="tabpanel">Topic 62 content</div>
                  <div class="tab-pane" id="topic63" role="tabpanel">Topic 63 content</div>
                  <div class="tab-pane" id="topic64" role="tabpanel">Topic 64 content</div>
                  <div class="tab-pane" id="topic65" role="tabpanel">Topic 65 content</div>
                  <div class="tab-pane" id="topic66" role="tabpanel">Topic 66 content</div>
                  <div class="tab-pane" id="topic67" role="tabpanel">Topic 67 content</div>
                  <div class="tab-pane" id="topic68" role="tabpanel">Topic 68 content</div>
                  <div class="tab-pane" id="topic69" role="tabpanel">Topic 69 content</div>
                </div>
              </div>
            </div>
            <div id="section7" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic70" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-70"><span class="topic-icon"></span><span class="lead topic-title">Topic 70</span></a></li>
                    <li class="nav-item"><a href="#topic71" class="nav-link topic-link" data-toggle="tab" aria-control="topic-71"><span class="topic-icon"></span><span class="lead topic-title">Topic 71</span></a></li>
                    <li class="nav-item"><a href="#topic72" class="nav-link topic-link" data-toggle="tab" aria-control="topic-72"><span class="topic-icon"></span><span class="lead topic-title">Topic 72</span></a></li>
                    <li class="nav-item"><a href="#topic73" class="nav-link topic-link" data-toggle="tab" aria-control="topic-73"><span class="topic-icon"></span><span class="lead topic-title">Topic 73</span></a></li>
                    <li class="nav-item"><a href="#topic74" class="nav-link topic-link" data-toggle="tab" aria-control="topic-74"><span class="topic-icon"></span><span class="lead topic-title">Topic 74</span></a></li>
                    <li class="nav-item"><a href="#topic75" class="nav-link topic-link" data-toggle="tab" aria-control="topic-75"><span class="topic-icon"></span><span class="lead topic-title">Topic 75</span></a></li>
                    <li class="nav-item"><a href="#topic76" class="nav-link topic-link" data-toggle="tab" aria-control="topic-76"><span class="topic-icon"></span><span class="lead topic-title">Topic 76</span></a></li>
                    <li class="nav-item"><a href="#topic77" class="nav-link topic-link" data-toggle="tab" aria-control="topic-77"><span class="topic-icon"></span><span class="lead topic-title">Topic 77</span></a></li>
                    <li class="nav-item"><a href="#topic78" class="nav-link topic-link" data-toggle="tab" aria-control="topic-78"><span class="topic-icon"></span><span class="lead topic-title">Topic 78</span></a></li>
                    <li class="nav-item"><a href="#topic79" class="nav-link topic-link" data-toggle="tab" aria-control="topic-79"><span class="topic-icon"></span><span class="lead topic-title">Topic 79</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic70" role="tabpanel">Topic 70 content</div>
                  <div class="tab-pane" id="topic71" role="tabpanel">Topic 71 content</div>
                  <div class="tab-pane" id="topic72" role="tabpanel">Topic 72 content</div>
                  <div class="tab-pane" id="topic73" role="tabpanel">Topic 73 content</div>
                  <div class="tab-pane" id="topic74" role="tabpanel">Topic 74 content</div>
                  <div class="tab-pane" id="topic75" role="tabpanel">Topic 75 content</div>
                  <div class="tab-pane" id="topic76" role="tabpanel">Topic 76 content</div>
                  <div class="tab-pane" id="topic77" role="tabpanel">Topic 77 content</div>
                  <div class="tab-pane" id="topic78" role="tabpanel">Topic 78 content</div>
                  <div class="tab-pane" id="topic79" role="tabpanel">Topic 79 content</div>
                </div>
              </div>
            </div>
            <div id="section8" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic80" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-80"><span class="topic-icon"></span><span class="lead topic-title">Topic 80</span></a></li>
                    <li class="nav-item"><a href="#topic81" class="nav-link topic-link" data-toggle="tab" aria-control="topic-81"><span class="topic-icon"></span><span class="lead topic-title">Topic 81</span></a></li>
                    <li class="nav-item"><a href="#topic82" class="nav-link topic-link" data-toggle="tab" aria-control="topic-82"><span class="topic-icon"></span><span class="lead topic-title">Topic 82</span></a></li>
                    <li class="nav-item"><a href="#topic83" class="nav-link topic-link" data-toggle="tab" aria-control="topic-83"><span class="topic-icon"></span><span class="lead topic-title">Topic 83</span></a></li>
                    <li class="nav-item"><a href="#topic84" class="nav-link topic-link" data-toggle="tab" aria-control="topic-84"><span class="topic-icon"></span><span class="lead topic-title">Topic 84</span></a></li>
                    <li class="nav-item"><a href="#topic85" class="nav-link topic-link" data-toggle="tab" aria-control="topic-85"><span class="topic-icon"></span><span class="lead topic-title">Topic 85</span></a></li>
                    <li class="nav-item"><a href="#topic86" class="nav-link topic-link" data-toggle="tab" aria-control="topic-86"><span class="topic-icon"></span><span class="lead topic-title">Topic 86</span></a></li>
                    <li class="nav-item"><a href="#topic87" class="nav-link topic-link" data-toggle="tab" aria-control="topic-87"><span class="topic-icon"></span><span class="lead topic-title">Topic 87</span></a></li>
                    <li class="nav-item"><a href="#topic88" class="nav-link topic-link" data-toggle="tab" aria-control="topic-88"><span class="topic-icon"></span><span class="lead topic-title">Topic 88</span></a></li>
                    <li class="nav-item"><a href="#topic89" class="nav-link topic-link" data-toggle="tab" aria-control="topic-89"><span class="topic-icon"></span><span class="lead topic-title">Topic 89</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic80" role="tabpanel">Topic 80 content</div>
                  <div class="tab-pane" id="topic81" role="tabpanel">Topic 81 content</div>
                  <div class="tab-pane" id="topic82" role="tabpanel">Topic 82 content</div>
                  <div class="tab-pane" id="topic83" role="tabpanel">Topic 83 content</div>
                  <div class="tab-pane" id="topic84" role="tabpanel">Topic 84 content</div>
                  <div class="tab-pane" id="topic85" role="tabpanel">Topic 85 content</div>
                  <div class="tab-pane" id="topic86" role="tabpanel">Topic 86 content</div>
                  <div class="tab-pane" id="topic87" role="tabpanel">Topic 87 content</div>
                  <div class="tab-pane" id="topic88" role="tabpanel">Topic 88 content</div>
                  <div class="tab-pane" id="topic89" role="tabpanel">Topic 89 content</div>
                </div>
              </div>
            </div>
            <div id="section9" class="tab-pane" role="tabpanel">
              <div class="d-flex flex-md-row-reverse">
                <div class="col col-md-2 topics-wrapper">
                  <button type="button" name="button" class="slide-tabs slide-top"></button>
                  <ul id="topics" class="nav nav-tabs flex-column flex-nowrap topics" role="tablist">
                    <li class="nav-item"><a href="#topic90" class="nav-link topic-link active" data-toggle="tab" aria-control="topic-90"><span class="topic-icon"></span><span class="lead topic-title">Topic 90</span></a></li>
                    <li class="nav-item"><a href="#topic91" class="nav-link topic-link" data-toggle="tab" aria-control="topic-91"><span class="topic-icon"></span><span class="lead topic-title">Topic 91</span></a></li>
                    <li class="nav-item"><a href="#topic92" class="nav-link topic-link" data-toggle="tab" aria-control="topic-92"><span class="topic-icon"></span><span class="lead topic-title">Topic 92</span></a></li>
                    <li class="nav-item"><a href="#topic93" class="nav-link topic-link" data-toggle="tab" aria-control="topic-93"><span class="topic-icon"></span><span class="lead topic-title">Topic 93</span></a></li>
                    <li class="nav-item"><a href="#topic94" class="nav-link topic-link" data-toggle="tab" aria-control="topic-94"><span class="topic-icon"></span><span class="lead topic-title">Topic 94</span></a></li>
                    <li class="nav-item"><a href="#topic95" class="nav-link topic-link" data-toggle="tab" aria-control="topic-95"><span class="topic-icon"></span><span class="lead topic-title">Topic 95</span></a></li>
                    <li class="nav-item"><a href="#topic96" class="nav-link topic-link" data-toggle="tab" aria-control="topic-96"><span class="topic-icon"></span><span class="lead topic-title">Topic 96</span></a></li>
                    <li class="nav-item"><a href="#topic97" class="nav-link topic-link" data-toggle="tab" aria-control="topic-97"><span class="topic-icon"></span><span class="lead topic-title">Topic 97</span></a></li>
                    <li class="nav-item"><a href="#topic98" class="nav-link topic-link" data-toggle="tab" aria-control="topic-98"><span class="topic-icon"></span><span class="lead topic-title">Topic 98</span></a></li>
                    <li class="nav-item"><a href="#topic99" class="nav-link topic-link" data-toggle="tab" aria-control="topic-99"><span class="topic-icon"></span><span class="lead topic-title">Topic 99</span></a></li>
                  </ul>
                  <button type="button" name="button" class="slide-tabs slide-bottom"></button>
                </div>
                <div class="tab-content col col-md-10 topic-content">
                  <div class="tab-pane active" id="topic90" role="tabpanel">Topic 90 content</div>
                  <div class="tab-pane" id="topic91" role="tabpanel">Topic 91 content</div>
                  <div class="tab-pane" id="topic92" role="tabpanel">Topic 92 content</div>
                  <div class="tab-pane" id="topic93" role="tabpanel">Topic 93 content</div>
                  <div class="tab-pane" id="topic94" role="tabpanel">Topic 94 content</div>
                  <div class="tab-pane" id="topic95" role="tabpanel">Topic 95 content</div>
                  <div class="tab-pane" id="topic96" role="tabpanel">Topic 96 content</div>
                  <div class="tab-pane" id="topic97" role="tabpanel">Topic 97 content</div>
                  <div class="tab-pane" id="topic98" role="tabpanel">Topic 98 content</div>
                  <div class="tab-pane" id="topic99" role="tabpanel">Topic 99 content</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        ';

        return $output;
    }

} // class ends
