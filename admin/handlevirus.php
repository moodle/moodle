<?php
/** This expects the output from a command like
 * clamscan -r --infected --no-summary <files> 2>&1 | php -d error_log=/path/to/log thisfile.php
 * also it's important that the output of clamscan prints the FULL PATH to each infected file, so use absolute paths for area to scan
 * also it should be run as root, or whatever the webserver runs as so that it has the right permissions in the quarantine dir etc.
 * php -d error_log=/path/to/log thisfile.php will override the default error log for php cli, which is stderr, so if you want this script to just print stuff out, use php thisfile.php instead.
 */

die('TODO: MDL-19380');

$fd = fopen('php://stdin','r');
if (!$fd) {
    exit();
}

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once($CFG->libdir.'/eventslib.php');
require_once($CFG->dirroot.'/lib/uploadlib.php'); // contains virus handling stuff.

$site = get_site();

while(!feof($fd)) {
    $entry = fgets($fd);
    if (strlen(trim($entry)) == 0) {
        continue;
    }
    if (!$file = validate_line($entry)) {
        continue;
    }
    $bits = explode('/',$file);
    $a->filename = $bits[count($bits)-1];

    if (!$log = $DB->get_record("log", array("module"=>"upload", "info"=>$file, "action"=>"upload"))) {
        $a->action = clam_handle_infected_file($file,0,false);
        clam_replace_infected_file($file);
        notify_admins_unknown($file,$a);
        continue;
    }
    $action = clam_handle_infected_file($file,$log->userid,true);
    clam_replace_infected_file($file);

    list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $sql = "SELECT c.id, c.fullname $ctxselect FROM {course} c $ctxjoin WHERE c.id = :courseid";
    $course = $DB->get_record_sql($sql, array('courseid' => $log->course));
    context_instance_preload($course);

    $user = $DB->get_record("user", array("id"=>$log->userid));
    $subject = get_string('virusfoundsubject','moodle',format_string($site->fullname));
    $a->date = userdate($log->time);

    $a->action = $action;
    $a->course = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
    $a->user = fullname($user);

    notify_user($user,$subject,$a);
    notify_admins($user,$subject,$a);
}
fclose($fd);


function notify_user($user,$subject,$a) {

    if (!$user) {
        return false;
    }
    $body = get_string('virusfoundlater','moodle',$a);

    $eventdata = new stdClass();
    $eventdata->modulename        = 'moodle';
    $eventdata->userfrom          = get_admin();
    $eventdata->userto            = $user;
    $eventdata->subject           = $subject;
    $eventdata->fullmessage       = $body;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    message_send($eventdata);
}


function notify_admins($user,$subject,$a) {

    $admins = get_admins();

    $body = get_string('virusfoundlateradmin','moodle',$a);
    foreach ($admins as $admin) {
        $eventdata = new stdClass();
        $eventdata->modulename        = 'moodle';
        $eventdata->userfrom          = get_admin();
        $eventdata->userto            = $admin;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $body;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }
}

function notify_admins_unknown($file,$a) {

    global $site;

    $admins = get_admins();
    $subject = get_string('virusfoundsubject','moodle',format_string($site->fullname));
    $body = get_string('virusfoundlateradminnolog','moodle',$a);
    foreach ($admins as $admin) {
        $eventdata = new stdClass();
        $eventdata->modulename        = 'moodle';
        $eventdata->userfrom          = get_admin();
        $eventdata->userto            = $admin;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $body;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }
}

function validate_line($line) {
    global $CFG;
    if (strpos($line,"FOUND") === false) {
        return false;
    }
    $index = strpos($line,":");
    $file = substr($line,0,$index);
    if (!(strpos($file,$CFG->dataroot) === false)) {
        if (!file_exists($file)) {
            return false;
        }
    }
    else {
        if ($file{0} == "/") {
            $file = $CFG->dataroot.$file;
        }
        else {
            $file = $CFG->dataroot."/".$file;
        }
        if (!file_exists($file)) {
            return false;
        }
    }
    // clean up
    $file = preg_replace('/\.\//','/',$file);
    $file = preg_replace('/\/\//','/',$file);
    return $file;
}


