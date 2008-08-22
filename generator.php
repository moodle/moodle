<?php

require_once('config.php');
/**
 * SCRIPT CONFIGURATION
 */
$starttimer = time()+microtime();

$settings = array();
$settings['data-prefix'] = 'test_';
$settings['database-prefix'] = null;
$settings['modules-to-ignore'] = array('hotpot', 'lams', 'journal', 'scorm', 'exercise', 'dialogue');
$settings['pre-cleanup'] = false;
$settings['post-cleanup'] = false;
$settings['time-limit'] = 0;
$settings['verbose'] = false;
$settings['quiet'] = false;
$settings['no-data'] = false;
$settings['ignore-errors'] = false;
$settings['number-of-courses'] = 1;
$settings['number-of-students'] = 250;
$settings['students-per-course'] = 20;
$settings['number-of-sections'] = 10;
$settings['number-of-modules'] = 50;
$settings['questions-per-course'] = 20;
$settings['questions-per-quiz'] = 5;
$settings['entries-per-glossary'] = 1;
$settings['assignment-grades'] = true;
$settings['quiz-grades'] = true;
$settings['modules-list'] = array('forum' => 'forum',
                                  'assignment' => 'assignment',
                                  'glossary' => 'glossary',
                                  'quiz' => 'quiz',
                                  'comments' => 'comments',
                                  'feedback' => 'feedback',
                                  'label' => 'label',
                                  'lesson' => 'lesson',
                                  'chat' => 'chat',
                                  'choice' => 'choice',
                                  'resource' => 'resource',
                                  'survey' => 'survey',
                                  'wiki' => 'wiki',
                                  'workshop' => 'workshop');
$settings['username'] = null;
$settings['password'] = null;
$settings['eolchar'] = '<br />'; // Character used to break lines

// Argument arrays: 0=>short name, 1=>long name
$arguments = array(
 array('short'=>'u', 'long'=>'username',
    'help' => 'Your moodle username', 'type'=>'STRING', 'default' => ''),
 array('short'=>'pw', 'long'=>'password',
    'help' => 'Your moodle password', 'type'=>'STRING', 'default' => ''),
 array('short'=>'p', 'long'=>'data-prefix',
    'help' => 'An optional prefix prepended to the unique identifiers of the generated data. Default=test_',
    'type'=>'STRING', 'default' => 'test_'),
 array('short'=>'P', 'long' => 'database-prefix',
    'help' => 'Database prefix to use: tables must already exist or the script will abort!',
    'type'=>'STRING', 'default' => $CFG->prefix),
 array('short'=>'c', 'long' => 'pre-cleanup', 'help' => 'Delete previously generated data'),
 array('short'=>'C', 'long' => 'post-cleanup',
    'help' => 'Deletes all generated data at the end of the script (for benchmarking of generation only)'),
 array('short'=>'t', 'long' => 'time-limit',
    'help' => 'Optional time limit after which to abort the generation, 0 = no limit. Default=0',
    'type'=>'SECONDS', 'default' => 0),
 array('short'=>'v', 'long' => 'verbose', 'help' => 'Display extra information about the data generation'),
 array('short'=>'q', 'long' => 'quiet', 'help' => 'Inhibits all outputs'),
 array('short'=>'i', 'long' => 'ignore-errors', 'help' => 'Continue script execution when errors occur'),
 array('short'=>'N', 'long' => 'no-data', 'help' => 'Generate nothing (used for cleaning up only)'),
 array('short'=>'T', 'long' => 'tiny', 'help' => 'Generates a tiny data set (1 of each course, module, user and section)'),
 array('short'=>'nc', 'long' => 'number-of-courses',
    'help' => 'The number of courses to generate. Default=1', 'type'=>'NUMBER', 'default' => 1),
 array('short'=>'ns', 'long' => 'number-of-students',
    'help' => 'The number of students to generate. Default=250', 'type'=>'NUMBER', 'default' => 250),
 array('short'=>'sc', 'long' => 'students-per-course',
    'help' => 'The number of students to enrol in each course. Default=20', 'type'=>'NUMBER', 'default' => 20),
 array('short'=>'nsec', 'long' => 'number-of-sections',
    'help' => 'The number of sections to generate in each course. Default=10', 'type'=>'NUMBER', 'default' => 10),
 array('short'=>'nmod', 'long' => 'number-of-modules',
    'help' => 'The number of modules to generate in each section. Default=10', 'type'=>'NUMBER', 'default' => 10),
 array('short'=>'mods', 'long' => 'modules-list',
    'help' => 'The list of modules you want to generate', 'default' => $settings['modules-list'], 'type' => 'mod1,mod2...'),
 array('short'=>'ag', 'long' => 'assignment-grades',
    'help' => 'Generate random grades for each student/assignment tuple', 'default' => true),
 array('short'=>'qg', 'long' => 'quiz-grades',
    'help' => 'Generate random grades for each student/quiz tuple', 'default' => true),
 array('short'=>'eg', 'long' => 'entries-per-glossary',
    'help' => 'The number of definitions to generate per glossary. Default=0', 'type'=>'NUMBER', 'default' => 1),
 array('short'=>'nq', 'long' => 'questions-per-course',
    'help' => 'The number of questions to generate per course. Default=20', 'type'=>'NUMBER', 'default' => 20),
 array('short'=>'qq', 'long' => 'questions-per-quiz',
    'help' => 'The number of questions to assign to each quiz. Default=5', 'type'=>'NUMBER', 'default' => 5),
);

// Building the USAGE output of the command line version
if (isset($argv) && isset($argc)) {
    $help = "Moodle Data Generator. Generates Data for Moodle sites. Good for benchmarking and other tests.\n\n"
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

    $help .= "\nEmail nicolas@moodle.com for any suggestions or bug reports.";

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
                if (!empty($arg_array['type']) && $arg_array['type'] == 'mod1,mod2...') {
                    $value = explode(',', $value);
                }
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
set_time_limit($settings['time-limit']);
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot .'/mod/resource/lib.php');
verbose("Loading libraries...");
$systemcontext = get_context_instance(CONTEXT_SYSTEM);

/**
 * WEB INTERFACE FORM
 */

class generator_form extends moodleform {
    function definition() {
        global $arguments, $settings;
        $mform =& $this->_form;

        foreach ($arguments as $arg_array) {
            $type = 'advcheckbox';
            $options = null;
            $htmloptions = null;

            $label = ucfirst(str_replace('-', ' ', $arg_array['long']));
            if (!empty($arg_array['type']) && $arg_array['type'] == 'mod1,mod2...') {
                $type = 'select';
                $options = $settings['modules-list'];
                $htmloptions = array('multiple' => 'multiple');
            } elseif (!empty($arg_array['type'])) {
                $type = 'text';
            }

            if ($arg_array['long'] == 'password' || $arg_array['long'] == 'username') {
                continue;
            }

            $mform->addElement($type, $arg_array['long'], $label, $options, $htmloptions);
            $mform->setHelpButton($arg_array['long'], array(false, $label, false, true, false, $arg_array['help']));

            if (isset($arg_array['default'])) {
                $mform->setDefault($arg_array['long'], $arg_array['default']);
            }
        }
        $this->add_action_buttons(false, 'Generate data!');
    }

    function definition_after_data() {

    }
}


if (!is_null($settings['database-prefix'])) {

    $CFG->prefix = $settings['database-prefix'];
    // Check that all required tables exist

    $tables = array('user', 'course', 'modules', 'course_modules', 'chat', 'choice', 'context', 'course_sections', 'data', 'forum',
                    'glossary', 'label', 'lesson', 'question', 'quiz', 'resource', 'survey', 'wiki', 'workshop', 'course_categories',
                    'role_capabilities', 'config_plugins', 'block', 'message', 'groups', 'block_pinned', 'log', 'grade_items', 'forum_discussions',
                    'event', 'lesson_default', 'grade_categories', 'assignment', 'role_assignments', 'block_instance', 'forum_posts');
    sort($tables);
    $table_errors = array();

    foreach ($tables as $table) {
        require_once($CFG->libdir . '/ddllib.php');
        $dbman = $DB->get_manager();
        $xmltable = new XMLDBTable($table);
        if (!$dbman->table_exists($xmltable)) {
            $table_errors[] = $settings['database-prefix'] . $table;
        }
    }

    if (!empty($table_errors) && !$settings['quiet']) {
        if (!$settings['quiet']) {
            echo "The following tables do not exist in the database:" . $settings['eolchar'];
            foreach ($table_errors as $table) {
                echo "    $table" . $settings['eolchar'];
            }
            echo "Please create these tables or choose a different database prefix before running this script with these parameters again." . $settings['eolchar'];
        }
        if (!$settings['ignore-errors']) {
            die();
        }
    }
}

// Tiny data set
if (!empty($settings['tiny'])) {
    verbose("Generating a tiny data set: 1 student in 1 course with 1 module in 1 section...");
    $settings['number-of-courses'] = 1;
    $settings['number-of-students'] = 1;
    $settings['number-of-modules'] = 1;
    $settings['number-of-sections'] = 1;
    $settings['assignment-grades'] = false;
    $settings['quiz-grades'] = false;
    $settings['students-per-course'] = 1;
    $settings['questions-per-course'] = 1;
    $settings['questions-per-quiz'] = 1;
}

$run_script = true;
$web_interface = false;

// If eolchar is still <br />, load the web interface
if ($settings['eolchar'] == '<br />') {
    print_header("Data generator");
    print_heading("Data generator: web interface");
    $mform = new generator_form();

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
                echo "You do not have administration privileges on this Moodle site. These are required for running the generation script.{$settings['eolchar']}";
                die();
            }
        }
    }

    /**
     * DELETE PREVIOUS TEST DATA
     */
    if ($settings['pre-cleanup']) {
        verbose("Deleting previous test data...");

        data_cleanup();

        if (!$settings['quiet']) {
            echo "Previous test data has been deleted.{$settings['eolchar']}";
        }
    }

    if (!$settings['no-data']) {
        /**
         * USER GENERATION
         */
        verbose("Generating {$settings['number-of-students']} students...");
        $lastnames = array('SMITH', 'JOHNSON', 'WILLIAMS', 'JONES', 'BROWN', 'DAVIS', 'MILLER', 'WILSON', 'MOORE', 'TAYLOR', 'ANDERSON', 'THOMAS', 'JACKSON', 'WHITE',
                           'HARRIS', 'MARTIN', 'THOMPSON', 'GARCIA', 'MARTINEZ', 'ROBINSON', 'CLARK', 'RODRIGUEZ', 'LEWIS', 'LEE', 'WALKER', 'HALL', 'ALLEN',
                           'YOUNG', 'HERNANDEZ', 'KING', 'WRIGHT', 'LOPEZ', 'HILL', 'SCOTT', 'GREEN', 'ADAMS', 'BAKER', 'GONZALEZ', 'NELSON', 'CARTER',
                           'MITCHELL', 'PEREZ', 'ROBERTS', 'TURNER', 'PHILLIPS', 'CAMPBELL', 'PARKER', 'EVANS', 'EDWARDS', 'COLLINS', 'STEWART', 'SANCHEZ', 'MORRIS',
                           'ROGERS', 'REED', 'COOK', 'MORGAN', 'BELL', 'MURPHY', 'BAILEY', 'RIVERA', 'COOPER', 'RICHARDSON', 'COX', 'HOWARD', 'WARD',
                           'TORRES', 'PETERSON', 'GRAY', 'RAMIREZ', 'JAMES', 'WATSON', 'BROOKS', 'KELLY', 'SANDERS', 'PRICE', 'BENNETT', 'WOOD', 'BARNES',
                           'ROSS', 'HENDERSON', 'COLEMAN', 'JENKINS', 'PERRY', 'POWELL', 'LONG', 'PATTERSON', 'HUGHES', 'FLORES', 'WASHINGTON', 'BUTLER',
                           'SIMMONS', 'FOSTER', 'GONZALES', 'BRYANT', 'ALEXANDER', 'RUSSELL', 'GRIFFIN', 'DIAZ', 'HAYES', 'MYERS', 'FORD', 'HAMILTON', 'GRAHAM',
                           'SULLIVAN', 'WALLACE', 'WOODS', 'COLE', 'WEST', 'JORDAN', 'OWENS', 'REYNOLDS', 'FISHER', 'ELLIS', 'HARRISON', 'GIBSON', 'MCDONALD',
                           'CRUZ', 'MARSHALL', 'ORTIZ', 'GOMEZ', 'MURRAY', 'FREEMAN', 'WELLS', 'WEBB', 'SIMPSON', 'STEVENS', 'TUCKER', 'PORTER', 'HUNTER',
                           'HICKS', 'CRAWFORD', 'HENRY', 'BOYD', 'MASON', 'MORALES', 'KENNEDY', 'WARREN', 'DIXON', 'RAMOS', 'REYES', 'BURNS', 'GORDON', 'SHAW',
                           'HOLMES', 'RICE', 'ROBERTSON', 'HUNT', 'BLACK', 'DANIELS', 'PALMER', 'MILLS', 'NICHOLS', 'GRANT', 'KNIGHT', 'FERGUSON', 'ROSE',
                           'STONE', 'HAWKINS', 'DUNN', 'PERKINS', 'HUDSON', 'SPENCER', 'GARDNER', 'STEPHENS', 'PAYNE', 'PIERCE', 'BERRY', 'MATTHEWS', 'ARNOLD',
                           'WAGNER', 'WILLIS', 'RAY', 'WATKINS', 'OLSON', 'CARROLL', 'DUNCAN', 'SNYDER', 'HART', 'CUNNINGHAM', 'BRADLEY', 'LANE', 'ANDREWS',
                           'RUIZ', 'HARPER', 'FOX', 'RILEY', 'ARMSTRONG', 'CARPENTER', 'WEAVER', 'GREENE', 'LAWRENCE', 'ELLIOTT', 'CHAVEZ', 'SIMS', 'AUSTIN',
                           'PETERS', 'KELLEY', 'FRANKLIN', 'LAWSON', 'FIELDS', 'GUTIERREZ', 'RYAN', 'SCHMIDT', 'CARR', 'VASQUEZ', 'CASTILLO', 'WHEELER', 'CHAPMAN',
                           'OLIVER', 'MONTGOMERY', 'RICHARDS', 'WILLIAMSON', 'JOHNSTON', 'BANKS', 'MEYER', 'BISHOP', 'MCCOY', 'HOWELL', 'ALVAREZ', 'MORRISON',
                           'HANSEN', 'FERNANDEZ', 'GARZA', 'HARVEY', 'LITTLE', 'BURTON', 'STANLEY', 'NGUYEN', 'GEORGE', 'JACOBS', 'REID', 'KIM', 'FULLER', 'LYNCH',
                           'DEAN', 'GILBERT', 'GARRETT', 'ROMERO', 'WELCH', 'LARSON', 'FRAZIER', 'BURKE', 'HANSON', 'DAY', 'MENDOZA', 'MORENO', 'BOWMAN', 'MEDINA',
                           'FOWLER');

        $firstnames = array( 'JAMES', 'JOHN', 'ROBERT', 'MARY', 'MICHAEL ', 'WILLIAM', 'DAVID', 'RICHARD', 'CHARLES', 'JOSEPH ', 'THOMAS', 'PATRICIA',
                             'LINDA', 'CHRISTOPHER', 'BARBARA ', 'DANIEL', 'PAUL', 'MARK', 'ELIZABETH', 'JENNIFER ', 'DONALD', 'GEORGE', 'MARIA',
                             'KENNETH', 'SUSAN ', 'STEVEN', 'EDWARD', 'MARGARET', 'BRIAN', 'DOROTHY ', 'RONALD', 'ANTHONY', 'LISA', 'KEVIN', 'NANCY ',
                             'KAREN', 'BETTY', 'HELEN', 'JASON', 'MATTHEW ', 'GARY', 'TIMOTHY', 'SANDRA', 'JOSE', 'LARRY ', 'JEFFREY', 'DONNA', 'FRANK',
                             'CAROL', 'RUTH ', 'SCOTT', 'ERIC', 'STEPHEN', 'ANDREW', 'SHARON ', 'MICHELLE', 'LAURA', 'SARAH', 'KIMBERLY', 'DEBORAH ',
                             'JESSICA', 'RAYMOND', 'SHIRLEY', 'CYNTHIA', 'ANGELA ', 'MELISSA', 'BRENDA', 'AMY', 'GREGORY', 'ANNA ', 'JOSHUA', 'JERRY',
                             'REBECCA', 'VIRGINIA', 'KATHLEEN', 'PAMELA', 'DENNIS', 'MARTHA', 'DEBRA', 'AMANDA', 'STEPHANIE', 'WALTER', 'PATRICK',
                             'CAROLYN', 'CHRISTINE', 'PETER', 'MARIE', 'JANET', 'CATHERINE', 'HAROLD', 'FRANCES', 'DOUGLAS', 'HENRY', 'ANN', 'JOYCE',
                             'DIANE', 'ALICE', 'JULIE', 'CARL', 'HEATHER');
        $users_count = 0;
        $users = array();

        shuffle($lastnames);
        shuffle($firstnames);

        $next_user_id = $DB->get_field_sql("SELECT MAX(id) FROM {user}") + 1;
        for ($i = 0; $i < $settings['number-of-students']; $i++) {

            $lastname = trim(ucfirst(strtolower($lastnames[rand(0, count($lastnames) - 1)])));
            $firstname = $firstnames[rand(0, count($firstnames) - 1)];

            $user = new stdClass();
            $user->firstname = trim(ucfirst(strtolower($firstname)));
            $user->username = $settings['data-prefix'] . strtolower(substr($firstname, 0, 7) . substr($lastname, 0, 7)) . $next_user_id++;
            $user->lastname = $lastname;
            $user->email = $user->username . '@example.com';
            $user->mnethostid = 1;
            $user->city = 'Test City';
            $user->country = 'AU';
            $user->password = md5('password');
            $user->auth        = 'manual';
            $user->confirmed   = 1;
            $user->lang        = $CFG->lang;
            $user->timemodified= time();

            if (!$user->id = $DB->insert_record("user", $user)) {
                verbose("Error inserting a user in the database! Aborting the script!");
                if (!$settings['ignore-errors']) {
                    die();
                }
            } else {
                $users_count++;
                $users[] = $user->id;
                $next_user_id = $user->id + 1;
                verbose("Inserted $user->firstname $user->lastname into DB (username=$user->username, password=password).");
            }
        }

        if (!$settings['quiet']) {
            echo "$users_count users correctly inserted in the database.{$settings['eolchar']}";
        }

        /**
         * COURSE GENERATION
         */
        verbose("Generating {$settings['number-of-courses']} courses...");
        $base_course = new stdClass();
        $next_course_id = $DB->get_field_sql("SELECT MAX(id) FROM {course}") + 1;

        $base_course->MAX_FILE_SIZE = '2097152';
        $base_course->category = '1';
        $base_course->summary = 'Blah Blah';
        $base_course->format = 'weeks';
        $base_course->numsections = '10';
        $base_course->startdate = mktime();
        $base_course->id = '0';

        $courses_count = 0;
        $courses = array();
        for ($i = 1; $i <= $settings['number-of-courses']; $i++) {
            $newcourse = fullclone($base_course);
            $newcourse->fullname = "Test course $next_course_id";
            $newcourse->shortname = "Test $next_course_id";
            $newcourse->idnumber = $settings['data-prefix'] . $next_course_id;

            if (!$course = create_course($newcourse)) {
                verbose("Error inserting a new course in the database!");
                if (!$settings['ignore-errors']) {
                    die();
                }
            } else {
                $courses_count++;
                $next_course_id++;
                $courses[] = $course->id;
                $next_course_id = $course->id + 1;
                verbose("Inserted $course->fullname into DB (idnumber=$course->idnumber).");
            }
        }

        if (!$settings['quiet']) {
            echo "$courses_count test courses correctly inserted into the database.{$settings['eolchar']}";
        }

        /**
         * MODULES GENERATION
         */

        // Parse the modules-list variable

        verbose("Generating {$settings['number-of-sections']} sections with {$settings['number-of-modules']} modules in each section, for each course...");
        $modules = $DB->get_records('modules');

        foreach ($modules as $key => $module) {
            $module->count = 0;

            // Scorm, lams and hotpot are too complex to set up, remove them
            if (in_array($module->name, $settings['modules-to-ignore']) || !in_array($module->name, $settings['modules-list'])) {
                unset($modules[$key]);
            }
        }

        // Dirty hack for renumbering the modules array's keys
        $first_module = reset($modules);
        array_shift($modules);
        array_unshift($modules, $first_module);

        $resource_types = array('text', 'file', 'html', 'repository', 'directory', 'ims');
        $glossary_formats = array('continuous', 'encyclopedia', 'entrylist', 'faq', 'fullwithauthor', 'fullwithoutauthor', 'dictionary');
        $assignment_types = array('upload', 'uploadsingle', 'online', 'offline');
        $forum_types = array('single', 'eachuser', 'qanda', 'general');

        $quizzes = array();
        $assignments = array();
        $glossaries = array();

        if (count($courses) > 0) {
            $libraries = array();
            foreach ($courses as $courseid) {

                // Text resources
                for ($i = 1; $i <= $settings['number-of-sections']; $i++) {
                    for ($j = 0; $j < $settings['number-of-modules']; $j++) {

                        $module = new stdClass();

                        // If only one module is created, and we also need to add a question to a quiz, create only a quiz
                        if ($settings['number-of-modules'] == 1 && $settings['questions-per-quiz'] > 0 && !empty($modules[8])) {
                            $moduledata = $modules[8];
                        } else {
                            $moduledata = $modules[array_rand($modules)];
                        }

                        $libfile = "$CFG->dirroot/mod/$moduledata->name/lib.php";
                        if (file_exists($libfile)) {
                            if (!in_array($libfile, $libraries)) {
                                verbose("Including library for $moduledata->name...");
                                $libraries[] = $libfile;
                                require_once($libfile);
                            }
                        } else {
                            verbose("Could not load lib file for module $moduledata->name!");
                            if (!$settings['ignore-errors']) {
                                die();
                            }
                        }

                        // Basically 2 types of text fields: description and content
                        $description = "This $moduledata->name has been randomly generated by a very useful script, for the purpose of testing "
                                     . "the boundaries of Moodle in various contexts. Moodle should be able to scale to any size without "
                                     . "its speed and ease of use being affected dramatically.";
                        $content = 'Very useful content, I am sure you would agree';

                        // Special module-specific config
                        switch ($moduledata->name) {
                            case 'assignment':
                                $module->description = $description;
                                $module->assignmenttype = $assignment_types[rand(0, count($assignment_types) - 1)];
                                $module->timedue = mktime() + 89487321;
                                $module->grade = rand(50,100);
                                break;
                            case 'chat':
                                $module->intro = $description;
                                $module->schedule = 1;
                                $module->chattime = 60 * 60 * 4;
                                break;
                            case 'choice':
                                $module->text = $content;
                                $module->option = array('Good choice', 'Bad choice', 'No choice');
                                $module->limit  = array(1, 5, 0);
                                break;
                            case 'comments':
                                $module->intro = $description;
                                $module->comments = $content;
                                break;
                            case 'feedback':
                                $module->intro = $description;
                                $module->comments = $content;
                                break;
                            case 'forum':
                                $module->intro = $description;
                                $module->type = $forum_types[rand(0, count($forum_types) - 1)];
                                $module->forcesubscribe = rand(0, 1);
                                break;
                            case 'glossary':
                                $module->intro = $description;
                                $module->displayformat = $glossary_formats[rand(0, count($glossary_formats) - 1)];
                                $module->cmidnumber = rand(0,999999);
                                break;
                            case 'label':
                                $module->content = $content;
                                break;
                            case 'lesson':
                                $module->lessondefault = 1;
                                $module->available = mktime();
                                $module->deadline = mktime() + 719891987;
                                $module->grade = 100;
                                break;
                            case 'quiz':
                                $module->intro = $description;
                                $module->feedbacktext = 'blah';
                                $module->feedback = 1;
                                $module->feedbackboundaries = array(2, 1);
                                $module->grade = 10;
                                $module->timeopen = time();
                                $module->timeclose = time() + 68854;
                                $module->shufflequestions = true;
                                $module->shuffleanswers = true;
                                $module->quizpassword = '';
                                break;
                            case 'resource':
                                $module->type = $resource_types[rand(0, count($resource_types) - 1)];
                                $module->alltext = $content;
                                $module->summary = $description;
                                $module->windowpopup = rand(0,1);
                                $module->resizable = rand(0,1);
                                $module->scrollbars = rand(0,1);
                                $module->directories = rand(0,1);
                                $module->location = 'file.txt';
                                $module->menubar = rand(0,1);
                                $module->toolbar = rand(0,1);
                                $module->status = rand(0,1);
                                $module->width = rand(200,600);
                                $module->height = rand(200,600);
                                $module->directories = rand(0,1);
                                $module->param_navigationmenu = rand(0,1);
                                $module->param_navigationbuttons = rand(0,1);
                                $module->reference = 1;
                                $module->forcedownload = 1;
                                break;
                            case 'survey':
                                $module->template = rand(1,5);
                                $module->intro = $description;
                                break;
                            case 'wiki':
                                $module->summary = $description;
                                break;
                            case 'workshop':
                                $module->description = $description;
                                $module->submissionstartminute = rand(1,59);
                                $module->submissionstarthour = rand(1,23);
                                $module->submissionstartday = rand(1,27);
                                $module->submissionstartmonth = rand(1,11);
                                $module->submissionstartyear = rand(2007,2011);
                                $module->assessmentstartminute = rand(1,59);
                                $module->assessmentstarthour = rand(1,23);
                                $module->assessmentstartday = rand(1,27);
                                $module->assessmentstartmonth = rand(1,11);
                                $module->assessmentstartyear = rand(2007,2011);
                                $module->submissionendminute = rand(1,59);
                                $module->submissionendhour = rand(1,23);
                                $module->submissionendday = rand(1,27);
                                $module->submissionendmonth = rand(1,11);
                                $module->submissionendyear = rand(2007,2011);
                                $module->assessmentendminute = rand(1,59);
                                $module->assessmentendhour = rand(1,23);
                                $module->assessmentendday = rand(1,27);
                                $module->assessmentendmonth = rand(1,11);
                                $module->assessmentendyear = rand(2007,2011);
                                $module->releaseminute = rand(1,59);
                                $module->releasehour = rand(1,23);
                                $module->releaseday = rand(1,27);
                                $module->releasemonth = rand(1,11);
                                $module->releaseyear = rand(2007,2011);
                                break;
                        }

                        $module->name = $settings['data-prefix'] . ucfirst($moduledata->name) . ' ' . $moduledata->count++;

                        $module->course = $courseid;
                        $module->section = $i;
                        $module->module = $moduledata->id;
                        $module->modulename = $moduledata->name;
                        $module->add = $moduledata->name;
                        $module->cmidnumber = '';
                        $add_instance_function = $moduledata->name . '_add_instance';

                        if (function_exists($add_instance_function)) {
                            $module->instance = $add_instance_function($module, '');
                        } else {
                            verbose("Function $add_instance_function does not exist!");
                            if (!$settings['ignore-errors']) {
                                die();
                            }
                        }

                        $section = get_course_section($i, $courseid);
                        $module->section = $section->id;
                        $module->coursemodule = add_course_module($module);
                        $module->section = $i;

                        add_mod_to_section($module);

                        $module->cmidnumber = set_coursemodule_idnumber($module->coursemodule, '');

                        verbose("A $moduledata->name module was added to section $i (id $module->section) of course $courseid.");
                        rebuild_course_cache($courseid);

                        if ($moduledata->name == 'quiz') {
                            $quiz_instance = $DB->get_field('course_modules', 'instance', array('id' => $module->coursemodule));
                            $quiz = $DB->get_record('quiz', array('id' => $quiz_instance));
                            $quiz->instance = $quiz_instance;
                            $quizzes[] = $quiz;
                        } elseif ($moduledata->name == 'assignment') {
                            $assignment_instance = $DB->get_field('course_modules', 'instance', array('id' => $module->coursemodule));
                            $assignment = $DB->get_record('assignment', array('id' => $assignment_instance));
                            $assignment->instance = $assignment_instance;
                            $assignments[] = $assignment;
                        } elseif ($moduledata->name == 'glossary') {
                            $glossary_instance = $DB->get_field('course_modules', 'instance', array('id' => $module->coursemodule));
                            $glossary = $DB->get_record('glossary', array('id' => $glossary_instance));
                            $glossary->instance = $glossary_instance;
                            $glossaries[] = $glossary;
                        }
                    }
                }
            }

            if (!$settings['quiet']) {
                echo "Successfully generated " . $settings['number-of-modules'] * $settings['number-of-sections'] . " modules in each course!{$settings['eolchar']}";
            }
        }

        /**
         * QUESTIONS GENERATION
         */
        if (!empty($settings['questions-per-course'])) {
            require_once($CFG->libdir .'/questionlib.php');
            require_once($CFG->dirroot .'/mod/quiz/editlib.php');
            $questions = array();
            $questionsmenu = question_type_menu();
            $questiontypes = array();
            foreach ($questionsmenu as $qtype => $qname) {
                $questiontypes[] = $qtype;
            }

            // Add the questions
            foreach ($courses as $courseid) {
                $questions[$courseid] = array();
                for ($i = 0; $i < $settings['questions-per-course']; $i++) {
                    $qtype = $questiontypes[array_rand($questiontypes)];

                    // Only the following types are supported right now. Hang around for more!
                    $supported_types = array('match', 'essay', 'multianswer', 'multichoice', 'shortanswer', 'numerical', 'truefalse', 'calculated');
                    $qtype = $supported_types[array_rand($supported_types)];

                    if ($qtype == 'calculated') {
                        continue;
                    }
                    $classname = "question_{$qtype}_qtype";
                    if ($qtype == 'multianswer') {
                        $classname = "embedded_cloze_qtype";
                    }

                    $question = new $classname();
                    $question->qtype = $qtype;
                    $questions[$courseid][] = $question->generate_test("question$qtype-$i", $courseid);
                    verbose("Generated a question of type $qtype for course id $courseid.");
                }
            }

            // Assign questions to quizzes, if such exist
            if (!empty($quizzes) && !empty($questions) && !empty($settings['questions-per-quiz'])) {
                // Cannot assign more questions per quiz than are available, so determine which is the largest
                $questions_per_quiz = max(count($questions), $settings['questions-per-quiz']);

                foreach ($quizzes as $quiz) {
                    $questions_added = array();
                    for ($i = 0; $i < $questions_per_quiz; $i++) {

                        // Add a random question to the quiz
                        do {
                            $random = rand(0, count($questions[$quiz->course]));
                        } while (in_array($random, $questions_added) || !array_key_exists($random, $questions[$quiz->course]));

                        if (!quiz_add_quiz_question($questions[$quiz->course][$random]->id, $quiz)) {

                            // Could not add question to quiz!! report error
                            echo "WARNING: Could not add question id $random to quiz id $quiz->id{$settings['eolchar']}";
                        } else {
                            verbose("Adding question id $random to quiz id $quiz->id.");
                            $questions_added[] = $random;
                        }
                    }
                }
            }
        }

        /**
         * ROLE ASSIGNMENTS
         */
        if (count($courses) > 0) {
            verbose("Inserting student->course role assignments...");
            $assigned_count = 0;
            $assigned_users = array();
            $course_users = array();

            foreach ($courses as $courseid) {
                // Select $students_per_course for assignment to course
                shuffle($users);
                $users_to_assign = array_slice($users, 0, $settings['students-per-course']);

                foreach ($users_to_assign as $random_user) {

                    $context = get_context_instance(CONTEXT_COURSE, $courseid);

                    $newra = new stdClass();
                    $newra->roleid = 5; // Student role
                    $newra->contextid = $context->id;
                    $newra->userid = $random_user;
                    $newra->hidden = 0;
                    $newra->enrol = 1;
                    $success = $DB->insert_record('role_assignments', $newra);

                    if ($success) {
                        $assigned_count++;
                        if (!isset($assigned_users[$random_user])) {
                            $assigned_users[$random_user] = 1;
                        } else {
                            $assigned_users[$random_user]++;
                        }
                        verbose("Student $random_user was assigned to course $courseid.");
                    } else {
                        verbose("Could not assign student $random_user to course $courseid!");
                        if (!$settings['ignore-errors']) {
                            die();
                        }
                    }
                }
            }

            if (!$settings['quiet']) {
                echo "$assigned_count user => course role assignments have been correctly performed.{$settings['eolchar']}";
            }
        }

        /**
         * ASSIGNMENT GRADES GENERATION
         */
        if ($settings['assignment-grades']) {
            $grades_count = 0;
            foreach ($course_users as $userid => $courses) {
                foreach ($assignments as $assignment) {
                    if (in_array($assignment->course, $courses)) {
                        $maxgrade = $assignment->grade;
                        $random_grade = rand(0, $maxgrade);
                        $grade = new stdClass();
                        $grade->assignment = $assignment->id;
                        $grade->userid = $userid;
                        $grade->grade = $random_grade;
                        $grade->rawgrade = $random_grade;
                        $grade->teacher = $USER->id;
                        $DB->insert_record('assignment_submissions', $grade);
                        grade_update('mod/assignment', $courseid, 'mod', 'assignment', $assignment->id, 0, $grade);
                        verbose("A grade ($random_grade) has been given to user $userid for assignment $assignment->id");
                        $grades_count++;
                    }
                }
            }
            echo "$grades_count assignment grades have been generated.{$settings['eolchar']}";
        }

        /**
         * QUIZ GRADES GENERATION
         */
        if ($settings['quiz-grades']) {
            $grades_count = 0;
            foreach ($course_users as $userid => $courses) {
                foreach ($quizzes as $quiz) {
                    if (in_array($quiz->course, $courses)) {
                        $maxgrade = $quiz->grade;
                        $random_grade = rand(0, $maxgrade);
                        $grade = new stdClass();
                        $grade->quiz = $quiz->id;
                        $grade->userid = $userid;
                        $grade->grade = $random_grade;
                        $grade->rawgrade = $random_grade;
                        $DB->insert_record('quiz_grades', $grade);
                        grade_update('mod/quiz', $courseid, 'mod', 'quiz', $quiz->id, 0, $grade);
                        verbose("A grade ($random_grade) has been given to user $userid for quiz $quiz->id");
                        $grades_count++;
                    }
                }
            }
            echo "$grades_count quiz grades have been generated.{$settings['eolchar']}";
        }

        /**
         * GLOSSARY ENTRIES GENERATION
         */
        $entries_count = 0;
        if ($settings['entries-per-glossary']) {
            foreach ($glossaries as $glossary) {
                for ($i = 0; $i < $settings['entries-per-glossary']; $i++) {
                    $entry = new stdClass();
                    $entry->glossaryid = $glossary->id;
                    $entry->userid = $USER->id;
                    $entry->concept = "Test concept";
                    $entry->definition = "A test concept is nothing to write home about: just a test concept.";
                    $entry->format = 1;
                    $entry->timecreated = time();
                    $entry->timemodified = time();
                    $entry->teacherentry = 0;
                    $entry->approved = 1;
                    if ($DB->insert_record('glossary_entries', $entry)) {
                        $entries_count++;
                    }
                }
            }
            echo "$entries_count glossary definitions have been generated.{$settings['eolchar']}";
        }

    }

    /**
     * POST-GENERATION CLEANUP
     */
    if ($settings['post-cleanup']) {
        if (!$settings['quiet']) {
            echo "Removing generated data..." . $settings['eolchar'];
        }
        data_cleanup();
        if (!$settings['quiet']) {
            echo "Generated data has been deleted." . $settings['eolchar'];
        }
    }

    /**
     * FINISHING SCRIPT
     */
    $stoptimer = time()+microtime();
    $timer = round($stoptimer-$starttimer,4);
    if (!$settings['quiet']) {
        echo "End of script! ($timer seconds taken){$settings['eolchar']}";
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

/**
 * Attempts to delete all generated test data. A few conditions are required for this to be successful:
 *   1. If a database-prefix has been given, tables with this prefix must exist
 *   2. If a data prefix has been given (e.g. test_), test data must contain this prefix in their unique identifiers (not PKs)
 * The first method is safest, because it will not interfere with existing tables, but you have to create all the tables yourself.
 */
function data_cleanup() {
    global $settings, $tables, $DB;

    if ($settings['quiet']) {
        ob_start();
    }

    if (!is_null($settings['database-prefix']) && isset($tables)) { // Truncate test tables if a specific db prefix was given
        foreach ($tables as $table_name) {
            // Don't empty a few tables
            if (!in_array($table_name, array('modules', 'block'))) {
                if ($DB->delete_records($table_name)) {
                    verbose("Truncated table $table_name");
                } else {
                    verbose("Could not truncate table $table_name");
                    if (!$settings['ignore-errors']) {
                        die();
                    }
                }
            }
        }

    } else { // Delete records in normal tables if no specific db prefix was given
        $courses = $DB->get_records_select('course', "idnumber LIKE ?", array("{$settings['data-prefix']}%"), null, 'id');

        if (is_array($courses) && count($courses) > 0) {
            foreach ($courses as $course) {
                if (!delete_course($course->id, false)) {
                    verbose("Could not delete course $course->id or some of its associated records from the database.");
                    if (!$settings['ignore-errors']) {
                        die();
                    }
                } else {
                    verbose("Deleted course $course->id and all associated records from the database.");
                }
            }
        }

        verbose("Deleting test users (permanently)...");
        if (!$DB->delete_records_select('user', "username LIKE ?", array("{$settings['data-prefix']}%"))) {
            verbose("Error deleting users from the database");
            if (!$settings['ignore-errors']) {
                die();
            }
        }
    }

    if ($settings['quiet']) {
        ob_end_clean();
    }
}
?>
