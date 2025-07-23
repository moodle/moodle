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
 * Strings for component 'quiz', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accessnoticesheader'] = 'You can preview this quiz, but if this were a real attempt, you would be blocked because:';
$string['action'] = 'Action';
$string['activityoverview'] = 'You have quizzes that are due';
$string['adaptive'] = 'Adaptive mode';
$string['adaptive_help'] = 'If enabled, multiple responses to a question are allowed within the same attempt at the quiz. So for example if a response is marked as incorrect, the student will be allowed to try again immediately. However, depending on the "Apply penalties" setting, a penalty will usually be subtracted for each wrong attempt.';
$string['add'] = 'Add';
$string['addaquestion'] = 'a new question';
$string['addasection'] = 'a new section heading';
$string['addarandomquestion'] = 'a random question';
$string['addarandomquestion_help'] = 'When a random question is added, it results in a randomly-chosen question from the category being inserted into the quiz. This means that different students are likely to get a different selection of questions, and when a quiz allows multiple attempts then each attempt is likely to contain a new selection of questions.';
$string['addarandomquestion_success'] = 'Random questions have been added';
$string['addarandomselectedquestion'] = 'Add a random selected question ...';
$string['adddescriptionlabel'] = 'Add a description item';
$string['addingquestion'] = 'Adding a question';
$string['addingquestions'] = '<p>This side of the page is where you manage your database of questions. Questions are stored in categories to help you keep them organised, and can be used by any quiz in your course or even other courses if you choose to \'publish\' them.</p>
<p>After you select or create a question category you will be able to create or edit questions. You can select any of these questions to add to your quiz over on the other side of this page.</p>';
$string['addmoreoverallfeedbacks'] = 'Add {no} more feedback fields';
$string['addnewgroupoverride'] = 'Add group override';
$string['addnewpagesafterselected'] = 'Add new pages after selected questions';
$string['addnewquestionsqbank'] = 'Add questions to the category {$a->catname}: {$a->link}';
$string['addnewuseroverride'] = 'Add user override';
$string['addpagebreak'] = 'Add page break';
$string['addpagehere'] = 'Add page here';
$string['addquestion'] = 'Add question';
$string['addquestionfrombankatend'] = 'Add from the question bank at the end';
$string['addquestionfrombanktopage'] = 'Add from the question bank to page {$a}';
$string['addquestions'] = 'Add questions';
$string['addquestionstoquiz'] = 'Add questions to current quiz';
$string['addrandom'] = 'Add {$a} random questions';
$string['addrandomfromcategory'] = 'Add random questions from category:';
$string['addrandomquestion'] = 'Add random question';
$string['addrandomquestionatend'] = 'Add a random question at the end';
$string['addrandomquestiontopage'] = 'Add a random question to page {$a}';
$string['addrandomquestiontoquiz'] = 'Add a random question to quiz {$a}';
$string['addrandom1'] = '<< Add';
$string['addrandom2'] = 'random questions';
$string['addselectedquestionstoquiz'] = 'Add selected questions to the quiz';
$string['addselectedtoquiz'] = 'Add selected to quiz';
$string['addtoquiz'] = 'Add to quiz';
$string['affectedstudents'] = 'Affected {$a}';
$string['aftereachquestion'] = 'After adding each question';
$string['afternquestions'] = 'After adding {$a} questions';
$string['age'] = 'age';
$string['allattempts'] = 'All attempts';
$string['allinone'] = 'Unlimited';
$string['allowreview'] = 'Allow review';
$string['alreadysubmitted'] = 'It is likely that you have already submitted this attempt';
$string['alternativeunits'] = 'Alternative units';
$string['alwaysavailable'] = 'Always available';
$string['analysisoptions'] = 'Analysis options';
$string['analysistitle'] = 'Item analysis table';
$string['answer'] = 'Answer';
$string['answered'] = 'Answered';
$string['answerhowmany'] = 'One or multiple answers?';
$string['answers'] = 'Answers';
$string['answersingleno'] = 'Multiple answers allowed';
$string['answersingleyes'] = 'One answer only';
$string['answertoolong'] = 'Answer too long after line {$a} (255 char. max)';
$string['anytags'] = 'Any tags';
$string['aon'] = 'AON format';
$string['areyousureremoveselected'] = 'Are you sure you want to remove all the selected questions?';
$string['asshownoneditscreen'] = 'As shown on the edit screen';
$string['attempt'] = 'Attempt {$a}';
$string['attemptalreadyclosed'] = 'This attempt has already been finished.';
$string['attemptclosed'] = 'Attempt has not closed yet';
$string['attemptduration'] = 'Duration';
$string['attemptedon'] = 'Attempted on';
$string['attempterror'] = 'You are not allowed to attempt this quiz at this time because: {$a}';
$string['attempterrorinvalid'] = 'Invalid quiz attempt ID';
$string['attempterrorcontentchange'] = 'This quiz preview no longer exists. (When a quiz is edited, any in-progress previews are automatically deleted.)';
$string['attempterrorcontentchangeforuser'] = 'This quiz attempt no longer exists.';
$string['attemptfirst'] = 'First attempt';
$string['attemptgradeddelay'] = 'Attempt graded notification delay';
$string['attemptgradeddelay_desc'] = 'A delay is applied before sending attempt graded notifications to allow time for the teacher to edit the grade.';
$string['attemptincomplete'] = 'That attempt (by {$a}) is not yet completed.';
$string['attemptlast'] = 'Last attempt';
$string['attemptnumber'] = 'Attempt';
$string['attemptquiznow'] = 'Attempt quiz now';
$string['attemptquiz'] = 'Attempt quiz';
$string['attemptreviewtitle'] = '{$a}: Attempt review';
$string['attemptreviewtitlepaged'] = '{$a->name}: Attempt review (page {$a->currentpage} of {$a->totalpages})';
$string['attempts'] = 'Attempts';
$string['attempts_help'] = 'The total number of attempts allowed (not the number of extra attempts).';
$string['attemptsallowed'] = 'Attempts allowed';
$string['attemptselection'] = 'Select which attempts to analyze per user:';
$string['attemptsexist'] = 'You can no longer add or remove questions.';
$string['attemptsnum'] = 'Attempts: {$a}';
$string['attemptsnumstudents'] = 'Students: {$a->studentsnum} Attempts: {$a->total}';
$string['attemptsnumthisgroup'] = 'Attempts: {$a->total} ({$a->group} from this group)';
$string['attemptsnumyourgroups'] = 'Attempts: {$a->total} ({$a->group} from your groups)';
$string['attemptsonly'] = 'Show only students with attempts';
$string['attemptstate'] = 'Status';
$string['attemptstillinprogress'] = 'Attempt still in progress';
$string['attemptsummarytitle'] = '{$a}: Attempt summary';
$string['attemptsunlimited'] = 'Unlimited attempts';
$string['attempttitle'] = '{$a}';
$string['attempttitlepaged'] = '{$a->name} (page {$a->currentpage} of {$a->totalpages})';
$string['autosaveperiod'] = 'Auto-save delay';
$string['autosaveperiod_desc'] = 'Responses can be saved automatically during quiz attempts. The responses are saved whenever one is changed, and then after this delay. There is a trade-off: a shorter delay increases the server load, but reduces the chance that students lose their work. If you are going to make this delay much shorter, you should change the value gradually and monitor the server load. If the load gets too high, make the delay longer again. Setting the delay to 0 turns off auto-saving.';
$string['back'] = 'Back to preview question';
$string['backtocourse'] = 'Back to the course';
$string['backtoquestionlist'] = 'Back to question list';
$string['backtoquiz'] = 'Back to quiz editing';
$string['bestgrade'] = 'Best grade';
$string['bothattempts'] = 'Show students with and without attempts';
$string['browsersecurity'] = 'Browser security';
$string['browsersecurity_help'] = 'If "Full screen pop-up with some JavaScript security" is selected,

* The quiz will only start if the student has a JavaScript-enabled web-browser
* The quiz appears in a full screen popup window that covers all the other windows and has no navigation controls
* Students are prevented, as far as is possible, from using facilities like copy and paste';
$string['cachedef_overrides'] = 'User and group override information';
$string['calculated'] = 'Calculated';
$string['calculatedquestion'] = 'Calculated question not supported at line {$a}. The question will be ignored';
$string['cannotcreatepath'] = 'Path cannot be created ({$a})';
$string['cannoteditafterattempts'] = 'You cannot add or remove questions because this quiz has been attempted. ({$a})';
$string['cannotfindprevattempt'] = 'Cannot find previous attempt to build on.';
$string['cannotfindquestionregard'] = 'Failed to get questions for regrading!';
$string['cannotinsert'] = 'Cannot insert question';
$string['cannotinsertrandomquestion'] = 'Could not insert new random question!';
$string['cannotloadquestion'] = 'Could not load question options';
$string['cannotloadtypeinfo'] = 'Unable to load questiontype specific question information';
$string['cannotopen'] = 'Cannot open export file ({$a})';
$string['cannotremoveallsectionslots'] = 'You have selected all questions under the \'{$a}\' section heading. It is not allowed to remove all questions under a section heading.';
$string['cannotremoveslots'] = 'Cannot remove questions';
$string['cannotrestore'] = 'Could not restore question sessions';
$string['cannotreviewopen'] = 'You cannot review this attempt, it is still open.';
$string['cannotsavelayout'] = 'Could not save layout';
$string['cannotsavenumberofquestion'] = 'Could not save number of questions per page';
$string['cannotsavequestion'] = 'Cannot save question list';
$string['cannotsetgrade'] = 'Could not set a new maximum grade for the quiz';
$string['cannotsetsumgrades'] = 'Failed to set sumgrades';
$string['cannotstartgradesmismatch'] = 'Cannot start an attempt at this quiz. The quiz is set to be graded out of {$a->grade}, but none of the questions in the quiz have a grade. This can be fixed on the \'Edit quiz\' page.';
$string['cannotstartmissingquestion'] = 'Cannot start an attempt at this quiz. The quiz definition includes a question that does not exist.';
$string['cannotstartnoquestions'] = 'Cannot start an attempt at this quiz. The quiz has not been set up yet. No questions have been added.';
$string['cannotwrite'] = 'Cannot write to export file ({$a})';
$string['canredoquestions'] = 'Allow redo within an attempt';
$string['canredoquestions_desc'] = 'If enabled, after finishing attempting a question, a \'Try another question like this one\' button is displayed. This allows for a similar question (selected randomly) to be attempted, or the same question again, without the entire quiz attempt having to be submitted and another attempt started. This option is useful for practice quizzes.

This setting only affects questions and behaviours (such as immediate feedback or interactive with multiple tries) where it is possible to finish a question before the attempt is submitted.';
$string['canredoquestions_help'] = 'If enabled, after finishing attempting a question, a \'Try another question like this one\' button is displayed. This allows for a similar question (selected randomly) to be attempted, or the same question again, without the entire quiz attempt having to be submitted and another attempt started. This option is useful for practice quizzes.

This setting only affects questions and behaviours (such as immediate feedback or interactive with multiple tries) where it is possible to finish a question before the attempt is submitted.';
$string['canredoquestionsyes'] = 'Yes, provide the option to try another question';
$string['caseno'] = 'No, case is unimportant';
$string['casesensitive'] = 'Case sensitivity';
$string['caseyes'] = 'Yes, case must match';
$string['categoryadded'] = 'The category \'{$a}\' was added';
$string['categorydeleted'] = 'The category \'{$a}\' was deleted';
$string['categorynoedit'] = 'You do not have editing privileges in the category \'{$a}\'.';
$string['categoryupdated'] = 'The category was successfully updated';
$string['close'] = 'Close window';
$string['closed'] = 'Closed';
$string['closebeforeopen'] = 'The close date must be after the open date.';
$string['closepreview'] = 'Close preview';
$string['closereview'] = 'Close review';
$string['comment'] = 'Comment';
$string['commentorgrade'] = 'Make comment or override grade';
$string['comments'] = 'Comments';
$string['completedon'] = 'Completed';
$string['completiondetail:minattempts'] = 'Make attempts: {$a}';
$string['completiondetail:passorexhaust'] = 'Receive a pass grade or complete all available attempts';
$string['completionminattempts'] = 'Minimum attempts';
$string['completionminattemptsdesc'] = 'Minimum number of attempts required: {$a}';
$string['completionminattemptserror'] = 'Minimum number of attempts must be lower or equal to attempts allowed.';
$string['completionpassorattemptsexhausteddesc'] = 'Student must achieve a passing grade, or exhaust all available attempts to complete this activity';
$string['completionattemptsexhausted'] = 'Passing grade or all available attempts completed';
$string['completionattemptsexhausted_help'] = 'Mark quiz complete when the student has exhausted the maximum number of attempts.';
$string['configadaptive'] = 'If you choose Yes for this option then the student will be allowed multiple responses to a question even within the same attempt at the quiz.';
$string['configattemptsallowed'] = 'Restriction on the number of attempts students are allowed at the quiz.';
$string['configdecimaldigits'] = 'Number of digits that should be shown after the decimal point when displaying grades.';
$string['configdecimalplaces'] = 'Number of digits that should be shown after the decimal point when displaying grades for the quiz.';
$string['configdecimalplacesquestion'] = 'Number of digits that should be shown after the decimal point when displaying the mark for individual questions.';
$string['configdelaylater'] = 'If you set a time delay here, the student cannot start their third, fourth, ... attempt until this much time has passed since the end of their previous attempt.';
$string['configdelay1'] = 'If you set a time delay, then a student has to wait for that time before they can attempt a quiz after the first attempt.';
$string['configdelay1st2nd'] = 'If you set a time delay here, the student cannot start their second attempt until this much time has passed since the end of their first attempt.';
$string['configdelay2'] = 'If you set a time delay here, then a student has to wait for that time before they can attempt their third or later attempts.';
$string['configeachattemptbuildsonthelast'] = 'If multiple attempts are allowed then each new attempt contains the results of the previous attempt.';
$string['configgrademethod'] = 'When multiple attempts are allowed, which method should be used to calculate the student\'s final grade for the quiz.';
$string['configintro'] = 'The values you set here define the default values that are used in the settings form when you create a new quiz. You can also configure which quiz settings are considered advanced.';
$string['configmaximumgrade'] = 'The default grade that the quiz grade is scaled to be out of.';
$string['confignewpageevery'] = 'When adding questions to the quiz page breaks will automatically be inserted according to the setting you choose here.';
$string['confignavmethod'] = 'In Free navigation, questions may be answered in any order using navigation. In Sequential, questions must be answered in strict sequence.';
$string['configoutcomesadvanced'] = 'If this option is turned on, then the Outcomes on the quiz editing form are advanced settings.';
$string['configpenaltyscheme'] = 'Penalty subtracted for each wrong response in adaptive mode.';
$string['configpopup'] = 'Force the attempt to open in a popup window, and use JavaScript tricks to try to restrict copy and paste, etc. during quiz attempts.';
$string['configrequirepassword'] = 'Students must enter this password before they can attempt the quiz.';
$string['configrequiresubnet'] = 'Students can only attempt the quiz from these computers.';
$string['configreviewoptions'] = 'These options control what information users can see when they review a quiz attempt or look at the quiz reports.';
$string['configshowblocks'] = 'Show blocks during quiz attempts.';
$string['configshowuserpicture'] = 'Show the user\'s picture on screen during attempts.';
$string['configshufflewithin'] = 'If you enable this option, then the parts making up the individual questions will be randomly shuffled each time a student starts an attempt at this quiz, provided the option is also enabled in the question settings.';
$string['configtimelimit'] = 'Default time limit for quizzes in minutes. 0 mean no time limit.';
$string['configtimelimitsec'] = 'Default time limit for quizzes in seconds. 0 mean no time limit.';
$string['configurerandomquestion'] = 'Configure question';
$string['confirmclose'] = 'Once you submit your answers, you won’t be able to change them.';
$string['confirmremovequestion'] = 'Are you sure you want to remove this {$a} question?';
$string['confirmremovesectionheading'] = 'Are you sure you want to remove the \'{$a}\' section heading?';
$string['confirmserverdelete'] = 'Are you sure you want to remove the server <b>{$a}</b> from the list?';
$string['connectionok'] = 'Network connection restored. You may continue safely.';
$string['connectionerror'] = 'Network connection lost. (Autosave failed).

Make a note of any responses entered on this page in the last few minutes, then try to re-connect.

Once connection has been re-established, your responses should be saved and this message will disappear.';
$string['containercategorycreated'] = 'This category has been created to store all the original categories moved to site level due to the causes specified below.';
$string['continueattemptquiz'] = 'Continue your attempt';
$string['continuepreview'] = 'Continue the last preview';
$string['copyingfrom'] = 'Creating a copy of the question \'{$a}\'';
$string['copyingquestion'] = 'Copying a question';
$string['correct'] = 'Correct';
$string['correctanswer'] = 'Correct answer';
$string['correctanswerformula'] = 'Correct answer formula';
$string['correctansweris'] = 'Correct answer: {$a}';
$string['correctanswerlength'] = 'Significant figures';
$string['correctanswers'] = 'Correct answers';
$string['correctanswershows'] = 'Correct answer shows';
$string['corrresp'] = 'Correct response';
$string['countdown'] = 'Countdown';
$string['countdownfinished'] = 'The quiz is closing, you should submit your answers now.';
$string['countdowntenminutes'] = 'The quiz will be closing in ten minutes.';
$string['coursetestmanager'] = 'Course Test Manager format';
$string['createcategoryandaddrandomquestion'] = 'Create category and add random question';
$string['createfirst'] = 'You must create some short-answer questions first.';
$string['createmultiple'] = 'Add several random questions to quiz';
$string['createnewquestion'] = 'Create new question';
$string['createquestionandadd'] = 'Create a new question and add it to the quiz.';
$string['custom'] = 'Custom format';
$string['dataitemneed'] = 'You need to add at least one set of data items to get a valid question';
$string['datasetdefinitions'] = 'Reusable dataset definitions for category {$a}';
$string['datasetnumber'] = 'Number';
$string['daysavailable'] = 'Days available';
$string['decimaldigits'] = 'Decimal digits in grades';
$string['decimalplaces'] = 'Decimal places in grades';
$string['decimalplaces_help'] = 'This setting specifies the number of digits shown after the decimal point when displaying grades. It only affects the display of grades, not the grades stored in the database, nor the internal calculations, which are carried out to full accuracy.';
$string['decimalplacesquestion'] = 'Decimal places in marks for questions';
$string['decimalplacesquestion_help'] = 'The number of digits shown after the decimal point when displaying the marks for individual questions.';
$string['decimalpoints'] = 'Decimal places';
$string['default'] = 'Default';
$string['defaultgrade'] = 'Default question grade';
$string['defaultinfo'] = 'The default category for questions.';
$string['delaylater'] = 'Enforced delay between later attempts';
$string['delaylater_help'] = 'If enabled, a student must wait for the specified time to elapse before attempting the quiz a third time and any subsequent times.';
$string['delay1'] = 'Time delay between first and second attempt';
$string['delay1st2nd'] = 'Enforced delay between 1st and 2nd attempts';
$string['delay1st2nd_help'] = 'If enabled, a student must wait for the specified time to elapse before being able to attempt the quiz a second time.';
$string['delay2'] = 'Time delay between later attempts';
$string['deleteattemptcheck'] = 'Are you absolutely sure you want to completely delete these attempts?';
$string['deleteselected'] = 'Delete selected';
$string['deletingquestionattempts'] = 'Deleting question attempts';
$string['description'] = 'Description';
$string['disabled'] = 'Disabled';
$string['displayoptions'] = 'Display options';
$string['donotuseautosave'] = 'Do not use auto-save';
$string['download'] = 'Click to download the exported category file';
$string['downloadextra'] = '(file is also stored in the course files in the /backupdata/quiz folder)';
$string['dragtoafter'] = 'After {$a}';
$string['dragtostart'] = 'To the start';
$string['duplicateresponse'] = 'This submission has been ignored because you gave an equivalent answer earlier.';
$string['eachattemptbuildsonthelast'] = 'Each attempt builds on the last';
$string['eachattemptbuildsonthelast_help'] = 'If multiple attempts are allowed and this setting is enabled, each new quiz attempt will contain the results of the previous attempt. This allows a quiz to be completed over several attempts.';
$string['edit_slotdisplaynumber_hint'] = 'Edit question number (maximum 16 characters)';
$string['edit_slotdisplaynumber_label'] = 'New value for {$a}';
$string['editcategories'] = 'Edit categories';
$string['editcategory'] = 'Edit category';
$string['editcatquestions'] = 'Edit category questions';
$string['editingquestion'] = 'Editing a question';
$string['editingquiz'] = 'Editing quiz';
$string['editingquiz_help'] = 'When creating a quiz, the main concepts are:

* The quiz, containing questions over one or more pages
* The question bank, which stores copies of all questions organised into categories
* Random questions -  A student gets different questions each time they attempt the quiz and different students can get different questions';
$string['editingquiz_link'] = 'mod/quiz/edit';
$string['editingquizx'] = 'Editing quiz: {$a}';
$string['editmaxmark'] = 'Edit maximum mark';
$string['editoverride'] = 'Edit override';
$string['editqcats'] = 'Edit questions categories';
$string['editquestion'] = 'Edit question';
$string['editquestions'] = 'Edit questions';
$string['editquiz'] = 'Edit quiz';
$string['editquizquestions'] = 'Edit quiz questions';
$string['emailconfirmbody'] = 'Hi {$a->username},

Thank you for submitting your answers to \'{$a->quizname}\' in course \'{$a->coursename}\' at {$a->submissiontime}.

This message confirms that your answers have been saved.

You can access this quiz at {$a->quizurl}.';
$string['emailconfirmbodyautosubmit'] = 'Hi {$a->username},

The time for the quiz \'{$a->quizname}\' in the course \'{$a->coursename}\' expired. Your answers were submitted automatically at {$a->submissiontime}.

This message confirms that your answers have been saved.

You can access this quiz at {$a->quizurl}.';
$string['emailconfirmsmall'] = 'Thank you for submitting your answers to \'{$a->quizname}\'';
$string['emailconfirmautosubmitsmall'] = 'Thank you for submitting your answers to \'{$a->quizname}\'';
$string['emailconfirmsubject'] = 'Submission confirmation: {$a->quizname}';
$string['emailnotifybody'] = 'Hi {$a->username},

{$a->studentname} has completed \'{$a->quizname}\' ({$a->quizurl}) in course \'{$a->coursename}\'.

You can review this attempt at {$a->quizreviewurl}.';
$string['emailnotifysmall'] = '{$a->studentname} has completed {$a->quizname}. See {$a->quizreviewurl}';
$string['emailnotifysubject'] = '{$a->studentname} has completed {$a->quizname}';
$string['emailmanualgradedbody'] = 'Hi {$a->studentname},

Your answers to \'{$a->quizname}\' in course \'{$a->coursename}\' at {$a->attempttimefinish} have now been graded.

You will be able to view your score and feedback by visiting \'{$a->quizurl}\' and reviewing your attempt.';

$string['emailmanualgradedsubject'] = 'Your attempt at {$a->quizname} has been graded';
$string['emailoverduebody'] = 'Hi {$a->studentname},

You started an attempt at \'{$a->quizname}\' in course \'{$a->coursename}\', but you never submitted it. It should have been submitted by {$a->attemptduedate}.

If you would still like to submit this attempt, please go to {$a->attemptsummaryurl} and click the submit button. You must do this before {$a->attemptgraceend} otherwise your attempt will not be counted.';
$string['emailoverduesmall'] = 'You did not submit your attempt at {$a->quizname}. Please go to {$a->attemptsummaryurl} before {$a->attemptgraceend} if you would still like to submit.';
$string['emailoverduesubject'] = 'Attempt now overdue: {$a->quizname}';
$string['empty'] = 'Empty';
$string['enabled'] = 'Enabled';
$string['endtest'] = 'Finish attempt ...';
$string['erroraccessingreport'] = 'You cannot access this report';
$string['errorinquestion'] = 'Error in question';
$string['errormissingquestion'] = 'Error: The system is missing the question with id {$a}';
$string['errornotnumbers'] = 'Error - answers must be numeric';
$string['errorunexpectedevent'] = 'Unexpected event code {$a->event} found for question {$a->questionid} in attempt {$a->attemptid}.';
$string['essay'] = 'Essay';
$string['essayquestions'] = 'Questions';
$string['eventattemptautosaved'] = 'Quiz attempt auto-saved';
$string['eventattemptdeleted'] = 'Quiz attempt deleted';
$string['eventattemptmanualgradingcomplete'] = 'Quiz attempt manual grading complete';
$string['eventattemptpreviewstarted'] = 'Quiz attempt preview started';
$string['eventattemptquestionrestarted'] = 'Quiz attempt question restarted';
$string['eventattemptreviewed'] = 'Quiz attempt reviewed';
$string['eventattemptsummaryviewed'] = 'Quiz attempt summary viewed';
$string['eventattemptupdated'] = 'Quiz attempt updated';
$string['eventattemptviewed'] = 'Quiz attempt viewed';
$string['eventeditpageviewed'] = 'Quiz edit page viewed';
$string['eventoverridecreated'] = 'Quiz override created';
$string['eventoverridedeleted'] = 'Quiz override deleted';
$string['eventoverrideupdated'] = 'Quiz override updated';
$string['eventpagebreakcreated'] = 'Page break created';
$string['eventpagebreakdeleted'] = 'Page break deleted';
$string['eventquestionmanuallygraded'] = 'Question manually graded';
$string['eventquizattemptabandoned'] = 'Quiz attempt abandoned';
$string['eventquizattemptregraded'] = 'Quiz attempt regraded';
$string['eventquizattemptreopened'] = 'Quiz attempt reopened';
$string['eventquizattemptstarted'] = 'Quiz attempt started';
$string['eventquizattemptsubmitted'] = 'Quiz attempt submitted';
$string['eventquizattempttimelimitexceeded'] = 'Quiz attempt time limit exceeded';
$string['eventquizgradeitemcreated'] = 'Quiz grade item created';
$string['eventquizgradeitemdeleted'] = 'Quiz grade item deleted';
$string['eventquizgradeitemorderchanged'] = 'Quiz grade item order changed';
$string['eventquizgradeitemupdated'] = 'Quiz grade item updated';
$string['eventquizgradeupdated'] = 'Quiz grade updated';
$string['eventquizrepaginated'] = 'Quiz re-paginated';
$string['eventreportviewed'] = 'Quiz report viewed';
$string['eventsectionbreakcreated'] = 'Section break created';
$string['eventsectionbreakdeleted'] = 'Section break deleted';
$string['eventsectiontitleupdated'] = 'Section title updated';
$string['eventsectionshuffleupdated'] = 'Section shuffle updated';
$string['eventslotcreated'] = 'Slot created';
$string['eventslotdeleted'] = 'Slot deleted';
$string['eventslotdisplayedquestionnumberupdated'] = 'Slot displayed question number updated';
$string['eventslotgradeitemupdated'] = 'Slot grade item updated';
$string['eventslotmarkupdated'] = 'Slot mark updated';
$string['eventslotversionupdated'] = 'Slot version updated';
$string['eventslotmoved'] = 'Slot moved';
$string['eventslotrequirepreviousupdated'] = 'Slot require previous updated';
$string['everynquestions'] = 'Every {$a} questions';
$string['everyquestion'] = 'Every question';
$string['everythingon'] = 'Everything on';
$string['existingcategory'] = 'Existing category';
$string['exportcategory'] = 'export category';
$string['exporterror'] = 'An error occurred during export processing';
$string['exportingquestions'] = 'Questions are being exported to file';
$string['exportname'] = 'File name';
$string['exportquestions'] = 'Export questions to file';
$string['extraattemptrestrictions'] = 'Extra restrictions on attempts';
$string['false'] = 'False';
$string['feedback'] = 'Feedback';
$string['feedbackerrorboundaryformat'] = 'Feedback grade boundaries must be either a percentage or a number. The value you entered in boundary {$a} is not recognised.';
$string['feedbackerrorboundaryoutofrange'] = 'Feedback grade boundaries must be between 0% and 100%. The value you entered in boundary {$a} is out of range.';
$string['feedbackerrorjunkinboundary'] = 'You must fill in the feedback grade boundary boxes without leaving any gaps.';
$string['feedbackerrorjunkinfeedback'] = 'You must fill in the feedback boxes without leaving any gaps.';
$string['feedbackerrororder'] = 'Feedback grade boundaries must be in order, highest first. The value you entered in boundary {$a} is out of sequence.';
$string['file'] = 'File';
$string['fileformat'] = 'File format';
$string['fillcorrect'] = 'Fill with correct';
$string['filloutnumericalanswer'] = 'You provide at least one possible answer and tolerance. The first matching answer will be used to determine the grade and feedback. If you supply some feedback with no answer at the end, that will be shown to students whose response is not matched by any of the other answers.';
$string['filloutoneanswer'] = 'You must provide at least one possible answer. Answers left blank will not be used. \'*\' can be used as a wildcard to match any characters. The first matching answer will be used to determine the grade and feedback.';
$string['filloutthreequestions'] = 'You must provide at least three questions with matching answers. You can provide extra wrong answers by giving an answer with a blank question. Entries where both the question and the answer are blank will be ignored.';
$string['fillouttwochoices'] = 'You must fill out at least two choices.  Choices left blank will not be used.';
$string['finishattemptdots'] = 'Finish attempt...';
$string['finishreview'] = 'Finish review';
$string['forceregeneration'] = 'force regeneration';
$string['formatnotfound'] = 'Import/export format {$a} not found';
$string['formulaerror'] = 'Formula errors!';
$string['fractionsaddwrong'] = 'The positive grades you have chosen do not add up to 100%<br />Instead, they add up to {$a}%<br />Do you want to go back and fix this question?';
$string['fractionsnomax'] = 'One of the answers should be 100%, so that it is<br />possible to get a full grade for this question.<br />Do you want to go back and fix this question?';
$string['fromfile'] = 'from file:';
$string['functiondisabledbysecuremode'] = 'That functionality is currently disabled';
$string['generalfeedback'] = 'General feedback';
$string['generalfeedback_help'] = 'General feedback is text which is shown after a question has been attempted. Unlike feedback for a specific question which depends on the response given, the same general feedback is always shown.';
$string['graceperiod'] = 'Submission grace period';
$string['graceperiod_desc'] = 'If what to do when the time expires is set to \'There is a grace period...\', then this is the default amount of extra time that is allowed.';
$string['graceperiod_help'] = 'If what to do when the time expires is set to \'There is a grace period...\', then this is the amount of extra time that is allowed.';
$string['graceperiodmin'] = 'Last submission grace period';
$string['graceperiodmin_desc'] = 'There is a potential problem right at the end of the quiz. On the one hand, we want to let students continue working right up until the last second - with the help of the timer that automatically submits the quiz when time runs out. On the other hand, the server may then be overloaded, and take some time to get to process the responses. Therefore, we will accept responses for up to this long after time expires, so they are not penalised for the server being slow. However, the student could cheat and get this many seconds to answer the quiz. You have to make a trade-off based on how much you trust the performance of your server during quizzes.';
$string['graceperiodtoosmall'] = 'The grace period must be more than {$a}.';
$string['gradeall'] = 'Grade all';
$string['gradeaverage'] = 'Average grade';
$string['gradeboundary'] = 'Grade boundary';
$string['gradeessays'] = 'Grade essays';
$string['gradehighest'] = 'Highest grade';
$string['gradeitemdefaultname'] = 'New grade item {$a}';
$string['gradeitemdelete'] = 'Delete grade item {$a}';
$string['gradeitemedit'] = 'Edit name of grade item {$a}';
$string['gradeitemmarkscheme'] = 'Assign grade items';
$string['gradeitemnewname'] = 'New name for grade item {$a}';
$string['gradeitemnoneselected'] = '[none]';
$string['gradeitemnoslots'] = 'This quiz has no questions yet. Please add questions first, then return here to set up grade items.';
$string['gradeitems'] = 'Grade items';
$string['gradeitemsautosetup'] = 'Set up a grade for each section';
$string['gradeitemsetup'] = 'Grade items setup';
$string['gradeitemsnoneyet'] = 'Create grade items within your quiz. Allocate questions or quiz sections to these grade items to break down grade results into different areas.';
$string['gradeitemsremoveall'] = 'Reset setup';
$string['gradeitemsremoveallconfirm'] = 'Reset grade items setup?';
$string['gradeitemsremoveallmessage'] = 'This will delete all grade items and unassign questions and sections from them.<br><br>This action will not affect the questions and sections themselves, nor existing attempts within the quiz.';
$string['gradeitemsremovealltitle'] = 'Reset grade items setup';
$string['grademethod'] = 'Grading method';
$string['grademethod_help'] = 'When multiple attempts are allowed, the following methods are available for calculating the final quiz grade:

* Highest grade of all attempts
* Average (mean) grade of all attempts
* First attempt (all other attempts are ignored)
* Last attempt (all other attempts are ignored)';
$string['gradesofar'] = '{$a->method}: {$a->mygrade} / {$a->quizgrade}.';
$string['gradetopassmustbeset'] = 'Grade to pass cannot be zero as this quiz has its completion method set to require passing grade. Please set a non-zero value.';
$string['gradetopassoutof'] = 'Grade to pass: {$a->grade} out of {$a->maxgrade}';
$string['gradingdetails'] = 'Marks for this submission: {$a->raw}/{$a->max}.';
$string['gradingdetailsadjustment'] = 'With previous penalties this gives <strong>{$a->cur}/{$a->max}</strong>.';
$string['gradingdetailspenalty'] = 'This submission attracted a penalty of {$a}.';
$string['gradingdetailszeropenalty'] = 'You were not penalized for this submission.';
$string['gradingmethod'] = 'Grading method: {$a}';
$string['groupoverrides'] = 'Group overrides';
$string['groupsnone'] = 'No groups you can access.';
$string['guestsno'] = 'Sorry, guests cannot see or attempt quizzes';
$string['hidebreaks'] = 'Hide page breaks';
$string['hidereordertool'] = 'Hide the reordering tool';
$string['history'] = 'History of responses:';
$string['howquestionsbehave_desc'] = 'Default setting for how questions behave in a quiz.';
$string['imagedisplay'] = 'Image to display';
$string['import_help'] = 'This function allows you to import questions from external text files.

If your file contains non-ascii characters then it must use UTF-8 encoding. Be particularly cautious with files generated by Microsoft Office applications, as these commonly use special encoding which will not be handled correctly.

Import and Export formats are a pluggable resource. Other optional formats may be available in the Modules and Plugins database.';
$string['import_link'] = 'question/import';
$string['importcategory'] = 'import category';
$string['importerror'] = 'An error occurred during import processing';
$string['importfilearea'] = 'Import from file already in course files...';
$string['importfileupload'] = 'Import from file upload...';
$string['importfromthisfile'] = 'Import from this file';
$string['importingquestions'] = 'Importing {$a} questions from file';
$string['importmaxerror'] = 'There is an error in the question. There are too many answers.';
$string['importmax10error'] = 'There is an error in the question. You may not have more than ten answers';
$string['importquestions'] = 'Import questions from file';
$string['inactiveoverridehelp'] = '* This override is inactive because the user\'s access to the activity is restricted. This can be due to group or role assignments, other access restrictions, or the activity being hidden.';
$string['incorrect'] = 'Incorrect';
$string['indicator:cognitivedepth'] = 'Quiz cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a Quiz activity.';
$string['indicator:cognitivedepthdef'] = 'Quiz cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the Quiz activities during this analysis interval (Levels = No view, View, Submit, View feedback, Comment on feedback, Resubmit after viewing feedback)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Quiz social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a Quiz activity.';
$string['indicator:socialbreadthdef'] = 'Quiz social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the Quiz activities during this analysis interval (Levels = No participation, Participant alone, Participant with others)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['indivresp'] = 'Responses of individuals to each item';
$string['info'] = 'Info';
$string['infoshort'] = 'i';
$string['initialnumfeedbacks'] = 'Initial number of overall feedback fields';
$string['initialnumfeedbacks_desc'] = 'When creating a new quiz, provide this many blank overall feedback boxes. Once the quiz has been created, the form shows the number of fields required for the number of feedbacks in the quiz. The setting must be at least 1.';
$string['inprogress'] = 'In progress';
$string['introduction'] = 'Description';
$string['invalidattemptid'] = 'No such attempt ID exists';
$string['invalidcategory'] = 'Category ID is invalid';
$string['invalidoverrideid'] = 'Invalid override ID.';
$string['invalidquestionid'] = 'Invalid question id';
$string['invalidquizid'] = 'Invalid quiz ID';
$string['invalidrandomslot'] = 'Invalid random question slot id.';
$string['invalidsource'] = 'The source is not accepted as valid.';
$string['invalidsourcetype'] = 'Invalid source type.';
$string['invalidstateid'] = 'Invalid state id';
$string['lastanswer'] = 'Your last answer was';
$string['lastautosave'] = 'Last saved: {$a}';
$string['layout'] = 'Layout';
$string['layoutasshown'] = 'Page layout as shown.';
$string['layoutasshownwithpages'] = 'Page layout as shown. <small>(Automatic new page every {$a} questions.)</small>';
$string['layoutshuffledandpaged'] = 'Questions randomly shuffled with {$a} questions per page.';
$string['layoutshuffledsinglepage'] = 'Questions randomly shuffled, all on one page.';
$string['link'] = 'Link';
$string['listitems'] = 'Listing of items in quiz';
$string['literal'] = 'Literal';
$string['loadingquestionsfailed'] = 'Loading questions failed: {$a}';
$string['makecopy'] = 'Save as new question';
$string['managetypes'] = 'Manage question types and servers';
$string['manualgrading'] = 'Grading';
$string['manualgradequestion'] = 'Manually grade question {$a->question} in {$a->quiz} by {$a->user}';
$string['mark'] = 'Submit';
$string['markall'] = 'Submit page';
$string['marks'] = 'Marks';
$string['marks_help'] = 'The mark obtained for each question and the overall attempt score. You can only select Marks if Maximum marks is selected.';
$string['match'] = 'Matching';
$string['matchanswer'] = 'Matching answer';
$string['matchanswerno'] = 'Matching answer {$a}';
$string['messageprovider:attempt_overdue'] = 'Warning when your quiz attempt becomes overdue';
$string['messageprovider:confirmation'] = 'Confirmation of your own quiz submissions';
$string['messageprovider:attempt_grading_complete'] = 'Notification that your attempt has been graded';
$string['messageprovider:quiz_open_soon'] = 'Quiz opens soon';
$string['messageprovider:submission'] = 'Notification of your students\' quiz submissions';
$string['max'] = 'Max';
$string['maxmark'] = 'Maximum mark';
$string['maxmarks'] = 'Maximum marks';
$string['maxmarks_help'] = 'The maximum mark available for each question.';

$string['min'] = 'Min';
$string['minutes'] = 'Minutes';
$string['missingcategory'] = 'Missing question category';
$string['missingcorrectanswer'] = 'Correct answer must be specified';
$string['missingitemtypename'] = 'Missing name';
$string['missingquestion'] = 'This question no longer seems to exist';
$string['modulename'] = 'Quiz';
$string['modulename_help'] = 'The quiz activity enables a teacher to create quizzes comprising questions of various types, including multiple choice, matching, short-answer and numerical.

The teacher can allow the quiz to be attempted multiple times, with the questions shuffled or randomly selected from the question bank. A time limit may be set.

Each attempt is marked automatically, with the exception of essay questions, and the grade is recorded in the gradebook.

The teacher can choose when and if hints, feedback and correct answers are shown to students.

Quizzes may be used

* As course exams
* As mini tests for reading assignments or at the end of a topic
* As exam practice using questions from past exams
* To deliver immediate feedback about performance
* For self-assessment';
$string['modulename_link'] = 'mod/quiz/view';
$string['modulenameplural'] = 'Quizzes';
$string['moveselectedonpage'] = 'Move selected questions to page: {$a}';
$string['multichoice'] = 'Multiple choice';
$string['multipleanswers'] = 'Choose at least one answer.';
$string['mustbesubmittedby'] = 'This attempt must be submitted by {$a}.';
$string['name'] = 'Name';
$string['navigatenext'] = 'Next page';
$string['navigateprevious'] = 'Previous page';
$string['navmethod'] = 'Navigation method';
$string['navmethod_free'] = 'Free';
$string['navmethod_help'] = 'When sequential navigation is enabled a student must progress through the quiz in order and may not return to previous pages nor skip ahead.';
$string['navmethod_seq'] = 'Sequential';
$string['navnojswarning'] = 'Warning: these links will not save your answers. Use the next button at the bottom of the page.';
$string['neverallononepage'] = 'Never, all questions on one page';
$string['newattemptfail'] = 'Error: Could not start a new attempt at the quiz';
$string['newcategory'] = 'New category';
$string['newpage'] = 'New page';
$string['newpage_help'] = 'For longer quizzes it makes sense to stretch the quiz over several pages by limiting the number of questions per page. When adding questions to the quiz, page breaks will automatically be inserted according to this setting. However page breaks may later be moved manually on the editing page.';
$string['newpageevery'] = 'Automatically start a new page';
$string['newsectionheading'] = 'New heading';
$string['noanswers'] = 'No answers were selected!';
$string['noattempts'] = 'No attempts have been made on this quiz';
$string['noattemptsfound'] = 'No attempts found.';
$string['noattemptstoshow'] = 'There are no attempts to show';
$string['nocategory'] = 'Incorrect or no category specified';
$string['noclose'] = 'No close date';
$string['nocommentsyet'] = 'No comments yet.';
$string['noconnection'] = 'There is currently no connection to a web service that can process this question. Please contact your administrator';
$string['nodataset'] = 'nothing - it is not a wild card';
$string['nodatasubmitted'] = 'No data was submitted.';
$string['noessayquestionsfound'] = 'No manually graded questions found';
$string['nogradewarning'] = 'This quiz is not graded, so you cannot set overall feedback that differs by grade.';
$string['nomoreattempts'] = 'No more attempts are allowed';
$string['none'] = 'None';
$string['noopen'] = 'No open date';
$string['nooverridedata'] = 'You must override at least one of the quiz settings.';
$string['nopossibledatasets'] = 'No possible datasets';
$string['noquestionintext'] = 'The question text does not contain any embedded questions';
$string['noquestions'] = 'No questions have been added yet';
$string['noquestionsfound'] = 'No questions found';
$string['noquestionsinquiz'] = 'There are no questions in this quiz.';
$string['noquestionsnotinuse'] = 'This random question is not in use, since its category is empty.';
$string['noquestionsonpage'] = 'Empty page';
$string['noresponse'] = 'No response';
$string['noreview'] = 'You are not allowed to review this quiz';
$string['noreviewattempt'] = 'You are not allowed to review this attempt.';
$string['noreviewshort'] = 'Review not permitted';
$string['noreviewuntil'] = 'You are not allowed to review this quiz until {$a}';
$string['noreviewuntilshort'] = 'Available {$a}';
$string['noscript'] = 'JavaScript must be enabled to continue!';
$string['notavailabletostudents'] = 'Note: This quiz is currently not available to your students.';
$string['notenoughrandomquestions'] = 'There are not enough questions in category {$a->category} to create the question {$a->name} ({$a->id}).';
$string['notenoughsubquestions'] = 'Not enough sub-questions have been defined!<br />Do you want to go back and fix this question?';
$string['notifyattemptsgradedtask'] = 'Send quiz attempt graded notifications';
$string['notimedependentitems'] = 'Time dependent items are not currently supported by the quiz module. As a work around, set a time limit for the whole quiz. Do you wish to choose a different item (or use the current item regardless)?';
$string['notyetgraded'] = 'Not yet graded';
$string['notyetviewed'] = 'Not yet viewed';
$string['notyourattempt'] = 'This is not your attempt!';
$string['noview'] = 'Logged-in user is not allowed to view this quiz';
$string['numattempts'] = '{$a->studentnum} {$a->studentstring} have made {$a->attemptnum} attempts';
$string['numberabbr'] = '#';
$string['numerical'] = 'Numerical';
$string['numquestionsx'] = 'Questions: {$a}';
$string['oneminute'] = '1 minute';
$string['onlyteachersexport'] = 'Only teachers can export questions';
$string['onlyteachersimport'] = 'Only teachers with editing rights can import questions';
$string['onthispage'] = 'This page';
$string['open'] = 'Not answered';
$string['openafterclose'] = 'Could not update the quiz. You have specified an open date after the close date.';
$string['openclosedatesupdated'] = 'Open and close dates';
$string['optional'] = 'optional';
$string['orderandpaging'] = 'Order and paging';
$string['orderandpaging_help'] = 'The numbers 10, 20, 30, ... opposite each question indicate the order of the questions. The numbers increase in steps of 10 to leave space for additional questions to be inserted. To reorder the questions, change the numbers then click the "Reorder questions" button.

To add page breaks after particular questions, tick the checkboxes next to the questions then click the "Add new pages after selected questions" button.

To arrange the questions over a number of pages, click the Repaginate button and select the desired number of questions per page.';
$string['orderingquiz'] = 'Order and paging';
$string['orderingquizx'] = 'Order and paging: {$a}';
$string['outcomesadvanced'] = 'Outcomes are advanced settings';
$string['outof'] = '{$a->grade} out of {$a->maxgrade}';
$string['outofpercent'] = '{$a->grade} out of {$a->maxgrade} ({$a->percent}%)';
$string['outofshort'] = '{$a->grade}/{$a->maxgrade}';
$string['overallfeedback'] = 'Overall feedback';
$string['overallfeedback_help'] = 'Overall feedback is text that is shown after a quiz has been attempted. By specifying additional grade boundaries (as a percentage or as a number), the text shown can depend on the grade obtained.';
$string['overdue'] = 'Overdue';
$string['overduehandling'] = 'When time expires';
$string['overduehandling_desc'] = 'What should happen by default if a student does not submit the quiz before time expires.';
$string['overduehandling_help'] = 'This setting controls what happens if a student fails to submit their quiz attempt before the time expires. If the student is actively working on the quiz at the time, then the countdown timer will always automatically submit the attempt for them, but if they have logged out, then this setting controls what happens.';
$string['overduehandling_link'] = 'mod/quiz/timing';
$string['overduehandlingautosubmit'] = 'Open attempts are submitted automatically';
$string['overduehandlinggraceperiod'] = 'There is a grace period when open attempts can be submitted, but no more questions answered';
$string['overduehandlingautoabandon'] = 'Attempts must be submitted before time expires, or they are not counted';
$string['overduemustbesubmittedby'] = 'This attempt is now overdue. It should already have been submitted. If you would like this quiz to be graded, you must submit it by {$a}. If you do not submit it by then, no marks from this attempt will be counted.';
$string['override'] = 'Override';
$string['overridecannotchange'] = 'The user or group cannot be changed after an override is created.';
$string['overridecannotsetbothgroupanduser'] = 'Both group and user cannot be set at the same time.';
$string['overridedeletegroupsure'] = 'Are you sure you want to delete the override for group {$a}?';
$string['overridedeleteusersure'] = 'Are you sure you want to delete the override for user {$a}?';
$string['overridegroup'] = 'Override group';
$string['overridegroupeventname'] = '{$a->quiz} - {$a->group}';
$string['overrideinvalidattempts'] = 'Attempts value must be greater than zero.';
$string['overrideinvalidexistingid'] = 'Existing override doesn\'t exist.';
$string['overrideinvalidgroup'] = 'Group given doesn\'t exist.';
$string['overrideinvalidquiz'] = 'Quiz ID set doesn\'t exist.';
$string['overrideinvalidtimelimit'] = 'Time limit must be greater than zero.';
$string['overrideinvaliduser'] = 'User given doesn\'t exist.';
$string['overridemissingdelete'] = 'Override ID(s) {$a} couldn\'t be deleted because they don\'t exist or are not a part of the given quiz.';
$string['overridemultiplerecordsexist'] = 'Multiple overrides cannot be made for the same user/group.';
$string['overridemustsetuserorgroup'] = 'A user or group must be set.';
$string['overrides'] = 'Overrides';
$string['overridesforquiz'] = 'Settings overrides: {$a}';
$string['overridesnoneforgroups'] = 'No group settings overrides have been created for this quiz.';
$string['overridesnoneforusers'] = 'No user settings overrides have been created for this quiz.';
$string['overridessummary'] = 'Settings overrides exist ({$a})';
$string['overridessummarythisgroup'] = 'Settings overrides exist ({$a}) for this group';
$string['overridessummaryyourgroups'] = 'Settings overrides exist ({$a}) for your groups';
$string['overridessummarygroup'] = 'Groups: {$a}';
$string['overridessummaryuser'] = 'Users: {$a}';
$string['overrideuser'] = 'Override user';
$string['overrideusereventname'] = '{$a->quiz} - Override';
$string['pageshort'] = 'P';
$string['page-mod-quiz-x'] = 'Any quiz module page';
$string['page-mod-quiz-attempt'] = 'Attempt quiz page';
$string['page-mod-quiz-edit'] = 'Edit quiz page';
$string['page-mod-quiz-report'] = 'Any quiz report page';
$string['page-mod-quiz-review'] = 'Review quiz attempt page';
$string['page-mod-quiz-summary'] = 'Quiz attempt summary page';
$string['page-mod-quiz-view'] = 'Quiz information page';
$string['pagesize'] = 'Page size';
$string['parent'] = 'Parent';
$string['parentcategory'] = 'Parent category';
$string['parsingquestions'] = 'Parsing questions from import file.';
$string['partiallycorrect'] = 'Partially correct';
$string['penalty'] = 'Penalty';
$string['penaltyscheme'] = 'Apply penalties';
$string['penaltyscheme_help'] = 'If enabled, a penalty is subtracted from the final mark for a question for a wrong response. The amount of penalty is specified in the question settings. This setting only applies if adaptive mode is enabled.';
$string['percentcorrect'] = 'Percent correct';
$string['pleaseclose'] = 'Your request has been processed. You can now close this window';
$string['pluginadministration'] = 'Quiz administration';
$string['pluginname'] = 'Quiz';
$string['popup'] = 'Show quiz in a \'secure\' window';
$string['popupblockerwarning'] = 'This section of the test is in secure mode, this means that you need to take the quiz in a secure window. Please turn off your popup blocker. Thank you.';
$string['popupnotice'] = 'Students will see this quiz in a secure window';
$string['preprocesserror'] = 'Error occurred during pre-processing!';
$string['preview'] = 'Preview';
$string['previewquestion'] = 'Preview question';
$string['previewquiz'] = 'Preview {$a}';
$string['previewquizstart'] = 'Preview quiz';
$string['previewquiznow'] = 'Preview quiz now';
$string['previous'] = 'Previous state';
$string['privacy:metadata:core_question'] = 'The quiz activity stores question usage information in the core_question subsystem.';
$string['privacy:metadata:quiz'] = 'The quiz activity makes use of quiz reports.';
$string['privacy:metadata:quiz_attempts'] = 'Details about each attempt on a quiz.';
$string['privacy:metadata:quiz_attempts:attempt'] = 'The attempt number.';
$string['privacy:metadata:quiz_attempts:currentpage'] = 'The current page that the user is on.';
$string['privacy:metadata:quiz_attempts:gradednotificationsenttime'] = 'The time the user was notified that manual grading of their attempt was complete';
$string['privacy:metadata:quiz_attempts:preview'] = 'Whether this is a preview of the quiz.';
$string['privacy:metadata:quiz_attempts:state'] = 'The current state of the attempt.';
$string['privacy:metadata:quiz_attempts:sumgrades'] = 'The sum of grades in the attempt.';
$string['privacy:metadata:quiz_attempts:timecheckstate'] = 'The time that the state was checked.';
$string['privacy:metadata:quiz_attempts:timefinish'] = 'The time that the attempt was completed.';
$string['privacy:metadata:quiz_attempts:timemodified'] = 'The time that the attempt was updated.';
$string['privacy:metadata:quiz_attempts:timemodifiedoffline'] = 'The time that the attempt was updated via an offline update.';
$string['privacy:metadata:quiz_attempts:timestart'] = 'The time that the attempt was started.';
$string['privacy:metadata:quiz_grades'] = 'Details about the overall grade for this quiz.';
$string['privacy:metadata:quiz_grades:grade'] = 'The overall grade for this quiz.';
$string['privacy:metadata:quiz_grades:quiz'] = 'The quiz that was graded.';
$string['privacy:metadata:quiz_grades:timemodified'] = 'The time that the grade was modified.';
$string['privacy:metadata:quiz_grades:userid'] = 'The user who was graded.';
$string['privacy:metadata:quiz_overrides'] = 'Details about overrides for this quiz';
$string['privacy:metadata:quiz_overrides:quiz'] = 'The quiz with override information';
$string['privacy:metadata:quiz_overrides:timeclose'] = 'The new close time for the quiz.';
$string['privacy:metadata:quiz_overrides:timelimit'] = 'The new time limit for the quiz.';
$string['privacy:metadata:quiz_overrides:timeopen'] = 'The new open time for the quiz.';
$string['privacy:metadata:quiz_overrides:userid'] = 'The user being overridden';
$string['privacy:metadata:quizaccess'] = 'The quiz activity makes use of quiz access rules.';
$string['publish'] = 'Publish';
$string['publishedit'] = 'You must have permission in the publishing course to add or edit questions in this category';
$string['qbrief'] = 'Q. {$a}';
$string['qname'] = 'name';
$string['qti'] = 'IMS QTI format';
$string['qtypename'] = 'type, name';
$string['question'] = 'Question';
$string['questionbank'] = 'from question bank';
$string['questionbankmanagement'] = 'Question bank management';
$string['questionbehaviour'] = 'Question behaviour';
$string['questioncats'] = 'Question categories';
$string['questiondeleted'] = 'This question has been deleted. Please contact your teacher.';
$string['questiondependencyadd'] = 'No restriction on when question {$a->thisq} can be attempted • Click to change';
$string['questiondependencyfree'] = 'No restriction on this question';
$string['questiondependencyremove'] = 'Question {$a->thisq} cannot be attempted until the previous question {$a->previousq} has been completed • Click to change';
$string['questiondependsonprevious'] = 'This question cannot be attempted until the previous question has been completed.';
$string['questiondraftonly'] = 'The question {$a} is in draft status. To use it in the quiz, go to the question bank and change the status to ready.';
$string['questiondraftwillnotwork'] = 'This question is in draft status. To use it in the quiz, go to the question bank and change the status to ready.';
$string['questioninuse'] = 'The question \'{$a->questionname}\' is currently being used in: <br />{$a->quiznames}<br />The question will not be deleted from these quizzes but only from the category list.';
$string['questionmissing'] = 'Question for this session is missing';
$string['questionname'] = 'Question name';
$string['questionnonav'] = '<span class="accesshide">Question </span>{$a->number}<span class="accesshide"> {$a->attributes}</span>';
$string['questionnonavinfo'] = '<span class="accesshide">Information </span>{$a->number}<span class="accesshide"> {$a->attributes}</span>';
$string['questionnotloaded'] = 'Question {$a} has not been loaded from the database';
$string['questionorder'] = 'Question order';
$string['questionposition'] = 'New position in order for question {$a}';
$string['questions'] = 'Questions';
$string['questionsetpreview'] = 'Question set preview';
$string['questionsinclhidden'] = 'Questions (including hidden)';
$string['questionsinthisquiz'] = 'Questions in this quiz';
$string['questionsmatchingfilter'] = 'Questions matching this filter: {$a}';
$string['questionsperpage'] = 'Questions per page';
$string['questionsperpageselected'] = 'Questions per page has been set so the paging is currently fixed. As a result, the paging controls have been disabled. You can change this in {$a}.';
$string['questionsperpagex'] = 'Questions per page: {$a}';
$string['questiontext'] = 'Question text';
$string['questiontextisempty'] = '[Empty question text]';
$string['questiontype'] = 'Question type {$a}';
$string['questiontypesetupoptions'] = 'Setup options for question types:';
$string['quiz:addinstance'] = 'Add a new quiz';
$string['quiz:attempt'] = 'Attempt quizzes';
$string['quizavailable'] = 'The quiz is available until: {$a}';
$string['quizclose'] = 'Close the quiz';
$string['quizclosed'] = 'This quiz closed on {$a}';
$string['quizcloses'] = 'Quiz closes';
$string['quizeventcloses'] = '{$a} closes';
$string['quizcloseson'] = 'This quiz will close on {$a}.';
$string['quiz:deleteattempts'] = 'Delete quiz attempts';
$string['quiz:emailconfirmsubmission'] = 'Receive confirmation of your own quiz submissions';
$string['quiz:emailnotifysubmission'] = 'Receive notification of your students\' quiz submissions';
$string['quiz:emailnotifyattemptgraded'] = 'Receive notification when your attempt has been graded';
$string['quiz:emailwarnoverdue'] = 'Receive warning when your quiz attempt becomes overdue';
$string['quiz:grade'] = 'Grade quizzes manually';
$string['quiz:ignoretimelimits'] = 'Ignore quiz time limit';
$string['quizisclosed'] = 'This quiz is closed';
$string['quizisopen'] = 'This quiz is open';
$string['quizisclosedwillopen'] = 'Quiz closed (opens {$a})';
$string['quizisopenwillclose'] = 'Quiz open (closes {$a})';
$string['quiz:manage'] = 'Manage quizzes';
$string['quiz:manageoverrides'] = 'Manage quiz settings overrides';
$string['quiz:viewoverrides'] = 'View quiz settings overrides';
$string['quiznavigation'] = 'Quiz navigation';
$string['quizopen'] = 'Open the quiz';
$string['quizeventopens'] = '{$a} opens';
$string['quizopenclose'] = 'Open and close dates';
$string['quizopenclose_help'] = 'Students can only start their attempt(s) after the open time and they must complete their attempts before the close time.';
$string['quizopenclose_link'] = 'mod/quiz/timing';
$string['quizopened'] = 'This quiz is open.';
$string['quizopenedon'] = 'This quiz opened on {$a}';
$string['quizopens'] = 'Quiz opens';
$string['quizopendatesoonhtml'] = '<p>Hi {$a->firstname},</p>
<p>The quiz <strong>{$a->quizname}</strong> in course {$a->coursename} is opening soon.
<p><strong>Opens: {$a->timeopen}</strong></p>
<p><strong>Closes: {$a->timeclose}</strong></p>
<p><a href="{$a->url}">Go to quiz</a></p>';
$string['quizopendatesoonsubject'] = 'Opens on {$a->timeopen}: {$a->quizname}';
$string['quizopenwillclose'] = 'This quiz is open, will close on {$a} at';
$string['quizordernotrandom'] = 'Order of quiz not shuffled';
$string['quizorderrandom'] = '* Order of quiz is shuffled';
$string['quiz:preview'] = 'Preview quizzes';
$string['quiz:regrade'] = 'Regrade quiz attempts';
$string['quiz:reopenattempts'] = 'Reopen never submitted quiz attempts';
$string['quizreport'] = 'Quiz report';
$string['quiz:reviewmyattempts'] = 'Review your own attempts';
$string['quizsettings'] = 'Quiz settings';
$string['quizsetupnavigation'] = 'Quiz setup navigation';
$string['quiz:view'] = 'View quiz information';
$string['quiz:viewreports'] = 'View quiz reports';
$string['quiztimer'] = 'Quiz Timer';
$string['quizwillopen'] = 'This quiz will open {$a}';
$string['random'] = 'Random question';
$string['randomcatwithsubcat'] = '{$a} and subcategories';
$string['randomcoursecatwithsubcat'] = 'Any category inside course category {$a}';
$string['randomcoursewithsubcat'] = 'Any category in this course';
$string['randomcreate'] = 'Create random questions';
$string['randomediting'] = 'Editing a random question';
$string['randomfaultynosubcat'] = 'Faulty question';
$string['randomfromcategory'] = 'Random question from category:';
$string['randomfromexistingcategory'] = 'Random question from an existing category';
$string['randomfromunavailabletag'] = '{$a} (unavailable)';
$string['randommodulewithsubcat'] = 'Any category of this quiz';
$string['randomnumber'] = 'Number of random questions';
$string['randomnosubcat'] = 'Questions from this category only, not its subcategories.';
$string['randomqname'] = 'Random question based on filter condition';
$string['randomqnamecat'] = 'Random ({$a->category}) based on filter condition';
$string['randomqnamecattags'] = 'Random ({$a->category}) based on filter condition with tags: {$a->tags}';
$string['randomquestion'] = 'Random question';
$string['randomqnametags'] = 'Random question based on filter condition with tags: {$a}';
$string['randomquestion_help'] = 'A random question is a way of inserting a randomly-chosen question from a specified category or by a specified tag into an activity.';
$string['randomquestiontags'] = 'Tags';
$string['randomquestiontags_help'] = 'You can restrict the selection criteria further by specifying some question tags here.

The "random" questions will be selected from the questions that have all these tags.';
$string['randomquestionusinganewcategory'] = 'Random question using a new category';
$string['randomsystemwithsubcat'] = 'Any system-level category';
$string['randomwithsubcat'] = 'Questions from this category and its subcategories.';
$string['readytosend'] = 'You are about to send your whole quiz to be graded.  Are you sure you want to continue?';
$string['reattemptquiz'] = 'Re-attempt quiz';
$string['recentlyaddedquestion'] = 'Recently added question!';
$string['recurse'] = 'Include questions from subcategories too';
$string['redoquestion'] = 'Try another question like this one';
$string['redoesofthisquestion'] = 'Other questions attempted here: {$a}';
$string['regrade'] = 'Regrade all attempts';
$string['regradecomplete'] = 'All attempts have been regraded';
$string['regradecount'] = '{$a->changed} out of {$a->attempt} grades were changed';
$string['regradedisplayexplanation'] = 'Attempts that change during regrading are displayed as hyperlinks to the question review window';
$string['regradenotallowed'] = 'You do not have permission to regrade this quiz';
$string['regradingquestion'] = 'Regrading "{$a}".';
$string['regradingquiz'] = 'Regrading quiz "{$a}"';
$string['remove'] = 'Remove';
$string['removeallgroupoverrides'] = 'All group overrides';
$string['removeallquizattempts'] = 'All quiz attempts';
$string['removealluseroverrides'] = 'All user overrides';
$string['removeemptypage'] = 'Remove empty page';
$string['removepagebreak'] = 'Remove page break';
$string['removeselected'] = 'Remove selected';
$string['rename'] = 'Rename';
$string['renderingserverconnectfailed'] = 'The server {$a} failed to process an RQP request. Check that the URL is correct.';
$string['reopenattempt'] = 'Reopen';
$string['reopenattemptareyousuremessage'] = 'This will reopen attempt {$a->attemptnumber} by {$a->attemptuser}.';
$string['reopenattemptareyousuretitle'] = 'Reopen attempt?';
$string['reopenattemptwrongstate'] = 'Attempt {$a->attemptid} is in the wrong state ({$a->state}) to be reopened.';
$string['reopenedattemptwillbeinprogress'] = 'The attempt will remain open and can be continued.';
$string['reopenedattemptwillbeinprogressuntil'] = 'The attempt will remain open and can be continued until the quiz closes on {$a}.';
$string['reopenedattemptwillbesubmitted'] = 'The attempt will be immediately submitted for grading.';
$string['reorderquestions'] = 'Reorder questions';
$string['reordertool'] = 'Show the reordering tool';
$string['repaginate'] = 'Repaginate with {$a} questions per page';
$string['repaginatecommand'] = 'Repaginate';
$string['repaginatenow'] = 'Repaginate now';
$string['replace'] = 'Replace';
$string['replacementoptions'] = 'Replacement options';
$string['report'] = 'Reports';
$string['reportanalysis'] = 'Item analysis';
$string['reportattemptsfrom'] = 'Attempts from';
$string['reportattemptsthatare'] = 'Attempts that are';
$string['reportdisplayoptions'] = 'Display options';
$string['reportfullstat'] = 'Detailed statistics';
$string['reportmulti_percent'] = 'Multi-percentages';
$string['reportmulti_q_x_student'] = 'Multi-student choices';
$string['reportmulti_resp'] = 'Individual responses';
$string['reportmustselectstate'] = 'You must select at least one state.';
$string['reportnotfound'] = 'Report not known ({$a})';
$string['reportoverview'] = 'Overview';
$string['reportregrade'] = 'Regrade attempts';
$string['reportresponses'] = 'Detailed responses';
$string['reports'] = 'Reports';
$string['reportshowonly'] = 'Show only attempts';
$string['reportshowonlyfinished'] = 'Show at most one finished attempt per user ({$a})';
$string['reportsimplestat'] = 'Simple statistics';
$string['reportusersall'] = 'all users who have attempted the quiz';
$string['reportuserswith'] = 'enrolled users who have attempted the quiz';
$string['reportuserswithorwithout'] = 'enrolled users who have, or have not, attempted the quiz';
$string['reportuserswithout'] = 'enrolled users who have not attempted the quiz';
$string['reportwhattoinclude'] = 'What to include in the report';
$string['requirepassword'] = 'Require password';
$string['requirepassword_help'] = 'If a password is specified, a student must enter it in order to attempt the quiz.';
$string['requiresubnet'] = 'Require network address';
$string['requiresubnet_help'] = 'Quiz access may be restricted to particular subnets on the LAN or Internet by specifying a comma-separated list of partial or full IP address numbers. This can be useful for an invigilated (proctored) quiz, to ensure that only people in a certain location can access the quiz.';
$string['response'] = 'Response';
$string['responses'] = 'Responses';
$string['results'] = 'Results';
$string['returnattempt'] = 'Return to attempt';
$string['reuseifpossible'] = 'reuse previously removed';
$string['reverttodefaults'] = 'Revert to quiz defaults';
$string['review'] = 'Review';
$string['reviewafter'] = 'Allow review after quiz is closed';
$string['reviewalways'] = 'Allow review at any time';
$string['reviewattempt'] = 'Review attempt';
$string['reviewbefore'] = 'Allow review while quiz is open';
$string['reviewclosed'] = 'After the quiz is closed';
$string['reviewduring'] = 'During the attempt';
$string['reviewimmediately'] = 'Immediately after the attempt';
$string['reviewnever'] = 'Never allow review';
$string['reviewofquestion'] = 'Review of question {$a->question} in {$a->quiz} by {$a->user}';
$string['reviewopen'] = 'Later, while the quiz is still open';
$string['reviewoptions'] = 'Students may review';
$string['reviewoptionsheading'] = 'Review options';
$string['reviewoptionsheading_help'] = 'These options control what information students can see when they review a quiz attempt or look at the quiz reports.

**During the attempt** settings are only relevant for some behaviours, like \'interactive with multiple tries\', which may display feedback during the attempt.

**Immediately after the attempt** settings apply for the first two minutes after \'Submit all and finish\' is clicked.

**Later, while the quiz is still open** settings apply after this, and before the quiz close date.

**After the quiz is closed** settings apply after the quiz close date has passed. If the quiz does not have a close date, this state is never reached.';
$string['reviewoverallfeedback'] = 'Overall feedback';
$string['reviewoverallfeedback_help'] = 'The feedback given at the end of the attempt, depending on the student\'s total mark.';
$string['reviewresponse'] = 'Review response';
$string['reviewresponsetoq'] = 'Review response (question {$a})';
$string['reviewthisattempt'] = 'Review your responses to this attempt';
$string['rqp'] = 'Remote question';
$string['rqps'] = 'Remote questions';
$string['sameasoverall'] = 'Same as for overall grades';
$string['save'] = 'Save';
$string['saveandedit'] = 'Save changes and edit questions';
$string['saveattemptfailed'] = 'Failed to save the current quiz attempt.';
$string['savedfromdeletedcourse'] = 'Saved from deleted course "{$a}"';
$string['savegrades'] = 'Save grades';
$string['savemanualgradingfailed'] = 'Modification not saved. Please check the message below and try again.';
$string['savemyanswers'] = 'Save my answers';
$string['savenosubmit'] = 'Save without submitting';
$string['saveoverrideandstay'] = 'Save and enter another override';
$string['savequiz'] = 'Save this whole quiz';
$string['saving'] = 'Saving';
$string['savingnewgradeforquestion'] = 'Saving new grade for question id {$a}.';
$string['savingnewmaximumgrade'] = 'Saving new maximum grade.';
$string['score'] = 'Raw score';
$string['scores'] = 'Scores';
$string['search:activity'] = 'Quiz - activity information';
$string['sectionheadingedit'] = 'Edit heading \'{$a}\'';
$string['sectionheadingremove'] = 'Remove heading \'{$a}\'';
$string['sectionnoname'] = 'Untitled section';
$string['seequestions'] = '(See questions)';
$string['select'] = 'Select';
$string['selectall'] = 'Select all';
$string['selectattempt'] = 'Select attempt';
$string['selectcategory'] = 'Select category';
$string['selectedattempts'] = 'Selected attempts...';
$string['selectmultipleitems'] = 'Select multiple items';
$string['selectmultipletoolbar'] = 'Select multiple toolbar';
$string['selectnone'] = 'Deselect all';
$string['selectquestionslot'] = 'Select question {$a}';
$string['selectquestiontype'] = '-- Select question type --';
$string['sendnotificationopendatesoon'] = 'Notify user of an approaching quiz open date';
$string['serveradded'] = 'Server added';
$string['serveridentifier'] = 'Identifier';
$string['serverinfo'] = 'Server information';
$string['servers'] = 'Servers';
$string['serverurl'] = 'Server URL';
$string['shortanswer'] = 'Short answer';
$string['show'] = 'Show';
$string['showall'] = 'Show all questions on one page';
$string['showblocks'] = 'Show blocks during quiz attempts';
$string['showblocks_help'] = 'If set to yes then normal blocks will be shown during quiz attempts';
$string['showbreaks'] = 'Show page breaks';
$string['showcategorycontents'] = 'Show category contents {$a->arrow}';
$string['showcorrectanswer'] = 'In feedback, show correct answers?';
$string['showdetailedmarks'] = 'Show mark details';
$string['showeachpage'] = 'Show one page at a time';
$string['showfeedback'] = 'After answering, show feedback?';
$string['showinsecurepopup'] = 'Use a \'secure\' popup window for attempts';
$string['showlargeimage'] = 'Large image';
$string['shownoattempts'] = 'Show students with no attempts';
$string['shownoattemptsonly'] = 'Show only students with no attempts';
$string['shownoimage'] = 'No image';
$string['showreport'] = 'Show report';
$string['showsmallimage'] = 'Small image';
$string['showteacherattempts'] = 'Show teacher attempts';
$string['showuserpicture'] = 'Show the user\'s picture';
$string['showuserpicture_help'] = 'If enabled, a student\'s name and picture will be shown on-screen during the attempt, and on the review screen, making it easier to check that the student is logged in as themselves in an invigilated (proctored) exam.';
$string['shuffle'] = 'Shuffle';
$string['shuffleanswers'] = 'Shuffle answers';
$string['shuffledrandomly'] = 'Shuffled randomly';
$string['shufflequestions'] = 'Shuffle';
$string['shufflequestions_help'] = 'If enabled, every time the quiz is attempted, the order of the questions in this section will be shuffled into a different random order.

This can make it harder for students to share answers, but it also makes it harder for students to discuss a particular question with the teacher.';
$string['shufflewithin'] = 'Shuffle within questions';
$string['shufflewithin_help'] = 'If enabled, the parts making up each question will be randomly shuffled each time a student attempts the quiz, provided the option is also enabled in the question settings. This setting only applies to questions that have multiple parts, such as multiple choice or matching questions.';
$string['singleanswer'] = 'Choose one answer.';
$string['sortage'] = 'Sort by age';
$string['sortalpha'] = 'Sort by name';
$string['sortquestionsbyx'] = 'Sort questions by: {$a}';
$string['sortsubmit'] = 'Sort questions';
$string['sorttypealpha'] = 'Sort by type, name';
$string['specificapathnotonquestion'] = 'The specified file path is not on the specified question';
$string['specificquestionnotonquiz'] = 'Specified question is not on the specified quiz';
$string['startagain'] = 'Start again';
$string['startattempt'] = 'Start attempt';
$string['startedon'] = 'Started';
$string['startnewpreview'] = 'Start a new preview';
$string['stateabandoned'] = 'Never submitted';
$string['statefinished'] = 'Finished';
$string['statefinisheddetails'] = 'Submitted {$a}';
$string['stateinprogress'] = 'In progress';
$string['statenotloaded'] = 'The state for question {$a} has not been loaded from the database';
$string['stateoverdue'] = 'Overdue';
$string['stateoverduedetails'] = 'Must be submitted by {$a}';
$string['status'] = 'Status';
$string['stoponerror'] = 'Stop on error';
$string['submission_confirmation'] = 'Submit all your answers and finish?';
$string['submission_confirmation_unanswered'] = 'Questions without a response: {$a}';
$string['submitallandfinish'] = 'Submit all and finish';
$string['subneterror'] = 'Sorry, this quiz has been locked so that it is only accessible from certain locations.  Currently your computer is not one of those allowed to use this quiz.';
$string['subnetnotice'] = 'This quiz has been locked so that it is only accessible from certain locations. Your computer is not on an allowed subnet. As teacher you are allowed to preview anyway.';
$string['subplugintype_quiz'] = 'Report';
$string['subplugintype_quiz_plural'] = 'Reports';
$string['subplugintype_quizaccess'] = 'Access rule';
$string['subplugintype_quizaccess_plural'] = 'Access rules';
$string['substitutedby'] = 'will be substituted by';
$string['summaryofattempt'] = 'Summary of attempt';
$string['summaryofattempts'] = 'Your attempts';
$string['summaryofattemptscaption'] = 'Attempt {$a} summary';
$string['temporaryblocked'] = 'You are temporarily not allowed to re-attempt the quiz.<br /> You will be able to take another attempt on:';
$string['theattempt'] = 'The attempt';
$string['theattempt_help'] = 'Whether the student can review the attempt at all.';
$string['time'] = 'Time';
$string['timecompleted'] = 'Completed';
$string['timedelay'] = 'You are not allowed to do the quiz since you have not passed the time delay before attempting another quiz';
$string['timeleft'] = 'Time left';
$string['timelimit'] = 'Time limit';
$string['timelimit_help'] = 'If enabled, the time limit is stated on the initial quiz page and a countdown timer is displayed in the quiz navigation block.';
$string['timelimit_link'] = 'mod/quiz/timing';
$string['timelimitexeeded'] = 'Sorry! Quiz time limit exceeded!';
$string['timestr'] = '%H:%M:%S on %d/%m/%y';
$string['timesup'] = 'Time is up!';
$string['timing'] = 'Timing';
$string['tofile'] = 'to file';
$string['tolerance'] = 'Tolerance';
$string['toomanyrandom'] = 'The number of random questions required is more than are still available in the category!';
$string['top'] = 'Top';
$string['totalmarks'] = 'Total of marks';
$string['totalmarksx'] = 'Total of marks: {$a}';
$string['totalquestionsinrandomqcategory'] = 'Total of {$a} questions in category.';
$string['true'] = 'True';
$string['truefalse'] = 'True/false';
$string['type'] = 'Type';
$string['unfinished'] = 'open';
$string['ungraded'] = 'Ungraded';
$string['unit'] = 'Unit';
$string['unknowntype'] = 'Question type not supported at line {$a}. The question will be ignored';
$string['updatefilterconditon'] = 'Update filter conditions';
$string['updatefilterconditon_success'] = 'Successfully updated filter conditions';
$string['updateoverdueattemptstask'] = 'Updating overdue quiz attempts';
$string['updatesettings'] = 'Update quiz settings';
$string['updatequizslotswithrandomxofy'] = 'Updating quiz slots with "random" question data ({$a->done}/{$a->total})';
$string['updatingatttemptgrades'] = 'Updating attempt grades.';
$string['updatingfinalgrades'] = 'Updating final grades.';
$string['updatingthegradebook'] = 'Updating the gradebook.';
$string['upgradesure'] = '<div>In particular the quiz module will perform an extensive change of the quiz tables and this upgrade has not yet been sufficiently tested. You are very strongly urged to backup your database tables before proceeding.</div>';
$string['upgradingquizattempts'] = 'Upgrading quiz attempts: quiz {$a->done}/{$a->outof} (Quiz id {$a->info})';
$string['upgradingveryoldquizattempts'] = 'Upgrading very old quiz attempts: {$a->done}/{$a->outof}';
$string['url'] = 'URL';
$string['usedcategorymoved'] = 'This category has been preserved and moved to the site level because it is a published category still in use by other courses.';
$string['useroverrides'] = 'User overrides';
$string['usersnone'] = 'No students have access to this quiz';
$string['validate'] = 'Validate';
$string['viewallanswers'] = 'View {$a} quiz attempts';
$string['viewallreports'] = 'View reports for {$a} attempts';
$string['viewed'] = 'Viewed';
$string['warningmissingtype'] = '<b>This question is of a type that has not been installed on your Moodle yet.<br />Please alert your Moodle administrator.</b>';
$string['wheregrade'] = 'Where\'s my grade?';
$string['wildcard'] = 'Wild card';
$string['windowclosing'] = 'This window will close shortly.';
$string['withsummary'] = 'with summary statistics';
$string['wronguse'] = 'You can not use this page like that';
$string['xhtml'] = 'XHTML';
$string['youneedtoenrol'] = 'You need to enrol in this course before you can attempt this quiz';
$string['yourfinalgradeis'] = 'Your final grade for this quiz is {$a}.';
$string['questionversion'] = 'v{$a}';
$string['questionversionlatest'] = 'v{$a} (latest)';
$string['alwayslatest'] = 'Always latest';
$string['gobacktoquiz'] = 'Go back';

// Deprecated since Moodle 4.3.
$string['completionminattemptsgroup'] = 'Require attempts';

// Deprecated since Moodle 4.4.
$string['grade'] = 'Grade';
$string['timetaken'] = 'Time taken';

// Deprecated since Moodle 4.5.
$string['attemptsdeleted'] = 'Quiz attempts deleted';
$string['gradesdeleted'] = 'Quiz grades deleted';
$string['useroverridesdeleted'] = 'User overrides deleted';
$string['groupoverridesdeleted'] = 'Group overrides deleted';
