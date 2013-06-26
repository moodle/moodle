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
 * Library code used by the roles administration interfaces.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * User selector subclass for the list of users who already have the role in
 * question on the assign roles page.
 */
class core_role_existing_role_holders extends core_role_assign_user_selector_base {

    public function __construct($name, $options) {
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $DB;

        list($wherecondition, $params) = $this->search_sql($search, 'u');
        list($ctxcondition, $ctxparams) = $DB->get_in_or_equal(get_parent_contexts($this->context, true), SQL_PARAMS_NAMED, 'ctx');
        $params = array_merge($params, $ctxparams);
        $params['roleid'] = $this->roleid;

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $params = array_merge($params, $sortparams);

        $sql = "SELECT ra.id as raid," . $this->required_fields_sql('u') . ",ra.contextid,ra.component
                FROM {role_assignments} ra
                JOIN {user} u ON u.id = ra.userid
                JOIN {context} ctx ON ra.contextid = ctx.id
                WHERE
                    $wherecondition AND
                    ctx.id $ctxcondition AND
                    ra.roleid = :roleid
                ORDER BY ctx.depth DESC, ra.component, $sort";
        $contextusers = $DB->get_records_sql($sql, $params);

        // No users at all.
        if (empty($contextusers)) {
            return array();
        }

        // We have users. Out put them in groups by context depth.
        // To help the loop below, tack a dummy user on the end of the results
        // array, to trigger output of the last group.
        $dummyuser = new stdClass;
        $dummyuser->contextid = 0;
        $dummyuser->id = 0;
        $dummyuser->component = '';
        $contextusers[] = $dummyuser;
        $results = array(); // The results array we are building up.
        $doneusers = array(); // Ensures we only list each user at most once.
        $currentcontextid = $this->context->id;
        $currentgroup = array();
        foreach ($contextusers as $user) {
            if (isset($doneusers[$user->id])) {
                continue;
            }
            $doneusers[$user->id] = 1;
            if ($user->contextid != $currentcontextid) {
                // We have got to the end of the previous group. Add it to the results array.
                if ($currentcontextid == $this->context->id) {
                    $groupname = $this->this_con_group_name($search, count($currentgroup));
                } else {
                    $groupname = $this->parent_con_group_name($search, $currentcontextid);
                }
                $results[$groupname] = $currentgroup;
                // Get ready for the next group.
                $currentcontextid = $user->contextid;
                $currentgroup = array();
            }
            // Add this user to the group we are building up.
            unset($user->contextid);
            if ($currentcontextid != $this->context->id) {
                $user->disabled = true;
            }
            if ($user->component !== '') {
                // bad luck, you can tweak only manual role assignments
                $user->disabled = true;
            }
            unset($user->component);
            $currentgroup[$user->id] = $user;
        }

        return $results;
    }

    protected function this_con_group_name($search, $numusers) {
        if ($this->context->contextlevel == CONTEXT_SYSTEM) {
            // Special case in the System context.
            if ($search) {
                return get_string('extusersmatching', 'role', $search);
            } else {
                return get_string('extusers', 'role');
            }
        }
        $contexttype = get_contextlevel_name($this->context->contextlevel);
        if ($search) {
            $a = new stdClass;
            $a->search = $search;
            $a->contexttype = $contexttype;
            if ($numusers) {
                return get_string('usersinthisxmatching', 'role', $a);
            } else {
                return get_string('noneinthisxmatching', 'role', $a);
            }
        } else {
            if ($numusers) {
                return get_string('usersinthisx', 'role', $contexttype);
            } else {
                return get_string('noneinthisx', 'role', $contexttype);
            }
        }
    }

    protected function parent_con_group_name($search, $contextid) {
        $context = context::instance_by_id($contextid);
        $contextname = print_context_name($context, true, true);
        if ($search) {
            $a = new stdClass;
            $a->contextname = $contextname;
            $a->search = $search;
            return get_string('usersfrommatching', 'role', $a);
        } else {
            return get_string('usersfrom', 'role', $contextname);
        }
    }
}
