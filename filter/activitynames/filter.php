<?php // $Id$
    //This function provides automatic linking to
    //activities when its name (title) is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by stronk7.

    function activitynames_filter($courseid, $text) {

        global $CFG, $COURSE;

        // Trivial-cache - keyed on $cachedcourseid
        static $activitylist = null;
        static $cachedcourseid;

        if (empty($courseid)) {
            $courseid = SITEID;
        }

        // Initialise/invalidate our trivial cache if dealing with a different course
        if (!isset($cachedcourseid) || $cachedcourseid !== (int)$courseid) {
            $activitylist = null;
        } 
        $cachedcourseid = (int)$courseid;

        /// It may be cached

        if (is_null($activitylist)) {
            $activitylist = array();

            if ($COURSE->id == $courseid) {
                $course = $COURSE;
            } else {
                $course = get_record("course", "id", $courseid);
            }

            if (!isset($course->modinfo)) {
                return $text;
            }

        /// Casting $course->modinfo to string prevents one notice when the field is null
            $modinfo = unserialize((string)$course->modinfo);

            if (!empty($modinfo)) {

                $activitylist = array();      /// We will store all the activities here

                //Sort modinfo by name length
                usort($modinfo, 'comparemodulenamesbylength');

                foreach ($modinfo as $activity) {
                    //Exclude labels, hidden activities and activities for group members only 
                    if ($activity->mod != "label" and $activity->visible and empty($activity->groupmembersonly)) {
                        $title = s(trim(strip_tags(urldecode($activity->name))));
                        $currentname = trim(urldecode($activity->name));
                        $entitisedname  = s($currentname);
                        /// Avoid empty or unlinkable activity names
                        if (!empty($title)) {
                            $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/$activity->mod/view.php?id=$activity->cm\" $CFG->frametarget>";
                            $activitylist[] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                            if ($currentname != $entitisedname) { /// If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545
                                $activitylist[] = new filterobject($entitisedname, $href_tag_begin, '</a>', false, true);
                            }
                        }
                    }
                }
            }
        }

        if ($activitylist) {
            return $text = filter_phrases ($text, $activitylist);
        } else {
            return $text;
        }
    }



    //This function is used to order module names from longer to shorter
    function comparemodulenamesbylength($a, $b)  {
        if (strlen($a->name) == strlen($b->name)) {
            return 0;
        }
        return (strlen($a->name) < strlen($b->name)) ? 1 : -1;
    }
?>
