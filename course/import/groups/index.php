<?php // $Id: uploadgroups.php, 2005/10/31 19:09:31

/// Bulk group creation registration script from a comma separated file

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/group/lib.php');

    $id = required_param('id', PARAM_INT);    // Course id

    if (! $course = get_record('course', 'id', $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $id);


    if (!has_capability('moodle/course:managegroups', $context)) {
        error("You do not have the required permissions to manage groups.");
    }

    //if (!confirm_sesskey()) {
    //    print_error('confirmsesskeybad', 'error');
    //}

      $strimportgroups = get_string("importgroups");

    $csv_encode = '/\&\#44/';
    if (isset($CFG->CSV_DELIMITER)) {
        $csv_delimiter = '\\' . $CFG->CSV_DELIMITER;
        $csv_delimiter2 = $CFG->CSV_DELIMITER;

        if (isset($CFG->CSV_ENCODE)) {
            $csv_encode = '/\&\#' . $CFG->CSV_ENCODE . '/';
        }
    } else {
        $csv_delimiter = "\,";
        $csv_delimiter2 = ",";
    }

/// Print the header
    $navlinks = array();
    $navlinks[] = array('name' => $course->shortname,
                        'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => get_string('import'),
                        'link' => "$CFG->wwwroot/course/import.php?id=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => $strimportgroups, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: $strimportgroups", $course->fullname, $navigation);

/// If a file has been uploaded, then process it

    require_once($CFG->dirroot.'/lib/uploadlib.php');
    $um = new upload_manager('userfile',false,false,null,false,0);
    if ($um->preprocess_files() and confirm_sesskey()) {
        $filename = $um->files['userfile']['tmp_name'];

        //Fix mac/dos newlines
        $text = my_file_get_contents($filename);
        $text = preg_replace('!\r\n?!',"\n",$text);
        $fp = fopen($filename, "w");
        fwrite($fp,$text);
        fclose($fp);

        $fp = fopen($filename, "r");

        // make arrays of valid fields for error checking
        $required = array("groupname" => 1, );
        $optionalDefaults = array("lang" => 1, );
        $optional = array("coursename" => 1,
                          "idnumber" =>1,
                          "description" => 1,
                          "enrolmentkey" => 1,
                          "theme" => 1,
                          "picture" => 1,
                          "hidepicture" => 1, );

        // --- get header (field names) ---
        $header = split($csv_delimiter, fgets($fp,1024));
        // check for valid field names
        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
            if ( !(isset($required[$h]) or
                isset($optionalDefaults[$h]) or
                isset($optional[$h])) ) {
                print_error('invalidfieldname', 'error', 'index.php?id='.$id.'&amp;sesskey='.$USER->sesskey, $h);
            }
            if ( isset($required[$h]) ) {
                $required[$h] = 2;
            }
        }
        // check for required fields
        foreach ($required as $key => $value) {
            if ($value < 2) {
                print_error('fieldrequired', 'error', 'uploaduser.php?id='.$id.'&amp;sesskey='.$USER->sesskey, $key);
            }
        }
        $linenum = 2; // since header is line 1

        while (!feof ($fp)) {

            $newgroup = new object();//to make Martin happy
            foreach ($optionalDefaults as $key => $value) {
                $newgroup->$key = current_language(); //defaults to current language
            }
           //Note: commas within a field should be encoded as &#44 (for comma separated csv files)
           //Note: semicolon within a field should be encoded as &#59 (for semicolon separated csv files)
            $line = split($csv_delimiter, fgets($fp,1024));
            foreach ($line as $key => $value) {
                //decode encoded commas
                $record[$header[$key]] = preg_replace($csv_encode,$csv_delimiter2,trim($value));
            }
            if ($record[$header[0]]) {
                // add a new group to the database

                // add fields to object $user
                foreach ($record as $name => $value) {
                    // check for required values
                    if (isset($required[$name]) and !$value) {
                        error(get_string('missingfield', 'error', $name). " ".
                              get_string('erroronline', 'error', $linenum) .". ".
                              get_string('processingstops', 'error'),
                              'uploaduser.php?sesskey='.$USER->sesskey);
                        //print_error('missingfield', 'error', 'uploaduser.php?sesskey='.$USER->sesskey, $name);
                    }
                    else if ($name == "groupname") {
                        $newgroup->name = addslashes($value);
                    }
                    // normal entry
                    else {
                        $newgroup->{$name} = addslashes($value);
                    }
                }
                ///Find the courseid of the course with the given shortname

                //if idnumber is set, we use that.
                //unset invalid courseid
                if (isset($newgroup->idnumber)){
                    if (!$mycourse = get_record('course', 'idnumber',$newgroup->idnumber)){
                        notify(get_string('unknowncourseidnumber', 'error', $newgroup->idnumber));
                        unset($newgroup->courseid);//unset so 0 doesnt' get written to database
                    }
                    $newgroup->courseid = $mycourse->id;
                }
                //else use course short name to look up
                //unset invalid coursename (if no id)

                else if (isset($newgroup->coursename)){
                    if (!$mycourse = get_record('course', 'shortname',$newgroup->coursename)){
                        notify(get_string('unknowncourse', 'error', $newgroup->coursename));
                        unset($newgroup->courseid);//unset so 0 doesnt' get written to database
                    }
                    $newgroup->courseid = $mycourse->id;
                }
                //else juse use current id
                else{
                    $newgroup->courseid = $id;
                }

                //if courseid is set
                if (isset($newgroup->courseid)){

                    $newgroup->courseid = (int)$newgroup->courseid;
                    $newgroup->timecreated = time();
                    $linenum++;
                    $groupname = $newgroup->name;
                    $newgrpcoursecontext = get_context_instance(CONTEXT_COURSE, $newgroup->courseid);

                    ///Users cannot upload groups in courses they cannot update.
                    if (!has_capability('moodle/course:managegroups', $newgrpcoursecontext)){
                        notify(get_string('nopermissionforcreation','group',$groupname));

                    } else {
                        if ( $groupid = groups_get_group_by_name($newgroup->courseid, $groupname) || !($newgroup->id = groups_create_group($newgroup)) ) {

                            //Record not added - probably because group is already registered
                            //In this case, output groupname from previous registration
                            if ($groupid) {
                                notify("$groupname :".get_string('groupexistforcourse', 'error', $groupname));
                            } else {
                                notify(get_string('groupnotaddederror', 'error', $groupname));
                            }
                        }
                        else {
                            notify(get_string('groupaddedsuccesfully', 'group', $groupname));
                        }
                    }
                } //close courseid validity check
                unset ($newgroup);
            }//close if ($record[$header[0]])
        }//close while($fp)
        fclose($fp);

        echo '<hr />';
    }

/// Print the form
    require('mod.php');

    print_footer($course);

function my_file_get_contents($filename, $use_include_path = 0) {
/// Returns the file as one big long string

    $data = "";
    $file = @fopen($filename, "rb", $use_include_path);
    if ($file) {
        while (!feof($file)) {
            $data .= fread($file, 1024);
        }
        fclose($file);
    }
    return $data;
}

?>
