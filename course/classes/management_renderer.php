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
 * Contains renderers for the course management pages.
 *
 * @package core_course
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/renderer.php');

/**
 * Main renderer for the course management pages.
 *
 * @package core_course
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_management_renderer extends plugin_renderer_base {

    /**
     * Initialises the JS required to enhance the management interface.
     *
     * Thunderbirds are go, this function kicks into gear the JS that makes the
     * course management pages that much cooler.
     */
    public function enhance_management_interface() {
        $this->page->requires->yui_module('moodle-course-management', 'M.course.management.init');
        $this->page->requires->strings_for_js(
            array(
                'show',
                'showcategory',
                'hide',
                'expand',
                'expandcategory',
                'collapse',
                'collapsecategory',
                'confirmcoursemove',
                'move',
                'cancel',
                'confirm'
            ),
            'moodle'
        );
    }

    /**
     * Displays a heading for the management pages.
     *
     * @param string $heading The heading to display
     * @param string|null $viewmode The current view mode if there are options.
     * @param int|null $categoryid The currently selected category if there is one.
     * @return string
     */
    public function management_heading($heading, $viewmode = null, $categoryid = null) {
        $html = html_writer::start_div('coursecat-management-header clearfix');
        if (!empty($heading)) {
            $html .= $this->heading($heading);
        }
        if ($viewmode !== null) {
            $html .= html_writer::start_div();
            $html .= $this->view_mode_selector(\core_course\management\helper::get_management_viewmodes(), $viewmode);
            if ($viewmode === 'courses') {
                $categories = coursecat::make_categories_list(array('moodle/category:manage', 'moodle/course:create'));
                $nothing = false;
                if ($categoryid === null) {
                    $nothing = array('' => get_string('selectacategory'));
                    $categoryid = '';
                }
                $select = new single_select($this->page->url, 'categoryid', $categories, $categoryid, $nothing);
                $html .= $this->render($select);
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Prepares the form element for the course category listing bulk actions.
     *
     * @return string
     */
    public function management_form_start() {
        $form = array('action' => $this->page->url->out(), 'method' => 'POST', 'id' => 'coursecat-management');

        $html = html_writer::start_tag('form', $form);
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $html .=  html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'bulkaction'));
        return $html;
    }

    /**
     * Closes the course category bulk management form.
     *
     * @return string
     */
    public function management_form_end() {
        return html_writer::end_tag('form');
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
            'class' => 'ml',
            'role' => 'tree',
            'aria-labelledby' => 'category-listing-title'
        );

        $html  = html_writer::start_div('category-listing');
        $html .= html_writer::tag('h3', get_string('categories'), array('id' => 'category-listing-title'));
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
            'class' => 'listitem listitem-category',
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
            $icon = $this->output->pix_icon('t/switch_minus', get_string('collapse'), 'moodle', array('class' => 'tree-icon', 'title' => ''));
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
            $icon = $this->output->pix_icon('t/switch_plus', get_string('expand'), 'moodle', array('class' => 'tree-icon', 'title' => ''));
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
                'i/navigationitem',
                '',
                'moodle',
                array('class' => 'tree-icon', 'title' => get_string('showcategory', 'moodle', $text))
            );
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
        $html .= html_writer::start_div('float-right');
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
            $actions[] = html_writer::link($url, get_string('createnewcategory'));
        }
        if (coursecat::can_approve_course_requests()) {
            $actions[] = html_writer::link(new moodle_url('/course/pending.php'), get_string('coursespending'));
        }
        if (count($actions) === 0) {
            return '';
        }
        return html_writer::div(join(' | ', $actions), 'listing-actions category-listing-actions');
    }

    /**
     * Renderers the actions for individual category list items.
     *
     * @param coursecat $category
     * @param array $actions
     * @return string
     */
    public function category_listitem_actions(coursecat $category, array $actions = null) {
        if ($actions === null) {
            $actions = \core_course\management\helper::get_category_listitem_actions($category);
        }
        $menu = new action_menu();
        $menu->attributes['class'] .= ' category-item-actions item-actions';
        $hasitems = false;
        foreach ($actions as $key => $action) {
            $hasitems = true;
            $menu->add(new action_menu_link(
                $action['url'],
                $action['icon'],
                $action['string'],
                in_array($key, array('show', 'hide', 'moveup', 'movedown')),
                array('data-action' => $key, 'class' => 'action-'.$key)
            ));
        }
        if (!$hasitems) {
            return '';
        }
        return $this->render($menu);
    }

    public function render_action_menu($menu) {
        global $OUTPUT;

        return $OUTPUT->render($menu);
    }

    /**
     * Renders bulk actions for categories.
     *
     * @param coursecat $category The currently selected category if there is one.
     * @return string
     */
    public function category_bulk_actions(coursecat $category = null) {
        // Resort courses.
        // Change parent.
        if (!coursecat::can_resort_any() && !coursecat::can_change_parent_any()) {
            return '';
        }
        $strgo = new lang_string('go');

        $html  = html_writer::start_div('category-bulk-actions bulk-actions');
        $html .= html_writer::div(get_string('categorybulkaction'), 'accesshide', array('tabindex' => '0'));
        if (coursecat::can_resort_any()) {
            $selectoptions = array(
                'selectedcategories' => get_string('selectedcategories'),
                'allcategories' => get_string('allcategories')
            );
            $form = html_writer::start_div();
            if ($category) {
                $selectoptions = array('thiscategory' => get_string('thiscategory')) + $selectoptions;
                $form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'currentcategoryid', 'value' => $category->id));
            }
            $form .= html_writer::div(
                html_writer::select(
                    $selectoptions,
                    'selectsortby',
                    'selectedcategories',
                    false,
                    array('aria-label' => get_string('selectcategorysort'))
                )
            );
            $form .= html_writer::div(
                html_writer::select(
                    array(
                        'name' => get_string('sortbyx', 'moodle', get_string('categoryname')),
                        'namedesc' => get_string('sortbyxreverse', 'moodle', get_string('categoryname')),
                        'idnumber' => get_string('sortbyx', 'moodle', get_string('idnumbercoursecategory')),
                        'idnumberdesc' => get_string('sortbyxreverse' , 'moodle' , get_string('idnumbercoursecategory')),
                        'none' => get_string('dontsortcategories')
                    ),
                    'resortcategoriesby',
                    'name',
                    false,
                    array('aria-label' => get_string('selectcategorysortby'), 'class' => 'm-t-1')
                )
            );
            $form .= html_writer::div(
                html_writer::select(
                    array(
                        'fullname' => get_string('sortbyx', 'moodle', get_string('fullnamecourse')),
                        'fullnamedesc' => get_string('sortbyxreverse', 'moodle', get_string('fullnamecourse')),
                        'shortname' => get_string('sortbyx', 'moodle', get_string('shortnamecourse')),
                        'shortnamedesc' => get_string('sortbyxreverse', 'moodle', get_string('shortnamecourse')),
                        'idnumber' => get_string('sortbyx', 'moodle', get_string('idnumbercourse')),
                        'idnumberdesc' => get_string('sortbyxreverse', 'moodle', get_string('idnumbercourse')),
                        'timecreated' => get_string('sortbyx', 'moodle', get_string('timecreatedcourse')),
                        'timecreateddesc' => get_string('sortbyxreverse', 'moodle', get_string('timecreatedcourse')),
                        'none' => get_string('dontsortcourses')
                    ),
                    'resortcoursesby',
                    'fullname',
                    false,
                    array('aria-label' => get_string('selectcoursesortby'), 'class' => 'm-t-1')
                )
            );
            $form .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'bulksort',
                'value' => get_string('sort'), 'class' => 'btn btn-secondary m-y-1'));
            $form .= html_writer::end_div();

            $html .= html_writer::start_div('detail-pair row yui3-g m-y-1');
            $html .= html_writer::div(html_writer::span(get_string('sorting')), 'pair-key span3 col-md-3 yui3-u-1-4');
            $html .= html_writer::div($form, 'pair-value span9 col-md-9 yui3-u-3-4');
            $html .= html_writer::end_div();
        }
        if (coursecat::can_change_parent_any()) {
            $options = array();
            if (has_capability('moodle/category:manage', context_system::instance())) {
                $options[0] = coursecat::get(0)->get_formatted_name();
            }
            $options += coursecat::make_categories_list('moodle/category:manage');
            $select = html_writer::select(
                $options,
                'movecategoriesto',
                '',
                array('' => 'choosedots'),
                array('aria-labelledby' => 'moveselectedcategoriesto', 'class' => 'm-r-1')
            );
            $submit = array('type' => 'submit', 'name' => 'bulkmovecategories', 'value' => get_string('move'),
                'class' => 'btn btn-secondary');
            $html .= $this->detail_pair(
                html_writer::span(get_string('moveselectedcategoriesto'), '', array('id' => 'moveselectedcategoriesto')),
                $select . html_writer::empty_tag('input', $submit)
            );
        }
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renders a course listing.
     *
     * @param coursecat $category The currently selected category. This is what the listing is focused on.
     * @param course_in_list $course The currently selected course.
     * @param int $page The page being displayed.
     * @param int $perpage The number of courses to display per page.
     * @return string
     */
    public function course_listing(coursecat $category = null, course_in_list $course = null, $page = 0, $perpage = 20) {

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

        $html  = html_writer::start_div('course-listing'.$class, array(
            'data-category' => $category->id,
            'data-page' => $page,
            'data-totalpages' => $totalpages,
            'data-totalcourses' => $totalcourses,
            'data-canmoveoutof' => $category->can_move_courses_out_of() && $category->can_move_courses_into()
        ));
        $html .= html_writer::tag('h3', $category->get_formatted_name(),
            array('id' => 'course-listing-title', 'tabindex' => '0'));
        $html .= $this->course_listing_actions($category, $course, $perpage);
        $html .= $this->listing_pagination($category, $page, $perpage);
        $html .= html_writer::start_tag('ul', array('class' => 'ml', 'role' => 'group'));
        foreach ($category->get_courses($options) as $listitem) {
            $html .= $this->course_listitem($category, $listitem, $courseid);
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->listing_pagination($category, $page, $perpage, true);
        $html .= $this->course_bulk_actions($category);
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renders pagination for a course listing.
     *
     * @param coursecat $category The category to produce pagination for.
     * @param int $page The current page.
     * @param int $perpage The number of courses to display per page.
     * @param bool $showtotals Set to true to show the total number of courses and what is being displayed.
     * @return string
     */
    protected function listing_pagination(coursecat $category, $page, $perpage, $showtotals = false) {
        $html = '';
        $totalcourses = $category->get_courses_count();
        $totalpages = ceil($totalcourses / $perpage);
        if ($showtotals) {
            if ($totalpages == 0) {
                $str = get_string('nocoursesyet');
            } else if ($totalpages == 1) {
                $str = get_string('showingacourses', 'moodle', $totalcourses);
            } else {
                $a = new stdClass;
                $a->start = ($page * $perpage) + 1;
                $a->end = min((($page + 1) * $perpage), $totalcourses);
                $a->total = $totalcourses;
                $str = get_string('showingxofycourses', 'moodle', $a);
            }
            $html .= html_writer::div($str, 'listing-pagination-totals dimmed');
        }

        if ($totalcourses <= $perpage) {
            return $html;
        }
        $aside = 2;
        $span = $aside * 2 + 1;
        $start = max($page - $aside, 0);
        $end = min($page + $aside, $totalpages - 1);
        if (($end - $start) < $span) {
            if ($start == 0) {
                $end = min($totalpages - 1, $span - 1);
            } else if ($end == ($totalpages - 1)) {
                $start = max(0, $end - $span + 1);
            }
        }
        $items = array();
        $baseurl = new moodle_url('/course/management.php', array('categoryid' => $category->id));
        if ($page > 0) {
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => 0)), get_string('first'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page - 1)), get_string('prev'));
            $items[] = '...';
        }
        for ($i = $start; $i <= $end; $i++) {
            $class = '';
            if ($page == $i) {
                $class = 'active-page';
            }
            $pageurl = new moodle_url($baseurl, array('page' => $i));
            $items[] = $this->action_button($pageurl, $i + 1, null, $class, get_string('pagea', 'moodle', $i+1));
        }
        if ($page < ($totalpages - 1)) {
            $items[] = '...';
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page + 1)), get_string('next'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $totalpages - 1)), get_string('last'));
        }

        $html .= html_writer::div(join('', $items), 'listing-pagination');
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
            'class' => 'listitem listitem-course',
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
            $actions[] = html_writer::link($url, get_string('createnewcourse'));
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
        return html_writer::div(join(' | ', $actions), 'listing-actions course-listing-actions');
    }

    /**
     * Renderers actions for individual course actions.
     *
     * @param coursecat $category The currently selected category.
     * @param course_in_list $course The course to renderer actions for.
     * @return string
     */
    public function course_listitem_actions(coursecat $category, course_in_list $course) {
        $actions = \core_course\management\helper::get_course_listitem_actions($category, $course);
        if (empty($actions)) {
            return '';
        }
        $actionshtml = array();
        foreach ($actions as $action) {
            $action['attributes']['role'] = 'button';
            $actionshtml[] = $this->output->action_icon($action['url'], $action['icon'], null, $action['attributes']);
        }
        return html_writer::span(join('', $actionshtml), 'course-item-actions item-actions');
    }

    /**
     * Renderers bulk actions that can be performed on courses.
     *
     * @param coursecat $category The currently selected category and the category in which courses that
     *      are selectable belong.
     * @return string
     */
    public function course_bulk_actions(coursecat $category) {
        $html  = html_writer::start_div('course-bulk-actions bulk-actions');
        if ($category->can_move_courses_out_of()) {
            $html .= html_writer::div(get_string('coursebulkaction'), 'accesshide', array('tabindex' => '0'));
            $options = coursecat::make_categories_list('moodle/category:manage');
            $select = html_writer::select(
                $options,
                'movecoursesto',
                '',
                array('' => 'choosedots'),
                array('aria-labelledby' => 'moveselectedcoursesto', 'class' => 'm-r-1')
            );
            $submit = array('type' => 'submit', 'name' => 'bulkmovecourses', 'value' => get_string('move'),
                'class' => 'btn btn-secondary');
            $html .= $this->detail_pair(
                html_writer::span(get_string('moveselectedcoursesto'), '', array('id' => 'moveselectedcoursesto')),
                $select . html_writer::empty_tag('input', $submit)
            );
        }
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renderers bulk actions that can be performed on courses in search returns
     *
     * @return string
     */
    public function course_search_bulk_actions() {
        $html  = html_writer::start_div('course-bulk-actions bulk-actions');
        $html .= html_writer::div(get_string('coursebulkaction'), 'accesshide', array('tabindex' => '0'));
        $options = coursecat::make_categories_list('moodle/category:manage');
        $select = html_writer::select(
            $options,
            'movecoursesto',
            '',
            array('' => 'choosedots'),
            array('aria-labelledby' => 'moveselectedcoursesto')
        );
        $submit = array('type' => 'submit', 'name' => 'bulkmovecourses', 'value' => get_string('move'),
            'class' => 'btn btn-secondary');
        $html .= $this->detail_pair(
            html_writer::span(get_string('moveselectedcoursesto'), '', array('id' => 'moveselectedcoursesto')),
            $select . html_writer::empty_tag('input', $submit)
        );
        $html .= html_writer::end_div();
        return $html;
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

        $html  = html_writer::start_div('course-detail');
        $html .= html_writer::tag('h3', $fullname, array('id' => 'course-detail-title', 'tabindex' => '0'));
        $html .= $this->course_detail_actions($course);
        foreach ($details as $class => $data) {
            $html .= $this->detail_pair($data['key'], $data['value'], $class);
        }
        $html .= html_writer::end_div();
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
        $html .= html_writer::div(html_writer::span($key), 'pair-key span3 col-md-3 yui3-u-1-4');
        $html .= html_writer::div(html_writer::span($value), 'pair-value span9 col-md-9 m-b-1 yui3-u-3-4');
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
            $options[] = $this->action_link($action['url'], $action['string']);
        }
        return html_writer::div(join(' | ', $options), 'listing-actions course-detail-listing-actions');
    }

    /**
     * Creates an action button (styled link)
     *
     * @param moodle_url $url The URL to go to when clicked.
     * @param string $text The text for the button.
     * @param string $id An id to give the button.
     * @param string $class A class to give the button.
     * @param array $attributes Any additional attributes
     * @return string
     */
    protected function action_button(moodle_url $url, $text, $id = null, $class = null, $title = null, array $attributes = array()) {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' yui3-button';
        } else {
            $attributes['class'] = 'yui3-button';
        }
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        if (!is_null($class)) {
            $attributes['class'] .= ' '.$class;
        }
        if (is_null($title)) {
            $title = $text;
        }
        $attributes['title'] = $title;
        if (!isset($attributes['role'])) {
            $attributes['role'] = 'button';
        }
        return html_writer::link($url, $text, $attributes);
    }

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
        $gridclass = 'grid-row-r row-fluid';
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
     * Closes the grid.
     *
     * @return string
     */
    public function grid_end() {
        return html_writer::end_div();
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

        // Calculate Bootstrap grid sizing.
        $bootstrapclass = 'span'.$size.' col-md-'.$size;

        // Calculate YUI grid sizing.
        if ($size === 12) {
            $maxsize = 1;
            $size = 1;
        } else {
            $maxsize = 12;
            $divisors = array(8, 6, 5, 4, 3, 2);
            foreach ($divisors as $divisor) {
                if (($maxsize % $divisor === 0) && ($size % $divisor === 0)) {
                    $maxsize = $maxsize / $divisor;
                    $size = $size / $divisor;
                    break;
                }
            }
        }
        if ($maxsize > 1) {
            $yuigridclass =  "grid-col-{$size}-{$maxsize} grid-col";
        } else {
            $yuigridclass =  "grid-col-1 grid-col";
        }

        if (is_null($class)) {
            $class = $yuigridclass . ' ' . $bootstrapclass;
        } else {
            $class .= ' ' . $yuigridclass . ' ' . $bootstrapclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class, $attributes);
    }

    /**
     * Closes a grid column.
     *
     * @return string
     */
    public function grid_column_end() {
        return html_writer::end_div();
    }

    /**
     * Renders an action_icon.
     *
     * This function uses the {@link core_renderer::action_link()} method for the
     * most part. What it does different is prepare the icon as HTML and use it
     * as the link text.
     *
     * @param string|moodle_url $url A string URL or moodel_url
     * @param pix_icon $pixicon
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     * @param bool $linktext show title next to image in link
     * @return string HTML fragment
     */
    public function action_icon($url, pix_icon $pixicon, component_action $action = null,
                                array $attributes = null, $linktext = false) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $attributes = (array)$attributes;

        if (empty($attributes['class'])) {
            // Let devs override the class via $attributes.
            $attributes['class'] = 'action-icon';
        }

        $icon = $this->render($pixicon);

        if ($linktext) {
            $text = $pixicon->attributes['alt'];
        } else {
            $text = '';
        }

        return $this->action_link($url, $icon.$text, $action, $attributes);
    }

    /**
     * Displays a view mode selector.
     *
     * @param array $modes An array of view modes.
     * @param string $currentmode The current view mode.
     * @param moodle_url $url The URL to use when changing actions. Defaults to the page URL.
     * @param string $param The param name.
     * @return string
     */
    public function view_mode_selector(array $modes, $currentmode, moodle_url $url = null, $param = 'view') {
        if ($url === null) {
            $url = $this->page->url;
        }

        $menu = new action_menu;
        $menu->attributes['class'] .= ' view-mode-selector vms';

        $selected = null;
        foreach ($modes as $mode => $modestr) {
            $attributes = array(
                'class' => 'vms-mode',
                'data-mode' => $mode
            );
            if ($currentmode === $mode) {
                $attributes['class'] .= ' currentmode';
                $selected = $modestr;
            }
            if ($selected === null) {
                $selected = $modestr;
            }
            $modeurl = new moodle_url($url, array($param => $mode));
            if ($mode === 'default') {
                $modeurl->remove_params($param);
            }
            $menu->add(new action_menu_link_secondary($modeurl, null, $modestr, $attributes));
        }

        $menu->set_menu_trigger($selected);

        $html = html_writer::start_div('view-mode-selector vms');
        $html .= get_string('viewing').' '.$this->render($menu);
        $html .= html_writer::end_div();

        return $html;
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

        $html  = html_writer::start_div('course-listing', array(
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
     * Displays pagination for search results.
     *
     * @param int $totalcourses The total number of courses to be displayed.
     * @param int $page The current page.
     * @param int $perpage The number of courses being displayed.
     * @param bool $showtotals Whether or not to print total information.
     * @param string $search The string we are searching for.
     * @return string
     */
    protected function search_pagination($totalcourses, $page, $perpage, $showtotals = false, $search = '') {
        $html = '';
        $totalpages = ceil($totalcourses / $perpage);
        if ($showtotals) {
            if ($totalpages == 0) {
                $str = get_string('nocoursesfound', 'moodle', s($search));
            } else if ($totalpages == 1) {
                $str = get_string('showingacourses', 'moodle', $totalcourses);
            } else {
                $a = new stdClass;
                $a->start = ($page * $perpage) + 1;
                $a->end = min((($page + 1) * $perpage), $totalcourses);
                $a->total = $totalcourses;
                $str = get_string('showingxofycourses', 'moodle', $a);
            }
            $html .= html_writer::div($str, 'listing-pagination-totals dimmed');
        }

        if ($totalcourses < $perpage) {
            return $html;
        }
        $aside = 2;
        $span = $aside * 2 + 1;
        $start = max($page - $aside, 0);
        $end = min($page + $aside, $totalpages - 1);
        if (($end - $start) < $span) {
            if ($start == 0) {
                $end = min($totalpages - 1, $span - 1);
            } else if ($end == ($totalpages - 1)) {
                $start = max(0, $end - $span + 1);
            }
        }
        $items = array();
        $baseurl = $this->page->url;
        if ($page > 0) {
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => 0)), get_string('first'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page - 1)), get_string('prev'));
            $items[] = '...';
        }
        for ($i = $start; $i <= $end; $i++) {
            $class = '';
            if ($page == $i) {
                $class = 'active-page';
            }
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $i)), $i + 1, null, $class);
        }
        if ($page < ($totalpages - 1)) {
            $items[] = '...';
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page + 1)), get_string('next'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $totalpages - 1)), get_string('last'));
        }

        $html .= html_writer::div(join('', $items), 'listing-pagination');
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
            'class' => 'listitem listitem-course',
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
     * Renderers actions for individual course actions.
     *
     * @param course_in_list $course The course to renderer actions for.
     * @return string
     */
    public function search_listitem_actions(course_in_list $course) {
        $baseurl = new moodle_url(
            '/course/managementsearch.php',
            array('courseid' => $course->id, 'categoryid' => $course->category, 'sesskey' => sesskey())
        );
        $actions = array();
        // Edit.
        if ($course->can_access()) {
            if ($course->can_edit()) {
                $actions[] = $this->output->action_icon(
                    new moodle_url('/course/edit.php', array('id' => $course->id)),
                    new pix_icon('t/edit', get_string('edit')),
                    null,
                    array('class' => 'action-edit')
                );
            }
            // Delete.
            if ($course->can_delete()) {
                $actions[] = $this->output->action_icon(
                    new moodle_url('/course/delete.php', array('id' => $course->id)),
                    new pix_icon('t/delete', get_string('delete')),
                    null,
                    array('class' => 'action-delete')
                );
            }
            // Show/Hide.
            if ($course->can_change_visibility()) {
                    $actions[] = $this->output->action_icon(
                        new moodle_url($baseurl, array('action' => 'hidecourse')),
                        new pix_icon('t/hide', get_string('hide')),
                        null,
                        array('data-action' => 'hide', 'class' => 'action-hide')
                    );
                    $actions[] = $this->output->action_icon(
                        new moodle_url($baseurl, array('action' => 'showcourse')),
                        new pix_icon('t/show', get_string('show')),
                        null,
                        array('data-action' => 'show', 'class' => 'action-show')
                    );
            }
        }
        if (empty($actions)) {
            return '';
        }
        return html_writer::span(join('', $actions), 'course-item-actions item-actions');
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

        $output = html_writer::start_tag('form', array('id' => $formid, 'action' => $searchurl, 'method' => 'get',
            'class' => 'form-inline'));
        $output .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset m-y-1'));
        $output .= html_writer::tag('label', $strsearchcourses, array('for' => $inputid));
        $output .= html_writer::empty_tag('input', array('type' => 'text', 'id' => $inputid, 'size' => $inputsize,
            'name' => 'search', 'value' => s($value), 'class' => 'form-control m-x-1'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go'),
            'class' => 'btn btn-secondary'));
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Creates access hidden skip to links for the displayed sections.
     *
     * @param bool $displaycategorylisting
     * @param bool $displaycourselisting
     * @param bool $displaycoursedetail
     * @return string
     */
    public function accessible_skipto_links($displaycategorylisting, $displaycourselisting, $displaycoursedetail) {
        $html = html_writer::start_div('skiplinks accesshide');
        $url = new moodle_url($this->page->url);
        if ($displaycategorylisting) {
            $url->set_anchor('category-listing');
            $html .= html_writer::link($url, get_string('skiptocategorylisting'), array('class' => 'skip'));
        }
        if ($displaycourselisting) {
            $url->set_anchor('course-listing');
            $html .= html_writer::link($url, get_string('skiptocourselisting'), array('class' => 'skip'));
        }
        if ($displaycoursedetail) {
            $url->set_anchor('course-detail');
            $html .= html_writer::link($url, get_string('skiptocoursedetails'), array('class' => 'skip'));
        }
        $html .= html_writer::end_div();
        return $html;
    }

}
