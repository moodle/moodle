<?PHP // $Id$

    $textfilter_function = 'glossary_dynamic_link';

    if (function_exists($textfilter_function)) {
        return;
    }

    function glossary_dynamic_link($courseid, $text) {
    global $CFG;

        $GLOSSARY_CONCEPT_IS_ENTRY = 0;
        $GLOSSARY_CONCEPT_IS_CATEGORY = 1;

        $glossarieslist = get_records_select("glossary", "usedynalink != 0 and (course = $courseid or globalglossary != 0)","globalglossary, id");
        if ( $glossarieslist ) {
            $glossaries = "";
            foreach ( $glossarieslist as $glossary ) {
                $glossaries .= "$glossary->id,";
            }
            $glossaries=substr($glossaries,0,-1);
///         sorting by the lenght of the concept in order to assure that large concepts 
///            could be linked first, if they exist in the text to parse
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
            
            $entries = get_records_select("glossary_entries", "glossaryid IN ($glossaries) AND usedynalink != 0 and approved != 0 and concept != ''","$ebylenght glossaryid","id,glossaryid,concept,casesensitive,$GLOSSARY_CONCEPT_IS_ENTRY category,fullmatch");
            $categories  = get_records_select("glossary_categories", "glossaryid IN ($glossaries) AND usedynalink != 0", "$cbylenght glossaryid","id,glossaryid,name concept, 1 casesensitive,$GLOSSARY_CONCEPT_IS_CATEGORY category, 1 fullmatch");
            if ( $entries and $categories ) {
                $concepts = array_merge($entries, $categories);
                usort($concepts,'glossary_sort_entries_by_lenght');
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
                            if ( $cm->instance != $category->glossaryid  ) {
                                $cm = get_coursemodule_from_instance("glossary", $category->glossaryid, $courseid);
                            }
                        }

                        $title = strip_tags("$glossary->name: " . get_string("category","glossary"). " $category->name");
                        $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=cat&hook=$concept->id\">";
                    } else {
                        if ( $lastglossary != $concept->glossaryid ) {
                            $glossary = get_record("glossary","id",$concept->glossaryid);
                            $lastglossary = $glossary->id;
                        }

                        $concepttitle = urlencode($concept->concept);
                        $title = strip_tags("$glossary->name: $concepttitle");
                        $href_tag_begin = "<a target=\"entry\" class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/showentry.php?courseid=$courseid&concept=$concepttitle\" ".
                             "onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$courseid\&concept=$concepttitle', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";
                    }
                    $replace = "\\[]'\"*()\?";
                    $currentconcept = glossary_addslashes($replace,$concept->concept);                    
                    if ( $currentconcept = trim(strip_tags($currentconcept)) ) {
                        if ( !$concept->category ) {
                            if ( $aliases = get_records("glossary_alias","entryid",$concept->id, "alias") ) {
                                foreach ($aliases as $alias) {
                                    $currentalias = glossary_addslashes($replace,$alias->alias);
                                    if ( trim( $currentalias ) != '' ) {
                                        $currentconcept .= "|" . trim($currentalias);
                                    }
                                }
                            }
                        }
                        $text = glossary_link_concepts($text,$currentconcept,$href_tag_begin, "</a>",$concept->casesensitive,$concept->fullmatch);
                    }
                }
            }
        }
        return $text;
    }
    
    function glossary_link_concepts($text,$concept,$href_tag_begin,$href_tag_end = "</a>",$casesensitive,$fullmatch) {
        $concept = str_replace("/", "\/", $concept);
        $list_of_words_cp = $concept;

        if ($list_of_words_cp{0}=="|") {
            $list_of_words_cp{0} = "";
        }
        if ($list_of_words_cp{strlen($list_of_words_cp)-1}=="|") {
            $list_of_words_cp{strlen($list_of_words_cp)-1}="";
        }

        $list_of_words_cp = trim($list_of_words_cp);
        if ($fullmatch) {
            $invalidprefixs = "([a-zA-Z0-9])";
            $invalidsufixs  = "([a-zA-Z0-9])";

            // getting ride of words or phrases that containg the pivot concept on it		
            $words = array();
            $regexp = '/' . $invalidprefixs . "(" . $list_of_words_cp . ")" . "|"  . "(" . $list_of_words_cp . ")". $invalidsufixs .  '/is';
            preg_match_all($regexp,$text,$list_of_words);

            if ($list_of_words) {
                foreach (array_unique($list_of_words[0]) as $key=>$value) {
                    $words['<*'.$key.'*>'] = $value;
                }
                if ( $words ) {
                    $text = str_replace($words,array_keys($words),$text);
                }
            }
        }

        // getting ride of "nolink" tags
        $excludes = array();
        preg_match_all('/<nolink>(.+?)<\/nolink>/is',$text,$list_of_excludes);
        foreach (array_unique($list_of_excludes[0]) as $key=>$value) {
            $excludes['<+'.$key.'+>'] = $value;
        }
        if ( $excludes ) {
            $text = str_replace($excludes,array_keys($excludes),$text);
        }

        // getting ride of "A" tags
        $links = array();
        preg_match_all('/<A (.+?)>(.+?)<\/A>/is',$text,$list_of_links);

        foreach (array_unique($list_of_links[0]) as $key=>$value) {
            $links['<@'.$key.'@>'] = $value;
        }
        if ( $links ) {
            $text = str_replace($links,array_keys($links),$text);
        }
        // getting ride of all other tags
        $final = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_tags);

        foreach (array_unique($list_of_tags[0]) as $key=>$value) {
            $final['<|'.$key.'|>'] = $value;
        }

        $text = str_replace($final,array_keys($final),$text);

        $list_of_words_cp = "(".$list_of_words_cp.")";
        if ( $casesensitive ) {
            $text = ereg_replace("$list_of_words_cp", "$href_tag_begin"."\\1"."$href_tag_end", $text);
        } else {
            $text = eregi_replace("$list_of_words_cp", "$href_tag_begin"."\\1"."$href_tag_end", $text);
        }

        if ( $final ) {
            $text = str_replace(array_keys($final),$final,$text);
        }
        if ( $links ) {
            $text = str_replace(array_keys($links),$links,$text);
        }
        if ( $excludes ) {
            $text = str_replace(array_keys($excludes),$excludes,$text);
        }
        if ( $fullmatch and isset($words) ) {
            if ($words) {
                $text = str_replace(array_keys($words),$words,$text);
            }
        }
        return $text;
    }
    
    function glossary_sort_entries_by_lenght ( $entry0, $entry1 ) {
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
