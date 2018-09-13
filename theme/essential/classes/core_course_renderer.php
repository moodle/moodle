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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class theme_essential_core_course_renderer extends core_course_renderer {
    protected $enablecategoryicon;

    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        $this->enablecategoryicon = \theme_essential\toolbox::get_setting('enablecategoryicon');
    }

    /**
     * Returns HTML to display a course category as a part of a tree
     *
     * This is an internal function, to display a particular category and all its contents
     * use {@link core_course_renderer::course_category()}
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of this category in the current tree
     * @return string
     */
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        if (!$this->enablecategoryicon) {
            return parent::coursecat_category($chelper, $coursecat, $depth);
        }
        global $CFG;
        // Open category tag.
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // Do not load content.
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())
            ) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // Load category content.
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
            }
        }
        $classes[] = 'essentialcats';

        if (intval($CFG->version) >= 2013111800) {
            // Make sure JS file to expand category content is included.
            $this->coursecat_include_js();
        }

        $content = html_writer::start_tag('div', array(
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ));

        $coursescount = $coursecat->get_courses_count();
        if ($coursecat->get_children_count()) {
            $childcoursescount = $this->get_children_courses_count($coursecat);
            $coursescount = $coursescount.' - '.$childcoursescount;
            $coursecounttitle = get_string('numberofcoursesandsubcatcourses', 'theme_essential');
        } else {
            $coursecounttitle = get_string('numberofcourses');
        }
        $content .= html_writer::tag('span', $coursescount,
            array('title' => $coursecounttitle, 'class' => 'numberofcourse'));

        // Category name.
        $categoryname = html_writer::tag('span', $coursecat->get_formatted_name());

        // Do a settings check to output our icon / image for the category.
        if (\theme_essential\toolbox::get_setting('enablecustomcategoryicon')) {
            // User may have set a value for the category.
            $image = \theme_essential\toolbox::get_setting('categoryimage'.$coursecat->id, 'format_file_url');
            if (empty($image)) {
                $icon = \theme_essential\toolbox::get_setting('categoryicon'.$coursecat->id);;
            }
        }
        if ((empty($icon)) && (empty($image))) {
            // User hasn't set a value for the category, get the default.
            $image = \theme_essential\toolbox::get_setting('defaultcategoryimage', 'format_file_url');
            if (empty($image)) {
                $icon = \theme_essential\toolbox::get_setting('defaultcategoryicon');
            }
        }
        if (!empty($image)) {
            $categoryrepresentation = html_writer::start_tag('div', array('class' => 'categoryimage'));
            $categoryrepresentation .= html_writer::empty_tag('img', array('src' => $image, 'class' => 'img-responsive'));
            $categoryrepresentation .= html_writer::end_tag('div');
        } else if (!empty($icon)) {
            $categoryrepresentation = \theme_essential\toolbox::getfontawesomemarkup($icon);
        } else {
            $categoryrepresentation = '';
        }

        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                array('categoryid' => $coursecat->id)),
            $categoryrepresentation.$categoryname);
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname'));
        $content .= html_writer::end_tag('div'); // Class .info.

        // Add category content to the output.
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // Class .category.
        return $content;
    }

    /**
     * Returns the number of courses in the category and sub-categories.
     *
     * @param coursecat $coursecat
     * @return int Count of courses
     */
    protected function get_children_courses_count($coursecat) {
        $childcoursescount = 0;
        $coursecatchildren = $coursecat->get_children();
        foreach ($coursecatchildren as $coursecatchild) {
            $childcoursescount += $coursecatchild->get_courses_count();
            if ($coursecatchild->get_children_count()) {
                $childcoursescount += $this->get_children_courses_count($coursecatchild);
            }
        }
        return $childcoursescount;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|course_in_list $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if (!$this->enablecategoryicon) {
            return parent::coursecat_coursebox_content($chelper, $course);
        }
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        $coursehassummary = $course->has_summary();
        $coursehascontacts = $course->has_course_contacts();
        $courseoverviewfiles = $course->get_course_overviewfiles();
        // Display course summary.
        if ($coursehassummary) {
            $summaryclass = 'summary';
            if (($coursehascontacts == false) && (empty($courseoverviewfiles))) {
                $summaryclass .= ' fullsummarywidth';
            }
            if (!$course->visible) {
                $summaryclass .= ' dimmed';
            }
            $content .= html_writer::start_tag('div', array('class' => $summaryclass));
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('div'); // Class .summary.
        }

        // Display course overview files.
        $contentimages = $contentfiles = '';
        foreach ($courseoverviewfiles as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimages .= html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $url)),
                        array('class' => 'courseimage'));
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                        html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        $content .= $contentimages. $contentfiles;

        // Display course contacts.  See course_in_list::get_course_contacts().
        if ($coursehascontacts) {
            $teacherclass = 'teachers';
            if (!$course->visible) {
                $teacherclass .= ' dimmed';
            }
            if ((!empty($courseoverviewfiles)) && (!$coursehassummary)) {
                $teacherclass .= ' courseboxright';
            } else if ((empty($courseoverviewfiles)) && (!$coursehassummary)) {
                $teacherclass .= ' fullsummarywidth';
            } else if ((!empty($courseoverviewfiles)) && ($coursehassummary)) {
                $teacherclass .= ' fullsummarywidth';
            }
            $content .= html_writer::start_tag('ul', array('class' => $teacherclass));
            $teacherlinkattributes = array();
            if (!$course->visible) {
                $teacherlinkattributes['class'] = 'dimmed';
            }
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $faiconsetting = \theme_essential\toolbox::get_setting('courselistteachericon');
                $faiconsettinghtml = (empty($faiconsetting)) ? '' : '<span aria-hidden="true" class="'.
                    $faiconsetting.'"></span> ';
                $name = $faiconsettinghtml.$coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username'], $teacherlinkattributes);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // Class .teachers.
        }

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // Class .coursecat.
            }
        }

        return $content;
    }

    /**
     * Serves requests to /theme/essential/inspector.ajax.php
     *
     * @param string $term search term.
     * @return array of results.
     * @throws coding_exception
     */
    public function inspector_ajax($term) {
        global $USER;

        $data = array();

        $courses = enrol_get_my_courses();
        $site = get_site();

        if (array_key_exists($site->id, $courses)) {
            unset($courses[$site->id]);
        }

        foreach ($courses as $c) {
            if (isset($USER->lastcourseaccess[$c->id])) {
                $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
            } else {
                $courses[$c->id]->lastaccess = 0;
            }
        }

        // Get remote courses.
        $remotecourses = array();
        if (is_enabled_auth('mnet')) {
            $remotecourses = get_my_remotecourses();
        }
        // Remote courses will have remoteid as key, so it can be differentiated from normal courses.
        foreach ($remotecourses as $id => $val) {
            $remoteid = $val->remoteid * -1;
            $val->id = $remoteid;
            $courses[$remoteid] = $val;
        }

        if (empty($courses)) {
            return $data;
        }

        $courseitemsearchtype = \get_user_preferences('theme_essential_courseitemsearchtype');
        $sesskey = sesskey();
        foreach ($courses as $course) {
            if (!$courseitemsearchtype) {
                $label = $course->fullname;
                if (stristr($label, $term)) {
                    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id, 'sesskey' => $sesskey));
                    $data[] = array('id' => $courseurl->out(false), 'label' => $label, 'value' => $label);
                }
            } else {
                $modinfo = get_fast_modinfo($course);
                $courseformat = course_get_format($course->id);
                $course = $courseformat->get_course();
                $courseformatsettings = $courseformat->get_format_options();
                $coursenumsections = $courseformat->get_last_section_number();

                foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                    if (!$thissection->uservisible) {
                        continue;
                    }
                    if (is_object($thissection)) {
                        $thissection = $modinfo->get_section_info($thissection->section);
                    } else {
                        $thissection = $modinfo->get_section_info($thissection);
                    }
                    if ((string) $thissection->name !== '') {
                        $sectionname = format_string($thissection->name, true,
                            array('context' => context_course::instance($course->id)));
                    } else {
                        $sectionname = $courseformat->get_section_name($thissection->section);
                    }
                    if ($thissection->section <= $coursenumsections) {
                        // Do not link 'orphaned' sections.
                        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id, 'sesskey' => $sesskey));
                        if ((!empty($courseformatsettings['coursedisplay'])) &&
                            ($courseformatsettings['coursedisplay'] == COURSE_DISPLAY_MULTIPAGE)) {
                            $courseurl->param('section', $thissection->section);
                            $coursehref = $courseurl->out(false);
                        } else {
                            $coursehref = $courseurl->out(false).'#section-'.$thissection->section;
                        }
                        $label = $course->fullname.' - '.$sectionname;
                        if (stristr($label, $term)) {
                            $data[] = array('id' => $coursehref, 'label' => $label, 'value' => $label);
                        }
                    }
                    if (!empty($modinfo->sections[$thissection->section])) {
                        foreach ($modinfo->sections[$thissection->section] as $modnumber) {
                            $mod = $modinfo->cms[$modnumber];
                            if (!empty($mod->url)) {
                                $instancename = $mod->get_formatted_name();
                                $label = $course->fullname.' - '.$sectionname.' - '.$instancename;
                                if (stristr($label, $term)) {
                                    $data[] = array('id' => $mod->url->out(false), 'label' => $label, 'value' => $label);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}