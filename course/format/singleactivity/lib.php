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
 * This file contains main class for the course format singleactivity
 *
 * @package    format_singleactivity
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the singleactivity course format
 *
 * @package    format_singleactivity
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_singleactivity extends format_base {
    /** @var cm_info the current activity. Use get_activity() to retrieve it. */
    private $activity = false;

    /** @var int The category ID guessed from the form data. */
    private $categoryid = false;

    /**
     * The URL to use for the specified course
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $sectionnum = $section;
        if (is_object($sectionnum)) {
            $sectionnum = $section->section;
        }
        if ($sectionnum == 1) {
            return new moodle_url('/course/view.php', array('id' => $this->courseid, 'section' => 1));
        }
        if (!empty($options['navigation']) && $section !== null) {
            return null;
        }
        return new moodle_url('/course/view.php', array('id' => $this->courseid));
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        // Display orphaned activities for the users who can see them.
        $context = context_course::instance($this->courseid);
        if (has_capability('moodle/course:viewhiddensections', $context)) {
            $modinfo = get_fast_modinfo($this->courseid);
            if (!empty($modinfo->sections[1])) {
                $section1 = $modinfo->get_section_info(1);
                // Show orphaned activities.
                $orphanednode = $node->add(get_string('orphaned', 'format_singleactivity'),
                        $this->get_view_url(1), navigation_node::TYPE_SECTION, null, $section1->id);
                $orphanednode->nodetype = navigation_node::NODETYPE_BRANCH;
                $orphanednode->add_class('orphaned');
                foreach ($modinfo->sections[1] as $cmid) {
                    if (has_capability('moodle/course:viewhiddenactivities', context_module::instance($cmid))) {
                        $this->navigation_add_activity($orphanednode, $modinfo->cms[$cmid]);
                    }
                }
            }
        }
    }

    /**
     * Adds a course module to the navigation node
     *
     * This is basically copied from function global_navigation::load_section_activities()
     * because it is not accessible from outside.
     *
     * @param navigation_node $node
     * @param cm_info $cm
     * @return null|navigation_node
     */
    protected function navigation_add_activity(navigation_node $node, $cm) {
        if (!$cm->uservisible) {
            return null;
        }
        $action = $cm->url;
        if (!$action) {
            // Do not add to navigation activity without url (i.e. labels).
            return null;
        }
        $activityname = format_string($cm->name, true, array('context' => context_module::instance($cm->id)));
        if ($cm->icon) {
            $icon = new pix_icon($cm->icon, $cm->modfullname, $cm->iconcomponent);
        } else {
            $icon = new pix_icon('icon', $cm->modfullname, $cm->modname);
        }
        $activitynode = $node->add($activityname, $action, navigation_node::TYPE_ACTIVITY, null, $cm->id, $icon);
        if (global_navigation::module_extends_navigation($cm->modname)) {
            $activitynode->nodetype = navigation_node::NODETYPE_BRANCH;
        } else {
            $activitynode->nodetype = navigation_node::NODETYPE_LEAF;
        }
        return $activitynode;
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        // No blocks for this format because course view page is not displayed anyway.
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
        );
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Singleactivity course format uses one option 'activitytype'
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;

        $fetchtypes = $courseformatoptions === false;
        $fetchtypes = $fetchtypes || ($foreditform && !isset($courseformatoptions['activitytype']['label']));

        if ($fetchtypes) {
            $availabletypes = $this->get_supported_activities();
            if ($this->courseid) {
                // The course exists. Test against the course.
                $testcontext = context_course::instance($this->courseid);
            } else if ($this->categoryid) {
                // The course does not exist yet, but we have a category ID that we can test against.
                $testcontext = context_coursecat::instance($this->categoryid);
            } else {
                // The course does not exist, and we somehow do not have a category. Test capabilities against the system context.
                $testcontext = context_system::instance();
            }
            foreach (array_keys($availabletypes) as $activity) {
                $capability = "mod/{$activity}:addinstance";
                if (!has_capability($capability, $testcontext)) {
                    unset($availabletypes[$activity]);
                }
            }
        }

        if ($courseformatoptions === false) {
            $config = get_config('format_singleactivity');
            $courseformatoptions = array(
                'activitytype' => array(
                    'default' => $config->activitytype,
                    'type' => PARAM_TEXT,
                ),
            );

            if (!empty($availabletypes) && !isset($availabletypes[$config->activitytype])) {
                $courseformatoptions['activitytype']['default'] = array_keys($availabletypes)[0];
            }
        }

        if ($foreditform && !isset($courseformatoptions['activitytype']['label'])) {
            $courseformatoptionsedit = array(
                'activitytype' => array(
                    'label' => new lang_string('activitytype', 'format_singleactivity'),
                    'help' => 'activitytype',
                    'help_component' => 'format_singleactivity',
                    'element_type' => 'select',
                    'element_attributes' => array($availabletypes),
                ),
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
     *
     * Format singleactivity adds a warning when format of the course is about to be changed.
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $PAGE;

        if (!$this->course && $submitvalues = $mform->getSubmitValues()) {
            $this->categoryid = $submitvalues['category'];
        }

        $elements = parent::create_edit_form_elements($mform, $forsection);
        if (!$forsection && ($course = $PAGE->course) && !empty($course->format) &&
                $course->format !== 'site' && $course->format !== 'singleactivity') {
            // This is the existing course in other format, display a warning.
            $element = $mform->addElement('static', '', '',
                    html_writer::tag('span', get_string('warningchangeformat', 'format_singleactivity'),
                            array('class' => 'error')));
            array_unshift($elements, $element);
        }
        return $elements;
    }

    /**
     * Make sure that current active activity is in section 0
     *
     * All other activities are moved to section 1 that will be displayed as 'Orphaned'.
     * It may be needed after the course format was changed or activitytype in
     * course settings has been changed.
     *
     * @return null|cm_info current activity
     */
    public function reorder_activities() {
        course_create_sections_if_missing($this->courseid, array(0, 1));
        foreach ($this->get_sections() as $sectionnum => $section) {
            if (($sectionnum && $section->visible) ||
                    (!$sectionnum && !$section->visible)) {
                // Make sure that 0 section is visible and all others are hidden.
                set_section_visible($this->courseid, $sectionnum, $sectionnum == 0);
            }
        }
        $modinfo = get_fast_modinfo($this->courseid);

        // Find the current activity (first activity with the specified type in all course activities).
        $activitytype = $this->get_activitytype();
        $activity = null;
        if (!empty($activitytype)) {
            foreach ($modinfo->sections as $sectionnum => $cmlist) {
                foreach ($cmlist as $cmid) {
                    if ($modinfo->cms[$cmid]->modname === $activitytype) {
                        $activity = $modinfo->cms[$cmid];
                        break 2;
                    }
                }
            }
        }

        // Make sure the current activity is in the 0-section.
        $changed = false;
        if ($activity && $activity->sectionnum != 0) {
            moveto_module($activity, $modinfo->get_section_info(0));
            $changed = true;
        }
        if ($activity && !$activity->visible) {
            set_coursemodule_visible($activity->id, 1);
            $changed = true;
        }
        if ($changed) {
            // Cache was reset so get modinfo again.
            $modinfo = get_fast_modinfo($this->courseid);
        }

        // Move all other activities into section 1 (the order must be kept).
        $hasvisibleactivities = false;
        $firstorphanedcm = null;
        foreach ($modinfo->sections as $sectionnum => $cmlist) {
            if ($sectionnum && !empty($cmlist) && $firstorphanedcm === null) {
                $firstorphanedcm = reset($cmlist);
            }
            foreach ($cmlist as $cmid) {
                if ($sectionnum > 1) {
                    moveto_module($modinfo->get_cm($cmid), $modinfo->get_section_info(1));
                } else if (!$hasvisibleactivities && $sectionnum == 1 && $modinfo->get_cm($cmid)->visible) {
                    $hasvisibleactivities = true;
                }
            }
        }
        if (!empty($modinfo->sections[0])) {
            foreach ($modinfo->sections[0] as $cmid) {
                if (!$activity || $cmid != $activity->id) {
                    moveto_module($modinfo->get_cm($cmid), $modinfo->get_section_info(1), $firstorphanedcm);
                }
            }
        }
        if ($hasvisibleactivities) {
            set_section_visible($this->courseid, 1, false);
        }
        return $activity;
    }

    /**
     * Returns the name of activity type used for this course
     *
     * @return string|null
     */
    protected function get_activitytype() {
        $options = $this->get_format_options();
        $availabletypes = $this->get_supported_activities();
        if (!empty($options['activitytype']) &&
                array_key_exists($options['activitytype'], $availabletypes)) {
            return $options['activitytype'];
        } else {
            return null;
        }
    }

    /**
     * Returns the current activity if exists
     *
     * @return null|cm_info
     */
    protected function get_activity() {
        if ($this->activity === false) {
            $this->activity = $this->reorder_activities();
        }
        return $this->activity;
    }

    /**
     * Get the activities supported by the format.
     *
     * Here we ignore the modules that do not have a page of their own, like the label.
     *
     * @return array array($module => $name of the module).
     */
    public static function get_supported_activities() {
        $availabletypes = get_module_types_names();
        foreach ($availabletypes as $module => $name) {
            if (plugin_supports('mod', $module, FEATURE_NO_VIEW_LINK, false)) {
                unset($availabletypes[$module]);
            }
        }
        return $availabletypes;
    }

    /**
     * Checks if the current user can add the activity of the specified type to this course.
     *
     * @return bool
     */
    protected function can_add_activity() {
        global $CFG;
        if (!($modname = $this->get_activitytype())) {
            return false;
        }
        if (!has_capability('moodle/course:manageactivities', context_course::instance($this->courseid))) {
            return false;
        }
        if (!course_allowed_module($this->get_course(), $modname)) {
            return false;
        }
        $libfile = "$CFG->dirroot/mod/$modname/lib.php";
        if (!file_exists($libfile)) {
            return null;
        }
        return true;
    }

    /**
     * Checks if the activity type has multiple items in the activity chooser.
     * This may happen as a result of defining callback modulename_get_shortcuts().
     *
     * @return bool|null (null if the check is not possible)
     */
    public function activity_has_subtypes() {
        global $USER;
        if (!($modname = $this->get_activitytype())) {
            return null;
        }
        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();
        $metadata = $contentitemservice->get_content_items_for_user_in_course($USER, $this->get_course());

        // If there are multiple items originating from this mod_xxx component, then it's deemed to have subtypes.
        // If there is only 1 item, but it's not a reference to the core content item for the module, then it's also deemed to
        // have subtypes.
        $count = 0;
        foreach ($metadata as $key => $moduledata) {
            if ('mod_'.$modname === $moduledata->componentname) {
                $count ++;
            }
        }
        if ($count > 1) {
            return true;
        } else {
            // Get the single item.
            $itemmetadata = $metadata[array_search('mod_' . $modname, array_column($metadata, 'componentname'))];
            $urlbase = new \moodle_url('/course/mod.php', ['id' => $this->get_course()->id]);
            $referenceurl = new \moodle_url($urlbase, ['add' => $modname]);
            if ($referenceurl->out(false) != $itemmetadata->link) {
                return true;
            }
        }
        return false;
    }

    /**
     * Allows course format to execute code on moodle_page::set_course()
     *
     * This function is executed before the output starts.
     *
     * If everything is configured correctly, user is redirected from the
     * default course view page to the activity view page.
     *
     * "Section 1" is the administrative page to manage orphaned activities
     *
     * If user is on course view page and there is no module added to the course
     * and the user has 'moodle/course:manageactivities' capability, redirect to create module
     * form.
     *
     * @param moodle_page $page instance of page calling set_course
     */
    public function page_set_course(moodle_page $page) {
        global $PAGE;
        $page->add_body_class('format-'. $this->get_format());
        if ($PAGE == $page && $page->has_set_url() &&
                $page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
            $edit = optional_param('edit', -1, PARAM_BOOL);
            if (($edit == 0 || $edit == 1) && confirm_sesskey()) {
                // This is a request to turn editing mode on or off, do not redirect here, /course/view.php will do redirection.
                return;
            }
            $cm = $this->get_activity();
            $cursection = optional_param('section', null, PARAM_INT);
            if (!empty($cursection) && has_capability('moodle/course:viewhiddensections',
                    context_course::instance($this->courseid))) {
                // Display orphaned activities (course view page, section 1).
                return;
            }
            if (!$this->get_activitytype()) {
                if (has_capability('moodle/course:update', context_course::instance($this->courseid))) {
                    // Teacher is redirected to edit course page.
                    $url = new moodle_url('/course/edit.php', array('id' => $this->courseid));
                    redirect($url, get_string('erroractivitytype', 'format_singleactivity'));
                } else {
                    // Student sees an empty course page.
                    return;
                }
            }
            if ($cm === null) {
                if ($this->can_add_activity()) {
                    // This is a user who has capability to create an activity.
                    if ($this->activity_has_subtypes()) {
                        // Activity has multiple items in the activity chooser, it can not be added automatically.
                        if (optional_param('addactivity', 0, PARAM_INT)) {
                            return;
                        } else {
                            $url = new moodle_url('/course/view.php', array('id' => $this->courseid, 'addactivity' => 1));
                            redirect($url);
                        }
                    }
                    // Redirect to the add activity form.
                    $url = new moodle_url('/course/mod.php', array('id' => $this->courseid,
                        'section' => 0, 'sesskey' => sesskey(), 'add' => $this->get_activitytype()));
                    redirect($url);
                } else {
                    // Student views an empty course page.
                    return;
                }
            } else if (!$cm->uservisible || !$cm->url) {
                // Activity is set but not visible to current user or does not have url.
                // Display course page (either empty or with availability restriction info).
                return;
            } else {
                // Everything is set up and accessible, redirect to the activity page!
                redirect($cm->url);
            }
        }
    }

    /**
     * Allows course format to execute code on moodle_page::set_cm()
     *
     * If we are inside the main module for this course, remove extra node level
     * from navigation: substitute course node with activity node, move all children
     *
     * @param moodle_page $page instance of page calling set_cm
     */
    public function page_set_cm(moodle_page $page) {
        global $PAGE;
        parent::page_set_cm($page);
        if ($PAGE == $page && ($cm = $this->get_activity()) &&
                $cm->uservisible &&
                ($cm->id === $page->cm->id) &&
                ($activitynode = $page->navigation->find($cm->id, navigation_node::TYPE_ACTIVITY)) &&
                ($node = $page->navigation->find($page->course->id, navigation_node::TYPE_COURSE))) {
            // Substitute course node with activity node, move all children.
            $node->action = $activitynode->action;
            $node->type = $activitynode->type;
            $node->id = $activitynode->id;
            $node->key = $activitynode->key;
            $node->isactive = $node->isactive || $activitynode->isactive;
            $node->icon = null;
            if ($activitynode->children->count()) {
                foreach ($activitynode->children as &$child) {
                    $child->remove();
                    $node->add_node($child);
                }
            } else {
                $node->search_for_active_node();
            }
            $activitynode->remove();
        }
    }

    /**
     * Returns true if the course has a front page.
     *
     * @return boolean false
     */
    public function has_view_page() {
        return false;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of configuration settings
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        // Return everything (nothing to hide).
        return $this->get_format_options();
    }
}
