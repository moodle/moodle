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

use core\context_helper;
use core\exception\coding_exception;
use core\output\action_link;
use core\output\pix_icon;
use core\output\renderable;
use core\output\tabobject;
use core\url;

/**
 * This class is used to represent a node in a navigation tree
 *
 * This class is used to represent a node in a navigation tree within Moodle,
 * the tree could be one of global navigation, settings navigation, or the navbar.
 * Each node can be one of two types either a Leaf (default) or a branch.
 * When a node is first created it is created as a leaf, when/if children are added
 * the node then becomes a branch.
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_node implements renderable {
    /** @var int Used to identify this node a leaf (default) 0 */
    public const NODETYPE_LEAF = 0;
    /** @var int Used to identify this node a branch, happens with children  1 */
    public const NODETYPE_BRANCH = 1;
    /** @var null Unknown node type null */
    public const TYPE_UNKNOWN = null;
    /** @var int System node type 0 */
    public const TYPE_ROOTNODE = 0;
    /** @var int System node type 1 */
    public const TYPE_SYSTEM = 1;
    /** @var int Category node type 10 */
    public const TYPE_CATEGORY = 10;
    /** var int Category displayed in MyHome navigation node */
    public const TYPE_MY_CATEGORY = 11;
    /** @var int Course node type 20 */
    public const TYPE_COURSE = 20;
    /** @var int Course Structure node type 30 */
    public const TYPE_SECTION = 30;
    /** @var int Activity node type, e.g. Forum, Quiz 40 */
    public const TYPE_ACTIVITY = 40;
    /** @var int Resource node type, e.g. Link to a file, or label 50 */
    public const TYPE_RESOURCE = 50;
    /** @var int A custom node type, default when adding without specifing type 60 */
    public const TYPE_CUSTOM = 60;
    /** @var int Setting node type, used only within settings nav 70 */
    public const TYPE_SETTING = 70;
    /** @var int site admin branch node type, used only within settings nav 71 */
    public const TYPE_SITE_ADMIN = 71;
    /** @var int Setting node type, used only within settings nav 80 */
    public const TYPE_USER = 80;
    /** @var int Setting node type, used for containers of no importance 90 */
    public const TYPE_CONTAINER = 90;
    /** var int Course the current user is not enrolled in */
    public const COURSE_OTHER = 0;
    /** var int Course the current user is enrolled in but not viewing */
    public const COURSE_MY = 1;
    /** var int Course the current user is currently viewing */
    public const COURSE_CURRENT = 2;
    /** var string The course index page navigation node */
    public const COURSE_INDEX_PAGE = 'courseindexpage';

    /** @var string The name that will be used for the navigation cache */
    protected const CACHE_NAME = 'navigation';

    /** @var string The name that will be used for the site admin navigation cache */
    protected const SITE_ADMIN_CACHE_NAME = 'navigationsiteadmin';

    /** @var int Parameter to aid the coder in tracking [optional] */
    public $id = null;
    /** @var string|int The identifier for the node, used to retrieve the node */
    public $key = null;
    /** @var string|lang_string The text to use for the node */
    public $text = null;
    /** @var string Short text to use if requested [optional] */
    public $shorttext = null;
    /** @var string The title attribute for an action if one is defined */
    public $title = null;
    /** @var string A string that can be used to build a help button */
    public $helpbutton = null;
    /** @var url|action_link|null An action for the node (link) */
    public $action = null;
    /** @var pix_icon The path to an icon to use for this node */
    public $icon = null;
    /** @var int See TYPE_* constants defined for this class */
    public $type = self::TYPE_UNKNOWN;
    /** @var int See NODETYPE_* constants defined for this class */
    public $nodetype = self::NODETYPE_LEAF;
    /** @var bool If set to true the node will be collapsed by default */
    public $collapse = false;
    /** @var bool If set to true the node will be expanded by default */
    public $forceopen = false;
    /** @var array An array of CSS classes for the node */
    public $classes = [];
    /** @var array An array of HTML attributes for the node */
    public $attributes = [];
    /** @var navigation_node_collection An array of child nodes */
    public $children = [];
    /** @var bool If set to true the node will be recognised as active */
    public $isactive = false;
    /** @var bool If set to true the node will be dimmed */
    public $hidden = false;
    /** @var bool If set to false the node will not be displayed */
    public $display = true;
    /** @var bool If set to true then an HR will be printed before the node */
    public $preceedwithhr = false;
    /** @var bool If set to true the the navigation bar should ignore this node */
    public $mainnavonly = false;
    /** @var bool If set to true a title will be added to the action no matter what */
    public $forcetitle = false;
    /** @var navigation_node A reference to the node parent, you should never set this directly you should always call set_parent */
    public $parent = null;
    /** @var bool Override to not display the icon even if one is provided **/
    public $hideicon = false;
    /** @var bool Set to true if we KNOW that this node can be expanded.  */
    public $isexpandable = false;
    /** @var array */
    protected $namedtypes = [0 => 'system', 10 => 'category', 20 => 'course', 30 => 'structure', 40 => 'activity',
                                  50 => 'resource', 60 => 'custom', 70 => 'setting', 71 => 'siteadmin', 80 => 'user',
                                  90 => 'container'];
    /** @var url */
    protected static $fullmeurl = null;
    /** @var bool toogles auto matching of active node */
    public static $autofindactive = true;
    /** @var bool should we load full admin tree or rely on AJAX for performance reasons */
    protected static $loadadmintree = false;
    /** @var mixed If set to an int, that section will be included even if it has no activities */
    public $includesectionnum = false;
    /** @var bool does the node need to be loaded via ajax */
    public $requiresajaxloading = false;
    /** @var bool If set to true this node will be added to the "flat" navigation */
    public $showinflatnavigation = false;
    /** @var bool If set to true this node will be forced into a "more" menu whenever possible */
    public $forceintomoremenu = false;
    /** @var bool If set to true this node will be displayed in the "secondary" navigation when applicable */
    public $showinsecondarynavigation = true;
    /** @var bool If set to true the children of this node will be displayed within a submenu when applicable */
    public $showchildreninsubmenu = false;
    /** @var string tab element ID. */
    public $tab;
    /** @var string unique identifier. */
    public $moremenuid;
    /** @var bool node that have children. */
    public $haschildren;

    /**
     * Constructs a new navigation_node
     *
     * @param array|string $properties Either an array of properties or a string to use
     *                     as the text for the node
     */
    public function __construct($properties) {
        if (is_array($properties)) {
            // Check the array for each property that we allow to set at construction.
            // text         - The main content for the node.
            // shorttext    - A short text if required for the node.
            // icon         - The icon to display for the node.
            // type         - The type of the node.
            // key          - The key to use to identify the node.
            // parent       - A reference to the nodes parent.
            // action       - The action to attribute to this node, usually a URL to link to.
            if (array_key_exists('text', $properties)) {
                $this->text = $properties['text'];
            }
            if (array_key_exists('shorttext', $properties)) {
                $this->shorttext = $properties['shorttext'];
            }
            if (!array_key_exists('icon', $properties)) {
                $properties['icon'] = new pix_icon('i/navigationitem', '');
            }
            $this->icon = $properties['icon'];
            if ($this->icon instanceof pix_icon) {
                if (empty($this->icon->attributes['class'])) {
                    $this->icon->attributes['class'] = 'navicon';
                } else {
                    $this->icon->attributes['class'] .= ' navicon';
                }
            }
            if (array_key_exists('type', $properties)) {
                $this->type = $properties['type'];
            } else {
                $this->type = self::TYPE_CUSTOM;
            }
            if (array_key_exists('key', $properties)) {
                $this->key = $properties['key'];
            }

            // This needs to happen last because of the check_if_active call that occurs.
            if (array_key_exists('action', $properties)) {
                $this->action = $properties['action'];
                if (is_string($this->action)) {
                    $this->action = new url($this->action);
                }
                if (self::$autofindactive) {
                    $this->check_if_active();
                }
            }
            if (array_key_exists('parent', $properties)) {
                $this->set_parent($properties['parent']);
            }
        } else if (is_string($properties)) {
            $this->text = $properties;
        }
        if ($this->text === null) {
            throw new coding_exception('You must set the text for the node when you create it.');
        }
        // Instantiate a new navigation node collection for this nodes children.
        $this->children = new navigation_node_collection();
    }

    /**
     * Checks if this node is the active node.
     *
     * This is determined by comparing the action for the node against the
     * defined URL for the page. A match will see this node marked as active.
     *
     * @param int $strength One of URL_MATCH_EXACT, URL_MATCH_PARAMS, or URL_MATCH_BASE
     * @return bool
     */
    public function check_if_active($strength = URL_MATCH_EXACT) {
        global $FULLME, $PAGE;

        // Set fullmeurl if it hasn't already been set.
        if (self::$fullmeurl == null) {
            if ($PAGE->has_set_url()) {
                self::override_active_url(new url($PAGE->url));
            } else {
                self::override_active_url(new url($FULLME));
            }
        }

        // Compare the action of this node against the fullmeurl.
        if ($this->action instanceof url && $this->action->compare(self::$fullmeurl, $strength)) {
            $this->make_active();
            return true;
        }
        return false;
    }

    /**
     * True if this nav node has siblings in the tree.
     *
     * @return bool
     */
    public function has_siblings() {
        if (empty($this->parent) || empty($this->parent->children)) {
            return false;
        }
        if ($this->parent->children instanceof navigation_node_collection) {
            $count = $this->parent->children->count();
        } else {
            $count = count($this->parent->children);
        }
        return ($count > 1);
    }

    /**
     * Get a list of sibling navigation nodes at the same level as this one.
     *
     * @return bool|array of navigation_node
     */
    public function get_siblings() {
        // Returns a list of the siblings of the current node for display in a flat navigation element. Either
        // the in-page links or the breadcrumb links.
        $siblings = false;

        if ($this->has_siblings()) {
            $siblings = [];
            foreach ($this->parent->children as $child) {
                if ($child->display) {
                    $siblings[] = $child;
                }
            }
        }
        return $siblings;
    }

    /**
     * This sets the URL that the URL of new nodes get compared to when locating the active node.
     *
     * The active node is the node that matches the URL set here. By default this
     * is either $PAGE->url or if that hasn't been set $FULLME.
     *
     * @param url $url The url to use for the fullmeurl.
     * @param bool $loadadmintree use true if the URL point to administration tree
     */
    public static function override_active_url(url $url, $loadadmintree = false) {
        // Clone the URL, in case the calling script changes their URL later.
        self::$fullmeurl = new url($url);
        // True means we do not want AJAX loaded admin tree, required for all admin pages.
        if ($loadadmintree) {
            // Do not change back to false if already set.
            self::$loadadmintree = true;
        }
    }

    /**
     * Require the admin tree.
     *
     * Use when page is linked from the admin tree,
     * if not used navigation could not find the page using current URL
     * because the tree is not fully loaded.
     */
    public static function require_admin_tree() {
        self::$loadadmintree = true;
    }

    /**
     * Creates a navigation node, ready to add it as a child using add_node function.
     *
     * The created node needs to be added before you can use it.
     *
     * @param string $text
     * @param url|action_link $action
     * @param int $type
     * @param string $shorttext
     * @param string|int $key
     * @param pix_icon $icon
     * @return navigation_node
     */
    public static function create(
        $text,
        $action = null,
        $type = self::TYPE_CUSTOM,
        $shorttext = null,
        $key = null,
        ?pix_icon $icon = null
    ) {
        if ($action && !($action instanceof url || $action instanceof action_link)) {
            debugging(
                "It is required that the action provided be either an action_url|url." .
                " Please update your definition.",
                E_NOTICE
            );
        }
        // Properties array used when creating the new navigation node.
        $itemarray = [
            'text' => $text,
            'type' => $type,
        ];
        // Set the action if one was provided.
        if ($action !== null) {
            $itemarray['action'] = $action;
        }
        // Set the shorttext if one was provided.
        if ($shorttext !== null) {
            $itemarray['shorttext'] = $shorttext;
        }
        // Set the icon if one was provided.
        if ($icon !== null) {
            $itemarray['icon'] = $icon;
        }
        // Set the key.
        $itemarray['key'] = $key;
        // Construct and return.
        return new navigation_node($itemarray);
    }

    /**
     * Adds a navigation node as a child of this node.
     *
     * @param string $text
     * @param url|action_link|string $action
     * @param ?int $type
     * @param string $shorttext
     * @param string|int $key
     * @param pix_icon $icon
     * @return navigation_node
     */
    public function add($text, $action = null, $type = self::TYPE_CUSTOM, $shorttext = null, $key = null, ?pix_icon $icon = null) {
        if ($action && is_string($action)) {
            $action = new url($action);
        }
        // Create child node.
        $childnode = self::create($text, $action, $type, $shorttext, $key, $icon);

        // Add the child to end and return.
        return $this->add_node($childnode);
    }

    /**
     * Adds a navigation node as a child of this one, given a $node object
     * created using the create function.
     * @param navigation_node $childnode Node to add
     * @param string $beforekey
     * @return navigation_node The added node
     */
    public function add_node(navigation_node $childnode, $beforekey = null) {
        // First convert the nodetype for this node to a branch as it will now have children.
        if ($this->nodetype !== self::NODETYPE_BRANCH) {
            $this->nodetype = self::NODETYPE_BRANCH;
        }
        // Set the parent to this node.
        $childnode->set_parent($this);

        // Default the key to the number of children if not provided.
        if ($childnode->key === null) {
            $childnode->key = $this->children->count();
        }

        // Add the child using the navigation_node_collections add method.
        $node = $this->children->add($childnode, $beforekey);

        // If added node is a category node or the user is logged in and it's a course
        // then mark added node as a branch (makes it expandable by AJAX).
        $type = $childnode->type;
        if (
            ($type == self::TYPE_CATEGORY) || (isloggedin() && ($type == self::TYPE_COURSE)) || ($type == self::TYPE_MY_CATEGORY) ||
                ($type === self::TYPE_SITE_ADMIN)
        ) {
            $node->nodetype = self::NODETYPE_BRANCH;
        }
        // If this node is hidden mark it's children as hidden also.
        if ($this->hidden) {
            $node->hidden = true;
        }
        // Return added node (reference returned by $this->children->add().
        return $node;
    }

    /**
     * Return a list of all the keys of all the child nodes.
     * @return array the keys.
     */
    public function get_children_key_list() {
        return $this->children->get_key_list();
    }

    /**
     * Searches for a node of the given type with the given key.
     *
     * This searches this node plus all of its children, and their children....
     * If you know the node you are looking for is a child of this node then please
     * use the get method instead.
     *
     * @param int|string $key The key of the node we are looking for
     * @param ?int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function find($key, $type) {
        return $this->children->find($key, $type);
    }

    /**
     * Walk the tree building up a list of all the flat navigation nodes.
     *
     * @deprecated since Moodle 4.0
     * @param flat_navigation $nodes List of the found flat navigation nodes.
     * @param boolean $showdivider Show a divider before the first node.
     * @param string $label A label for the collection of navigation links.
     */
    public function build_flat_navigation_list(flat_navigation $nodes, $showdivider = false, $label = '') {
        debugging("Function has been deprecated with the deprecation of the flat_navigation class.");
        if ($this->showinflatnavigation) {
            $indent = 0;
            if ($this->type == self::TYPE_COURSE || $this->key === self::COURSE_INDEX_PAGE) {
                $indent = 1;
            }
            $flat = new flat_navigation_node($this, $indent);
            $flat->set_showdivider($showdivider, $label);
            $nodes->add($flat);
        }
        foreach ($this->children as $child) {
            $child->build_flat_navigation_list($nodes, false);
        }
    }

    /**
     * Get the child of this node that has the given key + (optional) type.
     *
     * If you are looking for a node and want to search all children + their children
     * then please use the find method instead.
     *
     * @param int|string $key The key of the node we are looking for
     * @param int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function get($key, $type = null) {
        return $this->children->get($key, $type);
    }

    /**
     * Removes this node.
     *
     * @return bool
     */
    public function remove() {
        return $this->parent->children->remove($this->key, $this->type);
    }

    /**
     * Checks if this node has or could have any children
     *
     * @return bool Returns true if it has children or could have (by AJAX expansion)
     */
    public function has_children() {
        return ($this->nodetype === self::NODETYPE_BRANCH || $this->children->count() > 0 || $this->isexpandable);
    }

    /**
     * Marks this node as active and forces it open.
     *
     * Important: If you are here because you need to mark a node active to get
     * the navigation to do what you want have you looked at {@link navigation_node::override_active_url()}?
     * You can use it to specify a different URL to match the active navigation node on
     * rather than having to locate and manually mark a node active.
     */
    public function make_active() {
        $this->isactive = true;
        $this->add_class('active_tree_node');
        $this->force_open();
        if ($this->parent !== null) {
            $this->parent->make_inactive();
        }
    }

    /**
     * Marks a node as inactive and recusised back to the base of the tree
     * doing the same to all parents.
     */
    public function make_inactive() {
        $this->isactive = false;
        $this->remove_class('active_tree_node');
        if ($this->parent !== null) {
            $this->parent->make_inactive();
        }
    }

    /**
     * Forces this node to be open and at the same time forces open all
     * parents until the root node.
     *
     * Recursive.
     */
    public function force_open() {
        $this->forceopen = true;
        if ($this->parent !== null) {
            $this->parent->force_open();
        }
    }

    /**
     * Adds a CSS class to this node.
     *
     * @param string $class
     * @return bool
     */
    public function add_class($class) {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }
        return true;
    }

    /**
     * Adds an HTML attribute to this node.
     *
     * @param string $name
     * @param string $value
     */
    public function add_attribute(string $name, string $value): void {
        $this->attributes[] = ['name' => $name, 'value' => $value];
    }

    /**
     * Removes a CSS class from this node.
     *
     * @param string $class
     * @return bool True if the class was successfully removed.
     */
    public function remove_class($class) {
        if (in_array($class, $this->classes)) {
            $key = array_search($class, $this->classes);
            if ($key !== false) {
                // Remove the class' array element.
                unset($this->classes[$key]);
                // Reindex the array to avoid failures when the classes array is iterated later in mustache templates.
                $this->classes = array_values($this->classes);

                return true;
            }
        }
        return false;
    }

    /**
     * Sets the title for this node and forces Moodle to utilise it.
     *
     * Note that this method is named identically to the public "title" property of the class, which unfortunately confuses
     * our Mustache renderer, because it will see the method and try and call it without any arguments (hence must be nullable)
     * before trying to access the public property
     *
     * @param string|null $title
     * @return string
     */
    public function title(?string $title = null): string {
        if ($title !== null) {
            $this->title = $title;
            $this->forcetitle = true;
        }
        return (string) $this->title;
    }

    /**
     * Resets the page specific information on this node if it is being unserialised.
     */
    public function __wakeup() {
        $this->forceopen = false;
        $this->isactive = false;
        $this->remove_class('active_tree_node');
    }

    /**
     * Checks if this node or any of its children contain the active node.
     *
     * Recursive.
     *
     * @return bool
     */
    public function contains_active_node() {
        if ($this->isactive) {
            return true;
        } else {
            foreach ($this->children as $child) {
                if ($child->isactive || $child->contains_active_node()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * To better balance the admin tree, we want to group all the short top branches together.
     *
     * This means < 8 nodes and no subtrees.
     *
     * @return bool
     */
    public function is_short_branch() {
        $limit = 8;
        if ($this->children->count() >= $limit) {
            return false;
        }
        foreach ($this->children as $child) {
            if ($child->has_children()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Finds the active node.
     *
     * Searches this nodes children plus all of the children for the active node
     * and returns it if found.
     *
     * Recursive.
     *
     * @return navigation_node|false
     */
    public function find_active_node() {
        if ($this->isactive) {
            return $this;
        } else {
            foreach ($this->children as &$child) {
                $outcome = $child->find_active_node();
                if ($outcome !== false) {
                    return $outcome;
                }
            }
        }
        return false;
    }

    /**
     * Searches all children for the best matching active node
     * @param int $strength The url match to be made.
     * @return navigation_node|false
     */
    public function search_for_active_node($strength = URL_MATCH_BASE) {
        if ($this->check_if_active($strength)) {
            return $this;
        } else {
            foreach ($this->children as &$child) {
                $outcome = $child->search_for_active_node($strength);
                if ($outcome !== false) {
                    return $outcome;
                }
            }
        }
        return false;
    }

    /**
     * Gets the content for this node.
     *
     * @param bool $shorttext If true shorttext is used rather than the normal text
     * @return string
     */
    public function get_content($shorttext = false) {
        $navcontext = context_helper::get_navigation_filter_context(null);
        $options = !empty($navcontext) ? ['context' => $navcontext] : null;

        if ($shorttext && $this->shorttext !== null) {
            return format_string($this->shorttext, null, $options);
        } else {
            return format_string($this->text, null, $options);
        }
    }

    /**
     * Gets the title to use for this node.
     *
     * @return string
     */
    public function get_title() {
        if ($this->forcetitle || $this->action != null) {
            return $this->title;
        } else {
            return '';
        }
    }

    /**
     * Used to easily determine if this link in the breadcrumbs has a valid action/url.
     *
     * @return boolean
     */
    public function has_action() {
        return !empty($this->action);
    }

    /**
     * Used to easily determine if the action is an internal link.
     *
     * @return bool
     */
    public function has_internal_action(): bool {
        global $CFG;
        if ($this->has_action()) {
            $url = $this->action();
            if ($this->action() instanceof action_link) {
                $url = $this->action()->url;
            }

            if (($url->out() === $CFG->wwwroot) || (strpos($url->out(), $CFG->wwwroot . '/') === 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Used to easily determine if this link in the breadcrumbs is hidden.
     *
     * @return boolean
     */
    public function is_hidden() {
        return $this->hidden;
    }

    /**
     * Gets the CSS class to add to this node to describe its type
     *
     * @return string
     */
    public function get_css_type() {
        if (array_key_exists($this->type, $this->namedtypes)) {
            return 'type_' . $this->namedtypes[$this->type];
        }
        return 'type_unknown';
    }

    /**
     * Finds all nodes that are expandable by AJAX
     *
     * @param array $expandable An array by reference to populate with expandable nodes.
     */
    public function find_expandable(array &$expandable) {
        foreach ($this->children as &$child) {
            if ($child->display && $child->has_children() && $child->children->count() == 0) {
                $child->id = 'expandable_branch_' . $child->type . '_' . clean_param($child->key, PARAM_ALPHANUMEXT);
                $this->add_class('canexpand');
                $child->requiresajaxloading = true;
                $expandable[] = ['id' => $child->id, 'key' => $child->key, 'type' => $child->type];
            }
            $child->find_expandable($expandable);
        }
    }

    /**
     * Finds all nodes of a given type (recursive)
     *
     * @param int $type One of navigation_node::TYPE_*
     * @return array
     */
    public function find_all_of_type($type) {
        $nodes = $this->children->type($type);
        foreach ($this->children as &$node) {
            $childnodes = $node->find_all_of_type($type);
            $nodes = array_merge($nodes, $childnodes);
        }
        return $nodes;
    }

    /**
     * Removes this node if it is empty
     */
    public function trim_if_empty() {
        if ($this->children->count() == 0) {
            $this->remove();
        }
    }

    /**
     * Creates a tab representation of this nodes children that can be used
     * with print_tabs to produce the tabs on a page.
     *
     * call_user_func_array('print_tabs', $node->get_tabs_array());
     *
     * @param array $inactive
     * @param bool $return
     * @return array Array (tabs, selected, inactive, activated, return)
     */
    public function get_tabs_array(array $inactive = [], $return = false) {
        $tabs = [];
        $rows = [];
        $selected = null;
        $activated = [];
        foreach ($this->children as $node) {
            $tabs[] = new tabobject($node->key, $node->action, $node->get_content(), $node->get_title());
            if ($node->contains_active_node()) {
                if ($node->children->count() > 0) {
                    $activated[] = $node->key;
                    foreach ($node->children as $child) {
                        if ($child->contains_active_node()) {
                            $selected = $child->key;
                        }
                        $rows[] = new tabobject($child->key, $child->action, $child->get_content(), $child->get_title());
                    }
                } else {
                    $selected = $node->key;
                }
            }
        }
        return [[$tabs, $rows], $selected, $inactive, $activated, $return];
    }

    /**
     * Sets the parent for this node and if this node is active ensures that the tree is properly
     * adjusted as well.
     *
     * @param navigation_node $parent
     */
    public function set_parent(navigation_node $parent) {
        // Set the parent (thats the easy part).
        $this->parent = $parent;
        // Check if this node is active (this is checked during construction).
        if ($this->isactive) {
            // Force all of the parent nodes open so you can see this node.
            $this->parent->force_open();
            // Make all parents inactive so that its clear where we are.
            $this->parent->make_inactive();
        }
    }

    /**
     * Hides the node and any children it has.
     *
     * @since Moodle 2.5
     * @param array $typestohide Optional. An array of node types that should be hidden.
     *      If null all nodes will be hidden.
     *      If an array is given then nodes will only be hidden if their type mtatches an element in the array.
     *          e.g. array(navigation_node::TYPE_COURSE) would hide only course nodes.
     */
    public function hide(?array $typestohide = null) {
        if ($typestohide === null || in_array($this->type, $typestohide)) {
            $this->display = false;
            if ($this->has_children()) {
                foreach ($this->children as $child) {
                    $child->hide($typestohide);
                }
            }
        }
    }

    /**
     * Get the action url for this navigation node.
     * Called from templates.
     *
     * @since Moodle 3.2
     */
    public function action() {
        if ($this->action instanceof url) {
            return $this->action;
        } else if ($this->action instanceof action_link) {
            return $this->action->url;
        }
        return $this->action;
    }

    /**
     * Return an array consisting of the additional attributes for the action url.
     *
     * @return array Formatted array to parse in a template
     */
    public function actionattributes() {
        if ($this->action instanceof action_link) {
            return array_map(function ($key, $value) {
                return [
                    'name' => $key,
                    'value' => $value,
                ];
            }, array_keys($this->action->attributes), $this->action->attributes);
        }

        return [];
    }

    /**
     * Check whether the node's action is of type action_link.
     *
     * @return bool
     */
    public function is_action_link() {
        return $this->action instanceof action_link;
    }

    /**
     * Return an array consisting of the actions for the action link.
     *
     * @return array Formatted array to parse in a template
     */
    public function action_link_actions() {
        global $PAGE;

        if (!$this->is_action_link()) {
            return [];
        }

        $actionid = $this->action->attributes['id'];
        $actionsdata = array_map(function ($action) use ($PAGE, $actionid) {
            $data = $action->export_for_template($PAGE->get_renderer('core'));
            $data->id = $actionid;
            return $data;
        }, !empty($this->action->actions) ? $this->action->actions : []);

        return ['actions' => $actionsdata];
    }

    /**
     * Sets whether the node and its children should be added into a "more" menu whenever possible.
     *
     * @param bool $forceintomoremenu
     */
    public function set_force_into_more_menu(bool $forceintomoremenu = false) {
        $this->forceintomoremenu = $forceintomoremenu;
        foreach ($this->children as $child) {
            $child->set_force_into_more_menu($forceintomoremenu);
        }
    }

    /**
     * Sets whether the node and its children should be displayed in the "secondary" navigation when applicable.
     *
     * @param bool $show
     */
    public function set_show_in_secondary_navigation(bool $show = true) {
        $this->showinsecondarynavigation = $show;
        foreach ($this->children as $child) {
            $child->set_show_in_secondary_navigation($show);
        }
    }

    /**
     * Add the menu item to handle locking and unlocking of a conext.
     *
     * @param \navigation_node $node Node to add
     * @param \context $context The context to be locked
     */
    protected function add_context_locking_node(\navigation_node $node, \context $context) {
        global $CFG;
        // Manage context locking.
        if (!empty($CFG->contextlocking) && has_capability('moodle/site:managecontextlocks', $context)) {
            $parentcontext = $context->get_parent_context();
            if (empty($parentcontext) || !$parentcontext->locked) {
                if ($context->locked) {
                    $lockicon = 'i/unlock';
                    $lockstring = get_string('managecontextunlock', 'admin');
                } else {
                    $lockicon = 'i/lock';
                    $lockstring = get_string('managecontextlock', 'admin');
                }
                $node->add(
                    $lockstring,
                    new url(
                        '/admin/lock.php',
                        [
                            'id' => $context->id,
                        ]
                    ),
                    self::TYPE_SETTING,
                    null,
                    'contextlocking',
                    new pix_icon($lockicon, '')
                );
            }
        }
    }

    /**
     * Reset all static data.
     *
     * @throws coding_exception if called outside of a unit test
     */
    public static function reset_all_data(): void {
        if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
            throw new coding_exception('Resetting all data is not allowed outside of PHPUnit tests.');
        }

        self::$fullmeurl = null;
        self::$autofindactive = true;
        self::$loadadmintree = false;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(navigation_node::class, \navigation_node::class);
