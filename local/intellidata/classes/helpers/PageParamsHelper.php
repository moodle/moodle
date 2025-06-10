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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.

 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class PageParamsHelper {

    /**
     * Page type module.
     */
    const PAGETYPE_MODULE = 'module';
    /**
     * Page type course.
     */
    const PAGETYPE_COURSE = 'course';
    /**
     * Page type user.
     */
    const PAGETYPE_USER   = 'user';
    /**
     * Page type site.
     */
    const PAGETYPE_SITE   = 'site';
    /**
     * Page type system.
     */
    const PAGEPARAM_SYSTEM = 1;

    /**
     * Get params.
     *
     * @param $pageparams
     * @return mixed
     */
    public static function get_params($pageparams) {
        global $PAGE, $SITE;

        if (isset($PAGE->cm->id)) {
            $pageparams['page'] = self::PAGETYPE_MODULE;
            $pageparams['param'] = $PAGE->cm->id;
        } else if (isset($PAGE->course->id) && $SITE->id != $PAGE->course->id) {
            $pageparams['page'] = self::PAGETYPE_COURSE;
            $pageparams['param'] = $PAGE->course->id;
        } else {
            $pageparams['page'] = self::PAGETYPE_SITE;
            $pageparams['param'] = self::PAGEPARAM_SYSTEM;
        }

        $pageparams['time'] = isset($pageparams['time']) ? $pageparams['time'] : 0;

        return $pageparams;
    }

    /**
     * Get course ID.
     *
     * @param $pageparams
     * @return false|int|mixed
     */
    public static function get_courseid($pageparams) {

        switch($pageparams['page']) {
            case self::PAGETYPE_MODULE:
                $courseid = self::get_courseid_by_module($pageparams['param']);
                break;
            case self::PAGETYPE_COURSE:
                $courseid = $pageparams['param'];
                break;
            default:
                $courseid = 0;
        }

        return $courseid;
    }

    /**
     * Get course ID by module.
     *
     * @param $cmid
     * @return false|mixed
     * @throws \dml_exception
     */
    public static function get_courseid_by_module($cmid) {
        global $DB;
        return $DB->get_field_sql("SELECT c.id
                                         FROM {course} c, {course_modules} cm
                                        WHERE c.id = cm.course
                                          AND cm.id = :cmid",
            ['cmid' => $cmid]);
    }
}
