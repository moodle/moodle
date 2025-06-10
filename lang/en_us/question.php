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
 * Strings for component 'question', language 'en_us', version '4.1'.
 *
 * @package     question
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['behaviour'] = 'Behavior';
$string['behaviourbeingused'] = 'behavior being used: {$a}';
$string['cannotdeletebehaviourinuse'] = 'You cannot delete the behavior \'{$a}\'. It is used by question attempts.';
$string['cannotdeleteneededbehaviour'] = 'Cannot delete the question behavior \'{$a}\'. There are other behaviors installed that rely on it.';
$string['cannotenablebehaviour'] = 'Question behavior {$a} cannot be used directly. It is for internal use only.';
$string['commentormark'] = 'Make comment or override points';
$string['defaultmark'] = 'Default points';
$string['defaultmarkmustbepositive'] = 'Default points must be positive.';
$string['deletingbehaviour'] = 'Deleting question behavior \'{$a}\'';
$string['fractionsnomax'] = 'One of the answers should have a score of 100% so it is possible to get full points for this question.';
$string['howquestionsbehave_help'] = 'Students can interact with the questions in the quiz in various different ways. For example, you may wish the students to enter an answer to each question and then submit the entire quiz, before anything is graded or they get any feedback. That would be \'Deferred feedback\' mode.

Alternatively, you may wish for students to submit each question as they go along to get immediate feedback, and if they do not get it right immediately, have another try for fewer points. That would be \'Interactive with multiple tries\' mode.

Those are probably the two most commonly used modes of behavior.';
$string['mark'] = 'Points';
$string['markedoutof'] = 'Points out of';
$string['markedoutofmax'] = 'Points out of {$a}';
$string['markoutofmax'] = '{$a->mark} points out of {$a->max}';
$string['marks'] = 'Points';
$string['penaltyfactor_help'] = 'This setting determines what fraction of the achieved score is subtracted for each wrong response. It is only applicable if the quiz is run in adaptive mode.

The penalty factor should be a number between 0 and 1. A penalty factor of 1 means that the student has to get the answer right in his first response to get any credit for it at all. A penalty factor of 0 means the student can try as often as he likes and still get full points.';
$string['penaltyforeachincorrecttry_help'] = 'When you run your questions using the \'Interactive with multiple tries\' or \'Adaptive mode\' behavior, so that the the student will have several tries to get the question right, then this option controls how much they are penalized for each incorrect try.

The penalty is a proportion of the total question grade, so if the question is worth three points, and the penalty is 0.3333333, then the student will score 3 if they get the question right first time, 2 if they get it right second try, and 1 of they get it right on the third try.';
$string['questionbehaviouradminsetting'] = 'Question behavior settings';
$string['questionbehavioursdisabled'] = 'Question behaviors to disable';
$string['questionbehavioursdisabledexplained'] = 'Enter a comma separated list of behaviors you do not want to appear in drop-down menu';
$string['questionbehavioursorder'] = 'Question behaviors order';
$string['questionbehavioursorderexplained'] = 'Enter a comma separated list of behaviors in the order you want them to appear in drop-down menu';
$string['showmarkandmax'] = 'Show points and max';
$string['showmaxmarkonly'] = 'Show max points only';
$string['uninstallbehaviour'] = 'Uninstall this question behavior.';
$string['unknownbehaviour'] = 'Unknown behavior: {$a}.';
$string['whethercorrect_help'] = 'This covers both the textual description \'Correct\', \'Partially correct\' or \'Incorrect\', and any colored highlighting that conveys the same information.';
