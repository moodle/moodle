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

    /**
     * This holds any categories that $_course belongs to, starting with the
     * particular category it belongs to, and working out through any parent
     * categories to the top level. These are loaded progressively, if neaded.
     * There are three states. $_categories = null initially when nothing is
     * loaded; $_categories = array($id => $cat, $parentid => null) when we have
     * loaded $_course->category, but not any parents; and a complete array once
     * everything is loaded.
     */
    protected $_categories = null;

    protected $_bodyclasses = array();

    protected $_pagetype = null;

    protected $_docspath = null;

    protected $_legacyclass = null;

    protected $_url = null;

    protected $_blocks = null;

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
     * @return mixed the category that the page course belongs to. If there isn't one
     * (that is, if this is the front page course) returns null.
     */
    public function get_category() {
        $this->ensure_category_loaded();
        if (!empty($this->_categories)) {
            return reset($this->_categories);
        } else {
            return null;
        }
    }

    /**
     * @return array an array of all the categories the page course belongs to,
     * starting with the immediately containing category, and working out to
     * the top-level category. This may be the empty array if we are in the
     * front page course.
     */
    public function get_categories() {
        $this->ensure_categories_loaded();
        return $this->_categories;
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

    /**
     * @return string the class names to put on the body element in the HTML.
     */
    public function get_docspath() {
        if (is_string($this->_docspath)) {
            return $this->_docspath;
        } else {
            return str_replace('-', '/', $this->pagetype);
        }
    }

    /**
     * @return moodle_url the clean URL required to load the current page. (You
     * should normally use this in preference to $ME or $FULLME.)
     */
    public function get_url() {
        if (is_null($this->_url)) {
            debugging('This page did no call $PAGE->set_url(...). Realying on a guess.', DEBUG_DEVELOPER);
            return new moodle_url($ME);
        }
        return new moodle_url($this->_url); // Return a clone for safety.
    }

    /**
     * @return blocks_manager the blocks manager object for this page.
     */
    public function get_blocks() {
        if (is_null($this->_blocks)) {
            $this->_blocks = new blocks_manager();
        }
        return $this->_blocks;
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

/// Other information getting methods ==========================================

    /**
     * @return boolean should the current user see this page in editing mode.
     * That is, are they allowed to edit this page, and are they currently in
     * editing mode.
     */
    public function user_is_editing() {
        global $USER;
        return !empty($USER->editing) && $this->user_allowed_editing();
    }

    /**
     * @return boolean does the user have permission to see this page in editing mode.
     */
    public function user_allowed_editing() {
        return true; // TODO
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
        global $COURSE;

        if (empty($course->id)) {
            throw new coding_exception('$course passed to moodle_page::set_course does not look like a proper course object.');
        }

        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('Cannot call moodle_page::set_course after output has been started.');
        }

        if (!empty($this->_course->id) && $this->_course->id != $course->id) {
            $this->_categories = null;
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
     * @param array $classes this utility method calls add_body_class for each array element.
     */
    public function add_body_classes($classes) {
        foreach ($classes as $class) {
            $this->add_body_class($class);
        }
    }

    /**
     * Set the course category this page belongs to manually. This automatically
     * sets $PAGE->course to be the site coures. You cannot use this method if
     * you have already set $PAGE->course - in that case, the category must be
     * the one that the course belongs to. This also automatically sets the
     * page context to the category context.
     * @param integer $categoryid The id of the category to set.
     */
    public function set_category_by_id($categoryid) {
        global $SITE, $DB;
        if (!is_null($this->_course)) {
            throw new coding_exception('Attempt to manually set the course category when the course has been set. This is not allowed.');
        }
        if (is_array($this->_categories)) {
            throw new coding_exception('Course category has already been set. You are not allowed to change it.');
        }
        $this->set_course($SITE);
        $this->load_category($categoryid);
        $this->set_context(get_context_instance(CONTEXT_COURSECAT, $categoryid));
    }

    /**
     * Set a different path to use for the 'Moodle docs for this page' link.
     * By default, it uses the pagetype, which is normally the same as the
     * script name. So, for example, for mod/quiz/attempt.php, pagetype is
     * mod-quiz-attempt, and so docspath is mod/quiz/attempt.
     * @param string $path the path to use at the end of the moodle docs URL.
     */
    public function set_docs_path($path) {
        $this->_docspath = $path;
    }

    /**
     * You should call this method from every page to set the cleaned-up URL
     * that should be used to return to this page. Used, for example, by the
     * blocks editing UI to know where to return the user after an action.
     * For example, course/view.php does:
     *      $id = optional_param('id', 0, PARAM_INT);
     *      $PAGE->set_url('course/view.php', array('id' => $id));
     * @param string $url a URL, relative to $CFG->wwwroot.
     * @param array $params paramters to add ot the URL.
     */
    public function set_url($url, $params = array()) {
        global $CFG;
        $this->_url = new moodle_url($CFG->wwwroot . '/' . $url, $params);
        if (is_null($this->_pagetype)) {
            $this->initialise_default_pagetype($url);
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
    protected function initialise_default_pagetype($script = null) {
        global $CFG, $SCRIPT;

        if (isset($CFG->pagepath)) {
            debugging('Some code appears to have set $CFG->pagepath. That was a horrible deprecated thing. ' .
                    'Don\'t do it! Try calling $PAGE->set_pagetype() instead.');
            $script = $CFG->pagepath;
            unset($CFG->pagepath);
        }

        if (is_null($script)) {
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
        global $CFG;

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
        $this->add_body_classes(get_browser_version_classes());
        $this->add_body_class('dir-' . get_string('thisdirection'));
        $this->add_body_class('lang-' . current_language());

        $this->add_body_class($this->url_to_class_name($CFG->wwwroot));

        if ($CFG->allowcategorythemes) {
            $this->ensure_category_loaded();
            foreach ($this->_categories as $catid => $notused) {
                $this->add_body_class('category-' . $catid);
            }
        } else {
            $catid = 0;
            if (is_array($this->_categories)) {
                $catids = array_keys($this->_categories);
                $catid = reset($catids);
            } else if (!empty($this->_course->category)) {
                $catid = $this->_course->category;
            }
            if ($catid) {
                $this->add_body_class('category-' . $catid);
            }
        }

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

    protected function ensure_category_loaded() {
        if (is_array($this->_categories)) {
            return; // Already done.
        }
        if (is_null($this->_course)) {
            throw new coding_exception('Attempt to get the course category for this page before the course was set.');
        }
        if ($this->_course->category == 0) {
            $this->_categories = array();
        } else {
            $this->load_category($this->_course->category);
        }
    }

    protected function load_category($categoryid) {
        global $DB;
        $category = $DB->get_record('course_categories', array('id' => $categoryid));
        if (!$category) {
            throw new moodle_exception('unknowncategory');
        }
        $this->_categories[$category->id] = $category;
        $parentcategoryids = explode('/', trim($category->path, '/'));
        array_pop($parentcategoryids);
        foreach (array_reverse($parentcategoryids) as $catid) {
            $this->_categories[$catid] = null;
        }
    }

    protected function ensure_categories_loaded() {
        global $DB;
        $this->ensure_category_loaded();
        if (!is_null(end($this->_categories))) {
            return; // Already done.
        }
        $idstoload = array_keys($this->_categories);
        array_shift($idstoload);
        $categories = $DB->get_records_list('course_categories', 'id', $idstoload);
        foreach ($idstoload as $catid) {
            $this->_categories[$catid] = $categories[$catid];
        }
    }

    protected function url_to_class_name($url) {
        $bits = parse_url($url);
        $class = str_replace('.', '-', $bits['host']);
        if (!empty($bits['port'])) {
            $class .= '--' . $bits['port'];
        }
        if (!empty($bits['path'])) {
            $path = trim($bits['path'], '/');
            if ($path) {
                $class .= '--' . str_replace('/', '-', $path);
            }
        }
        return $class;
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

    /**
     * @deprecated since Moodle 2.0 - use $PAGE->blocks->get_positions() instead
     * @return string the places on this page where blocks can go.
     */
    function blocks_get_positions() {
        debugging('Call to deprecated method moodle_page::blocks_get_positions. Use $PAGE->blocks->get_positions() instead.');
        return $this->blocks->get_positions();
    }

    /**
     * @deprecated since Moodle 2.0 - use $PAGE->blocks->get_default_position() instead
     * @return string the default place for blocks on this page.
     */
    function blocks_default_position() {
        debugging('Call to deprecated method moodle_page::blocks_default_position. Use $PAGE->blocks->get_default_position() instead.');
        return $this->blocks->get_default_position();
    }

    /**
     * @deprecated since Moodle 2.0 - no longer used.
     */
    function blocks_get_default() {
        debugging('Call to deprecated method moodle_page::blocks_get_default. This method has no function any more.');
    }

    /**
     * @deprecated since Moodle 2.0 - no longer used.
     */
    function blocks_move_position(&$instance, $move) {
        debugging('Call to deprecated method moodle_page::blocks_move_position. This method has no function any more.');
    }

    /**
     * @deprecated since Moodle 2.0 - use $this->url->params() instead.
     * @return array URL parameters for this page.
     */
    function url_get_parameters() {
        debugging('Call to deprecated method moodle_page::url_get_parameters. Use $this->url->params() instead.');
        return $this->url->params();
    }

    /**
     * @deprecated since Moodle 2.0 - use $this->url->params() instead.
     * @return string URL for this page without parameters.
     */
    function url_get_path() {
        debugging('Call to deprecated method moodle_page::url_get_path. Use $this->url->out(false) instead.');
        return $this->url->out(false);
    }

    /**
     * @deprecated since Moodle 2.0 - use $this->url->out() instead.
     * @return string full URL for this page.
     */
    function url_get_full($extraparams = array()) {
        debugging('Call to deprecated method moodle_page::url_get_full. Use $this->url->out() instead.');
        return $this->url->out($extraparams);
    }
}

/** Stub implementation of the blocks_manager, to stop things from breaking too badly. */
class blocks_manager {
    public function get_positions() {
        return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT);
    }

    public function get_default_position() {
        return BLOCK_POS_RIGHT;
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
    global $CFG, $PAGE, $SITE;

    $data = new stdClass;
    $data->pagetype = $type;
    $data->pageid   = $id;

    $classname = page_map_class($type);
    $legacypage = new $classname;
    $legacypage->init_quick($data);
    // $PAGE->set_pagetype($type);
    // $PAGE->set_url(str_replace($CFG->wwwroot . '/', '', $legacypage->url_get_full_()));
    // return $PAGE;

    $course = $PAGE->course;
    if ($course->id != $SITE->id) {
        $legacypage->set_course($course);
    } else {
        try {
            $category = $PAGE->category;
        } catch (coding_exception $e) {
            // Was not set before, so no need to try to set it again.
            $category = false;
        }
        if ($category) {
            $legacypage->set_category_by_id($category->id);
        } else {
            $legacypage->set_course($SITE);
        }
    }
    return $legacypage;
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

    // HTML OUTPUT SECTION

    // We have absolutely no idea what derived pages are all about
    function print_header($title, $morenavlinks=NULL) {
        trigger_error('Page class does not implement method <strong>print_header()</strong>', E_USER_WARNING);
        return;
    }

    // SELF-REPORTING SECTION


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

    // When we are creating a new page, use the data at your disposal to provide a textual representation of the
    // blocks that are going to get added to this new page. Delimit block names with commas (,) and use double
    // colons (:) to delimit between block positions in the page.
    function _legacy_blocks_get_default() {
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
