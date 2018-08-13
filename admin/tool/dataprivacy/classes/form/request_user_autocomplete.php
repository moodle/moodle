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
 * Filter selector field.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\form;

defined('MOODLE_INTERNAL') || die();

use MoodleQuickForm_autocomplete;

global $CFG;
require_once($CFG->libdir . '/form/autocomplete.php');

/**
 * Form field type for choosing a user on the data request creation form.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class request_user_autocomplete extends MoodleQuickForm_autocomplete {

    /**
     * Constructor.
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     *                       Valid options are:
     *                       - multiple bool Whether or not the field accepts more than one values.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array()) {
        $validattributes = array(
            'ajax' => 'tool_dataprivacy/form-user-selector',
        );
        if (!empty($options['multiple'])) {
            $validattributes['multiple'] = 'multiple';
        }

        parent::__construct($elementname, $elementlabel, array(), $validattributes);
    }

    /**
     * Set the value of this element.
     *
     * @param  string|array $value The value to set.
     * @return boolean
     */
    public function setValue($value) {
        global $DB;
        $values = (array) $value;
        $ids = [];
        foreach ($values as $onevalue) {
            if (!empty($onevalue) && (!$this->optionExists($onevalue)) &&
                    ($onevalue !== '_qf__force_multiselect_submission')) {
                array_push($ids, $onevalue);
            }
        }

        if (empty($ids)) {
            return $this->setSelected([]);
        }

        $toselect = [];
        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $allusernames = get_all_user_name_fields(true);
        // Exclude admins and guest user.
        $excludedusers = array_keys(get_admins()) + [guest_user()->id];
        $sort = 'firstname ASC';
        $fields = 'id, email, ' . $allusernames;
        // Fetch the selected users, exclude the admins and guest users.
        $users = get_users(true, '', true, $excludedusers, $sort, '', '', 0, 30, $fields, "id $insql", $inparams);
        foreach ($users as $user) {
            $userdata = (object)[
                'name' => fullname($user),
                'email' => $user->email
            ];
            $this->addOption(get_string('nameemail', 'tool_dataprivacy', $userdata), $user->id);
            array_push($toselect, $user->id);
        }

        return $this->setSelected($toselect);
    }
}
