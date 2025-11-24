#!/usr/bin/env php
<?php
/**
 * Create a test course for testing
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/public/config.php');
require_once($CFG->dirroot . '/course/lib.php');

echo "Creating test course...\n";

try {
    $coursedata = new stdClass();
    $coursedata->fullname = 'Test Course for API';
    $coursedata->shortname = 'TESTAPI';
    $coursedata->category = 1;
    $coursedata->summary = 'Test course for API module creation';
    $coursedata->summaryformat = FORMAT_HTML;
    $coursedata->format = 'topics';
    $coursedata->numsections = 5;
    
    $course = create_course($coursedata);
    echo "✓ Course created successfully!\n";
    echo "  Course ID: {$course->id}\n";
    echo "  Course Name: {$course->fullname}\n";
    exit(0);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    exit(1);
}
