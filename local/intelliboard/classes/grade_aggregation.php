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
 *
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

//DO NOT TOUCH THIS, please

defined('MOODLE_INTERNAL') || die();

class local_intelliboard_grade_aggregation
{

    public static function get_real_grade_avg($alias = 'g.', $round = 0, $alias_gi='gi.')
    {
        global $CFG;

        if ($CFG->dbtype == 'pgsql') {
            $group_concat = "string_agg({$alias}feedback, '; ')";
            $substring_index = "split_part(scale,',', cast(ROUND(AVG({$alias}finalgrade)) as int))";
            $case_round = "cast(ROUND(AVG({$alias}finalgrade), {$round}) as text)";
            $cast_as_int_start = 'cast(';
            $cast_as_int_end = ' as int)';
        } else {
            $group_concat = "GROUP_CONCAT(DISTINCT {$alias}feedback ORDER BY {$alias}feedback ASC SEPARATOR '; ')";
            $substring_index = "SUBSTRING_INDEX(SUBSTRING_INDEX(scale, ',', ROUND(AVG({$alias}finalgrade))), ',', -1)";
            $case_round = "ROUND(AVG({$alias}finalgrade), {$round})";
            $cast_as_int_start = '';
            $cast_as_int_end = '';
        }

        return "(CASE WHEN (AVG({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end})=MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end}))
                             OR (AVG({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end}) IS NULL AND MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end}) IS NULL)
                    THEN (CASE MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end})
                          WHEN 1 THEN (CASE MIN({$alias_gi}gradetype)
                                       WHEN 1 THEN {$case_round}
                                       WHEN 2 THEN (SELECT
                                                      {$substring_index}
                                                    FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                       WHEN 3 THEN {$group_concat}
                                       END)
                          WHEN 12 THEN CONCAT((CASE MIN({$alias_gi}gradetype)
                                               WHEN 1 THEN {$case_round}
                                               WHEN 2 THEN (SELECT
                                                              {$substring_index}
                                                            FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                               WHEN 3 THEN {$group_concat}
                                               END),' (',ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),'%)')
                          WHEN 13 THEN CONCAT((CASE MIN({$alias_gi}gradetype)
                                               WHEN 1 THEN {$case_round}
                                               WHEN 2 THEN (SELECT
                                                              {$substring_index}
                                                            FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                               WHEN 3 THEN {$group_concat}
                                               END),' (',CASE WHEN (SELECT gl.letter
                                                                    FROM {grade_letters} gl, {context} ctx
                                                                    WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                                    ORDER BY gl.lowerboundary
                                                                    LIMIT 1) IS NOT NULL
                                                        THEN (SELECT gl.letter
                                                              FROM {grade_letters} gl, {context} ctx
                                                              WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                              ORDER BY gl.lowerboundary
                                                              LIMIT 1)
                                                         ELSE
                                                           CASE
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                                           WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                                           ELSE ''
                                                           END
                                                         END,')')
                          WHEN 2 THEN CONCAT(ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),'%')
                          WHEN 21 THEN CONCAT(ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),
                                              '% (',CASE MIN({$alias_gi}gradetype)
                                                    WHEN 1 THEN {$case_round}
                                                    WHEN 2 THEN (SELECT
                                                                   {$substring_index}
                                                                 FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                                    WHEN 3 THEN {$group_concat}
                                                    END,')')
                          WHEN 23 THEN CONCAT(ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),
                                              '% (',CASE WHEN (SELECT gl.letter
                                                               FROM {grade_letters} gl, {context} ctx
                                                               WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                               ORDER BY gl.lowerboundary
                                                               LIMIT 1) IS NOT NULL
                                                    THEN (SELECT gl.letter
                                                          FROM {grade_letters} gl, {context} ctx
                                                          WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                          ORDER BY gl.lowerboundary
                                                          LIMIT 1)
                                                    ELSE
                                                      CASE
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                                      WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                                      ELSE ''
                                                      END
                                                    END,')')
                          WHEN 3 THEN CASE WHEN (SELECT gl.letter
                                                 FROM {grade_letters} gl, {context} ctx
                                                 WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                 ORDER BY gl.lowerboundary
                                                 LIMIT 1) IS NOT NULL
                                      THEN (SELECT gl.letter
                                            FROM {grade_letters} gl, {context} ctx
                                            WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                            ORDER BY gl.lowerboundary
                                            LIMIT 1)
                                      ELSE
                                        CASE
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                        WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                        ELSE ''
                                        END
                                      END
                          WHEN 31 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                         FROM {grade_letters} gl, {context} ctx
                                                         WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                         ORDER BY gl.lowerboundary
                                                         LIMIT 1) IS NOT NULL
                                              THEN (SELECT gl.letter
                                                    FROM {grade_letters} gl, {context} ctx
                                                    WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                    ORDER BY gl.lowerboundary
                                                    LIMIT 1)
                                              ELSE
                                                CASE
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                                ELSE ''
                                                END
                                              END ,' (', CASE MIN({$alias_gi}gradetype)
                                                         WHEN 1 THEN {$case_round}
                                                         WHEN 2 THEN (SELECT
                                                                        {$substring_index}
                                                                      FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                                         WHEN 3 THEN {$group_concat}
                                                         END, ')')
                          WHEN 32 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                         FROM {grade_letters} gl, {context} ctx
                                                         WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                         ORDER BY gl.lowerboundary
                                                         LIMIT 1) IS NOT NULL
                                              THEN (SELECT gl.letter
                                                    FROM {grade_letters} gl, {context} ctx
                                                    WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN({$alias_gi}courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                    ORDER BY gl.lowerboundary
                                                    LIMIT 1)
                                              ELSE
                                                CASE
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                                WHEN ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                                ELSE ''
                                                END
                                              END ,' (', ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}), '%)')
                          ELSE (CASE MIN({$alias_gi}gradetype)
                                WHEN 1 THEN {$case_round}
                                WHEN 2 THEN (SELECT
                                               {$substring_index}
                                             FROM {scale} s WHERE s.id=MIN({$alias_gi}scaleid))
                                WHEN 3 THEN {$group_concat}
                                END)
                          END)
                    ELSE CONCAT(ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),'%')
                     END )";
    }

    public static function get_real_grade_single($alias = 'g.', $round = 0, $alias_gi='gi.')
    {
        global $CFG;

        if ($CFG->dbtype == 'pgsql') {
            $substring_index = "split_part(scale,',', cast(ROUND({$alias}finalgrade) as int))";
            $case_round = "cast(ROUND({$alias}finalgrade, {$round}) as text)";
            $cast_as_int_start = 'cast(';
            $cast_as_int_end = ' as int)';
        } else {
            $substring_index = "SUBSTRING_INDEX(SUBSTRING_INDEX(scale, ',', ROUND({$alias}finalgrade)), ',', -1)";
            $case_round = "ROUND({$alias}finalgrade, {$round})";
            $cast_as_int_start = '';
            $cast_as_int_end = '';
        }

        return "(CASE {$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid={$alias_gi}courseid AND name='displaytype'){$cast_as_int_end}
                   WHEN 1 THEN (CASE {$alias_gi}gradetype
                                  WHEN 1 THEN {$case_round}
                                  WHEN 2 THEN (SELECT
                                                 {$substring_index}
                                               FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                                  WHEN 3 THEN {$alias}feedback
                                END)
                   WHEN 12 THEN CONCAT((CASE {$alias_gi}gradetype
                                  WHEN 1 THEN {$case_round}
                                  WHEN 2 THEN (SELECT
                                                 {$substring_index}
                                               FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                                  WHEN 3 THEN {$alias}feedback
                                END),' (',ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),'%)')
                   WHEN 13 THEN CONCAT((CASE {$alias_gi}gradetype
                                  WHEN 1 THEN {$case_round}
                                  WHEN 2 THEN (SELECT
                                                 {$substring_index}
                                               FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                                  WHEN 3 THEN {$alias}feedback
                                END),' (',CASE WHEN (SELECT gl.letter
                                                     FROM {grade_letters} gl, {context} ctx
                                                     WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                     ORDER BY gl.lowerboundary
                                                     LIMIT 1) IS NOT NULL
                                           THEN (SELECT gl.letter
                                                 FROM {grade_letters} gl, {context} ctx
                                                 WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                 ORDER BY gl.lowerboundary
                                                 LIMIT 1)
                                           ELSE
                                               CASE
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                               ELSE ''
                                               END
                                           END,')')
                   WHEN 2 THEN CONCAT(ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),'%')
                   WHEN 21 THEN CONCAT(ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),
                                       '% (',CASE {$alias_gi}gradetype
                                            WHEN 1 THEN {$case_round}
                                            WHEN 2 THEN (SELECT
                                                           {$substring_index}
                                                         FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                                            WHEN 3 THEN {$alias}feedback
                                            END,')')
                   WHEN 23 THEN CONCAT(ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}),
                                       '% (',CASE WHEN (SELECT gl.letter
                                                        FROM {grade_letters} gl, {context} ctx
                                                        WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                        ORDER BY gl.lowerboundary
                                                        LIMIT 1) IS NOT NULL
                                             THEN (SELECT gl.letter
                                                   FROM {grade_letters} gl, {context} ctx
                                                   WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                                   ORDER BY gl.lowerboundary
                                                   LIMIT 1)
                                             ELSE
                                               CASE
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                               WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                               ELSE ''
                                               END
                                             END,')')
                   WHEN 3 THEN CASE WHEN (SELECT gl.letter
                                          FROM {grade_letters} gl, {context} ctx
                                          WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                          ORDER BY gl.lowerboundary
                                          LIMIT 1) IS NOT NULL
                               THEN (SELECT gl.letter
                                     FROM {grade_letters} gl, {context} ctx
                                     WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                     ORDER BY gl.lowerboundary
                                     LIMIT 1)
                               ELSE
                                 CASE
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                 ELSE ''
                                 END
                               END
                   WHEN 31 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                          FROM {grade_letters} gl, {context} ctx
                                          WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                          ORDER BY gl.lowerboundary
                                          LIMIT 1) IS NOT NULL
                               THEN (SELECT gl.letter
                                     FROM {grade_letters} gl, {context} ctx
                                     WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                     ORDER BY gl.lowerboundary
                                     LIMIT 1)
                               ELSE
                                 CASE
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                 ELSE ''
                                 END
                               END ,' (', CASE {$alias_gi}gradetype
                                          WHEN 1 THEN {$case_round}
                                          WHEN 2 THEN (SELECT
                                                         {$substring_index}
                                                       FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                                          WHEN 3 THEN {$alias}feedback
                                          END, ')')
                   WHEN 32 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                          FROM {grade_letters} gl, {context} ctx
                                          WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                          ORDER BY gl.lowerboundary
                                          LIMIT 1) IS NOT NULL
                               THEN (SELECT gl.letter
                                     FROM {grade_letters} gl, {context} ctx
                                     WHERE ctx.contextlevel=50 AND ctx.instanceid={$alias_gi}courseid AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0)
                                     ORDER BY gl.lowerboundary
                                     LIMIT 1)
                               ELSE
                                 CASE
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 93 THEN 'A'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 90 THEN 'A-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 87 THEN 'B+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 83 THEN 'B'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 80 THEN 'B-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 77 THEN 'C+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 73 THEN 'C'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 70 THEN 'C-'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 67 THEN 'D+'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 60 THEN 'D'
                                 WHEN ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), 0) >= 0 THEN 'F'
                                 ELSE ''
                                 END
                               END ,' (', ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round}), '%)')
                   ELSE (CASE {$alias_gi}gradetype
                          WHEN 1 THEN {$case_round}
                          WHEN 2 THEN (SELECT
                                         {$substring_index}
                                       FROM {scale} s WHERE s.id={$alias_gi}scaleid)
                          WHEN 3 THEN {$alias}feedback
                          END)
                   END)";
    }

}
