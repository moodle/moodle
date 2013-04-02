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
 * This file contains the profile completion badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/user/lib.php");

/**
 * Profile completion badge award criteria
 *
 */
class award_criteria_profile extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_PROFILE] */
    public $criteriatype = BADGE_CRITERIA_TYPE_PROFILE;

    public $required_param = 'field';
    public $optional_params = array();

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options(&$mform) {
        global $DB;

        $none = true;
        $existing = array();
        $missing = array();

        // Note: cannot use user_get_default_fields() here because it is not possible to decide which fields user can modify.
        $dfields = array('firstname', 'lastname', 'email', 'address', 'phone1', 'phone2', 'icq', 'skype', 'yahoo',
                         'aim', 'msn', 'department', 'institution', 'description', 'city', 'url', 'country');

        $sql = "SELECT uf.id as fieldid, uf.name as name, ic.id as categoryid, ic.name as categoryname, uf.datatype
                FROM {user_info_field} uf
                JOIN {user_info_category} ic
                ON uf.categoryid = ic.id AND uf.visible <> 0
                ORDER BY ic.sortorder ASC, uf.sortorder ASC";

        // Get custom fields.
        $cfields = $DB->get_records_sql($sql);
        $cfids = array_map(create_function('$o', 'return $o->fieldid;'), $cfields);

        if ($this->id !== 0) {
            $existing = array_keys($this->params);
            $missing = array_diff($existing, array_merge($dfields, $cfids));
        }

        if (!empty($missing)) {
            $mform->addElement('header', 'category_errors', get_string('criterror', 'badges'));
            $mform->addHelpButton('category_errors', 'criterror', 'badges');
            foreach ($missing as $m) {
                $this->config_options($mform, array('id' => $m, 'checked' => true, 'name' => get_string('error:nosuchfield', 'badges'), 'error' => true));
                $none = false;
            }
        }

        if (!empty($dfields)) {
            $mform->addElement('header', 'first_header', $this->get_title());
            $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');
            foreach ($dfields as $field) {
                $checked = false;
                if (in_array($field, $existing)) {
                    $checked = true;
                }
                $this->config_options($mform, array('id' => $field, 'checked' => $checked, 'name' => get_user_field_name($field), 'error' => false));
                $none = false;
            }
        }

        if (!empty($cfields)) {
            foreach ($cfields as $field) {
                if (!isset($currentcat) || $currentcat != $field->categoryid) {
                    $currentcat = $field->categoryid;
                    $mform->addElement('header', 'category_' . $currentcat, format_string($field->categoryname));
                }
                $checked = false;
                if (in_array($field->fieldid, $existing)) {
                    $checked = true;
                }
                $this->config_options($mform, array('id' => $field->fieldid, 'checked' => $checked, 'name' => $field->name, 'error' => false));
                $none = false;
            }
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodprofile', 'badges'), 1);
            $agg[] =& $mform->createElement('static', 'none_break', null, '<br/>');
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodprofile', 'badges'), 2);
            $mform->addGroup($agg, 'methodgr', '', array(' '), false);
            if ($this->id !== 0) {
                $mform->setDefault('agg', $this->method);
            } else {
                $mform->setDefault('agg', BADGE_CRITERIA_AGGREGATION_ANY);
            }
        }

        return array($none, get_string('noparamstoadd', 'badges'));
    }

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            if (is_numeric($p['field'])) {
                $str = $DB->get_field('user_info_field', 'name', array('id' => $p['field']));
            } else {
                $str = get_user_field_name($p['field']);
            }
            if (!$str) {
                $output[] = $OUTPUT->error_text(get_string('error:nosuchfield', 'badges'));
            } else {
                $output[] = $str;
            }
        }

        if ($short) {
            return implode(', ', $output);
        } else {
            return html_writer::alist($output, array(), 'ul');
        }
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;

        $overall = false;
        foreach ($this->params as $param) {
            if (is_numeric($param['field'])) {
                $crit = $DB->get_field('user_info_data', 'data', array('userid' => $userid, 'fieldid' => $param['field']));
            } else {
                $crit = $DB->get_field('user', $param['field'], array('id' => $userid));
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (!$crit) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if (!$crit) {
                    $overall = false;
                    continue;
                } else {
                    return true;
                }
            }
        }
        return $overall;
    }
}
