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
 * @package dataformfield_entrystate
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_entrystate_entrystate extends mod_dataform\pluginbase\dataformfield_nocontent {

    const ROLE_AUTHOR = -1;
    const ROLE_ENTRIES_MANAGER = -2;

    /**
     * Returns configuration settings.
     *
     * @return int
     */
    public function get_param1() {
        if (!empty($this->_field->param1)) {
            if (!is_array($this->_field->param1)) {
                $this->_field->param1 = unserialize(base64_decode($this->_field->param1));
                // Explode states.
                if (!empty($this->_field->param1['states'])) {
                    $this->_field->param1['states'] = array_map('trim', explode("\n", $this->_field->param1['states']));
                }
                // Add 'from to' keys to transitions.
                if (!empty($this->_field->param1['transitions'])) {
                    $transitions = array();
                    foreach ($this->_field->param1['transitions'] as $transition) {
                        $from = $transition['from'];
                        $to = $transition['to'];
                        $transitions["$from $to"] = $transition;
                    }
                    $this->_field->param1['transitions'] = $transitions;
                }
            }
            return $this->_field->param1;
        }

        return null;
    }

    /**
     * Returns a list of the defined states.
     *
     * @return array
     */
    public function get_states() {
        $config = $this->param1;

        if (!empty($config['states'])) {
            return $config['states'];
        }
        return array();
    }

    /**
     * Returns a list of the defined transitions.
     *
     * @return array
     */
    public function get_transitions() {
        $config = $this->param1;

        if (!empty($config['transitions'])) {
            return $config['transitions'];
        }
        return array();
    }

    /**
     * Returns a list of states which the current user can set.
     *
     * @return array
     */
    public function get_user_transition_states($entry) {
        if ($states = $this->states) {
            if (!isset($entry->state)) {
                $entry->state = 0;
            }
            foreach ($states as $state => $name) {
                if (!$this->can_instate($entry, $state)) {
                    unset($states[$state]);
                }
            }
        }
        return $states;
    }

    /**
     * Validates state update request against the field configuration.
     *
     * @param stdClass $entry
     * @param int $newstate Target state
     * @return null|string Error message on error or null on success
     */
    public function update_state($entry, $newstate) {
        global $DB;

        // Valid requested state?
        if (!isset($this->states[$newstate])) {
            return get_string('incorrectstate', 'dataformfield_entrystate', $newstate);
        }

        // Any change at all?
        $oldstate = $entry->state;
        if ($newstate == $oldstate) {
            $info = (object) array('entryid' => $entry->id, 'newstate' => $this->states[$newstate]);
            return get_string('alreadyinstate', 'dataformfield_entrystate', $info);
        }

        // Field editable?
        if (!$this->is_editable($entry)) {
            return get_string('instatingdenied', 'dataformfield_entrystate');
        }

        // Allowed transition?
        if (!$this->can_instate($entry, $newstate)) {
            return get_string('instatingdenied', 'dataformfield_entrystate');
        }

        // All's good so update entry.
        $DB->set_field('dataform_entries', 'state', $newstate, array('id' => $entry->id));

        // Notify as required.
        $this->send_notifications($entry, $newstate);

        return null;
    }

    /**
     * Validates state update request against the field configuration.
     *
     * @param stdClass $entrt Entry object
     * @param int $newstate Target state
     * @return bool
     */
    public function can_instate($entry, $newstate) {
        global $USER;

        $transitions = $this->transitions;
        $oldstate = $entry->state;

        // Empty permission in the target state allows only entries manager to update.
        if (empty($transitions["$oldstate $newstate"]['permission'])) {
            return has_capability('mod/dataform:manageentries', $this->df->context);
        }

        $permissions = $transitions["$oldstate $newstate"]['permission'];

        $roleids = array();
        if ($userroles = get_user_roles($this->df->context)) {
            $roleids = array_map(
                function($a) {
                    return $a->roleid;
                },
                $userroles
            );
        }

        foreach ($permissions as $key) {
            if ($key == self::ROLE_AUTHOR) {
                if (!empty($entry->userid) and $entry->userid == $USER->id) {
                    return true;
                }
            } else if ($key == self::ROLE_ENTRIES_MANAGER) {
                if (has_capability('mod/dataform:manageentries', $this->df->context)) {
                    return true;
                }
            } else if (in_array($key, $roleids)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sends notifications to roles defined in field configuration.
     *
     * @param stdClass $entrt Entry object
     * @param int $newstate Target state
     * @return bool
     */
    public function send_notifications($entry, $newstate) {
        global $DB, $USER;

        $states = $this->states;
        $transitions = $this->transitions;
        $oldstate = $entry->state;

        // No notifications defined for this transition.
        if (empty($transitions["$oldstate $newstate"]['notification'])) {
            return;
        }

        $notifications = $transitions["$oldstate $newstate"]['notification'];

        $recipients = array();
        $roleids = array();
        foreach ($notifications as $key) {
            if ($key == self::ROLE_AUTHOR) {
                if (!empty($entry->userid)) {
                    $recipients[] = $DB->get_record('user', array('id' => $entry->userid));
                }
            } else if ($key == self::ROLE_ENTRIES_MANAGER) {
                if ($entriesmanagers = get_users_by_capability($this->df->context, 'mod/dataform:manageentries')) {
                    $recipients = $recipients + $entriesmanagers;
                }
            } else {
                $roleids[] = $key;
            }
        }

        // Add role users if needed.
        if ($roleids and $roleusers = get_role_users($roleids, $this->df->context)) {
            $recipients = $recipients + $roleusers;
        }

        // Send.
        if ($recipients) {
            $data = array();
            $note = (object) array('id' => $entry->id, 'old' => $states[$oldstate], 'new' => $states[$newstate]);
            $data['subject'] = get_string('statechanged', 'dataformfield_entrystate', $note);
            $data['content'] = $data['subject'];
            $data['contentformat'] = FORMAT_PLAIN;
            $data['recipients'] = $recipients;
            $data['sender'] = get_admin();
            $data['name'] = 'dataform_notification';
            $data['notification'] = 1;

            $notification = new \mod_dataform\observer\notification;
            $notification->send_message($data);
        }
    }

    /**
     * Overriding {@link dataformfield::update_content()} to update the entry record state.
     *
     * @param stdClass $entry
     * @param array $values An associative array of values (see {@link dataformfield::get_content_from_data()})
     * @param bool $savenew Whether an existing entry is saved as a new one
     * @return bool|int
     */
    public function update_content($entry, array $values = null, $savenew = false) {
        $oldstate = !empty($entry->state) ? $entry->state : 0;
        $newstate = reset($values);

        if ($newstate == $oldstate) {
            // Do nothing.
            return false;
        }

        $this->update_state($entry, $newstate);

        return true;
    }

    /**
     * Overrides {@link dataformfield::prepare_import_content()} to set import into entry::state.
     *
     * @return stdClass
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = 0) {
        global $DB;

        // Only one imported pattern ''.
        $settings = reset($importsettings);

        if (!empty($settings['name'])) {
            $csvname = $settings['name'];

            if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                if ($states = $this->states and $statekey = array_search($csvrecord[$csvname], $states)) {
                    $data->{"entry_{$entryid}_state"} = $statekey;
                }
            }
        }
        return $data;
    }

    /**
     *
     */
    public function get_search_sql($search) {
        global $USER;

        $state = $search[3];

        // State may be searched by name.
        $statekey = array_search($state, $this->states);
        if ($statekey !== false) {
            $search[3] = $statekey;
            return parent::get_search_sql($search);
        }

        // State may be searched by index.
        if (is_numeric($state) and $state < count($this->states)) {
            return parent::get_search_sql($search);
        }
        return null;
    }

    /**
     * Searches on the entries table that is included by default so just return nothing.
     *
     * @return string Empty string.
     */
    public function get_search_from_sql() {
        return '';
    }

    /**
     * Return array of sort options menu as
     * $fieldid,element => name, for the filter form.
     *
     *
     * @return null|array
     */
    public function get_sort_options_menu() {
        $fieldid = $this->id;
        $fieldname = $this->name;
        return array(
            "$fieldid,state" => "$fieldname ". get_string('state', 'dataformfield_entrystate'),
        );
    }

    /**
     * Returns an array of distinct content of the field.
     *
     * @param string $element
     * @param int $sortdir Sort direction 0|1 ASC|DESC
     * @return array
     */
    public function get_distinct_content($element, $sortdir = 0) {
        return array();
    }

    /**
     * Returns the field alias for sql queries.
     *
     * @param string The field element to query
     * @return string
     */
    protected function get_sql_alias($element = null) {
        return 'e';
    }

    // GRADING.
    /**
     * Returns the value replacement of the pattern for each user with content in the field.
     *
     * @param string $pattern
     * @param array $entryids The ids of entries the field values should be fetched from.
     *      If not provided the method should return values from all applicable entries.
     * @param int $userid   The id of the users whose field values are requested.
     *      If not specified, should return values for all applicable users.
     * @return null|array Array of userid => value pairs.
     */
    public function get_user_values($pattern, array $entryids = null, $userid = 0) {
        global $DB;

        // If specific user and list of entries provided,
        // get values only if the user has entries.
        if ($userid and $entryids) {
            if (empty($entryids[$userid])) {
                return array();
            }
            $entryids = $entryids[$userid];
        }

        $params = array();
        $params[] = $this->dataid;

        $selectwhere = array(' dataid = ? ');

        // User.
        if ($userid) {
            $selectwhere[] = ' userid = ? ';
            $params[] = $userid;
        }

        // Entries.
        if ($entryids) {
            list($inids, $eparams) = $DB->get_in_or_equal($entryids);
            $selectwhere[] = " id $inids ";
            $params = array_merge($params, $eparams);
        }

        $select = implode(' AND ', $selectwhere);
        $values = array();
        if ($entries = $DB->get_records_select('dataform_entries', $select, $params, 'state', 'id,userid,state')) {
            foreach ($entries as $entryid => $entry) {
                if (empty($values[$entry->userid])) {
                    $values[$entry->userid] = array();
                }
                $values[$entry->userid][] = $entry->state;
            }
        }
        return $values;
    }
}
