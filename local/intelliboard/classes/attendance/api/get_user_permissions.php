<?php

namespace local_intelliboard\attendance\api;

use local_intelliboard\reports\report_trait;

class get_user_permissions extends base
{
    use report_trait;

    const PERMISSIONS_TYPE_ASSIGNS = 1;
    const PERMISSIONS_TYPE_TEACHER_ENROLMENTS = 2;
    const PERMISSIONS_TYPE_STUDENT_ENROLMENTS = 2;

    public function run($params) {
        $type = json_decode($params['report_params'])->type;

        if ($type === self::PERMISSIONS_TYPE_ASSIGNS) {
            return $this->assign_permissions($params);
        } elseif ($type === self::PERMISSIONS_TYPE_TEACHER_ENROLMENTS) {
            return $this->teacher_enrol_permissions($params);
        } else {
            return $this->student_enrol_permissions($params);
        }
    }

    private function student_enrol_permissions($params) {
        list($rolesfiltersql, $rolesfilterparams) = $this->moodledb->get_in_or_equal(
            $this->get_student_roles(),
            SQL_PARAMS_NAMED,
            'std'
        );

        $courses = $this->moodledb->get_records_sql(
            "SELECT DISTINCT ctx.instanceid
               FROM {role_assignments} ra
               JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.userid = :userid
              WHERE ra.roleid {$rolesfiltersql}",
            array_merge(['userid' => $params['userid']], $rolesfilterparams)
        );
        $courses = $courses ? array_keys($courses) : [-1];

        return [
            'courses' => $courses,
            'users' => [$params['userid']],
        ];
    }

    private function teacher_enrol_permissions($params) {
        list($rolesfiltersql, $rolesfilterparams) = $this->moodledb->get_in_or_equal(
            $this->get_teacher_roles(),
            SQL_PARAMS_NAMED,
            'tcrs'
        );

        $courses = $this->moodledb->get_records_sql(
            "SELECT DISTINCT ctx.instanceid
               FROM {role_assignments} ra
               JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.userid = :userid
              WHERE ra.roleid {$rolesfiltersql}",
            array_merge(['userid' => $params['userid']], $rolesfilterparams)
        );
        $courses = $courses ? array_keys($courses) : [-1];

        list($coursesfiltersqlsql, $coursesfilterparams) = $this->moodledb->get_in_or_equal($courses);

        $users = $this->moodledb->get_records_sql(
            "SELECT DISTINCT ra.userid
                   FROM {role_assignments} ra
                   JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ctx.instanceid {$coursesfiltersqlsql}",
            $coursesfilterparams
        );
        $users = $users ? array_keys($users) : [-1];

        return [
            'courses' => $courses,
            'users' => $users,
        ];
    }

    private function assign_permissions($params) {
        $assigns = $this->moodledb->get_records('local_intelliboard_assign', ['userid' => $params['userid']]);

        $assignusers = [];
        $assigncourses = [];
        $assigncohorts = [];
        $assigncategories = [];
        $assignfields = [];

        foreach ($assigns as  $assign) {
            if ($assign->type == 'users') {
                $assignusers[] = (int) $assign->instance;
            } elseif ($assign->type == 'courses') {
                $assigncourses[] = (int) $assign->instance;
            } elseif ($assign->type == 'categories') {
                $assigncategories[] = (int) $assign->instance;
            } elseif ($assign->type == 'cohorts') {
                $assigncohorts[] = (int) $assign->instance;
            } elseif ($assign->type == 'fields') {
                $assignfields[] = $assign->instance;
            }
        }

        if ($assignfields) {
            $assignusers = array_merge($assignusers, $this->get_cupf_users($assignfields));
        }

        if ($assigncategories) {
            $assigncourses = array_merge($assigncourses, $this->get_categories_courses($assigncategories));
        }

        $permissionsusers = $this->get_permissions_users($assignusers, $assigncourses, $assigncohorts);
        $permissionscourses = $this->get_permissions_courses($assignusers, $assigncourses, $assigncohorts);

        return [
            'users' => $permissionsusers,
            'courses' => $permissionscourses,
        ];
    }

    /**
     * @param $assignusers
     * @param $assigncourses
     * @param $assigncohorts
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_permissions_users($assignusers, $assigncourses, $assigncohorts) {
        $permissionsusers = $assignusers;

        if ($assigncohorts) {
            $result = $this->moodledb->get_records_list('cohort_members', 'cohortid', $assigncohorts);
            if ($result) {
                foreach ($result as $value) {
                    $permissionsusers[] = intval($value->userid);
                }
            }
        }
        if ($assigncourses) {
            list($coursesfiltersqlsql, $coursesfilterparams) = $this->moodledb->get_in_or_equal($assigncourses);
            $result = $this->moodledb->get_records_sql(
                "SELECT DISTINCT ra.userid
                   FROM {role_assignments} ra
                   JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ctx.instanceid {$coursesfiltersqlsql}",
                $coursesfilterparams
            );
            if ($result) {
                foreach ($result as $value) {
                    $permissionsusers[] = intval($value->userid);
                }
            }
        }

        return array_unique($permissionsusers);
    }

    private function get_permissions_courses($assignusers, $assigncourses, $assigncohorts) {
        $permissionscourses = $assigncourses;

        if ($assignusers) {
            list($usersfiltersqlsql, $usersfilterparams) = $this->moodledb->get_in_or_equal($assignusers);
            $result = $this->moodledb->get_records_sql(
                "SELECT distinct ctx.instanceid
                   FROM {role_assignments} ra
                   JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.userid {$usersfiltersqlsql}",
                $usersfilterparams
            );

            foreach ($result as $value) {
                $permissionscourses[] = intval($value->instanceid);
            }
        }
        if ($assigncohorts) {
            $cohortmembers = $this->moodledb->get_records_list('cohort_members', 'cohortid', $assigncohorts);
            $cohortmembers = array_map(function ($item) {
                return $item->userid;
            }, $cohortmembers);

            if ($cohortmembers) {
                list($usersfiltersqlsql, $usersfilterparams) = $this->moodledb->get_in_or_equal($cohortmembers);
                $courses = $this->moodledb->get_records_sql(
                    "SELECT distinct ctx.instanceid
                       FROM {role_assignments} ra
                       JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.userid {$usersfiltersqlsql}",
                    $usersfilterparams
                );
                foreach ($courses as $value) {
                    $permissionscourses[] = intval($value->instanceid);
                }
            }
        }

        return array_unique($permissionscourses);
    }

    /**
     * @param $assignfields
     * @return array
     * @throws \dml_exception
     */
    private function get_cupf_users($assignfields) {
        $sqlfilter = [];
        $sqlparams = [];
        $users = [];

        foreach ($assignfields as $key => $field) {
            $fieldid = intval(explode("|", $field)[0]);
            $value = explode("|", $field)[1];
            $sqlfilter[] = "fieldid = :field{$key} AND " . $this->moodledb->sql_like('data', ":data{$key}", false, false);
            $sqlparams["data{$key}"] = "$value";
            $sqlparams["field{$key}"] = $fieldid;
        }

        $sqlfilter = " AND (". implode(") OR (", $sqlfilter) . ")";
        $cupfdata = $this->moodledb->get_records_sql(
            "SELECT DISTINCT userid FROM {user_info_data} WHERE data <> '' {$sqlfilter}",
            $sqlparams
        );

        if ($cupfdata) {
            foreach ($cupfdata as $item) {
                $users[] = intval($item->userid);
            }
        }

        return $users;
    }

    /**
     * @param $assigncategories
     * @return array
     */
    private function get_categories_courses($assigncategories) {
        if (file_exists($this->moodlecfg->libdir.'/coursecatlib.php')) {
            require_once($this->moodlecfg->libdir.'/coursecatlib.php');
            $categories = \coursecat::get_many($assigncategories);
        } else {
            $categories = \core_course_category::get_many($assigncategories);
        }

        $courses = [];

        foreach ($categories as $category) {
            if (!$category) {
                continue;
            }

            $childrencourses = $category->get_courses(['recursive'=>true]);
            foreach($childrencourses as $course) {
                $courses[] = intval($course->id);
            }
        }

        return $courses;
    }
}