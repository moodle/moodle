<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'quiz', language 'en_us', version '4.1'.
 *
 * @package     quiz
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addingquestions'] = '<p>This side of the page is where you manage your database of questions. Questions are stored in categories to help you keep them organized, and can be used by any quiz in your course or even other courses if you choose to \'publish\' them.</p>
<p>After you select or create a question category you will be able to create or edit questions. You can select any of these questions to add to your quiz over on the other side of this page.</p>';
$string['editingquiz_help'] = 'When creating a quiz, the main concepts are:

* The quiz, containing questions over one or more pages
* The question bank, which stores copies of all questions organized into categories
* Random questions -  A student gets different questions each time they attempt the quiz and different students can get different questions';
$string['graceperiodmin_desc'] = 'There is a potential problem right at the end of the quiz. On the one hand, we want to let students continue working right up until the last second - with the help of the timer that automatically submits the quiz when time runs out. On the other hand, the server may then be overloaded, and take some time to get to process the responses. Therefore, we will accept responses for up to this many seconds after time expires, so they are not penalized for the server being slow. However, the student could cheat and get this many seconds to answer the quiz. You have to make a trade-off based on how much you trust the performance of your server during quizzes.';
$string['marks'] = 'Points';
$string['marks_help'] = 'The numerical points for each question, and the overall attempt score.';
$string['questionbehaviour'] = 'Question behavior';
$string['reportuserswith'] = 'enrolled users who have attempted the quiz';
$string['reportuserswithorwithout'] = 'enrolled users who have, or have not, attempted the quiz';
$string['reportuserswithout'] = 'enrolled users who have not attempted the quiz';
$string['reviewoptionsheading_help'] = 'These options control what information students can see when they review a quiz attempt or look at the quiz reports.

**During the attempt** settings are are only relevant for some behaviors, like \'interactive with multiple tries\', which may display feedback during the attempt.

**Immediately after the attempt** settings apply for the first two minutes after \'Submit all and finish\' is clicked.

**Later, while the quiz is still open** settings apply after this, and before the quiz close date.

**After the quiz is closed** settings apply after the quiz close date has passed. If the quiz does not have a close date, this state is never reached.';
$string['showdetailedmarks'] = 'Show point details';
$string['totalmarksx'] = 'Total of points: {$a}';
$string['youneedtoenrol'] = 'You need to enroll in this course before you can attempt this quiz';
