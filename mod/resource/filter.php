<?php // $Id$
    //This function provides automatic linking to
    //resources when its name (title) is found inside every Moodle text
    //Williams, Stronk7, Martin D

    function resource_filter($courseid, $text) {

        global $CFG;

    /// The resources are sorted from long to short so longer ones can be linked first.

        if ($resources = get_records('resource', 'course', $courseid, 'CHAR_LENGTH(name) DESC', 'id,name')) {

            $links = array();

            foreach ($resources as $resource) {
                $currentname = trim($resource->name);
                $links[] = new filterobject($currentname,
                        '<a class="resource autolink" title="'.strip_tags($currentname).'" href="'.
                         $CFG->wwwroot.'/mod/resource/view.php?r='.$resource->id.'">', 
                         '</a>', false, true);
            }

            $text = filter_phrases($text, $links);  // Look for all these links in the text
        }

        return $text;
    }

?>
