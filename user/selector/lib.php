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
 *
 * In your theme, you must give each user-selector a defined width. If the
 * user selector has name="myid", then the div myid_wrapper must have a width
 * specified.
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
     * If you update the database in such a way that it is likely to change the
     * list of users that this component is allowed to select from, then you
     * must call this method. For example, on the role assign page, after you have
     * assigned some roles to some users, you should call this.
     */
    public function invalidate_selected_users() {
        $this->selected = null;
    }

    /**
     * Output this user_selector as HTML.
     * @param boolean $return if true, return the HTML as a string instead of outputting it.
     * @return mixed if $return is true, returns the HTML as a string, otherwise returns nothing.
     */
    public function display($return = false) {
        // Get the list of requested users.
        $search = optional_param($this->name . '_searchtext', '', PARAM_RAW);
        $groupedusers = $this->find_users($search);

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
        $output .= $this->output_options($groupedusers);

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

    /**
     * Set the user fields that are displayed in the selector in addition to the
     * user's name.
     *
     * @param array $fields a list of field names that exist in the user table.
     */
    public function set_extra_fields($fields) {
        $this->extrafields = $fields;
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

    /**
     * @return array the options needed to recreate this user_selector.
     */
    protected function get_options() {
        return array(
            'class' => get_class($this),
            'name' => $this->name,
            'exclude' => $this->exclude,
        );
    }

    // Inner workings ==========================================================

    /**
     * Get the list of users that were selected by doing optional_param then
     * validating the result.
     *
     * @return array of user objects.
     */
    protected function load_selected_users() {
        // See if we got anything.
        $userids = optional_param($this->name, array(), PARAM_INTEGER);
        if (empty($userids)) {
            return array();
        }
        if (!$this->multiselect) {
            $userids = array($userids);
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

        // If we are only supposed to be selecting a single user, make sure we do.
        if (!$this->multiselect && count($users) > 1) {
            $users = array_slice($users, 0, 1);
        }

        return $users;
    }

    /**
     * @param string $u the table alias for the user table in the query being
     *      built. May be ''.
     * @return string fragment of SQL to go in the select list of the query.
     */
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

    /**
     * @param string $search the text to search for.
     * @param string $u the table alias for the user table in the query being
     *      built. May be ''.
     * @return array an array with two elements, a fragment of SQL to go in the
     *      where clause the query, and an array containing any required parameters.
     *      this uses ? style placeholders.
     */
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
     * @param array $groupedusers an array, as returned by find_users.
     * @return string HTML code.
     */
    protected function output_options($groupedusers) {
        $output = '';

        // Ensure that the list of previously selected users is up to date.
        $this->get_selected_users();

        // If $groupedusers is empty, make a 'no matching users' group. If there
        // is only one selected user, set a flag to select them.
        $select = false;
        if (empty($groupedusers)) {
            $groupedusers = array(get_string('nomatchingusers') => array());
        } else if (count($groupedusers) == 1 && count(reset($groupedusers)) == 1) {
            $select = true;
            if (!$this->multiselect) {
                $this->selected = array();
            }
        }

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

    /**
     * Output one particular optgroup. Used by the preceding function output_options.
     *
     * @param string $groupname the label for this optgroup.
     * @param array $users the users to put in this optgroup.
     * @param boolean $select if true, select the users in this group.
     * @return string HTML code.
     */
    protected function output_optgroup($groupname, $users, $select) {
        if (!empty($users)) {
            $output = '  <optgroup label="' . s($groupname) . ' (' . count($users) . ')">' . "\n";
            foreach ($users as $user) {
                if ($select || isset($this->selected[$user->id])) {
                    $selectattr = ' selected="selected"';
                } else {
                    $selectattr = '';
                }
                unset($this->selected[$user->id]);
                $output .= '    <option' . $selectattr . ' value="' . $user->id . '">' .
                        $this->output_user($user) . "</option>\n";
            }
        } else {
            $output = '  <optgroup label="' . s($groupname) . '">' . "\n";
            $output .= '    <option disabled="disabled">&nbsp;</option>' . "\n";
        }
        $output .= "  </optgroup>\n";
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
     * 
     *
     * @return any HTML needed here.
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
                sesskey(), $this->extrafields, get_string('previouslyselectedusers'),
                get_string('nomatchingusers')), true);
        return $output;
    }
}

/**
 * User selector subclass  for the list of potential users on the assign roles page.
 *
 */
class role_assign_potential_user_selector extends user_selector_base {
    public function find_users($search) {
        return array(); // TODO
    }
}

/**
 * User selector subclass for the list of users who already have the role in
 * question on the assign roles page.
 */
class role_assign_current_user_selector extends user_selector_base {
    public function find_users($search) {
        return array(); // TODO
    }
}

abstract class groups_user_selector_base extends user_selector_base {
    protected $groupid;
    protected $courseid;

    /**
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct($name, $options) {
        global $CFG;
        parent::__construct($name, $options);
        $this->groupid = $options['groupid'];
        $this->courseid = $options['courseid'];
        require_once($CFG->dirroot . '/group/lib.php');
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['groupid'] = $this->groupid;
        $options['courseid'] = $this->courseid;
        return $options;
    }

    /**
     * Enter description here...
     *
     * @param array $roles array in the format returned by groups_calculate_role_people.
     * @return array array in the format find_users is supposed to return.
     */
    protected function convert_array_format($roles) {
        if (empty($roles)) {
            $roles = array();
        }
        $groupedusers = array();
        foreach ($roles as $role) {
            $groupedusers[$role->name] = $role->users;
            foreach ($groupedusers[$role->name] as &$user) {
                unset($user->roles);
                $user->fullname = fullname($user);
            }
        }
        return $groupedusers;
    }
}

/**
 * User selector subclass for the list of users who are in a certain group.
 * Used on the add group memebers page.
 */
class group_members_selector extends groups_user_selector_base {
    public function find_users($search) {
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $roles = groups_get_members_by_role($this->groupid, $this->courseid,
                $this->required_fields_sql('u'), 'u.lastname, u.firstname',
                $wherecondition, $params);
        return $this->convert_array_format($roles);
    }
}

/**
 * User selector subclass for the list of users who are not in a certain group.
 * Used on the add group memebers page.
 */
class group_non_members_selector extends groups_user_selector_base {
    const MAX_USERS_PER_PAGE = 100;

    protected function output_user($user) {
        return parent::output_user($user) . ' (' . $user->numgroups . ')';
    }

    public function find_users($search) {
        global $DB;

        // Get list of allowed roles.
        $context = get_context_instance(CONTEXT_COURSE, $this->courseid);
        if (!$validroleids = groups_get_possible_roles($context)) {
            return array();
        }
        list($roleids, $roleparams) = $DB->get_in_or_equal($validroleids);

        // Get the search condition.
        list($searchcondition, $searchparams) = $this->search_sql($search, 'u');

        // Build the SQL
        $fields = "SELECT r.id AS roleid, r.shortname AS roleshortname, r.name AS rolename, u.id AS userid, " . 
                $this->required_fields_sql('u') .
                ', (SELECT count(igm.groupid) FROM {groups_members} igm JOIN {groups} ig ON
                    igm.groupid = ig.id WHERE igm.userid = u.id AND ig.courseid = ?) AS numgroups ';
        $sql = "   FROM {user} u
                   JOIN {role_assignments} ra ON ra.userid = u.id
                   JOIN {role} r ON r.id = ra.roleid
                  WHERE ra.contextid " . get_related_contexts_string($context) . "
                        AND u.deleted = 0
                        AND ra.roleid $roleids
                        AND u.id NOT IN (SELECT userid
                                          FROM {groups_members}
                                         WHERE groupid = ?)
                        AND $searchcondition";
        $orderby = " ORDER BY u.lastname, u.firstname";

        $params = array_merge($roleparams, array($this->groupid), $searchparams);
        $potentialmemberscount = $DB->count_records_sql('SELECT count(DISTINCT u.id) ' . $sql, $params);

        if ($potentialmemberscount > group_non_members_selector::MAX_USERS_PER_PAGE) {
            return array(get_string('toomanytoshow') => array(),
                    get_string('trysearching') => array());
        }

        array_unshift($params, $this->courseid);
        $rs = $DB->get_recordset_sql($fields . $sql . $orderby, $params);
        $roles =  groups_calculate_role_people($rs, $context);

        return $this->convert_array_format($roles);
    }
}
?>