<?PHP

    $textfilter_function = 'glossary_dynamic_link';

    if (function_exists($textfilter_function)) {
        return;
    }

    function glossary_dynamic_link($courseid, $text,$glossaryid = NULL) {
    global $CFG;
    static $entries;     // to avoid repeated calls to database
    static $glossary;    //    even when dealing with the same glossary
    
        if ( !$glossary and !$glossaryid ) {
            $PermissionGranted = 1;   // if it is the first call and no glossary was specify
        } elseif ( $glossaryid ) {
            if ( $glossary ) {   // if it is not the first call
                if ( $glossary->id != $glossaryid ) {   // ...and the specified glossary is different from the previous call
                    $PermissionGranted = 1;
                }
            } else {
                $PermissionGranted = 1;   // if it is the first call and a glossary was specify
            }
        }
        if ( $PermissionGranted ) {
            if ( !$glossaryid ) {  // If no glossary was specify, fetch the main glossary of the course
                $glossary = get_record("glossary","course",$courseid,"mainglossary",1);
            } else {               // if a glossary as specify, fetch this one
                $glossary = get_record("glossary","course",$courseid,"id",$glossaryid);
            }
        }
        if ( $glossary ) {
            if ( !$entries ) {
                // char_lenght is compatible with PostgreSQL and MySQL. Other DBMS must be implemented
                
                /* I'm ordering the cursor by the lenght of the concept trying to avoid the bug that occurs
                      when a concept in contained in other entry's concept (i.e. HOUSE is in DOLL HOUSE).
                      However, I haven't find a solution yet.
                      Will (Sept. 30, 2003)
                */
                if ($CFG->dbtype == "postgres7" or $CFG->dbtype == "mysql") {
                    $ORDER_BY = "CHAR_LENGTH(concept) DESC";
                } else {
                    $ORDER_BY = "concept ASC";
                }

                $ownentries = get_records("glossary_entries", "glossaryid", $glossary->id,$ORDER_BY);
                $importedentries = get_records("glossary_entries", "sourceglossaryid", $glossary->id,$ORDER_BY);

                if ( $ownentries and $importedentries ) {
                    $entries = array_merge($ownentries, $importedentries);
                    usort($entries, glossary_sort_entries_by_lenght);		
                } elseif ( $importedentries ) {
                    $entries = $importedentries;
                } elseif ( $ownentries ) {
                    $entries = $ownentries;
                }
            }
            if ( $entries ) {
                foreach ( $entries as $entry ) {
                    $title = strip_tags("$glossary->name: $entry->concept");
                    $href_tag_begin = "<a target=\"entry\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/showentry.php?courseid=$courseid&concept=$entry->concept\" ".
                         "onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$courseid\&concept=$entry->concept', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";

                    $concept = trim(strip_tags($entry->concept));

                    $text = glossary_link_concepts($text,$concept,$href_tag_begin, "</a>");
                }
            }
        }
        
        return $text;
    }
    
    function glossary_link_concepts($text,$concept,$href_tag_begin,$href_tag_end = "</a>") {
        $list_of_words_cp = $concept;

        // getting ride of "A" tags
        $final = array();

        preg_match_all('/<A (.+?)>(.+?)<\/A>/is',$text,$list_of_links);

        foreach (array_unique($list_of_links[0]) as $key=>$value) {
            $links['<|*'.$key.'*|>'] = $value;
        }
        $text = str_replace($links,array_keys($links),$text);

        // getting ride of all other tahs
        $final = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_words);

        foreach (array_unique($list_of_words[0]) as $key=>$value) {
            $final['<|'.$key.'|>'] = $value;
        }

        $text = str_replace($final,array_keys($final),$text);

        if ($list_of_words_cp{0}=="|") {
            $list_of_words_cp{0} = "";
        }
        if ($list_of_words_cp{strlen($list_of_words_cp)-1}=="|") {
            $list_of_words_cp{strlen($list_of_words_cp)-1}="";
        }
        $list_of_words_cp = "(".trim($list_of_words_cp).")";

        $text = eregi_replace("$list_of_words_cp", "$href_tag_begin"."\\1"."$href_tag_end", $text);
        $text = str_replace(array_keys($final),$final,$text);
        $text = str_replace(array_keys($links),$links,$text);

        return stripslashes($text);
    }
    
    function glossary_sort_entries_by_lenght ( $entry0, $entry1 ) {
        if ( strlen(trim($entry0->concept)) < strlen(trim($entry1->concept)) ) {
            return -1;
        } elseif ( strlen(trim($entry0->concept)) > strlen(trim($entry1->concept)) ) {
            return 1;
        } else {
            return 0;
        }
    }

?>
