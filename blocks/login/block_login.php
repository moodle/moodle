<?PHP //$Id$

class CourseBlock_login extends MoodleBlock {
    function CourseBlock_login ($course) {
        $this->title = get_string('login');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004081600;
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function get_content () {
        global $USER, $CFG;
        $wwwroot = '';
        $signup = '';

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            // This actually is not so secure ;-), 'cause we're
            // in unencrypted connection...
            $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
        }

        switch ($CFG->auth) {
            // I'm not sure if user can create an account
            // him/her self when using ldap authentication.
            // If true, then there should be a method for it.
            case "email":
                $signup = $wwwroot . '/login/signup.php';
                break;
            case "none":
                // just for the user to see instructions!
                $signup = $wwwroot . '/login/index.php';
                break;
            default:
                $signup = '';
        }

        $username = get_moodle_cookie();
        if (empty($USER->loggedin)) {
            $this->content->text  = "<form name=\"blocklogin\" method=\"post\"";
            $this->content->text .= " action=\"". $wwwroot ."/login/index.php\">\n";
            $this->content->text .= "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\"";
            $this->content->text .= " width=\"100%\" style=\"font-size: small;\">\n";
            $this->content->text .= "<tr>\n<td>". get_string("username") .":</td>\n</tr>\n";
            $this->content->text .= "<tr>\n<td><input type=\"text\" name=\"username\" value=\"";
            $this->content->text .=  $username . "\" /></td>\n</tr>\n";
            $this->content->text .= "<tr>\n<td>". get_string("password") .":</td>\n</tr>\n";
            $this->content->text .= "<tr>\n<td><input type=\"password\" name=\"password\" /></td>\n</tr>\n";
            $this->content->text .= "<tr>\n<td align=\"center\"><input type=\"submit\" value=\"";
            $this->content->text .= get_string("login");
            $this->content->text .= "\" /></td>\n</tr>\n";
            if (!empty($signup)) {
                $this->content->text .= "<tr><td align=\"center\"><a href=\"". $signup ."\">";
                $this->content->text .= get_string('startsignup');
                $this->content->text .= "</a></td></tr>\n";
            }
            $this->content->text .= "</table>\n";
            $this->content->text .= "</form>\n";
        } else {
            $this->content->text = ''; // It's time to dissapear!
                                       // And keep the self test happy by
                                       // passing empty string!
        }
        return $this->content;
    }
}

?>
