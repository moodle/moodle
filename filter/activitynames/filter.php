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
    // Trivial-cache - keyed on $cachedcourseid
    static $activitylist = null;
    static $cachedcourseid;

    function filter($text, array $options = array()) {
        if (!$courseid = get_courseid_from_context($this->context)) {
            return $text;
        }

        // Initialise/invalidate our trivial cache if dealing with a different course
        if (!isset(self::$cachedcourseid) || self::$cachedcourseid !== (int)$courseid) {
            self::$activitylist = null;
        }
        self::$cachedcourseid = (int)$courseid;

        /// It may be cached

        if (is_null(self::$activitylist)) {
            self::$activitylist = array();

            $modinfo = get_fast_modinfo($courseid);
            if (!empty($modinfo->cms)) {
                self::$activitylist = array();      /// We will store all the activities here

                //Sort modinfo by name length
                $sortedactivities = fullclone($modinfo->cms);
                usort($sortedactivities, 'filter_activitynames_comparemodulenamesbylength');

                foreach ($sortedactivities as $cm) {
                    //Exclude labels, hidden activities and activities for group members only
                    if ($cm->visible and empty($cm->groupmembersonly) and $cm->has_view()) {
                        $title = s(trim(strip_tags($cm->name)));
                        $currentname = trim($cm->name);
                        $entitisedname  = s($currentname);
                        /// Avoid empty or unlinkable activity names
                        if (!empty($title)) {
                            $href_tag_begin = html_writer::start_tag('a',
                                    array('class' => 'autolink', 'title' => $title,
                                        'href' => $cm->get_url()));
                            self::$activitylist[$cm->id] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                            if ($currentname != $entitisedname) { /// If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545
                                self::$activitylist[$cm->id.'-e'] = new filterobject($entitisedname, $href_tag_begin, '</a>', false, true);
                            }
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



//This function is used to order module names from longer to shorter
function filter_activitynames_comparemodulenamesbylength($a, $b)  {
    if (strlen($a->name) == strlen($b->name)) {
        return 0;
    }
    return (strlen($a->name) < strlen($b->name)) ? 1 : -1;
}

