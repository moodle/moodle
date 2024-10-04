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

namespace tool_usertours;

/**
 * Cache manager.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache {
    /**
     * @var CACHENAME_TOUR      The name of the cache used for storing tours.
     */
    const CACHENAME_TOUR = 'tourdata';

    /**
     * @var CACHEKEY_TOUR       The name of the key used for storing tours.
     */
    const CACHEKEY_TOUR = 'tours';

    /**
     * @var CACHENAME_STEP      The name of the cache used for storing steps.
     */
    const CACHENAME_STEP = 'stepdata';

    /**
     * Fetch all enabled tours.
     */
    public static function get_enabled_tourdata() {
        global $DB;

        $cache = \cache::make('tool_usertours', self::CACHENAME_TOUR);

        $data = $cache->get(self::CACHEKEY_TOUR);
        if ($data === false) {
            $sql = <<<EOF
                SELECT t.*
                  FROM {tool_usertours_tours} t
                 WHERE t.enabled = 1
                   AND t.id IN (SELECT s.tourid FROM {tool_usertours_steps} s GROUP BY s.tourid)
              ORDER BY t.sortorder ASC
EOF;

            $data = $DB->get_records_sql($sql);
            $cache->set('tours', $data);
        }

        return $data;
    }

    /**
     * Fetch all enabled tours matching the specified target.
     *
     * @param   moodle_url  $targetmatch    The URL to match.
     */
    public static function get_matching_tourdata(\moodle_url $targetmatch) {
        $tours = self::get_enabled_tourdata();

        // Attempt to determine whether this is the front page.
        // This is a special case because the frontpage uses a shortened page path making it difficult to detect exactly.
        $isfrontpage = $targetmatch->compare(new \moodle_url('/'), URL_MATCH_BASE);
        $isdashboard = $targetmatch->compare(new \moodle_url('/my/'), URL_MATCH_BASE);
        $ismycourses = $targetmatch->compare(new \moodle_url('/my/courses.php'), URL_MATCH_BASE);

        $possiblematches = [];
        if ($isfrontpage) {
            $possiblematches = ['FRONTPAGE', 'FRONTPAGE_MY', 'FRONTPAGE_MYCOURSES', 'FRONTPAGE_MY_MYCOURSES'];
        } else if ($isdashboard) {
            $possiblematches = ['MY', 'FRONTPAGE_MY', 'MY_MYCOURSES', 'FRONTPAGE_MY_MYCOURSES'];
        } else if ($ismycourses) {
            $possiblematches = ['MYCOURSES', 'FRONTPAGE_MYCOURSES', 'MY_MYCOURSES', 'FRONTPAGE_MY_MYCOURSES'];
        }

        $target = $targetmatch->out_as_local_url();
        return array_filter($tours, function ($tour) use ($possiblematches, $target) {
            if (in_array($tour->pathmatch, $possiblematches)) {
                return true;
            }
            $pattern = preg_quote($tour->pathmatch, '@');
            if (strpos($pattern, '%') !== false) {
                // The URL match format is something like: /my/%.
                // We need to find all the URLs which match the first part of the pattern.
                $pattern = str_replace('%', '.*', $pattern);
            } else {
                // The URL match format is something like: /my/courses.php.
                // We need to find all the URLs which match with whole pattern.
                $pattern .= '$';
            }
            return !!preg_match("@{$pattern}@", $target);
        });
    }

    /**
     * Notify of changes to any tour to clear the tour cache.
     */
    public static function notify_tour_change() {
        $cache = \cache::make('tool_usertours', self::CACHENAME_TOUR);
        $cache->delete(self::CACHEKEY_TOUR);
    }

    /**
     * Fetch the tour data for the specified tour.
     *
     * @param   int         $tourid         The ID of the tour to fetch steps for
     */
    public static function get_stepdata($tourid) {
        global $DB;

        $cache = \cache::make('tool_usertours', self::CACHENAME_STEP);

        $data = $cache->get($tourid);
        if ($data === false) {
            $sql = <<<EOF
                SELECT s.*
                  FROM {tool_usertours_steps} s
                 WHERE s.tourid = :tourid
              ORDER BY s.sortorder ASC
EOF;

            $data = $DB->get_records_sql($sql, ['tourid' => $tourid]);
            $cache->set($tourid, $data);
        }

        return $data;
    }
    /**
     * Notify of changes to any step to clear the step cache for that tour.
     *
     * @param   int         $tourid         The ID of the tour to clear the step cache for
     */
    public static function notify_step_change($tourid) {
        $cache = \cache::make('tool_usertours', self::CACHENAME_STEP);
        $cache->delete($tourid);
    }
}
