<?PHP // $Id$

// This file is not currently used within Moodle - see moodlelib.php
//
// It exists to provide a more object-oriented interface to some of 
// Moodle's main library functions, for use by external programs.
//
// This code is based on code from Greg Barnett for Crown College

$moodlelibfile = file("moodlelib.php");

$append = false;
$moodlelib = "";

foreach ($moodlelibfile as $line) {
    if (!$append) {
        if (substr($line, 0, 5) == "<?PHP") {
            $append = true;
        }
    } else {
        if (substr($line, 0, 2) == "?>") {
            break;
        }
        $moodlelib .= $line;
    }
}

eval ("class moodlelib { $moodlelib }");

?>
