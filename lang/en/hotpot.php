<?PHP // $Id$ 
      // quiz.php - created with Moodle 1.2 development (2003111400)

//translators:  You might want to leave the first two items 'as is' in English
$string['modulename'] = 'Hot Potatoes Quiz';
$string['modulenameplural'] = 'Hot Potatoes Quizzes';

// for mod.html
$string['textsourcequiz'] = 'Get from quiz';
$string['textsourcefilename'] = 'Use file name';
$string['textsourcefilepath'] = 'Use file path';
$string['textsourcespecific'] = 'Specific text';

$string['alwaysopen'] = 'Always open';
$string['specifictime'] = 'Specific time';
$string['neverclosed'] = 'Never closed';

$string['displayhotpotnext'] = 'Display Hot Potatoes quiz next';
$string['displaycoursenext'] = 'Display Course page next';
$string['displayindexnext'] = 'Display HotPot index next';

$string['outputformat'] = 'Output format';
$string['outputformat_best'] = 'best';
$string['outputformat_v6_plus'] = 'v6+';
$string['outputformat_v6'] = 'v6';
$string['outputformat_v5_plus'] = 'v5+';
$string['outputformat_v5'] = 'v5';
$string['outputformat_v4'] = 'v4';
$string['outputformat_v3'] = 'v3';
$string['outputformat_flash'] = 'Flash';
$string['outputformat_mobile'] = 'mobile';

$string['navigation'] = 'Navigation';
$string['navigation_bar'] = 'Moodle navigation bar';
$string['navigation_frame'] = 'Moodle navigation frame';
$string['navigation_iframe'] = 'Embedded &lt;IFRAME&gt;';
$string['navigation_buttons'] = 'Hot Potatoes quiz buttons';
$string['navigation_give_up'] = 'A single &quot;Give Up&quot; button';
$string['navigation_none'] = 'None';

$string['giveup'] = 'Give Up';
$string['location'] = 'File location';
$string['addquizchain'] = 'Add quiz chain';
$string['updatequizchain'] = 'Update quiz chain';
$string['shownextquiz'] = 'Show next quiz';
$string['forceplugins'] = "Force media plugins";
$string['clickreporting'] = "Enable click reporting";

$string['resultssaved'] = 'Quiz results were saved';

// for edit.php and show.php
$string['filetype'] = 'File type';
$string['quiztype'] = 'Quiz type';
$string['showxmlsource'] = 'Show XML source';
$string['showxmltree'] = 'Show XML tree';
$string['showhtmlsource'] = 'Show HTML source';
$string['enterafilename'] = 'Please enter a file name';

// show.php (javascript messages, so must be double escaped. i.e. "it's" ==> 'it\\\'s' OR "it\\'s")
$string['copytoclipboard'] = 'Copy to Clipboard';
$string['copiedtoclipboard'] = 'The contents of this page have been copied to the clipboard';

// lib.php (status)
$string['noactivity'] = 'No activity';
$string['inprogress'] = 'In progress';
$string['timedout'] = 'Timed out';
$string['abandoned'] = 'Abandoned';
$string['completed'] = 'Completed';

// lib.php (feedback)
$string['studentfeedback'] = 'Student feedback';
$string['feedbacknone'] = 'None';
$string['feedbackwebpage'] = 'Web page';
$string['feedbackformmail'] = 'Feedback form';
$string['feedbackmoodleforum'] = 'Moodle forum';
$string['feedbackmoodlemessaging'] = 'Moodle messaging';

// lib.php (responses)
$string['correct'] = 'Correct';
$string['ignored'] = 'Ignored';
$string['wrong'] = 'Wrong';
$string['score'] = 'Score';
$string['weighting'] = 'Weighting';
$string['hints'] = 'Hints';
$string['clues'] = 'Clues';
$string['checks'] = 'Checks';
$string['penalties'] = 'Penalties';

// index.php
$string['regrade'] = 'Regrade';
$string['regradecheck'] = 'Do you really want to regrade &quot;$a&quot;?';
$string['regraderequired'] = 'Regrade required';

// report.php
$string['reportclick'] = 'Click trail report';

$string['reportcontent'] = 'Content';
$string['reportformat'] = 'Format';

$string['reportbutton'] = 'Generate report';

$string['thiscourse'] = 'This course';
$string['allmycourses'] = 'All my courses';

$string['attemptsall'] = 'All attempts';
$string['attemptsbest'] = 'Best attempt';
$string['attemptsfirst'] = 'First attempt';
$string['attemptslast'] = 'Last attempt';

$string['reportformathtml'] = 'HTML';
$string['reportformatexcel'] = 'Excel';
$string['reportformattext'] = 'Text';
$string['reportencoding'] = 'Encoding';
$string['reportwrapdata'] = 'Wrap data';
$string['reportshowlegend'] = 'Show legend';

$string['rawdetails'] = 'Raw attempt details';

// report/*/report.php
$string['average'] = 'Average';
$string['questionshort'] = 'Q-$a';

// report/default.php
$string['reportlegend'] = 'Legend';

// report/overview/report.php
$string['deleteabandoned'] = 'Delete abandoned';
$string['deleteabandonedcheck'] = 'Do you really want to delete all $a abandoned attempts?';

// report/click/report.php
$string['reportnottriedsymbol'] = '-';
$string['reportcorrectsymbol'] = 'O';
$string['reportwrongsymbol'] = 'X';

$string['reportcoursename'] = 'Course name';
$string['reportsectionnumber'] = 'Section';
$string['reportexercisenumber'] = 'Exercise';
$string['reportexercisename'] = 'Ex. name';
$string['reportexercisetype'] = 'Ex. type';
$string['reportnumberofquestions'] = "No. of q's";

$string['reportstudentid'] = 'Student id';
$string['reportlogindate'] = 'Login date';
$string['reportlogintime'] = 'Login time';
$string['reportlogofftime'] = 'Logoff time';

$string['reportattemptnumber'] = 'Attempt';
$string['reportattemptstart'] = 'Att. start';
$string['reportattemptfinish'] = 'Att. finish';

$string['reportclicknumber'] = 'Click';
$string['reportclicktime'] = 'Click time';
$string['reportclicktype'] = 'Click type';

$string['reportthisclick'] = '$a this click';
$string['reportsofar'] = '$a so far';

$string['reportquestionstried'] = "Q's tried";

$string['reportright'] = 'Right';
$string['reportwrong'] = 'Wrong';
$string['reportnottried'] = 'Not tried';

$string['reportanswers'] = 'Answers';
$string['reportchanges'] = 'Changes';
$string['reportchecks'] = 'Checks';
$string['reportclues'] = 'Clues';
$string['reportevents'] = 'Events';
$string['reporthints'] = 'Hints';
$string['reportshowanswer'] = 'Show answers';
$string['reportstatus'] = 'Status';

$string['reportrawscore'] = 'Raw score';
$string['reportmaxscore'] = 'Max score';
$string['reportpercentscore'] = '%% Score';

$string['reporthotpotscore'] = 'Hotpot score';

// review.php
$string['noresponses'] = 'No information about individual questions and responses was found.';
$string['reporttimerecorded'] = 'Responses recorded';

// config.html
$string['configshowtimes'] = 'Should the time taken to process records be displayed in listings and reports? This is only really necessary of you are are trying to find out why your server is running slowly.';
$string['configexcelencodings'] = 'A list of encodings, separated by commas, that can be used to force report values into a specific encoding for spreadsheet programs. For example, Microsoft Excel requires the &quot;SJIS&quot; encoding for Japanese';

// error messages (lib.php)
$string['error_nofilename'] = 'Please enter a file name';
$string['error_notfileorfolder'] = '&quot;$a&quot; is not file or folder';
$string['error_nocourseorfilename'] = 'Could not create XML tree: missing course or file name';
$string['error_couldnotopensourcefile'] = 'Could not open the source file \\"$a\\"';
$string['error_couldnotopenfolder'] = 'Could not access the folder &quot;$a&quot;';
$string['error_couldnotopentemplate'] = 'Could not open template for &quot;$a&quot; format';
$string['error_noquizzesfound'] = 'No Hot Potatoes quizzes found';
$string['error_nofeedbackurlwebpage'] = 'Please enter a URL for the webpage';
$string['error_nofeedbackurlformmail'] = 'Please enter a URL for the form processing script';

// error messages (attempt.php)
$string['error_invalidquiztype'] = 'Quiz type is missing or invalid';
?>
