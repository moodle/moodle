<?php

namespace local_intelliboard\helpers;

class SQLEntityHelper
{
    public static function cohortMembersJoin($userid, $useridcoumn, $cohortsids = [], $leftjoin = false)
    {
        if (!$cohortsids) {
            $cohortsids = implode(",", array_keys(user_cohorts($userid)));
        } else {
            $cohortsids = implode(", ", $cohortsids);
        }

        if (!$cohortsids) {
            $cohortsids = "-1";
        }

        $sql = "JOIN (SELECT userid
                        FROM {cohort_members}
                       WHERE id > 0 AND cohortid IN ({$cohortsids})
                    GROUP BY userid
                     ) cm ON cm.userid = {$useridcoumn}";

        return $leftjoin ? "LEFT {$sql}" : $sql;
    }
}