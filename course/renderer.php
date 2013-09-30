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
    const COURSECAT_SHOW_COURSES_NONE = 0; /* do not show courses at all */
    const COURSECAT_SHOW_COURSES_COUNT = 5; /* do not show courses but show number of courses next to category name */
    const COURSECAT_SHOW_COURSES_COLLAPSED = 10;
    const COURSECAT_SHOW_COURSES_AUTO = 15; /* will choose between collapsed and expanded automatically */
    const COURSECAT_SHOW_COURSES_EXPANDED = 20;
    const COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT = 30;

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
        $this->add_modchoosertoggle();
    }

    /**
     * Adds the item in course settings navigation to toggle modchooser
     *
     * Theme can overwrite as an empty function to exclude it (for example if theme does not
     * use modchooser at all)
     */
    protected function add_modchoosertoggle() {
        global $CFG;
        static $modchoosertoggleadded = false;
        // Add the module chooser toggle to the course page
        if ($modchoosertoggleadded || $this->page->state > moodle_page::STATE_PRINTING_HEADER ||
                $this->page->course->id == SITEID ||
                !$this->page->user_is_editing() ||
                !($context = context_course::instance($this->page->course->id)) ||
                !has_capability('moodle/course:manageactivities', $context) ||
                !course_ajax_enabled($this->page->course) ||
                !($coursenode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) ||
                !($turneditingnode = $coursenode->get('turneditingonoff'))) {
            // too late or we are on site page or we could not find the adjacent nodes in course settings menu
            // or we are not allowed to edit
            return;
        }
        $modchoosertoggleadded = true;
        if ($this->page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
            // We are on the course page, retain the current page params e.g. section.
            $modchoosertoggleurl = clone($this->page->url);
        } else {
            // Edit on the main course page.
            $modchoosertoggleurl = new moodle_url('/course/view.php', array('id' => $this->page->course->id,
                'return' => $this->page->url->out_as_local_url(false)));
        }
        $modchoosertoggleurl->param('sesskey', sesskey());
        if ($usemodchooser = get_user_preferences('usemodchooser', $CFG->modchooserdefault)) {
            $modchoosertogglestring = get_string('modchooserdisable', 'moodle');
            $modchoosertoggleurl->param('modchooser', 'off');
        } else {
            $modchoosertogglestring = get_string('modchooserenable', 'moodle');
            $modchoosertoggleurl->param('modchooser', 'on');
        }
        $modchoosertoggle = navigation_node::create($modchoosertogglestring, $modchoosertoggleurl, navigation_node::TYPE_SETTING, null, 'modchoosertoggle');

        // Insert the modchoosertoggle after the settings node 'turneditingonoff' (navigation_node only has function to insert before, so we insert before and then swap).
        $coursenode->add_node($modchoosertoggle, 'turneditingonoff');
        $turneditingnode->remove();
        $coursenode->add_node($turneditingnode, 'modchoosertoggle');

        $modchoosertoggle->add_class('modchoosertoggle');
        $modchoosertoggle->add_class('visibleifjs');
        user_preference_allow_ajax_update('usemodchooser', PARAM_BOOL);
    }

    /**
     * Renders course info box.
     *
     * @param stdClass|course_in_list $course
     * @return string
     */
    public function course_info_box(stdClass $course) {
        $content = '';
        $content .= $this->output->box_start('generalbox info');
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
        $content .= $this->coursecat_coursebox($chelper, $course);
        $content .= $this->output->box_end();
        return $content;
    }

    /**
     * Renderers a structured array of courses and categories into a nice XHTML tree structure.
     *
     * @deprecated since 2.5
     *
     * Please see http://docs.moodle.org/dev/Courses_lists_upgrade_to_2.5
     *
     * @param array $ignored argument ignored
     * @return string
     */
    public final function course_category_tree(array $ignored) {
        debugging('Function core_course_renderer::course_category_tree() is deprecated, please use frontpage_combo_list()', DEBUG_DEVELOPER);
        return $this->frontpage_combo_list();
    }

    /**
     * Renderers a category for use with course_category_tree
     *
     * @deprecated since 2.5
     *
     * Please see http://docs.moodle.org/dev/Courses_lists_upgrade_to_2.5
     *
     * @param array $category
     * @param int $depth
     * @return string
     */
    protected final function course_category_tree_category(stdClass $category, $depth=1) {
        debugging('Function core_course_renderer::course_category_tree_category() is deprecated', DEBUG_DEVELOPER);
        return '';
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
        static $isdisplayed = false;
        if ($isdisplayed) {
            return '';
        }
        $isdisplayed = true;

        // Add the module chooser
        $this->page->requires->yui_module('moodle-course-modchooser',
        'M.course.init_chooser',
        array(array('courseid' => $course->id, 'closeButtonTitle' => get_string('close', 'editor')))
        );
        $this->page->requires->strings_for_js(array(
                'addresourceoractivity',
                'modchooserenable',
                'modchooserdisable',
        ), 'moodle');

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
        $activities = array_filter($modules, create_function('$mod', 'return ($mod->archetype !== MOD_ARCHETYPE_RESOURCE && $mod->archetype !== MOD_ARCHETYPE_SYSTEM);'));
        if (count($activities)) {
            $formcontent .= $this->course_modchooser_title('activities');
            $formcontent .= $this->course_modchooser_module_types($activities);
        }

        // Resources
        $resources = array_filter($modules, create_function('$mod', 'return ($mod->archetype === MOD_ARCHETYPE_RESOURCE);'));
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

    /**
     * Renders HTML for displaying the sequence of course module editing buttons
     *
     * @see course_get_cm_edit_actions()
     *
     * @param array $actions array of action_link or pix_icon objects
     * @return string
     */
    public function course_section_cm_edit_actions($actions) {
        $output = html_writer::start_tag('span', array('class' => 'commands'));
        foreach ($actions as $action) {
            if ($action instanceof renderable) {
                $output .= $this->output->render($action);
            } else {
                $output .= $action;
            }
        }
        $output .= html_writer::end_tag('span');
        return $output;
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
    function course_section_add_cm_control($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $CFG;

        $vertical = !empty($displayoptions['inblock']);

        // check to see if user can add menus and there are modules to add
        if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                || !$this->page->user_is_editing()
                || !($modnames = get_module_types_names()) || empty($modnames)) {
            return '';
        }

        // Retrieve all modules with associated metadata
        $modules = get_module_metadata($course, $modnames, $sectionreturn);
        $urlparams = array('section' => $section);

        // We'll sort resources and activities into two lists
        $activities = array(MOD_CLASS_ACTIVITY => array(), MOD_CLASS_RESOURCE => array());

        foreach ($modules as $module) {
            if (isset($module->types)) {
                // This module has a subtype
                // NOTE: this is legacy stuff, module subtypes are very strongly discouraged!!
                $subtypes = array();
                foreach ($module->types as $subtype) {
                    $link = $subtype->link->out(true, $urlparams);
                    $subtypes[$link] = $subtype->title;
                }

                // Sort module subtypes into the list
                $activityclass = MOD_CLASS_ACTIVITY;
                if ($module->archetype == MOD_CLASS_RESOURCE) {
                    $activityclass = MOD_CLASS_RESOURCE;
                }
                if (!empty($module->title)) {
                    // This grouping has a name
                    $activities[$activityclass][] = array($module->title => $subtypes);
                } else {
                    // This grouping does not have a name
                    $activities[$activityclass] = array_merge($activities[$activityclass], $subtypes);
                }
            } else {
                // This module has no subtypes
                $activityclass = MOD_CLASS_ACTIVITY;
                if ($module->archetype == MOD_ARCHETYPE_RESOURCE) {
                    $activityclass = MOD_CLASS_RESOURCE;
                } else if ($module->archetype === MOD_ARCHETYPE_SYSTEM) {
                    // System modules cannot be added by user, do not add to dropdown
                    continue;
                }
                $link = $module->link->out(true, $urlparams);
                $activities[$activityclass][$link] = $module->title;
            }
        }

        $straddactivity = get_string('addactivity');
        $straddresource = get_string('addresource');
        $sectionname = get_section_name($course, $section);
        $strresourcelabel = get_string('addresourcetosection', null, $sectionname);
        $stractivitylabel = get_string('addactivitytosection', null, $sectionname);

        $output = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-' . $section));

        if (!$vertical) {
            $output .= html_writer::start_tag('div', array('class' => 'horizontal'));
        }

        if (!empty($activities[MOD_CLASS_RESOURCE])) {
            $select = new url_select($activities[MOD_CLASS_RESOURCE], '', array(''=>$straddresource), "ressection$section");
            $select->set_help_icon('resources');
            $select->set_label($strresourcelabel, array('class' => 'accesshide'));
            $output .= $this->output->render($select);
        }

        if (!empty($activities[MOD_CLASS_ACTIVITY])) {
            $select = new url_select($activities[MOD_CLASS_ACTIVITY], '', array(''=>$straddactivity), "section$section");
            $select->set_help_icon('activities');
            $select->set_label($stractivitylabel, array('class' => 'accesshide'));
            $output .= $this->output->render($select);
        }

        if (!$vertical) {
            $output .= html_writer::end_tag('div');
        }

        $output .= html_writer::end_tag('div');

        if (course_ajax_enabled($course) && $course->id == $this->page->course->id) {
            // modchooser can be added only for the current course set on the page!
            $straddeither = get_string('addresourceoractivity');
            // The module chooser link
            $modchooser = html_writer::start_tag('div', array('class' => 'mdl-right'));
            $modchooser.= html_writer::start_tag('div', array('class' => 'section-modchooser'));
            $icon = $this->output->pix_icon('t/add', '');
            $span = html_writer::tag('span', $straddeither, array('class' => 'section-modchooser-text'));
            $modchooser .= html_writer::tag('span', $icon . $span, array('class' => 'section-modchooser-link'));
            $modchooser.= html_writer::end_tag('div');
            $modchooser.= html_writer::end_tag('div');

            // Wrap the normal output in a noscript div
            $usemodchooser = get_user_preferences('usemodchooser', $CFG->modchooserdefault);
            if ($usemodchooser) {
                $output = html_writer::tag('div', $output, array('class' => 'hiddenifjs addresourcedropdown'));
                $modchooser = html_writer::tag('div', $modchooser, array('class' => 'visibleifjs addresourcemodchooser'));
            } else {
                // If the module chooser is disabled, we need to ensure that the dropdowns are shown even if javascript is disabled
                $output = html_writer::tag('div', $output, array('class' => 'show addresourcedropdown'));
                $modchooser = html_writer::tag('div', $modchooser, array('class' => 'hide addresourcemodchooser'));
            }
            $output = $this->course_modchooser($modules, $course) . $modchooser . $output;
        }

        return $output;
    }

    /**
     * Renders html to display a course search form
     *
     * @param string $value default value to populate the search field
     * @param string $format display format - 'plain' (default), 'short' or 'navbar'
     * @return string
     */
    function course_search_form($value = '', $format = 'plain') {
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

        $strsearchcourses= get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $output = html_writer::start_tag('form', array('id' => $formid, 'action' => $searchurl, 'method' => 'get'));
        $output .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));
        $output .= html_writer::tag('label', $strsearchcourses.': ', array('for' => $inputid));
        $output .= html_writer::empty_tag('input', array('type' => 'text', 'id' => $inputid,
            'size' => $inputsize, 'name' => 'search', 'value' => s($value)));
        $output .= html_writer::empty_tag('input', array('type' => 'submit',
            'value' => get_string('go')));
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Renders html for completion box on course page
     *
     * If completion is disabled, returns empty string
     * If completion is automatic, returns an icon of the current completion state
     * If completion is manual, returns a form (with an icon inside) that allows user to
     * toggle completion
     *
     * @param stdClass $course course object
     * @param completion_info $completioninfo completion info for the course, it is recommended
     *     to fetch once for all modules in course/section for performance
     * @param cm_info $mod module to show completion for
     * @param array $displayoptions display options, not used in core
     * @return string
     */
    public function course_section_cm_completion($course, &$completioninfo, cm_info $mod, $displayoptions = array()) {
        global $CFG;
        $output = '';
        if (!empty($displayoptions['hidecompletion']) || !isloggedin() || isguestuser() || !$mod->uservisible) {
            return $output;
        }
        if ($completioninfo === null) {
            $completioninfo = new completion_info($course);
        }
        $completion = $completioninfo->is_enabled($mod);
        if ($completion == COMPLETION_TRACKING_NONE) {
            return $output;
        }

        $completiondata = $completioninfo->get_data($mod, true);
        $completionicon = '';

        if ($this->page->user_is_editing()) {
            switch ($completion) {
                case COMPLETION_TRACKING_MANUAL :
                    $completionicon = 'manual-enabled'; break;
                case COMPLETION_TRACKING_AUTOMATIC :
                    $completionicon = 'auto-enabled'; break;
            }
        } else if ($completion == COMPLETION_TRACKING_MANUAL) {
            switch($completiondata->completionstate) {
                case COMPLETION_INCOMPLETE:
                    $completionicon = 'manual-n'; break;
                case COMPLETION_COMPLETE:
                    $completionicon = 'manual-y'; break;
            }
        } else { // Automatic
            switch($completiondata->completionstate) {
                case COMPLETION_INCOMPLETE:
                    $completionicon = 'auto-n'; break;
                case COMPLETION_COMPLETE:
                    $completionicon = 'auto-y'; break;
                case COMPLETION_COMPLETE_PASS:
                    $completionicon = 'auto-pass'; break;
                case COMPLETION_COMPLETE_FAIL:
                    $completionicon = 'auto-fail'; break;
            }
        }
        if ($completionicon) {
            $formattedname = $mod->get_formatted_name();
            $imgalt = get_string('completion-alt-' . $completionicon, 'completion', $formattedname);
            if ($completion == COMPLETION_TRACKING_MANUAL && !$this->page->user_is_editing()) {
                $imgtitle = get_string('completion-title-' . $completionicon, 'completion', $formattedname);
                $newstate =
                    $completiondata->completionstate == COMPLETION_COMPLETE
                    ? COMPLETION_INCOMPLETE
                    : COMPLETION_COMPLETE;
                // In manual mode the icon is a toggle form...

                // If this completion state is used by the
                // conditional activities system, we need to turn
                // off the JS.
                $extraclass = '';
                if (!empty($CFG->enableavailability) &&
                        condition_info::completion_value_used_as_condition($course, $mod)) {
                    $extraclass = ' preventjs';
                }
                $output .= html_writer::start_tag('form', array('method' => 'post',
                    'action' => new moodle_url('/course/togglecompletion.php'),
                    'class' => 'togglecompletion'. $extraclass));
                $output .= html_writer::start_tag('div');
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'id', 'value' => $mod->id));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'modulename', 'value' => $mod->name));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'hidden', 'name' => 'completionstate', 'value' => $newstate));
                $output .= html_writer::empty_tag('input', array(
                    'type' => 'image',
                    'src' => $this->output->pix_url('i/completion-'.$completionicon),
                    'alt' => $imgalt, 'title' => $imgtitle,
                    'aria-live' => 'polite'));
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('form');
            } else {
                // In auto mode, or when editing, the icon is just an image
                $completionpixicon = new pix_icon('i/completion-'.$completionicon, $imgalt, '',
                        array('title' => $imgalt));
                $output .= html_writer::tag('span', $this->output->render($completionpixicon),
                        array('class' => 'autocompletion'));
            }
        }
        return $output;
    }

    /**
     * Checks if course module has any conditions that may make it unavailable for
     * all or some of the students
     *
     * This function is internal and is only used to create CSS classes for the module name/text
     *
     * @param cm_info $mod
     * @return bool
     */
    protected function is_cm_conditionally_hidden(cm_info $mod) {
        global $CFG;
        $conditionalhidden = false;
        if (!empty($CFG->enableavailability)) {
            $conditionalhidden = $mod->availablefrom > time() ||
                ($mod->availableuntil && $mod->availableuntil < time()) ||
                count($mod->conditionsgrade) > 0 ||
                count($mod->conditionscompletion) > 0 ||
                count($mod->conditionsfield);
        }
        return $conditionalhidden;
    }

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
        global $CFG;
        $output = '';
        if (!$mod->uservisible &&
                (empty($mod->showavailability) || empty($mod->availableinfo))) {
            // nothing to be displayed to the user
            return $output;
        }
        $url = $mod->get_url();
        if (!$url) {
            return $output;
        }

        //Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(textlib::strtolower($instancename),
                textlib::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        // For items which are hidden but available to current user
        // ($mod->uservisible), we show those as dimmed only if the user has
        // viewhiddenactivities, so that teachers see 'items which might not
        // be available to some students' dimmed but students do not see 'item
        // which is actually available to current student' dimmed.
        $linkclasses = '';
        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                has_capability('moodle/course:viewhiddenactivities',
                        context_course::instance($mod->course));
            if ($accessiblebutdim) {
                $linkclasses .= ' dimmed';
                $textclasses .= ' dimmed_text';
                if ($conditionalhidden) {
                    $linkclasses .= ' conditionalhidden';
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        } else {
            $linkclasses .= ' dimmed';
            $textclasses .= ' dimmed_text';
        }

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->get_on_click(), ENT_QUOTES);

        $groupinglabel = '';
        if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', context_course::instance($mod->course))) {
            $groupings = groups_get_all_groupings($mod->course);
            $groupinglabel = html_writer::tag('span', '('.format_string($groupings[$mod->groupingid]->name).')',
                    array('class' => 'groupinglabel '.$textclasses));
        }

        // Display link itself.
        $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) . $accesstext .
                html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            $output .= html_writer::link($url, $activitylink, array('class' => $linkclasses, 'onclick' => $onclick)) .
                    $groupinglabel;
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible)
            $output .= html_writer::tag('div', $activitylink, array('class' => $textclasses)) .
                    $groupinglabel;
        }
        return $output;
    }

    /**
     * Renders html to display the module content on the course page (i.e. text of the labels)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_text(cm_info $mod, $displayoptions = array()) {
        $output = '';
        if (!$mod->uservisible &&
                (empty($mod->showavailability) || empty($mod->availableinfo))) {
            // nothing to be displayed to the user
            return $output;
        }
        $content = $mod->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                has_capability('moodle/course:viewhiddenactivities',
                        context_course::instance($mod->course));
            if ($accessiblebutdim) {
                $textclasses .= ' dimmed_text';
                if ($conditionalhidden) {
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        } else {
            $textclasses .= ' dimmed_text';
        }
        if ($mod->get_url()) {
            if ($content) {
                // If specified, display extra content after link.
                $output = html_writer::tag('div', $content, array('class' =>
                        trim('contentafterlink ' . $textclasses)));
            }
        } else {
            // No link, so display only content.
            $output = html_writer::tag('div', $accesstext . $content, array('class' => $textclasses));
        }
        return $output;
    }

    /**
     * Renders HTML to show course module availability information (for someone who isn't allowed
     * to see the activity itself, or for staff)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_availability(cm_info $mod, $displayoptions = array()) {
        global $CFG;
        if (!$mod->uservisible) {
            // this is a student who is not allowed to see the module but might be allowed
            // to see availability info (i.e. "Available from ...")
            if (!empty($mod->showavailability) && !empty($mod->availableinfo)) {
                $output = html_writer::tag('div', $mod->availableinfo, array('class' => 'availabilityinfo'));
            }
            return $output;
        }
        // this is a teacher who is allowed to see module but still should see the
        // information that module is not available to all/some students
        $modcontext = context_module::instance($mod->id);
        $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
        if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Don't add availability information if user is not editing and activity is hidden.
            if ($mod->visible || $this->page->user_is_editing()) {
                $hidinfoclass = '';
                if (!$mod->visible) {
                    $hidinfoclass = 'hide';
                }
                $ci = new condition_info($mod);
                $fullinfo = $ci->get_full_information();
                if($fullinfo) {
                    return '<div class="availabilityinfo '.$hidinfoclass.'">'.get_string($mod->showavailability
                        ? 'userrestriction_visible'
                        : 'userrestriction_hidden','condition',
                        $fullinfo).'</div>';
                }
            }
        }
        return '';
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link cm_info::get_after_link()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2a) The 'showavailability' option is not set (if that is set,
        //     we need to display the activity so we can show
        //     availability info)
        // or
        // 2b) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.
        if (!$mod->uservisible &&
            (empty($mod->showavailability) || empty($mod->availableinfo))) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }
        $output .= html_writer::start_tag('div', array('class' => $indentclasses));

        // Start the div for the activity title, excluding the edit icons.
        $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));

        // Display the link to the module (or do nothing if module has no url)
        $output .= $this->course_section_cm_name($mod, $displayoptions);

        // Module can put text after the link (e.g. forum unread)
        $output .= $mod->get_after_link();

        // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
        $output .= html_writer::end_tag('div'); // .activityinstance

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->get_url();
        if (empty($url)) {
            $output .= $contentpart;
        }

        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $output .= ' '. $this->course_section_cm_edit_actions($editactions);
            $output .= $mod->get_after_edit_icons();
        }

        $output .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }

        // show availability info (if module is not available)
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        $output .= html_writer::end_tag('div'); // $indentclasses
        return $output;
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
        global $USER;

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

                if ($modulehtml = $this->course_section_cm($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        if (!empty($moduleshtml) || $ismoving) {

            $output .= html_writer::start_tag('ul', array('class' => 'section img-text'));

            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $output .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix)),
                            array('class' => 'movehere', 'title' => $strmovefull));
                }

                $mod = $modinfo->cms[$modnumber];
                $modclasses = 'activity '. $mod->modname. ' modtype_'.$mod->modname. ' '. $mod->get_extra_classes();
                $output .= html_writer::start_tag('li', array('class' => $modclasses, 'id' => 'module-'. $mod->id));
                $output .= $modulehtml;
                $output .= html_writer::end_tag('li');
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $output .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix)),
                        array('class' => 'movehere', 'title' => $strmovefull));
            }

            $output .= html_writer::end_tag('ul'); // .section
        }

        return $output;
    }

    /**
     * Displays a custom list of courses with paging bar if necessary
     *
     * If $paginationurl is specified but $totalcount is not, the link 'View more'
     * appears under the list.
     *
     * If both $paginationurl and $totalcount are specified, and $totalcount is
     * bigger than count($courses), a paging bar is displayed above and under the
     * courses list.
     *
     * @param array $courses array of course records (or instances of course_in_list) to show on this page
     * @param bool $showcategoryname whether to add category name to the course description
     * @param string $additionalclasses additional CSS classes to add to the div.courses
     * @param moodle_url $paginationurl url to view more or url to form links to the other pages in paging bar
     * @param int $totalcount total number of courses on all pages, if omitted $paginationurl will be displayed as 'View more' link
     * @param int $page current page number (defaults to 0 referring to the first page)
     * @param int $perpage number of records per page (defaults to $CFG->coursesperpage)
     * @return string
     */
    public function courses_list($courses, $showcategoryname = false, $additionalclasses = null, $paginationurl = null, $totalcount = null, $page = 0, $perpage = null) {
        global $CFG;
        // create instance of coursecat_helper to pass display options to function rendering courses list
        $chelper = new coursecat_helper();
        if ($showcategoryname) {
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT);
        } else {
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
        }
        if ($totalcount !== null && $paginationurl !== null) {
            // add options to display pagination
            if ($perpage === null) {
                $perpage = $CFG->coursesperpage;
            }
            $chelper->set_courses_display_options(array(
                'limit' => $perpage,
                'offset' => ((int)$page) * $perpage,
                'paginationurl' => $paginationurl,
            ));
        } else if ($paginationurl !== null) {
            // add options to display 'View more' link
            $chelper->set_courses_display_options(array('viewmoreurl' => $paginationurl));
            $totalcount = count($courses) + 1; // has to be bigger than count($courses) otherwise link will not be displayed
        }
        $chelper->set_attributes(array('class' => $additionalclasses));
        $content = $this->coursecat_courses($chelper, $courses, $totalcount);
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param course_in_list|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }
        $content .= html_writer::start_tag('div', array('class' => $classes)); // .coursebox

        $content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'name'));

        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
            }
        }
        $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .info

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
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
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // display course summary
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('div', array('class' => 'summary'));
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('div'); // .summary
        }

        // display course overview files
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
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

        // display course contacts. See course_in_list::get_course_contacts()
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // .teachers
        }

        // display course category if necessary (for example in search results)
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // .coursecat
            }
        }

        return $content;
    }

    /**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        foreach ($courses as $course) {
            $coursecount ++;
            $classes = ($coursecount%2) ? 'odd' : 'even';
            if ($coursecount == 1) {
                $classes .= ' first';
            }
            if ($coursecount >= count($courses)) {
                $classes .= ' last';
            }
            $content .= $this->coursecat_coursebox($chelper, $course, $classes);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // .courses
        return $content;
    }

    /**
     * Renders the list of subcategories in a category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call get_child_categories_count() AFTER get_child_categories() to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Returns HTML to display the subcategories and courses in the given category
     *
     * This method is re-used by AJAX to expand content of not loaded category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_category_content(coursecat_helper $chelper, $coursecat, $depth) {
        $content = '';
        // Subcategories
        $content .= $this->coursecat_subcategories($chelper, $coursecat, $depth);

        // AUTO show courses: Courses will be shown expanded if this is not nested category,
        // and number of courses no bigger than $CFG->courseswithsummarieslimit.
        $showcoursesauto = $chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO;
        if ($showcoursesauto && $depth) {
            // this is definitely collapsed mode
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
        }

        // Courses
        if ($chelper->get_show_courses() > core_course_renderer::COURSECAT_SHOW_COURSES_COUNT) {
            $courses = array();
            if (!$chelper->get_courses_display_option('nodisplay')) {
                $courses = $coursecat->get_courses($chelper->get_courses_display_options());
            }
            if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $chelper->set_courses_display_option('viewmoreurl', new moodle_url($viewmoreurl, array('categoryid' => $coursecat->id)));
                }
            }
            $content .= $this->coursecat_courses($chelper, $courses, $coursecat->get_courses_count());
        }

        if ($showcoursesauto) {
            // restore the show_courses back to AUTO
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO);
        }

        return $content;
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
        // open category tag
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // do not load content
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // load category content
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
            }
        }
        $content = html_writer::start_tag('div', array('class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth));

        // category name
        $categoryname = $coursecat->get_formatted_name();
        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                array('categoryid' => $coursecat->id)),
                $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        $content .= html_writer::start_tag('div', array('class' => 'info'));
        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'name'));
        $content .= html_writer::end_tag('div'); // .info

        // add category content to the output
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .category

        // Return the course category tree HTML
        return $content;
    }

    /**
     * Returns HTML to display a tree of subcategories and courses in the given category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat top category (this category's name and description will NOT be added to the tree)
     * @return string
     */
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        $categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }

        // Generate an id and the required JS call to make this a nice widget
        $id = html_writer::random_id('course_category_tree');
        $this->page->requires->js_init_call('M.util.init_toggle_class_on_click',
                array($id, '.category.with_children.loaded > .info .name', 'collapsed', '.category.with_children.loaded'));

        // Start content generation
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div',
                array('id' => $id, 'data-showcourses' => $chelper->get_show_courses()) + $attributes);

        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        if ($coursecat->get_children_count() && $chelper->get_subcat_depth() != 1) {
            // We don't need to display "Expand all"/"Collapse all" buttons if there are no
            // subcategories or there is only one level of subcategories loaded
            // TODO if subcategories are loaded by AJAX this might still be needed!
            $content .= html_writer::start_tag('div', array('class' => 'controls'));
            $content .= html_writer::tag('div', get_string('collapseall'), array('class' => 'addtoall expandall'));
            $content .= html_writer::tag('div', get_string('expandall'), array('class' => 'removefromall collapseall'));
            $content .= html_writer::end_tag('div');
        }

        $content .= html_writer::end_tag('div'); // .course_category_tree

        return $content;
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|coursecat $category
     */
    public function course_category($category) {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $coursecat = coursecat::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';

        $this->page->set_button($this->course_search_form('', 'navbar'));
        if (!$coursecat->id) {
            if (can_edit_in_category()) {
                // add 'Manage' button instead of course search form
                $managebutton = $this->single_button(new moodle_url('/course/manage.php'),
                                get_string('managecourses'), 'get');
                $this->page->set_button($managebutton);
            }
            if (coursecat::count_all() == 1) {
                // There exists only one category in the system, do not display link to it
                $coursecat = coursecat::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        } else {
            $this->page->set_title("$site->shortname: ". $coursecat->get_formatted_name());

            // Print the category selector
            $output .= html_writer::start_tag('div', array('class' => 'categorypicker'));
            $select = new single_select(new moodle_url('/course/index.php'), 'categoryid',
                    coursecat::make_categories_list(), $coursecat->id, null, 'switchcategory');
            $select->set_label(get_string('categories').':');
            $output .= $this->render($select);
            $output .= html_writer::end_tag('div'); // .categorypicker
        }

        // Print current category description
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }

        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
                ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));

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
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

        // Display course category tree
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add course search form (if we are inside category it was already added to the navbar)
        if (!$coursecat->id) {
            $output .= $this->course_search_form();
        }

        // Add action buttons
        $output .= $this->container_start('buttons');
        $context = get_category_or_system_context($coursecat->id);
        if (has_capability('moodle/course:create', $context)) {
            // Print link to create a new course, for the 1st available category.
            if ($coursecat->id) {
                $url = new moodle_url('/course/edit.php', array('category' => $coursecat->id, 'returnto' => 'category'));
            } else {
                $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
            }
            $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
        }
        ob_start();
        if (coursecat::count_all() == 1) {
            print_course_request_buttons(context_system::instance());
        } else {
            print_course_request_buttons($context);
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->container_end();

        return $output;
    }

    /**
     * Renders html to display search result page
     *
     * @param array $searchcriteria may contain elements: search, blocklist, modulelist, tagid
     * @return string
     */
    public function search_courses($searchcriteria) {
        global $CFG;
        $content = '';
        if (!empty($searchcriteria)) {
            // print search results
            require_once($CFG->libdir. '/coursecatlib.php');

            $displayoptions = array('sort' => array('displayname' => 1));
            // take the current page and number of results per page from query
            $perpage = optional_param('perpage', 0, PARAM_RAW);
            if ($perpage !== 'all') {
                $displayoptions['limit'] = ((int)$perpage <= 0) ? $CFG->coursesperpage : (int)$perpage;
                $page = optional_param('page', 0, PARAM_INT);
                $displayoptions['offset'] = $displayoptions['limit'] * $page;
            }
            // options 'paginationurl' and 'paginationallowall' are only used in method coursecat_courses()
            $displayoptions['paginationurl'] = new moodle_url('/course/search.php', $searchcriteria);
            $displayoptions['paginationallowall'] = true; // allow adding link 'View all'

            $class = 'course-search-result';
            foreach ($searchcriteria as $key => $value) {
                if (!empty($value)) {
                    $class .= ' course-search-result-'. $key;
                }
            }
            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)->
                    set_courses_display_options($displayoptions)->
                    set_search_criteria($searchcriteria)->
                    set_attributes(array('class' => $class));

            $courses = coursecat::search_courses($searchcriteria, $chelper->get_courses_display_options());
            $totalcount = coursecat::search_courses_count($searchcriteria);
            $courseslist = $this->coursecat_courses($chelper, $courses, $totalcount);

            if (!$totalcount) {
                if (!empty($searchcriteria['search'])) {
                    $content .= $this->heading(get_string('nocoursesfound', '', $searchcriteria['search']));
                } else {
                    $content .= $this->heading(get_string('novalidcourses'));
                }
            } else {
                $content .= $this->heading(get_string('searchresults'). ": $totalcount");
                $content .= $courseslist;
            }

            if (!empty($searchcriteria['search'])) {
                // print search form only if there was a search by search string, otherwise it is confusing
                $content .= $this->box_start('generalbox mdl-align');
                $content .= $this->course_search_form($searchcriteria['search']);
                $content .= $this->box_end();
            }
        } else {
            // just print search form
            $content .= $this->box_start('generalbox mdl-align');
            $content .= $this->course_search_form();
            $content .= html_writer::tag('div', get_string("searchhelp"), array('class' => 'searchhelp'));
            $content .= $this->box_end();
        }
        return $content;
    }

    /**
     * Renders html to print list of courses tagged with particular tag
     *
     * @param int $tagid id of the tag
     * @return string empty string if no courses are marked with this tag or rendered list of courses
     */
    public function tagged_courses($tagid) {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $displayoptions = array('limit' => $CFG->coursesperpage);
        $displayoptions['viewmoreurl'] = new moodle_url('/course/search.php',
                array('tagid' => $tagid, 'page' => 1, 'perpage' => $CFG->coursesperpage));
        $displayoptions['viewmoretext'] = new lang_string('findmorecourses');
        $chelper = new coursecat_helper();
        $searchcriteria = array('tagid' => $tagid);
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)->
                set_search_criteria(array('tagid' => $tagid))->
                set_courses_display_options($displayoptions)->
                set_attributes(array('class' => 'course-search-result course-search-result-tagid'));
                // (we set the same css class as in search results by tagid)
        $courses = coursecat::search_courses($searchcriteria, $chelper->get_courses_display_options());
        $totalcount = coursecat::search_courses_count($searchcriteria);
        $content = $this->coursecat_courses($chelper, $courses, $totalcount);
        if ($totalcount) {
            require_once $CFG->dirroot.'/tag/lib.php';
            $heading = get_string('courses') . ' ' . get_string('taggedwith', 'tag', tag_get_name($tagid)) .': '. $totalcount;
            return $this->heading($heading, 3). $content;
        }
        return '';
    }

    /**
     * Returns HTML to display one remote course
     *
     * @param stdClass $course remote course information, contains properties:
           id, remoteid, shortname, fullname, hostid, summary, summaryformat, cat_name, hostname
     * @return string
     */
    protected function frontpage_remote_course(stdClass $course) {
        $url = new moodle_url('/auth/mnet/jump.php', array(
            'hostid' => $course->hostid,
            'wantsurl' => '/course/view.php?id='. $course->remoteid
        ));

        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'coursebox remotecoursebox clearfix'));
        $output .= html_writer::start_tag('div', array('class' => 'info'));
        $output .= html_writer::start_tag('h3', array('class' => 'name'));
        $output .= html_writer::link($url, format_string($course->fullname), array('title' => get_string('entercourse')));
        $output .= html_writer::end_tag('h3'); // .name
        $output .= html_writer::tag('div', '', array('class' => 'moreinfo'));
        $output .= html_writer::end_tag('div'); // .info
        $output .= html_writer::start_tag('div', array('class' => 'content'));
        $output .= html_writer::start_tag('div', array('class' => 'summary'));
        $options = new stdClass();
        $options->noclean = true;
        $options->para = false;
        $options->overflowdiv = true;
        $output .= format_text($course->summary, $course->summaryformat, $options);
        $output .= html_writer::end_tag('div'); // .summary
        $addinfo = format_string($course->hostname) . ' : '
            . format_string($course->cat_name) . ' : '
            . format_string($course->shortname);
        $output .= html_writer::tag('div', $addinfo, array('class' => 'remotecourseinfo'));
        $output .= html_writer::end_tag('div'); // .content
        $output .= html_writer::end_tag('div'); // .coursebox
        return $output;
    }

    /**
     * Returns HTML to display one remote host
     *
     * @param array $host host information, contains properties: name, url, count
     * @return string
     */
    protected function frontpage_remote_host($host) {
        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'coursebox remotehost clearfix'));
        $output .= html_writer::start_tag('div', array('class' => 'info'));
        $output .= html_writer::start_tag('h3', array('class' => 'name'));
        $output .= html_writer::link($host['url'], s($host['name']), array('title' => s($host['name'])));
        $output .= html_writer::end_tag('h3'); // .name
        $output .= html_writer::tag('div', '', array('class' => 'moreinfo'));
        $output .= html_writer::end_tag('div'); // .info
        $output .= html_writer::start_tag('div', array('class' => 'content'));
        $output .= html_writer::start_tag('div', array('class' => 'summary'));
        $output .= $host['count'] . ' ' . get_string('courses');
        $output .= html_writer::end_tag('div'); // .content
        $output .= html_writer::end_tag('div'); // .coursebox
        return $output;
    }

    /**
     * Returns HTML to print list of courses user is enrolled to for the frontpage
     *
     * Also lists remote courses or remote hosts if MNET authorisation is used
     *
     * @return string
     */
    public function frontpage_my_courses() {
        global $USER, $CFG, $DB;

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        $output = '';
        if (!empty($CFG->navsortmycoursessort)) {
            // sort courses the same as in navigation menu
            $sortorder = 'visible DESC,'. $CFG->navsortmycoursessort.' ASC';
        } else {
            $sortorder = 'visible DESC,sortorder ASC';
        }
        $courses  = enrol_get_my_courses('summary, summaryformat', $sortorder);
        $rhosts   = array();
        $rcourses = array();
        if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
            $rcourses = get_my_remotecourses($USER->id);
            $rhosts   = get_my_remotehosts();
        }

        if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

            $chelper = new coursecat_helper();
            if (count($courses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to 'My courses'.
                $totalcount = count($courses);
                $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/'),
                        'viewmoretext' => new lang_string('mycourses')
                    ));
            } else {
                // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php'),
                        'viewmoretext' => new lang_string('fulllistofcourses')
                    ));
                $totalcount = $DB->count_records('course') - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                    set_attributes(array('class' => 'frontpage-course-list-enrolled'));
            $output .= $this->coursecat_courses($chelper, $courses, $totalcount);

            // MNET
            if (!empty($rcourses)) {
                // at the IDP, we know of all the remote courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag('div'); // .courses
            } elseif (!empty($rhosts)) {
                // non-IDP, we know of all the remote servers, but not courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag('div'); // .courses
            }
        }
        return $output;
    }

    /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_courses_display_options(array(
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = coursecat::get(0)->get_courses($chelper->get_courses_display_options());
        $totalcount = coursecat::get(0)->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            $output = $this->container_start('buttons');
            $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
            $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
            $output .= $this->container_end('buttons');
            return $output;
        }
        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }

    /**
     * Returns HTML to print tree with course categories and courses for the frontpage
     *
     * @return string
     */
    public function frontpage_combo_list() {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
            set_categories_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'categories', 'page' => 1))
            ))->
            set_courses_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'courses', 'page' => 1))
            ))->
            set_attributes(array('class' => 'frontpage-category-combo'));
        return $this->coursecat_tree($chelper, coursecat::get(0));
    }

    /**
     * Returns HTML to print tree of course categories (with number of courses) for the frontpage
     *
     * @return string
     */
    public function frontpage_categories_list() {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
                set_show_courses(self::COURSECAT_SHOW_COURSES_COUNT)->
                set_categories_display_options(array(
                    'limit' => $CFG->coursesperpage,
                    'viewmoreurl' => new moodle_url('/course/index.php',
                            array('browse' => 'categories', 'page' => 1))
                ))->
                set_attributes(array('class' => 'frontpage-category-names'));
        return $this->coursecat_tree($chelper, coursecat::get(0));
    }
}

/**
 * Class storing display options and functions to help display course category and/or courses lists
 *
 * This is a wrapper for coursecat objects that also stores display options
 * and functions to retrieve sorted and paginated lists of categories/courses.
 *
 * If theme overrides methods in core_course_renderers that access this class
 * it may as well not use this class at all or extend it.
 *
 * @package   core
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_helper {
    /** @var string [none, collapsed, expanded] how (if) display courses list */
    protected $showcourses = 10; /* core_course_renderer::COURSECAT_SHOW_COURSES_COLLAPSED */
    /** @var int depth to expand subcategories in the tree (deeper subcategories will be loaded by AJAX or proceed to category page by clicking on category name) */
    protected $subcatdepth = 1;
    /** @var array options to display courses list */
    protected $coursesdisplayoptions = array();
    /** @var array options to display subcategories list */
    protected $categoriesdisplayoptions = array();
    /** @var array additional HTML attributes */
    protected $attributes = array();
    /** @var array search criteria if the list is a search result */
    protected $searchcriteria = null;

    /**
     * Sets how (if) to show the courses - none, collapsed, expanded, etc.
     *
     * @param int $showcourses SHOW_COURSES_NONE, SHOW_COURSES_COLLAPSED, SHOW_COURSES_EXPANDED, etc.
     * @return coursecat_helper
     */
    public function set_show_courses($showcourses) {
        $this->showcourses = $showcourses;
        // Automatically set the options to preload summary and coursecontacts for coursecat::get_courses() and coursecat::search_courses()
        $this->coursesdisplayoptions['summary'] = $showcourses >= core_course_renderer::COURSECAT_SHOW_COURSES_AUTO;
        $this->coursesdisplayoptions['coursecontacts'] = $showcourses >= core_course_renderer::COURSECAT_SHOW_COURSES_EXPANDED;
        return $this;
    }

    /**
     * Returns how (if) to show the courses - none, collapsed, expanded, etc.
     *
     * @return int - COURSECAT_SHOW_COURSES_NONE, COURSECAT_SHOW_COURSES_COLLAPSED, COURSECAT_SHOW_COURSES_EXPANDED, etc.
     */
    public function get_show_courses() {
        return $this->showcourses;
    }

    /**
     * Sets the maximum depth to expand subcategories in the tree
     *
     * deeper subcategories may be loaded by AJAX or proceed to category page by clicking on category name
     *
     * @param int $subcatdepth
     * @return coursecat_helper
     */
    public function set_subcat_depth($subcatdepth) {
        $this->subcatdepth = $subcatdepth;
        return $this;
    }

    /**
     * Returns the maximum depth to expand subcategories in the tree
     *
     * deeper subcategories may be loaded by AJAX or proceed to category page by clicking on category name
     *
     * @return int
     */
    public function get_subcat_depth() {
        return $this->subcatdepth;
    }

    /**
     * Sets options to display list of courses
     *
     * Options are later submitted as argument to coursecat::get_courses() and/or coursecat::search_courses()
     *
     * Options that coursecat::get_courses() accept:
     *    - recursive - return courses from subcategories as well. Use with care,
     *      this may be a huge list!
     *    - summary - preloads fields 'summary' and 'summaryformat'
     *    - coursecontacts - preloads course contacts
     *    - isenrolled - preloads indication whether this user is enrolled in the course
     *    - sort - list of fields to sort. Example
     *             array('idnumber' => 1, 'shortname' => 1, 'id' => -1)
     *             will sort by idnumber asc, shortname asc and id desc.
     *             Default: array('sortorder' => 1)
     *             Only cached fields may be used for sorting!
     *    - offset
     *    - limit - maximum number of children to return, 0 or null for no limit
     *
     * Options summary and coursecontacts are filled automatically in the set_show_courses()
     *
     * Also renderer can set here any additional options it wants to pass between renderer functions.
     *
     * @param array $options
     * @return coursecat_helper
     */
    public function set_courses_display_options($options) {
        $this->coursesdisplayoptions = $options;
        $this->set_show_courses($this->showcourses); // this will calculate special display options
        return $this;
    }

    /**
     * Sets one option to display list of courses
     *
     * @see coursecat_helper::set_courses_display_options()
     *
     * @param string $key
     * @param mixed $value
     * @return coursecat_helper
     */
    public function set_courses_display_option($key, $value) {
        $this->coursesdisplayoptions[$key] = $value;
        return $this;
    }

    /**
     * Return the specified option to display list of courses
     *
     * @param string $optionname option name
     * @param mixed $defaultvalue default value for option if it is not specified
     * @return mixed
     */
    public function get_courses_display_option($optionname, $defaultvalue = null) {
        if (array_key_exists($optionname, $this->coursesdisplayoptions)) {
            return $this->coursesdisplayoptions[$optionname];
        } else {
            return $defaultvalue;
        }
    }

    /**
     * Returns all options to display the courses
     *
     * This array is usually passed to {@link coursecat::get_courses()} or
     * {@link coursecat::search_courses()}
     *
     * @return array
     */
    public function get_courses_display_options() {
        return $this->coursesdisplayoptions;
    }

    /**
     * Sets options to display list of subcategories
     *
     * Options 'sort', 'offset' and 'limit' are passed to coursecat::get_children().
     * Any other options may be used by renderer functions
     *
     * @param array $options
     * @return coursecat_helper
     */
    public function set_categories_display_options($options) {
        $this->categoriesdisplayoptions = $options;
        return $this;
    }

    /**
     * Return the specified option to display list of subcategories
     *
     * @param string $optionname option name
     * @param mixed $defaultvalue default value for option if it is not specified
     * @return mixed
     */
    public function get_categories_display_option($optionname, $defaultvalue = null) {
        if (array_key_exists($optionname, $this->categoriesdisplayoptions)) {
            return $this->categoriesdisplayoptions[$optionname];
        } else {
            return $defaultvalue;
        }
    }

    /**
     * Returns all options to display list of subcategories
     *
     * This array is usually passed to {@link coursecat::get_children()}
     *
     * @return array
     */
    public function get_categories_display_options() {
        return $this->categoriesdisplayoptions;
    }

    /**
     * Sets additional general options to pass between renderer functions, usually HTML attributes
     *
     * @param array $attributes
     * @return coursecat_helper
     */
    public function set_attributes($attributes) {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Return all attributes and erases them so they are not applied again
     *
     * @param string $classname adds additional class name to the beginning of $attributes['class']
     * @return array
     */
    public function get_and_erase_attributes($classname) {
        $attributes = $this->attributes;
        $this->attributes = array();
        if (empty($attributes['class'])) {
            $attributes['class'] = '';
        }
        $attributes['class'] = $classname . ' '. $attributes['class'];
        return $attributes;
    }

    /**
     * Sets the search criteria if the course is a search result
     *
     * Search string will be used to highlight terms in course name and description
     *
     * @param array $searchcriteria
     * @return coursecat_helper
     */
    public function set_search_criteria($searchcriteria) {
        $this->searchcriteria = $searchcriteria;
        return $this;
    }

    /**
     * Returns formatted and filtered description of the given category
     *
     * @param coursecat $coursecat category
     * @param stdClass|array $options format options, by default [noclean,overflowdiv],
     *     if context is not specified it will be added automatically
     * @return string|null
     */
    public function get_category_formatted_description($coursecat, $options = null) {
        if ($coursecat->id && !empty($coursecat->description)) {
            if (!isset($coursecat->descriptionformat)) {
                $descriptionformat = FORMAT_MOODLE;
            } else {
                $descriptionformat = $coursecat->descriptionformat;
            }
            if ($options === null) {
                $options = array('noclean' => true, 'overflowdiv' => true);
            } else {
                $options = (array)$options;
            }
            $context = context_coursecat::instance($coursecat->id);
            if (!isset($options['context'])) {
                $options['context'] = $context;
            }
            $text = file_rewrite_pluginfile_urls($coursecat->description,
                    'pluginfile.php', $context->id, 'coursecat', 'description', null);
            return format_text($text, $descriptionformat, $options);
        }
        return null;
    }

    /**
     * Returns given course's summary with proper embedded files urls and formatted
     *
     * @param course_in_list $course
     * @param array|stdClass $options additional formatting options
     * @return string
     */
    public function get_course_formatted_summary($course, $options = array()) {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        if (!$course->has_summary()) {
            return '';
        }
        $options = (array)$options;
        $context = context_course::instance($course->id);
        if (!isset($options['context'])) {
            // TODO see MDL-38521
            // option 1 (current), page context - no code required
            // option 2, system context
            // $options['context'] = context_system::instance();
            // option 3, course context:
            // $options['context'] = $context;
            // option 4, course category context:
            // $options['context'] = $context->get_parent_context();
        }
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat, $options, $course->id);
        if (!empty($this->searchcriteria['search'])) {
            $summary = highlight($this->searchcriteria['search'], $summary);
        }
        return $summary;
    }

    /**
     * Returns course name as it is configured to appear in courses lists formatted to course context
     *
     * @param course_in_list $course
     * @param array|stdClass $options additional formatting options
     * @return string
     */
    public function get_course_formatted_name($course, $options = array()) {
        $options = (array)$options;
        if (!isset($options['context'])) {
            $options['context'] = context_course::instance($course->id);
        }
        $name = format_string(get_course_display_name_for_list($course), true, $options);
        if (!empty($this->searchcriteria['search'])) {
            $name = highlight($this->searchcriteria['search'], $name);
        }
        return $name;
    }
}
