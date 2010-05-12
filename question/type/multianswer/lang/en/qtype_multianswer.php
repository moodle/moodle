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
 * Strings for component 'qtype_multianswer', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   qtype_multianswer
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addingmultianswer'] = 'Adding an Embedded answers (Cloze) question';
$string['confirmsave']='Confirm then save {$a}' ;
$string['correctanswer'] = 'Correct Answer';
$string['correctanswerandfeedback'] = 'Correct Answer and Feedback';
$string['decodeverifyquestiontext'] = 'Decode and Verify the Question Text';
$string['editingmultianswer'] = 'Editing an Embedded answers (Cloze) question';
$string['layout'] = 'Layout';
$string['layouthorizontal'] = 'Horizontal row of radio-buttons';
$string['layoutselectinline'] = 'Dropdown menu in-line in the text';
$string['layoutundefined'] = 'Undefined layout';
$string['layoutvertical'] = 'Vertical column of radio buttons';
$string['multianswer'] = 'Embedded answers (Cloze)';
$string['multianswersummary'] = 'Questions of this type are very flexible, but can only be created by entering text containing special codes that create embedded multiple choice, short answers and numerical questions.';
$string['multianswer_help'] = '<p>This very flexible question type is similar to a 
popular format known as the Cloze format.</p>

<p>Questions consist of a passage of text (in Moodle format) that has various sub-questions 
embedded within it, including</p>
<ul>
  <li>short answers&nbsp;(SHORTANSWER or SA or MW), case is unimportant,</li>
  <li>short answers&nbsp;(SHORTANSWER_C or SAC or MWC), case must match,</li>
  <li>numerical answers (NUMERICAL or NM),</li>
  <li>multiple choice (MULTICHOICE or MC), represented as a dropdown menu in-line in the text</li>
  <li>multiple choice (MULTICHOICE_V or MCV), represented a vertical column of radio buttons, or</li>
  <li>multiple choice (MULTICHOICE_H or MCH), represented as a horizontal row of radio-buttons.</li>
</ul>
<p>There is currently no graphical interface to create these 
questions - you need to specify the question format using the text box or by 
importing them from external files.</p>';
$string['nooptionsforsubquestion'] = 'Unable to get options for question part # {$a->sub} (question->id={$a->id})';
$string['noquestions'] = 'The Cloze(multianswer) question "<strong>{$a}</strong>" does not contain any question';
$string['qtypenotrecognized'] = 'questiontype {$a} not recognized';
$string['questiondefinition'] = 'Question definition';
$string['questionnotfound'] = 'Unable to find question of question part #{$a}';
$string['questionsmissing'] = 'No valid questions, create at least one question';
$string['unknownquestiontypeofsubquestion'] = 'Unknown question type: {$a->type} of question part # {$a->sub}';
$string['questionsless'] = 'questions less than in the multtianswer question stored in the database';
$string['questiontypechanged'] = ' at least one question type has been changed. Did you add,delete or move a question ? Look ahead ';
$string['questioninquiz'] = '

<ul>
  <li>add or delete questions, </li>
  <li>change the questions order in the text,</li>
  <li>change their question type (numerical, shortanswer, multiple choice). </li></ul>
';
$string['questionsaveasedited'] = 'The question will be saved as edited';
$string['confirmquestionsaveasedited'] = 'I confirm that I want the question be saved as edited';