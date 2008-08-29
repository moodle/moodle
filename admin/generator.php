<?php
require_once(dirname(__FILE__).'/../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot .'/mod/resource/lib.php');
/**
 * Controller class for data generation
 */
class generator {
    public $modules_to_ignore = array('hotpot', 'lams', 'journal', 'scorm', 'exercise', 'dialogue');
    public $modules_list = array('forum' => 'forum',
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
    public $tables = array('user', 'course', 'modules', 'course_modules', 'chat', 'choice', 'context',
                        'course_sections', 'data', 'forum', 'glossary', 'label', 'lesson', 'question',
                        'quiz', 'resource', 'survey', 'wiki', 'workshop', 'course_categories',
                        'role_capabilities', 'config_plugins', 'block', 'message', 'groups', 'block_pinned',
                        'log', 'grade_items', 'forum_discussions', 'event', 'lesson_default', 'grade_categories',
                        'assignment', 'role_assignments', 'block_instance', 'forum_posts');

    public $settings = array();
    public $eolchar = '<br />';

    public $starttime;

    public function __construct($settings = array(), $generate=false) {
        global $CFG;

        $this->starttimer = time()+microtime();

        $arguments = array(
             array('short'=>'u', 'long'=>'username',
                   'help' => 'Your moodle username', 'type'=>'STRING', 'default' => ''),
             array('short'=>'pw', 'long'=>'password',
                   'help' => 'Your moodle password', 'type'=>'STRING', 'default' => ''),
             array('short'=>'p', 'long'=>'data_prefix',
                   'help' => 'An optional prefix prepended to the unique identifiers of the generated data. Default=test_',
                   'type'=>'STRING', 'default' => 'test_'),
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
            $user->username = $this->get('data_prefix') . strtolower(substr($firstname, 0, 7)
                . substr($lastname, 0, 7)) . $next_user_id++;
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
                $this->verbose("Error inserting a user in the database! Aborting the script!");
                if (!$this->get('ignore_errors')) {
                    die();
                }
            } else {
                $users_count++;
                $users[] = $user->id;
                $next_user_id = $user->id + 1;
                $this->verbose("Inserted $user->firstname $user->lastname into DB "
                    ."(username=$user->username, password=password).");
            }
        }

        if (!$this->get('quiet')) {
            echo "$users_count users correctly inserted in the database.{$this->eolchar}";
        }
        return $users;
    }

    public function generate_courses() {
        global $DB;

        $this->verbose("Generating {$this->get('number_of_courses')} courses...");
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
            $newcourse->idnumber = $this->get('data_prefix') . $next_course_id;

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
        $modules = $DB->get_records('modules');

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

        $resource_types = array('text', 'file', 'html', 'repository', 'directory', 'ims');
        $glossary_formats = array('continuous', 'encyclopedia', 'entrylist', 'faq', 'fullwithauthor',
            'fullwithoutauthor', 'dictionary');
        $assignment_types = array('upload', 'uploadsingle', 'online', 'offline');
        $forum_types = array('single', 'eachuser', 'qanda', 'general');

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

                        $module->name = $this->get('data_prefix') . ucfirst($moduledata->name) . ' ' . $moduledata->count++;

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
                            $this->verbose("Function $add_instance_function does not exist!");
                            if (!$this->get('ignore_errors')) {
                                die();
                            }
                        }

                        $section = get_course_section($i, $courseid);
                        $module->section = $section->id;
                        $module->coursemodule = add_course_module($module);
                        $module->section = $i;

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

                        $modules_array[$moduledata->name][] = $module_record;
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
            $questionsmenu = question_type_menu();
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
                            $random = rand(0, count($questions[$quiz->course]));
                        } while (in_array($random, $questions_added) || !array_key_exists($random, $questions[$quiz->course]));

                        if (!quiz_add_quiz_question($questions[$quiz->course][$random]->id, $quiz)) {

                            // Could not add question to quiz!! report error
                            echo "WARNING: Could not add question id $random to quiz id $quiz->id{$this->eolchar}";
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
                        $course_users[$courseid][] = $random_user;
                        if (!isset($assigned_users[$random_user])) {
                            $assigned_users[$random_user] = 1;
                        } else {
                            $assigned_users[$random_user]++;
                        }
                        $this->verbose("Student $random_user was assigned to course $courseid.");
                    } else {
                        $this->verbose("Could not assign student $random_user to course $courseid!");
                        if (!$this->get('ignore_errors')) {
                            die();
                        }
                    }
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
                    $discussion->name = 'Test discussion';
                    $discussion->intro = 'This is just a test forum discussion';
                    $discussion->format = 1;
                    $discussion->forum = $forum->id;
                    $discussion->mailnow = false;
                    $discussion->course = $forum->course;

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
        global $CFG, $DB;

        /**
         * ASSIGNMENT GRADES GENERATION
         */
        if ($this->get('assignment_grades')) {
            $grades_count = 0;
            foreach ($course_users as $userid => $courses) {
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
                        $DB->insert_record('assignment_submissions', $grade);
                        grade_update('mod/assignment', $courseid, 'mod', 'assignment', $assignment->id, 0, $grade);
                        $this->verbose("A grade ($random_grade) has been given to user $userid for assignment $assignment->id");
                        $grades_count++;
                    }
                }
            }
            if ($grades_count > 0) {
                echo "$grades_count assignment grades have been generated.{$this->eolchar}";
            }
        }

        /**
         * QUIZ GRADES GENERATION
         */
        if ($this->get('quiz_grades')) {
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
            if ($grades_count > 0) {
                echo "$grades_count quiz grades have been generated.{$this->eolchar}";
            }
        }
        return null;
    }

    public function generate_module_content($course_users, $courses, $modules) {
        global $USER, $DB, $CFG;

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
                    if ($DB->insert_record('glossary_entries', $entry)) {
                        $entries_count++;
                    }
                }
            }
            if ($entries_count > 0 && !$this->get('quiet')) {
                echo "$entries_count glossary definitions have been generated.{$this->eolchar}";
            }
            return true;
        }
        return null;
    }

    /**
     * If an alternate DB prefix was given, we need to check that the appropriate tables
     * exist.
     */
    public function check_test_tables() {
        global $CFG;

        sort($this->tables);
        $CFG->prefix = $this->get('database_prefix');
        // Check that all required tables exist

        $table_errors = array();

        foreach ($this->tables as $table) {
            require_once($CFG->libdir . '/ddllib.php');
            $dbman = $DB->get_manager();
            $xmltable = new XMLDBTable($table);
            if (!$dbman->table_exists($xmltable)) {
                $table_errors[] = $this->get('database_prefix') . $table;
            }
        }

        if (!empty($table_errors) && !$this->get('quiet')) {
            if (!$this->get('quiet')) {
                echo "The following tables do not exist in the database:" . $this->eolchar;
                foreach ($table_errors as $table) {
                    echo "    $table" . $this->eolchar;
                }
                echo "Please create these tables or choose a different database prefix before running "
                    ."this script with these parameters again." . $this->eolchar;
            }
            if (!$this->get('ignore_errors')) {
                die();
            }
        }

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
     * Attempts to delete all generated test data. A few conditions are required for this to be successful:
     *   1. If a database-prefix has been given, tables with this prefix must exist
     *   2. If a data prefix has been given (e.g. test_), test data must contain this prefix in their unique identifiers (not PKs)
     * The first method is safest, because it will not interfere with existing tables, but you have to create all the tables yourself.
     */
    function data_cleanup() {
        global $DB;

        if ($this->get('quiet')) {
            ob_start();
        }

        // Truncate test tables if a specific db prefix was given
        if (!is_null($this->get('database_prefix')) && isset($tables)) {
            foreach ($tables as $table_name) {
                // Don't empty a few tables
                if (!in_array($table_name, array('modules', 'block'))) {
                    if ($DB->delete_records($table_name)) {
                        $this->verbose("Truncated table $table_name");
                    } else {
                        $this->verbose("Could not truncate table $table_name");
                        if (!$this->get('ignore_errors')) {
                            die();
                        }
                    }
                }
            }

        } else { // Delete records in normal tables if no specific db prefix was given
            $courses = $DB->get_records_select('course', "idnumber LIKE ?",
                array($this->get('data_prefix').'%'), null, 'id');

            if (is_array($courses) && count($courses) > 0) {
                foreach ($courses as $course) {
                    if (!delete_course($course->id, false)) {
                        $this->verbose("Could not delete course $course->id or some of "
                            ."its associated records from the database.");
                        if (!$this->get('ignore_errors')) {
                            die();
                        }
                    } else {
                        $this->verbose("Deleted course $course->id and all associated records from the database.");
                    }
                }
            }

            $this->verbose("Deleting test users (permanently)...");
            if (!$DB->delete_records_select('user', "username LIKE ?", array($this->get('data_prefix').'%'))) {
                $this->verbose("Error deleting users from the database");
                if (!$this->get('ignore_errors')) {
                    die();
                }
            }
        }

        if ($this->get('quiet')) {
            ob_end_clean();
        }
    }

    public function generate_data() {
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
        $timer = round($stoptimer-$this->starttimer,4);
        if (!$this->get('quiet')) {
            echo "End of script! ($timer seconds taken){$this->eolchar}";
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

                    if (!empty($argument->type) && $argument->type == 'mod1,mod2...') {
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
            $USER = complete_user_login($user);
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            if (!has_capability('moodle/site:doanything', $systemcontext)) {
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
            if (ereg('--?([^=]+)=(.*)',$arg,$reg)) {
                $_ARG[$reg[1]] = $reg[2];
            } elseif(ereg('-([a-zA-Z0-9]+)',$arg,$reg)) {
               $_ARG[$reg[1]] = 'true';
            }
        }
        return $_ARG;
    }
}

class generator_web extends generator {
    public $eolchar = '<br />';

    public function display() {
        print_header("Data generator");
        print_heading("Data generator: web interface");
        $mform = new generator_form();

        if ($data = $mform->get_data(false)) {
            foreach ($this->settings as $setting) {
                if (isset($data->{$setting->long})) {
                    $this->set($setting->long, $data->{$setting->long});
                }
            }
        }

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        if (!has_capability('moodle/site:doanything', $systemcontext)) {
            // If not logged in, give link to login page for current site
            notify("You must be logged in as administrator before using this script.");
            require_login();
        } else {
            $mform->display();
        }
    }

    public function __destroy() {
        print_footer();
    }
}

function generator_generate_data($settings) {
    $generator = new generator($settings);
    $generator->generate_data();
}

class fake_form {
    function get_new_filename($string) {
        return false;
    }
}

class generator_form extends moodleform {
    function definition() {
        global $generator;
        $mform =& $this->_form;
        $mform->addElement('hidden', 'web_interface', 1);

        foreach ($generator->settings as $setting) {
            $type = 'advcheckbox';
            $options = null;
            $htmloptions = null;

            $label = ucfirst(str_replace('-', ' ', $setting->long));
            if (!empty($setting->type) && $setting->type == 'mod1,mod2...') {
                $type = 'select';
                $options = $generator->modules_list;
                $htmloptions = array('multiple' => 'multiple');
            } elseif (!empty($setting->type)) {
                $type = 'text';
            }

            if ($setting->long == 'password' || $setting->long == 'username') {
                continue;
            }

            $mform->addElement($type, $setting->long, $label, $options, $htmloptions);
            $mform->setHelpButton($setting->long, array(false, $label, false, true, false, $setting->help));

            if (isset($setting->default)) {
                $mform->setDefault($setting->long, $setting->default);
            }
        }
        $this->add_action_buttons(false, 'Generate data!');
    }

    function definition_after_data() {

    }
}

$web_interface = optional_param('web_interface', false, PARAM_BOOL);

if (isset($argv) && isset($argc)) {
    $generator = new generator_cli($argv, $argc);
    $generator->generate_data();
} elseif($web_interface) {
    $generator = new generator_web();
    $generator->display();
    $generator->generate_data();
}

?>
