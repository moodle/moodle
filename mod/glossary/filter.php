<?php // $Id$

    function glossary_filter($courseid, $text) {
        global $CFG;

        if (empty($courseid)) {
            $courseid = SITEID;
        }

        $GLOSSARY_CONCEPT_IS_ENTRY = 0;
        $GLOSSARY_CONCEPT_IS_CATEGORY = 1;
        switch ($CFG->dbtype) {
            case 'postgres7':
                $as = 'as';
                break;
            case 'mysql':
                $as = '';
                break;
        }

        $conceptlist = array();

        $glossarieslist = get_records_sql ("SELECT g.* 
                                            FROM {$CFG->prefix}glossary g,
                                                 {$CFG->prefix}course_modules cm,
                                                 {$CFG->prefix}modules m
                                            WHERE m.name = 'glossary' AND
                                                  cm.module = m.id AND
                                                  cm.visible = 1 AND
                                                  g.id = cm.instance AND                                         
                                                  g.usedynalink != 0 AND
                                                  (g.course = '$courseid' OR g.globalglossary = 1)
                                            ORDER BY g.globalglossary, g.id");
        if ( $glossarieslist ) {
            $glossaries = "";
            foreach ( $glossarieslist as $glossary ) {
                $glossaries .= "$glossary->id,";
            }
            $glossaries=substr($glossaries,0,-1);
///         sorting by the length of the concept in order to assure that large concepts
///         are linked first, if they exist in the text to parse
            switch ($CFG->dbtype) {
                case "postgres7":
                case "mysql":
                    $ebylenght = "CHAR_LENGTH(concept) desc,";
                    $cbylenght = "CHAR_LENGTH(name) desc,";
                break;
                default:
                    $ebylenght = "";
                    $cbylenght = "";
                break;
            }

            $entries = get_records_select("glossary_entries", "glossaryid IN ($glossaries) AND usedynalink != 0 and approved != 0 and concept != ''","$ebylenght glossaryid","id,glossaryid, concept,casesensitive,$GLOSSARY_CONCEPT_IS_ENTRY $as category,fullmatch");
            $categories  = get_records_select("glossary_categories", "glossaryid IN ($glossaries) AND usedynalink != 0", "$cbylenght glossaryid","id,glossaryid, name $as concept, 1 $as casesensitive,$GLOSSARY_CONCEPT_IS_CATEGORY $as category, 1 $as fullmatch");
            if ( $entries and $categories ) {
                $concepts = array_merge($entries, $categories);
                usort($concepts,'glossary_sort_entries_by_length');
            } elseif ( $categories ) {
                $concepts = $categories;
            } elseif ( $entries ) {
                $concepts = $entries;
            }

            if ( isset($concepts) ) {
                $lastglossary = 0;
                $lastcategory = 0;
                $cm = '';
                foreach ( $concepts as $concept ) {
                    if ( $concept->category ) {
                        if ( $lastcategory != $concept->id ) {
                            $category = get_record("glossary_categories","id",$concept->id);
                            $lastcategory = $concept->id;
                            if ( empty($cm->instance) || $cm->instance != $category->glossaryid ) {
                                $gcat = get_record("glossary","id",$category->glossaryid);
                                if ( !$cm = get_coursemodule_from_instance("glossary", $category->glossaryid, $gcat->course) ) {
                                    $cm->id = 1;
                                }
                            }
                        }

                        $title = strip_tags("$glossary->name: " . get_string("category","glossary"). " $category->name");
                        $href_tag_begin = "<a class=\"glossary autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=cat&amp;hook=$concept->id\">";
                    } else {
                        if ( $lastglossary != $concept->glossaryid ) {
                            $glossary = get_record("glossary","id",$concept->glossaryid);
                            $lastglossary = $glossary->id;
                        }

                        $encodedconcept = urlencode($concept->concept);
                        $title = str_replace('"', "'", strip_tags("$glossary->name: $concept->concept"));
                        $href_tag_begin = "<a target=\"entry\" class=\"glossary autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/showentry.php?courseid=$courseid&amp;concept=$encodedconcept\" ".
                             "onclick=\"return openpopup('/mod/glossary/showentry.php?courseid=$courseid\&amp;concept=$encodedconcept', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";
                    }
                    $currentconcept = $concept->concept;
                    if ( $currentconcept = trim(strip_tags($currentconcept)) ) {
                        if ( !$concept->category ) {
                            if ( $aliases = get_records("glossary_alias","entryid",$concept->id, "alias") ) {
                                foreach ($aliases as $alias) {
                                    $currentalias = trim(strip_tags($alias->alias));
                                    //Avoid integers < 1000 to be linked. See bug 1446.
                                    $intcurrent = intval($currentalias);
                                    if (!(!empty($intcurrent) && strval($intcurrent) == $currentalias && $intcurrent < 1000)) {
                                        if ( $currentalias != '' ) {
                                            $conceptlist[] = new filterobject($currentalias, $href_tag_begin, '</a>', $concept->casesensitive, $concept->fullmatch);
                                        }
                                    }
                                }
                            }
                        }
                        if ($currentconcept) {
                            $conceptlist[] = new filterobject($currentconcept, $href_tag_begin, '</a>', $concept->casesensitive, $concept->fullmatch);
                        }
                    }
                }
            }
        }

        /// Remove any duplicate entries
        $conceptlist = filter_remove_duplicates($conceptlist);
        
        //return $text;
        return filter_phrases($text, $conceptlist);
    }





    function glossary_sort_entries_by_length ( $entry0, $entry1 ) {
        if ( strlen(trim($entry0->concept)) < strlen(trim($entry1->concept)) ) {
            return 1;
        } elseif ( strlen(trim($entry0->concept)) > strlen(trim($entry1->concept)) ) {
            return -1;
        } else {
            return 0;
        }
    }

    function glossary_addslashes ( $chars, $text ) {
        if ( $chars ) {
            for ($i = 0; $i < strlen($chars); $i++) {
                $text = str_replace($chars[$i], "\\" . $chars[$i], $text);
            }
        }
        return $text;
    }

?>
