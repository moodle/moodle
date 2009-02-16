<?php // $Id$
    //This function provides automatic linking to
    //resources when its name (title) is found inside every Moodle text
    //Williams, Stronk7, Martin D

    function resource_filter($courseid, $text) {

        global $CFG;

        // Trivial-cache - keyed on $cachedcourseid
        static $nothingtodo;
        static $resourcelist;
        static $cachedcourseid;

        // if we don't have a courseid, we can't run the query, so
        if (empty($courseid)) {
            return $text;
        }

        // Initialise/invalidate our trivial cache if dealing with a different course
        if (!isset($cachedcourseid) || $cachedcourseid !== (int)$courseid) {
            $resourcelist = array();
            $nothingtodo = false;
        } 
        $cachedcourseid = (int)$courseid;

        if ($nothingtodo === true) {
            return $text;
        }
        
    /// Create a list of all the resources to search for.  It may be cached already.

        if (empty($resourcelist)) {

            /* get all non-hidden resources from this course
             * sorted from long to short so longer ones can be 
             * linked first. And order by section so we try to 
             * link to the top resource first.
             */
            $resource_sql  = "SELECT r.id, r.name 
                FROM {$CFG->prefix}resource r, 
                     {$CFG->prefix}course_modules cm, 
                     {$CFG->prefix}modules m
                WHERE m.name = 'resource' AND
                        cm.module = m.id AND
                        cm.visible =  1 AND
                        r.id = cm.instance AND
                        cm.course = {$courseid}
                ORDER BY " . sql_length('r.name') . " DESC, cm.section ASC";

            if (!$resources = get_records_sql($resource_sql) ){
                $nothingtodo = true;
                return $text;
            }

            $resourcelist = array();

            foreach ($resources as $resource) {
                $currentname    = trim($resource->name);
                $entitisedname  = s($currentname);
                $strippedname   = strip_tags($currentname);
                /// Avoid empty or unlinkable resource names
                if (!empty($strippedname)) {
                    $resourcelist[] = new filterobject($currentname,
                            '<a class="resource autolink" title="'.$strippedname.'" href="'.
                             $CFG->wwwroot.'/mod/resource/view.php?r='.$resource->id.'" '.$CFG->frametarget.'>',
                             '</a>', false, true);
                    if ($currentname != $entitisedname) { /// If name has some entity (&amp; &quot; &lt; &gt;) add that filter too. MDL-17518
                        $resourcelist[] = new filterobject($entitisedname,
                                '<a class="resource autolink" title="'.$strippedname.'" href="'.
                                 $CFG->wwwroot.'/mod/resource/view.php?r='.$resource->id.'" '.$CFG->frametarget.'>',
                                 '</a>', false, true);

                    }
                }
            }

        }
        return  filter_phrases($text, $resourcelist);  // Look for all these links in the text
    }

?>
