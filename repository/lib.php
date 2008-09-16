<?php
 // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2008 onwards  Moodle Pty Ltd   http://moodle.com        //
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
 * About repository/lib.php:
 * two main classes:
 * 1. repository_type => a repository plugin, You can activate a plugin into
 * Moodle. You also can set some general settings/options for this type of repository.
 * All instances would share the same options (for example: a API key for the connection
 * to the repository)
 * 2. repository => an instance of a plugin. You can also call it an access or
 * an account. An instance has specific settings (for example: a public url) and a specific
 * name. That's this name which is displayed in the file picker.
 */



/**
 * This is the base class of the repository class
 *
 * To use repository plugin, you need to create a new folder under repository/, named as the remote
 * repository, the subclass must be defined in  the name

 *
 * class repository is an abstract class, some functions must be implemented in subclass.
 *
 * See an example of use of this library in repository/boxnet/repository.class.php
 *
 * A few notes:
 *   // print login page or a link to redirect to another page
 *   $repo->print_login();
 *   // call get_listing, and print result
 *   $repo->print_listing();
 *   // print a search box
 *   $repo->print_search();
 *
 * @version 1.0 dev
 * @package repository
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib/filelib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/formslib.php');
// File picker javascript code
require_once(dirname(dirname(__FILE__)) . '/repository/javascript.php');

/**
 * A repository_type is a repository plug-in. It can be Box.net, Flick-r, ...
 * A repository type can be edited, sorted and hidden. It is mandatory for an
 * administrator to create a repository type in order to be able to create
 * some instances of this type.
 *
 * Coding note:
 * - a repository_type object is mapped to the "repository" database table
 * - "typename" attibut maps the "type" database field. It is unique.
 * - general "options" for a repository type are saved in the config_plugin table
 * - when you delete a repository, all instances are deleted, and general
 *   options are also deleted from database
 * - When you create a type for a plugin that can't have multiple instances, a
 *   instance is automatically created.
 */
class repository_type {


    /**
     * Type name (no whitespace) - A type name is unique
     * Note: for a user-friendly type name see get_readablename()
     * @var String
     */
    private $_typename;


    /**
     * Options of this type
     * They are general options that any instance of this type would share
     * e.g. API key
     * These options are saved in config_plugin table
     * @var array
     */
    private $_options;


    /**
     * Is the repository type visible or hidden
     * If false (hidden): no instances can be created, edited, deleted, showned , used...
     * @var boolean
     */
    private $_visible;


    /**
     * 0 => not ordered, 1 => first position, 2 => second position...
     * A not order type would appear in first position (should never happened)
     * @var integer
     */
    private $_sortorder;

    /**
     * repository_type constructor
     * @global <type> $CFG
     * @param integer $typename
     * @param array $typeoptions
     * @param boolean $visible
     * @param integer $sortorder (don't really need set, it will be during create() call)
     */
    public function __construct($typename = '', $typeoptions = array(), $visible = false, $sortorder = 0) {
        global $CFG;

        //set type attributs
        $this->_typename = $typename;
        $this->_visible = $visible;
        $this->_sortorder = $sortorder;

        //set options attribut
        $this->_options = array();
        //check that the type can be setup
        if (repository_static_function($typename,"has_admin_config")) {
            $options = repository_static_function($typename,'get_admin_option_names');
            //set the type options
            foreach ($options as $config) {
                if (array_key_exists($config,$typeoptions)) {
                    $this->_options[$config] = $typeoptions[$config];
                }
            }
        }
    }

    /**
     * Get the type name (no whitespace)
     * For a human readable name, use get_readablename()
     * @return String the type name
     */
    public function get_typename() {
        return $this->_typename;
    }

    /**
     * Return a human readable and user-friendly type name
     * @return string user-friendly type name
     */
    public function get_readablename() {
        return get_string('repositoryname','repository_'.$this->_typename);
    }

    /**
     * Return general options
     * @return array the general options
     */
    public function get_options() {
        return $this->_options;
    }

    /**
     * Return visibility
     * @return boolean
     */
    public function get_visible() {
        return $this->_visible;
    }

    /**
     * Return order / position of display in the file picker
     * @return integer
     */
    public function get_sortorder() {
        return $this->_sortorder;
    }

    /**
     * Create a repository type (the type name must not already exist)
     * @global object $DB
     */
    public function create() {
        global $DB;

        //check that $type has been set
        $timmedtype = trim($this->_typename);
        if (empty($timmedtype)) {
            throw new repository_exception('emptytype', 'repository');
        }

        //set sortorder as the last position in the list
        if (!isset($this->_sortorder) || $this->_sortorder == 0 ) {
            $sql = "SELECT MAX(sortorder) FROM {repository}";
            $this->_sortorder = 1 + $DB->get_field_sql($sql);
        }

        //only create a new type if it doesn't already exist
        $existingtype = $DB->get_record('repository', array('type'=>$this->_typename));
        if (!$existingtype) {
            //create the type
            $newtype = new stdclass;
            $newtype->type = $this->_typename;
            $newtype->visible = $this->_visible;
            $newtype->sortorder = $this->_sortorder;
            $DB->insert_record('repository', $newtype);

            //save the options in DB
            $this->update_options();

            //if the plugin type has no multiple and no instance config so it wont
            //be possible for the administrator to create a instance
            //in this case we need to create an instance
            if (!repository_static_function($this->_typename,"has_instance_config")
                    && !repository_static_function($this->_typename,"has_multiple_instances")) {
                $instanceoptions = array();
                $instanceoptions['name'] = $this->_typename;
                repository_static_function($this->_typename, 'create', $this->_typename, 0, get_system_context(), $instanceoptions);
            }
        } else {
            throw new repository_exception('existingrepository', 'repository');
        }
    }


    /**
     * Update plugin options into the config_plugin table
     * @param array $options
     * @return boolean
     */
    public function update_options($options = null) {
        if (!empty($options)) {
            $this->_options = $options;
        }

        foreach ($this->_options as $name => $value) {
            set_config($name,$value,$this->_typename);
        }

        return true;
    }

    /**
     * Update visible database field with the value given as parameter
     * or with the visible value of this object
     * This function is private.
     * For public access, have a look to switch_and_update_visibility()
     * @global object $DB
     * @param boolean $visible
     * @return boolean
     */
    private function update_visible($visible = null) {
        global $DB;

        if (!empty($visible)) {
            $this->_visible = $visible;
        }
        else if (!isset($this->_visible)) {
            throw new repository_exception('updateemptyvisible', 'repository');
        }

        return $DB->set_field('repository', 'visible', $this->_visible, array('type'=>$this->_typename));
    }

    /**
     * Update database sortorder field with the value given as parameter
     * or with the sortorder value of this object
     * This function is private.
     * For public access, have a look to move_order()
     * @global object $DB
     * @param integer $sortorder
     * @return boolean
     */
    private function update_sortorder($sortorder = null) {
        global $DB;

        if (!empty($sortorder) && $sortorder!=0) {
            $this->_sortorder = $sortorder;
        }
        //if sortorder is not set, we set it as the ;ast position in the list
        else if (!isset($this->_sortorder) || $this->_sortorder == 0 ) {
            $sql = "SELECT MAX(sortorder) FROM {repository}";
            $this->_sortorder = 1 + $DB->get_field_sql($sql);
        }

        return $DB->set_field('repository', 'sortorder', $this->_sortorder, array('type'=>$this->_typename));
    }

    /**
     * Change order of the type with its adjacent upper or downer type
     * (database fields are updated)
     * Algorithm details:
     * 1. retrieve all types in an array. This array is sorted by sortorder,
     * and the array keys start from 0 to X (incremented by 1)
     * 2. switch sortorder values of this type and its adjacent type
     * @global object $DB
     * @param string $move "up" or "down"
     */
    public function move_order($move) {
        global $DB;

        $types = repository_get_types();    // retrieve all types

    /// retrieve this type into the returned array
        $i = 0;
        while (!isset($indice) && $i<count($types)) {
            if ($types[$i]->get_typename() == $this->_typename) {
                $indice = $i;
            }
            $i++;
        }

    /// retrieve adjacent indice
        switch ($move) {
            case "up":
                $adjacentindice = $indice - 1;
            break;
            case "down":
                $adjacentindice = $indice + 1;
            break;
            default:
            throw new repository_exception('movenotdefined', 'repository');
        }

        //switch sortorder of this type and the adjacent type
        //TODO: we could reset sortorder for all types. This is not as good in performance term, but
        //that prevent from wrong behaviour on a screwed database. As performance are not important in this particular case
        //it worth to change the algo.
        if ($adjacentindice>=0 && !empty($types[$adjacentindice])) {
            $DB->set_field('repository', 'sortorder', $this->_sortorder, array('type'=>$types[$adjacentindice]->get_typename()));
            $this->update_sortorder($types[$adjacentindice]->get_sortorder());
        }
    }

    /**
     * 1. Switch the visibility OFF if it's ON, and ON if it's OFF.
     * 2. Update the type
     * @return <type>
     */
    public function switch_and_update_visibility() {
        $this->_visible = !$this->_visible;
        return $this->update_visible();
    }


    /**
     * Delete a repository_type (general options are removed from config_plugin
     * table, and all instances are deleted)
     * @global object $DB
     * @return boolean
     */
    public function delete() {
        global $DB;

        //delete all instances of this type
        $instances = repository_get_instances(array(),null,false,$this->_typename);
        foreach ($instances as $instance) {
            $instance->delete();
        }

        //delete all general options
        foreach ($this->_options as $name => $value) {
            set_config($name, null, $this->_typename);
        }

        return $DB->delete_records('repository', array('type' => $this->_typename));
    }
}

/**
 * Return a type for a given type name.
 * @global object $DB
 * @param string $typename the type name
 * @return integer
 */
function repository_get_type_by_typename($typename) {
    global $DB;

    if (!$record = $DB->get_record('repository',array('type' => $typename))) {
        return false;
    }

    return new repository_type($typename, (array)get_config($typename), $record->visible, $record->sortorder);
}

/**
 * Return a type for a given type id.
 * @global object $DB
 * @param string $typename the type name
 * @return integer
 */
function repository_get_type_by_id($id) {
    global $DB;

    if (!$record = $DB->get_record('repository',array('id' => $id))) {
        return false;
    }

    return new repository_type($record->type, (array)get_config($record->type), $record->visible, $record->sortorder);
}

/**
 * Return all repository types ordered by sortorder
 * first type in returnedarray[0], second type in returnedarray[1], ...
 * @global object $DB
 * @param boolean $visible can return types by visiblity, return all types if null
 * @return array Repository types
 */
function repository_get_types($visible=null) {
    global $DB;

    $types = array();
    $params = null;
    if (!empty($visible)) {
        $params = array('visible' => $visible);
    }
    if ($records = $DB->get_records('repository',$params,'sortorder')) {
        foreach($records as $type) {
            $types[] = new repository_type($type->type, (array)get_config($type->type), $type->visible, $type->sortorder);
        }
    }

    return $types;
}

/**
 * The base class for all repository plugins
 */

abstract class repository {
    public $id;
    public $context;
    public $options;

    /**
     * 1. Initialize context and options
     * 2. Accept necessary parameters
     *
     * @param integer $repositoryid
     * @param integer $contextid
     * @param array $options
     */
    public function __construct($repositoryid, $contextid = SITEID, $options = array()) {
        $this->id = $repositoryid;
        $this->context = get_context_instance_by_id($contextid);
        $this->options = array();
        if (is_array($options)) {
            $options = array_merge($this->get_option(), $options);
        } else {
            $options = $this->get_option();
        }
        $this->options = array();
        foreach ($options as $n => $v) {
            $this->options[$n] = $v;
        }
        $this->name = $this->get_name();
    }

    /**
     * set options for repository instance
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->options[$name] = $value;
    }

    /**
     * get options for repository instance
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        trigger_error('Undefined property: '.$name, E_USER_NOTICE);
        return null;
    }

    /**
     * test option name
     *
     * @param string name
     */
    public function __isset($name) {
        return isset($this->options[$name]);
    }

    /**
     * Return the name of the repository class
     * @return <type>
     */
    public function __toString() {
        return 'Repository class: '.__CLASS__;
    }

    /**
     * Download a file, this function can be overridden by
     * subclass.
     *
     * @global object $CFG
     * @param string $url the url of file
     * @param string $file save location
     * @return string the location of the file
     * @see curl package
     */
    public function get_file($url, $file = '') {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/temp/download')) {
            mkdir($CFG->dataroot.'/temp/download/', 0777, true);
        }
        if (is_dir($CFG->dataroot.'/temp/download')) {
            $dir = $CFG->dataroot.'/temp/download/';
        }
        if (empty($file)) {
            $file = uniqid('repo').'_'.time().'.tmp';
        }
        if (file_exists($dir.$file)) {
            $file = uniqid('m').$file;
        }
        $fp = fopen($dir.$file, 'w');
        $c = new curl;
        $c->download(array(
                    array('url'=>$url, 'file'=>$fp)
                    ));
        return $dir.$file;
    }

    /**
     * Print a list or return formatted string, can be overridden by subclass
     *
     * @param string $list
     * @param boolean $print false, return html, otherwise, print it directly
     * @return <type>
     */
    public function print_listing($listing = array(), $print=true) {
        if (empty($listing)) {
            $listing = $this->get_listing();
        }
        if (empty($listing)) {
            $str = '';
        } else {
            $count = 0;
            $str = '<table>';
            foreach ($listing as $v) {
                $str .= '<tr id="entry_'.$count.'">';
                $str .= '<td><input type="checkbox" /></td>';
                $str .= '<td>'.$v['name'].'</td>';
                $str .= '<td>'.$v['size'].'</td>';
                $str .= '<td>'.$v['date'].'</td>';
                $str .= '</tr>';
                $count++;
            }
            $str .= '</table>';
        }
        if ($print) {
            echo $str;
            return null;
        } else {
            return $str;
        }
    }

    /**
     * Return the name of this instance, can be overridden.
     * @global <type> $DB
     * @return <type>
     */
    public function get_name() {
        global $DB;
        // We always verify instance id from database,
        // so we always know repository name before init
        // a repository, so we don't enquery repository
        // name from database again here.
        if (isset($this->options['name'])) {
            return $this->options['name'];
        } else {
            if ( $repo = $DB->get_record('repository_instances', array('id'=>$this->id)) ) {
                return $repo->name;
            } else {
                return '';
            }
        }
    }

    /**
     * Provide repository instance information for Ajax
     * @global object $CFG
     * @return object
     */
    final public function ajax_info() {
        global $CFG;
        $repo = new stdclass;
        $repo->id   = $this->id;
        $repo->name = $this->get_name();
        $repo->type = $this->options['type'];
        $repo->icon = $CFG->httpswwwroot.'/repository/'.$repo->type.'/icon.png';
        return $repo;
    }

    /**
     * Create an instance for this plug-in
     * @global object $CFG
     * @global object $DB
     * @param string $type the type of the repository
     * @param integer $userid the user id
     * @param object $context the context
     * @param array $params the options for this instance
     * @return <type>
     */
    final public static function create($type, $userid, $context, $params) {
        global $CFG, $DB;
        $params = (array)$params;
        require_once($CFG->dirroot . '/repository/'. $type . '/repository.class.php');
        $classname = 'repository_' . $type;
        if ($repo = $DB->get_record('repository', array('type'=>$type))) {
            $record = new stdclass;
            $record->name = $params['name'];
            $record->typeid = $repo->id;
            $record->timecreated  = time();
            $record->timemodified = time();
            $record->contextid = $context->id;
            $record->userid    = $userid;
            $id = $DB->insert_record('repository_instances', $record);
            $options = array();
            if (call_user_func($classname . '::has_instance_config')) {
                $configs = call_user_func($classname . '::get_instance_option_names');
                foreach ($configs as $config) {
                    $options[$config] = $params[$config];
                }
            }
            if (!empty($id)) {
                unset($options['name']);
                $instance = repository_get_instance($id);
                $instance->set_option($options);
                return $id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * delete a repository instance
     * @global object $DB
     * @return <type>
     */
    final public function delete() {
        global $DB;
        $DB->delete_records('repository_instances', array('id'=>$this->id));
        return true;
    }

    /**
     * Hide/Show a repository
     * @global object $DB
     * @param string $hide
     * @return <type>
     */
    final public function hide($hide = 'toggle') {
        global $DB;
        if ($entry = $DB->get_record('repository', array('id'=>$this->id))) {
            if ($hide === 'toggle' ) {
                if (!empty($entry->visible)) {
                    $entry->visible = 0;
                } else {
                    $entry->visible = 1;
                }
            } else {
                if (!empty($hide)) {
                    $entry->visible = 0;
                } else {
                    $entry->visible = 1;
                }
            }
            return $DB->update_record('repository', $entry);
        }
        return false;
    }

    /**
     * Cache login details for repositories
     * @global object $DB
     * @param string $username
     * @param string $password
     * @param integer $userid The id of specific user
     * @return integer Id of the record
     */
    public function store_login($username = '', $password = '', $userid = 1) {
        global $DB;

        $repository = new stdclass;
        if (!empty($this->id)) {
            $repository->id = $this->id;
        } else {
            $repository->userid         = $userid;
            $repository->repositorytype = $this->type;
            $repository->contextid      = $this->context->id;
        }
        if ($entry = $DB->get_record('repository', $repository)) {
            $repository->id = $entry->id;
            $repository->username = $username;
            $repository->password = $password;
            return $DB->update_record('repository', $repository);
        } else {
            $repository->username = $username;
            $repository->password = $password;
            return $DB->insert_record('repository', $repository);
        }
    }

    /**
     * Save settings for repository instance
     * $repo->set_option(array('api_key'=>'f2188bde132', 'name'=>'dongsheng'));
     * @global object $DB
     * @param array $options settings
     * @return int Id of the record
     */
    public function set_option($options = array()) {
        global $DB;

        if (!empty($options['name'])) {
            $r = new object();
            $r->id   = $this->id;
            $r->name = $options['name'];
            $DB->update_record('repository_instances', $r);
            unset($options['name']);
        }
        foreach ($options as $name=>$value) {
            if ($id = $DB->get_field('repository_instance_config', 'id', array('name'=>$name, 'instanceid'=>$this->id))) {
                if ($value===null) {
                    return $DB->delete_records('repository_instance_config', array('name'=>$name, 'instanceid'=>$this->id));
                } else {
                    return $DB->set_field('repository_instance_config', 'value', $value, array('id'=>$id));
                }
            } else {
                if ($value===null) {
                    return true;
                }
                $config = new object();
                $config->instanceid = $this->id;
                $config->name   = $name;
                $config->value  = $value;
                return $DB->insert_record('repository_instance_config', $config);
            }
        }
        return true;
    }

    /**
     * Get settings for repository instance
     * @global object $DB
     * @param <type> $config
     * @return array Settings
     */
    public function get_option($config = '') {
        global $DB;
        $entries = $DB->get_records('repository_instance_config', array('instanceid'=>$this->id));
        $ret = array();
        if (empty($entries)) {
            return $ret;
        }
        foreach($entries as $entry) {
            $ret[$entry->name] = $entry->value;
        }
        if (!empty($config)) {
            return $ret[$config];
        } else {
            return $ret;
        }
    }

    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * The format of the returned array must be:
     * array(
     *   'path' => (string) path for the current folder
     *   'dynload' => (bool) use dynamic loading,
     *   'manage' => (string) link to file manager,
     *   'nologin' => (bool) requires login,
     *   'nosearch' => (bool) no search link,
     *   'search_result' => (bool) this list is a searching result,
     *   'upload' => array( // upload manager
     *     'name' => (string) label of the form element,
     *     'id' => (string) id of the form element
     *   ),
     *   'list' => array(
     *     array( // file
     *       'title' => (string) file name,
     *       'date' => (string) file last modification time, usually userdate(...),
     *       'size' => (int) file size,
     *       'thumbnail' => (string) url to thumbnail for the file,
     *       'source' => plugin-dependent unique path to the file (id, url, path, etc.),
     *       'url'=> the accessible url of file
     *     ),
     *     array( // folder - same as file, but no 'source'.
     *       'title' => (string) folder name,
     *       'path' => (string) path to this folder
     *       'date' => (string) folder last modification time, usually userdate(...),
     *       'size' => 0,
     *       'thumbnail' => (string) url to thumbnail for the folder,
     *       'children' => array( // an empty folder needs to have 'children' defined, but empty.
     *         // content (files and folders)
     *       )
     *     ),
     *   )
     * )
     *
     * @param string $parent The parent path, this parameter can
     * a folder name, or a identification of folder
     * @param string $search The text will be searched.
     * @return array the list of files, including meta infomation
     */
    abstract public function get_listing($parent = '/');

    /**
     * Search files in repository
     * When doing global search, $search_text will be used as
     * keyword. 
     *
     * @return mixed, see get_listing()
     */
    public function search($search_text) {
        $list = array();
        $list['list'] = array();
        return false;
    }

    /**
     * Logout from repository instance
     * By default, this function will return a login form
     *
     * @return string
     */
    public function logout(){
        return $this->print_login();
    }

    /**
     * To check whether the user is logged in.
     *
     * @return boolean
     */
    public function check_login(){
        return true;
    }


    /**
     * Show the login screen, if required
     * This is an abstract function, it must be overriden.
     */
    abstract public function print_login();

    /**
     * Show the search screen, if required
     * @return null
     */
    public function print_search() {
        echo '<input type="hidden" name="repo_id" value="'.$this->id.'" />';
        echo '<input type="hidden" name="ctx_id" value="'.$this->context->id.'" />';
        echo '<input type="hidden" name="seekey" value="'.sesskey().'" />';
        echo '<input name="s" value="" />';
        return true;
    }

    /**
     * is it possible to do glboal search?
     * @return boolean
     */
    public function global_search() {
        return false;
    }

    /**
     * Defines operations that happen occasionally on cron
     * @return <type>
     */
    public function cron() {
        return true;
    }

    /**
     * function which is run when a type is created
     * This should be a function from a type, but as I plugin wrtie, only write
     * a class extended from repository class, the init() for type has been placed
     * into the repository.
     */
    public static function type_init(){

    }

    public static function add_unremovable_instances(){

    }

    /**
     * Return true if the plugin type has at least one general option field
     * By default: false
     * @return boolean
     */
    public static function has_admin_config() {
        return false;
    }

    /**
     * Return true if a plugin instance has at least one config field
     * By default: false
     * @return boolean
     */
    public static function has_instance_config() {
        return false;
    }

    /**
     * Return true if the plugin can have multiple instances
     * By default: false
     * @return boolean
     */
    public static function has_multiple_instances() {
        return false;
    }

    /**
     * Return names of the general options
     * By default: no general option name
     * @return array
     */
    public static function get_admin_option_names() {
        return array();
    }

    /**
     * Return names of the instance options
     * By default: no instance option name
     * @return array
     */
    public static function get_instance_option_names() {
        return array();
    }
}

/**
 * exception class for repository api
 */
class repository_exception extends moodle_exception {
}

/**
 * Check context
 * @param int $ctx_id
 * @return boolean
 */
function repository_check_context($ctx_id) {
    global $USER;

    $context = get_context_instance_by_id($ctx_id);
    $level = $context->contextlevel;

    if ($level == CONTEXT_COURSE) {
        if (!has_capability('moodle/course:view', $context)) {
            return false;
        } else {
            return true;
        }
    } else if ($level == CONTEXT_USER) {
        $c = get_context_instance(CONTEXT_USER, $USER->id);
        if ($c->id == $ctx_id) {
            return true;
        } else {
            return false;
        }
    } else if ($level == CONTEXT_SYSTEM) {
        // it is always ok in system level
        return true;
    }
    return false;
}

/**
 * Return all types that you a user can create/edit and which are also visible
 * Note: Mostly used in order to know if at least one editable type has been set
 * @return array types
 */
function repository_get_editable_types() {
    $types= repository_get_types(true);
    $editabletypes = array();
    foreach ($types as $type) {
        if (repository_static_function($type->get_typename(), 'has_multiple_instances')) {
            $editabletypes[]=$type;
        }
    }
    return $editabletypes;
}

/**
 * Return repository instances
 * @global object $DB
 * @global object $CFG
 * @global object $USER
 * @param object $contexts contexts for which the instances are set
 * @param integer $userid
 * @param boolean $onlyvisible if visible == true, return visible instances only,
 *                otherwise, return all instances
 * @param string $type a type name to retrieve
 * @return array repository instances
 */
function repository_get_instances($contexts=array(), $userid = null, $onlyvisible = true, $type=null) {
    global $DB, $CFG, $USER;

    $params = array();
    $sql = 'SELECT i.*, r.type AS repositorytype, r.sortorder, r.visible FROM {repository} r, {repository_instances} i WHERE ';
    $sql .= 'i.typeid = r.id ';

    if (!empty($userid) && is_numeric($userid)) {
        $sql .= ' AND (i.userid = 0 or i.userid = ?)';
        $params[] = $userid;
    }

    foreach ($contexts as $context) {
        if (empty($firstcontext)) {
            $firstcontext = true;
            $sql .= ' AND ((i.contextid = ?)';
        } else {
            $sql .= ' OR (i.contextid = ?)';
        }
        $params[] = $context->id;
    }

    if (!empty($firstcontext)) {
       $sql .=')';
    }

    if ($onlyvisible == true) {
        $sql .= ' AND (r.visible = 1)';
    }

    if (isset($type)) {
        $sql .= ' AND (r.type = ?)';
        $params[] = $type;
    }
    $sql .= ' order by r.sortorder, i.name';

    if (!$repos = $DB->get_records_sql($sql, $params)) {
        $repos = array();
    }

    $ret = array();
    foreach ($repos as $repo) {
        require_once($CFG->dirroot . '/repository/'. $repo->repositorytype.'/repository.class.php');
        $options['visible'] = $repo->visible;
        $options['name']    = $repo->name;
        $options['type']    = $repo->repositorytype;
        $options['typeid']  = $repo->typeid;
        $classname = 'repository_' . $repo->repositorytype;
        $ret[] = new $classname($repo->id, $repo->contextid, $options);
    }
    return $ret;
}

/**
 * Get single repository instance
 * @global object $DB
 * @global object $CFG
 * @param integer $id repository id
 * @return object repository instance
 */
function repository_get_instance($id) {
    global $DB, $CFG;
    $sql = 'SELECT i.*, r.type AS repositorytype, r.visible FROM {repository} r, {repository_instances} i WHERE ';
    $sql .= 'i.typeid = r.id AND ';
    $sql .= 'i.id = '.$id;

    if(!$instance = $DB->get_record_sql($sql)) {
        return false;
    }
    require_once($CFG->dirroot . '/repository/'. $instance->repositorytype
            . '/repository.class.php');
    $classname = 'repository_' . $instance->repositorytype;
    $options['typeid'] = $instance->typeid;
    $options['type']   = $instance->repositorytype;
    $options['name']   = $instance->name;
    return new $classname($instance->id, $instance->contextid, $options);
}

/**
 * call a static function
 * @global <type> $CFG
 * @param <type> $plugin
 * @param <type> $function
 * @param type $nocallablereturnvalue default value if function not found
 *             it's mostly used when you don't want to display an error but
 *             return a boolean
 * @return <type>
 */
function repository_static_function($plugin, $function) {
    global $CFG;

    //check that the plugin exists
    $typedirectory = $CFG->dirroot . '/repository/'. $plugin . '/repository.class.php';
    if (!file_exists($typedirectory)) {
        throw new repository_exception('invalidplugin', 'repository');
    }

    $pname = null;
    if (is_object($plugin) || is_array($plugin)) {
        $plugin = (object)$plugin;
        $pname = $plugin->name;
    } else {
        $pname = $plugin;
    }

    $args = func_get_args();
    if (count($args) <= 2) {
        $args = array();
    }
    else {
        array_shift($args);
        array_shift($args);
    }

    require_once($typedirectory);
    return call_user_func_array(array('repository_' . $plugin, $function), $args);
}

/**
 * Move file from download folder to file pool using FILE API
 * @global object $DB
 * @global object $CFG
 * @global object $USER
 * @param string $path file path in download folder
 * @param string $name file name
 * @param integer $itemid item id to identify a file in filepool
 * @param string $filearea file area
 * @return array information of file in file pool
 */
function repository_move_to_filepool($path, $name, $itemid, $filearea = 'user_draft') {
    global $DB, $CFG, $USER;
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $now = time();
    $entry = new object();
    $entry->filearea  = $filearea;
    $entry->contextid = $context->id;
    $entry->filename  = $name;
    $entry->filepath  = '/'.uniqid().'/';
    $entry->timecreated  = $now;
    $entry->timemodified = $now;
    if(is_numeric($itemid)) {
        $entry->itemid = $itemid;
    } else {
        $entry->itemid = 0;
    }
    $entry->mimetype     = mimeinfo('type', $path);
    $entry->userid       = $USER->id;
    $fs = get_file_storage();
    $browser = get_file_browser();
    if ($file = $fs->create_file_from_pathname($entry, $path)) {
        $delete = unlink($path);
        $ret = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        if(!empty($ret)) {
            return array('url'=>$ret->get_url(),'id'=>$file->get_itemid(), 'file'=>$file->get_filename());
        } else {
            return null;
        }
    } else {
        return null;
    }
}

/**
 * Save file to local filesystem pool
 * @param string $elname name of element
 * @param string $filearea
 * @param string $filepath
 * @param string $filename - use specified filename, if not specified name of uploaded file used
 * @param bool $override override file if exists
 * @return mixed stored_file object or false if error; may throw exception if duplicate found
 */
function repository_store_to_filepool($elname, $filearea='user_draft', $filepath='/', $filename = '', $override = false) {
    global $USER;
    if (!isset($_FILES[$elname])) {
        return false;
    }

    if (!$filename) {
        $filename = $_FILES[$elname]['name'];
    }
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
    $fs = get_file_storage();
    $browser = get_file_browser();

    if ($file = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
        if ($override) {
            $file->delete();
        } else {
            return false;
        }
    }

    $file_record = new object();
    $file_record->contextid = $context->id;
    $file_record->filearea  = $filearea;
    $file_record->itemid    = $itemid;
    $file_record->filepath  = $filepath;
    $file_record->filename  = $filename;
    $file_record->userid    = $USER->id;

    $file = $fs->create_file_from_pathname($file_record, $_FILES[$elname]['tmp_name']);
    $info = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $ret = array('url'=>$info->get_url(),'id'=>$itemid, 'file'=>$file->get_filename());
    return $ret;
}

/**
 * TODO: write comment
 */
final class repository_instance_form extends moodleform {
    protected $instance;
    protected $plugin;

    /**
     * TODO: write comment
     * @global <type> $CFG
     */
    public function definition() {
        global $CFG;
        // type of plugin, string
        $this->plugin = $this->_customdata['plugin'];
        $this->typeid = $this->_customdata['typeid'];
        $this->contextid = $this->_customdata['contextid'];
        $this->instance = (isset($this->_customdata['instance'])
                && is_subclass_of($this->_customdata['instance'], 'repository'))
            ? $this->_customdata['instance'] : null;

        $mform =& $this->_form;
        $strrequired = get_string('required');

        $mform->addElement('hidden', 'edit',  ($this->instance) ? $this->instance->id : 0);
        $mform->addElement('hidden', 'new',   $this->plugin);
        $mform->addElement('hidden', 'plugin', $this->plugin);
        $mform->addElement('hidden', 'typeid', $this->typeid);
        $mform->addElement('hidden', 'contextid', $this->contextid);

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="100" size="30"');
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        // let the plugin add the fields they want (either statically or not)
        if (repository_static_function($this->plugin, 'has_instance_config')) {
            if (!$this->instance) {
                $result = repository_static_function($this->plugin, 'instance_config_form', $mform);
            } else {
                $result = $this->instance->instance_config_form($mform);
            }
        }

        // and set the data if we have some.
        if ($this->instance) {
            $data = array();
            $data['name'] = $this->instance->name;
            foreach ($this->instance->get_instance_option_names() as $config) {
                if (!empty($this->instance->$config)) {
                    $data[$config] = $this->instance->$config;
                } else {
                    $data[$config] = '';
                }
            }
            $this->set_data($data);
        }
        $this->add_action_buttons(true, get_string('save','repository'));
    }

    /**
     * TODO: write comment
     * @global <type> $DB
     * @param <type> $data
     * @return <type>
     */
    public function validation($data) {
        global $DB;

        $errors = array();
        if ($DB->count_records('repository_instances', array('name' => $data['name'], 'typeid' => $data['typeid'])) > 1) {
            $errors = array('name' => get_string('err_uniquename', 'repository'));
        }

        $pluginerrors = array();
        if ($this->instance) {
            //$pluginerrors = $this->instance->admin_config_validation($data);
        } else {
            //$pluginerrors = repository_static_function($this->plugin, 'admin_config_validation', $data);
        }
        if (is_array($pluginerrors)) {
            $errors = array_merge($errors, $pluginerrors);
        }
        return $errors;
    }
}


/**
 * Display a form with the general option fields of a type
 */
final class repository_admin_form extends moodleform {
    protected $instance;
    protected $plugin;

    /**
     * Definition of the moodleform
     * @global object $CFG
     */
    public function definition() {
        global $CFG;
        // type of plugin, string
        $this->plugin = $this->_customdata['plugin'];
        $this->instance = (isset($this->_customdata['instance'])
                && is_a($this->_customdata['instance'], 'repository_type'))
            ? $this->_customdata['instance'] : null;

        $mform =& $this->_form;
        $strrequired = get_string('required');

        $mform->addElement('hidden', 'edit',  ($this->instance) ? $this->instance->get_typename() : 0);
        $mform->addElement('hidden', 'new',   $this->plugin);
        $mform->addElement('hidden', 'plugin', $this->plugin);
        // let the plugin add the fields they want (either statically or not)
        if (repository_static_function($this->plugin, 'has_admin_config')) {
            if (!$this->instance) {
                $result = repository_static_function($this->plugin, 'admin_config_form', $mform);
            } else {
                $classname = 'repository_' . $this->instance->get_typename();
                $result = call_user_func(array($classname,'admin_config_form'),$mform);
            }
        }

        // and set the data if we have some.
        if ($this->instance) {
            $data = array();
            $option_names = call_user_func(array($classname,'get_admin_option_names'));
            $instanceoptions = $this->instance->get_options();
            foreach ($option_names as $config) {
                if (!empty($instanceoptions[$config])) {
                    $data[$config] = $instanceoptions[$config];
                } else {
                    $data[$config] = '';
                }
            }
            $this->set_data($data);
        }
        $this->add_action_buttons(true, get_string('save','repository'));
    }
}


/**
 * Display a repository instance list (with edit/delete/create links)
 * @global object $CFG
 * @global object $USER
 * @param object $context the context for which we display the instance
 * @param string $typename if set, we display only one type of instance
 */
function repository_display_instances_list($context, $typename = null) {
    global $CFG, $USER;

    $output = print_box_start('generalbox','',true);
    //if the context is SYSTEM, so we call it from administration page
    $admin = ($context->id == SYSCONTEXTID) ? true : false;
    if ($admin) {
        $baseurl = $CFG->httpswwwroot . '/admin/repositoryinstance.php?sesskey=' . sesskey();
        $output .= "<div ><h2 style='text-align: center'>" . get_string('siteinstances', 'repository') . " ";
        $output .= "</h2></div>";
    } else {
        $baseurl = $CFG->httpswwwroot . '/repository/manage_instances.php?contextid=' . $context->id . '&amp;sesskey=' . sesskey();
    }

    $namestr = get_string('name');
    $pluginstr = get_string('plugin', 'repository');
    $settingsstr = get_string('settings');
    $deletestr = get_string('delete');
    $updown = get_string('updown', 'repository');
    $plugins = get_list_of_plugins('repository');
    //retrieve list of instances. In administration context we want to display all
    //instances of a type, even if this type is not visible. In course/user context we
    //want to display only visible instances, but for every type types. The repository_get_instances()
    //third parameter displays only visible type.
    $instances = repository_get_instances(array($context),null,!$admin,$typename);
    $instancesnumber = count($instances);
    $alreadyplugins = array();

    $table = new StdClass;
    $table->head = array($namestr, $pluginstr, $deletestr, $settingsstr);
    $table->align = array('left', 'left', 'center','center');
    $table->data = array();

    $updowncount = 1;

    foreach ($instances as $i) {
        $settings = '';
        $settings .= '<a href="' . $baseurl . '&amp;type='.$typename.'&amp;edit=' . $i->id . '">' . $settingsstr . '</a>' . "\n";
        $delete = '<a href="' . $baseurl . '&amp;type='.$typename.'&amp;delete=' .  $i->id . '">' . $deletestr . '</a>' . "\n";

        $type = repository_get_type_by_id($i->typeid);
        $table->data[] = array($i->name, $type->get_readablename(), $delete, $settings);

        //display a grey row if the type is defined as not visible
        if (isset($type) && !$type->get_visible()) {
            $table->rowclass[] = 'dimmed_text';
        } else {
            $table->rowclass[] = '';
        }

        if (!in_array($i->name, $alreadyplugins)) {
            $alreadyplugins[] = $i->name;
        }
    }
    $output .= print_table($table, true);
    $instancehtml = '<div>';
    $addable = 0;

    //if no type is set, we can create all type of instance
    if (!$typename) {
        $instancehtml .= '<h3>';
        $instancehtml .= get_string('createrepository', 'repository');
        $instancehtml .= '</h3><ul>';
        foreach ($plugins as $p) {
            $type = repository_get_type_by_typename($p);
            if (!empty($type) && $type->get_visible()) {
                if (repository_static_function($p, 'has_multiple_instances')) {
                    $instancehtml .= '<li><a href="'.$baseurl.'&amp;new='.$p.'">'.get_string('create', 'repository')
                        .' "'.get_string('repositoryname', 'repository_'.$p).'" '
                        .get_string('instance', 'repository').'</a></li>';
                    $addable++;
                }
            }
        }
        $instancehtml .= '</ul>';

    } else if (repository_static_function($typename, 'has_multiple_instances')) {   //create a unique type of instance
            $addable = 1;
            $instancehtml .= "<form action='".$baseurl."&amp;new=".$typename."' method='post'>
                <p style='text-align:center'><input type='submit' value='".get_string('createinstance', 'repository')."'/></p>
                </form>";     
    }

    if ($addable) {
        $instancehtml .= '</div>';
        $output .= $instancehtml;
    }

    $output .= print_box_end(true);

    //print the list + creation links
    print($output);
}
