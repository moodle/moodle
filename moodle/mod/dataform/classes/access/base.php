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
 * mod_dataform base permission class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /**
     * @return bool
     */
    public static function validate($params) {
        $dataformid = $params['dataformid'];

        $accessman = \mod_dataform_access_manager::instance($dataformid);
        $accesstype = get_called_class();

        // If capabilities not specified, use the access class default.
        if (empty($params['capabilities'])) {
            $params['capabilities'] = $accesstype::get_capabilities();
        }

        $rulesapplied = false;
        if ($rules = $accesstype::get_rules($accessman, $params)) {
            foreach ($rules as $rule) {
                if ($rule->is_enabled() and $rule->is_applicable($params)) {
                    $rulesapplied = true;
                    foreach ($params['capabilities'] as $capability) {
                        if (!$rule->has_capability($capability)) {
                            return false;
                        }
                    }
                }
            }
        }

        if (!$rulesapplied) {
            $dataformcontext = \mod_dataform_dataform::instance($dataformid)->context;
            foreach ($params['capabilities'] as $capability) {
                if (!has_capability($capability, $dataformcontext)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public static function get_capabilities() {
        return array();
    }

    /**
     * Returns false if the capability is not granted in applicable rules.
     *
     * @param string    $capability Capability name.
     * @param array     $params
     * @return bool
     */
    public static function has_capability($capability, array $params) {
        $dataformid = $params['dataformid'];
        $accessman = \mod_dataform_access_manager::instance($dataformid);
        $accesstype = get_called_class();

        $rulesapplied = false;
        if ($rules = $accesstype::get_rules($accessman, $params)) {
            foreach ($rules as $rule) {
                if ($rule->is_enabled() and $rule->is_applicable($params)) {
                    $rulesapplied = true;
                    if (!$rule->has_capability($capability)) {
                        return false;
                    }
                }
            }
        }

        if (!$rulesapplied) {
            $dataformcontext = \mod_dataform_dataform::instance($dataformid)->context;
            if (!has_capability($capability, $dataformcontext)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Throws an exception if the capability is not granted in applicable rules.
     *
     * @param string    $capability Capability name.
     * @param array     $params
     * @return void
     */
    public static function require_capability($capability, array $params) {
        $dataformid = $params['dataformid'];
        $accessman = \mod_dataform_access_manager::instance($dataformid);
        $accesstype = get_called_class();

        $rulesapplied = false;
        if ($rules = $accesstype::get_rules($accessman, $params)) {
            foreach ($rules as $rule) {
                if ($rule->is_enabled() and $rule->is_applicable($params)) {
                    $rulesapplied = true;
                    $rule->require_capability($capability, $params);
                }
            }
        }

        if (!$rulesapplied) {
            $dataformcontext = \mod_dataform_dataform::instance($dataformid)->context;
            require_capability($capability, $dataformcontext);
        }
    }

    /**
     *
     */
    public static function get_rules(\mod_dataform_access_manager $man, array $params) {
        return array();
    }

}
