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
 * mod_dataform entry view permission class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry_view extends entry_base {

    /**
     * @return bool
     */
    public static function validate($params) {
        $dataformid = $params['dataformid'];

        $df = \mod_dataform_dataform::instance($dataformid);

        // Unspecified entry.
        if (empty($params['entry'])) {
            return self::has_capability('mod/dataform:entryanyview', $params);
        }

        // Early entries.
        if ($df->is_early()) {
            $params['capabilities'] = array('mod/dataform:entryearlyview');
            if (!parent::validate($params)) {
                return false;
            }
        }

        // Late entries.
        if ($df->is_past_due()) {
            $params['capabilities'] = array('mod/dataform:entrylateview');
            if (!parent::validate($params)) {
                return false;
            }
        }

        $entry = !empty($params['entry']) ? $params['entry'] : \mod_dataform\pluginbase\dataformentry::blank_instance($df);

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

}
