<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ordering question type language srings
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['absoluteposition'] = 'Absolute position';
$string['addmultipleanswers'] = 'Add {$a} more items';
$string['addsingleanswer'] = 'Add one more item';
$string['allornothing'] = 'All or nothing';
$string['answer'] = 'Item text';
$string['answerheader'] = 'Draggable item {no}';
$string['answernumbering'] = 'Number the choices?';
$string['answernumbering123'] = '1., 2., 3., ...';
$string['answernumberingabc'] = 'a., b., c., ...';
$string['answernumberingABCD'] = 'A., B., C., ...';
$string['answernumberingiii'] = 'i., ii., iii., ...';
$string['answernumberingIIII'] = 'I., II., III., ...';
$string['answernumberingnone'] = 'No numbering';
$string['answernumbering_desc'] = 'The default numbering style.';

$string['correctorder'] = 'The correct order for these items is as follows:';

$string['defaultanswerformat'] = 'Default answer format';
$string['defaultquestionname'] = 'Drag the following items into the correct order.';

$string['duplicatesnotallowed'] = 'Duplication of draggable items is not allowed. The string "{$a->text}" is already used in {$a->item}.';
$string['editingordering'] = 'Editing ordering question';

$string['gradedetails'] = 'Grade details';
$string['gradingtype'] = 'Grading type';
$string['gradingtype_help'] = 'Choose the type of grading calculation.

**All or nothing**
: If all items are in the correct position, then full marks are awarded. Otherwise, the score is zero.

**Absolute position**
: An item is considered correct if it is in the same position as in the correct answer. The highest possible score for the question is **the same as** the number of items displayed to the student.

**Relative to correct position**
: An item is considered correct if it is in the same position as in the correct answer. Correct items receive a score equal to the number of items displayed minus one. Incorrect items receive a score equal to the number of items displayed minus one and minus the distance of the item from its correct position. Thus, if ***n*** items are displayed to the student, the number of marks available for each item is ***(n - 1)***, and the highest mark available for the question is ***n x (n - 1)***, which is the same as ***(n² - n)***.

**Relative to the next item (excluding last)**
: An item is considered correct if it is followed by the same item as it is in the correct answer. The item in the last position is not checked. Thus, the highest possible score for the question is **one less than** the number of items displayed to the student.

**Relative to the next item (including last)**
: An item is considered correct if it is followed by the same item as it is in the correct answer. This includes the last item which must have no item following it. Thus, the highest possible score for the question is **the same as** the number of items displayed to the student.

**Relative to both the previous and next items**
: An item is considered correct if both the previous and next items are the same as they are in the correct answer. The first item should have no previous item, and the last item should have no next item. Thus, there are two possible points for each item, and the highest possible score for the question is **twice** the number of items displayed to the student.

**Relative to ALL previous and next items**
: An item is considered correct if it is preceded by all the same items as it is in the correct answer, and it is followed by all the same items as it is in the correct answer. The order of the previous items does not matter, and nor does the order of the following items. Thus, if ***n*** items are displayed to the student, the number of marks available for each item is ***(n - 1)***, and the highest mark available for the question is ***n x (n - 1)***, which is the same as ***(n² - n)***.

**Longest ordered subset**
: The grade is the number of items in the longest ordered subset of items. The highest possible grade is the same as the number of items displayed. A subset must have at least two items. Subsets do not need to start at the first item (but they can) and they do not need to be contiguous (but they can be). Where there are multiple subsets of equal length, items in the subset that is found first, when searching from left to right, will be displayed as correct. Other items will be marked as incorrect.

**Longest contiguous subset**
: The grade is the number of items in the longest contiguous subset of items. The highest possible grade is the same as the number of items displayed. A subset must have at least two items. Subsets do not need to start at the first item (but they can) and they MUST BE CONTIGUOUS. Where there are multiple subsets of equal length, items in the subset that is found first, when searching from left to right, will be displayed as correct. Other items will be marked as incorrect.';

$string['horizontal'] = 'Horizontal';

$string['layouttype_help'] = 'Choose whether to display the items vertically or horizontally.';
$string['layouttype'] = 'Layout of items';
$string['longestcontiguoussubset'] = 'Longest contiguous subset';
$string['longestorderedsubset'] = 'Longest ordered subset';

$string['noresponsedetails'] = 'Sorry, no details of the response to this question are available.';
$string['noscore'] = 'No score';
$string['notenoughanswers'] = 'Ordering questions must have more than {$a} answers.';

$string['positionx'] = 'Position {$a}';
$string['pluginname'] = 'Ordering';
$string['pluginname_help'] = 'Several items are displayed in a jumbled order. The items can be dragged into a meaningful order.';
$string['pluginname_link'] = 'question/type/ordering';
$string['pluginnameadding'] = 'Adding an Ordering question';
$string['pluginnameediting'] = 'Editing an Ordering question';
$string['pluginnamesummary'] = 'Put jumbled items into a meaningful order.';
$string['privacy:metadata'] = 'The ordering question type plugin does not store any personal data.';

$string['relativeallpreviousandnext'] = 'Relative to ALL the previous and next items';
$string['relativenextexcludelast'] = 'Relative to the next item (excluding last)';
$string['relativenextincludelast'] = 'Relative to the next item (including last)';
$string['relativeonepreviousandnext'] = 'Relative to both the previous and next items';
$string['relativetocorrect'] = 'Relative to correct position';
$string['removeeditor'] = 'Remove HTML editor';
$string['removeitem'] = 'Remove draggable item';

$string['scoredetails'] = 'Here are the scores for each item in this response:';
$string['selectall'] = 'Select all items';
$string['selectcontiguous'] = 'Select a contiguous subset of items';
$string['selectcount'] = 'Size of subset';
$string['selectcount_help'] = 'The number of items that will be displayed when the question is appears in a quiz.';
$string['selectrandom'] = 'Select a random subset of items';
$string['selecttype_help'] = 'Choose whether to display all the items or a subset of the items.';
$string['selecttype'] = 'Item selection type';
$string['showgrading'] = 'Grading details';
$string['showgrading_help'] = 'Choose whether to show or hide details of the score calculation when a student reviews a response to this Ordering question.';

$string['vertical'] = 'Vertical';
