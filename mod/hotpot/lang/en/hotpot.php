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
 * Strings for component 'hotpot', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   hotpot
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['abandoned'] = 'Abandoned';
$string['addquizchain'] = 'Add quiz chain';
$string['allmycourses'] = 'All my courses';
$string['allowreview'] = 'Allow review';
$string['allowreview_help'] = 'If enabled, students may review their quiz attempts after the quiz is closed.';
$string['allusers'] = 'All users';
$string['alwaysopen'] = 'Always open';
$string['attemptsall'] = 'All attempts';
$string['attemptsbest'] = 'Best attempt';
$string['attemptsfirst'] = 'First attempt';
$string['attemptslast'] = 'Last attempt';
$string['average'] = 'Average';
$string['cannotfindmethod'] = 'Template block expand method not found: ({$a})';
$string['clickreporting'] = 'Enable click reporting';
$string['clickreporting_help'] = 'If enabled, a separate record is kept each time a "hint", "clue" or "check" button is clicked. This allows the teacher to see a very detailed report showing the state of the quiz at each click. Otherwise, only one record per attempt at a quiz is kept.';
$string['clues'] = 'Clues';
$string['completed'] = 'Completed';
$string['configexcelencodings'] = 'A list of encodings, separated by commas, that can be used to force report values into a specific encoding for spreadsheet programs. For example, Microsoft Excel requires the &quot;SJIS&quot; encoding for Japanese';
$string['configshowtimes'] = 'Should the time taken to process records be displayed in listings and reports? This is only really necessary if you are trying to find out why your server is running slowly.';
$string['copiedtoclipboard'] = 'The contents of this page have been copied to the clipboard';
$string['copytoclipboard'] = 'Copy to Clipboard';
$string['correct'] = 'Correct';
$string['deleteabandoned'] = 'Delete abandoned';
$string['deleteabandonedcheck'] = 'Do you really want to delete all {$a} abandoned attempts?';
$string['deleteallattempts'] = 'Delete all attempts';
$string['displaycoursenext'] = 'Display Course page next';
$string['displayhotpotnext'] = 'Display Hot Potatoes quiz next';
$string['displayindexnext'] = 'Display HotPot index next';
$string['enterafilename'] = 'Please enter a file name';
$string['error_couldnotopenfolder'] = 'Could not access the folder &quot;{$a}&quot;';
$string['error_couldnotopensourcefile'] = 'Could not open the source file "{$a}"';
$string['error_couldnotopentemplate'] = 'Could not open template for &quot;{$a}&quot; format';
$string['error_invalidquiztype'] = 'Quiz type is missing or invalid';
$string['error_nocourseorfilename'] = 'Could not create XML tree: missing course or file name';
$string['error_nofeedbackurlformmail'] = 'Please enter a URL for the form processing script';
$string['error_nofeedbackurlwebpage'] = 'Please enter a URL for the webpage';
$string['error_nofilename'] = 'Please enter a file name';
$string['error_noquizzesfound'] = 'No Hot Potatoes quizzes found';
$string['error_notfileorfolder'] = '&quot;{$a}&quot; is not file or folder';
$string['excelencodings'] = 'Excel encodings';
$string['feedbackformmail'] = 'Feedback form';
$string['feedbackmoodleforum'] = 'Moodle forum';
$string['feedbackmoodlemessaging'] = 'Moodle messaging';
$string['feedbacknone'] = 'None';
$string['feedbackwebpage'] = 'Web page';
$string['filetype'] = 'File type';
$string['forceplugins'] = 'Force media plugins';
$string['forceplugins_help'] = 'If enabled, Moodle-compatible media players will play files such as avi, mpeg, mpg, mp3, mov and wmv. Otherwise, Moodle will not change the settings of any media players in the quiz.';
$string['forceplugins_link'] = 'mod/hotpot/mod';
$string['giveup'] = 'Give Up';
$string['hints'] = 'Hints';
$string['hotpotadministration'] = 'Hot Potatoes quiz administration';
$string['hotpot:attempt'] = 'Attempt a quiz';
$string['hotpotcloses'] = 'Hot Potatoes quiz closes';
$string['hotpot:deleteattempt'] = 'Delete quiz attempts';
$string['hotpot:grade'] = 'Modify grades';
$string['hotpotopens'] = 'Hot Potatoes quiz opens';
$string['hotpot:view'] = 'Use quiz';
$string['hotpot:viewreport'] = 'View reports';
$string['checks'] = 'Checks';
$string['ignored'] = 'Ignored';
$string['inprogress'] = 'In progress';
$string['invalidattemptid'] = 'Attempt ID was incorrect';
$string['invalidhotpotid'] = 'hotpot ID was incorrect';
$string['location'] = 'File location';
$string['maxgrade'] = 'Maximum grade';
$string['maxgrade_help'] = 'This setting specifies the grade that all scores are scaled to. For example, if the quiz is worth 20% of the whole course, the maximum grade would be set to 20.';
$string['modulename'] = 'Hot Potatoes Quiz';
$string['modulename_help'] = 'The HotPot module enables the teacher to include Hot Potatoes quizzes in the course. Each attempt is automatically marked, and reports are available which show how individual questions were answered and some statistical trends in the scores.';
$string['modulename_link'] = 'hotpot';
$string['modulenameplural'] = 'Hot Potatoes Quizzes';
$string['navigation'] = 'Navigation';
$string['navigation_help'] = 'This setting determines the navigation used in the quiz:

* Moodle navigation bar - The Moodle navigation bar will be displayed in the same window as the quiz at the top of the page
* Moodle navigation frame - The Moodle navigation bar will be displayed in a separate frame at the top of the quiz
* Embedded IFRAME - The Moodle navigation bar will be displayed in the same window as the quiz and the quiz will be embedded in an IFRAME
* Hot Potatoes quiz buttons - The quiz will be displayed with the navigation buttons, if any, defined in the quiz
* A single "Give Up" button - The quiz will be displayed with a single "Give Up" button at the top of the page
* None - The quiz will be displayed with no navigation aids, so when all questions have been answered correctly, depending on the "Show next quiz?" setting, Moodle will either return to the course page or display the next quiz';
$string['navigation_bar'] = 'Moodle navigation bar';
$string['navigation_buttons'] = 'Hot Potatoes quiz buttons';
$string['navigation_frame'] = 'Moodle navigation frame';
$string['navigation_give_up'] = 'A single &quot;Give Up&quot; button';
$string['navigation_iframe'] = 'Embedded IFRAME';
$string['navigation_none'] = 'None';
$string['neverclosed'] = 'Never closed';
$string['noactivity'] = 'No activity';
$string['noresponses'] = 'No information about individual questions and responses was found.';
$string['notyourattempt'] = 'This is not your attempt!';
$string['outputformat'] = 'Output format';
$string['outputformat_help'] = 'This setting specifies the format to display the quiz.

* Best - The best format for the browser
* v6+ - Drag and drop format for v6+ browsers
* v6 - Format for v6 browsers';
$string['outputformat_best'] = 'best';
$string['outputformat_flash'] = 'Flash';
$string['outputformat_mobile'] = 'mobile';
$string['outputformat_v3'] = 'v3';
$string['outputformat_v4'] = 'v4';
$string['outputformat_v5'] = 'v5';
$string['outputformat_v5_plus'] = 'v5+';
$string['outputformat_v6'] = 'v6';
$string['outputformat_v6_plus'] = 'v6+';
$string['penalties'] = 'Penalties';
$string['questionshort'] = 'Q-{$a}';
$string['quiztype'] = 'Quiz type';
$string['quizunavailable'] = 'Quiz is unavailable at the moment';
$string['rawdetails'] = 'Raw attempt details';
$string['regrade'] = 'Regrade';
$string['regradecheck'] = 'Do you really want to regrade &quot;{$a}&quot;?';
$string['regraderequired'] = 'Regrade required';
$string['removegradeitem'] = 'Remove grade item';
$string['reportanswers'] = 'Answers';
$string['reportattemptfinish'] = 'Att. finish';
$string['reportattemptnumber'] = 'Attempt';
$string['reportattemptstart'] = 'Att. start';
$string['reportbutton'] = 'Generate report';
$string['reportclick'] = 'Click trail report';
$string['reportclicknumber'] = 'Click';
$string['reportclicktime'] = 'Click time';
$string['reportclicktype'] = 'Click type';
$string['reportclues'] = 'Clues';
$string['reportcontent'] = 'Content';
$string['reportcontent_help'] = 'There are 4 report types:

* Overview - A list of all attempts
* Simple statistics - A list of all attempts with average scores for individual questions and for the complete quiz
* Detailed statistics - Full details of all attempts together with a responses table and an item analysis table
* Click trail report (only available if click reporting is enabled) - Full details of every click by every student in all attempts';
$string['reportcontent_link'] = 'mod/hotpot/report';
$string['reportcorrectsymbol'] = 'O';
$string['reportcoursename'] = 'Course name';
$string['reportencoding'] = 'Encoding';
$string['reportevents'] = 'Events';
$string['reportexercisename'] = 'Ex. name';
$string['reportexercisenumber'] = 'Exercise';
$string['reportexercisetype'] = 'Ex. type';
$string['reportformat'] = 'Format';
$string['reportformat_help'] = 'Reports are available in HTML, Excel or text formats with the option to wrap data (to fit into table cells) and to have questions and answers represented by letters together with a legend showing which letters represent which questions or answers.';
$string['reportformatexcel'] = 'Excel';
$string['reportformathtml'] = 'HTML';
$string['reportformattext'] = 'Text';
$string['reporthints'] = 'Hints';
$string['reporthotpotscore'] = 'Hotpot score';
$string['reportchanges'] = 'Changes';
$string['reportchecks'] = 'Checks';
$string['reportlegend'] = 'Legend';
$string['reportlogindate'] = 'Login date';
$string['reportlogintime'] = 'Login time';
$string['reportlogofftime'] = 'Logoff time';
$string['reportmaxscore'] = 'Max score';
$string['reportnottried'] = 'Not tried';
$string['reportnottriedsymbol'] = '-';
$string['reportnumberofquestions'] = 'No. of q\'s';
$string['reportpercentscore'] = '% Score';
$string['reportquestionstried'] = 'Q\'s tried';
$string['reportrawscore'] = 'Raw score';
$string['reportright'] = 'Right';
$string['reportsectionnumber'] = 'Section';
$string['reportshowanswer'] = 'Show answers';
$string['reportshowlegend'] = 'Show legend';
$string['reportsofar'] = '{$a} so far';
$string['reportstatus'] = 'Status';
$string['reportstudentid'] = 'Student id';
$string['reportthisclick'] = '{$a} this click';
$string['reporttimerecorded'] = 'Responses recorded';
$string['reportwrapdata'] = 'Wrap data';
$string['reportwrong'] = 'Wrong';
$string['reportwrongsymbol'] = 'X';
$string['resultssaved'] = 'Quiz results were saved';
$string['score'] = 'Score';
$string['showhtmlsource'] = 'Show HTML source';
$string['shownextquiz'] = 'Show next quiz';
$string['shownextquiz_help'] = 'This setting determines whether, on finishing a quiz, Moodle will return to the course page or display the next quiz (if there is one).';
$string['showtimes'] = 'Show processing times';
$string['showxmlsource'] = 'Show XML source';
$string['showxmltree'] = 'Show XML tree';
$string['specifictime'] = 'Specific time';
$string['studentfeedback'] = 'Student feedback';
$string['studentfeedback_help'] = 'If enabled, a link to a pop-up feedback window will be displayed whenever the student clicks on the "Check" button. The feedback window allows students to send feedback to the teacher in 4 possible ways:

* Web page (requires URL of the web page, for example http://myserver.com/feedbackform.html)
* Feedback form (requires URL of the form script, for example http://myserver.com/cgi-bin/formmail.pl)
* Moodle forum - The forum index for the course will be displayed
* Moodle messaging - The Moodle instant messaging window will be displayed. If the course has several teachers, the student will be prompted to select a teacher before the messaging window appears.';
$string['textsourcefilename'] = 'Use file name';
$string['textsourcefilepath'] = 'Use file path';
$string['textsourcequiz'] = 'Get from quiz';
$string['textsourcespecific'] = 'Specific text';
$string['thiscourse'] = 'This course';
$string['timedout'] = 'Timed out';
$string['unknownreport'] = 'Report not known ({$a})';
$string['updatequizchain'] = 'Update quiz chain';
$string['updatequizchain_help'] = 'If enabled, if this quiz is part of a chain of Hot Potatoes quizzes, then all quizzes in the chain will be assigned identical settings to the current quiz. Otherwise, only the current quiz will be updated.';
$string['weighting'] = 'Weighting';
$string['wrong'] = 'Wrong';
