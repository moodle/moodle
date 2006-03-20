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

/**
* If start and end date for the quiz are more than this many seconds apart
* they will be represented by two separate events in the calendar
*/
define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

?>
