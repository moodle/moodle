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
        global $CFG, $COURSE, $DB;

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

            if ($COURSE->id == $courseid) {
                $course = $COURSE;
            } else {
                $course = $DB->get_record("course", array("id"=>$courseid));
            }

            if (!isset($course->modinfo)) {
                return $text;
            }

        /// Casting $course->modinfo to string prevents one notice when the field is null
            $modinfo = unserialize((string)$course->modinfo);

            if (!empty($modinfo)) {

                self::$activitylist = array();      /// We will store all the activities here

                //Sort modinfo by name length
                usort($modinfo, 'filter_activitynames_comparemodulenamesbylength');

                foreach ($modinfo as $activity) {
                    //Exclude labels, hidden activities and activities for group members only
                    if ($activity->mod != "label" and $activity->visible and empty($activity->groupmembersonly)) {
                        $title = s(trim(strip_tags($activity->name)));
                        $currentname = trim($activity->name);
                        $entitisedname  = s($currentname);
                        /// Avoid empty or unlinkable activity names
                        if (!empty($title)) {
                            $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/$activity->mod/view.php?id=$activity->cm\">";
                            self::$activitylist[] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                            if ($currentname != $entitisedname) { /// If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545
                                self::$activitylist[] = new filterobject($entitisedname, $href_tag_begin, '</a>', false, true);
                            }
                        }
                    }
                }
            }
        }

        if (self::$activitylist) {
            return $text = filter_phrases ($text, self::$activitylist);
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

