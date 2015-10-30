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
 * This filter provides automatic linking to
 * activities when its name (title) is found inside every Moodle text
 *
 * @package    filter
 * @subpackage activitynames
 * @copyright  2004 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Activity name filtering
 */
class filter_activitynames extends moodle_text_filter {
    // Trivial-cache - keyed on $cachedcourseid and $cacheduserid.
    static $activitylist = null;
    static $cachedcourseid;
    static $cacheduserid;

    function filter($text, array $options = array()) {
        global $USER; // Since 2.7 we can finally start using globals in filters.

        $coursectx = $this->context->get_course_context(false);
        if (!$coursectx) {
            return $text;
        }
        $courseid = $coursectx->instanceid;

        // Initialise/invalidate our trivial cache if dealing with a different course.
        if (!isset(self::$cachedcourseid) || self::$cachedcourseid !== (int)$courseid) {
            self::$activitylist = null;
        }
        self::$cachedcourseid = (int)$courseid;
        // And the same for user id.
        if (!isset(self::$cacheduserid) || self::$cacheduserid !== (int)$USER->id) {
            self::$activitylist = null;
        }
        self::$cacheduserid = (int)$USER->id;

        /// It may be cached

        if (is_null(self::$activitylist)) {
            self::$activitylist = array();

            $modinfo = get_fast_modinfo($courseid);
            if (!empty($modinfo->cms)) {
                self::$activitylist = array(); // We will store all the created filters here.

                // Create array of visible activities sorted by the name length (we are only interested in properties name and url).
                $sortedactivities = array();
                foreach ($modinfo->cms as $cm) {
                    // Use normal access control and visibility, but exclude labels and hidden activities.
                    if ($cm->visible and $cm->has_view() and $cm->uservisible) {
                        $sortedactivities[] = (object)array(
                            'name' => $cm->name,
                            'url' => $cm->url,
                            'id' => $cm->id,
                            'namelen' => -strlen($cm->name), // Negative value for reverse sorting.
                        );
                    }
                }
                // Sort activities by the length of the activity name in reverse order.
                core_collator::asort_objects_by_property($sortedactivities, 'namelen', core_collator::SORT_NUMERIC);

                foreach ($sortedactivities as $cm) {
                    $title = s(trim(strip_tags($cm->name)));
                    $currentname = trim($cm->name);
                    $entitisedname  = s($currentname);
                    // Avoid empty or unlinkable activity names.
                    if (!empty($title)) {
                        $href_tag_begin = html_writer::start_tag('a',
                                array('class' => 'autolink', 'title' => $title,
                                    'href' => $cm->url));
                        self::$activitylist[$cm->id] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                        if ($currentname != $entitisedname) {
                            // If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545.
                            self::$activitylist[$cm->id.'-e'] = new filterobject($entitisedname, $href_tag_begin, '</a>', false, true);
                        }
                    }
                }
            }
        }

        $filterslist = array();
        if (self::$activitylist) {
            $cmid = $this->context->instanceid;
            if ($this->context->contextlevel == CONTEXT_MODULE && isset(self::$activitylist[$cmid])) {
                // remove filterobjects for the current module
                $filterslist = array_values(array_diff_key(self::$activitylist, array($cmid => 1, $cmid.'-e' => 1)));
            } else {
                $filterslist = array_values(self::$activitylist);
            }
        }

        if ($filterslist) {
            return $text = filter_phrases($text, $filterslist);
        } else {
            return $text;
        }
    }
}
