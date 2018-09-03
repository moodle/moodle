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
 * Target class.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

/**
 * Target class.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class target {

    /**
     * @var TARGET_SELECTOR The target is a CSS selector.
     */
    const TARGET_SELECTOR = 0;

    /**
     * @var TARGET_BLOCK The target is a block.
     */
    const TARGET_BLOCK = 1;

    /**
     * @var TARGET_UNATTACHED The target is unattached to any specific node.
     */
    const TARGET_UNATTACHED = 2;

    /**
     * @var     array   $mapping    The list of target type to target name.
     */
    protected static $mapping = [
        self::TARGET_BLOCK      => 'block',
        self::TARGET_SELECTOR   => 'selector',
        self::TARGET_UNATTACHED => 'unattached',
    ];

    /**
     * Return the name of the class for this target type.
     *
     * @param   int     $type       The type of target.
     * @return  string              The class name.
     */
    public static function get_classname($type) {
        $targettype = self::$mapping[self::get_target_constant($type)];
        return "\\tool_usertours\\local\\target\\{$targettype}";
    }

    /**
     * Return the instance of the class for this target.
     *
     * @param   step    $step       The step.
     * @return  target              The target instance.
     */
    public static function get_target_type(step $step) {
        if (!isset(self::$mapping[$step->get_targettype()])) {
            throw new \moodle_exception('Unknown Target type');
        }

        $targettype = self::$mapping[$step->get_targettype()];
        return "\\tool_usertours\\local\\target\\{$targettype}";
    }

    /**
     * Return the constant used to describe this target.
     *
     * @param   string  $type       The type of the target.
     * @return  int                 The constant for this target.
     */
    public static function get_target_constant($type) {
        return array_search($type, self::$mapping);
    }

    /**
     * Return the constant used to describe this class.
     *
     * @param   string  $classname  The fully-qualified class name of the target
     * @return  int                 The constant for this target.
     */
    public static function get_target_constant_for_class($classname) {
        $rc = new \ReflectionClass($classname);

        return self::get_target_constant($rc->getShortName());
    }

    /**
     * Return the instance of the class for this target.
     *
     * @param   step    $step       The step.
     * @return  target              The target instance.
     */
    public static function get_target_instance(step $step) {
        $targetclass = self::get_target_type($step);
        return new $targetclass($step);
    }

    /**
     * Return the complete lits of target types.
     *
     * @return  array
     */
    public static function get_target_types() {
        return self::$mapping;
    }
}
