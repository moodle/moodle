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

namespace core\output;

/**
 * This class houses methods for checking theme usage in a given context.
 *
 * @package    core
 * @category   output
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_usage {

    /** @var string The theme usage type for users. */
    public const THEME_USAGE_TYPE_USER = 'user';

    /** @var string The theme usage type for courses. */
    public const THEME_USAGE_TYPE_COURSE = 'course';

    /** @var string The theme usage type for cohorts. */
    public const THEME_USAGE_TYPE_COHORT = 'cohort';

    /** @var string The theme usage type for categories. */
    public const THEME_USAGE_TYPE_CATEGORY = 'category';

    /** @var string The theme usage type for all. */
    public const THEME_USAGE_TYPE_ALL = 'all';

    /** @var int The theme is used in context. */
    public const THEME_IS_USED = 1;

    /** @var int The theme is not used in context. */
    public const THEME_IS_NOT_USED = 0;

    /**
     * Check if the theme is used in any context (e.g. user, course, cohort, category).
     *
     * This query is cached.
     *
     * @param string $themename The theme to check.
     * @return int Return 1 if at least one record was found, 0 if none.
     */
    public static function is_theme_used_in_any_context(string $themename): int {
        global $DB;
        $cache = \cache::make('core', 'theme_usedincontext');
        $isused = $cache->get($themename);

        if ($isused === false) {

            $sqlunions = [];

            // For each context, check if the config is enabled and there is at least one use.
            if (get_config('core', 'allowuserthemes')) {
                $sqlunions[self::THEME_USAGE_TYPE_USER] = "
                        SELECT u.id
                            FROM {user} u
                            WHERE u.theme = :usertheme
                            ";
            }

            if (get_config('core', 'allowcoursethemes')) {
                $sqlunions[self::THEME_USAGE_TYPE_COURSE] = "
                        SELECT c.id
                            FROM {course} c
                            WHERE c.theme = :coursetheme
                            ";
            }

            if (get_config('core', 'allowcohortthemes')) {
                $sqlunions[self::THEME_USAGE_TYPE_COHORT] = "
                        SELECT co.id
                            FROM {cohort} co
                            WHERE co.theme = :cohorttheme
                            ";
            }

            if (get_config('core', 'allowcategorythemes')) {
                $sqlunions[self::THEME_USAGE_TYPE_CATEGORY] = "
                        SELECT cat.id
                            FROM {course_categories} cat
                            WHERE cat.theme = :categorytheme
                            ";
            }

            // Union the sql statements from the different tables.
            if (!empty($sqlunions)) {
                $sql = implode(' UNION ', $sqlunions);

                // Prepare params.
                $params = [];
                foreach ($sqlunions as $type => $val) {
                    $params[$type . 'theme'] = $themename;
                }

                $result = $DB->record_exists_sql($sql, $params);
            }

            if (!empty($result)) {
                $isused = self::THEME_IS_USED;
            } else {
                $isused = self::THEME_IS_NOT_USED;
            }

            // Cache the result so we don't have to keep checking for this theme.
            $cache->set($themename, $isused);
            return $isused;

        } else {
            return $isused;
        }
    }
}
