<?PHP //$Id$
    //Functions used in restore
   
    //This function unzips a zip file in the same directory that it is
    //It automatically uses pclzip or command line unzip
    function restore_unzip ($file,$moodle_home) {
        
        global $CFG;

        $status = true;

        if (empty($CFG->unzip)) {    // Use built-in php-based unzip function
            include_once($moodle_home."/lib/pclzip/pclzip.lib.php");
            $archive = new PclZip($file);
            if (!$list = $archive->extract(dirname($file))) {
                $status = false;
            }
        } else {                     // Use external unzip program
            $command = "cd ".dirname($file)."; $CFG->unzip -o ".basename($file);
            Exec($command);
        }

        return $status;
    }

    //This function checks if moodle.xml seems to be a valid xml file
    //(exists, has an xml header and a course main tag
    function restore_check_moodle_file ($file) {
    
        $status = true;

        //Check if it exists
        if ($status = is_file($file)) {
            //Open it and read the first 200 bytes (chars)
            $handle = fopen ($file, "r");
            $first_chars = fread($handle,200);
            $status = fclose ($handle);
            //Chek if it has the requires strings
            if ($status) {
                $status = strpos($first_chars,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
                if ($status !== false) {
                    $status = strpos($first_chars,"<MOODLE_BACKUP>");
                }
            }
        }   

        return $status;  
    }   

    //This function read the xml file and store it data from the info section in an array
    function restore_read_xml_info ($xml_file) {

        //We call the main read_xml function, with todo = INFO
        $info = restore_read_xml ($xml_file,"INFO",false);

        return $info;

        echo "finished";
    }

    //=====================================================================================
    //==                                                                                 ==
    //==                         XML Functions (SAX)                                     ==
    //==                                                                                 ==
    //=====================================================================================

    //This is the class used to do all the xml parse
    class MoodleParser {

        var $level = 0;        //Level we are
        var $tree = array();   //Array of levels we are
        var $content = "";     //Content under current level
        var $todo = "";        //What we hav to do when parsing
        var $info = array();   //Information collected (todo = INFO)
        var $preferences = ""; //Preferences about what to load !!
        var $finished = false; //Flag to say xml_parse to stop

        function startElement($parser, $tagName, $attrs) {
            $this->tree[$this->level] = $tagName;
            //echo str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";
            $this->level++;
        }

        function endElement($parser, $tagName) {
            if (trim($this->content)) {
                //echo utf8_decode(str_repeat("&nbsp;",$this->level*2).$this->content."<br>\n");
            }
            $this->level--;
            //echo str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";
            $this->tree[$this->level] = "";
            $this->content = "";

            //Stop parsing if todo = INFO and tagName = INFO
            //Speed up a lot (avoid parse all)
            //echo $tagName."<br>";
            if (($this->todo == "INFO") and ($tagName == "INFO")) {
                $this->finished = true;
            }
        }

        function characterData($parser, $data) {
            $this->content .= $data;
        }
    }
    
    //This function executes the MoodleParser
    function restore_read_xml ($xml_file,$todo,$preferences) {

        $status = true;

        $xml_parser = xml_parser_create();
        $moodle_parser = new MoodleParser();
        $moodle_parser->todo = $todo;
        $moodle_parser->preferences = $preferences;
        xml_set_object($xml_parser,&$moodle_parser);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "characterData");
        $fp = fopen($xml_file,"r")
            or $status = false;
        if ($status) {
            while ($data = fread($fp, 4096) and !$moodle_parser->finished)
                    xml_parse($xml_parser, $data, feof($fp))
                            or die(sprintf("XML error: %s at line %d",
                            xml_error_string(xml_get_error_code($xml_parser)),
                                    xml_get_current_line_number($xml_parser)));
            fclose($fp);
        }
        xml_parser_free($xml_parser);

        if ($Status) {
            return $info;
        } else {
            return $status;
        }
    }
?>
