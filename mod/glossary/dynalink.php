<?PHP

    function glossary_dynamic_link($courseid, $text,$glossaryid = NULL) {
    global $CFG;
    static $entries;
        if ( !$glossaryid ) {
            $glossary = get_record("glossary","course",$courseid,"mainglossary",1);
        } else {
            $glossary = get_record("glossary","course",$courseid,"id",$glossaryid);
        }
        if ( $glossary ) {
            if ( !$entries ) {
                $entries = get_records("glossary_entries","glossaryid",$glossary->id,"concept ASC");
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
        $list_of_words = eregi_replace("[^-a-zA-Z0-9&']", " ", $concept);
        $list_array = explode(" ", $list_of_words);
        for ($i=0; $i<sizeof($list_array); $i++) {
            if (strlen($list_array[$i]) == 1) {
                $list_array[$i] = "";
            }
        }
        $list_of_words = implode(" ", $list_array);
        $list_of_words_cp = $list_of_words;

        $final = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_words);

        foreach (array_unique($list_of_words[0]) as $key=>$value) {
            $final['<|'.$key.'|>'] = $value;
        }

        $text = str_replace($final,array_keys($final),$text);
        $list_of_words_cp = eregi_replace(" +", "|", $list_of_words_cp);

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
?>
