#!/usr/bin/env php
<?php
/**
 * Test script for core_course_create_modules web service
 * 
 * This script tests creating a File resource module in Moodle
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/public/config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

// Set admin user for CLI context
$admin = get_admin();
\core\session\manager::set_user($admin);

// Get the first available course (not site course)
$course = $DB->get_record_sql("SELECT * FROM {course} WHERE id > 1 LIMIT 1");
if (!$course) {
    echo "Error: No course found. Please create a course first.\n";
    exit(1);
}

echo "Testing core_course_create_modules\n";
echo "===================================\n";
echo "Course ID: {$course->id}\n";
echo "Course Name: {$course->fullname}\n\n";

// Test 1: Create a File resource
echo "Test 1: Creating a File resource module\n";
echo "----------------------------------------\n";

try {
    $moduleinfo = new stdClass();
    $moduleinfo->modulename = 'resource';
    $moduleinfo->course = $course->id;
    $moduleinfo->section = 1;
    $moduleinfo->name = 'Test PDF Resource';
    $moduleinfo->visible = 1;
    $moduleinfo->introeditor = array('text' => 'This is a test PDF resource created via API', 'format' => FORMAT_HTML);
    $moduleinfo->display = 0; // Automatic
    $moduleinfo->showsize = 1;
    $moduleinfo->showtype = 1;
    $moduleinfo->showdate = 0;
    
    // Get module record
    $module = $DB->get_record('modules', array('name' => 'resource'), '*', MUST_EXIST);
    $moduleinfo->module = $module->id;
    
    $result = add_moduleinfo($moduleinfo, $course);
    
    echo "✓ Module created successfully!\n";
    echo "  Module ID: {$result->coursemodule}\n";
    echo "  Module Name: {$result->name}\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n\n";
    exit(1);
}

// Test 2: Create a Quiz module
echo "Test 2: Creating a Quiz module\n";
echo "-------------------------------\n";

try {
    $moduleinfo = new stdClass();
    $moduleinfo->modulename = 'quiz';
    $moduleinfo->course = $course->id;
    $moduleinfo->section = 1;
    $moduleinfo->name = 'Test Quiz';
    $moduleinfo->visible = 1;
    $moduleinfo->introeditor = array('text' => 'This is a test quiz created via API', 'format' => FORMAT_HTML);
    
    // Required quiz settings
    $moduleinfo->timeopen = 0;
    $moduleinfo->timeclose = 0;
    $moduleinfo->timelimit = 0;
    $moduleinfo->overduehandling = 'autosubmit';
    $moduleinfo->graceperiod = 0;
    $moduleinfo->preferredbehaviour = 'deferredfeedback';
    $moduleinfo->canredoquestions = 0;
    $moduleinfo->attempts = 0; // Unlimited
    $moduleinfo->attemptonlast = 0;
    $moduleinfo->grademethod = 1; // Highest grade
    $moduleinfo->decimalpoints = 2;
    $moduleinfo->questiondecimalpoints = -1;
    $moduleinfo->reviewattempt = 0x11110;
    $moduleinfo->reviewcorrectness = 0x11110;
    $moduleinfo->reviewmarks = 0x11110;
    $moduleinfo->reviewspecificfeedback = 0x11110;
    $moduleinfo->reviewgeneralfeedback = 0x11110;
    $moduleinfo->reviewrightanswer = 0x11110;
    $moduleinfo->reviewoverallfeedback = 0x11110;
    $moduleinfo->questionsperpage = 1;
    $moduleinfo->navmethod = 'free';
    $moduleinfo->shuffleanswers = 1;
    $moduleinfo->sumgrades = 0;
    $moduleinfo->grade = 10;
    $moduleinfo->quizpassword = '';
    $moduleinfo->subnet = '';
    $moduleinfo->browsersecurity = '-';
    $moduleinfo->delay1 = 0;
    $moduleinfo->delay2 = 0;
    $moduleinfo->showuserpicture = 0;
    $moduleinfo->showblocks = 0;
    $moduleinfo->completionattemptsexhausted = 0;
    $moduleinfo->completionminattempts = 0;
    $moduleinfo->allowofflineattempts = 0;
    
    // Get module record
    $module = $DB->get_record('modules', array('name' => 'quiz'), '*', MUST_EXIST);
    $moduleinfo->module = $module->id;
    
    $result = add_moduleinfo($moduleinfo, $course);
    
    echo "✓ Module created successfully!\n";
    echo "  Module ID: {$result->coursemodule}\n";
    echo "  Module Name: {$result->name}\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n\n";
    exit(1);
}

echo "All tests passed!\n";
exit(0);
