<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/config.php');
require_once($CFG->dirroot . '/lib/testing/generator/data_generator.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');

global $DB;
$generator = new testing_data_generator();

// ---------- Helpers: get-or-create / get-or-update ----------

function get_or_create_category(string $name, string $idnumber) {
    global $DB, $generator;
    if ($existing = $DB->get_record('course_categories', ['idnumber' => $idnumber])) {
        if ($existing->name !== $name) {
            $existing->name = $name;
            $DB->update_record('course_categories', $existing);
        }
        return $existing;
    }
    return $generator->create_category(['name' => $name, 'idnumber' => $idnumber]);
}

function get_or_create_course(string $fullname, string $shortname, int $categoryid) {
    global $DB, $generator;
    if ($existing = $DB->get_record('course', ['shortname' => $shortname])) {
        $changed = false;
        if ($existing->fullname !== $fullname) { $existing->fullname = $fullname; $changed = true; }
        if ($existing->category != $categoryid) { $existing->category = $categoryid; $changed = true; }
        if ($changed) {
            update_course($existing);
        }
        return $existing;
    }
    return $generator->create_course([
        'fullname'  => $fullname,
        'shortname' => $shortname,
        'category'  => $categoryid,
    ]);
}

function get_or_create_user(string $username, string $firstname, string $lastname, string $email) {
    global $DB, $generator;
    if ($existing = $DB->get_record('user', ['username' => $username, 'deleted' => 0])) {
        $changed = false;
        foreach (['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email] as $field => $value) {
            if ($existing->$field !== $value) { $existing->$field = $value; $changed = true; }
        }
        if ($changed) {
            $existing->timemodified = time();
            $DB->update_record('user', $existing);
        }
        return $existing;
    }
    return $generator->create_user([
        'username'  => $username,
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
    ]);
}

function get_or_create_grade_item(int $courseid, string $itemname): grade_item {
    global $DB;
    $record = $DB->get_record('grade_items', [
        'courseid' => $courseid,
        'itemtype' => 'manual',
        'itemname' => $itemname,
    ]);
    if ($record) {
        return new grade_item($record, false);
    }
    $gradeitem = new grade_item([
        'courseid'  => $courseid,
        'itemtype'  => 'manual',
        'itemname'  => $itemname,
        'grademax'  => 100,
        'grademin'  => 0,
    ], false);
    $gradeitem->insert();
    return $gradeitem;
}

// ---------- Main seeding logic (fixed, stable identifiers — no timestamps) ----------

$category = get_or_create_category('Computer Science', 'seed_cs_category');

$courseNames = ['Intro to Programming', 'Data Structures', 'Databases 101'];
$courses = [];
foreach ($courseNames as $name) {
    $shortname = 'seed_' . strtolower(str_replace(' ', '_', $name));
    $courses[] = get_or_create_course($name, $shortname, $category->id);
}

$students = [];
for ($i = 1; $i <= 15; $i++) {
    $students[] = get_or_create_user(
        "seed_student$i",
        "Student$i",
        'Test',
        "seed_student$i@example.com"
    );
}

foreach ($courses as $course) {
    foreach ($students as $student) {
        $generator->enrol_user($student->id, $course->id, 'student'); // already idempotent
    }
}

foreach ($courses as $course) {
    $gradeitem = get_or_create_grade_item($course->id, 'Final Exam');
    foreach ($students as $student) {
        $finalgrade = rand(50, 100);
        $gradeitem->update_final_grade($student->id, $finalgrade, 'seedscript');
    }
}

echo "Done seeding (safe to re-run).\n";