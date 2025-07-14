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
 * This file contains the base classes that are extended to create portfolio export functionality.
 *
 * For places in moodle that want to add export functionality to subclass.
 *
 * @package core_portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>, Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for callers
 *
 * @see also portfolio_module_caller_base
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class portfolio_caller_base {

    /** @var stdClass course active during the call */
    protected $course;

    /** @var array configuration used for export. Use set_export_config and get_export_config to access */
    protected $exportconfig = array();

    /** @var stdclass user currently exporting content */
    protected $user;

    /** @var stdClass a reference to the exporter object */
    protected $exporter;

    /** @var array can be optionally overridden by subclass constructors */
    protected $supportedformats;

    /** @var stored_file single file exports configuration*/
    protected $singlefile;

    /** @var stored_file|object set this for multi file exports */
    protected $multifiles;

    /** @var string set this for generated-file exports */
    protected $intendedmimetype;

    /**
     * Create portfolio_caller object
     *
     * @param array $callbackargs argument properties
     */
    public function __construct($callbackargs) {
        $expected = call_user_func(array(get_class($this), 'expected_callbackargs'));
        foreach ($expected as $key => $required) {
            if (!array_key_exists($key, $callbackargs)) {
                if ($required) {
                    $a = (object)array('arg' => $key, 'class' => get_class($this));
                    throw new portfolio_caller_exception('missingcallbackarg', 'portfolio', null, $a);
                }
                continue;
            }
            $this->{$key} = $callbackargs[$key];
        }
    }

    /**
     * If this caller wants any additional config items,
     * they should be defined here.
     *
     * @param moodleform $mform passed by reference, add elements to it.
     * @param portfolio_plugin_base $instance subclass of portfolio_plugin_base
     */
    public function export_config_form(&$mform, $instance) {}


    /**
     * Whether this caller wants any additional
     * config during export (eg options or metadata)
     *
     * @return bool
     */
    public function has_export_config() {
        return false;
    }

    /**
     * Just like the moodle form validation function,
     * this is passed in the data array from the form
     * and if a non empty array is returned, form processing will stop.
     *
     * @param array $data data from form.
     */
    public function export_config_validation($data) {}

    /**
     * How long does this reasonably expect to take..
     * Should we offer the user the option to wait..?
     * This is deliberately nonstatic so it can take filesize into account
     * the portfolio plugin can override this.
     * (so for example even if a huge file is being sent,
     * the download portfolio plugin doesn't care )
     */
    abstract public function expected_time();

    /**
     * Helper method to calculate expected time for multi or single file exports
     *
     * @return string file time expectation
     */
    public function expected_time_file() {
        if ($this->multifiles) {
            return portfolio_expected_time_file($this->multifiles);
        }
        else if ($this->singlefile) {
            return portfolio_expected_time_file($this->singlefile);
        }
        return PORTFOLIO_TIME_LOW;
    }

    /**
     * Function to build navigation
     */
    abstract public function get_navigation();

    /**
     * Helper function to get sha1
     */
    abstract public function get_sha1();

    /**
     * Helper function to calculate the sha1 for multi or single file exports
     *
     * @return string sha1 file exports
     */
    public function get_sha1_file() {
        if (empty($this->singlefile) && empty($this->multifiles)) {
            throw new portfolio_caller_exception('invalidsha1file', 'portfolio', $this->get_return_url());
        }
        if ($this->singlefile) {
            return $this->singlefile->get_contenthash();
        }
        $sha1s = array();
        foreach ($this->multifiles as $file) {
            $sha1s[] = $file->get_contenthash();
        }
        asort($sha1s);
        return sha1(implode('', $sha1s));
    }

    /**
     * Generic getter for properties belonging to this instance
     * <b>outside</b> the subclasses
     * like name, visible etc.
     *
     * @param string $field property's name
     * @return mixed
     * @throws portfolio_export_exception
     */
    public function get($field) {
        if (property_exists($this, $field)) {
            return $this->{$field};
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        throw new portfolio_export_exception($this->get('exporter'), 'invalidproperty', 'portfolio', $this->get_return_url(), $a);
    }

    /**
     * Generic setter for properties belonging to this instance
     * <b>outside</b> the subclass
     * like name, visible, etc.
     *
     * @param string $field property's name
     * @param mixed $value property's value
     * @return bool
     * @throws moodle_exception
     */
    final public function set($field, &$value) {
        if (property_exists($this, $field)) {
            $this->{$field} =& $value;
            return true;
        }
        $a = (object)array('property' => $field, 'class' => get_class($this));
        throw new portfolio_export_exception($this->get('exporter'), 'invalidproperty', 'portfolio', $this->get_return_url(), $a);
    }

    /**
     * Stores the config generated at export time.
     * Subclasses can retrieve values using
     * @see get_export_config
     *
     * @param array $config formdata
     */
    final public function set_export_config($config) {
        $allowed = array_merge(
            array('wait', 'hidewait', 'format', 'hideformat'),
            $this->get_allowed_export_config()
        );
        foreach ($config as $key => $value) {
            if (!in_array($key, $allowed)) {
                $a = (object)array('property' => $key, 'class' => get_class($this));
                throw new portfolio_export_exception($this->get('exporter'), 'invalidexportproperty', 'portfolio', $this->get_return_url(), $a);
            }
            $this->exportconfig[$key] = $value;
        }
    }

    /**
     * Returns a particular export config value.
     * Subclasses shouldn't need to override this
     *
     * @param string $key the config item to fetch
     * @return null|mixed of export configuration
     */
    final public function get_export_config($key) {
        $allowed = array_merge(
            array('wait', 'hidewait', 'format', 'hideformat'),
            $this->get_allowed_export_config()
        );
        if (!in_array($key, $allowed)) {
            $a = (object)array('property' => $key, 'class' => get_class($this));
            throw new portfolio_export_exception($this->get('exporter'), 'invalidexportproperty', 'portfolio', $this->get_return_url(), $a);
        }
        if (!array_key_exists($key, $this->exportconfig)) {
            return null;
        }
        return $this->exportconfig[$key];
    }

    /**
     * Similar to the other allowed_config functions
     * if you need export config, you must provide
     * a list of what the fields are.
     * Even if you want to store stuff during export
     * without displaying a form to the user,
     * you can use this.
     *
     * @return array array of allowed keys
     */
    public function get_allowed_export_config() {
        return array();
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
     * Called before the portfolio plugin gets control.
     * This function should copy all the files it wants to
     * the temporary directory, using copy_existing_file
     * or write_new_file
     *
     * @see copy_existing_file()
     * @see write_new_file()
     */
    abstract public function prepare_package();

    /**
     * Helper function to copy files into the temp area
     * for single or multi file exports.
     *
     * @return stored_file|bool
     */
    public function prepare_package_file() {
        if (empty($this->singlefile) && empty($this->multifiles)) {
            throw new portfolio_caller_exception('invalidpreparepackagefile', 'portfolio', $this->get_return_url());
        }
        if ($this->singlefile) {
            return $this->exporter->copy_existing_file($this->singlefile);
        }
        foreach ($this->multifiles as $file) {
            $this->exporter->copy_existing_file($file);
        }
    }

    /**
     * Array of formats this caller supports.
     *
     * @return array list of formats
     */
    final public function supported_formats() {
        $basic = $this->base_supported_formats();
        if (empty($this->supportedformats)) {
            $specific = array();
        } else if (!is_array($this->supportedformats)) {
            debugging(get_class($this) . ' has set a non array value of member variable supported formats - working around but should be fixed in code');
            $specific = array($this->supportedformats);
        } else {
            $specific = $this->supportedformats;
        }
        return portfolio_most_specific_formats($specific, $basic);
    }

    /**
     * Base supported formats
     *
     * @throws coding_exception
     */
    public static function base_supported_formats() {
        throw new coding_exception('base_supported_formats() method needs to be overridden in each subclass of portfolio_caller_base');
    }

    /**
     * This is the "return to where you were" url
     */
    abstract public function get_return_url();

    /**
     * Callback to do whatever capability checks required
     * in the caller (called during the export process
     */
    abstract public function check_permissions();

    /**
     * Clean name to display to the user about this caller location
     */
    public static function display_name() {
        throw new coding_exception('display_name() method needs to be overridden in each subclass of portfolio_caller_base');
    }

    /**
     * Return a string to put at the header summarising this export.
     * By default, it just display the name (usually just 'assignment' or something unhelpful
     *
     * @return string
     */
    public function heading_summary() {
        return get_string('exportingcontentfrom', 'portfolio', $this->display_name());
    }

    /**
     * Load data
     */
    abstract public function load_data();

    /**
     * Set up the required files for this export.
     * This supports either passing files directly
     * or passing area arguments directly through
     * to the files api using file_storage::get_area_files
     *
     * @param mixed $ids one of:
     *                   - single file id
     *                   - single stored_file object
     *                   - array of file ids or stored_file objects
     *                   - null
     * @return void
     */
    public function set_file_and_format_data($ids=null /* ..pass arguments to area files here. */) {
        $args = func_get_args();
        array_shift($args); // shift off $ids
        if (empty($ids) && count($args) == 0) {
            return;
        }
        $files = array();
        $fs = get_file_storage();
        if (!empty($ids)) {
            if (is_numeric($ids) || $ids instanceof stored_file) {
                $ids = array($ids);
            }
            foreach ($ids as $id) {
                if ($id instanceof stored_file) {
                    $files[] = $id;
                } else {
                    $files[] = $fs->get_file_by_id($id);
                }
            }
        } else if (count($args) != 0) {
            if (count($args) < 4) {
                throw new portfolio_caller_exception('invalidfileareaargs', 'portfolio');
            }
            $files = array_values(call_user_func_array(array($fs, 'get_area_files'), $args));
        }
        switch (count($files)) {
            case 0: return;
            case 1: {
                $this->singlefile = $files[0];
                return;
            }
            default: {
                $this->multifiles = $files;
            }
        }
    }

    /**
     * The button-location always knows best
     * what the formats are... so it should be trusted.
     *
     * @todo MDL-31298 - re-analyze set_formats_from_button comment
     * @param array $formats array of PORTFOLIO_FORMAT_XX
     * @return void
     */
    public function set_formats_from_button($formats) {
        $base = $this->base_supported_formats();
        if (count($base) != count($formats)
                || count($base) != count(array_intersect($base, $formats))) {
                $this->supportedformats = portfolio_most_specific_formats($formats, $base);
                return;
        }
        // in the case where the button hasn't actually set anything,
        // we need to run through again and resolve conflicts
        // TODO revisit this comment - it looks to me like it's lying
        $this->supportedformats = portfolio_most_specific_formats($formats, $formats);
    }

    /**
     * Adds a new format to the list of supported formats.
     * This functions also handles removing conflicting and less specific
     * formats at the same time.
     *
     * @param string $format one of PORTFOLIO_FORMAT_XX
     * @return void
     */
    protected function add_format($format) {
        if (in_array($format, $this->supportedformats)) {
            return;
        }
        $this->supportedformats = portfolio_most_specific_formats(array($format), $this->supportedformats);
    }

    /**
     * Gets mimetype
     *
     * @return string
     */
    public function get_mimetype() {
        if ($this->singlefile instanceof stored_file) {
            return $this->singlefile->get_mimetype();
        } else if (!empty($this->intendedmimetype)) {
            return $this->intendedmimetype;
        }
    }

    /**
     * Array of arguments the caller expects to be passed through to it.
     * This must be keyed on the argument name, and the array value is a boolean,
     * whether it is required, or just optional
     * eg array(
     *     id            => true,
     *     somethingelse => false
     * )
     */
    public static function expected_callbackargs() {
        throw new coding_exception('expected_callbackargs() method needs to be overridden in each subclass of portfolio_caller_base');
    }


    /**
     * Return the context for this export. used for $PAGE->set_context
     *
     * @param moodle_page $PAGE global page object
     */
    abstract public function set_context($PAGE);
}

/**
 * Base class for module callers.
 *
 * This just implements a few of the abstract functions
 * from portfolio_caller_base so that caller authors
 * don't need to.
 * @see also portfolio_caller_base
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class portfolio_module_caller_base extends portfolio_caller_base {

    /** @var object coursemodule object. set this in the constructor like $this->cm = get_coursemodule_from_instance('forum', $this->forum->id); */
    protected $cm;

    /** @var int cmid */
    protected $id;

    /** @var stdclass course object */
    protected $course;

    /**
     * Navigation passed to print_header.
     * Override this to do something more specific than the module view page
     * like adding more links to the breadcrumb.
     *
     * @return array
     */
    public function get_navigation() {
        // No extra navigation by default, link to the course module already included.
        $extranav = array();
        return array($extranav, $this->cm);
    }

    /**
     * The url to return to after export or on cancel.
     * Defaults value is set to the module 'view' page.
     * Override this if it's deeper inside the module.
     *
     * @return string
     */
    public function get_return_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/' . $this->cm->modname . '/view.php?id=' . $this->cm->id;
    }

    /**
     * Override the parent get function
     * to make sure when we're asked for a course,
     * We retrieve the object from the database as needed.
     *
     * @param string $key the name of get function
     * @return stdClass
     */
    public function get($key) {
        if ($key != 'course') {
            return parent::get($key);
        }
        global $DB;
        if (empty($this->course)) {
            $this->course = $DB->get_record('course', array('id' => $this->cm->course));
        }
        return $this->course;
    }

    /**
     * Return a string to put at the header summarising this export.
     * by default, this function just display the name and module instance name.
     * Override this to do something more specific
     *
     * @return string
     */
    public function heading_summary() {
        return get_string('exportingcontentfrom', 'portfolio', $this->display_name() . ': ' . $this->cm->name);
    }

    /**
     * Overridden to return the course module context
     *
     * @param moodle_page $PAGE global PAGE
     */
    public function set_context($PAGE) {
        $PAGE->set_cm($this->cm);
    }
}
