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

    function filter($text, array $options = array()) {
        $coursectx = $this->context->get_course_context(false);
        if (!$coursectx) {
            return $text;
        }
        $courseid = $coursectx->instanceid;

        $activitylist = $this->get_cached_activity_list($courseid);

        $filterslist = array();
        if (!empty($activitylist)) {
            $cmid = $this->context->instanceid;
            if ($this->context->contextlevel == CONTEXT_MODULE && isset($activitylist[$cmid])) {
                // remove filterobjects for the current module
                $filterslist = array_values(array_diff_key($activitylist, array($cmid => 1, $cmid.'-e' => 1)));
            } else {
                $filterslist = array_values($activitylist);
            }
        }

        if ($filterslist) {
            return $text = filter_phrases($text, $filterslist);
        } else {
            return $text;
        }
    }

    /**
     * Get all the cached activity list for a course
     *
     * @param int $courseid id of the course
     * @return filterobject[] the activities
     */
    protected function get_cached_activity_list($courseid) {
        global $USER;
        $cached = cache::make_from_params(cache_store::MODE_REQUEST, 'filter', 'activitynames');

        // Return cached activity list.
        if ($cached->get('cachecourseid') == $courseid && $cached->get('cacheuserid') == $USER->id) {
            return $cached->get('activitylist');
        }

        // Not cached yet, get activity list and set cache.
        $activitylist = $this->get_activity_list($courseid);
        $cached->set('cacheuserid', $USER->id);
        $cached->set('cachecourseid', $courseid);
        $cached->set('activitylist', $activitylist);
        return $activitylist;
    }

    /**
     * Get all the activity list for a course
     *
     * @param int $courseid id of the course
     * @return filterobject[] the activities
     */
    protected function get_activity_list($courseid) {
        $activitylist = array();

        $modinfo = get_fast_modinfo($courseid);
        if (!empty($modinfo->cms)) {
            $activitylist = array(); // We will store all the created filters here.

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
                    $hreftagbegin = html_writer::start_tag('a',
                        array('class' => 'autolink', 'title' => $title,
                            'href' => $cm->url));
                    $activitylist[$cm->id] = new filterobject($currentname, $hreftagbegin, '</a>', false, true);
                    if ($currentname != $entitisedname) {
                        // If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545.
                        $activitylist[$cm->id.'-e'] = new filterobject($entitisedname, $hreftagbegin, '</a>', false, true);
                    }
                }
            }
        }
        return $activitylist;
    }
}
