<?PHP //$Id$
    //Functions used in restore
   
    //This function unzips a zip file in the samen directory that it is
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
?>
