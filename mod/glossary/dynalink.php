<?PHP

    function glossary_dynamic_link($courseid, $text,$glossaryid = NULL) {
    global $CFG;
    static $entries;
    static $glossary;
        if ( !$glossary ) {
            $PermissionGranted = 1;
        } elseif ( $glossaryid ) {
            if ( $glossary->id != $glossaryid ) {
                $PermissionGranted = 1;
            } else {
            }
        } else {
            $PermissionGranted = 1;
        }
        if ( $PermissionGranted ) {
            if ( !$glossaryid ) {
                $glossary = get_record("glossary","course",$courseid,"mainglossary",1);
            } else {
                $glossary = get_record("glossary","course",$courseid,"id",$glossaryid);
            }
        }
        if ( $glossary ) {
            if ( !$entries ) {
                // char_lenght is compatible with PostGreSQL and MySQL. Other DBMS must be implemented
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
                    $href_tag_begin = "<a target=\"entry\" title=\"" . strip_tags("$glossary->name: $entry->concept") ."\" href=\"$CFG->wwwroot/mod/glossary/showentry.php?courseid=$courseid&concept=$entry->concept\" ".
                         "onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$courseid&concept=$entry->concept', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";

                    $concept = trim(strip_tags($entry->concept));

                    $text = glossary_link_concepts($text,$concept,$href_tag_begin);
                }
            }
        }
        
        return $text;
    }
    
    function glossary_link_concepts($text,$concept,$href_tag_begin,$href_tag_end = "</a>") {
        $list_of_words = $concept;
        $list_of_words_cp = $list_of_words;

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
