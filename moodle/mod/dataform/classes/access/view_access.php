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
 * mod_dataform view access permission class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_access extends base {

    /**
     * @return bool
     */
    public static function validate($params) {
        $dataformid = $params['dataformid'];
        $viewid = $params['viewid'];

        $df = \mod_dataform_dataform::instance($dataformid);
        $view = $df->view_manager->get_view_by_id($viewid);

        // Views manager can access any view in any mode.
        if (has_capability('mod/dataform:manageviews', $df->context)) {
            return true;
        }

        // Visible/Hidden view.
        $params['capabilities'] = array('mod/dataform:viewaccess');
        if ($view->visible and !parent::validate($params)) {
            return false;
        }

        // Disabled view.
        $params['capabilities'] = array('mod/dataform:viewaccessdisabled');
        if (!$view->visible and !parent::validate($params)) {
            return false;
        }

        // Early access.
        $params['capabilities'] = array('mod/dataform:viewaccessearly');
        if ($df->is_early() and !parent::validate($params)) {
            return false;
        }

        // Late access.
        $params['capabilities'] = array('mod/dataform:viewaccesslate');
        if ($df->is_past_due() and !parent::validate($params)) {
            return false;
        }

        return true;
    }

    /**
     * @return null|array
     */
    public static function get_rules(\mod_dataform_access_manager $man, array $params) {
        return $man->get_type_rules('view');
    }

    /**
     * @return array
     */
    public static function get_capabilities() {
        return array(
            'mod/dataform:viewaccess',
            'mod/dataform:viewaccessdisabled',
            'mod/dataform:viewaccessearly',
            'mod/dataform:viewaccesslate',
        );
    }
}
