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
 * Classes representing HTML elements, used by $OUTPUT methods
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package core
 * @category output
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Interface marking other classes as suitable for renderer_base::render()
 *
 * @copyright 2010 Petr Skoda (skodak) info@skodak.org
 * @package core
 * @category output
 */
interface renderable {
    // intentionally empty
}

/**
 * Interface marking other classes having the ability to export their data for use by templates.
 *
 * @copyright 2015 Damyon Wiese
 * @package core
 * @category output
 * @since 2.9
 */
interface templatable {

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output);
}

/**
 * Data structure representing a file picker.
 *
 * @copyright 2010 Dongsheng Cai
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class file_picker implements renderable {

    /**
     * @var stdClass An object containing options for the file picker
     */
    public $options;

    /**
     * Constructs a file picker object.
     *
     * The following are possible options for the filepicker:
     *    - accepted_types  (*)
     *    - return_types    (FILE_INTERNAL)
     *    - env             (filepicker)
     *    - client_id       (uniqid)
     *    - itemid          (0)
     *    - maxbytes        (-1)
     *    - maxfiles        (1)
     *    - buttonname      (false)
     *
     * @param stdClass $options An object containing options for the file picker.
     */
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot. '/repository/lib.php');
        $defaults = array(
            'accepted_types'=>'*',
            'return_types'=>FILE_INTERNAL,
            'env' => 'filepicker',
            'client_id' => uniqid(),
            'itemid' => 0,
            'maxbytes'=>-1,
            'maxfiles'=>1,
            'buttonname'=>false
        );
        foreach ($defaults as $key=>$value) {
            if (empty($options->$key)) {
                $options->$key = $value;
            }
        }

        $options->currentfile = '';
        if (!empty($options->itemid)) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if (empty($options->filename)) {
                if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id DESC', false)) {
                    $file = reset($files);
                }
            } else {
                $file = $fs->get_file($usercontext->id, 'user', 'draft', $options->itemid, $options->filepath, $options->filename);
            }
            if (!empty($file)) {
                $options->currentfile = html_writer::link(moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename()), $file->get_filename());
            }
        }

        // initilise options, getting files in root path
        $this->options = initialise_filepicker($options);

        // copying other options
        foreach ($options as $name=>$value) {
            if (!isset($this->options->$name)) {
                $this->options->$name = $value;
            }
        }
    }
}

/**
 * Data structure representing a user picture.
 *
 * @copyright 2009 Nicolas Connault, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Modle 2.0
 * @package core
 * @category output
 */
class user_picture implements renderable {
    /**
     * @var array List of mandatory fields in user record here. (do not include
     * TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     */
    protected static $fields = array('id', 'picture', 'firstname', 'lastname', 'firstnamephonetic', 'lastnamephonetic',
            'middlename', 'alternatename', 'imagealt', 'email');

    /**
     * @var stdClass A user object with at least fields all columns specified
     * in $fields array constant set.
     */
    public $user;

    /**
     * @var int The course id. Used when constructing the link to the user's
     * profile, page course id used if not specified.
     */
    public $courseid;

    /**
     * @var bool Add course profile link to image
     */
    public $link = true;

    /**
     * @var int Size in pixels. Special values are (true/1 = 100px) and
     * (false/0 = 35px)
     * for backward compatibility.
     */
    public $size = 35;

    /**
     * @var bool Add non-blank alt-text to the image.
     * Default true, set to false when image alt just duplicates text in screenreaders.
     */
    public $alttext = true;

    /**
     * @var bool Whether or not to open the link in a popup window.
     */
    public $popup = false;

    /**
     * @var string Image class attribute
     */
    public $class = 'userpicture';

    /**
     * @var bool Whether to be visible to screen readers.
     */
    public $visibletoscreenreaders = true;

    /**
     * @var bool Whether to include the fullname in the user picture link.
     */
    public $includefullname = false;

    /**
     * @var mixed Include user authentication token. True indicates to generate a token for current user, and integer value
     * indicates to generate a token for the user whose id is the value indicated.
     */
    public $includetoken = false;

    /**
     * User picture constructor.
     *
     * @param stdClass $user user record with at least id, picture, imagealt, firstname and lastname set.
     *                 It is recommended to add also contextid of the user for performance reasons.
     */
    public function __construct(stdClass $user) {
        global $DB;

        if (empty($user->id)) {
            throw new coding_exception('User id is required when printing user avatar image.');
        }

        // only touch the DB if we are missing data and complain loudly...
        $needrec = false;
        foreach (self::$fields as $field) {
            if (!property_exists($user, $field)) {
                $needrec = true;
                debugging('Missing '.$field.' property in $user object, this is a performance problem that needs to be fixed by a developer. '
                          .'Please use user_picture::fields() to get the full list of required fields.', DEBUG_DEVELOPER);
                break;
            }
        }

        if ($needrec) {
            $this->user = $DB->get_record('user', array('id'=>$user->id), self::fields(), MUST_EXIST);
        } else {
            $this->user = clone($user);
        }
    }

    /**
     * Returns a list of required user fields, useful when fetching required user info from db.
     *
     * In some cases we have to fetch the user data together with some other information,
     * the idalias is useful there because the id would otherwise override the main
     * id of the result record. Please note it has to be converted back to id before rendering.
     *
     * @param string $tableprefix name of database table prefix in query
     * @param array $extrafields extra fields to be included in result (do not include TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     * @param string $idalias alias of id field
     * @param string $fieldprefix prefix to add to all columns in their aliases, does not apply to 'id'
     * @return string
     */
    public static function fields($tableprefix = '', array $extrafields = NULL, $idalias = 'id', $fieldprefix = '') {
        if (!$tableprefix and !$extrafields and !$idalias) {
            return implode(',', self::$fields);
        }
        if ($tableprefix) {
            $tableprefix .= '.';
        }
        foreach (self::$fields as $field) {
            if ($field === 'id' and $idalias and $idalias !== 'id') {
                $fields[$field] = "$tableprefix$field AS $idalias";
            } else {
                if ($fieldprefix and $field !== 'id') {
                    $fields[$field] = "$tableprefix$field AS $fieldprefix$field";
                } else {
                    $fields[$field] = "$tableprefix$field";
                }
            }
        }
        // add extra fields if not already there
        if ($extrafields) {
            foreach ($extrafields as $e) {
                if ($e === 'id' or isset($fields[$e])) {
                    continue;
                }
                if ($fieldprefix) {
                    $fields[$e] = "$tableprefix$e AS $fieldprefix$e";
                } else {
                    $fields[$e] = "$tableprefix$e";
                }
            }
        }
        return implode(',', $fields);
    }

    /**
     * Extract the aliased user fields from a given record
     *
     * Given a record that was previously obtained using {@link self::fields()} with aliases,
     * this method extracts user related unaliased fields.
     *
     * @param stdClass $record containing user picture fields
     * @param array $extrafields extra fields included in the $record
     * @param string $idalias alias of the id field
     * @param string $fieldprefix prefix added to all columns in their aliases, does not apply to 'id'
     * @return stdClass object with unaliased user fields
     */
    public static function unalias(stdClass $record, array $extrafields = null, $idalias = 'id', $fieldprefix = '') {

        if (empty($idalias)) {
            $idalias = 'id';
        }

        $return = new stdClass();

        foreach (self::$fields as $field) {
            if ($field === 'id') {
                if (property_exists($record, $idalias)) {
                    $return->id = $record->{$idalias};
                }
            } else {
                if (property_exists($record, $fieldprefix.$field)) {
                    $return->{$field} = $record->{$fieldprefix.$field};
                }
            }
        }
        // add extra fields if not already there
        if ($extrafields) {
            foreach ($extrafields as $e) {
                if ($e === 'id' or property_exists($return, $e)) {
                    continue;
                }
                $return->{$e} = $record->{$fieldprefix.$e};
            }
        }

        return $return;
    }

    /**
     * Works out the URL for the users picture.
     *
     * This method is recommended as it avoids costly redirects of user pictures
     * if requests are made for non-existent files etc.
     *
     * @param moodle_page $page
     * @param renderer_base $renderer
     * @return moodle_url
     */
    public function get_url(moodle_page $page, renderer_base $renderer = null) {
        global $CFG;

        if (is_null($renderer)) {
            $renderer = $page->get_renderer('core');
        }

        // Sort out the filename and size. Size is only required for the gravatar
        // implementation presently.
        if (empty($this->size)) {
            $filename = 'f2';
            $size = 35;
        } else if ($this->size === true or $this->size == 1) {
            $filename = 'f1';
            $size = 100;
        } else if ($this->size > 100) {
            $filename = 'f3';
            $size = (int)$this->size;
        } else if ($this->size >= 50) {
            $filename = 'f1';
            $size = (int)$this->size;
        } else {
            $filename = 'f2';
            $size = (int)$this->size;
        }

        $defaulturl = $renderer->image_url('u/'.$filename); // default image

        if ((!empty($CFG->forcelogin) and !isloggedin()) ||
            (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))) {
            // Protect images if login required and not logged in;
            // also if login is required for profile images and is not logged in or guest
            // do not use require_login() because it is expensive and not suitable here anyway.
            return $defaulturl;
        }

        // First try to detect deleted users - but do not read from database for performance reasons!
        if (!empty($this->user->deleted) or strpos($this->user->email, '@') === false) {
            // All deleted users should have email replaced by md5 hash,
            // all active users are expected to have valid email.
            return $defaulturl;
        }

        // Did the user upload a picture?
        if ($this->user->picture > 0) {
            if (!empty($this->user->contextid)) {
                $contextid = $this->user->contextid;
            } else {
                $context = context_user::instance($this->user->id, IGNORE_MISSING);
                if (!$context) {
                    // This must be an incorrectly deleted user, all other users have context.
                    return $defaulturl;
                }
                $contextid = $context->id;
            }

            $path = '/';
            if (clean_param($page->theme->name, PARAM_THEME) == $page->theme->name) {
                // We append the theme name to the file path if we have it so that
                // in the circumstance that the profile picture is not available
                // when the user actually requests it they still get the profile
                // picture for the correct theme.
                $path .= $page->theme->name.'/';
            }
            // Set the image URL to the URL for the uploaded file and return.
            $url = moodle_url::make_pluginfile_url(
                    $contextid, 'user', 'icon', null, $path, $filename, false, $this->includetoken);
            $url->param('rev', $this->user->picture);
            return $url;
        }

        if ($this->user->picture == 0 and !empty($CFG->enablegravatar)) {
            // Normalise the size variable to acceptable bounds
            if ($size < 1 || $size > 512) {
                $size = 35;
            }
            // Hash the users email address
            $md5 = md5(strtolower(trim($this->user->email)));
            // Build a gravatar URL with what we know.

            // Find the best default image URL we can (MDL-35669)
            if (empty($CFG->gravatardefaulturl)) {
                $absoluteimagepath = $page->theme->resolve_image_location('u/'.$filename, 'core');
                if (strpos($absoluteimagepath, $CFG->dirroot) === 0) {
                    $gravatardefault = $CFG->wwwroot . substr($absoluteimagepath, strlen($CFG->dirroot));
                } else {
                    $gravatardefault = $CFG->wwwroot . '/pix/u/' . $filename . '.png';
                }
            } else {
                $gravatardefault = $CFG->gravatardefaulturl;
            }

            // If the currently requested page is https then we'll return an
            // https gravatar page.
            if (is_https()) {
                return new moodle_url("https://secure.gravatar.com/avatar/{$md5}", array('s' => $size, 'd' => $gravatardefault));
            } else {
                return new moodle_url("http://www.gravatar.com/avatar/{$md5}", array('s' => $size, 'd' => $gravatardefault));
            }
        }

        return $defaulturl;
    }
}

/**
 * Data structure representing a help icon.
 *
 * @copyright 2010 Petr Skoda (info@skodak.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class help_icon implements renderable, templatable {

    /**
     * @var string lang pack identifier (without the "_help" suffix),
     * both get_string($identifier, $component) and get_string($identifier.'_help', $component)
     * must exist.
     */
    public $identifier;

    /**
     * @var string Component name, the same as in get_string()
     */
    public $component;

    /**
     * @var string Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * Constructor
     *
     * @param string $identifier string for help page title,
     *  string with _help suffix is used for the actual help text.
     *  string with _link suffix is used to create a link to further info (if it exists)
     * @param string $component
     */
    public function __construct($identifier, $component) {
        $this->identifier = $identifier;
        $this->component  = $component;
    }

    /**
     * Verifies that both help strings exists, shows debug warnings if not
     */
    public function diag_strings() {
        $sm = get_string_manager();
        if (!$sm->string_exists($this->identifier, $this->component)) {
            debugging("Help title string does not exist: [$this->identifier, $this->component]");
        }
        if (!$sm->string_exists($this->identifier.'_help', $this->component)) {
            debugging("Help contents string does not exist: [{$this->identifier}_help, $this->component]");
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $title = get_string($this->identifier, $this->component);

        if (empty($this->linktext)) {
            $alt = get_string('helpprefix2', '', trim($title, ". \t"));
        } else {
            $alt = get_string('helpwiththis');
        }

        $data = get_formatted_help_string($this->identifier, $this->component, false);

        $data->alt = $alt;
        $data->icon = (new pix_icon('help', $alt, 'core', ['class' => 'iconhelp']))->export_for_template($output);
        $data->linktext = $this->linktext;
        $data->title = get_string('helpprefix2', '', trim($title, ". \t"));

        $options = [
            'component' => $this->component,
            'identifier' => $this->identifier,
            'lang' => current_language()
        ];

        // Debugging feature lets you display string identifier and component.
        if (isset($CFG->debugstringids) && $CFG->debugstringids && optional_param('strings', 0, PARAM_INT)) {
            $options['strings'] = 1;
        }

        $data->url = (new moodle_url('/help.php', $options))->out(false);
        $data->ltr = !right_to_left();
        return $data;
    }
}


/**
 * Data structure representing an icon font.
 *
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */
class pix_icon_font implements templatable {

    /**
     * @var pix_icon $pixicon The original icon.
     */
    private $pixicon = null;

    /**
     * @var string $key The mapped key.
     */
    private $key;

    /**
     * @var bool $mapped The icon could not be mapped.
     */
    private $mapped;

    /**
     * Constructor
     *
     * @param pix_icon $pixicon The original icon
     */
    public function __construct(pix_icon $pixicon) {
        global $PAGE;

        $this->pixicon = $pixicon;
        $this->mapped = false;
        $iconsystem = \core\output\icon_system::instance();

        $this->key = $iconsystem->remap_icon_name($pixicon->pix, $pixicon->component);
        if (!empty($this->key)) {
            $this->mapped = true;
        }
    }

    /**
     * Return true if this pix_icon was successfully mapped to an icon font.
     *
     * @return bool
     */
    public function is_mapped() {
        return $this->mapped;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        $pixdata = $this->pixicon->export_for_template($output);

        $title = isset($this->pixicon->attributes['title']) ? $this->pixicon->attributes['title'] : '';
        $alt = isset($this->pixicon->attributes['alt']) ? $this->pixicon->attributes['alt'] : '';
        if (empty($title)) {
            $title = $alt;
        }
        $data = array(
            'extraclasses' => $pixdata['extraclasses'],
            'title' => $title,
            'alt' => $alt,
            'key' => $this->key
        );

        return $data;
    }
}

/**
 * Data structure representing an icon subtype.
 *
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */
class pix_icon_fontawesome extends pix_icon_font {

}

/**
 * Data structure representing an icon.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class pix_icon implements renderable, templatable {

    /**
     * @var string The icon name
     */
    var $pix;

    /**
     * @var string The component the icon belongs to.
     */
    var $component;

    /**
     * @var array An array of attributes to use on the icon
     */
    var $attributes = array();

    /**
     * Constructor
     *
     * @param string $pix short icon name
     * @param string $alt The alt text to use for the icon
     * @param string $component component name
     * @param array $attributes html attributes
     */
    public function __construct($pix, $alt, $component='moodle', array $attributes = null) {
        global $PAGE;

        $this->pix = $pix;
        $this->component  = $component;
        $this->attributes = (array)$attributes;

        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = '';
        }

        // Set an additional class for big icons so that they can be styled properly.
        if (substr($pix, 0, 2) === 'b/') {
            $this->attributes['class'] .= ' iconsize-big';
        }

        // If the alt is empty, don't place it in the attributes, otherwise it will override parent alt text.
        if (!is_null($alt)) {
            $this->attributes['alt'] = $alt;

            // If there is no title, set it to the attribute.
            if (!isset($this->attributes['title'])) {
                $this->attributes['title'] = $this->attributes['alt'];
            }
        } else {
            unset($this->attributes['alt']);
        }

        if (empty($this->attributes['title'])) {
            // Remove the title attribute if empty, we probably want to use the parent node's title
            // and some browsers might overwrite it with an empty title.
            unset($this->attributes['title']);
        }

        // Hide icons from screen readers that have no alt.
        if (empty($this->attributes['alt'])) {
            $this->attributes['aria-hidden'] = 'true';
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;
        $extraclasses = '';

        foreach ($attributes as $key => $item) {
            if ($key == 'class') {
                $extraclasses = $item;
                unset($attributes[$key]);
                break;
            }
        }

        $attributes['src'] = $output->image_url($this->pix, $this->component)->out(false);
        $templatecontext = array();
        foreach ($attributes as $name => $value) {
            $templatecontext[] = array('name' => $name, 'value' => $value);
        }
        $title = isset($attributes['title']) ? $attributes['title'] : '';
        if (empty($title)) {
            $title = isset($attributes['alt']) ? $attributes['alt'] : '';
        }
        $data = array(
            'attributes' => $templatecontext,
            'extraclasses' => $extraclasses
        );

        return $data;
    }

    /**
     * Much simpler version of export that will produce the data required to render this pix with the
     * pix helper in a mustache tag.
     *
     * @return array
     */
    public function export_for_pix() {
        $title = isset($this->attributes['title']) ? $this->attributes['title'] : '';
        if (empty($title)) {
            $title = isset($this->attributes['alt']) ? $this->attributes['alt'] : '';
        }
        return [
            'key' => $this->pix,
            'component' => $this->component,
            'title' => $title
        ];
    }
}

/**
 * Data structure representing an activity icon.
 *
 * The difference is that activity icons will always render with the standard icon system (no font icons).
 *
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class image_icon extends pix_icon {
}

/**
 * Data structure representing an emoticon image
 *
 * @copyright 2010 David Mudrak
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class pix_emoticon extends pix_icon implements renderable {

    /**
     * Constructor
     * @param string $pix short icon name
     * @param string $alt alternative text
     * @param string $component emoticon image provider
     * @param array $attributes explicit HTML attributes
     */
    public function __construct($pix, $alt, $component = 'moodle', array $attributes = array()) {
        if (empty($attributes['class'])) {
            $attributes['class'] = 'emoticon';
        }
        parent::__construct($pix, $alt, $component, $attributes);
    }
}

/**
 * Data structure representing a simple form with only one button.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class single_button implements renderable {

    /**
     * @var moodle_url Target url
     */
    public $url;

    /**
     * @var string Button label
     */
    public $label;

    /**
     * @var string Form submit method post or get
     */
    public $method = 'post';

    /**
     * @var string Wrapping div class
     */
    public $class = 'singlebutton';

    /**
     * @var bool True if button is primary button. Used for styling.
     */
    public $primary = false;

    /**
     * @var bool True if button disabled, false if normal
     */
    public $disabled = false;

    /**
     * @var string Button tooltip
     */
    public $tooltip = null;

    /**
     * @var string Form id
     */
    public $formid;

    /**
     * @var array List of attached actions
     */
    public $actions = array();

    /**
     * @var array $params URL Params
     */
    public $params;

    /**
     * @var string Action id
     */
    public $actionid;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor
     * @param moodle_url $url
     * @param string $label button text
     * @param string $method get or post submit method
     * @param array $attributes Attributes for the HTML button tag
     */
    public function __construct(moodle_url $url, $label, $method='post', $primary=false, $attributes = []) {
        $this->url    = clone($url);
        $this->label  = $label;
        $this->method = $method;
        $this->primary = $primary;
        $this->attributes = $attributes;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the button is clicked.
     * The message must be a yes/no question.
     *
     * @param string $confirmmessage The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new confirm_action($confirmmessage));
    }

    /**
     * Add action to the button.
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Sets an attribute for the HTML button tag.
     *
     * @param  string $name  The attribute name
     * @param  mixed  $value The value
     * @return null
     */
    public function set_attribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * Export data.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $url = $this->method === 'get' ? $this->url->out_omit_querystring(true) : $this->url->out_omit_querystring();

        $data = new stdClass();
        $data->id = html_writer::random_id('single_button');
        $data->formid = $this->formid;
        $data->method = $this->method;
        $data->url = $url === '' ? '#' : $url;
        $data->label = $this->label;
        $data->classes = $this->class;
        $data->disabled = $this->disabled;
        $data->tooltip = $this->tooltip;
        $data->primary = $this->primary;

        $data->attributes = [];
        foreach ($this->attributes as $key => $value) {
            $data->attributes[] = ['name' => $key, 'value' => $value];
        }

        // Form parameters.
        $params = $this->url->params();
        if ($this->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        $data->params = array_map(function($key) use ($params) {
            return ['name' => $key, 'value' => $params[$key]];
        }, array_keys($params));

        // Button actions.
        $actions = $this->actions;
        $data->actions = array_map(function($action) use ($output) {
            return $action->export_for_template($output);
        }, $actions);
        $data->hasactions = !empty($data->actions);

        return $data;
    }
}


/**
 * Simple form with just one select field that gets submitted automatically.
 *
 * If JS not enabled small go button is printed too.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class single_select implements renderable, templatable {

    /**
     * @var moodle_url Target url - includes hidden fields
     */
    var $url;

    /**
     * @var string Name of the select element.
     */
    var $name;

    /**
     * @var array $options associative array value=>label ex.: array(1=>'One, 2=>Two)
     *     it is also possible to specify optgroup as complex label array ex.:
     *         array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *         array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    var $options;

    /**
     * @var string Selected option
     */
    var $selected;

    /**
     * @var array Nothing selected
     */
    var $nothing;

    /**
     * @var array Extra select field attributes
     */
    var $attributes = array();

    /**
     * @var string Button label
     */
    var $label = '';

    /**
     * @var array Button label's attributes
     */
    var $labelattributes = array();

    /**
     * @var string Form submit method post or get
     */
    var $method = 'get';

    /**
     * @var string Wrapping div class
     */
    var $class = 'singleselect';

    /**
     * @var bool True if button disabled, false if normal
     */
    var $disabled = false;

    /**
     * @var string Button tooltip
     */
    var $tooltip = null;

    /**
     * @var string Form id
     */
    var $formid = null;

    /**
     * @var help_icon The help icon for this element.
     */
    var $helpicon = null;

    /**
     * Constructor
     * @param moodle_url $url form action target, includes hidden fields
     * @param string $name name of selection field - the changing parameter in url
     * @param array $options list of options
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     */
    public function __construct(moodle_url $url, $name, array $options, $selected = '', $nothing = array('' => 'choosedots'), $formid = null) {
        $this->url      = $url;
        $this->name     = $name;
        $this->options  = $options;
        $this->selected = $selected;
        $this->nothing  = $nothing;
        $this->formid   = $formid;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the button is clicked.
     * The message must be a yes/no question.
     *
     * @param string $confirmmessage The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new component_action('submit', 'M.util.show_confirm_dialog', array('message' => $confirmmessage)));
    }

    /**
     * Add action to the button.
     *
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Adds help icon.
     *
     * @deprecated since Moodle 2.0
     */
    public function set_old_help_icon($helppage, $title, $component = 'moodle') {
        throw new coding_exception('set_old_help_icon() can not be used any more, please see set_help_icon().');
    }

    /**
     * Adds help icon.
     *
     * @param string $identifier The keyword that defines a help page
     * @param string $component
     */
    public function set_help_icon($identifier, $component = 'moodle') {
        $this->helpicon = new help_icon($identifier, $component);
    }

    /**
     * Sets select's label
     *
     * @param string $label
     * @param array $attributes (optional)
     */
    public function set_label($label, $attributes = array()) {
        $this->label = $label;
        $this->labelattributes = $attributes;

    }

    /**
     * Export data.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;

        $data = new stdClass();
        $data->name = $this->name;
        $data->method = $this->method;
        $data->action = $this->method === 'get' ? $this->url->out_omit_querystring(true) : $this->url->out_omit_querystring();
        $data->classes = $this->class;
        $data->label = $this->label;
        $data->disabled = $this->disabled;
        $data->title = $this->tooltip;
        $data->formid = !empty($this->formid) ? $this->formid : html_writer::random_id('single_select_f');
        $data->id = !empty($attributes['id']) ? $attributes['id'] : html_writer::random_id('single_select');

        // Select element attributes.
        // Unset attributes that are already predefined in the template.
        unset($attributes['id']);
        unset($attributes['class']);
        unset($attributes['name']);
        unset($attributes['title']);
        unset($attributes['disabled']);

        // Map the attributes.
        $data->attributes = array_map(function($key) use ($attributes) {
            return ['name' => $key, 'value' => $attributes[$key]];
        }, array_keys($attributes));

        // Form parameters.
        $params = $this->url->params();
        if ($this->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        $data->params = array_map(function($key) use ($params) {
            return ['name' => $key, 'value' => $params[$key]];
        }, array_keys($params));

        // Select options.
        $hasnothing = false;
        if (is_string($this->nothing) && $this->nothing !== '') {
            $nothing = ['' => $this->nothing];
            $hasnothing = true;
            $nothingkey = '';
        } else if (is_array($this->nothing)) {
            $nothingvalue = reset($this->nothing);
            if ($nothingvalue === 'choose' || $nothingvalue === 'choosedots') {
                $nothing = [key($this->nothing) => get_string('choosedots')];
            } else {
                $nothing = $this->nothing;
            }
            $hasnothing = true;
            $nothingkey = key($this->nothing);
        }
        if ($hasnothing) {
            $options = $nothing + $this->options;
        } else {
            $options = $this->options;
        }

        foreach ($options as $value => $name) {
            if (is_array($options[$value])) {
                foreach ($options[$value] as $optgroupname => $optgroupvalues) {
                    $sublist = [];
                    foreach ($optgroupvalues as $optvalue => $optname) {
                        $option = [
                            'value' => $optvalue,
                            'name' => $optname,
                            'selected' => strval($this->selected) === strval($optvalue),
                        ];

                        if ($hasnothing && $nothingkey === $optvalue) {
                            $option['ignore'] = 'data-ignore';
                        }

                        $sublist[] = $option;
                    }
                    $data->options[] = [
                        'name' => $optgroupname,
                        'optgroup' => true,
                        'options' => $sublist
                    ];
                }
            } else {
                $option = [
                    'value' => $value,
                    'name' => $options[$value],
                    'selected' => strval($this->selected) === strval($value),
                    'optgroup' => false
                ];

                if ($hasnothing && $nothingkey === $value) {
                    $option['ignore'] = 'data-ignore';
                }

                $data->options[] = $option;
            }
        }

        // Label attributes.
        $data->labelattributes = [];
        // Unset label attributes that are already in the template.
        unset($this->labelattributes['for']);
        // Map the label attributes.
        foreach ($this->labelattributes as $key => $value) {
            $data->labelattributes[] = ['name' => $key, 'value' => $value];
        }

        // Help icon.
        $data->helpicon = !empty($this->helpicon) ? $this->helpicon->export_for_template($output) : false;

        return $data;
    }
}

/**
 * Simple URL selection widget description.
 *
 * @copyright 2009 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class url_select implements renderable, templatable {
    /**
     * @var array $urls associative array value=>label ex.: array(1=>'One, 2=>Two)
     *     it is also possible to specify optgroup as complex label array ex.:
     *         array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *         array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    var $urls;

    /**
     * @var string Selected option
     */
    var $selected;

    /**
     * @var array Nothing selected
     */
    var $nothing;

    /**
     * @var array Extra select field attributes
     */
    var $attributes = array();

    /**
     * @var string Button label
     */
    var $label = '';

    /**
     * @var array Button label's attributes
     */
    var $labelattributes = array();

    /**
     * @var string Wrapping div class
     */
    var $class = 'urlselect';

    /**
     * @var bool True if button disabled, false if normal
     */
    var $disabled = false;

    /**
     * @var string Button tooltip
     */
    var $tooltip = null;

    /**
     * @var string Form id
     */
    var $formid = null;

    /**
     * @var help_icon The help icon for this element.
     */
    var $helpicon = null;

    /**
     * @var string If set, makes button visible with given name for button
     */
    var $showbutton = null;

    /**
     * Constructor
     * @param array $urls list of options
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @param string $showbutton Set to text of button if it should be visible
     *   or null if it should be hidden (hidden version always has text 'go')
     */
    public function __construct(array $urls, $selected = '', $nothing = array('' => 'choosedots'), $formid = null, $showbutton = null) {
        $this->urls       = $urls;
        $this->selected   = $selected;
        $this->nothing    = $nothing;
        $this->formid     = $formid;
        $this->showbutton = $showbutton;
    }

    /**
     * Adds help icon.
     *
     * @deprecated since Moodle 2.0
     */
    public function set_old_help_icon($helppage, $title, $component = 'moodle') {
        throw new coding_exception('set_old_help_icon() can not be used any more, please see set_help_icon().');
    }

    /**
     * Adds help icon.
     *
     * @param string $identifier The keyword that defines a help page
     * @param string $component
     */
    public function set_help_icon($identifier, $component = 'moodle') {
        $this->helpicon = new help_icon($identifier, $component);
    }

    /**
     * Sets select's label
     *
     * @param string $label
     * @param array $attributes (optional)
     */
    public function set_label($label, $attributes = array()) {
        $this->label = $label;
        $this->labelattributes = $attributes;
    }

    /**
     * Clean a URL.
     *
     * @param string $value The URL.
     * @return The cleaned URL.
     */
    protected function clean_url($value) {
        global $CFG;

        if (empty($value)) {
            // Nothing.

        } else if (strpos($value, $CFG->wwwroot . '/') === 0) {
            $value = str_replace($CFG->wwwroot, '', $value);

        } else if (strpos($value, '/') !== 0) {
            debugging("Invalid url_select urls parameter: url '$value' is not local relative url!", DEBUG_DEVELOPER);
        }

        return $value;
    }

    /**
     * Flatten the options for Mustache.
     *
     * This also cleans the URLs.
     *
     * @param array $options The options.
     * @param array $nothing The nothing option.
     * @return array
     */
    protected function flatten_options($options, $nothing) {
        $flattened = [];

        foreach ($options as $value => $option) {
            if (is_array($option)) {
                foreach ($option as $groupname => $optoptions) {
                    if (!isset($flattened[$groupname])) {
                        $flattened[$groupname] = [
                            'name' => $groupname,
                            'isgroup' => true,
                            'options' => []
                        ];
                    }
                    foreach ($optoptions as $optvalue => $optoption) {
                        $cleanedvalue = $this->clean_url($optvalue);
                        $flattened[$groupname]['options'][$cleanedvalue] = [
                            'name' => $optoption,
                            'value' => $cleanedvalue,
                            'selected' => $this->selected == $optvalue,
                        ];
                    }
                }

            } else {
                $cleanedvalue = $this->clean_url($value);
                $flattened[$cleanedvalue] = [
                    'name' => $option,
                    'value' => $cleanedvalue,
                    'selected' => $this->selected == $value,
                ];
            }
        }

        if (!empty($nothing)) {
            $value = key($nothing);
            $name = reset($nothing);
            $flattened = [
                $value => ['name' => $name, 'value' => $value, 'selected' => $this->selected == $value]
            ] + $flattened;
        }

        // Make non-associative array.
        foreach ($flattened as $key => $value) {
            if (!empty($value['options'])) {
                $flattened[$key]['options'] = array_values($value['options']);
            }
        }
        $flattened = array_values($flattened);

        return $flattened;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $attributes = $this->attributes;

        $data = new stdClass();
        $data->formid = !empty($this->formid) ? $this->formid : html_writer::random_id('url_select_f');
        $data->classes = $this->class;
        $data->label = $this->label;
        $data->disabled = $this->disabled;
        $data->title = $this->tooltip;
        $data->id = !empty($attributes['id']) ? $attributes['id'] : html_writer::random_id('url_select');
        $data->sesskey = sesskey();
        $data->action = (new moodle_url('/course/jumpto.php'))->out(false);

        // Remove attributes passed as property directly.
        unset($attributes['class']);
        unset($attributes['id']);
        unset($attributes['name']);
        unset($attributes['title']);
        unset($attributes['disabled']);

        $data->showbutton = $this->showbutton;

        // Select options.
        $nothing = false;
        if (is_string($this->nothing) && $this->nothing !== '') {
            $nothing = ['' => $this->nothing];
        } else if (is_array($this->nothing)) {
            $nothingvalue = reset($this->nothing);
            if ($nothingvalue === 'choose' || $nothingvalue === 'choosedots') {
                $nothing = [key($this->nothing) => get_string('choosedots')];
            } else {
                $nothing = $this->nothing;
            }
        }
        $data->options = $this->flatten_options($this->urls, $nothing);

        // Label attributes.
        $data->labelattributes = [];
        // Unset label attributes that are already in the template.
        unset($this->labelattributes['for']);
        // Map the label attributes.
        foreach ($this->labelattributes as $key => $value) {
            $data->labelattributes[] = ['name' => $key, 'value' => $value];
        }

        // Help icon.
        $data->helpicon = !empty($this->helpicon) ? $this->helpicon->export_for_template($output) : false;

        // Finally all the remaining attributes.
        $data->attributes = [];
        foreach ($attributes as $key => $value) {
            $data->attributes[] = ['name' => $key, 'value' => $value];
        }

        return $data;
    }
}

/**
 * Data structure describing html link with special action attached.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class action_link implements renderable {

    /**
     * @var moodle_url Href url
     */
    public $url;

    /**
     * @var string Link text HTML fragment
     */
    public $text;

    /**
     * @var array HTML attributes
     */
    public $attributes;

    /**
     * @var array List of actions attached to link
     */
    public $actions;

    /**
     * @var pix_icon Optional pix icon to render with the link
     */
    public $icon;

    /**
     * Constructor
     * @param moodle_url $url
     * @param string $text HTML fragment
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     * @param pix_icon $icon optional pix_icon to render with the link text
     */
    public function __construct(moodle_url $url,
                                $text,
                                component_action $action=null,
                                array $attributes=null,
                                pix_icon $icon=null) {
        $this->url = clone($url);
        $this->text = $text;
        $this->attributes = (array)$attributes;
        if ($action) {
            $this->add_action($action);
        }
        $this->icon = $icon;
    }

    /**
     * Add action to the link.
     *
     * @param component_action $action
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Adds a CSS class to this action link object
     * @param string $class
     */
    public function add_class($class) {
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = $class;
        } else {
            $this->attributes['class'] .= ' ' . $class;
        }
    }

    /**
     * Returns true if the specified class has been added to this link.
     * @param string $class
     * @return bool
     */
    public function has_class($class) {
        return strpos(' ' . $this->attributes['class'] . ' ', ' ' . $class . ' ') !== false;
    }

    /**
     * Return the rendered HTML for the icon. Useful for rendering action links in a template.
     * @return string
     */
    public function get_icon_html() {
        global $OUTPUT;
        if (!$this->icon) {
            return '';
        }
        return $OUTPUT->render($this->icon);
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $attributes = $this->attributes;

        if (empty($attributes['id'])) {
            $attributes['id'] = html_writer::random_id('action_link');
        }
        $data->id = $attributes['id'];
        unset($attributes['id']);

        $data->disabled = !empty($attributes['disabled']);
        unset($attributes['disabled']);

        $data->text = $this->text instanceof renderable ? $output->render($this->text) : (string) $this->text;
        $data->url = $this->url ? $this->url->out(false) : '';
        $data->icon = $this->icon ? $this->icon->export_for_pix() : null;
        $data->classes = isset($attributes['class']) ? $attributes['class'] : '';
        unset($attributes['class']);

        $data->attributes = array_map(function($key, $value) {
            return [
                'name' => $key,
                'value' => $value
            ];
        }, array_keys($attributes), $attributes);

        $data->actions = array_map(function($action) use ($output) {
            return $action->export_for_template($output);
        }, !empty($this->actions) ? $this->actions : []);
        $data->hasactions = !empty($this->actions);

        return $data;
    }
}

/**
 * Simple html output class
 *
 * @copyright 2009 Tim Hunt, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class html_writer {

    /**
     * Outputs a tag with attributes and contents
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param string $contents What goes between the opening and closing tags
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function tag($tagname, $contents, array $attributes = null) {
        return self::start_tag($tagname, $attributes) . $contents . self::end_tag($tagname);
    }

    /**
     * Outputs an opening tag with attributes
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function start_tag($tagname, array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . '>';
    }

    /**
     * Outputs a closing tag
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @return string HTML fragment
     */
    public static function end_tag($tagname) {
        return '</' . $tagname . '>';
    }

    /**
     * Outputs an empty tag with attributes
     *
     * @param string $tagname The name of tag ('input', 'img', 'br' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function empty_tag($tagname, array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . ' />';
    }

    /**
     * Outputs a tag, but only if the contents are not empty
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param string $contents What goes between the opening and closing tags
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function nonempty_tag($tagname, $contents, array $attributes = null) {
        if ($contents === '' || is_null($contents)) {
            return '';
        }
        return self::tag($tagname, $contents, $attributes);
    }

    /**
     * Outputs a HTML attribute and value
     *
     * @param string $name The name of the attribute ('src', 'href', 'class' etc.)
     * @param string $value The value of the attribute. The value will be escaped with {@link s()}
     * @return string HTML fragment
     */
    public static function attribute($name, $value) {
        if ($value instanceof moodle_url) {
            return ' ' . $name . '="' . $value->out() . '"';
        }

        // special case, we do not want these in output
        if ($value === null) {
            return '';
        }

        // no sloppy trimming here!
        return ' ' . $name . '="' . s($value) . '"';
    }

    /**
     * Outputs a list of HTML attributes and values
     *
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     *       The values will be escaped with {@link s()}
     * @return string HTML fragment
     */
    public static function attributes(array $attributes = null) {
        $attributes = (array)$attributes;
        $output = '';
        foreach ($attributes as $name => $value) {
            $output .= self::attribute($name, $value);
        }
        return $output;
    }

    /**
     * Generates a simple image tag with attributes.
     *
     * @param string $src The source of image
     * @param string $alt The alternate text for image
     * @param array $attributes The tag attributes (array('height' => $max_height, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function img($src, $alt, array $attributes = null) {
        $attributes = (array)$attributes;
        $attributes['src'] = $src;
        $attributes['alt'] = $alt;

        return self::empty_tag('img', $attributes);
    }

    /**
     * Generates random html element id.
     *
     * @staticvar int $counter
     * @staticvar type $uniq
     * @param string $base A string fragment that will be included in the random ID.
     * @return string A unique ID
     */
    public static function random_id($base='random') {
        static $counter = 0;
        static $uniq;

        if (!isset($uniq)) {
            $uniq = uniqid();
        }

        $counter++;
        return $base.$uniq.$counter;
    }

    /**
     * Generates a simple html link
     *
     * @param string|moodle_url $url The URL
     * @param string $text The text
     * @param array $attributes HTML attributes
     * @return string HTML fragment
     */
    public static function link($url, $text, array $attributes = null) {
        $attributes = (array)$attributes;
        $attributes['href']  = $url;
        return self::tag('a', $text, $attributes);
    }

    /**
     * Generates a simple checkbox with optional label
     *
     * @param string $name The name of the checkbox
     * @param string $value The value of the checkbox
     * @param bool $checked Whether the checkbox is checked
     * @param string $label The label for the checkbox
     * @param array $attributes Any attributes to apply to the checkbox
     * @param array $labelattributes Any attributes to apply to the label, if present
     * @return string html fragment
     */
    public static function checkbox($name, $value, $checked = true, $label = '',
            array $attributes = null, array $labelattributes = null) {
        $attributes = (array) $attributes;
        $output = '';

        if ($label !== '' and !is_null($label)) {
            if (empty($attributes['id'])) {
                $attributes['id'] = self::random_id('checkbox_');
            }
        }
        $attributes['type']    = 'checkbox';
        $attributes['value']   = $value;
        $attributes['name']    = $name;
        $attributes['checked'] = $checked ? 'checked' : null;

        $output .= self::empty_tag('input', $attributes);

        if ($label !== '' and !is_null($label)) {
            $labelattributes = (array) $labelattributes;
            $labelattributes['for'] = $attributes['id'];
            $output .= self::tag('label', $label, $labelattributes);
        }

        return $output;
    }

    /**
     * Generates a simple select yes/no form field
     *
     * @param string $name name of select element
     * @param bool $selected
     * @param array $attributes - html select element attributes
     * @return string HTML fragment
     */
    public static function select_yes_no($name, $selected=true, array $attributes = null) {
        $options = array('1'=>get_string('yes'), '0'=>get_string('no'));
        return self::select($options, $name, $selected, null, $attributes);
    }

    /**
     * Generates a simple select form field
     *
     * @param array $options associative array value=>label ex.:
     *                array(1=>'One, 2=>Two)
     *              it is also possible to specify optgroup as complex label array ex.:
     *                array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *                array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     * @param string $name name of select element
     * @param string|array $selected value or array of values depending on multiple attribute
     * @param array|bool $nothing add nothing selected option, or false of not added
     * @param array $attributes html select element attributes
     * @return string HTML fragment
     */
    public static function select(array $options, $name, $selected = '', $nothing = array('' => 'choosedots'), array $attributes = null) {
        $attributes = (array)$attributes;
        if (is_array($nothing)) {
            foreach ($nothing as $k=>$v) {
                if ($v === 'choose' or $v === 'choosedots') {
                    $nothing[$k] = get_string('choosedots');
                }
            }
            $options = $nothing + $options; // keep keys, do not override

        } else if (is_string($nothing) and $nothing !== '') {
            // BC
            $options = array(''=>$nothing) + $options;
        }

        // we may accept more values if multiple attribute specified
        $selected = (array)$selected;
        foreach ($selected as $k=>$v) {
            $selected[$k] = (string)$v;
        }

        if (!isset($attributes['id'])) {
            $id = 'menu'.$name;
            // name may contaion [], which would make an invalid id. e.g. numeric question type editing form, assignment quickgrading
            $id = str_replace('[', '', $id);
            $id = str_replace(']', '', $id);
            $attributes['id'] = $id;
        }

        if (!isset($attributes['class'])) {
            $class = 'menu'.$name;
            // name may contaion [], which would make an invalid class. e.g. numeric question type editing form, assignment quickgrading
            $class = str_replace('[', '', $class);
            $class = str_replace(']', '', $class);
            $attributes['class'] = $class;
        }
        $attributes['class'] = 'select custom-select ' . $attributes['class']; // Add 'select' selector always.

        $attributes['name'] = $name;

        if (!empty($attributes['disabled'])) {
            $attributes['disabled'] = 'disabled';
        } else {
            unset($attributes['disabled']);
        }

        $output = '';
        foreach ($options as $value=>$label) {
            if (is_array($label)) {
                // ignore key, it just has to be unique
                $output .= self::select_optgroup(key($label), current($label), $selected);
            } else {
                $output .= self::select_option($label, $value, $selected);
            }
        }
        return self::tag('select', $output, $attributes);
    }

    /**
     * Returns HTML to display a select box option.
     *
     * @param string $label The label to display as the option.
     * @param string|int $value The value the option represents
     * @param array $selected An array of selected options
     * @return string HTML fragment
     */
    private static function select_option($label, $value, array $selected) {
        $attributes = array();
        $value = (string)$value;
        if (in_array($value, $selected, true)) {
            $attributes['selected'] = 'selected';
        }
        $attributes['value'] = $value;
        return self::tag('option', $label, $attributes);
    }

    /**
     * Returns HTML to display a select box option group.
     *
     * @param string $groupname The label to use for the group
     * @param array $options The options in the group
     * @param array $selected An array of selected values.
     * @return string HTML fragment.
     */
    private static function select_optgroup($groupname, $options, array $selected) {
        if (empty($options)) {
            return '';
        }
        $attributes = array('label'=>$groupname);
        $output = '';
        foreach ($options as $value=>$label) {
            $output .= self::select_option($label, $value, $selected);
        }
        return self::tag('optgroup', $output, $attributes);
    }

    /**
     * This is a shortcut for making an hour selector menu.
     *
     * @param string $type The type of selector (years, months, days, hours, minutes)
     * @param string $name fieldname
     * @param int $currenttime A default timestamp in GMT
     * @param int $step minute spacing
     * @param array $attributes - html select element attributes
     * @return HTML fragment
     */
    public static function select_time($type, $name, $currenttime = 0, $step = 5, array $attributes = null) {
        global $OUTPUT;

        if (!$currenttime) {
            $currenttime = time();
        }
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $currentdate = $calendartype->timestamp_to_date_array($currenttime);
        $userdatetype = $type;
        $timeunits = array();

        switch ($type) {
            case 'years':
                $timeunits = $calendartype->get_years();
                $userdatetype = 'year';
                break;
            case 'months':
                $timeunits = $calendartype->get_months();
                $userdatetype = 'month';
                $currentdate['month'] = (int)$currentdate['mon'];
                break;
            case 'days':
                $timeunits = $calendartype->get_days();
                $userdatetype = 'mday';
                break;
            case 'hours':
                for ($i=0; $i<=23; $i++) {
                    $timeunits[$i] = sprintf("%02d",$i);
                }
                break;
            case 'minutes':
                if ($step != 1) {
                    $currentdate['minutes'] = ceil($currentdate['minutes']/$step)*$step;
                }

                for ($i=0; $i<=59; $i+=$step) {
                    $timeunits[$i] = sprintf("%02d",$i);
                }
                break;
            default:
                throw new coding_exception("Time type $type is not supported by html_writer::select_time().");
        }

        $attributes = (array) $attributes;
        $data = (object) [
            'name' => $name,
            'id' => !empty($attributes['id']) ? $attributes['id'] : self::random_id('ts_'),
            'label' => get_string(substr($type, 0, -1), 'form'),
            'options' => array_map(function($value) use ($timeunits, $currentdate, $userdatetype) {
                return [
                    'name' => $timeunits[$value],
                    'value' => $value,
                    'selected' => $currentdate[$userdatetype] == $value
                ];
            }, array_keys($timeunits)),
        ];

        unset($attributes['id']);
        unset($attributes['name']);
        $data->attributes = array_map(function($name) use ($attributes) {
            return [
                'name' => $name,
                'value' => $attributes[$name]
            ];
        }, array_keys($attributes));

        return $OUTPUT->render_from_template('core/select_time', $data);
    }

    /**
     * Shortcut for quick making of lists
     *
     * Note: 'list' is a reserved keyword ;-)
     *
     * @param array $items
     * @param array $attributes
     * @param string $tag ul or ol
     * @return string
     */
    public static function alist(array $items, array $attributes = null, $tag = 'ul') {
        $output = html_writer::start_tag($tag, $attributes)."\n";
        foreach ($items as $item) {
            $output .= html_writer::tag('li', $item)."\n";
        }
        $output .= html_writer::end_tag($tag);
        return $output;
    }

    /**
     * Returns hidden input fields created from url parameters.
     *
     * @param moodle_url $url
     * @param array $exclude list of excluded parameters
     * @return string HTML fragment
     */
    public static function input_hidden_params(moodle_url $url, array $exclude = null) {
        $exclude = (array)$exclude;
        $params = $url->params();
        foreach ($exclude as $key) {
            unset($params[$key]);
        }

        $output = '';
        foreach ($params as $key => $value) {
            $attributes = array('type'=>'hidden', 'name'=>$key, 'value'=>$value);
            $output .= self::empty_tag('input', $attributes)."\n";
        }
        return $output;
    }

    /**
     * Generate a script tag containing the the specified code.
     *
     * @param string $jscode the JavaScript code
     * @param moodle_url|string $url optional url of the external script, $code ignored if specified
     * @return string HTML, the code wrapped in <script> tags.
     */
    public static function script($jscode, $url=null) {
        if ($jscode) {
            return self::tag('script', "\n//<![CDATA[\n$jscode\n//]]>\n") . "\n";

        } else if ($url) {
            return self::tag('script', '', ['src' => $url]) . "\n";

        } else {
            return '';
        }
    }

    /**
     * Renders HTML table
     *
     * This method may modify the passed instance by adding some default properties if they are not set yet.
     * If this is not what you want, you should make a full clone of your data before passing them to this
     * method. In most cases this is not an issue at all so we do not clone by default for performance
     * and memory consumption reasons.
     *
     * @param html_table $table data to be rendered
     * @return string HTML code
     */
    public static function table(html_table $table) {
        // prepare table data and populate missing properties with reasonable defaults
        if (!empty($table->align)) {
            foreach ($table->align as $key => $aa) {
                if ($aa) {
                    $table->align[$key] = 'text-align:'. fix_align_rtl($aa) .';';  // Fix for RTL languages
                } else {
                    $table->align[$key] = null;
                }
            }
        }
        if (!empty($table->size)) {
            foreach ($table->size as $key => $ss) {
                if ($ss) {
                    $table->size[$key] = 'width:'. $ss .';';
                } else {
                    $table->size[$key] = null;
                }
            }
        }
        if (!empty($table->wrap)) {
            foreach ($table->wrap as $key => $ww) {
                if ($ww) {
                    $table->wrap[$key] = 'white-space:nowrap;';
                } else {
                    $table->wrap[$key] = '';
                }
            }
        }
        if (!empty($table->head)) {
            foreach ($table->head as $key => $val) {
                if (!isset($table->align[$key])) {
                    $table->align[$key] = null;
                }
                if (!isset($table->size[$key])) {
                    $table->size[$key] = null;
                }
                if (!isset($table->wrap[$key])) {
                    $table->wrap[$key] = null;
                }

            }
        }
        if (empty($table->attributes['class'])) {
            $table->attributes['class'] = 'generaltable';
        }
        if (!empty($table->tablealign)) {
            $table->attributes['class'] .= ' boxalign' . $table->tablealign;
        }

        // explicitly assigned properties override those defined via $table->attributes
        $table->attributes['class'] = trim($table->attributes['class']);
        $attributes = array_merge($table->attributes, array(
                'id'            => $table->id,
                'width'         => $table->width,
                'summary'       => $table->summary,
                'cellpadding'   => $table->cellpadding,
                'cellspacing'   => $table->cellspacing,
            ));
        $output = html_writer::start_tag('table', $attributes) . "\n";

        $countcols = 0;

        // Output a caption if present.
        if (!empty($table->caption)) {
            $captionattributes = array();
            if ($table->captionhide) {
                $captionattributes['class'] = 'accesshide';
            }
            $output .= html_writer::tag(
                'caption',
                $table->caption,
                $captionattributes
            );
        }

        if (!empty($table->head)) {
            $countcols = count($table->head);

            $output .= html_writer::start_tag('thead', array()) . "\n";
            $output .= html_writer::start_tag('tr', array()) . "\n";
            $keys = array_keys($table->head);
            $lastkey = end($keys);

            foreach ($table->head as $key => $heading) {
                // Convert plain string headings into html_table_cell objects
                if (!($heading instanceof html_table_cell)) {
                    $headingtext = $heading;
                    $heading = new html_table_cell();
                    $heading->text = $headingtext;
                    $heading->header = true;
                }

                if ($heading->header !== false) {
                    $heading->header = true;
                }

                $tagtype = 'td';
                if ($heading->header && (string)$heading->text != '') {
                    $tagtype = 'th';
                }

                $heading->attributes['class'] .= ' header c' . $key;
                if (isset($table->headspan[$key]) && $table->headspan[$key] > 1) {
                    $heading->colspan = $table->headspan[$key];
                    $countcols += $table->headspan[$key] - 1;
                }

                if ($key == $lastkey) {
                    $heading->attributes['class'] .= ' lastcol';
                }
                if (isset($table->colclasses[$key])) {
                    $heading->attributes['class'] .= ' ' . $table->colclasses[$key];
                }
                $heading->attributes['class'] = trim($heading->attributes['class']);
                $attributes = array_merge($heading->attributes, [
                    'style'     => $table->align[$key] . $table->size[$key] . $heading->style,
                    'colspan'   => $heading->colspan,
                ]);

                if ($tagtype == 'th') {
                    $attributes['scope'] = !empty($heading->scope) ? $heading->scope : 'col';
                }

                $output .= html_writer::tag($tagtype, $heading->text, $attributes) . "\n";
            }
            $output .= html_writer::end_tag('tr') . "\n";
            $output .= html_writer::end_tag('thead') . "\n";

            if (empty($table->data)) {
                // For valid XHTML strict every table must contain either a valid tr
                // or a valid tbody... both of which must contain a valid td
                $output .= html_writer::start_tag('tbody', array('class' => 'empty'));
                $output .= html_writer::tag('tr', html_writer::tag('td', '', array('colspan'=>count($table->head))));
                $output .= html_writer::end_tag('tbody');
            }
        }

        if (!empty($table->data)) {
            $keys       = array_keys($table->data);
            $lastrowkey = end($keys);
            $output .= html_writer::start_tag('tbody', array());

            foreach ($table->data as $key => $row) {
                if (($row === 'hr') && ($countcols)) {
                    $output .= html_writer::tag('td', html_writer::tag('div', '', array('class' => 'tabledivider')), array('colspan' => $countcols));
                } else {
                    // Convert array rows to html_table_rows and cell strings to html_table_cell objects
                    if (!($row instanceof html_table_row)) {
                        $newrow = new html_table_row();

                        foreach ($row as $cell) {
                            if (!($cell instanceof html_table_cell)) {
                                $cell = new html_table_cell($cell);
                            }
                            $newrow->cells[] = $cell;
                        }
                        $row = $newrow;
                    }

                    if (isset($table->rowclasses[$key])) {
                        $row->attributes['class'] .= ' ' . $table->rowclasses[$key];
                    }

                    if ($key == $lastrowkey) {
                        $row->attributes['class'] .= ' lastrow';
                    }

                    // Explicitly assigned properties should override those defined in the attributes.
                    $row->attributes['class'] = trim($row->attributes['class']);
                    $trattributes = array_merge($row->attributes, array(
                            'id'            => $row->id,
                            'style'         => $row->style,
                        ));
                    $output .= html_writer::start_tag('tr', $trattributes) . "\n";
                    $keys2 = array_keys($row->cells);
                    $lastkey = end($keys2);

                    $gotlastkey = false; //flag for sanity checking
                    foreach ($row->cells as $key => $cell) {
                        if ($gotlastkey) {
                            //This should never happen. Why do we have a cell after the last cell?
                            mtrace("A cell with key ($key) was found after the last key ($lastkey)");
                        }

                        if (!($cell instanceof html_table_cell)) {
                            $mycell = new html_table_cell();
                            $mycell->text = $cell;
                            $cell = $mycell;
                        }

                        if (($cell->header === true) && empty($cell->scope)) {
                            $cell->scope = 'row';
                        }

                        if (isset($table->colclasses[$key])) {
                            $cell->attributes['class'] .= ' ' . $table->colclasses[$key];
                        }

                        $cell->attributes['class'] .= ' cell c' . $key;
                        if ($key == $lastkey) {
                            $cell->attributes['class'] .= ' lastcol';
                            $gotlastkey = true;
                        }
                        $tdstyle = '';
                        $tdstyle .= isset($table->align[$key]) ? $table->align[$key] : '';
                        $tdstyle .= isset($table->size[$key]) ? $table->size[$key] : '';
                        $tdstyle .= isset($table->wrap[$key]) ? $table->wrap[$key] : '';
                        $cell->attributes['class'] = trim($cell->attributes['class']);
                        $tdattributes = array_merge($cell->attributes, array(
                                'style' => $tdstyle . $cell->style,
                                'colspan' => $cell->colspan,
                                'rowspan' => $cell->rowspan,
                                'id' => $cell->id,
                                'abbr' => $cell->abbr,
                                'scope' => $cell->scope,
                            ));
                        $tagtype = 'td';
                        if ($cell->header === true) {
                            $tagtype = 'th';
                        }
                        $output .= html_writer::tag($tagtype, $cell->text, $tdattributes) . "\n";
                    }
                }
                $output .= html_writer::end_tag('tr') . "\n";
            }
            $output .= html_writer::end_tag('tbody') . "\n";
        }
        $output .= html_writer::end_tag('table') . "\n";

        return $output;
    }

    /**
     * Renders form element label
     *
     * By default, the label is suffixed with a label separator defined in the
     * current language pack (colon by default in the English lang pack).
     * Adding the colon can be explicitly disabled if needed. Label separators
     * are put outside the label tag itself so they are not read by
     * screenreaders (accessibility).
     *
     * Parameter $for explicitly associates the label with a form control. When
     * set, the value of this attribute must be the same as the value of
     * the id attribute of the form control in the same document. When null,
     * the label being defined is associated with the control inside the label
     * element.
     *
     * @param string $text content of the label tag
     * @param string|null $for id of the element this label is associated with, null for no association
     * @param bool $colonize add label separator (colon) to the label text, if it is not there yet
     * @param array $attributes to be inserted in the tab, for example array('accesskey' => 'a')
     * @return string HTML of the label element
     */
    public static function label($text, $for, $colonize = true, array $attributes=array()) {
        if (!is_null($for)) {
            $attributes = array_merge($attributes, array('for' => $for));
        }
        $text = trim($text);
        $label = self::tag('label', $text, $attributes);

        // TODO MDL-12192 $colonize disabled for now yet
        // if (!empty($text) and $colonize) {
        //     // the $text may end with the colon already, though it is bad string definition style
        //     $colon = get_string('labelsep', 'langconfig');
        //     if (!empty($colon)) {
        //         $trimmed = trim($colon);
        //         if ((substr($text, -strlen($trimmed)) == $trimmed) or (substr($text, -1) == ':')) {
        //             //debugging('The label text should not end with colon or other label separator,
        //             //           please fix the string definition.', DEBUG_DEVELOPER);
        //         } else {
        //             $label .= $colon;
        //         }
        //     }
        // }

        return $label;
    }

    /**
     * Combines a class parameter with other attributes. Aids in code reduction
     * because the class parameter is very frequently used.
     *
     * If the class attribute is specified both in the attributes and in the
     * class parameter, the two values are combined with a space between.
     *
     * @param string $class Optional CSS class (or classes as space-separated list)
     * @param array $attributes Optional other attributes as array
     * @return array Attributes (or null if still none)
     */
    private static function add_class($class = '', array $attributes = null) {
        if ($class !== '') {
            $classattribute = array('class' => $class);
            if ($attributes) {
                if (array_key_exists('class', $attributes)) {
                    $attributes['class'] = trim($attributes['class'] . ' ' . $class);
                } else {
                    $attributes = $classattribute + $attributes;
                }
            } else {
                $attributes = $classattribute;
            }
        }
        return $attributes;
    }

    /**
     * Creates a <div> tag. (Shortcut function.)
     *
     * @param string $content HTML content of tag
     * @param string $class Optional CSS class (or classes as space-separated list)
     * @param array $attributes Optional other attributes as array
     * @return string HTML code for div
     */
    public static function div($content, $class = '', array $attributes = null) {
        return self::tag('div', $content, self::add_class($class, $attributes));
    }

    /**
     * Starts a <div> tag. (Shortcut function.)
     *
     * @param string $class Optional CSS class (or classes as space-separated list)
     * @param array $attributes Optional other attributes as array
     * @return string HTML code for open div tag
     */
    public static function start_div($class = '', array $attributes = null) {
        return self::start_tag('div', self::add_class($class, $attributes));
    }

    /**
     * Ends a <div> tag. (Shortcut function.)
     *
     * @return string HTML code for close div tag
     */
    public static function end_div() {
        return self::end_tag('div');
    }

    /**
     * Creates a <span> tag. (Shortcut function.)
     *
     * @param string $content HTML content of tag
     * @param string $class Optional CSS class (or classes as space-separated list)
     * @param array $attributes Optional other attributes as array
     * @return string HTML code for span
     */
    public static function span($content, $class = '', array $attributes = null) {
        return self::tag('span', $content, self::add_class($class, $attributes));
    }

    /**
     * Starts a <span> tag. (Shortcut function.)
     *
     * @param string $class Optional CSS class (or classes as space-separated list)
     * @param array $attributes Optional other attributes as array
     * @return string HTML code for open span tag
     */
    public static function start_span($class = '', array $attributes = null) {
        return self::start_tag('span', self::add_class($class, $attributes));
    }

    /**
     * Ends a <span> tag. (Shortcut function.)
     *
     * @return string HTML code for close span tag
     */
    public static function end_span() {
        return self::end_tag('span');
    }
}

/**
 * Simple javascript output class
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class js_writer {

    /**
     * Returns javascript code calling the function
     *
     * @param string $function function name, can be complex like Y.Event.purgeElement
     * @param array $arguments parameters
     * @param int $delay execution delay in seconds
     * @return string JS code fragment
     */
    public static function function_call($function, array $arguments = null, $delay=0) {
        if ($arguments) {
            $arguments = array_map('json_encode', convert_to_array($arguments));
            $arguments = implode(', ', $arguments);
        } else {
            $arguments = '';
        }
        $js = "$function($arguments);";

        if ($delay) {
            $delay = $delay * 1000; // in miliseconds
            $js = "setTimeout(function() { $js }, $delay);";
        }
        return $js . "\n";
    }

    /**
     * Special function which adds Y as first argument of function call.
     *
     * @param string $function The function to call
     * @param array $extraarguments Any arguments to pass to it
     * @return string Some JS code
     */
    public static function function_call_with_Y($function, array $extraarguments = null) {
        if ($extraarguments) {
            $extraarguments = array_map('json_encode', convert_to_array($extraarguments));
            $arguments = 'Y, ' . implode(', ', $extraarguments);
        } else {
            $arguments = 'Y';
        }
        return "$function($arguments);\n";
    }

    /**
     * Returns JavaScript code to initialise a new object
     *
     * @param string $var If it is null then no var is assigned the new object.
     * @param string $class The class to initialise an object for.
     * @param array $arguments An array of args to pass to the init method.
     * @param array $requirements Any modules required for this class.
     * @param int $delay The delay before initialisation. 0 = no delay.
     * @return string Some JS code
     */
    public static function object_init($var, $class, array $arguments = null, array $requirements = null, $delay=0) {
        if (is_array($arguments)) {
            $arguments = array_map('json_encode', convert_to_array($arguments));
            $arguments = implode(', ', $arguments);
        }

        if ($var === null) {
            $js = "new $class(Y, $arguments);";
        } else if (strpos($var, '.')!==false) {
            $js = "$var = new $class(Y, $arguments);";
        } else {
            $js = "var $var = new $class(Y, $arguments);";
        }

        if ($delay) {
            $delay = $delay * 1000; // in miliseconds
            $js = "setTimeout(function() { $js }, $delay);";
        }

        if (count($requirements) > 0) {
            $requirements = implode("', '", $requirements);
            $js = "Y.use('$requirements', function(Y){ $js });";
        }
        return $js."\n";
    }

    /**
     * Returns code setting value to variable
     *
     * @param string $name
     * @param mixed $value json serialised value
     * @param bool $usevar add var definition, ignored for nested properties
     * @return string JS code fragment
     */
    public static function set_variable($name, $value, $usevar = true) {
        $output = '';

        if ($usevar) {
            if (strpos($name, '.')) {
                $output .= '';
            } else {
                $output .= 'var ';
            }
        }

        $output .= "$name = ".json_encode($value).";";

        return $output;
    }

    /**
     * Writes event handler attaching code
     *
     * @param array|string $selector standard YUI selector for elements, may be
     *     array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array $arguments An optional array of argument parameters to pass to the function
     * @return string JS code fragment
     */
    public static function event_handler($selector, $event, $function, array $arguments = null) {
        $selector = json_encode($selector);
        $output = "Y.on('$event', $function, $selector, null";
        if (!empty($arguments)) {
            $output .= ', ' . json_encode($arguments);
        }
        return $output . ");\n";
    }
}

/**
 * Holds all the information required to render a <table> by {@link core_renderer::table()}
 *
 * Example of usage:
 * $t = new html_table();
 * ... // set various properties of the object $t as described below
 * echo html_writer::table($t);
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class html_table {

    /**
     * @var string Value to use for the id attribute of the table
     */
    public $id = null;

    /**
     * @var array Attributes of HTML attributes for the <table> element
     */
    public $attributes = array();

    /**
     * @var array An array of headings. The n-th array item is used as a heading of the n-th column.
     * For more control over the rendering of the headers, an array of html_table_cell objects
     * can be passed instead of an array of strings.
     *
     * Example of usage:
     * $t->head = array('Student', 'Grade');
     */
    public $head;

    /**
     * @var array An array that can be used to make a heading span multiple columns.
     * In this example, {@link html_table:$data} is supposed to have three columns. For the first two columns,
     * the same heading is used. Therefore, {@link html_table::$head} should consist of two items.
     *
     * Example of usage:
     * $t->headspan = array(2,1);
     */
    public $headspan;

    /**
     * @var array An array of column alignments.
     * The value is used as CSS 'text-align' property. Therefore, possible
     * values are 'left', 'right', 'center' and 'justify'. Specify 'right' or 'left' from the perspective
     * of a left-to-right (LTR) language. For RTL, the values are flipped automatically.
     *
     * Examples of usage:
     * $t->align = array(null, 'right');
     * or
     * $t->align[1] = 'right';
     */
    public $align;

    /**
     * @var array The value is used as CSS 'size' property.
     *
     * Examples of usage:
     * $t->size = array('50%', '50%');
     * or
     * $t->size[1] = '120px';
     */
    public $size;

    /**
     * @var array An array of wrapping information.
     * The only possible value is 'nowrap' that sets the
     * CSS property 'white-space' to the value 'nowrap' in the given column.
     *
     * Example of usage:
     * $t->wrap = array(null, 'nowrap');
     */
    public $wrap;

    /**
     * @var array Array of arrays or html_table_row objects containing the data. Alternatively, if you have
     * $head specified, the string 'hr' (for horizontal ruler) can be used
     * instead of an array of cells data resulting in a divider rendered.
     *
     * Example of usage with array of arrays:
     * $row1 = array('Harry Potter', '76 %');
     * $row2 = array('Hermione Granger', '100 %');
     * $t->data = array($row1, $row2);
     *
     * Example with array of html_table_row objects: (used for more fine-grained control)
     * $cell1 = new html_table_cell();
     * $cell1->text = 'Harry Potter';
     * $cell1->colspan = 2;
     * $row1 = new html_table_row();
     * $row1->cells[] = $cell1;
     * $cell2 = new html_table_cell();
     * $cell2->text = 'Hermione Granger';
     * $cell3 = new html_table_cell();
     * $cell3->text = '100 %';
     * $row2 = new html_table_row();
     * $row2->cells = array($cell2, $cell3);
     * $t->data = array($row1, $row2);
     */
    public $data = [];

    /**
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     * @var string Width of the table, percentage of the page preferred.
     */
    public $width = null;

    /**
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     * @var string Alignment for the whole table. Can be 'right', 'left' or 'center' (default).
     */
    public $tablealign = null;

    /**
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     * @var int Padding on each cell, in pixels
     */
    public $cellpadding = null;

    /**
     * @var int Spacing between cells, in pixels
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $cellspacing = null;

    /**
     * @var array Array of classes to add to particular rows, space-separated string.
     * Class 'lastrow' is added automatically for the last row in the table.
     *
     * Example of usage:
     * $t->rowclasses[9] = 'tenth'
     */
    public $rowclasses;

    /**
     * @var array An array of classes to add to every cell in a particular column,
     * space-separated string. Class 'cell' is added automatically by the renderer.
     * Classes 'c0' or 'c1' are added automatically for every odd or even column,
     * respectively. Class 'lastcol' is added automatically for all last cells
     * in a row.
     *
     * Example of usage:
     * $t->colclasses = array(null, 'grade');
     */
    public $colclasses;

    /**
     * @var string Description of the contents for screen readers.
     *
     * The "summary" attribute on the "table" element is not supported in HTML5.
     * Consider describing the structure of the table in a "caption" element or in a "figure" element containing the table;
     * or, simplify the structure of the table so that no description is needed.
     *
     * @deprecated since Moodle 3.9.
     */
    public $summary;

    /**
     * @var string Caption for the table, typically a title.
     *
     * Example of usage:
     * $t->caption = "TV Guide";
     */
    public $caption;

    /**
     * @var bool Whether to hide the table's caption from sighted users.
     *
     * Example of usage:
     * $t->caption = "TV Guide";
     * $t->captionhide = true;
     */
    public $captionhide = false;

    /**
     * Constructor
     */
    public function __construct() {
        $this->attributes['class'] = '';
    }
}

/**
 * Component representing a table row.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class html_table_row {

    /**
     * @var string Value to use for the id attribute of the row.
     */
    public $id = null;

    /**
     * @var array Array of html_table_cell objects
     */
    public $cells = array();

    /**
     * @var string Value to use for the style attribute of the table row
     */
    public $style = null;

    /**
     * @var array Attributes of additional HTML attributes for the <tr> element
     */
    public $attributes = array();

    /**
     * Constructor
     * @param array $cells
     */
    public function __construct(array $cells=null) {
        $this->attributes['class'] = '';
        $cells = (array)$cells;
        foreach ($cells as $cell) {
            if ($cell instanceof html_table_cell) {
                $this->cells[] = $cell;
            } else {
                $this->cells[] = new html_table_cell($cell);
            }
        }
    }
}

/**
 * Component representing a table cell.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class html_table_cell {

    /**
     * @var string Value to use for the id attribute of the cell.
     */
    public $id = null;

    /**
     * @var string The contents of the cell.
     */
    public $text;

    /**
     * @var string Abbreviated version of the contents of the cell.
     */
    public $abbr = null;

    /**
     * @var int Number of columns this cell should span.
     */
    public $colspan = null;

    /**
     * @var int Number of rows this cell should span.
     */
    public $rowspan = null;

    /**
     * @var string Defines a way to associate header cells and data cells in a table.
     */
    public $scope = null;

    /**
     * @var bool Whether or not this cell is a header cell.
     */
    public $header = null;

    /**
     * @var string Value to use for the style attribute of the table cell
     */
    public $style = null;

    /**
     * @var array Attributes of additional HTML attributes for the <td> element
     */
    public $attributes = array();

    /**
     * Constructs a table cell
     *
     * @param string $text
     */
    public function __construct($text = null) {
        $this->text = $text;
        $this->attributes['class'] = '';
    }
}

/**
 * Component representing a paging bar.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class paging_bar implements renderable, templatable {

    /**
     * @var int The maximum number of pagelinks to display.
     */
    public $maxdisplay = 18;

    /**
     * @var int The total number of entries to be pages through..
     */
    public $totalcount;

    /**
     * @var int The page you are currently viewing.
     */
    public $page;

    /**
     * @var int The number of entries that should be shown per page.
     */
    public $perpage;

    /**
     * @var string|moodle_url If this  is a string then it is the url which will be appended with $pagevar,
     * an equals sign and the page number.
     * If this is a moodle_url object then the pagevar param will be replaced by
     * the page no, for each page.
     */
    public $baseurl;

    /**
     * @var string This is the variable name that you use for the pagenumber in your
     * code (ie. 'tablepage', 'blogpage', etc)
     */
    public $pagevar;

    /**
     * @var string A HTML link representing the "previous" page.
     */
    public $previouslink = null;

    /**
     * @var string A HTML link representing the "next" page.
     */
    public $nextlink = null;

    /**
     * @var string A HTML link representing the first page.
     */
    public $firstlink = null;

    /**
     * @var string A HTML link representing the last page.
     */
    public $lastlink = null;

    /**
     * @var array An array of strings. One of them is just a string: the current page
     */
    public $pagelinks = array();

    /**
     * Constructor paging_bar with only the required params.
     *
     * @param int $totalcount The total number of entries available to be paged through
     * @param int $page The page you are currently viewing
     * @param int $perpage The number of entries that should be shown per page
     * @param string|moodle_url $baseurl url of the current page, the $pagevar parameter is added
     * @param string $pagevar name of page parameter that holds the page number
     */
    public function __construct($totalcount, $page, $perpage, $baseurl, $pagevar = 'page') {
        $this->totalcount = $totalcount;
        $this->page       = $page;
        $this->perpage    = $perpage;
        $this->baseurl    = $baseurl;
        $this->pagevar    = $pagevar;
    }

    /**
     * Prepares the paging bar for output.
     *
     * This method validates the arguments set up for the paging bar and then
     * produces fragments of HTML to assist display later on.
     *
     * @param renderer_base $output
     * @param moodle_page $page
     * @param string $target
     * @throws coding_exception
     */
    public function prepare(renderer_base $output, moodle_page $page, $target) {
        if (!isset($this->totalcount) || is_null($this->totalcount)) {
            throw new coding_exception('paging_bar requires a totalcount value.');
        }
        if (!isset($this->page) || is_null($this->page)) {
            throw new coding_exception('paging_bar requires a page value.');
        }
        if (empty($this->perpage)) {
            throw new coding_exception('paging_bar requires a perpage value.');
        }
        if (empty($this->baseurl)) {
            throw new coding_exception('paging_bar requires a baseurl value.');
        }

        if ($this->totalcount > $this->perpage) {
            $pagenum = $this->page - 1;

            if ($this->page > 0) {
                $this->previouslink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>$pagenum)), get_string('previous'), array('class'=>'previous'));
            }

            if ($this->perpage > 0) {
                $lastpage = ceil($this->totalcount / $this->perpage);
            } else {
                $lastpage = 1;
            }

            if ($this->page > round(($this->maxdisplay/3)*2)) {
                $currpage = $this->page - round($this->maxdisplay/3);

                $this->firstlink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>0)), '1', array('class'=>'first'));
            } else {
                $currpage = 0;
            }

            $displaycount = $displaypage = 0;

            while ($displaycount < $this->maxdisplay and $currpage < $lastpage) {
                $displaypage = $currpage + 1;

                if ($this->page == $currpage) {
                    $this->pagelinks[] = html_writer::span($displaypage, 'current-page');
                } else {
                    $pagelink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>$currpage)), $displaypage);
                    $this->pagelinks[] = $pagelink;
                }

                $displaycount++;
                $currpage++;
            }

            if ($currpage < $lastpage) {
                $lastpageactual = $lastpage - 1;
                $this->lastlink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>$lastpageactual)), $lastpage, array('class'=>'last'));
            }

            $pagenum = $this->page + 1;

            if ($pagenum != $lastpage) {
                $this->nextlink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>$pagenum)), get_string('next'), array('class'=>'next'));
            }
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->previous = null;
        $data->next = null;
        $data->first = null;
        $data->last = null;
        $data->label = get_string('page');
        $data->pages = [];
        $data->haspages = $this->totalcount > $this->perpage;
        $data->pagesize = $this->perpage;

        if (!$data->haspages) {
            return $data;
        }

        if ($this->page > 0) {
            $data->previous = [
                'page' => $this->page,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $this->page - 1]))->out(false)
            ];
        }

        $currpage = 0;
        if ($this->page > round(($this->maxdisplay / 3) * 2)) {
            $currpage = $this->page - round($this->maxdisplay / 3);
            $data->first = [
                'page' => 1,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => 0]))->out(false)
            ];
        }

        $lastpage = 1;
        if ($this->perpage > 0) {
            $lastpage = ceil($this->totalcount / $this->perpage);
        }

        $displaycount = 0;
        $displaypage = 0;
        while ($displaycount < $this->maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage + 1;

            $iscurrent = $this->page == $currpage;
            $link = new moodle_url($this->baseurl, [$this->pagevar => $currpage]);

            $data->pages[] = [
                'page' => $displaypage,
                'active' => $iscurrent,
                'url' => $iscurrent ? null : $link->out(false)
            ];

            $displaycount++;
            $currpage++;
        }

        if ($currpage < $lastpage) {
            $data->last = [
                'page' => $lastpage,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $lastpage - 1]))->out(false)
            ];
        }

        if ($this->page + 1 != $lastpage) {
            $data->next = [
                'page' => $this->page + 2,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $this->page + 1]))->out(false)
            ];
        }

        return $data;
    }
}

/**
 * Component representing initials bar.
 *
 * @copyright 2017 Ilya Tregubov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 * @package core
 * @category output
 */
class initials_bar implements renderable, templatable {

    /**
     * @var string Currently selected letter.
     */
    public $current;

    /**
     * @var string Class name to add to this initial bar.
     */
    public $class;

    /**
     * @var string The name to put in front of this initial bar.
     */
    public $title;

    /**
     * @var string URL parameter name for this initial.
     */
    public $urlvar;

    /**
     * @var string URL object.
     */
    public $url;

    /**
     * @var array An array of letters in the alphabet.
     */
    public $alpha;

    /**
     * Constructor initials_bar with only the required params.
     *
     * @param string $current the currently selected letter.
     * @param string $class class name to add to this initial bar.
     * @param string $title the name to put in front of this initial bar.
     * @param string $urlvar URL parameter name for this initial.
     * @param string $url URL object.
     * @param array $alpha of letters in the alphabet.
     */
    public function __construct($current, $class, $title, $urlvar, $url, $alpha = null) {
        $this->current       = $current;
        $this->class    = $class;
        $this->title    = $title;
        $this->urlvar    = $urlvar;
        $this->url    = $url;
        $this->alpha    = $alpha;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        if ($this->alpha == null) {
            $this->alpha = explode(',', get_string('alphabet', 'langconfig'));
        }

        if ($this->current == 'all') {
            $this->current = '';
        }

        // We want to find a letter grouping size which suits the language so
        // find the largest group size which is less than 15 chars.
        // The choice of 15 chars is the largest number of chars that reasonably
        // fits on the smallest supported screen size. By always using a max number
        // of groups which is a factor of 2, we always get nice wrapping, and the
        // last row is always the shortest.
        $groupsize = count($this->alpha);
        $groups = 1;
        while ($groupsize > 15) {
            $groups *= 2;
            $groupsize = ceil(count($this->alpha) / $groups);
        }

        $groupsizelimit = 0;
        $groupnumber = 0;
        foreach ($this->alpha as $letter) {
            if ($groupsizelimit++ > 0 && $groupsizelimit % $groupsize == 1) {
                $groupnumber++;
            }
            $groupletter = new stdClass();
            $groupletter->name = $letter;
            $groupletter->url = $this->url->out(false, array($this->urlvar => $letter));
            if ($letter == $this->current) {
                $groupletter->selected = $this->current;
            }
            if (!isset($data->group[$groupnumber])) {
                $data->group[$groupnumber] = new stdClass();
            }
            $data->group[$groupnumber]->letter[] = $groupletter;
        }

        $data->class = $this->class;
        $data->title = $this->title;
        $data->url = $this->url->out(false, array($this->urlvar => ''));
        $data->current = $this->current;
        $data->all = get_string('all');

        return $data;
    }
}

/**
 * This class represents how a block appears on a page.
 *
 * During output, each block instance is asked to return a block_contents object,
 * those are then passed to the $OUTPUT->block function for display.
 *
 * contents should probably be generated using a moodle_block_..._renderer.
 *
 * Other block-like things that need to appear on the page, for example the
 * add new block UI, are also represented as block_contents objects.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class block_contents {

    /** Used when the block cannot be collapsed **/
    const NOT_HIDEABLE = 0;

    /** Used when the block can be collapsed but currently is not **/
    const VISIBLE = 1;

    /** Used when the block has been collapsed **/
    const HIDDEN = 2;

    /**
     * @var int Used to set $skipid.
     */
    protected static $idcounter = 1;

    /**
     * @var int All the blocks (or things that look like blocks) printed on
     * a page are given a unique number that can be used to construct id="" attributes.
     * This is set automatically be the {@link prepare()} method.
     * Do not try to set it manually.
     */
    public $skipid;

    /**
     * @var int If this is the contents of a real block, this should be set
     * to the block_instance.id. Otherwise this should be set to 0.
     */
    public $blockinstanceid = 0;

    /**
     * @var int If this is a real block instance, and there is a corresponding
     * block_position.id for the block on this page, this should be set to that id.
     * Otherwise it should be 0.
     */
    public $blockpositionid = 0;

    /**
     * @var array An array of attribute => value pairs that are put on the outer div of this
     * block. {@link $id} and {@link $classes} attributes should be set separately.
     */
    public $attributes;

    /**
     * @var string The title of this block. If this came from user input, it should already
     * have had format_string() processing done on it. This will be output inside
     * <h2> tags. Please do not cause invalid XHTML.
     */
    public $title = '';

    /**
     * @var string The label to use when the block does not, or will not have a visible title.
     * You should never set this as well as title... it will just be ignored.
     */
    public $arialabel = '';

    /**
     * @var string HTML for the content
     */
    public $content = '';

    /**
     * @var array An alternative to $content, it you want a list of things with optional icons.
     */
    public $footer = '';

    /**
     * @var string Any small print that should appear under the block to explain
     * to the teacher about the block, for example 'This is a sticky block that was
     * added in the system context.'
     */
    public $annotation = '';

    /**
     * @var int One of the constants NOT_HIDEABLE, VISIBLE, HIDDEN. Whether
     * the user can toggle whether this block is visible.
     */
    public $collapsible = self::NOT_HIDEABLE;

    /**
     * Set this to true if the block is dockable.
     * @var bool
     */
    public $dockable = false;

    /**
     * @var array A (possibly empty) array of editing controls. Each element of
     * this array should be an array('url' => $url, 'icon' => $icon, 'caption' => $caption).
     * $icon is the icon name. Fed to $OUTPUT->image_url.
     */
    public $controls = array();


    /**
     * Create new instance of block content
     * @param array $attributes
     */
    public function __construct(array $attributes = null) {
        $this->skipid = self::$idcounter;
        self::$idcounter += 1;

        if ($attributes) {
            // standard block
            $this->attributes = $attributes;
        } else {
            // simple "fake" blocks used in some modules and "Add new block" block
            $this->attributes = array('class'=>'block');
        }
    }

    /**
     * Add html class to block
     *
     * @param string $class
     */
    public function add_class($class) {
        $this->attributes['class'] .= ' '.$class;
    }

    /**
     * Check if the block is a fake block.
     *
     * @return boolean
     */
    public function is_fake() {
        return isset($this->attributes['data-block']) && $this->attributes['data-block'] == '_fake';
    }
}


/**
 * This class represents a target for where a block can go when it is being moved.
 *
 * This needs to be rendered as a form with the given hidden from fields, and
 * clicking anywhere in the form should submit it. The form action should be
 * $PAGE->url.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class block_move_target {

    /**
     * @var moodle_url Move url
     */
    public $url;

    /**
     * Constructor
     * @param moodle_url $url
     */
    public function __construct(moodle_url $url) {
        $this->url  = $url;
    }
}

/**
 * Custom menu item
 *
 * This class is used to represent one item within a custom menu that may or may
 * not have children.
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class custom_menu_item implements renderable, templatable {

    /**
     * @var string The text to show for the item
     */
    protected $text;

    /**
     * @var moodle_url The link to give the icon if it has no children
     */
    protected $url;

    /**
     * @var string A title to apply to the item. By default the text
     */
    protected $title;

    /**
     * @var int A sort order for the item, not necessary if you order things in
     * the CFG var.
     */
    protected $sort;

    /**
     * @var custom_menu_item A reference to the parent for this item or NULL if
     * it is a top level item
     */
    protected $parent;

    /**
     * @var array A array in which to store children this item has.
     */
    protected $children = array();

    /**
     * @var int A reference to the sort var of the last child that was added
     */
    protected $lastsort = 0;

    /**
     * Constructs the new custom menu item
     *
     * @param string $text
     * @param moodle_url $url A moodle url to apply as the link for this item [Optional]
     * @param string $title A title to apply to this item [Optional]
     * @param int $sort A sort or to use if we need to sort differently [Optional]
     * @param custom_menu_item $parent A reference to the parent custom_menu_item this child
     *        belongs to, only if the child has a parent. [Optional]
     */
    public function __construct($text, moodle_url $url=null, $title=null, $sort = null, custom_menu_item $parent = null) {
        $this->text = $text;
        $this->url = $url;
        $this->title = $title;
        $this->sort = (int)$sort;
        $this->parent = $parent;
    }

    /**
     * Adds a custom menu item as a child of this node given its properties.
     *
     * @param string $text
     * @param moodle_url $url
     * @param string $title
     * @param int $sort
     * @return custom_menu_item
     */
    public function add($text, moodle_url $url = null, $title = null, $sort = null) {
        $key = count($this->children);
        if (empty($sort)) {
            $sort = $this->lastsort + 1;
        }
        $this->children[$key] = new custom_menu_item($text, $url, $title, $sort, $this);
        $this->lastsort = (int)$sort;
        return $this->children[$key];
    }

    /**
     * Removes a custom menu item that is a child or descendant to the current menu.
     *
     * Returns true if child was found and removed.
     *
     * @param custom_menu_item $menuitem
     * @return bool
     */
    public function remove_child(custom_menu_item $menuitem) {
        $removed = false;
        if (($key = array_search($menuitem, $this->children)) !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
            $removed = true;
        } else {
            foreach ($this->children as $child) {
                if ($removed = $child->remove_child($menuitem)) {
                    break;
                }
            }
        }
        return $removed;
    }

    /**
     * Returns the text for this item
     * @return string
     */
    public function get_text() {
        return $this->text;
    }

    /**
     * Returns the url for this item
     * @return moodle_url
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Returns the title for this item
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Sorts and returns the children for this item
     * @return array
     */
    public function get_children() {
        $this->sort();
        return $this->children;
    }

    /**
     * Gets the sort order for this child
     * @return int
     */
    public function get_sort_order() {
        return $this->sort;
    }

    /**
     * Gets the parent this child belong to
     * @return custom_menu_item
     */
    public function get_parent() {
        return $this->parent;
    }

    /**
     * Sorts the children this item has
     */
    public function sort() {
        usort($this->children, array('custom_menu','sort_custom_menu_items'));
    }

    /**
     * Returns true if this item has any children
     * @return bool
     */
    public function has_children() {
        return (count($this->children) > 0);
    }

    /**
     * Sets the text for the node
     * @param string $text
     */
    public function set_text($text) {
        $this->text = (string)$text;
    }

    /**
     * Sets the title for the node
     * @param string $title
     */
    public function set_title($title) {
        $this->title = (string)$title;
    }

    /**
     * Sets the url for the node
     * @param moodle_url $url
     */
    public function set_url(moodle_url $url) {
        $this->url = $url;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        require_once($CFG->libdir . '/externallib.php');

        $syscontext = context_system::instance();

        $context = new stdClass();
        $context->text = external_format_string($this->text, $syscontext->id);
        $context->url = $this->url ? $this->url->out() : null;
        $context->title = external_format_string($this->title, $syscontext->id);
        $context->sort = $this->sort;
        $context->children = array();
        if (preg_match("/^#+$/", $this->text)) {
            $context->divider = true;
        }
        $context->haschildren = !empty($this->children) && (count($this->children) > 0);
        foreach ($this->children as $child) {
            $child = $child->export_for_template($output);
            array_push($context->children, $child);
        }

        return $context;
    }
}

/**
 * Custom menu class
 *
 * This class is used to operate a custom menu that can be rendered for the page.
 * The custom menu is built using $CFG->custommenuitems and is a structured collection
 * of custom_menu_item nodes that can be rendered by the core renderer.
 *
 * To configure the custom menu:
 *     Settings: Administration > Appearance > Themes > Theme settings
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class custom_menu extends custom_menu_item {

    /**
     * @var string The language we should render for, null disables multilang support.
     */
    protected $currentlanguage = null;

    /**
     * Creates the custom menu
     *
     * @param string $definition the menu items definition in syntax required by {@link convert_text_to_menu_nodes()}
     * @param string $currentlanguage the current language code, null disables multilang support
     */
    public function __construct($definition = '', $currentlanguage = null) {
        $this->currentlanguage = $currentlanguage;
        parent::__construct('root'); // create virtual root element of the menu
        if (!empty($definition)) {
            $this->override_children(self::convert_text_to_menu_nodes($definition, $currentlanguage));
        }
    }

    /**
     * Overrides the children of this custom menu. Useful when getting children
     * from $CFG->custommenuitems
     *
     * @param array $children
     */
    public function override_children(array $children) {
        $this->children = array();
        foreach ($children as $child) {
            if ($child instanceof custom_menu_item) {
                $this->children[] = $child;
            }
        }
    }

    /**
     * Converts a string into a structured array of custom_menu_items which can
     * then be added to a custom menu.
     *
     * Structure:
     *     text|url|title|langs
     * The number of hyphens at the start determines the depth of the item. The
     * languages are optional, comma separated list of languages the line is for.
     *
     * Example structure:
     *     First level first item|http://www.moodle.com/
     *     -Second level first item|http://www.moodle.com/partners/
     *     -Second level second item|http://www.moodle.com/hq/
     *     --Third level first item|http://www.moodle.com/jobs/
     *     -Second level third item|http://www.moodle.com/development/
     *     First level second item|http://www.moodle.com/feedback/
     *     First level third item
     *     English only|http://moodle.com|English only item|en
     *     German only|http://moodle.de|Deutsch|de,de_du,de_kids
     *
     *
     * @static
     * @param string $text the menu items definition
     * @param string $language the language code, null disables multilang support
     * @return array
     */
    public static function convert_text_to_menu_nodes($text, $language = null) {
        $root = new custom_menu();
        $lastitem = $root;
        $lastdepth = 0;
        $hiddenitems = array();
        $lines = explode("\n", $text);
        foreach ($lines as $linenumber => $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            // Parse item settings.
            $itemtext = null;
            $itemurl = null;
            $itemtitle = null;
            $itemvisible = true;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if (!empty($setting)) {
                    switch ($i) {
                        case 0: // Menu text.
                            $itemtext = ltrim($setting, '-');
                            break;
                        case 1: // URL.
                            try {
                                $itemurl = new moodle_url($setting);
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL.
                                $itemurl = null;
                            }
                            break;
                        case 2: // Title attribute.
                            $itemtitle = $setting;
                            break;
                        case 3: // Language.
                            if (!empty($language)) {
                                $itemlanguages = array_map('trim', explode(',', $setting));
                                $itemvisible &= in_array($language, $itemlanguages);
                            }
                            break;
                    }
                }
            }
            // Get depth of new item.
            preg_match('/^(\-*)/', $line, $match);
            $itemdepth = strlen($match[1]) + 1;
            // Find parent item for new item.
            while (($lastdepth - $itemdepth) >= 0) {
                $lastitem = $lastitem->get_parent();
                $lastdepth--;
            }
            $lastitem = $lastitem->add($itemtext, $itemurl, $itemtitle, $linenumber + 1);
            $lastdepth++;
            if (!$itemvisible) {
                $hiddenitems[] = $lastitem;
            }
        }
        foreach ($hiddenitems as $item) {
            $item->parent->remove_child($item);
        }
        return $root->get_children();
    }

    /**
     * Sorts two custom menu items
     *
     * This function is designed to be used with the usort method
     *     usort($this->children, array('custom_menu','sort_custom_menu_items'));
     *
     * @static
     * @param custom_menu_item $itema
     * @param custom_menu_item $itemb
     * @return int
     */
    public static function sort_custom_menu_items(custom_menu_item $itema, custom_menu_item $itemb) {
        $itema = $itema->get_sort_order();
        $itemb = $itemb->get_sort_order();
        if ($itema == $itemb) {
            return 0;
        }
        return ($itema > $itemb) ? +1 : -1;
    }
}

/**
 * Stores one tab
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class tabobject implements renderable, templatable {
    /** @var string unique id of the tab in this tree, it is used to find selected and/or inactive tabs */
    var $id;
    /** @var moodle_url|string link */
    var $link;
    /** @var string text on the tab */
    var $text;
    /** @var string title under the link, by defaul equals to text */
    var $title;
    /** @var bool whether to display a link under the tab name when it's selected */
    var $linkedwhenselected = false;
    /** @var bool whether the tab is inactive */
    var $inactive = false;
    /** @var bool indicates that this tab's child is selected */
    var $activated = false;
    /** @var bool indicates that this tab is selected */
    var $selected = false;
    /** @var array stores children tabobjects */
    var $subtree = array();
    /** @var int level of tab in the tree, 0 for root (instance of tabtree), 1 for the first row of tabs */
    var $level = 1;

    /**
     * Constructor
     *
     * @param string $id unique id of the tab in this tree, it is used to find selected and/or inactive tabs
     * @param string|moodle_url $link
     * @param string $text text on the tab
     * @param string $title title under the link, by defaul equals to text
     * @param bool $linkedwhenselected whether to display a link under the tab name when it's selected
     */
    public function __construct($id, $link = null, $text = '', $title = '', $linkedwhenselected = false) {
        $this->id = $id;
        $this->link = $link;
        $this->text = $text;
        $this->title = $title ? $title : $text;
        $this->linkedwhenselected = $linkedwhenselected;
    }

    /**
     * Travels through tree and finds the tab to mark as selected, all parents are automatically marked as activated
     *
     * @param string $selected the id of the selected tab (whatever row it's on),
     *    if null marks all tabs as unselected
     * @return bool whether this tab is selected or contains selected tab in its subtree
     */
    protected function set_selected($selected) {
        if ((string)$selected === (string)$this->id) {
            $this->selected = true;
            // This tab is selected. No need to travel through subtree.
            return true;
        }
        foreach ($this->subtree as $subitem) {
            if ($subitem->set_selected($selected)) {
                // This tab has child that is selected. Mark it as activated. No need to check other children.
                $this->activated = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Travels through tree and finds a tab with specified id
     *
     * @param string $id
     * @return tabtree|null
     */
    public function find($id) {
        if ((string)$this->id === (string)$id) {
            return $this;
        }
        foreach ($this->subtree as $tab) {
            if ($obj = $tab->find($id)) {
                return $obj;
            }
        }
        return null;
    }

    /**
     * Allows to mark each tab's level in the tree before rendering.
     *
     * @param int $level
     */
    protected function set_level($level) {
        $this->level = $level;
        foreach ($this->subtree as $tab) {
            $tab->set_level($level + 1);
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return object
     */
    public function export_for_template(renderer_base $output) {
        if ($this->inactive || ($this->selected && !$this->linkedwhenselected) || $this->activated) {
            $link = null;
        } else {
            $link = $this->link;
        }
        $active = $this->activated || $this->selected;

        return (object) [
            'id' => $this->id,
            'link' => is_object($link) ? $link->out(false) : $link,
            'text' => $this->text,
            'title' => $this->title,
            'inactive' => !$active && $this->inactive,
            'active' => $active,
            'level' => $this->level,
        ];
    }

}

/**
 * Renderable for the main page header.
 *
 * @package core
 * @category output
 * @since 2.9
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_header implements renderable {

    /**
     * @var string $heading Main heading.
     */
    public $heading;
    /**
     * @var int $headinglevel Main heading 'h' tag level.
     */
    public $headinglevel;
    /**
     * @var string|null $imagedata HTML code for the picture in the page header.
     */
    public $imagedata;
    /**
     * @var array $additionalbuttons Additional buttons for the header e.g. Messaging button for the user header.
     *      array elements - title => alternate text for the image, or if no image is available the button text.
     *                       url => Link for the button to head to. Should be a moodle_url.
     *                       image => location to the image, or name of the image in /pix/t/{image name}.
     *                       linkattributes => additional attributes for the <a href> element.
     *                       page => page object. Don't include if the image is an external image.
     */
    public $additionalbuttons;

    /**
     * Constructor.
     *
     * @param string $heading Main heading data.
     * @param int $headinglevel Main heading 'h' tag level.
     * @param string|null $imagedata HTML code for the picture in the page header.
     * @param string $additionalbuttons Buttons for the header e.g. Messaging button for the user header.
     */
    public function __construct($heading = null, $headinglevel = 1, $imagedata = null, $additionalbuttons = null) {

        $this->heading = $heading;
        $this->headinglevel = $headinglevel;
        $this->imagedata = $imagedata;
        $this->additionalbuttons = $additionalbuttons;
        // If we have buttons then format them.
        if (isset($this->additionalbuttons)) {
            $this->format_button_images();
        }
    }

    /**
     * Adds an array element for a formatted image.
     */
    protected function format_button_images() {

        foreach ($this->additionalbuttons as $buttontype => $button) {
            $page = $button['page'];
            // If no image is provided then just use the title.
            if (!isset($button['image'])) {
                $this->additionalbuttons[$buttontype]['formattedimage'] = $button['title'];
            } else {
                // Check to see if this is an internal Moodle icon.
                $internalimage = $page->theme->resolve_image_location('t/' . $button['image'], 'moodle');
                if ($internalimage) {
                    $this->additionalbuttons[$buttontype]['formattedimage'] = 't/' . $button['image'];
                } else {
                    // Treat as an external image.
                    $this->additionalbuttons[$buttontype]['formattedimage'] = $button['image'];
                }
            }

            if (isset($button['linkattributes']['class'])) {
                $class = $button['linkattributes']['class'] . ' btn';
            } else {
                $class = 'btn';
            }
            // Add the bootstrap 'btn' class for formatting.
            $this->additionalbuttons[$buttontype]['linkattributes'] = array_merge($button['linkattributes'],
                    array('class' => $class));
        }
    }
}

/**
 * Stores tabs list
 *
 * Example how to print a single line tabs:
 * $rows = array(
 *    new tabobject(...),
 *    new tabobject(...)
 * );
 * echo $OUTPUT->tabtree($rows, $selectedid);
 *
 * Multiple row tabs may not look good on some devices but if you want to use them
 * you can specify ->subtree for the active tabobject.
 *
 * @copyright 2013 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 * @package core
 * @category output
 */
class tabtree extends tabobject {
    /**
     * Constuctor
     *
     * It is highly recommended to call constructor when list of tabs is already
     * populated, this way you ensure that selected and inactive tabs are located
     * and attribute level is set correctly.
     *
     * @param array $tabs array of tabs, each of them may have it's own ->subtree
     * @param string|null $selected which tab to mark as selected, all parent tabs will
     *     automatically be marked as activated
     * @param array|string|null $inactive list of ids of inactive tabs, regardless of
     *     their level. Note that you can as weel specify tabobject::$inactive for separate instances
     */
    public function __construct($tabs, $selected = null, $inactive = null) {
        $this->subtree = $tabs;
        if ($selected !== null) {
            $this->set_selected($selected);
        }
        if ($inactive !== null) {
            if (is_array($inactive)) {
                foreach ($inactive as $id) {
                    if ($tab = $this->find($id)) {
                        $tab->inactive = true;
                    }
                }
            } else if ($tab = $this->find($inactive)) {
                $tab->inactive = true;
            }
        }
        $this->set_level(0);
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return object
     */
    public function export_for_template(renderer_base $output) {
        $tabs = [];
        $secondrow = false;

        foreach ($this->subtree as $tab) {
            $tabs[] = $tab->export_for_template($output);
            if (!empty($tab->subtree) && ($tab->level == 0 || $tab->selected || $tab->activated)) {
                $secondrow = new tabtree($tab->subtree);
            }
        }

        return (object) [
            'tabs' => $tabs,
            'secondrow' => $secondrow ? $secondrow->export_for_template($output) : false
        ];
    }
}

/**
 * An action menu.
 *
 * This action menu component takes a series of primary and secondary actions.
 * The primary actions are displayed permanently and the secondary attributes are displayed within a drop
 * down menu.
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu implements renderable, templatable {

    /**
     * Top right alignment.
     */
    const TL = 1;

    /**
     * Top right alignment.
     */
    const TR = 2;

    /**
     * Top right alignment.
     */
    const BL = 3;

    /**
     * Top right alignment.
     */
    const BR = 4;

    /**
     * The instance number. This is unique to this instance of the action menu.
     * @var int
     */
    protected $instance = 0;

    /**
     * An array of primary actions. Please use {@link action_menu::add_primary_action()} to add actions.
     * @var array
     */
    protected $primaryactions = array();

    /**
     * An array of secondary actions. Please use {@link action_menu::add_secondary_action()} to add actions.
     * @var array
     */
    protected $secondaryactions = array();

    /**
     * An array of attributes added to the container of the action menu.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributes = array();
    /**
     * An array of attributes added to the container of the primary actions.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributesprimary = array();
    /**
     * An array of attributes added to the container of the secondary actions.
     * Initialised with defaults during construction.
     * @var array
     */
    public $attributessecondary = array();

    /**
     * The string to use next to the icon for the action icon relating to the secondary (dropdown) menu.
     * @var array
     */
    public $actiontext = null;

    /**
     * The string to use for the accessible label for the menu.
     * @var array
     */
    public $actionlabel = null;

    /**
     * An icon to use for the toggling the secondary menu (dropdown).
     * @var pix_icon
     */
    public $actionicon;

    /**
     * Any text to use for the toggling the secondary menu (dropdown).
     * @var string
     */
    public $menutrigger = '';

    /**
     * Any extra classes for toggling to the secondary menu.
     * @var string
     */
    public $triggerextraclasses = '';

    /**
     * Place the action menu before all other actions.
     * @var bool
     */
    public $prioritise = false;

    /**
     * Constructs the action menu with the given items.
     *
     * @param array $actions An array of actions (action_menu_link|pix_icon|string).
     */
    public function __construct(array $actions = array()) {
        static $initialised = 0;
        $this->instance = $initialised;
        $initialised++;

        $this->attributes = array(
            'id' => 'action-menu-'.$this->instance,
            'class' => 'moodle-actionmenu',
            'data-enhance' => 'moodle-core-actionmenu'
        );
        $this->attributesprimary = array(
            'id' => 'action-menu-'.$this->instance.'-menubar',
            'class' => 'menubar',
            'role' => 'menubar'
        );
        $this->attributessecondary = array(
            'id' => 'action-menu-'.$this->instance.'-menu',
            'class' => 'menu',
            'data-rel' => 'menu-content',
            'aria-labelledby' => 'action-menu-toggle-'.$this->instance,
            'role' => 'menu'
        );
        $this->set_alignment(self::TR, self::BR);
        foreach ($actions as $action) {
            $this->add($action);
        }
    }

    /**
     * Sets the label for the menu trigger.
     *
     * @param string $label The text
     */
    public function set_action_label($label) {
        $this->actionlabel = $label;
    }

    /**
     * Sets the menu trigger text.
     *
     * @param string $trigger The text
     * @param string $extraclasses Extra classes to style the secondary menu toggle.
     */
    public function set_menu_trigger($trigger, $extraclasses = '') {
        $this->menutrigger = $trigger;
        $this->triggerextraclasses = $extraclasses;
    }

    /**
     * Return true if there is at least one visible link in the menu.
     *
     * @return bool
     */
    public function is_empty() {
        return !count($this->primaryactions) && !count($this->secondaryactions);
    }

    /**
     * Initialises JS required fore the action menu.
     * The JS is only required once as it manages all action menu's on the page.
     *
     * @param moodle_page $page
     */
    public function initialise_js(moodle_page $page) {
        static $initialised = false;
        if (!$initialised) {
            $page->requires->yui_module('moodle-core-actionmenu', 'M.core.actionmenu.init');
            $initialised = true;
        }
    }

    /**
     * Adds an action to this action menu.
     *
     * @param action_menu_link|pix_icon|string $action
     */
    public function add($action) {
        if ($action instanceof action_link) {
            if ($action->primary) {
                $this->add_primary_action($action);
            } else {
                $this->add_secondary_action($action);
            }
        } else if ($action instanceof pix_icon) {
            $this->add_primary_action($action);
        } else {
            $this->add_secondary_action($action);
        }
    }

    /**
     * Adds a primary action to the action menu.
     *
     * @param action_menu_link|action_link|pix_icon|string $action
     */
    public function add_primary_action($action) {
        if ($action instanceof action_link || $action instanceof pix_icon) {
            $action->attributes['role'] = 'menuitem';
            if ($action instanceof action_menu_link) {
                $action->actionmenu = $this;
            }
        }
        $this->primaryactions[] = $action;
    }

    /**
     * Adds a secondary action to the action menu.
     *
     * @param action_link|pix_icon|string $action
     */
    public function add_secondary_action($action) {
        if ($action instanceof action_link || $action instanceof pix_icon) {
            $action->attributes['role'] = 'menuitem';
            if ($action instanceof action_menu_link) {
                $action->actionmenu = $this;
            }
        }
        $this->secondaryactions[] = $action;
    }

    /**
     * Returns the primary actions ready to be rendered.
     *
     * @param core_renderer $output The renderer to use for getting icons.
     * @return array
     */
    public function get_primary_actions(core_renderer $output = null) {
        global $OUTPUT;
        if ($output === null) {
            $output = $OUTPUT;
        }
        $pixicon = $this->actionicon;
        $linkclasses = array('toggle-display');

        $title = '';
        if (!empty($this->menutrigger)) {
            $pixicon = '<b class="caret"></b>';
            $linkclasses[] = 'textmenu';
        } else {
            $title = new lang_string('actionsmenu', 'moodle');
            $this->actionicon = new pix_icon(
                't/edit_menu',
                '',
                'moodle',
                array('class' => 'iconsmall actionmenu', 'title' => '')
            );
            $pixicon = $this->actionicon;
        }
        if ($pixicon instanceof renderable) {
            $pixicon = $output->render($pixicon);
            if ($pixicon instanceof pix_icon && isset($pixicon->attributes['alt'])) {
                $title = $pixicon->attributes['alt'];
            }
        }
        $string = '';
        if ($this->actiontext) {
            $string = $this->actiontext;
        }
        $label = '';
        if ($this->actionlabel) {
            $label = $this->actionlabel;
        } else {
            $label = $title;
        }
        $actions = $this->primaryactions;
        $attributes = array(
            'class' => implode(' ', $linkclasses),
            'title' => $title,
            'aria-label' => $label,
            'id' => 'action-menu-toggle-'.$this->instance,
            'role' => 'menuitem'
        );
        $link = html_writer::link('#', $string . $this->menutrigger . $pixicon, $attributes);
        if ($this->prioritise) {
            array_unshift($actions, $link);
        } else {
            $actions[] = $link;
        }
        return $actions;
    }

    /**
     * Returns the secondary actions ready to be rendered.
     * @return array
     */
    public function get_secondary_actions() {
        return $this->secondaryactions;
    }

    /**
     * Sets the selector that should be used to find the owning node of this menu.
     * @param string $selector A CSS/YUI selector to identify the owner of the menu.
     */
    public function set_owner_selector($selector) {
        $this->attributes['data-owner'] = $selector;
    }

    /**
     * Sets the alignment of the dialogue in relation to button used to toggle it.
     *
     * @param int $dialogue One of action_menu::TL, action_menu::TR, action_menu::BL, action_menu::BR.
     * @param int $button One of action_menu::TL, action_menu::TR, action_menu::BL, action_menu::BR.
     */
    public function set_alignment($dialogue, $button) {
        if (isset($this->attributessecondary['data-align'])) {
            // We've already got one set, lets remove the old class so as to avoid troubles.
            $class = $this->attributessecondary['class'];
            $search = 'align-'.$this->attributessecondary['data-align'];
            $this->attributessecondary['class'] = str_replace($search, '', $class);
        }
        $align = $this->get_align_string($dialogue) . '-' . $this->get_align_string($button);
        $this->attributessecondary['data-align'] = $align;
        $this->attributessecondary['class'] .= ' align-'.$align;
    }

    /**
     * Returns a string to describe the alignment.
     *
     * @param int $align One of action_menu::TL, action_menu::TR, action_menu::BL, action_menu::BR.
     * @return string
     */
    protected function get_align_string($align) {
        switch ($align) {
            case self::TL :
                return 'tl';
            case self::TR :
                return 'tr';
            case self::BL :
                return 'bl';
            case self::BR :
                return 'br';
            default :
                return 'tl';
        }
    }

    /**
     * Sets a constraint for the dialogue.
     *
     * The constraint is applied when the dialogue is shown and limits the display of the dialogue to within the
     * element the constraint identifies.
     *
     * This is required whenever the action menu is displayed inside any CSS element with the .no-overflow class
     * (flexible_table and any of it's child classes are a likely candidate).
     *
     * @param string $ancestorselector A snippet of CSS used to identify the ancestor to contrain the dialogue to.
     */
    public function set_constraint($ancestorselector) {
        $this->attributessecondary['data-constraint'] = $ancestorselector;
    }

    /**
     * If you call this method the action menu will be displayed but will not be enhanced.
     *
     * By not displaying the menu enhanced all items will be displayed in a single row.
     *
     * @deprecated since Moodle 3.2
     */
    public function do_not_enhance() {
        debugging('The method action_menu::do_not_enhance() is deprecated, use a list of action_icon instead.', DEBUG_DEVELOPER);
    }

    /**
     * Returns true if this action menu will be enhanced.
     *
     * @return bool
     */
    public function will_be_enhanced() {
        return isset($this->attributes['data-enhance']);
    }

    /**
     * Sets nowrap on items. If true menu items should not wrap lines if they are longer than the available space.
     *
     * This property can be useful when the action menu is displayed within a parent element that is either floated
     * or relatively positioned.
     * In that situation the width of the menu is determined by the width of the parent element which may not be large
     * enough for the menu items without them wrapping.
     * This disables the wrapping so that the menu takes on the width of the longest item.
     *
     * @param bool $value If true nowrap gets set, if false it gets removed. Defaults to true.
     */
    public function set_nowrap_on_items($value = true) {
        $class = 'nowrap-items';
        if (!empty($this->attributes['class'])) {
            $pos = strpos($this->attributes['class'], $class);
            if ($value === true && $pos === false) {
                // The value is true and the class has not been set yet. Add it.
                $this->attributes['class'] .= ' '.$class;
            } else if ($value === false && $pos !== false) {
                // The value is false and the class has been set. Remove it.
                $this->attributes['class'] = substr($this->attributes['class'], $pos, strlen($class));
            }
        } else if ($value) {
            // The value is true and the class has not been set yet. Add it.
            $this->attributes['class'] = $class;
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $attributes = $this->attributes;
        $attributesprimary = $this->attributesprimary;
        $attributessecondary = $this->attributessecondary;

        $data->instance = $this->instance;

        $data->classes = isset($attributes['class']) ? $attributes['class'] : '';
        unset($attributes['class']);

        $data->attributes = array_map(function($key, $value) {
            return [ 'name' => $key, 'value' => $value ];
        }, array_keys($attributes), $attributes);

        $primary = new stdClass();
        $primary->title = '';
        $primary->prioritise = $this->prioritise;

        $primary->classes = isset($attributesprimary['class']) ? $attributesprimary['class'] : '';
        unset($attributesprimary['class']);
        $primary->attributes = array_map(function($key, $value) {
            return [ 'name' => $key, 'value' => $value ];
        }, array_keys($attributesprimary), $attributesprimary);

        $actionicon = $this->actionicon;
        if (!empty($this->menutrigger)) {
            $primary->menutrigger = $this->menutrigger;
            $primary->triggerextraclasses = $this->triggerextraclasses;
            if ($this->actionlabel) {
                $primary->title = $this->actionlabel;
            } else if ($this->actiontext) {
                $primary->title = $this->actiontext;
            } else {
                $primary->title = strip_tags($this->menutrigger);
            }
        } else {
            $primary->title = get_string('actionsmenu');
            $iconattributes = ['class' => 'iconsmall actionmenu', 'title' => $primary->title];
            $actionicon = new pix_icon('t/edit_menu', '', 'moodle', $iconattributes);
        }

        if ($actionicon instanceof pix_icon) {
            $primary->icon = $actionicon->export_for_pix();
            if (!empty($actionicon->attributes['alt'])) {
                $primary->title = $actionicon->attributes['alt'];
            }
        } else {
            $primary->iconraw = $actionicon ? $output->render($actionicon) : '';
        }

        $primary->actiontext = $this->actiontext ? (string) $this->actiontext : '';
        $primary->items = array_map(function($item) use ($output) {
            $data = (object) [];
            if ($item instanceof action_menu_link) {
                $data->actionmenulink = $item->export_for_template($output);
            } else if ($item instanceof action_menu_filler) {
                $data->actionmenufiller = $item->export_for_template($output);
            } else if ($item instanceof action_link) {
                $data->actionlink = $item->export_for_template($output);
            } else if ($item instanceof pix_icon) {
                $data->pixicon = $item->export_for_template($output);
            } else {
                $data->rawhtml = ($item instanceof renderable) ? $output->render($item) : $item;
            }
            return $data;
        }, $this->primaryactions);

        $secondary = new stdClass();
        $secondary->classes = isset($attributessecondary['class']) ? $attributessecondary['class'] : '';
        unset($attributessecondary['class']);
        $secondary->attributes = array_map(function($key, $value) {
            return [ 'name' => $key, 'value' => $value ];
        }, array_keys($attributessecondary), $attributessecondary);
        $secondary->items = array_map(function($item) use ($output) {
            $data = (object) [];
            if ($item instanceof action_menu_link) {
                $data->actionmenulink = $item->export_for_template($output);
            } else if ($item instanceof action_menu_filler) {
                $data->actionmenufiller = $item->export_for_template($output);
            } else if ($item instanceof action_link) {
                $data->actionlink = $item->export_for_template($output);
            } else if ($item instanceof pix_icon) {
                $data->pixicon = $item->export_for_template($output);
            } else {
                $data->rawhtml = ($item instanceof renderable) ? $output->render($item) : $item;
            }
            return $data;
        }, $this->secondaryactions);

        $data->primary = $primary;
        $data->secondary = $secondary;

        return $data;
    }

}

/**
 * An action menu filler
 *
 * @package core
 * @category output
 * @copyright 2013 Andrew Nicols
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu_filler extends action_link implements renderable {

    /**
     * True if this is a primary action. False if not.
     * @var bool
     */
    public $primary = true;

    /**
     * Constructs the object.
     */
    public function __construct() {
    }
}

/**
 * An action menu action
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu_link extends action_link implements renderable {

    /**
     * True if this is a primary action. False if not.
     * @var bool
     */
    public $primary = true;

    /**
     * The action menu this link has been added to.
     * @var action_menu
     */
    public $actionmenu = null;

    /**
     * The number of instances of this action menu link (and its subclasses).
     * @var int
     */
    protected static $instance = 1;

    /**
     * Constructs the object.
     *
     * @param moodle_url $url The URL for the action.
     * @param pix_icon $icon The icon to represent the action.
     * @param string $text The text to represent the action.
     * @param bool $primary Whether this is a primary action or not.
     * @param array $attributes Any attribtues associated with the action.
     */
    public function __construct(moodle_url $url, pix_icon $icon = null, $text, $primary = true, array $attributes = array()) {
        parent::__construct($url, $text, null, $attributes, $icon);
        $this->primary = (bool)$primary;
        $this->add_class('menu-action');
        $this->attributes['role'] = 'menuitem';
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);
        $data->instance = self::$instance++;

        // Ignore what the parent did with the attributes, except for ID and class.
        $data->attributes = [];
        $attributes = $this->attributes;
        unset($attributes['id']);
        unset($attributes['class']);

        // Handle text being a renderable.
        if ($this->text instanceof renderable) {
            $data->text = $this->render($this->text);
        }

        $data->showtext = (!$this->icon || $this->primary === false);

        $data->icon = null;
        if ($this->icon) {
            $icon = $this->icon;
            if ($this->primary || !$this->actionmenu->will_be_enhanced()) {
                $attributes['title'] = $data->text;
            }
            $data->icon = $icon ? $icon->export_for_pix() : null;
        }

        $data->disabled = !empty($attributes['disabled']);
        unset($attributes['disabled']);

        $data->attributes = array_map(function($key, $value) {
            return [
                'name' => $key,
                'value' => $value
            ];
        }, array_keys($attributes), $attributes);

        return $data;
    }
}

/**
 * A primary action menu action
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu_link_primary extends action_menu_link {
    /**
     * Constructs the object.
     *
     * @param moodle_url $url
     * @param pix_icon $icon
     * @param string $text
     * @param array $attributes
     */
    public function __construct(moodle_url $url, pix_icon $icon = null, $text, array $attributes = array()) {
        parent::__construct($url, $icon, $text, true, $attributes);
    }
}

/**
 * A secondary action menu action
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_menu_link_secondary extends action_menu_link {
    /**
     * Constructs the object.
     *
     * @param moodle_url $url
     * @param pix_icon $icon
     * @param string $text
     * @param array $attributes
     */
    public function __construct(moodle_url $url, pix_icon $icon = null, $text, array $attributes = array()) {
        parent::__construct($url, $icon, $text, false, $attributes);
    }
}

/**
 * Represents a set of preferences groups.
 *
 * @package core
 * @category output
 * @copyright 2015 Frédéric Massart - FMCorz.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preferences_groups implements renderable {

    /**
     * Array of preferences_group.
     * @var array
     */
    public $groups;

    /**
     * Constructor.
     * @param array $groups of preferences_group
     */
    public function __construct($groups) {
        $this->groups = $groups;
    }

}

/**
 * Represents a group of preferences page link.
 *
 * @package core
 * @category output
 * @copyright 2015 Frédéric Massart - FMCorz.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preferences_group implements renderable {

    /**
     * Title of the group.
     * @var string
     */
    public $title;

    /**
     * Array of navigation_node.
     * @var array
     */
    public $nodes;

    /**
     * Constructor.
     * @param string $title The title.
     * @param array $nodes of navigation_node.
     */
    public function __construct($title, $nodes) {
        $this->title = $title;
        $this->nodes = $nodes;
    }
}

/**
 * Progress bar class.
 *
 * Manages the display of a progress bar.
 *
 * To use this class.
 * - construct
 * - call create (or use the 3rd param to the constructor)
 * - call update or update_full() or update() repeatedly
 *
 * @copyright 2008 jamiesensei
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */
class progress_bar implements renderable, templatable {
    /** @var string html id */
    private $html_id;
    /** @var int total width */
    private $width;
    /** @var int last percentage printed */
    private $percent = 0;
    /** @var int time when last printed */
    private $lastupdate = 0;
    /** @var int when did we start printing this */
    private $time_start = 0;

    /**
     * Constructor
     *
     * Prints JS code if $autostart true.
     *
     * @param string $htmlid The container ID.
     * @param int $width The suggested width.
     * @param bool $autostart Whether to start the progress bar right away.
     */
    public function __construct($htmlid = '', $width = 500, $autostart = false) {
        if (!CLI_SCRIPT && !NO_OUTPUT_BUFFERING) {
            debugging('progress_bar used in a non-CLI script without setting NO_OUTPUT_BUFFERING.', DEBUG_DEVELOPER);
        }

        if (!empty($htmlid)) {
            $this->html_id  = $htmlid;
        } else {
            $this->html_id  = 'pbar_'.uniqid();
        }

        $this->width = $width;

        if ($autostart) {
            $this->create();
        }
    }

    /**
     * Create a new progress bar, this function will output html.
     *
     * @return void Echo's output
     */
    public function create() {
        global $OUTPUT;

        $this->time_start = microtime(true);
        if (CLI_SCRIPT) {
            return; // Temporary solution for cli scripts.
        }

        flush();
        echo $OUTPUT->render($this);
        flush();
    }

    /**
     * Update the progress bar.
     *
     * @param int $percent From 1-100.
     * @param string $msg The message.
     * @return void Echo's output
     * @throws coding_exception
     */
    private function _update($percent, $msg) {
        if (empty($this->time_start)) {
            throw new coding_exception('You must call create() (or use the $autostart ' .
                    'argument to the constructor) before you try updating the progress bar.');
        }

        if (CLI_SCRIPT) {
            return; // Temporary solution for cli scripts.
        }

        $estimate = $this->estimate($percent);

        if ($estimate === null) {
            // Always do the first and last updates.
        } else if ($estimate == 0) {
            // Always do the last updates.
        } else if ($this->lastupdate + 20 < time()) {
            // We must update otherwise browser would time out.
        } else if (round($this->percent, 2) === round($percent, 2)) {
            // No significant change, no need to update anything.
            return;
        }

        $estimatemsg = null;
        if (is_numeric($estimate)) {
            $estimatemsg = get_string('secondsleft', 'moodle', round($estimate, 2));
        }

        $this->percent = round($percent, 2);
        $this->lastupdate = microtime(true);

        echo html_writer::script(js_writer::function_call('updateProgressBar',
            array($this->html_id, $this->percent, $msg, $estimatemsg)));
        flush();
    }

    /**
     * Estimate how much time it is going to take.
     *
     * @param int $pt From 1-100.
     * @return mixed Null (unknown), or int.
     */
    private function estimate($pt) {
        if ($this->lastupdate == 0) {
            return null;
        }
        if ($pt < 0.00001) {
            return null; // We do not know yet how long it will take.
        }
        if ($pt > 99.99999) {
            return 0; // Nearly done, right?
        }
        $consumed = microtime(true) - $this->time_start;
        if ($consumed < 0.001) {
            return null;
        }

        return (100 - $pt) * ($consumed / $pt);
    }

    /**
     * Update progress bar according percent.
     *
     * @param int $percent From 1-100.
     * @param string $msg The message needed to be shown.
     */
    public function update_full($percent, $msg) {
        $percent = max(min($percent, 100), 0);
        $this->_update($percent, $msg);
    }

    /**
     * Update progress bar according the number of tasks.
     *
     * @param int $cur Current task number.
     * @param int $total Total task number.
     * @param string $msg The message needed to be shown.
     */
    public function update($cur, $total, $msg) {
        $percent = ($cur / $total) * 100;
        $this->update_full($percent, $msg);
    }

    /**
     * Restart the progress bar.
     */
    public function restart() {
        $this->percent    = 0;
        $this->lastupdate = 0;
        $this->time_start = 0;
    }

    /**
     * Export for template.
     *
     * @param  renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        return [
            'id' => $this->html_id,
            'width' => $this->width,
        ];
    }
}
