<?php  // $Id$
/**
* Constants for the questions and quizzes.
*
* This is included by lib.php as well as questionlib.php
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been completely
*         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
*         the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

/// CONSTANTS ///////////////////////////////////////////////////////////////////

/**#@+
* Option flags for ->optionflags
* The options are read out via bitwise operation using these constants
*/
/**
* Whether the questions to be run in adaptive mode. If this is not set then
* a question closes immediately after the first submission of responses. This
* is how Moodle worked before version 1.5
*/
define('QUIZ_ADAPTIVE', 1);

/** When processing responses the code checks that the new responses at
* a question differ from those given on the previous submission. If
* furthermore $ignoredupresp (ignore duplicate responses) is set to true
* then the code goes through the whole history of attempts and checks if
* ANY of them are identical to the current response in which case the 
* current response is ignored.
*/
define('QUIZ_IGNORE_DUPRESP', 2);

/**#@-*/

/**#@+
* The different review options are stored in the bits of $quiz->review
* These constants help to extract the options
*/
/**
* The first 6 bits refer to the time immediately after the attempt
*/
define('QUIZ_REVIEW_IMMEDIATELY', 64-1);
/**
* the next 6 bits refer to the time after the attempt but while the quiz is open
*/
define('QUIZ_REVIEW_OPEN', 4096-64);
/**
* the final 6 bits refer to the time after the quiz closes
*/
define('QUIZ_REVIEW_CLOSED', 262144-4096);

// within each group of 6 bits we determine what should be shown
define('QUIZ_REVIEW_RESPONSES', 1+64+4096);    // Show responses
define('QUIZ_REVIEW_SCORES', 2*4161);   // Show scores
define('QUIZ_REVIEW_FEEDBACK', 4*4161); // Show feedback
define('QUIZ_REVIEW_ANSWERS', 8*4161);  // Show correct answers
// Some handling of worked solutions is already in the code but not yet fully supported
// and not switched on in the user interface.
define('QUIZ_REVIEW_SOLUTIONS', 16*4161);  // Show solutions
// the 6th bit is as yet unused
/**#@-*/

/**#@+
* The different types of events that can create question states
*/
define('QUIZ_EVENTOPEN', '0');
define('QUIZ_EVENTNAVIGATE', '1');
define('QUIZ_EVENTSAVE', '2');
define('QUIZ_EVENTGRADE', '3');
define('QUIZ_EVENTDUPLICATEGRADE', '4');
define('QUIZ_EVENTVALIDATE', '5');
define('QUIZ_EVENTCLOSE', '6');
/**#@-*/

/**#@+
* The defined question types
*
* @todo It would be nicer to have a fully automatic plug-in system
*/
define("SHORTANSWER",   "1");
define("TRUEFALSE",     "2");
define("MULTICHOICE",   "3");
define("RANDOM",        "4");
define("MATCH",         "5");
define("RANDOMSAMATCH", "6");
define("DESCRIPTION",   "7");
define("NUMERICAL",     "8");
define("MULTIANSWER",   "9");
define("CALCULATED",   "10");
define("RQP",          "11");
define("ESSAY",        "12");
/**#@-*/

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_CATEGORIES_SORTORDER", "999");


/**
* If start and end date for the quiz are more than this many seconds apart
* they will be represented by two separate events in the calendar
*/
define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

?>
