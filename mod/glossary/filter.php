<?PHP // $Id$

    $textfilter_function = 'glossary_dynamic_link';

    if (function_exists($textfilter_function)) {
        return;
    }

    function glossary_dynamic_link($courseid, $text) {
        global $CFG;

        if (empty($courseid)) {
            if ($site = get_site()) {
                $courseid = $site->id;
            }
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
            
            $entries = get_records_select("glossary_entries", "glossaryid IN ($glossaries) AND usedynalink != 0 and approved != 0 and concept != ''","$ebylenght glossaryid","id,glossaryid, concept,casesensitive,$GLOSSARY_CONCEPT_IS_ENTRY $as category,fullmatch");
            $categories  = get_records_select("glossary_categories", "glossaryid IN ($glossaries) AND usedynalink != 0", "$cbylenght glossaryid","id,glossaryid, name $as concept, 1 $as casesensitive,$GLOSSARY_CONCEPT_IS_CATEGORY $as category, 1 $as fullmatch");
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
                                $gcat = get_record("glossary","id",$category->glossaryid);
                                if ( !$cm = get_coursemodule_from_instance("glossary", $category->glossaryid, $gcat->course) ) {
                                    $cm->id = 1;
                                }
                            }
                        }

                        $title = strip_tags("$glossary->name: " . get_string("category","glossary"). " $category->name");
                        $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=cat&hook=$concept->id\">";
                    } else {
                        if ( $lastglossary != $concept->glossaryid ) {
                            $glossary = get_record("glossary","id",$concept->glossaryid);
                            $lastglossary = $glossary->id;
                        }

                        $encodedconcept = urlencode($concept->concept);
                        $title = str_replace('"', "'", strip_tags("$glossary->name: $concept->concept"));
                        $href_tag_begin = "<a target=\"entry\" class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/glossary/showentry.php?courseid=$courseid&concept=$encodedconcept\" ".
                             "onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$courseid\&concept=$encodedconcept', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";
                    }
                    $currentconcept = $concept->concept;
                    if ( $currentconcept = trim(strip_tags($currentconcept)) ) {
                        //Avoid integers < 1000 to be linked. See bug 1446.
                        $intcurrent = intval($currentconcept);
                        if (!empty($intcurrent) && strval($intcurrent) == $currentconcept && $intcurrent < 1000) {
                            $currentconcept = '';
                        }
                        if ( !$concept->category ) {
                            if ( $aliases = get_records("glossary_alias","entryid",$concept->id, "alias") ) {
                                foreach ($aliases as $alias) {
                                    $currentalias = trim(strip_tags($alias->alias));
                                    //Avoid integers < 1000 to be linked. See bug 1446.
                                    $intcurrent = intval($currentalias);
                                    if (!(!empty($intcurrent) && strval($intcurrent) == $currentalias && $intcurrent < 1000)) {
                                        if ( $currentalias != '' ) {
                                            $currentconcept .= "|" . $currentalias;
                                        }
                                    }
                                }
                            }
                        }
                        if ($currentconcept) {
                            $text = glossary_link_concepts($text,$currentconcept,$href_tag_begin, "</a>",$concept->casesensitive,$concept->fullmatch);
                        }
                    }
                }
            }
        }
        return $text;
    }
    
    function glossary_link_concepts($text,$concept,$href_tag_begin,$href_tag_end = "</a>",$casesensitive,$fullmatch) {

        $list_of_words_cp = strip_tags($concept);

        $list_of_words_cp = trim($list_of_words_cp,'|');

        $list_of_words_cp = trim($list_of_words_cp);

        $list_of_words_cp = preg_quote($list_of_words_cp,'/');

        //Take out scaped pipes
        $list_of_words_cp = str_replace('\|','|',$list_of_words_cp);

        if ($fullmatch) {
            $invalidprefixs = "([a-zA-Z0-9])";
            $invalidsufixs  = "([a-zA-Z0-9])";

            //Avoid seaching in the string if it's inside invalidprefixs and invalidsufixs
            $words = array();
            $regexp = '/'.$invalidprefixs.'('.$list_of_words_cp.')|('.$list_of_words_cp.')'.$invalidsufixs.'/is';

            preg_match_all($regexp,$text,$list_of_words);

            if ($list_of_words) {
                foreach (array_unique($list_of_words[0]) as $key=>$value) {
                    $words['<*'.$key.'*>'] = $value;
                }
                if (!empty($words)) {
                    $text = str_replace($words,array_keys($words),$text);
                }
            }
        }

        //Now avoid searching inside the <nolink>tag
        $excludes = array();
        preg_match_all('/<nolink>(.+?)<\/nolink>/is',$text,$list_of_excludes);
        foreach (array_unique($list_of_excludes[0]) as $key=>$value) {
            $excludes['<+'.$key.'+>'] = $value;
        }
        if (!empty($excludes)) {
            $text = str_replace($excludes,array_keys($excludes),$text);
        }

        //Now avoid searching inside links
        $links = array();
        preg_match_all('/<A[\s](.+?)>(.+?)<\/A>/is',$text,$list_of_links);
        foreach (array_unique($list_of_links[0]) as $key=>$value) {
            $links['<@'.$key.'@>'] = $value;
        }
        if (!empty($links)) {
            $text = str_replace($links,array_keys($links),$text);
        }

        //Now avoid searching inside every tag
        $final = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_tags);
        foreach (array_unique($list_of_tags[0]) as $key=>$value) {
            $final['<|'.$key.'|>'] = $value;
        }
        if (!empty($final)) {
            $text = str_replace($final,array_keys($final),$text);
        }


        if ($casesensitive) {
            $text = preg_replace('/('.$list_of_words_cp.')/s', $href_tag_begin.'$1'.$href_tag_end, $text);
        } else {
            $text = preg_replace('/('.$list_of_words_cp.')/is', $href_tag_begin.'$1'.$href_tag_end, $text);
        }

        //Now rebuild excluded areas
        if (!empty($final)) {
            $text = str_replace(array_keys($final),$final,$text);
        }
        if (!empty($links)) {
            $text = str_replace(array_keys($links),$links,$text);
        }
        if (!empty( $excludes)) {
            $text = str_replace(array_keys($excludes),$excludes,$text);
        }
        if ($fullmatch and !empty($words)) {
            $text = str_replace(array_keys($words),$words,$text);
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
