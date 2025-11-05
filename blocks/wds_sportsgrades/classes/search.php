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
 * Search functionality for the Sports Grades block.
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wds_sportsgrades;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for searching student athletes.
 */
class search {

    /**
     * Search for student athletes based on criteria.
     *
     * @param array $parms Search parameters
     * @return array Search results
     */
    public static function search_students($parms) {
        global $DB, $USER;

        // Get our settings.
        $s = get_config('block_wds_sportsgrades');

        // Let's work with arrays.
        $parms = json_decode(json_encode($parms), true);

        // Check if user has access to any sports.
        $access = self::get_user_access($USER->id);

        if (empty($access)) {
            return ['error' => get_string('noaccess', 'block_wds_sportsgrades')];
        }

        $sqlmentors = " FROM {user} u
            INNER JOIN {enrol_wds_students} stu
                ON u.id = stu.userid
            INNER JOIN {enrol_wds_students_meta} sm
                ON sm.studentid = stu.id
            INNER JOIN {enrol_wds_periods} per
                ON per.academic_period_id = sm.academic_period_id
                AND per.start_date - (86400 * $s->daysprior) <= UNIX_TIMESTAMP()
                AND per.end_date + (86400 * $s->daysafter) >= UNIX_TIMESTAMP()
            INNER JOIN mdl_block_wds_sportsgrades_mentor sgm ON sgm.userid = u.id AND sgm.mentorid = " . $USER->id . "
            INNER JOIN (
                SELECT
                    stumeta.studentid,
                    stumeta.academic_period_id,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Athletic_Team_ID' THEN stumeta.data ELSE NULL END) AS sport,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Academic_Unit_Code' THEN units.academic_unit ELSE NULL END) AS college,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Program_of_Study_Code' THEN programs.program_of_study ELSE NULL END) AS major,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Classification' THEN stumeta.data ELSE NULL END) AS classification
                FROM {enrol_wds_students_meta} stumeta
                INNER JOIN {enrol_wds_periods} per2
                    ON per2.academic_period_id = stumeta.academic_period_id
                    AND per2.start_date - (86400 * $s->daysprior) <= UNIX_TIMESTAMP()
                    AND per2.end_date + (86400 * $s->daysafter) >= UNIX_TIMESTAMP()
                JOIN (
                    SELECT DISTINCT stu.id AS studentid, sm.academic_period_id
                        FROM {user} u
                        JOIN {enrol_wds_students} stu ON u.id = stu.userid
                        JOIN {enrol_wds_students_meta} sm ON sm.studentid = stu.id
                ) filtered_students
                    ON stumeta.studentid = filtered_students.studentid
                    AND stumeta.academic_period_id = filtered_students.academic_period_id
                LEFT JOIN {enrol_wds_units} units
                    ON stumeta.datatype = 'Academic_Unit_Code' AND stumeta.data = units.academic_unit_code
                LEFT JOIN {enrol_wds_programs} programs
                    ON stumeta.datatype = 'Program_of_Study_Code' AND stumeta.data = programs.program_of_study_code
                GROUP BY stumeta.studentid,stumeta.academic_period_id
            ) p
                ON p.studentid = stu.id
                AND p.academic_period_id = per.academic_period_id
            WHERE sm.datatype = 'Athletic_Team_ID'";

        // Build the SQL query.
        $sqlselect = "SELECT CONCAT(sm.id, '-', u.id) as uniqueid,
            sm.id AS stumetaid,
            u.id AS userid,
            stu.id AS studentid,
            u.username,
            u.firstname,
            u.lastname,
            u.email,
            stu.universal_id,
            p.sport,
            p.college,
            p.major,
            p.classification";

        $sqlfrom = " FROM {user} u
            INNER JOIN {enrol_wds_students} stu
                ON u.id = stu.userid
            INNER JOIN {enrol_wds_students_meta} sm
                ON sm.studentid = stu.id
            INNER JOIN {enrol_wds_periods} per
                ON per.academic_period_id = sm.academic_period_id
                AND per.start_date - (86400 * $s->daysprior) <= UNIX_TIMESTAMP()
                AND per.end_date + (86400 * $s->daysafter) >= UNIX_TIMESTAMP()
            INNER JOIN (
                SELECT 
                    stumeta.studentid,
                    stumeta.academic_period_id,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Athletic_Team_ID' THEN stumeta.data ELSE NULL END) AS sport,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Academic_Unit_Code' THEN units.academic_unit ELSE NULL END) AS college,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Program_of_Study_Code' THEN programs.program_of_study ELSE NULL END) AS major,
                    GROUP_CONCAT(CASE WHEN stumeta.datatype = 'Classification' THEN stumeta.data ELSE NULL END) AS classification
                FROM {enrol_wds_students_meta} stumeta
                INNER JOIN {enrol_wds_periods} per2
                    ON per2.academic_period_id = stumeta.academic_period_id
                    AND per2.start_date - (86400 * $s->daysprior) <= UNIX_TIMESTAMP()
                    AND per2.end_date + (86400 * $s->daysafter) >= UNIX_TIMESTAMP()
                JOIN (
                    SELECT DISTINCT stu.id AS studentid, sm.academic_period_id
                        FROM {user} u
                        JOIN {enrol_wds_students} stu ON u.id = stu.userid
                        JOIN {enrol_wds_students_meta} sm ON sm.studentid = stu.id
                ) filtered_students
                    ON stumeta.studentid = filtered_students.studentid
                    AND stumeta.academic_period_id = filtered_students.academic_period_id
                LEFT JOIN {enrol_wds_units} units
                    ON stumeta.datatype = 'Academic_Unit_Code' AND stumeta.data = units.academic_unit_code
                LEFT JOIN {enrol_wds_programs} programs
                    ON stumeta.datatype = 'Program_of_Study_Code' AND stumeta.data = programs.program_of_study_code
                GROUP BY stumeta.studentid, stumeta.academic_period_id
            ) p
                ON p.studentid = stu.id
                AND p.academic_period_id = per.academic_period_id
            WHERE sm.datatype = 'Athletic_Team_ID'";

        $conditions = [];
        $mconditions = [];
        $parmssql = [];
        $mparmssql = [];

        // Filter by sport access permissions.
        if (!empty($access['sports']) && !$access['all_students']) {
            $sportplaceholders = [];
            $i = 0;
            foreach ($access['sports'] as $sportcode) {
                $parmname = 'sport' . $i;
                $sportplaceholders[] = ':' . $parmname;
                $parmssql[$parmname] = $sportcode;
                $mparmssql[$parmname] = $sportcode;
                $i++;
            }
            $conditions[] = "sm.data IN (" . implode(',', $sportplaceholders) . ")";
        }

        if (!empty($access['student_ids']) && !$access['all_students']) {
            $studentplaceholders = [];
            $i = 0;
            foreach ($access['student_ids'] as $studentid) {
                $mparmname = 'student' . $i;
                $studentplaceholders[] = ':' . $mparmname;
                $mparmssql[$mparmname] = $studentid;
                $i++;
            }

            if (!empty($mconditions)) {
                $mconditions[] = "OR u.id IN (" . implode(',', $studentplaceholders) . ")";
            } else {
                $mconditions[] = "u.id IN (" . implode(',', $studentplaceholders) . ")";
            }
        }

        // Apply search filters.
        if (!empty($parms['universal_id'])) {
            $conditions[] = "stu.universal_id LIKE :universal_id";
            $mconditions[] = "stu.universal_id LIKE :universal_id";
            $parmssql['universal_id'] = '%' . $parms['universal_id'] . '%';
            $mparmssql['universal_id'] = '%' . $parms['universal_id'] . '%';
        }

        if (!empty($parms['username'])) {
            $conditions[] = "u.username LIKE :username";
            $mconditions[] = "u.username LIKE :username";
            $parmssql['username'] = '%' . $parms['username'] . '%';
            $mparmssql['username'] = '%' . $parms['username'] . '%';
        }

        if (!empty($parms['firstname'])) {
            $conditions[] = "u.firstname LIKE :firstname";
            $mconditions[] = "u.firstname LIKE :firstname";
            $parmssql['firstname'] = '%' . $parms['firstname'] . '%';
            $mparmssql['firstname'] = '%' . $parms['firstname'] . '%';
        }

        if (!empty($parms['lastname'])) {
            $conditions[] = "u.lastname LIKE :lastname";
            $mconditions[] = "u.lastname LIKE :lastname";
            $parmssql['lastname'] = '%' . $parms['lastname'] . '%';
            $mparmssql['lastname'] = '%' . $parms['lastname'] . '%';
        }

        if (!empty($parms['major'])) {
            $conditions[] = "p.major LIKE :major";
            $mconditions[] = "p.major LIKE :major";
            $parmssql['major'] = '%' . $parms['major'] . '%';
            $mparmssql['major'] = '%' . $parms['major'] . '%';
        }

        if (!empty($parms['classification'])) {
            $conditions[] = "p.classification = :classification";
            $mconditions[] = "p.classification = :classification";
            $parmssql['classification'] = $parms['classification'];
            $mparmssql['classification'] = $parms['classification'];
        }

        if (!empty($parms['sport'])) {
            $conditions[] = "sm.data = :sport";
            $mconditions[] = "sm.data = :sport";
            $parmssql['sport'] = $parms['sport'];
            $mparmssql['sport'] = $parms['sport'];
        }

        // Build the WHERE clause.
        $sqlwhere = !empty($conditions) ? " AND " . implode(' AND ', $conditions) : "";
        $msqlwhere = !empty($mconditions) ? " AND " . implode(' AND ', $mconditions) : "";

        // Add ORDER BY clause to sort results.
        $sqlorder = " ORDER BY u.lastname ASC, u.firstname ASC";

        // Execute the query.
        $sql = $sqlselect . $sqlfrom . $sqlwhere . $sqlorder;
        $msql = $sqlselect . $sqlmentors . $msqlwhere . $sqlorder;

        try {
            // Get the sports students.
            if (!empty($access['sports']) || $access['all_students'] == true) {
                $students = $DB->get_records_sql($sql, $parmssql);
            } else {
                $students = [];
            }

            // Get the individual students.
            $mstudents = $DB->get_records_sql($msql, $mparmssql);

            // Merge the students and singletons together.
            $mergedstudents = $students + $mstudents;

            // Sort by first name, last name.
            uasort($mergedstudents, function($a, $b) {

                // Compare lastnames first.
                $lastcompare = strcasecmp($a->lastname, $b->lastname);
                if ($lastcompare !== 0) {
                    return $lastcompare;
                }

                // If lastnames are the same, compare firstnames.
                return strcasecmp($a->firstname, $b->firstname);
            });

            // Process results to add sports information.
            $results = [];
            $processed_student_ids = [];

            foreach ($mergedstudents as $student) {
                // Only process each student once to avoid duplicates.
                if (in_array($student->studentid, $processed_student_ids)) {
                    continue;
                }

                // Get sports for each student.
                $sports = self::get_student_sports($student->studentid);

                $student->sports = $sports;
                $results[] = $student;

                // Mark this student as processed.
                $processed_student_ids[] = $student->studentid;
            }

            return ['success' => true, 'results' => array_values($results)];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get sports that a student is enrolled in.
     *
     * @param int $studentid User ID of the student
     * @return array Array of sport objects with id, code, and name
     */
    public static function get_student_sports($studentid) {
        global $DB;

        // Get our settings.
        $s = get_config('block_wds_sportsgrades');

        $sql = "SELECT CONCAT(sm.id, '-', s.id) as uniqueid, s.id, s.code, s.name
            FROM {enrol_wds_sport} s
            INNER JOIN {enrol_wds_students_meta} sm
                ON sm.data = s.code
                AND sm.datatype = 'Athletic_Team_ID'
            INNER JOIN {enrol_wds_periods} per
                ON per.academic_period_id = sm.academic_period_id
                AND per.start_date - (86400 * $s->daysprior) <= UNIX_TIMESTAMP()
                AND per.end_date + (86400 * $s->daysafter) >= UNIX_TIMESTAMP()
            WHERE sm.studentid = :studentid
            GROUP BY uniqueid
            ORDER BY s.name ASC";

        $sports = $DB->get_records_sql($sql, ['studentid' => $studentid]);

        // Transform the result to use the sport id as the key.
        $result = [];
        foreach ($sports as $sport) {
            $result[$sport->id] = (object)[
                'id' => $sport->id,
                'code' => $sport->code,
                'name' => $sport->name
            ];
        }

        return $result;
    }

    /**
     * Get access permissions for a user.
     *
     * @param int $userid User ID
     * @return array Access permissions
     */
    public static function get_user_access($userid) {
        global $DB, $USER;

        // Build the access array of arrays.
        $access = [
            'sports' => [],
            'student_ids' => [],
            'all_students' => false
        ];

        // Get our settings.
        $s = get_config('block_wds_sportsgrades');

        // Admin can access all students if config allows.
        if (is_siteadmin() && $s->adminaccessall) {
            $access['all_students'] = true;
            return $access;
        }

        // Build the parms to fetch user sport access.
        $sparms = ['userid' => $userid];

        // Build the SQL to fetch user sport access.
        $ssql = 'SELECT sa.id, sa.userid, COALESCE(s.code, "") AS code
            FROM {block_wds_sportsgrades_access} sa
            LEFT JOIN {enrol_wds_sport} s ON sa.sportid = s.id
            WHERE sa.userid = :userid';

        // Get sports that the user has access to through the new access table.
        $sports_mentors = $DB->get_records_sql($ssql, $sparms);

        // Build the sports access array.
        foreach ($sports_mentors as $mentor) {
            if ($mentor->code == '') {
                $access['all_students'] = true;
            } else {
                $access['sports'][] = $mentor->code;
            }
        }

        // Get specific students that the user has access to.
        $usql = 'SELECT sm.id, sm.userid, "" AS code
            FROM {block_wds_sportsgrades_mentor} sm
            WHERE sm.mentorid = :userid';

        // Get sports that the user has access to through the new access table.
        $sports_users = $DB->get_records_sql($usql, $sparms);

        // Build the sports access array.
        foreach ($sports_users as $mentor) {

            $access['student_ids'][] = $mentor->userid;
        }

        return $access;
    }
}
