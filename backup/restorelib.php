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

    //This function read the xml file and store it data from the info zone in an object
    function restore_read_xml_info ($xml_file) {

        //We call the main read_xml function, with todo = INFO
        $info = restore_read_xml ($xml_file,"INFO",false);

        return $info;
    }

    //This function read the xml file and store it data from the course header zone in an object  
    function restore_read_xml_course_header ($xml_file) {

        //We call the main read_xml function, with todo = COURSE_HEADER
        $info = restore_read_xml ($xml_file,"COURSE_HEADER",false);

        return $info;
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
        var $info = "";        //Information collected. Temp storage.
        var $preferences = ""; //Preferences about what to load !!
        var $finished = false; //Flag to say xml_parse to stop

        //This function is used to get the current contents property value
        //They are trimed and converted from utf8
        function getContents() {
            return trim(utf8_decode($this->content));
        }
 
        //This is the startTag handler we use where we are reading the info zone (todo="INFO")
        function startElementInfo($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Check if we are into INFO zone
            //if ($this->tree[2] == "INFO")                                                             //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the course header zone (todo="COURSE_HEADER")
        function startElementCourseHeader($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Check if we are into COURSE_HEADER zone
            //if ($this->tree[3] == "HEADER")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
        }

        //This is the startTag default handler we use when todo is undefined
        function startElement($parser, $tagName, $attrs) {
            $this->level++;
            $this->tree[$this->level] = $tagName;
            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
        }
 
        //This is the endTag handler we use where we are reading the info zone (todo="INFO")
        function endElementInfo($parser, $tagName) {
            //Check if we are into INFO zone
            if ($this->tree[2] == "INFO") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 3) {
                    switch ($tagName) {
                        case "NAME":
                            $this->info->backup_name = $this->getContents();
                            break;
                        case "MOODLE_VERSION":
                            $this->info->backup_moodle_version = $this->getContents();
                            break;
                        case "MOODLE_RELEASE":
                            $this->info->backup_moodle_release = $this->getContents();
                            break;
                        case "BACKUP_VERSION":
                            $this->info->backup_backup_version = $this->getContents();
                            break;
                        case "BACKUP_RELEASE":
                            $this->info->backup_backup_release = $this->getContents();
                            break;
                        case "DATE":
                            $this->info->backup_date = $this->getContents();
                            break;
                    }
                }
                if ($this->tree[3] == "DETAILS") {
                    if ($this->level == 4) {
                        switch ($tagName) {
                            case "USERS":
                                $this->info->backup_users = $this->getContents();
                                break;
                            case "LOGS":
                                $this->info->backup_logs = $this->getContents();
                                break;
                            case "USERFILES":
                                $this->info->backup_user_files = $this->getContents();
                                break;
                            case "COURSEFILES":
                                $this->info->backup_course_files = $this->getContents();
                                break;
                        }
                    }
                    if ($this->level == 5) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempName = $this->getContents();
                                break;
                            case "INCLUDED":
                                $this->info->mods[$this->info->tempName]->backup = $this->getContents();
                                break;
                            case "USERINFO":
                                $this->info->mods[$this->info->tempName]->userinfo = $this->getContents();
                                break;
                        }
                    }
                }
            }


            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

            //Stop parsing if todo = INFO and tagName = INFO (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "INFO") {
                $this->finished = true;
            }
        }

        //This is the endTag handler we use where we are reading the course_header zone (todo="COURSE_HEADER")
        function endElementCourseHeader($parser, $tagName) {
            //Check if we are into COURSE_HEADER zone
            if ($this->tree[3] == "HEADER") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case "ID":
                            $this->info->course_id = $this->getContents();
                            break;
                        case "PASSWORD":
                            $this->info->course_password = $this->getContents();
                            break;
                        case "FULLNAME":
                            $this->info->course_fullname = $this->getContents();
                            break;
                        case "SHORTNAME":
                            $this->info->course_shortname = $this->getContents();
                            break;
                        case "SUMMARY":
                            $this->info->course_summary = $this->getContents();
                            break;
                        case "FORMAT":
                            $this->info->course_format = $this->getContents();
                            break;
                        case "NEWSITEMS":
                            $this->info->course_newsitems = $this->getContents();
                            break;
                        case "TEACHER":
                            $this->info->course_teacher = $this->getContents();
                            break;
                        case "TEACHERS":
                            $this->info->course_teachers = $this->getContents();
                            break;
                        case "STUDENT":
                            $this->info->course_student = $this->getContents();
                            break;
                        case "STUDENTS":
                            $this->info->course_students = $this->getContents();
                            break;
                        case "GUEST":
                            $this->info->course_guest = $this->getContents();
                            break;
                        case "STARDATE":
                            $this->info->course_stardate = $this->getContents();
                            break;
                        case "NUMSECTIONS":
                            $this->info->course_numsections = $this->getContents();
                            break;
                        case "SHOWRECENT":
                            $this->info->course_showrecent = $this->getContents();
                            break;
                        case "MARKER":
                            $this->info->course_marker = $this->getContents();
                            break;
                        case "TIMECREATED":
                            $this->info->course_timecreated = $this->getContents();
                            break;
                        case "TIMEMODIFIED":
                            $this->info->course_timemodified = $this->getContents();
                            break;
                    }
                }
                if ($this->tree[4] == "CATEGORY") {
                    if ($this->level == 5) {
                        switch ($tagName) {
                            case "ID":
                                $this->info->category->id = $this->getContents();
                                break;
                            case "NAME":
                                $this->info->category->name = $this->getContents();
                                break;
                        }
                    }
                }

            }
            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

            //Stop parsing if todo = COURSE_HEADER and tagName = HEADER (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "HEADER") {
                $this->finished = true;
            }
        }

        //This is the endTag default handler we use when todo is undefined
        function endElement($parser, $tagName) {
            if (trim($this->content))                                                                     //Debug
                echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";
        }

        //This is the handler to read data contents (simple accumule it)
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
        //Depending of the todo we use some element_handler or another
        if ($todo == "INFO") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        } else if ($todo == "COURSE_HEADER") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementCourseHeader", "endElementCourseHeader");
        } else {
            //Define default handlers (must no be invoked when everything become finished)
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        }
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
        //Get info from parser
        $info = $moodle_parser->info;
        
        //Clear parser mem
        xml_parser_free($xml_parser);

        if ($status) {
            return $info;
        } else {
            return $status;
        }
    }
?>
