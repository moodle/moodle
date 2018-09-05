<?php

// ===================
// essential strings
// ===================
//
$string['modulename'] = 'HotPot';
$string['modulename_help'] = 'The HotPot module allows teachers to distribute interactive learning materials to their students via Moodle and view reports on the students\' responses and results. .

A single HotPot activity consists of an optional entry page, a single elearning exercise, and an optional exit page. The elearning exercise may be a static web page or an interactive web page which offers students text, audio and visual prompts and records their responses. The elearning exercise is created on the teacher\'s computer using authoring software and then uploaded to Moodle.

A HotPot activity can handle exercises created with the following authoring software:

* Hot Potatoes (version 6)
* Qedoc
* Xerte
* iSpring
* any HTML editor';
$string['modulename_link'] = 'mod/hotpot/view';
$string['modulenameplural'] = 'HotPots';
$string['pluginadministration'] = 'HotPot administration';
$string['pluginname'] = 'HotPot module';

// ===================
// subplugin strings
// ===================
//
$string['subplugintype_hotpotattempt'] = 'Output format';
$string['subplugintype_hotpotattempt_plural'] = 'Output formats';
$string['subplugintype_hotpotreport'] = 'Report';
$string['subplugintype_hotpotreport_plural'] = 'Reports';
$string['subplugintype_hotpotsource'] = 'Source file';
$string['subplugintype_hotpotsource_plural'] = 'Source files';

// ===================
// roles strings
// ===================
//
$string['hotpot:addinstance'] = 'Add a new HotPot activity';
$string['hotpot:attempt'] = 'Attempt a HotPot activity and submit results';
$string['hotpot:deleteallattempts'] = 'Delete any user\'s attempts at a HotPot activity';
$string['hotpot:deletemyattempts'] = 'Delete your own attempts at a HotPot activity';
$string['hotpot:ignoretimelimits'] = 'Ignores time limits on a HotPot activity';
$string['hotpot:manage'] = 'Change the settings of a HotPot activity';
$string['hotpot:preview'] = 'Preview a HotPot activity';
$string['hotpot:reviewallattempts'] = 'View any user\'s attempts at a HotPot activity';
$string['hotpot:reviewmyattempts'] = 'View your own attempts at a HotPot activity';
$string['hotpot:view'] = 'View the entry page of a HotPot activity';

// ===================
// config strings
// ===================
//
$string['configbodystyles'] = 'By default, Moodle theme styles will override HotPot activity styles. However, for any styles selected here, the HotPot activity styles will be given priority over the Moodle theme styles.';
$string['configenablecache'] = 'Maintaining a cache of HotPot quizzes can dramatically speed up the delivery of quizzes to the students.';
$string['configenablecron'] = 'Specify the hours in your time zone at which the HotPot cron script may run';
$string['configenablemymoodle'] = 'This settings controls whether HotPots are listed on the MyMoodle page or not';
$string['configenableobfuscate'] = 'Obfuscating the text strings and URLs in javascript code makes it more difficult to guess answers by viewing the source of the HTML page in the browser.';
$string['configenableswf'] = 'Allow embedding of SWF files in HotPot activities. If enabled, this setting overrides filter_mediaplugin_enable_swf.';
$string['configfile'] = 'Configuration file';
$string['configframeheight'] = 'When a quiz is displayed within a frame, this value is the height (in pixels) of the top frame which contains the Moodle navigation bar.';
$string['configlocation'] = 'Configuration file location';
$string['configlockframe'] = 'If this setting is enabled, then the navigation frame, if used, will be locked so that it is not scrollable, not resizeable and has no border';
$string['configmaxeventlength'] = 'If a HotPot has both an open and a close time specified, and the difference between the two times is greater than the number of days specified here, then two separate calendar events will be added to the course calendar. For shorter durations, or when just one time is specified, only one calendar event will be added. If neither time is specified, no calendar event will be added.';
$string['configstoredetails'] = 'If this setting is enabled, then the raw XML details of attempts at HotPot quizzes will be stored in the hotpot_details table. This allows quiz attempts to be regraded in the future to reflect changes in the HotPot quiz scoring system. However, enabling this option on a busy site will cause the hotpot_details table to grow very quickly.';

// ===================
// event strings
// ===================
//
$string['event_attempt_reviewed'] = 'HotPot attempt reviewed';
$string['event_attempt_reviewed_description'] = 'The user with id "{$a->userid}" reviewed an attempt at the "hotpot" activity with course module id "{$a->cmid}"';
$string['event_attempt_reviewed_explanation'] = 'A user has just reviewed an attempt at a HotPot activity';
$string['event_attempt_started'] = 'HotPot attempt started';
$string['event_attempt_started_description'] = 'The user with id "{$a->userid}" started an attempt at the "hotpot" activity with course module id "{$a->cmid}"';
$string['event_attempt_started_explanation'] = 'A user has just started an attempt at a HotPot activity';
$string['event_attempt_submitted'] = 'HotPot attempt submitted';
$string['event_attempt_submitted_description'] = 'The user with id "{$a->userid}" submitted an attempt at the "hotpot" activity with course module id "{$a->cmid}"';
$string['event_attempt_submitted_explanation'] = 'A user has just submitted an attempt at a HotPot activity';
$string['event_base'] = 'HotPot event detected';
$string['event_base_description'] = 'The user with id "{$a->userid}" initiated an event in the "hotpot" activity with course module id "{$a->cmid}"';
$string['event_base_explanation'] = 'An event was  detected by the HotPot module';
$string['event_report_viewed'] = 'HotPot report viewed';
$string['event_report_viewed_description'] = 'The user with id "{$a->userid}" viewed a report on attempts at the "hotpot" activity with course module id "{$a->cmid}"';
$string['event_report_viewed_explanation'] = 'A user has just viewed a report about attempts at a HotPot activity';

// ===================
// more strings
// ===================
//
$string['abandoned'] = 'Abandoned';
$string['abandonhotpot'] = 'Your results so far will be saved but you cannot resume or restart this activity later.';
$string['activitycloses'] = 'Activity closes';
$string['activitygrade'] = 'Activity grade';
$string['activityopens'] = 'Activity opens';
$string['added'] = 'Added';
$string['addquizchain'] = 'Add quiz chain';
$string['addquizchain_help'] = 'Should all the quizzes in a quiz chain be added?

**No**
: only one quiz will be added to the course

**Yes**
: if the source file is a **quiz file**, it is treated as the start of a chain of quizzes and all quizzes in the chain will be added to the course with identical settings. Each quiz in the chain must have a link to the next file in the chain.

If the source file is a **folder**, all recognizable quizzes in the folder will be added to the course to form a chain of quizzes with identical settings.

If the source file is a **unit file**, such as a Hot Potatoes masher file or index.html, quizzes listed in the unit file will be added to the course as a chain of quizzes with identical settings.';
$string['allowpaste'] = 'Allow paste';
$string['allowpaste_help'] = 'If this setting is enabled, students will be allowed to copy, paste and drag text into text input boxes.';
$string['allowreview'] = 'Allow review';
$string['allowreview_help'] = 'If enabled, students may review their quiz attempts after the quiz is closed.';
$string['analysisreport'] = 'Item Analysis';
$string['attempted'] = 'Attempted';
$string['attemptlimit'] = 'Attempt limit';
$string['attemptlimit_help'] = 'The maximum number of attempts a student may have at this HotPot activity';
$string['attemptnumber'] = 'Attempt number';
$string['attempts'] = 'Attempts';
$string['attemptscore'] = 'Attempt score';
$string['attemptsunlimited'] = 'Unlimited attempts';
$string['average'] = 'Average';
$string['averagescore'] = 'Average score';
$string['bodystyles'] = 'Body styles';
$string['bodystylesbackground'] = 'Background color and image';
$string['bodystylescolor'] = 'Text color';
$string['bodystylesfont'] = 'Font size and family';
$string['bodystylesmargin'] = 'Left and right margin';
$string['cacherecords'] = 'HotPot cache records';
$string['canrestarthotpot'] = 'Your results so far will be saved and you can redo "{$a}" later';
$string['canresumehotpot'] = 'Your results so far will be saved and you can resume "{$a}" later.';
$string['checks'] = 'Checks';
$string['checksomeboxes'] = 'Please check some boxes';
$string['clearcache'] = 'Clear HotPot cache';
$string['cleardetails'] = 'Clear HotPot details';
$string['clearedcache'] = 'The HotPot cache has been cleared';
$string['cleareddetails'] = 'The HotPot details have been cleared';
$string['clickreporting'] = 'Enable click reporting';
$string['clickreporting_help'] = 'If enabled, a separate record is kept each time a "hint", "clue" or "check" button is clicked. This allows the teacher to see a very detailed report showing the state of the quiz at each click. Otherwise, only one record per attempt at a quiz is kept.';
$string['clicktrailreport'] = 'Click trails';
$string['closed'] = 'This activity has closed';
$string['clues'] = 'Clues';
$string['completed'] = 'Completed';
$string['completioncompleted'] = 'Require completed status';
$string['completionmingrade'] = 'Require minimum grade';
$string['completionpass'] = 'Require passing grade';
$string['completionwarning'] = 'These fields are disabled if the grade limit for this activity is "No grade" or the grade weighting is "No weighting"';
$string['confirmdeleteattempts'] = 'Do you really want to delete these attempts?';
$string['confirmstop'] = 'Are you sure you want to navigate away from this page?';
$string['correct'] = 'Correct';
$string['couldnotinsertsubmissionform'] = 'Could not insert submission form';
$string['d_index'] = 'Discrimination index';
$string['delay1'] = 'Delay 1';
$string['delay1_help'] = 'The minimum delay between the first and second attempts.';
$string['delay1summary'] = 'Time delay between first and second attempt';
$string['delay2'] = 'Delay 2';
$string['delay2_help'] = 'The minimum delay between attempts after the second attempt.';
$string['delay2summary'] = 'Time delay between later attempts';
$string['delay3'] = 'Delay 3';
$string['delay3_help'] = 'The setting specifies the delay between finishing the quiz and returning control of the display to Moodle.

**Use specific delay**
: control will be returned to Moodle after the specified delay.

**Use settings in source/template file**
: control will be returned to Moodle after the number of seconds specified in the source file or the template files for this output format.

**Wait till student clicks OK**
: control will be returned to Moodle after the student clicks the OK button on the completion message in the quiz.

**Do not continue automatically**
: control will not be returned to Moodle after the quiz is finished. The student will be free to navigate away from the quiz page.

Note, the quiz results are always returned to Moodle immediately the quiz is completed or abandoned, regardless of this setting.';
$string['delay3afterok'] = 'Wait till student clicks OK';
$string['delay3disable'] = 'Do not continue automatically';
$string['delay3specific'] = 'Use specific delay';
$string['delay3summary'] = 'Time delay at the end of the quiz';
$string['delay3template'] = 'Use settings in source/template file';
$string['deleteallattempts'] = 'Delete all attempts';
$string['deleteattempts'] = 'Delete attempts';
$string['detailsrecords'] = 'HotPot details records';
$string['duration'] = 'Duration';
$string['enablecache'] = 'Enable HotPot cache';
$string['enablecron'] = 'Enable HotPot cron';
$string['enablemymoodle'] = 'Show HotPots on MyMoodle';
$string['enableobfuscate'] = 'Enable obfuscation of text and media players';
$string['enableswf'] = 'Allow embedding of SWF files in HotPot activities';
$string['entry_attempts'] = 'Attempts';
$string['entry_dates'] = 'Dates';
$string['entry_grading'] = 'Grading';
$string['entry_title'] = 'Unit name as title';
$string['entrycm'] = 'Previous activity';
$string['entrycm_help'] = 'This setting specifies a Moodle activity and a minimum grade for that activity which must be achieved before this HotPot activity can be attempted.

The teacher can select a specific activity,
or one of the following general purpose settings:

* Previous activity in this course
* Previous activity in this section
* Previous HotPot in this course
* Previous HotPot in this section';
$string['entrycmcourse'] = 'Previous activity in this course';
$string['entrycmsection'] = 'Previous activity in this course section';
$string['entrycompletionwarning'] = 'Before you start this activity, you must look at {$a}.';
$string['entrygrade'] = 'Previous activity grade';
$string['entrygradewarning'] = 'You cannot start this activity until you score {$a->entrygrade}% on {$a->entryactivity}. Currently, your grade for that activity is {$a->usergrade}%';
$string['entryhotpotcourse'] = 'Previous HotPot in this course';
$string['entryhotpotsection'] = 'Previous HotPot in this course section';
$string['entryoptions'] = 'Entry page options';
$string['entryoptions_help'] = 'These check boxes enable and disable the display of items on the HotPot\'s entry page.

**Unit name as title**
: if checked, the unit name will be displayed as the title of the entry page.

**Grading**
: if checked, the HotPot\'s grading information will be displayed on the entry page.

**Dates**
: if checked, the HotPot\'s open and close dates will be displayed on the entry page.

**Attempts**
: if checked, a table showing details of a user\'s previous attempts at this HotPot will be displayed on the entry page. Attempts that may be resumed will have a resume button displayed in the rightmost column.';
$string['entrypage'] = 'Show entry page';
$string['entrypage_help'] = 'Should the students be shown an initial page before starting the HotPot activity?

**Yes**
: the students will be shown an entry page before starting the HotPot. The contents of the entry page are determined by the HotPot\'s entry page options.

**No**
: the students will not be shown an entry page, and will start the HotPot immediately.

An entry page is always shown to the teacher, in order to provide access to the reports and edit quizzes page';
$string['entrypagehdr'] = 'Entry page';
$string['entrytext'] = 'Entry page text';
$string['exit_areyouok'] = 'Hello, are you still there?';
$string['exit_attemptscore'] = 'Your score for that attempt was {$a}';
$string['exit_course'] = 'Course';
$string['exit_course_text'] = 'Return to the main course page';
$string['exit_encouragement'] = 'Encouragement';
$string['exit_excellent'] = 'Excellent!';
$string['exit_feedback'] = 'Exit page feedback';
$string['exit_feedback_help'] = 'These options enable and disable the display of feedback items on a HotPot\'s exit page.

**Unit name as title**
: if checked, the unit name will be displayed as the title of the exit page.

**Encouragement**
: if checked, some encouragement will displayed on the exit page. The encouragement depends on the HotPot grade:
: **&gt; 90%**: Excellent!
: **&gt; 60%**: Well done
: **&gt; 0%**: Good try
: **= 0%**: Are you OK?

**Unit attempt grade**
: if checked, the grade for the unit attempt that has just been completed will be displayed on the exit page.

**Unit grade**
: if checked the HotPot grade will be displayed on the exit page.

In addition, if the unit grading method is highest a message to tell the user if the most recent attempt was equal to or better than their previous will be displayed.';
$string['exit_goodtry'] = 'Good try!';
$string['exit_grades'] = 'Grades';
$string['exit_grades_text'] = 'Look at your grades so far for this course';
$string['exit_hotpotgrade'] = 'Your grade for this activity is {$a}';
$string['exit_hotpotgrade_average'] = 'Your average grade so far for this activity is {$a}';
$string['exit_hotpotgrade_highest'] = 'Your highest grade so far for this activity is {$a}';
$string['exit_hotpotgrade_highest_equal'] = 'You equalled your previous best for this activity!';
$string['exit_hotpotgrade_highest_previous'] = 'Your previous highest grade for this activity was {$a}';
$string['exit_hotpotgrade_highest_zero'] = 'You have not scored higher than {$a} for this activity yet';
$string['exit_index'] = 'Index';
$string['exit_index_text'] = 'Go to the index of activities';
$string['exit_links'] = 'Exit page links';
$string['exit_links_help'] = 'These options enable and disable the display of certain navigation links on a HotPot\'s exit page.

**Retry**
: if multiple attempts are allowed at this HotPot and the student still has some attempts left, a link to allow the student to retry the HotPot will be displayed

**Index**
: if checked, a link to the HotPot index page will be displayed.

**Course**
: if checked, a link to the Moodle course page will be displayed.

**Grades**
: if checked, a link to the Moodle gradebook will be displayed.';
$string['exit_next'] = 'Next';
$string['exit_next_text'] = 'Try the next activity';
$string['exit_noscore'] = 'You have successfully completed this activity!';
$string['exit_retry'] = 'Retry';
$string['exit_retry_text'] = 'Retry this activity';
$string['exit_welldone'] = 'Well done!';
$string['exit_whatnext_0'] = 'What would you like to do next?';
$string['exit_whatnext_1'] = 'Choose your destiny ...';
$string['exit_whatnext_default'] = 'Please choose one of the following:';
$string['exitcm'] = 'Next activity';
$string['exitcm_help'] = 'This setting specifies a Moodle activity to be done after this HotPot activity is completed. The optional grade is the minimum grade for this HotPot activity that is required before the next activity is shown.

The teacher can select a specific activity, or a one of the following general purpose settings:

* Next activity in this course
* Next activity in this section
* Next HotPot activity in this course
* Next HotPot activity in this section

If other exit page options are disabled and the student has achieved the required grade on this HotPot activity, the next activity will be shown straight away. Otherwise, the student will be shown a link to the next activity, which they can click when they are ready.';
$string['exitcmcourse'] = 'Next activity in this course';
$string['exitcmsection'] = 'Next activity in this course section';
$string['exitgrade'] = 'Next activity grade';
$string['exithotpotcourse'] = 'Next HotPot in this course';
$string['exithotpotsection'] = 'Next HotPot in this course section';
$string['exitoptions'] = 'Exit page options';
$string['exitpage'] = 'Show exit page';
$string['exitpage_help'] = 'Should a exit page displayed after the HotPot quiz has been completed?

**Yes**
: the students will be shown an exit page when the HotPot is completed. The contents of the exit page are determined by the settings for the HotPot\'s exit page feedback and links.

**No**
: the students will not be shown an exit page. Instead, they will either go immediately to the next activity or return to the Moodle course page.';
$string['exitpagehdr'] = 'Exit page';
$string['exittext'] = 'Exit page text';
$string['feedbackdiscuss'] = 'Discuss this quiz in a forum';
$string['feedbackformmail'] = 'Feedback form';
$string['feedbackmoodleforum'] = 'Moodle forum';
$string['feedbackmoodlemessaging'] = 'Moodle messaging';
$string['feedbacknone'] = 'None';
$string['feedbacksendmessage'] = 'Send a message to your instructor';
$string['feedbackwebpage'] = 'Web page';
$string['firstattempt'] = 'First attempt';
$string['forceplugins'] = 'Force media plugins';
$string['forceplugins_help'] = 'If enabled, Moodle-compatible media players will play files such as avi, mpeg, mpg, mp3, mov and wmv. Otherwise, Moodle will not change the settings of any media players in the quiz.';
$string['frameheight'] = 'Frame height';
$string['giveup'] = 'Give Up';
$string['grademethod'] = 'Grading method';
$string['grademethod_help'] = 'This setting defines how the HotPot grade is calculated from the attempt scores.

**Highest score**
: the grade will be set to the highest score for an attempt at this HotPot activity.

**Average scsore**
: the grade will be set to the average score for attempts at this HotPot activity.

**First attempt**
: the grade will be set to the score of the first attempt at this HotPot activity.

**Last attempt**
: the grade will be set to the score of the most recent attempt at this HotPot activity.';
$string['gradeweighting'] = 'Grade weighting';
$string['gradeweighting_help'] = 'Grades for this HotPot activity will be scaled to this number in the Moodle gradebook.';
$string['highestscore'] = 'Highest score';
$string['hints'] = 'Hints';
$string['hotpotname'] = 'HotPot activity name';
$string['ignored'] = 'Ignored';
$string['inprogress'] = 'In progress';
$string['isgreaterthan'] = 'is greater than';
$string['islessthan'] = 'is less than';
$string['lastaccess'] = 'Last access';
$string['lastattempt'] = 'Last attempt';
$string['lockframe'] = 'Lock frame';
$string['maxeventlength'] = 'Maximum number of days for a single calendar event';
$string['mediafilter_hotpot'] = 'HotPot media filter';
$string['mediafilter_moodle'] = 'Moodle\'s standard media filters';
$string['migratingfiles'] = 'Migrating Hot Potatoes quiz files';
$string['migratinglogs'] = 'Migrating Hot Potatoes logs';
$string['missingsourcetype'] = 'HotPot record is missing sourcetype';
$string['nameadd'] = 'Name';
$string['nameadd_help'] = 'The name can be specfic text entered by the teacher or it can be automatically generated.

**Get from source file**
: the name will be extracted from the source file.

**Use source file name**
: the source file name will be used as the name.

**Use source file path**
: the source file path will be used as the name. Any slashes in the file path will be replaced by spaces.

**Specific text**
: the specific text entered by the teacher will be used as the name.';
$string['nameedit'] = 'Name';
$string['nameedit_help'] = 'The specific text that is displayed to the students';
$string['navigation'] = 'Navigation';
$string['navigation_embed'] = 'Embedded web page';
$string['navigation_frame'] = 'Moodle navigation frame';
$string['navigation_give_up'] = 'A single &quot;Give Up&quot; button';
$string['navigation_help'] = 'This setting specifies the navigation used in the quiz:

**Moodle navigation bar**
: the Moodle navigation bar will be displayed in the same window as the quiz at the top of the page

**Moodle navigation frame**
: the Moodle navigation bar will be displayed in a separate frame at the top of the quiz

**Embedded web page**
: the Moodle navigation bar will be displayed in with the Hot Potatoes quiz embedded within the window

**Original navigation aids**
: the quiz will be displayed with the navigation buttons, if any, defined in the quiz

**A single "Give Up" button**
: the quiz will be displayed with a single "Give Up" button at the top of the page

**None**
: the quiz will be displayed without any navigation aids, so when all questions have been answered correctly, depending on the "Show next quiz?" setting, Moodle will either return to the course page or display the next quiz';
$string['navigation_moodle'] = 'Standard Moodle navigation bars (top and side)';
$string['navigation_none'] = 'None';
$string['navigation_original'] = 'Original navigation aids';
$string['navigation_topbar'] = 'Top Moodle navigation bar only (no side bars)';
$string['noactivity'] = 'No activity';
$string['nohotpots'] = 'No HotPots found';
$string['nomoreattempts'] = 'Sorry, you have no more attempts left at this activity';
$string['noresponses'] = 'No information about individual questions and responses was found.';
$string['noreview'] = 'Sorry, you are not allowed to view details of this quiz attempt.';
$string['noreviewafterclose'] = 'Sorry, this quiz has closed. You are no longer allowed to view details of this quiz attempt.';
$string['noreviewbeforeclose'] = 'Sorry, you are not allowed to view details this quiz attempt until {$a}';
$string['nosourcefilesettings'] = 'HotPot record is missing source file information';
$string['notattemptedyet'] = 'Not attempted yet';
$string['notavailable'] = 'Sorry, this activity is not currently available to you.';
$string['outputformat'] = 'Output format';
$string['outputformat_best'] = 'Best';
$string['outputformat_help'] = 'The output format specifies how the content will be presented to the student.

The output formats that are available depend on the type of the source file. Some types of source file have just one output format, while other types of source file have several output formats.

The "best" setting will display the content using the optimal output format for the student\'s browser.';
$string['outputformat_hp_6_jcloze_html_dropdown'] = 'DropDown from html';
$string['outputformat_hp_6_jcloze_html_findit_a'] = 'FindIt (a) from html';
$string['outputformat_hp_6_jcloze_html_findit_b'] = 'FindIt (b) from html';
$string['outputformat_hp_6_jcloze_html_jgloss'] = 'JGloss from html';
$string['outputformat_hp_6_jcloze_html'] = 'JCloze (v6) from html';
$string['outputformat_hp_6_jcloze_xml_anctscan'] = 'ANCT-Scan from HP6 JCloze xml';
$string['outputformat_hp_6_jcloze_xml_dropdown'] = 'DropDown from HP6 JCloze xml';
$string['outputformat_hp_6_jcloze_xml_findit_a'] = 'FindIt (a) from HP6 JCloze xml';
$string['outputformat_hp_6_jcloze_xml_findit_b'] = 'FindIt (b) from HP6 JCloze xml';
$string['outputformat_hp_6_jcloze_xml_jgloss'] = 'JGloss from HP6 JCloze xml';
$string['outputformat_hp_6_jcloze_xml_v6_autoadvance'] = 'JCloze (v6) from HP6 xml (Auto-advance)';
$string['outputformat_hp_6_jcloze_xml_v6'] = 'JCloze (v6) from HP6 xml';
$string['outputformat_hp_6_jcross_html'] = 'JCross (v6) from html';
$string['outputformat_hp_6_jcross_xml_v6'] = 'JCross (v6) from xml';
$string['outputformat_hp_6_jmatch_html'] = 'JMatch (v6) from html';
$string['outputformat_hp_6_jmatch_xml_flashcard'] = 'JMatch (flashcard) from xml';
$string['outputformat_hp_6_jmatch_xml_jmemori'] = 'JMemori from xml';
$string['outputformat_hp_6_jmatch_xml_sort'] = 'JMatch Sort from xml';
$string['outputformat_hp_6_jmatch_xml_v6'] = 'JMatch (v6) from xml';
$string['outputformat_hp_6_jmatch_xml_v6_plus'] = 'JMatch (v6+) from xml';
$string['outputformat_hp_6_jmatch_html_sort'] = 'JMatch Sort from html';
$string['outputformat_hp_6_jmix_html'] = 'JMix (v6) from html';
$string['outputformat_hp_6_jmix_xml_v6'] = 'JMix (v6) from xml';
$string['outputformat_hp_6_jmix_xml_v6_plus'] = 'JMix (v6+) from xml';
$string['outputformat_hp_6_jmix_xml_v6_plus_deluxe'] = 'JMix (v6+ with prefix, suffix with distractors) from xml';
$string['outputformat_hp_6_jmix_xml_v6_plus_keypress'] = 'JMix (v6+ with key press) from xml';
$string['outputformat_hp_6_jquiz_html'] = 'JQuiz (v6) from html';
$string['outputformat_hp_6_jquiz_xml_v6'] = 'JQuiz (v6) from xml';
$string['outputformat_hp_6_jquiz_xml_v6_autoadvance'] = 'JQuiz (v6) from xml (Auto-advance)';
$string['outputformat_hp_6_jquiz_xml_v6_exam'] = 'JQuiz (v6) from xml (Exam)';
$string['outputformat_hp_6_rhubarb_html'] = 'Rhubarb (v6) from html';
$string['outputformat_hp_6_rhubarb_xml'] = 'Rhubarb (v6) from xml';
$string['outputformat_hp_6_sequitur_html'] = 'Sequitur (v6) from html';
$string['outputformat_hp_6_sequitur_html_incremental'] = 'Sequitur (v6) from html, incremental scoring';
$string['outputformat_hp_6_sequitur_xml'] = 'Sequitur (v6) from xml';
$string['outputformat_hp_6_sequitur_xml_incremental'] = 'Sequitur (v6) from xml, incremental scoring';
$string['outputformat_html_ispring'] = 'iSpring HTML file';
$string['outputformat_html_xerte'] = 'Xerte HTML file';
$string['outputformat_html_xhtml'] = 'Standard HTML file';
$string['outputformat_qedoc'] = 'Qedoc file';
$string['overviewreport'] = 'Overview';
$string['penalties'] = 'Penalties';
$string['percent'] = 'Percent';
$string['pressoktocontinue'] = 'Press OK to continue, or Cancel to stay on the current page.';
$string['questionshort'] = 'Q-{$a}';
$string['quizname_help'] = 'help text for Quiz name';
$string['quizzes'] = 'Quizzes';
$string['responses'] = 'Responses';
$string['responsesreport'] = 'Responses';
$string['reviewafterattempt'] = 'Allow review after attempt';
$string['reviewafterclose'] = 'Allow review after HotPot closes';
$string['reviewduringattempt'] = 'Allow review during attempt';
$string['reviewoptions'] = 'Review options';
$string['score'] = 'Score';
$string['scoresreport'] = 'Scores';
$string['selectattempts'] = 'Select attempts';
$string['showerrormessage'] = 'HotPot error: {$a}';
$string['sourcefile'] = 'Source file';
$string['sourcefile_help'] = 'This setting specifies the file containing the content that will be shown to the students.

Usually the source file will have been created outside of Moodle, and then uploaded to the files area of a Moodle course.
It may be an html file, or it may be another kind of file that has been created with authoring software such as Hot Potatoes or Qedoc.

The source file may be specified as a folder and file path in the Moodle course files area, or it may be a url beginning with http:// or https://

For Qedoc materials, the source file must be the url of a Qedoc module that has been uploaded to the Qedoc server.

* e.g. http://www.qedoc.net/library/ABCDE_123.zip
* For information about uploading Qedoc modules see: [Qedoc documentation: Uploading_modules](http://www.qedoc.org/en/index.php?title=Uploading_modules)';
$string['sourcefilenotfound'] = 'Source file not found (or empty): {$a}';
$string['status'] = 'Status';
$string['stopbutton'] = 'Show stop button';
$string['stopbutton_help'] = 'If this setting is enabled, a stop button will be inserted into the quiz.

If a student clicks the stop button, the results so far will be returned to Moodle and the status of the quiz attempt will be set to abandoned.

The text that is displayed on the stop button can be one of the preset phrases from Moodle\'s language packs, or the teacher can specify their own text for the button.';
$string['stopbutton_langpack'] = 'From language pack';
$string['stopbutton_specific'] = 'Use specific text';
$string['stoptext'] = 'Stop button text';
$string['storedetails'] = 'Store the raw XML details of HotPot quiz attempts';
$string['studentfeedback'] = 'Student feedback';
$string['studentfeedback_help'] = 'If enabled, a link to a pop-up feedback window will be displayed whenever the student clicks on the "Check" button. The feedback window allows students to discuss this quiz with their teacher and classmates in one of the following ways:

**Web page**
: requires URL of the web page, for example http://myserver.com/feedbackform.html

**Feedback form**
: requires URL of the form script, for example http://myserver.com/cgi-bin/formmail.pl

**Moodle forum**
: the forum index for the course will be displayed

**Moodle messaging**
: the Moodle instant messaging window will be displayed. If the course has several teachers, the student will be prompted to select a teacher before the messaging window appears.';
$string['submits'] = 'Submissions';
$string['textsourcefile'] = 'Get from source file';
$string['textsourcefilename'] = 'Use source file name';
$string['textsourcefilepath'] = 'Use source file path';
$string['textsourcequiz'] = 'Get from quiz';
$string['textsourcespecific'] = 'Specific text';
$string['timeclose'] = 'Available until';
$string['timedout'] = 'Timed out';
$string['timelimit'] = 'Time limit';
$string['timelimit_help'] = 'This setting specifies the maximum duration of a single attempt.

**Use settings in source/template file**
: the time limit will be taken from the source file or the template files for this output format

**Use specific time**
: the time limit specified on the HotPot quiz settings page will be used as the time limit for an attempt at this quiz. This setting overrides time limits in the source file, configuration file, or template files for this output format.

**Disable**
: no time limit will be set for attempts at this quiz.

Note that if an attempt is resumed, the timer continues from where the attempt was previously paused.';
$string['timelimitexpired'] = 'The time limit for this attempt has expired';
$string['timelimitspecific'] = 'Use specific time';
$string['timelimitsummary'] = 'Time limit for one attempt';
$string['timelimittemplate'] = 'Use settings in source/template file';
$string['timeopen'] = 'Available from';
$string['timeopenclose'] = 'Open and close times';
$string['timeopenclose_help'] = 'You can specify times when the quiz is accessible for people to make attempts. Before the opening time, and after the closing time, the quiz will be unavailable.';
$string['title'] = 'Title';
$string['title_help'] = 'This setting specifies the title to be displayed on the web page.

**HotPot activity name**
: the name of this HotPot activity will be displayed as the web page title.

**Get from source file**
: the title, if any, defined in the source file will be used as the web page title.

**Use source file name**
: the source file name, excluding any folder names, will be used as the web page title.

**Use source file path**
: the source file path, including any folder names, will be used as the web page title.';
$string['toolsindex'] = 'HotPot Tools index';
$string['unitname_help'] = 'help text for unit name';
$string['unrecognizedsourcefile'] = 'Sorry, the HotPot module could not detect the type of the source file: {$a}';
$string['updated'] = 'Updated';
$string['updatinggrades'] = 'Updating HotPot grades';
$string['usefilters'] = 'Use filters';
$string['usefilters_help'] = 'If this setting is enabled, the content will be passed through the Moodle filters before being sent to the browser.';
$string['useglossary'] = 'Use glossary';
$string['useglossary_help'] = 'If this setting is enabled, the content will be passed through Moodle\'s Glossary Auto-linking filter before being sent to the browser.

Note that this setting overrides the site administration setting to enable or disable the Glossary Auto-linking filter.';
$string['usemediafilter'] = 'Use media filter';
$string['usemediafilter_help'] = 'This setting specifies the media filter to be used.

**None**
: the content will not be passed through any media filters.

**Moodle\'s standard media filters**
: the content will be passed through Moodle\'s standard media filters. These filters search for links to common types of sound and movie file, and convert those links to suitable media players.

**HotPot media filter**
: the content will be passed through filters which detect links, images, sounds and movies to be specified using a square bracket notation.

The square-bracket notation has the following syntax:
<code>[url player width height options]</code>

**url**
: the relative or absolute url of the media file

**player** (optional)
: the name of the player to be inserted. The default value for this setting is "moodle". The standard version of the HotPot module also offers the following players:
: **dew**: an mp3 player
: **dyer**: mp3 player by Bernard Dyer
: **hbs**: mp3 player from Half-Baked Software
: **image**: insert an image into the web page
: **link**: insert a link to another web page

**width** (optional)
: the required width of the player

**height** (optional)
: the required height of the player. If omitted this value will be set to the same as the width setting.

**options** (optional)
: a comma-separated list options to be passed to the player. Each option can be a simple on/off switch, or a name value pair.
: **name=value
: **name="some value with spaces"';
$string['viewreports'] = 'View reports for {$a} user(s)';
$string['views'] = 'Views';
$string['weighting'] = 'Weighting';
$string['wrong'] = 'Wrong';
$string['zeroduration'] = 'No duration';
$string['zeroscore'] = 'Zero score';
