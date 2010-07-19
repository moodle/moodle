<?PHP
    //This file returns the required rss feeds
    //The URL format MUST include:
    //    context: the context id
    //    user:   the user id
    //    name:   the name of the module (forum...)
    //    id:     the id (instance) of the module (forumid...)
    //If the course has a password or it doesn't
    //allow guest access then the user field is
    //required to see that the user is enrolled
    //in the course, else no check is performed.
    //This allows to limit a bit the rss access
    //to correct users. It isn't unbreakable,
    //obviously, but its the best I've thought!!

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);//comment this out to see any error messages during RSS generation

// session not used here
define('NO_MOODLE_COOKIES', true);

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/rsslib.php');

//Check RSS feeds are enabled
if (empty($CFG->enablerssfeeds)) {
    debugging('DISABLED (admin variables)');
    rss_not_found();
}

$lifetime = 3600;  // Seconds for files to remain in caches - 1 hour
$filename   = 'rss.xml';

// this is a big one big hack - NO_MOODLE_COOKIES is not compatible with capabilities MDL-7243
// it should be replaced once we get to codes in urls

$relativepath = get_file_argument();
if (!$relativepath) {
    rss_not_found();
}

// extract relative path components
$args = explode('/', trim($relativepath, '/'));
if (count($args) < 5) {
    rss_not_found();
}

$contextid   = (int)$args[0];
$token  = $args[1];
$componentname = clean_param($args[2], PARAM_FILE);
$instance   = $args[3];

$userid = rss_get_userid_from_token($token);
if (!$userid) {
    rss_not_authenticated();
}
$user = get_complete_user_data('id', $userid);
session_set_user($user);

//Set context
$context = get_context_instance_by_id($contextid);
if (!$context) {
    rss_not_found();
}
$PAGE->set_context($context);

//Get course from context
//TODO: note that in the case of the hub rss feed, the feed is not related to a course context,
//it is more a "site" context. The Hub RSS bypass the following line using context id = 2
$coursecontext = get_course_context($context);
$course = $DB->get_record('course', array('id' => $coursecontext->instanceid), '*', MUST_EXIST);

//this will store the path to the cached rss feed contents
$pathname = null;

$componentdir = get_component_directory($componentname);
list($type, $plugin) = normalize_component($componentname);

if (file_exists($componentdir)) {
    require_once("$componentdir/rsslib.php");
    $functionname = $plugin.'_rss_get_feed';

    if (function_exists($functionname)) {

        if ($componentname=='blog') {

            $blogid = (int) $args[4];  // could be groupid / courseid  / userid  depending on $instance
            if ($args[5] != 'rss.xml') {
                $tagid = (int) $args[5];
            } else {
                $tagid = 0;
            }

            try {
                require_login($course, false, NULL, false, true);
            } catch (Exception $e) {
                rss_not_found();
            }
            $pathname = $functionname($instance, $blogid, $tagid);
        } else if ($componentname=='local_hub') {
            
            $pathname = $functionname($args);
        } else {

            $instance = (int)$instance;

            try {
                $cm = get_coursemodule_from_instance($plugin, $instance, 0, false, MUST_EXIST);
                require_login($course, false, $cm, false, true);
            } catch (Exception $e) {
                rss_not_found();
            }

            $pathname = $functionname($context, $cm, $instance, $args);
        }
    }
}

//Check that file exists
if (empty($pathname) || !file_exists($pathname)) {
    rss_not_found();
}

//rss_update_token_last_access($USER->id);

//Send it to user!
send_file($pathname, $filename, $lifetime);

function rss_not_found() {
    /// error, send some XML with error message
    global $lifetime, $filename;
    send_file(rss_geterrorxmlfile(), $filename, $lifetime, false, true);
}

function rss_not_authenticated() {
    global $lifetime, $filename;
    send_file(rss_geterrorxmlfile('rsserrorauth'), $filename, $lifetime, false, true);
}

