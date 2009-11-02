<?php

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



require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/formslib.php');

define('FILE_EXTERNAL', 1);
define('FILE_INTERNAL', 2);


// File picker javascript code

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
     * Return if the instance is visible in a context
     * TODO: check if the context visibility has been overwritten by the plugin creator
     *       (need to create special functions to be overvwritten in repository class)
     * @param objet $contextlevel - context level
     * @return boolean
     */
    public function get_contextvisibility($contextlevel) {

        if ($contextlevel == CONTEXT_COURSE) {
            return $this->_options['enablecourseinstances'];
        }

        if ($contextlevel == CONTEXT_USER) {
            return $this->_options['enableuserinstances'];
        }

        //the context is SITE
        return true;
    }



    /**
     * repository_type constructor
     * @global object $CFG
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
        $options = repository::static_function($typename,'get_type_option_names');
        //check that the type can be setup
        if (!empty($options)) {
            //set the type options
            foreach ($options as $config) {
                if (array_key_exists($config,$typeoptions)) {
                    $this->_options[$config] = $typeoptions[$config];
                }
            }
        }

        //retrieve visibility from option
        if (array_key_exists('enablecourseinstances',$typeoptions)) {
            $this->_options['enablecourseinstances'] = $typeoptions['enablecourseinstances'];
        } else {
             $this->_options['enablecourseinstances'] = 0;
        }

        if (array_key_exists('enableuserinstances',$typeoptions)) {
            $this->_options['enableuserinstances'] = $typeoptions['enableuserinstances'];
        } else {
             $this->_options['enableuserinstances'] = 0;
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
     * @param boolean throw exception?
     * @return mixed return int if create successfully, return false if
     *         any errors
     * @global object $DB
     */
    public function create($silent = false) {
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
            $plugin_id = $DB->insert_record('repository', $newtype);
            //save the options in DB
            $this->update_options();

            //if the plugin type has no multiple instance (e.g. has no instance option name) so it wont
            //be possible for the administrator to create a instance
            //in this case we need to create an instance
            $instanceoptionnames = repository::static_function($this->_typename, 'get_instance_option_names');
            if (empty($instanceoptionnames)) {
                $instanceoptions = array();
                $instanceoptions['name'] = $this->_typename;
                repository::static_function($this->_typename, 'create', $this->_typename, 0, get_system_context(), $instanceoptions);
            }
            //run plugin_init function
            if (!repository::static_function($this->_typename, 'plugin_init')) {
                if (!$silent) {
                    throw new repository_exception('cannotinitplugin', 'repository');
                }
            }

            if(!empty($plugin_id)) {
                // return plugin_id if create successfully
                return $plugin_id;
            } else {
                return false;
            }

        } else {
            if (!$silent) {
                throw new repository_exception('existingrepository', 'repository');
            }
            // If plugin existed, return false, tell caller no new plugins were created.
            return false;
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

        $types = repository::get_types();    // retrieve all types

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
     * @return boolean
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
        $instances = repository::get_instances(array(), null, false, $this->_typename);
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
 * This is the base class of the repository class
 *
 * To use repository plugin, see:
 * http://docs.moodle.org/en/Development:Repository_How_to_Create_Plugin
 *
 * class repository is an abstract class, some functions must be implemented in subclass.
 *
 * See an example: repository/boxnet/repository.class.php
 *
 * A few notes:
 *   // for ajax file picker, this will print a json string to tell file picker
 *   // how to build a login form
 *   $repo->print_login();
 *   // for ajax file picker, this will return a files list.
 *   $repo->get_listing();
 *   // this function will be used for non-javascript version.
 *   $repo->print_listing();
 *   // print a search box
 *   $repo->print_search();
 *
 * @package repository
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
abstract class repository {
    // $disabled can be set to true to disable a plugin by force
    // example: self::$disabled = true
    public $disabled = false;
    public $id;
    public $context;
    public $options;
    public $readonly;
    public $returntypes;

    /**
     * Return a type for a given type name.
     * @global object $DB
     * @param string $typename the type name
     * @return integer
     */
    public static function get_type_by_typename($typename) {
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
    public static function get_type_by_id($id) {
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
    public static function get_types($visible=null) {
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
     * Check context
     * @param int $ctx_id
     * @return boolean
     */
    public static function check_context($ctx_id) {
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
     * Note: Mostly used in order to know if at least one editable type can be set
     * @param object $context the context for which we want the editable types
     * @return array types
     */
    public static function get_editable_types($context = null) {

        if (empty($context)) {
            $context = get_system_context();
        }

        $types= repository::get_types(true);
        $editabletypes = array();
        foreach ($types as $type) {
            $instanceoptionnames = repository::static_function($type->get_typename(), 'get_instance_option_names');
            if (!empty($instanceoptionnames)) {
                if ($type->get_contextvisibility($context->contextlevel)) {
                    $editabletypes[]=$type;
                }
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
    public static function get_instances($contexts=array(), $userid = null, $onlyvisible = true, $type=null, $accepted_types = '*', $returntypes = 3) {
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
        $ft = new file_type_to_ext();
        foreach ($repos as $repo) {
            require_once($CFG->dirroot . '/repository/'. $repo->repositorytype.'/repository.class.php');
            $options['visible'] = $repo->visible;
            $options['name']    = $repo->name;
            $options['type']    = $repo->repositorytype;
            $options['typeid']  = $repo->typeid;
            // tell instance what file types will be accepted by file picker
            $options['accepted_types'] = $ft->get_file_ext($accepted_types);
            $classname = 'repository_' . $repo->repositorytype;//
            $is_supported = true;

            $repository = new $classname($repo->id, $repo->contextid, $options, $repo->readonly);
            $context = get_context_instance_by_id($repo->contextid);
            if (empty($repository->super_called)) {
                debugging('parent::__construct must be called by '.$repo->repositorytype.' plugin.');
            } else {
                if ($accepted_types !== '*' and $repository->supported_filetypes() !== '*') {
                    $accepted_types = $ft->get_file_ext($accepted_types);
                    $supported_filetypes = $ft->get_file_ext($repository->supported_filetypes());
                    $is_supported = false;
                    foreach  ($supported_filetypes as $type) {
                        if (in_array($type, $accepted_types)) {
                            $is_supported = true;
                        }
                    }
                }
                if ($returntypes !== 3 and $repository->supported_returntypes() !== 3) {
                    $type = $repository->supported_returntypes();
                    if ($type & $returntypes) {
                        //
                    } else {
                        $is_supported = false;
                    }
                }
                if (!$onlyvisible || ($repository->is_visible() && !$repository->disabled)) {
                    // super_called will make sure the parent construct function is called
                    // by repository construct function
                    $capability = has_capability('repository/'.$repo->repositorytype.':view', get_system_context());
                    if ($is_supported && $capability) {
                        $ret[] = $repository;
                    }
                }
            }
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
    public static function get_instance($id) {
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
        $obj = new $classname($instance->id, $instance->contextid, $options, $instance->readonly);
        if (empty($obj->super_called)) {
            debugging('parent::__construct must be called by '.$classname.' plugin.');
        }
        return $obj;
    }

    /**
     * call a static function
     * @global object $CFG
     * @param string $plugin
     * @param string $function
     * @param type $nocallablereturnvalue default value if function not found
     *             it's mostly used when you don't want to display an error but
     *             return a boolean
     * @return mixed
     */
    public static function static_function($plugin, $function) {
        global $CFG;

        //check that the plugin exists
        $typedirectory = $CFG->dirroot . '/repository/'. $plugin . '/repository.class.php';
        if (!file_exists($typedirectory)) {
            //throw new repository_exception('invalidplugin', 'repository');
            return false;
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
    public static function move_to_filepool($path, $name, $itemid, $filepath = '/', $filearea = 'user_draft') {
        global $DB, $CFG, $USER, $OUTPUT;
        if ($filepath !== '/') {
            $filepath = trim($filepath, '/');
            $filepath = '/'.$filepath.'/';
        }
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $now = time();
        $entry = new object();
        $entry->filearea  = $filearea;
        $entry->contextid = $context->id;
        $entry->filename  = $name;
        $entry->filepath  = $filepath;
        $entry->timecreated  = $now;
        $entry->timemodified = $now;
        $entry->userid       = $USER->id;
        $entry->mimetype     = mimeinfo('type', $path);
        if(is_numeric($itemid)) {
            $entry->itemid = $itemid;
        } else {
            $entry->itemid = 0;
        }
        $fs = get_file_storage();
        $browser = get_file_browser();
        if ($existingfile = $fs->get_file($context->id, $filearea, $itemid, $path, $name)) {
            $existingfile->delete();
        }
        if ($file = $fs->create_file_from_pathname($entry, $path)) {
            if (empty($CFG->repository_no_delete)) {
                $delete = unlink($path);
                unset($CFG->repository_no_delete);
            }
            $ret = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            if(!empty($ret)) {
                return array('url'=>$ret->get_url(),
                    'id'=>$file->get_itemid(),
                    'file'=>$file->get_filename(),
                    'icon' => $OUTPUT->old_icon_url(file_extension_icon($path, 32))
                );
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
    public static function store_to_filepool($elname, $filearea='user_draft', $filepath='/', $itemid='', $filename = '', $override = false) {
        global $USER;

        if ($filepath !== '/') {
            $filepath = trim($filepath, '/');
            $filepath = '/'.$filepath.'/';
        }

        if (!isset($_FILES[$elname])) {
            return false;
        }

        if (!$filename) {
            $filename = $_FILES[$elname]['name'];
        }
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        if (empty($itemid)) {
            $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
        }
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
     * Return the user files tree in a format to be returned by the function get_listing
     * @global object $CFG
     * @param string $search
     * @return array
     */
    public static function get_user_file_tree($search = ""){
        global $CFG;
        $ret = array();
        $ret['nologin'] = true;
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
        $browser = get_file_browser();
        $itemid = null;
        $filename = null;
        $filearea = null;
        $path = '/';
        $ret['dynload'] = false;

        if ($fileinfo = $browser->get_file_info(get_system_context(), $filearea, $itemid, $path, $filename)) {

            $ret['path'] = array();
            $params = $fileinfo->get_params();
            $filearea = $params['filearea'];
            $ret['path'][] = repository::encode_path($filearea, $path, $fileinfo->get_visible_name());
            if ($fileinfo->is_directory()) {
                $level = $fileinfo->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $ret['path'][] = repository::encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }
            }
            $filecount = repository::build_tree($fileinfo, $search, $ret['dynload'], $ret['list']);
            $ret['path'] = array_reverse($ret['path']);
        }

        if (empty($ret['list'])) {
            //exit(mnet_server_fault(9016, get_string('emptyfilelist', 'repository_local')));
            throw new Exception('emptyfilelist');
        } else {
            return $ret;
        }

    }

    /**
     *
     * @param string $filearea
     * @param string $path
     * @param string $visiblename
     * @return array
     */
    public static function encode_path($filearea, $path, $visiblename) {
        return array('path'=>serialize(array($filearea, $path)), 'name'=>$visiblename);
    }

    /**
     * Builds a tree of files This function is
     * then called recursively.
     *
     * @param $fileinfo an object returned by file_browser::get_file_info()
     * @param $search searched string
     * @param $dynamicmode bool no recursive call is done when in dynamic mode
     * @param $list - the array containing the files under the passed $fileinfo
     * @returns int the number of files found
     *
     * todo: take $search into account, and respect a threshold for dynamic loading
     */
    public static function build_tree($fileinfo, $search, $dynamicmode, &$list) {
        global $CFG, $OUTPUT;

        $filecount = 0;
        $children = $fileinfo->get_children();

        foreach ($children as $child) {
            $filename = $child->get_visible_name();
            $filesize = $child->get_filesize();
            $filesize = $filesize ? display_size($filesize) : '';
            $filedate = $child->get_timemodified();
            $filedate = $filedate ? userdate($filedate) : '';
            $filetype = $child->get_mimetype();

            if ($child->is_directory()) {
                $path = array();
                $level = $child->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $path[] = repository::encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }

                $tmp = array(
                    'title' => $child->get_visible_name(),
                    'size' => 0,
                    'date' => $filedate,
                    'path' => array_reverse($path),
                    'thumbnail' => $OUTPUT->old_icon_url('f/folder-32')
                );

                //if ($dynamicmode && $child->is_writable()) {
                //    $tmp['children'] = array();
                //} else {
                    // if folder name matches search, we send back all files contained.
                $_search = $search;
                if ($search && stristr($tmp['title'], $search) !== false) {
                    $_search = false;
                }
                $tmp['children'] = array();
                $_filecount = repository::build_tree($child, $_search, $dynamicmode, $tmp['children']);
                if ($search && $_filecount) {
                    $tmp['expanded'] = 1;
                }

                //}

                //Uncomment this following line if you wanna display all directory ()even empty
                if (!$search || $_filecount || (stristr($tmp['title'], $search) !== false)) {
                    $filecount += $_filecount;
                    $list[] = $tmp;
                }

            } else { // not a directory
                // skip the file, if we're in search mode and it's not a match
                if ($search && (stristr($filename, $search) === false)) {
                    continue;
                }
                $params = $child->get_params();
                $source = serialize(array($params['contextid'], $params['filearea'], $params['itemid'], $params['filepath'], $params['filename']));
                $list[] = array(
                    'title' => $filename,
                    'size' => $filesize,
                    'date' => $filedate,
                    //'source' => $child->get_url(),
                    'source' => base64_encode($source),
                    'thumbnail'=>$OUTPUT->old_icon_url(file_extension_icon($filename, 32)),
                );
                $filecount++;
            }
        }

        return $filecount;
    }


    /**
     * Display a repository instance list (with edit/delete/create links)
     * @global object $CFG
     * @global object $USER
     * @param object $context the context for which we display the instance
     * @param string $typename if set, we display only one type of instance
     */
    public static function display_instances_list($context, $typename = null) {
        global $CFG, $USER, $OUTPUT;

        $output = $OUTPUT->box_start('generalbox');
        //if the context is SYSTEM, so we call it from administration page
        $admin = ($context->id == SYSCONTEXTID) ? true : false;
        if ($admin) {
            $baseurl = "$CFG->httpswwwroot/$CFG->admin/repositoryinstance.php?sesskey=" . sesskey();
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
        //retrieve list of instances. In administration context we want to display all
        //instances of a type, even if this type is not visible. In course/user context we
        //want to display only visible instances, but for every type types. The repository::get_instances()
        //third parameter displays only visible type.
        $instances = repository::get_instances(array($context), null, !$admin, $typename);
        $instancesnumber = count($instances);
        $alreadyplugins = array();

        $table = new html_table();
        $table->head = array($namestr, $pluginstr, $deletestr, $settingsstr);
        $table->align = array('left', 'left', 'center','center');
        $table->data = array();

        $updowncount = 1;

        foreach ($instances as $i) {
            $settings = '';
            $delete = '';
            $settings .= '<a href="' . $baseurl . '&amp;type='.$typename.'&amp;edit=' . $i->id . '">' . $settingsstr . '</a>' . "\n";
            if (!$i->readonly) {
                $delete .= '<a href="' . $baseurl . '&amp;type='.$typename.'&amp;delete=' .  $i->id . '">' . $deletestr . '</a>' . "\n";
            }

            $type = repository::get_type_by_id($i->options['typeid']);
            $table->data[] = array($i->name, $type->get_readablename(), $delete, $settings);

            //display a grey row if the type is defined as not visible
            if (isset($type) && !$type->get_visible()) {
                $table->rowclasses[] = 'dimmed_text';
            } else {
                $table->rowclasses[] = '';
            }

            if (!in_array($i->name, $alreadyplugins)) {
                $alreadyplugins[] = $i->name;
            }
        }
        $output .= $OUTPUT->table($table);
        $instancehtml = '<div>';
        $addable = 0;

        //if no type is set, we can create all type of instance
        if (!$typename) {
            $instancehtml .= '<h3>';
            $instancehtml .= get_string('createrepository', 'repository');
            $instancehtml .= '</h3><ul>';
            $types = repository::get_editable_types($context);
            foreach ($types as $type) {
                if (!empty($type) && $type->get_visible()) {
                    $instanceoptionnames = repository::static_function($type->get_typename(), 'get_instance_option_names');
                    if (!empty($instanceoptionnames)) {
                        $instancehtml .= '<li><a href="'.$baseurl.'&amp;new='.$type->get_typename().'">'.get_string('createxxinstance', 'repository', get_string('repositoryname', 'repository_'.$type->get_typename()))
                            .'</a></li>';
                        $addable++;
                    }
                }
            }
            $instancehtml .= '</ul>';

        } else {
            $instanceoptionnames = repository::static_function($typename, 'get_instance_option_names');
            if (!empty($instanceoptionnames)) {   //create a unique type of instance
                $addable = 1;
                $instancehtml .= "<form action='".$baseurl."&amp;new=".$typename."' method='post'>
                    <p style='text-align:center'><input type='submit' value='".get_string('createinstance', 'repository')."'/></p>
                    </form>";
            }
        }

        if ($addable) {
            $instancehtml .= '</div>';
            $output .= $instancehtml;
        }

        $output .= $OUTPUT->box_end();

        //print the list + creation links
        print($output);
    }
    /**
     * 1. Initialize context and options
     * 2. Accept necessary parameters
     *
     * @param integer $repositoryid
     * @param integer $contextid
     * @param array $options
     */
    public function __construct($repositoryid, $contextid = SITEID, $options = array(), $readonly = 0) {
        $this->id = $repositoryid;
        $this->context = get_context_instance_by_id($contextid);
        $this->readonly = $readonly;
        $this->options = array();
        if (is_array($options)) {
            $options = array_merge($this->get_option(), $options);
        } else {
            $options = $this->get_option();
        }
        foreach ($options as $n => $v) {
            $this->options[$n] = $v;
        }
        $this->name = $this->get_name();
        $this->returntypes = $this->supported_returntypes();
        $this->super_called = true;
    }

    /**
     * Decide where to save the file, can be
     * reused by sub class
     * @param string filename
     */
    public function prepare_file($filename) {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/temp/download')) {
            mkdir($CFG->dataroot.'/temp/download/', 0777, true);
        }
        if (is_dir($CFG->dataroot.'/temp/download')) {
            $dir = $CFG->dataroot.'/temp/download/';
        }
        if (empty($filename)) {
            $filename = uniqid('repo').'_'.time().'.tmp';
        }
        if (file_exists($dir.$filename)) {
            $filename = uniqid('m').$filename;
        }
        return $dir.$filename;
    }

    /**
     * Download a file, this function can be overridden by
     * subclass.
     *
     * @global object $CFG
     * @param string $url the url of file
     * @param string $filename save location
     * @return string the location of the file
     * @see curl package
     */
    public function get_file($url, $filename = '') {
        global $CFG;
        $path = $this->prepare_file($filename);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return $path;
    }

    /**
     * Return is the instance is visible
     * (is the type visible ? is the context enable ?)
     * @return boolean
     */
    public function is_visible() {
        $type = repository::get_type_by_id($this->options['typeid']);
        $instanceoptions = repository::static_function($type->get_typename(), 'get_instance_option_names');

        if ($type->get_visible()) {
            //if the instance is unique so it's visible, otherwise check if the instance has a enabled context
            if (empty($instanceoptions) || $type->get_contextvisibility($this->context->contextlevel)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the name of this instance, can be overridden.
     * @global object $DB
     * @return string
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
     * what kind of files will be in this repository?
     * @return array return '*' means this repository support any files, otherwise
     *               return mimetypes of files, it can be an array
     */
    public function supported_filetypes() {
        // return array('text/plain', 'image/gif');
        return '*';
    }

    /**
     * does it return a file url or a item_id
     * @return string
     */
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Provide repository instance information for Ajax
     * @global object $CFG
     * @return object
     */
    final public function get_meta() {
        global $CFG;
        $ft = new file_type_to_ext;
        $meta = new stdclass;
        $meta->id   = $this->id;
        $meta->name = $this->get_name();
        $meta->type = $this->options['type'];
        $meta->icon = $CFG->httpswwwroot.'/repository/'.$meta->type.'/icon.png';
        $meta->supported_types = $ft->get_file_ext($this->supported_filetypes());
        $meta->accepted_types = $this->options['accepted_types'];
        return $meta;
    }

    /**
     * Create an instance for this plug-in
     * @global object $CFG
     * @global object $DB
     * @param string $type the type of the repository
     * @param integer $userid the user id
     * @param object $context the context
     * @param array $params the options for this instance
     * @return mixed
     */
    final public static function create($type, $userid, $context, $params, $readonly=0) {
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
            $record->readonly = $readonly;
            $record->userid    = $userid;
            $id = $DB->insert_record('repository_instances', $record);
            $options = array();
            $configs = call_user_func($classname . '::get_instance_option_names');
            if (!empty($configs)) {
                foreach ($configs as $config) {
                    $options[$config] = $params[$config];
                }
            }

            if (!empty($id)) {
                unset($options['name']);
                $instance = repository::get_instance($id);
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
     * @return mixed
     */
    final public function delete() {
        global $DB;
        $DB->delete_records('repository_instances', array('id'=>$this->id));
        $DB->delete_records('repository_instance_config', array('instanceid'=>$this->id));
        return true;
    }

    /**
     * Hide/Show a repository
     * @global object $DB
     * @param string $hide
     * @return boolean
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
        $result = true;
        foreach ($options as $name=>$value) {
            if ($id = $DB->get_field('repository_instance_config', 'id', array('name'=>$name, 'instanceid'=>$this->id))) {
                $result = $result && $DB->set_field('repository_instance_config', 'value', $value, array('id'=>$id));
            } else {
                $config = new object();
                $config->instanceid = $this->id;
                $config->name   = $name;
                $config->value  = $value;
                $result = $result && $DB->insert_record('repository_instance_config', $config);
            }
        }
        return $result;
    }

    /**
     * Get settings for repository instance
     * @global object $DB
     * @param string $config
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

    public function filter(&$value) {
        $pass = false;
        $accepted_types = optional_param('accepted_types', '', PARAM_RAW);
        $ft = new file_type_to_ext;
        $ext = $ft->get_file_ext($this->supported_filetypes());
        if (isset($value['children'])) {
            $pass = true;
            if (!empty($value['children'])) {
                $value['children'] = array_filter($value['children'], array($this, 'filter'));
            }
        } else {
            if ($accepted_types == '*' or empty($accepted_types)
                or (is_array($accepted_types) and in_array('*', $accepted_types))) {
                $pass = true;
            } elseif (is_array($accepted_types)) {
                foreach ($accepted_types as $type) {
                    if (preg_match('#'.$type.'$#', $value['title'])) {
                        $pass = true;
                    }
                }
            }
        }
        return $pass;
    }

    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * See details on http://docs.moodle.org/en/Development:Repository_plugins
     *
     * @param string $parent The parent path, this parameter can
     * a folder name, or a identification of folder
     * @return array the list of files, including meta infomation
     */
    public function get_listing($path = '', $page = '') {
    }

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
     */
    public function print_login(){
        return $this->get_listing();
    }

    /**
     * Show the search screen, if required
     * @return null
     */
    public function print_search() {
        $str = '';
        $str .= '<input type="hidden" name="repo_id" value="'.$this->id.'" />';
        $str .= '<input type="hidden" name="ctx_id" value="'.$this->context->id.'" />';
        $str .= '<input type="hidden" name="seekey" value="'.sesskey().'" />';
        $str .= '<label>'.get_string('keyword', 'repository').': </label><br/><input name="s" value="" /><br/>';
        return $str;
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
     * @return boolean
     */
    public function cron() {
        return true;
    }

    /**
     * function which is run when the type is created (moodle administrator add the plugin)
     * @return boolean success or fail?
     */
    public static function plugin_init() {
        return true;
    }

    /**
     * Edit/Create Admin Settings Moodle form
     * @param object $ Moodle form (passed by reference)
     */
     public function type_config_form(&$mform) {
    }

      /**
     * Edit/Create Instance Settings Moodle form
     * @param object $ Moodle form (passed by reference)
     */
     public function instance_config_form(&$mform) {
    }

    /**
     * Return names of the general options
     * By default: no general option name
     * @return array
     */
    public static function get_type_option_names() {
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

    /**
     * Override it if you need to implement need mnet function
     * @return array
     */
     public static function mnet_publishes() {
        return array();
    }

}

/**
 * exception class for repository api
 */
class repository_exception extends moodle_exception {
}



/**
 * TODO: write comment
 */
final class repository_instance_form extends moodleform {
    protected $instance;
    protected $plugin;

    /**
     * TODO: write comment
     * @global object $CFG
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
        $mform->setType('edit', PARAM_INT);
        $mform->addElement('hidden', 'new',   $this->plugin);
        $mform->setType('new', PARAM_FORMAT);
        $mform->addElement('hidden', 'plugin', $this->plugin);
        $mform->setType('plugin', PARAM_SAFEDIR);
        $mform->addElement('hidden', 'typeid', $this->typeid);
        $mform->setType('typeid', PARAM_INT);
        $mform->addElement('hidden', 'contextid', $this->contextid);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="100" size="30"');
        $mform->addRule('name', $strrequired, 'required', null, 'client');


        //add fields
        if (!$this->instance) {
            $result = repository::static_function($this->plugin, 'instance_config_form', $mform);
        }
        else {
            $data = array();
            $data['name'] = $this->instance->name;
            if (!$this->instance->readonly) {
                $result = $this->instance->instance_config_form($mform);
                // and set the data if we have some.
                foreach ($this->instance->get_instance_option_names() as $config) {
                    if (!empty($this->instance->options[$config])) {
                        $data[$config] = $this->instance->options[$config];
                     } else {
                        $data[$config] = '';
                     }
                }
            }
            $this->set_data($data);
        }

        $this->add_action_buttons(true, get_string('save','repository'));
    }

    /**
     * TODO: write comment
     * @global object $DB
     * @param mixed $data
     * @return mixed
     */
    public function validation($data) {
        global $DB;

        $errors = array();
        $sql = "SELECT count('x') FROM {repository_instances} i, {repository} r WHERE r.type=:plugin AND r.id=i.typeid AND i.name=:name";
        if ($DB->count_records_sql($sql, array('name' => $data['name'], 'plugin' => $data['plugin'])) > 1) {
            $errors = array('name' => get_string('err_uniquename', 'repository'));
        }

        return $errors;
    }
}


/**
 * Display a form with the general option fields of a type
 */
final class repository_type_form extends moodleform {
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
        $mform->setType('edit', PARAM_INT);
        $mform->addElement('hidden', 'new',   $this->plugin);
        $mform->setType('new', PARAM_FORMAT);
        $mform->addElement('hidden', 'plugin', $this->plugin);
        $mform->setType('plugin', PARAM_SAFEDIR);

        // let the plugin add its specific fields
        if (!$this->instance) {
            $result = repository::static_function($this->plugin, 'type_config_form', $mform);
        } else {
            $classname = 'repository_' . $this->instance->get_typename();
            $result = call_user_func(array($classname, 'type_config_form'), $mform);
        }

        //add "enable course/user instances" checkboxes if multiple instances are allowed
        $instanceoptionnames = repository::static_function($this->plugin, 'get_instance_option_names');
        if (!empty($instanceoptionnames)){
            $mform->addElement('checkbox', 'enablecourseinstances', get_string('enablecourseinstances', 'repository'));
            $mform->addElement('checkbox', 'enableuserinstances', get_string('enableuserinstances', 'repository'));
        }

        // set the data if we have some.
        if ($this->instance) {
            $data = array();
            $option_names = call_user_func(array($classname,'get_type_option_names'));
            if (!empty($instanceoptionnames)){
                $option_names[] = 'enablecourseinstances';
                $option_names[] = 'enableuserinstances';
            }

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

function repository_setup_default_plugins() {
    global $OUTPUT;
    //if the plugin type has no multiple instance (e.g. has no instance option name)
    //repository_type::create will create an instance automatically
    $local_plugin = new repository_type('local', array(), true);
    $local_plugin_id = $local_plugin->create(true);
    $upload_plugin = new repository_type('upload', array(), true);
    $upload_plugin_id = $upload_plugin->create(true);
    if (is_int($local_plugin_id) or is_int($upload_plugin_id)) {
        echo $OUTPUT->box(get_string('setupdefaultplugins', 'repository'));
    }
    return true;
}

/**
 * Loads
 * @return void
 */
function repository_head_setup() {
    global $PAGE;

    $PAGE->requires->yui_lib('yahoo')->in_head();
    $PAGE->requires->yui_lib('dom')->in_head();
    $PAGE->requires->yui_lib('element')->in_head();
    $PAGE->requires->yui_lib('event')->in_head();
    $PAGE->requires->yui_lib('json')->in_head();
    $PAGE->requires->yui_lib('treeview')->in_head();
    $PAGE->requires->yui_lib('dragdrop')->in_head();
    $PAGE->requires->yui_lib('container')->in_head();
    $PAGE->requires->yui_lib('resize')->in_head();
    $PAGE->requires->yui_lib('layout')->in_head();
    $PAGE->requires->yui_lib('connection')->in_head();
    $PAGE->requires->yui_lib('button')->in_head();
    $PAGE->requires->yui_lib('selector')->in_head();

    //TODO: remove the ->in_head() once we refactor the inline script tags in repo code
    $PAGE->requires->js('repository/repository.src.js')->in_head();

    //TODO: remove following after we moe the content of file
    //      proper place (==themes)
    $PAGE->requires->css('repository/repository.css');
}

/**
 * Return javascript to create file picker to browse repositories
 * @global object $CFG
 * @global object $USER
 * @param object $context the context
 * @param string $id unique id for every file picker
 * @param string $accepted_filetypes
 * @param string $returntypes the return value of file picker
 * @return array
 */
function repository_get_client($context, $id = '',  $accepted_filetypes = '*', $returntypes = 3) {
    global $CFG, $USER, $PAGE, $OUTPUT;

    $ft = new file_type_to_ext();
    $image_file_ext = json_encode($ft->get_file_ext(array('image')));
    $video_file_ext = json_encode($ft->get_file_ext(array('video')));
    $accepted_file_ext = json_encode($ft->get_file_ext($accepted_filetypes));

    $js  = '';
    if (!isset($CFG->filepickerjsloaded)) {
        $lang = array();
        $lang['title'] = get_string('title', 'repository');
        $lang['preview'] = get_string('preview', 'repository');
        $lang['add']     = get_string('add', 'repository');
        $lang['back']      = get_string('back', 'repository');
        $lang['cancel']    = get_string('cancel');
        $lang['close']     = get_string('close', 'repository');
        $lang['ccache']    = get_string('cleancache', 'repository');
        $lang['copying']   = get_string('copying', 'repository');
        $lang['downbtn']   = get_string('getfile', 'repository');
        $lang['download']  = get_string('downloadsucc', 'repository');
        $lang['date']      = get_string('date', 'repository').': ';
        $lang['error']     = get_string('error', 'repository');
        $lang['emptylist'] = get_string('emptylist', 'repository');
        $lang['filenotnull'] = get_string('filenotnull', 'repository');
        $lang['federatedsearch'] = get_string('federatedsearch', 'repository');
        $lang['help']      = get_string('help');
        $lang['refresh']   = get_string('refresh', 'repository');
        $lang['invalidjson'] = get_string('invalidjson', 'repository');
        $lang['listview']  = get_string('listview', 'repository');
        $lang['login']     = get_string('login', 'repository');
        $lang['logout']    = get_string('logout', 'repository');
        $lang['loading']   = get_string('loading', 'repository');
        $lang['thumbview'] = get_string('thumbview', 'repository');
        $lang['title']     = get_string('title', 'repository');
        $lang['noresult']  = get_string('noresult', 'repository');
        $lang['mgr']       = get_string('manageurl', 'repository');
        $lang['noenter']   = get_string('noenter', 'repository');
        $lang['save']      = get_string('save', 'repository');
        $lang['saveas']    = get_string('saveas', 'repository').': ';
        $lang['saved']     = get_string('saved', 'repository');
        $lang['saving']    = get_string('saving', 'repository');
        $lang['size']      = get_string('size', 'repository').': ';
        $lang['sync']      = get_string('sync', 'repository');
        $lang['search']    = get_string('search', 'repository');
        $lang['searching'] = get_string('searching', 'repository');
        $lang['submit']    = get_string('submit', 'repository');
        $lang['preview']   = get_string('preview', 'repository');
        $lang['popup']     = get_string('popup', 'repository');
        $lang['upload']    = get_string('upload', 'repository').'...';
        $lang['uploading'] = get_string('uploading', 'repository');
        $lang['xhtml']     = get_string('xhtmlerror', 'repository');
        $lang = json_encode($lang);

        $options = array();
        $context = get_system_context();
        $options['contextid'] = $context->id;
        $options['icons']['loading'] = $OUTPUT->old_icon_url('i/loading');
        $options['icons']['progressbar'] = $OUTPUT->old_icon_url('i/progressbar');
        $options['icons']['search'] = $OUTPUT->old_icon_url('a/search');
        $options['icons']['refresh'] = $OUTPUT->old_icon_url('a/refresh');
        $options['icons']['setting'] = $OUTPUT->old_icon_url('a/setting');
        $options['icons']['logout'] = $OUTPUT->old_icon_url('a/logout');
        $options['icons']['help'] = $OUTPUT->old_icon_url('a/help');
        $options = json_encode($options);
        // fp_config includes filepicker options

        $accepted_file_ext = json_encode($ft->get_file_ext($accepted_filetypes));
        $js .= <<<EOD
<script type="text/javascript">
var fp_lang = $lang;
var fp_config = $options;
MOODLE.repository.extensions.image = $image_file_ext;
MOODLE.repository.extensions.media = $video_file_ext;
</script>
EOD;

        $CFG->filepickerjsloaded = true;
    } else {
        // if yui and repository javascript libs are loaded
        $js = '';
    }

    // print instances listing
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    if (is_array($accepted_filetypes) && in_array('*', $accepted_filetypes)) {
        $accepted_filetypes = '*';
    }
    $repos = repository::get_instances(array($user_context, $context, get_system_context()), null, true, null, $accepted_filetypes, $returntypes);

    // print repository instances listing
    $js .= <<<EOD
<script type="text/javascript">
MOODLE.repository.listing['$id'] = [];
EOD;
    foreach ($repos as $repo) {
        $meta = $repo->get_meta();
        $js .= "\r\n";
        $js .= 'MOODLE.repository.listing[\''.$id.'\']['.$meta->id.']='.json_encode($meta).';';
        $js .= "\n";
    }
    $js .= "\r\n";
    $js .= "</script>";

    return $js;
}
