<?php //$Id$

/**
 * This file contains the parent class for moodle pages, page_base, 
 * as well as the page_course subclass.
 * A page is defined by its page type (ie. course, blog, activity) and its page id
 * (courseid, blogid, activity id, etc).
 *
 * @author Jon Papaioannou
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package pages
 */

function page_import_types($path) {
    global $CFG;

    static $types = array();

    if(substr($path, -1) != '/') {
        $path .= '/';
    }

    $path = clean_param($path, PARAM_PATH);

    if(isset($types[$path])) {
        return $types[$path];
    }

    $file = $CFG->dirroot.'/'.$path.'pagelib.php';

    if(is_file($file)) {
        require($file);
        if(!isset($DEFINEDPAGES)) {
            error('Imported '.$file.' but found no page classes');
        }
        return $types[$path] = $DEFINEDPAGES;
    }

    return false;
}

/**
 * Factory function page_create_object(). Called with a numeric ID for a page, it autodetects
 * the page type, constructs the correct object and returns it.
 */

function page_create_instance($instance) {
    page_id_and_class($id, $class);
    return page_create_object($id, $instance);
}

/**
 * Factory function page_create_object(). Called with a pagetype identifier and possibly with
 * its numeric ID. Returns a fully constructed page_base subclass you can work with.
 */

function page_create_object($type, $id = NULL) {
    global $CFG;

    $data = new stdClass;
    $data->pagetype = $type;
    $data->pageid   = $id;

    $classname = page_map_class($type);

    $object = new $classname;
    // TODO: subclassing check here

    if ($object->get_type() !== $type) {
        // Somehow somewhere someone made a mistake
        debugging('Page object\'s type ('. $object->get_type() .') does not match requested type ('. $type .')');
    }

    $object->init_quick($data);
    return $object;
}

/**
 * Function page_map_class() is the way for your code to define its own page subclasses and let Moodle recognize them.
 * Use it to associate the textual identifier of your Page with the actual class name that has to be instantiated.
 */

function page_map_class($type, $classname = NULL) {
    global $CFG;

    static $mappings = NULL;
    
    if ($mappings === NULL) {
        $mappings = array(
            PAGE_COURSE_VIEW => 'page_course'
        );
    }

    if (!empty($type) && !empty($classname)) {
        $mappings[$type] = $classname;
    }

    if (!isset($mappings[$type])) {
        debugging('Page class mapping requested for unknown type: '.$type);
    }

    if (empty($classname) && !class_exists($mappings[$type])) {
        debugging('Page class mapping for id "'.$type.'" exists but class "'.$mappings[$type].'" is not defined');
    }

    return $mappings[$type];
}

/**
 * Parent class from which all Moodle page classes derive
 *
 * @author Jon Papaioannou
 * @package pages
 * @todo This parent class is very messy still. Please for the moment ignore it and move on to the derived class page_course to see the comments there.
 */

class page_base {
    /**
     * The string identifier for the type of page being described.
     * @var string $type
     */
    var $type           = NULL;

    /**
     * The numeric identifier of the page being described.
     * @var int $id
     */
    var $id             = NULL;

    /**
     * Class bool to determine if the instance's full initialization has been completed.
     * @var boolean $full_init_done
     */
    var $full_init_done = false;

    /**
     * The class attribute that Moodle has to assign to the BODY tag for this page.
     * @var string $body_class
     */
    var $body_class     = NULL;

    /**
     * The id attribute that Moodle has to assign to the BODY tag for this page.
     * @var string $body_id
     */
    var $body_id        = NULL;

/// Class Functions

    // CONSTRUCTION

    // A whole battery of functions to allow standardized-name constructors in all versions of PHP.
    // The constructor is actually called construct()
    function page_base() {
        $this->construct();
    }

    function construct() {
        page_id_and_class($this->body_id, $this->body_class);
    }

    // USER-RELATED THINGS

    // By default, no user is editing anything and none CAN edit anything. Developers
    // will have to override these settings to let Moodle know when it should grant
    // editing rights to the user viewing the page.
    function user_allowed_editing() {
        trigger_error('Page class does not implement method <strong>user_allowed_editing()</strong>', E_USER_WARNING);
        return false;
    }
    function user_is_editing() {
        trigger_error('Page class does not implement method <strong>user_is_editing()</strong>', E_USER_WARNING);
        return false;
    }

    // HTML OUTPUT SECTION

    // We have absolutely no idea what derived pages are all about
    function print_header($title, $morenavlinks=NULL) {
        trigger_error('Page class does not implement method <strong>print_header()</strong>', E_USER_WARNING);
        return;
    }

    // BLOCKS RELATED SECTION

    // By default, pages don't have any blocks. Override this in your derived class if you need blocks.
    function blocks_get_positions() {
        return array();
    }

    // Thus there is no default block position. If you override the above you should override this one too.
    // Because this makes sense only if blocks_get_positions() is overridden and because these two should
    // be overridden as a group or not at all, this one issues a warning. The sneaky part is that this warning
    // will only be seen if you override blocks_get_positions() but NOT blocks_default_position().
    function blocks_default_position() {
        trigger_error('Page class does not implement method <strong>blocks_default_position()</strong>', E_USER_WARNING);
        return NULL;
    }

    // If you don't override this, newly constructed pages of this kind won't have any blocks.
    function blocks_get_default() {
        return '';
    }

    // If you don't override this, your blocks will not be able to change positions
    function blocks_move_position(&$instance, $move) {
        return $instance->position;
    }

    // SELF-REPORTING SECTION

    // Derived classes HAVE to define their "home url"
    function url_get_path() {
        trigger_error('Page class does not implement method <strong>url_get_path()</strong>', E_USER_WARNING);
        return NULL;
    }

    // It's not always required to pass any arguments to the home url, so this doesn't trigger any errors (sensible default)
    function url_get_parameters() {
        return array();
    }

    // This should actually NEVER be overridden unless you have GOOD reason. Works fine as it is.
    function url_get_full($extraparams = array()) {
        $path = $this->url_get_path();
        if(empty($path)) {
            return NULL;
        }

        $params = $this->url_get_parameters();
        if (!empty($params)) {
            $params = array_merge($params, $extraparams);
        } else {
            $params = $extraparams;
        }

        if(empty($params)) {
            return $path;
        }
        
        $first = true;

        foreach($params as $var => $value) {
            $path .= $first? '?' : '&amp;';
            $path .= $var .'='. urlencode($value);
            $first = false;
        }

        return $path;
    }

    // This forces implementers to actually hardwire their page identification constant in the class.
    // Good thing, if you ask me. That way we can later auto-detect "installed" page types by querying
    // the classes themselves in the future.
    function get_type() {
        trigger_error('Page class does not implement method <strong>get_type()</strong>', E_USER_ERROR);
        return NULL;
    }

    // Simple stuff, do not override this.
    function get_id() {
        return $this->id;
    }

    // "Sensible default" case here. Take it from the body id.
    function get_format_name() {
        return $this->body_id;
    }

    // Returns $this->body_class
    function get_body_class() {
        return $this->body_class;
    }

    // Returns $this->body_id
    function get_body_id() {
        return $this->body_id;
    }

    // Initialize the data members of the parent class
    function init_quick($data) {
        $this->type = $data->pagetype;
        $this->id   = $data->pageid;
    }

    function init_full() {
        $this->full_init_done = true;
    }


    // is this  page always editable, regardless of anything else?
    function edit_always() {
        return (has_capability('moodle/site:manageblocks', get_context_instance(CONTEXT_SYSTEM)) &&  defined('ADMIN_STICKYBLOCKS'));
    }
}


/**
 * Class that models the behavior of a moodle course
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_course extends page_base {

    // Any data we might need to store specifically about ourself should be declared here.
    // After init_full() is called for the first time, ALL of these variables should be
    // initialized correctly and ready for use.
    var $courserecord = NULL;

    // Do any validation of the officially recognized bits of the data and forward to parent.
    // Do NOT load up "expensive" resouces (e.g. SQL data) here!
    function init_quick($data) {
        if(empty($data->pageid) && !defined('ADMIN_STICKYBLOCKS')) {
            error('Cannot quickly initialize page: empty course id');
        }
        parent::init_quick($data);
    }

    // Here you should load up all heavy-duty data for your page. Basically everything that
    // does not NEED to be loaded for the class to make basic decisions should NOT be loaded
    // in init_quick() and instead deferred here. Of course this function had better recognize
    // $this->full_init_done to prevent wasteful multiple-time data retrieval.
    function init_full() {
        global $COURSE;
        if($this->full_init_done) {
            return;
        }
        if (empty($this->id)) {
            $this->id = 0; // avoid db errors
        }
        if ($this->id == $COURSE->id) {
            $this->courserecord = $COURSE;
        } else {
            $this->courserecord = get_record('course', 'id', $this->id);
        }

        if(empty($this->courserecord) && !defined('ADMIN_STICKYBLOCKS')) {
            error('Cannot fully initialize page: invalid course id '. $this->id);
        }

        $this->context = get_context_instance(CONTEXT_COURSE, $this->id);

        // Preload - ensures that the context cache is populated
        // in one DB query...
        $this->childcontexts = get_child_contexts($this->context);

        // Mark we're done
        $this->full_init_done = true;
    }

    // USER-RELATED THINGS

    // Can user edit the course page or "sticky page"?
    // This is also about editting of blocks BUT mainly activities in course page layout, see
    // update_course_icon() has very similar checks - it must use the same capabilities
    //
    // this is a _very_ expensive check - so cache it during execution
    //
    function user_allowed_editing() {

        $this->init_full();

        if (isset($this->_user_allowed_editing)) {
            return $this->_user_allowed_editing;
        }

        if (has_capability('moodle/site:manageblocks', get_context_instance(CONTEXT_SYSTEM))
            && defined('ADMIN_STICKYBLOCKS')) {
            $this->_user_allowed_editing = true;
            return true;
        }
        if (has_capability('moodle/course:manageactivities', $this->context)) {
            $this->_user_allowed_editing = true;
            return true;
        }

        // Exhaustive (and expensive!) checks to see if the user
        // has editing abilities to a specific module/block/group...
        // This code would benefit from the ability to check specifically
        // for overrides.
        foreach ($this->childcontexts as $cc) {
            if (($cc->contextlevel == CONTEXT_MODULE &&
                 has_capability('moodle/course:manageactivities', $cc)) ||
                ($cc->contextlevel == CONTEXT_BLOCK &&
                 has_capability('moodle/site:manageblocks', $cc))) {
                $this->_user_allowed_editing = true;
                return true;
            }
        }
    }

    // Is the user actually editing this course page or "sticky page" right now?
    function user_is_editing() {
        if (has_capability('moodle/site:manageblocks', get_context_instance(CONTEXT_SYSTEM)) && defined('ADMIN_STICKYBLOCKS')) {
            //always in edit mode on sticky page
            return true;
        }
        return isediting($this->id);
    }

    // HTML OUTPUT SECTION

    // This function prints out the common part of the page's header.
    // You should NEVER print the header "by hand" in other code.
    function print_header($title, $morenavlinks=NULL, $meta='', $bodytags='', $extrabuttons='') {
        global $USER, $CFG;

        $this->init_full();
        $replacements = array(
            '%fullname%' => $this->courserecord->fullname
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }
    
        $navlinks = array();
        
        if(!empty($morenavlinks)) {
            $navlinks = array_merge($navlinks, $morenavlinks);
        }

        $navigation = build_navigation($navlinks);

        // The "Editing On" button will be appearing only in the "main" course screen
        // (i.e., no breadcrumbs other than the default one added inside this function)
        $buttons = switchroles_form($this->courserecord->id);
        if ($this->user_allowed_editing()) {
            $buttons .= update_course_icon($this->courserecord->id );
        }
        $buttons = empty($morenavlinks) ? $buttons : '&nbsp;';

        // Add any extra buttons requested (by the resource module, for example)
        if ($extrabuttons != '') {
            $buttons = ($buttons == '&nbsp;') ? $extrabuttons : $buttons.$extrabuttons;
        }

        print_header($title, $this->courserecord->fullname, $navigation,
                     '', $meta, true, $buttons, user_login_string($this->courserecord, $USER), false, $bodytags);
    }

    // SELF-REPORTING SECTION

    // This is hardwired here so the factory function page_create_object() can be sure there was no mistake.
    // Also, it doubles as a way to let others inquire about our type.
    function get_type() {
        return PAGE_COURSE_VIEW;
    }

    // This is like the "category" of a page of this "type". For example, if the type is PAGE_COURSE_VIEW
    // the format_name is the actual name of the course format. If the type were PAGE_ACTIVITY_VIEW, then
    // the format_name might be that activity's name etc.
    function get_format_name() {
        $this->init_full();
        if (defined('ADMIN_STICKYBLOCKS')) {
            return PAGE_COURSE_VIEW;
        }
        if($this->id == SITEID) {
            return parent::get_format_name();
        }
        // This needs to reflect the path hierarchy under Moodle root.
        return 'course-view-'.$this->courserecord->format;
    }

    // This should return a fully qualified path to the URL which is responsible for displaying us.
    function url_get_path() {
        global $CFG;
        if (defined('ADMIN_STICKYBLOCKS')) {
            return $CFG->wwwroot.'/'.$CFG->admin.'/stickyblocks.php';
        }
        if($this->id == SITEID) {
            return $CFG->wwwroot .'/index.php';
        }
        else {
            return $CFG->wwwroot .'/course/view.php';
        }
    }

    // This should return an associative array of any GET/POST parameters that are needed by the URL
    // which displays us to make it work. If none are needed, return an empty array.
    function url_get_parameters() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return array('pt' => ADMIN_STICKYBLOCKS);
        }
        if($this->id == SITEID) {
            return array();
        }
        else {
            return array('id' => $this->id);
        }
    }

    // BLOCKS RELATED SECTION

    // Which are the positions in this page which support blocks? Return an array containing their identifiers.
    // BE CAREFUL, ORDER DOES MATTER! In textual representations, lists of blocks in a page use the ':' character
    // to delimit different positions in the page. The part before the first ':' in such a representation will map
    // directly to the first item of the array you return here, the second to the next one and so on. This way,
    // you can add more positions in the future without interfering with legacy textual representations.
    function blocks_get_positions() {
        return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT);
    }

    // When a new block is created in this page, which position should it go to?
    function blocks_default_position() {
        return BLOCK_POS_RIGHT;
    }

    // When we are creating a new page, use the data at your disposal to provide a textual representation of the
    // blocks that are going to get added to this new page. Delimit block names with commas (,) and use double
    // colons (:) to delimit between block positions in the page. See blocks_get_positions() for additional info.
    function blocks_get_default() {
        global $CFG;
        
        $this->init_full();

        if($this->id == SITEID) {
        // Is it the site?
            if (!empty($CFG->defaultblocks_site)) {
                $blocknames = $CFG->defaultblocks_site;
            }
            /// Failsafe - in case nothing was defined.
            else {
                $blocknames = 'site_main_menu,admin_tree:course_summary,calendar_month';
            }
        }
        // It's a normal course, so do it according to the course format
        else {
            $pageformat = $this->courserecord->format;
            if (!empty($CFG->{'defaultblocks_'. $pageformat})) {
                $blocknames = $CFG->{'defaultblocks_'. $pageformat};
            }
            else {
                $format_config = $CFG->dirroot.'/course/format/'.$pageformat.'/config.php';
                if (@is_file($format_config) && is_readable($format_config)) {
                    require($format_config);
                }
                if (!empty($format['defaultblocks'])) {
                    $blocknames = $format['defaultblocks'];
                }
                else if (!empty($CFG->defaultblocks)){
                    $blocknames = $CFG->defaultblocks;
                }
                /// Failsafe - in case nothing was defined.
                else {
                    $blocknames = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
                }
            }
        }
        
        return $blocknames;
    }

    // Given an instance of a block in this page and the direction in which we want to move it, where is
    // it going to go? Return the identifier of the instance's new position. This allows us to tell blocklib
    // how we want the blocks to move around in this page in an arbitrarily complex way. If the move as given
    // does not make sense, make sure to return the instance's original position.
    //
    // Since this is going to get called a LOT, pass the instance by reference purely for speed. Do **NOT**
    // modify its data in any way, this will actually confuse blocklib!!!
    function blocks_move_position(&$instance, $move) {
        if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
            return BLOCK_POS_RIGHT;
        } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
            return BLOCK_POS_LEFT;
        }
        return $instance->position;
    }
}

/**
 * Class that models the common parts of all activity modules
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_generic_activity extends page_base {
    var $activityname   = NULL;
    var $courserecord   = NULL;
    var $modulerecord   = NULL;
    var $activityrecord = NULL;

    function init_full() {
        if($this->full_init_done) {
            return;
        }
        if(empty($this->activityname)) {
            error('Page object derived from page_generic_activity but did not define $this->activityname');
        }
        if (!$this->modulerecord = get_coursemodule_from_instance($this->activityname, $this->id)) {
            error('Cannot fully initialize page: invalid '.$this->activityname.' instance id '. $this->id);
        }
        $this->courserecord = get_record('course', 'id', $this->modulerecord->course);
        if(empty($this->courserecord)) {
            error('Cannot fully initialize page: invalid course id '. $this->modulerecord->course);
        }
        $this->activityrecord = get_record($this->activityname, 'id', $this->id);
        if(empty($this->activityrecord)) {
            error('Cannot fully initialize page: invalid '.$this->activityname.' id '. $this->id);
        }
        $this->full_init_done = true;
    }

    function user_allowed_editing() {
        $this->init_full();
        // Yu: I think this is wrong, should be checking manageactivities instead
        //return has_capability('moodle/site:manageblocks', get_context_instance(CONTEXT_COURSE, $this->modulerecord->course));
        return has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_MODULE, $this->modulerecord->id));         
    }

    function user_is_editing() {
        $this->init_full();
        return isediting($this->modulerecord->course);
    }

    function url_get_path() {
        global $CFG;
        return $CFG->wwwroot .'/mod/'.$this->activityname.'/view.php';
    }

    function url_get_parameters() {
        $this->init_full();
        return array('id' => $this->modulerecord->id);
    }

    function blocks_get_positions() {
        return array(BLOCK_POS_LEFT);
    }

    function blocks_default_position() {
        return BLOCK_POS_LEFT;
    }
    
    function print_header($title, $morenavlinks = NULL, $bodytags = '', $meta = '') {
        global $USER, $CFG;
    
        $this->init_full();
        $replacements = array(
            '%fullname%' => format_string($this->activityrecord->name)
        );
        foreach ($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }
    
        if (empty($morenavlinks) && $this->user_allowed_editing()) {
            $buttons = '<table><tr><td>'.update_module_button($this->modulerecord->id, $this->courserecord->id, get_string('modulename', $this->activityname)).'</td>';
            if (!empty($CFG->showblocksonmodpages)) {
                $buttons .= '<td><form '.$CFG->frametarget.' method="get" action="view.php"><div>'.
                    '<input type="hidden" name="id" value="'.$this->modulerecord->id.'" />'.
                    '<input type="hidden" name="edit" value="'.($this->user_is_editing()?'off':'on').'" />'.
                    '<input type="submit" value="'.get_string($this->user_is_editing()?'blockseditoff':'blocksediton').'" /></div></form></td>';
            }
            $buttons .= '</tr></table>';
        } else {
            $buttons = '&nbsp;';
        }
        
        if (empty($morenavlinks)) {
            $morenavlinks = array();
        }
        $navigation = build_navigation($morenavlinks, $this->modulerecord);
        print_header($title, $this->courserecord->fullname, $navigation, '', $meta, true, $buttons, navmenu($this->courserecord, $this->modulerecord), false, $bodytags);
    }
    
}

?>
