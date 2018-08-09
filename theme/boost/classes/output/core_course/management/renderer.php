<?php
// This file is part of The Bootstrap Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_boost
 * @copyright   2018 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost\output\core_course\management;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/classes/management_renderer.php");

use html_writer;
use coursecat;
use moodle_url;
use course_in_list;
use lang_string;
use context_system;
use stdClass;
use action_menu;
use action_menu_link_secondary;

/**
 * Main renderer for the course management pages.
 *
 * @package theme_boost
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_course_management_renderer {

    /**
     * Opens a grid.
     *
     * Call {@link core_course_management_renderer::grid_column_start()} to create columns.
     *
     * @param string $id An id to give this grid.
     * @param string $class A class to give this grid.
     * @return string
     */
    public function grid_start($id = null, $class = null) {
        $gridclass = 'grid-start grid-row-r d-flex flex-wrap row';
        if (is_null($class)) {
            $class = $gridclass;
        } else {
            $class .= ' ' . $gridclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class, $attributes);
    }

    /**
     * Opens a grid column
     *
     * @param int $size The number of segments this column should span.
     * @param string $id An id to give the column.
     * @param string $class A class to give the column.
     * @return string
     */
    public function grid_column_start($size, $id = null, $class = null) {

        if ($id == 'course-detail') {
            $size = 12;
            $bootstrapclass = 'col-md-'.$size;
        } else {
            $bootstrapclass = 'd-flex flex-wrap px-3 mb-3';
        }

        $yuigridclass = "col-sm";

        if (is_null($class)) {
            $class = $yuigridclass . ' ' . $bootstrapclass;
        } else {
            $class .= ' ' . $yuigridclass . ' ' . $bootstrapclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class . " grid_column_start", $attributes);
    }

    /**
     * Renderers detailed course information.
     *
     * @param course_in_list $course The course to display details for.
     * @return string
     */
    public function course_detail(course_in_list $course) {
        $details = \core_course\management\helper::get_course_detail_array($course);
        $fullname = $details['fullname']['value'];

        $html = html_writer::start_div('course-detail card');
        $html .= html_writer::start_div('card-header');
        $html .= html_writer::tag('h3', $fullname, array('id' => 'course-detail-title',
            'class' => 'card-title', 'tabindex' => '0'));
        $html .= html_writer::end_div();
        $html .= html_writer::start_div('card-body');
        $html .= $this->course_detail_actions($course);
        foreach ($details as $class => $data) {
            $html .= $this->detail_pair($data['key'], $data['value'], $class);
        }
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renders html to display a course search form
     *
     * @param string $value default value to populate the search field
     * @param string $format display format - 'plain' (default), 'short' or 'navbar'
     * @return string
     */
    public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }

        switch ($format) {
            case 'navbar' :
                $formid = 'coursesearchnavbar';
                $inputid = 'navsearchbox';
                $inputsize = 20;
                break;
            case 'short' :
                $inputid = 'shortsearchbox';
                $inputsize = 12;
                break;
            default :
                $inputid = 'coursesearchbox';
                $inputsize = 30;
        }

        $strsearchcourses = get_string("searchcourses");
        $searchurl = new moodle_url('/course/management.php');

        $output = html_writer::start_div('row');
        $output .= html_writer::start_div('col-md-12');
        $output .= html_writer::start_tag('form', array('class' => 'card', 'id' => $formid,
            'action' => $searchurl, 'method' => 'get'));
        $output .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));
        $output .= html_writer::tag('div', $this->output->heading($strsearchcourses.': ', 2, 'm-0'),
            array('class' => 'card-header'));
        $output .= html_writer::start_div('card-body');
        $output .= html_writer::start_div('input-group col-sm-6 col-lg-4 m-auto');
        $output .= html_writer::empty_tag('input', array('class' => 'form-control', 'type' => 'text', 'id' => $inputid,
            'size' => $inputsize, 'name' => 'search', 'value' => s($value)));
        $output .= html_writer::start_tag('span', array('class' => 'input-group-btn'));
        $output .= html_writer::tag('button', get_string('go'), array('class' => 'btn btn-primary', 'type' => 'submit'));
        $output .= html_writer::end_tag('span');
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();

        return $output;
    }

    /**
     * Presents a course category listing.
     *
     * @param coursecat $category The currently selected category. Also the category to highlight in the listing.
     * @return string
     */
    public function category_listing(coursecat $category = null) {

        if ($category === null) {
            $selectedparents = array();
            $selectedcategory = null;
        } else {
            $selectedparents = $category->get_parents();
            $selectedparents[] = $category->id;
            $selectedcategory = $category->id;
        }
        $catatlevel = \core_course\management\helper::get_expanded_categories('');
        $catatlevel[] = array_shift($selectedparents);
        $catatlevel = array_unique($catatlevel);

        $listing = coursecat::get(0)->get_children();

        $attributes = array(
            'class' => 'ml-1 list-unstyled',
            'role' => 'tree',
            'aria-labelledby' => 'category-listing-title'
        );

        $html  = html_writer::start_div('category-listing card w-100');
        $html .= html_writer::tag('h3', get_string('categories'),
            array('class' => 'card-header', 'id' => 'category-listing-title'));
        $html .= html_writer::start_div('card-body');
        $html .= $this->category_listing_actions($category);
        $html .= html_writer::start_tag('ul', $attributes);
        foreach ($listing as $listitem) {
            // Render each category in the listing.
            $subcategories = array();
            if (in_array($listitem->id, $catatlevel)) {
                $subcategories = $listitem->get_children();
            }
            $html .= $this->category_listitem(
                $listitem,
                $subcategories,
                $listitem->get_children_count(),
                $selectedcategory,
                $selectedparents
            );
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->category_bulk_actions($category);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renders a category list item.
     *
     * This function gets called recursively to render sub categories.
     *
     * @param coursecat $category The category to render as listitem.
     * @param coursecat[] $subcategories The subcategories belonging to the category being rented.
     * @param int $totalsubcategories The total number of sub categories.
     * @param int $selectedcategory The currently selected category
     * @param int[] $selectedcategories The path to the selected category and its ID.
     * @return string
     */
    public function category_listitem(coursecat $category, array $subcategories, $totalsubcategories,
                                      $selectedcategory = null, $selectedcategories = array()) {

        $isexpandable = ($totalsubcategories > 0);
        $isexpanded = (!empty($subcategories));
        $activecategory = ($selectedcategory === $category->id);
        $attributes = array(
            'class' => 'listitem listitem-category list-group-item list-group-item-action',
            'data-id' => $category->id,
            'data-expandable' => $isexpandable ? '1' : '0',
            'data-expanded' => $isexpanded ? '1' : '0',
            'data-selected' => $activecategory ? '1' : '0',
            'data-visible' => $category->visible ? '1' : '0',
            'role' => 'treeitem',
            'aria-expanded' => $isexpanded ? 'true' : 'false'
        );
        $text = $category->get_formatted_name();
        if ($category->parent) {
            $a = new stdClass;
            $a->category = $text;
            $a->parentcategory = $category->get_parent_coursecat()->get_formatted_name();
            $textlabel = get_string('categorysubcategoryof', 'moodle', $a);
        }
        $courseicon = $this->output->pix_icon('i/course', get_string('courses'));
        $bcatinput = array(
            'type' => 'checkbox',
            'name' => 'bcat[]',
            'value' => $category->id,
            'class' => 'bulk-action-checkbox',
            'aria-label' => get_string('bulkactionselect', 'moodle', $text),
            'data-action' => 'select'
        );

        if (!$category->can_resort_subcategories() && !$category->has_manage_capability()) {
            // Very very hardcoded here.
            $bcatinput['style'] = 'visibility:hidden';
        }

        $viewcaturl = new moodle_url('/course/management.php', array('categoryid' => $category->id));
        if ($isexpanded) {
            $icon = $this->output->pix_icon('t/switch_minus', get_string('collapse'),
                'moodle', array('class' => 'tree-icon', 'title' => ''));
            $icon = html_writer::link(
                $viewcaturl,
                $icon,
                array(
                    'class' => 'float-left',
                    'data-action' => 'collapse',
                    'title' => get_string('collapsecategory', 'moodle', $text),
                    'aria-controls' => 'subcategoryof'.$category->id
                )
            );
        } else if ($isexpandable) {
            $icon = $this->output->pix_icon('t/switch_plus', get_string('expand'),
                'moodle', array('class' => 'tree-icon', 'title' => ''));
            $icon = html_writer::link(
                $viewcaturl,
                $icon,
                array(
                    'class' => 'float-left',
                    'data-action' => 'expand',
                    'title' => get_string('expandcategory', 'moodle', $text)
                )
            );
        } else {
            $icon = $this->output->pix_icon(
                'i/empty',
                '',
                'moodle',
                array('class' => 'tree-icon'));
            $icon = html_writer::span($icon, 'float-left');
        }
        $actions = \core_course\management\helper::get_category_listitem_actions($category);
        $hasactions = !empty($actions) || $category->can_create_course();

        $html = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= html_writer::start_div('float-left ba-checkbox');
        $html .= html_writer::empty_tag('input', $bcatinput).'&nbsp;';
        $html .= html_writer::end_div();
        $html .= $icon;
        if ($hasactions) {
            $textattributes = array('class' => 'float-left categoryname');
        } else {
            $textattributes = array('class' => 'float-left categoryname without-actions');
        }
        if (isset($textlabel)) {
            $textattributes['aria-label'] = $textlabel;
        }
        $html .= html_writer::link($viewcaturl, $text, $textattributes);
        $html .= html_writer::start_div('float-right d-flex');
        if ($category->idnumber) {
            $html .= html_writer::tag('span', s($category->idnumber), array('class' => 'dimmed idnumber'));
        }
        if ($hasactions) {
            $html .= $this->category_listitem_actions($category, $actions);
        }
        $countid = 'course-count-'.$category->id;
        $html .= html_writer::span(
            html_writer::span($category->get_courses_count()) .
            html_writer::span(get_string('courses'), 'accesshide', array('id' => $countid)) .
            $courseicon,
            'course-count dimmed',
            array('aria-labelledby' => $countid)
        );
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        if ($isexpanded) {
            $html .= html_writer::start_tag('ul',
                array('class' => 'ml', 'role' => 'group', 'id' => 'subcategoryof'.$category->id));
            $catatlevel = \core_course\management\helper::get_expanded_categories($category->path);
            $catatlevel[] = array_shift($selectedcategories);
            $catatlevel = array_unique($catatlevel);
            foreach ($subcategories as $listitem) {
                $childcategories = (in_array($listitem->id, $catatlevel)) ? $listitem->get_children() : array();
                $html .= $this->category_listitem(
                    $listitem,
                    $childcategories,
                    $listitem->get_children_count(),
                    $selectedcategory,
                    $selectedcategories
                );
            }
            $html .= html_writer::end_tag('ul');
        }
        $html .= html_writer::end_tag('li');
        return $html;
    }

    /**
     * Renderers the actions that are possible for the course category listing.
     *
     * These are not the actions associated with an individual category listing.
     * That happens through category_listitem_actions.
     *
     * @param coursecat $category
     * @return string
     */
    public function category_listing_actions(coursecat $category = null) {
        $actions = array();

        $cancreatecategory = $category && $category->can_create_subcategory();
        $cancreatecategory = $cancreatecategory || coursecat::can_create_top_level_category();
        if ($category === null) {
            $category = coursecat::get(0);
        }

        if ($cancreatecategory) {
            $url = new moodle_url('/course/editcategory.php', array('parent' => $category->id));
            $actions[] = html_writer::link($url, get_string('createnewcategory'), array('class' => 'btn btn-default'));
        }
        if (coursecat::can_approve_course_requests()) {
            $actions[] = html_writer::link(new moodle_url('/course/pending.php'), get_string('coursespending'));
        }
        if (count($actions) === 0) {
            return '';
        }
        return html_writer::div(join(' ', $actions), 'listing-actions category-listing-actions mb-3');
    }

    /**
     * Renders a course listing.
     *
     * @param coursecat $category The currently selected category. This is what the listing is focused on.
     * @param course_in_list $course The currently selected course.
     * @param int $page The page being displayed.
     * @param int $perpage The number of courses to display per page.
     * @param string|null $viewmode The view mode the page is in, one out of 'default', 'combined', 'courses' or 'categories'.
     * @return string
     */
    public function course_listing(coursecat $category = null, course_in_list $course = null,
            $page = 0, $perpage = 20, $viewmode = 'default') {

        if ($category === null) {
            $html = html_writer::start_div('select-a-category');
            $html .= html_writer::tag('h3', get_string('courses'),
                array('id' => 'course-listing-title', 'tabindex' => '0'));
            $html .= $this->output->notification(get_string('selectacategory'), 'notifymessage');
            $html .= html_writer::end_div();
            return $html;
        }

        $page = max($page, 0);
        $perpage = max($perpage, 2);
        $totalcourses = $category->coursecount;
        $totalpages = ceil($totalcourses / $perpage);
        if ($page > $totalpages - 1) {
            $page = $totalpages - 1;
        }
        $options = array(
            'offset' => $page * $perpage,
            'limit' => $perpage
        );
        $courseid = isset($course) ? $course->id : null;
        $class = '';
        if ($page === 0) {
            $class .= ' firstpage';
        }
        if ($page + 1 === (int)$totalpages) {
            $class .= ' lastpage';
        }

        $html  = html_writer::start_div('card course-listing w-100'.$class, array(
            'data-category' => $category->id,
            'data-page' => $page,
            'data-totalpages' => $totalpages,
            'data-totalcourses' => $totalcourses,
            'data-canmoveoutof' => $category->can_move_courses_out_of() && $category->can_move_courses_into()
        ));
        $html .= html_writer::tag('h3', $category->get_formatted_name(),
            array('id' => 'course-listing-title', 'tabindex' => '0', 'class' => 'card-header'));
        $html .= html_writer::start_div('card-body');
        $html .= $this->course_listing_actions($category, $course, $perpage);
        $html .= $this->listing_pagination($category, $page, $perpage, false, $viewmode);
        $html .= html_writer::start_tag('ul', array('class' => 'ml course-list', 'role' => 'group'));
        foreach ($category->get_courses($options) as $listitem) {
            $html .= $this->course_listitem($category, $listitem, $courseid);
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->listing_pagination($category, $page, $perpage, true, $viewmode);
        $html .= $this->course_bulk_actions($category);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renderers a course list item.
     *
     * This function will be called for every course being displayed by course_listing.
     *
     * @param coursecat $category The currently selected category and the category the course belongs to.
     * @param course_in_list $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     */
    public function course_listitem(coursecat $category, course_in_list $course, $selectedcourse) {

        $text = $course->get_formatted_name();
        $attributes = array(
            'class' => 'listitem listitem-course list-group-item list-group-item-action',
            'data-id' => $course->id,
            'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
            'data-visible' => $course->visible ? '1' : '0'
        );

        $bulkcourseinput = array(
            'type' => 'checkbox',
            'name' => 'bc[]',
            'value' => $course->id,
            'class' => 'bulk-action-checkbox',
            'aria-label' => get_string('bulkactionselect', 'moodle', $text),
            'data-action' => 'select'
        );
        if (!$category->has_manage_capability()) {
            // Very very hardcoded here.
            $bulkcourseinput['style'] = 'visibility:hidden';
        }

        $viewcourseurl = new moodle_url($this->page->url, array('courseid' => $course->id));

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');

        if ($category->can_resort_courses()) {
            // In order for dnd to be available the user must be able to resort the category children..
            $html .= html_writer::div($this->output->pix_icon('i/move_2d', get_string('dndcourse')), 'float-left drag-handle');
        }

        $html .= html_writer::start_div('ba-checkbox float-left');
        $html .= html_writer::empty_tag('input', $bulkcourseinput).'&nbsp;';
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, array('class' => 'float-left coursename'));
        $html .= html_writer::start_div('float-right');
        if ($course->idnumber) {
            $html .= html_writer::tag('span', s($course->idnumber), array('class' => 'dimmed idnumber'));
        }
        $html .= $this->course_listitem_actions($category, $course);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }

    /**
     * Renderers actions for the course listing.
     *
     * Not to be confused with course_listitem_actions which renderers the actions for individual courses.
     *
     * @param coursecat $category
     * @param course_in_list $course The currently selected course.
     * @param int $perpage
     * @return string
     */
    public function course_listing_actions(coursecat $category, course_in_list $course = null, $perpage = 20) {
        $actions = array();
        if ($category->can_create_course()) {
            $url = new moodle_url('/course/edit.php', array('category' => $category->id, 'returnto' => 'catmanage'));
            $actions[] = html_writer::link($url, get_string('createnewcourse'), array('class' => 'btn btn-default'));
        }
        if ($category->can_request_course()) {
            // Request a new course.
            $url = new moodle_url('/course/request.php', array('return' => 'management'));
            $actions[] = html_writer::link($url, get_string('requestcourse'));
        }
        if ($category->can_resort_courses()) {
            $params = $this->page->url->params();
            $params['action'] = 'resortcourses';
            $params['sesskey'] = sesskey();
            $baseurl = new moodle_url('/course/management.php', $params);
            $fullnameurl = new moodle_url($baseurl, array('resort' => 'fullname'));
            $fullnameurldesc = new moodle_url($baseurl, array('resort' => 'fullnamedesc'));
            $shortnameurl = new moodle_url($baseurl, array('resort' => 'shortname'));
            $shortnameurldesc = new moodle_url($baseurl, array('resort' => 'shortnamedesc'));
            $idnumberurl = new moodle_url($baseurl, array('resort' => 'idnumber'));
            $idnumberdescurl = new moodle_url($baseurl, array('resort' => 'idnumberdesc'));
            $timecreatedurl = new moodle_url($baseurl, array('resort' => 'timecreated'));
            $timecreateddescurl = new moodle_url($baseurl, array('resort' => 'timecreateddesc'));
            $menu = new action_menu(array(
                new action_menu_link_secondary($fullnameurl,
                                               null,
                                               get_string('sortbyx', 'moodle', get_string('fullnamecourse'))),
                new action_menu_link_secondary($fullnameurldesc,
                                               null,
                                               get_string('sortbyxreverse', 'moodle', get_string('fullnamecourse'))),
                new action_menu_link_secondary($shortnameurl,
                                               null,
                                               get_string('sortbyx', 'moodle', get_string('shortnamecourse'))),
                new action_menu_link_secondary($shortnameurldesc,
                                               null,
                                               get_string('sortbyxreverse', 'moodle', get_string('shortnamecourse'))),
                new action_menu_link_secondary($idnumberurl,
                                               null,
                                               get_string('sortbyx', 'moodle', get_string('idnumbercourse'))),
                new action_menu_link_secondary($idnumberdescurl,
                                               null,
                                               get_string('sortbyxreverse', 'moodle', get_string('idnumbercourse'))),
                new action_menu_link_secondary($timecreatedurl,
                                               null,
                                               get_string('sortbyx', 'moodle', get_string('timecreatedcourse'))),
                new action_menu_link_secondary($timecreateddescurl,
                                               null,
                                               get_string('sortbyxreverse', 'moodle', get_string('timecreatedcourse')))
            ));
            $menu->set_menu_trigger(get_string('resortcourses'));
            $actions[] = $this->render($menu);
        }
        $strall = get_string('all');
        $menu = new action_menu(array(
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 5)), null, 5),
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 10)), null, 10),
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 20)), null, 20),
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 50)), null, 50),
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 100)), null, 100),
            new action_menu_link_secondary(new moodle_url($this->page->url, array('perpage' => 999)), null, $strall),
        ));
        if ((int)$perpage === 999) {
            $perpage = $strall;
        }
        $menu->attributes['class'] .= ' courses-per-page';
        $menu->set_menu_trigger(get_string('perpagea', 'moodle', $perpage));
        $actions[] = $this->render($menu);
        return html_writer::div(join(' ', $actions), 'listing-actions course-listing-actions');
    }

    /**
     * Displays a search result listing.
     *
     * @param array $courses The courses to display.
     * @param int $totalcourses The total number of courses to display.
     * @param course_in_list $course The currently selected course if there is one.
     * @param int $page The current page, starting at 0.
     * @param int $perpage The number of courses to display per page.
     * @param string $search The string we are searching for.
     * @return string
     */
    public function search_listing(array $courses, $totalcourses, course_in_list $course = null, $page = 0, $perpage = 20,
        $search = '') {
        $page = max($page, 0);
        $perpage = max($perpage, 2);
        $totalpages = ceil($totalcourses / $perpage);
        if ($page > $totalpages - 1) {
            $page = $totalpages - 1;
        }
        $courseid = isset($course) ? $course->id : null;
        $first = true;
        $last = false;
        $i = $page * $perpage;

        $html  = html_writer::start_div('course-listing w-100', array(
            'data-category' => 'search',
            'data-page' => $page,
            'data-totalpages' => $totalpages,
            'data-totalcourses' => $totalcourses
        ));
        $html .= html_writer::tag('h3', get_string('courses'));
        $html .= $this->search_pagination($totalcourses, $page, $perpage);
        $html .= html_writer::start_tag('ul', array('class' => 'ml'));
        foreach ($courses as $listitem) {
            $i++;
            if ($i == $totalcourses) {
                $last = true;
            }
            $html .= $this->search_listitem($listitem, $courseid, $first, $last);
            $first = false;
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->search_pagination($totalcourses, $page, $perpage, true, $search);
        $html .= $this->course_search_bulk_actions();
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renderers a search result course list item.
     *
     * This function will be called for every course being displayed by course_listing.
     *
     * @param course_in_list $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     */
    public function search_listitem(course_in_list $course, $selectedcourse) {

        $text = $course->get_formatted_name();
        $attributes = array(
            'class' => 'listitem listitem-course list-group-item list-group-item-action',
            'data-id' => $course->id,
            'data-selected' => ($selectedcourse == $course->id) ? '1' : '0',
            'data-visible' => $course->visible ? '1' : '0'
        );
        $bulkcourseinput = '';
        if (coursecat::get($course->category)->can_move_courses_out_of()) {
            $bulkcourseinput = array(
                'type' => 'checkbox',
                'name' => 'bc[]',
                'value' => $course->id,
                'class' => 'bulk-action-checkbox',
                'aria-label' => get_string('bulkactionselect', 'moodle', $text),
                'data-action' => 'select'
            );
        }
        $viewcourseurl = new moodle_url($this->page->url, array('courseid' => $course->id));
        $categoryname = coursecat::get($course->category)->get_formatted_name();

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= html_writer::start_div('float-left');
        if ($bulkcourseinput) {
            $html .= html_writer::empty_tag('input', $bulkcourseinput).'&nbsp;';
        }
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, array('class' => 'float-left coursename'));
        $html .= html_writer::tag('span', $categoryname, array('class' => 'float-left categoryname'));
        $html .= html_writer::start_div('float-right');
        $html .= $this->search_listitem_actions($course);
        $html .= html_writer::tag('span', s($course->idnumber), array('class' => 'dimmed idnumber'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }

    /**
     * Renderers a key value pair of information for display.
     *
     * @param string $key
     * @param string $value
     * @param string $class
     * @return string
     */
    protected function detail_pair($key, $value, $class ='') {
        $html = html_writer::start_div('detail-pair row yui3-g '.preg_replace('#[^a-zA-Z0-9_\-]#', '-', $class));
        $html .= html_writer::div(html_writer::span($key), 'pair-key col-md-3 yui3-u-1-4 font-weight-bold');
        $html .= html_writer::div(html_writer::span($value), 'pair-value col-md-8 yui3-u-3-4');
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * A collection of actions for a course.
     *
     * @param course_in_list $course The course to display actions for.
     * @return string
     */
    public function course_detail_actions(course_in_list $course) {
        $actions = \core_course\management\helper::get_course_detail_actions($course);
        if (empty($actions)) {
            return '';
        }
        $options = array();
        foreach ($actions as $action) {
            $options[] = $this->action_link($action['url'], $action['string'], null,
                array('class' => 'btn btn-sm btn-secondary mr-1 mb-3'));
        }
        return html_writer::div(join('', $options), 'listing-actions course-detail-listing-actions');
    }

}
