<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Random course generator. By Nicolas Connault and friends.
 *
 * To use go to .../admin/generator.php?web_interface=1 in your browser.
 *
 * @package generator
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

define('GENERATOR_RANDOM', 0);
define('GENERATOR_SEQUENCE', 1);

/**
 * Controller class for data generation
 */
class generator {
    public $modules_to_ignore = array('hotpot', 'lams', 'journal', 'scorm', 'exercise', 'dialogue');
    public $modules_list = array('forum' => 'forum',
                                 'assignment' => 'assignment',
                                 'chat' => 'chat',
                                 'data' => 'data',
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

    public $resource_types = array('text', 'file', 'html', 'repository', 'directory', 'ims');
    public $glossary_formats = array('continuous', 'encyclopedia', 'entrylist', 'faq', 'fullwithauthor', 'fullwithoutauthor', 'dictionary');
    public $assignment_types = array('upload', 'uploadsingle', 'online', 'offline');
    public $forum_types = array('general'); // others include 'single', 'eachuser', 'qanda'

    public $resource_type_counter = 0;
    public $assignment_type_counter = 0;
    public $forum_type_counter = 0;

    public $settings = array();
    public $eolchar = '<br />';
    public $do_generation = false;
    public $starttime;
    public $original_db;

    public function __construct($settings = array(), $generate=false) {
        global $CFG;

        $this->starttime = time()+microtime();

        $arguments = array(
             array('short'=>'u', 'long'=>'username',
                   'help' => 'Your moodle username', 'type'=>'STRING', 'default' => ''),
             array('short'=>'pw', 'long'=>'password',
                   'help' => 'Your moodle password', 'type'=>'STRING', 'default' => ''),
             array('short'=>'P', 'long' => 'database_prefix',
                   'help' => 'Database prefix to use: tables must already exist or the script will abort!',
                   'type'=>'STRING', 'default' => $CFG->prefix),
             array('short'=>'c', 'long' => 'pre_cleanup', 'help' => 'Delete previously generated data'),
             array('short'=>'C', 'long' => 'post_cleanup',
                   'help' => 'Deletes all generated data at the end of the script (for benchmarking of generation only)'),
             array('short'=>'t', 'long' => 'time_limit',
                   'help' => 'Optional time limit after which to abort the generation, 0 = no limit. Default=0',
                   'type'=>'SECONDS', 'default' => 0),
             array('short'=>'v', 'long' => 'verbose', 'help' => 'Display extra information about the data generation'),
             array('short'=>'q', 'long' => 'quiet', 'help' => 'Inhibits all outputs'),
             array('short'=>'i', 'long' => 'ignore_errors', 'help' => 'Continue script execution when errors occur'),
             array('short'=>'N', 'long' => 'no_data', 'help' => 'Generate nothing (used for cleaning up only)'),
             array('short'=>'T', 'long' => 'tiny',
                   'help' => 'Generates a tiny data set (1 of each course, module, user and section)',
                   'default' => 0),
             array('short'=>'nc', 'long' => 'number_of_courses',
                   'help' => 'The number of courses to generate. Default=1',
                   'type'=>'NUMBER', 'default' => 1),
             array('short'=>'ns', 'long' => 'number_of_students',
                   'help' => 'The number of students to generate. Default=250',
                   'type'=>'NUMBER', 'default' => 250),
             array('short'=>'sc', 'long' => 'students_per_course',
                   'help' => 'The number of students to enrol in each course. Default=20',
                   'type'=>'NUMBER', 'default' => 20),
             array('short'=>'nsec', 'long' => 'number_of_sections',
                   'help' => 'The number of sections to generate in each course. Default=10',
                   'type'=>'NUMBER', 'default' => 10),
             array('short'=>'nmod', 'long' => 'number_of_modules',
                   'help' => 'The number of modules to generate in each section. Default=10',
                   'type'=>'NUMBER', 'default' => 10),
             array('short'=>'mods', 'long' => 'modules_list',
                   'help' => 'The list of modules you want to generate', 'default' => $this->modules_list,
                   'type' => 'mod1,mod2...'),
             array('short'=>'rt', 'long' => 'resource_type',
                   'help' => 'The specific type of resource you want to generate. Defaults to all',
                   'default' => $this->resource_types,
                   'type' => 'SELECT'),
             array('short'=>'at', 'long' => 'assignment_type',
                   'help' => 'The specific type of assignment you want to generate. Defaults to all',
                   'default' => $this->assignment_types,
                   'type' => 'SELECT'),
             array('short'=>'ft', 'long' => 'forum_type',
                   'help' => 'The specific type of forum you want to generate. Defaults to all',
                   'default' => $this->forum_types,
                   'type' => 'SELECT'),
             array('short'=>'gf', 'long' => 'glossary_format',
                   'help' => 'The specific format of glossary you want to generate. Defaults to all',
                   'default' => $this->glossary_formats,
                   'type' => 'SELECT'),
             array('short'=>'ag', 'long' => 'assignment_grades',
                   'help' => 'Generate random grades for each student/assignment tuple', 'default' => true),
             array('short'=>'qg', 'long' => 'quiz_grades',
                   'help' => 'Generate random grades for each student/quiz tuple', 'default' => true),
             array('short'=>'eg', 'long' => 'entries_per_glossary',
                   'help' => 'The number of definitions to generate per glossary. Default=0',
                   'type'=>'NUMBER', 'default' => 1),
             array('short'=>'nq', 'long' => 'questions_per_course',
                   'help' => 'The number of questions to generate per course. Default=20',
                   'type'=>'NUMBER', 'default' => 20),
             array('short'=>'qq', 'long' => 'questions_per_quiz',
                   'help' => 'The number of questions to assign to each quiz. Default=5',
                   'type'=>'NUMBER', 'default' => 5),
             array('short'=>'df', 'long' => 'discussions_per_forum',
                   'help' => 'The number of discussions to generate for each forum. Default=5',
                   'type'=>'NUMBER', 'default' => 5),
             array('short'=>'pd', 'long' => 'posts_per_discussion',
                   'help' => 'The number of posts to generate for each forum discussion. Default=15',
                   'type'=>'NUMBER', 'default' => 15),
             array('short'=>'fd', 'long' => 'fields_per_database',
                   'help' => 'The number of fields to generate for each database. Default=4',
                   'type'=>'NUMBER', 'default' => 4),
             array('short'=>'drs', 'long' => 'database_records_per_student',
                   'help' => 'The number of records to generate for each student/database tuple. Default=1',
                   'type'=>'NUMBER', 'default' => 1),
             array('short'=>'mc', 'long' => 'messages_per_chat',
                   'help' => 'The number of messages to generate for each chat module. Default=10',
                   'type'=>'NUMBER', 'default' => 10),
            );

        foreach ($arguments as $args_array) {
            $this->settings[$args_array['long']] = new generator_argument($args_array);
        }

        foreach ($settings as $setting => $value) {
            $this->settings[$setting]->value = $value;
        }

        if ($generate) {
            $this->generate_data();
        }
    }

    public function connect() {
        global $DB, $CFG;
        $this->original_db = $DB;

        $class = get_class($DB);
        $DB = new $class();
        $DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $this->get('database_prefix'));
    }

    public function dispose() {
        global $DB;
        $DB->dispose();
        $DB = $this->original_db;
    }

    public function generate_users() {
        global $DB, $CFG;

        /**
         * USER GENERATION
         */
        $this->verbose("Generating ".$this->get('number_of_students')." students...");
        $lastnames = array('SMITH','JOHNSON','WILLIAMS','JONES','BROWN','DAVIS','MILLER','WILSON',
            'MOORE','TAYLOR','ANDERSON','THOMAS','JACKSON','WHITE','HARRIS','MARTIN','THOMPSON',
            'GARCIA','MARTINEZ','ROBINSON','CLARK','RODRIGUEZ','LEWIS','LEE','WALKER','HALL',
            'ALLEN','YOUNG','HERNANDEZ','KING','WRIGHT','LOPEZ','HILL','SCOTT','GREEN','ADAMS',
            'BAKER','GONZALEZ','NELSON','CARTER','MITCHELL','PEREZ','ROBERTS','TURNER','PHILLIPS',
            'CAMPBELL','PARKER','EVANS','EDWARDS','COLLINS','STEWART','SANCHEZ','MORRIS','ROGERS',
            'REED','COOK','MORGAN','BELL','MURPHY','BAILEY','RIVERA','COOPER','RICHARDSON','COX',
            'HOWARD','WARD','TORRES','PETERSON','GRAY','RAMIREZ','JAMES','WATSON','BROOKS','KELLY',
            'SANDERS','PRICE','BENNETT','WOOD','BARNES','ROSS','HENDERSON','COLEMAN','JENKINS','PERRY',
            'POWELL','LONG','PATTERSON','HUGHES','FLORES','WASHINGTON','BUTLER','SIMMONS','FOSTER',
            'GONZALES','BRYANT','ALEXANDER','RUSSELL','GRIFFIN','DIAZ','HAYES','MYERS','FORD','HAMILTON',
            'GRAHAM','SULLIVAN','WALLACE','WOODS','COLE','WEST','JORDAN','OWENS','REYNOLDS','FISHER',
            'ELLIS','HARRISON','GIBSON','MCDONALD','CRUZ','MARSHALL','ORTIZ','GOMEZ','MURRAY','FREEMAN',
            'WELLS','WEBB','SIMPSON','STEVENS','TUCKER','PORTER','HUNTER','HICKS','CRAWFORD','HENRY',
            'BOYD','MASON','MORALES','KENNEDY','WARREN','DIXON','RAMOS','REYES','BURNS','GORDON','SHAW',
            'HOLMES','RICE','ROBERTSON','HUNT','BLACK','DANIELS','PALMER','MILLS','NICHOLS','GRANT',
            'KNIGHT','FERGUSON','ROSE','STONE','HAWKINS','DUNN','PERKINS','HUDSON','SPENCER','GARDNER',
            'STEPHENS','PAYNE','PIERCE','BERRY','MATTHEWS','ARNOLD','WAGNER','WILLIS','RAY','WATKINS',
            'OLSON','CARROLL','DUNCAN','SNYDER','HART','CUNNINGHAM','BRADLEY','LANE','ANDREWS','RUIZ',
            'HARPER','FOX','RILEY','ARMSTRONG','CARPENTER','WEAVER','GREENE','LAWRENCE','ELLIOTT','CHAVEZ',
            'SIMS','AUSTIN','PETERS','KELLEY','FRANKLIN','LAWSON','FIELDS','GUTIERREZ','RYAN','SCHMIDT',
            'CARR','VASQUEZ','CASTILLO','WHEELER','CHAPMAN','OLIVER','MONTGOMERY','RICHARDS','WILLIAMSON',
            'JOHNSTON','BANKS','MEYER','BISHOP','MCCOY','HOWELL','ALVAREZ','MORRISON','HANSEN','FERNANDEZ',
            'GARZA','HARVEY','LITTLE','BURTON','STANLEY','NGUYEN','GEORGE','JACOBS','REID','KIM','FULLER',
            'LYNCH','DEAN','GILBERT','GARRETT','ROMERO','WELCH','LARSON','FRAZIER','BURKE','HANSON','DAY',
            'MENDOZA','MORENO','BOWMAN','MEDINA','FOWLER');
        $firstnames = array( 'JAMES','JOHN','ROBERT','MARY','MICHAEL','WILLIAM','DAVID','RICHARD',
            'CHARLES','JOSEPH','THOMAS','PATRICIA','LINDA','CHRISTOPHER','BARBARA','DANIEL','PAUL',
            'MARK','ELIZABETH','JENNIFER','DONALD','GEORGE','MARIA','KENNETH','SUSAN','STEVEN','EDWARD',
            'MARGARET','BRIAN','DOROTHY','RONALD','ANTHONY','LISA','KEVIN','NANCY','KAREN','BETTY',
            'HELEN','JASON','MATTHEW','GARY','TIMOTHY','SANDRA','JOSE','LARRY','JEFFREY','DONNA',
            'FRANK','CAROL','RUTH','SCOTT','ERIC','STEPHEN','ANDREW','SHARON','MICHELLE','LAURA',
            'SARAH','KIMBERLY','DEBORAH','JESSICA','RAYMOND','SHIRLEY','CYNTHIA','ANGELA','MELISSA',
            'BRENDA','AMY','GREGORY','ANNA','JOSHUA','JERRY','REBECCA','VIRGINIA','KATHLEEN','PAMELA',
            'DENNIS','MARTHA','DEBRA','AMANDA','STEPHANIE','WALTER','PATRICK','CAROLYN','CHRISTINE',
            'PETER','MARIE','JANET','CATHERINE','HAROLD','FRANCES','DOUGLAS','HENRY','ANN','JOYCE',
            'DIANE','ALICE','JULIE','CARL','HEATHER');
        $users_count = 0;
        $users = array();

        shuffle($lastnames);
        shuffle($firstnames);

        $next_user_id = $DB->get_field_sql("SELECT MAX(id) FROM {user}") + 1;

        for ($i = 0; $i < $this->get('number_of_students'); $i++) {

            $lastname = trim(ucfirst(strtolower($lastnames[rand(0, count($lastnames) - 1)])));
            $firstname = $firstnames[rand(0, count($firstnames) - 1)];

            $user = new stdClass();
            $user->firstname = trim(ucfirst(strtolower($firstname)));
            $user->username = strtolower(substr($firstname, 0, 7) . substr($lastname, 0, 7)) . $next_user_id++;
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

            $user->id = $DB->insert_record("user", $user);
            $users_count++;
            $users[] = $user->id;
            $next_user_id = $user->id + 1;
            $this->verbose("Inserted $user->firstname $user->lastname into DB "
                ."(username=$user->username, password=password).");
        }

        if (!$this->get('quiet')) {
            echo "$users_count users correctly inserted in the database.{$this->eolchar}";
        }
        return $users;
    }

    public function generate_data() {
        if (!$this->do_generation) {
            return false;
        }

        set_time_limit($this->get('time_limit'));

        // Process tiny data set
        $tiny = $this->get('tiny');
        if (!empty($tiny)) {
            $this->verbose("Generating a tiny data set: 1 student in 1 course with 1 module in 1 section...");
            $this->set('number_of_courses',1);
            $this->set('number_of_students',1);
            $this->set('number_of_modules',1);
            $this->set('number_of_sections',1);
            $this->set('assignment_grades',false);
            $this->set('quiz_grades',false);
            $this->set('students_per_course',1);
            $this->set('questions_per_course',1);
            $this->set('questions_per_quiz',1);
        }

        if ($this->get('pre_cleanup')) {
            $this->verbose("Deleting previous test data...");
            $this->data_cleanup();

            if (!$this->get('quiet')) {
                echo "Previous test data has been deleted.{$this->eolchar}";
            }
        }


        if (!$this->get('no_data')) {
            $users = $this->generate_users();
            $courses = $this->generate_courses();
            $modules = $this->generate_modules($courses);
            $questions = $this->generate_questions($courses, $modules);
            $course_users = $this->generate_role_assignments($users, $courses);
            $this->generate_forum_posts($course_users, $modules);
            $this->generate_grades($course_users, $courses, $modules);
            $this->generate_module_content($course_users, $courses, $modules);
        }

        if ($this->get('post_cleanup')) {
            if (!$this->get('quiet')) {
                echo "Removing generated data..." . $this->eolchar;
            }
            $this->data_cleanup();
            if (!$this->get('quiet')) {
                echo "Generated data has been deleted." . $this->eolchar;
            }
        }

        /**
         * FINISHING SCRIPT
         */
        $stoptimer = time()+microtime();
        $timer = round($stoptimer-$this->starttime,4);
        if (!$this->get('quiet')) {
            echo "End of script! ($timer seconds taken){$this->eolchar}";
        }

    }

    public function generate_courses() {
        global $DB;

        $this->verbose("Generating " . $this->get('number_of_courses')." courses...");
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
        for ($i = 1; $i <= $this->get('number_of_courses'); $i++) {
            $newcourse = fullclone($base_course);
            $newcourse->fullname = "Test course $next_course_id";
            $newcourse->shortname = "Test $next_course_id";
            $newcourse->idnumber = $next_course_id;
            if (!$course = create_course($newcourse)) {
                $this->verbose("Error inserting a new course in the database!");
                if (!$this->get('ignore_errors')) {
                    die();
                }
            } else {
                $courses_count++;
                $next_course_id++;
                $courses[] = $course->id;
                $next_course_id = $course->id + 1;
                $this->verbose("Inserted $course->fullname into DB (idnumber=$course->idnumber).");
            }
        }

        if (!$this->get('quiet')) {
            echo "$courses_count test courses correctly inserted into the database.{$this->eolchar}";
        }
        return $courses;
    }

    public function generate_modules($courses) {
        global $DB, $CFG;
        // Parse the modules-list variable

        $this->verbose("Generating " . $this->get('number_of_sections')." sections with "
            .$this->get('number_of_modules')." modules in each section, for each course...");

        list($modules_list_sql, $modules_params) =
            $DB->get_in_or_equal($this->get('modules_list'), SQL_PARAMS_NAMED, 'mod', true);

        list($modules_ignored_sql, $ignore_params) =
            $DB->get_in_or_equal($this->modules_to_ignore, SQL_PARAMS_NAMED, 'ignore', false);

        $wheresql = "name $modules_list_sql AND name $modules_ignored_sql";
        $modules = $DB->get_records_select('modules', $wheresql, array_merge($modules_params, $ignore_params));

        foreach ($modules as $key => $module) {
            $module->count = 0;

            // Scorm, lams and hotpot are too complex to set up, remove them
            if (in_array($module->name, $this->modules_to_ignore) ||
                !in_array($module->name, $this->modules_list)) {
                unset($modules[$key]);
            }
        }

        // Dirty hack for renumbering the modules array's keys
        $first_module = reset($modules);
        array_shift($modules);
        array_unshift($modules, $first_module);

        $modules_array = array();

        if (count($courses) > 0) {
            $libraries = array();
            foreach ($courses as $courseid) {

                // Text resources
                for ($i = 1; $i <= $this->get('number_of_sections'); $i++) {
                    for ($j = 0; $j < $this->get('number_of_modules'); $j++) {

                        $module = new stdClass();

                        // If only one module is created, and we also need to add a question to a quiz, create only a quiz
                        if ($this->get('number_of_modules') == 1
                                    && $this->get('questions_per_quiz') > 0
                                    && !empty($modules[8])) {
                            $moduledata = $modules[8];
                        } else {
                            $moduledata = $modules[array_rand($modules)];
                        }

                        $libfile = "$CFG->dirroot/mod/$moduledata->name/lib.php";
                        if (file_exists($libfile)) {
                            if (!in_array($libfile, $libraries)) {
                                $this->verbose("Including library for $moduledata->name...");
                                $libraries[] = $libfile;
                                require_once($libfile);
                            }
                        } else {
                            $this->verbose("Could not load lib file for module $moduledata->name!");
                            if (!$this->get('ignore_errors')) {
                                die();
                            }
                        }

                        // Basically 2 types of text fields: description and content
                        $description = "This $moduledata->name has been randomly generated by a very useful script, "
                                     . "for the purpose of testing "
                                     . "the boundaries of Moodle in various contexts. Moodle should be able to scale to "
                                     . "any size without "
                                     . "its speed and ease of use being affected dramatically.";
                        $content = 'Very useful content, I am sure you would agree';

                        $module_type_index = 0;
                        $module->introformat = FORMAT_MOODLE;
                        $module->messageformat = FORMAT_MOODLE;

                        // Special module-specific config
                        switch ($moduledata->name) {
                            case 'assignment':
                                $module->intro = $description;
                                $module->assignmenttype = $this->get_module_type('assignment');
                                $module->timedue = mktime() + 89487321;
                                $module->grade = rand(50,100);
                                break;
                            case 'chat':
                                $module->intro = $description;
                                $module->schedule = 1;
                                $module->chattime = 60 * 60 * 4;
                                break;
                            case 'data':
                                $module->intro = $description;
                                $module->name = 'test';
                                break;
                            case 'choice':
                                $module->intro = $description;
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
                                $module->page_after_submit = $description;
                                $module->comments = $content;
                                break;
                            case 'forum':
                                $module->intro = $description;
                                $module->type = $this->get_module_type('forum');
                                $module->forcesubscribe = rand(0, 1);
                                $module->format = 1;
                                break;
                            case 'glossary':
                                $module->intro = $description;
                                $module->displayformat = $this->glossary_formats[rand(0, count($this->glossary_formats) - 1)];
                                $module->cmidnumber = rand(0,999999);
                                break;
                            case 'label':
                                $module->content = $content;
                                $module->intro = $description;
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
                                $module->type = $this->get_module_type('resource');
                                $module->alltext = $content;
                                $module->summary = $description;
                                $module->windowpopup = rand(0,1);
                                $module->display = rand(0,1);
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
                                $module->files = false;
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
                                $module->intro = $description;
                                $module->summary = $description;
                                break;
                        }

                        $module->name = ucfirst($moduledata->name) . ' ' . $moduledata->count++;

                        $module->course = $courseid;
                        $module->section = $i;
                        $module->module = $moduledata->id;
                        $module->modulename = $moduledata->name;
                        $module->add = $moduledata->name;
                        $module->cmidnumber = '';
                        $module->coursemodule = '';
                        $add_instance_function = $moduledata->name . '_add_instance';

                        $section = get_course_section($i, $courseid);
                        $module->section = $section->id;
                        $module->coursemodule = add_course_module($module);
                        $module->section = $i;

                        if (function_exists($add_instance_function)) {
                            $this->verbose("Calling module function $add_instance_function");
                            $module->instance = $add_instance_function($module, '');
                            $DB->set_field('course_modules', 'instance', $module->instance, array('id'=>$module->coursemodule));
                        } else {
                            $this->verbose("Function $add_instance_function does not exist!");
                            if (!$this->get('ignore_errors')) {
                                die();
                            }
                        }

                        add_mod_to_section($module);

                        $module->cmidnumber = set_coursemodule_idnumber($module->coursemodule, '');

                        $this->verbose("A $moduledata->name module was added to section $i (id $module->section) "
                            ."of course $courseid.");
                        rebuild_course_cache($courseid);

                        $module_instance = $DB->get_field('course_modules', 'instance', array('id' => $module->coursemodule));
                        $module_record = $DB->get_record($moduledata->name, array('id' => $module_instance));
                        $module_record->instance = $module_instance;

                        if (empty($modules_array[$moduledata->name])) {
                            $modules_array[$moduledata->name] = array();
                        }

                        // TODO Find out why some $module_record end up empty here... (particularly quizzes)
                        if (!empty($module_record->instance)) {
                            $modules_array[$moduledata->name][] = $module_record;
                        }
                    }
                }
            }

            if (!$this->get('quiet')) {
                echo "Successfully generated " . $this->get('number_of_modules') * $this->get('number_of_sections')
                    . " modules in each course!{$this->eolchar}";
            }

            return $modules_array;
        }
        return null;
    }

    public function generate_questions($courses, $modules) {
        global $DB, $CFG;

        if (!is_null($this->get('questions_per_course')) && count($courses) > 0 && is_array($courses)) {
            require_once($CFG->libdir .'/questionlib.php');
            require_once($CFG->dirroot .'/mod/quiz/editlib.php');
            $questions = array();
            $questionsmenu = question_bank::get_creatable_qtypes();
            $questiontypes = array();
            foreach ($questionsmenu as $qtype => $qname) {
                $questiontypes[] = $qtype;
            }

            // Add the questions
            foreach ($courses as $courseid) {
                $questions[$courseid] = array();
                for ($i = 0; $i < $this->get('questions_per_course'); $i++) {
                    $qtype = $questiontypes[array_rand($questiontypes)];

                    // Only the following types are supported right now. Hang around for more!
                    $supported_types = array('match', 'essay', 'multianswer', 'multichoice', 'shortanswer',
                            'numerical', 'truefalse', 'calculated');
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
                    $this->verbose("Generated a question of type $qtype for course id $courseid.");
                }
            }

            // Assign questions to quizzes, if such exist
            if (!empty($modules['quiz']) && !empty($questions) && !is_null($this->get('questions_per_quiz'))) {
                $quizzes = $modules['quiz'];

                // Cannot assign more questions per quiz than are available, so determine which is the largest
                $questions_per_quiz = max(count($questions), $this->get('questions_per_quiz'));

                foreach ($quizzes as $quiz) {
                    $questions_added = array();
                    for ($i = 0; $i < $questions_per_quiz; $i++) {

                        // Add a random question to the quiz
                        do {
                            if (empty($quiz->course)) {
                                print_object($quizzes);die();
                            }
                            $random = rand(0, count($questions[$quiz->course]));
                        } while (in_array($random, $questions_added) || !array_key_exists($random, $questions[$quiz->course]));

                        if (!quiz_add_quiz_question($questions[$quiz->course][$random]->id, $quiz)) {

                            // Could not add question to quiz!! report error
                            if (!$this->get('quiet')) {
                                echo "WARNING: Could not add question id $random to quiz id $quiz->id{$this->eolchar}";
                            }
                        } else {
                            $this->verbose("Adding question id $random to quiz id $quiz->id.");
                            $questions_added[] = $random;
                        }
                    }
                }
            }
            return $questions;
        }
        return null;
    }

    public function generate_role_assignments($users, $courses) {
        global $CFG, $DB;
        $course_users = array();

        if (count($courses) > 0) {
            $this->verbose("Inserting student->course role assignments...");
            $assigned_count = 0;
            $assigned_users = array();

            foreach ($courses as $courseid) {
                $course_users[$courseid] = array();

                // Select $students_per_course for assignment to course
                shuffle($users);
                $users_to_assign = array_slice($users, 0, $this->get('students_per_course'));

                $context = get_context_instance(CONTEXT_COURSE, $courseid);
                foreach ($users_to_assign as $random_user) {
                    role_assign(5, $random_user, $context->id);

                    $assigned_count++;
                    $course_users[$courseid][] = $random_user;
                    if (!isset($assigned_users[$random_user])) {
                        $assigned_users[$random_user] = 1;
                    } else {
                        $assigned_users[$random_user]++;
                    }
                    $this->verbose("Student $random_user was assigned to course $courseid.");
                }
            }

            if (!$this->get('quiet')) {
                echo "$assigned_count user => course role assignments have been correctly performed.{$this->eolchar}";
            }
            return $course_users;
        }
        return null;
    }

    public function generate_forum_posts($course_users, $modules) {
        global $CFG, $DB, $USER;

        if (in_array('forum', $this->modules_list) &&
                $this->get('discussions_per_forum') &&
                $this->get('posts_per_discussion') &&
                isset($modules['forum'])) {

            $discussions_count = 0;
            $posts_count = 0;

            foreach ($modules['forum'] as $forum) {
                $forum_users = $course_users[$forum->course];

                for ($i = 0; $i < $this->get('discussions_per_forum'); $i++) {
                    $mform = new fake_form();

                    require_once($CFG->dirroot.'/mod/forum/lib.php');

                    $discussion = new stdClass();
                    $discussion->course        = $forum->course;
                    $discussion->forum         = $forum->id;
                    $discussion->name          = 'Test discussion';
                    $discussion->intro         = 'This is just a test forum discussion';
                    $discussion->assessed      = 0;
                    $discussion->messageformat = 1;
                    $discussion->messagetrust = 0;
                    $discussion->mailnow       = false;
                    $discussion->groupid       = -1;
                    $discussion->attachments   = null;
                    $discussion->itemid = 752157083;

                    $message = '';
                    $super_global_user = clone($USER);
                    $user_id = $forum_users[array_rand($forum_users)];
                    $USER = $DB->get_record('user', array('id' => $user_id));

                    if ($discussion_id = forum_add_discussion($discussion, $mform, $message)) {
                        $discussion = $DB->get_record('forum_discussions', array('id' => $discussion_id));
                        $discussions_count++;

                        // Add posts to this discussion
                        $post_ids = array($discussion->firstpost);

                        for ($j = 0; $j < $this->get('posts_per_discussion'); $j++) {
                            $global_user = clone($USER);
                            $user_id = $forum_users[array_rand($forum_users)];
                            $USER = $DB->get_record('user', array('id' => $user_id));
                            $post = new stdClass();
                            $post->discussion = $discussion_id;
                            $post->subject = 'Re: test discussion';
                            $post->message = '<p>Nothing much to say, since this is just a test...</p>';
                            $post->format = 1;
                            $post->attachments = null;
                            $post->itemid = 752157083;
                            $post->parent = $post_ids[array_rand($post_ids)];

                            if ($post_ids[] = forum_add_new_post($post, $mform, $message)) {
                                $posts_count++;
                            }
                            $USER = $global_user;
                        }
                    }

                    $USER = $super_global_user;

                    if ($forum->type == 'single') {
                        break;
                    }
                }
            }
            if ($discussions_count > 0 && !$this->get('quiet')) {
                echo "$discussions_count forum discussions have been generated.{$this->eolchar}";
            }
            if ($posts_count > 0 && !$this->get('quiet')) {
                echo "$posts_count forum posts have been generated.{$this->eolchar}";
            }

            return true;
        }
        return null;

    }

    public function generate_grades($course_users, $courses, $modules) {
        global $CFG, $DB, $USER;

        /**
         * ASSIGNMENT GRADES GENERATION
         */
        if ($this->get('assignment_grades') && isset($modules['assignment'])) {
            $grades_count = 0;
            foreach ($course_users as $courseid => $userid_array) {
                foreach ($userid_array as $userid) {
                    foreach ($modules['assignment'] as $assignment) {
                        if (in_array($assignment->course, $courses)) {
                            $maxgrade = $assignment->grade;
                            $random_grade = rand(0, $maxgrade);
                            $grade = new stdClass();
                            $grade->assignment = $assignment->id;
                            $grade->userid = $userid;
                            $grade->grade = $random_grade;
                            $grade->rawgrade = $random_grade;
                            $grade->teacher = $USER->id;
                            $grade->submissioncomment = 'comment';
                            $DB->insert_record('assignment_submissions', $grade);
                            grade_update('mod/assignment', $assignment->course, 'mod', 'assignment', $assignment->id, 0, $grade);
                            $this->verbose("A grade ($random_grade) has been given to user $userid "
                                        . "for assignment $assignment->id");
                            $grades_count++;
                        }
                    }
                }
            }
            if ($grades_count > 0) {
                $this->verbose("$grades_count assignment grades have been generated.{$this->eolchar}");
            }
        }

        /**
         * QUIZ GRADES GENERATION
         */
        if ($this->get('quiz_grades') && isset($modules['quiz'])) {
            $grades_count = 0;
            foreach ($course_users as $userid => $courses) {
                foreach ($modules['quiz'] as $quiz) {
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
                        $this->verbose("A grade ($random_grade) has been given to user $userid for quiz $quiz->id");
                        $grades_count++;
                    }
                }
            }
            if ($grades_count > 0 && !$this->get('quiet')) {
                echo "$grades_count quiz grades have been generated.{$this->eolchar}";
            }
        }
        return null;
    }

    public function generate_module_content($course_users, $courses, $modules) {
        global $USER, $DB, $CFG;
        $result = null;

        $entries_count = 0;
        if ($this->get('entries_per_glossary') && !empty($modules['glossary'])) {
            foreach ($modules['glossary'] as $glossary) {
                for ($i = 0; $i < $this->get('entries_per_glossary'); $i++) {
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
                    $DB->insert_record('glossary_entries', $entry);
                    $entries_count++;
                }
            }
            if ($entries_count > 0 && !$this->get('quiet')) {
                echo "$entries_count glossary definitions have been generated.{$this->eolchar}";
            }
            $result = true;
        }

        $fields_count = 0;
        if (!empty($modules['data']) && $this->get('fields_per_database') && $this->get('database_records_per_student')) {
            $database_field_types = array('checkbox',
                                          'date',
                                          'file',
                                          'latlong',
                                          'menu',
                                          'multimenu',
                                          'number',
                                          'picture',
                                          'radiobutton',
                                          'text',
                                          'textarea',
                                          'url');


            $fields = array();

            foreach ($modules['data'] as $data) {

                for ($i = 0; $i < $this->get('fields_per_database'); $i++) {
                    $type = $database_field_types[array_rand($database_field_types)];
                    require_once($CFG->dirroot.'/mod/data/field/'.$type.'/field.class.php');
                    $newfield = 'data_field_'.$type;
                    $cm = get_coursemodule_from_instance('data', $data->id);
                    $newfield = new $newfield(0, $data, $cm);
                    $fields[$data->id][] = $newfield;
                    $newfield->insert_field();
                }

                // Generate fields for each database (same fields for all, no arguing)
                for ($i = 0; $i < $this->get('fields_per_database'); $i++) {

                }

                // Generate database records for each student, if needed
                for ($i = 0; $i < $this->get('database_records_per_student'); $i++) {

                }
            }
            if ($fields_count > 0 && !$this->get('quiet')) {
                $datacount = count($modules['data']);
                echo "$fields_count database fields have been generated for each of the "
                   . "$datacount generated databases.{$this->eolchar}";
            }
            $result = true;
        }

        $messages_count = 0;
        if (!empty($modules['chat']) && $this->get('messages_per_chat')) {

            // Insert all users into chat_users table, then a message from each user
            foreach ($modules['chat'] as $chat) {

                foreach ($course_users as $courseid => $users_array) {

                    foreach ($users_array as $userid) {
                        if ($messages_count < $this->get('messages_per_chat')) {
                            $chat_user = new stdClass();
                            $chat_user->chatid = $chat->id;
                            $chat_user->userid = $userid;
                            $chat_user->course = $courseid;
                            $DB->insert_record('chat_users', $chat_user);

                            $chat_message = new stdClass();
                            $chat_message->chatid = $chat->id;
                            $chat_message->userid = $userid;
                            $chat_message->message = "Hi, everyone!";
                            $DB->insert_record('chat_messages', $chat_message);

                            $messages_count++;
                        }
                    }
                }
            }

            if ($messages_count > 0 && !$this->get('quiet')) {
                $datacount = count($modules['chat']);
                echo "$messages_count messages have been generated for each of the "
                   . "$datacount generated chats.{$this->eolchar}";
            }
            $result = true;
        }

        return $result;
    }


    /**
     * If verbose is switched on, prints a string terminated by the global eolchar string.
     * @param string $string The string to STDOUT
     */
    public function verbose($string) {
        if ($this->get('verbose') && !$this->get('quiet')) {
            echo $string . $this->eolchar;
        }
    }


    /**
     * Attempts to delete all generated test data.
     * WARNING: THIS WILL COMPLETELY MESS UP A "REAL" SITE, AND IS INTENDED ONLY FOR DEVELOPMENT PURPOSES
     */
    function data_cleanup() {
        global $DB;

        if ($this->get('quiet')) {
            ob_start();
        }

        // TODO Cleanup code

        if ($this->get('quiet')) {
            ob_end_clean();
        }
    }

    public function get($setting) {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting]->value;
        } else {
            return null;
        }
    }

    public function set($setting, $value) {
        if (isset($this->settings[$setting])) {
            $this->settings[$setting]->value = $value;
        } else {
            return false;
        }
    }

    public function get_module_type($modulename) {
        $return_val = false;

        $type = $this->get($modulename.'_type');

        if (is_object($type) && isset($type->type) && isset($type->options)) {

            if ($type->type == GENERATOR_RANDOM) {
                $return_val = $type->options[array_rand($type->options)];

            } elseif ($type->type == GENERATOR_SEQUENCE) {
                $return_val = $type->options[$this->{$modulename.'_type_counter'}];
                $this->{$modulename.'_type_counter'}++;

                if ($this->{$modulename.'_type_counter'} == count($type->options)) {
                    $this->{$modulename.'_type_counter'} = 0;
                }
            }

        } elseif (is_array($type)) {
            $return_val = $type[array_rand($type)];

        } elseif (is_string($type)) {
            $return_val = $type;
        }

        return $return_val;
    }
}

class generator_argument {
    public $short;
    public $long;
    public $help;
    public $type;
    public $default = null;
    public $value;

    public function __construct($params) {
        foreach ($params as $key => $val) {
            $this->$key = $val;
        }
        $this->value = $this->default;
    }
}

class generator_cli extends generator {
    public $eolchar = "\n";

    public function __construct($settings, $argc) {
        parent::__construct();

        // Building the USAGE output of the command line version
        $help = "Moodle Data Generator. Generates Data for Moodle sites. Good for benchmarking and other tests.\n\n"
              . "FOR DEVELOPMENT PURPOSES ONLY! DO NOT USE ON A PRODUCTION SITE!\n\n"
              . "Note: By default the script attempts to fill DB tables prefixed with tst_\n"
              . "To override the prefix, use the -P (--database_prefix) setting.\n\n"
              . "Usage: {$settings[0]}; [OPTION] ...\n"
              . "Options:\n"
              . "  -h,    -?, -help, --help               This output\n";

        foreach ($this->settings as $argument) {
            $equal = '';
            if (!empty($argument->type)) {
                $equal = "={$argument->type}";
            }

            $padding1 = 5 - strlen($argument->short);
            $padding2 = 30 - (strlen($argument->long) + strlen($equal));
            $paddingstr1 = '';
            for ($i = 0; $i < $padding1; $i++) {
                $paddingstr1 .= ' ';
            }
            $paddingstr2 = '';
            for ($i = 0; $i < $padding2; $i++) {
                $paddingstr2 .= ' ';
            }

            $help .= "  -{$argument->short},$paddingstr1--{$argument->long}$equal$paddingstr2{$argument->help}\n";
        }

        $help .= "\nEmail nicolas@moodle.com for any suggestions or bug reports.\n";

        if ($argc == 1 || in_array($settings[1], array('--help', '-help', '-h', '-?'))) {
            echo $help;
            die();

        } else {
            $this->do_generation = true;
            $settings = $this->_arguments($settings);
            $argscount = 0;

            foreach ($this->settings as $argument) {
                $value = null;

                if (in_array($argument->short, array_keys($settings))) {
                    $value = $settings[$argument->short];
                    unset($settings[$argument->short]);

                } elseif (in_array($argument->long, array_keys($settings))) {
                    $value = $settings[$argument->long];
                    unset($settings[$argument->long]);
                }

                if (!is_null($value)) {

                    if (!empty($argument->type) && ($argument->type == 'mod1,mod2...' || $argument->type == 'SELECT')) {
                        $value = explode(',', $value);
                    }

                    $this->set($argument->long, $value);
                    $argscount++;
                }
            }

            // If some params are left in argv, it means they are not supported
            if ($argscount == 0 || count($settings) > 0) {
                echo $help;
                die();
            }
        }

        $this->connect();
    }

    public function generate_data() {
        if (is_null($this->get('username')) || $this->get('username') == '') {
            echo "You must enter a valid username for a moodle administrator account on this site.{$this->eolchar}";
            die();
        } elseif (is_null($this->get('password')) || $this->get('password') == '') {
            echo "You must enter a valid password for a moodle administrator account on this site.{$this->eolchar}";
            die();
        } else {
            if (!$user = authenticate_user_login($this->get('username'), $this->get('password'))) {
                echo "Invalid username or password!{$this->eolchar}";
                die();
            }
            complete_user_login($user);
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);

            if (!is_siteadmin($user->id)) {//TODO: add some proper access control check here!!
                echo "You do not have administration privileges on this Moodle site. "
                    ."These are required for running the generation script.{$this->eolchar}";
                die();
            }
        }

        parent::generate_data();
    }

    /**
     * Converts the standard $argv into an associative array taking var=val arguments into account
     * @param array $argv
     * @return array $_ARG
     */
    private function _arguments($argv) {
        $_ARG = array();
        foreach ($argv as $arg) {
            if (preg_match('/--?([^=]+)=(.*)/',$arg,$reg)) {
                $_ARG[$reg[1]] = $reg[2];
            } elseif(preg_match('/-([a-zA-Z0-9]+)/',$arg,$reg)) {
               $_ARG[$reg[1]] = 'true';
            }
        }
        return $_ARG;
    }
}

class generator_web extends generator {
    public $eolchar = '<br />';
    public $mform;

    public function setup() {
        global $CFG;
        $this->mform = new generator_form();

        $this->do_generation = optional_param('do_generation', false, PARAM_BOOL);

        if ($data = $this->mform->get_data(false)) {
            foreach ($this->settings as $setting) {
                if (isset($data->{$setting->long})) {
                    $this->set($setting->long, $data->{$setting->long});
                }
            }
        }
    }

    public function display() {
        global $OUTPUT, $PAGE;
        $PAGE->set_title("Data generator");
        echo $OUTPUT->header();
        echo $OUTPUT->heading("Data generator: web interface");
        echo $OUTPUT->heading("FOR DEVELOPMENT PURPOSES ONLY. DO NOT USE ON A PRODUCTION SITE!", 3);
        echo $OUTPUT->heading("Your database contents will probably be massacred. You have been warned", 5);

        $this->mform->display();
        $this->connect();
    }

    public function complete() {
        global $OUTPUT;
        $this->dispose();
        echo $OUTPUT->footer();
    }
}

class generator_silent extends generator {

}

function generator_generate_data($settings) {
    $generator = new generator($settings);
    $generator->do_generation = true;
    $generator->generate_data();
}

class fake_form {
    function get_new_filename($string) {
        return false;
    }

    function save_stored_file() {
        return true;
    }

    function get_data() {
        return array();
    }
}

class generator_form extends moodleform {
    function definition() {
        global $generator, $CFG; //TODO: sloppy coding style!!

        $mform =& $this->_form;
        $mform->addElement('hidden', 'do_generation', 1);
        $mform->setType('do_generation', PARAM_INT);

        foreach ($generator->settings as $setting) {
            $type = 'advcheckbox';
            $options = null;
            $htmloptions = null;

            $label = ucfirst(str_replace('_', ' ', $setting->long));
            if (!empty($setting->type) && $setting->type == 'mod1,mod2...') {
                $type = 'select';
                $options = $generator->modules_list;
                $htmloptions = array('multiple' => 'multiple');
            } elseif (!empty($setting->type) && $setting->type == 'SELECT') {
                $type = 'select';
                $options = array();
                foreach ($setting->default as $option) {
                    $options[$option] = $option;
                }
                $htmloptions = array('multiple' => 'multiple');
            } elseif (!empty($setting->type)) {
                $type = 'text';
            }

            if ($setting->long == 'password' || $setting->long == 'username') {
                continue;
            }

            $mform->addElement($type, $setting->long, $label, $options, $htmloptions);

            if (isset($setting->default)) {
                $mform->setDefault($setting->long, $setting->default);
            }
        }
        $this->add_action_buttons(false, 'Generate data!');
    }

    function definition_after_data() {

    }
}

if (CLI_SCRIPT) {
    $generator = new generator_cli($argv, $argc);
    $generator->generate_data();
} elseif (strstr($_SERVER['REQUEST_URI'], 'generator.php')) {
    require_login();
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/site:config', $systemcontext);

    $PAGE->set_url('/admin/generator.php');
    $PAGE->set_pagelayout('base');
    $generator = new generator_web();
    $generator->setup();
    $generator->display();
    $generator->generate_data();
    $generator->complete();
} else {
    $generator = new generator_silent();
}
