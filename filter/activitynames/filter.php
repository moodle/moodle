<?php // $Id$
    //This function provides automatic linking to
    //activities when its name (title) is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by stronk7.

    function activitynames_filter($courseid, $text) {

        global $CFG;

        if (empty($courseid)) {
            $courseid = SITEID;
        }

        $course = get_record("course","id",$courseid);
        $modinfo = unserialize($course->modinfo);

        if (!empty($modinfo)) {

            $linkarray = array();

            //Sort modinfo by name length
            usort($modinfo,'comparemodulenamesbylength');

            $cm = '';
            foreach ($modinfo as $activity) {
                //Exclude labels and hidden items
                if ($activity->mod != "label" && $activity->visible) {
                    $title = strip_tags(urldecode($activity->name));
                    $title = str_replace('"', "'", $title);
                    $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/$activity->mod/view.php?id=$activity->cm\">";
                    $currentname = urldecode($activity->name);
                    if ($currentname = trim($currentname)) {
                        $linkarray[] = new filterobject($currentname, $href_tag_begin, '</a>', false, true);
                    }
                }
            }
            $text = filter_phrases ($text, $linkarray);
        }
        return $text;
    }



    //This function is used to order module names from longer to shorter
    function comparemodulenamesbylength($a, $b)  {
        if (strlen($a->name) == strlen($b->name)) {
            return 0;
        }
        return (strlen($a->name) < strlen($b->name)) ? 1 : -1;
    }
?>
