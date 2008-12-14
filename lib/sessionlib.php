<?php  //$Id$

/**
 * Class handling all session and cookies related stuff.
 */
class moodle_session {
    private $session;

    function __construct() {
        global $CFG;

        $this->prepare_cookies();
        $this->init_session_storage();

        if (!empty($CFG->usesid) && empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
            $this->sid_start_ob();
        }

        if (!NO_MOODLE_COOKIES) {
            session_name('MoodleSession'.$CFG->sessioncookie);
            session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
            @session_start();
            if (!isset($_SESSION['SESSION'])) {
                $_SESSION['SESSION'] = new object();
                $_SESSION['SESSION']->session_test = random_string(10);
                if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
                    $_SESSION['SESSION']->has_timed_out = true;
                }
                setcookie('MoodleSessionTest'.$CFG->sessioncookie, $_SESSION['SESSION']->session_test, 0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
                $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] = $_SESSION['SESSION']->session_test;
            }
            if (!isset($_SESSION['USER'])) {
                $_SESSION['USER'] = new object();
            }

            if (!isset($_SESSION['USER']->id)) {
                $_SESSION['USER']->id = 0; // to enable proper function of $CFG->notloggedinroleid hack
                if (isset($CFG->mnet_localhost_id)) {
                    $_SESSION['USER']->mnethostid = $CFG->mnet_localhost_id;
                }
            }

            $this->session = null;

        } else {
            $this->session = new object();
        }
    }

    /**
     * Verify session, this detects problems with "switched" sessions
     * or multiple different wwwroot used at the same time.
     */
    public function session_verify() {
        global $CFG;

    /// disable checks when working in cookieless mode
        if (empty($CFG->usesid) || !empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
            if ($this->session != NULL) {
                if (empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
                    $this->report_session_error();
                } else if (isset($this->session->session_test) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] != $this->session->session_test) {
                    $this->report_session_error();
                }
            }
        }
    }

    /**
     * Report serious problem detected in suer session
     */
    function report_session_error() {
        global $CFG, $FULLME;

        if (empty($CFG->lang)) {
            $CFG->lang = "en";
        }
        // Set up default theme and locale
        theme_setup();
        moodle_setlocale();

        //clear session cookies
        setcookie('MoodleSession'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);

        //increment database error counters
        if (isset($CFG->session_error_counter)) {
            set_config('session_error_counter', 1 + $CFG->session_error_counter);
        } else {
            set_config('session_error_counter', 1);
        }
        redirect($FULLME, get_string('sessionerroruser2', 'error'), 5);
    }

    /**
     * Terminates active moodle session
     */
    public function terminate() {
        global $CFG, $SESSION, $USER;

        // Initialize variable to pass-by-reference to headers_sent(&$file, &$line)
        $file = null;
        $line = null;
        if (headers_sent($file, $line)) {
            error_log('MoodleSessionTest cookie could not be set in moodlelib.php:'.__LINE__);
            error_log('Headers were already sent in file: '.$file.' on line '.$line);
        } else {
            setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        }

        $this->session = new object();
        $_SESSION      = array();

        $USER     = new object();
        $USER->id = 0;
        if (isset($CFG->mnet_localhost_id)) {
            $USER->mnethostid = $CFG->mnet_localhost_id;
        }

        @session_write_close();
    }


    public function __set($name, $value) {
        if (!is_null($this->session)) {
            $this->session->{$name} = $value;
        } else {
            $_SESSION['SESSION']->{$name} = $value;
        }
    }

    public function &__get($name) { // this is a weird hack for this stupid bug http://bugs.php.net/bug.php?id=39449
        if (!is_null($this->session)) {
            return $this->session->{$name};
        } else {
            return $_SESSION['SESSION']->{$name};
        }
    }

    public function __isset($name) {
        if (!is_null($this->session)) {
            return isset($this->session->{$name});
        } else {
            return isset($_SESSION['SESSION']->{$name});
        }
    }

    public function __unset($name) {
        if (!is_null($this->session)) {
            unset($this->session->{$name});
        } else {
            unset($_SESSION['SESSION']->{$name});
        }
    }

    /**
     * Prepare cookies and varions system settings
     */
    private function prepare_cookies() {
        global $CFG, $nomoodlecookie;

        if (!defined('NO_MOODLE_COOKIES')) {
            if (isset($nomoodlecookie)) {
                // backwards compatibility only
                define('NO_MOODLE_COOKIES', $nomoodlecookie);
                unset($nomoodlecookie);
            } else {
                define('NO_MOODLE_COOKIES', false);
            }
        }

        if (!isset($CFG->cookiesecure) or strpos($CFG->wwwroot, 'https://') !== 0) {
            $CFG->cookiesecure = 0;
        }

        if (!isset($CFG->cookiehttponly)) {
            $CFG->cookiehttponly = 0;
        }

    /// Set sessioncookie and sessioncookiepath variable if it isn't already
        if (!isset($CFG->sessioncookie)) {
            $CFG->sessioncookie = '';
        }
        if (!isset($CFG->sessioncookiedomain)) {
            $CFG->sessioncookiedomain = '';
        }
        if (!isset($CFG->sessioncookiepath)) {
            $CFG->sessioncookiepath = '/';
        }

        //discard session ID from POST, GET and globals to tighten security,
        //this session fixation prevention can not be used in cookieless mode
        if (empty($CFG->usesid)) {
            unset(${'MoodleSession'.$CFG->sessioncookie});
            unset($_GET['MoodleSession'.$CFG->sessioncookie]);
            unset($_POST['MoodleSession'.$CFG->sessioncookie]);
        }
        //compatibility hack for Moodle Cron, cookies not deleted, but set to "deleted" - should not be needed with NO_MOODLE_COOKIES in cron.php now
        if (!empty($_COOKIE['MoodleSession'.$CFG->sessioncookie]) && $_COOKIE['MoodleSession'.$CFG->sessioncookie] == "deleted") {
            unset($_COOKIE['MoodleSession'.$CFG->sessioncookie]);
        }
        if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] == "deleted") {
            unset($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]);
        }
    }

    /**
     * Inits session storage.
     */
    private function init_session_storage() {
        global $CFG;

    /// Set up session handling
        if(empty($CFG->respectsessionsettings)) {
            if (true) {   /// File-based sessions
                // Some distros disable GC by setting probability to 0
                // overriding the PHP default of 1
                // (gc_probability is divided by gc_divisor, which defaults to 1000)
                if (ini_get('session.gc_probability') == 0) {
                    ini_set('session.gc_probability', 1);
                }

                if (!empty($CFG->sessiontimeout)) {
                    ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);
                }

                if (!file_exists($CFG->dataroot .'/sessions')) {
                    make_upload_directory('sessions');
                }
                ini_set('session.save_path', $CFG->dataroot .'/sessions');

            } else {                         /// Database sessions
                // TODO: implement proper database session storage
            }
        }
    }

    /**
     * Sets a moodle cookie with a weakly encrypted string
     *
     * @uses $CFG
     * @uses DAYSECS
     * @uses HOURSECS
     * @param string $thing The string to encrypt and place in a cookie
     */
    public static function set_moodle_cookie($thing) {
        global $CFG;

        if ($thing == 'guest') {  // Ignore guest account
            return;
        }

        $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

        $days = 60;
        $seconds = DAYSECS*$days;

        // no need to set secure or http cookie only here - it is not secret
        setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain);
        setcookie($cookiename, rc4encrypt($thing), time()+$seconds, $CFG->sessioncookiepath, $CFG->sessioncookiedomain);
    }

    /**
     * Gets a moodle cookie with a weakly encrypted string
     *
     * @uses $CFG
     * @return string
     */
    public static function get_moodle_cookie() {
        global $CFG;

        $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

        if (empty($_COOKIE[$cookiename])) {
            return '';
        } else {
            $thing = rc4decrypt($_COOKIE[$cookiename]);
            return ($thing == 'guest') ? '': $thing;  // Ignore guest account
        }
    }

    /**
    * Enable cookieless sessions by including $CFG->usesid=true;
    * in config.php.
    * Based on code from php manual by Richard at postamble.co.uk
    * Attempts to use cookies if cookies not present then uses session ids attached to all urls and forms to pass session id from page to page.
    * If site is open to google, google is given guest access as usual and there are no sessions. No session ids will be attached to urls for googlebot.
    * This doesn't require trans_sid to be turned on but this is recommended for better performance
    * you should put :
    * session.use_trans_sid = 1
    * in your php.ini file and make sure that you don't have a line like this in your php.ini
    * session.use_only_cookies = 1
    * @author Richard at postamble.co.uk and Jamie Pratt
    * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
    */
    /**
    * You won't call this function directly. This function is used to process
    * text buffered by php in an output buffer. All output is run through this function
    * before it is ouput.
    * @param string $buffer is the output sent from php
    * @return string the output sent to the browser
    */
    public static function sid_ob_rewrite($buffer){
        $replacements = array(
            '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")([^"]*)(")/i',
            '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')([^\']*)(\')/i');

        $buffer = preg_replace_callback($replacements, array('moodle_session', 'sid_rewrite_link_tag'), $buffer);
        $buffer = preg_replace('/<form\s[^>]*>/i',
            '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);

          return $buffer;
    }
    /**
    * You won't call this function directly. This function is used to process
    * text buffered by php in an output buffer. All output is run through this function
    * before it is ouput.
    * This function only processes absolute urls, it is used when we decide that
    * php is processing other urls itself but needs some help with internal absolute urls still.
    * @param string $buffer is the output sent from php
    * @return string the output sent to the browser
    */
    public static function sid_ob_rewrite_absolute($buffer){
        $replacements = array(
            '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")((?:http|https)[^"]*)(")/i',
            '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')((?:http|https)[^\']*)(\')/i');

        $buffer = preg_replace_callback($replacements, array('moodle_session', 'sid_rewrite_link_tag'), $buffer);
        $buffer = preg_replace('/<form\s[^>]*>/i',
            '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);
        return $buffer;
    }

    /**
    * A function to process link, a and script tags found
    * by preg_replace_callback in {@link sid_ob_rewrite($buffer)}.
    */
    public static function sid_rewrite_link_tag($matches){
        $url = $matches[4];
        $url = moodle_session::sid_process_url($url);
        return $matches[1].$url.$matches[5];
    }

    /**
    * You can call this function directly. This function is used to process
    * urls to add a moodle session id to the url for internal links.
    * @param string $url is a url
    * @return string the processed url
    */
    public static function sid_process_url($url) {
        global $CFG;

        if ((preg_match('/^(http|https):/i', $url)) // absolute url
            &&  ((stripos($url, $CFG->wwwroot)!==0) && stripos($url, $CFG->httpswwwroot)!==0)) { // and not local one
            return $url; //don't attach sessid to non local urls
        }
        if ($url[0]=='#' || (stripos($url, 'javascript:')===0)) {
            return $url; //don't attach sessid to anchors
        }
        if (strpos($url, session_name())!==FALSE) {
            return $url; //don't attach sessid to url that already has one sessid
        }
        if (strpos($url, "?")===FALSE) {
            $append = "?".strip_tags(session_name() . '=' . session_id());
        }    else {
            $append = "&amp;".strip_tags(session_name() . '=' . session_id());
        }
        //put sessid before any anchor
        $p = strpos($url, "#");
        if ($p!==FALSE){
            $anch = substr($url, $p);
            $url = substr($url, 0, $p).$append.$anch ;
        } else  {
            $url .= $append ;
        }
        return $url;
    }

    /**
    * Call this function before there has been any output to the browser to
    * buffer output and add session ids to all internal links.
    */
    public static function sid_start_ob(){
        global $CFG;
        //don't attach sess id for bots

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            if (!empty($CFG->opentogoogle)) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false) {
                    @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                    $CFG->usesid=false;
                    return;
                }
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false) {
                    @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                    $CFG->usesid=false;
                    return;
                }
            }
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false) {
                @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                $CFG->usesid=false;
                return;
            }
        }

        @ini_set('session.use_trans_sid', '1'); // try and turn on trans_sid

        if (ini_get('session.use_trans_sid') != 0) {
            // use trans sid as its available
            ini_set('url_rewriter.tags', 'a=href,area=href,script=src,link=href,frame=src,form=fakeentry');
            ob_start(array('moodle_session', 'sid_ob_rewrite_absolute'));
        } else {
            //rewrite all links ourselves
            ob_start(array('moodle_session', 'sid_ob_rewrite'));
        }
    }
}
