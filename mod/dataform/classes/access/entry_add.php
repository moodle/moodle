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
 * mod_dataform entry add permission class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry_add extends entry_base {


    /**
     * @return bool
     */
    public static function validate($params) {
        $dataformid = $params['dataformid'];

        $df = \mod_dataform_dataform::instance($dataformid);

        // Cannot add in a view that does not allow submission.
        if (!empty($params['viewid'])) {
            $view = $df->view_manager->get_view_by_id($params['viewid']);
            if (!$view or !$view->allows_submission()) {
                return false;;
            }
        }
        // User at max entries (per interval).
        if ($df->user_at_max_entries(true)) {
            // No more entries for you (come back next interval or so).
            return false;
        }

        // Early entries.
        if ($df->is_early()) {
            $params['capabilities'] = array('mod/dataform:entryearlyadd');
            if (!parent::validate($params)) {
                return false;
            }
        }

        // Late entries.
        if ($df->is_past_due()) {
            $params['capabilities'] = array('mod/dataform:entrylateadd');
            if (!parent::validate($params)) {
                return false;
            }
        }

        $entry = !empty($params['entry']) ? $params['entry'] : \mod_dataform\pluginbase\dataformentry::blank_instance($df);

        // Own entry.
        if (\mod_dataform\pluginbase\dataformentry::is_own($entry)) {
            $params['capabilities'] = array('mod/dataform:entryownadd');
            return parent::validate($params);
        }

        // Group entry.
        if (\mod_dataform\pluginbase\dataformentry::is_grouped($entry)) {
            if (groups_is_member($entry->groupid)) {
                $params['capabilities'] = array('mod/dataform:entrygroupadd');
                return parent::validate($params);
            }
        }

        // Anonymous entry.
        if (\mod_dataform\pluginbase\dataformentry::is_anonymous($entry)) {
            if ((isguestuser() or !isloggedin()) and $df->anonymous) {
                return true;
            }
            $params['capabilities'] = array('mod/dataform:entryanonymousadd');
            return parent::validate($params);
        }

        // Any entry.
        if (\mod_dataform\pluginbase\dataformentry::is_others($entry)) {
            $params['capabilities'] = array('mod/dataform:entryanyadd');
            return parent::validate($params);
        }

        return false;
    }
}
