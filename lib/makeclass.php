<?PHP // $Id$

// This file is not used by Moodle itself.
//
// It exists to provide a more object-oriented interface to some of 
// Moodle's main library functions, for use by external programs.
// 
// Thanks to Greg Barnett from Crown College for this code

// Usage example (from an external program):
// 
//     require("blah/moodle/lib/makeclass.php"); 
//     makeClassFromFile("blah/moodle/lib/moodlelib.php", "moodlelib"); 
//
//     moodle::isteacher($courseID);    // for example ... 
//


function makeClassFromFile($file, $classname) {
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

