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

declare(strict_types=1);

namespace core_course\reportbuilder\datasource;

use core_course\reportbuilder\local\entities\course_category;
use core_course\reportbuilder\local\entities\access;
use core_course\reportbuilder\local\entities\completion;
use core_course\reportbuilder\local\entities\enrolment;
use core_group\reportbuilder\local\entities\group;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\database;

/**
 * Course participants datasource
 *
 * @package     core_course
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants extends datasource {

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $courseentity = new course();
        $course = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity);

        $this->set_main_table('course', $course);

        // Exclude site course.
        $paramsiteid = database::generate_param_name();
        $this->add_base_condition_sql("{$course}.id != :{$paramsiteid}", [$paramsiteid => SITEID]);

        // Join the course category entity.
        $coursecatentity = new course_category();
        $categories = $coursecatentity->get_table_alias('course_categories');
        $this->add_entity($coursecatentity
            ->add_join("JOIN {course_categories} {$categories} ON {$categories}.id = {$course}.category"));

        // Join the enrolments entity.
        $enrolmententity = new enrolment();
        $userenrolment = $enrolmententity->get_table_alias('user_enrolments');
        $enrol = $enrolmententity->get_table_alias('enrol');
        $enroljoin = "LEFT JOIN {enrol} {$enrol} ON {$enrol}.courseid = {$course}.id";
        $userenrolmentjoin = " LEFT JOIN {user_enrolments} {$userenrolment} ON {$userenrolment}.enrolid = {$enrol}.id";
        $enrolmententity->add_joins([$enroljoin, $userenrolmentjoin]);
        $this->add_entity($enrolmententity);

        // Join user entity.
        $userentity = new user();
        $user = $userentity->get_table_alias('user');
        $userentity->add_joins($enrolmententity->get_joins());
        $userentity->add_join("LEFT JOIN {user} {$user} ON {$userenrolment}.userid = {$user}.id AND {$user}.deleted = 0");
        $this->add_entity($userentity);

        // Join group entity.
        $groupentity = (new group())
            ->set_table_alias('context', $courseentity->get_table_alias('context'));
        $groups = $groupentity->get_table_alias('groups');

        // Sub-select for all course group members.
        $groupsinnerselect = "
            SELECT grs.*, grms.userid
              FROM {groups} grs
              JOIN {groups_members} grms ON grms.groupid = grs.id";

        $this->add_entity($groupentity
            ->add_join($courseentity->get_context_join())
            ->add_joins($userentity->get_joins())
            ->add_join("
                LEFT JOIN ({$groupsinnerselect}) {$groups}
                       ON {$groups}.courseid = {$course}.id
                      AND {$groups}.userid = {$user}.id")
        );

        // Join completion entity.
        $completionentity = new completion();
        $completion = $completionentity->get_table_alias('course_completion');
        $completionentity->add_joins($userentity->get_joins());
        $completionentity->add_join("
            LEFT JOIN {course_completions} {$completion}
                   ON {$completion}.course = {$course}.id AND {$completion}.userid = {$user}.id
        ");
        $completionentity->set_table_alias('user', $user);
        $this->add_entity($completionentity);

        // Join course access entity.
        $accessentity = new access();
        $lastaccess = $accessentity->get_table_alias('user_lastaccess');
        $accessentity->add_joins($userentity->get_joins());
        $accessentity->add_join("
            LEFT JOIN {user_lastaccess} {$lastaccess}
                   ON {$lastaccess}.userid = {$user}.id AND {$lastaccess}.courseid = {$course}.id
        ");
        $this->add_entity($accessentity);

        // Add all entities columns/filters/conditions.
        $this->add_all_from_entities();
    }

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('courseparticipants', 'course');
    }

    /**
     * Return the columns that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'course:coursefullnamewithlink',
            'enrolment:method',
            'user:fullnamewithlink',
        ];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'user:suspended',
            'user:confirmed',
        ];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'user:suspended',
            'user:confirmed',
        ];
    }
}
