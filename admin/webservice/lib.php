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
 * Web services admin library
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/user/selector/lib.php');

/*
 * This class displays either all the Moodle users allowed to use a service,
 * either all the other Moodle users.
 */
class service_user_selector extends user_selector_base {
    const MAX_USERS_PER_PAGE = 100;

    protected $serviceid;
    protected $displayallowedusers; //set to true if the selector displays the
                                    //allowed users on this service
                                    //, set to false if the selector displays the
                                    // other users (false is the default default)

    public function __construct($name, $options) {
        parent::__construct($name, $options);
        if (!empty($options['serviceid'])) {
            $this->serviceid = $options['serviceid'];
        } else {
            throw new moodle_exception('serviceidnotfound');
        }
        $this->displayallowedusers = !empty($options['displayallowedusers']);
    }

    /**
     * Find allowed or not allowed users of a service (depend of $this->displayallowedusers)
     * @global object $DB
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $DB;
        //by default wherecondition retrieves all users except the deleted, not
        //confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['serviceid'] = $this->serviceid;


        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        if ($this->displayallowedusers) {
            ///the following SQL retrieve all users that are allowed to the serviceid
            $sql = " FROM {user} u, {external_services_users} esu
                 WHERE $wherecondition
                       AND u.deleted = 0
                       AND esu.userid = u.id
                       AND esu.externalserviceid = :serviceid";
        }
        else {
            ///the following SQL retrieve all users that are not allowed to the serviceid
            $sql = " FROM {user} u WHERE $wherecondition AND u.deleted = 0
                 AND NOT EXISTS (SELECT esu.userid FROM {external_services_users} esu
                                                  WHERE esu.externalserviceid = :serviceid
                                                        AND esu.userid = u.id)";
        }

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $order = ' ORDER BY ' . $sort;

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > service_user_selector::MAX_USERS_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        if (empty($availableusers)) {
            return array();
        }


        if ($search) {
            $groupname = ($this->displayallowedusers) ?
                get_string('serviceusersmatching', 'webservice', $search)
                : get_string('potusersmatching', 'webservice', $search);
        }
        else {
            $groupname = ($this->displayallowedusers) ?
                get_string('serviceusers', 'webservice')
                : get_string('potusers', 'webservice');
        }

        return array($groupname => $availableusers);
    }

    /**
     * This options are automatically used by the AJAX search
     * @global object $CFG
     * @return object options pass to the constructor when AJAX search call a new selector
     */
    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin.'/webservice/lib.php'; //need to be set, otherwise
                                                        // the /user/selector/search.php
                                                        //will fail to find this user_selector class
        $options['serviceid'] = $this->serviceid;
        $options['displayallowedusers'] = $this->displayallowedusers;
        return $options;
    }
}
