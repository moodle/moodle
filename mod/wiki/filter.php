<?PHP // $Id$
    //This function provides automatic linking to
    //wiki pages when its page title is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by mchurch. Enjoy! :-)

    require_once($CFG->dirroot.'/mod/wiki/lib.php');

    function wiki_filter($courseid, $text) {

        global $CFG;

        if (empty($courseid)) {
            if ($site = get_site()) {
                $courseid = $site->id;
            }
        }

        if (!($course = get_record('course', 'id', $courseid))) {
            return $text;
        }

        $linkarray = array();

//      Get all wikis for this course.
        $wikis = wiki_get_course_wikis($courseid);
        if (empty($wikis)) {
            return $text;
        }

//      Walk through each wiki, and get entries.
        foreach ($wikis as $wiki) {
            if ($wiki_entries = wiki_get_entries($wiki)) {

//              Walk through each entry and get the pages.
                foreach ($wiki_entries as $wiki_entry) {
                    if ($wiki_pages = get_records('wiki_pages', 'wiki', $wiki_entry->id, 'pagename, version DESC')) {
//                      Walk through each page and filter.
                        $wikientries = array();
                        foreach ($wiki_pages as $wiki_page) {
                            if (!in_array($wiki_page->pagename, $wikientries)) {
                                $startlink = '<a class="autolink" title="Wiki" href="'
                                        .$CFG->wwwroot.'/mod/wiki/view.php?wid='.$wiki->id
                                        .'&amp;userid='.$wiki_entry->userid
                                        .'&amp;groupid='.$wiki_entry->groupid
                                        .'&amp;page='.$wiki_page->pagename.'">';
                                $linkarray[] = new filterobject($wiki_page->pagename, $startlink, '</a>', false, true);
                                $wikientries[] = $wiki_page->pagename;
                            }
                        }
                    }
                }
            }
        }

        return filter_phrases($text, $linkarray);
    }

?>
