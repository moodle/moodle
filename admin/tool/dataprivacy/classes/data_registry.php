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
use tool_dataprivacy\purpose;
use tool_dataprivacy\category;
use tool_dataprivacy\contextlevel;
use tool_dataprivacy\context_instance;

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
     * @var array Inheritance between context levels.
     */
    private static $contextlevelinheritance = [
        CONTEXT_USER => [CONTEXT_SYSTEM],
        CONTEXT_COURSECAT => [CONTEXT_SYSTEM],
        CONTEXT_COURSE => [CONTEXT_COURSECAT, CONTEXT_SYSTEM],
        CONTEXT_MODULE => [CONTEXT_COURSE, CONTEXT_COURSECAT, CONTEXT_SYSTEM],
        CONTEXT_BLOCK => [CONTEXT_COURSE, CONTEXT_COURSECAT, CONTEXT_SYSTEM],
    ];

    /**
     * Returns purpose and category var names from a context class name
     *
     * @param string $classname
     * @return string[]
     */
    public static function var_names_from_context($classname) {
        return [
            $classname . '_purpose',
            $classname . '_category',
        ];
    }

    /**
     * Returns the default purpose id and category id for the provided context level.
     *
     * The caller code is responsible of checking that $contextlevel is an integer.
     *
     * @param int $contextlevel
     * @return int|false[]
     */
    public static function get_defaults($contextlevel) {

        $classname = \context_helper::get_class_for_level($contextlevel);
        list($purposevar, $categoryvar) = self::var_names_from_context($classname);

        $purposeid = get_config('tool_dataprivacy', $purposevar);
        $categoryid = get_config('tool_dataprivacy', $categoryvar);

        if (empty($purposeid)) {
            $purposeid = false;
        }
        if (empty($categoryid)) {
            $categoryid = false;
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
    public static function get_effective_context_value(\context $context, $element, $forcedvalue=false) {

        if ($element !== 'purpose' && $element !== 'category') {
            throw new coding_exception('Only \'purpose\' and \'category\' are supported.');
        }
        $fieldname = $element . 'id';

        if ($forcedvalue === false) {
            $instance = context_instance::get_record_by_contextid($context->id, false);

            if (!$instance) {
                // If the instance does not have a value defaults to not set, so we grab the context level default as its value.
                $instancevalue = context_instance::NOTSET;
            } else {
                $instancevalue = $instance->get($fieldname);
            }
        } else {
            $instancevalue = $forcedvalue;
        }

        // Not set.
        if ($instancevalue == context_instance::NOTSET) {

            // The effective value varies depending on the context level.
            if ($context->contextlevel == CONTEXT_USER) {
                // Use the context level value as we don't allow people to set specific instances values.
                return self::get_effective_contextlevel_value($context->contextlevel, $element);
            } else {
                // Use the default context level value.
                list($purposeid, $categoryid) = self::get_effective_default_contextlevel_purpose_and_category(
                    $context->contextlevel
                );
                return self::get_element_instance($element, $$fieldname);
            }
        }

        // Specific value for this context instance.
        if ($instancevalue != context_instance::INHERIT) {
            return self::get_element_instance($element, $instancevalue);
        }

        // This context is using inherited so let's return the parent effective value.
        $parentcontext = $context->get_parent_context();
        if (!$parentcontext) {
            return false;
        }

        // The forced value should not be transmitted to parent contexts.
        return self::get_effective_context_value($parentcontext, $element);
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
     * @param int $forcedvalue Use this value as if this was this context level purpose.
     * @return \tool_dataprivacy\purpose|false
     */
    public static function get_effective_contextlevel_value($contextlevel, $element, $forcedvalue = false) {

        if ($element !== 'purpose' && $element !== 'category') {
            throw new coding_exception('Only \'purpose\' and \'category\' are supported.');
        }
        $fieldname = $element . 'id';

        if ($contextlevel != CONTEXT_SYSTEM && $contextlevel != CONTEXT_USER) {
            throw new \coding_exception('Only context_system and context_user values can be retrieved, no other context levels ' .
                'have a purpose or a category.');
        }

        if ($forcedvalue === false) {
            $instance = contextlevel::get_record_by_contextlevel($contextlevel, false);
            if (!$instance) {
                // If the context level does not have a value defaults to not set, so we grab the context level default as
                // its value.
                $instancevalue = context_instance::NOTSET;
            } else {
                $instancevalue = $instance->get($fieldname);
            }
        } else {
            $instancevalue = $forcedvalue;
        }

        // Not set -> Use the default context level value.
        if ($instancevalue == context_instance::NOTSET) {
            list($purposeid, $categoryid) = self::get_effective_default_contextlevel_purpose_and_category($contextlevel);
            return self::get_element_instance($element, $$fieldname);
        }

        // Specific value for this context instance.
        if ($instancevalue != context_instance::INHERIT) {
            return self::get_element_instance($element, $instancevalue);
        }

        if ($contextlevel == CONTEXT_SYSTEM) {
            throw new coding_exception('Something went wrong, system defaults should be set and we should already have a value.');
        }

        // If we reach this point is that we are inheriting so get the parent context level and repeat.
        $parentcontextlevel = reset(self::$contextlevelinheritance[$contextlevel]);

        // Forced value are intentionally not passed as the force value should only affect the immediate context level.
        return self::get_effective_contextlevel_value($parentcontextlevel, $element);
    }

    /**
     * Returns the effective default purpose and category for a context level.
     *
     * @param int $contextlevel
     * @param int $forcedpurposevalue Use this value as if this was this context level purpose.
     * @param int $forcedcategoryvalue Use this value as if this was this context level category.
     * @return int[]
     */
    public static function get_effective_default_contextlevel_purpose_and_category($contextlevel, $forcedpurposevalue = false,
                                                                                   $forcedcategoryvalue = false) {

        list($purposeid, $categoryid) = self::get_defaults($contextlevel);

        // Honour forced values.
        if ($forcedpurposevalue) {
            $purposeid = $forcedpurposevalue;
        }
        if ($forcedcategoryvalue) {
            $categoryid = $forcedcategoryvalue;
        }

        // Not set == INHERIT for defaults.
        if ($purposeid == context_instance::INHERIT || $purposeid == context_instance::NOTSET) {
            $purposeid = false;
        }
        if ($categoryid == context_instance::INHERIT || $categoryid == context_instance::NOTSET) {
            $categoryid = false;
        }

        if ($contextlevel != CONTEXT_SYSTEM && ($purposeid === false || $categoryid === false)) {
            foreach (self::$contextlevelinheritance[$contextlevel] as $parent) {

                list($parentpurposeid, $parentcategoryid) = self::get_defaults($parent);
                // Not set == INHERIT for defaults.
                if ($parentpurposeid == context_instance::INHERIT || $parentpurposeid == context_instance::NOTSET) {
                    $parentpurposeid = false;
                }
                if ($parentcategoryid == context_instance::INHERIT || $parentcategoryid == context_instance::NOTSET) {
                    $parentcategoryid = false;
                }

                if ($purposeid === false && $parentpurposeid) {
                    $purposeid = $parentpurposeid;
                }

                if ($categoryid === false && $parentcategoryid) {
                    $categoryid = $parentcategoryid;
                }
            }
        }

        // They may still be false, but we return anyway.
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
