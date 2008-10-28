<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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
 * Code for ajax user selectors.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package userselector
 */

/**
 * The default size of a user selector.
 */
define('USER_SELECTOR_DEFAULT_ROWS', 20);

/**
 * Base class for user selectors.
 */
abstract class user_selector_base {
    /** The control name (and id) in the HTML. */
    protected $name;
    /** Extra fields to search on and return in addition to firstname and lastname. */
    protected $extrafields;
    /** Whether the conrol should allow selection of many users, or just one. */
    protected $multiselect = true;
    /** The height this control should have, in rows. */
    protected $rows = USER_SELECTOR_DEFAULT_ROWS;
    /** A list of userids that should not be returned by this control. */
    protected $exclude = array();
    /** A list of the users who are selected. */
    protected $selected = null;

    // This is used by get selected users, 
    private $validatinguserids = null;

    // Public API ==============================================================

    /**
     * Constructor. Each subclass must have a constructor with this signature.
     *
     * @param string $name the control name/id for use in the HTML.
     * @param array $options other options needed to construct this selector.
     * You must be able to clone a userselector by doing new get_class($us)($us->get_name(), $us->get_options());
     */
    public function __construct($name, $options = array()) {
        global $CFG;
        $this->name = $name;
        if (empty($CFG->extrauserselectorfields)) {
            $this->extrafields = array();
        } else {
            $this->extrafields = explode(',', $CFG->extrauserselectorfields);
        }
        if (isset($options['exclude']) && is_array($options['exclude'])) {
            $this->exclude = $options['exclude'];
        }
    }

    /**
     * All to the list of user ids that this control will not select. For example,
     * on the role assign page, we do not list the users who already have the role
     * in question.
     *
     * @param array $arrayofuserids the user ids to exclude.
     */
    public function exclude($arrayofuserids) {
        $this->exclude = array_unique(array_merge($this->exclude, $arrayofuserids));
    }

    /**
     * Clear the list of excluded user ids.
     */
    public function clear_exclusions() {
        $exclude = array();
    }

    /**
     * @return array the list of user ids that this control will not select.
     */
    public function get_exclusions() {
        return clone($this->exclude);
    }

    /**
     * @return array the userids that were selected. This is a more sophisticated version
     * of optional_param($this->name, array(), PARAM_INTEGER) that validates the
     * returned list of ids against the rules for this user selector.
     */
    public function get_selected_users() {
        // Do a lazy load.
        if (is_null($this->selected)) {
            $this->selected = $this->load_selected_users();
        }
        return $this->selected;
    }

    /**
     * Output this user_selector as HTML.
     * @param boolean $return if true, return the HTML as a string instead of outputting it.
     * @return mixed if $return is true, returns the HTML as a string, otherwise returns nothing.
     */
    public function display($return = false) {
        global $USER, $CFG;

        // Get the list of requested users, and if there is only one, set a flag to autoselect it.
        $search = optional_param($this->name . '_searchtext', '', PARAM_RAW);
        $groupedusers = $this->find_users($search);
        $select = false;
        if (empty($groupedusers) && empty($this->selected)) {
            $groupedusers = array(get_string('nomatchingusers') => array());
        } else if (count($groupedusers) == 1 && count(reset($groupedusers)) == 1) {
            $select = true;
        }

        // Output the select.
        $name = $this->name;
        $multiselect = '';
        if ($this->multiselect) {
            $name .= '[]';
            $multiselect = 'multiple="multiple" ';
        }
        $output = '<div class="userselector" id="' . $this->name . '_wrapper">' . "\n" .
                '<select name="' . $name . '" id="' . $this->name . '" ' .
                $multiselect . 'size="' . $this->rows . '">' . "\n";

        // Populate the select.
        $output .= $this->output_options($groupedusers, $select);

        // Output the search controls.
        $output .= "</select>\n<div>\n";
        $output .= '<input type="text" name="' . $this->name . '_searchtext" id="' .
                $this->name . '_searchtext" value="' . s($search) . '" />';
        $output .= '<input type="submit" name="' . $this->name . '_searchbutton" id="' .
                $this->name . '_searchbutton" value="' . $this->search_button_caption() . '" />';
        $output .= "</div>\n</div>\n\n";

        // Initialise the ajax functionality.
        $output .= $this->initialise_javascript();

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
     * Whether this control will allow selection of many, or just one user.
     *
     * @param boolean $multiselect true = allow multiple selection.
     */
    public function set_multiselect($multiselect) {
        $this->multiselect = $multiselect;
    }

    /**
     * @return boolean whether this control will allow selection of more than one user.
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

    // API for sublasses =======================================================

    /**
     * Search the database for users matching the $search string, and any other
     * conditions that apply. The SQL for testing whether a user matches the
     * search string should be obtained by calling the search_sql method.
     *
     * @param string $search the search string.
     * @return array An array of arrays of users. The array keys of the outer
     *      array should be the string names of optgroups. The keys of the inner
     *      arrays should be userids, and the values should be user objects
     *      containing at least the list of fields returned by the method
     *      required_fields_sql().
     */
    public abstract function find_users($search);

    protected function get_options() {
        return array(
            'class' => get_class($this),
            'name' => $this->name,
            'exclude' => $this->exclude,
        );
    }

    // Inner workings ==========================================================

    protected function load_selected_users() {
        // See if we got anything.
        $userids = optional_param($this->name, array(), PARAM_INTEGER);
        if (empty($userids)) {
            return array();
        }

        // If we did, use the find_users method to validate the ids.
        $this->validatinguserids = $userids;
        $groupedusers = $this->find_users('');
        $this->validatinguserids = null;

        // Aggregate the resulting list back into a single one.
        $users = array();
        foreach ($groupedusers as $group) {
            $users += $group;
        }
        return $users;
    }

    protected function required_fields_sql($u) {
        // Raw list of fields.
        $fields = array('id', 'firstname', 'lastname');
        $fields = array_merge($fields, $this->extrafields);

        // Prepend the table alias.
        if ($u) {
            foreach ($fields as &$field) {
                $field = $u . '.' . $field;
            }
        }
        return implode(',', $fields);
    }

    protected function search_sql($search, $u) {
        global $DB;
        $params = array();
        $tests = array();

        // If we have a $search string, put a field LIKE '$search%' condition on each field.
        if ($search) {
            $conditions = array(
                $DB->sql_fullname($u . '.firstname', $u . '.lastname'),
                $conditions[] = $u . '.lastname'
            );
            foreach ($this->extrafields as $field) {
                $conditions[] = $u . '.' . $field;
            }
            $ilike = ' ' . $DB->sql_ilike() . ' ?';
            foreach ($conditions as &$condition) {
                $condition .= $ilike;
                $params[] = $search . '%';
            }
            $tests[] = '(' . implode(' OR ', $conditions) . ')';
        }

        // If we are being asked to exclude any users, do that.
        if (!empty($this->exclude)) {
            list($usertest, $userparams) = $DB->get_in_or_equal($this->exclude, SQL_PARAMS_QM, '', false);
            $tests[] = $u . '.id ' . $usertest;
            $params = array_merge($params, $userparams);
        }

        // If we are validating a set list of userids, add an id IN (...) test.
        if (!empty($this->validatinguserids)) {
            list($usertest, $userparams) = $DB->get_in_or_equal($this->validatinguserids);
            $tests[] = $u . '.id ' . $usertest;
            $params = array_merge($params, $userparams);
        }

        if (empty($tests)) {
            $tests[] = '1 = 1';
        }

        // Combing the conditions and return.
        return array(implode(' AND ', $tests), $params);
    }

    /**
     * Output the list of <optgroup>s and <options>s that go inside the select.
     * This method should do the same as the JavaScript method
     * user_selector.prototype.handle_response.
     *
     * @param unknown_type $groupedusers
     * @param unknown_type $select
     * @return unknown
     */
    protected function output_options($groupedusers, $select) {
        $output = '';

        // Ensure that the list of previously selected users is up to date.
        $this->get_selected_users();

        // Output each optgroup.
        foreach ($groupedusers as $groupname => $users) {
            $output .= $this->output_optgroup($groupname, $users, $select);
        }

        // If there were previously selected users who do not match the search, show them too.
        if (!empty($this->selected)) {
            $output .= $this->output_optgroup(get_string('previouslyselectedusers'), $this->selected, true);
        }

        // This method trashes $this->selected, so clear the cache so it is
        // rebuilt before anyone tried to use it again.
        $this->selected = null;

        return $output;
    }

    protected function output_optgroup($groupname, $users, $select) {
        $output = '<optgroup label="' . s($groupname) . ' (' . count($users) . ')">' . "\n";
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($select || isset($this->selected[$user->id])) {
                    $selectattr = ' selected="selected"';
                } else {
                    $selectattr = '';
                }
                unset($this->selected[$user->id]);
                $output .= '<option' . $selectattr . ' value="' . $user->id . '">' .
                        $this->output_user($user) . "</option>\n";
            }
        } else {
            $output .= '<option disabled="disabled">&nbsp;</option>' . "\n";
        }
        $output .= "</optgroup>\n";
        return $output;
    }

    /**
     * Convert a user object to a string suitable for displaying as an option in the list box.
     *
     * @param object $user the user to display.
     * @return string a string representation of the user.
     */
    protected function output_user($user) {
        $bits = array(
            fullname($user)
        );
        foreach ($this->extrafields as $field) {
            $bits[] = $user->$field;
        }
        return implode(', ', $bits);
    }

    /**
     * @return string the caption for the search button.
     */
    protected function search_button_caption() {
        return get_string('search');
    }

    /**
     * Enter description here...
     *
     */
    protected function initialise_javascript() {
        global $USER;
        $output = '';

        // Required JavaScript code.
        require_js(array('yui_yahoo', 'yui_event', 'yui_json', 'yui_connection', 'yui_datasource'));
        require_js('user/selector/script.js');

        // Put the options into the session, to allow search.php to respond to the ajax requests.
        $options = $this->get_options();
        $hash = md5(serialize($options));
        $USER->userselectors[$hash] = $options;

        // Initialise the selector.
        $output .= print_js_call('new user_selector', array($this->name, $hash,
                sesskey(), $this->extrafields, get_string('previouslyselectedusers')), true);
        return $output;
    }
}

class role_assign_potential_user_selector extends user_selector_base {
    public function find_users($search) {
        return array(); // TODO
    }
}

class role_assign_current_user_selector extends user_selector_base {
    public function find_users($search) {
        return array(); // TODO
    }
}

class group_members_user_selector extends user_selector_base {
    public function find_users($search) {
        return array(); // TODO
    }
}
?>