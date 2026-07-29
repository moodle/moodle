<?php
// Demo learning data for reporting and analytics prototypes.
define('CLI_SCRIPT', true);

require(__DIR__ . '/config.php');
require_once($CFG->libdir . '/testing/generator/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');

global $DB;
$generator = new testing_data_generator();

/** Create or update a category identified by a stable idnumber. */
function seed_category(string $name, string $idnumber) {
    global $DB, $generator;

    if ($category = $DB->get_record('course_categories', ['idnumber' => $idnumber])) {
        if ($category->name !== $name) {
            $category->name = $name;
            $DB->update_record('course_categories', $category);
        }
        return $category;
    }

    return $generator->create_category(['name' => $name, 'idnumber' => $idnumber]);
}

/** Create or update a course identified by its shortname. */
function seed_course(array $definition, int $categoryid): stdClass {
    global $DB, $generator;

    $fields = [
        'fullname' => $definition['fullname'],
        'shortname' => $definition['shortname'],
        'category' => $categoryid,
        'summary' => $definition['summary'],
        'summaryformat' => FORMAT_HTML,
        'visible' => 1,
        'enablecompletion' => 1,
    ];
    if ($course = $DB->get_record('course', ['shortname' => $definition['shortname']])) {
        $changed = false;
        foreach ($fields as $field => $value) {
            if ($course->$field != $value) {
                $course->$field = $value;
                $changed = true;
            }
        }
        if ($changed) {
            update_course($course);
        }
        return $course;
    }

    return $generator->create_course($fields);
}

/** Create or update a user identified by username. */
function seed_user(array $definition): stdClass {
    global $DB, $generator;

    $user = $DB->get_record('user', ['username' => $definition['username'], 'deleted' => 0]);
    // Upgrade the first version of this demo instead of creating duplicate students.
    if (!$user && !empty($definition['legacyusername'])) {
        $user = $DB->get_record('user', ['username' => $definition['legacyusername'], 'deleted' => 0]);
    }
    if ($user) {
        $changed = false;
        foreach (['username', 'firstname', 'lastname', 'email', 'idnumber'] as $field) {
            if ($user->$field !== $definition[$field]) {
                $user->$field = $definition[$field];
                $changed = true;
            }
        }
        if ($changed) {
            $user->timemodified = time();
            $DB->update_record('user', $user);
        }
        return $user;
    }

    unset($definition['legacyusername']);
    return $generator->create_user($definition);
}

/** Create a page once, then keep its visible learning content current. */
function seed_page(int $courseid, string $name, string $intro, string $content, int $section): void {
    global $DB, $generator;

    if ($page = $DB->get_record('page', ['course' => $courseid, 'name' => $name])) {
        $page->intro = $intro;
        $page->introformat = FORMAT_HTML;
        $page->content = $content;
        $page->contentformat = FORMAT_HTML;
        $page->timemodified = time();
        $page->revision++;
        $DB->update_record('page', $page);
        return;
    }

    $generator->create_module('page', [
        'course' => $courseid,
        'name' => $name,
        'intro' => $intro,
        'introformat' => FORMAT_HTML,
        'content' => $content,
        'contentformat' => FORMAT_HTML,
        'completion' => COMPLETION_TRACKING_MANUAL,
    ], ['section' => $section]);
}

/** Create or update a manual assessment grade item with a stable idnumber. */
function seed_grade_item(int $courseid, string $idnumber, string $itemname, bool $migratelegacyfinal = false): grade_item {
    global $DB;

    $record = $DB->get_record('grade_items', [
        'courseid' => $courseid,
        'itemtype' => 'manual',
        'idnumber' => $idnumber,
    ]);
    if (!$record && $migratelegacyfinal) {
        $record = $DB->get_record('grade_items', [
            'courseid' => $courseid,
            'itemtype' => 'manual',
            'itemname' => 'Final Exam',
            'idnumber' => '',
        ]);
    }
    if ($record) {
        if ($record->idnumber !== $idnumber || $record->itemname !== $itemname || (float)$record->grademax !== 100.0) {
            $record->idnumber = $idnumber;
            $record->itemname = $itemname;
            $record->grademin = 0;
            $record->grademax = 100;
            $record->timemodified = time();
            $DB->update_record('grade_items', $record);
        }
        return new grade_item($record, false);
    }

    $item = new grade_item([
        'courseid' => $courseid,
        'itemtype' => 'manual',
        'idnumber' => $idnumber,
        'itemname' => $itemname,
        'grademin' => 0,
        'grademax' => 100,
    ], false);
    $item->insert();
    return $item;
}

function seed_score(int $studentindex, int $courseindex, int $assessmentindex): int {
    // Fixed scores make repeated runs true updates, not a new random sample.
    $baseline = [91, 84, 78, 69, 87, 61, 74, 95, 66, 82, 58, 89, 72, 64, 80, 53, 76, 92, 68, 85, 57, 79, 70, 88];
    $courseadjustments = [2, -5, -1, 4];
    $assessmentadjustments = [3, -2, 1];
    $pattern = (($studentindex * 7 + $courseindex * 5 + $assessmentindex * 3) % 9) - 4;
    return max(35, min(100, $baseline[$studentindex] + $courseadjustments[$courseindex] +
        $assessmentadjustments[$assessmentindex] + $pattern));
}

$category = seed_category('Computer Science and Digital Skills', 'seed_cs_category');

$instructors = [];
foreach ([
    ['username' => 'seed_instructor_nadia', 'firstname' => 'Nadia', 'lastname' => 'Hassan', 'email' => 'nadia.hassan@example.com', 'idnumber' => 'DEMO-I-001'],
    ['username' => 'seed_instructor_omar', 'firstname' => 'Omar', 'lastname' => 'Saleh', 'email' => 'omar.saleh@example.com', 'idnumber' => 'DEMO-I-002'],
    ['username' => 'seed_instructor_lina', 'firstname' => 'Lina', 'lastname' => 'Khalil', 'email' => 'lina.khalil@example.com', 'idnumber' => 'DEMO-I-003'],
] as $definition) {
    $instructors[$definition['username']] = seed_user($definition);
}

$students = [];
$studentnames = [
    ['Amina', 'Youssef'], ['Karim', 'Adel'], ['Salma', 'Mostafa'], ['Yara', 'Nabil'],
    ['Mina', 'Fawzy'], ['Hana', 'Samir'], ['Tarek', 'Mahmoud'], ['Nour', 'Ehab'],
    ['Farah', 'Ali'], ['Ziad', 'Ramy'], ['Mariam', 'Sayed'], ['Adam', 'Ibrahim'],
    ['Reem', 'Walid'], ['Yassin', 'Amr'], ['Laila', 'Hossam'], ['Mahmoud', 'Tamer'],
    ['Jana', 'Ashraf'], ['Mostafa', 'Kamal'], ['Sara', 'Ahmed'], ['Hassan', 'Fathi'],
    ['Aya', 'Medhat'], ['Omar', 'Nasser'], ['Dina', 'Sherif'], ['Rana', 'Gamal'],
];
foreach ($studentnames as $index => [$firstname, $lastname]) {
    $number = str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT);
    $username = 'seed_student_' . $number;
    $students[$username] = seed_user([
        'username' => $username,
        'legacyusername' => 'seed_student' . ($index + 1),
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $username . '@example.com',
        'idnumber' => 'DEMO-S-' . $number,
    ]);
}

$courses = [
    [
        'fullname' => 'Programming Fundamentals', 'shortname' => 'seed_intro_to_programming', 'instructor' => 'seed_instructor_nadia',
        'summary' => '<p>Build a foundation in computational thinking, variables, decisions, loops, and functions.</p>',
        'pages' => [
            ['Getting Started with Problem Solving', '<p>How to break a problem into inputs, processing, and outputs.</p>', '<h2>Learning goal</h2><p>Write clear step-by-step algorithms before coding.</p>'],
            ['Variables and Control Flow', '<p>Use data types, conditional logic, and loops to solve small programming problems.</p>', '<h2>Practice</h2><p>Trace an algorithm and explain each decision branch.</p>'],
        ],
        'assessments' => ['Problem-Solving Quiz', 'Control Flow Exercise', 'Programming Mini-Project'],
        'roster' => range(1, 20),
    ],
    [
        'fullname' => 'Data Structures and Algorithms', 'shortname' => 'seed_data_structures', 'instructor' => 'seed_instructor_omar',
        'summary' => '<p>Explore arrays, linked lists, stacks, queues, trees, and algorithmic efficiency.</p>',
        'pages' => [
            ['Choosing the Right Data Structure', '<p>Compare common structures by their operations and trade-offs.</p>', '<h2>Learning goal</h2><p>Select a structure that fits a stated problem.</p>'],
            ['Algorithm Complexity', '<p>Read and compare Big-O complexity for simple algorithms.</p>', '<h2>Practice</h2><p>Estimate how running time changes as input size grows.</p>'],
        ],
        'assessments' => ['Data Structures Quiz', 'Complexity Analysis Exercise', 'Implementation Project'],
        'roster' => range(3, 22),
    ],
    [
        'fullname' => 'Database Design and SQL', 'shortname' => 'seed_databases_101', 'instructor' => 'seed_instructor_lina',
        'summary' => '<p>Model relational data, write SQL queries, and apply normalization principles.</p>',
        'pages' => [
            ['From Requirements to an ER Diagram', '<p>Identify entities, attributes, keys, and relationships from a business scenario.</p>', '<h2>Learning goal</h2><p>Create a normalized conceptual data model.</p>'],
            ['Querying Relational Data with SQL', '<p>Use SELECT, JOIN, GROUP BY, and aggregates to answer business questions.</p>', '<h2>Practice</h2><p>Write queries against a small sales dataset.</p>'],
        ],
        'assessments' => ['SQL Query Lab', 'Database Design Assignment', 'Database Case Study'],
        'roster' => range(1, 18),
    ],
    [
        'fullname' => 'Web Development Essentials', 'shortname' => 'seed_cs230', 'instructor' => 'seed_instructor_nadia',
        'summary' => '<p>Create accessible, responsive web pages with HTML, CSS, and introductory JavaScript.</p>',
        'pages' => [
            ['Semantic HTML and Accessibility', '<p>Structure content with meaningful HTML and accessible labels.</p>', '<h2>Learning goal</h2><p>Explain why semantic markup helps people and tools.</p>'],
            ['Responsive Layouts and Interaction', '<p>Use CSS layout techniques and JavaScript events for simple interactions.</p>', '<h2>Practice</h2><p>Adapt a page layout for narrow and wide screens.</p>'],
        ],
        'assessments' => ['HTML and CSS Lab', 'JavaScript Knowledge Check', 'Responsive Site Project'],
        'roster' => range(5, 24),
    ],
];

foreach ($courses as $courseindex => $definition) {
    $course = seed_course($definition, $category->id);
    $generator->enrol_user($instructors[$definition['instructor']]->id, $course->id, 'editingteacher');

    foreach ($definition['pages'] as $pageindex => [$name, $intro, $content]) {
        seed_page($course->id, $name, $intro, $content, $pageindex + 1);
    }

    foreach ($definition['roster'] as $studentnumber) {
        $username = 'seed_student_' . str_pad((string)$studentnumber, 3, '0', STR_PAD_LEFT);
        $generator->enrol_user($students[$username]->id, $course->id, 'student');
    }

    foreach ($definition['assessments'] as $assessmentindex => $assessmentname) {
        $item = seed_grade_item(
            $course->id,
            $definition['shortname'] . '_assessment_' . ($assessmentindex + 1),
            $assessmentname,
            $assessmentindex === 2
        );
        foreach ($definition['roster'] as $studentnumber) {
            $username = 'seed_student_' . str_pad((string)$studentnumber, 3, '0', STR_PAD_LEFT);
            $item->update_final_grade($students[$username]->id, seed_score($studentnumber - 1, $courseindex, $assessmentindex), 'seed_demo_data');
        }
    }
}

echo "Seeded " . count($courses) . " courses, " . count($students) . " students, and " . count($instructors) . " instructors. Safe to re-run.\n";
