<?php  // $Id$

    ob_start(); //for whitespace test
    require_once('../config.php');

    // extra whitespace test - intentionally breaks cookieless mode
    $extraws = '';
    while (true) {
        $extraws .= ob_get_contents();
        if (!@ob_end_clean()) {
            break;
        }
    }

    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('healthcenter');

    define('SEVERITY_NOTICE',      'notice');
    define('SEVERITY_ANNOYANCE',   'annoyance');
    define('SEVERITY_SIGNIFICANT', 'significant');
    define('SEVERITY_CRITICAL',    'critical');

    $solution = optional_param('solution', 0, PARAM_SAFEDIR); //in fact it is class name alhanumeric and _

    require_login();
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID));

    $site = get_site();

    admin_externalpage_print_header();

echo <<<STYLES
<style type="text/css">
div#healthnoproblemsfound {
    width: 60%;
    margin: auto;
    padding: 1em;
    border: 1px black solid;
    -moz-border-radius: 6px;
}
dl.healthissues {
    width: 60%;
    margin: auto;
}
dl.critical dt, dl.critical dd {
    background-color: #a71501;
}
dl.significant dt, dl.significant dd {
    background-color: #d36707;
}
dl.annoyance dt, dl.annoyance dd {
    background-color: #dba707;
}
dl.notice dt, dl.notice dd {
    background-color: #e5db36;
}
dt.solution, dd.solution, div#healthnoproblemsfound {
    background-color: #5BB83E !important;
}
dl.healthissues dt, dl.healthissues dd {
    margin: 0px;
    padding: 1em;
    border: 1px black solid;
}
dl.healthissues dt {
    font-weight: bold;
    border-bottom: none;
    padding-bottom: 0.5em;
}
dl.healthissues dd {
    border-top: none;
    padding-top: 0.5em;
    margin-bottom: 10px;
}
dl.healthissues dd form {
    margin-top: 0.5em;
    text-align: right;
}
form#healthformreturn {
    text-align: center;
    margin: 2em;
}
dd.solution p {
    padding: 0px;
    margin: 1em 0px;
}
dd.solution li {
    margin-top: 1em;
}

</style>
STYLES;

    if(strpos($solution, 'problem_') === 0 && class_exists($solution)) {
        health_print_solution($solution);
    }
    else {
        health_find_problems();
    }


    admin_externalpage_print_footer();


function health_find_problems() {

    print_heading(get_string('healthcenter'));

    $issues   = array(
        SEVERITY_CRITICAL    => array(),
        SEVERITY_SIGNIFICANT => array(),
        SEVERITY_ANNOYANCE   => array(),
        SEVERITY_NOTICE      => array(),
    );
    $problems = 0;

    for($i = 1; $i < 1000000; ++$i) {
        $classname = sprintf('problem_%06d', $i);
        if(!class_exists($classname)) {
            break;
        }
        $problem = new $classname;
        if($problem->exists()) {
            $severity = $problem->severity();
            $issues[$severity][$classname] = array(
                'severity'    => $severity,
                'description' => $problem->description(),
                'title'       => $problem->title()
            );
            ++$problems;
        }
        unset($problem);
    }

    if($problems == 0) {
        echo '<div id="healthnoproblemsfound">';
        echo get_string('healthnoproblemsfound');
        echo '</div>';
    }
    else {
        print_heading(get_string('healthproblemsdetected'));
        $severities = array(SEVERITY_CRITICAL, SEVERITY_SIGNIFICANT, SEVERITY_ANNOYANCE, SEVERITY_NOTICE);
        foreach($severities as $severity) {
            if(!empty($issues[$severity])) {
                echo '<dl class="healthissues '.$severity.'">';
                foreach($issues[$severity] as $classname => $data) {
                    echo '<dt id="'.$classname.'">'.$data['title'].'</dt>';
                    echo '<dd>'.$data['description'];
                    echo '<form action="health.php#solution" method="get">';
                    echo '<input type="hidden" name="solution" value="'.$classname.'" /><input type="submit" value="'.get_string('viewsolution').'" />';
                    echo '</form></dd>';
                }
                echo '</dl>';
            }
        }
    }
}

function health_print_solution($classname) {
    $problem = new $classname;
    $data = array(
        'title'       => $problem->title(),
        'severity'    => $problem->severity(),
        'description' => $problem->description(),
        'solution'    => $problem->solution()
    );

    print_heading(get_string('healthcenter'));
    print_heading(get_string('healthproblemsolution'));
    echo '<dl class="healthissues '.$data['severity'].'">';
    echo '<dt>'.$data['title'].'</dt>';
    echo '<dd>'.$data['description'].'</dd>';
    echo '<dt id="solution" class="solution">'.get_string('healthsolution').'</dt>';
    echo '<dd class="solution">'.$data['solution'].'</dd></dl>';
    echo '<form id="healthformreturn" action="health.php#'.$classname.'" method="get">';
    echo '<input type="submit" value="'.get_string('healthreturntomain').'" />';
    echo '</form>';
}

class problem_base {
    function exists() {
        return false;
    }
    function title() {
        return '???';
    }
    function severity() {
        return SEVERITY_NOTICE;
    }
    function description() {
        return '';
    }
    function solution() {
        return '';
    }
}

class problem_000001 extends problem_base {
    function title() {
        return 'Invalid value for $CFG->dirroot';
    }
    function exists() {
        global $CFG;
        $dirroot = dirname(realpath('../index.php'));
        if (!empty($dirroot) && $dirroot != $CFG->dirroot) {
            return true;
        }
        return false;
    }
    function severity() {
        return SEVERITY_CRITICAL;
    }
    function description() {
        global $CFG;
        return 'Your <strong>config.php</strong> file contains the setting <strong>$CFG-&gt;dirroot = "'.$CFG->dirroot.'"</strong>, which is incorrect. Unless you correct this problem, Moodle will not function correctly, if at all.';
    }
    function solution() {
        global $CFG;
        $dirroot = dirname(realpath('../index.php'));
        return 'You need to edit your <strong>config.php</strong> file. Find the line which reads <pre>$CFG->dirroot = \''.$CFG->dirroot.'\';</pre> and change it to read <pre>$CFG->dirroot = \''.$dirroot.'\'</pre>';
    }
}

class problem_000002 extends problem_base {
    function title() {
        return 'Extra characters at the end of config.php or other library function';
    }
    function exists() {
        global $extraws;

        if($extraws === '') {
            return false;
        }
        return true;
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        return 'Your Moodle configuration file config.php or another library file, contains some characters after the closing PHP tag (?>). This causes Moodle to exhibit several kinds of problems (such as broken downloaded files) and must be fixed.';
    }
    function solution() {
        global $CFG;
        return 'You need to edit <strong>'.$CFG->dirroot.'/config.php</strong> and remove all characters (including spaces and returns) after the ending ?> tag. These two characters should be the very last in that file. The extra trailing whitespace may be also present in other PHP files that are included from lib/setup.php.';
    }
}

class problem_000003 extends problem_base {
    function title() {
        return '$CFG->dataroot does not exist or does not have write permissions';
    }
    function exists() {
        global $CFG;
        if(!is_dir($CFG->dataroot) || !is_writable($CFG->dataroot)) {
            return true;
        }
        return false;
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        global $CFG;
        return 'Your <strong>config.php</strong> says that your "data root" directory is <strong>'.$CFG->dataroot.'</strong>. However, this directory either does not exist or cannot be written to by Moodle. This means that a variety of problems will be present, such as users not being able to log in and not being able to upload any files. It is imperative that you address this problem for Moodle to work correctly.';
    }
    function solution() {
        global $CFG;
        return 'First of all, make sure that the directory <strong>'.$CFG->dataroot.'</strong> exists. If the directory does exist, then you must make sure that Moodle is able to write to it. Contact your web server administrator and request that he gives write permissions for that directory to the user that the web server process is running as.';
    }
}

class problem_000004 extends problem_base {
    function title() {
        return 'cron.php is not set up to run automatically';
    }
    function exists() {
        global $CFG;
        $lastcron = get_field_sql('SELECT max(lastcron) FROM '.$CFG->prefix.'modules');
        return (time() - $lastcron > 3600 * 24);
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        return 'The cron.php mainenance script has not been run in the past 24 hours. This probably means that your server is not configured to automatically run this script in regular time intervals. If this is the case, then Moodle will mostly work as it should but some operations (notably sending email to users) will not be carried out at all.';
    }
    function solution() {
        global $CFG;
        return 'For detailed instructions on how to enable cron, see <a href="'.$CFG->wwwroot.'/doc/?file=install.html#cron">this section</a> of the installation manual.';
    }
}

class problem_000005 extends problem_base {
    function title() {
        return 'PHP: session.auto_start is enabled';
    }
    function exists() {
        return ini_get_bool('session.auto_start');
    }
    function severity() {
        return SEVERITY_CRITICAL;
    }
    function description() {
        return 'Your PHP configuration includes an enabled setting, session.auto_start, that <strong>must be disabled</strong> in order for Moodle to work correctly. Notable symptoms arising from this misconfiguration include fatal errors and/or blank pages when trying to log in.';
    }
    function solution() {
        global $CFG;
        return '<p>There are two ways you can solve this problem:</p><ol><li>If you have access to your main <strong>php.ini</strong> file, then find the line that looks like this: <pre>session.auto_start = 1</pre> and change it to <pre>session.auto_start = 0</pre> and then restart your web server. Be warned that this, as any other PHP setting change, might affect other web applications running on the server.</li><li>Finally, you may be able to change this setting just for your site by creating or editing the file <strong>'.$CFG->dirroot.'/.htaccess</strong> to contain this line: <pre>php_value session.auto_start "0"</pre></li></ol>';
    }
}

class problem_000006 extends problem_base {
    function title() {
        return 'PHP: magic_quotes_runtime is enabled';
    }
    function exists() {
        return (ini_get_bool('magic_quotes_runtime'));
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        return 'Your PHP configuration includes an enabled setting, magic_quotes_runtime, that <strong>must be disabled</strong> in order for Moodle to work correctly. Notable symptoms arising from this misconfiguration include strange display errors whenever a text field that includes single or double quotes is processed.';
    }
    function solution() {
        global $CFG;
        return '<p>There are two ways you can solve this problem:</p><ol><li>If you have access to your main <strong>php.ini</strong> file, then find the line that looks like this: <pre>magic_quotes_runtime = On</pre> and change it to <pre>magic_quotes_runtime = Off</pre> and then restart your web server. Be warned that this, as any other PHP setting change, might affect other web applications running on the server.</li><li>Finally, you may be able to change this setting just for your site by creating or editing the file <strong>'.$CFG->dirroot.'/.htaccess</strong> to contain this line: <pre>php_value magic_quotes_runtime "Off"</pre></li></ol>';
    }
}

class problem_000007 extends problem_base {
    function title() {
        return 'PHP: file_uploads is disabled';
    }
    function exists() {
        return !ini_get_bool('file_uploads');
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        return 'Your PHP configuration includes a disabled setting, file_uploads, that <strong>must be enabled</strong> to let Moodle offer its full functionality. Until this setting is enabled, it will not be possible to upload any files into Moodle. This includes, for example, course content and user pictures.';
    }
    function solution() {
        global $CFG;
        return '<p>There are two ways you can solve this problem:</p><ol><li>If you have access to your main <strong>php.ini</strong> file, then find the line that looks like this: <pre>file_uploads = Off</pre> and change it to <pre>file_uploads = On</pre> and then restart your web server. Be warned that this, as any other PHP setting change, might affect other web applications running on the server.</li><li>Finally, you may be able to change this setting just for your site by creating or editing the file <strong>'.$CFG->dirroot.'/.htaccess</strong> to contain this line: <pre>php_value file_uploads "On"</pre></li></ol>';
    }
}

class problem_000008 extends problem_base {
    function title() {
        return 'PHP: memory_limit cannot be controlled by Moodle';
    }
    function exists() {
        $oldmemlimit = @ini_get('memory_limit');
        if(empty($oldmemlimit)) {
            // PHP not compiled with memory limits, this means that it's
            // probably limited to 8M or in case of Windows not at all.
            // We can ignore it for now - there is not much to test anyway
            // TODO: add manual test that fills memory??
            return false;
        }
        $oldmemlimit = get_real_size($oldmemlimit);
        //now lets change the memory limit to something unique below 128M==134217728
        @ini_set('memory_limit', 134217720);
        $testmemlimit = get_real_size(@ini_get('memory_limit'));
        //verify the change had any effect at all
        if ($oldmemlimit == $testmemlimit) {
            //memory limit can not be changed - is it big enough then?
            if ($oldmemlimit < get_real_size('128M')) {
                return true;
            } else {
                return false;
            }
        }
        @ini_set('memory_limit', $oldmemlimit);
        return false;
    }
    function severity() {
        return SEVERITY_NOTICE;
    }
    function description() {
        return 'The settings for PHP on your server do not allow a script to request more memory during its execution. '.
               'This means that there is a hard limit of '.@ini_get('memory_limit').' for each script. '.
               'It is possible that certain operations within Moodle will require more than this amount in order '.
               'to complete successfully, especially if there are lots of data to be processed.';
    }
    function solution() {
        return 'It is recommended that you contact your web server administrator to address this issue.';
    }
}

class problem_000009 extends problem_base {
    function title() {
        return 'SQL: using account without password';
    }
    function exists() {
        global $CFG;
        return empty($CFG->dbpass);
    }
    function severity() {
        return SEVERITY_CRITICAL;
    }
    function description() {
        global $CFG;
        return 'The user account your are connecting to the database server with is set up without a password. This is a very big security risk and is only somewhat lessened if your database is configured to not accept connections from any hosts other than the server Moodle is running on. Unless you use a strong password to connect to the database, you risk unauthorized access to and manipulation of your data.'.($CFG->dbuser != 'root'?'':' <strong>This is especially alarming because such access to the database would be as the superuser (root)!</strong>');
    }
    function solution() {
        global $CFG;
        return 'You should change the password of the user <strong>'.$CFG->dbuser.'</strong> both in your database and in your Moodle <strong>config.php</strong> immediately!'.($CFG->dbuser != 'root'?'':' It would also be a good idea to change the user account from root to something else, because this would lessen the impact in the event that your database is compromised anyway.');
    }
}

class problem_000010 extends problem_base {
    function title() {
        return 'Uploaded files: slasharguments disabled or not working';
    }
    function exists() {
        if (!$this->is_enabled()) {
            return true;
        }
        if ($this->status() < 1) {
            return true;
        }
        return false;
    }
    function severity() {
        if ($this->is_enabled() and $this->status() == 0) {
            return SEVERITY_SIGNIFICANT;
        } else {
            return SEVERITY_ANNOYANCE;
        }
    }
    function description() {
        global $CFG;
        $desc = 'Slasharguments are needed for relative linking in uploaded resources:<ul>';
        if (!$this->is_enabled()) {
            $desc .= '<li>slasharguments are <strong>disabled</strong> in Moodle configuration</li>';
        } else {
            $desc .= '<li>slasharguments are enabled in Moodle configuration</li>';
        }
        if ($this->status() == -1) {
            $desc .= '<li>can not run automatic test, you can verify it <a href="'.$CFG->wwwroot.'/file.php/testslasharguments" target="_blank">here</a> manually</li>';
        } else if ($this->status() == 0) {
            $desc .= '<li>slashargument test <strong>failed</strong>, please check server configuration</li>';
        } else {
            $desc .= '<li>slashargument test passed</li>';
        }
        $desc .= '</ul>';
        return $desc;
    }
    function solution() {
        global $CFG;
        $enabled = $this->is_enabled();
        $status = $this->status();
        $solution = '';
        if ($enabled and ($status == 0)) {
            $solution .= 'Slasharguments are enabled, but the test failed. Please disable slasharguments in Moodle configuration or fix the server configuration.<hr />';
        } else if ((!$enabled) and ($status == 0)) {
            $solution .= 'Slasharguments are disabled and the test failed. You may try to fix the server configuration.<hr />';
        } else if ($enabled and ($status == -1)) {
            $solution .= 'Slasharguments are enabled, <a href="'.$CFG->wwwroot.'/file.php/testslasharguments">automatic testing</a> not possible.<hr />';
        } else if ((!$enabled) and ($status == -1)) {
            $solution .= 'Slasharguments are disabled, <a href="'.$CFG->wwwroot.'/file.php/testslasharguments">automatic testing</a> not possible.<hr />';
        } else if ((!$enabled) and ($status > 0)) {
            $solution .= 'Slasharguments are disabled though the iternal test is OK. You should enable slasharguments in Moodle configuration.';
        } else if ($enabled and ($status > 0)) {
            $solution .= 'Congratulations - everything seems OK now :-D';
        }
        if ($status < 1) {
            $solution .= '<p>IIS:<ul><li>try to add <code>cgi.fix_pathinfo=1</code> to php.ini</li><li>do NOT enable AllowPathInfoForScriptMappings !!!</li><li>slasharguments may not work when using ISAPI and PHP 4.3.10 and older</li></ul></p>';
            $solution .= '<p>Apache 1:<ul><li>try to add <code>cgi.fix_pathinfo=1</code> to php.ini</li></ul></p>';
            $solution .= '<p>Apache 2:<ul><li>you must add <code>AcceptPathInfo on</code> to php.ini or .htaccess</li><li>try to add <code>cgi.fix_pathinfo=1</code> to php.ini</li></ul></p>';
        }
        return $solution;
    }
    function is_enabled() {
        global $CFG;
        return !empty($CFG->slasharguments);
    }
    function status() {
        global $CFG;
        $handle = @fopen($CFG->wwwroot.'/file.php?file=/testslasharguments', "r");
        $contents = @trim(fread($handle, 10));
        @fclose($handle);
        if ($contents != 'test -1') {
            return -1;
        }
        $handle = @fopen($CFG->wwwroot.'/file.php/testslasharguments', "r");
        $contents = trim(@fread($handle, 10));
        @fclose($handle);
        switch ($contents) {
            case 'test 1': return 1;
            case 'test 2': return 2;
            default:  return 0;
        }
    }
}

class problem_000011 extends problem_base {
    function title() {
        return 'Session errors detected';
    }
    function exists() {
        global $CFG;
        return isset($CFG->session_error_counter);
    }
    function severity() {
        return SEVERITY_ANNOYANCE;
    }
    function description() {
        global $CFG;
        if (isset($CFG->session_error_counter)) {
            return 'Session problems were detected. Total count: '.$CFG->session_error_counter;
        } else {
            return 'No session errors detected.';
        }
    }
    function solution() {
        global $CFG;
        if (optional_param('resetsesserrorcounter', 0, PARAM_BOOL)) {
            if (get_field('config', 'name', 'name', 'session_error_counter')) {
                delete_records('config', 'name', 'session_error_counter');
            }
            return 'Error counter was cleared.';
        } else {
            return '<p>Session errors can be caused by:<ul><li>unresolved problem in server software (aka random switching of users),</li><li>blocked or modified cookies,</li><li>deleting of active session files.</li></ul></p><p><a href="'.me().'&resetsesserrorcounter=1">Reset counter.</a></p>';
        }
    }
}


class problem_00000x extends problem_base {
    function title() {
        return '';
    }
    function exists() {
        return false;
    }
    function severity() {
        return SEVERITY_SIGNIFICANT;
    }
    function description() {
        return '';
    }
    function solution() {
        global $CFG;
        return '';
    }
}

/*

TODO:

    session.save_path -- it doesn't really matter because we are already IN a session, right?
    detect unsupported characters in $CFG->wwwroot - see bug Bug #6091 - relative vs absolute path during backup/restore process

*/

?>
