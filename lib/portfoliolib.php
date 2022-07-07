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
 * This file contains all global functions to do with manipulating portfolios.
 *
 * Everything else that is logically namespaced by class is in its own file
 * in lib/portfolio/ directory.
 *
 * Major Contributors
 *     - Penny Leach <penny@catalyst.net.nz>
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// require some of the sublibraries first.
// this is not an exhaustive list, the others are pulled in as they're needed
// so we don't have to always include everything unnecessarily for performance

// very lightweight list of constants. always needed and no further dependencies
require_once($CFG->libdir . '/portfolio/constants.php');
// a couple of exception deinitions. always needed and no further dependencies
require_once($CFG->libdir . '/portfolio/exceptions.php');  // exception classes used by portfolio code
// The base class for the caller classes. We always need this because we're either drawing a button,
// in which case the button needs to know the calling class definition, which requires the base class,
// or we're exporting, in which case we need the caller class anyway.
require_once($CFG->libdir . '/portfolio/caller.php');

// the other dependencies are included on demand:
// libdir/portfolio/formats.php  - the classes for the export formats
// libdir/portfolio/forms.php    - all portfolio form classes (requires formslib)
// libdir/portfolio/plugin.php   - the base class for the export plugins
// libdir/portfolio/exporter.php - the exporter class


/**
 * Use this to add a portfolio button or icon or form to a page.
 *
 * These class methods do not check permissions. the caller must check permissions first.
 * Later, during the export process, the caller class is instantiated and the check_permissions method is called
 * If you are exporting a single file, you should always call set_format_by_file($file)
 * This class can be used like this:
 * <code>
 * $button = new portfolio_add_button();
 * $button->set_callback_options('name_of_caller_class', array('id' => 6), 'yourcomponent'); eg. mod_forum
 * $button->render(PORTFOLIO_ADD_FULL_FORM, get_string('addeverythingtoportfolio', 'yourcomponent'));
 * </code>
 * or like this:
 * <code>
 * $button = new portfolio_add_button(array('callbackclass' => 'name_of_caller_class', 'callbackargs' => array('id' => 6), 'callbackcomponent' => 'yourcomponent')); eg. mod_forum
 * $somehtml .= $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
 * </code>
 *{@link http://docs.moodle.org/dev/Adding_a_Portfolio_Button_to_a_page} for more information
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_add_button {

    /** @var string the name of the callback functions */
    private $callbackclass;

    /** @var array can be an array of arguments to pass back to the callback functions (passed by reference)*/
    private $callbackargs;

    /** @var string caller file */
    private $callbackcomponent;

    /** @var array array of more specific formats (eg based on mime detection) */
    private $formats;

    /** @var array array of portfolio instances */
    private $instances;

    /** @var stored_file for single-file exports */
    private $file;

    /** @var string for writing specific types of files*/
    private $intendedmimetype;

    /**
     * Constructor. Either pass the options here or set them using the helper methods.
     * Generally the code will be clearer if you use the helper methods.
     *
     * @param array $options keyed array of options:
     *                       key 'callbackclass': name of the caller class (eg forum_portfolio_caller')
     *                       key 'callbackargs': the array of callback arguments your caller class wants passed to it in the constructor
     *                       key 'callbackcomponent': the file containing the class definition of your caller class.
     *                       See set_callback_options for more information on these three.
     *                       key 'formats': an array of PORTFOLIO_FORMATS this caller will support
     *                       See set_formats or set_format_by_file for more information on this.
     */
    public function __construct($options=null) {
        global $SESSION, $CFG;

        if (empty($CFG->enableportfolios)) {
            debugging('Building portfolio add button while portfolios is disabled. This code can be optimised.', DEBUG_DEVELOPER);
        }

        $cache = cache::make('core', 'portfolio_add_button_portfolio_instances');
        $instances = $cache->get('instances');
        if ($instances === false) {
            $instances = portfolio_instances();
            $cache->set('instances', $instances);
        }

        $this->instances = $instances;
        if (empty($options)) {
            return true;
        }
        $constructoroptions = array('callbackclass', 'callbackargs', 'callbackcomponent');
        foreach ((array)$options as $key => $value) {
            if (!in_array($key, $constructoroptions)) {
                throw new portfolio_button_exception('invalidbuttonproperty', 'portfolio', $key);
            }
        }

        $this->set_callback_options($options['callbackclass'], $options['callbackargs'], $options['callbackcomponent']);
    }

    /**
     * Function to set the callback options
     *
     * @param string $class Name of the class containing the callback functions
     *      activity components should ALWAYS use their name_portfolio_caller
     *      other locations must use something unique
     * @param array $argarray This can be an array or hash of arguments to pass
     *      back to the callback functions (passed by reference)
     *      these MUST be primatives to be added as hidden form fields.
     *      and the values get cleaned to PARAM_ALPHAEXT or PARAM_FLOAT or PARAM_PATH
     * @param string $component This is the name of the component in Moodle, eg 'mod_forum'
     */
    public function set_callback_options($class, array $argarray, $component) {
        global $CFG;

        // Require the base class first before any other files.
        require_once($CFG->libdir . '/portfolio/caller.php');

        // Include any potential callback files and check for errors.
        portfolio_include_callback_file($component, $class);

        // This will throw exceptions but should not actually do anything other than verify callbackargs.
        $test = new $class($argarray);
        unset($test);

        $this->callbackcomponent = $component;
        $this->callbackclass = $class;
        $this->callbackargs = $argarray;
    }

    /**
     * Sets the available export formats for this content.
     * This function will also poll the static function in the caller class
     * and make sure we're not overriding a format that has nothing to do with mimetypes.
     * Eg: if you pass IMAGE here but the caller can export LEAP2A it will keep LEAP2A as well.
     * @see portfolio_most_specific_formats for more information
     * @see portfolio_format_from_mimetype
     *
     * @param array $formats if the calling code knows better than the static method on the calling class (base_supported_formats).
     *                       Eg: if it's going to be a single file, or if you know it's HTML, you can pass it here instead.
     *                       This is almost always the case so it should be use all the times
     *                       portfolio_format_from_mimetype for how to get the appropriate formats to pass here for uploaded files.
     *                       or just call set_format_by_file instead
     */
    public function set_formats($formats=null) {
        if (is_string($formats)) {
            $formats = array($formats);
        }
        if (empty($formats)) {
            $formats = array();
        }
        if (empty($this->callbackclass)) {
            throw new portfolio_button_exception('noclassbeforeformats', 'portfolio');
        }
        $callerformats = call_user_func(array($this->callbackclass, 'base_supported_formats'));
        $this->formats = portfolio_most_specific_formats($formats, $callerformats);
    }

    /**
     * Reset formats to the default,
     * which is usually what base_supported_formats returns
     */
    public function reset_formats() {
        $this->set_formats();
    }


    /**
     * If we already know we have exactly one file,
     * bypass set_formats and just pass the file
     * so we can detect the formats by mimetype.
     *
     * @param stored_file $file file to set the format from
     * @param array $extraformats any additional formats other than by mimetype
     *                            eg leap2a etc
     */
    public function set_format_by_file(stored_file $file, $extraformats=null) {
        $this->file = $file;
        $fileformat = portfolio_format_from_mimetype($file->get_mimetype());
        if (is_string($extraformats)) {
            $extraformats = array($extraformats);
        } else if (!is_array($extraformats)) {
            $extraformats = array();
        }
        $this->set_formats(array_merge(array($fileformat), $extraformats));
    }

    /**
     * Correllary this is use to set_format_by_file, but it is also used when there is no stored_file and
     * when we're writing out a new type of file (like csv or pdf)
     *
     * @param string $extn the file extension we intend to generate
     * @param array  $extraformats any additional formats other than by mimetype
     *                             eg leap2a etc
     */
    public function set_format_by_intended_file($extn, $extraformats=null) {
        $mimetype = mimeinfo('type', 'something. ' . $extn);
        $fileformat = portfolio_format_from_mimetype($mimetype);
        $this->intendedmimetype = $fileformat;
        if (is_string($extraformats)) {
            $extraformats = array($extraformats);
        } else if (!is_array($extraformats)) {
            $extraformats = array();
        }
        $this->set_formats(array_merge(array($fileformat), $extraformats));
    }

    /**
     * Echo the form/button/icon/text link to the page
     *
     * @param int $format format to display the button or form or icon or link.
     *                    See constants PORTFOLIO_ADD_XXX for more info.
     *                    optional, defaults to PORTFOLIO_ADD_FULL_FORM
     * @param string $addstr string to use for the button or icon alt text or link text.
     *                       this is whole string, not key. optional, defaults to 'Export to portfolio';
     */
    public function render($format=null, $addstr=null) {
        echo $this->to_html($format, $addstr);
    }

    /**
     * Returns the form/button/icon/text link as html
     *
     * @param int $format format to display the button or form or icon or link.
     *                    See constants PORTFOLIO_ADD_XXX for more info.
     *                    Optional, defaults to PORTFOLIO_ADD_FULL_FORM
     * @param string $addstr string to use for the button or icon alt text or link text.
     *                       This is whole string, not key.  optional, defaults to 'Add to portfolio';
     * @return void|string|moodle_url
     */
    public function to_html($format=null, $addstr=null) {
        global $CFG, $COURSE, $OUTPUT, $USER;
        if (!$this->is_renderable()) {
            return;
        }
        if (empty($this->callbackclass) || empty($this->callbackcomponent)) {
            throw new portfolio_button_exception('mustsetcallbackoptions', 'portfolio');
        }
        if (empty($this->formats)) {
            // use the caller defaults
            $this->set_formats();
        }
        $url = new moodle_url('/portfolio/add.php');
        foreach ($this->callbackargs as $key => $value) {
            if (!empty($value) && !is_string($value) && !is_numeric($value)) {
                $a = new stdClass();
                $a->key = $key;
                $a->value = print_r($value, true);
                debugging(get_string('nonprimative', 'portfolio', $a));
                return;
            }
            $url->param('ca_' . $key, $value);
        }
        $url->param('sesskey', sesskey());
        $url->param('callbackcomponent', $this->callbackcomponent);
        $url->param('callbackclass', $this->callbackclass);
        $url->param('course', (!empty($COURSE)) ? $COURSE->id : 0);
        $url->param('callerformats', implode(',', $this->formats));
        $mimetype = null;
        if ($this->file instanceof stored_file) {
            $mimetype = $this->file->get_mimetype();
        } else if ($this->intendedmimetype) {
            $mimetype = $this->intendedmimetype;
        }
        $selectoutput = '';
        if (count($this->instances) == 1) {
            $tmp = array_values($this->instances);
            $instance = $tmp[0];

            $formats = portfolio_supported_formats_intersect($this->formats, $instance->supported_formats());
            if (count($formats) == 0) {
                // bail. no common formats.
                //debugging(get_string('nocommonformats', 'portfolio', (object)array('location' => $this->callbackclass, 'formats' => implode(',', $this->formats))));
                return;
            }
            if ($error = portfolio_instance_sanity_check($instance)) {
                // bail, plugin is misconfigured
                //debugging(get_string('instancemisconfigured', 'portfolio', get_string($error[$instance->get('id')], 'portfolio_' . $instance->get('plugin'))));
                return;
            }
            if (!$instance->allows_multiple_exports() && $already = portfolio_existing_exports($USER->id, $instance->get('plugin'))) {
                //debugging(get_string('singleinstancenomultiallowed', 'portfolio'));
                return;
            }
            if ($mimetype&& !$instance->file_mime_check($mimetype)) {
                // bail, we have a specific file or mimetype and this plugin doesn't support it
                //debugging(get_string('mimecheckfail', 'portfolio', (object)array('plugin' => $instance->get('plugin'), 'mimetype' => $mimetype)));
                return;
            }
            $url->param('instance', $instance->get('id'));
        }
        else {
            if (!$selectoutput = portfolio_instance_select($this->instances, $this->formats, $this->callbackclass, $mimetype, 'instance', true)) {
                return;
            }
        }
        // If we just want a moodle_url to redirect to, do it now.
        if ($format == PORTFOLIO_ADD_MOODLE_URL) {
            return $url;
        }

        // if we just want a url to redirect to, do it now
        if ($format == PORTFOLIO_ADD_FAKE_URL) {
            return $url->out(false);
        }

        if (empty($addstr)) {
            $addstr = get_string('addtoportfolio', 'portfolio');
        }
        if (empty($format)) {
            $format = PORTFOLIO_ADD_FULL_FORM;
        }

        $formoutput = '<form method="post" action="' . $CFG->wwwroot . '/portfolio/add.php" id="portfolio-add-button">' . "\n";
        $formoutput .= html_writer::input_hidden_params($url);
        $linkoutput = '';

        switch ($format) {
            case PORTFOLIO_ADD_FULL_FORM:
                $formoutput .= $selectoutput;
                $formoutput .= "\n" . '<input type="submit" class="btn btn-secondary" value="' . $addstr .'" />';
                $formoutput .= "\n" . '</form>';
            break;
            case PORTFOLIO_ADD_ICON_FORM:
                $formoutput .= $selectoutput;
                $formoutput .= "\n" . '<button class="portfolio-add-icon">' . $OUTPUT->pix_icon('t/portfolioadd', $addstr) . '</button>';
                $formoutput .= "\n" . '</form>';
            break;
            case PORTFOLIO_ADD_ICON_LINK:
                $linkoutput = $OUTPUT->action_icon($url, new pix_icon('t/portfolioadd', $addstr, '',
                    array('class' => 'portfolio-add-icon smallicon')));
            break;
            case PORTFOLIO_ADD_TEXT_LINK:
                $linkoutput = html_writer::link($url, $addstr, array('class' => 'portfolio-add-link',
                    'title' => $addstr));
            break;
            default:
                debugging(get_string('invalidaddformat', 'portfolio', $format));
        }
        $output = (in_array($format, array(PORTFOLIO_ADD_FULL_FORM, PORTFOLIO_ADD_ICON_FORM)) ? $formoutput : $linkoutput);
        return $output;
    }

    /**
     * Perform some internal checks.
     * These are not errors, just situations
     * where it's not appropriate to add the button
     *
     * @return bool
     */
    private function is_renderable() {
        global $CFG;
        if (empty($CFG->enableportfolios)) {
            return false;
        }
        if (defined('PORTFOLIO_INTERNAL')) {
            // something somewhere has detected a risk of this being called during inside the preparation
            // eg forum_print_attachments
            return false;
        }
        if (empty($this->instances) || count($this->instances) == 0) {
            return false;
        }
        return true;
    }

    /**
     * Getter for $format property
     *
     * @return array
     */
    public function get_formats() {
        return $this->formats;
    }

    /**
     * Getter for $callbackargs property
     *
     * @return array
     */
    public function get_callbackargs() {
        return $this->callbackargs;
    }

    /**
     * Getter for $callbackcomponent property
     *
     * @return string
     */
    public function get_callbackcomponent() {
        return $this->callbackcomponent;
    }

    /**
     * Getter for $callbackclass property
     *
     * @return string
     */
    public function get_callbackclass() {
        return $this->callbackclass;
    }
}

/**
 * Returns a drop menu with a list of available instances.
 *
 * @param array          $instances      array of portfolio plugin instance objects - the instances to put in the menu
 * @param array          $callerformats  array of PORTFOLIO_FORMAT_XXX constants - the formats the caller supports (this is used to filter plugins)
 * @param string         $callbackclass  the callback class name - used for debugging only for when there are no common formats
 * @param string         $mimetype       if we already know we have exactly one file, or are going to write one, pass it here to do mime filtering.
 * @param string         $selectname     the name of the select element. Optional, defaults to instance.
 * @param bool           $return         whether to print or return the output. Optional, defaults to print.
 * @param bool           $returnarray    if returning, whether to return the HTML or the array of options. Optional, defaults to HTML.
 * @return void|array|string the html, from <select> to </select> inclusive.
 */
function portfolio_instance_select($instances, $callerformats, $callbackclass, $mimetype=null, $selectname='instance', $return=false, $returnarray=false) {
    global $CFG, $USER;

    if (empty($CFG->enableportfolios)) {
        return;
    }

    $insane = portfolio_instance_sanity_check();
    $pinsane = portfolio_plugin_sanity_check();

    $count = 0;
    $selectoutput = "\n" . '<label class="accesshide" for="instanceid">' . get_string('plugin', 'portfolio') . '</label>';
    $selectoutput .= "\n" . '<select id="instanceid" name="' . $selectname . '" class="custom-select">' . "\n";
    $existingexports = portfolio_existing_exports_by_plugin($USER->id);
    foreach ($instances as $instance) {
        $formats = portfolio_supported_formats_intersect($callerformats, $instance->supported_formats());
        if (count($formats) == 0) {
            // bail. no common formats.
            continue;
        }
        if (array_key_exists($instance->get('id'), $insane)) {
            // bail, plugin is misconfigured
            //debugging(get_string('instanceismisconfigured', 'portfolio', get_string($insane[$instance->get('id')], 'portfolio_' . $instance->get('plugin'))));
            continue;
        } else if (array_key_exists($instance->get('plugin'), $pinsane)) {
            // bail, plugin is misconfigured
            //debugging(get_string('pluginismisconfigured', 'portfolio', get_string($pinsane[$instance->get('plugin')], 'portfolio_' . $instance->get('plugin'))));
            continue;
        }
        if (!$instance->allows_multiple_exports() && in_array($instance->get('plugin'), $existingexports)) {
            // bail, already exporting something with this plugin and it doesn't support multiple exports
            continue;
        }
        if ($mimetype && !$instance->file_mime_check($mimetype)) {
            //debugging(get_string('mimecheckfail', 'portfolio', (object)array('plugin' => $instance->get('plugin'), 'mimetype' => $mimetype())));
            // bail, we have a specific file and this plugin doesn't support it
            continue;
        }
        $count++;
        $selectoutput .= "\n" . '<option value="' . $instance->get('id') . '">' . $instance->get('name') . '</option>' . "\n";
        $options[$instance->get('id')] = $instance->get('name');
    }
    if (empty($count)) {
        // bail. no common formats.
        //debugging(get_string('nocommonformats', 'portfolio', (object)array('location' => $callbackclass, 'formats' => implode(',', $callerformats))));
        return;
    }
    $selectoutput .= "\n" . "</select>\n";
    if (!empty($returnarray)) {
        return $options;
    }
    if (!empty($return)) {
        return $selectoutput;
    }
    echo $selectoutput;
}

/**
 * Return all portfolio instances
 *
 * @todo MDL-15768 - check capabilities here
 * @param bool $visibleonly Don't include hidden instances. Defaults to true and will be overridden to true if the next parameter is true
 * @param bool $useronly    Check the visibility preferences and permissions of the logged in user. Defaults to true.
 * @return array of portfolio instances (full objects, not just database records)
 */
function portfolio_instances($visibleonly=true, $useronly=true) {

    global $DB, $USER;

    $values = array();
    $sql = 'SELECT * FROM {portfolio_instance}';

    if ($visibleonly || $useronly) {
        $values[] = 1;
        $sql .= ' WHERE visible = ?';
    }
    if ($useronly) {
        $sql .= ' AND id NOT IN (
                SELECT instance FROM {portfolio_instance_user}
                WHERE userid = ? AND name = ? AND ' . $DB->sql_compare_text('value') . ' = ?
            )';
        $values = array_merge($values, array($USER->id, 'visible', 0));
    }
    $sql .= ' ORDER BY name';

    $instances = array();
    foreach ($DB->get_records_sql($sql, $values) as $instance) {
        $instances[$instance->id] = portfolio_instance($instance->id, $instance);
    }
    return $instances;
}

/**
 * Return whether there are visible instances in portfolio.
 *
 * @return bool true when there are some visible instances.
 */
function portfolio_has_visible_instances() {
    global $DB;
    return $DB->record_exists('portfolio_instance', array('visible' => 1));
}

/**
 * Supported formats currently in use.
 * Canonical place for a list of all formats
 * that portfolio plugins and callers
 * can use for exporting content
 *
 * @return array keyed array of all the available export formats (constant => classname)
 */
function portfolio_supported_formats() {
    return array(
        PORTFOLIO_FORMAT_FILE         => 'portfolio_format_file',
        PORTFOLIO_FORMAT_IMAGE        => 'portfolio_format_image',
        PORTFOLIO_FORMAT_RICHHTML     => 'portfolio_format_richhtml',
        PORTFOLIO_FORMAT_PLAINHTML    => 'portfolio_format_plainhtml',
        PORTFOLIO_FORMAT_TEXT         => 'portfolio_format_text',
        PORTFOLIO_FORMAT_VIDEO        => 'portfolio_format_video',
        PORTFOLIO_FORMAT_PDF          => 'portfolio_format_pdf',
        PORTFOLIO_FORMAT_DOCUMENT     => 'portfolio_format_document',
        PORTFOLIO_FORMAT_SPREADSHEET  => 'portfolio_format_spreadsheet',
        PORTFOLIO_FORMAT_PRESENTATION => 'portfolio_format_presentation',
        /*PORTFOLIO_FORMAT_MBKP, */ // later
        PORTFOLIO_FORMAT_LEAP2A       => 'portfolio_format_leap2a',
        PORTFOLIO_FORMAT_RICH         => 'portfolio_format_rich',
    );
}

/**
 * Deduce export format from file mimetype
 * This function returns the revelant portfolio export format
 * which is used to determine which portfolio plugins can be used
 * for exporting this content
 * according to the given mime type
 * this only works when exporting exactly <b>one</b> file, or generating a new one
 * (like a pdf or csv export)
 *
 * @param string $mimetype (usually $file->get_mimetype())
 * @return string the format constant (see PORTFOLIO_FORMAT_XXX constants)
 */
function portfolio_format_from_mimetype($mimetype) {
    global $CFG;
    static $alreadymatched;
    if (empty($alreadymatched)) {
        $alreadymatched = array();
    }
    if (array_key_exists($mimetype, $alreadymatched)) {
        return $alreadymatched[$mimetype];
    }
    $allformats = portfolio_supported_formats();
    require_once($CFG->libdir . '/portfolio/formats.php');
    foreach ($allformats as $format => $classname) {
        $supportedmimetypes = call_user_func(array($classname, 'mimetypes'));
        if (!is_array($supportedmimetypes)) {
            debugging("one of the portfolio format classes, $classname, said it supported something funny for mimetypes, should have been array...");
            debugging(print_r($supportedmimetypes, true));
            continue;
        }
        if (in_array($mimetype, $supportedmimetypes)) {
            $alreadymatched[$mimetype] = $format;
            return $format;
        }
    }
    return PORTFOLIO_FORMAT_FILE; // base case for files...
}

/**
 * Intersection of plugin formats and caller formats.
 * Walks both the caller formats and portfolio plugin formats
 * and looks for matches (walking the hierarchy as well)
 * and returns the intersection
 *
 * @param array $callerformats formats the caller supports
 * @param array $pluginformats formats the portfolio plugin supports
 * @return array
 */
function portfolio_supported_formats_intersect($callerformats, $pluginformats) {
    global $CFG;
    $allformats = portfolio_supported_formats();
    $intersection = array();
    foreach ($callerformats as $cf) {
        if (!array_key_exists($cf, $allformats)) {
            if (!portfolio_format_is_abstract($cf)) {
                debugging(get_string('invalidformat', 'portfolio', $cf));
            }
            continue;
        }
        require_once($CFG->libdir . '/portfolio/formats.php');
        $cfobj = new $allformats[$cf]();
        foreach ($pluginformats as $p => $pf) {
            if (!array_key_exists($pf, $allformats)) {
                if (!portfolio_format_is_abstract($pf)) {
                    debugging(get_string('invalidformat', 'portfolio', $pf));
                }
                unset($pluginformats[$p]); // to avoid the same warning over and over
                continue;
            }
            if ($cfobj instanceof $allformats[$pf]) {
                $intersection[] = $cf;
            }
        }
    }
    return $intersection;
}

/**
 * Tiny helper to figure out whether a portfolio format is abstract
 *
 * @param string $format the format to test
 * @return bool
 */
function portfolio_format_is_abstract($format) {
    if (class_exists($format)) {
        $class = $format;
    } else if (class_exists('portfolio_format_' . $format)) {
        $class = 'portfolio_format_' . $format;
    } else {
        $allformats = portfolio_supported_formats();
        if (array_key_exists($format, $allformats)) {
            $class = $allformats[$format];
        }
    }
    if (empty($class)) {
        return true; // it may as well be, we can't instantiate it :)
    }
    $rc = new ReflectionClass($class);
    return $rc->isAbstract();
}

/**
 * Return the combination of the two arrays of formats with duplicates in terms of specificity removed
 * and also removes conflicting formats.
 * Use case: a module is exporting a single file, so the general formats would be FILE and MBKP
 *           while the specific formats would be the specific subclass of FILE based on mime (say IMAGE)
 *           and this function would return IMAGE and MBKP
 *
 * @param array $specificformats array of more specific formats (eg based on mime detection)
 * @param array $generalformats  array of more general formats (usually more supported)
 * @return array merged formats with dups removed
 */
function portfolio_most_specific_formats($specificformats, $generalformats) {
    global $CFG;
    $allformats = portfolio_supported_formats();
    if (empty($specificformats)) {
        return $generalformats;
    } else if (empty($generalformats)) {
        return $specificformats;
    }
    $removedformats = array();
    foreach ($specificformats as $k => $f) {
        // look for something less specific and remove it, ie outside of the inheritance tree of the current formats.
        if (!array_key_exists($f, $allformats)) {
            if (!portfolio_format_is_abstract($f)) {
                throw new portfolio_button_exception('invalidformat', 'portfolio', $f);
            }
        }
        if (in_array($f, $removedformats)) {
            // already been removed from the general list
            //debugging("skipping $f because it was already removed");
            unset($specificformats[$k]);
        }
        require_once($CFG->libdir . '/portfolio/formats.php');
        $fobj = new $allformats[$f];
        foreach ($generalformats as $key => $cf) {
            if (in_array($cf, $removedformats)) {
                //debugging("skipping $cf because it was already removed");
                continue;
            }
            $cfclass = $allformats[$cf];
            $cfobj = new $allformats[$cf];
            if ($fobj instanceof $cfclass && $cfclass != get_class($fobj)) {
                //debugging("unsetting $key $cf because it's not specific enough ($f is better)");
                unset($generalformats[$key]);
                $removedformats[] = $cf;
                continue;
            }
            // check for conflicts
            if ($fobj->conflicts($cf)) {
                //debugging("unsetting $key $cf because it conflicts with $f");
                unset($generalformats[$key]);
                $removedformats[] = $cf;
                continue;
            }
            if ($cfobj->conflicts($f)) {
                //debugging("unsetting $key $cf because it reverse-conflicts with $f");
                $removedformats[] = $cf;
                unset($generalformats[$key]);
                continue;
            }
        }
        //debugging('inside loop');
        //print_object($generalformats);
    }

    //debugging('final formats');
    $finalformats =  array_unique(array_merge(array_values($specificformats), array_values($generalformats)));
    //print_object($finalformats);
    return $finalformats;
}

/**
 * Helper function to return a format object from the constant
 *
 * @param string $name the constant PORTFOLIO_FORMAT_XXX
 * @return portfolio_format
 */
function portfolio_format_object($name) {
    global $CFG;
    require_once($CFG->libdir . '/portfolio/formats.php');
    $formats = portfolio_supported_formats();
    return new $formats[$name];
}

/**
 * Helper function to return an instance of a plugin (with config loaded)
 *
 * @param int   $instanceid id of instance
 * @param object $record database row that corresponds to this instance
 *                       this is passed to avoid unnecessary lookups
 *                       Optional, and the record will be retrieved if null.
 * @return object of portfolio_plugin_XXX
 */
function portfolio_instance($instanceid, $record=null) {
    global $DB, $CFG;

    if ($record) {
        $instance  = $record;
    } else {
        if (!$instance = $DB->get_record('portfolio_instance', array('id' => $instanceid))) {
            throw new portfolio_exception('invalidinstance', 'portfolio');
        }
    }
    require_once($CFG->libdir . '/portfolio/plugin.php');
    require_once($CFG->dirroot . '/portfolio/'. $instance->plugin . '/lib.php');
    $classname = 'portfolio_plugin_' . $instance->plugin;
    return new $classname($instanceid, $instance);
}

/**
 * Helper function to call a static function on a portfolio plugin class.
 * This will figure out the classname and require the right file and call the function.
 * You can send a variable number of arguments to this function after the first two
 * and they will be passed on to the function you wish to call.
 *
 * @param string $plugin   name of plugin
 * @param string $function function to call
 * @return mixed
 */
function portfolio_static_function($plugin, $function) {
    global $CFG;

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

    require_once($CFG->libdir . '/portfolio/plugin.php');
    require_once($CFG->dirroot . '/portfolio/' . $plugin .  '/lib.php');
    return call_user_func_array(array('portfolio_plugin_' . $plugin, $function), $args);
}

/**
 * Helper function to check all the plugins for sanity and set any insane ones to invisible.
 *
 * @param array $plugins array of supported plugin types
 * @return array array of insane instances (keys= id, values = reasons (keys for plugin lang)
 */
function portfolio_plugin_sanity_check($plugins=null) {
    global $DB;
    if (is_string($plugins)) {
        $plugins = array($plugins);
    } else if (empty($plugins)) {
        $plugins = core_component::get_plugin_list('portfolio');
        $plugins = array_keys($plugins);
    }

    $insane = array();
    foreach ($plugins as $plugin) {
        if ($result = portfolio_static_function($plugin, 'plugin_sanity_check')) {
            $insane[$plugin] = $result;
        }
    }
    if (empty($insane)) {
        return array();
    }
    list($where, $params) = $DB->get_in_or_equal(array_keys($insane));
    $where = ' plugin ' . $where;
    $DB->set_field_select('portfolio_instance', 'visible', 0, $where, $params);
    return $insane;
}

/**
 * Helper function to check all the instances for sanity and set any insane ones to invisible.
 *
 * @param array $instances array of plugin instances
 * @return array array of insane instances (keys= id, values = reasons (keys for plugin lang)
 */
function portfolio_instance_sanity_check($instances=null) {
    global $DB;
    if (empty($instances)) {
        $instances = portfolio_instances(false);
    } else if (!is_array($instances)) {
        $instances = array($instances);
    }

    $insane = array();
    foreach ($instances as $instance) {
        if (is_object($instance) && !($instance instanceof portfolio_plugin_base)) {
            $instance = portfolio_instance($instance->id, $instance);
        } else if (is_numeric($instance)) {
            $instance = portfolio_instance($instance);
        }
        if (!($instance instanceof portfolio_plugin_base)) {
            debugging('something weird passed to portfolio_instance_sanity_check, not subclass or id');
            continue;
        }
        if ($result = $instance->instance_sanity_check()) {
            $insane[$instance->get('id')] = $result;
        }
    }
    if (empty($insane)) {
        return array();
    }
    list ($where, $params) = $DB->get_in_or_equal(array_keys($insane));
    $where = ' id ' . $where;
    $DB->set_field_select('portfolio_instance', 'visible', 0, $where, $params);
    portfolio_insane_notify_admins($insane, true);
    return $insane;
}

/**
 * Helper function to display a table of plugins (or instances) and reasons for disabling
 *
 * @param array $insane array of portfolio plugin
 * @param array $instances if reporting instances rather than whole plugins, pass the array (key = id, value = object) here
 * @param bool $return option to deliver the report in html format or print it out directly to the page.
 * @return void|string of portfolio report in html table format
 */
function portfolio_report_insane($insane, $instances=false, $return=false) {
    global $OUTPUT;
    if (empty($insane)) {
        return;
    }

    static $pluginstr;
    if (empty($pluginstr)) {
        $pluginstr = get_string('plugin', 'portfolio');
    }
    if ($instances) {
        $headerstr = get_string('someinstancesdisabled', 'portfolio');
    } else {
        $headerstr = get_string('somepluginsdisabled', 'portfolio');
    }

    $output = $OUTPUT->notification($headerstr, 'notifyproblem');
    $table = new html_table();
    $table->head = array($pluginstr, '');
    $table->data = array();
    foreach ($insane as $plugin => $reason) {
        if ($instances) {
            $instance = $instances[$plugin];
            $plugin   = $instance->get('plugin');
            $name     = $instance->get('name');
        } else {
            $name = $plugin;
        }
        $table->data[] = array($name, get_string($reason, 'portfolio_' . $plugin));
    }
    $output .= html_writer::table($table);
    $output .= '<br /><br /><br />';

    if ($return) {
        return $output;
    }
    echo $output;
}

/**
 * Helper function to rethrow a caught portfolio_exception as an export exception.
 * Used because when a portfolio_export exception is thrown the export is cancelled
 * throws portfolio_export_exceptiog
 *
 * @param portfolio_exporter $exporter  current exporter object
 * @param object             $exception exception to rethrow
 */
function portfolio_export_rethrow_exception($exporter, $exception) {
    throw new portfolio_export_exception($exporter, $exception->errorcode, $exception->module, $exception->link, $exception->a);
}

/**
 * Try and determine expected_time for purely file based exports
 * or exports that might include large file attachments.
 *
 * @param stored_file|array $totest - either an array of stored_file objects or a single stored_file object
 * @return string PORTFOLIO_TIME_XXX
 */
function portfolio_expected_time_file($totest) {
    global $CFG;
    if ($totest instanceof stored_file) {
        $totest = array($totest);
    }
    $size = 0;
    foreach ($totest as $file) {
        if (!($file instanceof stored_file)) {
            debugging('something weird passed to portfolio_expected_time_file - not stored_file object');
            debugging(print_r($file, true));
            continue;
        }
        $size += $file->get_filesize();
    }

    $fileinfo = portfolio_filesize_info();

    $moderate = $high = 0; // avoid warnings

    foreach (array('moderate', 'high') as $setting) {
        $settingname = 'portfolio_' . $setting . '_filesize_threshold';
        if (empty($CFG->{$settingname}) || !array_key_exists($CFG->{$settingname}, $fileinfo['options'])) {
            debugging("weird or unset admin value for $settingname, using default instead");
            $$setting = $fileinfo[$setting];
        } else {
            $$setting = $CFG->{$settingname};
        }
    }

    if ($size < $moderate) {
        return PORTFOLIO_TIME_LOW;
    } else if ($size < $high) {
        return PORTFOLIO_TIME_MODERATE;
    }
    return PORTFOLIO_TIME_HIGH;
}


/**
 * The default filesizes and threshold information for file based transfers.
 * This shouldn't need to be used outside the admin pages and the portfolio code
 *
 * @return array
 */
function portfolio_filesize_info() {
    $filesizes = array();
    $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152, 5242880, 10485760, 20971520, 52428800);
    foreach ($sizelist as $size) {
        $filesizes[$size] = display_size($size);
    }
    return array(
        'options' => $filesizes,
        'moderate' => 1048576,
        'high'     => 5242880,
    );
}

/**
 * Try and determine expected_time for purely database based exports
 * or exports that might include large parts of a database.
 *
 * @param int $recordcount number of records trying to export
 * @return string PORTFOLIO_TIME_XXX
 */
function portfolio_expected_time_db($recordcount) {
    global $CFG;

    if (empty($CFG->portfolio_moderate_dbsize_threshold)) {
        set_config('portfolio_moderate_dbsize_threshold', 10);
    }
    if (empty($CFG->portfolio_high_dbsize_threshold)) {
        set_config('portfolio_high_dbsize_threshold', 50);
    }
    if ($recordcount < $CFG->portfolio_moderate_dbsize_threshold) {
        return PORTFOLIO_TIME_LOW;
    } else if ($recordcount < $CFG->portfolio_high_dbsize_threshold) {
        return PORTFOLIO_TIME_MODERATE;
    }
    return PORTFOLIO_TIME_HIGH;
}

/**
 * Function to send portfolio report to admins
 *
 * @param array $insane array of insane plugins
 * @param array $instances (optional) if reporting instances rather than whole plugins
 */
function portfolio_insane_notify_admins($insane, $instances=false) {

    global $CFG;

    if (defined('ADMIN_EDITING_PORTFOLIO')) {
        return true;
    }

    $admins = get_admins();

    if (empty($admins)) {
        return;
    }
    if ($instances) {
        $instances = portfolio_instances(false, false);
    }

    $site = get_site();

    $a = new StdClass;
    $a->sitename = format_string($site->fullname, true, array('context' => context_course::instance(SITEID)));
    $a->fixurl   = "$CFG->wwwroot/$CFG->admin/settings.php?section=manageportfolios";
    $a->htmllist = portfolio_report_insane($insane, $instances, true);
    $a->textlist = '';

    foreach ($insane as $k => $reason) {
        if ($instances) {
            $a->textlist = $instances[$k]->get('name') . ': ' . $reason . "\n";
        } else {
            $a->textlist = $k . ': ' . $reason . "\n";
        }
    }

    $subject   = get_string('insanesubject', 'portfolio');
    $plainbody = get_string('insanebody', 'portfolio', $a);
    $htmlbody  = get_string('insanebodyhtml', 'portfolio', $a);
    $smallbody = get_string('insanebodysmall', 'portfolio', $a);

    foreach ($admins as $admin) {
        $eventdata = new \core\message\message();
        $eventdata->courseid = SITEID;
        $eventdata->modulename = 'portfolio';
        $eventdata->component = 'portfolio';
        $eventdata->name = 'notices';
        $eventdata->userfrom = get_admin();
        $eventdata->userto = $admin;
        $eventdata->subject = $subject;
        $eventdata->fullmessage = $plainbody;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = $htmlbody;
        $eventdata->smallmessage = $smallbody;
        message_send($eventdata);
    }
}

/**
 * Setup page export
 *
 * @param moodle_page $PAGE global variable from page object
 * @param portfolio_caller_base $caller plugin type caller
 */
function portfolio_export_pagesetup($PAGE, $caller) {
    // set up the context so that build_navigation works nice
    $caller->set_context($PAGE);

    list($extranav, $cm) = $caller->get_navigation();

    // and now we know the course for sure and maybe the cm, call require_login with it
    require_login($PAGE->course, false, $cm);

    foreach ($extranav as $navitem) {
        $PAGE->navbar->add($navitem['name']);
    }
    $PAGE->navbar->add(get_string('exporting', 'portfolio'));
}

/**
 * Get export type id
 *
 * @param string $type plugin type
 * @param int $userid the user to check for
 * @return mixed|bool
 */
function portfolio_export_type_to_id($type, $userid) {
    global $DB;
    $sql = 'SELECT t.id FROM {portfolio_tempdata} t JOIN {portfolio_instance} i ON t.instance = i.id WHERE t.userid = ? AND i.plugin = ?';
    return $DB->get_field_sql($sql, array($userid, $type));
}

/**
 * Return a list of current exports for the given user.
 * This will not go through and call rewaken_object, because it's heavy.
 * It's really just used to figure out what exports are currently happening.
 * This is useful for plugins that don't support multiple exports per session
 *
 * @param int $userid the user to check for
 * @param string $type (optional) the portfolio plugin to filter by
 * @return array
 */
function portfolio_existing_exports($userid, $type=null) {
    global $DB;
    $sql = 'SELECT t.*,t.instance,i.plugin,i.name FROM {portfolio_tempdata} t JOIN {portfolio_instance} i ON t.instance = i.id WHERE t.userid = ? ';
    $values = array($userid);
    if ($type) {
        $sql .= ' AND i.plugin = ?';
        $values[] = $type;
    }
    return $DB->get_records_sql($sql, $values);
}

/**
 * Return an array of existing exports by type for a given user.
 * This is much more lightweight than existing_exports because it only returns the types, rather than the whole serialised data
 * so can be used for checking availability of multiple plugins at the same time.
 * @see existing_exports
 *
 * @param int $userid the user to check for
 * @return array
 */
function portfolio_existing_exports_by_plugin($userid) {
    global $DB;
    $sql = 'SELECT t.id,i.plugin FROM {portfolio_tempdata} t JOIN {portfolio_instance} i ON t.instance = i.id WHERE t.userid = ? ';
    $values = array($userid);
    return $DB->get_records_sql_menu($sql, $values);
}

/**
 * Return default common options for {@link format_text()} when preparing a content to be exported.
 * It is important not to apply filters and not to clean the HTML in format_text()
 *
 * @return stdClass
 */
function portfolio_format_text_options() {

    $options                = new stdClass();
    $options->para          = false;
    $options->newlines      = true;
    $options->filter        = false;
    $options->noclean       = true;
    $options->overflowdiv   = false;

    return $options;
}

/**
 * callback function from {@link portfolio_rewrite_pluginfile_urls}
 * looks through preg_replace matches and replaces content with whatever the active portfolio export format says
 *
 * @param int $contextid module context id
 * @param string $component module name (eg:mod_assignment)
 * @param string $filearea normal file_area arguments
 * @param int $itemid component item id
 * @param portfolio_format $format exporter format type
 * @param array $options extra options to pass through to the file_output function in the format (optional)
 * @param array $matches internal matching
 * @return object|array|string
 */
function portfolio_rewrite_pluginfile_url_callback($contextid, $component, $filearea, $itemid, $format, $options, $matches) {
    $matches = $matches[0]; // No internal matching.

    // Loads the HTML.
    $dom = new DomDocument();
    if (!$dom->loadHTML($matches)) {
        return $matches;
    }

    // Navigates to the node.
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('/html/body/child::*');
    if (empty($nodes) || count($nodes) > 1) {
        // Unexpected sequence, none or too many nodes.
        return $matches;
    }
    $dom = $nodes->item(0);

    $attributes = array();
    foreach ($dom->attributes as $attr => $node) {
        $attributes[$attr] = $node->value;
    }
    // now figure out the file
    $fs = get_file_storage();
    $key = 'href';
    if (!array_key_exists('href', $attributes) && array_key_exists('src', $attributes)) {
        $key = 'src';
    }
    if (!array_key_exists($key, $attributes)) {
        debugging('Couldn\'t find an attribute to use that contains @@PLUGINFILE@@ in portfolio_rewrite_pluginfile');
        return $matches;
    }
    $filename = substr($attributes[$key], strpos($attributes[$key], '@@PLUGINFILE@@') + strlen('@@PLUGINFILE@@'));
    $filepath = '/';
    if (strpos($filename, '/') !== 0) {
        $bits = explode('/', $filename);
        $filename = array_pop($bits);
        $filepath = implode('/', $bits);
    }
    if (!$file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, urldecode($filename))) {
        debugging("Couldn't find a file from the embedded path info context $contextid component $component filearea $filearea itemid $itemid filepath $filepath name $filename");
        return $matches;
    }
    if (empty($options)) {
        $options = array();
    }
    $options['attributes'] = $attributes;
    return $format->file_output($file, $options);
}

/**
 * Function to require any potential callback files, throwing exceptions
 * if an issue occurs.
 *
 * @param string $component This is the name of the component in Moodle, eg 'mod_forum'
 * @param string $class Name of the class containing the callback functions
 *     activity components should ALWAYS use their name_portfolio_caller
 *     other locations must use something unique
 */
function portfolio_include_callback_file($component, $class = null) {
    global $CFG;
    require_once($CFG->libdir . '/adminlib.php');

    // It's possible that they are passing a file path rather than passing a component.
    // We want to try and convert this to a component name, eg. mod_forum.
    $pos = strrpos($component, '/');
    if ($pos !== false) {
        // Get rid of the first slash (if it exists).
        $component = ltrim($component, '/');
        // Get a list of valid plugin types.
        $plugintypes = core_component::get_plugin_types();
        // Assume it is not valid for now.
        $isvalid = false;
        // Go through the plugin types.
        foreach ($plugintypes as $type => $path) {
            // Getting the path relative to the dirroot.
            $path = preg_replace('|^' . preg_quote($CFG->dirroot, '|') . '/|', '', $path);
            if (strrpos($component, $path) === 0) {
                // Found the plugin type.
                $isvalid = true;
                $plugintype = $type;
                $pluginpath = $path;
            }
        }
        // Throw exception if not a valid component.
        if (!$isvalid) {
            throw new coding_exception('Somehow a non-valid plugin path was passed, could be a hackz0r attempt, exiting.');
        }
        // Remove the file name.
        $component = trim(substr($component, 0, $pos), '/');
        // Replace the path with the type.
        $component = str_replace($pluginpath, $plugintype, $component);
        // Ok, replace '/' with '_'.
        $component = str_replace('/', '_', $component);
        // Place a debug message saying the third parameter should be changed.
        debugging('The third parameter sent to the function set_callback_options should be the component name, not a file path, please update this.', DEBUG_DEVELOPER);
    }

    // Check that it is a valid component.
    if (!get_component_version($component)) {
        throw new portfolio_button_exception('nocallbackcomponent', 'portfolio', '', $component);
    }

    // Obtain the component's location.
    if (!$componentloc = core_component::get_component_directory($component)) {
        throw new portfolio_button_exception('nocallbackcomponent', 'portfolio', '', $component);
    }

    // Check if the component contains the necessary file for the portfolio plugin.
    // These are locallib.php, portfoliolib.php and portfolio_callback.php.
    $filefound = false;
    if (file_exists($componentloc . '/locallib.php')) {
        $filefound = true;
        require_once($componentloc . '/locallib.php');
    }
    if (file_exists($componentloc . '/portfoliolib.php')) {
        $filefound = true;
        debugging('Please standardise your plugin by renaming your portfolio callback file to locallib.php, or if that file already exists moving the portfolio functionality there.', DEBUG_DEVELOPER);
        require_once($componentloc . '/portfoliolib.php');
    }
    if (file_exists($componentloc . '/portfolio_callback.php')) {
        $filefound = true;
        debugging('Please standardise your plugin by renaming your portfolio callback file to locallib.php, or if that file already exists moving the portfolio functionality there.', DEBUG_DEVELOPER);
        require_once($componentloc . '/portfolio_callback.php');
    }

    // Ensure that we found a file we can use, if not throw an exception.
    if (!$filefound) {
        throw new portfolio_button_exception('nocallbackfile', 'portfolio', '', $component);
    }

    if (!is_null($class)) {
        // If class is specified, check it exists and extends portfolio_caller_base.
        if (!class_exists($class) || !is_subclass_of($class, 'portfolio_caller_base')) {
            throw new portfolio_button_exception('nocallbackclass', 'portfolio', '', $class);
        }
    }
}

/**
 * Go through all the @@PLUGINFILE@@ matches in some text,
 * extract the file information and pass it back to the portfolio export format
 * to regenerate the html to output
 *
 * @param string $text the text to search through
 * @param int $contextid normal file_area arguments
 * @param string $component module name
 * @param string $filearea normal file_area arguments
 * @param int $itemid normal file_area arguments
 * @param portfolio_format $format the portfolio export format
 * @param array $options additional options to be included in the plugin file url (optional)
 * @return mixed
 */
function portfolio_rewrite_pluginfile_urls($text, $contextid, $component, $filearea, $itemid, $format, $options=null) {
    $patterns = array(
        '(<(a|A)[^<]*?href="@@PLUGINFILE@@/[^>]*?>.*?</(a|A)>)',
        '(<(img|IMG)\s[^<]*?src="@@PLUGINFILE@@/[^>]*?/?>)',
    );
    $pattern = '~' . implode('|', $patterns) . '~';
    $callback = partial('portfolio_rewrite_pluginfile_url_callback', $contextid, $component, $filearea, $itemid, $format, $options);
    return preg_replace_callback($pattern, $callback, $text);
}
// this function has to go last, because the regexp screws up syntax highlighting in some editors

