<?php // $Id$
    //This function provides automatic linking to
    //activities when its name (title) is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by stronk7.

    function activitynames_filter($courseid, $text) {

        global $CFG;

        // Trivial-cache - keyed on $cachedcourseid
        static $activitylist;
        static $cachedcourse;

        if (empty($courseid)) {
            $courseid = SITEID;
        }

        // Initialise/invalidate our trivial cache if dealing with a different course
        if (!isset($cachedcourseid) || $cachedcourseid !== (int)$courseid) {
            $activitylist = array();
        } 
        $cachedcourseid = (int)$courseid;

        /// It may be cached

        if (empty($activitylist)) {

            $course = get_record("course","id",$courseid);
            $modinfo = unserialize($course->modinfo);

            if (!empty($modinfo)) {

                $activitylist = array();      /// We will store all the activities here

                //Sort modinfo by name length
                usort($modinfo,'comparemodulenamesbylength');

                foreach ($modinfo as $activity) {
                    //Exclude labels and hidden items
                    if ($activity->mod != "label" && $activity->visible) {
                        $title = trim(strip_tags(urldecode($activity->name)));
                        /// Avoid empty or unlinkable activity names
                        if (!empty($title)) {
                            $title = str_replace('"', "'", $title);
                            $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/$activity->mod/view.php?id=$activity->cm\" target=\"$CFG->framename\">";
                            $currentname = urldecode($activity->name);
                            if ($currentname = trim($currentname)) {
                                $activitylist[] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                            }
                        }
                    }
                }
            }
        }

        return $text = filter_phrases ($text, $activitylist);
    }



    //This function is used to order module names from longer to shorter
    function comparemodulenamesbylength($a, $b)  {
        if (strlen($a->name) == strlen($b->name)) {
            return 0;
        }
        return (strlen($a->name) < strlen($b->name)) ? 1 : -1;
    }
?>
