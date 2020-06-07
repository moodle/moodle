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
 * This file contains classes used to manage the repository plugins in Moodle
 *
 * @since Moodle 2.0
 * @package   core_repository
 * @copyright 2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/formslib.php');

define('FILE_EXTERNAL',  1);
define('FILE_INTERNAL',  2);
define('FILE_REFERENCE', 4);
define('FILE_CONTROLLED_LINK', 8);

define('RENAME_SUFFIX', '_2');

/**
 * This class is used to manage repository plugins
 *
 * A repository_type is a repository plug-in. It can be Box.net, Flick-r, ...
 * A repository type can be edited, sorted and hidden. It is mandatory for an
 * administrator to create a repository type in order to be able to create
 * some instances of this type.
 * Coding note:
 * - a repository_type object is mapped to the "repository" database table
 * - "typename" attibut maps the "type" database field. It is unique.
 * - general "options" for a repository type are saved in the config_plugin table
 * - when you delete a repository, all instances are deleted, and general
 *   options are also deleted from database
 * - When you create a type for a plugin that can't have multiple instances, a
 *   instance is automatically created.
 *
 * @package   core_repository
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_type implements cacheable_object {


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
     *
     * @todo check if the context visibility has been overwritten by the plugin creator
     *       (need to create special functions to be overvwritten in repository class)
     * @param stdClass $context context
     * @return bool
     */
    public function get_contextvisibility($context) {
        global $USER;

        if ($context->contextlevel == CONTEXT_COURSE) {
            return $this->_options['enablecourseinstances'];
        }

        if ($context->contextlevel == CONTEXT_USER) {
            return $this->_options['enableuserinstances'];
        }

        //the context is SITE
        return true;
    }



    /**
     * repository_type constructor
     *
     * @param int $typename
     * @param array $typeoptions
     * @param bool $visible
     * @param int $sortorder (don't really need set, it will be during create() call)
     */
    public function __construct($typename = '', $typeoptions = array(), $visible = true, $sortorder = 0) {
        global $CFG;

        //set type attributs
        $this->_typename = $typename;
        $this->_visible = $visible;
        $this->_sortorder = $sortorder;

        //set options attribut
        $this->_options = array();
        $options = repository::static_function($typename, 'get_type_option_names');
        //check that the type can be setup
        if (!empty($options)) {
            //set the type options
            foreach ($options as $config) {
                if (array_key_exists($config, $typeoptions)) {
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
     *
     * @return string the type name
     */
    public function get_typename() {
        return $this->_typename;
    }

    /**
     * Return a human readable and user-friendly type name
     *
     * @return string user-friendly type name
     */
    public function get_readablename() {
        return get_string('pluginname','repository_'.$this->_typename);
    }

    /**
     * Return general options
     *
     * @return array the general options
     */
    public function get_options() {
        return $this->_options;
    }

    /**
     * Return visibility
     *
     * @return bool
     */
    public function get_visible() {
        return $this->_visible;
    }

    /**
     * Return order / position of display in the file picker
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->_sortorder;
    }

    /**
     * Create a repository type (the type name must not already exist)
     * @param bool $silent throw exception?
     * @return mixed return int if create successfully, return false if
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
            $newtype = new stdClass();
            $newtype->type = $this->_typename;
            $newtype->visible = $this->_visible;
            $newtype->sortorder = $this->_sortorder;
            $plugin_id = $DB->insert_record('repository', $newtype);
            //save the options in DB
            $this->update_options();

            $instanceoptionnames = repository::static_function($this->_typename, 'get_instance_option_names');

            //if the plugin type has no multiple instance (e.g. has no instance option name) so it wont
            //be possible for the administrator to create a instance
            //in this case we need to create an instance
            if (empty($instanceoptionnames)) {
                $instanceoptions = array();
                if (empty($this->_options['pluginname'])) {
                    // when moodle trying to install some repo plugin automatically
                    // this option will be empty, get it from language string when display
                    $instanceoptions['name'] = '';
                } else {
                    // when admin trying to add a plugin manually, he will type a name
                    // for it
                    $instanceoptions['name'] = $this->_options['pluginname'];
                }
                repository::static_function($this->_typename, 'create', $this->_typename, 0, context_system::instance(), $instanceoptions);
            }
            //run plugin_init function
            if (!repository::static_function($this->_typename, 'plugin_init')) {
                $this->update_visibility(false);
                if (!$silent) {
                    throw new repository_exception('cannotinitplugin', 'repository');
                }
            }

            cache::make('core', 'repositories')->purge();
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
     *
     * @param array $options
     * @return bool
     */
    public function update_options($options = null) {
        global $DB;
        $classname = 'repository_' . $this->_typename;
        $instanceoptions = repository::static_function($this->_typename, 'get_instance_option_names');
        if (empty($instanceoptions)) {
            // update repository instance name if this plugin type doesn't have muliti instances
            $params = array();
            $params['type'] = $this->_typename;
            $instances = repository::get_instances($params);
            $instance = array_pop($instances);
            if ($instance) {
                $DB->set_field('repository_instances', 'name', $options['pluginname'], array('id'=>$instance->id));
            }
            unset($options['pluginname']);
        }

        if (!empty($options)) {
            $this->_options = $options;
        }

        foreach ($this->_options as $name => $value) {
            set_config($name, $value, $this->_typename);
        }

        cache::make('core', 'repositories')->purge();
        return true;
    }

    /**
     * Update visible database field with the value given as parameter
     * or with the visible value of this object
     * This function is private.
     * For public access, have a look to switch_and_update_visibility()
     *
     * @param bool $visible
     * @return bool
     */
    private function update_visible($visible = null) {
        global $DB;

        if (!empty($visible)) {
            $this->_visible = $visible;
        }
        else if (!isset($this->_visible)) {
            throw new repository_exception('updateemptyvisible', 'repository');
        }

        cache::make('core', 'repositories')->purge();
        return $DB->set_field('repository', 'visible', $this->_visible, array('type'=>$this->_typename));
    }

    /**
     * Update database sortorder field with the value given as parameter
     * or with the sortorder value of this object
     * This function is private.
     * For public access, have a look to move_order()
     *
     * @param int $sortorder
     * @return bool
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

        cache::make('core', 'repositories')->purge();
        return $DB->set_field('repository', 'sortorder', $this->_sortorder, array('type'=>$this->_typename));
    }

    /**
     * Change order of the type with its adjacent upper or downer type
     * (database fields are updated)
     * Algorithm details:
     * 1. retrieve all types in an array. This array is sorted by sortorder,
     * and the array keys start from 0 to X (incremented by 1)
     * 2. switch sortorder values of this type and its adjacent type
     *
     * @param string $move "up" or "down"
     */
    public function move_order($move) {
        global $DB;

        $types = repository::get_types();    // retrieve all types

        // retrieve this type into the returned array
        $i = 0;
        while (!isset($indice) && $i<count($types)) {
            if ($types[$i]->get_typename() == $this->_typename) {
                $indice = $i;
            }
            $i++;
        }

        // retrieve adjacent indice
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
     * 1. Change visibility to the value chosen
     * 2. Update the type
     *
     * @param bool $visible
     * @return bool
     */
    public function update_visibility($visible = null) {
        if (is_bool($visible)) {
            $this->_visible = $visible;
        } else {
            $this->_visible = !$this->_visible;
        }
        return $this->update_visible();
    }


    /**
     * Delete a repository_type (general options are removed from config_plugin
     * table, and all instances are deleted)
     *
     * @param bool $downloadcontents download external contents if exist
     * @return bool
     */
    public function delete($downloadcontents = false) {
        global $DB;

        //delete all instances of this type
        $params = array();
        $params['context'] = array();
        $params['onlyvisible'] = false;
        $params['type'] = $this->_typename;
        $instances = repository::get_instances($params);
        foreach ($instances as $instance) {
            $instance->delete($downloadcontents);
        }

        //delete all general options
        foreach ($this->_options as $name => $value) {
            set_config($name, null, $this->_typename);
        }

        cache::make('core', 'repositories')->purge();
        try {
            $DB->delete_records('repository', array('type' => $this->_typename));
        } catch (dml_exception $ex) {
            return false;
        }
        return true;
    }

    /**
     * Prepares the repository type to be cached. Implements method from cacheable_object interface.
     *
     * @return array
     */
    public function prepare_to_cache() {
        return array(
            'typename' => $this->_typename,
            'typeoptions' => $this->_options,
            'visible' => $this->_visible,
            'sortorder' => $this->_sortorder
        );
    }

    /**
     * Restores repository type from cache. Implements method from cacheable_object interface.
     *
     * @return array
     */
    public static function wake_from_cache($data) {
        return new repository_type($data['typename'], $data['typeoptions'], $data['visible'], $data['sortorder']);
    }
}

/**
 * This is the base class of the repository class.
 *
 * To create repository plugin, see: {@link http://docs.moodle.org/dev/Repository_plugins}
 * See an example: {@link repository_boxnet}
 *
 * @package   core_repository
 * @copyright 2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class repository implements cacheable_object {
    /**
     * Timeout in seconds for downloading the external file into moodle
     * @deprecated since Moodle 2.7, please use $CFG->repositorygetfiletimeout instead
     */
    const GETFILE_TIMEOUT = 30;

    /**
     * Timeout in seconds for syncronising the external file size
     * @deprecated since Moodle 2.7, please use $CFG->repositorysyncfiletimeout instead
     */
    const SYNCFILE_TIMEOUT = 1;

    /**
     * Timeout in seconds for downloading an image file from external repository during syncronisation
     * @deprecated since Moodle 2.7, please use $CFG->repositorysyncimagetimeout instead
     */
    const SYNCIMAGE_TIMEOUT = 3;

    // $disabled can be set to true to disable a plugin by force
    // example: self::$disabled = true
    /** @var bool force disable repository instance */
    public $disabled = false;
    /** @var int repository instance id */
    public $id;
    /** @var stdClass current context */
    public $context;
    /** @var array repository options */
    public $options;
    /** @var bool Whether or not the repository instance is editable */
    public $readonly;
    /** @var int return types */
    public $returntypes;
    /** @var stdClass repository instance database record */
    public $instance;
    /** @var string Type of repository (webdav, google_docs, dropbox, ...). Read from $this->get_typename(). */
    protected $typename;

    /**
     * Constructor
     *
     * @param int $repositoryid repository instance id
     * @param int|stdClass $context a context id or context object
     * @param array $options repository options
     * @param int $readonly indicate this repo is readonly or not
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly = 0) {
        global $DB;
        $this->id = $repositoryid;
        if (is_object($context)) {
            $this->context = $context;
        } else {
            $this->context = context::instance_by_id($context);
        }
        $cache = cache::make('core', 'repositories');
        if (($this->instance = $cache->get('i:'. $this->id)) === false) {
            $this->instance = $DB->get_record_sql("SELECT i.*, r.type AS repositorytype, r.sortorder, r.visible
                      FROM {repository} r, {repository_instances} i
                     WHERE i.typeid = r.id and i.id = ?", array('id' => $this->id));
            $cache->set('i:'. $this->id, $this->instance);
        }
        $this->readonly = $readonly;
        $this->options = array();

        if (is_array($options)) {
            // The get_option() method will get stored options in database.
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
     * Get repository instance using repository id
     *
     * Note that this function does not check permission to access repository contents
     *
     * @throws repository_exception
     *
     * @param int $repositoryid repository instance ID
     * @param context|int $context context instance or context ID where this repository will be used
     * @param array $options additional repository options
     * @return repository
     */
    public static function get_repository_by_id($repositoryid, $context, $options = array()) {
        global $CFG, $DB;
        $cache = cache::make('core', 'repositories');
        if (!is_object($context)) {
            $context = context::instance_by_id($context);
        }
        $cachekey = 'rep:'. $repositoryid. ':'. $context->id. ':'. serialize($options);
        if ($repository = $cache->get($cachekey)) {
            return $repository;
        }

        if (!$record = $cache->get('i:'. $repositoryid)) {
            $sql = "SELECT i.*, r.type AS repositorytype, r.visible, r.sortorder
                      FROM {repository_instances} i
                      JOIN {repository} r ON r.id = i.typeid
                     WHERE i.id = ?";
            if (!$record = $DB->get_record_sql($sql, array($repositoryid))) {
                throw new repository_exception('invalidrepositoryid', 'repository');
            }
            $cache->set('i:'. $record->id, $record);
        }

        $type = $record->repositorytype;
        if (file_exists($CFG->dirroot . "/repository/$type/lib.php")) {
            require_once($CFG->dirroot . "/repository/$type/lib.php");
            $classname = 'repository_' . $type;
            $options['type'] = $type;
            $options['typeid'] = $record->typeid;
            $options['visible'] = $record->visible;
            if (empty($options['name'])) {
                $options['name'] = $record->name;
            }
            $repository = new $classname($repositoryid, $context, $options, $record->readonly);
            if (empty($repository->super_called)) {
                // to make sure the super construct is called
                debugging('parent::__construct must be called by '.$type.' plugin.');
            }
            $cache->set($cachekey, $repository);
            return $repository;
        } else {
            throw new repository_exception('invalidplugin', 'repository');
        }
    }

    /**
     * Returns the type name of the repository.
     *
     * @return string type name of the repository.
     * @since  Moodle 2.5
     */
    public function get_typename() {
        if (empty($this->typename)) {
            $matches = array();
            if (!preg_match("/^repository_(.*)$/", get_class($this), $matches)) {
                throw new coding_exception('The class name of a repository should be repository_<typeofrepository>, '.
                        'e.g. repository_dropbox');
            }
            $this->typename = $matches[1];
        }
        return $this->typename;
    }

    /**
     * Get a repository type object by a given type name.
     *
     * @static
     * @param string $typename the repository type name
     * @return repository_type|bool
     */
    public static function get_type_by_typename($typename) {
        global $DB;
        $cache = cache::make('core', 'repositories');
        if (($repositorytype = $cache->get('typename:'. $typename)) === false) {
            $repositorytype = null;
            if ($record = $DB->get_record('repository', array('type' => $typename))) {
                $repositorytype = new repository_type($record->type, (array)get_config($record->type), $record->visible, $record->sortorder);
                $cache->set('typeid:'. $record->id, $repositorytype);
            }
            $cache->set('typename:'. $typename, $repositorytype);
        }
        return $repositorytype;
    }

    /**
     * Get the repository type by a given repository type id.
     *
     * @static
     * @param int $id the type id
     * @return object
     */
    public static function get_type_by_id($id) {
        global $DB;
        $cache = cache::make('core', 'repositories');
        if (($repositorytype = $cache->get('typeid:'. $id)) === false) {
            $repositorytype = null;
            if ($record = $DB->get_record('repository', array('id' => $id))) {
                $repositorytype = new repository_type($record->type, (array)get_config($record->type), $record->visible, $record->sortorder);
                $cache->set('typename:'. $record->type, $repositorytype);
            }
            $cache->set('typeid:'. $id, $repositorytype);
        }
        return $repositorytype;
    }

    /**
     * Return all repository types ordered by sortorder field
     * first repository type in returnedarray[0], second repository type in returnedarray[1], ...
     *
     * @static
     * @param bool $visible can return types by visiblity, return all types if null
     * @return array Repository types
     */
    public static function get_types($visible=null) {
        global $DB, $CFG;
        $cache = cache::make('core', 'repositories');
        if (!$visible) {
            $typesnames = $cache->get('types');
        } else {
            $typesnames = $cache->get('typesvis');
        }
        $types = array();
        if ($typesnames === false) {
            $typesnames = array();
            $vistypesnames = array();
            if ($records = $DB->get_records('repository', null ,'sortorder')) {
                foreach($records as $type) {
                    if (($repositorytype = $cache->get('typename:'. $type->type)) === false) {
                        // Create new instance of repository_type.
                        if (file_exists($CFG->dirroot . '/repository/'. $type->type .'/lib.php')) {
                            $repositorytype = new repository_type($type->type, (array)get_config($type->type), $type->visible, $type->sortorder);
                            $cache->set('typeid:'. $type->id, $repositorytype);
                            $cache->set('typename:'. $type->type, $repositorytype);
                        }
                    }
                    if ($repositorytype) {
                        if (empty($visible) || $repositorytype->get_visible()) {
                            $types[] = $repositorytype;
                            $vistypesnames[] = $repositorytype->get_typename();
                        }
                        $typesnames[] = $repositorytype->get_typename();
                    }
                }
            }
            $cache->set('types', $typesnames);
            $cache->set('typesvis', $vistypesnames);
        } else {
            foreach ($typesnames as $typename) {
                $types[] = self::get_type_by_typename($typename);
            }
        }
        return $types;
    }

    /**
     * Checks if user has a capability to view the current repository.
     *
     * @return bool true when the user can, otherwise throws an exception.
     * @throws repository_exception when the user does not meet the requirements.
     */
    public final function check_capability() {
        global $USER;

        // The context we are on.
        $currentcontext = $this->context;

        // Ensure that the user can view the repository in the current context.
        $can = has_capability('repository/'.$this->get_typename().':view', $currentcontext);

        // Context in which the repository has been created.
        $repocontext = context::instance_by_id($this->instance->contextid);

        // Prevent access to private repositories when logged in as.
        if ($can && \core\session\manager::is_loggedinas()) {
            if ($this->contains_private_data() || $repocontext->contextlevel == CONTEXT_USER) {
                $can = false;
            }
        }

        // We are going to ensure that the current context was legit, and reliable to check
        // the capability against. (No need to do that if we already cannot).
        if ($can) {
            if ($repocontext->contextlevel == CONTEXT_USER) {
                // The repository is a user instance, ensure we're the right user to access it!
                if ($repocontext->instanceid != $USER->id) {
                    $can = false;
                }
            } else if ($repocontext->contextlevel == CONTEXT_COURSE) {
                // The repository is a course one. Let's check that we are on the right course.
                if (in_array($currentcontext->contextlevel, array(CONTEXT_COURSE, CONTEXT_MODULE, CONTEXT_BLOCK))) {
                    $coursecontext = $currentcontext->get_course_context();
                    if ($coursecontext->instanceid != $repocontext->instanceid) {
                        $can = false;
                    }
                } else {
                    // We are on a parent context, therefore it's legit to check the permissions
                    // in the current context.
                }
            } else {
                // Nothing to check here, system instances can have different permissions on different
                // levels. We do not want to prevent URL hack here, because it does not make sense to
                // prevent a user to access a repository in a context if it's accessible in another one.
            }
        }

        if ($can) {
            return true;
        }

        throw new repository_exception('nopermissiontoaccess', 'repository');
    }

    /**
     * Check if file already exists in draft area.
     *
     * @static
     * @param int $itemid of the draft area.
     * @param string $filepath path to the file.
     * @param string $filename file name.
     * @return bool
     */
    public static function draftfile_exists($itemid, $filepath, $filename) {
        global $USER;
        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);
        return $fs->file_exists($usercontext->id, 'user', 'draft', $itemid, $filepath, $filename);
    }

    /**
     * Parses the moodle file reference and returns an instance of stored_file
     *
     * @param string $reference reference to the moodle internal file as retruned by
     *        {@link repository::get_file_reference()} or {@link file_storage::pack_reference()}
     * @return stored_file|null
     */
    public static function get_moodle_file($reference) {
        $params = file_storage::unpack_reference($reference, true);
        $fs = get_file_storage();
        return $fs->get_file($params['contextid'], $params['component'], $params['filearea'],
                    $params['itemid'], $params['filepath'], $params['filename']);
    }

    /**
     * Repository method to make sure that user can access particular file.
     *
     * This is checked when user tries to pick the file from repository to deal with
     * potential parameter substitutions is request
     *
     * @param string $source source of the file, returned by repository as 'source' and received back from user (not cleaned)
     * @return bool whether the file is accessible by current user
     */
    public function file_is_accessible($source) {
        if ($this->has_moodle_files()) {
            $reference = $this->get_file_reference($source);
            try {
                $params = file_storage::unpack_reference($reference, true);
            } catch (file_reference_exception $e) {
                return false;
            }
            $browser = get_file_browser();
            $context = context::instance_by_id($params['contextid']);
            $file_info = $browser->get_file_info($context, $params['component'], $params['filearea'],
                    $params['itemid'], $params['filepath'], $params['filename']);
            return !empty($file_info);
        }
        return true;
    }

    /**
     * This function is used to copy a moodle file to draft area.
     *
     * It DOES NOT check if the user is allowed to access this file because the actual file
     * can be located in the area where user does not have access to but there is an alias
     * to this file in the area where user CAN access it.
     * {@link file_is_accessible} should be called for alias location before calling this function.
     *
     * @param string $source The metainfo of file, it is base64 encoded php serialized data
     * @param stdClass|array $filerecord contains itemid, filepath, filename and optionally other
     *      attributes of the new file
     * @param int $maxbytes maximum allowed size of file, -1 if unlimited. If size of file exceeds
     *      the limit, the file_exception is thrown.
     * @param int $areamaxbytes the maximum size of the area. A file_exception is thrown if the
     *      new file will reach the limit.
     * @return array The information about the created file
     */
    public function copy_to_area($source, $filerecord, $maxbytes = -1, $areamaxbytes = FILE_AREA_MAX_BYTES_UNLIMITED) {
        global $USER;
        $fs = get_file_storage();

        if ($this->has_moodle_files() == false) {
            throw new coding_exception('Only repository used to browse moodle files can use repository::copy_to_area()');
        }

        $user_context = context_user::instance($USER->id);

        $filerecord = (array)$filerecord;
        // make sure the new file will be created in user draft area
        $filerecord['component'] = 'user';
        $filerecord['filearea'] = 'draft';
        $filerecord['contextid'] = $user_context->id;
        $draftitemid = $filerecord['itemid'];
        $new_filepath = $filerecord['filepath'];
        $new_filename = $filerecord['filename'];

        // the file needs to copied to draft area
        $stored_file = self::get_moodle_file($source);
        if ($maxbytes != -1 && $stored_file->get_filesize() > $maxbytes) {
            $maxbytesdisplay = display_size($maxbytes);
            throw new file_exception('maxbytesfile', (object) array('file' => $filerecord['filename'],
                                                                    'size' => $maxbytesdisplay));
        }
        // Validate the size of the draft area.
        if (file_is_draft_area_limit_reached($draftitemid, $areamaxbytes, $stored_file->get_filesize())) {
            throw new file_exception('maxareabytes');
        }

        if (repository::draftfile_exists($draftitemid, $new_filepath, $new_filename)) {
            // create new file
            $unused_filename = repository::get_unused_filename($draftitemid, $new_filepath, $new_filename);
            $filerecord['filename'] = $unused_filename;
            $fs->create_file_from_storedfile($filerecord, $stored_file);
            $event = array();
            $event['event'] = 'fileexists';
            $event['newfile'] = new stdClass;
            $event['newfile']->filepath = $new_filepath;
            $event['newfile']->filename = $unused_filename;
            $event['newfile']->url = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $unused_filename)->out();
            $event['existingfile'] = new stdClass;
            $event['existingfile']->filepath = $new_filepath;
            $event['existingfile']->filename = $new_filename;
            $event['existingfile']->url = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $new_filename)->out();
            return $event;
        } else {
            $fs->create_file_from_storedfile($filerecord, $stored_file);
            $info = array();
            $info['itemid'] = $draftitemid;
            $info['file'] = $new_filename;
            $info['title'] = $new_filename;
            $info['contextid'] = $user_context->id;
            $info['url'] = moodle_url::make_draftfile_url($draftitemid, $new_filepath, $new_filename)->out();
            $info['filesize'] = $stored_file->get_filesize();
            return $info;
        }
    }

    /**
     * Get an unused filename from the current draft area.
     *
     * Will check if the file ends with ([0-9]) and increase the number.
     *
     * @static
     * @param int $itemid draft item ID.
     * @param string $filepath path to the file.
     * @param string $filename name of the file.
     * @return string an unused file name.
     */
    public static function get_unused_filename($itemid, $filepath, $filename) {
        global $USER;
        $contextid = context_user::instance($USER->id)->id;
        $fs = get_file_storage();
        return $fs->get_unused_filename($contextid, 'user', 'draft', $itemid, $filepath, $filename);
    }

    /**
     * Append a suffix to filename.
     *
     * @static
     * @param string $filename
     * @return string
     * @deprecated since 2.5
     */
    public static function append_suffix($filename) {
        debugging('The function repository::append_suffix() has been deprecated. Use repository::get_unused_filename() instead.',
            DEBUG_DEVELOPER);
        $pathinfo = pathinfo($filename);
        if (empty($pathinfo['extension'])) {
            return $filename . RENAME_SUFFIX;
        } else {
            return $pathinfo['filename'] . RENAME_SUFFIX . '.' . $pathinfo['extension'];
        }
    }

    /**
     * Return all types that you a user can create/edit and which are also visible
     * Note: Mostly used in order to know if at least one editable type can be set
     *
     * @static
     * @param stdClass $context the context for which we want the editable types
     * @return array types
     */
    public static function get_editable_types($context = null) {

        if (empty($context)) {
            $context = context_system::instance();
        }

        $types= repository::get_types(true);
        $editabletypes = array();
        foreach ($types as $type) {
            $instanceoptionnames = repository::static_function($type->get_typename(), 'get_instance_option_names');
            if (!empty($instanceoptionnames)) {
                if ($type->get_contextvisibility($context)) {
                    $editabletypes[]=$type;
                }
             }
        }
        return $editabletypes;
    }

    /**
     * Return repository instances
     *
     * @static
     * @param array $args Array containing the following keys:
     *           currentcontext : instance of context (default system context)
     *           context : array of instances of context (default empty array)
     *           onlyvisible : bool (default true)
     *           type : string return instances of this type only
     *           accepted_types : string|array return instances that contain files of those types (*, web_image, .pdf, ...)
     *           return_types : int combination of FILE_INTERNAL & FILE_EXTERNAL & FILE_REFERENCE & FILE_CONTROLLED_LINK.
     *                          0 means every type. The default is FILE_INTERNAL | FILE_EXTERNAL.
     *           userid : int if specified, instances belonging to other users will not be returned
     *
     * @return array repository instances
     */
    public static function get_instances($args = array()) {
        global $DB, $CFG, $USER;

        // Fill $args attributes with default values unless specified
        if (!isset($args['currentcontext']) || !($args['currentcontext'] instanceof context)) {
            $current_context = context_system::instance();
        } else {
            $current_context = $args['currentcontext'];
        }
        $args['currentcontext'] = $current_context->id;
        $contextids = array();
        if (!empty($args['context'])) {
            foreach ($args['context'] as $context) {
                $contextids[] = $context->id;
            }
        }
        $args['context'] = $contextids;
        if (!isset($args['onlyvisible'])) {
            $args['onlyvisible'] = true;
        }
        if (!isset($args['return_types'])) {
            $args['return_types'] = FILE_INTERNAL | FILE_EXTERNAL;
        }
        if (!isset($args['type'])) {
            $args['type'] = null;
        }
        if (empty($args['disable_types']) || !is_array($args['disable_types'])) {
            $args['disable_types'] = null;
        }
        if (empty($args['userid']) || !is_numeric($args['userid'])) {
            $args['userid'] = null;
        }
        if (!isset($args['accepted_types']) || (is_array($args['accepted_types']) && in_array('*', $args['accepted_types']))) {
            $args['accepted_types'] = '*';
        }
        ksort($args);
        $cachekey = 'all:'. serialize($args);

        // Check if we have cached list of repositories with the same query
        $cache = cache::make('core', 'repositories');
        if (($cachedrepositories = $cache->get($cachekey)) !== false) {
            // convert from cacheable_object_array to array
            $repositories = array();
            foreach ($cachedrepositories as $repository) {
                $repositories[$repository->id] = $repository;
            }
            return $repositories;
        }

        // Prepare DB SQL query to retrieve repositories
        $params = array();
        $sql = "SELECT i.*, r.type AS repositorytype, r.sortorder, r.visible
                  FROM {repository} r, {repository_instances} i
                 WHERE i.typeid = r.id ";

        if ($args['disable_types']) {
            list($types, $p) = $DB->get_in_or_equal($args['disable_types'], SQL_PARAMS_NAMED, 'distype', false);
            $sql .= " AND r.type $types";
            $params = array_merge($params, $p);
        }

        if ($args['userid']) {
            $sql .= " AND (i.userid = 0 or i.userid = :userid)";
            $params['userid'] = $args['userid'];
        }

        if ($args['context']) {
            list($ctxsql, $p2) = $DB->get_in_or_equal($args['context'], SQL_PARAMS_NAMED, 'ctx');
            $sql .= " AND i.contextid $ctxsql";
            $params = array_merge($params, $p2);
        }

        if ($args['onlyvisible'] == true) {
            $sql .= " AND r.visible = 1";
        }

        if ($args['type'] !== null) {
            $sql .= " AND r.type = :type";
            $params['type'] = $args['type'];
        }
        $sql .= " ORDER BY r.sortorder, i.name";

        if (!$records = $DB->get_records_sql($sql, $params)) {
            $records = array();
        }

        $repositories = array();
        // Sortorder should be unique, which is not true if we use $record->sortorder
        // and there are multiple instances of any repository type
        $sortorder = 1;
        foreach ($records as $record) {
            $cache->set('i:'. $record->id, $record);
            if (!file_exists($CFG->dirroot . '/repository/'. $record->repositorytype.'/lib.php')) {
                continue;
            }
            $repository = self::get_repository_by_id($record->id, $current_context);
            $repository->options['sortorder'] = $sortorder++;

            $is_supported = true;

            // check mimetypes
            if ($args['accepted_types'] !== '*' and $repository->supported_filetypes() !== '*') {
                $accepted_ext = file_get_typegroup('extension', $args['accepted_types']);
                $supported_ext = file_get_typegroup('extension', $repository->supported_filetypes());
                $valid_ext = array_intersect($accepted_ext, $supported_ext);
                $is_supported = !empty($valid_ext);
            }
            // Check return values.
            if (!empty($args['return_types']) && !($repository->supported_returntypes() & $args['return_types'])) {
                $is_supported = false;
            }

            if (!$args['onlyvisible'] || ($repository->is_visible() && !$repository->disabled)) {
                // check capability in current context
                $capability = has_capability('repository/'.$record->repositorytype.':view', $current_context);
                if ($record->repositorytype == 'coursefiles') {
                    // coursefiles plugin needs managefiles permission
                    $capability = $capability && has_capability('moodle/course:managefiles', $current_context);
                }
                if ($is_supported && $capability) {
                    $repositories[$repository->id] = $repository;
                }
            }
        }
        $cache->set($cachekey, new cacheable_object_array($repositories));
        return $repositories;
    }

    /**
     * Get single repository instance for administrative actions
     *
     * Do not use this function to access repository contents, because it
     * does not set the current context
     *
     * @see repository::get_repository_by_id()
     *
     * @static
     * @param integer $id repository instance id
     * @return repository
     */
    public static function get_instance($id) {
        return self::get_repository_by_id($id, context_system::instance());
    }

    /**
     * Call a static function. Any additional arguments than plugin and function will be passed through.
     *
     * @static
     * @param string $plugin repository plugin name
     * @param string $function function name
     * @return mixed
     */
    public static function static_function($plugin, $function) {
        global $CFG;

        //check that the plugin exists
        $typedirectory = $CFG->dirroot . '/repository/'. $plugin . '/lib.php';
        if (!file_exists($typedirectory)) {
            //throw new repository_exception('invalidplugin', 'repository');
            return false;
        }

        $args = func_get_args();
        if (count($args) <= 2) {
            $args = array();
        } else {
            array_shift($args);
            array_shift($args);
        }

        require_once($typedirectory);
        return call_user_func_array(array('repository_' . $plugin, $function), $args);
    }

    /**
     * Scan file, throws exception in case of infected file.
     *
     * Please note that the scanning engine must be able to access the file,
     * permissions of the file are not modified here!
     *
     * @static
     * @deprecated since Moodle 3.0
     * @param string $thefile
     * @param string $filename name of the file
     * @param bool $deleteinfected
     */
    public static function antivir_scan_file($thefile, $filename, $deleteinfected) {
        debugging('Please upgrade your code to use \core\antivirus\manager::scan_file instead', DEBUG_DEVELOPER);
        \core\antivirus\manager::scan_file($thefile, $filename, $deleteinfected);
    }

    /**
     * Repository method to serve the referenced file
     *
     * @see send_stored_file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        if ($this->has_moodle_files()) {
            $fs = get_file_storage();
            $params = file_storage::unpack_reference($storedfile->get_reference(), true);
            $srcfile = null;
            if (is_array($params)) {
                $srcfile = $fs->get_file($params['contextid'], $params['component'], $params['filearea'],
                        $params['itemid'], $params['filepath'], $params['filename']);
            }
            if (empty($options)) {
                $options = array();
            }
            if (!isset($options['filename'])) {
                $options['filename'] = $storedfile->get_filename();
            }
            if (!$srcfile) {
                send_file_not_found();
            } else {
                send_stored_file($srcfile, $lifetime, $filter, $forcedownload, $options);
            }
        } else {
            throw new coding_exception("Repository plugin must implement send_file() method.");
        }
    }

    /**
     * Return human readable reference information
     *
     * @param string $reference value of DB field files_reference.reference
     * @param int $filestatus status of the file, 0 - ok, 666 - source missing
     * @return string
     */
    public function get_reference_details($reference, $filestatus = 0) {
        if ($this->has_moodle_files()) {
            $fileinfo = null;
            $params = file_storage::unpack_reference($reference, true);
            if (is_array($params)) {
                $context = context::instance_by_id($params['contextid'], IGNORE_MISSING);
                if ($context) {
                    $browser = get_file_browser();
                    $fileinfo = $browser->get_file_info($context, $params['component'], $params['filearea'], $params['itemid'], $params['filepath'], $params['filename']);
                }
            }
            if (empty($fileinfo)) {
                if ($filestatus == 666) {
                    if (is_siteadmin() || ($context && has_capability('moodle/course:managefiles', $context))) {
                        return get_string('lostsource', 'repository',
                                $params['contextid']. '/'. $params['component']. '/'. $params['filearea']. '/'. $params['itemid']. $params['filepath']. $params['filename']);
                    } else {
                        return get_string('lostsource', 'repository', '');
                    }
                }
                return get_string('undisclosedsource', 'repository');
            } else {
                return $fileinfo->get_readable_fullname();
            }
        }
        return '';
    }

    /**
     * Cache file from external repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     * Invoked at MOODLE/repository/repository_ajax.php
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param stored_file $storedfile created file reference
     */
    public function cache_file_by_reference($reference, $storedfile) {
    }

    /**
     * reference_file_selected
     *
     * This function is called when a controlled link file is selected in a file picker and the form is
     * saved. The expected behaviour for repositories supporting controlled links is to
     * - copy the file to the moodle system account
     * - put it in a folder that reflects the context it is being used
     * - make sure the sharing permissions are correct (read-only with the link)
     * - return a new reference string pointing to the newly copied file.
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param context $context the target context for this new file.
     * @param string $component the target component for this new file.
     * @param string $filearea the target filearea for this new file.
     * @param string $itemid the target itemid for this new file.
     * @return string updated reference (final one before it's saved to db).
     */
    public function reference_file_selected($reference, $context, $component, $filearea, $itemid) {
        return $reference;
    }

    /**
     * Return the source information
     *
     * The result of the function is stored in files.source field. It may be analysed
     * when the source file is lost or repository may use it to display human-readable
     * location of reference original.
     *
     * This method is called when file is picked for the first time only. When file
     * (either copy or a reference) is already in moodle and it is being picked
     * again to another file area (also as a copy or as a reference), the value of
     * files.source is copied.
     *
     * @param string $source source of the file, returned by repository as 'source' and received back from user (not cleaned)
     * @return string|null
     */
    public function get_file_source_info($source) {
        if ($this->has_moodle_files()) {
            $reference = $this->get_file_reference($source);
            return $this->get_reference_details($reference, 0);
        }
        return $source;
    }

    /**
     * Move file from download folder to file pool using FILE API
     *
     * @todo MDL-28637
     * @static
     * @param string $thefile file path in download folder
     * @param stdClass $record
     * @return array containing the following keys:
     *           icon
     *           file
     *           id
     *           url
     */
    public static function move_to_filepool($thefile, $record) {
        global $DB, $CFG, $USER, $OUTPUT;

        // scan for viruses if possible, throws exception if problem found
        // TODO: MDL-28637 this repository_no_delete is a bloody hack!
        \core\antivirus\manager::scan_file($thefile, $record->filename, empty($CFG->repository_no_delete));

        $fs = get_file_storage();
        // If file name being used.
        if (repository::draftfile_exists($record->itemid, $record->filepath, $record->filename)) {
            $draftitemid = $record->itemid;
            $new_filename = repository::get_unused_filename($draftitemid, $record->filepath, $record->filename);
            $old_filename = $record->filename;
            // Create a tmp file.
            $record->filename = $new_filename;
            $newfile = $fs->create_file_from_pathname($record, $thefile);
            $event = array();
            $event['event'] = 'fileexists';
            $event['newfile'] = new stdClass;
            $event['newfile']->filepath = $record->filepath;
            $event['newfile']->filename = $new_filename;
            $event['newfile']->url = moodle_url::make_draftfile_url($draftitemid, $record->filepath, $new_filename)->out();

            $event['existingfile'] = new stdClass;
            $event['existingfile']->filepath = $record->filepath;
            $event['existingfile']->filename = $old_filename;
            $event['existingfile']->url      = moodle_url::make_draftfile_url($draftitemid, $record->filepath, $old_filename)->out();
            return $event;
        }
        if ($file = $fs->create_file_from_pathname($record, $thefile)) {
            if (empty($CFG->repository_no_delete)) {
                $delete = unlink($thefile);
                unset($CFG->repository_no_delete);
            }
            return array(
                'url'=>moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename())->out(),
                'id'=>$file->get_itemid(),
                'file'=>$file->get_filename(),
                'icon' => $OUTPUT->image_url(file_extension_icon($thefile, 32))->out(),
            );
        } else {
            return null;
        }
    }

    /**
     * Builds a tree of files This function is then called recursively.
     *
     * @static
     * @todo take $search into account, and respect a threshold for dynamic loading
     * @param file_info $fileinfo an object returned by file_browser::get_file_info()
     * @param string $search searched string
     * @param bool $dynamicmode no recursive call is done when in dynamic mode
     * @param array $list the array containing the files under the passed $fileinfo
     * @return int the number of files found
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
                    $path[] = array($params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }

                $tmp = array(
                    'title' => $child->get_visible_name(),
                    'size' => 0,
                    'date' => $filedate,
                    'path' => array_reverse($path),
                    'thumbnail' => $OUTPUT->image_url(file_folder_icon(90))->out(false)
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
                $source = serialize(array($params['contextid'], $params['component'], $params['filearea'], $params['itemid'], $params['filepath'], $params['filename']));
                $list[] = array(
                    'title' => $filename,
                    'size' => $filesize,
                    'date' => $filedate,
                    //'source' => $child->get_url(),
                    'source' => base64_encode($source),
                    'icon'=>$OUTPUT->image_url(file_file_icon($child, 24))->out(false),
                    'thumbnail'=>$OUTPUT->image_url(file_file_icon($child, 90))->out(false),
                );
                $filecount++;
            }
        }

        return $filecount;
    }

    /**
     * Display a repository instance list (with edit/delete/create links)
     *
     * @static
     * @param stdClass $context the context for which we display the instance
     * @param string $typename if set, we display only one type of instance
     */
    public static function display_instances_list($context, $typename = null) {
        global $CFG, $USER, $OUTPUT;

        $output = $OUTPUT->box_start('generalbox');
        //if the context is SYSTEM, so we call it from administration page
        $admin = ($context->id == SYSCONTEXTID) ? true : false;
        if ($admin) {
            $baseurl = new moodle_url('/'.$CFG->admin.'/repositoryinstance.php', array('sesskey'=>sesskey()));
            $output .= $OUTPUT->heading(get_string('siteinstances', 'repository'));
        } else {
            $baseurl = new moodle_url('/repository/manage_instances.php', array('contextid'=>$context->id, 'sesskey'=>sesskey()));
        }

        $namestr = get_string('name');
        $pluginstr = get_string('plugin', 'repository');
        $settingsstr = get_string('settings');
        $deletestr = get_string('delete');
        // Retrieve list of instances. In administration context we want to display all
        // instances of a type, even if this type is not visible. In course/user context we
        // want to display only visible instances, but for every type types. The repository::get_instances()
        // third parameter displays only visible type.
        $params = array();
        $params['context'] = array($context);
        $params['currentcontext'] = $context;
        $params['return_types'] = 0;
        $params['onlyvisible'] = !$admin;
        $params['type']        = $typename;
        $instances = repository::get_instances($params);
        $instancesnumber = count($instances);
        $alreadyplugins = array();

        $table = new html_table();
        $table->head = array($namestr, $pluginstr, $settingsstr, $deletestr);
        $table->align = array('left', 'left', 'center','center');
        $table->data = array();

        $updowncount = 1;

        foreach ($instances as $i) {
            $settings = '';
            $delete = '';

            $type = repository::get_type_by_id($i->options['typeid']);

            if ($type->get_contextvisibility($context)) {
                if (!$i->readonly) {

                    $settingurl = new moodle_url($baseurl);
                    $settingurl->param('type', $i->options['type']);
                    $settingurl->param('edit', $i->id);
                    $settings .= html_writer::link($settingurl, $settingsstr);

                    $deleteurl = new moodle_url($baseurl);
                    $deleteurl->param('delete', $i->id);
                    $deleteurl->param('type', $i->options['type']);
                    $delete .= html_writer::link($deleteurl, $deletestr);
                }
            }

            $type = repository::get_type_by_id($i->options['typeid']);
            $table->data[] = array(format_string($i->name), $type->get_readablename(), $settings, $delete);

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
        $output .= html_writer::table($table);
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
                    // If the user does not have the permission to view the repository, it won't be displayed in
                    // the list of instances. Hiding the link to create new instances will prevent the
                    // user from creating them without being able to find them afterwards, which looks like a bug.
                    if (!has_capability('repository/'.$type->get_typename().':view', $context)) {
                        continue;
                    }
                    $instanceoptionnames = repository::static_function($type->get_typename(), 'get_instance_option_names');
                    if (!empty($instanceoptionnames)) {
                        $baseurl->param('new', $type->get_typename());
                        $instancehtml .= '<li><a href="'.$baseurl->out().'">'.get_string('createxxinstance', 'repository', get_string('pluginname', 'repository_'.$type->get_typename())).  '</a></li>';
                        $baseurl->remove_params('new');
                        $addable++;
                    }
                }
            }
            $instancehtml .= '</ul>';

        } else {
            $instanceoptionnames = repository::static_function($typename, 'get_instance_option_names');
            if (!empty($instanceoptionnames)) {   //create a unique type of instance
                $addable = 1;
                $baseurl->param('new', $typename);
                $output .= $OUTPUT->single_button($baseurl, get_string('createinstance', 'repository'), 'get');
                $baseurl->remove_params('new');
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
     * Prepare file reference information
     *
     * @param string $source source of the file, returned by repository as 'source' and received back from user (not cleaned)
     * @return string file reference, ready to be stored
     */
    public function get_file_reference($source) {
        if ($source && $this->has_moodle_files()) {
            $params = @json_decode(base64_decode($source), true);
            if (!is_array($params) || empty($params['contextid'])) {
                throw new repository_exception('invalidparams', 'repository');
            }
            $params = array(
                'component' => empty($params['component']) ? ''   : clean_param($params['component'], PARAM_COMPONENT),
                'filearea'  => empty($params['filearea'])  ? ''   : clean_param($params['filearea'], PARAM_AREA),
                'itemid'    => empty($params['itemid'])    ? 0    : clean_param($params['itemid'], PARAM_INT),
                'filename'  => empty($params['filename'])  ? null : clean_param($params['filename'], PARAM_FILE),
                'filepath'  => empty($params['filepath'])  ? null : clean_param($params['filepath'], PARAM_PATH),
                'contextid' => clean_param($params['contextid'], PARAM_INT)
            );
            // Check if context exists.
            if (!context::instance_by_id($params['contextid'], IGNORE_MISSING)) {
                throw new repository_exception('invalidparams', 'repository');
            }
            return file_storage::pack_reference($params);
        }
        return $source;
    }

    /**
     * Get a unique file path in which to save the file.
     *
     * The filename returned will be removed at the end of the request and
     * should not be relied upon to exist in subsequent requests.
     *
     * @param string $filename file name
     * @return file path
     */
    public function prepare_file($filename) {
        if (empty($filename)) {
            $filename = 'file';
        }
        return sprintf('%s/%s', make_request_directory(), $filename);
    }

    /**
     * Does this repository used to browse moodle files?
     *
     * @return bool
     */
    public function has_moodle_files() {
        return false;
    }

    /**
     * Return file URL, for most plugins, the parameter is the original
     * url, but some plugins use a file id, so we need this function to
     * convert file id to original url.
     *
     * @param string $url the url of file
     * @return string
     */
    public function get_link($url) {
        return $url;
    }

    /**
     * Downloads a file from external repository and saves it in temp dir
     *
     * Function get_file() must be implemented by repositories that support returntypes
     * FILE_INTERNAL or FILE_REFERENCE. It is invoked to pick up the file and copy it
     * to moodle. This function is not called for moodle repositories, the function
     * {@link repository::copy_to_area()} is used instead.
     *
     * This function can be overridden by subclass if the files.reference field contains
     * not just URL or if request should be done differently.
     *
     * @see curl
     * @throws file_exception when error occured
     *
     * @param string $url the content of files.reference field, in this implementaion
     * it is asssumed that it contains the string with URL of the file
     * @param string $filename filename (without path) to save the downloaded file in the
     * temporary directory, if omitted or file already exists the new filename will be generated
     * @return array with elements:
     *   path: internal location of the file
     *   url: URL to the source (from parameters)
     */
    public function get_file($url, $filename = '') {
        global $CFG;

        $path = $this->prepare_file($filename);
        $c = new curl;

        $result = $c->download_one($url, null, array('filepath' => $path, 'timeout' => $CFG->repositorygetfiletimeout));
        if ($result !== true) {
            throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Downloads the file from external repository and saves it in moodle filepool.
     * This function is different from {@link repository::sync_reference()} because it has
     * bigger request timeout and always downloads the content.
     *
     * This function is invoked when we try to unlink the file from the source and convert
     * a reference into a true copy.
     *
     * @throws exception when file could not be imported
     *
     * @param stored_file $file
     * @param int $maxbytes throw an exception if file size is bigger than $maxbytes (0 means no limit)
     */
    public function import_external_file_contents(stored_file $file, $maxbytes = 0) {
        if (!$file->is_external_file()) {
            // nothing to import if the file is not a reference
            return;
        } else if ($file->get_repository_id() != $this->id) {
            // error
            debugging('Repository instance id does not match');
            return;
        } else if ($this->has_moodle_files()) {
            // files that are references to local files are already in moodle filepool
            // just validate the size
            if ($maxbytes > 0 && $file->get_filesize() > $maxbytes) {
                $maxbytesdisplay = display_size($maxbytes);
                throw new file_exception('maxbytesfile', (object) array('file' => $file->get_filename(),
                                                                        'size' => $maxbytesdisplay));
            }
            return;
        } else {
            if ($maxbytes > 0 && $file->get_filesize() > $maxbytes) {
                // note that stored_file::get_filesize() also calls synchronisation
                $maxbytesdisplay = display_size($maxbytes);
                throw new file_exception('maxbytesfile', (object) array('file' => $file->get_filename(),
                                                                        'size' => $maxbytesdisplay));
            }
            $fs = get_file_storage();

            // If a file has been downloaded, the file record should report both a positive file
            // size, and a contenthash which does not related to empty content.
            // If thereis no file size, or the contenthash is for an empty file, then the file has
            // yet to be successfully downloaded.
            $contentexists = $file->get_filesize() && !$file->compare_to_string('');

            if (!$file->get_status() && $contentexists) {
                // we already have the content in moodle filepool and it was synchronised recently.
                // Repositories may overwrite it if they want to force synchronisation anyway!
                return;
            } else {
                // attempt to get a file
                try {
                    $fileinfo = $this->get_file($file->get_reference());
                    if (isset($fileinfo['path'])) {
                        $file->set_synchronised_content_from_file($fileinfo['path']);
                    } else {
                        throw new moodle_exception('errorwhiledownload', 'repository', '', '');
                    }
                } catch (Exception $e) {
                    if ($contentexists) {
                        // better something than nothing. We have a copy of file. It's sync time
                        // has expired but it is still very likely that it is the last version
                    } else {
                        throw($e);
                    }
                }
            }
        }
    }

    /**
     * Return size of a file in bytes.
     *
     * @param string $source encoded and serialized data of file
     * @return int file size in bytes
     */
    public function get_file_size($source) {
        // TODO MDL-33297 remove this function completely?
        $browser    = get_file_browser();
        $params     = unserialize(base64_decode($source));
        $contextid  = clean_param($params['contextid'], PARAM_INT);
        $fileitemid = clean_param($params['itemid'], PARAM_INT);
        $filename   = clean_param($params['filename'], PARAM_FILE);
        $filepath   = clean_param($params['filepath'], PARAM_PATH);
        $filearea   = clean_param($params['filearea'], PARAM_AREA);
        $component  = clean_param($params['component'], PARAM_COMPONENT);
        $context    = context::instance_by_id($contextid);
        $file_info  = $browser->get_file_info($context, $component, $filearea, $fileitemid, $filepath, $filename);
        if (!empty($file_info)) {
            $filesize = $file_info->get_filesize();
        } else {
            $filesize = null;
        }
        return $filesize;
    }

    /**
     * Return is the instance is visible
     * (is the type visible ? is the context enable ?)
     *
     * @return bool
     */
    public function is_visible() {
        $type = repository::get_type_by_id($this->options['typeid']);
        $instanceoptions = repository::static_function($type->get_typename(), 'get_instance_option_names');

        if ($type->get_visible()) {
            //if the instance is unique so it's visible, otherwise check if the instance has a enabled context
            if (empty($instanceoptions) || $type->get_contextvisibility(context::instance_by_id($this->instance->contextid))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Can the instance be edited by the current user?
     *
     * The property $readonly must not be used within this method because
     * it only controls if the options from self::get_instance_option_names()
     * can be edited.
     *
     * @return bool true if the user can edit the instance.
     * @since Moodle 2.5
     */
    public final function can_be_edited_by_user() {
        global $USER;

        // We need to be able to explore the repository.
        try {
            $this->check_capability();
        } catch (repository_exception $e) {
            return false;
        }

        $repocontext = context::instance_by_id($this->instance->contextid);
        if ($repocontext->contextlevel == CONTEXT_USER && $repocontext->instanceid != $USER->id) {
            // If the context of this instance is a user context, we need to be this user.
            return false;
        } else if ($repocontext->contextlevel == CONTEXT_MODULE && !has_capability('moodle/course:update', $repocontext)) {
            // We need to have permissions on the course to edit the instance.
            return false;
        } else if ($repocontext->contextlevel == CONTEXT_SYSTEM && !has_capability('moodle/site:config', $repocontext)) {
            // Do not meet the requirements for the context system.
            return false;
        }

        return true;
    }

    /**
     * Return the name of this instance, can be overridden.
     *
     * @return string
     */
    public function get_name() {
        if ($name = $this->instance->name) {
            return $name;
        } else {
            return get_string('pluginname', 'repository_' . $this->get_typename());
        }
    }

    /**
     * Is this repository accessing private data?
     *
     * This function should return true for the repositories which access external private
     * data from a user. This is the case for repositories such as Dropbox, Google Docs or Box.net
     * which authenticate the user and then store the auth token.
     *
     * Of course, many repositories store 'private data', but we only want to set
     * contains_private_data() to repositories which are external to Moodle and shouldn't be accessed
     * to by the users having the capability to 'login as' someone else. For instance, the repository
     * 'Private files' is not considered as private because it's part of Moodle.
     *
     * You should not set contains_private_data() to true on repositories which allow different types
     * of instances as the levels other than 'user' are, by definition, not private. Also
     * the user instances will be protected when they need to.
     *
     * @return boolean True when the repository accesses private external data.
     * @since  Moodle 2.5
     */
    public function contains_private_data() {
        return true;
    }

    /**
     * What kind of files will be in this repository?
     *
     * @return array return '*' means this repository support any files, otherwise
     *               return mimetypes of files, it can be an array
     */
    public function supported_filetypes() {
        // return array('text/plain', 'image/gif');
        return '*';
    }

    /**
     * Tells how the file can be picked from this repository
     *
     * Maximum value is FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE
     *
     * @return int
     */
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Tells how the file can be picked from this repository
     *
     * Maximum value is FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE
     *
     * @return int
     */
    public function default_returntype() {
        return FILE_INTERNAL;
    }

    /**
     * Provide repository instance information for Ajax
     *
     * @return stdClass
     */
    final public function get_meta() {
        global $CFG, $OUTPUT;
        $meta = new stdClass();
        $meta->id   = $this->id;
        $meta->name = format_string($this->get_name());
        $meta->type = $this->get_typename();
        $meta->icon = $OUTPUT->image_url('icon', 'repository_'.$meta->type)->out(false);
        $meta->supported_types = file_get_typegroup('extension', $this->supported_filetypes());
        $meta->return_types = $this->supported_returntypes();
        $meta->defaultreturntype = $this->default_returntype();
        $meta->sortorder = $this->options['sortorder'];
        return $meta;
    }

    /**
     * Create an instance for this plug-in
     *
     * @static
     * @param string $type the type of the repository
     * @param int $userid the user id
     * @param stdClass $context the context
     * @param array $params the options for this instance
     * @param int $readonly whether to create it readonly or not (defaults to not)
     * @return mixed
     */
    public static function create($type, $userid, $context, $params, $readonly=0) {
        global $CFG, $DB;
        $params = (array)$params;
        require_once($CFG->dirroot . '/repository/'. $type . '/lib.php');
        $classname = 'repository_' . $type;
        if ($repo = $DB->get_record('repository', array('type'=>$type))) {
            $record = new stdClass();
            $record->name = $params['name'];
            $record->typeid = $repo->id;
            $record->timecreated  = time();
            $record->timemodified = time();
            $record->contextid = $context->id;
            $record->readonly = $readonly;
            $record->userid    = $userid;
            $id = $DB->insert_record('repository_instances', $record);
            cache::make('core', 'repositories')->purge();
            $options = array();
            $configs = call_user_func($classname . '::get_instance_option_names');
            if (!empty($configs)) {
                foreach ($configs as $config) {
                    if (isset($params[$config])) {
                        $options[$config] = $params[$config];
                    } else {
                        $options[$config] = null;
                    }
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
     *
     * @param bool $downloadcontents
     * @return bool
     */
    final public function delete($downloadcontents = false) {
        global $DB;
        if ($downloadcontents) {
            $this->convert_references_to_local();
        } else {
            $this->remove_files();
        }
        cache::make('core', 'repositories')->purge();
        try {
            $DB->delete_records('repository_instances', array('id'=>$this->id));
            $DB->delete_records('repository_instance_config', array('instanceid'=>$this->id));
        } catch (dml_exception $ex) {
            return false;
        }
        return true;
    }

    /**
     * Delete all the instances associated to a context.
     *
     * This method is intended to be a callback when deleting
     * a course or a user to delete all the instances associated
     * to their context. The usual way to delete a single instance
     * is to use {@link self::delete()}.
     *
     * @param int $contextid context ID.
     * @param boolean $downloadcontents true to convert references to hard copies.
     * @return void
     */
    final public static function delete_all_for_context($contextid, $downloadcontents = true) {
        global $DB;
        $repoids = $DB->get_fieldset_select('repository_instances', 'id', 'contextid = :contextid', array('contextid' => $contextid));
        if ($downloadcontents) {
            foreach ($repoids as $repoid) {
                $repo = repository::get_repository_by_id($repoid, $contextid);
                $repo->convert_references_to_local();
            }
        }
        cache::make('core', 'repositories')->purge();
        $DB->delete_records_list('repository_instances', 'id', $repoids);
        $DB->delete_records_list('repository_instance_config', 'instanceid', $repoids);
    }

    /**
     * Hide/Show a repository
     *
     * @param string $hide
     * @return bool
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
     * Save settings for repository instance
     * $repo->set_option(array('api_key'=>'f2188bde132', 'name'=>'dongsheng'));
     *
     * @param array $options settings
     * @return bool
     */
    public function set_option($options = array()) {
        global $DB;

        if (!empty($options['name'])) {
            $r = new stdClass();
            $r->id   = $this->id;
            $r->name = $options['name'];
            $DB->update_record('repository_instances', $r);
            unset($options['name']);
        }
        foreach ($options as $name=>$value) {
            if ($id = $DB->get_field('repository_instance_config', 'id', array('name'=>$name, 'instanceid'=>$this->id))) {
                $DB->set_field('repository_instance_config', 'value', $value, array('id'=>$id));
            } else {
                $config = new stdClass();
                $config->instanceid = $this->id;
                $config->name   = $name;
                $config->value  = $value;
                $DB->insert_record('repository_instance_config', $config);
            }
        }
        cache::make('core', 'repositories')->purge();
        return true;
    }

    /**
     * Get settings for repository instance.
     *
     * @param string $config a specific option to get.
     * @return mixed returns an array of options. If $config is not empty, then it returns that option,
     *               or null if the option does not exist.
     */
    public function get_option($config = '') {
        global $DB;
        $cache = cache::make('core', 'repositories');
        if (($entries = $cache->get('ops:'. $this->id)) === false) {
            $entries = $DB->get_records('repository_instance_config', array('instanceid' => $this->id));
            $cache->set('ops:'. $this->id, $entries);
        }

        $ret = array();
        foreach($entries as $entry) {
            $ret[$entry->name] = $entry->value;
        }

        if (!empty($config)) {
            if (isset($ret[$config])) {
                return $ret[$config];
            } else {
                return null;
            }
        } else {
            return $ret;
        }
    }

    /**
     * Filter file listing to display specific types
     *
     * @param array $value
     * @return bool
     */
    public function filter(&$value) {
        $accepted_types = optional_param_array('accepted_types', '', PARAM_RAW);
        if (isset($value['children'])) {
            if (!empty($value['children'])) {
                $value['children'] = array_filter($value['children'], array($this, 'filter'));
            }
            return true; // always return directories
        } else {
            if ($accepted_types == '*' or empty($accepted_types)
                or (is_array($accepted_types) and in_array('*', $accepted_types))) {
                return true;
            } else {
                foreach ($accepted_types as $ext) {
                    if (preg_match('#'.$ext.'$#i', $value['title'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * See details on {@link http://docs.moodle.org/dev/Repository_plugins}
     *
     * @param string $path this parameter can a folder name, or a identification of folder
     * @param string $page the page number of file list
     * @return array the list of files, including meta infomation, containing the following keys
     *           manage, url to manage url
     *           client_id
     *           login, login form
     *           repo_id, active repository id
     *           login_btn_action, the login button action
     *           login_btn_label, the login button label
     *           total, number of results
     *           perpage, items per page
     *           page
     *           pages, total pages
     *           issearchresult, is it a search result?
     *           list, file list
     *           path, current path and parent path
     */
    public function get_listing($path = '', $page = '') {
    }


    /**
     * Prepare the breadcrumb.
     *
     * @param array $breadcrumb contains each element of the breadcrumb.
     * @return array of breadcrumb elements.
     * @since Moodle 2.3.3
     */
    protected static function prepare_breadcrumb($breadcrumb) {
        global $OUTPUT;
        $foldericon = $OUTPUT->image_url(file_folder_icon(24))->out(false);
        $len = count($breadcrumb);
        for ($i = 0; $i < $len; $i++) {
            if (is_array($breadcrumb[$i]) && !isset($breadcrumb[$i]['icon'])) {
                $breadcrumb[$i]['icon'] = $foldericon;
            } else if (is_object($breadcrumb[$i]) && !isset($breadcrumb[$i]->icon)) {
                $breadcrumb[$i]->icon = $foldericon;
            }
        }
        return $breadcrumb;
    }

    /**
     * Prepare the file/folder listing.
     *
     * @param array $list of files and folders.
     * @return array of files and folders.
     * @since Moodle 2.3.3
     */
    protected static function prepare_list($list) {
        global $OUTPUT;
        $foldericon = $OUTPUT->image_url(file_folder_icon(24))->out(false);

        // Reset the array keys because non-numeric keys will create an object when converted to JSON.
        $list = array_values($list);

        $len = count($list);
        for ($i = 0; $i < $len; $i++) {
            if (is_object($list[$i])) {
                $file = (array)$list[$i];
                $converttoobject = true;
            } else {
                $file =& $list[$i];
                $converttoobject = false;
            }

            if (isset($file['source'])) {
                $file['sourcekey'] = sha1($file['source'] . self::get_secret_key() . sesskey());
            }

            if (isset($file['size'])) {
                $file['size'] = (int)$file['size'];
                $file['size_f'] = display_size($file['size']);
            }
            if (isset($file['license']) && get_string_manager()->string_exists($file['license'], 'license')) {
                $file['license_f'] = get_string($file['license'], 'license');
            }
            if (isset($file['image_width']) && isset($file['image_height'])) {
                $a = array('width' => $file['image_width'], 'height' => $file['image_height']);
                $file['dimensions'] = get_string('imagesize', 'repository', (object)$a);
            }
            foreach (array('date', 'datemodified', 'datecreated') as $key) {
                if (!isset($file[$key]) && isset($file['date'])) {
                    $file[$key] = $file['date'];
                }
                if (isset($file[$key])) {
                    // must be UNIX timestamp
                    $file[$key] = (int)$file[$key];
                    if (!$file[$key]) {
                        unset($file[$key]);
                    } else {
                        $file[$key.'_f'] = userdate($file[$key], get_string('strftimedatetime', 'langconfig'));
                        $file[$key.'_f_s'] = userdate($file[$key], get_string('strftimedatetimeshort', 'langconfig'));
                    }
                }
            }
            $isfolder = (array_key_exists('children', $file) || (isset($file['type']) && $file['type'] == 'folder'));
            $filename = null;
            if (isset($file['title'])) {
                $filename = $file['title'];
            }
            else if (isset($file['fullname'])) {
                $filename = $file['fullname'];
            }
            if (!isset($file['mimetype']) && !$isfolder && $filename) {
                $file['mimetype'] = get_mimetype_description(array('filename' => $filename));
            }
            if (!isset($file['icon'])) {
                if ($isfolder) {
                    $file['icon'] = $foldericon;
                } else if ($filename) {
                    $file['icon'] = $OUTPUT->image_url(file_extension_icon($filename, 24))->out(false);
                }
            }

            // Recursively loop over children.
            if (isset($file['children'])) {
                $file['children'] = self::prepare_list($file['children']);
            }

            // Convert the array back to an object.
            if ($converttoobject) {
                $list[$i] = (object)$file;
            }
        }
        return $list;
    }

    /**
     * Prepares list of files before passing it to AJAX, makes sure data is in the correct
     * format and stores formatted values.
     *
     * @param array|stdClass $listing result of get_listing() or search() or file_get_drafarea_files()
     * @return array
     */
    public static function prepare_listing($listing) {
        $wasobject = false;
        if (is_object($listing)) {
            $listing = (array) $listing;
            $wasobject = true;
        }

        // Prepare the breadcrumb, passed as 'path'.
        if (isset($listing['path']) && is_array($listing['path'])) {
            $listing['path'] = self::prepare_breadcrumb($listing['path']);
        }

        // Prepare the listing of objects.
        if (isset($listing['list']) && is_array($listing['list'])) {
            $listing['list'] = self::prepare_list($listing['list']);
        }

        // Convert back to an object.
        if ($wasobject) {
            $listing = (object) $listing;
        }
        return $listing;
    }

    /**
     * Search files in repository
     * When doing global search, $search_text will be used as
     * keyword.
     *
     * @param string $search_text search key word
     * @param int $page page
     * @return mixed see {@link repository::get_listing()}
     */
    public function search($search_text, $page = 0) {
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
     * @return bool
     */
    public function check_login(){
        return true;
    }


    /**
     * Show the login screen, if required
     *
     * @return string
     */
    public function print_login(){
        return $this->get_listing();
    }

    /**
     * Show the search screen, if required
     *
     * @return string
     */
    public function print_search() {
        global $PAGE;
        $renderer = $PAGE->get_renderer('core', 'files');
        return $renderer->repository_default_searchform();
    }

    /**
     * For oauth like external authentication, when external repository direct user back to moodle,
     * this function will be called to set up token and token_secret
     */
    public function callback() {
    }

    /**
     * is it possible to do glboal search?
     *
     * @return bool
     */
    public function global_search() {
        return false;
    }

    /**
     * Defines operations that happen occasionally on cron
     *
     * @return bool
     */
    public function cron() {
        return true;
    }

    /**
     * function which is run when the type is created (moodle administrator add the plugin)
     *
     * @return bool success or fail?
     */
    public static function plugin_init() {
        return true;
    }

    /**
     * Edit/Create Admin Settings Moodle form
     *
     * @param moodleform $mform Moodle form (passed by reference)
     * @param string $classname repository class name
     */
    public static function type_config_form($mform, $classname = 'repository') {
        $instnaceoptions = call_user_func(array($classname, 'get_instance_option_names'), $mform, $classname);
        if (empty($instnaceoptions)) {
            // this plugin has only one instance
            // so we need to give it a name
            // it can be empty, then moodle will look for instance name from language string
            $mform->addElement('text', 'pluginname', get_string('pluginname', 'repository'), array('size' => '40'));
            $mform->addElement('static', 'pluginnamehelp', '', get_string('pluginnamehelp', 'repository'));
            $mform->setType('pluginname', PARAM_TEXT);
        }
    }

    /**
     * Validate Admin Settings Moodle form
     *
     * @static
     * @param moodleform $mform Moodle form (passed by reference)
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $errors array of ("fieldname"=>errormessage) of errors
     * @return array array of errors
     */
    public static function type_form_validation($mform, $data, $errors) {
        return $errors;
    }


    /**
     * Edit/Create Instance Settings Moodle form
     *
     * @param moodleform $mform Moodle form (passed by reference)
     */
    public static function instance_config_form($mform) {
    }

    /**
     * Return names of the general options.
     * By default: no general option name
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array('pluginname');
    }

    /**
     * Return names of the instance options.
     * By default: no instance option name
     *
     * @return array
     */
    public static function get_instance_option_names() {
        return array();
    }

    /**
     * Validate repository plugin instance form
     *
     * @param moodleform $mform moodle form
     * @param array $data form data
     * @param array $errors errors
     * @return array errors
     */
    public static function instance_form_validation($mform, $data, $errors) {
        return $errors;
    }

    /**
     * Create a shorten filename
     *
     * @param string $str filename
     * @param int $maxlength max file name length
     * @return string short filename
     */
    public function get_short_filename($str, $maxlength) {
        if (core_text::strlen($str) >= $maxlength) {
            return trim(core_text::substr($str, 0, $maxlength)).'...';
        } else {
            return $str;
        }
    }

    /**
     * Overwrite an existing file
     *
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @param string $newfilepath
     * @param string $newfilename
     * @return bool
     */
    public static function overwrite_existing_draftfile($itemid, $filepath, $filename, $newfilepath, $newfilename) {
        global $USER;
        $fs = get_file_storage();
        $user_context = context_user::instance($USER->id);
        if ($file = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $filepath, $filename)) {
            if ($tempfile = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $newfilepath, $newfilename)) {
                // Remember original file source field.
                $source = @unserialize($file->get_source());
                // Remember the original sortorder.
                $sortorder = $file->get_sortorder();
                if ($tempfile->is_external_file()) {
                    // New file is a reference. Check that existing file does not have any other files referencing to it
                    if (isset($source->original) && $fs->search_references_count($source->original)) {
                        return (object)array('error' => get_string('errordoublereference', 'repository'));
                    }
                }
                // delete existing file to release filename
                $file->delete();
                // create new file
                $newfile = $fs->create_file_from_storedfile(array('filepath'=>$filepath, 'filename'=>$filename), $tempfile);
                // Preserve original file location (stored in source field) for handling references
                if (isset($source->original)) {
                    if (!($newfilesource = @unserialize($newfile->get_source()))) {
                        $newfilesource = new stdClass();
                    }
                    $newfilesource->original = $source->original;
                    $newfile->set_source(serialize($newfilesource));
                }
                $newfile->set_sortorder($sortorder);
                // remove temp file
                $tempfile->delete();
                return true;
            }
        }
        return false;
    }

    /**
     * Updates a file in draft filearea.
     *
     * This function can only update fields filepath, filename, author, license.
     * If anything (except filepath) is updated, timemodified is set to current time.
     * If filename or filepath is updated the file unconnects from it's origin
     * and therefore all references to it will be converted to copies when
     * filearea is saved.
     *
     * @param int $draftid
     * @param string $filepath path to the directory containing the file, or full path in case of directory
     * @param string $filename name of the file, or '.' in case of directory
     * @param array $updatedata array of fields to change (only filename, filepath, license and/or author can be updated)
     * @throws moodle_exception if for any reason file can not be updated (file does not exist, target already exists, etc.)
     */
    public static function update_draftfile($draftid, $filepath, $filename, $updatedata) {
        global $USER;
        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);
        // make sure filename and filepath are present in $updatedata
        $updatedata = $updatedata + array('filepath' => $filepath, 'filename' => $filename);
        $filemodified = false;
        if (!$file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, $filename)) {
            if ($filename === '.') {
                throw new moodle_exception('foldernotfound', 'repository');
            } else {
                throw new moodle_exception('filenotfound', 'error');
            }
        }
        if (!$file->is_directory()) {
            // This is a file
            if ($updatedata['filepath'] !== $filepath || $updatedata['filename'] !== $filename) {
                // Rename/move file: check that target file name does not exist.
                if ($fs->file_exists($usercontext->id, 'user', 'draft', $draftid, $updatedata['filepath'], $updatedata['filename'])) {
                    throw new moodle_exception('fileexists', 'repository');
                }
                if (($filesource = @unserialize($file->get_source())) && isset($filesource->original)) {
                    unset($filesource->original);
                    $file->set_source(serialize($filesource));
                }
                $file->rename($updatedata['filepath'], $updatedata['filename']);
                // timemodified is updated only when file is renamed and not updated when file is moved.
                $filemodified = $filemodified || ($updatedata['filename'] !== $filename);
            }
            if (array_key_exists('license', $updatedata) && $updatedata['license'] !== $file->get_license()) {
                // Update license and timemodified.
                $file->set_license($updatedata['license']);
                $filemodified = true;
            }
            if (array_key_exists('author', $updatedata) && $updatedata['author'] !== $file->get_author()) {
                // Update author and timemodified.
                $file->set_author($updatedata['author']);
                $filemodified = true;
            }
            // Update timemodified:
            if ($filemodified) {
                $file->set_timemodified(time());
            }
        } else {
            // This is a directory - only filepath can be updated for a directory (it was moved).
            if ($updatedata['filepath'] === $filepath) {
                // nothing to update
                return;
            }
            if ($fs->file_exists($usercontext->id, 'user', 'draft', $draftid, $updatedata['filepath'], '.')) {
                // bad luck, we can not rename if something already exists there
                throw new moodle_exception('folderexists', 'repository');
            }
            $xfilepath = preg_quote($filepath, '|');
            if (preg_match("|^$xfilepath|", $updatedata['filepath'])) {
                // we can not move folder to it's own subfolder
                throw new moodle_exception('folderrecurse', 'repository');
            }

            // If directory changed the name, update timemodified.
            $filemodified = (basename(rtrim($file->get_filepath(), '/')) !== basename(rtrim($updatedata['filepath'], '/')));

            // Now update directory and all children.
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftid);
            foreach ($files as $f) {
                if (preg_match("|^$xfilepath|", $f->get_filepath())) {
                    $path = preg_replace("|^$xfilepath|", $updatedata['filepath'], $f->get_filepath());
                    if (($filesource = @unserialize($f->get_source())) && isset($filesource->original)) {
                        // unset original so the references are not shown any more
                        unset($filesource->original);
                        $f->set_source(serialize($filesource));
                    }
                    $f->rename($path, $f->get_filename());
                    if ($filemodified && $f->get_filepath() === $updatedata['filepath'] && $f->get_filename() === $filename) {
                        $f->set_timemodified(time());
                    }
                }
            }
        }
    }

    /**
     * Delete a temp file from draft area
     *
     * @param int $draftitemid
     * @param string $filepath
     * @param string $filename
     * @return bool
     */
    public static function delete_tempfile_from_draft($draftitemid, $filepath, $filename) {
        global $USER;
        $fs = get_file_storage();
        $user_context = context_user::instance($USER->id);
        if ($file = $fs->get_file($user_context->id, 'user', 'draft', $draftitemid, $filepath, $filename)) {
            $file->delete();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Find all external files in this repo and import them
     */
    public function convert_references_to_local() {
        $fs = get_file_storage();
        $files = $fs->get_external_files($this->id);
        foreach ($files as $storedfile) {
            $fs->import_external_file($storedfile);
        }
    }

    /**
     * Find all external files linked to this repository and delete them.
     */
    public function remove_files() {
        $fs = get_file_storage();
        $files = $fs->get_external_files($this->id);
        foreach ($files as $storedfile) {
            $storedfile->delete();
        }
    }

    /**
     * Function repository::reset_caches() is deprecated, cache is handled by MUC now.
     * @deprecated since Moodle 2.6 MDL-42016 - please do not use this function any more.
     */
    public static function reset_caches() {
        throw new coding_exception('Function repository::reset_caches() can not be used any more, cache is handled by MUC now.');
    }

    /**
     * Function repository::sync_external_file() is deprecated. Use repository::sync_reference instead
     *
     * @deprecated since Moodle 2.6 MDL-42016 - please do not use this function any more.
     * @see repository::sync_reference()
     */
    public static function sync_external_file($file, $resetsynchistory = false) {
        throw new coding_exception('Function repository::sync_external_file() can not be used any more. ' .
            'Use repository::sync_reference instead.');
    }

    /**
     * Performs synchronisation of an external file if the previous one has expired.
     *
     * This function must be implemented for external repositories supporting
     * FILE_REFERENCE, it is called for existing aliases when their filesize,
     * contenthash or timemodified are requested. It is not called for internal
     * repositories (see {@link repository::has_moodle_files()}), references to
     * internal files are updated immediately when source is modified.
     *
     * Referenced files may optionally keep their content in Moodle filepool (for
     * thumbnail generation or to be able to serve cached copy). In this
     * case both contenthash and filesize need to be synchronized. Otherwise repositories
     * should use contenthash of empty file and correct filesize in bytes.
     *
     * Note that this function may be run for EACH file that needs to be synchronised at the
     * moment. If anything is being downloaded or requested from external sources there
     * should be a small timeout. The synchronisation is performed to update the size of
     * the file and/or to update image and re-generated image preview. There is nothing
     * fatal if syncronisation fails but it is fatal if syncronisation takes too long
     * and hangs the script generating a page.
     *
     * Note: If you wish to call $file->get_filesize(), $file->get_contenthash() or
     * $file->get_timemodified() make sure that recursion does not happen.
     *
     * Called from {@link stored_file::sync_external_file()}
     *
     * @uses stored_file::set_missingsource()
     * @uses stored_file::set_synchronized()
     * @param stored_file $file
     * @return bool false when file does not need synchronisation, true if it was synchronised
     */
    public function sync_reference(stored_file $file) {
        if ($file->get_repository_id() != $this->id) {
            // This should not really happen because the function can be called from stored_file only.
            return false;
        }

        if ($this->has_moodle_files()) {
            // References to local files need to be synchronised only once.
            // Later they will be synchronised automatically when the source is changed.
            if ($file->get_referencelastsync()) {
                return false;
            }
            $fs = get_file_storage();
            $params = file_storage::unpack_reference($file->get_reference(), true);
            if (!is_array($params) || !($storedfile = $fs->get_file($params['contextid'],
                    $params['component'], $params['filearea'], $params['itemid'], $params['filepath'],
                    $params['filename']))) {
                $file->set_missingsource();
            } else {
                $file->set_synchronized($storedfile->get_contenthash(), $storedfile->get_filesize(), 0, $storedfile->get_timemodified());
            }
            return true;
        }

        return false;
    }

    /**
     * Build draft file's source field
     *
     * {@link file_restore_source_field_from_draft_file()}
     * XXX: This is a hack for file manager (MDL-28666)
     * For newly created  draft files we have to construct
     * source filed in php serialized data format.
     * File manager needs to know the original file information before copying
     * to draft area, so we append these information in mdl_files.source field
     *
     * @param string $source
     * @return string serialised source field
     */
    public static function build_source_field($source) {
        $sourcefield = new stdClass;
        $sourcefield->source = $source;
        return serialize($sourcefield);
    }

    /**
     * Prepares the repository to be cached. Implements method from cacheable_object interface.
     *
     * @return array
     */
    public function prepare_to_cache() {
        return array(
            'class' => get_class($this),
            'id' => $this->id,
            'ctxid' => $this->context->id,
            'options' => $this->options,
            'readonly' => $this->readonly
        );
    }

    /**
     * Restores the repository from cache. Implements method from cacheable_object interface.
     *
     * @return array
     */
    public static function wake_from_cache($data) {
        $classname = $data['class'];
        return new $classname($data['id'], $data['ctxid'], $data['options'], $data['readonly']);
    }

    /**
     * Gets a file relative to this file in the repository and sends it to the browser.
     * Used to allow relative file linking within a repository without creating file records
     * for linked files
     *
     * Repositories that overwrite this must be very careful - see filesystem repository for example.
     *
     * @param stored_file $mainfile The main file we are trying to access relative files for.
     * @param string $relativepath the relative path to the file we are trying to access.
     *
     */
    public function send_relative_file(stored_file $mainfile, $relativepath) {
        // This repository hasn't implemented this so send_file_not_found.
        send_file_not_found();
    }

    /**
     * helper function to check if the repository supports send_relative_file.
     *
     * @return true|false
     */
    public function supports_relative_file() {
        return false;
    }

    /**
     * Helper function to indicate if this repository uses post requests for uploading files.
     *
     * @deprecated since Moodle 3.2, 3.1.1, 3.0.5
     * @return bool
     */
    public function uses_post_requests() {
        debugging('The method repository::uses_post_requests() is deprecated and must not be used anymore.', DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Generate a secret key to be used for passing sensitive information around.
     *
     * @return string repository secret key.
     */
    final static public function get_secret_key() {
        global $CFG;

        if (!isset($CFG->reposecretkey)) {
            set_config('reposecretkey', time() . random_string(32));
        }
        return $CFG->reposecretkey;
    }
}

/**
 * Exception class for repository api
 *
 * @since Moodle 2.0
 * @package   core_repository
 * @copyright 2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_exception extends moodle_exception {
}

/**
 * This is a class used to define a repository instance form
 *
 * @since Moodle 2.0
 * @package   core_repository
 * @copyright 2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class repository_instance_form extends moodleform {
    /** @var stdClass repository instance */
    protected $instance;
    /** @var string repository plugin type */
    protected $plugin;

    /**
     * Added defaults to moodle form
     */
    protected function add_defaults() {
        $mform =& $this->_form;
        $strrequired = get_string('required');

        $mform->addElement('hidden', 'edit',  ($this->instance) ? $this->instance->id : 0);
        $mform->setType('edit', PARAM_INT);
        $mform->addElement('hidden', 'new',   $this->plugin);
        $mform->setType('new', PARAM_ALPHANUMEXT);
        $mform->addElement('hidden', 'plugin', $this->plugin);
        $mform->setType('plugin', PARAM_PLUGIN);
        $mform->addElement('hidden', 'typeid', $this->typeid);
        $mform->setType('typeid', PARAM_INT);
        $mform->addElement('hidden', 'contextid', $this->contextid);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="100" size="30"');
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
    }

    /**
     * Define moodle form elements
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

        $this->add_defaults();

        // Add instance config options.
        $result = repository::static_function($this->plugin, 'instance_config_form', $mform);
        if ($result === false) {
            // Remove the name element if no other config options.
            $mform->removeElement('name');
        }
        if ($this->instance) {
            $data = array();
            $data['name'] = $this->instance->name;
            if (!$this->instance->readonly) {
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

        if ($result === false) {
            $mform->addElement('cancel');
        } else {
            $this->add_action_buttons(true, get_string('save','repository'));
        }
    }

    /**
     * Validate moodle form data
     *
     * @param array $data form data
     * @param array $files files in form
     * @return array errors
     */
    public function validation($data, $files) {
        global $DB;
        $errors = array();
        $plugin = $this->_customdata['plugin'];
        $instance = (isset($this->_customdata['instance'])
                && is_subclass_of($this->_customdata['instance'], 'repository'))
            ? $this->_customdata['instance'] : null;

        if (!$instance) {
            $errors = repository::static_function($plugin, 'instance_form_validation', $this, $data, $errors);
        } else {
            $errors = $instance->instance_form_validation($this, $data, $errors);
        }

        $sql = "SELECT count('x')
                  FROM {repository_instances} i, {repository} r
                 WHERE r.type=:plugin AND r.id=i.typeid AND i.name=:name AND i.contextid=:contextid";
        $params = array('name' => $data['name'], 'plugin' => $this->plugin, 'contextid' => $this->contextid);
        if ($instance) {
            $sql .= ' AND i.id != :instanceid';
            $params['instanceid'] = $instance->id;
        }
        if ($DB->count_records_sql($sql, $params) > 0) {
            $errors['name'] = get_string('erroruniquename', 'repository');
        }

        return $errors;
    }
}

/**
 * This is a class used to define a repository type setting form
 *
 * @since Moodle 2.0
 * @package   core_repository
 * @copyright 2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class repository_type_form extends moodleform {
    /** @var stdClass repository instance */
    protected $instance;
    /** @var string repository plugin name */
    protected $plugin;
    /** @var string action */
    protected $action;

    /**
     * Definition of the moodleform
     */
    public function definition() {
        global $CFG;
        // type of plugin, string
        $this->plugin = $this->_customdata['plugin'];
        $this->instance = (isset($this->_customdata['instance'])
                && is_a($this->_customdata['instance'], 'repository_type'))
            ? $this->_customdata['instance'] : null;

        $this->action = $this->_customdata['action'];
        $this->pluginname = $this->_customdata['pluginname'];
        $mform =& $this->_form;
        $strrequired = get_string('required');

        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_TEXT);
        $mform->addElement('hidden', 'repos', $this->plugin);
        $mform->setType('repos', PARAM_PLUGIN);

        // let the plugin add its specific fields
        $classname = 'repository_' . $this->plugin;
        require_once($CFG->dirroot . '/repository/' . $this->plugin . '/lib.php');
        //add "enable course/user instances" checkboxes if multiple instances are allowed
        $instanceoptionnames = repository::static_function($this->plugin, 'get_instance_option_names');

        $result = call_user_func(array($classname, 'type_config_form'), $mform, $classname);

        if (!empty($instanceoptionnames)) {
            $sm = get_string_manager();
            $component = 'repository';
            if ($sm->string_exists('enablecourseinstances', 'repository_' . $this->plugin)) {
                $component .= ('_' . $this->plugin);
            }
            $mform->addElement('checkbox', 'enablecourseinstances', get_string('enablecourseinstances', $component));
            $mform->setType('enablecourseinstances', PARAM_BOOL);

            $component = 'repository';
            if ($sm->string_exists('enableuserinstances', 'repository_' . $this->plugin)) {
                $component .= ('_' . $this->plugin);
            }
            $mform->addElement('checkbox', 'enableuserinstances', get_string('enableuserinstances', $component));
            $mform->setType('enableuserinstances', PARAM_BOOL);
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
            // XXX: set plugin name for plugins which doesn't have muliti instances
            if (empty($instanceoptionnames)){
                $data['pluginname'] = $this->pluginname;
            }
            $this->set_data($data);
        }

        $this->add_action_buttons(true, get_string('save','repository'));
    }

    /**
     * Validate moodle form data
     *
     * @param array $data moodle form data
     * @param array $files
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = array();
        $plugin = $this->_customdata['plugin'];
        $instance = (isset($this->_customdata['instance'])
                && is_subclass_of($this->_customdata['instance'], 'repository'))
            ? $this->_customdata['instance'] : null;
        if (!$instance) {
            $errors = repository::static_function($plugin, 'type_form_validation', $this, $data, $errors);
        } else {
            $errors = $instance->type_form_validation($this, $data, $errors);
        }

        return $errors;
    }
}

/**
 * Generate all options needed by filepicker
 *
 * @param array $args including following keys
 *          context
 *          accepted_types
 *          return_types
 *
 * @return array the list of repository instances, including meta infomation, containing the following keys
 *          externallink
 *          repositories
 *          accepted_types
 */
function initialise_filepicker($args) {
    global $CFG, $USER, $PAGE;
    static $templatesinitialized = array();
    require_once($CFG->libdir . '/licenselib.php');

    $return = new stdClass();

    $licenses = license_manager::get_licenses();

    if (!empty($CFG->sitedefaultlicense)) {
        $return->defaultlicense = $CFG->sitedefaultlicense;
    }

    $return->licenses = $licenses;

    $return->author = fullname($USER);

    if (empty($args->context)) {
        $context = $PAGE->context;
    } else {
        $context = $args->context;
    }
    $disable_types = array();
    if (!empty($args->disable_types)) {
        $disable_types = $args->disable_types;
    }

    $user_context = context_user::instance($USER->id);

    list($context, $course, $cm) = get_context_info_array($context->id);
    $contexts = array($user_context, context_system::instance());
    if (!empty($course)) {
        // adding course context
        $contexts[] = context_course::instance($course->id);
    }
    $externallink = (int)get_config(null, 'repositoryallowexternallinks');
    $repositories = repository::get_instances(array(
        'context'=>$contexts,
        'currentcontext'=> $context,
        'accepted_types'=>$args->accepted_types,
        'return_types'=>$args->return_types,
        'disable_types'=>$disable_types
    ));

    $return->repositories = array();

    if (empty($externallink)) {
        $return->externallink = false;
    } else {
        $return->externallink = true;
    }

    $return->rememberuserlicensepref = (bool) get_config(null, 'rememberuserlicensepref');

    $return->userprefs = array();
    $return->userprefs['recentrepository'] = get_user_preferences('filepicker_recentrepository', '');
    $return->userprefs['recentlicense'] = get_user_preferences('filepicker_recentlicense', '');
    $return->userprefs['recentviewmode'] = get_user_preferences('filepicker_recentviewmode', '');

    user_preference_allow_ajax_update('filepicker_recentrepository', PARAM_INT);
    user_preference_allow_ajax_update('filepicker_recentlicense', PARAM_SAFEDIR);
    user_preference_allow_ajax_update('filepicker_recentviewmode', PARAM_INT);


    // provided by form element
    $return->accepted_types = file_get_typegroup('extension', $args->accepted_types);
    $return->return_types = $args->return_types;
    $templates = array();
    foreach ($repositories as $repository) {
        $meta = $repository->get_meta();
        // Please note that the array keys for repositories are used within
        // JavaScript a lot, the key NEEDS to be the repository id.
        $return->repositories[$repository->id] = $meta;
        // Register custom repository template if it has one
        if(method_exists($repository, 'get_upload_template') && !array_key_exists('uploadform_' . $meta->type, $templatesinitialized)) {
            $templates['uploadform_' . $meta->type] = $repository->get_upload_template();
            $templatesinitialized['uploadform_' . $meta->type] = true;
        }
    }
    if (!array_key_exists('core', $templatesinitialized)) {
        // we need to send each filepicker template to the browser just once
        $fprenderer = $PAGE->get_renderer('core', 'files');
        $templates = array_merge($templates, $fprenderer->filepicker_js_templates());
        $templatesinitialized['core'] = true;
    }
    if (sizeof($templates)) {
        $PAGE->requires->js_init_call('M.core_filepicker.set_templates', array($templates), true);
    }
    return $return;
}

/**
 * Convenience function to handle deletion of files.
 *
 * @param object $context The context where the delete is called
 * @param string $component component
 * @param string $filearea filearea
 * @param int $itemid the item id
 * @param array $files Array of files object with each item having filename/filepath as values
 * @return array $return Array of strings matching up to the parent directory of the deleted files
 * @throws coding_exception
 */
function repository_delete_selected_files($context, string $component, string $filearea, $itemid, array $files) {
    $fs = get_file_storage();
    $return = [];

    foreach ($files as $selectedfile) {
        $filename = clean_filename($selectedfile->filename);
        $filepath = clean_param($selectedfile->filepath, PARAM_PATH);
        $filepath = file_correct_filepath($filepath);

        if ($storedfile = $fs->get_file($context->id, $component, $filearea, $itemid, $filepath, $filename)) {
            $parentpath = $storedfile->get_parent_directory()->get_filepath();
            if ($storedfile->is_directory()) {
                $files = $fs->get_directory_files($context->id, $component, $filearea, $itemid, $filepath, true);
                foreach ($files as $file) {
                    $file->delete();
                }
                $storedfile->delete();
                $return[$parentpath] = "";
            } else {
                if ($result = $storedfile->delete()) {
                    $return[$parentpath] = "";
                }
            }
        }
    }

    return $return;
}

/**
 * Convenience function to handle deletion of files.
 *
 * @param object $context The context where the delete is called
 * @param string $component component
 * @param string $filearea filearea
 * @param int $itemid the item id
 * @param array $files Array of files object with each item having filename/filepath as values
 * @return array $return Array of strings matching up to the parent directory of the deleted files
 * @throws coding_exception
 */
function repository_download_selected_files($context, string $component, string $filearea, $itemid, array $files) {
    global $USER;
    $return = false;

    $zipper = get_file_packer('application/zip');
    $fs = get_file_storage();
    // Archive compressed file to an unused draft area.
    $newdraftitemid = file_get_unused_draft_itemid();
    $filestoarchive = [];

    foreach ($files as $selectedfile) {
        $filename = $selectedfile->filename ? clean_filename($selectedfile->filename) : '.'; // Default to '.' for root.
        $filepath = clean_param($selectedfile->filepath, PARAM_PATH); // Default to '/' for downloadall.
        $filepath = file_correct_filepath($filepath);
        $area = file_get_draft_area_info($itemid, $filepath);
        if ($area['filecount'] == 0 && $area['foldercount'] == 0) {
            continue;
        }

        $storedfile = $fs->get_file($context->id, $component, $filearea, $itemid, $filepath, $filename);
        // If it is empty we are downloading a directory.
        $archivefile = $storedfile->get_filename();
        if (!$filename || $filename == '.' ) {
            $foldername = explode('/', trim($filepath, '/'));
            $folder = trim(array_pop($foldername), '/');
            $archivefile = $folder ?? '/';
        }

        $filestoarchive[$archivefile] = $storedfile;
    }
    $zippedfile = get_string('files') . '.zip';
    if ($newfile =
        $zipper->archive_to_storage(
            $filestoarchive,
            $context->id,
            $component,
            $filearea,
            $newdraftitemid,
            "/",
            $zippedfile, $USER->id)
    ) {
        $return = new stdClass();
        $return->fileurl = moodle_url::make_draftfile_url($newdraftitemid, '/', $zippedfile)->out();
        $return->filepath = $filepath;
    }

    return $return;
}
