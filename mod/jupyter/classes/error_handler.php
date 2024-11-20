<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Provides function for creating an error message.
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_jupyter;

use context_module;
use core\notification;

/**
 * Error handler.
 *
 * @package mod_jupyter
 */
class error_handler {
    /**
     * Shows error message for jupyterhub connection error.
     * @param string $msg error message
     * @param context_module $modulecontext plugin module context
     */
    public static function jupyter_connect_err(string $msg, context_module $modulecontext) {
        if (has_capability('mod/jupyter:viewerrordetails', $modulecontext)) {
                notification::error(get_string('jupyter_connect_err_admin', 'jupyter', [
                    'url' => get_config('mod_jupyter', 'jupyterhub_url'),
                    'msg' => $msg
                ]));
        } else {
            notification::error(get_string('jupyter_connect_err', 'jupyter'));
        }
    }

    /**
     * Shows error message for jupyterhub response error.
     * @param string $msg error message
     * @param context_module $modulecontext plugin module context
     */
    public static function jupyter_resp_err(string $msg, context_module $modulecontext) {
        if (has_capability('mod/jupyter:viewerrordetails', $modulecontext)) {
            notification::error(get_string('jupyter_resp_err_admin', 'jupyter', [
                'url' => get_config('mod_jupyter', 'jupyterhub_url'),
                'msg' => $msg
            ]));
        } else {
            notification::error(get_string('jupyter_resp_err', 'jupyter'));
        }
    }

    /**
     * Shows error message for gradeservice connection error.
     * @param string $msg error message
     * @param context_module $modulecontext plugin module context
     */
    public static function gradeservice_connect_err(string $msg, context_module $modulecontext) {
        if (has_capability('mod/jupyter:viewerrordetails', $modulecontext)) {
            notification::error(get_string('gradeservice_connect_err_admin', 'jupyter', [
                'url' => get_config('mod_jupyter', 'gradeservice_url'),
                'msg' => $msg
            ]));
        } else {
            notification::error(get_string('gradeservice_connect_err', 'jupyter'));
        }
    }

    /**
     * Shows error message for gradeservice response error.
     * @param string $msg error message
     * @param context_module $modulecontext plugin module context
     */
    public static function gradeservice_resp_err(string $msg, context_module $modulecontext) {
        if (has_capability('mod/jupyter:viewerrordetails', $modulecontext)) {
            notification::error(get_string('gradeservice_resp_err_admin', 'jupyter', [
                'url' => get_config('mod_jupyter', 'gradeservice_url'),
                'msg' => $msg
            ]));
        } else {
            notification::error(get_string('gradeservice_resp_err', 'jupyter'));
        }
    }
}
