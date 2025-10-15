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

namespace core\navigation;

use admin_category;
use admin_externalpage;
use admin_settingpage;
use core\component;
use core\context;
use core\context\course as context_course;
use core\context\system as context_system;
use core\context\user as context_user;
use core\context_helper;
use core\exception\coding_exception;
use core\output\action_link;
use core\output\pix_icon;
use core\url;
use core\moodlenet\utilities;
use core_contentbank\contentbank;
use core_plugin_manager;
use dml_missing_record_exception;
use moodle_page;
use part_of_admin_tree;
use repository;

/**
 * Class used to manage the settings option for the current page
 *
 * This class is used to manage the settings options in a tree format (recursively)
 * and was created initially for use with the settings blocks.
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_navigation extends navigation_node {
    /** @var context the current context */
    protected $context;
    /** @var moodle_page the moodle page that the navigation belongs to */
    protected $page;
    /** @var string contains administration section navigation_nodes */
    protected $adminsection;
    /** @var bool A switch to see if the navigation node is initialised */
    protected $initialised = false;
    /** @var array An array of users that the nodes can extend for. */
    protected $userstoextendfor = [];
    /** @var navigation_cache **/
    protected $cache;

    /**
     * Sets up the object with basic settings and preparse it for use
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page &$page) {
        if (during_initial_install()) {
            return;
        }
        $this->page = $page;
        // Initialise the main navigation. It is most important that this is done before we try anything.
        $this->page->navigation->initialise();

        // Initialise the navigation cache.
        $this->cache = new navigation_cache(self::CACHE_NAME);
        $this->children = new navigation_node_collection();
    }

    /**
     * Initialise the settings navigation based on the current context
     *
     * This function initialises the settings navigation tree for a given context
     * by calling supporting functions to generate major parts of the tree.
     *
     */
    public function initialise() {
        global $DB, $SESSION, $SITE;

        if (during_initial_install()) {
            return false;
        } else if ($this->initialised) {
            return true;
        }
        $this->id = 'settingsnav';
        $this->context = $this->page->context;

        $context = $this->context;
        if ($context->contextlevel == CONTEXT_BLOCK) {
            $this->load_block_settings();
            $context = $context->get_parent_context();
            $this->context = $context;
        }
        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                if ($this->page->url->compare(new url('/admin/settings.php', ['section' => 'frontpagesettings']))) {
                    $this->load_front_page_settings(($context->id == $this->context->id));
                }
                break;
            case CONTEXT_COURSECAT:
                $this->load_category_settings();
                break;
            case CONTEXT_COURSE:
                if ($this->page->course->id != $SITE->id) {
                    $this->load_course_settings(($context->id == $this->context->id));
                } else {
                    $this->load_front_page_settings(($context->id == $this->context->id));
                }
                break;
            case CONTEXT_MODULE:
                $this->load_module_settings();
                $this->load_course_settings();
                break;
            case CONTEXT_USER:
                if ($this->page->course->id != $SITE->id) {
                    $this->load_course_settings();
                }
                break;
        }

        $usersettings = $this->load_user_settings($this->page->course->id);

        $adminsettings = false;
        if (isloggedin() && !isguestuser() && (!isset($SESSION->load_navigation_admin) || $SESSION->load_navigation_admin)) {
            $isadminpage = $this->is_admin_tree_needed();

            if (has_capability('moodle/site:configview', context_system::instance())) {
                if (has_capability('moodle/site:config', context_system::instance())) {
                    // Make sure this works even if config capability changes on the fly
                    // and also make it fast for admin right after login.
                    $SESSION->load_navigation_admin = 1;
                    if ($isadminpage) {
                        $adminsettings = $this->load_administration_settings();
                    }
                } else if (!isset($SESSION->load_navigation_admin)) {
                    $adminsettings = $this->load_administration_settings();
                    $SESSION->load_navigation_admin = (int)($adminsettings->children->count() > 0);
                } else if ($SESSION->load_navigation_admin) {
                    if ($isadminpage) {
                        $adminsettings = $this->load_administration_settings();
                    }
                }

                // Print empty navigation node, if needed.
                if ($SESSION->load_navigation_admin && !$isadminpage) {
                    if ($adminsettings) {
                        // Do not print settings tree on pages that do not need it, this helps with performance.
                        $adminsettings->remove();
                        $adminsettings = false;
                    }
                    $siteadminnode = $this->add(
                        get_string('administrationsite'),
                        new url('/admin/search.php'),
                        self::TYPE_SITE_ADMIN,
                        null,
                        'siteadministration'
                    );
                    $siteadminnode->id = 'expandable_branch_' . $siteadminnode->type . '_' .
                            clean_param($siteadminnode->key, PARAM_ALPHANUMEXT);
                    $siteadminnode->requiresajaxloading = 'true';
                }
            }
        }

        if ($context->contextlevel == CONTEXT_SYSTEM && $adminsettings) {
            $adminsettings->force_open();
        } else if ($context->contextlevel == CONTEXT_USER && $usersettings) {
            $usersettings->force_open();
        }

        // At this point we give any local plugins the ability to extend/tinker with the navigation settings.
        $this->load_local_plugin_settings();

        foreach ($this->children as $key => $node) {
            if ($node->nodetype == self::NODETYPE_BRANCH && $node->children->count() == 0) {
                // Site administration is shown as link.
                if (!empty($SESSION->load_navigation_admin) && ($node->type === self::TYPE_SITE_ADMIN)) {
                    continue;
                }
                $node->remove();
            }
        }
        $this->initialised = true;
    }

    /**
     * Override the parent function so that we can add preceeding hr's and set a
     * root node class against all first level element
     *
     * It does this by first calling the parent's add method {@link navigation_node::add()}
     * and then proceeds to use the key to set class and hr
     *
     * @param string $text text to be used for the link.
     * @param string|url $url url for the new node
     * @param int $type the type of node navigation_node::TYPE_*
     * @param string $shorttext
     * @param string|int $key a key to access the node by.
     * @param pix_icon $icon An icon that appears next to the node.
     * @return navigation_node with the new node added to it.
     */
    #[\Override]
    public function add($text, $url = null, $type = null, $shorttext = null, $key = null, ?pix_icon $icon = null) {
        $node = parent::add($text, $url, $type, $shorttext, $key, $icon);
        $node->add_class('root_node');
        return $node;
    }

    /**
     * This function allows the user to add something to the start of the settings
     * navigation, which means it will be at the top of the settings navigation block
     *
     * @param string $text text to be used for the link.
     * @param string|url $url url for the new node
     * @param int $type the type of node navigation_node::TYPE_*
     * @param string $shorttext
     * @param string|int $key a key to access the node by.
     * @param pix_icon $icon An icon that appears next to the node.
     * @return navigation_node $node with the new node added to it.
     */
    public function prepend($text, $url = null, $type = null, $shorttext = null, $key = null, ?pix_icon $icon = null) {
        $children = $this->children;
        $childrenclass = get_class($children);
        $this->children = new $childrenclass();
        $node = $this->add($text, $url, $type, $shorttext, $key, $icon);
        foreach ($children as $child) {
            $this->children->add($child);
        }
        return $node;
    }

    /**
     * Does this page require loading of full admin tree or is
     * it enough rely on AJAX?
     *
     * @return bool
     */
    protected function is_admin_tree_needed() {
        if (self::$loadadmintree) {
            // Usually external admin page or settings page.
            return true;
        }

        if ($this->page->pagelayout === 'admin' || strpos($this->page->pagetype, 'admin-') === 0) {
            // Admin settings tree is intended for system level settings and management only, use navigation for the rest!
            if ($this->page->context->contextlevel != CONTEXT_SYSTEM) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Load the site administration tree
     *
     * This function loads the site administration tree by using the lib/adminlib library functions
     *
     * @param navigation_node $referencebranch A reference to a branch in the settings
     *      navigation tree
     * @param part_of_admin_tree $adminbranch The branch to add, if null generate the admin
     *      tree and start at the beginning
     * @return mixed A key to access the admin tree by
     */
    protected function load_administration_settings(
        ?navigation_node $referencebranch = null,
        ?part_of_admin_tree $adminbranch = null,
    ) {
        global $CFG;

        // Check if we are just starting to generate this navigation.
        if ($referencebranch === null) {
            // Require the admin lib then get an admin structure.
            if (!function_exists('admin_get_root')) {
                require_once($CFG->dirroot . '/lib/adminlib.php');
            }
            $adminroot = admin_get_root(false, false);
            // This is the active section identifier.
            $this->adminsection = $this->page->url->param('section');

            // Disable the navigation from automatically finding the active node.
            navigation_node::$autofindactive = false;
            $referencebranch = $this->add(
                get_string('administrationsite'),
                '/admin/search.php',
                self::TYPE_SITE_ADMIN,
                null,
                'root',
            );
            foreach ($adminroot->children as $adminbranch) {
                $this->load_administration_settings($referencebranch, $adminbranch);
            }
            navigation_node::$autofindactive = true;

            // Use the admin structure to locate the active page.
            if (!$this->contains_active_node() && $current = $adminroot->locate($this->adminsection, true)) {
                $currentnode = $this;
                while (($pathkey = array_pop($current->path)) !== null && $currentnode) {
                    $currentnode = $currentnode->get($pathkey);
                }
                if ($currentnode) {
                    $currentnode->make_active();
                }
            } else {
                $this->scan_for_active_node($referencebranch);
            }
            return $referencebranch;
        } else if ($adminbranch->check_access()) {
            // We have a reference branch that we can access and is not hidden `hurrah`
            // Now we need to display it and any children it may have.
            $url = null;
            $icon = null;

            if ($adminbranch instanceof \core_admin\local\settings\linkable_settings_page) {
                if (empty($CFG->linkadmincategories) && $adminbranch instanceof admin_category) {
                    $url = null;
                } else {
                    $url = $adminbranch->get_settings_page_url();
                }
            }

            // Add the branch.
            $reference = $referencebranch->add(
                $adminbranch->visiblename,
                $url,
                self::TYPE_SETTING,
                null,
                $adminbranch->name,
                $icon,
            );

            if ($adminbranch->is_hidden()) {
                if (
                    (
                        $adminbranch instanceof admin_externalpage
                        || $adminbranch instanceof admin_settingpage
                    )
                    && $adminbranch->name == $this->adminsection
                ) {
                    $reference->add_class('hidden');
                } else {
                    $reference->display = false;
                }
            }

            // Check if we are generating the admin notifications and whether notificiations exist.
            if ($adminbranch->name === 'adminnotifications' && admin_critical_warnings_present()) {
                $reference->add_class('criticalnotification');
            }
            // Check if this branch has children.
            if (
                $reference
                && isset($adminbranch->children)
                && is_array($adminbranch->children)
                && count($adminbranch->children) > 0
            ) {
                foreach ($adminbranch->children as $branch) {
                    // Generate the child branches as well now using this branch as the reference.
                    $this->load_administration_settings($reference, $branch);
                }
            } else {
                $reference->icon = new pix_icon('i/settings', '');
            }
        }
    }

    /**
     * This function recursivily scans nodes until it finds the active node or there
     * are no more nodes.
     * @param navigation_node $node
     */
    protected function scan_for_active_node(navigation_node $node) {
        if (!$node->check_if_active() && $node->children->count() > 0) {
            foreach ($node->children as &$child) {
                $this->scan_for_active_node($child);
            }
        }
    }

    /**
     * Gets a navigation node given an array of keys that represent the path to
     * the desired node.
     *
     * @param array $path
     * @return navigation_node|false
     */
    protected function get_by_path(array $path) {
        $node = $this->get(array_shift($path));
        foreach ($path as $key) {
            $node->get($key);
        }
        return $node;
    }

    /**
     * This function loads the course settings that are available for the user
     *
     * @param bool $forceopen If set to true the course node will be forced open
     * @return navigation_node|false
     */
    protected function load_course_settings($forceopen = false) {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/course/lib.php');

        $course = $this->page->course;
        $coursecontext = context_course::instance($course->id);
        $adminoptions = course_get_user_administration_options($course, $coursecontext);

        // Note: Do not test if enrolled or viewing here because we need the enrol link in Course administration section.
        $coursenode = $this->add(get_string('courseadministration'), null, self::TYPE_COURSE, null, 'courseadmin');
        if ($forceopen) {
            $coursenode->force_open();
        }

        // MoodleNet links.
        if ($this->page->user_is_editing()) {
            $this->page->requires->js_call_amd('core/moodlenet/mutations', 'init');
        }
        $usercanshare = utilities::can_user_share($coursecontext, $USER->id, 'course');
        $issuerid = get_config('moodlenet', 'oauthservice');
        try {
            $issuer = \core\oauth2\api::get_issuer($issuerid);
            $isvalidinstance = utilities::is_valid_instance($issuer);
            if ($usercanshare && $isvalidinstance) {
                $this->page->requires->js_call_amd('core/moodlenet/send_resource', 'init');
                $action = new action_link(new url(''), '', null, [
                    'data-action' => 'sendtomoodlenet',
                    'data-type' => 'course',
                ]);
                // Share course to MoodleNet link.
                $coursenode->add(
                    get_string('moodlenet:sharetomoodlenet', 'moodle'),
                    $action,
                    self::TYPE_SETTING,
                    null,
                    'exportcoursetomoodlenet'
                )->set_force_into_more_menu(true);
                // MoodleNet share progress link.
                $url = new url('/moodlenet/shareprogress.php');
                $coursenode->add(
                    get_string('moodlenet:shareprogress'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'moodlenetshareprogress'
                )->set_force_into_more_menu(true);
            }
        } catch (dml_missing_record_exception $e) {
            debugging(
                "Invalid MoodleNet OAuth 2 service set in site administration: 'moodlenet | oauthservice'. " .
                "This must be a valid issuer."
            );
        }

        if ($adminoptions->update) {
            // Add the course settings link.
            $url = new url('/course/edit.php', ['id' => $course->id]);
            $coursenode->add(
                get_string('settings'),
                $url,
                self::TYPE_SETTING,
                null,
                'editsettings',
                new pix_icon('i/settings', '')
            );
        }

        if ($adminoptions->editcompletion) {
            // Add the course completion settings link.
            $url = new url('/course/completion.php', ['id' => $course->id]);
            $coursenode->add(
                get_string('coursecompletion', 'completion'),
                $url,
                self::TYPE_SETTING,
                null,
                'coursecompletion',
                new pix_icon('i/settings', '')
            );
        }

        if (!$adminoptions->update && $adminoptions->tags) {
            $url = \core\router\util::get_path_for_callable([
                \core_course\route\controller\tags_controller::class,
                'administer_tags',
            ], ['course' => $course->id]);
            $coursenode->add(
                get_string('coursetags', 'tag'),
                $url,
                self::TYPE_SETTING,
                null,
                'coursetags',
                new pix_icon('i/settings', ''),
            );
            $coursenode->get('coursetags')->set_force_into_more_menu();
        }

        // Add enrol nodes.
        enrol_add_course_navigation($coursenode, $course);

        // Manage filters.
        if ($adminoptions->filters) {
            $url = new url('/filter/manage.php', ['contextid' => $coursecontext->id]);
            $coursenode->add(
                get_string('filters', 'admin'),
                $url,
                self::TYPE_SETTING,
                null,
                'filtermanagement',
                new pix_icon('i/filter', '')
            );
        }

        // View course reports.
        if ($adminoptions->reports) {
            $reportnav = $coursenode->add(
                get_string('reports'),
                new url('/report/view.php', ['courseid' => $coursecontext->instanceid]),
                self::TYPE_CONTAINER,
                null,
                'coursereports',
                new pix_icon('i/stats', '')
            );
            $coursereports = component::get_plugin_list('coursereport');
            foreach ($coursereports as $report => $dir) {
                $libfile = $CFG->dirroot . '/course/report/' . $report . '/lib.php';
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $reportfunction = $report . '_report_extend_navigation';
                    if (function_exists($report . '_report_extend_navigation')) {
                        $reportfunction($reportnav, $course, $coursecontext);
                    }
                }
            }

            $reports = get_plugin_list_with_function('report', 'extend_navigation_course', 'lib.php');
            foreach ($reports as $reportfunction) {
                $reportfunction($reportnav, $course, $coursecontext);
            }

            if (!$reportnav->has_children()) {
                $reportnav->remove();
            }
        }

        // Grade penalty navigation.
        \core_grades\penalty_manager::extend_navigation_course($coursenode, $course, $coursecontext);

        // Check if we can view the gradebook's setup page.
        if ($adminoptions->gradebook) {
            $url = new url('/grade/edit/tree/index.php', ['id' => $course->id]);
            $coursenode->add(
                get_string('gradebooksetup', 'grades'),
                $url,
                self::TYPE_SETTING,
                null,
                'gradebooksetup',
                new pix_icon('i/settings', '')
            );
        }

        // Add the context locking node.
        $this->add_context_locking_node($coursenode, $coursecontext);

        // Add outcome if permitted.
        if ($adminoptions->outcomes) {
            $url = new url('/grade/edit/outcome/course.php', ['id' => $course->id]);
            $coursenode->add(
                get_string('outcomes', 'grades'),
                $url,
                self::TYPE_SETTING,
                null,
                'outcomes',
                new pix_icon('i/outcomes', ''),
            );
        }

        // Add badges navigation.
        if ($adminoptions->badges) {
            require_once($CFG->libdir . '/badgeslib.php');
            badges_add_course_navigation($coursenode, $course);
        }

        // Questions.
        require_once($CFG->libdir . '/questionlib.php');
        $baseurl = \core_question\local\bank\question_bank_helper::get_url_for_qbank_list($course->id);
        question_extend_settings_navigation($coursenode, $coursecontext, $baseurl);

        if ($adminoptions->update) {
            // Repository Instances.
            if (!$this->cache->cached('contexthasrepos' . $coursecontext->id)) {
                require_once($CFG->dirroot . '/repository/lib.php');
                $editabletypes = repository::get_editable_types($coursecontext);
                $haseditabletypes = !empty($editabletypes);
                unset($editabletypes);
                $this->cache->set('contexthasrepos' . $coursecontext->id, $haseditabletypes);
            } else {
                $haseditabletypes = $this->cache->{'contexthasrepos' . $coursecontext->id};
            }
            if ($haseditabletypes) {
                $url = new url('/repository/manage_instances.php', ['contextid' => $coursecontext->id]);
                $coursenode->add(
                    get_string('repositories'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    null,
                    new pix_icon('i/repository', ''),
                );
            }
        }

        // Manage files.
        if ($adminoptions->files) {
            // Hidden in new courses and courses where legacy files were turned off.
            $url = new url('/files/index.php', ['contextid' => $coursecontext->id]);
            $coursenode->add(
                get_string('courselegacyfiles'),
                $url,
                self::TYPE_SETTING,
                null,
                'coursefiles',
                new pix_icon('i/folder', ''),
            );
        }

        // Let plugins hook into course navigation.
        $pluginsfunction = get_plugins_with_function('extend_navigation_course', 'lib.php');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            // Ignore the report and gradepenalty plugins as they were already loaded above.
            if ($plugintype == 'report' || $plugintype == 'gradepenalty') {
                continue;
            }
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($coursenode, $course, $coursecontext);
            }
        }

        // Prepare data for course content download functionality if it is enabled.
        if (\core\content::can_export_context($coursecontext, $USER)) {
            $linkattr = \core_course\output\content_export_link::get_attributes($coursecontext);
            $actionlink = new action_link($linkattr->url, $linkattr->displaystring, null, $linkattr->elementattributes);

            $coursenode->add(
                $linkattr->displaystring,
                $actionlink,
                self::TYPE_SETTING,
                null,
                'download',
                new pix_icon('t/download', '')
            );
            $coursenode->get('download')->set_force_into_more_menu(true);
        }

        // Course reuse options.
        if (
            $adminoptions->import
                || $adminoptions->backup
                || $adminoptions->restore
                || $adminoptions->copy
                || $adminoptions->reset
        ) {
            $coursereusenav = $coursenode->add(
                get_string('coursereuse'),
                new url('/backup/view.php', ['id' => $course->id]),
                self::TYPE_CONTAINER,
                null,
                'coursereuse',
                new pix_icon('t/edit', ''),
            );

            // Import data from other courses.
            if ($adminoptions->import) {
                $url = new url('/backup/import.php', ['id' => $course->id]);
                $coursereusenav->add(get_string('import'), $url, self::TYPE_SETTING, null, 'import', new pix_icon('i/import', ''));
            }

            // Backup this course.
            if ($adminoptions->backup) {
                $url = new url('/backup/backup.php', ['id' => $course->id]);
                $coursereusenav->add(get_string('backup'), $url, self::TYPE_SETTING, null, 'backup', new pix_icon('i/backup', ''));
            }

            // Restore to this course.
            if ($adminoptions->restore) {
                $url = new url('/backup/restorefile.php', ['contextid' => $coursecontext->id]);
                $coursereusenav->add(
                    get_string('restore'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'restore',
                    new pix_icon('i/restore', ''),
                );
            }

            // Copy this course.
            if ($adminoptions->copy) {
                $url = new url('/backup/copy.php', ['id' => $course->id]);
                $coursereusenav->add(get_string('copycourse'), $url, self::TYPE_SETTING, null, 'copy', new pix_icon('t/copy', ''));
            }

            // Reset this course.
            if ($adminoptions->reset) {
                $url = new url('/course/reset.php', ['id' => $course->id]);
                $coursereusenav->add(get_string('reset'), $url, self::TYPE_SETTING, null, 'reset', new pix_icon('i/return', ''));
            }
        }

        // Return we are done.
        return $coursenode;
    }

    /**
     * Get the moodle_page object associated to the current settings navigation.
     *
     * @return moodle_page
     */
    public function get_page(): moodle_page {
        return $this->page;
    }

    /**
     * This function calls the module function to inject module settings into the
     * settings navigation tree.
     *
     * This only gets called if there is a corrosponding function in the modules
     * lib file.
     *
     * For examples mod/forum/lib.php {@link forum_extend_settings_navigation()}
     *
     * @return navigation_node|false
     */
    protected function load_module_settings() {
        global $CFG, $USER;

        if (!$this->page->cm && $this->context->contextlevel == CONTEXT_MODULE && $this->context->instanceid) {
            $cm = get_coursemodule_from_id(false, $this->context->instanceid, 0, false, MUST_EXIST);
            $this->page->set_cm($cm, $this->page->course);
        }

        $file = $CFG->dirroot . '/mod/' . $this->page->activityname . '/lib.php';
        if (file_exists($file)) {
            require_once($file);
        }

        $modulenode = $this->add(
            get_string('pluginadministration', $this->page->activityname),
            null,
            self::TYPE_SETTING,
            null,
            'modulesettings',
        );
        $modulenode->nodetype = navigation_node::NODETYPE_BRANCH;
        $modulenode->force_open();

        // Settings for the module.
        if (has_capability('moodle/course:manageactivities', $this->page->cm->context)) {
            $url = new url('/course/modedit.php', ['update' => $this->page->cm->id, 'return' => 1]);
            $modulenode->add(get_string('settings'), $url, self::TYPE_SETTING, null, 'modedit', new pix_icon('i/settings', ''));
        }
        // Assign local roles.
        if (count(get_assignable_roles($this->page->cm->context)) > 0) {
            $url = new url('/' . $CFG->admin . '/roles/assign.php', ['contextid' => $this->page->cm->context->id]);
            $modulenode->add(
                get_string('localroles', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'roleassign',
                new pix_icon('i/role', '')
            );
        }
        // Override roles.
        if (
            has_capability('moodle/role:review', $this->page->cm->context)
            || count(get_overridable_roles($this->page->cm->context)) > 0
        ) {
            $url = new url('/admin/roles/permissions.php', ['contextid' => $this->page->cm->context->id]);
            $modulenode->add(
                get_string('permissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'roleoverride',
                new pix_icon('i/permissions', '')
            );
        }
        // Check role permissions.
        if (
            has_any_capability(
                ['moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:assign'],
                $this->page->cm->context,
            )
        ) {
            $url = new url('/' . $CFG->admin . '/roles/check.php', ['contextid' => $this->page->cm->context->id]);
            $modulenode->add(
                get_string('checkpermissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'rolecheck',
                new pix_icon('i/checkpermissions', ''),
            );
        }

        // Add the context locking node.
        $this->add_context_locking_node($modulenode, $this->page->cm->context);

        // Manage filters.
        if (
            has_capability('moodle/filter:manage', $this->page->cm->context) &&
            count(filter_get_available_in_context($this->page->cm->context)) > 0
        ) {
            $url = new url('/filter/manage.php', ['contextid' => $this->page->cm->context->id]);
            $modulenode->add(
                get_string('filters', 'admin'),
                $url,
                self::TYPE_SETTING,
                null,
                'filtermanage',
                new pix_icon('i/filter', '')
            );
        }
        // Add reports.
        $reports = get_plugin_list_with_function('report', 'extend_navigation_module', 'lib.php');
        foreach ($reports as $reportfunction) {
            $reportfunction($modulenode, $this->page->cm);
        }
        // Add a backup link.
        $featuresfunc = $this->page->activityname . '_supports';
        if (
            function_exists($featuresfunc)
            && $featuresfunc(FEATURE_BACKUP_MOODLE2)
            && has_capability('moodle/backup:backupactivity', $this->page->cm->context)
        ) {
            $url = new url('/backup/backup.php', ['id' => $this->page->cm->course, 'cm' => $this->page->cm->id]);
            $modulenode->add(get_string('backup'), $url, self::TYPE_SETTING, null, 'backup', new pix_icon('i/backup', ''));
        }

        // Restore this activity.
        $featuresfunc = $this->page->activityname . '_supports';
        if (
            function_exists($featuresfunc) &&
            $featuresfunc(FEATURE_BACKUP_MOODLE2) &&
            has_capability('moodle/restore:restoreactivity', $this->page->cm->context)
        ) {
            $url = new url('/backup/restorefile.php', ['contextid' => $this->page->cm->context->id]);
            $modulenode->add(get_string('restore'), $url, self::TYPE_SETTING, null, 'restore', new pix_icon('i/restore', ''));
        }

        // Allow the active advanced grading method plugin to append its settings.
        $featuresfunc = $this->page->activityname . '_supports';
        if (
            function_exists($featuresfunc)
            && $featuresfunc(FEATURE_ADVANCED_GRADING)
            && has_capability('moodle/grade:managegradingforms', $this->page->cm->context)
        ) {
            require_once($CFG->dirroot . '/grade/grading/lib.php');
            $gradingman = get_grading_manager($this->page->cm->context, 'mod_' . $this->page->activityname);
            $gradingman->extend_settings_navigation($this, $modulenode);
        }

        $function = $this->page->activityname . '_extend_settings_navigation';
        if (function_exists($function)) {
            $function($this, $modulenode);
        }

        // Send activity to MoodleNet.
        $usercanshare = utilities::can_user_share($this->context->get_course_context(), $USER->id);
        $issuerid = get_config('moodlenet', 'oauthservice');
        try {
            $issuer = \core\oauth2\api::get_issuer($issuerid);
            $isvalidinstance = utilities::is_valid_instance($issuer);
            if ($usercanshare && $isvalidinstance) {
                $this->page->requires->js_call_amd('core/moodlenet/send_resource', 'init');
                $action = new action_link(new url(''), '', null, [
                    'data-action' => 'sendtomoodlenet',
                    'data-type' => 'activity',
                ]);
                $modulenode->add(
                    get_string('moodlenet:sharetomoodlenet', 'moodle'),
                    $action,
                    self::TYPE_SETTING,
                    null,
                    'exportmoodlenet'
                )->set_force_into_more_menu(true);
            }
        } catch (dml_missing_record_exception $e) {
            debugging("Invalid MoodleNet OAuth 2 service set in site administration: 'moodlenet | oauthservice'. " .
                "This must be a valid issuer.");
        }

        // Remove the module node if there are no children.
        if ($modulenode->children->count() <= 0) {
            $modulenode->remove();
        }

        return $modulenode;
    }

    /**
     * Loads the user settings block of the settings nav
     *
     * This function is simply works out the userid and whether we need to load
     * just the current users profile settings, or the current user and the user the
     * current user is viewing.
     *
     * This function has some very ugly code to work out the user, if anyone has
     * any bright ideas please feel free to intervene.
     *
     * @param int $courseid The course id of the current course
     * @return navigation_node|false
     */
    protected function load_user_settings($courseid = SITEID) {
        global $USER, $CFG;

        if (isguestuser() || !isloggedin()) {
            return false;
        }

        $navusers = $this->page->navigation->get_extending_users();

        if (count($this->userstoextendfor) > 0 || count($navusers) > 0) {
            $usernode = null;
            foreach ($this->userstoextendfor as $userid) {
                if ($userid == $USER->id) {
                    continue;
                }
                $node = $this->generate_user_settings($courseid, $userid, 'userviewingsettings');
                if (is_null($usernode)) {
                    $usernode = $node;
                }
            }
            foreach ($navusers as $user) {
                if ($user->id == $USER->id) {
                    continue;
                }
                $node = $this->generate_user_settings($courseid, $user->id, 'userviewingsettings');
                if (is_null($usernode)) {
                    $usernode = $node;
                }
            }
            $this->generate_user_settings($courseid, $USER->id);
        } else {
            $usernode = $this->generate_user_settings($courseid, $USER->id);
        }
        return $usernode;
    }

    /**
     * Extends the settings navigation for the given user.
     *
     * Note: This method gets called automatically if you call
     * $PAGE->navigation->extend_for_user($userid)
     *
     * @param int $userid
     */
    public function extend_for_user($userid) {
        global $CFG;

        if (!in_array($userid, $this->userstoextendfor)) {
            $this->userstoextendfor[] = $userid;
            if ($this->initialised) {
                $this->generate_user_settings($this->page->course->id, $userid, 'userviewingsettings');
                $children = [];
                foreach ($this->children as $child) {
                    $children[] = $child;
                }
                array_unshift($children, array_pop($children));
                $this->children = new navigation_node_collection();
                foreach ($children as $child) {
                    $this->children->add($child);
                }
            }
        }
    }

    /**
     * This function gets called by {@link settings_navigation::load_user_settings()} and actually works out
     * what can be shown/done
     *
     * @param int $courseid The current course' id
     * @param int $userid The user id to load for
     * @param string $gstitle The string to pass to get_string for the branch title
     * @return navigation_node|false
     */
    protected function generate_user_settings($courseid, $userid, $gstitle = 'usercurrentsettings') {
        global $DB, $CFG, $USER, $SITE;

        if ($courseid != $SITE->id) {
            if (!empty($this->page->course->id) && $this->page->course->id == $courseid) {
                $course = $this->page->course;
            } else {
                $select = context_helper::get_preload_record_columns_sql('ctx');
                $sql = "SELECT c.*, $select
                          FROM {course} c
                          JOIN {context} ctx ON c.id = ctx.instanceid
                         WHERE c.id = :courseid AND ctx.contextlevel = :contextlevel";
                $params = ['courseid' => $courseid, 'contextlevel' => CONTEXT_COURSE];
                $course = $DB->get_record_sql($sql, $params, MUST_EXIST);
                context_helper::preload_from_record($course);
            }
        } else {
            $course = $SITE;
        }

        $coursecontext = context_course::instance($course->id);
        $systemcontext   = context_system::instance();
        $currentuser = ($USER->id == $userid);

        if ($currentuser) {
            $user = $USER;
            $usercontext = context_user::instance($user->id);
        } else {
            $select = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT u.*, $select
                      FROM {user} u
                      JOIN {context} ctx ON u.id = ctx.instanceid
                     WHERE u.id = :userid AND ctx.contextlevel = :contextlevel";
            $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
            $user = $DB->get_record_sql($sql, $params, IGNORE_MISSING);
            if (!$user) {
                return false;
            }
            context_helper::preload_from_record($user);

            // Check that the user can view the profile.
            $usercontext = context_user::instance($user->id); // User context.
            $canviewuser = has_capability('moodle/user:viewdetails', $usercontext);

            if ($course->id == $SITE->id) {
                // Reduce possibility of "browsing" userbase at site level.
                if ($CFG->forceloginforprofiles && !has_coursecontact_role($user->id) && !$canviewuser) {
                    // Teachers can browse and be browsed at site level.
                    // If not forceloginforprofiles, allow access (See MDL-4366).
                    return false;
                }
            } else {
                $canviewusercourse = has_capability('moodle/user:viewdetails', $coursecontext);
                $userisenrolled = is_enrolled($coursecontext, $user->id, '', true);
                if ((!$canviewusercourse && !$canviewuser) || !$userisenrolled) {
                    return false;
                }
                $canaccessallgroups = has_capability('moodle/site:accessallgroups', $coursecontext);
                if (!$canaccessallgroups && groups_get_course_groupmode($course) == SEPARATEGROUPS && !$canviewuser) {
                    // If groups are in use, make sure we can see that group (MDL-45874). That does not apply to parents.
                    if ($courseid == $this->page->course->id) {
                        $mygroups = get_fast_modinfo($this->page->course)->groups;
                    } else {
                        $mygroups = groups_get_user_groups($courseid);
                    }
                    $usergroups = groups_get_user_groups($courseid, $userid);
                    if (!array_intersect_key($mygroups[0], $usergroups[0])) {
                        return false;
                    }
                }
            }
        }

        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $this->page->context));

        $key = $gstitle;
        $prefurl = new url('/user/preferences.php');
        if ($gstitle != 'usercurrentsettings') {
            $key .= $userid;
            $prefurl->param('userid', $userid);
        }

        // Add a user setting branch.
        if ($gstitle == 'usercurrentsettings') {
            $mainpage = $this->add(get_string('home'), new url('/'), self::TYPE_CONTAINER, null, 'site');

            // This should be set to false as we don't want to show this to the user. It's only for generating the correct
            // breadcrumb.
            $mainpage->display = false;
            $homepage = get_home_page();
            if (($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES)) {
                $mainpage->mainnavonly = true;
            }

            $iscurrentuser = ($user->id == $USER->id);

            $baseargs = ['id' => $user->id];
            if ($course->id != $SITE->id && !$iscurrentuser) {
                $baseargs['course'] = $course->id;
                $issitecourse = false;
            } else {
                // Load all categories and get the context for the system.
                $issitecourse = true;
            }

            // Add the user profile to the dashboard.
            $profilenode = $mainpage->add(get_string('profile'), new url(
                '/user/profile.php',
                ['id' => $user->id]
            ), self::TYPE_SETTING, null, 'myprofile');

            // Add blog nodes.
            if (!empty($CFG->enableblogs)) {
                if (!$this->cache->cached('userblogoptions' . $user->id)) {
                    require_once($CFG->dirroot . '/blog/lib.php');
                    // Get all options for the user.
                    $options = blog_get_options_for_user($user);
                    $this->cache->set('userblogoptions' . $user->id, $options);
                } else {
                    $options = $this->cache->{'userblogoptions' . $user->id};
                }

                if (count($options) > 0) {
                    $blogs = $profilenode->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER);
                    foreach ($options as $type => $option) {
                        if ($type == "rss") {
                            $blogs->add(
                                $option['string'],
                                $option['link'],
                                self::TYPE_SETTING,
                                null,
                                null,
                                new pix_icon('i/rss', '')
                            );
                        } else {
                            $blogs->add($option['string'], $option['link'], self::TYPE_SETTING, null, 'blog' . $type);
                        }
                    }
                }
            }

            // Add the messages link.
            // It is context based so can appear in the user's profile and in course participants information.
            if (!empty($CFG->messaging)) {
                $messageargs = ['user1' => $USER->id];
                if ($USER->id != $user->id) {
                    $messageargs['user2'] = $user->id;
                }
                $url = new url('/message/index.php', $messageargs);
                $mainpage->add(get_string('messages', 'message'), $url, self::TYPE_SETTING, null, 'messages');
            }

            // Add the "My private files" link.
            // This link doesn't have a unique display for course context so only display it under the user's profile.
            if ($issitecourse && $iscurrentuser && has_capability('moodle/user:manageownfiles', $usercontext)) {
                $url = new url('/user/files.php');
                $mainpage->add(get_string('privatefiles'), $url, self::TYPE_SETTING, null, 'privatefiles');
            }

            // Add a node to view the users notes if permitted.
            if (
                !empty($CFG->enablenotes) &&
                    has_any_capability(['moodle/notes:manage', 'moodle/notes:view'], $coursecontext)
            ) {
                $url = new url('/notes/index.php', ['user' => $user->id]);
                if ($coursecontext->instanceid != SITEID) {
                    $url->param('course', $coursecontext->instanceid);
                }
                $profilenode->add(get_string('notes', 'notes'), $url);
            }

            // Show the grades node.
            if (($issitecourse && $iscurrentuser) || has_capability('moodle/user:viewdetails', $usercontext)) {
                require_once($CFG->dirroot . '/user/lib.php');
                // Set the grades node to link to the "Grades" page.
                if ($course->id == SITEID) {
                    $url = user_mygrades_url($user->id, $course->id);
                } else { // Otherwise we are in a course and should redirect to the user grade report (Activity report version).
                    $url = new url('/course/user.php', ['mode' => 'grade', 'id' => $course->id, 'user' => $user->id]);
                }
                $mainpage->add(get_string('grades', 'grades'), $url, self::TYPE_SETTING, null, 'mygrades');
            }

            // Let plugins hook into user navigation.
            $pluginsfunction = get_plugins_with_function('extend_navigation_user', 'lib.php');
            foreach ($pluginsfunction as $plugintype => $plugins) {
                if ($plugintype != 'report') {
                    foreach ($plugins as $pluginfunction) {
                        $pluginfunction($profilenode, $user, $usercontext, $course, $coursecontext);
                    }
                }
            }

            $usersetting = navigation_node::create(get_string('preferences', 'moodle'), $prefurl, self::TYPE_CONTAINER, null, $key);
            $mainpage->add_node($usersetting);
        } else {
            $usersetting = $this->add(get_string('preferences', 'moodle'), $prefurl, self::TYPE_CONTAINER, null, $key);
            $usersetting->display = false;
        }
        $usersetting->id = 'usersettings';

        // Check if the user has been deleted.
        if ($user->deleted) {
            if (!has_capability('moodle/user:update', $coursecontext)) {
                // We can't edit the user so just show the user deleted message.
                $usersetting->add(get_string('userdeleted'), null, self::TYPE_SETTING);
            } else {
                // We can edit the user so show the user deleted message and link it to the profile.
                if ($course->id == $SITE->id) {
                    $profileurl = new url('/user/profile.php', ['id' => $user->id]);
                } else {
                    $profileurl = new url('/user/view.php', ['id' => $user->id, 'course' => $course->id]);
                }
                $usersetting->add(get_string('userdeleted'), $profileurl, self::TYPE_SETTING);
            }
            return true;
        }

        $userauthplugin = false;
        if (!empty($user->auth)) {
            $userauthplugin = get_auth_plugin($user->auth);
        }

        $useraccount = $usersetting->add(get_string('useraccount'), null, self::TYPE_CONTAINER, null, 'useraccount');

        // Add the profile edit link.
        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (
                ($currentuser || is_siteadmin($USER) || !is_siteadmin($user)) &&
                    has_capability('moodle/user:update', $systemcontext)
            ) {
                $url = new url('/user/editadvanced.php', ['id' => $user->id, 'course' => $course->id]);
                $useraccount->add(get_string('editmyprofile'), $url, self::TYPE_SETTING, null, 'editprofile');
            } else if (
                (has_capability('moodle/user:editprofile', $usercontext) && !is_siteadmin($user)) ||
                    ($currentuser && has_capability('moodle/user:editownprofile', $systemcontext))
            ) {
                if ($userauthplugin && $userauthplugin->can_edit_profile()) {
                    $url = $userauthplugin->edit_profile_url();
                    if (empty($url)) {
                        $url = new url('/user/edit.php', ['id' => $user->id, 'course' => $course->id]);
                    }
                    $useraccount->add(get_string('editmyprofile'), $url, self::TYPE_SETTING, null, 'editprofile');
                }
            }
        }

        // Change password link.
        if (
            $userauthplugin && $currentuser && !\core\session\manager::is_loggedinas() && !isguestuser() &&
                has_capability('moodle/user:changeownpassword', $systemcontext) && $userauthplugin->can_change_password()
        ) {
            $passwordchangeurl = $userauthplugin->change_password_url();
            if (empty($passwordchangeurl)) {
                $passwordchangeurl = new url('/login/change_password.php', ['id' => $course->id]);
            }
            $useraccount->add(get_string("changepassword"), $passwordchangeurl, self::TYPE_SETTING, null, 'changepassword');
        }

        // Default homepage.
        $defaulthomepageuser = (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER));
        if (isloggedin() && !isguestuser($user) && $defaulthomepageuser) {
            if (
                $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                    has_capability('moodle/user:editprofile', $usercontext)
            ) {
                $url = new url('/user/defaulthomepage.php', ['id' => $user->id]);
                $useraccount->add(get_string('defaulthomepageuser'), $url, self::TYPE_SETTING, null, 'defaulthomepageuser');
            }
        }

        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (
                $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                    has_capability('moodle/user:editprofile', $usercontext)
            ) {
                $url = new url('/user/language.php', ['id' => $user->id, 'course' => $course->id]);
                $useraccount->add(get_string('preferredlanguage'), $url, self::TYPE_SETTING, null, 'preferredlanguage');
            }
        }
        $pluginmanager = core_plugin_manager::instance();
        $enabled = $pluginmanager->get_enabled_plugins('mod');
        if (isset($enabled['forum']) && isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (
                $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                    has_capability('moodle/user:editprofile', $usercontext)
            ) {
                $url = new url('/user/forum.php', ['id' => $user->id, 'course' => $course->id]);
                $useraccount->add(get_string('forumpreferences'), $url, self::TYPE_SETTING);
            }
        }
        $editors = editors_get_enabled();
        if (count($editors) > 1) {
            if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
                if (
                    $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                        has_capability('moodle/user:editprofile', $usercontext)
                ) {
                    $url = new url('/user/editor.php', ['id' => $user->id, 'course' => $course->id]);
                    $useraccount->add(get_string('editorpreferences'), $url, self::TYPE_SETTING);
                }
            }
        }

        // Add "Calendar preferences" link.
        if (isloggedin() && !isguestuser($user)) {
            if (
                $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                    has_capability('moodle/user:editprofile', $usercontext)
            ) {
                $url = new url('/user/calendar.php', ['id' => $user->id]);
                $useraccount->add(
                    get_string('calendarpreferences', 'calendar'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'preferredcalendar',
                );
            }
        }

        // Add "Content bank preferences" link.
        if (isloggedin() && !isguestuser($user)) {
            if (
                $currentuser && has_capability('moodle/user:editownprofile', $systemcontext) ||
                has_capability('moodle/user:editprofile', $usercontext)
            ) {
                $url = new url('/user/contentbank.php', ['id' => $user->id]);
                $useraccount->add(
                    get_string('contentbankpreferences', 'core_contentbank'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'contentbankpreferences'
                );
            }
        }

        // View the roles settings.
        if (
            has_any_capability(
                ['moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:manage'],
                $usercontext,
            )
        ) {
            $roles = $usersetting->add(get_string('roles'), null, self::TYPE_SETTING);

            $url = new url('/admin/roles/usersroles.php', ['userid' => $user->id, 'courseid' => $course->id]);
            $roles->add(get_string('thisusersroles', 'role'), $url, self::TYPE_SETTING);

            $assignableroles = get_assignable_roles($usercontext, ROLENAME_BOTH);

            if (!empty($assignableroles)) {
                $url = new url(
                    '/admin/roles/assign.php',
                    ['contextid' => $usercontext->id, 'userid' => $user->id, 'courseid' => $course->id]
                );
                $roles->add(get_string('assignrolesrelativetothisuser', 'role'), $url, self::TYPE_SETTING);
            }

            if (
                has_capability('moodle/role:review', $usercontext)
                || count(get_overridable_roles($usercontext, ROLENAME_BOTH)) > 0
            ) {
                $url = new url(
                    '/admin/roles/permissions.php',
                    ['contextid' => $usercontext->id, 'userid' => $user->id, 'courseid' => $course->id]
                );
                $roles->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING);
            }

            $url = new url(
                '/admin/roles/check.php',
                ['contextid' => $usercontext->id, 'userid' => $user->id, 'courseid' => $course->id]
            );
            $roles->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING);
        }

        // Repositories.
        if (!$this->cache->cached('contexthasrepos' . $usercontext->id)) {
            require_once($CFG->dirroot . '/repository/lib.php');
            $editabletypes = repository::get_editable_types($usercontext);
            $haseditabletypes = !empty($editabletypes);
            unset($editabletypes);
            $this->cache->set('contexthasrepos' . $usercontext->id, $haseditabletypes);
        } else {
            $haseditabletypes = $this->cache->{'contexthasrepos' . $usercontext->id};
        }
        if ($haseditabletypes) {
            $repositories = $usersetting->add(get_string('repositories', 'repository'), null, self::TYPE_SETTING);
            $repositories->add(get_string('manageinstances', 'repository'), new url(
                '/repository/manage_instances.php',
                ['contextid' => $usercontext->id]
            ));
        }

        // Portfolio.
        if ($currentuser && !empty($CFG->enableportfolios) && has_capability('moodle/portfolio:export', $systemcontext)) {
            require_once($CFG->libdir . '/portfoliolib.php');
            if (portfolio_has_visible_instances()) {
                $portfolio = $usersetting->add(get_string('portfolios', 'portfolio'), null, self::TYPE_SETTING);

                $url = new url('/user/portfolio.php', ['courseid' => $course->id]);
                $portfolio->add(get_string('configure', 'portfolio'), $url, self::TYPE_SETTING);

                $url = new url('/user/portfoliologs.php', ['courseid' => $course->id]);
                $portfolio->add(get_string('logs', 'portfolio'), $url, self::TYPE_SETTING);
            }
        }

        $enablemanagetokens = false;
        if (!empty($CFG->enablerssfeeds)) {
            $enablemanagetokens = true;
        } else if (
            !is_siteadmin($USER->id)
             && !empty($CFG->enablewebservices)
             && has_capability('moodle/webservice:createtoken', context_system::instance())
        ) {
            $enablemanagetokens = true;
        }
        // Security keys.
        if ($currentuser && $enablemanagetokens) {
            $url = new url('/user/managetoken.php');
            $useraccount->add(get_string('securitykeys', 'webservice'), $url, self::TYPE_SETTING);
        }

        // Messaging.
        if (
            ($currentuser && has_capability('moodle/user:editownmessageprofile', $systemcontext)) || (!isguestuser($user) &&
                has_capability('moodle/user:editmessageprofile', $usercontext) && !is_primary_admin($user->id))
        ) {
            $messagingurl = new url('/message/edit.php', ['id' => $user->id]);
            $notificationsurl = new url('/message/notificationpreferences.php', ['userid' => $user->id]);
            $useraccount->add(get_string('messagepreferences', 'message'), $messagingurl, self::TYPE_SETTING);
            $useraccount->add(get_string('notificationpreferences', 'message'), $notificationsurl, self::TYPE_SETTING);
        }

        // Blogs.
        if ($currentuser && !empty($CFG->enableblogs)) {
            $blog = $usersetting->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER, null, 'blogs');
            if (has_capability('moodle/blog:view', $systemcontext)) {
                $blog->add(
                    get_string('preferences', 'blog'),
                    new url('/blog/preferences.php'),
                    navigation_node::TYPE_SETTING
                );
            }
            if (
                !empty($CFG->useexternalblogs) && $CFG->maxexternalblogsperuser > 0 &&
                    has_capability('moodle/blog:manageexternal', $systemcontext)
            ) {
                $blog->add(
                    get_string('externalblogs', 'blog'),
                    new url('/blog/external_blogs.php'),
                    navigation_node::TYPE_SETTING
                );
                $blog->add(
                    get_string('addnewexternalblog', 'blog'),
                    new url('/blog/external_blog_edit.php'),
                    navigation_node::TYPE_SETTING
                );
            }
            // Remove the blog node if empty.
            $blog->trim_if_empty();
        }

        // Badges.
        if ($currentuser && !empty($CFG->enablebadges)) {
            $badges = $usersetting->add(get_string('badges'), null, navigation_node::TYPE_CONTAINER, null, 'badges');
            if (has_capability('moodle/badges:manageownbadges', $usercontext)) {
                $url = new url('/badges/mybadges.php');
                $badges->add(get_string('managebadges', 'badges'), $url, self::TYPE_SETTING);
            }
            $badges->add(
                get_string('preferences', 'badges'),
                new url('/badges/preferences.php'),
                navigation_node::TYPE_SETTING
            );
            if (!empty($CFG->badges_allowexternalbackpack)) {
                $badges->add(
                    get_string('backpackdetails', 'badges'),
                    new url('/badges/mybackpack.php'),
                    navigation_node::TYPE_SETTING
                );
            }
        }

        // Let plugins hook into user settings navigation.
        $pluginsfunction = get_plugins_with_function('extend_navigation_user_settings', 'lib.php');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($usersetting, $user, $usercontext, $course, $coursecontext);
            }
        }

        return $usersetting;
    }

    /**
     * Loads block specific settings in the navigation.
     *
     * @return navigation_node
     */
    protected function load_block_settings() {
        global $CFG;

        $blocknode = $this->add($this->context->get_context_name(), null, self::TYPE_SETTING, null, 'blocksettings');
        $blocknode->force_open();

        // Assign local roles.
        if (get_assignable_roles($this->context, ROLENAME_ORIGINAL)) {
            $assignurl = new url('/' . $CFG->admin . '/roles/assign.php', ['contextid' => $this->context->id]);
            $blocknode->add(
                get_string('assignroles', 'role'),
                $assignurl,
                self::TYPE_SETTING,
                null,
                'roles',
                new pix_icon('i/assignroles', '')
            );
        }

        // Override roles.
        if (has_capability('moodle/role:review', $this->context) ||  count(get_overridable_roles($this->context)) > 0) {
            $url = new url('/admin/roles/permissions.php', ['contextid' => $this->context->id]);
            $blocknode->add(
                get_string('permissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'permissions',
                new pix_icon('i/permissions', '')
            );
        }
        // Check role permissions.
        if (
            has_any_capability(
                ['moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:assign'],
                $this->context,
            )
        ) {
            $url = new url('/' . $CFG->admin . '/roles/check.php', ['contextid' => $this->context->id]);
            $blocknode->add(
                get_string('checkpermissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'checkpermissions',
                new pix_icon('i/checkpermissions', '')
            );
        }

        // Add the context locking node.
        $this->add_context_locking_node($blocknode, $this->context);

        return $blocknode;
    }

    /**
     * Loads category specific settings in the navigation
     *
     * @return navigation_node
     */
    protected function load_category_settings() {
        global $CFG;

        // We can land here while being in the context of a block, in which case we
        // should get the parent context which should be the category one. See self::initialise().
        if ($this->context->contextlevel == CONTEXT_BLOCK) {
            $catcontext = $this->context->get_parent_context();
        } else {
            $catcontext = $this->context;
        }

        // Let's make sure that we always have the right context when getting here.
        if ($catcontext->contextlevel != CONTEXT_COURSECAT) {
            throw new coding_exception('Unexpected context while loading category settings.');
        }

        $categorynodetype = navigation_node::TYPE_CONTAINER;
        $categorynode = $this->add($catcontext->get_context_name(), null, $categorynodetype, null, 'categorysettings');
        $categorynode->nodetype = navigation_node::NODETYPE_BRANCH;
        $categorynode->force_open();

        if (can_edit_in_category($catcontext->instanceid)) {
            $url = new url('/course/management.php', ['categoryid' => $catcontext->instanceid]);
            $editstring = get_string('managecategorythis');
            $node = $categorynode->add($editstring, $url, self::TYPE_SETTING, null, 'managecategory', new pix_icon('i/edit', ''));
            $node->set_show_in_secondary_navigation(false);
        }

        if (has_capability('moodle/category:manage', $catcontext)) {
            $editurl = new url('/course/editcategory.php', ['id' => $catcontext->instanceid]);
            $categorynode->add(get_string('settings'), $editurl, self::TYPE_SETTING, null, 'edit', new pix_icon('i/edit', ''));

            $addsubcaturl = new url('/course/editcategory.php', ['parent' => $catcontext->instanceid]);
            $categorynode->add(
                get_string('addsubcategory'),
                $addsubcaturl,
                self::TYPE_SETTING,
                null,
                'addsubcat',
                new pix_icon('i/withsubcat', '')
            )->set_show_in_secondary_navigation(false);
        }

        // Assign local roles.
        $assignableroles = get_assignable_roles($catcontext);
        if (!empty($assignableroles)) {
            $assignurl = new url('/' . $CFG->admin . '/roles/assign.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('assignroles', 'role'),
                $assignurl,
                self::TYPE_SETTING,
                null,
                'roles',
                new pix_icon('i/assignroles', ''),
            );
        }

        // Override roles.
        if (has_capability('moodle/role:review', $catcontext) || count(get_overridable_roles($catcontext)) > 0) {
            $url = new url('/' . $CFG->admin . '/roles/permissions.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('permissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'permissions',
                new pix_icon('i/permissions', ''),
            );
        }
        // Check role permissions.
        if (
            has_any_capability(['moodle/role:assign', 'moodle/role:safeoverride',
                'moodle/role:override', 'moodle/role:assign'], $catcontext)
        ) {
            $url = new url('/' . $CFG->admin . '/roles/check.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('checkpermissions', 'role'),
                $url,
                self::TYPE_SETTING,
                null,
                'rolecheck',
                new pix_icon('i/checkpermissions', ''),
            );
        }

        // Add the context locking node.
        $this->add_context_locking_node($categorynode, $catcontext);

        // Cohorts.
        if (has_any_capability(['moodle/cohort:view', 'moodle/cohort:manage'], $catcontext)) {
            $categorynode->add(get_string('cohorts', 'cohort'), new url(
                '/cohort/index.php',
                ['contextid' => $catcontext->id]
            ), self::TYPE_SETTING, null, 'cohort', new pix_icon('i/cohort', ''));
        }

        // Manage filters.
        if (has_capability('moodle/filter:manage', $catcontext) && count(filter_get_available_in_context($catcontext)) > 0) {
            $url = new url('/filter/manage.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('filters', 'admin'),
                $url,
                self::TYPE_SETTING,
                null,
                'filters',
                new pix_icon('i/filter', ''),
            );
        }

        // Restore.
        if (has_capability('moodle/restore:restorecourse', $catcontext)) {
            $url = new url('/backup/restorefile.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('restorecourse', 'admin'),
                $url,
                self::TYPE_SETTING,
                null,
                'restorecourse',
                new pix_icon('i/restore', ''),
            );
        }

        // Let plugins hook into category settings navigation.
        $pluginsfunction = get_plugins_with_function('extend_navigation_category_settings', 'lib.php');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($categorynode, $catcontext);
            }
        }

        $cb = new contentbank();
        if (
            $cb->is_context_allowed($catcontext)
            && has_capability('moodle/contentbank:access', $catcontext)
        ) {
            $url = new url('/contentbank/index.php', ['contextid' => $catcontext->id]);
            $categorynode->add(
                get_string('contentbank'),
                $url,
                self::TYPE_CUSTOM,
                null,
                'contentbank',
                new pix_icon('i/contentbank', '')
            );
        }

        return $categorynode;
    }

    /**
     * Determine whether the user is assuming another role
     *
     * This function checks to see if the user is assuming another role by means of
     * role switching. In doing this we compare each RSW key (context path) against
     * the current context path. This ensures that we can provide the switching
     * options against both the course and any page shown under the course.
     *
     * @return bool|int The role(int) if the user is in another role, false otherwise
     */
    protected function in_alternative_role() {
        global $USER;
        if (!empty($USER->access['rsw']) && is_array($USER->access['rsw'])) {
            if (!empty($this->page->context) && !empty($USER->access['rsw'][$this->page->context->path])) {
                return $USER->access['rsw'][$this->page->context->path];
            }
            foreach ($USER->access['rsw'] as $key => $role) {
                if (strpos($this->context->path, $key) === 0) {
                    return $role;
                }
            }
        }
        return false;
    }

    /**
     * This function loads all of the front page settings into the settings navigation.
     * This function is called when the user is on the front page, or $COURSE==$SITE
     * @param bool $forceopen (optional)
     * @return navigation_node
     */
    protected function load_front_page_settings($forceopen = false) {
        global $SITE, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $course = clone($SITE);
        $coursecontext = context_course::instance($course->id);   // Course context.
        $adminoptions = course_get_user_administration_options($course, $coursecontext);

        $frontpage = $this->add(get_string('frontpagesettings'), null, self::TYPE_SETTING, null, 'frontpage');
        if ($forceopen) {
            $frontpage->force_open();
        }
        $frontpage->id = 'frontpagesettings';

        if ($this->page->user_allowed_editing() && !$this->page->theme->haseditswitch) {
            // Add the turn on/off settings.
            $url = new url('/course/view.php', ['id' => $course->id, 'sesskey' => sesskey()]);
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $editstring = get_string('turneditingoff');
            } else {
                $url->param('edit', 'on');
                $editstring = get_string('turneditingon');
            }
            $frontpage->add($editstring, $url, self::TYPE_SETTING, null, null, new pix_icon('i/edit', ''));
        }

        if ($adminoptions->update) {
            // Add the course settings link.
            $url = new url('/admin/settings.php', ['section' => 'frontpagesettings']);
            $frontpage->add(
                get_string('settings'),
                $url,
                self::TYPE_SETTING,
                null,
                'editsettings',
                new pix_icon('i/settings', '')
            );
        }

        // Add enrol nodes.
        enrol_add_course_navigation($frontpage, $course);

        // Manage filters.
        if ($adminoptions->filters) {
            $url = new url('/filter/manage.php', ['contextid' => $coursecontext->id]);
            $frontpage->add(
                get_string('filters', 'admin'),
                $url,
                self::TYPE_SETTING,
                null,
                'filtermanagement',
                new pix_icon('i/filter', '')
            );
        }

        // View course reports.
        if ($adminoptions->reports) {
            $frontpagenav = $frontpage->add(
                get_string('reports'),
                new url(
                    '/report/view.php',
                    ['courseid' => $coursecontext->instanceid]
                ),
                self::TYPE_CONTAINER,
                null,
                'coursereports',
                new pix_icon('i/stats', '')
            );
            $coursereports = component::get_plugin_list('coursereport');
            foreach ($coursereports as $report => $dir) {
                $libfile = $CFG->dirroot . '/course/report/' . $report . '/lib.php';
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $reportfunction = $report . '_report_extend_navigation';
                    if (function_exists($report . '_report_extend_navigation')) {
                        $reportfunction($frontpagenav, $course, $coursecontext);
                    }
                }
            }

            $reports = get_plugin_list_with_function('report', 'extend_navigation_course', 'lib.php');
            foreach ($reports as $reportfunction) {
                $reportfunction($frontpagenav, $course, $coursecontext);
            }

            if (!$frontpagenav->has_children()) {
                $frontpagenav->remove();
            }
        }

        // Questions.
        require_once($CFG->libdir . '/questionlib.php');
        $baseurl = \core_question\local\bank\question_bank_helper::get_url_for_qbank_list($course->id);
        question_extend_settings_navigation($frontpage, $coursecontext, $baseurl);

        // Manage files.
        if ($adminoptions->files) {
            // Hide in new installs.
            $url = new url('/files/index.php', ['contextid' => $coursecontext->id]);
            $frontpage->add(get_string('sitelegacyfiles'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/folder', ''));
        }

        // Let plugins hook into frontpage navigation.
        $pluginsfunction = get_plugins_with_function('extend_navigation_frontpage', 'lib.php');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($frontpage, $course, $coursecontext);
            }
        }

        // Course reuse options.
        if ($adminoptions->backup || $adminoptions->restore) {
            $coursereusenav = $frontpage->add(
                get_string('coursereuse'),
                new url('/backup/view.php', ['id' => $course->id]),
                self::TYPE_CONTAINER,
                null,
                'coursereuse',
                new pix_icon('t/edit', ''),
            );

            // Backup this course.
            if ($adminoptions->backup) {
                $url = new url('/backup/backup.php', ['id' => $course->id]);
                $coursereusenav->add(get_string('backup'), $url, self::TYPE_SETTING, null, 'backup', new pix_icon('i/backup', ''));
            }

            // Restore to this course.
            if ($adminoptions->restore) {
                $url = new url('/backup/restorefile.php', ['contextid' => $coursecontext->id]);
                $coursereusenav->add(
                    get_string('restore'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'restore',
                    new pix_icon('i/restore', ''),
                );
            }
        }

        return $frontpage;
    }

    /**
     * This function gives local plugins an opportunity to modify the settings navigation.
     */
    protected function load_local_plugin_settings() {

        foreach (get_plugin_list_with_function('local', 'extend_settings_navigation') as $function) {
            $function($this, $this->context);
        }
    }

    /**
     * This function marks the cache as volatile so it is cleared during shutdown
     */
    public function clear_cache() {
        $this->cache->volatile();
    }

    /**
     * Checks to see if there are child nodes available in the specific user's preference node.
     * If so, then they have the appropriate permissions view this user's preferences.
     *
     * @since Moodle 2.9.3
     * @param int $userid The user's ID.
     * @return bool True if child nodes exist to view, otherwise false.
     */
    public function can_view_user_preferences($userid) {
        if (is_siteadmin()) {
            return true;
        }
        // See if any nodes are present in the preferences section for this user.
        $preferencenode = $this->find('userviewingsettings' . $userid, null);
        if ($preferencenode && $preferencenode->has_children()) {
            // Run through each child node.
            foreach ($preferencenode->children as $childnode) {
                // If the child node has children then this user has access to a link in the preferences page.
                if ($childnode->has_children()) {
                    return true;
                }
            }
        }
        // No links found for the user to access on the preferences page.
        return false;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(settings_navigation::class, \settings_navigation::class);
