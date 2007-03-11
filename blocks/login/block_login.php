<?PHP //$Id$

class block_login extends block_base {
    function init() {
        $this->title = get_string('login');
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

        if ($CFG->auth == 'email') {
            $signup = $wwwroot . '/login/signup.php';
            $forgot = $wwwroot . '/login/forgot_password.php';
        } else {
            if (!empty($CFG->{'auth_'.$CFG->auth.'_stdchangepassword'})
                || $CFG->changepassword 
                || is_internal_auth() ) {
                if (is_internal_auth() || !empty($CFG->{'auth_'.$CFG->auth.'_stdchangepassword'})) {
                    $forgot =  $wwwroot . '/login/forgot_password.php';
                }
                else {
                    $forgot = $CFG->changepassword;
                }
            }
        }

        $username = get_moodle_cookie() === 'nobody' ? '' : get_moodle_cookie();

        $this->content->footer = '';
        $this->content->text = '';

        if (empty($USER->loggedin) or isguest()) {   // Show the block
            $this->content->text .= '<form class="loginform" name="login" method="post" action="'.$wwwroot.'/login/index.php">';
            $this->content->text .= '<table align="center" cellpadding="2" cellspacing="0" class="logintable">';

            $this->content->text .= '<tr><td class="c0 r0" align="right">'.get_string('username').':</td>';
            $this->content->text .= '<td class="c1 r0"><input type="text" name="username" size="10" value="'.s($username).'" /></td></tr>';

            $this->content->text .= '<tr><td class="c0 r1" align="right">'.get_string('password').':</td>';
            $this->content->text .= '<td class="c1 r1"><input type="password" name="password" size="10" value="" /></td></tr>';

            $this->content->text .= '<tr><td class="c0 r2">&nbsp;</td><td class="c1 r2" align="left"><input type="submit" value="'.get_string('login').'" /></td></tr>';

            $this->content->text .= '</table></form>';

            if (!empty($signup)) {
                $this->content->footer .= '<div><a href="'.$signup.'">'.get_string('startsignup').'</a></div>';
            }
            if (!empty($forgot)) {
                $this->content->footer .= '<div><a href="'.$forgot.'">'.get_string('forgotaccount').'</a></div>';
            }
        }

        return $this->content;
    }
}

?>
