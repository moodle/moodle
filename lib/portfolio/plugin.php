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
 * This file contains the base classes for portfolio plugins to inherit from:
 *
 * portfolio_plugin_pull_base and portfolio_plugin_push_base
 * which both in turn inherit from portfolio_plugin_base.
 *
 * @package    core_portfolio
 * @copyright  2008 Penny Leach <penny@catalyst.net.nz>,
 *             Martin Dougiamas <http://dougiamas.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The base class for portfolio plugins.
 *
 * All plugins must subclass this
 * either via portfolio_plugin_pull_base or portfolio_plugin_push_base
 * @see portfolio_plugin_pull_base
 * @see portfolio_plugin_push_base
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class portfolio_plugin_base {

    /** @var bool whether this object needs writing out to the database */
    protected $dirty;

    /** @var integer id of instance */
    protected $id;

    /** @var string name of instance */
    protected $name;

    /** @var string plugin this instance belongs to */
    protected $plugin;

    /** @var bool whether this instance is visible or not */
    protected $visible;

    /** @var stdClass admin configured config use {@see set_config} and {@see get_config} to access */
    protected $config;

    /** @var array user config cache. keyed on userid and then on config field => value use {@link get_user_config} and {@link set_user_config} to access. */
    protected $userconfig;

    /** @var array export config during export use {@link get_export_config} and {@link set export_config} to access. */
    protected $exportconfig;

    /** @var stdClass user currently exporting data */
    protected $user;

    /** @var stdClass a reference to the exporter object */
    protected $exporter;

    /**
     * Array of formats this portfolio supports
     * the intersection of what this function returns
     * and what the caller supports will be used.
     * Use the constants PORTFOLIO_FORMAT_*
     *
     * @return array list of formats
     */
    public function supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_RICH);
    }

    /**
     * Override this if you are supporting the 'file' type (or a subformat)
     * but have restrictions on mimetypes
     *
     * @param string $mimetype file type or subformat
     * @return bool
     */
    public static function file_mime_check($mimetype) {
        return true;
    }


    /**
     * How long does this reasonably expect to take..
     * Should we offer the user the option to wait..
     * This is deliberately nonstatic so it can take filesize into account
     *
     * @param string $callertime - what the caller thinks
     *                             the portfolio plugin instance
     *                             is given the final say
     *                             because it might be (for example) download.
     */
    public abstract function expected_time($callertime);

    /**
     * Is this plugin push or pull.
     * If push, cleanup will be called directly after send_package
     * If not, cleanup will be called after portfolio/file.php is requested
     */
    public abstract function is_push();

    /**
     * Returns the user-friendly name for this plugin.
     * Usually just get_string('pluginname', 'portfolio_something')
     */
    public static function get_name() {
        throw new coding_exception('get_name() method needs to be overridden in each subclass of portfolio_plugin_base');
    }

    /**
     * Check sanity of plugin.
     * If this function returns something non empty, ALL instances of your plugin
     * will be set to invisble and not be able to be set back until it's fixed
     *
     * @return string|int|bool - string = error string KEY (must be inside portfolio_$yourplugin) or 0/false if you're ok
     */
    public static function plugin_sanity_check() {
        return 0;
    }

    /**
     * Check sanity of instances.
     * If this function returns something non empty, the instance will be
     * set to invislbe and not be able to be set back until it's fixed.
     *
     * @return int|string|bool - string = error string KEY (must be inside portfolio_$yourplugin) or 0/false if you're ok
     */
    public function instance_sanity_check() {
        return 0;
    }

    /**
     * Does this plugin need any configuration by the administrator?
     * If you override this to return true,
     * you <b>must</b> implement admin_config_form.
     * @see admin_config_form
     *
     * @return bool
     */
    public static function has_admin_config() {
        return false;
    }

    /**
     * Can this plugin be configured by the user in their profile?
     * If you override this to return true,
     * you <b>must</b> implement user_config_form
     * @see user_config_form
     *
     * @return bool
     */
    public function has_user_config() {
        return false;
    }

    /**
     * Does this plugin need configuration during export time?
     * If you override this to return true,
     * you <b>must</b> implement export_config_form.
     * @see export_config_form
     *
     * @return bool
     */
    public function has_export_config() {
        return false;
    }

    /**
     * Just like the moodle form validation function.
     * This is passed in the data array from the form
     * and if a non empty array is returned, form processing will stop.
     *
     * @param array $data data from form.
     */
    public function export_config_validation(array $data) {}

    /**
     * Just like the moodle form validation function.
     * This is passed in the data array from the form
     * and if a non empty array is returned, form processing will stop.
     *
     * @param array $data data from form.
     */
    public function user_config_validation(array $data) {}

    /**
     * Sets the export time config from the moodle form.
     * You can also use this to set export config that
     * isn't actually controlled by the user.
     * Eg: things that your subclasses want to keep in state
     * across the export.
     * Keys must be in get_allowed_export_config
     * This is deliberately not final (see googledocs plugin)
     * @see get_allowed_export_config
     *
     * @param array $config named array of config items to set.
     */
    public function set_export_config($config) {
        $allowed = array_merge(
            array('wait', 'hidewait', 'format', 'hideformat'),
            $this->get_allowed_export_config()
        );
        foreach ($config as $key => $value) {
            if (!in_array($key, $allowed)) {
                $a = (object)array('property' => $key, 'class' => get_class($this));
                throw new portfolio_export_exception($this->get('exporter'), 'invalidexportproperty', 'portfolio', null, $a);
            }
            $this->exportconfig[$key] = $value;
        }
    }

    /**
     * Gets an export time config value.
     * Subclasses should not override this.
     *
     * @param string $key field to fetch
     * @return null|string config value
     */
    public final function get_export_config($key) {
        $allowed = array_merge(
            array('hidewait', 'wait', 'format', 'hideformat'),
            $this->get_allowed_export_config()
        );
        if (!in_array($key, $allowed)) {
            $a = (object)array('property' => $key, 'class' => get_class($this));
            throw new portfolio_export_exception($this->get('exporter'), 'invalidexportproperty', 'portfolio', null, $a);
        }
        if (!array_key_exists($key, $this->exportconfig)) {
            return null;
        }
        return $this->exportconfig[$key];
    }

    /**
     * After the user submits their config,
     * they're given a confirm screen
     * summarising what they've chosen.
     * This function should return a table of nice strings => values
     * of what they've chosen
     * to be displayed in a table.
     *
     * @return bool
     */
    public function get_export_summary() {
        return false;
    }

    /**
     * Called after the caller has finished having control
     * of its prepare_package function.
     * This function should read all the files from the portfolio
     * working file area and zip them and send them or whatever it wants.
     * get_tempfiles to get the list of files.
     * @see get_tempfiles
     *
     */
    public abstract function prepare_package();

    /**
     * This is the function that is responsible for sending
     * the package to the remote system,
     * or whatever request is necessary to initiate the transfer.
     *
     * @return bool success
     */
    public abstract function send_package();


    /**
     * Once everything is done and the user
     * has the finish page displayed to them.
     * The base class takes care of printing them
     * "return to where you are" or "continue to portfolio" links.
     * This function allows for exta finish options from the plugin
     *
     * @return bool
     */
    public function get_extra_finish_options() {
        return false;
    }

    /**
     * The url for the user to continue to their portfolio
     * during the lifecycle of the request
     */
    public abstract function get_interactive_continue_url();

    /**
     * The url to save in the log as the continue url.
     * This is passed through resolve_static_continue_url()
     * at display time to the user.
     *
     * @return string
     */
    public function get_static_continue_url() {
        return $this->get_interactive_continue_url();
    }

    /**
     * Override this function if you need to add something on to the url
     * for post-export continues (eg from the log page).
     * Mahara does this, for example, to start a jump session.
     *
     * @param string $url static continue url
     * @return string
     */
    public function resolve_static_continue_url($url) {
        return $url;
    }

    /**
     * mform to display to the user in their profile
     * if your plugin can't be configured by the user,
     * @see has_user_config.
     * Don't bother overriding this function
     *
     * @param moodleform $mform passed by reference, add elements to it
     */
    public function user_config_form(&$mform) {}

    /**
     * mform to display to the admin configuring the plugin.
     * If your plugin can't be configured by the admin,
     * @see has_admin_config
     * Don't bother overriding this function.
     * This function can be called statically or non statically,
     * depending on whether it's creating a new instance (statically),
     * or editing an existing one (non statically)
     *
     * @param moodleform $mform passed by reference, add elements to it.
     */
    public static function admin_config_form(&$mform) {}

    /**
     * Just like the moodle form validation function,
     * this is passed in the data array from the form
     * and if a non empty array is returned, form processing will stop.
     *
     * @param array $data data from form.
     */
    public static function admin_config_validation($data) {}

    /**
     * mform to display to the user exporting data using this plugin.
     * If your plugin doesn't need user input at this time,
     * @see has_export_config.
     * Don't bother overrideing this function
     *
     * @param moodleform $mform passed by reference, add elements to it.
     */
    public function export_config_form(&$mform) {}

    /**
     * Override this if your plugin doesn't allow multiple instances
     *
     * @return bool
     */
    public static function allows_multiple_instances() {
        return true;
    }

    /**
     * If at any point the caller wants to steal control,
     * it can, by returning something that isn't false
     * in this function
     * The controller will redirect to whatever url
     * this function returns.
     * Afterwards, you can redirect back to portfolio/add.php?postcontrol=1
     * and post_control is called before the rest of the processing
     * for the stage is done,
     * @see post_control
     *
     * @param int $stage to steal control *before* (see constants PARAM_STAGE_*}
     * @return bool
     */
    public function steal_control($stage) {
        return false;
    }

    /**
     * After a plugin has elected to steal control,
     * and control returns to portfolio/add.php|postcontrol=1,
     * this function is called, and passed the stage that was stolen control from
     * and the request (get and post but not cookie) parameters.
     * This is useful for external systems that need to redirect the user back
     * with some extra data in the url (like auth tokens etc)
     * for an example implementation, see googledocs portfolio plugin.
     *
     * @param int $stage the stage before control was stolen
     * @param array $params a merge of $_GET and $_POST
     */
    public function post_control($stage, $params) { }

    /**
     * This function creates a new instance of a plugin
     * saves it in the database, saves the config
     * and returns it.
     * You shouldn't need to override it
     * unless you're doing something really funky
     *
     * @param string $plugin portfolio plugin to create
     * @param string $name name of new instance
     * @param array $config what the admin config form returned
     * @return object subclass of portfolio_plugin_base
     */
    public static function create_instance($plugin, $name, $config) {
        global $DB, $CFG;
        $new = (object)array(
            'plugin' => $plugin,
            'name'   => $name,
        );
        if (!portfolio_static_function($plugin, 'allows_multiple_instances')) {
            // check we don't have one already
            if ($DB->record_exists('portfolio_instance', array('plugin' => $plugin))) {
                throw new portfolio_exception('multipleinstancesdisallowed', 'portfolio', '', $plugin);
            }
        }
        $newid = $DB->insert_record('portfolio_instance', $new);
        require_once($CFG->dirroot . '/portfolio/' . $plugin . '/lib.php');
        $classname = 'portfolio_plugin_'  . $plugin;
        $obj = new $classname($newid);
        $obj->set_config($config);
        $obj->save();
        return $obj;
    }

    /**
     * Construct a plugin instance.
     * Subclasses should not need to override this unless they're doing something special
     * and should call parent::__construct afterwards.
     *
     * @param int $instanceid id of plugin instance to construct
     * @param mixed $record stdclass object or named array - use this if you already have the record to avoid another query
     * @return portfolio_plugin_base
     */
    public function __construct($instanceid, $record=null) {
        global $DB;
        if (!$record) {
            if (!$record = $DB->get_record('portfolio_instance', array('id' => $instanceid))) {
                throw new portfolio_exception('invalidinstance', 'portfolio');
            }
        }
        foreach ((array)$record as $key =>$value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->config = new StdClass;
        $this->userconfig = array();
        $this->exportconfig = array();
        foreach ($DB->get_records('portfolio_instance_config', array('instance' => $instanceid)) as $config) {
            $this->config->{$config->name} = $config->value;
        }
        $this->init();
        return $this;
    }

    /**
     * Called after __construct - allows plugins to perform initialisation tasks
     * without having to override the constructor.
     */
    protected function init() { }

    /**
     * A list of fields that can be configured per instance.
     * This is used for the save handlers of the config form
     * and as checks in set_config and get_config.
     *
     * @return array array of strings (config item names)
     */
    public static function get_allowed_config() {
        return array();
    }

    /**
     * A list of fields that can be configured by the user.
     * This is used for the save handlers in the config form
     * and as checks in set_user_config and get_user_config.
     *
     * @return array array of strings (config field names)
     */
    public function get_allowed_user_config() {
        return array();
    }

    /**
     * A list of fields that can be configured by the user.
     * This is used for the save handlers in the config form
     * and as checks in set_export_config and get_export_config.
     *
     * @return array array of strings (config field names)
     */
    public function get_allowed_export_config() {
        return array();
    }

    /**
     * Saves (or updates) the config stored in portfolio_instance_config.
     * You shouldn't need to override this unless you're doing something funky.
     *
     * @param array $config array of config items.
     */
    public final function set_config($config) {
        global $DB;
        foreach ($config as $key => $value) {
            // try set it in $this first
            try {
                $this->set($key, $value);
                continue;
            } catch (portfolio_exception $e) { }
            if (!in_array($key, $this->get_allowed_config())) {
                $a = (object)array('property' => $key, 'class' => get_class($this));
                throw new portfolio_export_exception($this->get('exporter'), 'invalidconfigproperty', 'portfolio', null, $a);
            }
            if (!isset($this->config->{$key})) {
                $DB->insert_record('portfolio_instance_config', (object)array(
                    'instance' => $this->id,
                    'name' => $key,
                    'value' => $value,
                ));
            } else if ($this->config->{$key} != $value) {
                $DB->set_field('portfolio_instance_config', 'value', $value, array('name' => $key, 'instance' => $this->id));
            }
            $this->config->{$key} = $value;
        }
    }

    /**
     * Gets the value of a particular config item
     *
     * @param string $key key to fetch
     * @return null|mixed the corresponding value
     */
    public final function get_config($key) {
        if (!in_array($key, $this->get_allowed_config())) {
            $a = (object)array('property' => $key, 'class' => get_class($this));
            throw new portfolio_export_exception($this->get('exporter'), 'invalidconfigproperty', 'portfolio', null, $a);
        }
        if (isset($this->config->{$key})) {
            return $this->config->{$key};
        }
        return null;
    }

    /**
     * Get the value of a config item for a particular user.
     *
     * @param string $key key to fetch
     * @param int $userid id of user (defaults to current)
     * @return string the corresponding value
     *
     */
    public final function get_user_config($key, $userid=0) {
        global $DB;

        if (empty($userid)) {
            $userid = $this->user->id;
        }

        if ($key != 'visible') { // handled by the parent class
            if (!in_array($key, $this->get_allowed_user_config())) {
                $a = (object)array('property' => $key, 'class' => get_class($this));
                throw new portfolio_export_exception($this->get('exporter'), 'invaliduserproperty', 'portfolio', null, $a);
            }
        }
        if (!array_key_exists($userid, $this->userconfig)) {
            $this->userconfig[$userid] = (object)array_fill_keys(array_merge(array('visible'), $this->get_allowed_user_config()), null);
            foreach ($DB->get_records('portfolio_instance_user', array('instance' => $this->id, 'userid' => $userid)) as $config) {
                $this->userconfig[$userid]->{$config->name} = $config->value;
            }
        }
        if ($this->userconfig[$userid]->visible === null) {
            $this->set_user_config(array('visible' => 1), $userid);
        }
        return $this->userconfig[$userid]->{$key};

    }

    /**
     * Sets config options for a given user.
     *
     * @param array $config array containing key/value pairs to set
     * @param int $userid userid to set config for (defaults to current)
     *
     */
    public final function set_user_config($config, $userid=0) {
        global $DB;

        if (empty($userid)) {
            $userid = $this->user->id;
        }

        foreach ($config as $key => $value) {
            if ($key != 'visible' && !in_array($key, $this->get_allowed_user_config())) {
                $a = (object)array('property' => $key, 'class' => get_class($this));
                throw new portfolio_export_exception($this->get('exporter'), 'invaliduserproperty', 'portfolio', null, $a);
            }
            if (!$existing = $DB->get_record('portfolio_instance_user', array('instance'=> $this->id, 'userid' => $userid, 'name' => $key))) {
                $DB->insert_record('portfolio_instance_user', (object)array(
                    'instance' => $this->id,
                    'name' => $key,
                    'value' => $value,
                    'userid' => $userid,
                ));
            } else if ($existing->value != $value) {
                $DB->set_field('portfolio_instance_user', 'value', $value, array('name' => $key, 'instance' => $this->id, 'userid' => $userid));
            }
            $this->userconfig[$userid]->{$key} = $value;
        }

    }

    /**
     * Generic getter for properties belonging to this instance
     * <b>outside</b> the subclasses
     * like name, visible etc.
     *
     * @param string $field property name
     * @return mixed value of the field
     */
    public final function get($field) {
        // This is a legacy change to the way files are get/set.
        // We now only set $this->file to the id of the \stored_file. So, we need to convert that id back to a \stored_file here.
        if ($field === 'file') {
            return $this->get_file();
        }
        if (property_exists($this, $field)) {
            return $this->{$field};
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        throw new portfolio_export_exception($this->get('exporter'), 'invalidproperty', 'portfolio', null, $a);
    }

    /**
     * Generic setter for properties belonging to this instance
     * <b>outside</b> the subclass
     * like name, visible, etc.
     *
     * @param string $field property's name
     * @param string $value property's value
     * @return bool
     */
    public final function set($field, $value) {
        // This is a legacy change to the way files are get/set.
        // Make sure we never save the \stored_file object. Instead, use the id from $file->get_id() - set_file() does this for us.
        if ($field === 'file') {
            $this->set_file($value);
            return true;
        }
        if (property_exists($this, $field)) {
            $this->{$field} =& $value;
            $this->dirty = true;
            return true;
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        if ($this->get('exporter')) {
            throw new portfolio_export_exception($this->get('exporter'), 'invalidproperty', 'portfolio', null, $a);
        }
        throw new portfolio_exception('invalidproperty', 'portfolio', null, $a); // this happens outside export (eg admin settings)

    }

    /**
     * Saves stuff that's been stored in the object to the database.
     * You shouldn't need to override this
     * unless you're doing something really funky.
     * and if so, call parent::save when you're done.
     *
     * @return bool
     */
    public function save() {
        global $DB;
        if (!$this->dirty) {
            return true;
        }
        $fordb = new StdClass();
        foreach (array('id', 'name', 'plugin', 'visible') as $field) {
            $fordb->{$field} = $this->{$field};
        }
        $DB->update_record('portfolio_instance', $fordb);
        $this->dirty = false;
        return true;
    }

    /**
     * Deletes everything from the database about this plugin instance.
     * You shouldn't need to override this unless you're storing stuff
     * in your own tables.  and if so, call parent::delete when you're done.
     *
     * @return bool
     */
    public function delete() {
        global $DB;
        $DB->delete_records('portfolio_instance_config', array('instance' => $this->get('id')));
        $DB->delete_records('portfolio_instance_user', array('instance' => $this->get('id')));
        $DB->delete_records('portfolio_tempdata', array('instance' => $this->get('id')));
        $DB->delete_records('portfolio_instance', array('id' => $this->get('id')));
        $this->dirty = false;
        return true;
    }

    /**
     * Perform any required cleanup functions
     *
     * @return bool
     */
    public function cleanup() {
        return true;
    }

    /**
     * Whether this plugin supports multiple exports in the same session
     * most plugins should handle this, but some that require a redirect for authentication
     * and then don't support dynamically constructed urls to return to (eg box.net)
     * need to override this to return false.
     * This means that moodle will prevent multiple exports of this *type* of plugin
     * occurring in the same session.
     *
     * @return bool
     */
    public static function allows_multiple_exports() {
        return true;
    }

    /**
     * Return a string to put at the header summarising this export
     * by default, just the plugin instance name
     *
     * @return string
     */
    public function heading_summary() {
        return get_string('exportingcontentto', 'portfolio', $this->name);
    }
}

/**
 * Class to inherit from for 'push' type plugins
 *
 * Eg: those that send the file via a HTTP post or whatever
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class portfolio_plugin_push_base extends portfolio_plugin_base {

    /**
     * Get the capability to push
     *
     * @return bool
     */
    public function is_push() {
        return true;
    }
}

/**
 * Class to inherit from for 'pull' type plugins.
 *
 * Eg: those that write a file and wait for the remote system to request it
 * from portfolio/file.php.
 * If you're using this you must do $this->set('file', $file) so that it can be served.
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class portfolio_plugin_pull_base extends portfolio_plugin_base {

    /** @var int $file the id of a single file */
    protected $file;

    /**
     * return the enablelity to push
     *
     * @return bool
     */
    public function is_push() {
        return false;
    }

    /**
     * The base part of the download file url to pull files from
     * your plugin might need to add &foo=bar on the end
     * @see verify_file_request_params
     *
     * @return string the url
     */
    public function get_base_file_url() {
        global $CFG;
        return $CFG->wwwroot . '/portfolio/file.php?id=' . $this->exporter->get('id');
    }

    /**
     * Before sending the file when the pull is requested, verify the request parameters.
     * These might include a token of some sort of whatever
     *
     * @param array $params request parameters (POST wins over GET)
     */
    public abstract function verify_file_request_params($params);

    /**
     * Called from portfolio/file.php.
     * This function sends the stored file out to the browser.
     * The default is to just use send_stored_file,
     * but other implementations might do something different,
     * for example, send back the file base64 encoded and encrypted
     * mahara does this but in the response to an xmlrpc request
     * rather than through file.php
     */
    public function send_file() {
        $file = $this->get('file');
        if (!($file instanceof stored_file)) {
            throw new portfolio_export_exception($this->get('exporter'), 'filenotfound', 'portfolio');
        }
        // don't die(); afterwards, so we can clean up.
        send_stored_file($file, 0, 0, true, array('dontdie' => true));
        $this->get('exporter')->log_transfer();
    }

    /**
     * Sets the $file instance var to the id of the supplied \stored_file.

     * This helper allows the $this->get('file') call to return a \stored_file, but means that we only ever record an id reference
     * in the $file instance var.
     *
     * @param \stored_file $file The stored_file instance.
     * @return void
     */
    protected function set_file(\stored_file $file) {
        $fileid = $file->get_id();
        if (empty($fileid)) {
            debugging('stored_file->id should not be empty');
            $this->file = null;
        } else {
            $this->file = $fileid;
        }
    }

    /**
     * Gets the \stored_file object from the file id in the $file instance var.
     *
     * @return stored_file|null the \stored_file object if it exists, null otherwise.
     */
    protected function get_file() {
        if (!$this->file) {
            return null;
        }
        // The get_file_by_id call can return false, so normalise to null.
        $file = get_file_storage()->get_file_by_id($this->file);
        return ($file) ? $file : null;
    }
}
