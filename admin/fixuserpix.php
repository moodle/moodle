<?php

require_once('../config.php');
/**
 * SCRIPT CONFIGURATION
 */
$starttimer = time()+microtime();

$settings = array();
$settings['verbose'] = false;
$settings['username'] = null;
$settings['password'] = null;
$settings['eolchar'] = '<br />'; // Character used to break lines

// Argument arrays: 0=>short name, 1=>long name
$arguments = array(
 array('short'=>'u', 'long'=>'username', 'help' => 'Your moodle username', 'type'=>'STRING', 'default' => ''),
 array('short'=>'pw', 'long'=>'password', 'help' => 'Your moodle password', 'type'=>'STRING', 'default' => ''),
 array('short'=>'v', 'long' => 'verbose', 'help' => 'Display extra information about the process')
);

// Building the USAGE output of the command line version
if (isset($argv) && isset($argc)) {
    $help = "Moodle User Pix Fix. Restores user profile images that were not properly moved during 1.8.2 upgrade to newer versions.\n\n"
          . "Usage: {$argv[0]}; [OPTION] ...\n"
          . "Options:\n"
          . "  -h,    -?, -help, --help               This output\n";

    foreach ($arguments as $arg_array) {
        $equal = '';
        if (!empty($arg_array['type'])) {
            $equal = "={$arg_array['type']}";
        }

        $padding1 = 5 - strlen($arg_array['short']);
        $padding2 = 30 - (strlen($arg_array['long']) + strlen($equal));
        $paddingstr1 = '';
        for ($i = 0; $i < $padding1; $i++) {
            $paddingstr1 .= ' ';
        }
        $paddingstr2 = '';
        for ($i = 0; $i < $padding2; $i++) {
            $paddingstr2 .= ' ';
        }

        $help .= "  -{$arg_array['short']},$paddingstr1--{$arg_array['long']}$equal$paddingstr2{$arg_array['help']}\n";
    }

    $help .= "\nEmail nicolasconnault@gmail.com for any suggestions or bug reports.\n";

    if ($argc == 1 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
        echo $help;
        die();

    } else {

        $settings['eolchar'] = "\n";
        $argv = arguments($argv);
        $argscount = 0;

        foreach ($arguments as $arg_array) {
            $value = null;
            if (in_array($arg_array['short'], array_keys($argv))) {
                $value = $argv[$arg_array['short']];
                unset($argv[$arg_array['short']]);
            } elseif (in_array($arg_array['long'], array_keys($argv))) {
                $value = $argv[$arg_array['long']];
                unset($argv[$arg_array['long']]);
            }
            if (!is_null($value)) {
                $settings[$arg_array['long']] = $value;
                $argscount++;
            }
        }

        // If some params are left in argv, it means they are not supported
        if ($argscount == 0 || count($argv) > 0) {
            echo $help;
            die();
        }
    }
}

/**
 * SCRIPT SETUP
 */
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot .'/course/lib.php');
verbose("Loading libraries...");
$systemcontext = get_context_instance(CONTEXT_SYSTEM);

/**
 * WEB INTERFACE FORM
 */

class pixfix_form extends moodleform {
    function definition() {
        global $arguments;
        $mform =& $this->_form;

        foreach ($arguments as $arg_array) {
            $type = 'advcheckbox';

            $label = ucfirst(str_replace('-', ' ', $arg_array['long']));
            if (!empty($arg_array['type'])) {
                $type = 'text';
            }

            if ($arg_array['long'] == 'password' || $arg_array['long'] == 'username') {
                continue;
            }

            $mform->addElement($type, $arg_array['long'], $label);

            if (isset($arg_array['default'])) {
                $mform->setDefault($arg_array['long'], $arg_array['default']);
            }
        }
        $this->add_action_buttons(false, 'Restore Images');
    }

    function definition_after_data() {

    }
}

$run_script = true;
$web_interface = false;

// If eolchar is still <br />, load the web interface
if ($settings['eolchar'] == '<br />') {
    print_header("User Pix-Fix");
    print_heading("User Pix-Fix");
    $mform = new pixfix_form();

    if ($data = $mform->get_data(false)) {
        foreach ($arguments as $arg_array) {
            if (!empty($data->{$arg_array['long']})) {
                $settings[$arg_array['long']] = $data->{$arg_array['long']};
            }
        }
    } else {
        $run_script = false;
    }

    if (!has_capability('moodle/site:doanything', $systemcontext)) {
        // If not logged in, give link to login page for current site
        notify("You must be logged in as administrator before using this script.");
        require_login();
    } else {
        $mform->display();
    }

    $web_interface = true;
}

if ($run_script) {

    // User authentication
    if (!$web_interface) {
        if (empty($settings['username'])) {
            echo "You must enter a valid username for a moodle administrator account on this site.{$settings['eolchar']}";
            die();
        } elseif (empty($settings['password'])) {
            echo "You must enter a valid password for a moodle administrator account on this site.{$settings['eolchar']}";
            die();
        } else {
            if (!$user = authenticate_user_login($settings['username'], $settings['password'])) {
                echo "Invalid username or password!{$settings['eolchar']}";
                die();
            }
            $USER = complete_user_login($user);
            if (!has_capability('moodle/site:doanything', $systemcontext)) {
                echo "You do not have administration privileges on this Moodle site. These are required for running the restore script.{$settings['eolchar']}";
                die();
            }
        }
    }

    // Script code here

    // Look for old moodledata/users directory
    $oldusersdir = $CFG->dataroot . '/users';

    if (!file_exists($oldusersdir)) {
        notify('The old directory for user profile images ('.$oldusersdir.') does not exist. Pictures cannot be restored!');
    } else {
        // Find user profile images that are not yet in the new directory
        $folders = get_directory_list($oldusersdir, '', false, true, false);

        $restored_count = 0;

        foreach ($folders as $userid) {
            $olddir = $oldusersdir . '/' . $userid;
            $files = get_directory_list($olddir);

            if (empty($files)) {
                continue;
            }

            // Create new user directory
            if (!$newdir = make_user_directory($userid)) {
                // some weird directory - do not stop the upgrade, just ignore it
                continue;
            }

            // Move contents of old directory to new one
            if (file_exists($olddir) && file_exists($newdir)) {
                $restored = false;

                foreach ($files as $file) {
                    if (!file_exists($newdir . '/' . $file)) {
                        copy($olddir . '/' . $file, $newdir . '/' . $file);
                        verbose("Moved $olddir/$file into $newdir/$file");
                        $restored = true;
                    }
                }

                if ($restored) {
                    $restored_count++;
                }
            } else {
                notify("Could not move the contents of $olddir into $newdir!");
                $result = false;
                break;
            }
        }

        if ($settings['eolchar'] == '<br />') {
            print_box_start('generalbox centerpara');
        }
        if ($restored_count > 0) {
            echo "Successfully restored profile images for $restored_count users!" . $settings['eolchar'];
        } else {
            echo "Did not find any user profile images in need of restoring." . $settings['eolchar'];
        }
        if ($settings['eolchar'] == '<br />') {
            print_box_end();
        }
    }
}

if ($settings['eolchar'] == '<br />') {
    print_footer();
}

/**
 * Converts the standard $argv into an associative array taking var=val arguments into account
 * @param array $argv
 * @return array $_ARG
 */
function arguments($argv) {
    $_ARG = array();
    foreach ($argv as $arg) {
        if (ereg('--?([^=]+)=(.*)',$arg,$reg)) {
            $_ARG[$reg[1]] = $reg[2];
        } elseif(ereg('-([a-zA-Z0-9]+)',$arg,$reg)) {
           $_ARG[$reg[1]] = 'true';
        }
    }
    return $_ARG;
}

/**
 * If verbose is switched on, prints a string terminated by the global eolchar string.
 * @param string $string The string to STDOUT
 */
function verbose($string) {
    global $settings;
    if ($settings['verbose'] && !$settings['quiet']) {
        echo $string . $settings['eolchar'];
    }
}

?>
