<?php
// This file facilitates the conversion of a Blackboard course export
// into a Moodle course export.  It assumes an unzipped directory and makes in-place alterations.

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

// Ziba Scott <ziba@linuxbox.com> 10-25-04
require_once($CFG->dirroot.'/backup/bb/xsl_emulate_xslt.inc');

function get_subdirs($directory){
    if (!$opendirectory = opendir( $directory )) {
        return array();
    }
    while(false !== ($filename = readdir($opendirectory))) {
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
    global $CFG, $OUTPUT;

    throw new coding_exception('bb_convert was not converted to new file api yet, sorry');

    // Check for a Blackboard manifest file
    if (is_readable($dir.'/imsmanifest.xml') && !is_readable($dir.'/moodle.xml')){

        if (!function_exists('xslt_create')) {  // XSLT MUST be installed for this to work
            echo $OUTPUT->notification('You need the XSLT library installed in PHP to open this Blackboard file');
            return false;
        }

        //Select the proper XSL file
        $xslt_file = choose_bb_xsl($dir.'/imsmanifest.xml');


        //TODO: Use the get_string function for this
        echo "<li>Converting Blackboard export</li>";

        // The XSL file must be in the same directory as the Blackboard files when it is processed
        if (!copy($CFG->dirroot."/backup/bb/$xslt_file", "$dir/$xslt_file")) {
            echo $OUTPUT->notification('Could not copy the XSLT file to '."$dir/$xslt_file");
            return false;
        }

        // Change to that directory
        $startdir = getcwd();
        chdir($dir);


        // Process the Blackboard XML files with the chosen XSL file.
        // The imsmanifest contains all the XML files and their relationships.
        // The XSL processor will open them as needed.
        $xsltproc = xslt_create();
        if (!xslt_process($xsltproc, 'imsmanifest.xml', "$dir/$xslt_file", "$dir/moodle.xml")) {
            echo $OUTPUT->notification('Failed writing xml file');
            chdir($startdir);
            return false;
        }


        // Copy the Blackboard course files into the moodle course_files structure
        $subdirs = get_subdirs($dir."/");
        mkdir("$dir/course_files", $CFG->directorypermissions);
        foreach ($subdirs as $subdir){
            rename($subdir, "course_files/$subdir");
            rename_hexfiles($subdir);
        }

        chdir($startdir);

        // Blackboard export successfully converted
        return true;
    }
    // This is not a Blackboard export
    return true;

}

/**
 * grabs all files in the directory, checks if the filenames start with a ! or @
 * then checks to see if the name is a hex - if so, it translates/renames correctly.
 *
 * @param string $subdir - the directory to parse.
 *
 */
function rename_hexfiles($subdir) {
    //this bit of code grabs all files in the directory, and if they start with ! or @, performs the name conversion
    if ($handle = opendir("course_files/$subdir")) {
        while ($file = readdir($handle)) {
            if ($file == '..' or $file == '.') { //don't bother processing these!
                continue;
            }
            if(substr($file,0,1)=="!" || substr($file,0,1)=="@"){
                $outputfilename = "";
                $filebase = substr($file,1,strrpos($file,".")-1);
                if (ctype_xdigit($filebase)) { //check if this name is a hex - if not, don't bother to rename
                    $filenamesplit = str_split($filebase,2);
                    foreach($filenamesplit as $hexvalue){
                        $outputfilename .= chr(hexdec($hexvalue));
                    }
                    $outputfilename .= strrchr($file,".");
                    rename("course_files/$subdir/$file","course_files/$subdir/$outputfilename");
                }
            }
        }
        closedir($handle);
    }
}
