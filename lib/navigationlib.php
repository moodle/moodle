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
 * @since 2.0
 * @package moodlecore
 * @subpackage navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!function_exists('get_all_sections')) {
    /** Include course lib for its functions */
    require_once($CFG->dirroot.'/course/lib.php');
}

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
    /** @var navigation_node A reference to the node parent */
    public $parent = null;
    /** @var bool Override to not display the icon even if one is provided **/
    public $hideicon = false;
    /** @var array */
    protected $namedtypes = array(0=>'system',10=>'category',20=>'course',30=>'structure',40=>'activity',50=>'resource',60=>'custom',70=>'setting', 80=>'user');
    /** @var moodle_url */
    protected static $fullmeurl = null;
    /** @var bool toogles auto matching of active node */
    public static $autofindactive = true;

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
            if (array_key_exists('icon', $properties)) {
                $this->icon = $properties['icon'];
                if ($this->icon instanceof pix_icon) {
                    if (empty($this->icon->attributes['class'])) {
                        $this->icon->attributes['class'] = 'navicon';
                    } else {
                        $this->icon->attributes['class'] .= ' navicon';
                    }
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
            if (array_key_exists('parent', $properties)) {
                $this->parent = $properties['parent'];
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
     * Overrides the fullmeurl variable providing
     *
     * @param moodle_url $url The url to use for the fullmeurl.
     */
    public static function override_active_url(moodle_url $url) {
        self::$fullmeurl = $url;
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
        // First convert the nodetype for this node to a branch as it will now have children
        if ($this->nodetype !== self::NODETYPE_BRANCH) {
            $this->nodetype = self::NODETYPE_BRANCH;
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
            $key = $this->children->count();
        }
        // Set the key
        $itemarray['key'] = $key;
        // Set the parent to this node
        $itemarray['parent'] = $this;
        // Add the child using the navigation_node_collections add method
        $node = $this->children->add(new navigation_node($itemarray));
        // If the node is a category node or the user is logged in and its a course
        // then mark this node as a branch (makes it expandable by AJAX)
        if (($type==self::TYPE_CATEGORY) || (isloggedin() && $type==self::TYPE_COURSE)) {
            $node->nodetype = self::NODETYPE_BRANCH;
        }
        // If this node is hidden mark it's children as hidden also
        if ($this->hidden) {
            $node->hidden = true;
        }
        // Return the node (reference returned by $this->children->add()
        return $node;
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
        if ($this->forcetitle || ($this->shorttext!==null && $this->title !== $this->shorttext) || $this->title !== $this->text) {
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
        if (!isloggedin()) {
            return;
        }
        foreach ($this->children as &$child) {
            if ($child->nodetype == self::NODETYPE_BRANCH && $child->children->count()==0 && $child->display) {
                $child->id = 'expandable_branch_'.(count($expandable)+1);
                $this->add_class('canexpand');
                $expandable[] = array('id'=>$child->id,'branchid'=>$child->key,'type'=>$child->type);
            }
            $child->find_expandable($expandable);
        }
    }

    public function find_all_of_type($type) {
        $nodes = $this->children->type($type);
        foreach ($this->children as &$node) {
            $childnodes = $node->find_all_of_type($type);
            $nodes = array_merge($nodes, $childnodes);
        }
        return $nodes;
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
     * @param navigation_node $node
     * @return navigation_node
     */
    public function add(navigation_node $node) {
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
        // Add the node to the appropriate place in the ordered structure.
        $this->orderedcollection[$type][$key] = $node;
        // Add a reference to the node to the progressive collection.
        $this->collection[$this->count] = &$this->orderedcollection[$type][$key];
        // Update the last property to a reference to this new node.
        $this->last = &$this->orderedcollection[$type][$key];
        $this->count++;
        // Return the reference to the now added node
        return $this->last;
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
                if ($node->key == $key && ($type == null || $type === $node->type)) {
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
     * @return int
     */
    public function count() {
        return count($this->collection);
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
    /** @var array */
    protected $extendforuser = array();
    /** @var navigation_cache */
    protected $cache;
    /** @var array */
    protected $addedcourses = array();
    /** @var int */
    protected $expansionlimit = 0;

    /**
     * Constructs a new global navigation
     *
     * @param moodle_page $page The page this navigation object belongs to
     */
    public function __construct(moodle_page $page) {
        global $SITE, $USER;

        if (during_initial_install()) {
            return;
        }

        // Use the parents consturctor.... good good reuse
        $properties = array(
            'key' => 'home',
            'type' => navigation_node::TYPE_SYSTEM,
            'text' => get_string('myhome'),
            'action' => new moodle_url('/my/')
        );
        if (!isloggedin()) {
            $properties['text'] = get_string('home');
            $properties['action'] = new moodle_url('/');
        }
        parent::__construct($properties);

        // Initalise and set defaults
        $this->page = $page;
        $this->forceopen = true;
        $this->cache = new navigation_cache(NAVIGATION_CACHE_NAME);

        // Check if we need to clear the cache
        $regenerate = optional_param('regenerate', null, PARAM_TEXT);
        if ($regenerate === 'navigation') {
            $this->cache->clear();
        }
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
        global $CFG, $SITE, $USER;
        // Check if it has alread been initialised
        if ($this->initialised || during_initial_install()) {
            return true;
        }

        // Set up the five base root nodes. These are nodes where we will put our
        // content and are as follows:
        // site:        Navigation for the front page.
        // myprofile:     User profile information goes here.
        // mycourses:   The users courses get added here.
        // courses:     Additional courses are added here.
        // users:       Other users information loaded here.
        $this->rootnodes = array();
        $this->rootnodes['site']      = $this->add_course($SITE);
        $this->rootnodes['myprofile']   = $this->add(get_string('myprofile'), null, self::TYPE_USER, null, 'myprofile');
        $this->rootnodes['mycourses'] = $this->add(get_string('mycourses'), null, self::TYPE_ROOTNODE, null, 'mycourses');
        $this->rootnodes['courses']   = $this->add(get_string('courses'), null, self::TYPE_ROOTNODE, null, 'courses');
        $this->rootnodes['users']     = $this->add(get_string('users'), null, self::TYPE_ROOTNODE, null, 'users');

        // Fetch all of the users courses.
        $this->mycourses = get_my_courses($USER->id);
        // Check if any courses were returned.
        if (count($this->mycourses) > 0) {
            // Add all of the users courses to the navigation
            foreach ($this->mycourses as &$course) {
              $course->coursenode = $this->add_course($course);
            }
        } else {
            // The user had no specific courses! they could be no logged in, guest
            // or admin so load all courses instead.
            $this->load_all_courses();
        }

        // Next load context specific content into the navigation
        switch ($this->page->context->contextlevel) {
            case CONTEXT_SYSTEM :
            case CONTEXT_COURSECAT :
                // Load the front page course navigation
                $this->load_course($SITE);
                break;
            case CONTEXT_BLOCK :
            case CONTEXT_COURSE :
                // Load the course associated with the page into the navigation
                $course = $this->page->course;
                $coursenode = $this->load_course($course);
                // Make it active
                $coursenode->make_active();
                // Add the essentials such as reports etc...
                $this->add_course_essentials($coursenode, $course);
                if ($this->format_display_course_content($course->format)) {
                    // Load the course sections
                    $sections = $this->load_course_sections($course, $coursenode);
                }
                break;
            case CONTEXT_MODULE :
                $course = $this->page->course;
                $cm = $this->page->cm;
                // Load the course associated with the page into the navigation
                $coursenode = $this->load_course($course);
                $this->add_course_essentials($coursenode, $course);
                // Load the course sections into the page
                $sections = $this->load_course_sections($course, $coursenode);
                if ($course->id !== SITEID) {
                    // Find the section for the $CM associated with the page and collect
                    // its section number.
                    foreach ($sections as $section) {
                        if ($section->id == $cm->section) {
                            $cm->sectionnumber = $section->section;
                            break;
                        }
                    }

                    // Load all of the section activities for the section the cm belongs to.
                    $activities = $this->load_section_activities($sections[$cm->sectionnumber]->sectionnode, $cm->sectionnumber, get_fast_modinfo($course));
                } else {
                    $activities = array();
                    $activities[$cm->id] = $coursenode->get($cm->id, navigation_node::TYPE_ACTIVITY);
                }
                // Finally load the cm specific navigaton information
                $this->load_activity($cm, $course, $activities[$cm->id]);
                // And make the activity node active.
                $activities[$cm->id]->make_active();
                break;
            case CONTEXT_USER :
                $course = $this->page->course;
                if ($course->id != SITEID) {
                    // Load the course associated with the user into the navigation
                    $coursenode = $this->load_course($course);
                    $this->add_course_essentials($coursenode, $course);
                    $sections = $this->load_course_sections($course, $coursenode);
                }
                break;
        }

        // Load for the current user
        $this->load_for_user();
        // Load each extending user into the navigation.
        foreach ($this->extendforuser as $user) {
            if ($user->id !== $USER->id) {
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
            if (!$node->has_children()) {
                $node->remove();
            }
        }

        // If the user is not logged in modify the navigation structure as detailed
        // in {@link http://docs.moodle.org/en/Development:Navigation_2.0_structure}
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

        $this->initialised = true;
        return true;
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
     * @return array An array of navigation_node
     */
    protected function load_all_courses() {
        global $DB, $USER;
        list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        $sql = "SELECT c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.category,cat.path AS categorypath $ccselect
                FROM {course} c
                $ccjoin
                LEFT JOIN {course_categories} cat ON cat.id=c.category
                WHERE c.id != :siteid
                ORDER BY c.sortorder ASC";
        $courses = $DB->get_records_sql($sql, array('siteid'=>SITEID));
        $coursenodes = array();
        foreach ($courses as $course) {
            context_instance_preload($course);
            $coursenodes[$course->id] = $this->add_course($course);
        }
        return $coursenodes;
    }

    /**
     * Loads the given course into the navigation
     *
     * @param stdClass $course
     * @return navigation_node
     */
    protected function load_course(stdClass $course) {
        if ($course->id == SITEID) {
            $coursenode = $this->rootnodes['site'];
        } else if (array_key_exists($course->id, $this->mycourses)) {
            if (!isset($this->mycourses[$course->id]->coursenode)) {
                $this->mycourses[$course->id]->coursenode = $this->add_course($course);
            }
            $coursenode = $this->mycourses[$course->id]->coursenode;
        } else {
            $coursenode = $this->add_course($course);
        }
        return $coursenode;
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
     * Generically loads the course sections into the course's navigation.
     *
     * @param stdClass $course
     * @param navigation_node $coursenode
     * @param string $name The string that identifies each section. e.g Topic, or Week
     * @param string $activeparam The url used to identify the active section
     * @return array An array of course section nodes
     */
    public function load_generic_course_sections(stdClass $course, navigation_node $coursenode, $courseformat='unknown') {
        global $DB, $USER;
        
        $modinfo = get_fast_modinfo($course);
        $sections = array_slice(get_all_sections($course->id), 0, $course->numsections+1, true);
        $viewhiddensections = has_capability('moodle/course:viewhiddensections', $this->page->context);

        if (isloggedin() && !isguestuser()) {
            $activesection = $DB->get_field("course_display", "display", array("userid"=>$USER->id, "course"=>$course->id));
        } else {
            $activesection = null;
        }

        $namingfunction = 'callback_'.$courseformat.'_get_section_name';
        $namingfunctionexists = (function_exists($namingfunction));
        
        $activeparamfunction = 'callback_'.$courseformat.'_request_key';
        if (function_exists($activeparamfunction)) {
            $activeparam = $activeparamfunction();
        } else {
            $activeparam = 'section';
        }

        foreach ($sections as &$section) {
            if ($course->id == SITEID) {
                $this->load_section_activities($coursenode, $section->section, $modinfo);
            } else {
                if ((!$viewhiddensections && !$section->visible) || (!$this->showemptysections && !array_key_exists($section->section, $modinfo->sections))) {
                    continue;
                }
                if ($namingfunctionexists) {
                    $sectionname = $namingfunction($course, $section, $sections);
                } else {
                    $sectionname = get_string('section').' '.$section->section;
                }
                $url = new moodle_url('/course/view.php', array('id'=>$course->id, $activeparam=>$section->section));
                $sectionnode = $coursenode->add($sectionname, $url, navigation_node::TYPE_SECTION, null, $section->id);
                $sectionnode->nodetype = navigation_node::NODETYPE_BRANCH;
                $sectionnode->hidden = (!$section->visible);
                if ($this->page->context->contextlevel != CONTEXT_MODULE && ($sectionnode->isactive || ($activesection != null && $section->section == $activesection))) {
                    $sectionnode->force_open();
                    $this->load_section_activities($sectionnode, $section->section, $modinfo);
                }
                $section->sectionnode = $sectionnode;
            }
        }
        return $sections;
    }
    /**
     * Loads all of the activities for a section into the navigation structure.
     *
     * @param navigation_node $sectionnode
     * @param int $sectionnumber
     * @param stdClass $modinfo Object returned from {@see get_fast_modinfo()}
     * @return array Array of activity nodes
     */
    protected function load_section_activities(navigation_node $sectionnode, $sectionnumber, $modinfo) {
        if (!array_key_exists($sectionnumber, $modinfo->sections)) {
            return true;
        }

        $viewhiddenactivities = has_capability('moodle/course:viewhiddenactivities', $this->page->context);

        $activities = array();

        foreach ($modinfo->sections[$sectionnumber] as $cmid) {
            $cm = $modinfo->cms[$cmid];
            if (!$viewhiddenactivities && !$cm->visible) {
                continue;
            }
            if ($cm->icon) {
                $icon = new pix_icon($cm->icon, '', $cm->iconcomponent);
            } else {
                $icon = new pix_icon('icon', '', $cm->modname);
            }
            $url = new moodle_url('/mod/'.$cm->modname.'/view.php', array('id'=>$cm->id));
            $activitynode = $sectionnode->add($cm->name, $url, navigation_node::TYPE_ACTIVITY, $cm->name, $cm->id, $icon);
            $activitynode->title(get_string('modulename', $cm->modname));
            $activitynode->hidden = (!$cm->visible);
            if ($this->module_extends_navigation($cm->modname)) {
                $activitynode->nodetype = navigation_node::NODETYPE_BRANCH;
            }
            $activities[$cmid] = $activitynode;
        }

        return $activities;
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
     * @param stdClass $cm
     * @param stdClass $course
     * @param navigation_node $activity
     * @return bool
     */
    protected function load_activity(stdClass $cm, stdClass $course, navigation_node $activity) {
        global $CFG, $DB;

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
    protected function load_for_user($user=null) {
        global $DB, $CFG, $USER;

        $iscurrentuser = false;
        if ($user === null) {
            // We can't require login here but if the user isn't logged in we don't
            // want to show anything
            if (!isloggedin()) {
                return false;
            }
            $user = $USER;
            $iscurrentuser = true;
        } else if (!is_object($user)) {
            // If the user is not an object then get them from the database
            $user = $DB->get_record('user', array('id'=>(int)$user), '*', MUST_EXIST);
        }
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);

        // Get the course set against the page, by default this will be the site
        $course = $this->page->course;
        $baseargs = array('id'=>$user->id);
        if ($course->id !== SITEID) {
            if (array_key_exists($course->id, $this->mycourses)) {
                $coursenode = $this->mycourses[$course->id]->coursenode;
            } else {
                $coursenode = $this->rootnodes['courses']->find($course->id, navigation_node::TYPE_COURSE);
                if (!$coursenode) {
                    $coursenode = $this->load_course($course);
                }
            }
            $baseargs['course'] = $course->id;
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            $issitecourse = false;
        } else {
            // Load all categories and get the context for the system
            $coursecontext = get_context_instance(CONTEXT_SYSTEM);
            $issitecourse = true;
        }

        // Create a node to add user information under.
        if ($iscurrentuser) {
            // If it's the current user the information will go under the profile root node
            $usernode = $this->rootnodes['myprofile'];
        } else {
            if (!$issitecourse) {
                // Not the current user so add it to the participants node for the current course
                $usersnode = $coursenode->get('participants', navigation_node::TYPE_CONTAINER);
            } else {
                // This is the site so add a users node to the root branch
                $usersnode = $this->rootnodes['users'];
                $usersnode->action = new moodle_url('/user/index.php', array('id'=>$course->id));
            }
            // Add a branch for the current user
            $usernode = $usersnode->add(fullname($user, true));
        }

        if ($this->page->context->contextlevel == CONTEXT_USER && $user->id == $this->page->context->instanceid) {
            $usernode->force_open();
        }

        // If the user is the current user or has permission to view the details of the requested
        // user than add a view profile link.
        if ($iscurrentuser || has_capability('moodle/user:viewdetails', $coursecontext) || has_capability('moodle/user:viewdetails', $usercontext)) {
            $usernode->add(get_string('viewprofile'), new moodle_url('/user/view.php',$baseargs));
        }

        // Add nodes for forum posts and discussions if the user can view either or both
        $canviewposts = has_capability('moodle/user:readuserposts', $usercontext);
        $canviewdiscussions = has_capability('mod/forum:viewdiscussion', $coursecontext);
        if ($canviewposts || $canviewdiscussions) {
            $forumtab = $usernode->add(get_string('forumposts', 'forum'));
            if ($canviewposts) {
                $forumtab->add(get_string('posts', 'forum'), new moodle_url('/mod/forum/user.php', $baseargs));
            }
            if ($canviewdiscussions) {
                $forumtab->add(get_string('discussions', 'forum'), new moodle_url('/mod/forum/user.php', array_merge($baseargs, array('mode'=>'discussions'))));
            }
        }

        // Add blog nodes
        if (!empty($CFG->bloglevel)) {
            require_once($CFG->dirroot.'/blog/lib.php');
            // Get all options for the user
            $options = blog_get_options_for_user($user);
            if (count($options) > 0) {
                $blogs = $usernode->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER);
                foreach ($options as $option) {
                    $blogs->add($option['string'], $option['link']);
                }
            }
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
        $reporttab = $usernode->add(get_string('activityreports'));
        $anyreport  = has_capability('moodle/user:viewuseractivitiesreport', $usercontext);
        $viewreports = ($anyreport || ($course->showreports && $iscurrentuser));
        $reportargs = array('user'=>$user->id);
        if (!empty($course->id)) {
            $reportargs['id'] = $course->id;
        } else {
            $reportargs['id'] = SITEID;
        }
        if ($viewreports || has_capability('coursereport/outline:view', $coursecontext)) {
            $reporttab->add(get_string('outlinereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'outline'))));
            $reporttab->add(get_string('completereport'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'complete'))));
        }

        if ($viewreports || has_capability('coursereport/log:viewtoday', $coursecontext)) {
            $reporttab->add(get_string('todaylogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'todaylogs'))));
        }

        if ($viewreports || has_capability('coursereport/log:view', $coursecontext)) {
            $reporttab->add(get_string('alllogs'), new moodle_url('/course/user.php', array_merge($reportargs, array('mode'=>'alllogs'))));
        }

        if (!empty($CFG->enablestats)) {
            if ($viewreports || has_capability('coursereport/stats:view', $coursecontext)) {
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
        if (count($reporttab->children)===0) {
            $usernode->remove_child($reporttab);
        }

        // If the user is the current user add the repositories for the current user
        if ($iscurrentuser) {
            require_once($CFG->dirroot . '/repository/lib.php');
            $editabletypes = repository::get_editable_types($usercontext);
            if (!empty($editabletypes)) {
                $usernode->add(get_string('repositories', 'repository'), new moodle_url('/repository/manage_instances.php', array('contextid' => $usercontext->id)));
            }
        }
        return true;
    }

    /**
     * This method simply checks to see if a given module can extend the navigation.
     *
     * @param string $modname
     * @return bool
     */
    protected function module_extends_navigation($modname) {
        global $CFG;
        if ($this->cache->cached($modname.'_extends_navigation')) {
            return $this->cache->{$modname.'_extends_navigation'};
        }
        $file = $CFG->dirroot.'/mod/'.$modname.'/lib.php';
        $function = $modname.'_extend_navigation';
        if (function_exists($function)) {
            $this->cache->{$modname.'_extends_navigation'} = true;
            return true;
        } else if (file_exists($file)) {
            require_once($file);
            if (function_exists($function)) {
                $this->cache->{$modname.'_extends_navigation'} = true;
                return true;
            }
        }
        $this->cache->{$modname.'_extends_navigation'} = false;
        return false;
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
     * Adds the given course to the navigation structure.
     *
     * @param stdClass $course
     * @return navigation_node
     */
    public function add_course(stdClass $course) {
        if (array_key_exists($course->id, $this->addedcourses)) {
            return $this->addedcourses[$course->id];
        }

        $canviewhidden = has_capability('moodle/course:viewhiddencourses', $this->page->context);
        if ($course->id !== SITEID && !$canviewhidden && (!$course->visible || !course_parent_visible($course))) {
            return false;
        }

        if ($course->id == SITEID) {
            $parent = $this;
            $url = new moodle_url('/');
        } else if (array_key_exists($course->id, $this->mycourses)) {
            $parent = $this->rootnodes['mycourses'];
            $url = new moodle_url('/course/view.php', array('id'=>$course->id));
        } else {
            $parent = $this->rootnodes['courses'];
            $url = new moodle_url('/course/view.php', array('id'=>$course->id));
        }
        $coursenode = $parent->add($course->fullname, $url, self::TYPE_COURSE, $course->shortname, $course->id);
        $coursenode->nodetype = self::NODETYPE_BRANCH;
        $coursenode->hidden = (!$course->visible);
        $this->addedcourses[$course->id] = &$coursenode;
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
    public function add_course_essentials(navigation_node $coursenode, stdClass $course) {
        global $CFG;

        if ($course->id === SITEID) {
            return $this->add_front_page_course_essentials($coursenode, $course);
        }

        if ($coursenode == false || $coursenode->get('participants', navigation_node::TYPE_CONTAINER)) {
            return true;
        }

        //Participants
        if (has_capability('moodle/course:viewparticipants', $this->page->context)) {
            require_once($CFG->dirroot.'/blog/lib.php');
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
            if ($CFG->bloglevel >= 3) {
                $blogsurls = new moodle_url('/blog/index.php', array('courseid' => $filterselect));
                $participants->add(get_string('blogs','blog'), $blogsurls->out());
            }
            if (!empty($CFG->enablenotes) && (has_capability('moodle/notes:manage', $this->page->context) || has_capability('moodle/notes:view', $this->page->context))) {
                $participants->add(get_string('notes','notes'), new moodle_url('/notes/index.php', array('filtertype'=>'course', 'filterselect'=>$filterselect)));
            }
        } else if (count($this->extendforuser) > 0) {
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

    public function add_front_page_course_essentials(navigation_node $coursenode, stdClass $course) {
        global $CFG;

        if ($coursenode == false || $coursenode->get('participants', navigation_node::TYPE_CUSTOM)) {
            return true;
        }

        //Participants
        if (has_capability('moodle/course:viewparticipants', $this->page->context)) {
            $coursenode->add(get_string('participants'), new moodle_url('/user/index.php?id='.$course->id), self::TYPE_CUSTOM, get_string('participants'), 'participants');
        }
        
        $currentgroup = groups_get_course_group($course, true);
        if ($course->id == SITEID) {
            $filterselect = '';
        } else if ($course->id && !$currentgroup) {
            $filterselect = $course->id;
        } else {
            $filterselect = $currentgroup;
        }
        $filterselect = clean_param($filterselect, PARAM_INT);

        // Blogs
        if (has_capability('moodle/blog:view', $this->page->context)) {
            require_once($CFG->dirroot.'/blog/lib.php');
            if (blog_is_enabled_for_user()) {
                $blogsurls = new moodle_url('/blog/index.php', array('courseid' => $filterselect));
                $coursenode->add(get_string('blogs','blog'), $blogsurls->out());
            }
        }

        // Notes
        if (!empty($CFG->enablenotes) && (has_capability('moodle/notes:manage', $this->page->context) || has_capability('moodle/notes:view', $this->page->context))) {
            $coursenode->add(get_string('notes','notes'), new moodle_url('/notes/index.php', array('filtertype'=>'course', 'filterselect'=>$filterselect)));
        }

        // Tags
        if (!empty($CFG->usetags) && isloggedin()) {
            $coursenode->add(get_string('tags', 'tag'), new moodle_url('/tag/search.php'));
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

    public function set_expansion_limit($type) {
        $nodes = $this->find_all_of_type($type);
        foreach ($nodes as &$node) {
            foreach ($node->children as &$child) {
                $child->display = false;
            }
        }
        return true;
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

    /** @var array */
    protected $expandable = array();

    /**
     * Constructs the navigation for use in AJAX request
     */
    public function __construct() {
        global $SITE;
        $this->cache = new navigation_cache(NAVIGATION_CACHE_NAME);
        $this->children = new navigation_node_collection();
        $this->rootnodes = array();
        //$this->rootnodes['site']      = $this->add_course($SITE);
        $this->rootnodes['courses'] = $this->add(get_string('courses'), null, self::TYPE_ROOTNODE, null, 'courses');
    }
    /**
     * Initialise the navigation given the type and id for the branch to expand.
     *
     * @param int $branchtype One of navigation_node::TYPE_*
     * @param int $id
     * @return array The expandable nodes
     */
    public function initialise($branchtype, $id) {
        global $DB, $PAGE;

        if ($this->initialised || during_initial_install()) {
            return $this->expandable;
        }

        // Branchtype will be one of navigation_node::TYPE_*
        switch ($branchtype) {
            case self::TYPE_COURSE :
                $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
                require_course_login($course);
                $this->page = $PAGE;
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
                $course = $DB->get_record_sql($sql, array($id), MUST_EXIST);
                require_course_login($course);
                $this->page = $PAGE;
                $coursenode = $this->add_course($course);
                $this->add_course_essentials($coursenode, $course);
                $sections = $this->load_course_sections($course, $coursenode);
                $this->load_section_activities($sections[$course->sectionnumber]->sectionnode, $course->sectionnumber, get_fast_modinfo($course));
                break;
            case self::TYPE_ACTIVITY :
                $cm = get_coursemodule_from_id(false, $id, 0, false, MUST_EXIST);
                $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
                require_course_login($course, true, $cm);
                $this->page = $PAGE;
                $coursenode = $this->load_course($course);
                $sections = $this->load_course_sections($course, $coursenode);
                foreach ($sections as $section) {
                    if ($section->id == $cm->section) {
                        $cm->sectionnumber = $section->section;
                        break;
                    }
                }
                $activities = $this->load_section_activities($sections[$cm->sectionnumber]->sectionnode, $cm->sectionnumber, get_fast_modinfo($course));
                $modulenode = $this->load_activity($cm, $course, $activities[$cm->id]);
                break;
            default:
                throw new Exception('Unknown type');
                return $this->expandable;
        }

        $this->find_expandable($this->expandable);
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
                // Removes the first node from the settings (root node) from the list
                array_pop($items);
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
    /** @var navigation_cache */
    protected $cache;
    /** @var moodle_page */
    protected $page;
    /** @var string */
    protected $adminsection;
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
        global $DB;

        if (during_initial_install()) {
            return false;
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
        $admin = $this->load_administration_settings();

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

        // Make sure the first child doesnt have proceed with hr set to true

        foreach ($this->children as $key=>$node) {
            if ($node->nodetype != self::NODETYPE_BRANCH || $node->children->count()===0) {
                $node->remove();
            }
        }
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
        $this->children = new get_class($children);
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
     * The array of resources and activities that can be added to a course is then
     * stored in the cache so that we can access it for anywhere.
     * It saves us generating it all the time
     *
     * <code php>
     * // To get resources:
     * $this->cache->{'course'.$courseid.'resources'}
     * // To get activities:
     * $this->cache->{'course'.$courseid.'activities'}
     * </code>
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
        $this->cache->{'course'.$course->id.'resources'} = $resources;
        $this->cache->{'course'.$course->id.'activities'} = $activities;
    }

    /**
     * This function loads the course settings that are available for the user
     *
     * @param bool $forceopen If set to true the course node will be forced open
     * @return navigation_node|false
     */
    protected function load_course_settings($forceopen = false) {
        global $CFG, $USER, $SESSION, $OUTPUT;

        $course = $this->page->course;
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        if (!$this->cache->cached('canviewcourse'.$course->id)) {
            $this->cache->{'canviewcourse'.$course->id} = has_capability('moodle/course:participate', $coursecontext);
        }
        if ($course->id === SITEID || !$this->cache->{'canviewcourse'.$course->id}) {
            return false;
        }

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
                // Add `add` resources|activities branches
                $structurefile = $CFG->dirroot.'/course/format/'.$course->format.'/lib.php';
                if (file_exists($structurefile)) {
                    require_once($structurefile);
                    $formatstring = call_user_func('callback_'.$course->format.'_definition');
                    $formatidentifier = optional_param(call_user_func('callback_'.$course->format.'_request_key'), 0, PARAM_INT);
                } else {
                    $formatstring = get_string('topic');
                    $formatidentifier = optional_param('topic', 0, PARAM_INT);
                }
                if (!$this->cache->cached('coursesections'.$course->id)) {
                    $this->cache->{'coursesections'.$course->id} = get_all_sections($course->id);
                }
                $sections = $this->cache->{'coursesections'.$course->id};

                $addresource = $this->add(get_string('addresource'));
                $addactivity = $this->add(get_string('addactivity'));
                if ($formatidentifier!==0) {
                    $addresource->force_open();
                    $addactivity->force_open();
                }

                if (!$this->cache->cached('course'.$course->id.'resources')) {
                    $this->get_course_modules($course);
                }
                $resources = $this->cache->{'course'.$course->id.'resources'};
                $activities = $this->cache->{'course'.$course->id.'activities'};

                $textlib = textlib_get_instance();

                foreach ($sections as $section) {
                    if ($formatidentifier !== 0 && $section->section != $formatidentifier) {
                        continue;
                    }
                    $sectionurl = new moodle_url('/course/view.php', array('id'=>$course->id, $formatstring=>$section->section));
                    if ($section->section == 0) {
                        $sectionresources = $addresource->add(get_string('course'), $sectionurl, self::TYPE_SETTING);
                        $sectionactivities = $addactivity->add(get_string('course'), $sectionurl, self::TYPE_SETTING);
                    } else {
                        $sectionresources = $addresource->add($formatstring.' '.$section->section, $sectionurl, self::TYPE_SETTING);
                        $sectionactivities = $addactivity->add($formatstring.' '.$section->section, $sectionurl, self::TYPE_SETTING);
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
                            $subbranch = $sectionresources->add(trim($activity, '-'));
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
                            $sectionresources->add($activity, $url, self::TYPE_SETTING);
                        }
                    }
                }
            }

            // Add the course settings link
            $url = new moodle_url('/course/edit.php', array('id'=>$course->id));
            $coursenode->add(get_string('settings'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));

            // Add the course completion settings link
            if ($CFG->enablecompletion && $course->enablecompletion) {
                $url = new moodle_url('/course/completion.php', array('id'=>$course->id));
                $coursenode->add(get_string('completion', 'completion'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
            }
        }

        if (has_capability('moodle/role:assign', $coursecontext)) {
            // Add assign or override roles if allowed
            $url = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$coursecontext->id));
            $coursenode->add(get_string('assignroles', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            // Override roles
            if (has_capability('moodle/role:review', $coursecontext) or count(get_overridable_roles($coursecontext))>0) {
                $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$coursecontext->id));
                $coursenode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            }
            // Check role permissions
            if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $coursecontext)) {
                $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$coursecontext->id));
                $coursenode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            }
            // Manage filters
            if (has_capability('moodle/filter:manage', $coursecontext) && count(filter_get_available_in_context($coursecontext))>0) {
                $url = new moodle_url('/filter/manage.php', array('contextid'=>$coursecontext->id));
                $coursenode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/filter', ''));
            }
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
            $coursenode->add(get_string('grades'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/grades', ''));
        }

        //  Add outcome if permitted
        if (!empty($CFG->enableoutcomes) && has_capability('moodle/course:update', $coursecontext)) {
            $url = new moodle_url('/grade/edit/outcome/course.php', array('id'=>$course->id));
            $coursenode->add(get_string('outcomes', 'grades'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/outcomes', ''));
        }

        // Add meta course links
        if ($course->metacourse) {
            if (has_capability('moodle/course:managemetacourse', $coursecontext)) {
                $url = new moodle_url('/course/importstudents.php', array('id'=>$course->id));
                $coursenode->add(get_string('childcourses'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/course', ''));
            } else if (has_capability('moodle/role:assign', $coursecontext)) {
                $roleassign = $coursenode->add(get_string('childcourses'), null,  self::TYPE_SETTING, null, null, new pix_icon('i/course', ''));
                $roleassign->hidden = true;
            }
        }

        // Manage groups in this course
        if (($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $coursecontext)) {
            $url = new moodle_url('/group/index.php', array('id'=>$course->id));
            $coursenode->add(get_string('groups'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/group', ''));
        }

        // Backup this course
        if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
            $url = new moodle_url('/backup/backup.php', array('id'=>$course->id));
            $coursenode->add(get_string('backup'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/backup', ''));
        }

        // Restore to this course
        if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
            $url = new moodle_url('/files/index.php', array('id'=>$course->id, 'wdir'=>'/backupdata'));
            $url = null; // Disabled until restore is implemented. MDL-21432
            $coursenode->add(get_string('restore'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/restore', ''));
        }

        // Import data from other courses
        if (has_capability('moodle/restore:restoretargetimport', $coursecontext)) {
            $url = new moodle_url('/course/import.php', array('id'=>$course->id));
            $url = null; // Disabled until restore is implemented. MDL-21432
            $coursenode->add(get_string('import'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/restore', ''));
        }

        // Publish course on a hub
        if (has_capability('moodle/course:publish', $coursecontext)) {
            $url = new moodle_url('/course/publish/index.php', array('id'=>$course->id));
            $coursenode->add(get_string('publish'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/publish', ''));
        }

        // Reset this course
        if (has_capability('moodle/course:reset', $coursecontext)) {
            $url = new moodle_url('/course/reset.php', array('id'=>$course->id));
            $coursenode->add(get_string('reset'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/return', ''));
        }

        // Manage questions
        $questioncaps = array('moodle/question:add',
                              'moodle/question:editmine',
                              'moodle/question:editall',
                              'moodle/question:viewmine',
                              'moodle/question:viewall',
                              'moodle/question:movemine',
                              'moodle/question:moveall');
        if (has_any_capability($questioncaps, $this->context)) {
            $questionlink = $CFG->wwwroot.'/question/edit.php';
        } else if (has_capability('moodle/question:managecategory', $this->context)) {
            $questionlink = $CFG->wwwroot.'/question/category.php';
        }
        if (isset($questionlink)) {
            $url = new moodle_url($questionlink, array('courseid'=>$course->id));
            $coursenode->add(get_string('questions','quiz'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/questions', ''));
        }

        // Repository Instances
        require_once($CFG->dirroot.'/repository/lib.php');
        $editabletypes = repository::get_editable_types($this->context);
        if (has_capability('moodle/course:update', $this->context) && !empty($editabletypes)) {
            $url = new moodle_url('/repository/manage_instances.php', array('contextid'=>$this->context->id));
            $coursenode->add(get_string('repositories'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/repository', ''));
        }

        // Manage files
        if (has_capability('moodle/course:managefiles', $this->context)) {
            $url = new moodle_url('/files/index.php', array('id'=>$course->id));
            $coursenode->add(get_string('files'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/files', ''));
        }

        // Authorize hooks
        if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize')) {
            require_once($CFG->dirroot.'/enrol/authorize/const.php');
            $url = new moodle_url('/enrol/authorize/index.php', array('course'=>$course->id));
            $coursenode->add(get_string('payments'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/payment', ''));
            if (has_capability('enrol/authorize:managepayments', $this->page->context)) {
                $cnt = $DB->count_records('enrol_authorize', array('status'=>AN_STATUS_AUTH, 'courseid'=>$course->id));
                if ($cnt) {
                    $url = new moodle_url('/enrol/authorize/index.php', array('course'=>$course->id,'status'=>AN_STATUS_AUTH));
                    $coursenode->add(get_string('paymentpending', 'moodle', $cnt), $url, self::TYPE_SETTING, null, null, new pix_icon('i/payment', ''));
                }
            }
        }

        // Unenrol link
        if (empty($course->metacourse) && ($course->id!==SITEID)) {
            if (is_enrolled(get_context_instance(CONTEXT_COURSE, $course->id))) {
                if (has_capability('moodle/role:unassignself', $this->page->context, NULL, false) and get_user_roles($this->page->context, $USER->id, false)) {  // Have some role
                    $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/unenrol.php?id='.$course->id.'">'.get_string('unenrolme', '', format_string($course->shortname)).'</a>';
                    $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/user') . '" class="icon" alt="" />';
                }

            } else if (is_viewing(get_context_instance(CONTEXT_COURSE, $course->id))) {
                // inspector, manager, etc. - do not show anything
            } else {
                // access because otherwise they would not get into this course at all
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/enrol.php?id='.$course->id.'">'.get_string('enrolme', '', format_string($course->shortname)).'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/user') . '" class="icon" alt="" />';
            }
        }

        // Switch roles
        $roles = array();
        $assumedrole = $this->in_alternative_role();
        if ($assumedrole!==false) {
            $roles[0] = get_string('switchrolereturn');
        }
        if (has_capability('moodle/role:switchroles', $this->context)) {
            $availableroles = get_switchable_roles($this->context);
            if (is_array($availableroles)) {
                foreach ($availableroles as $key=>$role) {
                    if ($key == $CFG->guestroleid || $assumedrole===(int)$key) {
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
            $SESSION->returnurl = serialize($returnurl);
            foreach ($roles as $key=>$name) {
                $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>$key, 'returnurl'=>'1'));
                $switchroles->add($name, $url, self::TYPE_SETTING, null, $key, new pix_icon('i/roles', ''));
            }
        }
        // Return we are done
        return $coursenode;
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

        $modulenode = $this->add(get_string($this->page->activityname.'administration', $this->page->activityname));
        $modulenode->force_open();

        // Settings for the module
        if (has_capability('moodle/course:manageactivities', $this->page->cm->context)) {
            $url = new moodle_url('/course/modedit.php', array('update' => $this->page->cm->id, 'return' => true, 'sesskey' => sesskey()));
            $modulenode->add(get_string('settings'), $url, navigation_node::TYPE_SETTING);
        }
        // Assign local roles
        if (count(get_assignable_roles($this->page->cm->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('localroles', 'role'), $url, self::TYPE_SETTING);
        }
        // Override roles
        if (has_capability('moodle/role:review', $this->page->cm->context) or count(get_overridable_roles($this->page->cm->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING);
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $this->page->cm->context)) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING);
        }
        // Manage filters
        if (has_capability('moodle/filter:manage', $this->page->cm->context) && count(filter_get_available_in_context($this->page->cm->context))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$this->page->cm->context->id));
            $modulenode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING);
        }

        $file = $CFG->dirroot.'/mod/'.$this->page->activityname.'/lib.php';
        $function = $this->page->activityname.'_extend_settings_navigation';

        if (file_exists($file)) {
            require_once($file);
        }
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

        // This is terribly ugly code, but I couldn't see a better way around it
        // we need to pick up the user id, it can be the current user or someone else
        // and the key depends on the current location
        // Default to look at id
        $userkey='id';
        if (strpos($FULLME,'/blog/') || strpos($FULLME, $CFG->admin.'/roles/')) {
            // And blog and roles just do thier own thing using `userid`
            $userkey = 'userid';
        } else if ($this->context->contextlevel >= CONTEXT_COURSECAT && strpos($FULLME, '/message/')===false && strpos($FULLME, '/mod/forum/user')===false && strpos($FULLME, '/user/editadvanced')===false) {
            // If we have a course context and we are not in message or forum
            // Message and forum both pick the user up from `id`
            $userkey = 'user';
        }

        $userid = optional_param($userkey, $USER->id, PARAM_INT);
        if ($userid!=$USER->id) {
            $usernode = $this->generate_user_settings($courseid, $userid, 'userviewingsettings');
            $this->generate_user_settings($courseid, $USER->id);
        } else {
            $usernode = $this->generate_user_settings($courseid, $USER->id);
        }
        return $usernode;
    }

    /**
     * This function gets called by {@link load_user_settings()} and actually works out
     * what can be shown/done
     *
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
                $course = $DB->get_record("course", array("id"=>$courseid), '*', MUST_EXIST);
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
            if (!$user = $DB->get_record('user', array('id'=>$userid))) {
                return false;
            }
            // Check that the user can view the profile
            $usercontext = get_context_instance(CONTEXT_USER, $user->id);       // User context
            if ($course->id==SITEID) {
                if ($CFG->forceloginforprofiles && !!has_coursemanager_role($user->id) && !has_capability('moodle/user:viewdetails', $usercontext)) {  // Reduce possibility of "browsing" userbase at site level
                    // Teachers can browse and be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
                    return false;
                }
            } else {
                if ((!has_capability('moodle/user:viewdetails', $coursecontext) && !has_capability('moodle/user:viewdetails', $usercontext)) || !has_capability('moodle/course:participate', $coursecontext, $user->id, false)) {
                    return false;
                }
                if (groups_get_course_groupmode($course) == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $coursecontext)) {
                    // If groups are in use, make sure we can see that group
                    return false;
                }
            }
        }

        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $this->page->context));

        // Add a user setting branch
        $usersetting = $this->add(get_string($gstitle, 'moodle', $fullname));
        $usersetting->id = 'usersettings';

        // Check if the user has been deleted
        if ($user->deleted) {
            if (!has_capability('moodle/user:update', $coursecontext)) {
                // We can't edit the user so just show the user deleted message
                $usersetting->add(get_string('userdeleted'), null, self::TYPE_SETTING);
            } else {
                // We can edit the user so show the user deleted message and link it to the profile
                $profileurl = new moodle_url('/user/view.php', array('id'=>$user->id, 'course'=>$course->id));
                $usersetting->add(get_string('userdeleted'), $profileurl, self::TYPE_SETTING);
            }
            return true;
        }

        // Add the profile edit link
        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (($currentuser || !is_primary_admin($user->id)) && has_capability('moodle/user:update', $systemcontext)) {
                $url = new moodle_url('/user/editadvanced.php', array('id'=>$user->id, 'course'=>$course->id));
                $usersetting->add(get_string('editmyprofile'), $url, self::TYPE_SETTING);
            } else if ((has_capability('moodle/user:editprofile', $usercontext) && !is_primary_admin($user->id)) || ($currentuser && has_capability('moodle/user:editownprofile', $systemcontext))) {
                $url = new moodle_url('/user/edit.php', array('id'=>$user->id, 'course'=>$course->id));
                $usersetting->add(get_string('editmyprofile'), $url, self::TYPE_SETTING);
            }
        }

        // Change password link
        if (!empty($user->auth)) {
            $userauth = get_auth_plugin($user->auth);
            if ($currentuser && !session_is_loggedinas() && $userauth->can_change_password() && !isguestuser() && has_capability('moodle/user:changeownpassword', $systemcontext)) {
                $passwordchangeurl = $userauth->change_password_url();
                if (!$passwordchangeurl) {
                    if (empty($CFG->loginhttps)) {
                        $wwwroot = $CFG->wwwroot;
                    } else {
                        $wwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
                    }
                    $passwordchangeurl = new moodle_url('/login/change_password.php');
                } else {
                    $urlbits = explode($passwordchangeurl. '?', 1);
                    $passwordchangeurl = new moodle_url($urlbits[0]);
                    if (count($urlbits)==2 && preg_match_all('#\&([^\=]*?)\=([^\&]*)#si', '&'.$urlbits[1], $matches)) {
                        foreach ($matches as $pair) {
                            $fullmeurl->param($pair[1],$pair[2]);
                        }
                    }
                }
                $passwordchangeurl->param('id', $course->id);
                $usersetting->add(get_string("changepassword"), $passwordchangeurl, self::TYPE_SETTING);
            }
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
                $portfolio->add(get_string('configure', 'portfolio'), new moodle_url('/user/portfolio.php'), self::TYPE_SETTING);
                $portfolio->add(get_string('logs', 'portfolio'), new moodle_url('/user/portfoliologs.php'), self::TYPE_SETTING);
            }
        }

        // Security keys
        if ($currentuser && !is_siteadmin($USER->id) && !empty($CFG->enablewebservices) && has_capability('moodle/webservice:createtoken', $systemcontext)) {
            $url = new moodle_url('/user/managetoken.php', array('sesskey'=>sesskey()));
            $usersetting->add(get_string('securitykeys', 'webservice'), $url, self::TYPE_SETTING);
        }

        // Repository
        if (!$currentuser) {
            require_once($CFG->dirroot . '/repository/lib.php');
            $editabletypes = repository::get_editable_types($usercontext);
            if ($usercontext->contextlevel == CONTEXT_USER && !empty($editabletypes)) {
                $url = new moodle_url('/repository/manage_instances.php', array('contextid'=>$usercontext->id));
                $usersetting->add(get_string('repositories', 'repository'), $url, self::TYPE_SETTING);
            }
        }

        // Messaging
        if (has_capability('moodle/user:editownmessageprofile', $systemcontext)) {
            $url = new moodle_url('/message/edit.php', array('id'=>$user->id, 'course'=>$course->id));
            $usersetting->add(get_string('editmymessage', 'message'), $url, self::TYPE_SETTING);
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

        if ($this->page->user_is_editing() && has_capability('moodle/category:manage', $this->context)) {
            $categorynode->add(get_string('editcategorythis'), new moodle_url('/course/editcategory.php', array('id' => $this->context->instanceid)));
            $categorynode->add(get_string('addsubcategory'), new moodle_url('/course/editcategory.php', array('parent' => $this->context->instanceid)));
        }

        // Assign local roles
        $assignurl = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$this->context->id));
        $categorynode->add(get_string('assignroles', 'role'), $assignurl, self::TYPE_SETTING);

        // Override roles
        if (has_capability('moodle/role:review', $this->context) or count(get_overridable_roles($this->context))>0) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING);
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $this->context)) {
            $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING);
        }
        // Manage filters
        if (has_capability('moodle/filter:manage', $this->context) && count(filter_get_available_in_context($this->context))>0) {
            $url = new moodle_url('/filter/manage.php', array('contextid'=>$this->context->id));
            $categorynode->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING);
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
            $frontpage->add(get_string('settings'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
        }

        //Participants
        if (has_capability('moodle/site:viewparticipants', $coursecontext)) {
            $url = new moodle_url('/user/index.php', array('contextid'=>$coursecontext->id));
            $frontpage->add(get_string('participants'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/users', ''));
        }

        // Roles
        if (has_capability('moodle/role:assign', $coursecontext)) {
            // Add assign or override roles if allowed
            $url = new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid'=>$coursecontext->id));
            $frontpage->add(get_string('assignroles', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            // Override roles
            if (has_capability('moodle/role:review', $coursecontext) or count(get_overridable_roles($coursecontext))>0) {
                $url = new moodle_url('/'.$CFG->admin.'/roles/permissions.php', array('contextid'=>$coursecontext->id));
                $frontpage->add(get_string('permissions', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            }
            // Check role permissions
            if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $coursecontext)) {
                $url = new moodle_url('/'.$CFG->admin.'/roles/check.php', array('contextid'=>$coursecontext->id));
                $frontpage->add(get_string('checkpermissions', 'role'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/roles', ''));
            }
            // Manage filters
            if (has_capability('moodle/filter:manage', $coursecontext) && count(filter_get_available_in_context($coursecontext))>0) {
                $url = new moodle_url('/filter/manage.php', array('contextid'=>$coursecontext->id));
                $frontpage->add(get_string('filters', 'admin'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/filter', ''));
            }
        }

        // Backup this course
        if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
            $url = new moodle_url('/backup/backup.php', array('id'=>$course->id));
            $frontpage->add(get_string('backup'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/backup', ''));
        }

        // Restore to this course
        if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
            $url = new moodle_url('/files/index.php', array('id'=>$course->id, 'wdir'=>'/backupdata'));
            $frontpage->add(get_string('restore'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/restore', ''));
        }

        // Manage questions
        $questioncaps = array('moodle/question:add',
                              'moodle/question:editmine',
                              'moodle/question:editall',
                              'moodle/question:viewmine',
                              'moodle/question:viewall',
                              'moodle/question:movemine',
                              'moodle/question:moveall');
        if (has_any_capability($questioncaps, $this->context)) {
            $questionlink = $CFG->wwwroot.'/question/edit.php';
        } else if (has_capability('moodle/question:managecategory', $this->context)) {
            $questionlink = $CFG->wwwroot.'/question/category.php';
        }
        if (isset($questionlink)) {
            $url = new moodle_url($questionlink, array('courseid'=>$course->id));
            $frontpage->add(get_string('questions','quiz'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/questions', ''));
        }

        // Manage files
        if (has_capability('moodle/course:managefiles', $this->context)) {
            $url = new moodle_url('/files/index.php', array('id'=>$course->id));
            $frontpage->add(get_string('files'), $url, self::TYPE_SETTING, null, null, new pix_icon('i/files', ''));
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
            $this->expandable[(string)$node['branchid']] = $node;
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
        global $OUTPUT;

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
        if (array_key_exists((string)$child->key, $this->expandable)) {
            $attributes['expandable'] = $child->key;
            $child->add_class($this->expandable[$child->key]['id']);
        }

        if (count($child->classes)>0) {
            $attributes['class'] .= ' '.join(' ',$child->classes);
        }
        if (is_string($child->action)) {
            $attributes['link'] = $child->action;
        } else if ($child->action instanceof moodle_url) {
            $attributes['link'] = $child->action->out();
        }
        $attributes['hidden'] = ($child->hidden);
        $attributes['haschildren'] = ($child->children->count()>0 || $child->type == navigation_node::TYPE_CATEGORY);

        if (count($child->children)>0) {
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
    public function __construct($area, $timeout=60) {
        global $SESSION;
        $this->creation = time();
        $this->area = $area;

        if (!isset($SESSION->navcache)) {
            $SESSION->navcache = new stdClass;
        }

        if (!isset($SESSION->navcache->{$area})) {
            $SESSION->navcache->{$area} = array();
        }
        $this->session = &$SESSION->navcache->{$area};
        $this->timeout = time()-$timeout;
        if (rand(0,10)===0) {
            $this->garbage_collection();
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
    public function compare($key, $value, $serialise=true) {
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
        $this->session = array();
    }
    /**
     * Checks all cache entries and removes any that have expired, good ole cleanup
     */
    protected function garbage_collection() {
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
        }
    }
}
