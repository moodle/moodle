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
 * Existing admin user selector.
 *
 * @package    core_role
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/selector/lib.php');

class core_role_admins_existing_selector extends user_selector_base {
    /**
     * Create instance.
     *
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct($name = null, $options = array()) {
        if (is_null($name)) {
            $name = 'removeselect';
        }
        $options['multiselect'] = false;
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $DB, $CFG;
        list($wherecondition, $params) = $this->search_sql($search, '');

        $fields      = 'SELECT ' . $this->required_fields_sql('');

        if ($wherecondition) {
            $wherecondition = "$wherecondition AND id IN ($CFG->siteadmins)";
        } else {
            $wherecondition = "id IN ($CFG->siteadmins)";
        }
        $sql = " FROM {user}
                WHERE $wherecondition";

        list($sort, $sortparams) = users_order_by_sql('', $search, $this->accesscontext);
        $params = array_merge($params, $sortparams);
        $order = ' ORDER BY ' . $sort;

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        $mainadmin = array();
        $mainadminuser = get_admin();
        if ($mainadminuser && isset($availableusers[$mainadminuser->id])) {
            $mainadmin = array($mainadminuser->id => $availableusers[$mainadminuser->id]);
            unset($availableusers[$mainadminuser->id]);
        }

        $result = array();
        if ($mainadmin) {
            $result[get_string('mainadmin', 'core_role')] = $mainadmin;
        }

        if ($availableusers) {
            if ($search) {
                $groupname = get_string('extusersmatching', 'core_role', $search);
            } else {
                $groupname = get_string('extusers', 'core_role');
            }
            $result[$groupname] = $availableusers;
        }

        return $result;
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}
