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
 * Renderer for use with the course section and all the goodness that falls
 * within it.
 *
 * This renderer should contain methods useful to courses, and categories.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The core course renderer
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class core_course_renderer extends plugin_renderer_base {

    /**
     * A cache of strings
     * @var stdClass
     */
    protected $strings;

    /**
     * Override the constructor so that we can initialise the string cache
     *
     * @param moodle_page $page
     * @param string $target
     */
    public function __construct(moodle_page $page, $target) {
        $this->strings = new stdClass;
        parent::__construct($page, $target);
    }

    /**
     * Renders course info box.
     *
     * @param stdClass $course
     * @return string
     */
    public function course_info_box(stdClass $course) {
        global $CFG;

        $context = context_course::instance($course->id);

        $content = '';
        $content .= $this->output->box_start('generalbox info');

        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $content .= format_text($summary, $course->summaryformat, array('overflowdiv'=>true), $course->id);
        if (!empty($CFG->coursecontact)) {
            $coursecontactroles = explode(',', $CFG->coursecontact);
            foreach ($coursecontactroles as $roleid) {
                if ($users = get_role_users($roleid, $context, true)) {
                    foreach ($users as $teacher) {
                        $role = new stdClass();
                        $role->id = $teacher->roleid;
                        $role->name = $teacher->rolename;
                        $role->shortname = $teacher->roleshortname;
                        $role->coursealias = $teacher->rolecoursealias;
                        $fullname = fullname($teacher, has_capability('moodle/site:viewfullnames', $context));
                        $namesarray[] = role_get_name($role, $context).': <a href="'.$CFG->wwwroot.'/user/view.php?id='.
                            $teacher->id.'&amp;course='.SITEID.'">'.$fullname.'</a>';
                    }
                }
            }

            if (!empty($namesarray)) {
                $content .= "<ul class=\"teachers\">\n<li>";
                $content .= implode('</li><li>', $namesarray);
                $content .= "</li></ul>";
            }
        }

        $content .= $this->output->box_end();

        return $content;
    }

    /**
     * Renderers a structured array of courses and categories into a nice
     * XHTML tree structure.
     *
     * This method was designed initially to display the front page course/category
     * combo view. The structure can be retrieved by get_course_category_tree()
     *
     * @param array $structure
     * @return string
     */
    public function course_category_tree(array $structure) {
        $this->strings->summary = get_string('summary');

        // Generate an id and the required JS call to make this a nice widget
        $id = html_writer::random_id('course_category_tree');
        $this->page->requires->js_init_call('M.util.init_toggle_class_on_click', array($id, '.category.with_children .category_label', 'collapsed', '.category.with_children'));

        // Start content generation
        $content = html_writer::start_tag('div', array('class'=>'course_category_tree', 'id'=>$id));
        foreach ($structure as $category) {
            $content .= $this->course_category_tree_category($category);
        }
        $content .= html_writer::start_tag('div', array('class'=>'controls'));
        $content .= html_writer::tag('div', get_string('collapseall'), array('class'=>'addtoall expandall'));
        $content .= html_writer::tag('div', get_string('expandall'), array('class'=>'removefromall collapseall'));
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');

        // Return the course category tree HTML
        return $content;
    }

    /**
     * Renderers a category for use with course_category_tree
     *
     * @param array $category
     * @param int $depth
     * @return string
     */
    protected function course_category_tree_category(stdClass $category, $depth=1) {
        $content = '';
        $hassubcategories = (isset($category->categories) && count($category->categories)>0);
        $hascourses = (isset($category->courses) && count($category->courses)>0);
        $classes = array('category');
        if ($category->parent != 0) {
            $classes[] = 'subcategory';
        }
        if (empty($category->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($hassubcategories || $hascourses) {
            $classes[] = 'with_children';
            if ($depth > 1) {
                $classes[] = 'collapsed';
            }
        }
        $categoryname = format_string($category->name, true, array('context' => context_coursecat::instance($category->id)));

        $content .= html_writer::start_tag('div', array('class'=>join(' ', $classes)));
        $content .= html_writer::start_tag('div', array('class'=>'category_label'));
        $content .= html_writer::link(new moodle_url('/course/category.php', array('id'=>$category->id)), $categoryname, array('class'=>'category_link'));
        $content .= html_writer::end_tag('div');
        if ($hassubcategories) {
            $content .= html_writer::start_tag('div', array('class'=>'subcategories'));
            foreach ($category->categories as $subcategory) {
                $content .= $this->course_category_tree_category($subcategory, $depth+1);
            }
            $content .= html_writer::end_tag('div');
        }
        if ($hascourses) {
            $content .= html_writer::start_tag('div', array('class'=>'courses'));
            $coursecount = 0;
            $strinfo = new lang_string('info');
            foreach ($category->courses as $course) {
                $classes = array('course');
                $linkclass = 'course_link';
                if (!$course->visible) {
                    $linkclass .= ' dimmed';
                }
                $coursecount ++;
                $classes[] = ($coursecount%2)?'odd':'even';
                $content .= html_writer::start_tag('div', array('class'=>join(' ', $classes)));
                $content .= html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), format_string($course->fullname), array('class'=>$linkclass));
                $content .= html_writer::start_tag('div', array('class'=>'course_info clearfix'));

                // print enrol info
                if ($icons = enrol_get_course_info_icons($course)) {
                    foreach ($icons as $pix_icon) {
                        $content .= $this->render($pix_icon);
                    }
                }

                if ($course->summary) {
                    $url = new moodle_url('/course/info.php', array('id' => $course->id));
                    $image = html_writer::empty_tag('img', array('src'=>$this->output->pix_url('i/info'), 'alt'=>$this->strings->summary));
                    $content .= $this->action_link($url, $image, new popup_action('click', $url, 'courseinfo'), array('title' => $this->strings->summary));
                }
                $content .= html_writer::end_tag('div');
                $content .= html_writer::end_tag('div');
            }
            $content .= html_writer::end_tag('div');
        }
        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Build the HTML for the module chooser javascript popup
     *
     * @param array $modules A set of modules as returned form @see
     * get_module_metadata
     * @param object $course The course that will be displayed
     * @return string The composed HTML for the module
     */
    public function course_modchooser($modules, $course) {
        global $OUTPUT;

        // Add the header
        $header = html_writer::tag('div', get_string('addresourceoractivity', 'moodle'),
                array('class' => 'hd choosertitle'));

        $formcontent = html_writer::start_tag('form', array('action' => new moodle_url('/course/jumpto.php'),
                'id' => 'chooserform', 'method' => 'post'));
        $formcontent .= html_writer::start_tag('div', array('id' => 'typeformdiv'));
        $formcontent .= html_writer::tag('input', '', array('type' => 'hidden', 'id' => 'course',
                'name' => 'course', 'value' => $course->id));
        $formcontent .= html_writer::tag('input', '',
                array('type' => 'hidden', 'class' => 'jump', 'name' => 'jump', 'value' => ''));
        $formcontent .= html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'sesskey',
                'value' => sesskey()));
        $formcontent .= html_writer::end_tag('div');

        // Put everything into one tag 'options'
        $formcontent .= html_writer::start_tag('div', array('class' => 'options'));
        $formcontent .= html_writer::tag('div', get_string('selectmoduletoviewhelp', 'moodle'),
                array('class' => 'instruction'));
        // Put all options into one tag 'alloptions' to allow us to handle scrolling
        $formcontent .= html_writer::start_tag('div', array('class' => 'alloptions'));

         // Activities
        $activities = array_filter($modules, function($mod) {
            return ($mod->archetype !== MOD_ARCHETYPE_RESOURCE && $mod->archetype !== MOD_ARCHETYPE_SYSTEM);
        });
        if (count($activities)) {
            $formcontent .= $this->course_modchooser_title('activities');
            $formcontent .= $this->course_modchooser_module_types($activities);
        }

        // Resources
        $resources = array_filter($modules, function($mod) {
            return ($mod->archetype === MOD_ARCHETYPE_RESOURCE);
        });
        if (count($resources)) {
            $formcontent .= $this->course_modchooser_title('resources');
            $formcontent .= $this->course_modchooser_module_types($resources);
        }

        $formcontent .= html_writer::end_tag('div'); // modoptions
        $formcontent .= html_writer::end_tag('div'); // types

        $formcontent .= html_writer::start_tag('div', array('class' => 'submitbuttons'));
        $formcontent .= html_writer::tag('input', '',
                array('type' => 'submit', 'name' => 'submitbutton', 'class' => 'submitbutton', 'value' => get_string('add')));
        $formcontent .= html_writer::tag('input', '',
                array('type' => 'submit', 'name' => 'addcancel', 'class' => 'addcancel', 'value' => get_string('cancel')));
        $formcontent .= html_writer::end_tag('div');
        $formcontent .= html_writer::end_tag('form');

        // Wrap the whole form in a div
        $formcontent = html_writer::tag('div', $formcontent, array('id' => 'chooseform'));

        // Put all of the content together
        $content = $formcontent;

        $content = html_writer::tag('div', $content, array('class' => 'choosercontainer'));
        return $header . html_writer::tag('div', $content, array('class' => 'chooserdialoguebody'));
    }

    /**
     * Build the HTML for a specified set of modules
     *
     * @param array $modules A set of modules as used by the
     * course_modchooser_module function
     * @return string The composed HTML for the module
     */
    protected function course_modchooser_module_types($modules) {
        $return = '';
        foreach ($modules as $module) {
            if (!isset($module->types)) {
                $return .= $this->course_modchooser_module($module);
            } else {
                $return .= $this->course_modchooser_module($module, array('nonoption'));
                foreach ($module->types as $type) {
                    $return .= $this->course_modchooser_module($type, array('option', 'subtype'));
                }
            }
        }
        return $return;
    }

    /**
     * Return the HTML for the specified module adding any required classes
     *
     * @param object $module An object containing the title, and link. An
     * icon, and help text may optionally be specified. If the module
     * contains subtypes in the types option, then these will also be
     * displayed.
     * @param array $classes Additional classes to add to the encompassing
     * div element
     * @return string The composed HTML for the module
     */
    protected function course_modchooser_module($module, $classes = array('option')) {
        $output = '';
        $output .= html_writer::start_tag('div', array('class' => implode(' ', $classes)));
        $output .= html_writer::start_tag('label', array('for' => 'module_' . $module->name));
        if (!isset($module->types)) {
            $output .= html_writer::tag('input', '', array('type' => 'radio',
                    'name' => 'jumplink', 'id' => 'module_' . $module->name, 'value' => $module->link));
        }

        $output .= html_writer::start_tag('span', array('class' => 'modicon'));
        if (isset($module->icon)) {
            // Add an icon if we have one
            $output .= $module->icon;
        }
        $output .= html_writer::end_tag('span');

        $output .= html_writer::tag('span', $module->title, array('class' => 'typename'));
        if (!isset($module->help)) {
            // Add help if found
            $module->help = get_string('nohelpforactivityorresource', 'moodle');
        }

        // Format the help text using markdown with the following options
        $options = new stdClass();
        $options->trusted = false;
        $options->noclean = false;
        $options->smiley = false;
        $options->filter = false;
        $options->para = true;
        $options->newlines = false;
        $options->overflowdiv = false;
        $module->help = format_text($module->help, FORMAT_MARKDOWN, $options);
        $output .= html_writer::tag('span', $module->help, array('class' => 'typesummary'));
        $output .= html_writer::end_tag('label');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    protected function course_modchooser_title($title, $identifier = null) {
        $module = new stdClass();
        $module->name = $title;
        $module->types = array();
        $module->title = get_string($title, $identifier);
        $module->help = '';
        return $this->course_modchooser_module($module, array('moduletypetitle'));
    }
}
