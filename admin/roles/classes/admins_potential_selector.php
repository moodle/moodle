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
 * Potential admin user selector.
 *
 * @package    core_role
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/selector/lib.php');

class core_role_admins_potential_selector extends user_selector_base {
    /**
     * Create instance.
     *
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct($name = null, $options = array()) {
        global $CFG;
        if (is_null($name)) {
            $name = 'addselect';
        }
        $options['multiselect'] = false;
        $options['includecustomfields'] = true;
        $options['exclude'] = explode(',', $CFG->siteadmins);
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $CFG, $DB;

        [$wherecondition, $params] = $this->search_sql($search, 'u');
        $params = array_merge($params, $this->userfieldsparams);

        $fields = 'SELECT u.id, ' . $this->userfieldsselects;
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                      $this->userfieldsjoin
                WHERE $wherecondition AND mnethostid = :localmnet";

        // It could be dangerous to make remote users admins and also this could lead to other problems.
        $params['localmnet'] = $CFG->mnet_localhost_id;

        [$sort, $sortparams] = users_order_by_sql('u', $search, $this->accesscontext, $this->userfieldsmappings);
        $order = ' ORDER BY ' . $sort;

        // Check to see if there are too many to show sensibly.
        if (!$this->is_validating()) {
            $potentialcount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialcount > $this->maxusersperpage) {
                return $this->too_many_results($search, $potentialcount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'core_role', $search);
        } else {
            $groupname = get_string('potusers', 'core_role');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}
