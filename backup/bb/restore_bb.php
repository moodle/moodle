<?php
// This file facilitates the conversion of a Blackboard course export
// into a Moodle course export.  It assumes an unzipped directory and makes in-place alterations.
  
// Ziba Scott <ziba@linuxbox.com> 10-25-04
  
function get_subdirs($directory){
    $opendirectory = opendir( $directory );
    while($filename = readdir($opendirectory)) {
        if (is_dir($directory.$filename) and $filename != ".." and $filename != "."){
            $subdirs[] = $filename;
        }
    }
    closedir($opendirectory);
    return $subdirs;
}


function choose_bb_xsl($manifest){
    $f = fopen($manifest,"r");
    $buffer = fgets($f, 400);
    $buffer = fgets($f, 400);
    fclose($f);
    if (strstr($buffer,"xmlns:bb=\"http://www.blackboard.com/content-packaging/\"")){
        return "bb6_to_moodle.xsl";
    }
    return "bb5.5_to_moodle.xsl";
}


function blackboard_convert($dir){
    global $CFG;

    if (!function_exists('xslt_create')) {  // XSLT MUST be installed for this to work
        return true;
    }

    // Check for a Blackboard manifest file
    if(is_file($dir."/imsmanifest.xml")){

        //Select the proper XSL file
        $xslt_file = choose_bb_xsl($dir."/imsmanifest.xml");

        //TODO: Use the get_string function
        echo "<li>Converting Blackboard export</li>";

        // The XSL file must be in the same directory as the Blackboard files when it is processed
        copy($CFG->dirroot."/backup/bb/$xslt_file", "$dir/$xslt_file");
        $startdir = getcwd();
        chdir($dir);


        // Process the Blackboard XML files with the chosen XSL file.
        // The imsmanifest contains all the XML files and their relationships. 
        // The XSL processor will open them as needed.
        $xsltproc = xslt_create();
        if (!xslt_process($xsltproc, "imsmanifest.xml", $xslt_file, "$dir/moodle.xml")) {
            dump("Failed writing xml file");
            chdir($startdir);
            return false;
        }


        // Copy the Blackboard course files into the moodle course_files structure
        $subdirs = get_subdirs($dir."/");
        mkdir("$dir/course_files");
        foreach ($subdirs as $subdir){
            rename($subdir, "course_files/$subdir");
        }

        chdir($startdir);

        // Blackboard export successfully converted
        return true;
    }
    // This is not a Blackboard export
    return true;

}

?>
