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
 * @package    core
 * @subpackage lib
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Interface marking other classes as suitable for renderer_base::render()
 * @author 2010 Petr Skoda (skodak) info@skodak.org
 */
interface renderable {
    // intentionally empty
}

/**
 * Data structure representing a file picker.
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class file_picker implements renderable {
    public $options;
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
            $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class user_picture implements renderable {
    /**
     * @var array List of mandatory fields in user record here. (do not include TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     */
    protected static $fields = array('id', 'picture', 'firstname', 'lastname', 'imagealt', 'email');

    /**
     * @var object $user A user object with at least fields all columns specified in $fields array constant set.
     */
    public $user;
    /**
     * @var int $courseid The course id. Used when constructing the link to the user's profile,
     * page course id used if not specified.
     */
    public $courseid;
    /**
     * @var bool $link add course profile link to image
     */
    public $link = true;
    /**
     * @var int $size Size in pixels. Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatibility
     */
    public $size = 35;
    /**
     * @var boolean $alttext add non-blank alt-text to the image.
     * Default true, set to false when image alt just duplicates text in screenreaders.
     */
    public $alttext = true;
    /**
     * @var boolean $popup Whether or not to open the link in a popup window.
     */
    public $popup = false;
    /**
     * @var string Image class attribute
     */
    public $class = 'userpicture';

    /**
     * User picture constructor.
     *
     * @param object $user user record with at least id, picture, imagealt, firstname and lastname set.
     * @param array $options such as link, size, link, ...
     */
    public function __construct(stdClass $user) {
        global $DB;

        if (empty($user->id)) {
            throw new coding_exception('User id is required when printing user avatar image.');
        }

        // only touch the DB if we are missing data and complain loudly...
        $needrec = false;
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $user)) {
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
        $fields = array();
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
    public static function unalias(stdClass $record, array $extrafields=null, $idalias='id', $fieldprefix='') {

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
}

/**
 * Data structure representing a help icon.
 *
 * @copyright 2009 Nicolas Connault, 2010 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class old_help_icon implements renderable {
    /**
     * @var string $helpidentifier lang pack identifier
     */
    public $helpidentifier;
    /**
     * @var string $title A descriptive text for title tooltip
     */
    public $title = null;
    /**
     * @var string $component Component name, the same as in get_string()
     */
    public $component = 'moodle';
    /**
     * @var string $linktext Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * Constructor: sets up the other components in case they are needed
     * @param string $helpidentifier  The keyword that defines a help page
     * @param string $title A descriptive text for accessibility only
     * @param string $component
     * @param bool $linktext add extra text to icon
     * @return void
     */
    public function __construct($helpidentifier, $title, $component = 'moodle') {
        if (empty($title)) {
            throw new coding_exception('A help_icon object requires a $text parameter');
        }
        if (empty($helpidentifier)) {
            throw new coding_exception('A help_icon object requires a $helpidentifier parameter');
        }

        $this->helpidentifier  = $helpidentifier;
        $this->title           = $title;
        $this->component       = $component;
    }
}

/**
 * Data structure representing a help icon.
 *
 * @copyright 2010 Petr Skoda (info@skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class help_icon implements renderable {
    /**
     * @var string $identifier lang pack identifier (without the "_help" suffix),
     *    both get_string($identifier, $component) and get_string($identifier.'_help', $component)
     *    must exist.
     */
    public $identifier;
    /**
     * @var string $component Component name, the same as in get_string()
     */
    public $component;
    /**
     * @var string $linktext Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * Constructor
     * @param string $identifier  string for help page title,
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
}


/**
 * Data structure representing an icon.
 *
 * @copyright 2010 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class pix_icon implements renderable {
    var $pix;
    var $component;
    var $attributes = array();

    /**
     * Constructor
     * @param string $pix short icon name
     * @param string $component component name
     * @param array $attributes html attributes
     */
    public function __construct($pix, $alt, $component='moodle', array $attributes = null) {
        $this->pix        = $pix;
        $this->component  = $component;
        $this->attributes = (array)$attributes;

        $this->attributes['alt'] = $alt;
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = 'smallicon';
        }
        if (!isset($this->attributes['title'])) {
            $this->attributes['title'] = $this->attributes['alt'];
        }
    }
}

/**
 * Data structure representing an emoticon image
 *
 * @since     Moodle 2.0
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class single_button implements renderable {
    /**
     * Target url
     * @var moodle_url
     */
    var $url;
    /**
     * Button label
     * @var string
     */
    var $label;
    /**
     * Form submit method
     * @var string post or get
     */
    var $method = 'post';
    /**
     * Wrapping div class
     * @var string
     * */
    var $class = 'singlebutton';
    /**
     * True if button disabled, false if normal
     * @var boolean
     */
    var $disabled = false;
    /**
     * Button tooltip
     * @var string
     */
    var $tooltip = null;
    /**
     * Form id
     * @var string
     */
    var $formid;
    /**
     * List of attached actions
     * @var array of component_action
     */
    var $actions = array();

    /**
     * Constructor
     * @param string|moodle_url $url
     * @param string $label button text
     * @param string $method get or post submit method
     */
    public function __construct(moodle_url $url, $label, $method='post') {
        $this->url    = clone($url);
        $this->label  = $label;
        $this->method = $method;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the button is clicked.
     * The message must be a yes/no question.
     * @param string $message The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     * @return void
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new component_action('click', 'M.util.show_confirm_dialog', array('message' => $confirmmessage)));
    }

    /**
     * Add action to the button.
     * @param component_action $action
     * @return void
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }
}


/**
 * Simple form with just one select field that gets submitted automatically.
 * If JS not enabled small go button is printed too.
 *
 * @copyright 2009 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class single_select implements renderable {
    /**
     * Target url - includes hidden fields
     * @var moodle_url
     */
    var $url;
    /**
     * Name of the select element.
     * @var string
     */
    var $name;
    /**
     * @var array $options associative array value=>label ex.:
     *              array(1=>'One, 2=>Two)
     *              it is also possible to specify optgroup as complex label array ex.:
     *                array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *                array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    var $options;
    /**
     * Selected option
     * @var string
     */
    var $selected;
    /**
     * Nothing selected
     * @var array
     */
    var $nothing;
    /**
     * Extra select field attributes
     * @var array
     */
    var $attributes = array();
    /**
     * Button label
     * @var string
     */
    var $label = '';
    /**
     * Form submit method
     * @var string post or get
     */
    var $method = 'get';
    /**
     * Wrapping div class
     * @var string
     * */
    var $class = 'singleselect';
    /**
     * True if button disabled, false if normal
     * @var boolean
     */
    var $disabled = false;
    /**
     * Button tooltip
     * @var string
     */
    var $tooltip = null;
    /**
     * Form id
     * @var string
     */
    var $formid = null;
    /**
     * List of attached actions
     * @var array of component_action
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
    public function __construct(moodle_url $url, $name, array $options, $selected='', $nothing=array(''=>'choosedots'), $formid=null) {
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
     * @param string $message The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     * @return void
     */
    public function add_confirm_action($confirmmessage) {
        $this->add_action(new component_action('submit', 'M.util.show_confirm_dialog', array('message' => $confirmmessage)));
    }

    /**
     * Add action to the button.
     * @param component_action $action
     * @return void
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    /**
     * Adds help icon.
     * @param string $page  The keyword that defines a help page
     * @param string $title A descriptive text for accessibility only
     * @param string $component
     * @param bool $linktext add extra text to icon
     * @return void
     */
    public function set_old_help_icon($helppage, $title, $component = 'moodle') {
        $this->helpicon = new old_help_icon($helppage, $title, $component);
    }

    /**
     * Adds help icon.
     * @param string $identifier The keyword that defines a help page
     * @param string $component
     * @param bool $linktext add extra text to icon
     * @return void
     */
    public function set_help_icon($identifier, $component = 'moodle') {
        $this->helpicon = new help_icon($identifier, $component);
    }

    /**
     * Sets select's label
     * @param string $label
     * @return void
     */
    public function set_label($label) {
        $this->label = $label;
    }
}


/**
 * Simple URL selection widget description.
 * @copyright 2009 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class url_select implements renderable {
    /**
     * @var array $urls associative array value=>label ex.:
     *              array(1=>'One, 2=>Two)
     *              it is also possible to specify optgroup as complex label array ex.:
     *                array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *                array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     */
    var $urls;
    /**
     * Selected option
     * @var string
     */
    var $selected;
    /**
     * Nothing selected
     * @var array
     */
    var $nothing;
    /**
     * Extra select field attributes
     * @var array
     */
    var $attributes = array();
    /**
     * Button label
     * @var string
     */
    var $label = '';
    /**
     * Wrapping div class
     * @var string
     * */
    var $class = 'urlselect';
    /**
     * True if button disabled, false if normal
     * @var boolean
     */
    var $disabled = false;
    /**
     * Button tooltip
     * @var string
     */
    var $tooltip = null;
    /**
     * Form id
     * @var string
     */
    var $formid = null;
    /**
     * List of attached actions
     * @var array of component_action
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
    public function __construct(array $urls, $selected='', $nothing=array(''=>'choosedots'),
            $formid=null, $showbutton=null) {
        $this->urls       = $urls;
        $this->selected   = $selected;
        $this->nothing    = $nothing;
        $this->formid     = $formid;
        $this->showbutton = $showbutton;
    }

    /**
     * Adds help icon.
     * @param string $page  The keyword that defines a help page
     * @param string $title A descriptive text for accessibility only
     * @param string $component
     * @param bool $linktext add extra text to icon
     * @return void
     */
    public function set_old_help_icon($helppage, $title, $component = 'moodle') {
        $this->helpicon = new old_help_icon($helppage, $title, $component);
    }

    /**
     * Adds help icon.
     * @param string $identifier The keyword that defines a help page
     * @param string $component
     * @param bool $linktext add extra text to icon
     * @return void
     */
    public function set_help_icon($identifier, $component = 'moodle') {
        $this->helpicon = new help_icon($identifier, $component);
    }

    /**
     * Sets select's label
     * @param string $label
     * @return void
     */
    public function set_label($label) {
        $this->label = $label;
    }
}


/**
 * Data structure describing html link with special action attached.
 * @copyright 2010 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class action_link implements renderable {
    /**
     * Href url
     * @var moodle_url
     */
    var $url;
    /**
     * Link text
     * @var string HTML fragment
     */
    var $text;
    /**
     * HTML attributes
     * @var array
     */
    var $attributes;
    /**
     * List of actions attached to link
     * @var array of component_action
     */
    var $actions;

    /**
     * Constructor
     * @param string|moodle_url $url
     * @param string $text HTML fragment
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     */
    public function __construct(moodle_url $url, $text, component_action $action=null, array $attributes=null) {
        $this->url       = clone($url);
        $this->text      = $text;
        $this->attributes = (array)$attributes;
        if ($action) {
            $this->add_action($action);
        }
    }

    /**
     * Add action to the link.
     * @param component_action $action
     * @return void
     */
    public function add_action(component_action $action) {
        $this->actions[] = $action;
    }

    public function add_class($class) {
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = $class;
        } else {
            $this->attributes['class'] .= ' ' . $class;
        }
    }
}

// ==== HTML writer and helper classes, will be probably moved elsewhere ======

/**
 * Simple html output class
 * @copyright 2009 Tim Hunt, 2010 Petr Skoda
 */
class html_writer {
    /**
     * Outputs a tag with attributes and contents
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
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function start_tag($tagname, array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . '>';
    }

    /**
     * Outputs a closing tag
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @return string HTML fragment
     */
    public static function end_tag($tagname) {
        return '</' . $tagname . '>';
    }

    /**
     * Outputs an empty tag with attributes
     * @param string $tagname The name of tag ('input', 'img', 'br' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function empty_tag($tagname, array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . ' />';
    }

    /**
     * Outputs a tag, but only if the contents are not empty
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
     * @param string $name The name of the attribute ('src', 'href', 'class' etc.)
     * @param string $value The value of the attribute. The value will be escaped with {@link s()}
     * @return string HTML fragment
     */
    public static function attribute($name, $value) {
        if (is_array($value)) {
            debugging("Passed an array for the HTML attribute $name", DEBUG_DEVELOPER);
        }
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
     * Generates random html element id.
     * @param string $base
     * @return string
     */
    public static function random_id($base='random') {
        return uniqid($base);
    }

    /**
     * Generates a simple html link
     * @param string|moodle_url $url
     * @param string $text link txt
     * @param array $attributes extra html attributes
     * @return string HTML fragment
     */
    public static function link($url, $text, array $attributes = null) {
        $attributes = (array)$attributes;
        $attributes['href']  = $url;
        return self::tag('a', $text, $attributes);
    }

    /**
     * generates a simple checkbox with optional label
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @param string $label
     * @param array $attributes
     * @return string html fragment
     */
    public static function checkbox($name, $value, $checked = true, $label = '', array $attributes = null) {
        $attributes = (array)$attributes;
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
            $output .= self::tag('label', $label, array('for'=>$attributes['id']));
        }

        return $output;
    }

    /**
     * Generates a simple select yes/no form field
     * @param string $name name of select element
     * @param bool $selected
     * @param array $attributes - html select element attributes
     * @return string HRML fragment
     */
    public static function select_yes_no($name, $selected=true, array $attributes = null) {
        $options = array('1'=>get_string('yes'), '0'=>get_string('no'));
        return self::select($options, $name, $selected, null, $attributes);
    }

    /**
     * Generates a simple select form field
     * @param array $options associative array value=>label ex.:
     *                array(1=>'One, 2=>Two)
     *              it is also possible to specify optgroup as complex label array ex.:
     *                array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *                array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     * @param string $name name of select element
     * @param string|array $selected value or array of values depending on multiple attribute
     * @param array|bool $nothing, add nothing selected option, or false of not added
     * @param array $attributes - html select element attributes
     * @return string HTML fragment
     */
    public static function select(array $options, $name, $selected = '', $nothing = array(''=>'choosedots'), array $attributes = null) {
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
        $attributes['class'] = 'select ' . $attributes['class']; /// Add 'select' selector always

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

    private static function select_option($label, $value, array $selected) {
        $attributes = array();
        $value = (string)$value;
        if (in_array($value, $selected, true)) {
            $attributes['selected'] = 'selected';
        }
        $attributes['value'] = $value;
        return self::tag('option', $label, $attributes);
    }

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
     * @param string $type The type of selector (years, months, days, hours, minutes)
     * @param string $name fieldname
     * @param int $currenttime A default timestamp in GMT
     * @param int $step minute spacing
     * @param array $attributes - html select element attributes
     * @return HTML fragment
     */
    public static function select_time($type, $name, $currenttime=0, $step=5, array $attributes=null) {
        if (!$currenttime) {
            $currenttime = time();
        }
        $currentdate = usergetdate($currenttime);
        $userdatetype = $type;
        $timeunits = array();

        switch ($type) {
            case 'years':
                for ($i=1970; $i<=2020; $i++) {
                    $timeunits[$i] = $i;
                }
                $userdatetype = 'year';
                break;
            case 'months':
                for ($i=1; $i<=12; $i++) {
                    $timeunits[$i] = userdate(gmmktime(12,0,0,$i,15,2000), "%B");
                }
                $userdatetype = 'month';
                $currentdate['month'] = $currentdate['mon'];
                break;
            case 'days':
                for ($i=1; $i<=31; $i++) {
                    $timeunits[$i] = $i;
                }
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

        if (empty($attributes['id'])) {
            $attributes['id'] = self::random_id('ts_');
        }
        $timerselector = self::select($timeunits, $name, $currentdate[$userdatetype], null, array('id'=>$attributes['id']));
        $label = self::tag('label', get_string(substr($type, 0, -1), 'form'), array('for'=>$attributes['id'], 'class'=>'accesshide'));

        return $label.$timerselector;
    }

    /**
     * Shortcut for quick making of lists
     * @param array $items
     * @param string $tag ul or ol
     * @param array $attributes
     * @return string
     */
    public static function alist(array $items, array $attributes = null, $tag = 'ul') {
        //note: 'list' is a reserved keyword ;-)

        $output = '';

        foreach ($items as $item) {
            $output .= html_writer::start_tag('li') . "\n";
            $output .= $item . "\n";
            $output .= html_writer::end_tag('li') . "\n";
        }

        return html_writer::tag($tag, $output, $attributes);
    }

    /**
     * Returns hidden input fields created from url parameters.
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
     * @param string $js the JavaScript code
	 * @param moodle_url|string optional url of the external script, $code ignored if specified
     * @return string HTML, the code wrapped in <script> tags.
     */
    public static function script($jscode, $url=null) {
        if ($jscode) {
            $attributes = array('type'=>'text/javascript');
            return self::tag('script', "\n//<![CDATA[\n$jscode\n//]]>\n", $attributes) . "\n";

        } else if ($url) {
            $attributes = array('type'=>'text/javascript', 'src'=>$url);
            return self::tag('script', '', $attributes) . "\n";

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

                if ($heading->header && empty($heading->scope)) {
                    $heading->scope = 'col';
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
                $attributes = array_merge($heading->attributes, array(
                        'style'     => $table->align[$key] . $table->size[$key] . $heading->style,
                        'scope'     => $heading->scope,
                        'colspan'   => $heading->colspan,
                    ));

                $tagtype = 'td';
                if ($heading->header === true) {
                    $tagtype = 'th';
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
            $oddeven    = 1;
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

                        foreach ($row as $item) {
                            $cell = new html_table_cell();
                            $cell->text = $item;
                            $newrow->cells[] = $cell;
                        }
                        $row = $newrow;
                    }

                    $oddeven = $oddeven ? 0 : 1;
                    if (isset($table->rowclasses[$key])) {
                        $row->attributes['class'] .= ' ' . $table->rowclasses[$key];
                    }

                    $row->attributes['class'] .= ' r' . $oddeven;
                    if ($key == $lastrowkey) {
                        $row->attributes['class'] .= ' lastrow';
                    }

                    $output .= html_writer::start_tag('tr', array('class' => trim($row->attributes['class']), 'style' => $row->style, 'id' => $row->id)) . "\n";
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
    public static function label($text, $for, $colonize=true, array $attributes=array()) {
        if (!is_null($for)) {
            $attributes = array_merge($attributes, array('for' => $for));
        }
        $text = trim($text);
        $label = self::tag('label', $text, $attributes);

        /*
        // TODO $colonize disabled for now yet - see MDL-12192 for details
        if (!empty($text) and $colonize) {
            // the $text may end with the colon already, though it is bad string definition style
            $colon = get_string('labelsep', 'langconfig');
            if (!empty($colon)) {
                $trimmed = trim($colon);
                if ((substr($text, -strlen($trimmed)) == $trimmed) or (substr($text, -1) == ':')) {
                    //debugging('The label text should not end with colon or other label separator,
                    //           please fix the string definition.', DEBUG_DEVELOPER);
                } else {
                    $label .= $colon;
                }
            }
        }
        */

        return $label;
    }
}

// ==== JS writer and helper classes, will be probably moved elsewhere ======

/**
 * Simple javascript output class
 * @copyright 2010 Petr Skoda
 */
class js_writer {
    /**
     * Returns javascript code calling the function
     * @param string $function function name, can be complex like Y.Event.purgeElement
     * @param array $arguments parameters
     * @param int $delay execution delay in seconds
     * @return string JS code fragment
     */
    public static function function_call($function, array $arguments = null, $delay=0) {
        if ($arguments) {
            $arguments = array_map('json_encode', $arguments);
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
     * Special function which adds Y as first argument of fucntion call.
     * @param string $function
     * @param array $extraarguments
     * @return string
     */
    public static function function_call_with_Y($function, array $extraarguments = null) {
        if ($extraarguments) {
            $extraarguments = array_map('json_encode', $extraarguments);
            $arguments = 'Y, ' . implode(', ', $extraarguments);
        } else {
            $arguments = 'Y';
        }
        return "$function($arguments);\n";
    }

    /**
     * Returns JavaScript code to initialise a new object
     * @param string|null $var If it is null then no var is assigned the new object
     * @param string $class
     * @param array $arguments
     * @param array $requirements
     * @param int $delay
     * @return string
     */
    public static function object_init($var, $class, array $arguments = null, array $requirements = null, $delay=0) {
        if (is_array($arguments)) {
            $arguments = array_map('json_encode', $arguments);
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
     * @param string $name
     * @param mixed $value json serialised value
     * @param bool $usevar add var definition, ignored for nested properties
     * @return string JS code fragment
     */
    public static function set_variable($name, $value, $usevar=true) {
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
     * @param mixed $selector standard YUI selector for elements, may be array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array  $arguments An optional array of argument parameters to pass to the function
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
 * Holds all the information required to render a <table> by {@see core_renderer::table()}
 *
 * Example of usage:
 * $t = new html_table();
 * ... // set various properties of the object $t as described below
 * echo html_writer::table($t);
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table {
    /**
     * @var string value to use for the id attribute of the table
     */
    public $id = null;
    /**
     * @var array attributes of HTML attributes for the <table> element
     */
    public $attributes = array();
    /**
     * For more control over the rendering of the headers, an array of html_table_cell objects
     * can be passed instead of an array of strings.
     * @var array of headings. The n-th array item is used as a heading of the n-th column.
     *
     * Example of usage:
     * $t->head = array('Student', 'Grade');
     */
    public $head;
    /**
     * @var array can be used to make a heading span multiple columns
     *
     * Example of usage:
     * $t->headspan = array(2,1);
     *
     * In this example, {@see html_table:$data} is supposed to have three columns. For the first two columns,
     * the same heading is used. Therefore, {@see html_table::$head} should consist of two items.
     */
    public $headspan;
    /**
     * @var array of column alignments. The value is used as CSS 'text-align' property. Therefore, possible
     * values are 'left', 'right', 'center' and 'justify'. Specify 'right' or 'left' from the perspective
     * of a left-to-right (LTR) language. For RTL, the values are flipped automatically.
     *
     * Examples of usage:
     * $t->align = array(null, 'right');
     * or
     * $t->align[1] = 'right';
     *
     */
    public $align;
    /**
     * @var array of column sizes. The value is used as CSS 'size' property.
     *
     * Examples of usage:
     * $t->size = array('50%', '50%');
     * or
     * $t->size[1] = '120px';
     */
    public $size;
    /**
     * @var array of wrapping information. The only possible value is 'nowrap' that sets the
     * CSS property 'white-space' to the value 'nowrap' in the given column.
     *
     * Example of usage:
     * $t->wrap = array(null, 'nowrap');
     */
    public $wrap;
    /**
     * @var array of arrays or html_table_row objects containing the data. Alternatively, if you have
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
    public $data;
    /**
     * @var string width of the table, percentage of the page preferred. Defaults to 80%
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $width = null;
    /**
     * @var string alignment the whole table. Can be 'right', 'left' or 'center' (default).
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $tablealign = null;
    /**
     * @var int padding on each cell, in pixels
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $cellpadding = null;
    /**
     * @var int spacing between cells, in pixels
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $cellspacing = null;
    /**
     * @var array classes to add to particular rows, space-separated string.
     * Classes 'r0' or 'r1' are added automatically for every odd or even row,
     * respectively. Class 'lastrow' is added automatically for the last row
     * in the table.
     *
     * Example of usage:
     * $t->rowclasses[9] = 'tenth'
     */
    public $rowclasses;
    /**
     * @var array classes to add to every cell in a particular column,
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
     * @var string description of the contents for screen readers.
     */
    public $summary;

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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table_row {
    /**
     * @var string value to use for the id attribute of the row
     */
    public $id = null;
    /**
     * @var array $cells Array of html_table_cell objects
     */
    public $cells = array();
    /**
     * @var string $style value to use for the style attribute of the table row
     */
    public $style = null;
    /**
     * @var array attributes of additional HTML attributes for the <tr> element
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table_cell {
    /**
     * @var string value to use for the id attribute of the cell
     */
    public $id = null;
    /**
     * @var string $text The contents of the cell
     */
    public $text;
    /**
     * @var string $abbr Abbreviated version of the contents of the cell
     */
    public $abbr = null;
    /**
     * @var int $colspan Number of columns this cell should span
     */
    public $colspan = null;
    /**
     * @var int $rowspan Number of rows this cell should span
     */
    public $rowspan = null;
    /**
     * @var string $scope Defines a way to associate header cells and data cells in a table
     */
    public $scope = null;
    /**
     * @var boolean $header Whether or not this cell is a header cell
     */
    public $header = null;
    /**
     * @var string $style value to use for the style attribute of the table cell
     */
    public $style = null;
    /**
     * @var array attributes of additional HTML attributes for the <td> element
     */
    public $attributes = array();

    public function __construct($text = null) {
        $this->text = $text;
        $this->attributes['class'] = '';
    }
}


/// Complex components aggregating simpler components


/**
 * Component representing a paging bar.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class paging_bar implements renderable {
    /**
     * @var int $maxdisplay The maximum number of pagelinks to display
     */
    public $maxdisplay = 18;
    /**
     * @var int $totalcount post or get
     */
    public $totalcount;
    /**
     * @var int $page The page you are currently viewing
     */
    public $page;
    /**
     * @var int $perpage The number of entries that should be shown per page
     */
    public $perpage;
    /**
     * @var string $baseurl If this  is a string then it is the url which will be appended with $pagevar, an equals sign and the page number.
     *      If this is a moodle_url object then the pagevar param will be replaced by the page no, for each page.
     */
    public $baseurl;
    /**
     * @var string $pagevar This is the variable name that you use for the page number in your code (ie. 'tablepage', 'blogpage', etc)
     */
    public $pagevar;
    /**
     * @var string $previouslink A HTML link representing the "previous" page
     */
    public $previouslink = null;
    /**
     * @var tring $nextlink A HTML link representing the "next" page
     */
    public $nextlink = null;
    /**
     * @var tring $firstlink A HTML link representing the first page
     */
    public $firstlink = null;
    /**
     * @var tring $lastlink A HTML link representing the last page
     */
    public $lastlink = null;
    /**
     * @var array $pagelinks An array of strings. One of them is just a string: the current page
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
     * @return void
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

            if ($this->page > 15) {
                $startpage = $this->page - 10;

                $this->firstlink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>0)), '1', array('class'=>'first'));
            } else {
                $startpage = 0;
            }

            $currpage = $startpage;
            $displaycount = $displaypage = 0;

            while ($displaycount < $this->maxdisplay and $currpage < $lastpage) {
                $displaypage = $currpage + 1;

                if ($this->page == $currpage) {
                    $this->pagelinks[] = $displaypage;
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

            if ($pagenum != $displaypage) {
                $this->nextlink = html_writer::link(new moodle_url($this->baseurl, array($this->pagevar=>$pagenum)), get_string('next'), array('class'=>'next'));
            }
        }
    }
}


/**
 * This class represents how a block appears on a page.
 *
 * During output, each block instance is asked to return a block_contents object,
 * those are then passed to the $OUTPUT->block function for display.
 *
 * {@link $contents} should probably be generated using a moodle_block_..._renderer.
 *
 * Other block-like things that need to appear on the page, for example the
 * add new block UI, are also represented as block_contents objects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class block_contents {
    /** @var int used to set $skipid. */
    protected static $idcounter = 1;

    const NOT_HIDEABLE = 0;
    const VISIBLE = 1;
    const HIDDEN = 2;

    /**
     * @var integer $skipid All the blocks (or things that look like blocks)
     * printed on a page are given a unique number that can be used to construct
     * id="" attributes. This is set automatically be the {@link prepare()} method.
     * Do not try to set it manually.
     */
    public $skipid;

    /**
     * @var integer If this is the contents of a real block, this should be set to
     * the block_instance.id. Otherwise this should be set to 0.
     */
    public $blockinstanceid = 0;

    /**
     * @var integer if this is a real block instance, and there is a corresponding
     * block_position.id for the block on this page, this should be set to that id.
     * Otherwise it should be 0.
     */
    public $blockpositionid = 0;

    /**
     * @param array $attributes an array of attribute => value pairs that are put on the
     * outer div of this block. {@link $id} and {@link $classes} attributes should be set separately.
     */
    public $attributes;

    /**
     * @param string $title The title of this block. If this came from user input,
     * it should already have had format_string() processing done on it. This will
     * be output inside <h2> tags. Please do not cause invalid XHTML.
     */
    public $title = '';

    /**
     * @param string $content HTML for the content
     */
    public $content = '';

    /**
     * @param array $list an alternative to $content, it you want a list of things with optional icons.
     */
    public $footer = '';

    /**
     * Any small print that should appear under the block to explain to the
     * teacher about the block, for example 'This is a sticky block that was
     * added in the system context.'
     * @var string
     */
    public $annotation = '';

    /**
     * @var integer one of the constants NOT_HIDEABLE, VISIBLE, HIDDEN. Whether
     * the user can toggle whether this block is visible.
     */
    public $collapsible = self::NOT_HIDEABLE;

    /**
     * A (possibly empty) array of editing controls. Each element of this array
     * should be an array('url' => $url, 'icon' => $icon, 'caption' => $caption).
     * $icon is the icon name. Fed to $OUTPUT->pix_url.
     * @var array
     */
    public $controls = array();


    /**
     * Create new instance of block content
     * @param array $attributes
     */
    public function __construct(array $attributes=null) {
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
     * @param string $class
     * @return void
     */
    public function add_class($class) {
        $this->attributes['class'] .= ' '.$class;
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class block_move_target {
    /**
     * Move url
     * @var moodle_url
     */
    public $url;
    /**
     * label
     * @var string
     */
    public $text;

    /**
     * Constructor
     * @param string $text
     * @param moodle_url $url
     */
    public function __construct($text, moodle_url $url) {
        $this->text = $text;
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class custom_menu_item implements renderable {
    /**
     * The text to show for the item
     * @var string
     */
    protected $text;
    /**
     * The link to give the icon if it has no children
     * @var moodle_url
     */
    protected $url;
    /**
     * A title to apply to the item. By default the text
     * @var string
     */
    protected $title;
    /**
     * A sort order for the item, not necessary if you order things in the CFG var
     * @var int
     */
    protected $sort;
    /**
     * A reference to the parent for this item or NULL if it is a top level item
     * @var custom_menu_item
     */
    protected $parent;
    /**
     * A array in which to store children this item has.
     * @var array
     */
    protected $children = array();
    /**
     * A reference to the sort var of the last child that was added
     * @var int
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
    public function __construct($text, moodle_url $url=null, $title=null, $sort = null, custom_menu_item $parent=null) {
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
    public function add($text, moodle_url $url=null, $title=null, $sort = null) {
        $key = count($this->children);
        if (empty($sort)) {
            $sort = $this->lastsort + 1;
        }
        $this->children[$key] = new custom_menu_item($text, $url, $title, $sort, $this);
        $this->lastsort = (int)$sort;
        return $this->children[$key];
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class custom_menu extends custom_menu_item {
    /**
     * Creates the custom menu
     * @param string $text Sets the text for this custom menu, never gets used and is optional
     */
    public function __construct($text='base') {
        global $CFG;
        parent::__construct($text);
        if (!empty($CFG->custommenuitems)) {
            $this->override_children(self::convert_text_to_menu_nodes($CFG->custommenuitems));
        }
    }

    /**
     * Overrides the children of this custom menu. Useful when getting children
     * from $CFG->custommenuitems
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
     *     text|url|title
     * The number of hyphens at the start determines the depth of the item
     *
     * Example structure:
     *     First level first item|http://www.moodle.com/
     *     -Second level first item|http://www.moodle.com/partners/
     *     -Second level second item|http://www.moodle.com/hq/
     *     --Third level first item|http://www.moodle.com/jobs/
     *     -Second level third item|http://www.moodle.com/development/
     *     First level second item|http://www.moodle.com/feedback/
     *     First level third item
     *
     * @static
     * @param string $text
     * @return array
     */
    public static function convert_text_to_menu_nodes($text) {
        $text = format_text($text, FORMAT_MOODLE, array(
            'para'     => false,
            'newlines' => false,
            'context'  => get_system_context()));
        $lines = explode("\n", $text);
        $children = array();
        $lastchild = null;
        $lastdepth = null;
        $lastsort = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            $bits = explode('|', $line ,4);    // name|url|title|sort
            if (!array_key_exists(0, $bits) || empty($bits[0])) {
                // Every item must have a name to be valid
                continue;
            } else {
                $bits[0] = ltrim($bits[0],'-');
            }
            if (!array_key_exists(1, $bits)) {
                // Set the url to null
                $bits[1] = null;
            } else {
                // Make sure the url is a moodle url
                $bits[1] = new moodle_url(trim($bits[1]));
            }
            if (!array_key_exists(2, $bits)) {
                // Set the title to null seeing as there isn't one
                $bits[2] = $bits[0];
            }
            // Set an incremental sort order to keep it simple.
            $bits[3] = $lastsort;
            $lastsort = $bits[3]+1;
            if (preg_match('/^(\-*)/', $line, $match) && $lastchild != null && $lastdepth !== null) {
                $depth = strlen($match[1]);
                if ($depth < $lastdepth) {
                    $difference = $lastdepth - $depth;
                    if ($lastdepth > 1 && $lastdepth != $difference) {
                        $tempchild = $lastchild->get_parent();
                        for ($i =0; $i < $difference; $i++) {
                            $tempchild = $tempchild->get_parent();
                        }
                        $lastchild = $tempchild->add($bits[0], $bits[1], $bits[2], $bits[3]);
                    } else {
                        $depth = 0;
                        $lastchild = new custom_menu_item($bits[0], $bits[1], $bits[2], $bits[3]);
                        $children[] = $lastchild;
                    }
                } else if ($depth > $lastdepth) {
                    $depth = $lastdepth + 1;
                    $lastchild = $lastchild->add($bits[0], $bits[1], $bits[2], $bits[3]);
                } else {
                    if ($depth == 0) {
                        $lastchild = new custom_menu_item($bits[0], $bits[1], $bits[2], $bits[3]);
                        $children[] = $lastchild;
                    } else {
                        $lastchild = $lastchild->get_parent()->add($bits[0], $bits[1], $bits[2], $bits[3]);
                    }
                }
            } else {
                $depth = 0;
                $lastchild = new custom_menu_item($bits[0], $bits[1], $bits[2], $bits[3]);
                $children[] = $lastchild;
            }
            $lastdepth = $depth;
        }
        return $children;
    }

    /**
     * Sorts two custom menu items
     *
     * This function is designed to be used with the usort method
     *     usort($this->children, array('custom_menu','sort_custom_menu_items'));
     *
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
