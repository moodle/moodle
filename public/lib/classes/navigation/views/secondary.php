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

namespace core\navigation\views;

use navigation_node;
use url_select;
use settings_navigation;

/**
 * Class secondary_navigation_view.
 *
 * The secondary navigation view is a stripped down tweaked version of the
 * settings_navigation/navigation
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary extends view {
    /** @var string $headertitle The header for this particular menu*/
    public $headertitle;

    /** @var int The maximum limit of navigation nodes displayed in the secondary navigation */
    const MAX_DISPLAYED_NAV_NODES = 5;

    /** @var navigation_node The course overflow node. */
    protected $courseoverflownode = null;

    /** @var string The key of the node to set as selected in the course overflow menu, if explicitly set by a page. */
    protected $overflowselected = null;

    /**
     * Defines the default structure for the secondary nav in a course context.
     *
     * In a course context, we are curating nodes from the settingsnav and navigation objects.
     * The following mapping construct specifies which object we are fetching it from, the type of the node, the key
     * and in what order we want the node - defined as per the mockups.
     *
     * @return array
     */
    protected function get_default_course_mapping(): array {
        $nodes = [];
        $nodes['settings'] = [
            self::TYPE_CONTAINER => [
                'coursereports' => 4,
                'questionbank' => 5,
            ],
            self::TYPE_SETTING => [
                'editsettings' => 0,
                'review' => 1.1,
                'manageinstances' => 1.2,
                'groups' => 1.3,
                'override' => 1.4,
                'roles' => 1.5,
                'permissions' => 1.6,
                'otherusers' => 1.7,
                'gradebooksetup' => 2.1,
                'outcomes' => 2.2,
                'coursecompletion' => 7,
                'coursebadges' => 8.1,
                'newbadge' => 8.2,
                'filtermanagement' => 10,
                'unenrolself' => 11,
                'coursetags' => 12,
                'download' => 13,
                'contextlocking' => 14,
            ],
        ];
        $nodes['navigation'] = [
            self::TYPE_CONTAINER => [
                'participants' => 1,
                'courseoverview' => 3,
            ],
            self::TYPE_SETTING => [
                'grades' => 2,
                'badgesview' => 8,
                'competencies' => 9,
                'communication' => 15,
            ],
            self::TYPE_CUSTOM => [
                'contentbank' => 6,
                'participants' => 1, // In site home, 'participants' is classified differently.
            ],
        ];

        return $nodes;
    }

    /**
     * Defines the default structure for the secondary nav in a module context.
     *
     * In a module context, we are curating nodes from the settingsnav object.
     * The following mapping construct specifies the type of the node, the key
     * and in what order we want the node - defined as per the mockups.
     *
     * @return array
     */
    protected function get_default_module_mapping(): array {
        return [
            self::TYPE_SETTING => [
                'modedit' => 1,
                "mod_{$this->page->activityname}_useroverrides" => 3, // Overrides are module specific.
                "mod_{$this->page->activityname}_groupoverrides" => 4,
                'roleassign' => 7.2,
                'filtermanage' => 6,
                'roleoverride' => 7,
                'rolecheck' => 7.1,
                'logreport' => 8,
                'backup' => 9,
                'restore' => 10,
                'competencybreakdown' => 11,
                'sendtomoodlenet' => 16,
            ],
            self::TYPE_CUSTOM => [
                'advgrading' => 2,
                'contentbank' => 12,
            ],
        ];
    }

    /**
     * Defines the default structure for the secondary nav in a category context.
     *
     * In a category context, we are curating nodes from the settingsnav object.
     * The following mapping construct specifies the type of the node, the key
     * and in what order we want the node - defined as per the mockups.
     *
     * @return array
     */
    protected function get_default_category_mapping(): array {
        return [
            self::TYPE_SETTING => [
                'edit' => 1,
                'permissions' => 2,
                'roles' => 2.1,
                'rolecheck' => 2.2,
            ]
        ];
    }

    /**
     * Define the keys of the course secondary nav nodes that should be forced into the "more" menu by default.
     *
     * @return array
     */
    protected function get_default_category_more_menu_nodes(): array {
        return ['addsubcat', 'roles', 'permissions', 'contentbank', 'cohort', 'filters', 'restorecourse'];
    }
    /**
     * Define the keys of the course secondary nav nodes that should be forced into the "more" menu by default.
     *
     * @return array
     */
    protected function get_default_course_more_menu_nodes(): array {
        return [];
    }

    /**
     * Define the keys of the module secondary nav nodes that should be forced into the "more" menu by default.
     *
     * @return array
     */
    protected function get_default_module_more_menu_nodes(): array {
        return ['roleoverride', 'rolecheck', 'logreport', 'roleassign', 'filtermanage', 'backup', 'restore',
            'competencybreakdown', "mod_{$this->page->activityname}_useroverrides",
            "mod_{$this->page->activityname}_groupoverrides"];
    }

    /**
     * Define the keys of the admin secondary nav nodes that should be forced into the "more" menu by default.
     *
     * @return array
     */
    protected function get_default_admin_more_menu_nodes(): array {
        return [];
    }

    /**
     * Initialise the view based navigation based on the current context.
     *
     * As part of the initial restructure, the secondary nav is only considered for the following pages:
     * 1 - Site admin settings
     * 2 - Course page - Does not include front_page which has the same context.
     * 3 - Module page
     */
    public function initialise(): void {
        global $SITE;

        if (during_initial_install() || $this->initialised) {
            return;
        }
        $this->id = 'secondary_navigation';
        $context = $this->context;
        $this->headertitle = get_string('menu');
        $defaultmoremenunodes = [];
        $maxdisplayednodes = self::MAX_DISPLAYED_NAV_NODES;

        switch ($context->contextlevel) {
            case CONTEXT_COURSE:
                $this->headertitle = get_string('courseheader');
                if ($this->page->course->format === 'singleactivity') {
                    $this->load_single_activity_course_navigation();
                } else {
                    $this->load_course_navigation();
                    $defaultmoremenunodes = $this->get_default_course_more_menu_nodes();
                }
                break;
            case CONTEXT_MODULE:
                $this->headertitle = get_string('activityheader');
                if ($this->page->course->format === 'singleactivity') {
                    $this->load_single_activity_course_navigation();
                } else {
                    $this->load_module_navigation($this->page->settingsnav);
                    $defaultmoremenunodes = $this->get_default_module_more_menu_nodes();
                }
                break;
            case CONTEXT_COURSECAT:
                $this->headertitle = get_string('categoryheader');
                $this->load_category_navigation();
                $defaultmoremenunodes = $this->get_default_category_more_menu_nodes();
                break;
            case CONTEXT_SYSTEM:
                $this->headertitle = get_string('homeheader');
                $this->load_admin_navigation();
                // If the site administration navigation was generated after load_admin_navigation().
                if ($this->has_children()) {
                    // Do not explicitly limit the number of navigation nodes displayed in the site administration
                    // navigation menu.
                    $maxdisplayednodes = null;
                }
                $defaultmoremenunodes = $this->get_default_admin_more_menu_nodes();
                break;
        }

        $this->remove_unwanted_nodes($this);

        // Don't need to show anything if only the view node is available. Remove it.
        if ($this->children->count() == 1) {
            $this->children->remove('modulepage');
        }
        // Force certain navigation nodes to be displayed in the "more" menu.
        $this->force_nodes_into_more_menu($defaultmoremenunodes, $maxdisplayednodes);
        // Search and set the active node.
        $this->scan_for_active_node($this);
        $this->initialised = true;
    }

    /**
     * Returns a node with the action being from the first found child node that has an action (Recursive).
     *
     * @param navigation_node $node The part of the node tree we are checking.
     * @param navigation_node $basenode  The very first node to be used for the return.
     * @return navigation_node|null
     */
    protected function get_node_with_first_action(navigation_node $node, navigation_node $basenode): ?navigation_node {
        $newnode = null;
        if (!$node->has_children()) {
            return null;
        }

        // Find the first child with an action and update the main node.
        foreach ($node->children as $child) {
            if ($child->has_action()) {
                $newnode = $basenode;
                $newnode->action = $child->action;
                return $newnode;
            }
        }
        if (is_null($newnode)) {
            // Check for children and go again.
            foreach ($node->children as $child) {
                if ($child->has_children()) {
                    $newnode = $this->get_node_with_first_action($child, $basenode);

                    if (!is_null($newnode)) {
                        return $newnode;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Some nodes are containers only with no action. If this container has an action then nothing is done. If it does not have
     * an action then a search is done through the children looking for the first node that has an action. This action is then given
     * to the parent node that is initially provided as a parameter.
     *
     * @param navigation_node $node The navigation node that we want to ensure has an action tied to it.
     * @return navigation_node The node intact with an action to use.
     */
    protected function get_first_action_for_node(navigation_node $node): ?navigation_node {
        // If the node does not have children and has no action then no further processing is needed.
        $newnode = null;
        if ($node->has_children() && !$node->has_action()) {
            // We want to find the first child with an action.
            // We want to check all children on this level before going further down.
            // Note that new node gets changed here.
            $newnode = $this->get_node_with_first_action($node, $node);
        } else if ($node->has_action()) {
            $newnode = $node;
        }
        return $newnode;
    }

    /**
     * Recursive call to add all custom navigation nodes to secondary
     *
     * @param navigation_node $node The node which should be added to secondary
     * @param navigation_node $basenode The original parent node
     * @param navigation_node|null $root The parent node nodes are to be added/removed to.
     * @param bool $forceadd Whether or not to bypass the external action check and force add all nodes
     */
    protected function add_external_nodes_to_secondary(navigation_node $node, navigation_node $basenode,
           ?navigation_node $root = null, bool $forceadd = false) {
        $root = $root ?? $this;
        // Add the first node.
        if ($node->has_action() && !$this->get($node->key)) {
            $root->add_node(clone $node);
        }

        // If the node has an external action add all children to the secondary navigation.
        if (!$node->has_internal_action() || $forceadd) {
            if ($node->has_children()) {
                foreach ($node->children as $child) {
                    if ($child->has_children()) {
                        $this->add_external_nodes_to_secondary($child, $basenode, $root, true);
                    } else if ($child->has_action() && !$this->get($child->key)) {
                        // Check whether the basenode matches a child's url.
                        // This would have happened in get_first_action_for_node.
                        // In these cases, we prefer the specific child content.
                        if ($basenode->has_action() && $basenode->action()->compare($child->action())) {
                            $root->children->remove($basenode->key, $basenode->type);
                        }
                        $root->add_node(clone $child);
                    }
                }
            }
        }
    }

    /**
     * Returns a list of all expected nodes in the course administration.
     *
     * @return array An array of keys for navigation nodes in the course administration.
     */
    protected function get_expected_course_admin_nodes(): array {
        $expectednodes = [];
        foreach ($this->get_default_course_mapping()['settings'] as $value) {
            foreach ($value as $nodekey => $notused) {
                $expectednodes[] = $nodekey;
            }
        }
        foreach ($this->get_default_course_mapping()['navigation'] as $value) {
            foreach ($value as $nodekey => $notused) {
                $expectednodes[] = $nodekey;
            }
        }
        $othernodes = ['users', 'gradeadmin', 'coursereports', 'coursebadges'];
        $leftovercourseadminnodes = ['backup', 'restore', 'import', 'copy', 'reset'];
        $expectednodes = array_merge($expectednodes, $othernodes);
        $expectednodes = array_merge($expectednodes, $leftovercourseadminnodes);
        return $expectednodes;
    }

    /**
     * Load the course secondary navigation. Since we are sourcing all the info from existing objects that already do
     * the relevant checks, we don't do it again here.
     *
     * @param navigation_node|null $rootnode The node where the course navigation nodes should be added into as children.
     *                                       If not explicitly defined, the nodes will be added to the secondary root
     *                                       node by default.
     */
    protected function load_course_navigation(?navigation_node $rootnode = null): void {
        global $SITE;

        $rootnode = $rootnode ?? $this;
        $course = $this->page->course;
        // Initialise the main navigation and settings nav.
        // It is important that this is done before we try anything.
        $settingsnav = $this->page->settingsnav;
        $navigation = $this->page->navigation;

        if ($course->id == $SITE->id) {
            $firstnodeidentifier = get_string('home'); // The first node in the site course nav is called 'Home'.
            $frontpage = $settingsnav->get('frontpage'); // The site course nodes are children of a dedicated 'frontpage' node.
            $settingsnav = $frontpage ?: $settingsnav;
            $courseadminnode = $frontpage ?: null; // Custom nodes for the site course are also children of the 'frontpage' node.
        } else {
            $firstnodeidentifier = get_string('course'); // Regular courses have a first node called 'Course'.
            $courseadminnode = $settingsnav->get('courseadmin'); // Custom nodes for regular courses live under 'courseadmin'.
        }

        // Add the known nodes from settings and navigation.
        $nodes = $this->get_default_course_mapping();
        $nodesordered = $this->get_leaf_nodes($settingsnav, $nodes['settings'] ?? []);
        $nodesordered += $this->get_leaf_nodes($navigation, $nodes['navigation'] ?? []);
        $this->add_ordered_nodes($nodesordered, $rootnode);

        // Try to get any custom nodes defined by plugins, which may include containers.
        if ($courseadminnode) {
            $expectedcourseadmin = $this->get_expected_course_admin_nodes();
            foreach ($courseadminnode->children as $other) {
                if (array_search($other->key, $expectedcourseadmin, true) === false) {
                    $othernode = $this->get_first_action_for_node($other);
                    $recursivenode = $othernode && !$rootnode->get($othernode->key) ? $othernode : $other;
                    // Get the first node and check whether it's been added already.
                    // Also check if the first node is an external link. If it is, add all children.
                    $this->add_external_nodes_to_secondary($recursivenode, $recursivenode, $rootnode);
                }
            }
        }

        // Add the respective first node, provided there are other nodes included.
        if (!empty($nodekeys = $rootnode->children->get_key_list())) {
            $rootnode->add_node(
                navigation_node::create($firstnodeidentifier, new \moodle_url('/course/view.php', ['id' => $course->id]),
                    self::TYPE_COURSE, null, 'coursehome'), reset($nodekeys)
            );
        }

        // Allow plugins to add nodes to the secondary navigation.
        $hook = new \core\hook\navigation\secondary_extend($this);
        \core\di::get(\core\hook\manager::class)->dispatch($hook);
    }

    /**
     * Gets the overflow navigation nodes for the course administration category.
     *
     * @param navigation_node|null $rootnode The node from where the course overflow nodes should be obtained.
     *                                       If not explicitly defined, the nodes will be obtained from the secondary root
     *                                       node by default.
     * @return navigation_node  The course overflow nodes.
     */
    protected function get_course_overflow_nodes(?navigation_node $rootnode = null): ?navigation_node {
        global $SITE;

        $rootnode = $rootnode ?? $this;
        // This gets called twice on some pages, and so trying to create this navigation node twice results in no children being
        // present the second time this is called.
        if (isset($this->courseoverflownode)) {
            return $this->courseoverflownode;
        }

        // Start with getting the base node for the front page or the course.
        $node = null;
        if ($this->page->course->id == $SITE->id) {
            $node = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
        } else {
            $node = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
        }
        $coursesettings = $node ? $node->get_children_key_list() : [];
        $thissettings = $rootnode->get_children_key_list();
        $diff = array_diff($coursesettings, $thissettings);

        // Remove our specific created elements (user - participants, badges - coursebadges, grades - gradebooksetup,
        // grades - outcomes).
        $shortdiff = array_filter($diff, function($value) {
            return !($value == 'users' || $value == 'coursebadges' || $value == 'gradebooksetup' ||
                $value == 'outcomes');
        });

        // Permissions may be in play here that ultimately will show no overflow.
        if (empty($shortdiff)) {
            return null;
        }

        $firstitem = array_shift($shortdiff);
        $navnode = $node->get($firstitem);
        foreach ($shortdiff as $key) {
            $courseadminnodes = $node->get($key);
            if ($courseadminnodes) {
                if ($courseadminnodes->parent->key == $node->key) {
                    $navnode->add_node($courseadminnodes);
                }
            }
        }
        $this->courseoverflownode = $navnode;
        return $navnode;

    }

    /**
     * Recursively looks for a match to the current page url.
     *
     * @param navigation_node $node The node to look through.
     * @return navigation_node|null The node that matches this page's url.
     */
    protected function nodes_match_current_url(navigation_node $node): ?navigation_node {
        $pagenode = $this->page->url;
        if ($node->has_action()) {
            // Check this node first.
            if ($node->action->compare($pagenode)) {
                return $node;
            }
        }
        if ($node->has_children()) {
            foreach ($node->children as $child) {
                $result = $this->nodes_match_current_url($child);
                if ($result) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * Recursively search a node and its children for a node matching the key string $key.
     *
     * @param navigation_node $node the navigation node to check.
     * @param string $key the key of the node to match.
     * @return navigation_node|null node if found, otherwise null.
     */
    protected function node_matches_key_string(navigation_node $node, string $key): ?navigation_node {
        if ($node->has_action()) {
            // Check this node first.
            if ($node->key == $key) {
                return $node;
            }
        }
        if ($node->has_children()) {
            foreach ($node->children as $child) {
                $result = $this->node_matches_key_string($child, $key);
                if ($result) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * Force a specific node in the 'coursereuse' course overflow to be selected, based on the provided node key.
     *
     * Normally, the selected node is determined by matching the page URL to the node URL. E.g. The page 'backup/restorefile.php'
     * will match the "Restore" node which has a registered URL of 'backup/restorefile.php' because the URLs match.
     *
     * This method allows a page to choose a specific node to match, which is useful in cases where the page knows its URL won't
     * match the node it needs to reside under. I.e. this permits several pages to 'share' the same overflow node. When the page
     * knows the PAGE->url won't match the node URL, the page can simply say "I want to match the 'XXX' node".
     *
     * E.g.
     * - The $PAGE->url is 'backup/restore.php' (this page is used during restores but isn't the main landing page for a restore)
     * - The 'Restore' node in the overflow has a key of 'restore' and will only match 'backup/restorefile.php' by default (the
     * main restore landing page).
     * - The backup/restore.php page calls:
     * $PAGE->secondarynav->set_overflow_selected_node(new moodle_url('restore');
     * and when the page is loaded, the 'Restore' node be presented as the selected node.
     *
     * @param string $nodekey The string key of the overflow node to match.
     */
    public function set_overflow_selected_node(string $nodekey): void {
        $this->overflowselected = $nodekey;
    }

    /**
     * Returns a url_select object with overflow navigation nodes.
     * This looks to see if the current page is within the course administration, or some other page that requires an overflow
     * select object.
     *
     * @return url_select|null The overflow menu data.
     */
    public function get_overflow_menu_data(): ?url_select {

        if (!$this->page->get_navigation_overflow_state()) {
            return null;
        }

        $issingleactivitycourse = $this->page->course->format === 'singleactivity';
        $rootnode = $issingleactivitycourse ? $this->find('course', self::TYPE_COURSE) : $this;
        $activenode = $this->find_active_node();
        $incourseadmin = false;

        $activeleafnode = $this->page->settingsnav->find_active_node();
        $parentnode = $activeleafnode->parent ?? null;
        if ($issingleactivitycourse && $parentnode && $parentnode->key === 'quiz_report') {
            $activenode = $parentnode;
        }

        if (!$activenode || ($issingleactivitycourse && $activenode->key === 'course')) {
            // Could be in the course admin section.
            $courseadmin = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if (!$courseadmin) {
                return null;
            }

            $activenode = $courseadmin->find_active_node();
            if (!$activenode) {
                return null;
            }
            $incourseadmin = true;
        }

        if ($activenode->key === 'coursereuse' || $incourseadmin) {
            $courseoverflownode = $this->get_course_overflow_nodes($rootnode);
            if (is_null($courseoverflownode)) {
                return null;
            }
            if ($incourseadmin) {
                // Validate whether the active node is part of the expected course overflow nodes.
                if (($activenode->key !== $courseoverflownode->key) &&
                    !$courseoverflownode->find($activenode->key, $activenode->type)) {
                    return null;
                }
            }
            $menuarray = static::create_menu_element([$courseoverflownode]);
            if ($activenode->key != 'coursereuse') {
                $inmenu = false;
                foreach ($menuarray as $key => $value) {
                    if ($this->page->url->out(false) == $key) {
                        $inmenu = true;
                    }
                }
                if (!$inmenu) {
                    return null;
                }
            }
            // If the page has explicitly set the overflow node it would like selected, find and use that node.
            if ($this->overflowselected) {
                $selectedoverflownode = $this->node_matches_key_string($courseoverflownode, $this->overflowselected);
                $selectedoverflownodeurl = $selectedoverflownode ? $selectedoverflownode->action->out(false) : null;
            }

            $menuselect = new url_select($menuarray, $selectedoverflownodeurl ?? $this->page->url, null);
            $menuselect->set_label(get_string('browsecourseadminindex', 'course'), ['class' => 'visually-hidden']);
            return $menuselect;
        } else {
            return $this->get_other_overflow_menu_data($activenode);
        }
    }

    /**
     * Gets overflow menu data for third party plugin settings.
     *
     * @param navigation_node $activenode The node to gather the children for to put into the overflow menu.
     * @return url_select|null The overflow menu in a url_select object.
     */
    protected function get_other_overflow_menu_data(navigation_node $activenode): ?url_select {
        if (!$activenode->has_action()) {
            return null;
        }

        if (!$activenode->has_children()) {
            return null;
        }

        // If the setting is extending the course navigation then the page being redirected to should be in the course context.
        // It was decided on the issue that put this code here that plugins that extend the course navigation should have the pages
        // that are redirected to, be in the course context or module context depending on which callback was used.
        // Third part plugins were checked to see if any existing plugins had settings in a system context and none were found.
        // The request of third party developers is to keep their settings within the specified context.
        if ($this->page->context->contextlevel != CONTEXT_COURSE
                && $this->page->context->contextlevel != CONTEXT_MODULE
                && $this->page->context->contextlevel != CONTEXT_COURSECAT) {
            return null;
        }

        // These areas have their own code to retrieve added plugin navigation nodes.
        if ($activenode->key == 'coursehome' || $activenode->key == 'questionbank' || $activenode->key == 'coursereports') {
            return null;
        }

        $menunode = $this->page->settingsnav->find($activenode->key, null);

        if (!$menunode instanceof navigation_node) {
            return null;
        }
        // Loop through all children and try and find a match to the current url.
        $matchednode = $this->nodes_match_current_url($menunode);
        if (is_null($matchednode)) {
            return null;
        }
        if (!isset($menunode) || !$menunode->has_children()) {
            return null;
        }
        $selectdata = static::create_menu_element([$menunode], false);
        $urlselect = new url_select($selectdata, $matchednode->action->out(false), null);
        $urlselect->set_label(get_string('browsesettingindex', 'course'), ['class' => 'visually-hidden']);
        return $urlselect;
    }

    /**
     * Get the module's secondary navigation. This is based on settings_nav and would include plugin nodes added via
     * '_extend_settings_navigation'.
     * It populates the tree based on the nav mockup
     *
     * If nodes change, we will have to explicitly call the callback again.
     *
     * @param settings_navigation $settingsnav The settings navigation object related to the module page
     * @param navigation_node|null $rootnode The node where the module navigation nodes should be added into as children.
     *                                       If not explicitly defined, the nodes will be added to the secondary root
     *                                       node by default.
     */
    protected function load_module_navigation(settings_navigation $settingsnav, ?navigation_node $rootnode = null): void {
        $rootnode = $rootnode ?? $this;
        $mainnode = $settingsnav->find('modulesettings', self::TYPE_SETTING);
        $nodes = $this->get_default_module_mapping();

        if ($mainnode) {
            $url = new \moodle_url('/mod/' . $settingsnav->get_page()->activityname . '/view.php',
                ['id' => $settingsnav->get_page()->cm->id]);
            $setactive = $url->compare($settingsnav->get_page()->url, URL_MATCH_BASE);
            $node = $rootnode->add(get_string('modulename', $settingsnav->get_page()->activityname), $url,
                null, null, 'modulepage');
            if ($setactive) {
                $node->make_active();
            }
            // Add the initial nodes.
            $nodesordered = $this->get_leaf_nodes($mainnode, $nodes);
            $this->add_ordered_nodes($nodesordered, $rootnode);

            // We have finished inserting the initial structure.
            // Populate the menu with the rest of the nodes available.
            $this->load_remaining_nodes($mainnode, $nodes, $rootnode);
        }
    }

    /**
     * Load the course category navigation.
     */
    protected function load_category_navigation(): void {
        $settingsnav = $this->page->settingsnav;
        $mainnode = $settingsnav->find('categorysettings', self::TYPE_CONTAINER);
        $nodes = $this->get_default_category_mapping();

        if ($mainnode) {
            $url = new \moodle_url('/course/index.php', ['categoryid' => $this->context->instanceid]);
            $this->add(get_string('category'), $url, self::TYPE_CONTAINER, null, 'categorymain');

            // Add the initial nodes.
            $nodesordered = $this->get_leaf_nodes($mainnode, $nodes);
            $this->add_ordered_nodes($nodesordered);

            // We have finished inserting the initial structure.
            // Populate the menu with the rest of the nodes available.
            $this->load_remaining_nodes($mainnode, $nodes);
        }
    }

    /**
     * Load the site admin navigation
     */
    protected function load_admin_navigation(): void {
        global $PAGE, $SITE;

        $settingsnav = $this->page->settingsnav;
        $node = $settingsnav->find('root', self::TYPE_SITE_ADMIN);
        // We need to know if we are on the main site admin search page. Here the navigation between tabs are done via
        // anchors and page reload doesn't happen. On every nested admin settings page, the secondary nav needs to
        // exist as links with anchors appended in order to redirect back to the admin search page and the corresponding
        // tab. Note this value refers to being present on the page itself, before a search has been performed.
        $isadminsearchpage = $PAGE->url->compare(new \moodle_url('/admin/search.php', ['query' => '']), URL_MATCH_PARAMS);
        if ($node) {
            $siteadminnode = $this->add(get_string('general'), "#link$node->key", null, null, 'siteadminnode');
            if ($isadminsearchpage) {
                $siteadminnode->action = false;
                $siteadminnode->tab = "#link$node->key";
            } else {
                $siteadminnode->action = new \moodle_url("/admin/search.php", [], "link$node->key");
            }
            foreach ($node->children as $child) {
                if ($child->display && !$child->is_short_branch()) {
                    // Mimic the current boost behaviour and pass down anchors for the tabs.
                    if ($isadminsearchpage) {
                        $child->action = false;
                        $child->tab = "#link$child->key";
                    } else {
                        $child->action = new \moodle_url("/admin/search.php", [], "link$child->key");
                    }
                    $this->add_node(clone $child);
                } else {
                    $siteadminnode->add_node(clone $child);
                }
            }
        }
    }

    /**
     * Adds the indexed nodes to the current view or a given node. The key should indicate it's position in the tree.
     * Any sub nodes needs to be numbered appropriately, e.g. 3.1 would make the identified node be listed  under #3 node.
     *
     * @param array $nodes An array of navigation nodes to be added.
     * @param navigation_node|null $rootnode The node where the nodes should be added into as children. If not explicitly
     *                                       defined, the nodes will be added to the secondary root node by default.
     */
    protected function add_ordered_nodes(array $nodes, ?navigation_node $rootnode = null): void {
        $rootnode = $rootnode ?? $this;
        ksort($nodes);
        foreach ($nodes as $key => $node) {
            // If the key is a string then we are assuming this is a nested element.
            if (is_string($key)) {
                $parentnode = $nodes[floor($key)] ?? null;
                if ($parentnode) {
                    $parentnode->add_node(clone $node);
                }
            } else {
                $rootnode->add_node(clone $node);
            }
        }
    }

    /**
     * Find the remaining nodes that need to be loaded into secondary based on the current context or a given node.
     *
     * @param navigation_node $completenode The original node that we are sourcing information from
     * @param array           $nodesmap The map used to populate secondary nav in the given context
     * @param navigation_node|null $rootnode The node where the remaining nodes should be added into as children. If not
     *                                       explicitly defined, the nodes will be added to the secondary root node by
     *                                       default.
     */
    protected function load_remaining_nodes(navigation_node $completenode, array $nodesmap,
            ?navigation_node $rootnode = null): void {
        $flattenednodes = [];
        $rootnode = $rootnode ?? $this;
        foreach ($nodesmap as $nodecontainer) {
            $flattenednodes = array_merge(array_keys($nodecontainer), $flattenednodes);
        }

        $populatedkeys = $this->get_children_key_list();
        $existingkeys = $completenode->get_children_key_list();
        $leftover = array_diff($existingkeys, $populatedkeys);
        foreach ($leftover as $key) {
            if (!in_array($key, $flattenednodes, true) && $leftovernode = $completenode->get($key)) {
                // Check for nodes with children and potentially no action to direct to.
                if ($leftovernode->has_children()) {
                    $leftovernode = $this->get_first_action_for_node($leftovernode);
                }

                // We have found the first node with an action.
                if ($leftovernode) {
                    $this->add_external_nodes_to_secondary($leftovernode, $leftovernode, $rootnode);
                }
            }
        }
    }

    /**
     * Force certain secondary navigation nodes to be displayed in the "more" menu.
     *
     * @param array $defaultmoremenunodes Array with navigation node keys of the pre-defined nodes that
     *                                    should be added into the "more" menu by default
     * @param int|null $maxdisplayednodes The maximum limit of navigation nodes displayed in the secondary navigation
     */
    protected function force_nodes_into_more_menu(array $defaultmoremenunodes = [], ?int $maxdisplayednodes = null) {
        // Counter of the navigation nodes that are initially displayed in the secondary nav
        // (excludes the nodes from the "more" menu).
        $displayednodescount = 0;
        foreach ($this->children as $child) {
            // Skip if the navigation node has been already forced into the "more" menu.
            if ($child->forceintomoremenu) {
                continue;
            }
            // If the navigation node is in the pre-defined list of nodes that should be added by default in the
            // "more" menu or the maximum limit of displayed navigation nodes has been reached (if defined).
            if (in_array($child->key, $defaultmoremenunodes) ||
                    (!is_null($maxdisplayednodes) && $displayednodescount >= $maxdisplayednodes)) {
                // Force the node and its children into the "more" menu.
                $child->set_force_into_more_menu(true);
                continue;
            }
            $displayednodescount++;
        }
    }

    /**
     * Recursively remove navigation nodes that should not be displayed in the secondary navigation.
     *
     * @param navigation_node $node The starting navigation node.
     */
    protected function remove_unwanted_nodes(navigation_node $node) {
        foreach ($node->children as $child) {
            if (!$child->showinsecondarynavigation) {
                $child->remove();
                continue;
            }
            if (!empty($child->children)) {
                $this->remove_unwanted_nodes($child);
            }
        }
    }

    /**
     * Takes the given navigation nodes and searches for children and formats it all into an array in a format to be used by a
     * url_select element.
     *
     * @param navigation_node[] $navigationnodes Navigation nodes to format into a menu.
     * @param bool $forceheadings Whether the returned array should be forced to use headings.
     * @return array|null A url select element for navigating through the navigation nodes.
     */
    public static function create_menu_element(array $navigationnodes, bool $forceheadings = false): ?array {
        if (empty($navigationnodes)) {
            return null;
        }

        // If one item, do we put this into a url_select?
        if (count($navigationnodes) < 2) {
            // Check if there are children.
            $navnode = array_shift($navigationnodes);
            $menudata = [];
            if (!$navnode->has_children()) {
                // Just one item.
                if (!$navnode->has_action()) {
                    return null;
                }
                $menudata[$navnode->action->out(false)] = static::format_node_text($navnode);
            } else {
                if (static::does_menu_need_headings($navnode) || $forceheadings) {
                    // Let's do headings.
                    $menudata = static::get_headings_nav_array($navnode);
                } else {
                    // Simple flat nav.
                    $menudata = static::get_flat_nav_array($navnode);
                }
            }
            return $menudata;
        } else {
            // We have more than one navigation node to handle. Put each node in it's own heading.
            $menudata = [];
            $titledata = [];
            foreach ($navigationnodes as $navigationnode) {
                if ($navigationnode->has_children()) {
                    $menuarray = [];
                    // Add a heading and flatten out everything else.
                    if ($navigationnode->has_action()) {
                        $menuarray[static::format_node_text($navigationnode)][$navigationnode->action->out(false)] =
                            static::format_node_text($navigationnode);
                        $menuarray[static::format_node_text($navigationnode)] += static::get_whole_tree_flat($navigationnode);
                    } else {
                        $menuarray[static::format_node_text($navigationnode)] = static::get_whole_tree_flat($navigationnode);
                    }

                    $titledata += $menuarray;
                } else {
                    // Add with no heading.
                    if (!$navigationnode->has_action()) {
                        return null;
                    }
                    $menudata[$navigationnode->action->out(false)] = static::format_node_text($navigationnode);
                }
            }
            $menudata += [$titledata];
            return $menudata;
        }
    }

    /**
     * Recursively goes through the provided navigation node and returns a flat version.
     *
     * @param navigation_node $navigationnode The navigationnode.
     * @return array The whole tree flat.
     */
    protected static function get_whole_tree_flat(navigation_node $navigationnode): array {
        $nodes = [];
        foreach ($navigationnode->children as $child) {
            if ($child->has_action()) {
                $nodes[$child->action->out()] = $child->text;
            }
            if ($child->has_children()) {
                $childnodes = static::get_whole_tree_flat($child);
                $nodes = array_merge($nodes, $childnodes);
            }
        }
        return $nodes;
    }

    /**
     * Checks to see if the provided navigation node has children and determines if we want headings for a url select element.
     *
     * @param navigation_node  $navigationnode  The navigation node we are checking.
     * @return bool Whether we want headings or not.
     */
    protected static function does_menu_need_headings(navigation_node $navigationnode): bool {
        if (!$navigationnode->has_children()) {
            return false;
        }
        foreach ($navigationnode->children as $child) {
            if ($child->has_children()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Takes the navigation node and returns it in a flat fashion. This is not recursive.
     *
     * @param navigation_node $navigationnode The navigation node that we want to format into an array in a flat structure.
     * @return array The flat navigation array.
     */
    protected static function get_flat_nav_array(navigation_node $navigationnode): array {
        $menuarray = [];
        if ($navigationnode->has_action()) {
            $menuarray[$navigationnode->action->out(false)] = static::format_node_text($navigationnode);
        }

        foreach ($navigationnode->children as $child) {
            if ($child->has_action()) {
                $menuarray[$child->action->out(false)] = static::format_node_text($child);
            }
        }
        return $menuarray;
    }

    /**
     * For any navigation node that we have determined needs headings we return a more tree like array structure.
     *
     * @param navigation_node $navigationnode The navigation node to use for the formatted array structure.
     * @return array The headings navigation array structure.
     */
    protected static function get_headings_nav_array(navigation_node $navigationnode): array {
        $menublock = [];
        // We know that this single node has headings, so grab this for the first heading.
        $firstheading = [];
        if ($navigationnode->has_action()) {
            $firstheading[static::format_node_text($navigationnode)][$navigationnode->action->out(false)] =
                static::format_node_text($navigationnode);
            $firstheading[static::format_node_text($navigationnode)] += static::get_more_child_nodes($navigationnode, $menublock);
        } else {
            $firstheading[static::format_node_text($navigationnode)] = static::get_more_child_nodes($navigationnode, $menublock);
        }
         return [$firstheading + $menublock];
    }

    /**
     * Recursively goes and gets all children nodes.
     *
     * @param navigation_node $node The node to get the children of.
     * @param array $menublock Used to put all child nodes in its own container.
     * @return array The additional child nodes.
     */
    protected static function get_more_child_nodes(navigation_node $node, array &$menublock): array {
        $nodes = [];
        foreach ($node->children as $child) {
            if (!$child->has_children()) {
                if (!$child->has_action()) {
                    continue;
                }
                $nodes[$child->action->out(false)] = static::format_node_text($child);
            } else {
                $newarray = [];
                if ($child->has_action()) {
                    $newarray[static::format_node_text($child)][$child->action->out(false)] = static::format_node_text($child);
                    $newarray[static::format_node_text($child)] += static::get_more_child_nodes($child, $menublock);
                } else {
                    $newarray[static::format_node_text($child)] = static::get_more_child_nodes($child, $menublock);
                }
                $menublock += $newarray;
            }
        }
        return $nodes;
    }

    /**
     * Returns the navigation node text in a string.
     *
     * @param navigation_node $navigationnode The navigationnode to return the text string of.
     * @return string The navigation node text string.
     */
    protected static function format_node_text(navigation_node $navigationnode): string {
        return (is_a($navigationnode->text, 'lang_string')) ? $navigationnode->text->out() : $navigationnode->text;
    }

    /**
     * Load the single activity course secondary navigation.
     */
    protected function load_single_activity_course_navigation(): void {
        $page = $this->page;
        $course = $page->course;

        // Create 'Course' navigation node.
        $coursesecondarynode = navigation_node::create(get_string('course'), null, self::TYPE_COURSE, null, 'course');
        $this->load_course_navigation($coursesecondarynode);
        // Remove the unnecessary 'Course' child node generated in load_course_navigation().
        $coursehomenode = $coursesecondarynode->find('coursehome', self::TYPE_COURSE);
        if (!empty($coursehomenode)) {
            $coursehomenode->remove();
        }

        // Add the 'Course' node to the secondary navigation only if this node has children nodes.
        if (count($coursesecondarynode->children) > 0) {
            $this->add_node($coursesecondarynode);
            // Once all the items have been added to the 'Course' secondary navigation node, set the 'showchildreninsubmenu'
            // property to true. This is required to force the template to output these items within a dropdown menu.
            $coursesecondarynode->showchildreninsubmenu = true;
        }

        // Create 'Activity' navigation node.
        $activitysecondarynode = navigation_node::create(get_string('activity'), null, self::TYPE_ACTIVITY, null, 'activity');

        // We should display the module related navigation in the course context as well. Therefore, we need to
        // re-initialize the page object and manually set the course module to the one that it is currently visible in
        // the course in order to obtain the required module settings navigation.
        if ($page->context instanceof \context_course) {
            $this->page->set_secondary_active_tab($coursesecondarynode->key);
            // Get the currently used module in course.
            $format = course_get_format($course);
            if ($format instanceof \core_courseformat\main_activity_interface) {
                $module = $format->get_main_activity();
            } else {
                $module = current(array_filter(get_course_mods($course->id), function ($module) {
                    return $module->visible == 1;
                }));
            }

            // If the default module for the single course format has not been set yet, skip displaying the module
            // related navigation in the secondary navigation.
            if (!$module) {
                return;
            }
            $page = new \moodle_page();
            $page->set_cm($module, $course);
            $page->set_url(new \moodle_url('/mod/' . $page->activityname . '/view.php', ['id' => $page->cm->id]));
        }

        $this->load_module_navigation($page->settingsnav, $activitysecondarynode);

        // Add the 'Activity' node to the secondary navigation only if this node has more that one child node.
        if (count($activitysecondarynode->children) > 1) {
            // Set the 'showchildreninsubmenu' property to true to later output the the module navigation items within
            // a dropdown menu.
            $activitysecondarynode->showchildreninsubmenu = true;
            $this->add_node($activitysecondarynode);
            if ($this->context instanceof \context_module) {
                $this->page->set_secondary_active_tab($activitysecondarynode->key);
            }
        } else { // Otherwise, add the 'View activity' node to the secondary navigation.
            $viewactivityurl = new \moodle_url('/mod/' . $page->activityname . '/view.php', ['id' => $page->cm->id]);
            $this->add(get_string('modulename', $page->activityname), $viewactivityurl, null, null, 'modulepage');
            if ($this->context instanceof \context_module) {
                $this->page->set_secondary_active_tab('modulepage');
            }
        }
    }
}
