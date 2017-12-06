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
 * mod_dataform access validators.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\access;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_dataform field update permission class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_view extends base {

    /**
     * Validates that current user can view the field content.
     *
     * @return bool
     */
    public static function validate($params) {
        global $USER;

        $dataformid = $params['dataformid'];
        $df = \mod_dataform_dataform::instance($dataformid);

        // Must have fieldid.
        if (empty($params['fieldid'])) {
            return false;
        }
        $field = $df->field_manager->get_field_by_id($params['fieldid']);
        $params['field'] = $field;

        // Must have entry.
        $entry = $params['entry'];

        // Get blank instance for new entries.
        if ($entry->id < 0) {
            $entry = \mod_dataform\pluginbase\dataformentry::blank_instance($df);
        }

        if ($field->visible != $field::VISIBLE_ALL) {
            // Entry managers can access any field.
            if (!$canmanageentries = has_capability('mod/dataform:manageentries', $field->df->context)) {

                if ($field->visible == $field::VISIBLE_NONE) {
                    return false;
                }

                $userid = !empty($entry->userid) ? $entry->userid : 0;
                $isentryowner = ($USER->id == $userid);
                if ($field->visible == $field::VISIBLE_OWNER and !$isentryowner) {
                    return false;
                }
            }
        }

        // Early access.
        if ($df->is_early()) {
            $params['capabilities'] = array('mod/dataform:entryearlyview');
            if (!parent::validate($params)) {
                return false;
            }
        }

        // Late access.
        if ($df->is_past_due()) {
            $params['capabilities'] = array('mod/dataform:entrylateview');
            if (!parent::validate($params)) {
                return false;
            }
        }

        // Own entry.
        if (\mod_dataform\pluginbase\dataformentry::is_own($entry)) {
            $params['capabilities'] = array('mod/dataform:entryownview');
            return parent::validate($params);
        }

        // Group entry.
        if (\mod_dataform\pluginbase\dataformentry::is_grouped($entry)) {
            $params['capabilities'] = array('mod/dataform:entrygroupview');
            return parent::validate($params);
        }

        // Anonymous entry.
        if (\mod_dataform\pluginbase\dataformentry::is_anonymous($entry)) {
            $params['capabilities'] = array('mod/dataform:entryanonymousview');
            return parent::validate($params);
        }

        // Any entry.
        if (\mod_dataform\pluginbase\dataformentry::is_others($entry)) {
            $params['capabilities'] = array('mod/dataform:entryanyview');
            return parent::validate($params);
        }

        return false;
    }

    /**
     * @return null|array
     */
    public static function get_rules(\mod_dataform_access_manager $man, array $params) {
        return $man->get_type_rules('field');
    }

    /**
     * @return array
     */
    public static function get_capabilities() {
        return array();
    }
}
