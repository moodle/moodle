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
 * Data registry business logic methods. Mostly internal stuff.
 *
 * All methods should be considered part of the internal tool_dataprivacy API
 * unless something different is specified.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy;

use coding_exception;
use core\persistent;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/coursecatlib.php');

/**
 * Data registry business logic methods. Mostly internal stuff.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_registry {
    /**
     * Returns purpose and category var names from a context class name
     *
     * @param string $classname The context level's class.
     * @param string $pluginname The name of the plugin associated with the context level.
     * @return string[]
     */
    public static function var_names_from_context($classname, $pluginname = '') {
        $pluginname = trim($pluginname);
        if (!empty($pluginname)) {
            $categoryvar = $classname . '_' . $pluginname . '_category';
            $purposevar = $classname . '_' . $pluginname . '_purpose';
        } else {
            $categoryvar = $classname . '_category';
            $purposevar = $classname . '_purpose';
        }
        return [
            $purposevar,
            $categoryvar
        ];
    }

    /**
     * Returns the default purpose id and category id for the provided context level.
     *
     * The caller code is responsible of checking that $contextlevel is an integer.
     *
     * @param int $contextlevel The context level.
     * @param string $pluginname The name of the plugin associated with the context level.
     * @return int[]|false[]
     */
    public static function get_defaults($contextlevel, $pluginname = '') {
        $classname = \context_helper::get_class_for_level($contextlevel);
        list($purposevar, $categoryvar) = self::var_names_from_context($classname, $pluginname);

        $purposeid = get_config('tool_dataprivacy', $purposevar);
        $categoryid = get_config('tool_dataprivacy', $categoryvar);

        if (!empty($pluginname)) {
            list($purposevar, $categoryvar) = self::var_names_from_context($classname);
            // If the plugin-level doesn't have a default purpose set, try the context level.
            if ($purposeid == false) {
                $purposeid = get_config('tool_dataprivacy', $purposevar);
            }

            // If the plugin-level doesn't have a default category set, try the context level.
            if ($categoryid == false) {
                $categoryid = get_config('tool_dataprivacy', $categoryvar);
            }
        }

        if (empty($purposeid)) {
            $purposeid = context_instance::NOTSET;
        }
        if (empty($categoryid)) {
            $categoryid = context_instance::NOTSET;
        }

        return [$purposeid, $categoryid];
    }

    /**
     * Are data registry defaults set?
     *
     * At least the system defaults need to be set.
     *
     * @return bool
     */
    public static function defaults_set() {
        list($purposeid, $categoryid) = self::get_defaults(CONTEXT_SYSTEM);
        if (empty($purposeid) || empty($categoryid)) {
            return false;
        }
        return true;
    }

    /**
     * Returns all site categories that are visible to the current user.
     *
     * @return \coursecat[]
     */
    public static function get_site_categories() {
        global $DB;

        if (method_exists('\coursecat', 'get_all')) {
            $categories = \coursecat::get_all(['returnhidden' => true]);
        } else {
            // Fallback (to be removed once this gets integrated into master).
            $ids = $DB->get_fieldset_select('course_categories', 'id', '');
            $categories = \coursecat::get_many($ids);
        }

        foreach ($categories as $key => $category) {
            if (!$category->is_uservisible()) {
                unset($categories[$key]);
            }
        }
        return $categories;
    }

    /**
     * Returns the roles assigned to the provided level.
     *
     * Important to note that it returns course-level assigned roles
     * if the provided context level is below course.
     *
     * @param \context $context
     * @return array
     */
    public static function get_subject_scope(\context $context) {

        if ($contextcourse = $context->get_course_context(false)) {
            // Below course level we look at module or block level roles + course-assigned roles.
            $courseroles = get_roles_with_assignment_on_context($contextcourse);
            $roles = $courseroles + get_roles_with_assignment_on_context($context);
        } else {
            // We list category + system for others (we don't work with user instances so no need to work about them).
            $roles = get_roles_used_in_context($context);
        }

        return array_map(function($role) {
            if ($role->name) {
                return $role->name;
            } else {
                return $role->shortname;
            }
        }, $roles);
    }

    /**
     * Returns the effective value given a context instance
     *
     * @param \context $context
     * @param string $element 'category' or 'purpose'
     * @param int|false $forcedvalue Use this value as if this was this context instance value.
     * @return persistent|false It return a 'purpose' instance or a 'category' instance, depending on $element
     */
    public static function get_effective_context_value(\context $context, $element, $forcedvalue = false) {
        global $DB;

        if ($element !== 'purpose' && $element !== 'category') {
            throw new coding_exception('Only \'purpose\' and \'category\' are supported.');
        }
        $fieldname = $element . 'id';

        if (!empty($forcedvalue) && ($forcedvalue == context_instance::INHERIT)) {
            // Do not include the current context when calculating the value.
            // This has the effect that an inheritted value is calculated.
            $parentcontextids = $context->get_parent_context_ids(false);
        } else if (!empty($forcedvalue) && ($forcedvalue != context_instance::NOTSET)) {
            return self::get_element_instance($element, $forcedvalue);
        } else {
            // Fetch all parent contexts, including self.
            $parentcontextids = $context->get_parent_context_ids(true);
        }
        list($insql, $inparams) = $DB->get_in_or_equal($parentcontextids, SQL_PARAMS_NAMED);
        $inparams['contextmodule'] = CONTEXT_MODULE;

        if ('purpose' === $element) {
             $elementjoin = 'LEFT JOIN {tool_dataprivacy_purpose} ele ON ctxins.purposeid = ele.id';
             $elementfields = purpose::get_sql_fields('ele', 'ele');
        } else {
             $elementjoin = 'LEFT JOIN {tool_dataprivacy_category} ele ON ctxins.categoryid = ele.id';
             $elementfields = category::get_sql_fields('ele', 'ele');
        }
        $contextfields = \context_helper::get_preload_record_columns_sql('ctx');
        $fields = implode(', ', ['ctx.id', 'm.name AS modname', $contextfields, $elementfields]);

        $sql = "SELECT $fields
                  FROM {context} ctx
             LEFT JOIN {tool_dataprivacy_ctxinstance} ctxins ON ctx.id = ctxins.contextid
             LEFT JOIN {course_modules} cm ON ctx.contextlevel = :contextmodule AND ctx.instanceid = cm.id
             LEFT JOIN {modules} m ON m.id = cm.module
             {$elementjoin}
                 WHERE ctx.id {$insql}
              ORDER BY ctx.path DESC";
        $contextinstances = $DB->get_records_sql($sql, $inparams);

        // Check whether this context is a user context, or a child of a user context.
        // All children of a User context share the same context and cannot be set individually.
        foreach ($contextinstances as $record) {
            \context_helper::preload_from_record($record);
            $parent = \context::instance_by_id($record->id, false);

            if ($parent->contextlevel == CONTEXT_USER) {
                // Use the context level value for the user.
                return self::get_effective_contextlevel_value(CONTEXT_USER, $element);
            }
        }

        foreach ($contextinstances as $record) {
            $parent = \context::instance_by_id($record->id, false);

            $checkcontextlevel = false;
            if (empty($record->eleid)) {
                $checkcontextlevel = true;
            }

            if (!empty($forcedvalue) && context_instance::NOTSET == $forcedvalue) {
                $checkcontextlevel = true;
            }

            if ($checkcontextlevel) {
                // Check for a value at the contextlevel
                $forplugin = empty($record->modname) ? '' : $record->modname;
                list($purposeid, $categoryid) = self::get_effective_default_contextlevel_purpose_and_category(
                        $parent->contextlevel, false, false, $forplugin);

                $instancevalue = $$fieldname;

                if (context_instance::NOTSET != $instancevalue && context_instance::INHERIT != $instancevalue) {
                    // There is an actual value. Return it.
                    return self::get_element_instance($element, $instancevalue);
                }
            } else {
                $elementclass = "\\tool_dataprivacy\\{$element}";
                $instance = new $elementclass(null, $elementclass::extract_record($record, 'ele'));
                $instance->validate();

                return $instance;
            }
        }

        throw new coding_exception('Something went wrong, system defaults should be set and we should already have a value.');
    }

    /**
     * Returns the effective value for a context level.
     *
     * Note that this is different from the effective default context level
     * (see get_effective_default_contextlevel_purpose_and_category) as this is returning
     * the value set in the data registry, not in the defaults page.
     *
     * @param int $contextlevel
     * @param string $element 'category' or 'purpose'
     * @return \tool_dataprivacy\purpose|false
     */
    public static function get_effective_contextlevel_value($contextlevel, $element) {
        if ($element !== 'purpose' && $element !== 'category') {
            throw new coding_exception('Only \'purpose\' and \'category\' are supported.');
        }
        $fieldname = $element . 'id';

        if ($contextlevel != CONTEXT_SYSTEM && $contextlevel != CONTEXT_USER) {
            throw new \coding_exception('Only context_system and context_user values can be retrieved, no other context levels ' .
                'have a purpose or a category.');
        }

        list($purposeid, $categoryid) = self::get_effective_default_contextlevel_purpose_and_category($contextlevel);

        // Note: The $$fieldname points to either $purposeid, or $categoryid.
        if (context_instance::NOTSET != $$fieldname && context_instance::INHERIT != $$fieldname) {
            // There is a specific value set.
            return self::get_element_instance($element, $$fieldname);
        }

        throw new coding_exception('Something went wrong, system defaults should be set and we should already have a value.');
    }

    /**
     * Returns the effective default purpose and category for a context level.
     *
     * @param int $contextlevel
     * @param int|bool $forcedpurposevalue Use this value as if this was this context level purpose.
     * @param int|bool $forcedcategoryvalue Use this value as if this was this context level category.
     * @param string $component The name of the component to check.
     * @return int[]
     */
    public static function get_effective_default_contextlevel_purpose_and_category($contextlevel, $forcedpurposevalue = false,
                                                                                   $forcedcategoryvalue = false, $component = '') {
        // Get the defaults for this context level.
        list($purposeid, $categoryid) = self::get_defaults($contextlevel, $component);

        // Honour forced values.
        if ($forcedpurposevalue) {
            $purposeid = $forcedpurposevalue;
        }
        if ($forcedcategoryvalue) {
            $categoryid = $forcedcategoryvalue;
        }

        if ($contextlevel == CONTEXT_USER) {
            // Only user context levels inherit from a parent context level.
            list($parentpurposeid, $parentcategoryid) = self::get_defaults(CONTEXT_SYSTEM);

            if (context_instance::INHERIT == $purposeid || context_instance::NOTSET == $purposeid) {
                $purposeid = (int)$parentpurposeid;
            }

            if (context_instance::INHERIT == $categoryid || context_instance::NOTSET == $categoryid) {
                $categoryid = $parentcategoryid;
            }
        }

        return [$purposeid, $categoryid];
    }

    /**
     * Returns an instance of the provided element.
     *
     * @throws \coding_exception
     * @param string $element The element name 'purpose' or 'category'
     * @param int $id The element id
     * @return \core\persistent
     */
    private static function get_element_instance($element, $id) {
        if ($element !== 'purpose' && $element !== 'category') {
            throw new coding_exception('No other elements than purpose and category are allowed');
        }

        $classname = '\tool_dataprivacy\\' . $element;
        return new $classname($id);
    }
}
