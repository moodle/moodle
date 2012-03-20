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
 * This file contains classes used to manage the navigation structures in Moodle
 * and was introduced as part of the changes occuring in Moodle 2.0
 *
 * @since      2.0
 * @package    core
 * @subpackage navigation
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The name that will be used to separate the navigation cache within SESSION
 */
define('NAVIGATION_CACHE_NAME', 'navigation');

/**
 * This class is used to represent a node in a navigation tree
 *
 * This class is used to represent a node in a navigation tree within Moodle,
 * the tree could be one of global navigation, settings navigation, or the navbar.
 * Each node can be one of two types either a Leaf (default) or a branch.
 * When a node is first created it is created as a leaf, when/if children are added
 * the node then becomes a branch.
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_node implements renderable {
    /** @var int Used to identify this node a leaf (default) 0 */
    const NODETYPE_LEAF =   0;
    /** @var int Used to identify this node a branch, happens with children  1 */
    const NODETYPE_BRANCH = 1;
    /** @var null Unknown node type null */
    const TYPE_UNKNOWN =    null;
    /** @var int System node type 0 */
    const TYPE_ROOTNODE =   0;
    /** @var int System node type 1 */
    const TYPE_SYSTEM =     1;
    /** @var int Category node type 10 */
    const TYPE_CATEGORY =   10;
    /** @var int Course node type 20 */
    const TYPE_COURSE =     20;
    /** @var int Course Structure node type 30 */
    const TYPE_SECTION =    30;
    /** @var int Activity node type, e.g. Forum, Quiz 40 */
    const TYPE_ACTIVITY =   40;
    /** @var int Resource node type, e.g. Link to a file, or label 50 */
    const TYPE_RESOURCE =   50;
    /** @var int A custom node type, default when adding without specifing type 60 */
    const TYPE_CUSTOM =     60;
    /** @var int Setting node type, used only within settings nav 70 */
    const TYPE_SETTING =    70;
    /** @var int Setting node type, used only within settings nav 80 */
    const TYPE_USER =       80;
    /** @var int Setting node type, used for containers of no importance 90 */
    const TYPE_CONTAINER =  90;

    /** @var int Parameter to aid the coder in tracking [optional] */
    public $id = null;
    /** @var string|int The identifier for the node, used to retrieve the node */
    public $key = null;
    /** @var string The text to use for the node */
    public $text = null;
    /** @var string Short text to use if requested [optional] */
    public $shorttext = null;
    /** @var string The title attribute for an action if one is defined */
    public $title = null;
    /** @var string A string that can be used to build a help button */
    public $helpbutton = null;
    /** @var moodle_url|action_link|null An action for the node (link) */
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
    public $classes = array();
    /** @var navigation_node_collection An array of child nodes */
    public $children = array();
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
    /** @var array */
    protected $namedtypes = array(0=>'system',10=>'category',20=>'course',30=>'structure',40=>'activity',50=>'resource',60=>'custom',70=>'setting', 80=>'user');
    /** @var moodle_url */
    protected static $fullmeurl = null;
    /** @var bool toogles auto matching of active node */
    public static $autofindactive = true;
    /** @var mixed If set to an int, that section will be included even if it has no activities */
    public $includesectionnum = false;

    /**
     * Constructs a new navigation_node
     *
     * @param array|string $properties Either an array of properties or a string to use
     *                     as the text for the node
     */
    public function __construct($properties) {
        if (is_array($properties)) {
            // Check the array for each property that we allow to set at construction.
            // text         - The main content for the node
            // shorttext    - A short text if required for the node
            // icon         - The icon to display for the node
            // type         - The type of the node
            // key          - The key to use to identify the node
            // parent       - A reference to the nodes parent
            // action       - The action to attribute to this node, usually a URL to link to
            if (array_key_exists('text', $properties)) {
                $this->text = $properties['text'];
            }
            if (array_key_exists('shorttext', $properties)) {
                $this->shorttext = $properties['shorttext'];
            }
            if (!array_key_exists('icon', $properties)) {
                $properties['icon'] = new pix_icon('i/navigationitem', 'moodle');
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
            // This needs to happen last because of the check_if_active call that occurs
            if (array_key_exists('action', $properties)) {
                $this->action = $properties['action'];
                if (is_string($this->action)) {
                    $this->action = new moodle_url($this->action);
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
        // Default the title to the text
        $this->title = $this->text;
        // Instantiate a new navigation node collection for this nodes children
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
    public function check_if_active($strength=URL_MATCH_EXACT) {
        global $FULLME, $PAGE;
        // Set fullmeurl if it hasn't already been set
        if (self::$fullmeurl == null) {
            if ($PAGE->has_set_url()) {
                self::override_active_url(new moodle_url($PAGE->url));
            } else {
                self::override_active_url(new moodle_url($FULLME));
            }
        }

        // Compare the action of this node against the fullmeurl
        if ($this->action instanceof moodle_url && $this->action->compare(self::$fullmeurl, $strength)) {
            $this->make_active();
            return true;
        }
        return false;
    }

    /**
     * This sets the URL that the URL of new nodes get compared to when locating
     * the active node.
     *
     * The active node is the node that matches the URL set here. By default this
     * is either $PAGE->url or if that hasn't been set $FULLME.
     *
     * @param moodle_url $url The url to use for the fullmeurl.
     */
    public static function override_active_url(moodle_url $url) {
        // Clone the URL, in case the calling script changes their URL later.
        self::$fullmeurl = new moodle_url($url);
    }

    /**
     * Creates a navigation node, ready to add it as a child using add_node
     * function. (The created node needs to be added before you can use it.)
     * @param string $text
     * @param moodle_url|action_link $action
     * @param int $type
     * @param string $shorttext
     * @param string|int $key
     * @param pix_icon $icon
     * @return navigation_node
     */
    public static function create($text, $action=null, $type=self::TYPE_CUSTOM,
            $shorttext=null, $key=null, pix_icon $icon=null) {
        // Properties array used when creating the new navigation node
        $itemarray = array(
            'text' => $text,
            'type' => $type
        );
        // Set the action if one was provided
        if ($action!==null) {
            $itemarray['action'] = $action;
        }
        // Set the shorttext if one was provided
        if ($shorttext!==null) {
            $itemarray['shorttext'] = $shorttext;
        }
        // Set the icon if one was provided
        if ($icon!==null) {
            $itemarray['icon'] = $icon;
        }
        // Set the key
        $itemarray['key'] = $key;
        // Construct and return
        return new navigation_node($itemarray);
    }

    /**
     * Adds a navigation node as a child of this node.
     *
     * @param string $text
     * @param moodle_url|action_link $action
     * @param int $type
     * @param string $shorttext
     * @param string|int $key
     * @param pix_icon $icon
     * @return navigation_node
     */
    public function add($text, $action=null, $type=self::TYPE_CUSTOM, $shorttext=null, $key=null, pix_icon $icon=null) {
        // Create child node
        $childnode = self::create($text, $action, $type, $shorttext, $key, $icon);

        // Add the child to end and return
        return $this->add_node($childnode);
    }

    /**
     * Adds a navigation node as a child of this one, given a $node object
     * created using the create function.
     * @param navigation_node $childnode Node to add
     * @param int|string $key The key of a node to add this before. If not
     *   specified, adds at end of list
     * @return navigation_node The added node
     */
    public function add_node(navigation_node $childnode, $beforekey=null) {
        // First convert the nodetype for this node to a branch as it will now have children
        if ($this->nodetype !== self::NODETYPE_BRANCH) {
            $this->nodetype = self::NODETYPE_BRANCH;
        }
        // Set the parent to this node
        $childnode->set_parent($this);

        // Default the key to the number of children if not provided
        if ($childnode->key === null) {
            $childnode->key = $this->children->count();
        }

        // Add the child using the navigation_node_collections add method
        $node = $this->children->add($childnode, $beforekey);

        // If added node is a category node or the user is logged in and it's a course
        // then mark added node as a branch (makes it expandable by AJAX)
        $type = $childnode->type;
        if (($type==self::TYPE_CATEGORY) || (isloggedin() && $type==self::TYPE_COURSE)) {
            $node->nodetype = self::NODETYPE_BRANCH;
        }
        // If this node is hidden mark it's children as hidden also
        if ($this->hidden) {
            $node->hidden = true;
        }
        // Return added node (reference returned by $this->children->add()
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
     * @param int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function find($key, $type) {
        return $this->children->find($key, $type);
    }

    /**
     * Get ths child of this node that has the given key + (optional) type.
     *
     * If you are looking for a node and want to search all children + thier children
     * then please use the find method instead.
     *
     * @param int|string $key The key of the node we are looking for
     * @param int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function get($key, $type=null) {
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
        return ($this->nodetype === navigation_node::NODETYPE_BRANCH || $this->children->count()>0);
    }

    /**
     * Marks this node as active and forces it open.
     *
     * Important: If you are here because you need to mark a node active to get
     * the navigation to do what you want have you looked at {@see navigation_node::override_active_url()}?
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
     * Removes a CSS class from this node.
     *
     * @param string $class
     * @return bool True if the class was successfully removed.
     */
    public function remove_class($class) {
        if (in_array($class, $this->classes)) {
            $key = array_search($class,$this->classes);
            if ($key!==false) {
                unset($this->classes[$key]);
                return true;
            }
        }
        return false;
    }

    /**
     * Sets the title for this node and forces Moodle to utilise it.
     * @param string $title
     */
    public function title($title) {
        $this->title = $title;
        $this->forcetitle = true;
    }

    /**
     * Resets the page specific information on this node if it is being unserialised.
     */
    public function __wakeup(){
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
     * @return navigation_node|false
     */
    public function search_for_active_node() {
        if ($this->check_if_active(URL_MATCH_BASE)) {
            return $this;
        } else {
            foreach ($this->children as &$child) {
                $outcome = $child->search_for_active_node();
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
    public function get_content($shorttext=false) {
        if ($shorttext && $this->shorttext!==null) {
            return format_string($this->shorttext);
        } else {
            return format_string($this->text);
        }
    }

    /**
     * Gets the title to use for this node.
     *
     * @return string
     */
    public function get_title() {
        if ($this->forcetitle || $this->action != null){
            return $this->title;
        } else {
            return '';
        }
    }

    /**
     * Gets the CSS class to add to this node to describe its type
     *
     * @return string
     */
    public function get_css_type() {
        if (array_key_exists($this->type, $this->namedtypes)) {
            return 'type_'.$this->namedtypes[$this->type];
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
            if ($child->nodetype == self::NODETYPE_BRANCH && $child->children->count() == 0 && $child->display) {
                $child->id = 'expandable_branch_'.(count($expandable)+1);
                $this->add_class('canexpand');
                $expandable[] = array('id' => $child->id, 'key' => $child->key, 'type' => $child->type);
            }
            $child->find_expandable($expandable);
        }
    }

    /**
     * Finds all nodes of a given type (recursive)
     *
     * @param int $type On of navigation_node::TYPE_*
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
    public function get_tabs_array(array $inactive=array(), $return=false) {
        $tabs = array();
        $rows = array();
        $selected = null;
        $activated = array();
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
        return array(array($tabs, $rows), $selected, $inactive, $activated, $return);
    }

    /**
     * Sets the parent for this node and if this node is active ensures that the tree is properly
     * adjusted as well.
     *
     * @param navigation_node $parent
     */
    public function set_parent(navigation_node $parent) {
        // Set the parent (thats the easy part)
        $this->parent = $parent;
        // Check if this node is active (this is checked during construction)
        if ($this->isactive) {
            // Force all of the parent nodes open so you can see this node
            $this->parent->force_open();
            // Make all parents inactive so that its clear where we are.
            $this->parent->make_inactive();
        }
    }
}

/**
 * Navigation node collection
 *
 * This class is responsible for managing a collection of navigation nodes.
 * It is required because a node's unique identifier is a combination of both its
 * key and its type.
 *
 * Originally an array was used with a string key that was a combination of the two
 * however it was decided that a better solution would be to use a class that
 * implements the standard IteratorAggregate interface.
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_node_collection implements IteratorAggregate {
    /**
     * A multidimensional array to where the first key is the type and the second
     * key is the nodes key.
     * @var array
     */
    protected $collection = array();
    /**
     * An array that contains references to nodes in the same order they were added.
     * This is maintained as a progressive array.
     * @var array
     */
    protected $orderedcollection = array();
    /**
     * A reference to the last node that was added to the collection
     * @var navigation_node
     */
    protected $last = null;
    /**
     * The total number of items added to this array.
     * @var int
     */
    protected $count = 0;

    /**
     * Adds a navigation node to the collection
     *
     * @param navigation_node $node Node to add
     * @param string $beforekey If specified, adds before a node with this key,
     *   otherwise adds at end
     * @return navigation_node Added node
     */
    public function add(navigation_node $node, $beforekey=null) {
        global $CFG;
        $key = $node->key;
        $type = $node->type;

        // First check we have a 2nd dimension for this type
        if (!array_key_exists($type, $this->orderedcollection)) {
            $this->orderedcollection[$type] = array();
        }
        // Check for a collision and report if debugging is turned on
        if ($CFG->debug && array_key_exists($key, $this->orderedcollection[$type])) {
            debugging('Navigation node intersect: Adding a node that already exists '.$key, DEBUG_DEVELOPER);
        }

        // Find the key to add before
        $newindex = $this->count;
        $last = true;
        if ($beforekey !== null) {
            foreach ($this->collection as $index => $othernode) {
                if ($othernode->key === $beforekey) {
                    $newindex = $index;
                    $last = false;
                    break;
                }
            }
            if ($newindex === $this->count) {
                debugging('Navigation node add_before: Reference node not found ' . $beforekey .
                        ', options: ' . implode(' ', $this->get_key_list()), DEBUG_DEVELOPER);
            }
        }

        // Add the node to the appropriate place in the by-type structure (which
        // is not ordered, despite the variable name)
        $this->orderedcollection[$type][$key] = $node;
        if (!$last) {
            // Update existing references in the ordered collection (which is the
            // one that isn't called 'ordered') to shuffle them along if required
            for ($oldindex = $this->count; $oldindex > $newindex; $oldindex--) {
                $this->collection[$oldindex] = $this->collection[$oldindex - 1];
            }
        }
        // Add a reference to the node to the progressive collection.
        $this->collection[$newindex] = &$this->orderedcollection[$type][$key];
        // Update the last property to a reference to this new node.
        $this->last = &$this->orderedcollection[$type][$key];

        // Reorder the array by index if needed
        if (!$last) {
            ksort($this->collection);
        }
        $this->count++;
        // Return the reference to the now added node
        return $node;
    }

    /**
     * Return a list of all the keys of all the nodes.
     * @return array the keys.
     */
    public function get_key_list() {
        $keys = array();
        foreach ($this->collection as $node) {
            $keys[] = $node->key;
        }
        return $keys;
    }

    /**
     * Fetches a node from this collection.
     *
     * @param string|int $key The key of the node we want to find.
     * @param int $type One of navigation_node::TYPE_*.
     * @return navigation_node|null
     */
    public function get($key, $type=null) {
        if ($type !== null) {
            // If the type is known then we can simply check and fetch
            if (!empty($this->orderedcollection[$type][$key])) {
                return $this->orderedcollection[$type][$key];
            }
        } else {
            // Because we don't know the type we look in the progressive array
            foreach ($this->collection as $node) {
                if ($node->key === $key) {
                    return $node;
                }
            }
        }
        return false;
    }

    /**
     * Searches for a node with matching key and type.
     *
     * This function searches both the nodes in this collection and all of
     * the nodes in each collection belonging to the nodes in this collection.
     *
     * Recursive.
     *
     * @param string|int $key  The key of the node we want to find.
     * @param int $type  One of navigation_node::TYPE_*.
     * @return navigation_node|null
     */
    public function find($key, $type=null) {
        if ($type !== null && array_key_exists($type, $this->orderedcollection) && array_key_exists($key, $this->orderedcollection[$type])) {
            return $this->orderedcollection[$type][$key];
        } else {
            $nodes = $this->getIterator();
            // Search immediate children first
            foreach ($nodes as &$node) {
                if ($node->key === $key && ($type === null || $type === $node->type)) {
                    return $node;
                }
            }
            // Now search each childs children
            foreach ($nodes as &$node) {
                $result = $node->children->find($key, $type);
                if ($result !== false) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * Fetches the last node that was added to this collection
     *
     * @return navigation_node
     */
    public function last() {
        return $this->last;
    }

    /**
     * Fetches all nodes of a given type from this collection
     */
    public function type($type) {
        if (!array_key_exists($type, $this->orderedcollection)) {
            $this->orderedcollection[$type] = array();
        }
        return $this->orderedcollection[$type];
    }
    /**
     * Removes the node with the given key and type from the collection
     *
     * @param string|int $key
     * @param int $type
     * @return bool
     */
    public function remove($key, $type=null) {
        $child = $this->get($key, $type);
        if ($child !== false) {
            foreach ($this->collection as $colkey => $node) {
                if ($node->key == $key && $node->type == $type) {
                    unset($this->collection[$colkey]);
                    break;
                }
            }
            unset($this->orderedcollection[$child->type][$child->key]);
            $this->count--;
            return true;
        }
        return false;
    }

    /**
     * Gets the number of nodes in this collection
     *
     * This option uses an internal count rather than counting the actual options to avoid
     * a performance hit through the count function.
     *
     * @return int
     */
    public function count() {
        return $this->count;
    }
    /**
     * Gets an array iterator for the collection.
     *
     * This is required by the IteratorAggregator interface and is used by routines
     * such as the foreach loop.
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->collection);
    }
}

/**
 * The global navigation class used for... the global navigation
 *
 * This class is used by PAGE to store the global navigation for the site
 * and is then used by the settings nav and navbar to save on processing and DB calls
 *
 * See
 * <ul>
 * <li><b>{@link lib/pagelib.php}</b> {@link moodle_page::initialise_theme_and_output()}<li>
 * <li><b>{@link lib/ajax/getnavbranch.php}</b> Called by ajax<li>
 * </ul>
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_navigation extends navigation_node {
    /**
     * The Moodle page this navigation object belongs to.
     * @var moodle_page
     */
    protected $page;
    /** @var bool */
    protected $initialised = false;
    /** @var array */
    protected $mycourses = array();
    /** @var array */
    protected $rootnodes = array();
    /** @var bool */
    protected $showemptysections = false;
    /** @var bool */
    protected $showcategories = null;
    /** @var array */
    protected $extendforuser = array();
    /** @var navigation_cache */
    protected $cache;
    /** @var array */
    protected $addedcourses = array();
    /** @var array */
    protected $addedcategories = array();
    /** @var int */
    protected $expansionlimit = 0;
    /** @var int */
    protected $useridtouseforparentchecks = 0;

    /**
     * Constructs a new global navigation
     *
     * @param moodle_page $page The page this navigation object belongs to
     */
    public function __construct(moodle_page $page) {
        global $CFG, $SITE, $USER;

        if (during_initial_install()) {
            return;
        }

        if (get_home_page() == HOMEPAGE_SITE) {
            // We are using the site home for the root element
            $properties = array(
                'key' => 'home',
                'type' => navigation_node::TYPE_SYSTEM,
                'text' => get_string('home'),
                'action' => new moodle_url('/')
            );
        } else {
            // We are using the users my moodle for the root element
            $properties = array(
                'key' => 'myhome',
                'type' => navigation_node::TYPE_SYSTEM,
                'text' => get_string('myhome'),
                'action' => new moodle_url('/my/')
            );
        }

        // Use the parents consturctor.... good good reuse
        parent::__construct($properties);

        // Initalise and set defaults
        $this->page = $page;
        $this->forceopen = true;
        $this->cache = new navigation_cache(NAVIGATION_CACHE_NAME);
    }

    /**
     * Mutator to set userid to allow parent to see child's profile
     * page navigation. See MDL-25805 for initial issue. Linked to it
     * is an issue explaining why this is a REALLY UGLY HACK thats not
     * for you to use!
     *
     * @param int $userid userid of profile page that parent wants to navigate around.
     */
    public function set_userid_for_parent_checks($userid) {
        $this->useridtouseforparentchecks = $userid;
    }


    /**
     * Initialises the navigation object.
     *
     * This causes the navigation object to look at the current state of the page
     * that it is associated with and then load the appropriate content.
     *
     * This should only occur the first time that the navigation structure is utilised
     * which will normally be either when the navbar is called to be displayed or
     * when a block makes use of it.
     *
     * @return bool
     */
    public function initialise() {
        global $CFG, $SITE, $USER, $DB;
        // Check if it has alread been initialised
        if ($this->initialised || during_initial_install()) {
            return true;
        }
        $this->initialised = true;

        // Set up the five base root nodes. These are nodes where we will put our
        // content and are as follows:
        // site:        Navigation for the front page.
        // myprofile:     User profile information goes here.
        // mycourses:   The users courses get added here.
        // courses:     Additional courses are added here.
        // users:       Other users information loaded here.
        $this->rootnodes = array();
        if (get_home_page() == HOMEPAGE_SITE) {
            // The home element should be my moodle because the root element is the site
            if (isloggedin() && !isguestuser()) {  // Makes no sense if you aren't logged in
                $this->rootnodes['home'] = $this->add(get_string('myhome'), new moodle_url('/my/'), self::TYPE_SETTING, null, 'home');
            }
        } else {
            // The home element should be the site because the root node is my moodle
            $this->rootnodes['home'] = $this->add(get_string('sitehome'), new moodle_url('/'), self::TYPE_SETTING, null, 'home');
            if ($CFG->defaulthomepage == HOMEPAGE_MY) {
                // We need to stop automatic redirection
                $this->rootnodes['home']->action->param('redirect', '0');
            }
        }
        $this->rootnodes['site']      = $this->add_course($SITE);
        $this->rootnodes['myprofile'] = $this->add(get_string('myprofile'), null, self::TYPE_USER, null, 'myprofile');
        $this->rootnodes['mycourses'] = $this->add(get_string('mycourses'), null, self::TYPE_ROOTNODE, null, 'mycourses');
        $this->rootnodes['courses']   = $this->add(get_string('courses'), null, self::TYPE_ROOTNODE, null, 'courses');
        $this->rootnodes['users']     = $this->add(get_string('users'), null, self::TYPE_ROOTNODE, null, 'users');

        // Fetch all of the users courses.
        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = $CFG->navcourselimit;
        }

        $mycourses = enrol_get_my_courses(NULL, 'visible DESC,sortorder ASC', $limit);
        $showallcourses = (count($mycourses) == 0 || !empty($CFG->navshowallcourses));
        // When checking if we are to show categories there is an additional override.
        // If the user is viewing a category then we will load it regardless of settings.
        // to ensure that the navigation is consistent.
        $showcategories = $this->page->context->contextlevel == CONTEXT_COURSECAT || ($showallcourses && $this->show_categories());
        $issite = ($this->page->course->id == SITEID);
        $ismycourse = (array_key_exists($this->page->course->id, $mycourses));

        // Check if any courses were returned.
        if (count($mycourses) > 0) {
            // Add all of the users courses to the navigation
            foreach ($mycourses as $course) {
                $course->coursenode = $this->add_course($course, false, true);
            }
        }

        if ($showallcourses) {
            // Load all courses
            $this->load_all_courses();
        }

        // We always load the frontpage course to ensure it is available without
        // JavaScript enabled.
        $frontpagecourse = $this->load_course($SITE);
        $this->add_front_page_course_essentials($frontpagecourse, $SITE);
        $this->load_course_sections($SITE, $frontpagecourse);

        $canviewcourseprofile = true;

        // Next load context specific content into the navigation
        switch ($this->page->context->contextlevel) {
            case CONTEXT_SYSTEM :
                // This has already been loaded we just need to map the variable
                $coursenode = $frontpagecourse;
                $this->load_all_categories(null, $showcategories);
                break;
            case CONTEXT_COURSECAT :
                // This has already been loaded we just need to map the variable
                $coursenode = $frontpagecourse;
                $this->load_all_categories($this->page->context->instanceid, $showcategories);
                if (array_key_exists($this->page->context->instanceid, $this->addedcategories)) {
                    $this->addedcategories[$this->page->context->instanceid]->make_active();
                }
                break;
            case CONTEXT_BLOCK :
            case CONTEXT_COURSE :
                if ($issite) {
                    // If it is the front page course, or a block on it then
                    // everything has already been loaded.
                    break;
                }
                // Load the course associated with the page into the navigation
                $course = $this->page->course;
                if ($showcategories && !$ismycourse) {
                    $this->load_all_categories($course->category, $showcategories);
                }
                $coursenode = $this->load_course($course);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the
                // course node and not populate it.
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

                // Not enrolled, can't view, and hasn't switched roles
                if (!can_access_course($coursecontext)) {
                    // TODO: very ugly hack - do not force "parents" to enrol into course their child is enrolled in,
                    // this hack has been propagated from user/view.php to display the navigation node. (MDL-25805)
                    $isparent = false;
                    if ($this->useridtouseforparentchecks) {
                        if ($this->useridtouseforparentchecks != $USER->id) {
                            $usercontext   = get_context_instance(CONTEXT_USER, $this->useridtouseforparentchecks, MUST_EXIST);
                            if ($DB->record_exists('role_assignments', array('userid' => $USER->id, 'contextid' => $usercontext->id))
                                    and has_capability('moodle/user:viewdetails', $usercontext)) {
                                $isparent = true;
                            }
                        }
                    }

                    if (!$isparent) {
                        $coursenode->make_active();
                        $canviewcourseprofile = false;
                        break;
                    }
                }
                // Add the essentials such as reports etc...
                $this->add_course_essentials($coursenode, $course);
                if ($this->format_display_course_content($course->format)) {
                    // Load the course sections
                    $sections = $this->load_course_sections($course, $coursenode);
                }
                if (!$coursenode->contains_active_node() && !$coursenode->search_for_active_node()) {
                    $coursenode->make_active();
                }
                break;
            case CONTEXT_MODULE :
                if ($issite) {
                    // If this is the site course then most information will have
                    // already been loaded.
                    // However we need to check if there is more content that can
                    // yet be loaded for the specific module instance.
                    $activitynode = $this->rootnodes['site']->get($this->page->cm->id, navigation_node::TYPE_ACTIVITY);
                    if ($activitynode) {
                        $this->load_activity($this->page->cm, $this->page->course, $activitynode);
                    }
                    break;
                }

                $course = $this->page->course;
                $cm = $this->page->cm;

                if ($showcategories && !$ismycourse) {
                    $this->load_all_categories($course->category, $showcategories);
                }

                // Load the course associated with the page into the navigation
                $coursenode = $this->load_course($course);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the
                // course node and not populate it.
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                if (!can_access_course($coursecontext)) {
                    $coursenode->make_active();
                    $canviewcourseprofile = false;
                    break;
                }

                $this->add_course_essentials($coursenode, $course);

                // Get section number from $cm (if provided) - we need this
                // before loading sections in order to tell it to load this section
                // even if it would not normally display (=> it contains only
                // a label, which we are now editing)
                $sectionnum = isset($cm->sectionnum) ? $cm->sectionnum : 0;
                if ($sectionnum) {
                    // This value has to be stored in a member variable because
                    // otherwise we would have to pass it through a public API
                    // to course formats and they would need to change their
                    // functions to pass it along again...
                    $this->includesectionnum = $sectionnum;
                } else {
                    $this->includesectionnum = false;
                }

                // Load the course sections into the page
                $sections = $this->load_course_sections($course, $coursenode);
                if ($course->id != SITEID) {
                    // Find the section for the $CM associated with the page and collect
                    // its section number.
                    if ($sectionnum) {
                        $cm->sectionnumber = $sectionnum;
                    } else {
                        foreach ($sections as $section) {
                            if ($section->id == $cm->section) {
                                $cm->sectionnumber = $section->section;
                                break;
                            }
                        }
                    }

                    // Load all of the section activities for the section the cm belongs to.
                    if (isset($cm->sectionnumber) and !empty($sections[$cm->sectionnumber])) {
                        list($sectionarray, $activityarray) = $this->generate_sections_and_activities($course);
                        $activities = $this->load_section_activities($sections[$cm->sectionnumber]->sectionnode, $cm->sectionnumber, $activityarray);
                    } else {
                        $activities = array();
                        if ($activity = $this->load_stealth_activity($coursenode, get_fast_modinfo($course))) {
                            // "stealth" activity from unavailable section
                            $activities[$cm->id] = $activity;
                        }
                    }
                } else {
                    $activities = array();
                    $activities[$cm->id] = $coursenode->get($cm->id, navigation_node::TYPE_ACTIVITY);
                }
                if (!empty($activities[$cm->id])) {
                    // Finally load the cm specific navigaton information
                    $this->load_activity($cm, $course, $activities[$cm->id]);
                    // Check if we have an active ndoe
                    if (!$activities[$cm->id]->contains_active_node() && !$activities[$cm->id]->search_for_active_node()) {
                        // And make the activity node active.
                        $activities[$cm->id]->make_active();
                    }
                } else {
                    //TODO: something is wrong, what to do? (Skodak)
                }
                break;
            case CONTEXT_USER :
                if ($issite) {
                    // The users profile information etc is already loaded
                    // for the front page.
                    break;
                }
                $course = $this->page->course;
                if ($showcategories && !$ismycourse) {
                    $this->load_all_categories($course->category, $showcategories);
                }
                // Load the course associated with the user into the navigation
                $coursenode = $this->load_course($course);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the
                // course node and not populate it.
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                if (!can_access_course($coursecontext)) {
                    $coursenode->make_active();
                    $canviewcourseprofile = false;
                    break;
                }
                $this->add_course_essentials($coursenode, $course);
                $sections = $this->load_course_sections($course, $coursenode);
                break;
        }

        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = $CFG->navcourselimit;
        }
        if ($showcategories) {
            $categories = $this->find_all_of_type(self::TYPE_CATEGORY);
            foreach ($categories as &$category) {
                if ($category->children->count() >= $limit) {
                    $url = new moodle_url('/course/category.php', array('id'=>$category->key));
                    $category->add(get_string('viewallcourses'), $url, self::TYPE_SETTING);
                }
            }
        } else if ($this->rootnodes['courses']->children->count() >= $limit) {
            $this->rootnodes['courses']->add(get_string('viewallcoursescategories'), new moodle_url('/course/index.php'), self::TYPE_SETTING);
        }

        // Load for the current user
        $this->load_for_user();
        if ($this->page->context->contextlevel >= CONTEXT_COURSE && $this->page->context->instanceid != SITEID && $canviewcourseprofile) {
            $this->load_for_user(null, true);
        }
        // Load each extending user into the navigation.
        foreach ($this->extendforuser as $user) {
            if ($user->id != $USER->id) {
                $this->load_for_user($user);
            }
        }

        // Give the local plugins a chance to include some navigation if they want.
        foreach (get_list_of_plugins('local') as $plugin) {
            if (!file_exists($CFG->dirroot.'/local/'.$plugin.'/lib.php')) {
                continue;
            }
            require_once($CFG->dirroot.'/local/'.$plugin.'/lib.php');
            $function = $plugin.'_extends_navigation';
            if (function_exists($function)) {
                $function($this);
            }
        }

        // Remove any empty root nodes
        foreach ($this->rootnodes as $node) {
            // Dont remove the home node
            if ($node->key !== 'home' && !$node->has_children()) {
                $node->remove();
            }
        }

        if (!$this->contains_active_node()) {
            $this->search_for_active_node();
        }

        // If the user is not logged in modify the navigation structure as detailed
        // in {@link http://docs.moodle.org/dev/Navigation_2.0_structure}
        if (!isloggedin()) {
            $activities = clone($this->rootnodes['site']->children);
            $this->rootnodes['site']->remove();
            $children = clone($this->children);
            $this->children = new navigation_node_collection();
            foreach ($activities as $child) {
                $this->children->add($child);
            }
            foreach ($children as $child) {
                $this->children->add($child);
            }
        }
        return true;
    }

    /**
     * Returns true is courses should be shown within categories on the navigation.
     *
     * @return bool
     */
    protected function show_categories() {
        global $CFG, $DB;
        if ($this->showcategories === null) {
            $show = $this->page->context->contextlevel == CONTEXT_COURSECAT;
            $show = $show || (!empty($CFG->navshowcategories) && $DB->count_records('course_categories') > 1);
            $this->showcategories = $show;
        }
        return $this->showcategories;
    }

    /**
     * Checks the course format to see whether it wants the navigation to load
     * additional information for the course.
     *
     * This function utilises a callback that can exist within the course format lib.php file
     * The callback should be a function called:
     * callback_{formatname}_display_content()
     * It doesn't get any arguments and should return true if additional content is
     * desired. If the callback doesn't exist we assume additional content is wanted.
     *
     * @param string $format The course format
     * @return bool
     */
    protected function format_display_course_content($format) {
        global $CFG;
        $formatlib = $CFG->dirroot.'/course/format/'.$format.'/lib.php';
        if (file_exists($formatlib)) {
            require_once($formatlib);
            $displayfunc = 'callback_'.$format.'_display_content';
            if (function_exists($displayfunc) && !$displayfunc()) {
                return $displayfunc();
            }
        }
        return true;
    }

    /**
     * Loads of the the courses in Moodle into the navigation.
     *
     * @global moodle_database $DB
     * @param string|array $categoryids Either a string or array of category ids to load courses for
     * @return array An array of navigation_node
     */
    protected function load_all_courses($categoryids=null) {
        global $CFG, $DB, $USER;

        if ($categoryids !== null) {
            if (is_array($categoryids)) {
                list ($categoryselect, $params) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'catid');
            } else {
                $categoryselect = '= :categoryid';
                $params = array('categoryid', $categoryids);
            }
            $params['siteid'] = SITEID;
            $categoryselect = ' AND c.category '.$categoryselect;
        } else {
            $params = array('siteid' => SITEID);
            $categoryselect = '';
        }

        list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        list($courseids, $courseparams) = $DB->get_in_or_equal(array_keys($this->addedcourses) + array(SITEID), SQL_PARAMS_NAMED, 'lcourse', false);
        $sql = "SELECT c.id, c.sortorder, c.visible, c.fullname, c.shortname, c.category, cat.path AS categorypath $ccselect
                  FROM {course} c
                       $ccjoin
             LEFT JOIN {course_categories} cat ON cat.id=c.category
                 WHERE c.id {$courseids} {$categoryselect}
              ORDER BY c.sortorder ASC";
        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = $CFG->navcourselimit;
        }
        $courses = $DB->get_records_sql($sql, $params + $courseparams, 0, $limit);

        $coursenodes = array();
        foreach ($courses as $course) {
            context_instance_preload($course);
            $coursenodes[$course->id] = $this->add_course($course);
        }
        return $coursenodes;
    }

    /**
     * Loads all categories (top level or if an id is specified for that category)
     *
     * @param int $categoryid The category id to load or null/0 to load all base level categories
     * @param bool $showbasecategories If set to true all base level categories will be loaded as well
     *        as the requested category and any parent categories.
     * @return void
     */
    protected function load_all_categories($categoryid = null, $showbasecategories = false) {
        global $DB;

        // Check if this category has already been loaded
        if ($categoryid !== null && array_key_exists($categoryid, $this->addedcategories) && $this->addedcategories[$categoryid]->children->count() > 0) {
            return $this->addedcategories[$categoryid];
        }

        $coursestoload = array();
        if (empty($categoryid)) { // can be 0
            // We are going to load all of the first level categories (categories without parents)
            $categories = $DB->get_records('course_categories', array('parent'=>'0'), 'sortorder ASC, id ASC');
        } else if (array_key_exists($categoryid, $this->addedcategories)) {
            // The category itself has been loaded already so we just need to ensure its subcategories
            // have been loaded
            list($sql, $params) = $DB->get_in_or_equal(array_keys($this->addedcategories), SQL_PARAMS_NAMED, 'parent', false);
            if ($showbasecategories) {
                // We need to include categories with parent = 0 as well
                $sql = "SELECT *
                          FROM {course_categories} cc
                         WHERE (parent = :categoryid OR parent = 0) AND
                               parent {$sql}
                      ORDER BY depth DESC, sortorder ASC, id ASC";
            } else {
                $sql = "SELECT *
                          FROM {course_categories} cc
                         WHERE parent = :categoryid AND
                               parent {$sql}
                      ORDER BY depth DESC, sortorder ASC, id ASC";
            }
            $params['categoryid'] = $categoryid;
            $categories = $DB->get_records_sql($sql, $params);
            if (count($categories) == 0) {
                // There are no further categories that require loading.
                return;
            }
        } else {
            // This category hasn't been loaded yet so we need to fetch it, work out its category path
            // and load this category plus all its parents and subcategories
            $category = $DB->get_record('course_categories', array('id' => $categoryid), 'path', MUST_EXIST);
            $coursestoload = explode('/', trim($category->path, '/'));
            list($select, $params) = $DB->get_in_or_equal($coursestoload);
            $select = 'id '.$select.' OR parent '.$select;
            if ($showbasecategories) {
                $select .= ' OR parent = 0';
            }
            $params = array_merge($params, $params);
            $categories = $DB->get_records_select('course_categories', $select, $params, 'sortorder');
        }

        // Now we have an array of categories we need to add them to the navigation.
        while (!empty($categories)) {
            $category = reset($categories);
            if (array_key_exists($category->id, $this->addedcategories)) {
                // Do nothing
            } else if ($category->parent == '0') {
                $this->add_category($category, $this->rootnodes['courses']);
            } else if (array_key_exists($category->parent, $this->addedcategories)) {
                $this->add_category($category, $this->addedcategories[$category->parent]);
            } else {
                // This category isn't in the navigation and niether is it's parent (yet).
                // We need to go through the category path and add all of its components in order.
                $path = explode('/', trim($category->path, '/'));
                foreach ($path as $catid) {
                    if (!array_key_exists($catid, $this->addedcategories)) {
                        // This category isn't in the navigation yet so add it.
                        $subcategory = $categories[$catid];
                        if ($subcategory->parent == '0') {
                            // Yay we have a root category - this likely means we will now be able
                            // to add categories without problems.
                            $this->add_category($subcategory, $this->rootnodes['courses']);
                        } else if (array_key_exists($subcategory->parent, $this->addedcategories)) {
                            // The parent is in the category (as we'd expect) so add it now.
                            $this->add_category($subcategory, $this->addedcategories[$subcategory->parent]);
                            // Remove the category from the categories array.
                            unset($categories[$catid]);
                        } else {
                            // We should never ever arrive here - if we have then there is a bigger
                            // problem at hand.
                            throw new coding_exception('Category path order is incorrect and/or there are missing categories');
                        }
                    }
                }
            }
            // Remove the category from the categories array now that we know it has been added.
            unset($categories[$category->id]);
        }
        // Check if there are any categories to load.
        if (count($coursestoload) > 0) {
            $this->load_all_courses($coursestoload);
        }
    }

    /**
     * Adds a structured category to the navigation in the correct order/place
     *
     * @param stdClass $category
     * @param navigation_node $parent
     */
    protected function add_category(stdClass $category, navigation_node $parent) {
        if (array_key_exists($category->id, $this->addedcategories)) {
            return;
        }
        $url = new moodle_url('/course/category.php', array('id' => $category->id));
        $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
        $categoryname = format_string($category->name, true, array('context' => $context));
        $categorynode = $parent->add($categoryname, $url, self::TYPE_CATEGORY, $categoryname, $category->id);
        if (empty($category->visible)) {
            if (has_capability('moodle/category:viewhiddencategories', get_system_context())) {
                $categorynode->hidden = true;
            } else {
                $categorynode->display = false;
            }
        }
        $this->addedcategories[$category->id] = &$categorynode;
    }

    /**
     * Loads the given course into the navigation
     *
     * @param stdClass $course
     * @return navigation_node
     */
    protected function load_course(stdClass $course) {
        if ($course->id == SITEID) {
            return $this->rootnodes['site'];
        } else if (array_key_exists($course->id, $this->addedcourses)) {
            return $this->addedcourses[$course->id];
        } else {
            return $this->add_course($course);
        }
    }

    /**
     * Loads all of the courses section into the navigation.
     *
     * This function utilisies a callback that can be implemented within the course
     * formats lib.php file to customise the navigation that is generated at this
     * point for the course.
     *
     * By default (if not defined) the method {@see load_generic_course_sections} is
     * called instead.
     *
     * @param stdClass $course Database record for the course
     * @param navigation_node $coursenode The course node within the navigation
     * @return array Array of navigation nodes for the section with key = section id
     */
    protected function load_course_sections(stdClass $course, navigation_node $coursenode) {
        global $CFG;
        $structurefile = $CFG->dirroot.'/course/format/'.$course->format.'/lib.php';
        $structurefunc = 'callback_'.$course->format.'_load_content';
        if (function_exists($structurefunc)) {
            return $structurefunc($this, $course, $coursenode);
        } else if (file_exists($structurefile)) {
            require_once $structurefile;
            if (function_exists($structurefunc)) {
                return $structurefunc($this, $course, $coursenode);
            } else {
                return $this->load_generic_course_sections($course, $coursenode);
            }
        } else {
            return $this->load_generic_course_sections($course, $coursenode);
        }
    }

    /**
     * Generates an array of sections and an array of activities for the given course.
     *
     * This method uses the cache to improve performance and avoid the get_fast_modinfo call
     *
     * @param stdClass $course
     * @return array Array($sections, $activities)
     */
    protected function generate_sections_and_activities(stdClass $course) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        if (!$this->cache->cached('course_sections_'.$course->id) || !$this->cache->cached('course_activites_'.$course->id)) {
            $modinfo = get_fast_modinfo($course);
            $sections = array_slice(get_all_sections($course->id), 0, $course->numsections+1, true);

            $activities = array();

            foreach ($sections as $key => $section) {
                $sections[$key]->hasactivites = false;
                if (!array_key_exists($section->section, $modinfo->sections)) {
                    continue;
                }
                foreach ($modinfo->sections[$section->section] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible) {
                        continue;
                    }
                    $activity = new stdClass;
                    $activity->section = $section->section;
                    $activity->name = $cm->name;
                    $activity->icon = $cm->icon;
                    $activity->iconcomponent = $cm->iconcomponent;
                    $activity->id = $cm->id;
                    $activity->hidden = (!$cm->visible);
                    $activity->modname = $cm->modname;
                    $activity->nodetype = navigation_node::NODETYPE_LEAF;
                    $activity->onclick = $cm->get_on_click();
                    if (empty($activity->onclick) && !empty($cm->extra) && preg_match('/onclick=(\'|")([^\1]+)\1/', $cm->extra, $matches)) {
                        $activity->onclick = $matches[2];
                    }
                    $url = $cm->get_url();
                    if (!$url) {
                        $activity->url = null;
                        $activity->display = false;
                    } else {
                        $activity->url = $cm->get_url()->out();
                        $activity->display = true;
                        if (self::module_extends_navigation($cm->modname)) {
                            $activity->nodetype = navigation_node::NODETYPE_BRANCH;
                        }
                    }
                    $activities[$cmid] = $activity;
                    if ($activity->display) {
                        $sections[$key]->hasactivites = true;
                    }
                }
            }
            $this->cache->set('course_sections_'.$course->id, $sections);
            $this->cache->set('course_activites_'.$course->id, $activities);
        } else {
            $sections = $this->cache->{'course_sections_'.$course->id};
            $activities = $this->cache->{'course_activites_'.$course->id};
        }
        return array($sections, $activities);
    }

    /**
     * Generically loads the course sections into the course's navigation.
     *
     * @param stdClass $course
     * @param navigation_node $coursenode
     * @param string $courseformat The course format
     * @return array An array of course section nodes
     */
    public function load_generic_course_sections(stdClass $course, navigation_node $coursenode, $courseformat='unknown') {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/course/lib.php');

        list($sections, $activities) = $this->generate_sections_and_activities($course);

        $namingfunction = 'callback_'.$courseformat.'_get_section_name';
        $namingfunctionexists = (function_exists($namingfunction));
        $activesection = course_get_display($course->id);
        $viewhiddensections = has_capability('moodle/course:viewhiddensections', $this->page->context);

        $navigationsections = array();
        foreach ($sections as $sectionid => $section) {
            $section = clone($section);
            if ($course->id == SITEID) {
                $this->load_section_activities($coursenode, $section->section, $activities);
            } else {
                if ((!$viewhiddensections && !$section->visible) || (!$this->showemptysections &&
                        !$section->hasactivites && $this->includesectionnum !== $section->section)) {
                    continue;
                }
                if ($namingfunctionexists) {
                    $sectionname = $namingfunction($course, $section, $sections);
                } else {
                    $sectionname = get_string('section').' '.$section->section;
                }
                //$url = new moodle_url('/course/view.php', array('id'=>$course->id));
                $url = null;
                $sectionnode = $coursenode->add($sectionname, $url, navigation_node::TYPE_SECTION, null, $section->id);
                $sectionnode->nodetype = navigation_node::NODETYPE_BRANCH;
                $sectionnode->hidden = (!$section->visible);
                if ($this->page->context->contextlevel != CONTEXT_MODULE && $section->hasactivites && ($sectionnode->isactive || ($activesection && $section->section == $activesection))) {
                    $sectionnode->force_open();
                    $this->load_section_activities($sectionnode, $section->section, $activities);
                }
                $section->sectionnode = $sectionnode;
                $navigationsections[$sectionid] = $section;
            }
        }
        return $navigationsections;
    }
    /**
     * Loads all of the activities for a section into the navigation structure.
     *
     * @todo 2.2 - $activities should always be an array and we should no longer check for it being a
     *             course_modinfo object
     *
     * @param navigation_node $sectionnode
     * @param int $sectionnumber
     * @param course_modinfo $modinfo Object returned from {@see get_fast_modinfo()}
     * @return array Array of activity nodes
     */
    protected function load_section_activities(navigation_node $sectionnode, $sectionnumber, $activities) {
        // A static counter for JS function naming
        static $legacyonclickcounter = 0;

        if ($activities instanceof course_modinfo) {
            debugging('global_navigation::load_section_activities argument 3 should now recieve an array of activites. See that method for an example.', DEBUG_DEVELOPER);
            list($sections, $activities) = $this->generate_sections_and_activities($activities->course);
        }

        $activitynodes = array();
        foreach ($activities as $activity) {
            if ($activity->section != $sectionnumber) {
                continue;
            }
            if ($activity->icon) {
                $icon = new pix_icon($activity->icon, get_string('modulename', $activity->modname), $activity->iconcomponent);
            } else {
                $icon = new pix_icon('icon', get_string('modulename', $activity->modname), $activity->modname);
            }

            // Prepare the default name and url for the node
            $activityname = format_string($activity->name, true, array('context' => get_context_instance(CONTEXT_MODULE, $activity->id)));
            $action = new moodle_url($activity->url);

            // Check if the onclick property is set (puke!)
            if (!empty($activity->onclick)) {
                // Increment the counter so that we have a unique number.
                $legacyonclickcounter++;
                // Generate the function name we will use
                $functionname = 'legacy_activity_onclick_handler_'.$legacyonclickcounter;
                $propogrationhandler = '';
                // Check if we need to cancel propogation. Remember inline onclick
                // events would return false if they wanted to prevent propogation and the
                // default action.
                if (strpos($activity->onclick, 'return false')) {
                    $propogrationhandler = 'e.halt();';
                }
                // Decode the onclick - it has already been encoded for display (puke)
                $onclick = htmlspecialchars_decode($activity->onclick);
                // Build the JS function the click event will call
                $jscode = "function {$functionname}(e) { $propogrationhandler $onclick }";
                $this->page->requires->js_init_code($jscode);
                // Override the default url with the new action link
                $action = new action_link($action, $activityname, new component_action('click', $functionname));
            }

            $activitynode = $sectionnode->add($activityname, $action, navigation_node::TYPE_ACTIVITY, null, $activity->id, $icon);
            $activitynode->title(get_string('modulename', $activity->modname));
            $activitynode->hidden = $activity->hidden;
            $activitynode->display = $activity->display;
            $activitynode->nodetype = $activity->nodetype;
            $activitynodes[$activity->id] = $activitynode;
        }

        return $activitynodes;
    }
    /**
     * Loads a stealth module from unavailable section
     * @param navigation_node $coursenode
     * @param stdClass $modinfo
     * @return navigation_node or null if not accessible
     */
    protected function load_stealth_activity(navigation_node $coursenode, $modinfo) {
        if (empty($modinfo->cms[$this->page->cm->id])) {
            return null;
        }
        $cm = $modinfo->cms[$this->page->cm->id];
        if (!$cm->uservisible) {
            return null;
        }
        if ($cm->icon) {
            $icon = new pix_icon($cm->icon, get_string('modulename', $cm->modname), $cm->iconcomponent);
        } else {
            $icon = new pix_icon('icon', get_string('modulename', $cm->modname), $cm->modname);
        }
        $url = $cm->get_url();
        $activitynode = $coursenode->add(format_string($cm->name), $url, navigation_node::TYPE_ACTIVITY, null, $cm->id, $icon);
        $activitynode->title(get_string('modulename', $cm->modname));
        $activitynode->hidden = (!$cm->visible);
        if (!$url) {
            // Don't show activities that don't have links!
            $activitynode->display = false;
        } else if (self::module_extends_navigation($cm->modname)) {
            $activitynode->nodetype = navigation_node::NODETYPE_BRANCH;
        }
        return $activitynode;
    }
    /**
     * Loads the navigation structure for the given activity into the activities node.
     *
     * This method utilises a callback within the modules lib.php file to load the
     * content specific to activity given.
     *
     * The callback is a method: {modulename}_extend_navigation()
     * Examples:
     *  * {@see forum_extend_navigation()}
     *  * {@see workshop_extend_navigation()}
     *
     * @param cm_info|stdClass $cm
     * @param stdClass $course
     * @param navigation_node $activity
     * @return bool
     */
    protected function load_activity($cm, stdClass $course, navigation_node $activity) {
        global $CFG, $DB;

        // make sure we have a $cm from get_fast_modinfo as this contains activity access details
        if (!($cm instanceof cm_info)) {
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->get_cm($cm->id);
        }

        $activity->make_active();
        $file = $CFG->dirroot.'/mod/'.$cm->modname.'/lib.php';
        $function = $cm->modname.'_extend_navigation';

        if (file_exists($file)) {
            require_once($file);
            if (function_exists($function)) {
                $activtyrecord = $DB->get_record($cm->modname, array('id' => $cm->instance), '*', MUST_EXIST);
                $function($activity, $course, $activtyrecord, $cm);
                return true;
            }
        }
        $activity->nodetype = navigation_node::NODETYPE_LEAF;
        return false;
    }
    /**
     * Loads user specific information into the navigation in the appopriate place.
     *
     * If no user is provided the current user is assumed.
     *
     * @param stdClass $user
     * @return bool
     */
    protected function load_for_user($user=null, $forceforcontext=false) {
        global $DB, $CFG, $USER;

        if ($user === null) {
            // We can't require login here but if the user isn't logged in we don't
            // want to show anything
            if (!isloggedin() || isguestuser()) {
                return false;
            }
            $user = $USER;
        } else if (!is_object($user)) {
            // If the user is not an object then get them from the database
            list($select, $join) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
            $sql = "SELECT u.* $select FROM {user} u $join WHERE u.id = :userid";
            $user = $DB->get_record_sql($sql, array('userid' => (int)$user), MUST_EXIST);
            context_instance_preload($user);
        }

        $iscurrentuser = ($user->id == $USER->id);

        $usercontext = get_context_instance(CONTEXT_USER, $user->id);

        // Get the course set against the page, by default this will be the site
        $course = $this->page->course;
        $baseargs = array('id'=>$user->id);
        if ($course->id != SITEID && (!$iscurrentuser || $forceforcontext)) {
            $coursenode = $this->load_course($course);
            $baseargs['course'] = $course->id;
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            $issitecourse = false;
        } else {
            // Load all categories and get the context for the system
            $coursecontext = get_context_instance(CONTEXT_SYSTEM);
            $issitecourse = true;
        }

        // Create a node to add user information under.
        if ($iscurrentuser && !$forceforcontext) {
            // If it's the current user the information will go under the profile root node
            $usernode = $this->rootnodes['myprofile'];
        } else {
            if (!$issitecourse) {
                // Not the current user so add it to the participants node for the current course
                $usersnode = $coursenode->get('participants', navigation_node::TYPE_CONTAINER);
                $userviewurl = new moodle_url('/user/view.php', $baseargs);
            } else {
                // This is the site so add a users node to the root branch
                $usersnode = $this->rootnodes['users'];
                if (has_capability('moodle/course:viewparticipants', $coursecontext)) {
                    $usersnode->action = new moodle_url('/user/index.php', array('id'=>$course->id));
                }
                $userviewurl = new moodle_url('/user/profile.php', $baseargs);
            }
            if (!$usersnode) {
                // We should NEVER get here, if the course hasn't been populated
                // with a participants node then the navigaiton either wasn't generated
                // for it (you are missing a require_login or set_context call) or
                // you don't have access.... in the interests of no leaking informatin
                // we simply quit...
                return false;
            }
            // Add a branch for the current user
            $canseefullname = has_capability('moodle/site:viewfullnames', $coursecontext);
            $usernode = $usersnode->add(fullname($user, $canseefullname), $userviewurl, self::TYPE_USER, null, $user->id);

            if ($this->page->context->contextlevel == CONTEXT_USER && $user->id == $this->page->context->instanceid) {
                $usernode->make_active();
            }
        }

        // If the user is the current user or has permission to view the details of the requested
        // user than add a view profile link.
        if ($iscurrentuser || has_capability('moodle/user:viewdetails', $coursecontext) || has_capability('moodle/user:viewdetails', $usercontext)) {
            if ($issitecourse || ($iscurrentuser && !$forceforcontext)) {
                $usernode->add(get_string('viewprofile'), new moodle_url('/user/profile.php',$baseargs));
            } else {
                $usernode->add(get_string('viewprofile'), new moodle_url('/user/view.php',$baseargs));
            }
        }

        // Add nodes for forum posts and discussions if the user can view either or both
        // There are no capability checks here as the content of the page is based
        // purely on the forums the current user has access too.
        $forumtab = $usernode->add(get_string('forumposts', 'forum'));
        $forumtab->add(get_string('posts', 'forum'), new moodle_url('/mod/forum/user.php', $baseargs));
        $forumtab->add(get_string('discussions', 'forum'), new moodle_url('/mod/forum/user.php', array_merge($baseargs, array('mode'=>'discussions'))));

        // Add blog nodes
        if (!empty($CFG->bloglevel)) {
            if (!$this->cache->cached('userblogoptions'.$user->id)) {
                require_once($CFG->dirroot.'/blog/lib.php');
                // Get all options for the user
                $options = blog_get_options_for_user($user);
                $this->cache->set('userblogoptions'.$user->id, $options);
            } else {
                $options = $this->cache->{'userblogoptions'.$user->id};
            }

            if (count($options) > 0) {
                $blogs = $usernode->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER);
                foreach ($options as $type => $option) {
                    if ($type == "rss") {
                        $blogs->add($option['string'], $option['link'], settings_navigation::TYPE_SETTING, null, null, new pix_icon('i/rss', ''));
                    } else {
                        $blogs->add($option['string'], $option['link']);
                    }
                }
            }
        }

        if (!empty($CFG->messaging)) {
            $messageargs = null;
            if ($USER->id!=$user->id) {
                $messageargs = array('id'=>$user->id);
            }
            $url = new moodle_url('/message/index.php',$messageargs);
            $usernode->add(get_string('messages', 'message'), $url, self::TYPE_SETTING, null, 'messages');
        }

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        if ($iscurrentuser && has_capability('moodle/user:manageownfiles', $context)) {
            $url = new moodle_url('/user/files.php');
            $usernode->add(get_string('myfiles'), $url, self::TYPE_SETTING);
        }

        // Add a node to view the users notes if permitted
        if (!empty($CFG->enablenotes) && has_any_capability(array('moodle/notes:manage', 'moodle/notes:view'), $coursecontext)) {
            $url = new moodle_url('/notes/index.php',array('user'=>$user->id));
            if ($coursecontext->instanceid) {
                $url->param('course', $coursecontext->instanceid);
            }
            $usernode->add(get_string('notes', 'notes'), $url);
        }

        // Add a reports tab and then add reports the the user has permission to see.
        $anyreport      = has_capability('moodle/user:viewuseractivitiesreport', $usercontext);

        $outlinetreport = ($anyreport || has_capability('coursereport/outline:view', $coursecontext));
        $logtodayreport = ($anyreport || has_capability('coursereport/log:viewtoday', $coursecontext));
        $logreport      = ($anyreport || has_capability('coursereport/log:view', $coursecontext));
        $statsreport    = ($anyreport || has_capability('coursereport/stats:view', $coursecontext));

        $somereport     = $outlinetreport || $logtodayreport || $logreport || $statsreport;

        $viewreports = ($anyreport || $somereport || ($course->showreports && $iscurrentuser && $forceforcontext));
        if ($viewreports) {
            $reporttab = $usernode->add(get_string('activityreports'));
            $reportargs = array('user'=>$user->id);
            if (!empty($course->id)) {
                $reportargs['id'] = $course->id;
            } else {
                $reportargs['id'] = SITEID;
            }
            if ($viewreports || $outlinetreport) {
                $reporttab->add(get_string('outlinereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'outline'))));
                $reporttab->add(get_string('completereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'complete'))));
            }

            if ($viewreports || $logtodayreport) {
                $reporttab->add(get_string('todaylogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'todaylogs'))));
            }

            if ($viewreports || $logreport ) {
                $reporttab->add(get_string('alllogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'alllogs'))));
            }

            if (!empty($CFG->enablestats)) {
                if ($viewreports || $statsreport) {
                    $reporttab->add(get_string('stats'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'stats'))));
                }
            }

            $gradeaccess = false;
            if (has_capability('moodle/grade:viewall', $coursecontext)) {
                //ok - can view all course grades
                $gradeaccess = true;
            } else if ($course->showgrades) {
                if ($iscurrentuser && has_capability('moodle/grade:view', $coursecontext)) {
                    //ok - can view own grades
                    $gradeaccess = true;
                } else if (has_capability('moodle/grade:viewall', $usercontext)) {
                    // ok - can view grades of this user - parent most probably
                    $gradeaccess = true;
                } else if ($anyreport) {
                    // ok - can view grades of this user - parent most probably
                    $gradeaccess = true;
                }
            }
            if ($gradeaccess) {
                $reporttab->add(get_string('grade'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'grade'))));
            }

            // Check the number of nodes in the report node... if there are none remove
            // the node
            $reporttab->trim_if_empty();
        }

        // If the user is the current user add the repositories for the current user
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
        if ($iscurrentuser) {
            if (!$this->cache->cached('contexthasrepos'.$usercontext->id)) {
                require_once($CFG->dirroot . '/repository/lib.php');
                $editabletypes = repository::get_editable_types($usercontext);
                $haseditabletypes = !empty($editabletypes);
                unset($editabletypes);
                $this->cache->set('contexthasrepos'.$usercontext->id, $haseditabletypes);
            } else {
                $haseditabletypes = $this->cache->{'contexthasrepos'.$usercontext->id};
            }
            if ($haseditabletypes) {
                $usernode->add(get_string('repositories', 'repository'), new moodle_url('/repository/manage_instances.php', array('contextid' => $usercontext->id)));
            }
        } else if ($course->id == SITEID && has_capability('moodle/user:viewdetails', $usercontext) && (!in_array('mycourses', $hiddenfields) || has_capability('moodle/user:viewhiddendetails', $coursecontext))) {

            // Add view grade report is permitted
            $reports = get_plugin_list('gradereport');
            arsort($reports); // user is last, we want to test it first

            $userscourses = enrol_get_users_courses($user->id);
            $userscoursesnode = $usernode->add(get_string('courses'));

            foreach ($userscourses as $usercourse) {
                $usercoursecontext = get_context_instance(CONTEXT_COURSE, $usercourse->id);
                $usercourseshortname = format_string($usercourse->shortname, true, array('context' => $usercoursecontext));
                $usercoursenode = $userscoursesnode->add($usercourseshortname, new moodle_url('/user/view.php', array('id'=>$user->id, 'course'=>$usercourse->id)), self::TYPE_CONTAINER);

                $gradeavailable = has_capability('moodle/grade:viewall', $usercoursecontext);
                if (!$gradeavailable && !empty($usercourse->showgrades) && is_array($reports) && !empty($reports)) {
                    foreach ($reports as $plugin => $plugindir) {
                        if (has_capability('gradereport/'.$plugin.':view', $usercoursecontext)) {
                            //stop when the first visible plugin is found
                            $gradeavailable = true;
                            break;
                        }
                    }
                }

                if ($gradeavailable) {
                    $url = new moodle_url('/grade/report/index.php', array('id'=>$usercourse->id));
                    $usercoursenode->add(get_string('grades'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/grades', ''));
                }

                // Add a node to view the users notes if permitted
                if (!empty($CFG->enablenotes) && has_any_capability(array('moodle/notes:manage', 'moodle/notes:view'), $usercoursecontext)) {
                    $url = new moodle_url('/notes/index.php',array('user'=>$user->id, 'course'=>$usercourse->id));
                    $usercoursenode->add(get_string('notes', 'notes'), $url, self::TYPE_SETTING);
                }

                if (can_access_course(get_context_instance(CONTEXT_COURSE, $usercourse->id), $user->id)) {
                    $usercoursenode->add(get_string('entercourse'), new moodle_url('/course/view.php', array('id'=>$usercourse->id)), self::TYPE_SETTING, null, null, new pix_icon('i/course', ''));
                }

                $outlinetreport = ($anyreport || has_capability('coursereport/outline:view', $usercoursecontext));
                $logtodayreport = ($anyreport || has_capability('coursereport/log:viewtoday', $usercoursecontext));
                $logreport =      ($anyreport || has_capability('coursereport/log:view', $usercoursecontext));
                $statsreport =    ($anyreport || has_capability('coursereport/stats:view', $usercoursecontext));
                if ($outlinetreport || $logtodayreport || $logreport || $statsreport) {
                    $reporttab = $usercoursenode->add(get_string('activityreports'));
                    $reportargs = array('user'=>$user->id, 'id'=>$usercourse->id);
                    if ($outlinetreport) {
                        $reporttab->add(get_string('outlinereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'outline'))));
                        $reporttab->add(get_string('completereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'complete'))));
                    }

                    if ($logtodayreport) {
                        $reporttab->add(get_string('todaylogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'todaylogs'))));
                    }

                    if ($logreport) {
                        $reporttab->add(get_string('alllogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'alllogs'))));
                    }

                    if (!empty($CFG->enablestats) && $statsreport) {
                        $reporttab->add(get_string('stats'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'stats'))));
                    }
                }
            }
        }
        return true;
    }

    /**
     * This method simply checks to see if a given module can extend the navigation.
     *
     * TODO: A shared caching solution should be used to save details on what extends navigation
     *
     * @param string $modname
     * @return bool
     */
    protected static function module_extends_navigation($modname) {
        global $CFG;
        static $extendingmodules = array();
        if (!array_key_exists($modname, $extendingmodules)) {
            $extendingmodules[$modname] = false;
            $file = $CFG->dirroot.'/mod/'.$modname.'/lib.php';
            if (file_exists($file)) {
                $function = $modname.'_extend_navigation';
                require_once($file);
                $extendingmodules[$modname] = (function_exists($function));
            }
        }
        return $extendingmodules[$modname];
    }
    /**
     * Extends the navigation for the given user.
     *
     * @param stdClass $user A user from the database
     */
    public function extend_for_user($user) {
        $this->extendforuser[] = $user;
    }

    /**
     * Returns all of the users the navigation is being extended for
     *
     * @return array
     */
    public function get_extending_users() {
        return $this->extendforuser;
    }
    /**
     * Adds the given course to the navigation structure.
     *
     * @param stdClass $course
     * @return navigation_node
     */
    public function add_course(stdClass $course, $forcegeneric = false, $ismycourse = false) {
        global $CFG;

        // We found the course... we can return it now :)
        if (!$forcegeneric && array_key_exists($course->id, $this->addedcourses)) {
            return $this->addedcourses[$course->id];
        }

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

        if ($course->id != SITEID && !$course->visible) {
            if (is_role_switched($course->id)) {
                // user has to be able to access course in order to switch, let's skip the visibility test here
            } else if (!has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                return false;
            }
        }

        $issite = ($course->id == SITEID);
        $ismycourse = ($ismycourse && !$forcegeneric);
        $shortname = format_string($course->shortname, true, array('context' => $coursecontext));

        if ($issite) {
            $parent = $this;
            $url = null;
            $shortname = get_string('sitepages');
        } else if ($ismycourse) {
            $parent = $this->rootnodes['mycourses'];
            $url = new moodle_url('/course/view.php', array('id'=>$course->id));
        } else {
            $parent = $this->rootnodes['courses'];
            $url = new moodle_url('/course/view.php', array('id'=>$course->id));
        }

        if (!$ismycourse && !$issite && !empty($course->category)) {
            if ($this->show_categories()) {
                // We need to load the category structure for this course
                $this->load_all_categories($course->category);
            }
            if (array_key_exists($course->category, $this->addedcategories)) {
                $parent = $this->addedcategories[$course->category];
                // This could lead to the course being created so we should check whether it is the case again
                if (!$forcegeneric && array_key_exists($course->id, $this->addedcourses)) {
                    return $this->addedcourses[$course->id];
                }
            }
        }

        $coursenode = $parent->add($shortname, $url, self::TYPE_COURSE, $shortname, $course->id);
        $coursenode->nodetype = self::NODETYPE_BRANCH;
        $coursenode->hidden = (!$course->visible);
        $coursenode->title(format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id))));
        if (!$forcegeneric) {
            $this->addedcourses[$course->id] = &$coursenode;
        }
        if ($ismycourse && !empty($CFG->navshowallcourses)) {
            // We need to add this course to the general courses node as well as the
            // my courses node, rerun the function with the kill param
            $genericcourse = $this->add_course($course, true);
            if ($genericcourse->isactive) {
                $genericcourse->make_inactive();
                $genericcourse->collapse = true;
                if ($genericcourse->parent && $genericcourse->parent->type == self::TYPE_CATEGORY) {
                    $parent = $genericcourse->parent;
                    while ($parent && $parent->type == self::TYPE_CATEGORY) {
                        $parent->collapse = true;
                        $parent = $parent->parent;
                    }
                }
            }
        }

        return $coursenode;
    }
    /**
     * Adds essential course nodes to the navigation for the given course.
     *
     * This method adds nodes such as reports, blogs and participants
     *
     * @param navigation_node $coursenode
     * @param stdClass $course
     * @return bool
     */
    public function add_course_essentials($coursenode, stdClass $course) {
        global $CFG;

        if ($course->id == SITEID) {
            return $this->add_front_page_course_essentials($coursenode, $course);
        }

        if ($coursenode == false || !($coursenode instanceof navigation_node) || $coursenode->get('participants', navigation_node::TYPE_CONTAINER)) {
            return true;
        }

        //Participants
        if (has_capability('moodle/course:viewparticipants', $this->page->context)) {
            $participants = $coursenode->add(get_string('participants'), new moodle_url('/user/index.php?id='.$course->id), self::TYPE_CONTAINER, get_string('participants'), 'participants');
            $currentgroup = groups_get_course_group($course, true);
            if ($course->id == SITEID) {
                $filterselect = '';
            } else if ($course->id && !$currentgroup) {
                $filterselect = $course->id;
            } else {
                $filterselect = $currentgroup;
            }
            $filterselect = clean_param($filterselect, PARAM_INT);
            if (($CFG->bloglevel == BLOG_GLOBAL_LEVEL or ($CFG->bloglevel == BLOG_SITE_LEVEL and (isloggedin() and !isguestuser())))
               and has_capability('moodle/blog:view', get_context_instance(CONTEXT_SYSTEM))) {
                $blogsurls = new moodle_url('/blog/index.php', array('courseid' => $filterselect));
                $participants->add(get_string('blogscourse','blog'), $blogsurls->out());
            }
            if (!empty($CFG->enablenotes) && (has_capability('moodle/notes:manage', $this->page->context) || has_capability('moodle/notes:view', $this->page->context))) {
                $participants->add(get_string('notes','notes'), new moodle_url('/notes/index.php', array('filtertype'=>'course', 'filterselect'=>$course->id)));
            }
        } else if (count($this->extendforuser) > 0 || $this->page->course->id == $course->id) {
            $participants = $coursenode->add(get_string('participants'), null, self::TYPE_CONTAINER, get_string('participants'), 'participants');
        }

        // View course reports
        if (has_capability('moodle/site:viewreports', $this->page->context)) { // basic capability for listing of reports
            $reportnav = $coursenode->add(get_string('reports'), new moodle_url('/course/report.php', array('id'=>$course->id)), self::TYPE_CONTAINER, null, null, new pix_icon('i/stats', ''));
            $coursereports = get_plugin_list('coursereport');
            foreach ($coursereports as $report=>$dir) {
                $libfile = $CFG->dirroot.'/course/report/'.$report.'/lib.php';
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $reportfunction = $report.'_report_extend_navigation';
                    if (function_exists($report.'_report_extend_navigation')) {
                        $reportfunction($reportnav, $course, $this->page->context);
                    }
                }
            }
        }
        return true;
    }
    /**
     * This generates the the structure of the course that won't be generated when
     * the modules and sections are added.
     *
     * Things such as the reports branch, the participants branch, blogs... get
     * added to the course node by this method.
     *
     * @param navigation_node $coursenode
     * @param stdClass $course
     * @return bool True for successfull generation
     */
    public function add_front_page_course_essentials(navigation_node $coursenode, stdClass $course) {
        global $CFG;

        if ($coursenode == false || $coursenode->get('frontpageloaded', navigation_node::TYPE_CUSTOM)) {
            return true;
        }

        // Hidden node that we use to determine if the front page navigation is loaded.
        // This required as there are not other guaranteed nodes that may be loaded.
        $coursenode->add('frontpageloaded', null, self::TYPE_CUSTOM, null, 'frontpageloaded')->display = false;

        //Participants
        if (has_capability('moodle/course:viewparticipants',  get_system_context())) {
            $coursenode->add(get_string('participants'), new moodle_url('/user/index.php?id='.$course->id), self::TYPE_CUSTOM, get_string('participants'), 'participants');
        }

        $filterselect = 0;

        // Blogs
        if (!empty($CFG->bloglevel)
          and ($CFG->bloglevel == BLOG_GLOBAL_LEVEL or ($CFG->bloglevel == BLOG_SITE_LEVEL and (isloggedin() and !isguestuser())))
          and has_capability('moodle/blog:view', get_context_instance(CONTEXT_SYSTEM))) {
            $blogsurls = new moodle_url('/blog/index.php', array('courseid' => $filterselect));
            $coursenode->add(get_string('blogssite','blog'), $blogsurls->out());
        }

        // Notes
        if (!empty($CFG->enablenotes) && (has_capability('moodle/notes:manage', $this->page->context) || has_capability('moodle/notes:view', $this->page->context))) {
            $coursenode->add(get_string('notes','notes'), new moodle_url('/notes/index.php', array('filtertype'=>'course', 'filterselect'=>$filterselect)));
        }

        // Tags
        if (!empty($CFG->usetags) && isloggedin()) {
            $coursenode->add(get_string('tags', 'tag'), new moodle_url('/tag/search.php'));
        }

        if (isloggedin()) {
            // Calendar
            $calendarurl = new moodle_url('/calendar/view.php', array('view' => 'month'));
            $coursenode->add(get_string('calendar', 'calendar'), $calendarurl, self::TYPE_CUSTOM, null, 'calendar');
        }

        // View course reports
        if (has_capability('moodle/site:viewreports', $this->page->context)) { // basic capability for listing of reports
            $reportnav = $coursenode->add(get_string('reports'), new moodle_url('/course/report.php', array('id'=>$course->id)), self::TYPE_CONTAINER, null, null, new pix_icon('i/stats', ''));
            $coursereports = get_plugin_list('coursereport');
            foreach ($coursereports as $report=>$dir) {
                $libfile = $CFG->dirroot.'/course/report/'.$report.'/lib.php';
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $reportfunction = $report.'_report_extend_navigation';
                    if (function_exists($report.'_report_extend_navigation')) {
                        $reportfunction($reportnav, $course, $this->page->context);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Clears the navigation cache
     */
    public function clear_cache() {
        $this->cache->clear();
    }

    /**
     * Sets an expansion limit for the navigation
     *
     * The expansion limit is used to prevent the display of content that has a type
     * greater than the provided $type.
     *
     * Can be used to ensure things such as activities or activity content don't get
     * shown on the navigation.
     * They are still generated in order to ensure the navbar still makes sense.
     *
     * @param int $type One of navigation_node::TYPE_*
     * @return <type>
     */
    public function set_expansion_limit($type) {
        $nodes = $this->find_all_of_type($type);
        foreach ($nodes as &$node) {
            // We need to generate the full site node
            if ($type == self::TYPE_COURSE && $node->key == SITEID) {
                continue;
            }
            foreach ($node->children as &$child) {
                // We still want to show course reports and participants containers
                // or there will be navigation missing.
                if ($type == self::TYPE_COURSE && $child->type === self::TYPE_CONTAINER) {
                    continue;
                }
                $child->display = false;
            }
        }
        return true;
    }
    /**
     * Attempts to get the navigation with the given key from this nodes children.
     *
     * This function only looks at this nodes children, it does NOT look recursivily.
     * If the node can't be found then false is returned.
     *
     * If you need to search recursivily then use the {@see find()} method.
     *
     * Note: If you are trying to set the active node {@see navigation_node::override_active_url()}
     * may be of more use to you.
     *
     * @param string|int $key The key of the node you wish to receive.
     * @param int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function get($key, $type = null) {
        if (!$this->initialised) {
            $this->initialise();
        }
        return parent::get($key, $type);
    }

    /**
     * Searches this nodes children and thier children to find a navigation node
     * with the matching key and type.
     *
     * This method is recursive and searches children so until either a node is
     * found of there are no more nodes to search.
     *
     * If you know that the node being searched for is a child of this node
     * then use the {@see get()} method instead.
     *
     * Note: If you are trying to set the active node {@see navigation_node::override_active_url()}
     * may be of more use to you.
     *
     * @param string|int $key The key of the node you wish to receive.
     * @param int $type One of navigation_node::TYPE_*
     * @return navigation_node|false
     */
    public function find($key, $type) {
        if (!$this->initialised) {
            $this->initialise();
        }
        return parent::find($key, $type);
    }
}

/**
 * The limited global navigation class used for the AJAX extension of the global
 * navigation class.
 *
 * The primary methods that are used in the global navigation class have been overriden
 * to ensure that only the relevant branch is generated at the root of the tree.
 * This can be done because AJAX is only used when the backwards structure for the
 * requested branch exists.
 * This has been done only because it shortens the amounts of information that is generated
 * which of course will speed up the response time.. because no one likes laggy AJAX.
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_navigation_for_ajax extends global_navigation {

    protected $branchtype;
    protected $instanceid;

    /** @var array */
    protected $expandable = array();

    /**
     * Constructs the navigation for use in AJAX request
     */
    public function __construct($page, $branchtype, $id) {
        $this->page = $page;
        $this->cache = new navigation_cache(NAVIGATION_CACHE_NAME);
        $this->children = new navigation_node_collection();
        $this->branchtype = $branchtype;
        $this->instanceid = $id;
        $this->initialise();
    }
    /**
     * Initialise the navigation given the type and id for the branch to expand.
     *
     * @return array The expandable nodes
     */
    public function initialise() {
        global $CFG, $DB, $SITE;

        if ($this->initialised || during_initial_install()) {
            return $this->expandable;
        }
        $this->initialised = true;

        $this->rootnodes = array();
        $this->rootnodes['site']    = $this->add_course($SITE);
        $this->rootnodes['courses'] = $this->add(get_string('courses'), null, self::TYPE_ROOTNODE, null, 'courses');

        // Branchtype will be one of navigation_node::TYPE_*
        switch ($this->branchtype) {
            case self::TYPE_CATEGORY :
                $this->load_all_categories($this->instanceid);
                $limit = 20;
                if (!empty($CFG->navcourselimit)) {
                    $limit = (int)$CFG->navcourselimit;
                }
                $courses = $DB->get_records('course', array('category' => $this->instanceid), 'sortorder','*', 0, $limit);
                foreach ($courses as $course) {
                    $this->add_course($course);
                }
                break;
            case self::TYPE_COURSE :
                $course = $DB->get_record('course', array('id' => $this->instanceid), '*', MUST_EXIST);
                require_course_login($course);
                $this->page->set_context(get_context_instance(CONTEXT_COURSE, $course->id));
                $coursenode = $this->add_course($course);
                $this->add_course_essentials($coursenode, $course);
                if ($this->format_display_course_content($course->format)) {
                    $this->load_course_sections($course, $coursenode);
                }
                break;
            case self::TYPE_SECTION :
                $sql = 'SELECT c.*, cs.section AS sectionnumber
                        FROM {course} c
                        LEFT JOIN {course_sections} cs ON cs.course = c.id
                        WHERE cs.id = ?';
                $course = $DB->get_record_sql($sql, array($this->instanceid), MUST_EXIST);
                require_course_login($course);
                $this->page->set_context(get_context_instance(CONTEXT_COURSE, $course->id));
                $coursenode = $this->add_course($course);
                $this->add_course_essentials($coursenode, $course);
                $sections = $this->load_course_sections($course, $coursenode);
                list($sectionarray, $activities) = $this->generate_sections_and_activities($course);
                $this->load_section_activities($sections[$course->sectionnumber]->sectionnode, $course->sectionnumber, $activities);
                break;
            case self::TYPE_ACTIVITY :
                $sql = "SELECT c.*
                          FROM {course} c
                          JOIN {course_modules} cm ON cm.course = c.id
                         WHERE cm.id = :cmid";
                $params = array('cmid' => $this->instanceid);
                $course = $DB->get_record_sql($sql, $params, MUST_EXIST);
                $modinfo = get_fast_modinfo($course);
                $cm = $modinfo->get_cm($this->instanceid);
                require_course_login($course, true, $cm);
                $this->page->set_context(get_context_instance(CONTEXT_MODULE, $cm->id));
                $coursenode = $this->load_course($course);
                if ($course->id == SITEID) {
                    $modulenode = $this->load_activity($cm, $course, $coursenode->find($cm->id, self::TYPE_ACTIVITY));
                } else {
                    $sections   = $this->load_course_sections($course, $coursenode);
                    list($sectionarray, $activities) = $this->generate_sections_and_activities($course);
                    $activities = $this->load_section_activities($sections[$cm->sectionnum]->sectionnode, $cm->sectionnum, $activities);
                    $modulenode = $this->load_activity($cm, $course, $activities[$cm->id]);
                }
                break;
            default:
                throw new Exception('Unknown type');
                return $this->expandable;
        }

        if ($this->page->context->contextlevel == CONTEXT_COURSE && $this->page->context->instanceid != SITEID) {
            $this->load_for_user(null, true);
        }

        $this->find_expandable($this->expandable);
        return $this->expandable;
    }

    /**
     * Returns an array of expandable nodes
     * @return array
     */
    public function get_expandable() {
        return $this->expandable;
    }
}

/**
 * Navbar class
 *
 * This class is used to manage the navbar, which is initialised from the navigation
 * object held by PAGE
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navbar extends navigation_node {
    /** @var bool */
    protected $initialised = false;
    /** @var mixed */
    protected $keys = array();
    /** @var null|string */
    protected $content = null;
    /** @var moodle_page object */
    protected $page;
    /** @var bool */
    protected $ignoreactive = false;
    /** @var bool */
    protected $duringinstall = false;
    /** @var bool */
    protected $hasitems = false;
    /** @var array */
    protected $items;
    /** @var array */
    public $children = array();
    /** @var bool */
    public $includesettingsbase = false;
    /**
     * The almighty constructor
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page $page) {
        global $CFG;
        if (during_initial_install()) {
            $this->duringinstall = true;
            return false;
        }
        $this->page = $page;
        $this->text = get_string('home');
        $this->shorttext = get_string('home');
        $this->action = new moodle_url($CFG->wwwroot);
        $this->nodetype = self::NODETYPE_BRANCH;
        $this->type = self::TYPE_SYSTEM;
    }

    /**
     * Quick check to see if the navbar will have items in.
     *
     * @return bool Returns true if the navbar will have items, false otherwise
     */
    public function has_items() {
        if ($this->duringinstall) {
            return false;
        } else if ($this->hasitems !== false) {
            return true;
        }
        $this->page->navigation->initialise($this->page);

        $activenodefound = ($this->page->navigation->contains_active_node() ||
                            $this->page->settingsnav->contains_active_node());

        $outcome = (count($this->children)>0 || (!$this->ignoreactive && $activenodefound));
        $this->hasitems = $outcome;
        return $outcome;
    }

    /**
     * Turn on/off ignore active
     *
     * @param bool $setting
     */
    public function ignore_active($setting=true) {
        $this->ignoreactive = ($setting);
    }
    public function get($key, $type = null) {
        foreach ($this->children as &$child) {
            if ($child->key === $key && ($type == null || $type == $child->type)) {
                return $child;
            }
        }
        return false;
    }
    /**
     * Returns an array of navigation_node's that make up the navbar.
     *
     * @return array
     */
    public function get_items() {
        $items = array();
        // Make sure that navigation is initialised
        if (!$this->has_items()) {
            return $items;
        }
        if ($this->items !== null) {
            return $this->items;
        }

        if (count($this->children) > 0) {
            // Add the custom children
            $items = array_reverse($this->children);
        }

        $navigationactivenode = $this->page->navigation->find_active_node();
        $settingsactivenode = $this->page->settingsnav->find_active_node();

        // Check if navigation contains the active node
        if (!$this->ignoreactive) {

            if ($navigationactivenode && $settingsactivenode) {
                // Parse a combined navigation tree
                while ($settingsactivenode && $settingsactivenode->parent !== null) {
                    if (!$settingsactivenode->mainnavonly) {
                        $items[] = $settingsactivenode;
                    }
                    $settingsactivenode = $settingsactivenode->parent;
                }
                if (!$this->includesettingsbase) {
                    // Removes the first node from the settings (root node) from the list
                    array_pop($items);
                }
                while ($navigationactivenode && $navigationactivenode->parent !== null) {
                    if (!$navigationactivenode->mainnavonly) {
                        $items[] = $navigationactivenode;
                    }
                    $navigationactivenode = $navigationactivenode->parent;
                }
            } else if ($navigationactivenode) {
                // Parse the navigation tree to get the active node
                while ($navigationactivenode && $navigationactivenode->parent !== null) {
                    if (!$navigationactivenode->mainnavonly) {
                        $items[] = $navigationactivenode;
                    }
                    $navigationactivenode = $navigationactivenode->parent;
                }
            } else if ($settingsactivenode) {
                // Parse the settings navigation to get the active node
                while ($settingsactivenode && $settingsactivenode->parent !== null) {
                    if (!$settingsactivenode->mainnavonly) {
                        $items[] = $settingsactivenode;
                    }
                    $settingsactivenode = $settingsactivenode->parent;
                }
            }
        }

        $items[] = new navigation_node(array(
            'text'=>$this->page->navigation->text,
            'shorttext'=>$this->page->navigation->shorttext,
            'key'=>$this->page->navigation->key,
            'action'=>$this->page->navigation->action
        ));

        $this->items = array_reverse($items);
        return $this->items;
    }

    /**
     * Add a new navigation_node to the navbar, overrides parent::add
     *
     * This function overrides {@link navigation_node::add()} so that we can change
     * the way nodes get added to allow us to simply call add and have the node added to the
     * end of the navbar
     *
     * @param string $text
     * @param string|moodle_url $action
     * @param int $type
     * @param string|int $key
     * @param string $shorttext
     * @param string $icon
     * @return navigation_node
     */
    public function add($text, $action=null, $type=self::TYPE_CUSTOM, $shorttext=null, $key=null, pix_icon $icon=null) {
        if ($this->content !== null) {
            debugging('Nav bar items must be printed before $OUTPUT->header() has been called', DEBUG_DEVELOPER);
        }

        // Properties array used when creating the new navigation node
        $itemarray = array(
            'text' => $text,
            'type' => $type
        );
        // Set the action if one was provided
        if ($action!==null) {
            $itemarray['action'] = $action;
        }
        // Set the shorttext if one was provided
        if ($shorttext!==null) {
            $itemarray['shorttext'] = $shorttext;
        }
        // Set the icon if one was provided
        if ($icon!==null) {
            $itemarray['icon'] = $icon;
        }
        // Default the key to the number of children if not provided
        if ($key === null) {
            $key = count($this->children);
        }
        // Set the key
        $itemarray['key'] = $key;
        // Set the parent to this node
        $itemarray['parent'] = $this;
        // Add the child using the navigation_node_collections add method
        $this->children[] = new navigation_node($itemarray);
        return $this;
    }
}

/**
 * Class used to manage the settings option for the current page
 *
 * This class is used to manage the settings options in a tree format (recursively)
 * and was created initially for use with the settings blocks.
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_navigation extends navigation_node {
    /** @var stdClass */
    protected $context;
    /** @var moodle_page */
    protected $page;
    /** @var string */
    protected $adminsection;
    /** @var bool */
    protected $initialised = false;
    /** @var array */
    protected $userstoextendfor = array();
    /** @var navigation_cache **/
    protected $cache;

    /**
     * Sets up the object with basic settings and preparse it for use
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page &$page) {
        if (during_initial_install()) {
            return false;
        }
        $this->page = $page;
        // Initialise the main navigation. It is most important that this is done
        // before we try anything
        $this->page->navigation->initialise();
        // Initialise the navigation cache
        $this->cache = new navigation_cache(NAVIGATION_CACHE_NAME);
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
        global $DB, $SESSION;

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
            $context = $DB->get_record_sql('SELECT ctx.* FROM {block_instances} bi LEFT JOIN {context} ctx ON ctx.id=bi.parentcontextid WHERE bi.id=?', array($context->instanceid));
        }

        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                if ($this->page->url->compare(new moodle_url('/admin/settings.php', array('section'=>'frontpagesettings')))) {
                    $this->load_front_page_settings(($context->id == $this->context->id));
                }
                break;
            case CONTEXT_COURSECAT:
                $this->load_category_settings();
                break;
            case CONTEXT_COURSE:
                if ($this->page->course->id != SITEID) {
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
                if ($this->page->course->id != SITEID) {
                    $this->load_course_settings();
                }
                break;
        }

        $settings = $this->load_user_settings($this->page->course->id);

        if (isloggedin() && !isguestuser() && (!property_exists($SESSION, 'load_navigation_admin') || $SESSION->load_navigation_admin)) {
            $admin = $this->load_administration_settings();
            $SESSION->load_navigation_admin = ($admin->has_children());
        } else {
            $admin = false;
        }

        if ($context->contextlevel == CONTEXT_SYSTEM && $admin) {
            $admin->force_open();
        } else if ($context->contextlevel == CONTEXT_USER && $settings) {
            $settings->force_open();
        }

        // Check if the user is currently logged in as another user
        if (session_is_loggedinas()) {
            // Get the actual user, we need this so we can display an informative return link
            $realuser = session_get_realuser();
            // Add the informative return to original user link
            $url = new moodle_url('/course/loginas.php',array('id'=>$this->page->course->id, 'return'=>1,'sesskey'=>sesskey()));
            $this->add(get_string('returntooriginaluser', 'moodle', fullname($realuser, true)), $url, self::TYPE_SETTING, null, null, new pix_icon('t/left', ''));
        }

        foreach ($this->children as $key=>$node) {
            if ($node->nodetype != self::NODETYPE_BRANCH || $node->children->count()===0) {
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
     * @param string $text
     * @param sting|moodle_url $url
     * @param string $shorttext
     * @param string|int $key
     * @param int $type
     * @param string $icon
     * @return navigation_node
     */
    public function add($text, $url=null, $type=null, $shorttext=null, $key=null, pix_icon $icon=null) {
        $node = parent::add($text, $url, $type, $shorttext, $key, $icon);
        $node->add_class('root_node');
        return $node;
    }

    /**
     * This function allows the user to add something to the start of the settings
     * navigation, which means it will be at the top of the settings navigation block
     *
     * @param string $text
     * @param sting|moodle_url $url
     * @param string $shorttext
     * @param string|int $key
     * @param int $type
     * @param string $icon
     * @return navigation_node
     */
    public function prepend($text, $url=null, $type=null, $shorttext=null, $key=null, pix_icon $icon=null) {
        $children = $this->children;
        $childrenclass = get_class($children);
        $this->children = new $childrenclass;
        $node = $this->add($text, $url, $type, $shorttext, $key, $icon);
        foreach ($children as $child) {
            $this->children->add($child);
        }
        return $node;
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
    protected function load_administration_settings(navigation_node $referencebranch=null, part_of_admin_tree $adminbranch=null) {
        global $CFG;

        // Check if we are just starting to generate this navigation.
        if ($referencebranch === null) {

            // Require the admin lib then get an admin structure
            if (!function_exists('admin_get_root')) {
                require_once($CFG->dirroot.'/lib/adminlib.php');
            }
            $adminroot = admin_get_root(false, false);
            // This is the active section identifier
            $this->adminsection = $this->page->url->param('section');

            // Disable the navigation from automatically finding the active node
            navigation_node::$autofindactive = false;
            $referencebranch = $this->add(get_string('administrationsite'), null, self::TYPE_SETTING, null, 'root');
            foreach ($adminroot->children as $adminbranch) {
                $this->load_administration_settings($referencebranch, $adminbranch);
            }
            navigation_node::$autofindactive = true;

            // Use the admin structure to locate the active page
            if (!$this->contains_active_node() && $current = $adminroot->locate($this->adminsection, true)) {
                $currentnode = $this;
                while (($pathkey = array_pop($current->path))!==null && $currentnode) {
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
            // Now we need to display it and any children it may have
            $url = null;
            $icon = null;
            if ($adminbranch instanceof admin_settingpage) {
                $url = new moodle_url('/admin/settings.php', array('section'=>$adminbranch->name));
            } else if ($adminbranch instanceof admin_externalpage) {
                $url = $adminbranch->url;
            }

            // Add the branch
            $reference = $referencebranch->add($adminbranch->visiblename, $url, self::TYPE_SETTING, null, $adminbranch->name, $icon);

            if ($adminbranch->is_hidden()) {
                if (($adminbranch instanceof admin_externalpage || $adminbranch instanceof admin_settingpage) && $adminbranch->name == $this->adminsection) {
                    $reference->add_class('hidden');
                } else {
                    $reference->display = false;
                }
            }

            // Check if we are generating the admin notifications and whether notificiations exist
            if ($adminbranch->name === 'adminnotifications' && admin_critical_warnings_present()) {
                $reference->add_class('criticalnotification');
            }
            // Check if this branch has children
            if ($reference && isset($adminbranch->children) && is_array($adminbranch->children) && count($adminbranch->children)>0) {
                foreach ($adminbranch->children as $branch) {
                    // Generate the child branches as well now using this branch as the reference
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
        if (!$node->check_if_active() && $node->children->count()>0) {
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
     * Generate the list of modules for the given course.
     *
     * @param stdClass $course The course to get modules for
     */
    protected function get_course_modules($course) {
        global $CFG;
        $mods = $modnames = $modnamesplural = $modnamesused = array();
        // This function is included when we include course/lib.php at the top
        // of this file
        get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
        $resources = array();
        $activities = array();
        foreach($modnames as $modname=>$modnamestr) {
            if (!course_allowed_module($course, $modname)) {
                continue;
            }

            $libfile = "$CFG->dirroot/mod/$modname/lib.php";
            if (!file_exists($libfile)) {
                continue;
            }
            include_once($libfile);
            $gettypesfunc =  $modname.'_get_types';
            if (function_exists($gettypesfunc)) {
                $types = $gettypesfunc();
                foreach($types as $type) {
                    if (!isset($type->modclass) || !isset($type->typestr)) {
                        debugging('Incorrect activity type in '.$modname);
                        continue;
                    }
                    if ($type->modclass == MOD_CLASS_RESOURCE) {
                        $resources[html_entity_decode($type->type)] = $type->typestr;
                    } else {
                        $activities[html_entity_decode($type->type)] = $type->typestr;
                    }
                }
            } else {
                $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
                if ($archetype == MOD_ARCHETYPE_RESOURCE) {
                    $resources[$modname] = $modnamestr;
                } else {
                    // all other archetypes are considered activity
                    $activities[$modname] = $modnamestr;
                }
            }
        }
        return array($resources, $activities);
    }

    /**
     * This function loads the course settings that are available for the user
     *
     * @param bool $forceopen If set to true the course node will be forced open
     * @return navigation_node|false
     */
    protected function load_course_settings($forceopen = false) {
        global $CFG;

        $course = $this->page->course;
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

        // note: do not test if enrolled or viewing here because we need the enrol link in Course administration section

        $coursenode = $this->add(get_string('courseadministration'), null, self::TYPE_COURSE, null, 'courseadmin');
        if ($forceopen) {
            $coursenode->force_open();
        }

        if (has_capability('moodle/course:update', $coursecontext)) {
            // Add the turn on/off settings
            $url = new moodle_url('/course/view.php', array('id'=>$course->id, 'sesskey'=>sesskey()));
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $editstring = get_string('turneditingoff');
            } else {
                $url->param('edit', 'on');
                $editstring = get_string('turneditingon');
            }
            $coursenode->add($editstring, $url, self::TYPE_SETTING, null, null, new pix_icon('i/edit', ''));

            if ($this->page->user_is_editing()) {
                // Removed as per MDL-22732
                // $this->add_course_editing_links($course);
            }

            // Add the course settings link
            $url = new moodle_url('/course/edit.php', array('id'=>$course->id));
            $coursenode->add(get_string('editsettings'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));

            // Add the course completion settings link
            if ($CFG->enablecompletion && $course->enablecompletion) {
                $url = new moodle_url('/course/completion.php', array('id'=>$course->id));
                $coursenode->add(get_string('completion', 'completion'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
            }
        }

        // add enrol nodes
        enrol_add_course_navigation($coursenode, $course);

        // Manage filters
        if (has_capability('moodle/filter:manage', $coursecontext) && count(filter_get_available_in_context($coursecontext))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$coursecontext->id));
            $coursenode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/filter', ''));
        }

        // Add view grade report is permitted
        $reportavailable = false;
        if (has_capability('moodle/grade:viewall', $coursecontext)) {
            $reportavailable = true;
        } else if (!empty($course->showgrades)) {
            $reports = get_plugin_list('gradereport');
            if (is_array($reports) && count($reports)>0) {     // Get all installed reports
                arsort($reports); // user is last, we want to test it first
                foreach ($reports as $plugin => $plugindir) {
                    if (has_capability('gradereport/'.$plugin.':view', $coursecontext)) {
                        //stop when the first visible plugin is found
                        $reportavailable = true;
                        break;
                    }
                }
            }
        }
        if ($reportavailable) {
            $url = new moodle_url('/grade/report/index.php', array('id'=>$course->id));
            $gradenode = $coursenode->add(get_string('grades'), $url, self::TYPE_SETTING, null, 'grades', new pix_icon('i/grades', ''));
        }

        //  Add outcome if permitted
        if (!empty($CFG->enableoutcomes) && has_capability('moodle/course:update', $coursecontext)) {
            $url = new moodle_url('/grade/edit/outcome/course.php', array('id'=>$course->id));
            $coursenode->add(get_string('outcomes', 'grades'), $url, self::TYPE_SETTING, null, 'outcomes', new pix_icon('i/outcomes', ''));
        }

        // Backup this course
        if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
            $url = new moodle_url('/backup/backup.php', array('id'=>$course->id));
            $coursenode->add(get_string('backup'), $url, self::TYPE_SETTING, null, 'backup', new pix_icon('i/backup', ''));
        }

        // Restore to this course
        if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
            $url = new moodle_url('/backup/restorefile.php', array('contextid'=>$coursecontext->id));
            $coursenode->add(get_string('restore'), $url, self::TYPE_SETTING, null, 'restore', new pix_icon('i/restore', ''));
        }

        // Import data from other courses
        if (has_capability('moodle/restore:restoretargetimport', $coursecontext)) {
            $url = new moodle_url('/backup/import.php', array('id'=>$course->id));
            $coursenode->add(get_string('import'), $url, self::TYPE_SETTING, null, 'import', new pix_icon('i/restore', ''));
        }

        // Publish course on a hub
        if (has_capability('moodle/course:publish', $coursecontext)) {
            $url = new moodle_url('/course/publish/index.php', array('id'=>$course->id));
            $coursenode->add(get_string('publish'), $url, self::TYPE_SETTING, null, 'publish', new pix_icon('i/publish', ''));
        }

        // Reset this course
        if (has_capability('moodle/course:reset', $coursecontext)) {
            $url = new moodle_url('/course/reset.php', array('id'=>$course->id));
            $coursenode->add(get_string('reset'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/return', ''));
        }

        // Questions
        require_once($CFG->libdir . '/questionlib.php');
        question_extend_settings_navigation($coursenode, $coursecontext)->trim_if_empty();

        if (has_capability('moodle/course:update', $coursecontext)) {
            // Repository Instances
            if (!$this->cache->cached('contexthasrepos'.$coursecontext->id)) {
                require_once($CFG->dirroot . '/repository/lib.php');
                $editabletypes = repository::get_editable_types($coursecontext);
                $haseditabletypes = !empty($editabletypes);
                unset($editabletypes);
                $this->cache->set('contexthasrepos'.$coursecontext->id, $haseditabletypes);
            } else {
                $haseditabletypes = $this->cache->{'contexthasrepos'.$coursecontext->id};
            }
            if ($haseditabletypes) {
                $url = new moodle_url('/repository/manage_instances.php', array('contextid' => $coursecontext->id));
                $coursenode->add(get_string('repositories'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/repository', ''));
            }
        }

        // Manage files
        if ($course->legacyfiles == 2 and has_capability('moodle/course:managefiles', $coursecontext)) {
            // hidden in new courses and courses where legacy files were turned off
            $url = new moodle_url('/files/index.php', array('contextid'=>$coursecontext->id));
            $coursenode->add(get_string('courselegacyfiles'), $url, self::TYPE_SETTING, null, 'coursefiles', new pix_icon('i/files', ''));
        }

        // Switch roles
        $roles = array();
        $assumedrole = $this->in_alternative_role();
        if ($assumedrole !== false) {
            $roles[0] = get_string('switchrolereturn');
        }
        if (has_capability('moodle/role:switchroles', $coursecontext)) {
            $availableroles = get_switchable_roles($coursecontext);
            if (is_array($availableroles)) {
                foreach ($availableroles as $key=>$role) {
                    if ($assumedrole == (int)$key) {
                        continue;
                    }
                    $roles[$key] = $role;
                }
            }
        }
        if (is_array($roles) && count($roles)>0) {
            $switchroles = $this->add(get_string('switchroleto'));
            if ((count($roles)==1 && array_key_exists(0, $roles))|| $assumedrole!==false) {
                $switchroles->force_open();
            }
            $returnurl = $this->page->url;
            $returnurl->param('sesskey', sesskey());
            foreach ($roles as $key => $name) {
                $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>$key, 'returnurl'=>$returnurl->out(false)));
                $switchroles->add($name, $url, self::TYPE_SETTING, null, $key, new pix_icon('i/roles', ''));
            }
        }
        // Return we are done
        return $coursenode;
    }

    /**
     * Adds branches and links to the settings navigation to add course activities
     * and resources.
     *
     * @param stdClass $course
     */
    protected function add_course_editing_links($course) {
        global $CFG;

        require_once($CFG->dirroot.'/course/lib.php');

        // Add `add` resources|activities branches
        $structurefile = $CFG->dirroot.'/course/format/'.$course->format.'/lib.php';
        if (file_exists($structurefile)) {
            require_once($structurefile);
            $requestkey = call_user_func('callback_'.$course->format.'_request_key');
            $formatidentifier = optional_param($requestkey, 0, PARAM_INT);
        } else {
            $requestkey = get_string('section');
            $formatidentifier = optional_param($requestkey, 0, PARAM_INT);
        }

        $sections = get_all_sections($course->id);

        $addresource = $this->add(get_string('addresource'));
        $addactivity = $this->add(get_string('addactivity'));
        if ($formatidentifier!==0) {
            $addresource->force_open();
            $addactivity->force_open();
        }

        $this->get_course_modules($course);

        $textlib = textlib_get_instance();

        foreach ($sections as $section) {
            if ($formatidentifier !== 0 && $section->section != $formatidentifier) {
                continue;
            }
            $sectionurl = new moodle_url('/course/view.php', array('id'=>$course->id, $requestkey=>$section->section));
            if ($section->section == 0) {
                $sectionresources = $addresource->add(get_string('course'), $sectionurl, self::TYPE_SETTING);
                $sectionactivities = $addactivity->add(get_string('course'), $sectionurl, self::TYPE_SETTING);
            } else {
                $sectionname = get_section_name($course, $section);
                $sectionresources = $addresource->add($sectionname, $sectionurl, self::TYPE_SETTING);
                $sectionactivities = $addactivity->add($sectionname, $sectionurl, self::TYPE_SETTING);
            }
            foreach ($resources as $value=>$resource) {
                $url = new moodle_url('/course/mod.php', array('id'=>$course->id, 'sesskey'=>sesskey(), 'section'=>$section->section));
                $pos = strpos($value, '&type=');
                if ($pos!==false) {
                    $url->param('add', $textlib->substr($value, 0,$pos));
                    $url->param('type', $textlib->substr($value, $pos+6));
                } else {
                    $url->param('add', $value);
                }
                $sectionresources->add($resource, $url, self::TYPE_SETTING);
            }
            $subbranch = false;
            foreach ($activities as $activityname=>$activity) {
                if ($activity==='--') {
                    $subbranch = false;
                    continue;
                }
                if (strpos($activity, '--')===0) {
                    $subbranch = $sectionactivities->add(trim($activity, '-'));
                    continue;
                }
                $url = new moodle_url('/course/mod.php', array('id'=>$course->id, 'sesskey'=>sesskey(), 'section'=>$section->section));
                $pos = strpos($activityname, '&type=');
                if ($pos!==false) {
                    $url->param('add', $textlib->substr($activityname, 0,$pos));
                    $url->param('type', $textlib->substr($activityname, $pos+6));
                } else {
                    $url->param('add', $activityname);
                }
                if ($subbranch !== false) {
                    $subbranch->add($activity, $url, self::TYPE_SETTING);
                } else {
                    $sectionactivities->add($activity, $url, self::TYPE_SETTING);
                }
            }
        }
    }

    /**
     * This function calls the module function to inject module settings into the
     * settings navigation tree.
     *
     * This only gets called if there is a corrosponding function in the modules
     * lib file.
     *
     * For examples mod/forum/lib.php ::: forum_extend_settings_navigation()
     *
     * @return navigation_node|false
     */
    protected function load_module_settings() {
        global $CFG;

        if (!$this->page->cm && $this->context->contextlevel == CONTEXT_MODULE && $this->context->instanceid) {
            $cm = get_coursemodule_from_id(false, $this->context->instanceid, 0, false, MUST_EXIST);
            $this->page->set_cm($cm, $this->page->course);
        }

        $file = $CFG->dirroot.'/mod/'.$this->page->activityname.'/lib.php';
        if (file_exists($file)) {
            require_once($file);
        }

        $modulenode = $this->add(get_string('pluginadministration', $this->page->activityname));
        $modulenode->force_open();

        // Settings for the module
        if (has_capability('moodle/course:manageactivities', $this->page->cm->context)) {
            $url = new moodle_url('/course/modedit.php', array('update' => $this->page->cm->id, 'return' => true, 'sesskey' => sesskey()));
            $modulenode->add(get_string('editsettings'), $url, navigation_node::TYPE_SETTING, null, 'modedit');
        }
        // Assign local roles
        if (count(get_assignable_roles($this->page->cm->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('localroles', 'role'), $url, self::TYPE_SETTING, null, 'roleassign');
        }
        // Override roles
        if (has_capability('moodle/role:review', $this->page->cm->context) or count(get_overridable_roles($this->page->cm->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING, null, 'roleoverride');
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $this->page->cm->context)) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING, null, 'rolecheck');
        }
        // Manage filters
        if (has_capability('moodle/filter:manage', $this->page->cm->context) && count(filter_get_available_in_context($this->page->cm->context))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, 'filtermanage');
        }

        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_COURSE, $this->page->cm->course))) {
            $url = new moodle_url('/course/report/log/index.php', array('chooselog'=>'1','id'=>$this->page->cm->course,'modid'=>$this->page->cm->id));
            $modulenode->add(get_string('logs'), $url, self::TYPE_SETTING, null, 'logreport');
        }

        // Add a backup link
        $featuresfunc = $this->page->activityname.'_supports';
        if (function_exists($featuresfunc) && $featuresfunc(FEATURE_BACKUP_MOODLE2) && has_capability('moodle/backup:backupactivity', $this->page->cm->context)) {
            $url = new moodle_url('/backup/backup.php', array('id'=>$this->page->cm->course, 'cm'=>$this->page->cm->id));
            $modulenode->add(get_string('backup'), $url, self::TYPE_SETTING, null, 'backup');
        }

        // Restore this activity
        $featuresfunc = $this->page->activityname.'_supports';
        if (function_exists($featuresfunc) && $featuresfunc(FEATURE_BACKUP_MOODLE2) && has_capability('moodle/restore:restoreactivity', $this->page->cm->context)) {
            $url = new moodle_url('/backup/restorefile.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('restore'), $url, self::TYPE_SETTING, null, 'restore');
        }

        $function = $this->page->activityname.'_extend_settings_navigation';
        if (!function_exists($function)) {
            return $modulenode;
        }

        $function($this, $modulenode);

        // Remove the module node if there are no children
        if (empty($modulenode->children)) {
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
    protected function load_user_settings($courseid=SITEID) {
        global $USER, $FULLME, $CFG;

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
                $children = array();
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
     * This function gets called by {@link load_user_settings()} and actually works out
     * what can be shown/done
     *
     * @global moodle_database $DB
     * @param int $courseid The current course' id
     * @param int $userid The user id to load for
     * @param string $gstitle The string to pass to get_string for the branch title
     * @return navigation_node|false
     */
    protected function generate_user_settings($courseid, $userid, $gstitle='usercurrentsettings') {
        global $DB, $CFG, $USER, $SITE;

        if ($courseid != SITEID) {
            if (!empty($this->page->course->id) && $this->page->course->id == $courseid) {
                $course = $this->page->course;
            } else {
                list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
                $sql = "SELECT c.* $select FROM {course} c $join WHERE c.id = :courseid";
                $course = $DB->get_record_sql($sql, array('courseid' => $courseid), MUST_EXIST);
                context_instance_preload($course);
            }
        } else {
            $course = $SITE;
        }

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
        $systemcontext   = get_system_context();
        $currentuser = ($USER->id == $userid);

        if ($currentuser) {
            $user = $USER;
            $usercontext = get_context_instance(CONTEXT_USER, $user->id);       // User context
        } else {

            list($select, $join) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
            $sql = "SELECT u.* $select FROM {user} u $join WHERE u.id = :userid";
            $user = $DB->get_record_sql($sql, array('userid' => $userid), IGNORE_MISSING);
            if (!$user) {
                return false;
            }
            context_instance_preload($user);

            // Check that the user can view the profile
            $usercontext = get_context_instance(CONTEXT_USER, $user->id); // User context
            $canviewuser = has_capability('moodle/user:viewdetails', $usercontext);

            if ($course->id == SITEID) {
                if ($CFG->forceloginforprofiles && !has_coursecontact_role($user->id) && !$canviewuser) {  // Reduce possibility of "browsing" userbase at site level
                    // Teachers can browse and be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
                    return false;
                }
            } else {
                $canviewusercourse = has_capability('moodle/user:viewdetails', $coursecontext);
                $canaccessallgroups = has_capability('moodle/site:accessallgroups', $coursecontext);
                if ((!$canviewusercourse && !$canviewuser) || !can_access_course($coursecontext, $user->id)) {
                    return false;
                }
                if (!$canaccessallgroups && groups_get_course_groupmode($course) == SEPARATEGROUPS) {
                    // If groups are in use, make sure we can see that group
                    return false;
                }
            }
        }

        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $this->page->context));

        $key = $gstitle;
        if ($gstitle != 'usercurrentsettings') {
            $key .= $userid;
        }

        // Add a user setting branch
        $usersetting = $this->add(get_string($gstitle, 'moodle', $fullname), null, self::TYPE_CONTAINER, null, $key);
        $usersetting->id = 'usersettings';
        if ($this->page->context->contextlevel == CONTEXT_USER && $this->page->context->instanceid == $user->id) {
            // Automatically start by making it active
            $usersetting->make_active();
        }

        // Check if the user has been deleted
        if ($user->deleted) {
            if (!has_capability('moodle/user:update', $coursecontext)) {
                // We can't edit the user so just show the user deleted message
                $usersetting->add(get_string('userdeleted'), null, self::TYPE_SETTING);
            } else {
                // We can edit the user so show the user deleted message and link it to the profile
                if ($course->id == SITEID) {
                    $profileurl = new moodle_url('/user/profile.php', array('id'=>$user->id));
                } else {
                    $profileurl = new moodle_url('/user/view.php', array('id'=>$user->id, 'course'=>$course->id));
                }
                $usersetting->add(get_string('userdeleted'), $profileurl, self::TYPE_SETTING);
            }
            return true;
        }

        $userauthplugin = false;
        if (!empty($user->auth)) {
            $userauthplugin = get_auth_plugin($user->auth);
        }

        // Add the profile edit link
        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (($currentuser || is_siteadmin($USER) || !is_siteadmin($user)) && has_capability('moodle/user:update', $systemcontext)) {
                $url = new moodle_url('/user/editadvanced.php', array('id'=>$user->id, 'course'=>$course->id));
                $usersetting->add(get_string('editmyprofile'), $url, self::TYPE_SETTING);
            } else if ((has_capability('moodle/user:editprofile', $usercontext) && !is_siteadmin($user)) || ($currentuser && has_capability('moodle/user:editownprofile', $systemcontext))) {
                if ($userauthplugin && $userauthplugin->can_edit_profile()) {
                    $url = $userauthplugin->edit_profile_url();
                    if (empty($url)) {
                        $url = new moodle_url('/user/edit.php', array('id'=>$user->id, 'course'=>$course->id));
                    }
                    $usersetting->add(get_string('editmyprofile'), $url, self::TYPE_SETTING);
                }
            }
        }

        // Change password link
        if ($userauthplugin && $currentuser && !session_is_loggedinas() && !isguestuser() && has_capability('moodle/user:changeownpassword', $systemcontext) && $userauthplugin->can_change_password()) {
            $passwordchangeurl = $userauthplugin->change_password_url();
            if (empty($passwordchangeurl)) {
                $passwordchangeurl = new moodle_url('/login/change_password.php', array('id'=>$course->id));
            }
            $usersetting->add(get_string("changepassword"), $passwordchangeurl, self::TYPE_SETTING);
        }

        // View the roles settings
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:manage'), $usercontext)) {
            $roles = $usersetting->add(get_string('roles'), null, self::TYPE_SETTING);

            $url = new moodle_url('/admin/roles/usersroles.php', array('userid'=>$user->id, 'courseid'=>$course->id));
            $roles->add(get_string('thisusersroles', 'role'), $url, self::TYPE_SETTING);

            $assignableroles = get_assignable_roles($usercontext, ROLENAME_BOTH);

            if (!empty($assignableroles)) {
                $url = new moodle_url('/admin/roles/assign.php', array('contextid'=>$usercontext->id,'userid'=>$user->id, 'courseid'=>$course->id));
                $roles->add(get_string('assignrolesrelativetothisuser', 'role'), $url, self::TYPE_SETTING);
            }

            if (has_capability('moodle/role:review', $usercontext) || count(get_overridable_roles($usercontext, ROLENAME_BOTH))>0) {
                $url = new moodle_url('/admin/roles/permissions.php', array('contextid'=>$usercontext->id,'userid'=>$user->id, 'courseid'=>$course->id));
                $roles->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING);
            }

            $url = new moodle_url('/admin/roles/check.php', array('contextid'=>$usercontext->id,'userid'=>$user->id, 'courseid'=>$course->id));
            $roles->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING);
        }

        // Portfolio
        if ($currentuser && !empty($CFG->enableportfolios) && has_capability('moodle/portfolio:export', $systemcontext)) {
            require_once($CFG->libdir . '/portfoliolib.php');
            if (portfolio_instances(true, false)) {
                $portfolio = $usersetting->add(get_string('portfolios', 'portfolio'), null, self::TYPE_SETTING);

                $url = new moodle_url('/user/portfolio.php', array('courseid'=>$course->id));
                $portfolio->add(get_string('configure', 'portfolio'), $url, self::TYPE_SETTING);

                $url = new moodle_url('/user/portfoliologs.php', array('courseid'=>$course->id));
                $portfolio->add(get_string('logs', 'portfolio'), $url, self::TYPE_SETTING);
            }
        }

        $enablemanagetokens = false;
        if (!empty($CFG->enablerssfeeds)) {
            $enablemanagetokens = true;
        } else if (!is_siteadmin($USER->id)
             && !empty($CFG->enablewebservices)
             && has_capability('moodle/webservice:createtoken', get_system_context()) ) {
            $enablemanagetokens = true;
        }
        // Security keys
        if ($currentuser && $enablemanagetokens) {
            $url = new moodle_url('/user/managetoken.php', array('sesskey'=>sesskey()));
            $usersetting->add(get_string('securitykeys', 'webservice'), $url, self::TYPE_SETTING);
        }

        // Repository
        if (!$currentuser && $usercontext->contextlevel == CONTEXT_USER) {
            if (!$this->cache->cached('contexthasrepos'.$usercontext->id)) {
                require_once($CFG->dirroot . '/repository/lib.php');
                $editabletypes = repository::get_editable_types($usercontext);
                $haseditabletypes = !empty($editabletypes);
                unset($editabletypes);
                $this->cache->set('contexthasrepos'.$usercontext->id, $haseditabletypes);
            } else {
                $haseditabletypes = $this->cache->{'contexthasrepos'.$usercontext->id};
            }
            if ($haseditabletypes) {
                $url = new moodle_url('/repository/manage_instances.php', array('contextid'=>$usercontext->id));
                $usersetting->add(get_string('repositories', 'repository'), $url, self::TYPE_SETTING);
            }
        }

        // Messaging
        if (($currentuser && has_capability('moodle/user:editownmessageprofile', $systemcontext)) || (!isguestuser($user) && has_capability('moodle/user:editmessageprofile', $usercontext) && !is_primary_admin($user->id))) {
            $url = new moodle_url('/message/edit.php', array('id'=>$user->id, 'course'=>$course->id));
            $usersetting->add(get_string('editmymessage', 'message'), $url, self::TYPE_SETTING);
        }

        // Blogs
        if ($currentuser && !empty($CFG->bloglevel)) {
            $blog = $usersetting->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER, null, 'blogs');
            $blog->add(get_string('preferences', 'blog'), new moodle_url('/blog/preferences.php'), navigation_node::TYPE_SETTING);
            if (!empty($CFG->useexternalblogs) && $CFG->maxexternalblogsperuser > 0 && has_capability('moodle/blog:manageexternal', get_context_instance(CONTEXT_SYSTEM))) {
                $blog->add(get_string('externalblogs', 'blog'), new moodle_url('/blog/external_blogs.php'), navigation_node::TYPE_SETTING);
                $blog->add(get_string('addnewexternalblog', 'blog'), new moodle_url('/blog/external_blog_edit.php'), navigation_node::TYPE_SETTING);
            }
        }

        // Login as ...
        if (!$user->deleted and !$currentuser && !session_is_loggedinas() && has_capability('moodle/user:loginas', $coursecontext) && !is_siteadmin($user->id)) {
            $url = new moodle_url('/course/loginas.php', array('id'=>$course->id, 'user'=>$user->id, 'sesskey'=>sesskey()));
            $usersetting->add(get_string('loginas'), $url, self::TYPE_SETTING);
        }

        return $usersetting;
    }

    /**
     * Loads block specific settings in the navigation
     *
     * @return navigation_node
     */
    protected function load_block_settings() {
        global $CFG;

        $blocknode = $this->add(print_context_name($this->context));
        $blocknode->force_open();

        // Assign local roles
        $assignurl = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$this->context->id));
        $blocknode->add(get_string('assignroles', 'role'), $assignurl, self::TYPE_SETTING);

        // Override roles
        if (has_capability('moodle/role:review', $this->context) or  count(get_overridable_roles($this->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$this->context->id));
            $blocknode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING);
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $this->context)) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$this->context->id));
            $blocknode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING);
        }

        return $blocknode;
    }

    /**
     * Loads category specific settings in the navigation
     *
     * @return navigation_node
     */
    protected function load_category_settings() {
        global $CFG;

        $categorynode = $this->add(print_context_name($this->context));
        $categorynode->force_open();

        if (has_any_capability(array('moodle/category:manage', 'moodle/course:create'), $this->context)) {
            $url = new moodle_url('/course/category.php', array('id'=>$this->context->instanceid, 'sesskey'=>sesskey()));
            if ($this->page->user_is_editing()) {
                $url->param('categoryedit', '0');
                $editstring = get_string('turneditingoff');
            } else {
                $url->param('categoryedit', '1');
                $editstring = get_string('turneditingon');
            }
            $categorynode->add($editstring, $url, self::TYPE_SETTING, null, null, new pix_icon('i/edit', ''));
        }

        if ($this->page->user_is_editing() && has_capability('moodle/category:manage', $this->context)) {
            $editurl = new moodle_url('/course/editcategory.php', array('id' => $this->context->instanceid));
            $categorynode->add(get_string('editcategorythis'), $editurl, self::TYPE_SETTING, null, 'edit', new pix_icon('i/edit', ''));

            $addsubcaturl = new moodle_url('/course/editcategory.php', array('parent' => $this->context->instanceid));
            $categorynode->add(get_string('addsubcategory'), $addsubcaturl, self::TYPE_SETTING, null, 'addsubcat', new pix_icon('i/withsubcat', ''));
        }

        // Assign local roles
        if (has_capability('moodle/role:assign', $this->context)) {
            $assignurl = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('assignroles', 'role'), $assignurl, self::TYPE_SETTING, null, 'roles', new pix_icon('i/roles', ''));
        }

        // Override roles
        if (has_capability('moodle/role:review', $this->context) or count(get_overridable_roles($this->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING, null, 'permissions', new pix_icon('i/permissions', ''));
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $this->context)) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING, null, 'checkpermissions', new pix_icon('i/checkpermissions', ''));
        }

        // Cohorts
        if (has_capability('moodle/cohort:manage', $this->context) or has_capability('moodle/cohort:view', $this->context)) {
            $categorynode->add(get_string('cohorts', 'cohort'), new moodle_url('/cohort/index.php', array('contextid' => $this->context->id)), self::TYPE_SETTING, null, 'cohort', new pix_icon('i/cohort', ''));
        }

        // Manage filters
        if (has_capability('moodle/filter:manage', $this->context) && count(filter_get_available_in_context($this->context))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, 'filters', new pix_icon('i/filter', ''));
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
            foreach ($USER->access['rsw'] as $key=>$role) {
                if (strpos($this->context->path,$key)===0) {
                    return $role;
                }
            }
        }
        return false;
    }

    /**
     * This function loads all of the front page settings into the settings navigation.
     * This function is called when the user is on the front page, or $COURSE==$SITE
     * @return navigation_node
     */
    protected function load_front_page_settings($forceopen = false) {
        global $SITE, $CFG;

        $course = clone($SITE);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context

        $frontpage = $this->add(get_string('frontpagesettings'), null, self::TYPE_SETTING, null, 'frontpage');
        if ($forceopen) {
            $frontpage->force_open();
        }
        $frontpage->id = 'frontpagesettings';

        if (has_capability('moodle/course:update', $coursecontext)) {

            // Add the turn on/off settings
            $url = new moodle_url('/course/view.php', array('id'=>$course->id, 'sesskey'=>sesskey()));
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $editstring = get_string('turneditingoff');
            } else {
                $url->param('edit', 'on');
                $editstring = get_string('turneditingon');
            }
            $frontpage->add($editstring, $url, self::TYPE_SETTING, null, null, new pix_icon('i/edit', ''));

            // Add the course settings link
            $url = new moodle_url('/admin/settings.php', array('section'=>'frontpagesettings'));
            $frontpage->add(get_string('editsettings'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
        }

        // add enrol nodes
        enrol_add_course_navigation($frontpage, $course);

        // Manage filters
        if (has_capability('moodle/filter:manage', $coursecontext) && count(filter_get_available_in_context($coursecontext))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$coursecontext->id));
            $frontpage->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/filter', ''));
        }

        // Backup this course
        if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
            $url = new moodle_url('/backup/backup.php', array('id'=>$course->id));
            $frontpage->add(get_string('backup'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/backup', ''));
        }

        // Restore to this course
        if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
            $url = new moodle_url('/backup/restorefile.php', array('contextid'=>$coursecontext->id));
            $frontpage->add(get_string('restore'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/restore', ''));
        }

        // Questions
        require_once($CFG->libdir . '/questionlib.php');
        question_extend_settings_navigation($frontpage, $coursecontext)->trim_if_empty();

        // Manage files
        if ($course->legacyfiles == 2 and has_capability('moodle/course:managefiles', $this->context)) {
            //hiden in new installs
            $url = new moodle_url('/files/index.php', array('contextid'=>$coursecontext->id, 'itemid'=>0, 'component' => 'course', 'filearea'=>'legacy'));
            $frontpage->add(get_string('sitelegacyfiles'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/files', ''));
        }
        return $frontpage;
    }

    /**
     * This function marks the cache as volatile so it is cleared during shutdown
     */
    public function clear_cache() {
        $this->cache->volatile();
    }
}

/**
 * Simple class used to output a navigation branch in XML
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_json {
    /** @var array */
    protected $nodetype = array('node','branch');
    /** @var array */
    protected $expandable = array();
    /**
     * Turns a branch and all of its children into XML
     *
     * @param navigation_node $branch
     * @return string XML string
     */
    public function convert($branch) {
        $xml = $this->convert_child($branch);
        return $xml;
    }
    /**
     * Set the expandable items in the array so that we have enough information
     * to attach AJAX events
     * @param array $expandable
     */
    public function set_expandable($expandable) {
        foreach ($expandable as $node) {
            $this->expandable[$node['key'].':'.$node['type']] = $node;
        }
    }
    /**
     * Recusively converts a child node and its children to XML for output
     *
     * @param navigation_node $child The child to convert
     * @param int $depth Pointlessly used to track the depth of the XML structure
     * @return string JSON
     */
    protected function convert_child($child, $depth=1) {
        if (!$child->display) {
            return '';
        }
        $attributes = array();
        $attributes['id'] = $child->id;
        $attributes['name'] = $child->text;
        $attributes['type'] = $child->type;
        $attributes['key'] = $child->key;
        $attributes['class'] = $child->get_css_type();

        if ($child->icon instanceof pix_icon) {
            $attributes['icon'] = array(
                'component' => $child->icon->component,
                'pix' => $child->icon->pix,
            );
            foreach ($child->icon->attributes as $key=>$value) {
                if ($key == 'class') {
                    $attributes['icon']['classes'] = explode(' ', $value);
                } else if (!array_key_exists($key, $attributes['icon'])) {
                    $attributes['icon'][$key] = $value;
                }

            }
        } else if (!empty($child->icon)) {
            $attributes['icon'] = (string)$child->icon;
        }

        if ($child->forcetitle || $child->title !== $child->text) {
            $attributes['title'] = htmlentities($child->title);
        }
        if (array_key_exists($child->key.':'.$child->type, $this->expandable)) {
            $attributes['expandable'] = $child->key;
            $child->add_class($this->expandable[$child->key.':'.$child->type]['id']);
        }

        if (count($child->classes)>0) {
            $attributes['class'] .= ' '.join(' ',$child->classes);
        }
        if (is_string($child->action)) {
            $attributes['link'] = $child->action;
        } else if ($child->action instanceof moodle_url) {
            $attributes['link'] = $child->action->out();
        } else if ($child->action instanceof action_link) {
            $attributes['link'] = $child->action->url->out();
        }
        $attributes['hidden'] = ($child->hidden);
        $attributes['haschildren'] = ($child->children->count()>0 || $child->type == navigation_node::TYPE_CATEGORY);

        if ($child->children->count() > 0) {
            $attributes['children'] = array();
            foreach ($child->children as $subchild) {
                $attributes['children'][] = $this->convert_child($subchild, $depth+1);
            }
        }

        if ($depth > 1) {
            return $attributes;
        } else {
            return json_encode($attributes);
        }
    }
}

/**
 * The cache class used by global navigation and settings navigation to cache bits
 * and bobs that are used during their generation.
 *
 * It is basically an easy access point to session with a bit of smarts to make
 * sure that the information that is cached is valid still.
 *
 * Example use:
 * <code php>
 * if (!$cache->viewdiscussion()) {
 *     // Code to do stuff and produce cachable content
 *     $cache->viewdiscussion = has_capability('mod/forum:viewdiscussion', $coursecontext);
 * }
 * $content = $cache->viewdiscussion;
 * </code>
 *
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_cache {
    /** @var int */
    protected $creation;
    /** @var array */
    protected $session;
    /** @var string */
    protected $area;
    /** @var int */
    protected $timeout;
    /** @var stdClass */
    protected $currentcontext;
    /** @var int */
    const CACHETIME = 0;
    /** @var int */
    const CACHEUSERID = 1;
    /** @var int */
    const CACHEVALUE = 2;
    /** @var null|array An array of navigation cache areas to expire on shutdown */
    public static $volatilecaches;

    /**
     * Contructor for the cache. Requires two arguments
     *
     * @param string $area The string to use to segregate this particular cache
     *                it can either be unique to start a fresh cache or if you want
     *                to share a cache then make it the string used in the original
     *                cache
     * @param int $timeout The number of seconds to time the information out after
     */
    public function __construct($area, $timeout=1800) {
        $this->creation = time();
        $this->area = $area;
        $this->timeout = time() - $timeout;
        if (rand(0,100) === 0) {
            $this->garbage_collection();
        }
    }

    /**
     * Used to set up the cache within the SESSION.
     *
     * This is called for each access and ensure that we don't put anything into the session before
     * it is required.
     */
    protected function ensure_session_cache_initialised() {
        global $SESSION;
        if (empty($this->session)) {
            if (!isset($SESSION->navcache)) {
                $SESSION->navcache = new stdClass;
            }
            if (!isset($SESSION->navcache->{$this->area})) {
                $SESSION->navcache->{$this->area} = array();
            }
            $this->session = &$SESSION->navcache->{$this->area};
        }
    }

    /**
     * Magic Method to retrieve something by simply calling using = cache->key
     *
     * @param mixed $key The identifier for the information you want out again
     * @return void|mixed Either void or what ever was put in
     */
    public function __get($key) {
        if (!$this->cached($key)) {
            return;
        }
        $information = $this->session[$key][self::CACHEVALUE];
        return unserialize($information);
    }

    /**
     * Magic method that simply uses {@link set();} to store something in the cache
     *
     * @param string|int $key
     * @param mixed $information
     */
    public function __set($key, $information) {
        $this->set($key, $information);
    }

    /**
     * Sets some information against the cache (session) for later retrieval
     *
     * @param string|int $key
     * @param mixed $information
     */
    public function set($key, $information) {
        global $USER;
        $this->ensure_session_cache_initialised();
        $information = serialize($information);
        $this->session[$key]= array(self::CACHETIME=>time(), self::CACHEUSERID=>$USER->id, self::CACHEVALUE=>$information);
    }
    /**
     * Check the existence of the identifier in the cache
     *
     * @param string|int $key
     * @return bool
     */
    public function cached($key) {
        global $USER;
        $this->ensure_session_cache_initialised();
        if (!array_key_exists($key, $this->session) || !is_array($this->session[$key]) || $this->session[$key][self::CACHEUSERID]!=$USER->id || $this->session[$key][self::CACHETIME] < $this->timeout) {
            return false;
        }
        return true;
    }
    /**
     * Compare something to it's equivilant in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param bool $serialise Whether to serialise the value before comparison
     *              this should only be set to false if the value is already
     *              serialised
     * @return bool If the value is the same false if it is not set or doesn't match
     */
    public function compare($key, $value, $serialise = true) {
        if ($this->cached($key)) {
            if ($serialise) {
                $value = serialize($value);
            }
            if ($this->session[$key][self::CACHEVALUE] === $value) {
                return true;
            }
        }
        return false;
    }
    /**
     * Wipes the entire cache, good to force regeneration
     */
    public function clear() {
        global $SESSION;
        unset($SESSION->navcache);
        $this->session = null;
    }
    /**
     * Checks all cache entries and removes any that have expired, good ole cleanup
     */
    protected function garbage_collection() {
        if (empty($this->session)) {
            return true;
        }
        foreach ($this->session as $key=>$cachedinfo) {
            if (is_array($cachedinfo) && $cachedinfo[self::CACHETIME]<$this->timeout) {
                unset($this->session[$key]);
            }
        }
    }

    /**
     * Marks the cache as being volatile (likely to change)
     *
     * Any caches marked as volatile will be destroyed at the on shutdown by
     * {@link navigation_node::destroy_volatile_caches()} which is registered
     * as a shutdown function if any caches are marked as volatile.
     *
     * @param bool $setting True to destroy the cache false not too
     */
    public function volatile($setting = true) {
        if (self::$volatilecaches===null) {
            self::$volatilecaches = array();
            register_shutdown_function(array('navigation_cache','destroy_volatile_caches'));
        }

        if ($setting) {
            self::$volatilecaches[$this->area] = $this->area;
        } else if (array_key_exists($this->area, self::$volatilecaches)) {
            unset(self::$volatilecaches[$this->area]);
        }
    }

    /**
     * Destroys all caches marked as volatile
     *
     * This function is static and works in conjunction with the static volatilecaches
     * property of navigation cache.
     * Because this function is static it manually resets the cached areas back to an
     * empty array.
     */
    public static function destroy_volatile_caches() {
        global $SESSION;
        if (is_array(self::$volatilecaches) && count(self::$volatilecaches)>0) {
            foreach (self::$volatilecaches as $area) {
                $SESSION->navcache->{$area} = array();
            }
        } else {
            $SESSION->navcache = new stdClass;
        }
    }
}
