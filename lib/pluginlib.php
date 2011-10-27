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
 * Defines classes used for plugins management
 *
 * This library provides a unified interface to various plugin types in
 * Moodle. It is mainly used by the plugins management admin page and the
 * plugins check page during the upgrade.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Singleton class providing general plugins management functionality
 */
class plugin_manager {

    /** the plugin is shipped with standard Moodle distribution */
    const PLUGIN_SOURCE_STANDARD    = 'std';
    /** the plugin is added extension */
    const PLUGIN_SOURCE_EXTENSION   = 'ext';

    /** the plugin uses neither database nor capabilities, no versions */
    const PLUGIN_STATUS_NODB        = 'nodb';
    /** the plugin is up-to-date */
    const PLUGIN_STATUS_UPTODATE    = 'uptodate';
    /** the plugin is about to be installed */
    const PLUGIN_STATUS_NEW         = 'new';
    /** the plugin is about to be upgraded */
    const PLUGIN_STATUS_UPGRADE     = 'upgrade';
    /** the standard plugin is about to be deleted */
    const PLUGIN_STATUS_DELETE     = 'delete';
    /** the version at the disk is lower than the one already installed */
    const PLUGIN_STATUS_DOWNGRADE   = 'downgrade';
    /** the plugin is installed but missing from disk */
    const PLUGIN_STATUS_MISSING     = 'missing';

    /** @var plugin_manager holds the singleton instance */
    protected static $singletoninstance;
    /** @var array of raw plugins information */
    protected $pluginsinfo = null;
    /** @var array of raw subplugins information */
    protected $subpluginsinfo = null;

    /**
     * Direct initiation not allowed, use the factory method {@link self::instance()}
     *
     * @todo we might want to specify just a single plugin type to work with
     */
    protected function __construct() {
        $this->get_plugins(true);
    }

    /**
     * Sorry, this is singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class
     *
     * @return plugin_manager the singleton instance
     */
    public static function instance() {
        global $CFG;

        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * Returns a tree of known plugins and information about them
     *
     * @param bool $disablecache force reload, cache can be used otherwise
     * @return array 2D array. The first keys are plugin type names (e.g. qtype);
     *      the second keys are the plugin local name (e.g. multichoice); and
     *      the values are the corresponding {@link plugin_information} objects.
     */
    public function get_plugins($disablecache=false) {

        if ($disablecache or is_null($this->pluginsinfo)) {
            $this->pluginsinfo = array();
            $plugintypes = get_plugin_types();
            foreach ($plugintypes as $plugintype => $plugintyperootdir) {
                if (in_array($plugintype, array('base', 'general'))) {
                    throw new coding_exception('Illegal usage of reserved word for plugin type');
                }
                if (class_exists('plugintype_' . $plugintype)) {
                    $plugintypeclass = 'plugintype_' . $plugintype;
                } else {
                    $plugintypeclass = 'plugintype_general';
                }
                if (!in_array('plugin_information', class_implements($plugintypeclass))) {
                    throw new coding_exception('Class ' . $plugintypeclass . ' must implement plugin_information');
                }
                $plugins = call_user_func(array($plugintypeclass, 'get_plugins'), $plugintype, $plugintyperootdir, $plugintypeclass);
                $this->pluginsinfo[$plugintype] = $plugins;
            }
        }

        return $this->pluginsinfo;
    }

    /**
     * Returns list of plugins that define their subplugins and the information
     * about them from the db/subplugins.php file.
     *
     * At the moment, only activity modules can define subplugins.
     *
     * @param bool $disablecache force reload, cache can be used otherwise
     * @return array with keys like 'mod_quiz', and values the data from the
     *      corresponding db/subplugins.php file.
     */
    public function get_subplugins($disablecache=false) {

        if ($disablecache or is_null($this->subpluginsinfo)) {
            $this->subpluginsinfo = array();
            $mods = get_plugin_list('mod');
            foreach ($mods as $mod => $moddir) {
                $modsubplugins = array();
                if (file_exists($moddir . '/db/subplugins.php')) {
                    include($moddir . '/db/subplugins.php');
                    foreach ($subplugins as $subplugintype => $subplugintyperootdir) {
                        $subplugin = new stdClass();
                        $subplugin->type = $subplugintype;
                        $subplugin->typerootdir = $subplugintyperootdir;
                        $modsubplugins[$subplugintype] = $subplugin;
                    }
                $this->subpluginsinfo['mod_' . $mod] = $modsubplugins;
                }
            }
        }

        return $this->subpluginsinfo;
    }

    /**
     * Returns the name of the plugin that defines the given subplugin type
     *
     * If the given subplugin type is not actually a subplugin, returns false.
     *
     * @param string $subplugintype the name of subplugin type, eg. workshopform or quiz
     * @return false|string the name of the parent plugin, eg. mod_workshop
     */
    public function get_parent_of_subplugin($subplugintype) {

        $parent = false;
        foreach ($this->get_subplugins() as $pluginname => $subplugintypes) {
            if (isset($subplugintypes[$subplugintype])) {
                $parent = $pluginname;
                break;
            }
        }

        return $parent;
    }

    /**
     * Returns a localized name of a given plugin
     *
     * @param string $plugin name of the plugin, eg mod_workshop or auth_ldap
     * @return string
     */
    public function plugin_name($plugin) {
        list($type, $name) = normalize_component($plugin);
        return $this->pluginsinfo[$type][$name]->displayname;
    }

    /**
     * Returns a localized name of a plugin type in plural form
     *
     * Most plugin types define their names in core_plugin lang file. In case of subplugins,
     * we try to ask the parent plugin for the name. In the worst case, we will return
     * the value of the passed $type parameter.
     *
     * @param string $type the type of the plugin, e.g. mod or workshopform
     * @return string
     */
    public function plugintype_name_plural($type) {

        if (get_string_manager()->string_exists('type_' . $type . '_plural', 'core_plugin')) {
            // for most plugin types, their names are defined in core_plugin lang file
            return get_string('type_' . $type . '_plural', 'core_plugin');

        } else if ($parent = $this->get_parent_of_subplugin($type)) {
            // if this is a subplugin, try to ask the parent plugin for the name
            if (get_string_manager()->string_exists('subplugintype_' . $type . '_plural', $parent)) {
                return $this->plugin_name($parent) . ' / ' . get_string('subplugintype_' . $type . '_plural', $parent);
            } else {
                return $this->plugin_name($parent) . ' / ' . $type;
            }

        } else {
            return $type;
        }
    }

    /**
     * @param string $component frankenstyle component name.
     * @return plugin_information|null the corresponding plugin information.
     */
    public function get_plugin_info($component) {
        list($type, $name) = normalize_component($component);
        $plugins = $this->get_plugins();
        if (isset($plugins[$type][$name])) {
            return $plugins[$type][$name];
        } else {
            return null;
        }
    }

    /**
     * Get a list of any other pluings that require this one.
     * @param string $component frankenstyle component name.
     * @return array of frankensyle component names that require this one.
     */
    public function other_plugins_that_require($component) {
        $others = array();
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $required = $plugin->get_other_required_plugins();
                if (isset($required[$component])) {
                    $others[] = $plugin->component;
                }
            }
        }
        return $others;
    }

    /**
     * Check a dependencies list against the list of installed plugins.
     * @param array $dependencies compenent name to required version or ANY_VERSION.
     * @return bool true if all the dependencies are satisfied.
     */
    public function are_dependencies_satisfied($dependencies) {
        foreach ($dependencies as $component => $requiredversion) {
            $otherplugin = $this->get_plugin_info($component);
            if (is_null($otherplugin)) {
                return false;
            }

            if ($requiredversion != ANY_VERSION and $otherplugin->versiondisk < $requiredversion) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks all dependencies for all installed plugins. Used by install and upgrade.
     * @param int $moodleversion the version from version.php.
     * @return bool true if all the dependencies are satisfied for all plugins.
     */
    public function all_plugins_ok($moodleversion) {
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {

                if (!empty($plugin->versionrequires) && $plugin->versionrequires > $moodleversion) {
                    return false;
                }

                if (!$this->are_dependencies_satisfied($plugin->get_other_required_plugins())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Defines a list of all plugins that were originally shipped in the standard Moodle distribution,
     * but are not anymore and are deleted during upgrades.
     *
     * The main purpose of this list is to hide missing plugins during upgrade.
     *
     * @param string $type plugin type
     * @param string $name plugin name
     * @return bool
     */
    public static function is_deleted_standard_plugin($type, $name) {
        static $plugins = array(
            'block' => array('admin', 'admin_tree', 'loancalc', 'search'),
            'filter' => array('mod_data', 'mod_glossary'),
        );

        if (!isset($plugins[$type])) {
            return false;
        }
        return in_array($name, $plugins[$type]);
    }

    /**
     * Defines a white list of all plugins shipped in the standard Moodle distribution
     *
     * @param string $type
     * @return false|array array of standard plugins or false if the type is unknown
     */
    public static function standard_plugins_list($type) {
        static $standard_plugins = array(

            'assignment' => array(
                'offline', 'online', 'upload', 'uploadsingle'
            ),

            'auth' => array(
                'cas', 'db', 'email', 'fc', 'imap', 'ldap', 'manual', 'mnet',
                'nntp', 'nologin', 'none', 'pam', 'pop3', 'radius',
                'shibboleth', 'webservice'
            ),

            'block' => array(
                'activity_modules', 'admin_bookmarks', 'blog_menu',
                'blog_recent', 'blog_tags', 'calendar_month',
                'calendar_upcoming', 'comments', 'community',
                'completionstatus', 'course_list', 'course_overview',
                'course_summary', 'feedback', 'glossary_random', 'html',
                'login', 'mentees', 'messages', 'mnet_hosts', 'myprofile',
                'navigation', 'news_items', 'online_users', 'participants',
                'private_files', 'quiz_results', 'recent_activity',
                'rss_client', 'search_forums', 'section_links',
                'selfcompletion', 'settings', 'site_main_menu',
                'social_activities', 'tag_flickr', 'tag_youtube', 'tags'
            ),

            'coursereport' => array(
                //deprecated!
            ),

            'datafield' => array(
                'checkbox', 'date', 'file', 'latlong', 'menu', 'multimenu',
                'number', 'picture', 'radiobutton', 'text', 'textarea', 'url'
            ),

            'datapreset' => array(
                'imagegallery'
            ),

            'editor' => array(
                'textarea', 'tinymce'
            ),

            'enrol' => array(
                'authorize', 'category', 'cohort', 'database', 'flatfile',
                'guest', 'imsenterprise', 'ldap', 'manual', 'meta', 'mnet',
                'paypal', 'self'
            ),

            'filter' => array(
                'activitynames', 'algebra', 'censor', 'emailprotect',
                'emoticon', 'mediaplugin', 'multilang', 'tex', 'tidy',
                'urltolink', 'data', 'glossary'
            ),

            'format' => array(
                'scorm', 'social', 'topics', 'weeks'
            ),

            'gradeexport' => array(
                'ods', 'txt', 'xls', 'xml'
            ),

            'gradeimport' => array(
                'csv', 'xml'
            ),

            'gradereport' => array(
                'grader', 'outcomes', 'overview', 'user'
            ),

            'gradingform' => array(
                'rubric'
            ),

            'local' => array(
            ),

            'message' => array(
                'email', 'jabber', 'popup'
            ),

            'mnetservice' => array(
                'enrol'
            ),

            'mod' => array(
                'assignment', 'chat', 'choice', 'data', 'feedback', 'folder',
                'forum', 'glossary', 'imscp', 'label', 'lesson', 'lti', 'page',
                'quiz', 'resource', 'scorm', 'survey', 'url', 'wiki', 'workshop'
            ),

            'plagiarism' => array(
            ),

            'portfolio' => array(
                'boxnet', 'download', 'flickr', 'googledocs', 'mahara', 'picasa'
            ),

            'profilefield' => array(
                'checkbox', 'datetime', 'menu', 'text', 'textarea'
            ),

            'qbehaviour' => array(
                'adaptive', 'adaptivenopenalty', 'deferredcbm',
                'deferredfeedback', 'immediatecbm', 'immediatefeedback',
                'informationitem', 'interactive', 'interactivecountback',
                'manualgraded', 'missing'
            ),

            'qformat' => array(
                'aiken', 'blackboard', 'blackboard_six', 'examview', 'gift',
                'learnwise', 'missingword', 'multianswer', 'webct',
                'xhtml', 'xml'
            ),

            'qtype' => array(
                'calculated', 'calculatedmulti', 'calculatedsimple',
                'description', 'essay', 'match', 'missingtype', 'multianswer',
                'multichoice', 'numerical', 'random', 'randomsamatch',
                'shortanswer', 'truefalse'
            ),

            'quiz' => array(
                'grading', 'overview', 'responses', 'statistics'
            ),

            'quizaccess' => array(
                'delaybetweenattempts', 'ipaddress', 'numattempts', 'openclosedate',
                'password', 'safebrowser', 'securewindow', 'timelimit'
            ),

            'report' => array(
                'backups', 'completion', 'configlog', 'courseoverview',
                'log', 'loglive', 'outline', 'participation', 'progress', 'questioninstances', 'security', 'stats'
            ),

            'repository' => array(
                'alfresco', 'boxnet', 'coursefiles', 'dropbox', 'filesystem',
                'flickr', 'flickr_public', 'googledocs', 'local', 'merlot',
                'picasa', 'recent', 's3', 'upload', 'url', 'user', 'webdav',
                'wikimedia', 'youtube'
            ),

            'scormreport' => array(
                'basic',
                'interactions'
            ),

            'theme' => array(
                'afterburner', 'anomaly', 'arialist', 'base', 'binarius',
                'boxxie', 'brick', 'canvas', 'formal_white', 'formfactor',
                'fusion', 'leatherbound', 'magazine', 'mymobile', 'nimble',
                'nonzero', 'overlay', 'serenity', 'sky_high', 'splash',
                'standard', 'standardold'
            ),

            'tool' => array(
                'bloglevelupgrade', 'capability', 'customlang', 'dbtransfer', 'generator',
                'health', 'innodb', 'langimport', 'multilangupgrade', 'profiling',
                'qeupgradehelper', 'replace', 'spamcleaner', 'timezoneimport', 'unittest',
                'uploaduser', 'unsuproles', 'xmldb'
            ),

            'webservice' => array(
                'amf', 'rest', 'soap', 'xmlrpc'
            ),

            'workshopallocation' => array(
                'manual', 'random'
            ),

            'workshopeval' => array(
                'best'
            ),

            'workshopform' => array(
                'accumulative', 'comments', 'numerrors', 'rubric'
            )
        );

        if (isset($standard_plugins[$type])) {
            return $standard_plugins[$type];

        } else {
            return false;
        }
    }
}

/**
 * Interface for making information about a plugin available.
 *
 * Note that most of the useful information is made available in pubic fields,
 * which cannot be documented in this interface. See the field definitions on
 * {@link plugintype_base} to find out what information is available.
 *
 * @property-read string component the component name, type_name
 */
interface plugin_information {

    /**
     * Gathers and returns the information about all plugins of the given type
     *
     * Passing the parameter $typeclass allows us to reach the same effect as with the
     * late binding in PHP 5.3. Once PHP 5.3 is required, we can refactor this to use
     * {@example $plugin = new static();} instead of {@example $plugin = new $typeclass()}
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass);

    /**
     * Sets $displayname property to a localized name of the plugin
     *
     * @return void
     */
    public function init_display_name();

    /**
     * Sets $versiondisk property to a numerical value representing the
     * version of the plugin's source code.
     *
     * If the value is null after calling this method, either the plugin
     * does not use versioning (typically does not have any database
     * data) or is missing from disk.
     *
     * @return void
     */
    public function load_disk_version();

    /**
     * Sets $versiondb property to a numerical value representing the
     * currently installed version of the plugin.
     *
     * If the value is null after calling this method, either the plugin
     * does not use versioning (typically does not have any database
     * data) or has not been installed yet.
     *
     * @return void
     */
    public function load_db_version();

    /**
     * Sets $versionrequires property to a numerical value representing
     * the version of Moodle core that this plugin requires.
     *
     * @return void
     */
    public function load_required_main_version();

    /**
     * Sets $source property to one of plugin_manager::PLUGIN_SOURCE_xxx
     * constants.
     *
     * If the property's value is null after calling this method, then
     * the type of the plugin has not been recognized and you should throw
     * an exception.
     *
     * @return void
     */
    public function init_is_standard();

    /**
     * Returns true if the plugin is shipped with the official distribution
     * of the current Moodle version, false otherwise.
     *
     * @return bool
     */
    public function is_standard();

    /**
     * Returns the status of the plugin
     *
     * @return string one of plugin_manager::PLUGIN_STATUS_xxx constants
     */
    public function get_status();

    /**
     * Get the list of other plugins that this plugin requires ot be installed.
     * @return array with keys the frankenstyle plugin name, and values either
     *      a version string (like '2011101700') or the constant ANY_VERSION.
     */
    public function get_other_required_plugins();

    /**
     * Returns the information about plugin availability
     *
     * True means that the plugin is enabled. False means that the plugin is
     * disabled. Null means that the information is not available, or the
     * plugin does not support configurable availability or the availability
     * can not be changed.
     *
     * @return null|bool
     */
    public function is_enabled();

    /**
     * Returns the URL of the plugin settings screen
     *
     * Null value means that the plugin either does not have the settings screen
     * or its location is not available via this library.
     *
     * @return null|moodle_url
     */
    public function get_settings_url();

    /**
     * Returns the URL of the screen where this plugin can be uninstalled
     *
     * Visiting that URL must be safe, that is a manual confirmation is needed
     * for actual uninstallation of the plugin. Null value means that the
     * plugin either does not support uninstallation, or does not require any
     * database cleanup or the location of the screen is not available via this
     * library.
     *
     * @return null|moodle_url
     */
    public function get_uninstall_url();

    /**
     * Returns relative directory of the plugin with heading '/'
     *
     * @example /mod/workshop
     * @return string
     */
    public function get_dir();

    /**
     * Return the full path name of a file within the plugin.
     * No check is made to see if the file exists.
     * @param string $relativepath e.g. 'version.php'.
     * @return string e.g. $CFG->dirroot . '/mod/quiz/version.php'.
     */
    public function full_path($relativepath);
}

/**
 * Defines public properties that all plugintype classes must have
 * and provides default implementation of required methods.
 *
 * @property-read string component the component name, type_name
 */
abstract class plugintype_base {

    /** @var string the plugintype name, eg. mod, auth or workshopform */
    public $type;
    /** @var string full path to the location of all the plugins of this type */
    public $typerootdir;
    /** @var string the plugin name, eg. assignment, ldap */
    public $name;
    /** @var string the localized plugin name */
    public $displayname;
    /** @var string the plugin source, one of plugin_manager::PLUGIN_SOURCE_xxx constants */
    public $source;
    /** @var fullpath to the location of this plugin */
    public $rootdir;
    /** @var int|string the version of the plugin's source code */
    public $versiondisk;
    /** @var int|string the version of the installed plugin */
    public $versiondb;
    /** @var int|float|string required version of Moodle core  */
    public $versionrequires;
    /** @var array other plugins that this one depends on.
     *  Lazy-loaded by {@link get_other_required_plugins()} */
    public $dependencies = null;
    /** @var int number of instances of the plugin - not supported yet */
    public $instances;
    /** @var int order of the plugin among other plugins of the same type - not supported yet */
    public $sortorder;

    /**
     * @see plugin_information::get_plugins()
     */
    public static function get_plugins($type, $typerootdir, $typeclass) {

        // get the information about plugins at the disk
        $plugins = get_plugin_list($type);
        $ondisk = array();
        foreach ($plugins as $pluginname => $pluginrootdir) {
            $plugin                 = new $typeclass();
            $plugin->type           = $type;
            $plugin->typerootdir    = $typerootdir;
            $plugin->name           = $pluginname;
            $plugin->rootdir        = $pluginrootdir;

            $plugin->init_display_name();
            $plugin->load_disk_version();
            $plugin->load_db_version();
            $plugin->load_required_main_version();
            $plugin->init_is_standard();

            $ondisk[$pluginname] = $plugin;
        }
        return $ondisk;
    }

    /**
     * @see plugin_information::init_display_name()
     */
    public function init_display_name() {
        if (!get_string_manager()->string_exists('pluginname', $this->component)) {
            $this->displayname = '[pluginname,' . $this->component . ']';
        } else {
            $this->displayname = get_string('pluginname', $this->component);
        }
    }

    /**
     * Magic method getter, redirects to read only values.
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'component': return $this->type . '_' . $this->name;

            default:
                debugging('Invalid plugin property accessed! '.$name);
                return null;
        }
    }

    /**
     * @see plugin_information::full_path()
     */
    public function full_path($relativepath) {
        if (empty($this->rootdir)) {
            return '';
        }
        return $this->rootdir . '/' . $relativepath;
    }

    /**
     * Load the data from version.php.
     * @return object the data object defined in version.php.
     */
    protected function load_version_php() {
        $versionfile = $this->full_path('version.php');

        $plugin = new stdClass();
        if (is_readable($versionfile)) {
            include($versionfile);
        }
        return $plugin;
    }

    /**
     * @see plugin_information::load_disk_version()
     */
    public function load_disk_version() {
        $plugin = $this->load_version_php();
        if (isset($plugin->version)) {
            $this->versiondisk = $plugin->version;
        }
    }

    /**
     * @see plugin_information::load_required_main_version()
     */
    public function load_required_main_version() {
        $plugin = $this->load_version_php();
        if (isset($plugin->requires)) {
            $this->versionrequires = $plugin->requires;
        }
    }

    /**
     * Initialise {@link $dependencies} to the list of other plugins (in any)
     * that this one requires to be installed.
     */
    protected function load_other_required_plugins() {
        $plugin = $this->load_version_php();
        if (!empty($plugin->dependencies)) {
            $this->dependencies = $plugin->dependencies;
        } else {
            $this->dependencies = array(); // By default, no dependencies.
        }
    }

    /**
     * @see plugin_information::get_other_required_plugins()
     */
    public function get_other_required_plugins() {
        if (is_null($this->dependencies)) {
            $this->load_other_required_plugins();
        }
        return $this->dependencies;
    }

    /**
     * @see plugin_information::load_db_version()
     */
    public function load_db_version() {

        if ($ver = self::get_version_from_config_plugins($this->component)) {
            $this->versiondb = $ver;
        }
    }

    /**
     * @see plugin_information::init_is_standard()
     */
    public function init_is_standard() {

        $standard = plugin_manager::standard_plugins_list($this->type);

        if ($standard !== false) {
            $standard = array_flip($standard);
            if (isset($standard[$this->name])) {
                $this->source = plugin_manager::PLUGIN_SOURCE_STANDARD;
            } else if (!is_null($this->versiondb) and is_null($this->versiondisk)
                    and plugin_manager::is_deleted_standard_plugin($this->type, $this->name)) {
                $this->source = plugin_manager::PLUGIN_SOURCE_STANDARD; // to be deleted
            } else {
                $this->source = plugin_manager::PLUGIN_SOURCE_EXTENSION;
            }
        }
    }

    /**
     * @see plugin_information::is_standard()
     */
    public function is_standard() {
        return $this->source === plugin_manager::PLUGIN_SOURCE_STANDARD;
    }

    /**
     * @see plugin_information::get_status()
     */
    public function get_status() {

        if (is_null($this->versiondb) and is_null($this->versiondisk)) {
            return plugin_manager::PLUGIN_STATUS_NODB;

        } else if (is_null($this->versiondb) and !is_null($this->versiondisk)) {
            return plugin_manager::PLUGIN_STATUS_NEW;

        } else if (!is_null($this->versiondb) and is_null($this->versiondisk)) {
            if (plugin_manager::is_deleted_standard_plugin($this->type, $this->name)) {
                return plugin_manager::PLUGIN_STATUS_DELETE;
            } else {
                return plugin_manager::PLUGIN_STATUS_MISSING;
            }

        } else if ((string)$this->versiondb === (string)$this->versiondisk) {
            return plugin_manager::PLUGIN_STATUS_UPTODATE;

        } else if ($this->versiondb < $this->versiondisk) {
            return plugin_manager::PLUGIN_STATUS_UPGRADE;

        } else if ($this->versiondb > $this->versiondisk) {
            return plugin_manager::PLUGIN_STATUS_DOWNGRADE;

        } else {
            // $version = pi(); and similar funny jokes - hopefully Donald E. Knuth will never contribute to Moodle ;-)
            throw new coding_exception('Unable to determine plugin state, check the plugin versions');
        }
    }

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {
        return null;
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {
        return null;
    }

    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {
        return null;
    }

    /**
     * @see plugin_information::get_dir()
     */
    public function get_dir() {
        global $CFG;

        return substr($this->rootdir, strlen($CFG->dirroot));
    }

    /**
     * Provides access to plugin versions from {config_plugins}
     *
     * @param string $plugin plugin name
     * @param double $disablecache optional, defaults to false
     * @return int|false the stored value or false if not found
     */
    protected function get_version_from_config_plugins($plugin, $disablecache=false) {
        global $DB;
        static $pluginversions = null;

        if (is_null($pluginversions) or $disablecache) {
            try {
                $pluginversions = $DB->get_records_menu('config_plugins', array('name' => 'version'), 'plugin', 'plugin,value');
            } catch (dml_exception $e) {
                // before install
                $pluginversions = array();
            }
        }

        if (!array_key_exists($plugin, $pluginversions)) {
            return false;
        }

        return $pluginversions[$plugin];
    }
}

/**
 * General class for all plugin types that do not have their own class
 */
class plugintype_general extends plugintype_base implements plugin_information {

}

/**
 * Class for page side blocks
 */
class plugintype_block extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::get_plugins()
     */
    public static function get_plugins($type, $typerootdir, $typeclass) {

        // get the information about blocks at the disk
        $blocks = parent::get_plugins($type, $typerootdir, $typeclass);

        // add blocks missing from disk
        $blocksinfo = self::get_blocks_info();
        foreach ($blocksinfo as $blockname => $blockinfo) {
            if (isset($blocks[$blockname])) {
                continue;
            }
            $plugin                 = new $typeclass();
            $plugin->type           = $type;
            $plugin->typerootdir    = $typerootdir;
            $plugin->name           = $blockname;
            $plugin->rootdir        = null;
            $plugin->displayname    = $blockname;
            $plugin->versiondb      = $blockinfo->version;
            $plugin->init_is_standard();

            $blocks[$blockname]   = $plugin;
        }

        return $blocks;
    }

    /**
     * @see plugin_information::init_display_name()
     */
    public function init_display_name() {

        if (get_string_manager()->string_exists('pluginname', 'block_' . $this->name)) {
            $this->displayname = get_string('pluginname', 'block_' . $this->name);

        } else if (($block = block_instance($this->name)) !== false) {
            $this->displayname = $block->get_title();

        } else {
            parent::init_display_name();
        }
    }

    /**
     * @see plugin_information::load_db_version()
     */
    public function load_db_version() {
        global $DB;

        $blocksinfo = self::get_blocks_info();
        if (isset($blocksinfo[$this->name]->version)) {
            $this->versiondb = $blocksinfo[$this->name]->version;
        }
    }

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {

        $blocksinfo = self::get_blocks_info();
        if (isset($blocksinfo[$this->name]->visible)) {
            if ($blocksinfo[$this->name]->visible) {
                return true;
            } else {
                return false;
            }
        } else {
            return parent::is_enabled();
        }
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        if (($block = block_instance($this->name)) === false) {
            return parent::get_settings_url();

        } else if ($block->has_config()) {
            if (file_exists($this->full_path('settings.php'))) {
                return new moodle_url('/admin/settings.php', array('section' => 'blocksetting' . $this->name));
            } else {
                $blocksinfo = self::get_blocks_info();
                return new moodle_url('/admin/block.php', array('block' => $blocksinfo[$this->name]->id));
            }

        } else {
            return parent::get_settings_url();
        }
    }

    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {

        $blocksinfo = self::get_blocks_info();
        return new moodle_url('/admin/blocks.php', array('delete' => $blocksinfo[$this->name]->id, 'sesskey' => sesskey()));
    }

    /**
     * Provides access to the records in {block} table
     *
     * @param bool $disablecache do not use internal static cache
     * @return array array of stdClasses
     */
    protected static function get_blocks_info($disablecache=false) {
        global $DB;
        static $blocksinfocache = null;

        if (is_null($blocksinfocache) or $disablecache) {
            try {
                $blocksinfocache = $DB->get_records('block', null, 'name', 'name,id,version,visible');
            } catch (dml_exception $e) {
                // before install
                $blocksinfocache = array();
            }
        }

        return $blocksinfocache;
    }
}

/**
 * Class for text filters
 */
class plugintype_filter extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::get_plugins()
     */
    public static function get_plugins($type, $typerootdir, $typeclass) {
        global $CFG, $DB;

        $filters = array();

        // get the list of filters from both /filter and /mod location
        $installed = filter_get_all_installed();

        foreach ($installed as $filterlegacyname => $displayname) {
            $plugin                 = new $typeclass();
            $plugin->type           = $type;
            $plugin->typerootdir    = $typerootdir;
            $plugin->name           = self::normalize_legacy_name($filterlegacyname);
            $plugin->rootdir        = $CFG->dirroot . '/' . $filterlegacyname;
            $plugin->displayname    = $displayname;

            $plugin->load_disk_version();
            $plugin->load_db_version();
            $plugin->load_required_main_version();
            $plugin->init_is_standard();

            $filters[$plugin->name] = $plugin;
        }

        $globalstates = self::get_global_states();

        if ($DB->get_manager()->table_exists('filter_active')) {
            // if we're upgrading from 1.9, the table does not exist yet
            // if it does, make sure that all installed filters are registered
            $needsreload  = false;
            foreach (array_keys($installed) as $filterlegacyname) {
                if (!isset($globalstates[self::normalize_legacy_name($filterlegacyname)])) {
                    filter_set_global_state($filterlegacyname, TEXTFILTER_DISABLED);
                    $needsreload = true;
                }
            }
            if ($needsreload) {
                $globalstates = self::get_global_states(true);
            }
        }

        // make sure that all registered filters are installed, just in case
        foreach ($globalstates as $name => $info) {
            if (!isset($filters[$name])) {
                // oops, there is a record in filter_active but the filter is not installed
                $plugin                 = new $typeclass();
                $plugin->type           = $type;
                $plugin->typerootdir    = $typerootdir;
                $plugin->name           = $name;
                $plugin->rootdir        = $CFG->dirroot . '/' . $info->legacyname;
                $plugin->displayname    = $info->legacyname;

                $plugin->load_db_version();

                if (is_null($plugin->versiondb)) {
                    // this is a hack to stimulate 'Missing from disk' error
                    // because $plugin->versiondisk will be null !== false
                    $plugin->versiondb = false;
                }

                $filters[$plugin->name] = $plugin;
            }
        }

        return $filters;
    }

    /**
     * @see plugin_information::init_display_name()
     */
    public function init_display_name() {
        // do nothing, the name is set in self::get_plugins()
    }

    /**
     * @see plugintype_base::load_version_php().
     */
    protected function load_version_php() {
        if (strpos($this->name, 'mod_') === 0) {
            // filters bundled with modules do not have a version.php and so
            // do not provide their own versioning information.
            return new stdClass();
        }
        return parent::load_version_php();
    }

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {

        $globalstates = self::get_global_states();

        foreach ($globalstates as $filterlegacyname => $info) {
            $name = self::normalize_legacy_name($filterlegacyname);
            if ($name === $this->name) {
                if ($info->active == TEXTFILTER_DISABLED) {
                    return false;
                } else {
                    // it may be 'On' or 'Off, but available'
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        $globalstates = self::get_global_states();
        $legacyname = $globalstates[$this->name]->legacyname;
        if (filter_has_global_settings($legacyname)) {
            return new moodle_url('/admin/settings.php', array('section' => 'filtersetting' . str_replace('/', '', $legacyname)));
        } else {
            return null;
        }
    }

    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {

        if (strpos($this->name, 'mod_') === 0) {
            return null;
        } else {
            $globalstates = self::get_global_states();
            $legacyname = $globalstates[$this->name]->legacyname;
            return new moodle_url('/admin/filters.php', array('sesskey' => sesskey(), 'filterpath' => $legacyname, 'action' => 'delete'));
        }
    }

    /**
     * Convert legacy filter names like 'filter/foo' or 'mod/bar' into frankenstyle
     *
     * @param string $legacyfiltername legacy filter name
     * @return string frankenstyle-like name
     */
    protected static function normalize_legacy_name($legacyfiltername) {

        $name = str_replace('/', '_', $legacyfiltername);
        if (strpos($name, 'filter_') === 0) {
            $name = substr($name, 7);
            if (empty($name)) {
                throw new coding_exception('Unable to determine filter name: ' . $legacyfiltername);
            }
        }

        return $name;
    }

    /**
     * Provides access to the results of {@link filter_get_global_states()}
     * but indexed by the normalized filter name
     *
     * The legacy filter name is available as ->legacyname property.
     *
     * @param bool $disablecache
     * @return array
     */
    protected static function get_global_states($disablecache=false) {
        global $DB;
        static $globalstatescache = null;

        if ($disablecache or is_null($globalstatescache)) {

            if (!$DB->get_manager()->table_exists('filter_active')) {
                // we're upgrading from 1.9 and the table used by {@link filter_get_global_states()}
                // does not exist yet
                $globalstatescache = array();

            } else {
                foreach (filter_get_global_states() as $legacyname => $info) {
                    $name                       = self::normalize_legacy_name($legacyname);
                    $filterinfo                 = new stdClass();
                    $filterinfo->legacyname     = $legacyname;
                    $filterinfo->active         = $info->active;
                    $filterinfo->sortorder      = $info->sortorder;
                    $globalstatescache[$name]   = $filterinfo;
                }
            }
        }

        return $globalstatescache;
    }
}

/**
 * Class for activity modules
 */
class plugintype_mod extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::get_plugins()
     */
    public static function get_plugins($type, $typerootdir, $typeclass) {

        // get the information about plugins at the disk
        $modules = parent::get_plugins($type, $typerootdir, $typeclass);

        // add modules missing from disk
        $modulesinfo = self::get_modules_info();
        foreach ($modulesinfo as $modulename => $moduleinfo) {
            if (isset($modules[$modulename])) {
                continue;
            }
            $plugin                 = new $typeclass();
            $plugin->type           = $type;
            $plugin->typerootdir    = $typerootdir;
            $plugin->name           = $modulename;
            $plugin->rootdir        = null;
            $plugin->displayname    = $modulename;
            $plugin->versiondb      = $moduleinfo->version;
            $plugin->init_is_standard();

            $modules[$modulename]   = $plugin;
        }

        return $modules;
    }

    /**
     * @see plugin_information::init_display_name()
     */
    public function init_display_name() {
        if (get_string_manager()->string_exists('pluginname', $this->component)) {
            $this->displayname = get_string('pluginname', $this->component);
        } else {
            $this->displayname = get_string('modulename', $this->component);
        }
    }

    /**
     * Load the data from version.php.
     * @return object the data object defined in version.php.
     */
    protected function load_version_php() {
        $versionfile = $this->full_path('version.php');

        $module = new stdClass();
        if (is_readable($versionfile)) {
            include($versionfile);
        }
        return $module;
    }

    /**
     * @see plugin_information::load_db_version()
     */
    public function load_db_version() {
        global $DB;

        $modulesinfo = self::get_modules_info();
        if (isset($modulesinfo[$this->name]->version)) {
            $this->versiondb = $modulesinfo[$this->name]->version;
        }
    }

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {

        $modulesinfo = self::get_modules_info();
        if (isset($modulesinfo[$this->name]->visible)) {
            if ($modulesinfo[$this->name]->visible) {
                return true;
            } else {
                return false;
            }
        } else {
            return parent::is_enabled();
        }
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        if (file_exists($this->full_path('settings.php')) or file_exists($this->full_path('settingstree.php'))) {
            return new moodle_url('/admin/settings.php', array('section' => 'modsetting' . $this->name));
        } else {
            return parent::get_settings_url();
        }
    }

    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {

        if ($this->name !== 'forum') {
            return new moodle_url('/admin/modules.php', array('delete' => $this->name, 'sesskey' => sesskey()));
        } else {
            return null;
        }
    }

    /**
     * Provides access to the records in {modules} table
     *
     * @param bool $disablecache do not use internal static cache
     * @return array array of stdClasses
     */
    protected static function get_modules_info($disablecache=false) {
        global $DB;
        static $modulesinfocache = null;

        if (is_null($modulesinfocache) or $disablecache) {
            try {
                $modulesinfocache = $DB->get_records('modules', null, 'name', 'name,id,version,visible');
            } catch (dml_exception $e) {
                // before install
                $modulesinfocache = array();
            }
        }

        return $modulesinfocache;
    }
}


/**
 * Class for question behaviours.
 */
class plugintype_qbehaviour extends plugintype_base implements plugin_information {
    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {
        return new moodle_url('/admin/qbehaviours.php',
                array('delete' => $this->name, 'sesskey' => sesskey()));
    }
}


/**
 * Class for question types
 */
class plugintype_qtype extends plugintype_base implements plugin_information {
    /**
    * @see plugin_information::get_uninstall_url()
    */
    public function get_uninstall_url() {
        return new moodle_url('/admin/qtypes.php',
                array('delete' => $this->name, 'sesskey' => sesskey()));
    }
}


/**
 * Class for authentication plugins
 */
class plugintype_auth extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {
        global $CFG;
        /** @var null|array list of enabled authentication plugins */
        static $enabled = null;

        if (in_array($this->name, array('nologin', 'manual'))) {
            // these two are always enabled and can't be disabled
            return null;
        }

        if (is_null($enabled)) {
            $enabled = array_flip(explode(',', $CFG->auth));
        }

        return isset($enabled[$this->name]);
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {
        if (file_exists($this->full_path('settings.php'))) {
            return new moodle_url('/admin/settings.php', array('section' => 'authsetting' . $this->name));
        } else {
            return new moodle_url('/admin/auth_config.php', array('auth' => $this->name));
        }
    }
}

/**
 * Class for enrolment plugins
 */
class plugintype_enrol extends plugintype_base implements plugin_information {

    /**
     * We do not actually need whole enrolment classes here so we do not call
     * {@link enrol_get_plugins()}. Note that this may produce slightly different
     * results, for example if the enrolment plugin does not contain lib.php
     * but it is listed in $CFG->enrol_plugins_enabled
     *
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {
        global $CFG;
        /** @var null|array list of enabled enrolment plugins */
        static $enabled = null;

        if (is_null($enabled)) {
            $enabled = array_flip(explode(',', $CFG->enrol_plugins_enabled));
        }

        return isset($enabled[$this->name]);
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        if ($this->is_enabled() or file_exists($this->full_path('settings.php'))) {
            return new moodle_url('/admin/settings.php', array('section' => 'enrolsettings' . $this->name));
        } else {
            return parent::get_settings_url();
        }
    }

    /**
     * @see plugin_information::get_uninstall_url()
     */
    public function get_uninstall_url() {
        return new moodle_url('/admin/enrol.php', array('action' => 'uninstall', 'enrol' => $this->name, 'sesskey' => sesskey()));
    }
}

/**
 * Class for messaging processors
 */
class plugintype_message extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        if (file_exists($this->full_path('settings.php')) or file_exists($this->full_path('settingstree.php'))) {
            return new moodle_url('/admin/settings.php', array('section' => 'messagesetting' . $this->name));
        } else {
            return parent::get_settings_url();
        }
    }
}

/**
 * Class for repositories
 */
class plugintype_repository extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {

        $enabled = self::get_enabled_repositories();

        return isset($enabled[$this->name]);
    }

    /**
     * @see plugin_information::get_settings_url()
     */
    public function get_settings_url() {

        if ($this->is_enabled()) {
            return new moodle_url('/admin/repository.php', array('sesskey' => sesskey(), 'action' => 'edit', 'repos' => $this->name));
        } else {
            return parent::get_settings_url();
        }
    }

    /**
     * Provides access to the records in {repository} table
     *
     * @param bool $disablecache do not use internal static cache
     * @return array array of stdClasses
     */
    protected static function get_enabled_repositories($disablecache=false) {
        global $DB;
        static $repositories = null;

        if (is_null($repositories) or $disablecache) {
            $repositories = $DB->get_records('repository', null, 'type', 'type,visible,sortorder');
        }

        return $repositories;
    }
}

/**
 * Class for portfolios
 */
class plugintype_portfolio extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {

        $enabled = self::get_enabled_portfolios();

        return isset($enabled[$this->name]);
    }

    /**
     * Provides access to the records in {portfolio_instance} table
     *
     * @param bool $disablecache do not use internal static cache
     * @return array array of stdClasses
     */
    protected static function get_enabled_portfolios($disablecache=false) {
        global $DB;
        static $portfolios = null;

        if (is_null($portfolios) or $disablecache) {
            $portfolios = array();
            $instances  = $DB->get_recordset('portfolio_instance', null, 'plugin');
            foreach ($instances as $instance) {
                if (isset($portfolios[$instance->plugin])) {
                    if ($instance->visible) {
                        $portfolios[$instance->plugin]->visible = $instance->visible;
                    }
                } else {
                    $portfolios[$instance->plugin] = $instance;
                }
            }
        }

        return $portfolios;
    }
}

/**
 * Class for themes
 */
class plugintype_theme extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {
        global $CFG;

        if ((!empty($CFG->theme) and $CFG->theme === $this->name) or
            (!empty($CFG->themelegacy) and $CFG->themelegacy === $this->name)) {
            return true;
        } else {
            return parent::is_enabled();
        }
    }
}

/**
 * Class representing an MNet service
 */
class plugintype_mnetservice extends plugintype_base implements plugin_information {

    /**
     * @see plugin_information::is_enabled()
     */
    public function is_enabled() {
        global $CFG;

        if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
            return false;
        } else {
            return parent::is_enabled();
        }
    }
}

/**
 * Class for admin tool plugins
 */
class plugintype_tool extends plugintype_base implements plugin_information {

    public function get_uninstall_url() {
        return new moodle_url('/admin/tools.php', array('delete' => $this->name, 'sesskey' => sesskey()));
    }
}

/**
 * Class for admin tool plugins
 */
class plugintype_report extends plugintype_base implements plugin_information {

    public function get_uninstall_url() {
        return new moodle_url('/admin/reports.php', array('delete' => $this->name, 'sesskey' => sesskey()));
    }
}
