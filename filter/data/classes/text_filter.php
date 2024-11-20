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

namespace filter_data;

use core_filters\filter_object;

/**
 * Filter providing automatic linking to database activity entries when found inside every Moodle text.
 *
 * @package    filter_data
 * @copyright  2006 Vy-Shane Sin Fat
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        global $CFG, $DB, $USER;

        // Trivial-cache - keyed on $cachedcourseid + $cacheduserid.
        static $cachedcourseid = null;
        static $cacheduserid = null;
        static $coursecontentlist = [];
        static $sitecontentlist = [];

        static $nothingtodo;

        // Try to get current course.
        // We could be in a course category so no entries for courseid == 0 will be found.
        $courseid = 0;
        if ($coursectx = $this->context->get_course_context(false)) {
            $courseid = $coursectx->instanceid;
        }

        if ($cacheduserid !== $USER->id) {
            // Invalidate all caches if the user changed.
            $coursecontentlist = [];
            $sitecontentlist = [];
            $cacheduserid = $USER->id;
            $cachedcourseid = $courseid;
            $nothingtodo = false;
        } else if ($courseid != get_site()->id && $courseid != 0 && $cachedcourseid != $courseid) {
            // Invalidate course-level caches if the course id changed.
            $coursecontentlist = [];
            $cachedcourseid = $courseid;
            $nothingtodo = false;
        }

        if ($nothingtodo === true) {
            return $text;
        }

        // If courseid == 0 only site entries will be returned.
        $site = get_site();
        if ($courseid == get_site()->id || $courseid == 0) {
            $contentlist = & $sitecontentlist;
        } else {
            $contentlist = & $coursecontentlist;
        }

        // Create a list of all the resources to search for. It may be cached already.
        if (empty($contentlist)) {
            $coursestosearch = $courseid ? [$courseid] : []; // Add courseid if found.
            if (get_site()->id != $courseid) { // Add siteid if was not courseid.
                $coursestosearch[] = get_site()->id;
            }
            // We look for text field contents only if have autolink enabled (param1).
            [$coursesql, $params] = $DB->get_in_or_equal($coursestosearch);
            $sql = 'SELECT dc.id AS contentid, dr.id AS recordid, dc.content AS content, d.id AS dataid
                      FROM {data} d
                      JOIN {data_fields} df ON df.dataid = d.id
                      JOIN {data_records} dr ON dr.dataid = d.id
                      JOIN {data_content} dc ON dc.fieldid = df.id AND dc.recordid = dr.id
                     WHERE d.course ' . $coursesql . '
                       AND df.type = \'text\'
                       AND ' . $DB->sql_compare_text('df.param1', 1) . " = '1'";

            if (!$contents = $DB->get_records_sql($sql, $params)) {
                $nothingtodo = true;
                return $text;
            }

            foreach ($contents as $key => $content) {
                // Trim empty or unlinkable concepts.
                $currentcontent = trim(strip_tags($content->content));
                if (empty($currentcontent)) {
                    unset($contents[$key]);
                    continue;
                } else {
                    $contents[$key]->content = $currentcontent;
                }

                // Rule out any small integers.  See bug 1446.
                $currentint = intval($currentcontent);
                if ($currentint && (strval($currentint) == $currentcontent) && $currentint < 1000) {
                    unset($contents[$key]);
                }
            }

            if (empty($contents)) {
                $nothingtodo = true;
                return $text;
            }

            usort($contents, [self::class, 'sort_entries_by_length']);

            foreach ($contents as $content) {
                $hrefopen = '<a class="data autolink dataid' . $content->dataid . '" title="' . s($content->content) . '" ' .
                                  'href="' . $CFG->wwwroot . '/mod/data/view.php?d=' . $content->dataid .
                                  '&amp;rid=' . $content->recordid . '">';
                $contentlist[] = new filter_object($content->content, $hrefopen, '</a>', false, true);
            }

            $contentlist = filter_remove_duplicates($contentlist); // Clean dupes.
        }
        return filter_phrases($text, $contentlist);  // Look for all these links in the text.
    }

    /**
     * Helper to sort array values by content length.
     *
     * @param mixed $content0
     * @param mixed $content1
     * @return int
     */
    private static function sort_entries_by_length($content0, $content1) {
        $len0 = strlen($content0->content);
        $len1 = strlen($content1->content);

        if ($len0 < $len1) {
            return 1;
        } else if ($len0 > $len1) {
            return -1;
        } else {
            return 0;
        }
    }
}
