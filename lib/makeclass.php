<?PHP // $Id$

// This file is not used by Moodle itself.
//
// It exists to provide a more object-oriented interface to some of 
// Moodle's main library functions, for use by external programs.
// 
// Thanks to Greg Barnett from Crown College for ideas and code

// Usage example (from an external program):
// 
// /// Set things up
//     $external_moodle_access = true;                   // Affects setup.php
//     require_once("moodle/lib/makeclass.php");         // This file
//     makeClassFromFile("moodlelib.php", "moodlelib");  // File in $CFG->libdir
//
// /// Call moodle functions like this
//     moodle::isteacher($courseID); 


require_once("../config.php");

function makeClassFromFile($file, $classname) {
    global $CFG;

    $file = "$CFG->libdir/$file";

    # sanity checks
    assert('is_file($file)');
    assert('!class_exists($classname)');

    # Load the file into an array, strip out php tags at beginning and end,
    # This assumes that the php start and end tags are each on one line at the
    # beginning and end of the file, and the rest of the file consists only of
    # comments and functions.
    $functions = file($file);
    $functions = array_slice($functions, 1, -1);
    $functions = join('', $functions);

    eval ("class $classname { $functions }");
}

