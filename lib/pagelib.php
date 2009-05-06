<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * This file contains the moodle_page class. There is normally a single instance
 * of this class in the $PAGE global variable. This class is a central reporitory
 * of information about the page we are building up to send back to the user.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package pages
 */

/**
 * $PAGE is a central store of information about the current page we are
 * generating in response to the user's request. It does not do very much itself
 * except keep track of information, however, it serves as the access point to
 * some more significant components like $PAGE->theme, $PAGE->requires,
 * $PAGE->blocks, etc.
 */
class moodle_page {
    /**#@+ Tracks the where we are in the generation of the page. */
    const STATE_BEFORE_HEADER = 0;
    const STATE_PRINTING_HEADER = 1;
    const STATE_IN_BODY = 2;
    const STATE_PRINTING_FOOTER = 3;
    const STATE_DONE = 4;
    /**#@-*/

/// Field declarations =========================================================

    protected $_state = self::STATE_BEFORE_HEADER;

    protected $_course = null;

    protected $_context = null;

    protected $_bodyclasses = array();

    protected $_pagetype = null;

    protected $_legacyclass = null;

/// Getter methods =============================================================
/// Due to the __get magic below, you normally do not call these as $PAGE->get_x
/// methods, but instead use the $PAGE->x syntax.

    /**
     * @return integer one of the STATE_... constants. You should not normally need
     * to use this in your code. It is indended for internal use by this class
     * and its friends like print_header, to check that everything is working as
     * expected. Also accessible as $PAGE->state.
     */
    public function get_state() {
        return $this->_state;
    }

    /**
     * @return boolean has the header already been printed? Also accessible as
     * $PAGE->headerprinted.
     */
    public function get_headerprinted() {
        return $this->_state >= self::STATE_IN_BODY;
    }

    /**
     * @return object the current course that we are inside - a row from the
     * course table. (Also available as $COURSE global.) If we are not inside
     * an actual course, this will be the site course. You can also access this
     * as $PAGE->course.
     */
    public function get_course() {
        global $SITE;
        if (is_null($this->_course)) {
            return $SITE;
        }
        return $this->_course;
    }

    /**
     * @return object the main context to which this page belongs.
     */
    public function get_context() {
        if (is_null($this->_context)) {
            throw new coding_exception('$PAGE->context accessed before it was known.');
        }
        return $this->_context;
    }

    /**
     * @return string e.g. 'my-index' or 'mod-quiz-attempt'. Same as the id attribute on <body>.
     */
    public function get_pagetype() {
        if (is_null($this->_pagetype) || isset($CFG->pagepath)) {
            $this->initialise_default_pagetype();
        }
        return $this->_pagetype;
    }

    /**
     * @return string the class names to put on the body element in the HTML.
     */
    public function get_bodyclasses() {
        return implode(' ', array_keys($this->_bodyclasses));
    }

/// Setter methods =============================================================

    /**
     * Set the state. The state must be one of that STATE_... constants, and
     * the state is only allowed to advance one step at a time.
     * @param integer $state the new state.
     */
    public function set_state($state) {
        if ($state != $this->_state + 1 || $state > self::STATE_DONE) {
            throw new coding_exception('Invalid state passed to moodle_page::set_state. We are in state ' .
                    $this->_state . ' and state ' . $state . ' was requestsed.');
        }

        if ($state == self::STATE_PRINTING_HEADER) {
            if (!$this->_course) {
                global $SITE;
                $this->set_course($SITE);
            }

            $this->initialise_standard_body_classes();
        }

        $this->_state = $state;
    }

    /**
     * Set the current course. This sets both $PAGE->course and $COURSE. It also
     * sets the right theme and locale.
     *
     * Normally you don't need to call this function yourself, require_login will
     * call it for you if you pass a $course to it. You can use this function
     * on pages that do need to call require_login().
     *
     * Sets $PAGE->context to the course context, if it is not already set.
     *
     * @param object the course to set as the global course.
     */
    public function set_course($course) {
        global $COURSE, $SITE;

        if (empty($course->id)) {
            throw new coding_exception('$course passed to moodle_page::set_course does not look like a proper course object.');
        }

        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('Cannot call moodle_page::set_course after output has been started.');
        }

        $this->_course = clone($course);
        $COURSE = $this->_course;

        if (!$this->_context) {
            $this->set_context(get_context_instance(CONTEXT_COURSE, $this->_course->id));
        }

        moodle_setlocale();
        theme_setup();
    }

    /**
     * Set the main context to which this page belongs.
     * @param object $context a context object, normally obtained with get_context_instance.
     */
    public function set_context($context) {
        $this->_context = $context;
    }

    /**
     * @param string $pagetype e.g. 'my-index' or 'mod-quiz-attempt'. Normally
     * you do not need to set this manually, it is automatically created from the
     * script name. However, on some pages this is overridden. For example, the
     * page type for coures/view.php includes the course format, for example
     * 'coures-view-weeks'. This gets used as the id attribute on <body> and
     * also for determining which blocks are displayed.
     */
    public function set_pagetype($pagetype) {
        $this->_pagetype = $pagetype;
    }

    /**
     * @param string $class add this class name ot the class attribute on the body tag.
     */
    public function add_body_class($class) {
        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('Cannot call moodle_page::add_body_class after output has been started.');
        }
        $this->_bodyclasses[$class] = 1;
    }

    /**
     * PHP overloading magic to make the $PAGE->course syntax work.
     */
    public function __get($field) {
        $getmethod = 'get_' . $field;
        if (method_exists($this, $getmethod)) {
            return $this->$getmethod();
        } else {
            throw new coding_exception('Unknown field ' . $field . ' of $PAGE.');
        }
    }

/// Initialisation methods =====================================================
/// These set various things up in a default way.

    /**
     * Sets ->pagetype from the script name. For example, if the script that was
     * run is mod/quiz/view.php, ->pagetype will be set to 'mod-quiz-view'.
     * @param string $script the path to the script that should be used to
     * initialise ->pagetype. If not passed the $SCRIPT global will be used.
     * If legacy code has set $CFG->pagepath that will be used instead, and a
     * developer warning issued.
     */
    protected function initialise_default_pagetype($script = '') {
        global $CFG, $SCRIPT;

        if (isset($CFG->pagepath)) {
            debugging('Some code appears to have set $CFG->pagepath. That was a horrible deprecated thing. ' .
                    'Don\'t do it! Try calling $PAGE->set_pagetype() instead.');
            $script = $CFG->pagepath;
            unset($CFG->pagepath);
        }

        if (empty($script)) {
            $script = ltrim($SCRIPT, '/');
            $len = strlen($CFG->admin);
            if (substr($script, 0, $len) == $CFG->admin) {
                $script = 'admin' . substr($script, $len);
            }
        }

        $path = str_replace('.php', '', $script);
        if (substr($path, -1) == '/') {
            $path .= 'index';
        }

        if (empty($path) || $path == 'index') {
            $this->_pagetype = 'site-index';
        } else {
            $this->_pagetype = str_replace('/', '-', $path);
        }
    }

    protected function initialise_standard_body_classes() {
        $pagetype = $this->pagetype;
        if ($pagetype == 'site-index') {
            $this->_legacyclass = 'course';
        } else if (substr($pagetype, 0, 6) == 'admin-') {
            $this->_legacyclass = 'admin';
        } else {
            $this->_legacyclass = substr($pagetype, 0, strrpos($pagetype, '-'));
        }
        $this->add_body_class($this->_legacyclass);

        $this->add_body_class('course-' . $this->_course->id);
        $this->add_body_class(get_browser_version_classes());
        $this->add_body_class('dir-' . get_string('thisdirection'));
        $this->add_body_class('lang-' . current_language());

        if (!isloggedin()) {
            $this->add_body_class('notloggedin');
        }

        if (!empty($USER->editing)) {
            $this->add_body_class('editing');
        }

        if (!empty($CFG->blocksdrag)) {
            $this->add_body_class('drag');
        }
    }

/// Deprecated fields and methods for backwards compatibility ==================

    /**
     * @deprecated since Moodle 2.0 - use $PAGE->pagetype instead.
     * @return string page type.
     */
    public function get_type() {
        debugging('Call to deprecated method moodle_page::get_type. Please use $PAGE->pagetype instead.');
        return $this->get_pagetype();
    }

    /**
     * @deprecated since Moodle 2.0 - use $PAGE->pagetype instead.
     * @return string this is what page_id_and_class used to return via the $getclass parameter.
     */
    function get_format_name() {
        return $this->get_pagetype();
    }

    /**
     * @deprecated since Moodle 2.0 - use $PAGE->course instead.
     * @return object course.
     */
    public function get_courserecord() {
        debugging('Call to deprecated method moodle_page::get_courserecord. Please use $PAGE->course instead.');
        return $this->get_course();
    }

    /**
     * @deprecated since Moodle 2.0
     * @return string this is what page_id_and_class used to return via the $getclass parameter.
     */
    public function get_legacyclass() {
        if (is_null($this->_legacyclass)) {
            $this->initialise_standard_body_classes();
        }
        debugging('Call to deprecated method moodle_page::get_legacyclass.');
        return $this->_legacyclass;
    }
}

/**
 * @deprecated since Moodle 2.0
 * Load any page_base subclasses from the pagelib.php library in a particular folder.
 * @param $path the folder path
 * @return array an array of page types.
 */
function page_import_types($path) {
    global $CFG;
    debugging('Call to deprecated function page_import_types.', DEBUG_DEVELOPER);
}

/**
 * @deprecated since Moodle 2.0
 * @param integer $instance legacy page instance id.
 * @return the global $PAGE object.
 */
function page_create_instance($instance) {
    return page_create_object($PAGE->pagetype, $instance);
}

/**
 * Factory function page_create_object(). Called with a pagetype identifier and possibly with
 * its numeric ID. Returns a fully constructed page_base subclass you can work with.
 */
function page_create_object($type, $id = NULL) {
    global $CFG, $PAGE;

    $data = new stdClass;
    $data->pagetype = $type;
    $data->pageid   = $id;

    $classname = page_map_class($type);
    $object = new $classname;

    $object->init_quick($data);
    $object->set_course($PAGE->course);
    //$object->set_pagetype($type);
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
class page_base extends moodle_page {
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

/// Class Functions

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

    // Simple stuff, do not override this.
    function get_id() {
        return $this->id;
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

    // Do any validation of the officially recognized bits of the data and forward to parent.
    // Do NOT load up "expensive" resouces (e.g. SQL data) here!
    function init_quick($data) {
        if(empty($data->pageid) && !defined('ADMIN_STICKYBLOCKS')) {
            print_error('cannotinitpage', 'debug', '', (object)array('name'=>'course', 'id'=>'?'));
        }
        parent::init_quick($data);
    }

    // Here you should load up all heavy-duty data for your page. Basically everything that
    // does not NEED to be loaded for the class to make basic decisions should NOT be loaded
    // in init_quick() and instead deferred here. Of course this function had better recognize
    // $this->full_init_done to prevent wasteful multiple-time data retrieval.
    function init_full() {
        global $COURSE, $DB;

        if($this->full_init_done) {
            return;
        }
        if (empty($this->id)) {
            $this->id = 0; // avoid db errors
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
            '%fullname%' => $this->course->fullname
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
        $buttons = switchroles_form($this->course->id);
        if ($this->user_allowed_editing()) {
            $buttons .= update_course_icon($this->course->id );
        }
        $buttons = empty($morenavlinks) ? $buttons : '&nbsp;';

        // Add any extra buttons requested (by the resource module, for example)
        if ($extrabuttons != '') {
            $buttons = ($buttons == '&nbsp;') ? $extrabuttons : $buttons.$extrabuttons;
        }

        print_header($title, $this->course->fullname, $navigation,
                     '', $meta, true, $buttons, user_login_string($this->course, $USER), false, $bodytags);
    }

    // SELF-REPORTING SECTION

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
            $pageformat = $this->course->format;
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
    var $modulerecord   = NULL;
    var $activityrecord = NULL;

    function init_full() {
        global $DB;

        if($this->full_init_done) {
            return;
        }
        if(empty($this->activityname)) {
            print_error('noactivityname', 'debug');
        }
        if (!$this->modulerecord = get_coursemodule_from_instance($this->activityname, $this->id)) {
            print_error('cannotinitpager', 'debug', '', (object)array('name'=>$this->activityname, 'id'=>$this->id));
        }
        $this->activityrecord = $DB->get_record($this->activityname, array('id'=>$this->id));
        if(empty($this->activityrecord)) {
            print_error('cannotinitpager', 'debug', '', (object)array('name'=>$this->activityname, 'id'=>$this->id));
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
            $buttons = '<table><tr><td>'.update_module_button($this->modulerecord->id, $this->course->id, get_string('modulename', $this->activityname)).'</td>';
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
        print_header($title, $this->course->fullname, $navigation, '', $meta, true, $buttons, navmenu($this->course, $this->modulerecord), false, $bodytags);
    }
}

?>
