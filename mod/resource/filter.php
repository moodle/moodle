<?PHP // $Id$
    //This function provides automatic linking to
    //resources when its name (title) is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by stronk7. Enjoy! :-)

    $textfilter_function='resource_names_filter';

    if (function_exists($textfilter_function)) {
        return;
    }

    function resource_names_filter($courseid, $text) {

        global $CFG;

        if (empty($courseid)) {
            if ($site = get_site()) {
                $courseid = $site->id;
            }
        }

        switch ($CFG->dbtype) {
            case 'postgres7':
                $as = 'as';
                break;
            case 'mysql':
                $as = '';
                break;
        }    

        ///sorting by the lenght of the title in order to assure that large resources 
        ///could be linked first, if they exist in the text to parse

        switch ($CFG->dbtype) {
            case "postgres7":
            case "mysql":
                $rbylenght = "CHAR_LENGTH(name) desc";
            break;
            default:
                $rbylenght = "";
            break;
        }
            
        $resources = get_records_select("resource", "course = $courseid", "$rbylenght");

        if (!empty($resources)) {
            $cm = '';
            foreach ($resources as $resource) {
                if (!$cm = get_coursemodule_from_instance("resource", $resource->id, $courseid)) {
                    $cm->id = 1;
                }
                $title = strip_tags($resource->name);
                $href_tag_begin = "<a class=\"autolink\" title=\"$title\" href=\"$CFG->wwwroot/mod/resource/view.php?id=$cm->id\">";
                $currentname = $resource->name;                    
                if ($currentname = trim($currentname)) {
                    //Avoid integers < 1000 to be linked. See bug 1441.
                    $intcurrent = intval($currentname);
                    if (!(!empty($intcurrent) && strval($intcurrent) == $currentname && $intcurrent < 1000)) {
                        $text = resource_link_names($text,$currentname,$href_tag_begin, "</a>");
                    }
                }
            }
        }
        return $text;
    }
    
    function resource_link_names($text,$name,$href_tag_begin,$href_tag_end = "</a>") {

        $list_of_words_cp = strip_tags($name);

        $list_of_words_cp = trim($list_of_words_cp,'|');

        $list_of_words_cp = trim($list_of_words_cp);

        $list_of_words_cp = preg_quote($list_of_words_cp,'/');

        $invalidprefixs = "([a-zA-Z0-9])";
        $invalidsufixs  = "([a-zA-Z0-9])";

        //Avoid seaching in the string if it's inside invalidprefixs and invalidsufixs
        $words = array();
        $regexp = '/'.$invalidprefixs.'('.$list_of_words_cp.')|('.$list_of_words_cp.')'.$invalidsufixs.'/is';
        preg_match_all($regexp,$text,$list_of_words);

        foreach (array_unique($list_of_words[0]) as $key=>$value) {
            $words['<*'.$key.'*>'] = $value;
        }
        if (!empty($words)) {
            $text = str_replace($words,array_keys($words),$text);
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

        $text = preg_replace('/('.$list_of_words_cp.')/is', $href_tag_begin.'$1'.$href_tag_end,$text);

        //Now rebuild excluded areas
        if (!empty($final)) {
            $text = str_replace(array_keys($final),$final,$text);
        }
        if (!empty($links)) {
            $text = str_replace(array_keys($links),$links,$text);
        }
        if (!empty($excludes)) {
            $text = str_replace(array_keys($excludes),$excludes,$text);
        }
        if (!empty($words)) {
            $text = str_replace(array_keys($words),$words,$text);
        }
        return $text;
    }
?>
