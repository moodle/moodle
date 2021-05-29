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
 * @package   local_framework_selector
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   standard Moodle framework_selector
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The default size of a framework selector.
 */
define('FRAMEWORK_SELECTOR_DEFAULT_ROWS', 20);

/**
 * Base class for framework selectors.
 *
 * In your theme, you must give each framework-selector a defined width. If the
 * framework selector has name="myid", then the div myid_wrapper must have a width
 * specified.
 */
abstract class framework_selector_base {
    /** @var string The control name (and id) in the HTML. */
    protected $name;
    /** @var array Extra fields to search on and return in addition to firstname and lastname. */
    protected $extrafields;
    /** @var boolean Whether the conrol should allow selection of many frameworks, or just one. */
    protected $multiselect = true;
    /** @var int The height this control should have, in rows. */
    protected $rows = FRAMEWORK_SELECTOR_DEFAULT_ROWS;
    /** @var array A list of frameworkids that should not be returned by this control. */
    protected $exclude = array();
    /** @var array|null A list of the frameworks who are selected. */
    protected $selected = null;
    /** @var boolean When the search changes, do we keep previously selected options that do
     * not match the new search term? */
    protected $preserveselected = false;
    /** @var boolean If only one framework matches the search, should we select them automatically. */
    protected $autoselectunique = false;
    /** @var boolean When searching, do we only match the starts of fields (better performance)
     * or do we match occurrences anywhere? */
    protected $searchanywhere = false;
    /** @var mixed This is used by get selected frameworks */
    protected $validatingframeworkids = null;

    protected $file = null;
    protected $selectedid = 0;

    //defines the base required fields for this selector.
    protected $requiredfields = array('id', 'fullname');

    /**  @var boolean Used to ensure we only output the search options for one framework selector on
     * each page. */
    private static $searchoptionsoutput = false;

    /** @var array JavaScript YUI3 Module definition */
    protected static $jsmodule = array(
                'name' => 'framework_selector',
                'fullpath' => '/local/framework_selector/module.js',
                'requires'  => array('node', 'event-custom', 'datasource', 'json'),
                'strings' => array(
                    array('previouslyselectedframeworks', 'local_framework_selector', '%%SEARCHTERM%%'),
                    array('nomatchingframeworks', 'local_framework_selector', '%%SEARCHTERM%%'),
                    array('none')
                ));

    // Public API.

    /**
     * Constructor. Each subclass must have a constructor with this signature.
     *
     * @param string $name the control name/id for use in the HTML.
     * @param array $options other options needed to construct this selector.
     * You must be able to clone a frameworkselector by doing
     * new get_class($us)($us->get_name(), $us->get_options());
     */
    public function __construct($name, $options = array()) {
        global $CFG, $PAGE;

        // Initialise member variables from constructor arguments.
        $this->name = $name;
        if (isset($options['extrafields'])) {
            $this->extrafields = $options['extrafields'];
        } else if (!empty($CFG->extraframeworkselectorfields)) {
            $this->extrafields = explode(',', $CFG->extraframeworkselectorfields);
        } else {
            $this->extrafields = array();
        }
        if (isset($options['exclude']) && is_array($options['exclude'])) {
            $this->exclude = $options['exclude'];
        }
        if (isset($options['multiselect'])) {
            $this->multiselect = $options['multiselect'];
        }
        if (isset($options['file'])) {
            $this->file = $options['file'];
        }
        if (isset($options['selected'])) {
            $this->selected = $options['selected'];
        }
        if (isset($options['selectedid'])) {
            $this->selectedid = $options['selectedid'];
        }
        // Read the user prefs / optional_params that we use.
        $this->preserveselected = $this->initialise_option('frameworkselector_preserveselected',
                                                            $this->preserveselected);
        $this->autoselectunique = $this->initialise_option('frameworkselector_autoselectunique',
                                                            $this->autoselectunique);
        $this->searchanywhere = $this->initialise_option('frameworkselector_searchanywhere',
                                                          $this->searchanywhere);
    }

    /**
     * All to the list of framework ids that this control will not select. For example,
     * on the role assign page, we do not list the frameworks who already have the role
     * in question.
     *
     * @param array $arrayofframeworkids the framework ids to exclude.
     */
    public function exclude($arrayofframeworkids) {
        $this->exclude = array_unique(array_merge($this->exclude, $arrayofframeworkids));
    }

    /**
     * Clear the list of excluded framework ids.
     */
    public function clear_exclusions() {
        $exclude = array();
    }

    /**
     * @return array the list of framework ids that this control will not select.
     */
    public function get_exclusions() {
        return clone($this->exclude);
    }

    /**
     * @return array of framework objects. The frameworks that were selected. This is a
     * more sophisticated version of
     * optional_param($this->name, array(), PARAM_INTEGER) that validates the
     * returned list of ids against the rules for this framework selector.
     */
    public function get_selected_frameworks() {
        // Do a lazy load.
        if (is_null($this->selected)) {
            $this->selected = $this->load_selected_frameworks();
        }
        return $this->selected;
    }

    /**
     * Convenience method for when multiselect is false (throws an exception if not).
     * @return object the selected framework object, or null if none.
     */
    public function get_selected_framework() {
        if ($this->multiselect) {
            throw new moodle_exception('cannotcallusgetselectedframework', 'local_framework_selector');
        }
        $frameworks = $this->get_selected_frameworks();
        if (count($frameworks) == 1) {
            return reset($frameworks);
        } else if (count($frameworks) == 0) {
            return null;
        } else {
            throw new moodle_exception('frameworkselectortoomany', 'local_framework_selector');
        }
    }

    /**
     * If you update the database in such a way that it is likely to change the
     * list of frameworks that this component is allowed to select from, then you
     * must call this method. For example, on the role assign page, after you have
     * assigned some roles to some frameworks, you should call this.
     */
    public function invalidate_selected_frameworks() {
        $this->selected = null;
    }

    /**
     * Output this framework_selector as HTML.
     * @param boolean $return if true, return the HTML as a string instead of outputting it.
     * @return mixed if $return is true, returns the HTML as a string, otherwise returns nothing.
     */
    public function display($return = false) {
        global $PAGE;

        // Get the list of requested frameworks.
        $search = optional_param($this->name . '_searchtext', '', PARAM_RAW);
        if (optional_param($this->name . '_clearbutton', false, PARAM_BOOL)) {
            $search = '';
        }
        $groupedframeworks = $this->find_frameworks($search);

        // Output the select.
        $name = $this->name;
        $multiselect = '';
        if ($this->multiselect) {
            $name .= '[]';
            $multiselect = 'multiple="multiple" ';
        }
        $output = '<div class="frameworkselector" id="' . $this->name . '_wrapper">' . "\n" .
                '<select name="' . $name . '" id="' . $this->name . '" ' .
                $multiselect . 'size="' . $this->rows . '">' . "\n";

        // Populate the select.
        $output .= $this->output_options($groupedframeworks, $search);

        // Output the search controls.
        $output .= "</select>\n<div>\n";
        $output .= '<input type="text" name="' . $this->name . '_searchtext" id="' .
                $this->name . '_searchtext" size="15" value="' . s($search) . '" />';
        $output .= '<input type="submit" name="' . $this->name . '_searchbutton" id="' .
                $this->name . '_searchbutton" value="' . $this->search_button_caption() . '" />';
        $output .= '<input type="submit" name="' . $this->name . '_clearbutton" id="' .
                $this->name . '_clearbutton" value="' . get_string('clear') . '" />';

        // And the search options.
        $optionsoutput = false;
        if (!self::$searchoptionsoutput) {
            $output .= print_collapsible_region_start('', 'frameworkselector_options',
                    get_string('searchoptions', 'local_framework_selector'),
                               'frameworkselector_optionscollapsed', true, true);
            $output .= $this->option_checkbox('preserveselected', $this->preserveselected,
                    get_string('frameworkselectorpreserveselected', 'local_framework_selector'));
            $output .= $this->option_checkbox('autoselectunique', $this->autoselectunique,
                    get_string('frameworkselectorautoselectunique', 'local_framework_selector'));
            $output .= $this->option_checkbox('searchanywhere', $this->searchanywhere,
                    get_string('frameworkselectorsearchanywhere', 'local_framework_selector'));
            $output .= print_collapsible_region_end(true);

            $PAGE->requires->js_init_call('M.local_framework_selector.init_framework_selector_options_tracker',
                                          array(), false, self::$jsmodule);
            self::$searchoptionsoutput = true;
        }
        $output .= "</div>\n</div>\n\n";

        // Initialise the ajax functionality.
        $output .= $this->initialise_javascript($search);

        // Return or output it.
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * The height this control will be displayed, in rows.
     *
     * @param integer $numrows the desired height.
     */
    public function set_rows($numrows) {
        $this->rows = $numrows;
    }

    /**
     * @return integer the height this control will be displayed, in rows.
     */
    public function get_rows() {
        return $this->rows;
    }

    /**
     * Whether this control will allow selection of many, or just one framework.
     *
     * @param boolean $multiselect true = allow multiple selection.
     */
    public function set_multiselect($multiselect) {
        $this->multiselect = $multiselect;
    }

    /**
     * @return boolean whether this control will allow selection of more than one framework.
     */
    public function is_multiselect() {
        return $this->multiselect;
    }

    /**
     * @return string the id/name that this control will have in the HTML.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the framework fields that are displayed in the selector in addition to the
     * framework's name.
     *
     * @param array $fields a list of field names that exist in the framework table.
     */
    public function set_extra_fields($fields) {
        $this->extrafields = $fields;
    }

    // API for sublasses.

    /**
     * Search the database for frameworks matching the $search string, and any other
     * conditions that apply. The SQL for testing whether a framework matches the
     * search string should be obtained by calling the search_sql method.
     *
     * This method is used both when getting the list of choices to display to
     * the framework, and also when validating a list of frameworks that was selected.
     *
     * When preparing a list of frameworks to choose from ($this->is_validating()
     * return false) you should probably have an maximum number of frameworks you will
     * return, and if more frameworks than this match your search, you should instead
     * return a message generated by the too_many_results() method. However, you
     * should not do this when validating.
     *
     * If you are writing a new framework_selector subclass, I strongly recommend you
     * look at some of the subclasses later in this file and in admin/roles/lib.php.
     * They should help you see exactly what you have to do.
     *
     * @param string $search the search string.
     * @return array An array of arrays of frameworks. The array keys of the outer
     *      array should be the string names of optgroups. The keys of the inner
     *      arrays should be frameworkids, and the values should be framework objects
     *      containing at least the list of fields returned by the method
     *      required_fields_sql(). If a framework object has a ->disabled property
     *      that is true, then that option will be displayed greyed out, and
     *      will not be returned by get_selected_frameworks.
     */
    public abstract function find_frameworks($search);

    /**
     *
     * Note: this function must be implemented if you use the search ajax field
     *       (e.g. set $options['file'] = '/admin/filecontainingyourclass.php';)
     * @return array the options needed to recreate this framework_selector.
     */
    protected function get_options() {
        return array(
            'class' => get_class($this),
            'name' => $this->name,
            'exclude' => $this->exclude,
            'extrafields' => $this->extrafields,
            'multiselect' => $this->multiselect,
            'file' => $this->file,
            'selectedid' => $this->selectedid
        );
    }

    // Inner workings.

    /**
     * @return boolean if true, we are validating a list of selected frameworks,
     *      rather than preparing a list of uesrs to choose from.
     */
    protected function is_validating() {
        return !is_null($this->validatingframeworkids);
    }

    /**
     * Get the list of frameworks that were selected by doing optional_param then
     * validating the result.
     *
     * @return array of framework objects.
     */
    protected function load_selected_frameworks() {
        // See if we got anything.
        if (!$this->multiselect) {
            $frameworkids = optional_param($this->name, null, PARAM_INTEGER);
            if (empty($frameworkids)) {
                return array();
            } else {
                $frameworkids = array($frameworkids);
            }
        } else {
            $frameworkids = optional_param_array($this->name, array(), PARAM_INTEGER);
            if (empty($frameworkids)) {
                return array();
            }
        }

        // If we did, use the find_frameworks method to validate the ids.
        $this->validatingframeworkids = $frameworkids;
        $groupedframeworks = $this->find_frameworks('');
        $this->validatingframeworkids = null;

        // Aggregate the resulting list back into a single one.
        $frameworks = array();
        foreach ($groupedframeworks as $group) {
            foreach ($group as $framework) {
                if (!isset($frameworks[$framework->id]) && empty($framework->disabled)
                    && in_array($framework->id, $frameworkids)) {
                    $frameworks[$framework->id] = $framework;
                }
            }
        }

        // If we are only supposed to be selecting a single framework, make sure we do.
        if (!$this->multiselect && count($frameworks) > 1) {
            $frameworks = array_slice($frameworks, 0, 1);
        }

        return $frameworks;
    }

    /**
     * @param string $u the table alias for the framework table in the query being
     *      built. May be ''.
     * @return string fragment of SQL to go in the select list of the query.
     */
    protected function required_fields_sql($cf = '') {
        // Raw list of fields.
        $fields = (array) $this->requiredfields;
        $fields = array_merge($fields, $this->extrafields);

        // Prepend the table alias.
        if ($cf) {
            foreach ($fields as &$field) {
                $field = $cf . '.' . $field;
            }
        }

        return implode(',', $fields);
    }

    /**
     * @param string $search the text to search for.
     * @param string $u the table alias for the framework table in the query being
     *      built. May be ''.
     * @return array an array with two elements, a fragment of SQL to go in the
     *      where clause the query, and an array containing any required parameters.
     *      this uses ? style placeholders.
     */
    protected function search_sql($search, $cf = '') {
        global $DB, $CFG;
        $params = array();
        $tests = array();

        if (!empty($cf)) {
            $cf .= '.';
        }

        // If we have a $search string, put a field LIKE '$search%' condition on each field.
        if ($search) {
            $conditions = array();
            $conditions[] = $cf . 'shortname';
            foreach ($this->extrafields as $field) {
                $conditions[] = $field;
            }
            if ($this->searchanywhere) {
                $searchparam = '%' . $search . '%';
            } else {
                $searchparam = $search . '%';
            }
            $i = 0;
            foreach ($conditions as $key => $condition) {
                $conditions[$key] = $DB->sql_like($condition, ":con{$i}00", false, false);
                $params["con{$i}00"] = $searchparam;
                $i++;
            }
            $tests[] = '(' . implode(' OR ', $conditions) . ')';
        }

        // If we are being asked to exclude any frameworks, do that.
        if (!empty($this->exclude)) {
            list($frameworktest, $frameworkparams) = $DB->get_in_or_equal($this->exclude,
                                               SQL_PARAMS_NAMED, 'ex000', false);
            $tests[] = $cf . 'id ' . $frameworktest;
            $params = array_merge($params, $frameworkparams);
        }

        // If we are validating a set list of frameworkids, add an id IN (...) test.
        if (!empty($this->validatingframeworkids)) {
            list($frameworktest, $frameworkparams) = $DB->get_in_or_equal($this->validatingframeworkids,
                                               SQL_PARAMS_NAMED, 'val000');
            $tests[] = $cf . 'id ' . $frameworktest;
            $params = array_merge($params, $frameworkparams);
        }

        if (empty($tests)) {
            $tests[] = '1 = 1';
        }

        // Combing the conditions and return.
        return array(implode(' AND ', $tests), $params);
    }

    /**
     * Used to generate a nice message when there are too many frameworks to show.
     * The message includes the number of frameworks that currently match, and the
     * text of the message depends on whether the search term is non-blank.
     *
     * @param string $search the search term, as passed in to the find frameworks method.
     * @param int $count the number of frameworks that currently match.
     * @return array in the right format to return from the find_frameworks method.
     */
    protected function too_many_results($search, $count) {
        if ($search) {
            $a = new stdClass;
            $a->count = $count;
            $a->search = $search;
            return array(get_string('toomanyframeworksmatchsearch', 'local_framework_selector',
                    $a) => array(), get_string('pleasesearchmore', 'local_framework_selector')
                     => array());
        } else {
            return array(get_string('toomanyframeworkstoshow', 'local_framework_selector',
                         $count) => array(),
                    get_string('pleaseusesearch', 'local_framework_selector') => array());
        }
    }

    /**
     * Output the list of <optgroup>s and <options>s that go inside the select.
     * This method should do the same as the JavaScript method
     * framework_selector.prototype.handle_response.
     *
     * @param array $groupedframeworks an array, as returned by find_frameworks.
     * @return string HTML code.
     */
    protected function output_options($groupedframeworks, $search) {
        $output = '';

        // Ensure that the list of previously selected frameworks is up to date.
        $this->get_selected_frameworks();

        // If $groupedframeworks is empty, make a 'no matching frameworks' group. If there is
        // only one selected framework, set a flag to select them if that option is turned on.
        $select = false;
        if (empty($groupedframeworks)) {
            if (!empty($search)) {
                $groupedframeworks = array(get_string('nomatchingframeworks', 'local_framework_selector',
                $search) => array());
            } else {
                $groupedframeworks = array(get_string('none') => array());
            }
        } else if ($this->autoselectunique && count($groupedframeworks) == 1 &&
                count(reset($groupedframeworks)) == 1) {
            $select = true;
            if (!$this->multiselect) {
                $this->selected = array();
            }
        }

        // Output each optgroup.
        foreach ($groupedframeworks as $groupname => $frameworks) {
            $output .= $this->output_optgroup($groupname, $frameworks, $select);
        }

        // If there were previously selected frameworks who do not match the search, show them too.
        if ($this->preserveselected && !empty($this->selected)) {
            $output .= $this->output_optgroup(get_string('previouslyselectedframeworks',
                       'local_framework_selector', $search), $this->selected, true);
        }

        // This method trashes $this->selected, so clear the cache so it is
        // rebuilt before anyone tried to use it again.
        $this->selected = null;

        return $output;
    }

    /**
     * Output one particular optgroup. Used by the preceding function output_options.
     *
     * @param string $groupname the label for this optgroup.
     * @param array $frameworks the frameworks to put in this optgroup.
     * @param boolean $select if true, select the frameworks in this group.
     * @return string HTML code.
     */
    protected function output_optgroup($groupname, $frameworks, $select) {
        if (!empty($frameworks)) {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . ' (' .
                       count($frameworks) . ')">' . "\n";
            foreach ($frameworks as $framework) {
                $attributes = '';
                if (!empty($framework->disabled)) {
                    $attributes .= ' disabled="disabled"';
                } else if ($select || isset($this->selected[$framework->id])) {
                    $attributes .= ' selected="selected"';
                }
                unset($this->selected[$framework->id]);
                $output .= '    <option' . $attributes . ' value="' . $framework->id . '">' .
                        $this->output_framework($framework) . "</option>\n";
            }
        } else {
            $output = '  <optgroup label="' . htmlspecialchars($groupname) . '">' . "\n";
            $output .= '    <option disabled="disabled">&nbsp;</option>' . "\n";
        }
        $output .= "  </optgroup>\n";
        return $output;
    }

    /**
     * Convert a framework object to a string suitable for displaying as an option in the list box.
     *
     * @param object $framework the framework to display.
     * @return string a string representation of the framework.
     */
    public function output_framework($framework) {
        $bits = array(
            $framework->shortname
        );
        foreach ($this->extrafields as $field) {
            $bits[] = $framework->$field;
        }
        return implode(', ', $bits);
    }

    /**
     * @return string the caption for the search button.
     */
    protected function search_button_caption() {
        return get_string('search');
    }

    // Initialise one of the option checkboxes, either from
    // the request, or failing that from the user_preferences table, or
    // finally from the given default.
    private function initialise_option($name, $default) {
        $param = optional_param($name, null, PARAM_BOOL);
        if (is_null($param)) {
            return get_user_preferences($name, $default);
        } else {
            set_user_preference($name, $param);
            return $param;
        }
    }

    // Output one of the options checkboxes.
    private function option_checkbox($name, $on, $label) {
        if ($on) {
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }
        $name = 'frameworkselector_' . $name;
        $output = '<p><input type="hidden" name="' . $name . '" value="0" />' .
                // For the benefit of brain-dead IE, the id must be different from the
                // name of the hidden form field above.
                // It seems that document.getElementById('frog') in IE will return an
                // element with name="frog".
                '<input type="checkbox" id="' . $name . 'id" name="' . $name . '" value="1"' .
                $checked . ' /> ' .  '<label for="' . $name . 'id">' . $label . "</label></p>\n";
        user_preference_allow_ajax_update($name, PARAM_BOOL);
        return $output;
    }

    /**
     * @param boolean $optiontracker if true, initialise JavaScript for updating the user prefs.
     * @return any HTML needed here.
     */
    protected function initialise_javascript($search) {
        global $USER, $PAGE, $OUTPUT;
        $output = '';

        // Put the options into the session, to allow search.php to respond to the ajax requests.
        $options = $this->get_options();
        $hash = md5(serialize($options));
        $USER->frameworkselectors[$hash] = $options;

        // Initialise the selector.
        $PAGE->requires->js_init_call('M.local_framework_selector.init_framework_selector',
                                       array($this->name, $hash, $this->extrafields, $search),
                                       false, self::$jsmodule);
        return $output;
    }
}

