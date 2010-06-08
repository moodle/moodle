<?php

function glossary_filter($courseid, $text) {
    global $CFG, $DB, $OUTPUT;

    // Trivial-cache - keyed on $cachedcourseid
    static $nothingtodo;
    static $conceptlist;
    static $cachedcourseid;

    if (empty($courseid)) {
        $courseid = SITEID;
    }

    // Initialise/invalidate our trivial cache if dealing with a different course
    if (!isset($cachedcourseid) || $cachedcourseid !== (int)$courseid) {
        $conceptlist = array();
        $nothingtodo = false;
    }
    $cachedcourseid = (int)$courseid;

    if ($nothingtodo === true) {
        return $text;
    }

/// Create a list of all the concepts to search for.  It may be cached already.

    if (empty($conceptlist)) {

    /// Find all the glossaries we need to examine
        if (!$glossaries = $DB->get_records_sql_menu('SELECT g.id, g.name
                                                    FROM {glossary} g, {course_modules} cm, {modules} m
                                                    WHERE m.name = \'glossary\' AND
                                                          cm.module = m.id AND
                                                          cm.visible = 1 AND
                                                          g.id = cm.instance AND
                                                          g.usedynalink <> 0 AND
                                                          (g.course = ? OR g.globalglossary = 1)
                                                    ORDER BY g.globalglossary, g.id', array($courseid))) {
            $nothingtodo = true;
            return $text;
        }

    /// Make a list of glossary IDs for searching
        $glossarylist = implode(',', array_keys($glossaries));


    /// Pull out all the raw data from the database for entries, categories and aliases
        $entries = $DB->get_records_select('glossary_entries',
                                           'glossaryid IN ('.$glossarylist.') AND usedynalink != 0 AND approved != 0 ', null, '',
                                           'id,glossaryid, concept, casesensitive, 0 AS category, fullmatch');

        $categories = $DB->get_records_select('glossary_categories',
                                              'glossaryid IN ('.$glossarylist.') AND usedynalink != 0', null, '',
                                              'id,glossaryid,name AS concept, 1 AS casesensitive, 1 AS category, 1 AS fullmatch');

        $aliases = $DB->get_records_sql('SELECT ga.id, ge.glossaryid, ga.alias as concept, ge.concept as originalconcept,
                                                casesensitive, 0 AS category, fullmatch
                                           FROM {glossary_alias} ga,
                                                {glossary_entries} ge
                                          WHERE ga.entryid = ge.id
                                                AND ge.glossaryid IN ('.$glossarylist.')
                                                AND ge.usedynalink != 0
                                                AND ge.approved != 0', null);


    /// Combine them into one big list
        $concepts = array();
        if ($entries and $categories) {
            $concepts = array_merge($entries, $categories);
        } else if ($categories) {
            $concepts = $categories;
        } else if ($entries) {
            $concepts = $entries;
        }

        if ($aliases) {
            $concepts = array_merge($concepts, $aliases);
        }

        if (!empty($concepts)) {
            foreach ($concepts as $key => $concept) {
            /// Trim empty or unlinkable concepts
                $currentconcept = trim(strip_tags($concept->concept));
                if (empty($currentconcept)) {
                    unset($concepts[$key]);
                    continue;
                } else {
                    $concepts[$key]->concept = $currentconcept;
                }

            /// Rule out any small integers.  See bug 1446
                $currentint = intval($currentconcept);
                if ($currentint && (strval($currentint) == $currentconcept) && $currentint < 1000) {
                    unset($concepts[$key]);
                }
            }
        }

        if (empty($concepts)) {
            $nothingtodo = true;
            return $text;
        }

        usort($concepts, 'glossary_sort_entries_by_length');

        $strcategory = get_string('category', 'glossary');


    /// Loop through all the concepts, setting up our data structure for the filter

        $conceptlist = array();    /// We will store all the concepts here

        foreach ($concepts as $concept) {

            $glossaryname = $glossaries[$concept->glossaryid];

            if ($concept->category) {       // Link to a category
                $title = strip_tags($glossaryname.': '.$strcategory.' '.$concept->concept);
                $href_tag_begin = '<a class="glossary autolink glossaryid'.$concept->glossaryid.'" title="'.$title.'" '.
                                  'href="'.$CFG->wwwroot.'/mod/glossary/view.php?g='.$concept->glossaryid.
                                  '&amp;mode=cat&amp;hook='.$concept->id.'">';
            } else {
                if (!empty($concept->originalconcept)) {  // We are dealing with an alias (so show original)
                    $encodedconcept = urlencode($concept->originalconcept);
                    $title = str_replace('"', "'", strip_tags($glossaryname.': '.$concept->originalconcept));
                } else {
                    $encodedconcept = urlencode($concept->concept);
                    $title = str_replace('"', "'", strip_tags($glossaryname.': '.$concept->concept));
                }
                $randid = html_writer::random_id($title);
                $href_tag_begin = '<a id="'.$randid.'" class="glossary autolink glossaryid'.$concept->glossaryid.'" title="'.$title.'" '.
                                  'href="'.$CFG->wwwroot.'/mod/glossary/showentry.php?courseid='.$courseid.
                                  '&amp;concept='.$encodedconcept.'" >';

                //attach the onclick event
                $link = '/mod/glossary/showentry.php?courseid='.$courseid.'\&amp;concept='.$encodedconcept;
                $action = new popup_action('click', $link.'&popup=1', 'entry', array('height'=>600,'width'=>450));
                $OUTPUT->add_action_handler($action, $randid);
            }


            $conceptlist[] = new filterobject($concept->concept, $href_tag_begin, '</a>',
                                              $concept->casesensitive, $concept->fullmatch);
        }

        $conceptlist = filter_remove_duplicates($conceptlist);
    }

    global $GLOSSARY_EXCLUDECONCEPTS;
    if(!empty($GLOSSARY_EXCLUDECONCEPTS)) {
        $reducedconceptlist=array();
        foreach($conceptlist as $concept) {
            if(!in_array($concept->phrase,$GLOSSARY_EXCLUDECONCEPTS)) {
                $reducedconceptlist[]=$concept;
            }
        }
        return filter_phrases($text, $reducedconceptlist);
    }

    return filter_phrases($text, $conceptlist);   // Actually search for concepts!
}


function glossary_sort_entries_by_length ($entry0, $entry1) {
    $len0 = strlen($entry0->concept);
    $len1 = strlen($entry1->concept);

    if ($len0 < $len1) {
        return 1;
    } else if ($len0 > $len1) {
        return -1;
    } else {
        return 0;
    }
}



