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
 * Course renderer.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_pimenko\output\core;

use core_completion\progress;
use core_date;
use DateTime;
use stdClass;
use html_writer;
use completion_info;
use cm_info;
use coursecat_helper;
use moodle_url;
use lang_string;
use context_system;
use core_course_list_element;
use core_course_category;
use theme_config;
use context_course;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');

/**
 * Course renderer class.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {
    private $collapsecontainerid;

    /**
     * Renders html for completion box on course page
     * If completion is disabled, returns empty string
     * If completion is automatic, returns an icon of the current completion state
     * If completion is manual, returns a form (with an icon inside) that allows user to
     * toggle completion
     *
     * @param stdClass $course course object
     * @param completion_info $completioninfo completion info for the course, it is recommended
     *                                        to fetch once for all modules in course/section for performance
     * @param cm_info $mod module to show completion for
     * @param array $displayoptions display options, not used in core
     *
     * @return string
     */
    public function pimenko_completionicon($course, &$completioninfo, cm_info $mod, $displayoptions = []) {
        $content = '';

        // Vérification des conditions d'affichage.
        if ($this->shouldHideCompletion($displayoptions, $mod)) {
            return $content;
        }

        // Initialisation de $completioninfo si nécessaire.
        if ($completioninfo === null) {
            $completioninfo = new completion_info($course);
        }

        // Obtention du type de complétion.
        $completion = $completioninfo->is_enabled($mod);

        // Génération de l'icône de complétion en fonction du type et de l'état.
        $completionicon = $this->getCompletionIcon($completion, $completioninfo, $mod);

        // Génération du contenu HTML.
        if ($completionicon) {
            $content = $this->generateCompletionHtml($completionicon, $mod, $displayoptions, $completioninfo, $completion);
        }

        return $content;
    }

    // Méthode pour vérifier les conditions d'affichage.
    private function shouldHideCompletion($displayoptions, $mod) {
        return !empty($displayoptions['hidecompletion']) ||
            !isloggedin() ||
            isguestuser() ||
            !$mod->uservisible;
    }

    // Méthode pour obtenir l'icône de complétion en fonction du type et de l'état.
    private function getCompletionIcon($completion, $completioninfo, $mod) {
        if ($this->page->user_is_editing()) {
            return ($completion == COMPLETION_TRACKING_MANUAL) ? 'manual-enabled' : 'auto-enabled';
        } else if ($completion == COMPLETION_TRACKING_MANUAL) {
            switch ($completioninfo->get_data($mod, true)->completionstate) {
                case COMPLETION_INCOMPLETE:
                    return 'manual-n';
                case COMPLETION_COMPLETE:
                    return 'manual-y';
            }
        } else {
            switch ($completioninfo->get_data($mod, true)->completionstate) {
                case COMPLETION_INCOMPLETE:
                    return 'auto-n';
                case COMPLETION_COMPLETE:
                    return 'auto-y';
                case COMPLETION_COMPLETE_PASS:
                    return 'auto-pass';
                case COMPLETION_COMPLETE_FAIL:
                    return 'auto-fail';
            }
        }
    }

    // Méthode pour générer le contenu HTML en fonction de l'icône de complétion.
    private function generateCompletionHtml($completionicon, $mod, $displayoptions, $completioninfo, $completion) {
        $modtemplate = new stdClass();
        $modtemplate->completionicon = $completionicon;
        $modtemplate->modid = $mod->id;
        $modtemplate->modname = format_string($mod->name);
        $modtemplate->status = null;

        if ($this->page->pagelayout == 'incourse') {
            $modtemplate->displayicon = true;
        }

        $formattedname = $mod->get_formatted_name();

        if (!empty($displayoptions['showcompletiontext'])) {
            $modtemplate->completetext = format_string(
                get_string('completion-alt-' . $completionicon, 'theme_pimenko', $formattedname)
            );
            $modtemplate->tooltiptext = format_string(
                get_string('completion-tooltip-' . $completionicon, 'theme_pimenko')
            );
        }

        if ($this->page->user_is_editing()) {
            $modtemplate->useredit = 1;
            $modtemplate->state = 1;

            if ($completioninfo->get_data($mod, true)->completionstate == COMPLETION_COMPLETE) {
                $modtemplate->status = 'checked';
                $modtemplate->state = 0;
            }

            $modtemplate->class = 'completioncheck';
        } else if ($completion == COMPLETION_TRACKING_MANUAL) {
            $modtemplate->state = 1;

            if ($completioninfo->get_data($mod, true)->completionstate == COMPLETION_COMPLETE) {
                $modtemplate->status = 'checked';
                $modtemplate->state = 0;
            }

            $modtemplate->class = 'completioncheck';
        } else {
            if ($completionicon == 'auto-y' || $completionicon == 'auto-pass') {
                $modtemplate->status = 'checked disabled';
                $modtemplate->class = 'autocompletioncheck';
            } else {
                $modtemplate->status = 'disabled';
                $modtemplate->class = 'autocompletioncheck';
            }
        }

        return $this->output->render_from_template('theme_pimenko/completioncheck', $modtemplate);
    }

    /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG;

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(
            [
                'recursive' => true,
                'limit' => $CFG->frontpagecourselimit,
                'viewmoreurl' => new moodle_url('/course/index.php'),
                'viewmoretext' => new lang_string('fulllistofcourses')
            ]
        );
        $chelper->set_attributes(['class' => 'frontpage-course-list-all']);
        $courses = get_courses();

        if (!$courses && !$this->page->user_is_editing() && has_capability(
                'moodle/course:create',
                context_system::instance()
            )) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }

        return $this->frontpage_courseboxes(
            $chelper,
            $courses
        );
    }

    /**
     * Review frontpage coursebox renderer.
     *
     * @param coursecat_helper $chelper
     * @param                  $courses
     *
     * @return string
     */
    public function frontpage_courseboxes(coursecat_helper $chelper, $courses) {
        $content = '';
        $template = $this->get_courses_template(
            $chelper,
            $courses
        );

        $content .= $this->output->render_from_template(
            'theme_pimenko/course_card',
            $template
        );

        return $content;
    }

    public function get_courses_template($chelper, $courses) {
        global $CFG, $DB;

        if (empty($this->categories)) {
            $this->categories = $DB->get_records(
                'course_categories',
                ['visible' => 1]
            );
        }

        $template = new stdClass();
        $template->coursecount = 0;
        $template->courses = [];

        $rendercourses = [];
        $mycourses = enrol_get_my_courses();

        $template->tag_list = [];
        $output = null;

        // Show or hide some field for frontpage course card.
        $theme = theme_config::load('pimenko');

        $template->showcustomfields = false;
        if ($theme->settings->showcustomfields) {
            $template->showcustomfields = $theme->settings->showcustomfields;
        }

        $template->showcontacts = false;
        if ($theme->settings->showcontacts) {
            $template->showcontacts = $theme->settings->showcontacts;
        }

        $template->showstartdate = false;
        if ($theme->settings->showstartdate) {
            $template->showstartdate = $theme->settings->showstartdate;
        }

        foreach ($courses as $course) {
            if ($course->id == 1) {
                continue;
            }
            if ($course instanceof stdClass) {
                $course = new core_course_list_element($course);
            }
            if ($this->page->pagetype == "site-index" || array_key_exists($course->id, $mycourses)) {
                $rendercourse = new stdClass();
                // Get course name.
                $rendercourse->coursename = $chelper->get_course_formatted_name($course);

                // Display course contacts. See core_course_list_element::get_course_contacts().
                if ($course->has_course_contacts()) {
                    $rendercourse->contacts = [];
                    foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                        $contact = new stdClass();
                        $contact->role = $coursecontact['rolename'];
                        $contact->name = $coursecontact['username'];
                        $contact->url = new moodle_url(
                            '/user/view.php',
                            [
                                'id' => $userid,
                                'course' => SITEID
                            ]
                        );
                        $rendercourse->contacts[] = $contact;
                    }
                }

                // Get course description.
                if ($course->has_summary()) {
                    $rendercourse->coursedescription = strip_tags($chelper->get_course_formatted_summary($course));
                }
                // Get course dates.
                if ($course->startdate) {
                    $rendercourse->startdate = userdate(
                        $course->startdate,
                        get_string('strftimedate')
                    );
                }
                // Get course category name.
                if ($catid = $course->category) {
                    if (array_key_exists(
                        $catid,
                        $this->categories
                    )) {
                        $category = \core_course_category::get($course->category);
                        $rendercourse->category = $category->get_formatted_name();
                    } else {
                        $rendercourse->category = null;
                    }
                }

                // Get course link.
                $params = ["id" => $course->id];
                $rendercourse->viewurl = new moodle_url(
                    "/course/view.php",
                    $params
                );

                // Search custom fields.
                $customfields = $course->get_custom_fields();
                $rendercourse->customfields = [];
                // Adding of custom fields in the template.
                foreach ($customfields as $customfield) {
                    $cf = new stdClass();
                    $cf->customfield = $customfield->get_value();

                    if ($cf->customfield != '') {
                        $rendercourse->customfields[] = $cf;
                    }
                }

                // Course visible.
                $rendercourse->visible = $course->visible;

                // Get course image.
                foreach ($course->get_course_overviewfiles() as $file) {
                    if ($file->is_valid_image()) {
                        $rendercourse->courseimage =
                            $CFG->wwwroot . '/pluginfile.php/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                            $file->get_filearea() . $file->get_filepath() . $file->get_filename();
                    }
                }
                if (!isset($rendercourse->courseimage)) {
                    $rendercourse->courseimage = $this->output->get_generated_image_for_id($course->id);
                }

                // Get the course progress.
                $rendercourse->hasprogress = false;
                if (array_key_exists(
                    $course->id,
                    $mycourses
                )) {
                    $completion = new completion_info($course);
                    $rendercourse->hasprogress = true;
                    $rendercourse->progress = $this->course_progress($course->id);
                }

                $rendercourses[] = $rendercourse;
                $template->coursecount++;
            }
        }

        $template->courses = $rendercourses;
        return $template;
    }

    /**
     * Return course progress.
     *
     * @param int $courseid
     *
     * @return float
     */
    public function course_progress(int $courseid): float {
        $course = get_course($courseid);
        $percentage = progress::get_course_progress_percentage($course);
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        } else {
            $percentage = 0;
        }
        return $percentage;
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|core_course_category $category
     */
    public function course_category($category) {
        global $CFG;
        $usertop = core_course_category::user_top();

        if (empty($category)) {
            $coursecat = $usertop;
        } else if ($category instanceof core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        }

        $site = get_site();
        $actionbar = new \theme_pimenko\output\core\category_action_bar($this->page, $coursecat);

        $theme = theme_config::load('pimenko');
        if ($theme->settings->enablecatalog) {
            $editoption = $actionbar->export_for_template($this);

            $tagid = filter_input(INPUT_GET, 'tagid', FILTER_SANITIZE_URL);
            if (isset($editoption['tagselect'])) {
                foreach ($editoption['tagselect']->options as &$option) {
                    $url = parse_url($option['value']);
                    parse_str($url['query'], $params);
                    if ($params['tagid'] == $tagid) {
                        $option['selected'] = true;
                    } else {
                        $option['selected'] = false;
                    }
                }
            }

            if (!empty((array) $editoption['categoryselect'])) {
                $allcateg[] = [
                    'name' => get_string('allcategories', 'theme_pimenko'),
                    'value' => '/course/index.php',
                    'selected' => true
                ];

                if (($category === 0 || $category === '1' || count($editoption['categoryselect']->options) < 1)) {
                    $editoption['categoryselect']->options[0]['selected'] = false;
                    $allcateg[0]['selected'] = true;
                } else {
                    $allcateg[0]['selected'] = false;
                }

                $customtemplate = array_merge($allcateg, $editoption['categoryselect']->options);
                $editoption['categoryselect']->options = $customtemplate;
            } else {
                // If no categ we don't display this.
                unset($editoption['categoryselect']);
            }

            $template = $editoption;

        } else {
            $template = $actionbar->export_for_template($this);
        }

        $output = $this->render_from_template('core_course/category_actionbar', $template);

        if (core_course_category::is_simple_site()) {
            // There is only one category in the system, do not display link to it.
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");
        } else if (!$coursecat->id || !$coursecat->is_uservisible()) {
            $strcategories = get_string('categories');
            $this->page->set_title("$site->shortname: $strcategories");
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");
        }

        // Print current category description.
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }

        // Prepare parameters for courses and categories lists in the tree.
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
            ->set_attributes(array('class' => 'category-browse category-browse-' . $coursecat->id));

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->get_children_count()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->get_courses_count()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // We have a category that has both subcategories and courses, display pagination separately.
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        return $output;
    }

    /**
     * Returns HTML to display a tree of subcategories and courses in the given category
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat top category (this category's name and description will NOT be added to the tree)
     * @return string
     */
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        global $DB, $USER;
        // Reset the category expanded flag for this course category tree first.
        $theme = theme_config::load('pimenko');
        $gallery = $theme->settings->enablecatalog;
        $template = new stdClass();

        if (!$gallery) {
            $this->categoryexpandedonload = true;

            $template->categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
            if (empty($template->categorycontent)) {
                return '';
            }

            // Start content generation.
            $content = '';
            $template->attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
            $template->contentattributes = '';
            foreach ($template->attributes as $key => $attribute) {
                $template->contentattributes .= $key . "=" . $attribute;
            }

            if ($coursecat->get_children_count()) {
                $template->linkclass = 'collapseexpand aabtn';

                // Check if the category content contains subcategories with children's content loaded.
                if ($this->categoryexpandedonload) {
                    $template->linkclass .= ' collapse-all';
                    $template->linkname = get_string('collapseall');
                } else {
                    $template->linkname = get_string('expandall');
                }
                // Only show the collapse/expand if there are children to expand.
                $this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
            }

            $content .= $this->output->render_from_template(
                'theme_pimenko/course_category_tree',
                $template
            );
        } else {
            $context = context_system::instance();

            // If there is a category id filter then get only this category.
            if ($coursecat->id > 0) {
                $where = has_capability('moodle/category:viewhiddencategories', $context) ? '' : 'WHERE visible = 1';

                // Define the base SQL query.
                $basesql = "WITH RECURSIVE category_tree(id, name, parent, sortorder, visible) AS (
                            SELECT id, name, parent, sortorder, visible
                            FROM {course_categories} cc
                            WHERE id = :category_id
                            UNION ALL
                            SELECT cc.id, cc.name, cc.parent, cc.sortorder, cc.visible
                            FROM {course_categories} cc
                            JOIN category_tree ct ON ct.id = cc.parent
                        )
                        SELECT id, name, parent, visible
                        FROM category_tree
                        $where
                        ORDER BY sortorder
                    ";

                // Set the default parameters for the SQL query.
                $params = array(
                    'category_id' => $coursecat->id,
                );

                $cats = $DB->get_records_sql($basesql, $params);
            } else {
                // Else get all categories.
                $where = has_capability('moodle/category:viewhiddencategories', $context) ? '' : 'visible = 1';
                $cats = $DB->get_records_select('course_categories', $where, array(), 'sortorder');
            }

            $template->courses = [];

            $nbcourse = 1;
            // Categories.
            foreach ($cats as $cat) {

                $coursecategory = core_course_category::get(is_object($cat) ? $cat->id : $cat);

                $params['categoryid'] = $coursecategory->id;
                $params['tagid'] = filter_input(INPUT_GET, 'tagid', FILTER_SANITIZE_URL);
                $params['customfieldselected'] = filter_input(INPUT_GET, 'customfieldselected', FILTER_SANITIZE_URL);
                $params['customfieldtext'] = filter_input(INPUT_GET, 'customfieldtext', FILTER_SANITIZE_URL);
                $params['customfieldvalue'] = filter_input(INPUT_GET, 'customfieldvalue', FILTER_SANITIZE_URL);
                $params['day'] = filter_input(INPUT_GET, 'day', FILTER_SANITIZE_URL);
                $params['year'] = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_URL);
                $params['month'] = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_URL);
                $params['timestamp'] = filter_input(INPUT_GET, 'timestamp', FILTER_SANITIZE_URL);
                // Courses of categories.

                foreach (self::get_all_courses_by_category($params) as $c) {

                    $coursecontext = context_course::instance($c->id);

                    $course = new stdClass();
                    $course->id = $c->id;
                    $course->name = format_string(
                        $c->fullname,
                        true,
                        array('context' => context_course::instance($course->id))
                    );
                    $course->summary = $chelper->get_course_formatted_summary($c);
                    $course->visible = $c->visible;
                    $course->category = $coursecategory->get_formatted_name();
                    $course->categoryid = $coursecategory->id;

                    // Search custom fields.
                    $courseelement = new core_course_list_element($course);
                    $customfields = $courseelement->get_custom_fields();
                    $course->customfields = [];

                    // Enrolment count.
                    if (theme_config::load('pimenko')->settings->showsubscriberscount != 0) {
                        $coursesql = "WHERE e.courseid = :courseid";
                        $params['courseid'] = $course->id;
                        $sql = "SELECT COUNT(DISTINCT(ue.userid)) AS enroled_count
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                      $coursesql";
                        $enroledcount = $DB->get_field_sql($sql, $params);
                        $course->enroledcount = $enroledcount . ' ' . get_string('subscribers', 'theme_pimenko');
                    }

                    // Adding of custom fields in the template.
                    foreach ($customfields as $customfield) {
                        $cf = new stdClass();
                        $cf->customfield = $customfield->export_value();

                        if ($cf->customfield != '') {
                            $course->customfields[] = $cf;
                        }
                    }

                    if ($nbcourse <= 12) {
                        $course->display = "block";
                    } else {
                        $course->display = "none";
                    }
                    $nbcourse++;

                    $neverhidden = false;
                    $neverhiddenpaypal = false;
                    $enrolmethod = enrol_get_instances(
                        $c->id,
                        true
                    );
                    foreach ($enrolmethod as $enrol) {

                        if ($enrol->enrol == 'synopsis') {
                            $neverhidden = true;
                            break;
                        }
                        if ($enrol->enrol == 'synopsispaypal') {
                            $neverhiddenpaypal = true;
                            break;
                        }
                    }

                    // Url of the course.
                    $params = ["id" => $c->id];
                    if ($neverhidden) {
                        $course->url = new moodle_url(
                            "/enrol/synopsis/index.php",
                            $params
                        );

                    } else if ($neverhiddenpaypal) {
                        $course->url = new moodle_url(
                            "/enrol/synopsispaypal/index.php",
                            $params
                        );
                    } else {
                        $course->url = new moodle_url(
                            "/course/view.php",
                            $params
                        );
                    }

                    // Get course picture.
                    $coursefiles = $c->get_course_overviewfiles();
                    if (count($coursefiles) > 0) {
                        $file = reset($coursefiles);
                        $course->urlimg = new moodle_url(
                            '/pluginfile.php/' . $file->get_contextid() . '/course/overviewfiles/' . $file->get_source()
                        );
                    }

                    // Show course or not in catalog.
                    if ($course->visible == 1 || (theme_config::load(
                                'pimenko'
                            )->settings->viewallhiddencourses == 1 && ($neverhidden || $neverhiddenpaypal)) || is_enrolled(
                            $coursecontext,
                            $USER
                        ) || is_siteadmin($USER)) {
                        $template->courses[] = $course;
                    }
                }
            }

            if (count($template->courses) <= 12) {
                $template->loadmore = false;
            } else {
                $template->loadmore = true;
            }

            $template->catalogsummarymodal = $theme->settings->catalogsummarymodal;

            return $this->render_from_template(
                'theme_pimenko/course_gallery_container',
                $template
            );
        }

        return $content;
    }

    /**
     * Return all courses of a category
     *
     * @param array $params params
     * @return array Courses of the category
     */
    public static function get_all_courses_by_category($params): array {
        global $DB;

        $fields = [
            'c.id',
            'c.category',
            'c.sortorder',
            'c.shortname',
            'c.fullname',
            'c.idnumber',
            'c.startdate',
            'c.enddate',
            'c.visible',
            'c.cacherev',
            'c.summary',
            'c.summaryformat'
        ];

        // Define base SQL query.
        $sql = "SELECT " . implode(',', $fields) . " FROM {course} c
        WHERE c.category = :category AND c.id != 1";

        // Define conditions for WHERE clause.
        $where = [];

        if ($params['tagid'] != 0) {
            $where[] = "c.id IN (SELECT ti.itemid FROM {tag_instance} ti
                 WHERE ti.tagid = :tagid AND ti.itemtype = 'course')";
        } else if ($params['customfieldselected'] && $params['day'] && $params['month'] && $params['year']) {
            $timestamp = DateTime::createFromFormat('Y-m-d H:i:s',
                $params['year'] . '-' . $params['month'] . '-' . $params['day'] . ' 00:00:00',
                core_date::get_user_timezone_object());
            $params['timestamp'] = $timestamp->getTimestamp();
            $where[] = "c.id IN (SELECT cd.instanceid FROM {customfield_data} cd
                 LEFT JOIN {customfield_field} cf ON cd.fieldid = cf.id
                 WHERE cf.shortname = :customfieldselected AND cd.value = :timestamp)";
        } else if ($params['customfieldselected'] && $params['customfieldvalue'] != 'all') {
            $where[] = "c.id IN (SELECT cd.instanceid FROM {customfield_data} cd
                 LEFT JOIN {customfield_field} cf ON cd.fieldid = cf.id
                 WHERE cf.shortname = :customfieldselected AND cd.value = :customfieldvalue)";
        } else if ($params['customfieldtext'] && $params['customfieldvalue'] != 'all') {
            $params['customfieldvalue'] = '%' . $params['customfieldvalue'] . '%';
            $where[] = "c.id IN (SELECT cd.instanceid FROM {customfield_data} cd
                 LEFT JOIN {customfield_field} cf ON cd.fieldid = cf.id
                 WHERE cf.shortname = :customfieldtext AND cd.value LIKE :customfieldvalue)";
        }

        // Add conditions to the SQL query.
        if (!empty($where)) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        // Add ORDER BY clause.
        $sql .= " ORDER BY sortorder";

        $parameters = [
            'category' => $params['categoryid'],
            'tagid' => $params['tagid'],
            'customfieldtext' => $params['customfieldtext'],
            'customfieldselected' => $params['customfieldselected'],
            'customfieldvalue' => $params['customfieldvalue'],
            'timestamp' => $params['timestamp']
        ];

        $list = $DB->get_records_sql($sql, $parameters);

        $courses = [];

        // Prepare the list of core_course_list_element objects.
        foreach ($list as $record) {
            $courses[$record->id] = new core_course_list_element($record);
        }
        return $courses;
    }

    /**
     * Returns HTML to display course name.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_name(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        $template = new stdClass();
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $template->nametag = 'h3';
        } else {
            $template->nametag = 'div';
        }
        $coursename = $chelper->get_course_formatted_name($course);
        $template->coursenamelink = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
            $coursename, [
                'class' => $course->visible ? 'aalink' : 'aalink dimmed',
                'data-moreinfoid' => 'moreinfo' . $course->id,
                'data-summary' => $course->has_summary()
            ]);
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()
                || $course->has_custom_fields()) {
                $template->url = new moodle_url('/course/info.php', ['id' => $course->id]);
                $template->title = $this->strings->summary;
                $template->moreinfoid = 'moreinfo' . $course->id;
                $template->image = $this->output->pix_icon('i/info', $this->strings->summary);
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= $this->output->render_from_template(
            'theme_pimenko/course_name',
            $template
        );

        return $content;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $content = \html_writer::start_tag('div', ['class' => 'd-flex']);
        $content .= $this->course_overview_files($course);
        $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
        $content .= $this->course_summary($chelper, $course);
        $content .= $this->course_contacts($course);
        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('div');

        if ($this->page->pagetype !== 'enrol-index') {
            $content .= html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
                get_string(
                    'entercourse',
                    'theme_pimenko'
                ), ['class' => 'entercourse btn btn-secondary']);
        }

        return $content;
    }

    /**
     * @param $categories
     * @param $currentcategory
     * @param $name
     * @return string
     */
    private function coursecat_get_parents($categories, $currentcategory, $name): string {
        if ($currentcategory['parent'] != 0) {
            $name = $categories[$currentcategory['parent']]['name'] . ' -> ' . $name;
            return $this->coursecat_get_parents($categories, $categories[$currentcategory['parent']], $name);
        } else {
            return $name;
        }
    }
}
