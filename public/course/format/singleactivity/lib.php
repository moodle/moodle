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

use core_courseformat\sectiondelegate;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the singleactivity course format
 *
 * @package    format_singleactivity
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_singleactivity extends core_courseformat\base implements core_courseformat\main_activity_interface {
    /** @var cm_info the current activity. Use get_main_activity() to retrieve it. */
    private $activity = false;

    /** @var int The category ID guessed from the form data. */
    private $categoryid = false;

    /**
     * The URL to use for the specified course
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) ignored by this format
     *     'sr' (int) ignored by this format
     * @return moodle_url
     */
    public function get_view_url($section, $options = []) {
        return new moodle_url('/course/view.php', ['id' => $this->courseid]);
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        // SingleActivity course format does not extend navigation, it uses site_main_menu block instead.
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return [
            BLOCK_POS_LEFT => ['site_main_menu'],
            BLOCK_POS_RIGHT => [],
        ];
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
            core_collator::asort($availabletypes);
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
                    if (!$this->categoryid) {
                        unset($availabletypes[$activity]);
                    } else {
                        // We do not have a course yet, so we guess if the user will have the capability to add the activity after
                        // creating the course.
                        $categorycontext = \context_coursecat::instance($this->categoryid);
                        if (!guess_if_creator_will_have_course_capability($capability, $categorycontext)) {
                            unset($availabletypes[$activity]);
                        }
                    }
                }
            }
        }

        if ($courseformatoptions === false) {
            $config = get_config('format_singleactivity');
            $courseformatoptions = [
                'activitytype' => [
                    'default' => $config->activitytype,
                    'type' => PARAM_TEXT,
                ],
            ];

            if (!empty($availabletypes) && !isset($availabletypes[$config->activitytype])) {
                $courseformatoptions['activitytype']['default'] = array_keys($availabletypes)[0];
            }
        }

        if ($foreditform && !isset($courseformatoptions['activitytype']['label'])) {
            $courseformatoptionsedit = [
                'activitytype' => [
                    'label' => new lang_string('activitytype', 'format_singleactivity'),
                    'help' => 'activitytype',
                    'help_component' => 'format_singleactivity',
                    'element_type' => 'select',
                    'element_attributes' => [$availabletypes],
                ],
            ];
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
        if (!$this->course && $submitvalues = $mform->getSubmitValues()) {
            $this->categoryid = $submitvalues['category'];
        }

        $elements = parent::create_edit_form_elements($mform, $forsection);

        return $elements;
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

    #[\Override]
    public function get_main_activity(): ?\cm_info {
        if ($this->activity === false) {
            $modinfo = get_fast_modinfo($this->courseid);

            // Find the current activity (first activity with the specified type in all course activities).
            $activitytype = $this->get_activitytype();
            if (!empty($activitytype) && !empty($modinfo->sections)) {
                // Get the first activity of the specified type, but only if it is in the 0-section.
                if (isset($modinfo->sections[0])) {
                    $cmlist = $modinfo->sections[0];
                    foreach ($cmlist as $cmid) {
                        if ($modinfo->cms[$cmid]->modname === $activitytype) {
                            $this->activity = $modinfo->cms[$cmid];
                            break;
                        }
                    }
                }
            }

            // Make sure the current activity is visible.
            if ($this->activity && !$this->activity->visible) {
                set_coursemodule_visible($this->activity->id, 1);
            }
        }

        if ($this->activity !== false) {
            return $this->activity;
        }

        // No activity found, return null.
        return null;
    }

    /**
     * Get the activities supported by the format.
     *
     * Here we ignore the modules that do not have a page of their own or need sections,
     * like the label or subsection.
     *
     * @return array [$module => $name of the module].
     */
    public static function get_supported_activities() {
        $availabletypes = get_module_types_names();
        foreach ($availabletypes as $module => $name) {
            if (
                plugin_supports('mod', $module, FEATURE_NO_VIEW_LINK, false)
                || sectiondelegate::has_delegate_class('mod_' . $module)
                || !course_modinfo::is_mod_type_visible_on_course($module)
            ) {
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
            return false;
        }
        return true;
    }

    /**
     * Checks if the activity type has multiple items in the activity chooser.
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
            $cm = $this->get_main_activity();
            if (!$this->get_activitytype()) {
                if (has_capability('moodle/course:update', context_course::instance($this->courseid))) {
                    // Teacher is redirected to edit course page.
                    $url = new moodle_url('/course/edit.php', ['id' => $this->courseid]);
                    redirect($url, get_string('erroractivitytype', 'format_singleactivity'));
                } else {
                    // Student sees an empty course page.
                    return;
                }
            }
            if ($cm == null) {
                if ($this->can_add_activity()) {
                    // This is a user who has capability to create an activity.
                    if ($this->activity_has_subtypes()) {
                        // Activity has multiple items in the activity chooser, it can not be added automatically.
                        if (optional_param('addactivity', 0, PARAM_INT)) {
                            return;
                        } else {
                            $url = new moodle_url('/course/view.php', ['id' => $this->courseid, 'addactivity' => 1]);
                            redirect($url);
                        }
                    }
                    // Redirect to the add activity form.
                    $url = new moodle_url(
                        '/course/mod.php',
                        [
                            'id' => $this->courseid,
                            'section' => 0,
                            'sesskey' => sesskey(),
                            'add' => $this->get_activitytype(),
                        ],
                    );
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
        if ($PAGE == $page && ($cm = $this->get_main_activity()) &&
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

    #[\Override]
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

    #[\Override]
    public function supports_ajax() {
        // All home page is rendered in the backend, we only need an ajax editor components in edit mode.
        // This will also prevent redirecting to the login page when a guest tries to access the site,
        // and will make the home page loading faster.
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = $this->show_editor();
        return $ajaxsupport;
    }

    #[\Override]
    public function supports_components() {
        return true;
    }

    #[\Override]
    public function uses_sections() {
        return false;
    }

    #[\Override]
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ($section->is_delegated()) {
            return $section->name;
        }
        // The single activity only uses one section inside the additional activities block.
        return get_string('pluginname', 'format_singleactivity');
    }

    #[\Override]
    public function get_sectionnum(): int {
        // SingleActivity format uses only section 0.
        return 0;
    }

    #[\Override]
    public function allow_stealth_module_visibility($cm, $section) {
        return true;
    }

    /**
     * Returns if a specific section is visible to the current user.
     *
     * The single activity format does only have the section zero
     * and subsections (delegated sections).
     *
     * @param section_info $section the section modinfo
     * @return bool;
     */
    #[\Override]
    public function is_section_visible(section_info $section): bool {
        $visible = parent::is_section_visible($section);
        // Social format does only use section 0 as a normal section.
        // Any other included section should be a delegated one (subsections).
        return $visible && ($section->sectionnum == 0 || $section->is_delegated());
    }
}
