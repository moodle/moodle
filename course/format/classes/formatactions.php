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

namespace core_courseformat;

use core_courseformat\local\courseactions;
use core_courseformat\local\sectionactions;
use core_courseformat\local\cmactions;
use coding_exception;
use stdClass;

/**
 * Class to instantiate course format actions.
 *
 * This class is used to access course content actions.
 *
 * All course actions are divided into three main clases:
 * - course: actions related to the course.
 * - section: actions related to the sections.
 * - cm: actions related to the course modules.
 *
 * Format plugin can provide their own actions classes by extending the actions classes
 * with the following namespaces:
 * - course: format_{PLUGINNAME}\courseformat\courseactions
 * - section: format_{PLUGINNAME}\courseformat\sectionactions
 * - cm: format_{PLUGINNAME}\courseformat\cmactions
 *
 * There a static method to get the general formatactions instance:
 * - formatactions::instance($courseorid): returns an instance to access all available actions.
 *
 * The class also provides some convenience methods to get specific actions level on a specific course:
 * - formatactions::course($courseorid): returns an instance of the course actions class.
 * - formatactions::section($courseorid): returns an instance of the section actions class.
 * - formatactions::cm($courseorid): returns an instance of the cm actions class.
 *
 * There are two ways of executing actions. For example, to execute a section action
 * called "move_after" the options are:
 *
 * Option A: ideal for executing only one action.
 *
 * formatactions::section($courseid)->move_after($sectioninfo, $aftersectioninfo);
 *
 * Option B: when actions in the same course are going to be executed at different levels.
 *
 * $actions = formatactions::instance($courseid);
 * $actions->section->move_after($sectioninfo, $aftersectioninfo);
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class formatactions {
    /**
     * @var courseactions|null courseactions instance.
     */
    public courseactions $course;

    /**
     * @var sectionactions sectionactions instance.
     */
    public sectionactions $section;

    /**
     * @var cmactions cmactions instance.
     */
    public cmactions $cm;

    /**
     * Returns an instance of the actions class for the given course format.
     *
     * @param base $format the course format.
     */
    protected function __construct(base $format) {
        $actionclasses = [
            'course' => courseactions::class,
            'section' => sectionactions::class,
            'cm' => cmactions::class,
        ];
        foreach ($actionclasses as $action => $classname) {
            $formatalternative = 'format_' . $format->get_format() . '\\courseformat\\' . $action . 'actions';
            if (class_exists($formatalternative)) {
                if (!is_subclass_of($formatalternative, $classname)) {
                    throw new coding_exception("The \"$formatalternative\" must extend \"$classname\"");
                }
                $actionclasses[$action] = $formatalternative;
            }
            $this->$action = new $actionclasses[$action]($format->get_course());
        }
    }

    /**
     * Returns an instance of the actions class for the given course format.
     * @param int|stdClass $courseorid course id or record.
     * @return courseactions
     */
    public static function course($courseorid): courseactions {
        return self::instance($courseorid)->course;
    }

    /**
     * Returns an instance of the actions class for the given course format.
     *
     * @param int|stdClass $courseorid course id or record.
     * @return sectionactions
     */
    public static function section($courseorid): sectionactions {
        return self::instance($courseorid)->section;
    }

    /**
     * Returns an instance of the actions class for the given course format.
     * @param int|stdClass $courseorid course id or record.
     * @return cmactions
     */
    public static function cm($courseorid): cmactions {
        return self::instance($courseorid)->cm;
    }

    /**
     * Get a course action loader instance.
     * @param int|stdClass $courseorid course id or course.
     * @return self
     */
    public static function instance(int|stdClass $courseorid): self {
        $coursesectionscache = \cache::make('core', 'courseactionsinstances');
        $format = base::instance($courseorid);
        $courseid = $format->get_courseid();
        $cachekey = "{$courseid}_{$format->get_format()}";
        $cachedinstance = $coursesectionscache->get($cachekey);
        if ($cachedinstance) {
            return $cachedinstance;
        }
        $result = new self($format);
        $coursesectionscache->set($cachekey, $result);
        return $result;
    }
}
