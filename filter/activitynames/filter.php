<?php
    //This function provides automatic linking to
    //activities when its name (title) is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by stronk7.
class activitynames_filter extends moodle_text_filter {
    // Trivial-cache - keyed on $cachedcourseid
    static $activitylist = null;
    static $cachedcourseid;

    function filter($text) {
        global $CFG, $COURSE, $DB;

        if (empty($this->courseid)) {
            $this->courseid = SITEID;
        }

        // Initialise/invalidate our trivial cache if dealing with a different course
        if (!isset($this->cachedcourseid) || $this->cachedcourseid !== (int)$this->courseid) {
            $this->activitylist = null;
        }
        $this->cachedcourseid = (int)$this->courseid;

        /// It may be cached

        if (is_null($this->activitylist)) {
            $this->activitylist = array();

            if ($COURSE->id == $this->courseid) {
                $course = $COURSE;
            } else {
                $course = $DB->get_record("course", array("id"=>$this->courseid));
            }

            if (!isset($course->modinfo)) {
                return $text;
            }

        /// Casting $course->modinfo to string prevents one notice when the field is null
            $modinfo = unserialize((string)$course->modinfo);

            if (!empty($modinfo)) {

                $this->activitylist = array();      /// We will store all the activities here

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
                            $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/$activity->mod/view.php?id=$activity->cm\">";
                            $this->activitylist[] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                            if ($currentname != $entitisedname) { /// If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17545
                                $this->activitylist[] = new filterobject($entitisedname, $href_tag_begin, '</a>', false, true);
                            }
                        }
                    }
                }
            }
        }

        if ($this->activitylist) {
            return $text = filter_phrases ($text, $this->activitylist);
        } else {
            return $text;
        }
    }
}



//This function is used to order module names from longer to shorter
function comparemodulenamesbylength($a, $b)  {
    if (strlen($a->name) == strlen($b->name)) {
        return 0;
    }
    return (strlen($a->name) < strlen($b->name)) ? 1 : -1;
}

