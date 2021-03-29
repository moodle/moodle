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
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $none = true;
        $existing = array();
        $missing = array();

        // Note: cannot use user_get_default_fields() here because it is not possible to decide which fields user can modify.
        $dfields = array('firstname', 'lastname', 'email', 'address', 'phone1', 'phone2',
                         'department', 'institution', 'description', 'picture', 'city', 'country');

        // Get custom fields.
        $cfields = array_filter(profile_get_custom_fields(), function($field) {
            return $field->visible <> 0;
        });
        $cfids = array_keys($cfields);

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
                $this->config_options($mform, array('id' => $field, 'checked' => $checked,
                        'name' => \core_user\fields::get_display_name($field), 'error' => false));
                $none = false;
            }
        }

        if (!empty($cfields)) {
            foreach ($cfields as $field) {
                if (!isset($currentcat) || $currentcat != $field->categoryid) {
                    $currentcat = $field->categoryid;
                    $categoryname = $DB->get_field('user_info_category', 'name', ['id' => $field->categoryid]);
                    $mform->addElement('header', 'category_' . $currentcat, format_string($categoryname));
                }
                $checked = false;
                if (in_array($field->id, $existing)) {
                    $checked = true;
                }
                $this->config_options($mform, array('id' => $field->id, 'checked' => $checked, 'name' => $field->name, 'error' => false));
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
        global $OUTPUT, $CFG;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        $output = array();
        foreach ($this->params as $p) {
            if (is_numeric($p['field'])) {
                $fields = profile_get_custom_fields();
                // Get formatted field name if such field exists.
                $str = isset($fields[$p['field']]->name) ?
                    format_string($fields[$p['field']]->name) : null;
            } else {
                $str = \core_user\fields::get_display_name($p['field']);
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
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid, $filtered = false) {
        global $DB;

        // Users were already filtered by criteria completion, no checks required.
        if ($filtered) {
            return true;
        }

        $join = '';
        $whereparts = array();
        $sqlparams = array();
        $rule = ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) ? ' OR ' : ' AND ';

        foreach ($this->params as $param) {
            if (is_numeric($param['field'])) {
                // This is a custom field.
                $idx = count($whereparts) + 1;
                $join .= " LEFT JOIN {user_info_data} uid{$idx} ON uid{$idx}.userid = u.id AND uid{$idx}.fieldid = :fieldid{$idx} ";
                $sqlparams["fieldid{$idx}"] = $param['field'];
                $whereparts[] = "uid{$idx}.id IS NOT NULL";
            } else {
                // This is a field from {user} table.
                if ($param['field'] == 'picture') {
                    // The picture field is numeric and requires special handling.
                    $whereparts[] = "u.{$param['field']} != 0";
                } else {
                    $whereparts[] = $DB->sql_isnotempty('u', "u.{$param['field']}", false, true);
                }
            }
        }

        $sqlparams['userid'] = $userid;

        if ($whereparts) {
            $where = " AND (" . implode($rule, $whereparts) . ")";
        } else {
            $where = '';
        }
        $sql = "SELECT 1 FROM {user} u " . $join . " WHERE u.id = :userid $where";
        $overall = $DB->record_exists_sql($sql, $sqlparams);

        return $overall;
    }

    /**
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    public function get_completed_criteria_sql() {
        global $DB;

        $join = '';
        $whereparts = array();
        $params = array();
        $rule = ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) ? ' OR ' : ' AND ';

        foreach ($this->params as $param) {
            if (is_numeric($param['field'])) {
                // This is a custom field.
                $idx = count($whereparts);
                $join .= " LEFT JOIN {user_info_data} uid{$idx} ON uid{$idx}.userid = u.id AND uid{$idx}.fieldid = :fieldid{$idx} ";
                $params["fieldid{$idx}"] = $param['field'];
                $whereparts[] = "uid{$idx}.id IS NOT NULL";
            } else {
                // This is a field from {user} table.
                if ($param['field'] == 'picture') {
                    // The picture field is numeric and requires special handling.
                    $whereparts[] = "u.{$param['field']} != 0";
                } else {
                    $whereparts[] = $DB->sql_isnotempty('u', "u.{$param['field']}", false, true);
                }
            }
        }

        if ($whereparts) {
            $where = " AND (" . implode($rule, $whereparts) . ")";
        } else {
            $where = '';
        }
        return array($join, $where, $params);
    }
}
