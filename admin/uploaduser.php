<?PHP // $Id$

/// Bulk user registration script from a comma separated file
/// Returns list of users with their user ids

    require_once("../config.php");

    optional_variable($numusers, 0);

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to edit users this way.");
    }

    if (! $site = get_site()) {
        error("Could not find site-level course");
    }

    if (!$adminuser = get_admin()) {
        error("Could not find site admin");
    }

    $streditmyprofile = get_string("editmyprofile");
    $stradministration = get_string("administration");
    $strchoose = get_string("choose");
    $struser = get_string("user");
    $strusers = get_string("users");
    $strusersnew = get_string("usersnew");
    $struploadusers = get_string("uploadusers");
    $straddnewuser = get_string("importuser");

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

    print_header("$site->shortname: $struploadusers", $site->fullname, 
                 "<a href=\"index.php\">$stradministration</a> -> 
                  <a href=\"users.php\">$strusers</a> -> $struploadusers");


/// If a file has been uploaded, then process it

    if ($filename = valid_uploaded_file($_FILES['userfile'])) {

        //Fix mac/dos newlines
        $text = my_file_get_contents($filename);
        $text = preg_replace('!\r\n?!',"\n",$text);
        $fp = fopen($filename, "w");
        fwrite($fp,$text);
        fclose($fp);

        $fp = fopen($filename, "r");

        // make arrays of valid fields for error checking
        $required = array("username" => 1, 
                          "password" => 1, 
                          "firstname" => 1, 
                          "lastname" => 1,
                          "email" => 1);
        $optionalDefaults = array("institution" => 1, 
                                  "department" => 1, 
                                  "city" => 1, 
                                  "country" => 1,
                                  "lang" => 1, 
                                  "timezone" => 1);
        $optional = array("idnumber" => 1, 
                          "icq" => 1, 
                          "phone1" => 1, 
                          "phone2" => 1,
                          "address" => 1, 
                          "url" => 1,
                          "description" => 1, 
                          "mailformat" => 1, 
                          "maildisplay" => 1, 
                          "htmleditor" => 1, 
                          "autosubscribe" => 1,
                          "idnumber" => 1, 
                          "icq" => 1, 
                          "course1" => 1, 
                          "course2" => 1,
                          "course3" => 1, 
                          "course4" => 1, 
                          "course5" => 1,
			  "group1" => 1,
			  "group2" => 1,
			  "group3" => 1,
			  "group4" => 1,
			  "group5" =>1);

        // --- get header (field names) ---
        $header = split($csv_delimiter, fgets($fp,1024));
        // check for valid field names
        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
            if (!($required[$h] or $optionalDefaults[$h] or $optional[$h])) {
                error(get_string('invalidfieldname', 'error', $h), 'uploaduser.php');
            }
            if ($required[$h]) {
                $required[$h] = 2;
            }
        }
        // check for required fields
        foreach ($required as $key => $value) {
            if ($value < 2) {
                error(get_string('fieldrequired', 'error', $key), 'uploaduser.php');
            }
        }
        $linenum = 2; // since header is line 1

        while (!feof ($fp)) {
            foreach ($optionalDefaults as $key => $value) {
                $user->$key = addslashes($adminuser->$key);
            }
           //Note: commas within a field should be encoded as &#44 (for comma separated csv files)
           //Note: semicolon within a field should be encoded as &#59 (for semicolon separated csv files)
            $line = split($csv_delimiter, fgets($fp,1024));
            foreach ($line as $key => $value) {
                //decode encoded commas
                $record[$header[$key]] = preg_replace($csv_encode,$csv_delimiter2,trim($value));
            }
            if ($record[$header[0]]) {
                // add a new user to the database
                optional_variable($newuser, ""); 

                // add fields to object $user
                foreach ($record as $name => $value) {
                    // check for required values
                    if ($required[$name] and !$value) {
                        error(get_string('missingfield', 'error', $name). " ".
                              get_string('erroronline', 'error', $linenum), 
                              'uploaduser.php');
                    }
                    // password needs to be encrypted
                    else if ($name == "password") {
                        $user->password = md5($value);
                    }
                    // normal entry
                    else {
                        $user->{$name} = addslashes($value);
                    }
                }
                $user->confirmed = 1;
                $user->timemodified = time();
                $linenum++;
                $username = $user->username;
                $addcourse[0] = $user->course1;
                $addcourse[1] = $user->course2;
                $addcourse[2] = $user->course3;
                $addcourse[3] = $user->course4;
                $addcourse[4] = $user->course5;
                $addgroup[0] = $user->group1;
                $addgroup[1] = $user->group2;
                $addgroup[2] = $user->group3;
                $addgroup[3] = $user->group4;
                $addgroup[4] = $user->group5;
                $courses = get_courses("all");
                for ($i=0; $i<5; $i++) {
                    $courseid[$i]=0;
                }
                foreach ($courses as $course) {
                    for ($i=0; $i<5; $i++) {
                        if ($course->shortname == $addcourse[$i]) {
                            $courseid[$i] = $course->id;
                        }
                    }
                }
                if (! $user->id = insert_record("user", $user)) {
                    if (!$user = get_record("user", "username", "changeme")) {   // half finished user from another time
                        //Record not added - probably because user is already registered
                        //In this case, output userid from previous registration
                        //This can be used to obtain a list of userids for existing users
                        if ($user = get_record("user","username",$username)) {
                            notify("$user->id ".get_string('usernotaddedregistered', 'error', $username));
                        } else {
                            notify(get_string('usernotaddederror', 'error', $username));
                        } 
                    }
                } else if ($user->username != "changeme") {
                    notify("$struser: $user->id = $user->username");
                    $numusers++;
                }
                for ($i=0; $i<5; $i++) {
                    if ($addcourse[$i] && !$courseid[$i]) {
                        notify(get_string('unknowncourse', 'error', $addcourse[$i]));
                    }
                }
		for ($i=0; $i<5; $i++) {
                  $groupid[$i] = 0;
                  if ($addgroup[$i]) {
		    if (!$courseid[$i]) {
		      notify(get_string('coursegroupunknown','error',$addgroup[$i]));
		    } else {
		      if ($group = get_record("groups","courseid",$courseid[$i],"name",$addgroup[$i])) {
			$groupid[$i] = $group->id;
		      } else {
			notify(get_string('groupunknown','error',$addgroup[$i]));
		      }
		    }
		  }
		}
                for ($i=0; $i<5; $i++) {
                    if ($courseid[$i]) {
                        if (enrol_student($user->id, $courseid[$i])) {
                            notify('-->'. get_string('enrolledincourse', '', $addcourse[$i]));
                        } else {
                            notify('-->'.get_string('enrolledincoursenot', '', $addcourse[$i]));
                        }
                    }
                }
                for ($i=0; $i<5; $i++) {
		  if ($courseid[$i] && $groupid[$i]) {
		    if (record_exists("user_students","userid",$user->id,"course",$courseid[$i])) {
		      $usergroup = user_group($courseid[$i],$user->id);
                      if ($usergroup) {
			notify('-->' . get_string('groupalready','error',$usergroup->name));
		      } else {
			$group_member->groupid = $groupid[$i];
			$group_member->userid = $user->id;
			$group_member->timeadded = time();
			if (insert_record("groups_members",$group_member)) {
			  notify('-->' . get_string('addedtogroup','',$addgroup[$i]));
			} else {
			  notify('-->' . get_string('addedtogroupnot','',$addgroup[$i]));
			}
		      }
		    } else {
			notify('-->' . get_string('addedtogroupnotenrolled','',$addgroup[$i]));
		    }
		  }
		}

                unset ($user);
            }
        }
        fclose($fp);
        notify("$strusersnew: $numusers");

        echo '<hr />';
    }

/// Print the form
    print_heading_with_help($struploadusers, 'uploadusers');

    $maxuploadsize = get_max_upload_file_size();
    echo '<center>';
    echo '<form method="post" enctype="multipart/form-data" action="uploaduser.php">'.
         $strchoose.':<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'">'.
         '<input type="file" name="userfile" size=30>'.
         '<input type="submit" value="'.$struploadusers.'">'.
         '</form></br>';
    echo '</center>';

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

