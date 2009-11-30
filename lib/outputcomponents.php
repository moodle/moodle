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
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base class for classes representing HTML elements, like html_select.
 *
 * Handles the id and class attributes.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_html_component {
    /**
     * @var string value to use for the id attribute of this HTML tag.
     */
    public $id = '';
    /**
     * @var string $alt value to use for the alt attribute of this HTML tag.
     */
    public $alt = '';
    /**
     * @var string $style value to use for the style attribute of this HTML tag.
     */
    public $style = '';
    /**
     * @var array class names to add to this HTML element.
     */
    public $classes = array();
    /**
     * @var string $title The title attributes applicable to any XHTML element
     */
    public $title = '';
    /**
     * An optional array of component_action objects handling the action part of this component.
     * @var array $actions
     */
    protected $actions = array();

    /**
     * Ensure some class names are an array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return array the class names as an array.
     */
    public static function clean_classes($classes) {
        if (empty($classes)) {
            return array();
        } else if (is_array($classes)) {
            return $classes;
        } else {
            return explode(' ', trim($classes));
        }
    }

    /**
     * Set the class name array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return void
     */
    public function set_classes($classes) {
        $this->classes = self::clean_classes($classes);
    }

    /**
     * Add a class name to the class names array.
     * @param string $class the new class name to add.
     * @return void
     */
    public function add_class($class) {
        $this->classes[] = $class;
    }

    /**
     * Add a whole lot of class names to the class names array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return void
     */
    public function add_classes($classes) {
        $this->classes = array_merge($this->classes, self::clean_classes($classes));
    }

    /**
     * Get the class names as a string.
     * @return string the class names as a space-separated string. Ready to be put in the class="" attribute.
     */
    public function get_classes_string() {
        return implode(' ', $this->classes);
    }

    /**
     * Perform any cleanup or final processing that should be done before an
     * instance of this class is output.
     * @return void
     */
    public function prepare() {
        $this->classes = array_unique(self::clean_classes($this->classes));
    }

    /**
     * This checks developer do not try to assign a property directly
     * if we have a setter for it. Otherwise, the property is set as expected.
     * @param string $name The name of the variable to set
     * @param mixed $value The value to assign to the variable
     * @return void
     */
    public function __set($name, $value) {
        if ($name == 'class') {
            debugging('this way of setting css class has been deprecated. use set_classes() method instead.');
            $this->set_classes($value);
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * Adds a JS action to this component.
     * Note: the JS function you write must have only two arguments: (string)event and (object|array)args
     * If you want to add an instantiated component_action (or one of its subclasses), give the object as the only parameter
     *
     * @param mixed  $event a DOM event (click, mouseover etc.) or a component_action object
     * @param string $jsfunction The name of the JS function to call. required if argument 1 is a string (event)
     * @param array  $jsfunctionargs An optional array of JS arguments to pass to the function
     */
    public function add_action($event, $jsfunction=null, $jsfunctionargs=array()) {
        if (empty($this->id)) {
            $this->generate_id();
        }

        if ($event instanceof component_action) {
            $this->actions[] = $event;
        } else {
            if (empty($jsfunction)) {
                throw new coding_exception('moodle_html_component::add_action requires a JS function argument if the first argument is a string event');
            }
            $this->actions[] = new component_action($event, $jsfunction, $jsfunctionargs);
        }
    }

    /**
     * Internal method for generating a unique ID for the purpose of event handlers.
     */
    protected function generate_id() {
        $this->id = uniqid(get_class($this));
    }

    /**
     * Returns the array of component_actions.
     * @return array Component actions
     */
    public function get_actions() {
        return $this->actions;
    }

    /**
     * Shortcut for adding a JS confirm dialog when the component is clicked.
     * The message must be a yes/no question.
     * @param string $message The yes/no confirmation question. If "Yes" is clicked, the original action will occur.
     * @param string $callback The name of a JS function whose scope will be set to the simpleDialog object and have this
     *    function's arguments set as this.args.
     * @return void
     */
    public function add_confirm_action($message, $callback=null) {
        $this->add_action(new component_action('click', 'confirm_dialog', array('message' => $message, 'callback' => $callback)));
    }

    /**
     * Returns true if this component has an action of the requested type (component_action by default).
     * @param string $class The class of the action we are looking for
     * @return boolean True if action is found
     */
    public function has_action($class='component_action') {
        foreach ($this->actions as $action) {
            if (get_class($action) == $class) {
                return true;
            }
        }
        return false;
    }
}

class labelled_html_component extends moodle_html_component {
    /**
     * @var mixed $label The label for that component. String or html_label object
     */
    public $label;

    /**
     * Adds a descriptive label to the component.
     *
     * This can be used in two ways:
     *
     * <pre>
     * $component->set_label($elementlabel, $elementid);
     * // OR
     * $label = new html_label();
     * $label->for = $elementid;
     * $label->text = $elementlabel;
     * $component->set_label($label);
     * </pre>
     *
     * Use the second form when you need to add additional HTML attributes
     * to the label and/or JS actions.
     *
     * @param mixed $text Either the text of the label or a html_label object
     * @param text  $for The value of the "for" attribute (the associated element's id)
     * @return void
     */
    public function set_label($text, $for=null) {
        if ($text instanceof html_label) {
            $this->label = $text;
        } else if (!empty($text)) {
            $this->label = new html_label();
            $this->label->for = $for;
            if (empty($for)) {
                if (empty($this->id)) {
                    $this->generate_id();
                }
                $this->label->for = $this->id;
            }
            $this->label->text = $text;
        }
    }
}

/// Components representing HTML elements

/**
 * This class represents a label element
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_label extends moodle_html_component {
    /**
     * @var string $text The text to display in the label
     */
    public $text;
    /**
     * @var string $for The name of the form field this label is associated with
     */
    public $for;

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        if (empty($this->text)) {
            throw new coding_exception('html_label must have a $text value.');
        }
        parent::prepare();
    }
}

/**
 * This class hold all the information required to describe a <select> menu that
 * will be printed by {@link moodle_core_renderer::select()}. (Or by an overridden
 * version of that method in a subclass.)
 *
 * This component can also hold enough metadata to be used as a popup form. It just
 * needs a bit more setting up than for a simple menu. See the shortcut methods for
 * developer-friendly usage.
 *
 * All the fields that are not set by the constructor have sensible defaults, so
 * you only need to set the properties where you want non-default behaviour.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_select extends labelled_html_component {
    /**
     * The html_select object parses an array of options into component objects
     * @see nested attribute
     * @var mixed $options the choices to show in the menu. An array $value => $display, of html_select_option or of html_select_optgroup objects.
     */
    public $options;
    /**
     * @var string $name the name of this form control. That is, the name of the GET/POST
     * variable that will be set if this select is submitted as part of a form.
     */
    public $name;
    /**
     * @var string $selectedvalue the option to select initially. Should match one
     * of the $options array keys. Default none.
     */
    public $selectedvalue;
    /**
     * Defaults to get_string('choosedots').
     * Set this to '' if you do not want a 'nothing is selected' option.
     * This is ignored if the rendertype is 'radio' or 'checkbox'
     * @var string The label for the 'nothing is selected' option.
     */
    public $nothinglabel = null;
    /**
     * @var string The value returned by the 'nothing is selected' option. Defaults to 0.
     */
    public $nothingvalue = 0;
    /**
     * @var boolean set this to true if you want the control to appear disabled.
     */
    public $disabled = false;
    /**
     * @var integer if non-zero, sets the tabindex attribute on the <select> element. Default 0.
     */
    public $tabindex = 0;
    /**
     * @var mixed Defaults to false, which means display the select as a dropdown menu.
     * If true, display this select as a list box whose size is chosen automatically.
     * If an integer, display as list box of that size.
     */
    public $listbox = false;
    /**
     * @var integer if you are using $listbox === true to get an automatically
     * sized list box, the size of the list box will be the number of options,
     * or this number, whichever is smaller.
     */
    public $maxautosize = 10;
    /**
     * @var boolean if true, allow multiple selection. Only used if $listbox is true, or if
     *      the select is to be output as checkboxes.
     */
    public $multiple = false;
    /**
     * Another way to use nested menu is to prefix optgroup labels with -- and end the optgroup with --
     * Leave this setting to false if you are using the latter method.
     * @var boolean $nested if true, uses $options' keys as option headings (optgroup)
     */
    public $nested = false;
    /**
     * @var html_form $form An optional html_form component
     */
    public $form;
    /**
     * @var moodle_help_icon $form An optional moodle_help_icon component
     */
    public $helpicon;
    /**
     * @var boolean $rendertype How the select element should be rendered: menu or radio (checkbox is just radio + multiple)
     */
    public $rendertype = 'menu';

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        global $CFG;

        // name may contain [], which would make an invalid id. e.g. numeric question type editing form, assignment quickgrading
        if (empty($this->id)) {
            $this->id = 'menu' . str_replace(array('[', ']'), '', $this->name);
        }

        if (empty($this->classes)) {
            $this->set_classes(array('menu' . str_replace(array('[', ']'), '', $this->name)));
        }

        if (is_null($this->nothinglabel)) {
            $this->nothinglabel = get_string('choosedots');
        }

        if (!empty($this->label) && !($this->label instanceof html_label)) {
            $label = new html_label();
            $label->text = $this->label;
            $label->for = $this->name;
            $this->label = $label;
        }

        $this->add_class('select');

        $this->initialise_options();
        parent::prepare();
    }

    /**
     * This is a shortcut for making a simple select menu. It lets you specify
     * the options, name and selected option in one line of code.
     * @param array $options used to initialise {@link $options}.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @param string $nothinglabel The label for the 'nothing is selected' option. Defaults to "Choose..."
     * @return html_select A html_select object with the three common fields initialised.
     */
    public static function make($options, $name, $selected = '', $nothinglabel='choosedots') {
        $menu = new html_select();
        $menu->options = $options;
        $menu->name = $name;
        $menu->selectedvalue = $selected;
        return $menu;
    }

    /**
     * This is a shortcut for making a yes/no select menu.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @return html_select A menu initialised with yes/no options.
     */
    public static function make_yes_no($name, $selected) {
        return self::make(array(0 => get_string('no'), 1 => get_string('yes')), $name, $selected);
    }

    /**
     * This is a shortcut for making an hour selector menu.
     * @param string $type The type of selector (years, months, days, hours, minutes)
     * @param string $name fieldname
     * @param int $currenttime A default timestamp in GMT
     * @param int $step minute spacing
     * @return html_select A menu initialised with hour options.
     */
    public static function make_time_selector($type, $name, $currenttime=0, $step=5) {

        if (!$currenttime) {
            $currenttime = time();
        }
        $currentdate = usergetdate($currenttime);
        $userdatetype = $type;

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
                throw new coding_exception("Time type $type is not supported by html_select::make_time_selector().");
        }

        $timerselector = self::make($timeunits, $name, $currentdate[$userdatetype]);
        $timerselector->label = new html_label();

        $timerselector->label->text = get_string(substr($type, 0, -1), 'form');
        $timerselector->label->for = "menu$timerselector->name";
        $timerselector->label->add_class('accesshide');
        $timerselector->nothinglabel = '';

        return $timerselector;
    }

    /**
     * Given an associative array of type => fieldname and an optional timestamp,
     * returns an array of html_select components representing date/time selectors.
     * @param array $selectors Arrays of type => fieldname. Selectors will be returned in the order of the types given
     * @param int $currenttime A UNIX timestamp
     * @param int $step minute spacing
     * @return array Instantiated date/time selectors
     */
    public function make_time_selectors($selectors, $currenttime=0, $step=5) {
        $selects = array();
        foreach ($selectors as $type => $name) {
            $selects[] = html_select::make_time_selector($type, $name, $currenttime, $step);
        }
        return $selects;
    }

    /**
     * This is a shortcut for making a select popup form.
     * @param mixed $baseurl The target URL, string or moodle_url
     * @param string $name The variable which this select's options are changing in the URL
     * @param array $options A list of value-label pairs for the popup list
     * @param string $formid id for the control. Must be unique on the page. Used in the HTML.
     * @param string $selected The option that is initially selected
     * @return html_select A menu initialised as a popup form.
     */
    public function make_popup_form($baseurl, $name, $options, $formid, $selected=null) {
        global $CFG;

        $selectedurl = null;

        if (!($baseurl instanceof moodle_url)) {
            $baseurl = new moodle_url($baseurl);
        }

        if (!empty($selected)) {
            $selectedurl = $baseurl->out(false, array($name => $selected), false);
        }

        if (!($baseurl instanceof moodle_url)) {
            $baseurl = new moodle_url($baseurl);
        }

        // Replace real value by formatted URLs
        foreach ($options as $value => $label) {
            $options[$baseurl->out(false, array($name => $value), false)] = $label;
            unset($options[$value]);
        }

        $select = self::make($options, 'jump', $selectedurl);

        $select->form = new html_form();
        $select->form->id = $formid;
        $select->form->method = 'get';
        $select->form->jssubmitaction = true;
        $select->form->add_class('popupform');
        $select->form->url = new moodle_url($CFG->wwwroot . '/course/jumpto.php', array('sesskey' => sesskey()));
        $select->form->button->text = get_string('go');

        $select->id = $formid . '_jump';

        $select->add_action('change', 'submit_form_by_id', array('id' => $formid, 'selectid' => $select->id));

        return $select;
    }

    /**
     * Override the URLs of the default popup_form, which only supports one base URL
     * @param array $options value=>label pairs representing select options
     * @return void
     */
    public function override_option_values($options) {
        global $PAGE;

        $originalcount = count($options);
        $this->initialise_options();
        $newcount = count($this->options);
        $first = true;

        reset($options);

        foreach ($this->options as $optkey => $optgroup) {
            if ($optgroup instanceof html_select_optgroup) {
                foreach ($optgroup->options as $key => $option) {
                    next($options);
                    $this->options[$optkey]->options[$key]->value = key($options);

                    $optionurl = new moodle_url(key($options));

                    if ($optionurl->compare($PAGE->url, URL_MATCH_PARAMS)) {
                        $this->options[$optkey]->options[$key]->selected = 'selected';
                    }
                }
                next($options);
            } else if ($optgroup instanceof html_select_option && !($first && $originalcount < $newcount)) {
                $this->options[$optkey]->value = key($options);
                $optionurl = new moodle_url(key($options));

                if ($optionurl->compare($PAGE->url, URL_MATCH_PARAMS)) {
                    $this->options[$optkey]->selected = 'selected';
                }
                next($options);
            }
            $first = false;
        }
    }

    /**
     * Adds a help icon next to the select menu.
     *
     * This can be used in two ways:
     *
     * <pre>
     * $select->set_help_icon($page, $text, $linktext);
     * // OR
     * $helpicon = new moodle_help_icon();
     * $helpicon->page = $page;
     * $helpicon->text = $text;
     * $helpicon->linktext = $linktext;
     * $select->set_help_icon($helpicon);
     * </pre>
     *
     * Use the second form when you need to add additional HTML attributes
     * to the label and/or JS actions.
     *
     * @param mixed $page Either the keyword that defines a help page or a moodle_help_icon object
     * @param text  $text The text of the help icon
     * @param boolean $linktext Whether or not to show text next to the icon
     * @return void
     */
    public function set_help_icon($page, $text, $linktext=false) {
        if ($page instanceof moodle_help_icon) {
            $this->helpicon = $page;
        } else if (!empty($page)) {
            $this->helpicon = new moodle_help_icon();
            $this->helpicon->page = $page;
            $this->helpicon->text = $text;
            $this->helpicon->linktext = $linktext;
        }
    }

    /**
     * Parses the $options array and instantiates html_select_option objects in
     * the place of the original value => label pairs. This is useful for when you
     * need to setup extra html attributes and actions on individual options before
     * the component is sent to the renderer
     * @return void;
     */
    public function initialise_options() {
        // If options are already instantiated objects, stop here
        $firstoption = reset($this->options);
        if ($firstoption instanceof html_select_option || $firstoption instanceof html_select_optgroup) {
            return;
        }

        if ($this->rendertype == 'radio' && $this->multiple) {
            $this->rendertype = 'checkbox';
        }

        // If nested is on, or if radio/checkbox rendertype is set, remove the default Choose option
        if ($this->nested || $this->rendertype == 'radio' || $this->rendertype == 'checkbox') {
            $this->nothinglabel = '';
        }

        $options = $this->options;

        $this->options = array();

        if ($this->nested && $this->rendertype != 'menu') {
            throw new coding_exception('html_select cannot render nested options as radio buttons or checkboxes.');
        } else if ($this->nested) {
            foreach ($options as $section => $values) {
                $optgroup = new html_select_optgroup();
                $optgroup->text = $section;

                foreach ($values as $value => $display) {
                    $option = new html_select_option();
                    $option->value = s($value);
                    $option->text = $display;
                    if ($display === '') {
                        $option->text = $value;
                    }

                    if ((string) $value == (string) $this->selectedvalue ||
                            (is_array($this->selectedvalue) && in_array($value, $this->selectedvalue))) {
                        $option->selected = 'selected';
                    }

                    $optgroup->options[] = $option;
                }

                $this->options[] = $optgroup;
            }
        } else {
            $inoptgroup = false;
            $optgroup = false;

            foreach ($options as $value => $display) {
                if ($display == '--') { /// we are ending previous optgroup
                    // $this->options[] = $optgroup;
                    $inoptgroup = false;
                    continue;
                } else if (substr($display,0,2) == '--') { /// we are starting a new optgroup
                    if (!empty($optgroup->options)) {
                        $this->options[] = $optgroup;
                    }

                    $optgroup = new html_select_optgroup();
                    $optgroup->text = substr($display,2); // stripping the --

                    $inoptgroup = true; /// everything following will be in an optgroup
                    continue;

                } else {
                    // Add $nothing option if there are not optgroups
                    if ($this->nothinglabel && empty($this->options[0]) && !$inoptgroup) {
                        $nothingoption = new html_select_option();
                        $nothingoption->value = 0;
                        if (!empty($this->nothingvalue)) {
                            $nothingoption->value = $this->nothingvalue;
                        }
                        $nothingoption->text = $this->nothinglabel;
                        $this->options = array($nothingoption) + $this->options;
                    }

                    $option = new html_select_option();
                    $option->text = $display;

                    if ($display === '') {
                        $option->text = $value;
                    }

                    if ((string) $value == (string) $this->selectedvalue ||
                            (is_array($this->selectedvalue) && in_array($value, $this->selectedvalue))) {
                        $option->selected = 'selected';
                    }

                    $option->value = s($value);

                    if ($inoptgroup) {
                        $optgroup->options[] = $option;
                    } else {
                        $this->options[] = $option;
                    }
                }
            }

            if ($optgroup) {
                $this->options[] = $optgroup;
            }
        }
    }
}

/**
 * This class represents a select option element
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_select_option extends labelled_html_component {
    /**
     * @var string $value The value of this option (will be sent with form)
     */
    public $value;
    /**
     * @var string $text The display value of the option
     */
    public $text;
    /**
     * @var boolean $selected Whether or not this option is selected
     */
    public $selected = false;
    /**
     * @var boolean $disabled Whether or not this option is disabled
     */
    public $disabled = false;

    public function __construct() {
        $this->label = new html_label();
    }

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        if (empty($this->text)) {
            throw new coding_exception('html_select_option requires a $text value.');
        }

        if (empty($this->label->text)) {
            $this->set_label($this->text);
        } else if (!($this->label instanceof html_label)) {
            $this->set_label($this->label);
        }
        if (empty($this->id)) {
            $this->generate_id();
        }

        parent::prepare();
    }

    /**
     * Shortcut for making a checkbox-ready option
     * @param string $value The value of the checkbox
     * @param boolean $checked
     * @param string $label
     * @param string $alt
     * @return html_select_option A component ready for $OUTPUT->checkbox()
     */
    public function make_checkbox($value, $checked, $label, $alt='') {
        $checkbox = new html_select_option();
        $checkbox->value = $value;
        $checkbox->selected = $checked;
        $checkbox->text = $label;
        $checkbox->label->text = $label;
        $checkbox->alt = $alt;
        return $checkbox;
    }
}

/**
 * This class represents a select optgroup element
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_select_optgroup extends moodle_html_component {
    /**
     * @var string $text The display value of the optgroup
     */
    public $text;
    /**
     * @var array $options An array of html_select_option objects
     */
    public $options = array();

    public function prepare() {
        if (empty($this->text)) {
            throw new coding_exception('html_select_optgroup requires a $text value.');
        }
        if (empty($this->options)) {
            throw new coding_exception('html_select_optgroup requires at least one html_select_option object');
        }
        parent::prepare();
    }
}

/**
 * This class represents an input field
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_field extends labelled_html_component {
    /**
     * @var string $name The name attribute of the field
     */
    public $name;
    /**
     * @var string $value The value attribute of the field
     */
    public $value;
    /**
     * @var string $type The type attribute of the field (text, submit, checkbox etc)
     */
    public $type;
    /**
     * @var string $maxlength The maxlength attribute of the field (only applies to text type)
     */
    public $maxlength;
    /**
     * @var boolean $disabled Whether or not this field is disabled
     */
    public $disabled = false;

    public function __construct() {
        $this->label = new html_label();
    }

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        if (empty($this->style)) {
            $this->style = 'width: 4em;';
        }
        if (empty($this->id)) {
            $this->generate_id();
        }
        parent::prepare();
    }

    /**
     * Shortcut for creating a text input component.
     * @param string $name    The name of the text field
     * @param string $value   The value of the text field
     * @param string $alt     The info to be inserted in the alt tag
     * @param int $maxlength Sets the maxlength attribute of the field. Not set by default
     * @return html_field The field component
     */
    public static function make_text($name='unnamed', $value='', $alt='', $maxlength=0) {
        $field = new html_field();
        if (empty($alt)) {
            $alt = $name;
        }
        $field->type = 'text';
        $field->name = $name;
        $field->value = $value;
        $field->alt = $alt;
        $field->maxlength = $maxlength;
        return $field;
    }
}

/**
 * Holds all the information required to render a <table> by
 * {@see moodle_core_renderer::table()} or by an overridden version of that
 * method in a subclass.
 *
 * Example of usage:
 * $t = new html_table();
 * ... // set various properties of the object $t as described below
 * echo $OUTPUT->table($t);
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table extends labelled_html_component {
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
     * @var string width of the table, percentage of the page preferred. Defaults to 80% of the page width.
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
     * @var bool true causes the contents of the heading cells to be rotated 90 degrees.
     */
    public $rotateheaders = false;
    /**
     * @var array $headclasses Array of CSS classes to apply to the table's thead.
     */
    public $headclasses = array();
    /**
     * @var array $bodyclasses Array of CSS classes to apply to the table's tbody.
     */
    public $bodyclasses = array();
    /**
     * @var array $footclasses Array of CSS classes to apply to the table's tfoot.
     */
    public $footclasses = array();


    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        if (!empty($this->align)) {
            foreach ($this->align as $key => $aa) {
                if ($aa) {
                    $this->align[$key] = 'text-align:'. fix_align_rtl($aa) .';';  // Fix for RTL languages
                } else {
                    $this->align[$key] = '';
                }
            }
        }
        if (!empty($this->size)) {
            foreach ($this->size as $key => $ss) {
                if ($ss) {
                    $this->size[$key] = 'width:'. $ss .';';
                } else {
                    $this->size[$key] = '';
                }
            }
        }
        if (!empty($this->wrap)) {
            foreach ($this->wrap as $key => $ww) {
                if ($ww) {
                    $this->wrap[$key] = 'white-space:nowrap;';
                } else {
                    $this->wrap[$key] = '';
                }
            }
        }
        if (!empty($this->head)) {
            foreach ($this->head as $key => $val) {
                if (!isset($this->align[$key])) {
                    $this->align[$key] = '';
                }
                if (!isset($this->size[$key])) {
                    $this->size[$key] = '';
                }
                if (!isset($this->wrap[$key])) {
                    $this->wrap[$key] = '';
                }

            }
        }
        if (empty($this->classes)) { // must be done before align
            $this->set_classes(array('generaltable'));
        }
        if (!empty($this->tablealign)) {
            $this->add_class('boxalign' . $this->tablealign);
        }
        if (!empty($this->rotateheaders)) {
            $this->add_class('rotateheaders');
        } else {
            $this->rotateheaders = false; // Makes life easier later.
        }
        parent::prepare();
    }
    /**
     * @param string $name The name of the variable to set
     * @param mixed $value The value to assign to the variable
     * @return void
     */
    public function __set($name, $value) {
        if ($name == 'rowclass') {
            debugging('rowclass[] has been deprecated for html_table ' .
                      'and should be replaced with rowclasses[]. please fix the code.');
            $this->rowclasses = $value;
        } else {
            parent::__set($name, $value);
        }
    }
}

/**
 * Component representing a table row.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table_row extends moodle_html_component {
    /**
     * @var array $cells Array of html_table_cell objects
     */
    public $cells = array();

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
    }

    /**
     * Shortcut method for creating a row with an array of cells. Converts cells to html_table_cell objects.
     * @param array $cells
     * @return html_table_row
     */
    public function make($cells=array()) {
        $row = new html_table_row();
        foreach ($cells as $celltext) {
            if (!($celltext instanceof html_table_cell)) {
                $cell = new html_table_cell();
                $cell->text = $celltext;
                $row->cells[] = $cell;
            } else {
                $row->cells[] = $celltext;
            }
        }
        return $row;
    }
}

/**
 * Component representing a table cell.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table_cell extends moodle_html_component {
    /**
     * @var string $text The contents of the cell
     */
    public $text;
    /**
     * @var string $abbr Abbreviated version of the contents of the cell
     */
    public $abbr = '';
    /**
     * @var int $colspan Number of columns this cell should span
     */
    public $colspan = '';
    /**
     * @var int $rowspan Number of rows this cell should span
     */
    public $rowspan = '';
    /**
     * @var string $scope Defines a way to associate header cells and data cells in a table
     */
    public $scope = '';
    /**
     * @var boolean $header Whether or not this cell is a header cell
     */
    public $header = null;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        if ($this->header && empty($this->scope)) {
            $this->scope = 'col';
        }
        parent::prepare();
    }
}

/**
 * Component representing a XHTML link.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_link extends moodle_html_component {
    /**
     * URL can be simple text or a moodle_url object
     * @var mixed $url
     */
    public $url;

    /**
     * @var string $text The text that will appear between the link tags
     */
    public $text;

    /**
     * @var boolean $disabled Whether or not this link is disabled (will be rendered as plain text)
     */
    public $disabled = false;

    /**
     * @var boolean $disableifcurrent Whether or not this link should be disabled if it the same as the current page
     */
    public $disableifcurrent = false;

    /**
     * @see lib/moodle_html_component#prepare() Disables the link if it links to the current page.
     * @return void
     */
    public function prepare() {
        global $PAGE;
        // We can't accept an empty text value
        if (empty($this->text)) {
            throw new coding_exception('A html_link must have a descriptive text value!');
        }

        if (!($this->url instanceof moodle_url)) {
            $this->url = new moodle_url($this->url);
        }

        if ($this->url->compare($PAGE->url, URL_MATCH_PARAMS) && $this->disableifcurrent) {
            $this->disabled = true;
        }
        parent::prepare();
    }

    /**
     * Shortcut for creating a link component.
     * @param mixed  $url String or moodle_url
     * @param string $text The text of the link
     * @return html_link The link component
     */
    public function make($url, $text) {
        $link = new html_link();
        $link->url = $url;
        $link->text = $text;

        return $link;
    }
}

/**
 * Component representing a XHTML button (input of type 'button').
 * The renderer will either output it as a button with an onclick event,
 * or as a form with hidden inputs.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_button extends labelled_html_component {
    /**
     * @var string $text
     */
    public $text;

    /**
     * @var boolean $disabled Whether or not this button is disabled
     */
    public $disabled = false;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->add_class('singlebutton');

        if (empty($this->text)) {
            $this->text = get_string('submit');
        }

        if ($this->disabled) {
            $this->disabled = 'disabled';
        }

        parent::prepare();
    }
}

/**
 * Component representing an image.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_image extends labelled_html_component {
    /**
     * @var string $alt A descriptive text
     */
    public $alt = HTML_ATTR_EMPTY;
    /**
     * @var string $src The path to the image being used
     */
    public $src;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        if (empty($this->src)) {
            throw new coding_exception('html_image requires a $src value (moodle_url).');
        }

        $this->add_class('image');
        parent::prepare();
    }

    /**
     * Shortcut for initialising a html_image.
     *
     * @param mixed $url The URL to the image (string or moodle_url)
     */
    public function make($url) {
        $image = new html_image();
        $image->src = $url;
        return $image;
    }
}

/**
 * Component representing a textarea.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_textarea extends moodle_html_component {
    /**
     * @param string $name Name to use for the textarea element.
     */
    public $name;
    /**
     * @param string $value Initial content to display in the textarea.
     */
    public $value;
    /**
     * @param int $rows Number of rows to display  (minimum of 10 when $height is non-null)
     */
    public $rows;
    /**
     * @param int $cols Number of columns to display (minimum of 65 when $width is non-null)
     */
    public $cols;
    /**
     * @param bool $usehtmleditor Enables the use of the htmleditor for this field.
     */
    public $usehtmleditor;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->add_class('form-textarea');

        if (empty($this->id)) {
            $this->id = "edit-$this->name";
        }

        if ($this->usehtmleditor) {
            editors_head_setup();
            $editor = get_preferred_texteditor(FORMAT_HTML);
            $editor->use_editor($this->id, array('legacy'=>true));
            $this->value = htmlspecialchars($value);
        }

        parent::prepare();
    }
}

/**
 * Component representing a simple form wrapper. Its purpose is mainly to enclose
 * a submit input with the appropriate action and hidden inputs.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_form extends moodle_html_component {
    /**
     * @var string $method post or get
     */
    public $method = 'post';
    /**
     * If a string is given, it will be converted to a moodle_url during prepare()
     * @var mixed $url A moodle_url including params or a string
     */
    public $url;
    /**
     * @var array $params Optional array of parameters. Ignored if $url instanceof moodle_url
     */
    public $params = array();
    /**
     * @var boolean $showbutton If true, the submit button will always be shown even if JavaScript is available
     */
    public $showbutton = false;
    /**
     * @var string $targetwindow The name of the target page to open the linked page in.
     */
    public $targetwindow = 'self';
    /**
     * @var html_button $button A submit button
     */
    public $button;
    /**
     * @var boolean $jssubmitaction If true, the submit button will be hidden when JS is enabled
     */
    public $jssubmitaction = false;
    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->button = new html_button();
        $this->button->text = get_string('yes');
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {

        if (empty($this->url)) {
            throw new coding_exception('A html_form must have a $url value (string or moodle_url).');
        }

        if (!($this->url instanceof moodle_url)) {
            $this->url = new moodle_url($this->url, $this->params);
        }

        if ($this->method == 'post') {
            $this->url->param('sesskey', sesskey());
        }

        parent::prepare();
    }

    public static function make_button($url, $options, $label='OK', $method='post') {
        $form = new html_form();
        $form->url = new moodle_url($url, $options);
        $form->button->text = $label;
        $form->method = $method;
        return $form;
    }
}

/**
 * Component representing a list.
 *
 * The advantage of using this object instead of a flat array is that you can load it
 * with metadata (CSS classes, event handlers etc.) which can be used by the renderers.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_list extends moodle_html_component {

    /**
     * @var array $items An array of html_list_item or html_list objects
     */
    public $items = array();

    /**
     * @var string $type The type of list (ordered|unordered), definition type not yet supported
     */
    public $type = 'unordered';

    /**
     * @var string $text An optional descriptive text for the list. Will be output as a list item before opening the new list
     */
    public $text = false;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
    }

    /**
     * This function takes a nested array of data and maps it into this list's $items array
     * as proper html_list_item and html_list objects, with appropriate metadata.
     *
     * @param array $tree A nested array (array keys are ignored);
     * @param int $row Used in identifying the iteration level and in ul classes
     * @return void
     */
    public function load_data($tree, $level=0) {

        $this->add_class("list-$level");

        $i = 1;
        foreach ($tree as $key => $element) {
            if (is_array($element)) {
                $newhtmllist = new html_list();
                $newhtmllist->type = $this->type;
                $newhtmllist->load_data($element, $level + 1);
                $newhtmllist->text = $key;
                $this->items[] = $newhtmllist;
            } else {
                $listitem = new html_list_item();
                $listitem->value = $element;
                $listitem->add_class("list-item-$level-$i");
                $this->items[] = $listitem;
            }
            $i++;
        }
    }

    /**
     * Adds a html_list_item or html_list to this list.
     * If the param is a string, a html_list_item will be added.
     * @param mixed $item String, html_list or html_list_item object
     * @return void
     */
    public function add_item($item) {
        if ($item instanceof html_list_item || $item instanceof html_list) {
            $this->items[] = $item;
        } else {
            $listitem = new html_list_item();
            $listitem->value = $item;
            $this->items[] = $item;
        }
    }
}

/**
 * Component representing a list item.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_list_item extends moodle_html_component {
    /**
     * @var string $value The value of the list item
     */
    public $value;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
    }
}

/**
 * Component representing a span element. It has no special attributes, so
 * it is very low-level and can be used for styling and JS actions.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_span extends moodle_html_component {
    /**
     * @var string $text The contents of the span
     */
    public $contents;
    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
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
class moodle_paging_bar extends moodle_html_component {
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
    public $page = 0;
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
    public $pagevar = 'page';
    /**
     * @var html_link $previouslink A HTML link representing the "previous" page
     */
    public $previouslink = null;
    /**
     * @var html_link $nextlink A HTML link representing the "next" page
     */
    public $nextlink = null;
    /**
     * @var html_link $firstlink A HTML link representing the first page
     */
    public $firstlink = null;
    /**
     * @var html_link $lastlink A HTML link representing the last page
     */
    public $lastlink = null;
    /**
     * @var array $pagelinks An array of html_links. One of them is just a string: the current page
     */
    public $pagelinks = array();

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        if (!isset($this->totalcount) || is_null($this->totalcount)) {
            throw new coding_exception('moodle_paging_bar requires a totalcount value.');
        }
        if (!isset($this->page) || is_null($this->page)) {
            throw new coding_exception('moodle_paging_bar requires a page value.');
        }
        if (empty($this->perpage)) {
            throw new coding_exception('moodle_paging_bar requires a perpage value.');
        }
        if (empty($this->baseurl)) {
            throw new coding_exception('moodle_paging_bar requires a baseurl value.');
        }
        if (!($this->baseurl instanceof moodle_url)) {
            $this->baseurl = new moodle_url($this->baseurl);
        }

        if ($this->totalcount > $this->perpage) {
            $pagenum = $this->page - 1;

            if ($this->page > 0) {
                $this->previouslink = new html_link();
                $this->previouslink->add_class('previous');
                $this->previouslink->url = clone($this->baseurl);
                $this->previouslink->url->param($this->pagevar, $pagenum);
                $this->previouslink->text = get_string('previous');
            }

            if ($this->perpage > 0) {
                $lastpage = ceil($this->totalcount / $this->perpage);
            } else {
                $lastpage = 1;
            }

            if ($this->page > 15) {
                $startpage = $this->page - 10;

                $this->firstlink = new html_link();
                $this->firstlink->url = clone($this->baseurl);
                $this->firstlink->url->param($this->pagevar, 0);
                $this->firstlink->text = 1;
                $this->firstlink->add_class('first');
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
                    $pagelink = new html_link();
                    $pagelink->url = clone($this->baseurl);
                    $pagelink->url->param($this->pagevar, $currpage);
                    $pagelink->text = $displaypage;
                    $this->pagelinks[] = $pagelink;
                }

                $displaycount++;
                $currpage++;
            }

            if ($currpage < $lastpage) {
                $lastpageactual = $lastpage - 1;
                $this->lastlink = new html_link();
                $this->lastlink->url = clone($this->baseurl);
                $this->lastlink->url->param($this->pagevar, $lastpageactual);
                $this->lastlink->text = $lastpage;
                $this->lastlink->add_class('last');
            }

            $pagenum = $this->page + 1;

            if ($pagenum != $displaypage) {
                $this->nextlink = new html_link();
                $this->nextlink->url = clone($this->baseurl);
                $this->nextlink->url->param($this->pagevar, $pagenum);
                $this->nextlink->text = get_string('next');
                $this->nextlink->add_class('next');
            }
        }
    }

    /**
     * Shortcut for initialising a moodle_paging_bar with only the required params.
     *
     * @param int $totalcount Thetotal number of entries available to be paged through
     * @param int $page The page you are currently viewing
     * @param int $perpage The number of entries that should be shown per page
     * @param mixed $baseurl If this  is a string then it is the url which will be appended with $pagevar, an equals sign and the page number.
     *                          If this is a moodle_url object then the pagevar param will be replaced by the page no, for each page.
     */
    public function make($totalcount, $page, $perpage, $baseurl) {
        $pagingbar = new moodle_paging_bar();
        $pagingbar->totalcount = $totalcount;
        $pagingbar->page = $page;
        $pagingbar->perpage = $perpage;
        $pagingbar->baseurl = $baseurl;
        return $pagingbar;
    }
}

/**
 * Component representing a user picture.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_user_picture extends moodle_html_component {
    /**
     * @var mixed $user A userid or a user object with at least fields id, picture, imagealrt, firstname and lastname set.
     */
    public $user;
    /**
     * @var int $courseid The course id. Used when constructing the link to the user's profile.
     */
    public $courseid;
    /**
     * @var html_image $image A custom image used as the user picture.
     */
    public $image;
    /**
     * @var mixed $url False: picture not enclosed in a link. True: default link. moodle_url: custom link.
     */
    public $url;
    /**
     * @var int $size Size in pixels. Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatibility
     */
    public $size;
    /**
     * @var boolean $alttext add non-blank alt-text to the image. (Default true, set to false for purely
     */
    public $alttext = true;
    /**
     * @var boolean $popup Whether or not to open the link in a popup window
     */
    public $popup = false;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->image = new html_image();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        global $CFG, $DB, $OUTPUT;

        if (empty($this->user)) {
            throw new coding_exception('A moodle_user_picture object must have a $user object before being rendered.');
        }

        if (empty($this->courseid)) {
            throw new coding_exception('A moodle_user_picture object must have a courseid value before being rendered.');
        }

        if (!($this->image instanceof html_image)) {
            debugging('moodle_user_picture::image must be an instance of html_image', DEBUG_DEVELOPER);
        }

        $needrec = false;
        // only touch the DB if we are missing data...
        if (is_object($this->user)) {
            // Note - both picture and imagealt _can_ be empty
            // what we are trying to see here is if they have been fetched
            // from the DB. We should use isset() _except_ that some installs
            // have those fields as nullable, and isset() will return false
            // on null. The only safe thing is to ask array_key_exists()
            // which works on objects. property_exists() isn't quite
            // what we want here...
            if (! (array_key_exists('picture', $this->user)
                   && ($this->alttext && array_key_exists('imagealt', $this->user)
                       || (isset($this->user->firstname) && isset($this->user->lastname)))) ) {
                $needrec = true;
                $this->user = $this->user->id;
            }
        } else {
            if ($this->alttext) {
                // we need firstname, lastname, imagealt, can't escape...
                $needrec = true;
            } else {
                $userobj = new StdClass; // fake it to save DB traffic
                $userobj->id = $this->user;
                $userobj->picture = $this->image->src;
                $this->user = clone($userobj);
                unset($userobj);
            }
        }
        if ($needrec) {
            $this->user = $DB->get_record('user', array('id' => $this->user), 'id,firstname,lastname,imagealt');
        }

        if ($this->url === true) {
            $this->url = new moodle_url('/user/view.php', array('id' => $this->user->id, 'course' => $this->courseid));
        }

        if (!empty($this->url) && $this->popup) {
            $this->add_action(new popup_action('click', $this->url));
        }

        if (empty($this->size)) {
            $file = 'f2';
            $this->size = 35;
        } else if ($this->size === true or $this->size == 1) {
            $file = 'f1';
            $this->size = 100;
        } else if ($this->size >= 50) {
            $file = 'f1';
        } else {
            $file = 'f2';
        }

        if (!empty($this->size)) {
            $this->image->width = $this->size;
            $this->image->height = $this->size;
        }

        $this->add_class('userpicture');

        if (empty($this->image->src) && !empty($this->user->picture)) {
            $this->image->src = $this->user->picture;
        }

        if (!empty($this->image->src)) {
            require_once($CFG->libdir.'/filelib.php');
            $this->image->src = new moodle_url(get_file_url($this->user->id.'/'.$file.'.jpg', null, 'user'));
        } else { // Print default user pictures (use theme version if available)
            $this->add_class('defaultuserpic');
            $this->image->src = $OUTPUT->old_icon_url('u/' . $file);
        }

        if ($this->alttext) {
            if (!empty($this->user->imagealt)) {
                $this->image->alt = $this->user->imagealt;
            } else {
                // No alt text when there is nothing useful (accessibility)
                $this->image->alt = HTML_ATTR_EMPTY;
            }
        }

        parent::prepare();
    }

    public static function make($user, $courseid) {
        $userpic = new moodle_user_picture();
        $userpic->user = $user;
        $userpic->courseid = $courseid;
        return $userpic;
    }
}

/**
 * Component representing a help icon.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_help_icon extends labelled_html_component {
    /**
     * @var html_link $link A html_link object that will hold the URL info
     */
    public $link;
    /**
     * @var string $text A descriptive text
     */
    public $text;
    /**
     * @var string $page  The keyword that defines a help page
     */
    public $page;
    /**
     * @var string $module Which module is the page defined in
     */
    public $module = 'moodle';
    /**
     * @var boolean $linktext Whether or not to show text next to the icon
     */
    public $linktext = false;
    /**
     * @var mixed $image The help icon. Can be set to true (will use default help icon),
     *                   false (will not use any icon), the URL to an image, or a full
     *                   html_image object.
     */
    public $image;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->link = new html_link();
        $this->image = new html_image();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        global $COURSE, $OUTPUT, $CFG;

        if (empty($this->page)) {
            throw new coding_exception('A moodle_help_icon object requires a $page parameter');
        }

        if (empty($this->text) && empty($this->link->text)) {
            throw new coding_exception('A moodle_help_icon object (or its $link attribute) requires a $text parameter');
        } else if (!empty($this->text) && empty($this->link->text)) {
            $this->link->text = $this->text;
        }

        // fix for MDL-7734
        $this->link->url = new moodle_url($CFG->wwwroot.'/help.php', array('module' => $this->module, 'file' => $this->page .'.html'));

        // fix for MDL-7734
        if (!empty($COURSE->lang)) {
            $this->link->url->param('forcelang', $COURSE->lang);
        }

        // Catch references to the old text.html and emoticons.html help files that
        // were renamed in MDL-13233.
        if (in_array($this->page, array('text', 'emoticons', 'richtext'))) {
            $oldname = $this->page;
            $this->page .= '2';
            debugging("You are referring to the old help file '$oldname'. " .
                    "This was renamed to '$this->page' because of MDL-13233. " .
                    "Please update your code.", DEBUG_DEVELOPER);
        }

        if ($this->module == '') {
            $this->module = 'moodle';
        }

        // Warn users about new window for Accessibility
        $this->title = get_string('helpprefix2', '', trim($this->text, ". \t")) .' ('.get_string('newwindow').')';

        // Prepare image and linktext
        if ($this->image && !($this->image instanceof html_image)) {
            $image = fullclone($this->image);
            $this->image = new html_image();

            if ($image instanceof moodle_url) {
                $this->image->src = $image->out();
            } else if ($image === true) {
                $this->image->src = $OUTPUT->old_icon_url('help');
            } else if (is_string($image)) {
                $this->image->src = $image;
            }
            $this->image->alt = $this->text;

            if ($this->linktext) {
                $this->image->alt = get_string('helpwiththis');
            } else {
                $this->image->alt = $this->title;
            }
            $this->image->add_class('iconhelp');
        } else if (empty($this->image->src)) {
            if (!($this->image instanceof html_image)) {
                $this->image = new html_image();
            }
            $this->image->src = $OUTPUT->old_icon_url('help');
        }

        parent::prepare();
    }

    /**
     * This is a shortcut for creating a help_icon with only the 2 required params
     * @param string $page  The keyword that defines a help page
     * @param string $text A descriptive text
     * @return moodle_help_icon A moodle_help_icon object with the two common fields initialised.
     */
    public static function make($page, $text, $module='moodle', $linktext=false) {
        $helpicon = new moodle_help_icon();
        $helpicon->page = $page;
        $helpicon->text = $text;
        $helpicon->module = $module;
        $helpicon->linktext = $linktext;
        return $helpicon;
    }

    public static function make_scale_menu($courseid, $scale) {
        $helpbutton = new moodle_help_icon();
        $strscales = get_string('scales');
        $helpbutton->image->alt = $scale->name;
        $helpbutton->link->url = new moodle_url('/course/scales.php', array('id' => $courseid, 'list' => true, 'scaleid' => $scale->id));
        $popupaction = new popup_action('click', $helpbutton->url, 'ratingscale', $popupparams);
        $popupaction->width = 500;
        $popupaction->height = 400;
        $helpbutton->link->add_action($popupaction);
        $helpbutton->link->title = $scale->name;
        return $helpbutton;
    }
}

/**
 * Component representing an icon linking to a Moodle page.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_action_icon extends labelled_html_component {
    /**
     * @var string $linktext Optional text to display next to the icon
     */
    public $linktext;
    /**
     * @var html_image $image The icon
     */
    public $image;
    /**
     * @var html_link $link The link
     */
    public $link;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->image = new html_image();
        $this->link = new html_link();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->image->add_class('action-icon');

        if (!empty($this->actions)) {
            foreach ($this->actions as $action) {
                $this->link->add_action($action);
            }
            unset($this->actions);
        }

        parent::prepare();

        if (empty($this->image->src)) {
            throw new coding_exception('moodle_action_icon->image->src must not be empty');
        }

        if (empty($this->image->alt) && !empty($this->linktext)) {
            $this->image->alt = $this->linktext;
        } else if (empty($this->image->alt)) {
            debugging('moodle_action_icon->image->alt should not be empty.', DEBUG_DEVELOPER);
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
class block_contents extends moodle_html_component {
    /** @var int used to set $skipid. */
    protected static $idcounter = 1;

    const NOT_HIDEABLE = 0;
    const VISIBLE = 1;
    const HIDDEN = 2;

    /**
     * @param integer $skipid All the blocks (or things that look like blocks)
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
    public $attributes = array();

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
     * $icon is the icon name. Fed to $OUTPUT->old_icon_url.
     * @var array
     */
    public $controls = array();

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        $this->skipid = self::$idcounter;
        self::$idcounter += 1;
        $this->add_class('sideblock');
        if (empty($this->blockinstanceid) || !strip_tags($this->title)) {
            $this->collapsible = self::NOT_HIDEABLE;
        }
        if ($this->collapsible == self::HIDDEN) {
            $this->add_class('hidden');
        }
        if (!empty($this->controls)) {
            $this->add_class('block_with_controls');
        }
        parent::prepare();
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
class block_move_target extends moodle_html_component {
    /**
     * List of hidden form fields.
     * @var array
     */
    public $url = array();
    /**
     * List of hidden form fields.
     * @var array
     */
    public $text = '';
}
