<?PHP // $Id$

//Bulk user registration script from a comma separated file
//Returns list of users with their user ids

require_once("../config.php");
require_once("../lib/countries.php");
require_once("lib.php");


require_login();

if (!isadmin()) {
  error("You must be an administrator to edit users this way.");
}

$notify = FALSE;

// import users
if ($filename = valid_uploaded_file($_FILES['userfile'])) {
  optional_variable($numusers, 0);
  
  //Fix mac/dos newlines
  $text = my_file_get_contents($filename);
  $text = preg_replace('!\r\n?!',"\n",$text);
  $fp = fopen($filename, "w");
  fwrite($fp,$text);
  fclose($fp);

  $fp = fopen($filename, "r");

  // make arrays of valid fields for error checking
  $required = array("username" => 1, "password" => 1, 
		    "firstname" => 1, "lastname" => 1,
		    "email" => 1);
  $optionalDefaults = array("institution" => 1, "department" => 1, 
			    "city" => 1, "country" => 1,
			    "lang" => 1, "timezone" => 1);
  $optional = array("idnumber" => 1, "icq" => 1, 
		    "phone1" => 1, "phone2" => 1,
		    "address" => 1, "url" => 1,
		    "description" => 1, "mailformat" => 1, 
		    "htmleditor" => 1, "autosubscribe" => 1,
		    "idnumber" => 1, "icq" => 1, 
		    "course1" => 1, "course2" => 1,
		    "course3" => 1, "course4" => 1, 
		    "course5" => 1);

  // --- get header (field names) ---
  $header = split("\,", fgets($fp));
  // check for valid field names
  foreach ($header as $i => $h) {
    $h = trim($h); $header[$i] = $h; // remove whitespace
    if (!($required[$h] or $optionalDefaults[$h] or $optional[$h])) {
      error("\"$h\" is not a valid field name.");
    }
    if ($required[$h]) {
      $required[$h] = 2;
    }
  }
  // check for required fields
  foreach ($required as $key => $value) {
    if ($value < 2) {
      error("\"$key\" is a required field.");
    }
  }
  $linenum = 2; // since header is line 1

  while (!feof ($fp)) {
    //Note: commas within a field should be encoded as &#44
    //Last field, courseid, is optional. If present it should be the Moodle
    //course id number for the course in which student should be initially enroled
    $line = split("\,", fgets($fp));
    foreach ($line as $key => $value) {
      $record[$header[$key]] = trim($value);
    }
    if ($record[$header[0]]) {
      // add a new user to the database
      optional_variable($newuser, ""); 

      // add fields to object $user
      foreach ($record as $name => $value) {
	// check for required values
	if ($required[$name] and !$value) {
	  error("Missing \"$name\" on line $linenum.");
	}
	// password needs to be encrypted
	else if ($name == "password") {
	  $user->password = md5($value);
	}
	// normal entry
	else {
	  $user->{$name} = $value;
	}
      }
      $user->confirmed = 1;
      $user->timemodified = time();
      $linenum++;

      if (! $user->id = insert_record("user", $user)) {
        if (!$user = get_record("user", "username", "changeme")) {   // half finished user from another time
          //Record not added - probably because user is already registered
          //In this case, output userid from previous registration
          //This can be used to obtain a list of userids for existing users
          $error_uid = -1;
          if ($user = get_record("user","username",$username)) {
            $error_uid = $user->id;
          }
          $notifytext .= $error_uid . "," . $username . ",FAILED";
        }
      } elseif ($user->username != "changeme") {
        $notifytext .= $user->id . "," . $user->username . ",";
        $numusers++;
      }
      if ($courseid) {
        if (enrol_student($user->id, $courseid)) {
	  $notifytext .= ",enroled in course $courseid<br>\n";
      	} else {
	  $notifytext .= ",error: enrolment in course $courseid failed<br>\n";
      	}
      } else {$notifytext .= "<br>\n";}
      unset ($user);
    }
  }
  $notify = true;
  fclose($fp);
}

// print page
if (! $site = get_site()) {
  error("Could not find site-level course");
}
$streditmyprofile = get_string("editmyprofile");
$strnewuser = get_string("newuser");
$userfullname = $strnewuser;
$straddnewuser = get_string("importuser");
$stradministration = get_string("administration");

print_header("$site->shortname: $straddnewuser", "$site->fullname",
             "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> ->
                      $straddnewuser", "");
if ($notify) {
  notify("$notifytext <br>Added $numusers New Users");
}
// output form
echo "<form METHOD=\"post\" ENCTYPE=\"multipart/form-data\" action=\"uploaduser.php\">\n".
"<br><input type=\"submit\" value=\"Import Users\">".
"<INPUT type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"get_max_upload_file_size()\">\n".
"<input type=\"file\" name=\"userfile\" size=30>\n".
"</form></br>";

print_footer($course);

function my_file_get_contents($filename, $use_include_path = 0) {
  $data = ""; // just to be safe. Dunno, if this is really needed
  $file = @fopen($filename, "rb", $use_include_path);
  if ($file) {
    while (!feof($file)) $data .= fread($file, 1024);
    fclose($file);
  }
  return $data;
}

?>
