<!--
// PLEASE NOTE that this version is more recent than the incorrectly
// numbered v6.1, dated 2003.11.17. From now on, version numbers will
// follow those of Hot Potatoes.
/* hot-potatoes.js (v6.0.4.0 - 2005.02.18)
 * =======================================
 * by Gordon Bateson, February 2003
 * Copyright (c) 2003 Gordon Bateson. All Rights Reserved.
 *
 * You are hereby granted a royalty free license to use or modify this
 * software provided that this copyright notice appears on all copies.
 *
 * This software is provided "AS IS" without a warranty of any kind.
 *
 * Documentation and downloads may be available from:
 * http://www.kanazawa-gu.ac.jp/~gordon/research/hot-potatoes/
 */
// This JavaScript library modifies the SendResults and StartUp functions
// used by hotpot v5 and v6, so that more (or less!) details about the
// student can be input, and more details of a quiz's questions and answers
// can be submitted to the server when the quiz is finished
// If the arrays below (Login, DB, JBC, ...) are set BEFORE calling this
// script, they will NOT be overwritten. Any array that is not set, will
// use the defaults below. This is useful if you want to use different
// settings for different quizzes.
// ************
//  Login Screen
// ************
if (window.Login==null) {
	Login = new Array();
	Login[0] = true;	// Show prompt for user name
				// This can also be a string of user names ...
				// Login[0] = "Guest,Peter,Paul,Mary,Webmaster";
				// or an array of user names (and on-screen texts) (and passwords) ...
				// Login[0] = new Array("Guest", "001,Peter,xxxx", "002,Paul,yyyy", "003,Mary,zzzz", "Webmaster");
				// and can also be  written as ...
				// Login[0] = new Array(
				//	new Array("Guest"),
				//	new Array("001", "Peter", "xxxx"),
				//	new Array("002", "Paul", "yyyy"),
				//	new Array("003", "Mary", "zzzz"),
				//	new Array("Webmaster")
				// );
	Login[1] = true;	// Show prompt for student's UserID
				// If there is no password prompt (i.e. Logon[3] is false), this value
				// will be checked against the password information, if any, in Login[0]
	Login[2] = false;	// Show prompt for student's email
	Login[3] = false;	// Show prompt for quiz password, and check this value against
				// the password information, if any, in Login[0]
				// This can also be a string required to start the quiz ...
				// Login[3] = "password";
	Login[4] = true;	// Show prompt for the cookie expiry date
				// If false, cookies expire at the end of the current session
	Login[5] = "guest,webmaster"
				// guest user names (case insensitive) ...
				// Login[5] = "guest,webmaster";
				// These users do NOT need to fill in other login fields
				// and their quiz results are NOT added to the database
	// the Login prompts and error messages
	// are defined in the MSG array (see below)
}
// *********
//  Database (for use with BFormMail)
// *********
if (window.DB==null) {
	DB = new Array();
	DB[0] = true; // append form fields to database on server
			// If you are NOT using BFormMail's database feature,
			// set DB[0]=false, and you can then safely ignore DB[1 to 5]
	DB[1] = "/home/gordon/public_html/cgi/hot-potatoes-data";
			// append_db folder path (no trailing slash)
			// Can be either an absolute path  e.g. "/home/gordon/public_html/cgi/hot-potatoes-data"
			// or a relative (to CGI bin) path  e.g. "hot-potatoes-data"
	DB[2] = "hot-potatoes";
			// append_db file name (no extension)
			// If left blank, the quiz file name, without extension, will be used
			// i.e. each quiz will have its results stored in a different file.
			// If filled in, this file will store the results for ALL quizzes.
			// Database files and folders must be set up BEFORE running the quiz
			// must have appropriate access privileges (on Unix, use "chmod 666").
	DB[3] = ""; // append_db extension (if left blank, ".txt" will be used)
	DB[4] = ""; // db_fields (if left blank, ALL quiz fields will be sent)
	DB[5] = ""; // db_delimiter (if left blank, tab will be used)
	DB[6] = "REMOTE_ADDR,HTTP_USER_AGENT";
			// env_report ('REMOTE_ADDR','HTTP_USER_AGENT' and a few others)
	// for a complete description of these fields are, see ...
	// http://www.infosheet.com/stuff/BFormMail.readme
	// Switches DB[7] and DB[8] force the settings in the ResultForm
	// In v5 and v6 quizzes, these settings wil be override those in the original quiz
	// If the quiz results are to be sent to an LMS (via the "store" form)
	// then switches DB[7] and DB[8] are not used
	DB[7] = '';	// URL of form processing script
			// e.g. http://www.kanazawa-gu.ac.jp/~gordon/cgi/bformmail.cgi
	DB[8] = '';	// email address to which results should be sent
			// e.g. gordon@kanazawa-gu.ac.jp
}
// By default the quiz's question's scores will be returned.
// If you want more detailed information, set the flags below:
// ********
//  JBC
// ********
if (window.JBC==null) {
	JBC = new Array();
	JBC[0] = true;	// show separator line between answers on email
	JBC[1] = true;	// show number of attempts to answer question
	JBC[2] = true;	// show question texts
	JBC[3] = true;	// show right answer(s)
	JBC[4] = true;	// show wrong answer(s)
	JBC[5] = true;	// show ignored answer(s)
	JBC[6] = false;	// show answer as text (false) or number (true)
}
// JBC quizzes use the global variables 'I' and 'Status'
// I : an array of JBC_QUESTIONs (one for each question)
// JBC_QUESTION :
//	[0] : question text
//	[1] : array of JBC_ANSWERs (one for each answer)
//	[2] : single/multi flag
//		0 : single answer (using 'button')
//		1 : multiple answers (using 'checkbox')
// JBC_ANSWER :
//	[0] : answer text
//	[1] : answer feedback
//	[2] : correct answer flag
//		0 : this is NOT the correct answer
//		1 : this is the correct answer
// Status : an array of JBC_QUESTION_STATUSes
// JBC_QUESTION_STATUS:
//	[0] : correctly answered yet flag
//		0 : this question has NOT been correctly answered
//		1 : this question has been correctly answered
//	[1] : array of JBC_ANSWER_STATUSes (one for each answer)
//		'0' : initial value
//		'R' : single answer question was answered 'R'ight
//		'W' : single answer question was answered 'W'rong
//		'C' : multiple answer question's checkbox was 'C'hecked
//		'U' : multiple answer question's checkbox was 'U'nchecked
//	[2] : number of times this question has been wrongly answered
//	[3] : score (out of 1) for this question (maybe undefined on HP<5.5)
//		0 : not correct yet
//		0<[3]<1 : correct but only after [2] wrong attempts
//		1 : correct first time (bravo!)
//	N.B. score = (numberOfAnswers - numberofWrongTries) / numberOfAnswers
// ********
//  JCloze
// ********
if (window.JCloze==null) {
	JCloze = new Array();
	JCloze[0] = true;	// show separator line between answers on email
	JCloze[1] = true;	// show student's correct answer
	JCloze[2] = true;	// show other correct answer(s), if any
	JCloze[3] = true;	// show wrong answer(s), if any (NOT available for v5)
	JCloze[4] = false;	// show number of hints + checks (legacy field, replaced by [7]+[9])
	JCloze[5] = false;	// show if clue was asked for or not (legacy field, replaced by [8])
	JCloze[6] = true;	// show clue
	JCloze[7] = true;	// show number of hints (=next letter requests)
	JCloze[8] = true;	// show number of clues
	JCloze[9] = true;	// show number of checks
}
// JCloze quizzes use the global variables 'I' and 'State'
// I : array of JCLOZE_ANSWERs
// JCLOZE_ANSWER :
//	[0] : (unused)
//	[1] : array of JCLOZE_ANSWER_TEXTs
//	[2] : clue for this answer
// JCLOZE_ANSWER_TEXT :
//	[0] : array (seems unnecessary, just the text would be enough?)
//		[0] : text of possible answer
// State : array of JCLOZE_ANSWER_STATEs
// JCLOZE_ANSWER_STATE (v5) :
//	[0] : clue asked for or not
//	[1] : number of hints (show next letter) and penalties ('check' an incorrect answer)
//	[2] : length of answer matched
//	[3] : score for this item
//	[4] : already answered correctly
//	[5] : answer entered in text box (right or not)
// JCLOZE_ANSWER_STATE (v6)
//	this.ClueGiven = false;
//	this.HintsAndChecks = 0;
//	this.MatchedAnswerLength = 0;
//	this.ItemScore = 0;
//	this.AnsweredCorrectly = false;
//	this.Guesses = new Array(); last guess is correct answer
// ********
//  JCross
// ********
if (window.JCross==null) {
	JCross = new Array();
	JCross[0] = true;	// show separator line between answers on email
	JCross[1] = true;	// show number of penalties (hints or checks before complete)
	JCross[2] = true;	// show number of letters
	JCross[3] = true;	// show correct answers
	JCross[4] = true;	// show clues
	JCross[5] = true;	// show wrong answers
	JCross[6] = true;	// show if clue was asked for or not
	JCross[7] = true;	// show number of hints (=next letter requests)
	JCross[8] = true;	// show number of checks
	// there are no "ignored" answers for JCross quizzes
}
// JCross quizzes use the following global variables:
// 	L : letters (of correct answers)
// 	C : clue numbers (CL in v6)
// 	G : guesses
// 'L', 'C' ('CL') and 'G' are all 2-dimensional arrays (rows x cols)
//
// v5 quizzes additionally use the following single-dimension arrays
// 	A : clues for across (horizontal) words
// 	D : clues for down (vertical) words
// N.B. form is only sent when all answers are correct so
// you can't find out what 'wrong' answers were entered
// ********
//  JMatch
// ********
if (window.JMatch==null) {
	JMatch = new Array();
	JMatch[0] = true;	// show separator line between answers on email
	JMatch[1] = false;	// show number of penalties (= total number of checks)
	JMatch[2] = true;	// show LHS texts (the question)
	JMatch[3] = true;	// show correct answers
	JMatch[4] = true;	// show wrong answers
	JMatch[5] = true;	// show checks (per match) [empty or unchanged RHS are not counted]
	// JMatch has no "clue" or "hint" buttons
	// there cannot be any "ignored" answers
}
// v5 JMatch quizzes use the global variables 'I' and 'Status' (and 'RItems')
// v6 JMatch quizzes use only 'Status'
// v6+ JMatch quizzes use 'F' and 'D' (see below)
// I : an array of JMATCH_PAIRs (one for each pair)
// JMATCH_PAIR :
//	[0] : LHS text
//	[1] : RHS text
//	[2] : fixed (=not jumbled) flag
//		0 : not fixed
//		1 : fixed
//	[3] : index in drop down list selection
// Status : an array of JMATCH_PAIR_STATUSes
// JMATCH_PAIR_STATUS:
//	[0] : correctly matched yet flag
//		0 : this pair has NOT been correctly matched
//		1 : this pair has been correctly matched
//	[1] : number of times this item has been wrongly matched
// 	v6 quizzes only
//	[2] : id of original SELECT element containing possible matches
//	Note that after matching, this SELECT is removed, so don't try looking for it :-)
// v6+ JMatch quizzes use the global variables 'F' and 'D'
// F : array of JMATCH_FIXED_ITEMs
// JMATCH_FIXED_ITEM:
//	[0] : text
//	[1] : tag
// D : array of JMATCH_DRAGGABLE_ITEMs
// JMATCH_DRAGGABLE_ITEM
//	[0] : text
//	[1] : tag of the F item to which it SHOULD be dragged
//	[2] : tag of the F item to which it was dragged (initally 0)
// N.B. form is only sent when all answers are correct so
// you can't find out what 'wrong' answers were entered
// ********
//  JMix
// ********
if (window.JMix==null) {
	JMix = new Array();
	JMix[0] = true;		// show separator line between answers on email
	JMix[1] = false;	// show number of wrong guesses (replaced by JMix[5])
	JMix[2] = true;		// show right answer
	JMix[3] = true;		// show wrong answer, if any
	JMix[4] = false;	// show answer as text (false) or number (true)
	JMix[5] = true;		// show number of checks
	JMix[6] = true;		// show number of hints (=show next word)
}
// JMix quizzes use the global variables
// 'Segments', 'GuessSequence' and 'Penalties'
// Segments : array of JMix_QUESTIONs
// JMix_QUESTION:
//	[0] : text
//	[1] : order in sequence
//	[2] : used flag
// GuessSequence : array of 'order in sequence' numbers
// Penalties : number of incorrect guesses
// ********
//  JQuiz
// ********
if (window.JQuiz==null) {
	JQuiz = new Array();
	JQuiz[0] = true;	// show separator line between answers on email
	JQuiz[1] = true;	// show question text
	JQuiz[2] = true;	// show student's correct answer(s)
	JQuiz[3] = false;	// show wrong and ignored answer(s) (legacy field superceded by [8] & [9])
	JQuiz[4] = true;	// show number of hints requested
	JQuiz[5] = false;	// show number of checks of incorrect answers (legacy field superceded by [12])
	// HP6 v6 quizzes only
	JQuiz[6] = false;	// show answer value (false) or A,B,C... index (true)
	JQuiz[7] = false;	// show all students answers
	JQuiz[8] = true;	// show student's wrong answers
	JQuiz[9] = true;	// show ignored answers (not relevant for multi-select questions)
	JQuiz[10] = true;	// show score weightings
	JQuiz[11] = true;	// show question type
	JQuiz[12] = true;	// show number of checks (if true, then JQuiz[5] of will be ignored)
	JQuiz[13] = true;	// show number of times ShowAnswer button was pressed (usually 0 or 1)
}
// v5 JQuiz quizzes use the global variables 'I' and 'Status'
// I : array of JQUIZ_ANSWERs
// JQUIZ_ANSWER :
//	[0] : question text
//	[1] : array of JQUIZ_ANSWER_TEXTs (one for each answer)
// JQUIZ_ANSWER_TEXT :
//	[0] : array (seems unnecessary, just the text would be enough?)
//		[0] : text of possible answer
// Status : array of JQUIZ_ANSWER_STATEs
// JQUIZ_ANSWER_STATE :
//	[0] : question done or not
//	[1] : number of wrong checks
//	[2] : number of hints asked for
//	[3] : student's answer
//	[4] : score for this question
// v6 JQuiz quizzes use the global variables 'I' and 'State'
// I : array of JQUIZ_QUESTIONs
// JQUIZ_QUESTION :
//	[0] : weighting
//	[1] : ?? (always set to '')
//	[2] : question type
//		'0'=multiple-choice, '1'=short-answer, '2'=hybrid, '3'=multi-select
//	[3] : array of JQUIZ_ANSWERSs (one for each possible answer)
// JQUIZ_ANSWER :
//	[0] : answer value
//	[1] : feedback text
//	[2] : correct answer flag (1=a correct answer, 0=a wrong answer)
//	[3] : weighted score (as percentage) if correct
//	[4] : flag (usually set to 1, but for hybrid answers that are not
//		to be included in multiple choice options, it is set to 0)
// State : array of JQUIZ_QUESTION_STATEs
// JQUIZ_QUESTION_STATE :
//	[0] : score (-1 shows not done yet)
//	[1] : array showing on which number try each JQUIZ_ANSWER was selected
//	[2] : number of attempts at this question
//	[3] : total of weighted scores of correct answers that were selected
//		i.e. each time a correct answer is selected,
//		its JQUIZ_ANSWER[3] weighting is added to this total
//		so when all the correct answers have been selected, this will be 100
//	[4] : penalties incurred for hints (score is set to zero if >= 1)
//	[5] : 	- for multiple choice, short-answer and hybrid questions, this is a
//		comma-delimited list showing order in which answers were chosen
//		- for multi-select fields, this is bar-delimted ('|') list of settings
//		showing whether each checkbox was selected ('Y') on not ('N') when the
//		'Check' button was clicked. The final item in the list will be the
//		settings for the correct answer.
// N.B. JBC, JMatch(v5) and JQuiz(v5) all use global variables 'I' and 'Status'
//	JBC : I[0].length==3 && !window.RItems
// 	JQuiz(v5) : I[0].length==2
//	JMatch(v5) : I[0].length==4 && window.RItems
// N.B. JCloze(v5+6) and JQuiz(v6) both use global variables 'I' and 'State'
//	JCloze (v5) : I[0].length==3 && State[0].Guesses==null
//	JCloze (v6) : I[0].length==3 && State[0].Guesses!=null
// 	JQuiz  (v6) : I[0].length==4
// **********
//  Rhubarb
// **********
if (window.Rhubarb==null) {
	Rhubarb = new Array();
	Rhubarb[0] = true;  // show correct words (so far)
	Rhubarb[1] = true;  // show correct words as count (true) or list (false)
	Rhubarb[2] = true;  // show wrong words
	Rhubarb[3] = false; // show wrong words as count (true) or list (false)
	Rhubarb[4] = false; // show ignored words (not implemented yet)
	Rhubarb[5] = true;  // show hints
}
// **********
//  Sequitur
// **********
if (window.Sequitur==null) {
	Sequitur = new Array();
	Sequitur[0] = true;  // show count of correct button clicks
	Sequitur[1] = true;  // show count of wrong button clicks
}
// **********
//  Messages
// **********
if (window.MSG==null) {
	MSG = new Array();
	// Login prompts
	MSG[0] = 'Name';
	MSG[1] = 'ID';
	MSG[2] = 'Email';
	MSG[3] = 'Password';
	MSG[4] = 'Cookies';
	// Login buttons
	MSG[5] = 'Start the Quiz';
	MSG[6] = 'Cancel';
	// Cookie menu options (only used if Login[4] is true)
	MSG[7] = 'keep for this session only';
	MSG[8] = 'keep for one day';
	MSG[9] = 'keep for one month';
	MSG[10] = 'do NOT keep cookies';
	// Login error messages
	MSG[11] = 'Sorry, you were unable to login. Please try again later.';
	MSG[12] = 'Please fill in all the information.';
	MSG[13] = 'Incorrect Password. Please try again.';
	MSG[14] = 'Incorrect ID. Please try again.';
	MSG[15] = 'Email address does not appear to be valid.';
	// day and month names (used in Start_Time and End_Time)
	MSG[16] = new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	MSG[17] = new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	// enable popups
	MSG[18] = 'Please enable pop-up windows on your browser.';
	// browser specific instuctions on how to enable popup windows
	var n = navigator;
	var s = n.userAgent.toLowerCase();
	if (n.appName=='Netscape' && s.indexOf('gecko')>=0) {
		// Netscape 6 and 7
		MSG[18] += '\n\n' + 'Edit->Preferences, ' + (s.indexOf('mac')>=0 ? 'Advanced->Scripts & Plugins' : 'Privacy & Security->Popup Window Controls');
	} else if (s.indexOf('safari')>=0) {
		// Safari
		MSG[18] += '\n\n' + 'on Safari menu, uncheck "Block Pop-Up Windows"';
	} else if (s.indexOf('firebird')>=0) {
		// Firebird
		MSG[18] += '\n\n' + 'Preferences->Web Features, uncheck "Block Pop-Up Windows"';
	} else if (s.indexOf('msie 6')>=0) {
		// IE 6 (WinXP.SP2)
		MSG[18] += '\n\n' + 'Tools->Pop-up Blocker->Turn Off Pop-up Blocker';
	}
}
//if (window.FEEDBACK==null) {
//	FEEDBACK = new Array();
//	FEEDBACK[0] = ''; // url of feedback page/script
//	FEEDBACK[1] = ''; // array of array('teachername', 'value');
//	FEEDBACK[2] = ''; // 'student name' [formmail only]
//	FEEDBACK[3] = ''; // 'email@somewhere.com>' [formmail only]
//	FEEDBACK[4] = ''; // window width
//	FEEDBACK[5] = ''; // window height
//	FEEDBACK[6] = ''; // 'Send a message to teacher' [prompt/button text]
//	FEEDBACK[7] = ''; // 'Title'
//	FEEDBACK[8] = ''; // 'Teacher'
//	FEEDBACK[8] = ''; // 'Message'
//	FEEDBACK[10] = ''; // 'Close this window' [formmail only]
//}
// **********
//  HP array
// **********
HP = new Array();
for (var i=0; i<=8; i++) {
	HP[i] = new Array();
}
// indexes for the HP array (makes the code further down easier to read)
_score   = 0;
_weight  = 1;
_correct = 2;
_wrong   = 3;
_unused  = 4;
_hints   = 5;
_clues   = 6;
_checks  = 7;
_guesses = 8;
// *************
//  Server Fields
// *************
if (window.ServerFields==null) {
	ServerFields = new Array();
	// these fields will be added to the ResultForm and submitted to the CGI script on the server.
	// 'Sort', 'return_link_title', 'return_link_url' and 'print_blank_fields' are useful for formmail
	// override the HP setting of sort fields (forces ALL fields to be displayed)
	ServerFields[0] = new Array('sort', '');
	// add link to close pop-up results window
	ServerFields[1] = new Array('return_link_title', 'Close this window');
	ServerFields[2] = new Array('return_link_url', 'javascript:self.close()');
	// make sure zero values are printed
	ServerFields[3] = new Array('print_blank_fields', 'yes');
	// you can also set other fields for your customized CGI script
	// e.g. adding a server defined start time (instead of a client defined start time)
	// ServerFields[4] = new Array('serverStartTime', '<?php echo date("Y-m-d H:i:s") ?>');
}
// *********************
//      Login screen
//  (not required by LMS)
// *********************
function QuizLogin(LoginPrompt) {
	if (!is_LMS() && (Login[0] || Login[1] || Login[2] || Login[3])) {
		var html = ''
			+ '<html>'
			+ '<head></head>'
			+ '<body bgColor="#cccccc" onLoad="opener.setFocus(self)">'
			+ '<form onSubmit="'
			+ 	'self.ok=true;'
			+ 	'self.expiry=null;'
		;
		if (Login[4]) { // cookie expiry
			html += "opener.checkOK(self,'CookieExpiry');";
		}
		if (Login[0]) { // user name
			html += "opener.checkOK(self,'UserName');";
		}
		if (Login[1]) { // user ID
			html += "opener.checkOK(self,'UserID');";
		}
		if (Login[2]) { // user email
			html += "opener.checkOK(self,'UserEmail');";
		}
		if (Login[3]) { // quiz password
			html += "opener.checkOK(self,'Password');";
		}
		html += 	 'if(ok){'
			+	 	'opener.StartQuiz();'
			+	 	'self.close();'
			+	  '}else{'
			+	 	'if(isNaN(self.tries))self.tries=0;'
			+	 	'self.tries++;'
			+	 	'if(self.tries<3){'
			+			'opener.setFocus(self);'
			+	 	'}else{'
			+	 		"alert(opener.MSG[11]);"
			+	 		'opener.goBack();'
			+	 		'self.close();'
			+	 	'}'
			+	  '}'
			+	  'return false;'
			+ '">'
		;
		html += '<table>'
			+ 	'<caption>' + LoginPrompt + '</caption>';
		;
		if (Login[0]) { // user name
			var v = getCookie(self, 'UserName');
			html += '<tr>'
				+	'<th align=right nowrap>' + MSG[0] + ' :</th>'
				+	'<td>'
			;
			if (typeof(Login[0])=='boolean') { // text box
				html += '<input type=text name=UserName value="' + v + '">';
			} else { // drop down menu of names
				// pattern to match commas and white space
				var comma = (window.RegExp) ? new RegExp('\\s*,\\s*') : ',';
				// convert list of names to array, if necessary
				if (typeof(Login[0])=='string') {
					Login[0] = Login[0].split(comma);
				}
				html += '<select name=UserName size=1>'
					+ '<option value=""></option>'
				;
				for(var i=0; i<Login[0].length; i++) {
					// convert name details to array if nececount_cary
					if (typeof(Login[0][i])=='string') {
						Login[0][i] = Login[0][i].split(comma);
					}
					html += makeOption(Login[0][i][0], v, Login[0][i][1]);
				}
				html += '</select>';
			}
			html += 	'</td>'
				+ '</tr>'
			;
		}
		if (Login[1]) { // user ID
			var v = getCookie(self, 'UserID');
			html += '<tr><th align=right nowrap>' + MSG[1] + ' :</th><td><input type=text name=UserID value="' + v + '"></td></tr>';
		}
		if (Login[2]) { // user email
			var v = getCookie(self, 'UserEmail');
			html += '<tr><th align=right nowrap>' + MSG[2] +' :</th><td><input type=text name=UserEmail value="' + v + '"></td></tr>';
		}
		if (Login[3]) { // quiz password
			var v = getCookie(self, 'Password');
			html += '<tr><th align=right nowrap>' + MSG[3] + ' :</th><td><input type=password name=Password value="' + v + '"></td></tr>';
		}
		if (Login[4]) { // cookie lifespan
			var v = getCookie(self, 'CookieExpiry');
			html += '<tr>'
				+ 	'<th align=right nowrap>' + MSG[4] + ' :</th>'
				+ 	'<td>'
				+		'<select name="CookieExpiry" size=1>'
				+ 			makeOption('session', v, MSG[7])
				+ 			makeOption('day', v, MSG[8])
				+ 			makeOption('month', v, MSG[9])
				+ 			makeOption('never', v, MSG[10])
				+ 		'</select>'
				+ 	'</td>'
				+ '</tr>'
			;
		}
		html += 	'<tr>'
			+		'<th>&nbsp;</th>'
			+		'<td nowrap>'
			+			'<input type=submit value="' + MSG[5] + '"> '
			+ 			'<input type=button value="' + MSG[6] + '" onClick="opener.goBack();self.close();">'
			+		'</td>'
			+	'</tr>'
			+ '</table></form></body></html>'
		;
		// set height of Login Window
		var m = navigator.userAgent.indexOf('Mac')>=0;
		var h = (m ? 80 : 100);
		for (var i=0; i<5; i++) h += (Login[i] ? (m ? 20 : 25) : 0);
		// open up a new window
		if (!openWindow('', '', (m ? 320 : 300), h, 'RESIZABLE', html)) {
			alert(MSG[18]); // unable to open popup window
		}
	} else { // no Login required
		window.UserName = window.UserID = window.UserEmail = window.Password = '';
		window.StartQuiz();
	}
	return true;
}
function makeOption(value, v, txt) {
	return '<option value="' + value + '"' + (value==v ? ' SELECTED' : '') + '>' + (txt ? txt : value) + '</option>';
}
function setFocus(w) {
	w.focus(); // bring window to the front
	var obj = w.document.forms[0].elements;
	for(var i=0; i<obj.length; i++) {
		var v = getValue(w, i);
		if (v=='' || obj[i].type=='submit') {
			obj[i].focus();
			break;
		}
	}
}
function checkOK(w, n){
	var v = getValue(w, n, true);
	if (v || (n!='UserName' && isGuest())) {
		if (n=='CookieExpiry') setCookieExpiry(w, v);
		setCookie(self, n, v, w.expiry);
		if (n!='CookieExpiry') eval('self.' + n + '=v');
	} else {
		if (w.ok) alert(MSG[12]);
		w.ok = false;
	}
}
function getValue(w, n, flag) {
	var obj = w.document.forms[0].elements[n];
	var TYPE = obj.type.toUpperCase(); // required for ns4 (win)
	if (obj.options && TYPE.indexOf('SELECT')>=0){
		var v = obj.options[obj.selectedIndex].value;
	} else {
		var v = obj.value;
	}
	if (flag) {
		var msg = '';
		if (n=='Password' || (n=='UserID' && !Login[3])) {
			var pwd = getPassword(w);
			if (pwd && v!=pwd) msg = MSG[n=='Password' ? 13 : 14];
		}
		if (n=='UserEmail' && window.RegExp) {
			var r = '(\\w|-)+';
			r = r + '(\\.' + r + ')';
			r = new RegExp('^(' + r + '*)@(' + r + '+)$');
			if (v.match(r)==null) msg = MSG[15];
		}
		if (msg) {
			obj.value = v = '';
			if (w.ok) alert(msg);
			w.ok = false;
		}
	}
	return v;
}
function getPassword(w) {
	var pwd = '';
	if (Login[3] && typeof(Login[3])=='string') {
		pwd = Login[3];
	} else if ((Login[3] || Login[1]) && typeof(Login[0])=='object') {
		var username = getValue(w, 'UserName');
		for(var i=0; i<Login[0].length; i++) {
			if (username==Login[0][i][0]) {
				pwd = Login[0][i][2];
				break;
			}
		}
	}
	return pwd;
}
function setCookieExpiry(w, v) {
	if (v=='never'){
		w.expiry = new Date('Thu, 01-Jan-70 00:00:01 GMT');
	} else if (v=='day' || v=='month') {
		var ms = (v=='month' ? 31 : 1) * 60 * 60 * 24 * 1000;
		w.expiry = new Date((new Date()).getTime() + ms);
	}
}
function setCookie(w, name, value, expires, path, domain, secure) {
	if (name) w.document.cookie = ''
		+ 'HP_' + name + "=" + escape(value)
		+ (expires ? "; expires=" + expires.toGMTString() : "")
		+ (path ? "; path=" + path : "")
		+ (domain ? "; domain=" + domain : "")
		+ (secure ? "; secure" : "")
	;
}
function getCookie(w, n) {
	var c = w.document.cookie;
	var i = c.indexOf('HP_' + n + '=');
	var j = (i<0) ? -1 : c.indexOf(';', (i += n.length + 4));
	return (i<0) ? '' : unescape(c.substring(i, ((j<0) ? c.length : j)));
}
function goBack(w) {
	if (w==null) w = self; // default
	if (w.history.length) w.history.back();
}
function openWindow(url, name, width, height, attributes, html) {
	// set height, width and attributes
	if (window.screen && width && height) {
		var W = screen.availWidth;
		var H = screen.availHeight;
		width = Math.min(width, W);
		height = Math.min(height, H);
		attributes = ''
			+ (attributes ? (attributes+',') : '')
			+ 'WIDTH='+width+',HEIGHT='+height
		;
	}
	// create global hpWindows object, if necessary
	if (!window.hpWindows) window.hpWindows = new Array();
	// initialize window object
	var w = null;
	// has a window with this name been opened before?
	if (name && hpWindows[name]) {
		// http://www.webreference.com/js/tutorial1/exist.html
		if (hpWindows[name].open && !hpWindows[name].closed) {
			w = hpWindows[name];
			w.focus();
		} else {
			hpWindows[name] = null;
		}
	}
	// check window is not already open
	if (w==null) {
		// workaround for "Access is denied" errors in IE when offline
		// based on an idea seen at http://www.devshed.com/Client_Side/JavaScript/Mini_FAQ
		var ie_offline = (document.all && location.protocol=='file:');
		// try and open the new window
		w = window.open((ie_offline ? '' : url), name, attributes);
		// check window opened OK (user may have prevented popups)
		if (w) {
			// center the window
			if (window.screen && width && height) {
				w.moveTo((W-width)/2, (H-height)/2);
			}
			// add content, if required
			if (html) {
				with (w.document) {
					clear();
					open();
					write(html);
					close();
				}
			} else if (url && ie_offline) {
				w.location = url;
			}
			if (name) hpWindows[name] = w;
		}
	}
	return w;
}
// *********************
//  Send results by email
//  (not required by LMS)
// *********************
function SendAllResults(Score) {
	// check this quiz is not generated by a LMS
	if (!is_LMS()) {
		// add flat file database details to the results form
		AddDatabaseDetailsToResultForm();
		// add student details to the results form
		AddStudentDetailsToResultForm();
		// add question details to the results form
		AddQuestionDetailsToResultForm();
		// add server fields, if any, to results form
		AddServerFieldsToResultForm();
		// change "method" of form, because "get" only allows 512 byts of data
		ResultForm = replaceLast('method="get"', 'method="post"', ResultForm);
		// create results window and form
		var w = openWindow('', '', 500, 400, 'RESIZABLE,SCROLLBARS,LOCATION', ResultForm);
		// check window opened OK (user may have prevented popups)
		if (w) {
			// get shortcut to form object
			var form = w.document.forms[0];
			// update some important field values
			form.Score.value = Score + '%';
			form.realname.value = UserName;
			form.Start_Time.value = getTime(Start_Time);
			form.End_Time.value = getTime();
			// force email subject and Exercise title
			form.subject.value = document.title;
			form. Exercise.value = document.title;
			// update DB fields, if required
			if (DB[0] && !isGuest()) set_db_fields(form);
			if (DB[7]) form.action = DB[7];
			if (DB[8]) form.recipient.value = DB[8];
			// if this is a Netscape browser, check if the referer will be set OK
			if (navigator.appName=='Netscape' && (location.protocol=='file:' || navigator.userAgent.indexOf('Netscape6')>=0)) {
				// ns4 and ns7 set referer to 'file:// ...' when running a quiz offline
				// ns6.2 (at least) always sets referer to 'about:blank'
				// Netscape's setting of referer can cause BFormMail
				// to reject the form, so encode the form data as a URL
				var url = form.action;
				var obj = form.elements;
				for (var i=0; i<obj.length; i++) {
					var  v = escape(obj[i].value);
					v = v.replace((new RegExp('\\+', 'g')), '%2B');
					url += (i==0 ? '?' : '&') + obj[i].name + '=' + v;
				}
				w.location.href = url;
			} else { // browser can POST form ok
				form.submit();
			}
		} else { // unable to open popup window
			alert(MSG[18]);
		}
	} // end if LMS
}
function isGuest() {
	// check username is not a "guest" user
	var flag = false;
	var n = getCookie(self, 'UserName').toLowerCase();
	if (n) {
		// convert list of user names to array, if necessary
		if(typeof(Login[5])=='string') {
			Login[5] = Login[5].split(',');
		}
		for(var i=0; i<Login[5].length; i++) {
			if (n==Login[5][i].toLowerCase()) {
				flag = true;
				break;
			}
		}
	}
	return flag;
}
function set_db_fields(form) {
	// update list of DB fields, if required
	if (DB[4]=='' && window.RegExp) {
		// add administration fields
		var db_fields = ''
			+ 'subject,realname'
			+ (Login[1] ? ',ID' : '')
			+ (Login[2] ? ',email' : '')
			+ (Login[3] ? ',password' : '')
			+ ',Score,Start_Time,End_Time'
		;
		// add answer fields (except separators)
		var r = new RegExp('^[^_]+_q\\d\\d_\\w+$');
		for(var i=0; i<form.elements.length; i++) {
			var n = form.elements[i].name;
			if (r.test(n)) db_fields += ',' + n;
		}
		form.db_fields.value = db_fields;
	}
	// make sure delimiter is set (NS6+ requires this be set here, not any earlier)
	form.db_delimiter.value = (DB[5] ? DB[5] : '\t');
}
function AddStudentDetailsToResultForm() {
	var sDetails = '';
	if (Login[0]) { // user name
		// use 'realname' instead of a separate 'Name' field
		// sDetails += hpHiddenField('Name', window.UserName);
	}
	if (Login[1]) { // user ID
		sDetails += hpHiddenField('ID', window.UserID);
	}
	if (Login[2]) { // user email
		sDetails += hpHiddenField('email', window.UserEmail);
	}
	if (sDetails && window.RegExp) {
		// insert sDetails before '<input...Score...></input>'
		var r = new RegExp('<input[^>]*Score[^>]*><\\/input>', 'i');
		var m = r.exec(ResultForm);
		if (m) {
			ResultForm = ResultForm.replace(m[0], sDetails + m[0] + makeSeparator('Time_'));
			sDetails = '';
		}
	}
	if (Login[3]) { // quiz password
		sDetails += hpHiddenField('Password', window.Password);
		ResultForm = replaceLast('</form>', sDetails + '</form>', ResultForm);
	}
}
function AddQuestionDetailsToResultForm() {
	var qDetails = GetQuestionDetails();
	if (qDetails) {
		// insert qDetails before the final </form> tag in the ResultForm
		ResultForm = replaceLast('</form>', qDetails + '</form>', ResultForm);
	}
}
function AddDatabaseDetailsToResultForm() {
	if (window.DB && DB[0] && !isGuest()) {
		var dbDetails = '';
		var folder = DB[1];
		if (folder && folder.charAt(folder.length-1)!='/') folder += '/';
		var file = DB[2];
		if (file=='') {
			file = location.href;
			file= file.substring(file.lastIndexOf('/')+1);
			var i = file.indexOf('?');
			if (i >= 0) file = file.substring(0, i);
			var i = file.lastIndexOf('.');
			if (i >= 0) file = file.substring(0, i);
		}
		var ext = (DB[3] ? DB[3] : 'txt');
		if (ext.charAt(0)!='.') ext = '.' + ext;
		dbDetails += hpHiddenField('append_db', folder + file + ext);
		dbDetails += hpHiddenField('db_fields', DB[4]);
		dbDetails += hpHiddenField('db_delimiter', ''); // NS6+ requires this be set later
		if (DB[6]) dbDetails += hpHiddenField('env_report', DB[6]);
		// insert dbDetails before the final </form> tag in the ResultForm
		ResultForm = replaceLast('</form>', dbDetails + '</form>', ResultForm);
	}
}
function AddServerFieldsToResultForm() {
	if (window.ServerFields) {
		var s = ''; // input tags for s(erver fields)
		for (var i=0; i<ServerFields.length; i++) {
			if (ServerFields[i][0] && window.RegExp) {
				// remove previous field value, if any
				var r = new RegExp('<input[^>]*name\\s*=\\s*["\']\\s*' + ServerFields[i][0] + '[^>]*>(\\s*<\\/input>)?', 'i');
				if (r.test(ResultForm)) {
					ResultForm = ResultForm.replace(r, '');
				}
			}
			if (ServerFields[i][1]) {
				s += hpHiddenField(ServerFields[i][0], ServerFields[i][1]);
			}
		} // end for
		if (s) ResultForm = replaceLast('</form>', s + '</form>', ResultForm);
	}
}
function replaceLast(a, b, c) {
	// replace last occurrence of 'a' in 'c' with 'b'
	var l = a.length;
	var i = c.lastIndexOf(a);
	return (i<0 || l==0) ? c : (c.substring(0, i) + b + c.substring(i+l));
}
// *************************
//  Extract question details
// *************************
function GetQuestionDetails() {
	var hp = hpVersion();
	var t = hpQuizType();
	var v = hpQuizVersion();
	return	(t==1) ? GetJbcQuestionDetails(hp, v) :
		(t==2) ? GetJClozeQuestionDetails(hp, v) :
		(t==3) ? GetJCrossQuestionDetails(hp, v) :
		(t==4) ? GetJMatchQuestionDetails(hp, v) :
		(t==5) ? GetJMixQuestionDetails(hp, v) :
		(t==6) ? GetJQuizQuestionDetails(hp, v) :
		(t==7) ? GetRhubarbDetails(hp, v) :
		(t==8) ? GetSequiturDetails(hp, v) : '';
}
function GetJbcQuestionDetails(hp, v) {
	qDetails = '';
	// check the quiz version
	if (hp==5 || hp==6) {
		// get question details
		for(var q=0; q<I.length; q++) {
			// initialize strings to hold answer details
			var aDetails = new Array();
			aDetails[0] = new Array(); // right
			aDetails[1] = new Array(); // wrong
			aDetails[2] = new Array(); // ignored
			// get answer details
			for(var a=0; a<I[q][1].length; a++) {
				var i = (Status[q][1][a]=='R') ? 0 : (Status[q][1][a]=='0') ? 2 : 1;
				aDetails[i][aDetails[i].length] = (JBC[6] ? a : I[q][1][a][0]);
			}
			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JBC', q);
			// add separator, if required
			if (JBC[0]) qDetails += makeSeparator(Q);
			if (JBC[1]) { // number of attempts to answer question
				qDetails += hpHiddenField(Q+'attempts', Status[q][2] + (Status[q][0]==1 ? 1 : 0));
			}
			if (JBC[2]) { // question text
				qDetails += hpHiddenField(Q+'text', I[q][0]);
			}
			if (JBC[3] && (DB[0] || aDetails[0].length>0)) { // right
				qDetails += hpHiddenField(Q+'right', aDetails[0]);
			}
			if (JBC[4] && (DB[0] || aDetails[1].length>0)) { // wrong
				qDetails += hpHiddenField(Q+'wrong', aDetails[1]);
			}
			if (JBC[5] && (DB[0] || aDetails[2].length>0)) { // ignored
				qDetails += hpHiddenField(Q+'ignored', aDetails[2]);
			}
			// calculate score for this question, if required (for HP version < 5.5)
			if (isNaN(Status[q][3])) {
				var a1 = Status[q][1].length; // answers
				var a2 = Status[q][2]; // attempts
				Status[q][3] =  (a1<1 || a1<(a2-1)) ? 0 : ((a1 - (a2-1)) / a1);
			}
			// add 'score' for this question
			qDetails += hpHiddenField(Q+'score', Math.floor(Status[q][3]*100)+'%');
		} // end for
	}
	return qDetails;
}
function GetJClozeQuestionDetails(hp, v) {
	var qDetails = '';
	// check the quiz version
	if (hp==5 || hp==6) {
		var r = hpRottmeier();
		if (parseInt(r)==2) { // Rottmeier Find-It 3a+3b
			qDetails += hpHiddenField('JCloze_penalties', window.TotWrongChoices);
		}
		// get details for each question
		var q_max = (r==0) ? State.length :  GapList.length; // could use I.length for both
		for (var q=0; q<q_max; q++) {
			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JCloze', q);
			// add separator, if required
			if (JCloze[0]) qDetails += makeSeparator(Q);
			// score (as %)
			var x = (hp==5) ? State[q][3] : (r==0) ? State[q].ItemScore : GapList[q][1].Score;
			qDetails += hpHiddenField(Q+'score', Math.floor(x*100)+'%');
			var correct = (HP[_correct][q] ? HP[_correct][q] : '');
			if (JCloze[1]) { // student's correct answer
				qDetails += hpHiddenField(Q+'correct', correct);
			}
			if (JCloze[2]) { // ignored answers
				var x = new Array();
				if (r!=2.1) { // exclude Find-It 3a
					for (var i=0, ii=0; i<I[q][1].length; i++) {
						var s = I[q][1][i][0];
                        if (typeof(s)=='string' && s!='') {
                            if (s.toUpperCase() == correct.toUpperCase()) {
                                var is_ignored = false;
                            } else {
                                // DropDown 2.4
                                var is_ignored = true;
                                var iii_max = HP[_wrong][q] ? HP[_wrong][q].length : 0;
                                for (var iii=0; iii<iii_max; iii++) {
                                    if (s.toUpperCase() == HP[_wrong][q][iii].toUpperCase()) {
                                        var is_ignored = false;
                                    }
                                }
                            }
    						if (is_ignored) {
    							x[ii++] = s;
    						}
                        }
					}
				}
				qDetails += hpHiddenField(Q+'ignored', x);
			}
			if (JCloze[3]) {
				var x = (HP[_wrong][q] ? HP[_wrong][q] : '');
				qDetails += hpHiddenField(Q+'wrong', x);
			}
			if (JCloze[4]) { // number of penalties (Hints + Checks)
				var x = (hp==5) ? State[q][1] : (r==0) ? State[q].HintsAndChecks : (r==1) ?  GapList[q][1].NumOfTrials : (r==2.2) ?  GapList[q][1].HintsAndChecks : 0;
				qDetails += hpHiddenField(Q+'penalties', x);
			}
			if (JCloze[5]) { // clue shown?
				var x = (hp==5) ? State[q][0] : (r==0) ? State[q].ClueGiven: (r==1) ? GapList[q][1].ClueAskedFor : false;
				qDetails += hpHiddenField(Q+'clue_shown', (x ? 'YES' : 'NO'));
			}
			if (JCloze[6]) { // clue text
				qDetails += hpHiddenField(Q+'clue_text', I[q][2]);
			}
			if (JCloze[7]) { // number of hints
				var x = (HP[_hints][q] ? HP[_hints][q] : 0);
				qDetails += hpHiddenField(Q+'hints', x);
			}
			if (JCloze[8]) { // number of clues
				var x = HP[_clues][q] ? HP[_clues][q] : 0;
				qDetails += hpHiddenField(Q+'clues', x);
			}
			if (JCloze[9]) { // number of checks (including the final one for the correct answer)
				var x = (HP[_checks][q] ? HP[_checks][q] : 0);
				qDetails += hpHiddenField(Q+'checks', x);
			}
		} // end for
	}
	return qDetails;
}
function GetJCrossQuestionDetails(hp, v) {
	var qDetails = '';
	// check the quiz version
	if (hp==5 || hp==6) {
		// inialize letter count
		var letters = 0;
		// get details for each question
		for (var row=0; row<L.length; row++) {
			for (var col=0; col<L[row].length; col++) {
				// increment letter count, if required
				if (L[row][col]) letters++;
				// show answers and clues, if required
				var q = (hp==5) ? C[row][col] : CL[row][col];
				if (q) {
					for (var i=0; i<2; i++) { // 0==across, 1==down
						var AD = (i==0) ? 'A' : 'D';
						var acrossdown = (i==0) ? 'across' : 'down';

						var clue = (hp==5) ? eval(AD+'['+q+']') : GetJCrossClue('Clue_'+AD+'_'+q);
						if (clue) {
							// format 'Q' (a padded, two-digit version of 'q')
							var Q = getQ('JCross', q) + acrossdown + '_'; // e.g. JCross_01_across_

							if (JCross[0]) {
								qDetails += makeSeparator(Q);
							}
							if (JCross[5]) {
								var x = (HP[_correct][AD] && HP[_correct][AD][q]) ? HP[_correct][AD][q] : '';
								qDetails += hpHiddenField(Q+'correct', x);
							}
							if (JCross[4]) qDetails += hpHiddenField(Q+'clue', clue);
							if (JCross[5]) {
								var x = (HP[_wrong][AD] && HP[_wrong][AD][q]) ? HP[_wrong][AD][q] : '';
								qDetails += hpHiddenField(Q+'wrong', x);
							}
							if (JCross[6]) {
								var x = HP[_clues][q] ? HP[_clues][q] : 0;
								qDetails += hpHiddenField(Q+'clues', x);
							}
							if (JCross[7]) {
								var x = (HP[_hints][AD] && HP[_hints][AD][q]) ? HP[_hints][AD][q] : 0;
								qDetails += hpHiddenField(Q+'hints', x);
							}
							if (JCross[8]) {
								var x = (HP[_checks][AD] && HP[_checks][AD][q]) ? HP[_checks][AD][q] : '';
								qDetails += hpHiddenField(Q+'checks', x);
							}
						} // end for i
					} // end if clue
				} // end if q
			} // end for col
		} // end for row
		if (JCross[2]) { // show number of letters
			qDetails = hpHiddenField('JCross_letters', letters) + qDetails;
		}
		if (JCross[1]) { // show penalties
			var x = (window.Penalties) ? Penalties : 0;
			qDetails = hpHiddenField('JCross_penalties', x) + qDetails;
		}
	}
	return qDetails;
}
function GetJCrossClue(id) {
	var obj = (document.getElementById) ? document.getElementById(id) : null;
	return (obj) ? GetTextFromNodeN(obj, 'Clue') : '';
}
function GetJCrossWord(a, r, c, goDown) {
	// a is a 2-dimensional array of letters, r is a row number, c is a column number
	var s = '';
	while (r<a.length && c<a[r].length && a[r][c]) {
		s += a[r][c];
		if (goDown) {
			r++;
		} else {
			c++;
		}
	}
	return s;
}
function GetJMatchText(q, className) {
	var obj = (document.getElementById) ? document.getElementById('Questions') : null;
	return (obj) ? GetTextFromNodeN(obj, className, q) : '';
}
function GetJMatchRHS(v, q, getCorrect) {
	var rhs = '';
	if (v==5.1 || v==6.1) { // Drag-and-drop
		var max_i = (window.F && window.D) ? D.length : 0;
		for (var i=0; i<max_i; i++) {
			if (F[q][1]==D[i][getCorrect ? 1 : 2]) break;
		}
		if (i<max_i) rhs = D[i][0];
	} else if (v==5 || v==6) { // drop-down list of options
		var obj=document.getElementById(Status[q][2]);
		if (obj) { // not correct yet
			if (getCorrect) {
				var k = GetKeyFromSelect(obj);
				var i_max = obj.options.length;
				for (var i=0; i<i_max; i++) {
					if (obj.options[i].value==k) break;
				}
				if (i>=i_max) i = 0; // shouldn't happen
			} else {
				// get current guess, if any
				var i = obj.selectedIndex;
			}
			if (i) rhs = obj.options[i].innerHTML;
		} else { // correct
			rhs = GetJMatchText(q, 'RightItem');
		}
	}
	return rhs;
}
function GetJMixQuestionDetails(hp, v) {
	qDetails = '';
	// check the quiz version
	if (hp==5 || hp==6) {
		var q = 0; // question number
		// format 'Q' (a padded, two-digit version of 'q')
		var Q = getQ('JMix', q);
		// add separator, if required
		if (JMix[0]) qDetails += makeSeparator(Q);
		// add 'score' for this question
		var score = HP[_correct]==null ? 0 : ((Segments.length-Penalties)/Segments.length);
		qDetails += hpHiddenField(Q+'score', Math.floor(score*100)+'%');
		if (JMix[1]) { // number of wrong guesses
			qDetails += hpHiddenField(Q+'wrongGuesses', Penalties);
		}
		if (JMix[2]) { // right answer
			var x = (HP[_correct][q]) ? HP[_correct][q] : '';
			qDetails += hpHiddenField(Q+'correct', x);
		}
		if (JMix[3]) { // wrong answer(s)
			var x = (HP[_wrong][q]) ? HP[_wrong][q] : '';
			qDetails += hpHiddenField(Q+'wrong', x);
		}
		if (JMix[5]) { // checks
			var x = (HP[_checks][q]) ? HP[_checks][q] : 0;
			qDetails += hpHiddenField(Q+'checks', x);
		}
		if (JMix[6]) { // hints
			var x = (HP[_hints][q]) ? HP[_hints][q] : 0;
			qDetails += hpHiddenField(Q+'hints', x);
		}
	}
	return qDetails;
}
function GetJMixSequence(indexes) {
	var s = new Array();
	for (var i=0; i<indexes.length; i++) {
		s[i] = JMix[4] ? indexes[i] : GetJMixSegmentText(indexes[i]);
	}
	return s;
}
function GetJMixSegmentText(index){
	var i_max = Segments.length;
	for (var i=0; i<i_max; i++) {
		if (Segments[i][1] == index) break;
	}
	return (i<i_max) ? Segments[i][0] : '';
}
function GetJQuizQuestionDetails(hp, v) {
	var qDetails = '';
	// HP5.5 uses "Status" for v5 and v6 JMatch quizzes (HP6 uses "State")
	// var hp =  (window.Status) ? 5 : (window.State) ? 6 : 0;
	// check the quiz version
	if (hp==5 || hp==6) {
		// get details for each question
		var max_q = (hp==5) ? Status.length : State.length;
		for (var q=0; q<max_q; q++) {
			// skip this question if it was not used (HP6 v6 only)
			if (hp==6 && !State[q]) continue;
			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JQuiz', q);
			// add separator
			if (JQuiz[0]) qDetails += makeSeparator(Q);
			if (hp==6 && JQuiz[11]) { // question type
				var x = parseInt(I[q][2]);
				x = (x==0) ? 'multiple-choice' : (x==1) ? 'short-answer' : (x==2) ? 'hybrid' : (x==3) ? 'multi-select' : 'n/a';
				qDetails += hpHiddenField(Q+'type', x);
			}
			// score (as %)
			var x = (hp==5) ? Status[q][4]*10 : I[q][0]*State[q][0];
			if (x<0) x = 0;
			qDetails += hpHiddenField(Q+'score', Math.floor(x)+'%');
			if (hp==6 && JQuiz[10]) { // weighting
				qDetails += hpHiddenField(Q+'weighting', I[q][0]);
			}
			if (JQuiz[1]) { // question text
				var x = (hp==5) ? I[q][0] : (document.getElementById) ? GetTextFromNodeN(document.getElementById('Q_'+q), 'QuestionText') : '';
				qDetails += hpHiddenField(Q+'question', x);
			}
			if (JQuiz[2]) { // student's correct answers
				var x = (HP[_correct][q]) ? HP[_correct][q] : '';
				qDetails += hpHiddenField(Q+'correct', x);
			}
			if (JQuiz[3]) { // ignored and wrong answers
				var x = (hp==5) ? new Array() : GetJQuizAnswerDetails(q, 1);
				if (hp==5) {
					for (var i=0; i<I[q][1].length; i++) {
						var correct = HP[_correct][q] ? HP[_correct][q] : '';
						if (I[q][1][i][0] && I[q][1][i][0].toUpperCase()!=correct.toUpperCase()) {
							x[x.length] = I[q][1][i][0];
						}
					}
				}
				if (DB[0] || x) qDetails += hpHiddenField(Q+'other', x);
			}
			if (hp==6 && JQuiz[7]) { // all selected answers
				var x = GetJQuizAnswerDetails(q, 0);
				qDetails += hpHiddenField(Q+'selected', x);
			}
			if (JQuiz[8]) { // wrong answers
				var x = (HP[_wrong][q]) ? HP[_wrong][q] : '';
				qDetails += hpHiddenField(Q+'wrong', x);
			}
			if (hp==6 && JQuiz[9]) { // ignored answers
				var x = GetJQuizAnswerDetails(q, 4);
				qDetails += hpHiddenField(Q+'ignored', x);
			}
			if (JQuiz[4]) { // number of hints
				var x = (HP[_hints][q]) ? HP[_hints][q] : 0;
				qDetails += hpHiddenField(Q+'hints', x);
			}
			if (JQuiz[5] || JQuiz[12]) { // number of checks
				if (JQuiz[12]) { // strictly checks only
					var x = (HP[_checks][q]) ? HP[_checks][q] : 0;
				} else { // checks (+ hints in HP6)
					var x = (hp==5) ? Status[q][1] : (State[q][2]-1);
				}
				qDetails += hpHiddenField(Q+'checks', x);
			}
			if (JQuiz[13]) { // ShowAnswer button
				var x = (HP[_clues][q]) ? HP[_clues][q] : 0;
				qDetails += hpHiddenField(Q+'clues', x);
			}
		} // end for
	} // end if
	return qDetails;
}
function GetTextFromNodeN(obj, className, n) {
	// returns the text under the nth node of obj with the target class name
	var txt = '';
	if (obj && className) {
		if (typeof(n)=='undefined') {
			n = 0;
		}
		var nodes = GetNodesByClassName(obj, className);
		if (n<nodes.length) {
			txt += GetTextFromNode(nodes[n]);
		}
	}
	return txt;
}
function GetNodesByClassName(obj, className) {
	// returns an array of nodes with the target classname
	var nodes = new Array();
	if (obj) {
		if (className && obj.className==className) {
			nodes.push(obj);
		} else if (obj.childNodes) {
			for (var i=0; i<obj.childNodes.length; i++) {
				nodes = nodes.concat(GetNodesByClassName(obj.childNodes[i], className));
			}
		}
	}
	return nodes;
}
function GetTextFromNode(obj) {
	// return text in (and under) a single DOM node
	var txt = '';
	if (obj) {
		if (obj.nodeType==3) {
			txt = obj.nodeValue + ' ';
		}
		if (obj.childNodes) {
			for (var i=0; i<obj.childNodes.length; i++) {
				txt += GetTextFromNode(obj.childNodes[i]);
			}
		}
	}
	return txt;
}
function GetJQuizAnswerDetails(q, flag) {
	// flag : the type of information required about the student's answers
	//	0 : all student's answers
	//	1 : student's wrong and ignored answers
	//	2 : student's correct answers
	//	3 : student's wrong answers
	//	4 : ignored answers
	var x = State[q][5]; //Sequence of answers chosen by number
	if (I[q][2]=='3') { // multi-select
		if (flag==4) {
			var x = new Array();
		} else {
			// get required part of 'x' and convert to array
            if (x.charAt(0)=='|') {
                // HP 6.0 and 6.1 (always has leading bar)
    			var i = x.lastIndexOf('|');
    			var x = x.substring((flag==2 ? (i+1) : 1), ((flag==0 || flag==2) ? x.length : i)).split('|');
            } else {
                // HP 6.2 (no leading delimiter)
    			var i = x.lastIndexOf(' | ');
    			var x = x.substring((flag==2 ? (i+3) : 0), ((flag==0 || flag==2) ? x.length : i)).split(' | ');
            }
		}
		for (var i=0; i<x.length; i++) {
			var a = new Array();
			for (var ii=0; ii<x[i].length; ii++) {
				if (x[i].charAt(ii)=='Y') {
					var s = JQuiz[6] ? String.fromCharCode(97+ii) : I[q][3][ii][0];
					if (s && s.replace && window.RegExp) {
						s = s.replace(new RegExp('\\+', 'g'), '&#43;');
					}
					a.push(s);
				}
			}
			x[i] = a.join('+');
		}
	} else if (x) { // multiple-choice, short-answer and hybrid
        if (x.charAt(x.length-1)==',') {
            // HP 6.0 and 6.1 (always has trailing comma)
            x = x.substring(0, x.length-1).split(',');
        } else {
            // HP 6.2 (short answer also contains student entered text)
            x = x.split(' | ');
        }
		if (flag) {
			var a = new Array();
			if (flag==1 || flag==2 || flag==3) {
				for (var i=0; i<x.length; i++) {
                    var is_correct = false;
                    if (x[i].length==1) { // single letter
                        var ii = x[i].charCodeAt(0) - 65;
                        if (I[q][3] && I[q][3][ii] && I[q][3][ii][2]) {
                            var is_correct = true;
                        }
                    }
                    if (is_correct) {
                        if (flag==2) {
                            a.push(x[i]);
                        }
                    } else {
                        if (flag==1 || flag==3) {
                            a.push(x[i]);
                        }
                    }
				}
			}
			if (flag==1) {
				x = a;
				a = new Array();
			}
			if (flag==1 || flag==4) {
				for (var i=0; i<I[q][3].length; i++) {
					var s = String.fromCharCode(65+i);
					for (var ii=0; ii<x.length; ii++) {
						if (x[ii]==s) break;
					}
					if (ii==x.length) a.push(s);
				}
			}
			x = a;
		}
		// convert answer indexes to values, if required
		if (JQuiz[6]==false) {
			for (var i=0; i<x.length; i++) {
                if (x[i].length==1) { // single letter
                    var ii = x[i].charCodeAt(0) - 65;
                    if (I[q][3] && I[q][3][ii]) {
        				x[i] = I[q][3][ii][0];
                    }
                }
			}
		}
	} else {
		x = new Array();
	}
	return x;
}
function GetRhubarbDetails(v) {
	qDetails = '';
	if (v==6) {
		var q = 0; // always zero
		var Q = getQ('Rhubarb', q);
		if (document.title) { // use quiz title as question name
			qDetails += hpHiddenField(Q+'name', document.title);
		}
		if (Rhubarb[0]) { // correct words
			var x = (HP[_correct][q]) ? HP[_correct][q] : '';
			if (Rhubarb[1]) { // count of correct words
				for (var i=0,ii=0; i<x.length; i++) {
					if (x[i]) ii++;
				}
				x = ii;
			}
			qDetails += hpHiddenField(Q+'correct', x);
		}
		if (Rhubarb[2]) { // wrong words
			var x = (HP[_wrong][q]) ? HP[_wrong][q] : '';
			if (Rhubarb[3]) { // count of wrong words
				x = x.length;
			}
			qDetails += hpHiddenField(Q+'wrong', x);
		}
		if (Rhubarb[4]) { // ignored
			var x = '';
			qDetails += hpHiddenField(Q+'ignored', x);
		}
		if (Rhubarb[5]) { // hints
			var x = (HP[_hints][q]) ? HP[_hints][q] : '';
			qDetails += hpHiddenField(Q+'hints', x);
		}
	}
	return qDetails;
}
function GetSequiturDetails(v) {
	qDetails = '';
	if (v==6) {
		var q = 0; // always zero
		var Q = getQ('Sequitur', q);
		if (document.title) { // use quiz title as question name
			qDetails += hpHiddenField(Q+'name', document.title);
		}
		if (Sequitur[0]) { // number of correct buttons chosen
			var x = (HP[_correct][q]) ? HP[_correct][q] : '';
			qDetails += hpHiddenField(Q+'correct', x);
		}
		if (Sequitur[1]) { // number of wrong buttons chosen
			var x = (HP[_wrong][q]) ? HP[_wrong][q] : '';
			qDetails += hpHiddenField(Q+'wrong', x);
		}
	}
	return qDetails;
}
// *********************
//	click event handlers
// *********************
function hpClick(x, args) {
	// x is the button type
	// args is either empty, a single argument, or an array of arguments
	var btn = (x==1) ? 'Hint' : (x==2) ? 'Clue' : (x==3) ? 'Check' : (x==4)  ? 'Enter' : '';
	if (btn) {
		// convert args to array, if necessary
		var t = typeof(args);
		if (t=='object') {
			// do nothing (args is already an array)
		} else if (t=='undefined') {
			args = new Array();
		} else {
			args = new Array(''+args);
		}
		// call handler for this kind of button
		var x = eval('hpClick'+btn+'('+hpVersion()+','+hpQuizType()+','+hpQuizVersion()+',args);');
	}
}
function hpClickHint(hp, t, v, args) {
	if (t==2 || t==5 || t==6 || t==7) { // JCloze, JMix, JQuiz, Rhubarb
		var q = args[0]; // clue/question number
		if (!HP[_hints][q]) HP[_hints][q] = 0;
		HP[_hints][q]++;
	}
	if (t==3) { // JCross
		if (v==6 || v==5) {
			var q = args[0]; // clue/question number
			var AD = args[1]; // direction ('A' or 'D')
			if (!HP[_hints][AD]) HP[_hints][AD] = new Array();
			if (!HP[_hints][AD][q]) HP[_hints][AD][q] = 0;
			HP[_hints][AD][q]++;
		}
	}
	return true;
}
function hpClickClue(hp, t, v, args) {
	if (t==2 || t==3 || t==6) { // JCloze or JCross, or JQuiz (ShowAnswer button)
		var q = args[0]; // clue/question number
		if (!HP[_clues][q]) HP[_clues][q] = 0;
		HP[_clues][q]++;
	}
	return true;
}
function hpClickCheck(hp, t, v, args) {
	if (t==2) { // JCloze
    if (v==5 || v==6) {
			var r = hpRottmeier();
			var already_correct = 'true';
			if (r==0) {
				already_correct = (hp==5) ? 'State[i][4]==1' : 'State[i].AnsweredCorrectly==true';
			} else if (r==1) { // DropDown
				already_correct = 'GapList[i][1].GapLocked==true';
			} else if (r==2.1) { // Find-It 3a
				already_correct = 'GapList[i][1].ErrorFound==true';
			} else if (r==2.2) { // Find-It 3b
				already_correct = 'GapList[i][1].GapSolved==true';
			}
			var i_max = I.length;
			for (var i=0; i<i_max; i++) {
				if (eval(already_correct)) continue;
				var g = '';
				if (r==0 || r==2.2) {
					g = GetGapValue(i);
				} else if (r==1) { // DropDown
					if (hp==5) {
						g = eval('document.Cloze.Gap'+i+'.value');
					} else if (hp==6) {
						var ii = Get_SelectedDropValue(i);
                        if (isNaN(ii) || ii<0) { // 'null' || -1
                            g = ''; // no guess yet
                        } else {
                			if (window.MakeIndividualDropdowns) {
                                var is_wrong = (ii!=0);
                                g = I[i][1][ii][0];
                            } else {
                                var is_wrong = (ii!=i);
                                g = I[ii][1][0][0];
                            }
                        }
					}
				} else if (r==2.1 && i==args[0]) { // Find-It 3a
					g = I[i][1][0][0];
				}
				if (g) {
					if (!HP[_checks][i]) HP[_checks][i] = 0;
					HP[_checks][i]++;
					if (!HP[_guesses][i]) HP[_guesses][i] = new Array();
					var ii = HP[_guesses][i].length;
					// is this a new guess at this gap?
					if (ii==0 || g!=HP[_guesses][i][ii-1]) {
						HP[_guesses][i][ii] = g;
                        if (r==1) {
                            // Rottmeier DropDown 2.4
                            // do nothing
                        } else {
    						var G = g.toUpperCase();
    						var ii_max = I[i][1].length;
    						for (var ii=0; ii<ii_max; ii++) {
    							if (window.CaseSensitive) {
    								if (g==I[i][1][ii][0]) break;
    							} else {
    								if (G==I[i][1][ii][0].toUpperCase()) break;
    							}
    						}
                            var is_wrong = (ii==ii_max);
                        }
						if (is_wrong) { // guess is wrong
							if (!HP[_wrong][i]) HP[_wrong][i] = new Array();
							var ii_max = HP[_wrong][i].length;
							for (var ii=0; ii<ii_max; ii++) {
								if (HP[_wrong][i][ii]==g) break;
							}
							if (ii==ii_max) {
								HP[_wrong][i][ii] = g;
							}
						} else { // guess is correct
							HP[_correct][i] = g;
						}
					}
				}
			}
		}
	}
	if (t==3) { // JCross
		if (v==5 || v==6) {
			var q = args[0]; // clue/question number
			for (var row=0; row<L.length; row++) {
				for (var col=0; col<L[row].length; col++) {
					var q = (v==5) ? C[row][col] : CL[row][col];
					if (q) {
						hpClickCheckJCrossV5V6(hp, v, 'A', q, row, col);
						hpClickCheckJCrossV5V6(hp, v, 'D', q, row, col);
					}
				}
			}
		}
	}
	if (t==4) { // JMatch
		var a = new Array();
		var extra = ''; // extra js code to eval(uate)
		var guess = ''; // js code to eval(uate) guess
		var correct = ''; // js code to eval(uate) correct answer
		if (window.D && window.F) {
			// drag-and-drop, i.e. v5+ and v6+ (HP5 and HP6)
			a = F;
			guess = 'GetJMatchRHS(v,i)';
			correct = 'GetJMatchRHS(v,i,true)';
		} else  if (window.GetKeyFromSelect) {
			// HP6 v6
			a = Status;
			guess = 'GetJMatchRHS(v,i)';
			correct = 'GetJMatchRHS(v,i,true)';
		} else if (window.GetAnswer) {
			// HP5 v6,v5
			a = I;
			guess = "(I[i][2]==0||I[i][0]=='')?'':GetAnswer(i)";
			correct = 'I[i][3])';
		} else if (window.Draggables) {
			// HP5 v4
			a = Draggables;
			s = "Draggables[i].correct=='1'";
		} else if (window.CorrectAnswers) {
			// HP5 v3
			a = CorrectAnswers;
			guess = 'document.QuizForm.elements[i*2].selectedIndex';
			correct = 'CorrectAnswers[i]';
		}
		for (var i=0; i<a.length; i++) {
			// check this match has not already been finished
			if (!HP[_correct][i]) {
				// do extra setup, if necessary
				if (extra) eval(extra);
				// get the guess, if any
				var g = ''+eval(guess);
				if (g) {
					// is the guess correct?
					if (g==eval(correct)) {
						HP[_correct][i] = g;
					} else { // wrong answer
						// initialize wrong guess array if necessary
						if (!HP[_wrong][i]) HP[_wrong][i] = new Array();
						// check to see if the guess is already in the guess array
						var i_max = HP[_wrong][i].length;
						for (var ii=0; ii<i_max; ii++) {
							if (HP[_wrong][i][ii]==g) break;
						}
						// add the guess if it was not found
						if (ii==i_max) {
							HP[_wrong][i][ii]=g;
						} else {
							g = null; // this is not a new answer
						}
					}
					// increment checks for this question, if necessary
					if (g) {
						if (!HP[_checks][i]) HP[_checks][i] = 0;
						HP[_checks][i]++;
					}
				}
			}
		}
	} // end if JMatch
	if (t==5) { // JMix
		// get question number (always 0)
		var q = args[0];
		// check question has not already been answered correctly
		if (!HP[_correct][q]) {
			// match current guess against possible correct answers
			var a_max = Answers.length;
			for (var a=0; a<a_max; a++) {
				var i_max = Answers[a].length;
				for (var i=0; i<i_max; i++) {
					if (Answers[a][i] != GuessSequence[i]) break;
				}
				if (i==i_max) break; // correct answer was found
			}
			// at this point, (a==a_max) means guess is wrong
			// get array of segment texts in this g(uess)
			var g = GetJMixSequence(GuessSequence);
			// convert g(uess) array and to a s(tring)
			var s = '';
			var i_max = g.length;
			for (var i=0; i<i_max; i++) {
				g[i] = trim(g[i]);
				if (g[i]!='') {
					s += (s=='' ? '' : '+') +  g[i];
				}
			}
			if (s) {
				if (a==a_max) { // wrong
					if (!HP[_wrong][q]) HP[_wrong][q] = new Array();
					var i = HP[_wrong][q].length;
					HP[_wrong][q][i] = s;
				} else { // correct
					HP[_correct][q] = s;
				}
				// increment checks for this question
				if (!HP[_checks][q]) HP[_checks][q] = 0;
				HP[_checks][q]++;
			}
		}
	}
	if (t==6) { // JQuiz
		if (hp==5 || hp==6) {
			var q = args[0]; // clue/question number
			if (hp==5) {
				if (v==5) {
					var g = TrimString(eval('BottomFrame.document.QForm' + q + '.Guess').value);
				} else if (v==6) {
					var g = TrimString(eval('document.QForm.Guess').value);
				}
			} else  { // HP 6
				var g = args[1];
			}
			// increment check count
			if (!HP[_checks][q]) HP[_checks][q] = 0;
			HP[_checks][q]++;
			if (g) {
				var G = g.toUpperCase(); // used for shortanswer only
				var correct_answer = ''; // used for multiselect only
				// set index of answer array in I (the question array)
				var ans = (hp==5) ? 1 : 3;
				var i_max = I[q][ans].length;
				for (var i=0; i<i_max; i++) {
					// is this a (possible) correct answer?
					if (hp==5 || (hp==6 && I[q][ans][i][2])) {
						if (hp==6 && I[q][2]==3) { // multiselect
							correct_answer += (correct_answer  ? '&#43;' : '') + I[q][ans][i][0];
						} else { // multichoice, shortanswer
							if (window.CaseSensitive) {
								if (g==I[q][ans][i][0]) break;
							} else {
								if (G==I[q][ans][i][0].toUpperCase()) break;
							}
						}
					}
				}
				if (i==i_max && g!=correct_answer) { // wrong
					if (!HP[_wrong][q]) HP[_wrong][q] = new Array();
					var i_max = HP[_wrong][q].length;
					for (var i=0; i<i_max; i++) {
						if (HP[_wrong][q][i]==g) break;
					}
					if (i==i_max) HP[_wrong][q][i] = g;
				} else {
					HP[_correct][q] = g;
				}
			}
		}
	}
	if (t==7) { // Rhubarb
		if (hp==6) {
			var q = 0; // question number (always zero)
			var g = args[0]; // InputWord from CheckGuess()
			if (g) {
				var G = g.toUpperCase();
				var i_max = Words.length;
				for (var i=0; i<i_max; i++) {
					if (G==Words[i].toUpperCase()) break;
				}
				if (i<i_max) { // correct
					if (!HP[_correct][q]) HP[_correct][q] = new Array();
					HP[_correct][q][i] = g;
				} else { // wrong
					if (!HP[_wrong][q]) HP[_wrong][q] = new Array();
					var i_max = HP[_wrong][q].length;
					for (var i=0; i<i_max; i++) {
						if (G==HP[_wrong][q][i].toUpperCase()) break;
					}
					if (i==i_max) HP[_wrong][q][i] = g;
				}
			}
		}
	}
	if (t==8) { // Sequitur
		if (hp==6) {
			var q = 0; // question number (always zero)
			if (CurrentCorrect==args[0]) { // correct button chosen
				if (!HP[_correct][q]) HP[_correct][q] = 0;
				HP[_correct][q]++;
			} else {
				if (!HP[_wrong][q]) HP[_wrong][q] = 0;
				HP[_wrong][q]++;
			}
		}
	}
	//return true;
}
function hpClickCheckJCrossV5V6(hp, v, AD, q, row, col) {
	// v is the version of Hot Potatoes
	// AD is the direction ('A' or 'D')
	// make sure HP[_checks] and HP[_correct] are initialized
	if (!HP[_checks][AD]) HP[_checks][AD] = new Array();
	if (!HP[_correct][AD]) HP[_correct][AD] = new Array();
	// get clue, if any
	var clue = (hp==5) ? eval('window.'+AD) : GetJCrossClue('Clue_'+AD+'_' + q);
	// is this a question that has not been answered correctly yet?
	if (clue && !HP[_correct][AD][q]) {
		var check = false;
		var guess = GetJCrossWord(G, row, col, (AD=='D'));
		var correct = GetJCrossWord(L, row, col, (AD=='D'));
		if (guess==correct) {
			HP[_correct][AD][q] = correct;
			check = true;
		} else if (guess) {
			// make sure HP[_wrong] is initialized
			if (!HP[_wrong][AD]) HP[_wrong][AD] = new Array();
			if (!HP[_wrong][AD][q]) HP[_wrong][AD][q] = new Array();
			// check this guess has not been entered before
			var i_max = HP[_wrong][AD][q].length;
			for (var i=0; i<i_max; i++) {
				if (HP[_wrong][AD][q]==guess) break;
			}
			// add the guess if it has not been entered before
			if (i>=i_max) {
				HP[_wrong][AD][q][i] = guess;
				check = true;
			}
		}
		// update HP[_checks], if necessary
		if (check) {
			if (!HP[_checks][AD]) HP[_checks][AD] = new Array();
			if (!HP[_checks][AD][q]) HP[_checks][AD][q] = 0;
			HP[_checks][AD][q]++;
		}
	}
}
function hpClickEnter(hp, t, v, args) {
	if (t==3) { // JCross
		var q = args[0]; // clue/question number
		if (!HP[_enter][q]) HP[_enter][q] = 0;
		HP[_enter][q]++;
	}
	return true;
}
function GetJMatchQuestionDetails(hp, v) {
	var qDetails = '';
	// HP5.5 uses "I" for v5 and v6 JMatch quizzes
	// var hp5 = (window.I) ? true : false;
	// check the quiz version
	if (hp==5 || hp==6) {
		if (JMatch[1] && v==6.1) { // attempts
			qDetails += hpHiddenField('JMatch_attempts', Penalties+1);
		}
		// get number of questions
		var max_q = (hp==5 || v==6) ? Status.length : F.length;
		// get details for each question
		for (var q=0; q<max_q; q++) {
			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JMatch', q);
			// add separator, if required
			if (JMatch[0] && (JMatch[1] || JMatch[2] || JMatch[3])) {
				qDetails += makeSeparator(Q);
			}
			if (JMatch[1] && (hp==5 || v==6)) { // attempts
				qDetails += hpHiddenField(Q+'attempts', Status[q][1]);
			}
			if (JMatch[2]) { // LHS text (the question)
				var x = (v==5) ? I[q][0] : (v==6) ? GetJMatchText(q, 'LeftItem') : F[q][0];
				qDetails += hpHiddenField(Q+'lhs', x);
			}
			if (JMatch[3]) { // correct answer (if any)
				var x = HP[_correct][q] ? HP[_correct][q] : '';
				qDetails += hpHiddenField(Q+'correct', x);
			}
			if (JMatch[4]) { // wrong answers (if any)
				var x = HP[_wrong][q] ? HP[_wrong][q] : '';
				qDetails += hpHiddenField(Q+'wrong', x);
			}
			if (JMatch[5]) { // checks
				var x = HP[_checks][q] ? HP[_checks][q] : 0;
				qDetails += hpHiddenField(Q+'checks', x);
			}
		} // end for
	}
	return qDetails;
}
// *********************
//   library functions
// *********************
function pad(i, l) {
	var s = (i+'');
	while (s.length<l) s = '0' + s;
	return s;
}
function getQ(section, q) {
	// Q is a padded, two-digit version of the question number, 'q', prefixed by 'section'
	return section + '_q' + (q<9 ? '0' : '') + (q+1) + '_';
}
function makeSeparator(Q) {
	return 	is_LMS() ? '' : hpHiddenField(Q.substring(0, Q.length-1), '---------------------------------');
}
function hpHiddenField(name, value, comma, forceHTML) {
	var field = '';
	var t = typeof(value);
	if (t=='string') {
		value = encode_entities(value);
	} else if (t=='object') { // array
		var values = value;
		var i_max = values.length;
		value = '';
		if (comma==null) comma = ',';
		for (var i=0; i<i_max; i++) {
			values[i] = trim(values[i]);
			if (values[i]!=null && values[i]!='') {
				value += (i==0 ? '' : comma) +  encode_entities(values[i]);
			}
		}
	}
	if (is_LMS() && !forceHTML) {
		if (value && value.indexOf && value.indexOf('<')>=0 && value.indexOf('>')>=0) {
			value = '<![CDATA[' + value + ']]>';
		}
		field = '<field><fieldname>' + name + '</fieldname><fielddata>' + value + '</fielddata></field>';
	} else {
		field = '<input type=hidden name="' + name + '" value="' + value + '">';
	}
	return field;
}
function trim(s) {
	if (s==null) s = '';
	var i = 0;
	var ii = s.length;
	while (i<ii && s.charAt(i)==' ') {
		i++;
	}
	while (ii>i && s.charAt(ii-1)==' ') {
		ii--;
	}
	return s.substring(i, ii);
}
function encode_entities(s_in) {
	var i_max = (s_in) ? s_in.length : 0;
	var s_out = '';
	for (var i=0; i<i_max; i++) {
		var c = s_in.charCodeAt(i);
		// 34 : double quote .......["] &amp;
		// 38 : single quote .......['] &apos;
		// 43 : plus sign ..........[+]
		// 44 : comma ..............[,]
		// 60 : left angle bracket .[<] &lt;
		// 62 : right angle bracket [>] &gt;
		// >=128 multibyte character
		s_out += (c==43 || c==44 || c>=128) ? ('&#x' + pad(c.toString(16), 4) + ';') : s_in.charAt(i);
	}
	return s_out;
}
// *********************
//	initialization
//	  functions
// *********************
function getTime(obj) {
	obj = obj ? obj : new Date();
	// get year, month and day
	//	for an LMS : yyyy-mm-dd
	//	for email  : DayName MonthName dd yyyy
	var s = is_LMS() ?
		obj.getFullYear() + '-' + pad(obj.getMonth()+1, 2) + '-' + pad(obj.getDate(), 2) :
		MSG[16][obj.getDay()] + ' ' + MSG[17][obj.getMonth()] + ' ' + pad(obj.getDate(), 2) + ' ' + obj.getFullYear()
	;
	// get hours, minutes and seconds (hh:mm:ss)
	s += ' ' + pad(obj.getHours(), 2) + ':' + pad(obj.getMinutes(), 2) + ':' + pad(obj.getSeconds(), 2);
	// get time difference
	//	for an LMS : +xxxx
	//	for email  : GMT+xxxx
	var x = obj.getTimezoneOffset(); // e.g. -540
	if (!isNaN(x)) {
		s += ' ' + (is_LMS() ? '' : 'GMT') + (x<0 ? '+' : '-');
		x = Math.abs(x);
		s += pad(parseInt(x/60), 2) + pad(x - (parseInt(x/60)*60), 2);
	}
	return s;
}
function getFunc(fn) {
	if (typeof(fn)=='string') {
		fn = eval('window.' + fn);
	}
	return (typeof(fn)=='function') ? fn : null;
}
function getFuncCode(fn, extraCode, anchorCode, beforeAnchor) {
	var s = '';
	var obj = getFunc(fn);
	if (obj) {
		s = obj.toString();
		var i1 = s.indexOf('{')+1;
		var i2 = s.lastIndexOf('}');
		if (i1>0 && i1<i2) {
			s = s.substring(i1, i2);
		}
	}
	if (extraCode) {
		if (anchorCode) {
			if (beforeAnchor) {
				s = replaceLast(anchorCode, extraCode + anchorCode, s);
			} else {
				s = replaceLast(anchorCode, anchorCode + extraCode, s);
			}
		} else {
			if (beforeAnchor) {
				s = extraCode + s;
			} else {
				s = s + extraCode;
			}
		}
	}
	return s;
}
function getArgsStr(args, addQuotes) {
	// make s(tring) version of function args array
	var s = '';
	var i_max = args.length;
	for (var i=0; i<i_max; i++) {
        if (addQuotes) {
            s += '"' + args[i] + '",';
        } else {
            if (s) {
                s += ',';
            }
            s += args[i];
        }
	}
	return s;
}
function getFuncArgs(fn, flag) {
	// flag==0 : return args as string
	// flag==1 ; return args as array
	var i = 0;
	var a = new Array();
	var obj = getFunc(fn);
	if (obj) {
		var s = obj.toString();
		var i1 = s.indexOf('(') + 1;
		var i2 = s.indexOf(')', i1);
		// add args to a(rray)
		while (i1>0 && i1<i2) {
			var i3 = s.indexOf(',', i1); // next comma
			if (i3<0 || i3>i2) i3 = i2;
			a[i++] = trim(s.substring(i1, i3));
			i1 = i3+1;
		}
	}
	return flag ? a : getArgsStr(a);
}
function getPrompt(fn) {
	// the LoginPrompt is the text string in the first prompt(...) statement
	//	v5 : in the StartUp function
	//	v6 : in the GetUserName function
	// Note: netscape uses double-quote as delimiter, others use single quote
	var s = getFuncCode(fn);
	var i1 = s.indexOf('prompt') + 8;
	var i2 = s.indexOf(s.charAt(i1-1), i1);
	var p = (i1>=8 && i2>i1) ? s.substring(i1, i2) : '';
	// make sure browser has decoded the unicode prompt properly
	// this check is mainly for ns4, but there may be others
	if (window.RegExp) {
		var r = new RegExp('u([0-9A-F]{4})');
		var m = r.exec(p);
		while (m) {
			p = p.replace(m[0], '&#' + parseInt(m[1], 16) + ';');
			m = r.exec(p);
		}
	}
	return p;
}
function getStartUpCode(fn) {
	// the main initialization code comes from the StartUp function
	//	v5 : the code before "UserName", if any,
	//	     and the code after the 2nd subsequent '}'
	//	v6 : the code before and after 'GetUserName();'
	//	     i.e. all the code except the call to 'GetUserName();'
	var s = getFuncCode(fn);
	var i1 = s.indexOf('GetUserName();');
	if (i1>=0) { // v6
		var i2 = i1 + 14;
	} else { // v5
		var i1 = s.indexOf('UserName');
		var i2 = s.indexOf('}', s.indexOf('}', i1+8)+1)+1;
	}
	return (0<i1 && i1<i2) ? s.substring(0, i1) + s.substring(i2) : '';
}
function is_LMS() {
	if (!window.hpCheckedForm) {
		window.hpCheckedForm = true;
		window.hpFoundForm = hpFindForm('store') ? true : false;
	}
	return hpFoundForm;
}
function hpFeedback() {
	if (FEEDBACK[0]) {
		var url = '';
		var html = '';
		if (FEEDBACK[1] && FEEDBACK[2]) { // formmail
			html += '<html><body>'
				+ '<form action="' + FEEDBACK[0] + '" method="POST">'
				+ '<table border="0">'
				+ '<tr><th valign="top" align="right">' + FEEDBACK[7] + ':</th><td>' + document.title + '</td></tr>'
				+ '<tr><th valign="top" align="right">' + FEEDBACK[8] + ': </th><td>'
			;
			if (typeof(FEEDBACK[1])=='string') {
				html += FEEDBACK[1] + hpHiddenField('recipient', FEEDBACK[1], ',', true);
			} else if (typeof(FEEDBACK[1])=='object') {
				var i_max = FEEDBACK[1].length;
				if (i_max==1) { // one teacher
					html += FEEDBACK[1][0][0] + hpHiddenField('recipient', FEEDBACK[1][0][0]+' &lt;'+FEEDBACK[1][0][1]+'&gt;', ',', true);
				} else if (i_max>1) { // several teachers
					html += '<select name="recipient">';
					for (var i=0; i<i_max; i++) {
						html += '<option value="'+FEEDBACK[1][i][1]+'">' + FEEDBACK[1][i][0] + '</option>';
					}
					html += '</select>';
				}
			}
			html += '</td></tr>'
				+	'<tr><th valign="top" align="right">' + FEEDBACK[9] + ':</th>'
				+	'<td><TEXTAREA name="message" rows="10" cols="40"></TEXTAREA></td></tr>'
				+	'<tr><td>&nbsp;</td><td><input type="submit" value="' + FEEDBACK[6] + '">'
				+ 	hpHiddenField('realname', FEEDBACK[2], ',', true)
				+ 	hpHiddenField('email', FEEDBACK[3], ',', true)
				+ 	hpHiddenField('subject', document.title, ',', true)
				+ 	hpHiddenField('title', document.title, ',', true)
				+ 	hpHiddenField('return_link_title', FEEDBACK[10], ',', true)
				+ 	hpHiddenField('return_link_url', 'javascript:self.close()', ',', true)
				+	'</td></tr></table></form></body></html>'
			;
		} else if (FEEDBACK[1]) { // url only
			if (typeof(FEEDBACK[1])=='object') {
				var i_max = FEEDBACK[1].length;
				if (i_max>1) { // several teachers
					html += '<html><body>'
						+ '<form action="' + FEEDBACK[0] + '" method="POST" onsubmit="this.action+=this.recipient.options[this.recipient.selectedIndex].value">'
						+ '<table border="0">'
						+ '<tr><th valign="top" align="right">' + FEEDBACK[7] + ':</th><td>' + document.title + '</td></tr>'
						+ '<tr><th valign="top" align="right">' + FEEDBACK[8] + ': </th><td>'
					;
					html += '<select name="recipient">';
					for (var i=0; i<i_max; i++) {
						html += '<option value="'+FEEDBACK[1][i][1]+'">' + FEEDBACK[1][i][0] + '</option>';
					}
					html += '</select>';
					html += '</td></tr>'
						+	'<tr><td>&nbsp;</td><td><input type="submit" value="' + FEEDBACK[6] + '">'
						+	'</td></tr></table></form></body></html>'
					;
				} else if (i_max==1) { // one teacher
					url = FEEDBACK[0] + FEEDBACK[1][0][1];
				}
			} else if (typeof(FEEDBACK[1])=='string') {
				url = FEEDBACK[0] + FEEDBACK[1];
			}
		} else {
			url = FEEDBACK[0];
		}
		if (url || html) {
			var w = openWindow(url, 'feedback', FEEDBACK[4], FEEDBACK[5], 'RESIZABLE,SCROLLBARS', html);
			if (!w) {
				 // unable to open popup window
				alert(MSG[18]);
			}
		}
	}
}
// ********************
//	intercept clicks
// ********************
function hpNewFunction(f, a, s) {
    if (window.C && C.safari) {
        if (f=='CheckAnswers') {
            if (s.indexOf('TotalChars-State[i].HintsAndChecks/')>=0) {
                // special fix for "CheckAnswers" in JCloze
                s = s.replace(/TotalChars-State\[i\]\.HintsAndChecks/g, '(TotalChars-State[i].HintsAndChecks)');
            }
            if (s.indexOf('TotalChars-GapList[x][1].HintsAndChecks/')>=0) {
                // special fix for "CheckAnswers" in JCloze (Find-It)
                s = s.replace(/TotalChars-GapList\[x\]\[1\]\.HintsAndChecks/g, '(TotalChars-GapList[x][1].HintsAndChecks)');
            }
            if (s.indexOf('CorrectLetters-Penalties/')>=0) {
                // special fix for "CheckAnswers" in JMatch
                s = s.replace(/CorrectLetters-Penalties/g, '(CorrectLetters-Penalties)');
            }
            if (s.indexOf('TotCorrectChoices-Penalties/')>=0) {
                // special fix for "CheckAnswers" in JMix (v6)
                s = s.replace(/TotCorrectChoices-Penalties/g, '(TotCorrectChoices-Penalties)');
            }
            if (s.indexOf('TotalCorrect-Penalties/')>=0) {
                // special fix for "CheckAnswers" in JMix (v6+) Drag-and_Drop
                s = s.replace(/TotalCorrect-Penalties/g, '(TotalCorrect-Penalties)');
            }
        }
        if (s.indexOf('replace(\\[')>=0) {
            s = s.replace(/\\\[/g, '/\\[');
            s = s.replace(/\\\]/g, '\\]/g');
        }
        if (s.indexOf('for (i')>=0 || s.indexOf('for (x')>=0) {
            s = s.replace(/for \(/g, 'for (var ');
        }
        eval('window.' + f + '=function(' + getArgsStr(a) + '){' + s + '}');
    } else {
        eval('window.' + f + '=new Function(' + getArgsStr(a, true) + 's);');
    }
}
function hpInterceptFeedback() {
	// modify the function which writes feedback
	// 	v6: ShowMessage(Feedback)
	//		but Rhubarb prints score in other functions, so use 'CheckFinished'
	//	v5: WriteFeedback(Feedback)
	//	v4: WriteFeedback(Stuff)
	//	v3: WriteFeedback(Feedback) [except JMatch]
	//	v3: CheckAnswer()           [JMatch only]
	var f = '';
	if (window.CheckWord) { // Rhubarb
		f = 'CheckFinished';
		window.FEEDBACK = null;
	} else if (window.ShowText) { // Sequitur
		f = 'CheckAnswer';
		window.FEEDBACK = null;
	} else { // JBC, JCloze, JCross, JMatch, JMix, JQuiz
		f = window.ShowMessage ? 'ShowMessage' : window.WriteFeedback ? 'WriteFeedback' : 'CheckAnswer';
	}
	if (f) {
		var s = getFuncCode(f) + 'Finish();';
		var a = getFuncArgs(f, true);
		if (a[0] && window.FEEDBACK && FEEDBACK[0]) {
			s = a[0] + "+='<br /><br />" + '<a href="javascript:hpFeedback();">' + FEEDBACK[6] + "</A>';" + s;
		}
        hpNewFunction(f, a, s);
	}
}
function hpInterceptHints() {
	// modify the function which shows hints
	//	JBC:    none
	//	JCloze  v3-v6: ShowHint()
	//	JCross  v3: Cheat(), v4: ShowHint(), v5-v6[HP5]: ShowHint(Across,x,y,BoxName), v6[HP6]: ShowHint(Across,ClueNum,x,y,BoxId)
	//	JMatch: none
	//	JMix    v5-v6: CheckAnswer(CheckType=1)
	//	JQuiz   v3: CheckAnswer(ShowHint=true, QNum), v4: CheckAnswer(ShowHint=true), v5-v6[HP5]: CheckAnswer(ShowHint=true,QNum), v6[HP6]: ShowHint(QNum)
	var x = ''; // extra code, if any
	if (window.Cheat) {
		// JCross v3 ?
	} else if (window.ShowHint) {
		var f = 'ShowHint';
		var a = getFuncArgs(f, true);
		if (a.length==0) {
			if (window.FindCurrent) {
				// JCloze v3-v6
				x = 'var q=window.Locked?-1:FindCurrent();if(q>=0&&GetHint(q))hpClick(1,q);';
			} else {
				// JCross v4
				// work out which box would have a hint added
				// work out which question that box is part of using GridMap and WinLetters
			}
		} else if (a[0]=='Across') {
			if (a[1]=='ClueNum') {
				// JCross v6 [HP6]
				x = "var args=new Array(ClueNum,Across?'A':'D');hpClick(1,args);";
			} else if (a[1]=='x' && a[2]=='y') {
				// JCross v5-v6 [HP5]
				x = "var args=new Array(C[x][y],Across?'A':'D');hpClick(1,args);";
			}
		} else if (a[0]=='QNum') {
			// JQuiz v6[HP6]
			x = 'hpClick(1,QNum);';
		}
	} else if (window.Hint) {
		// Rhubarb
		var f = 'Hint';
		var a = getFuncArgs(f, true);
		x = 'hpClick(1,0);'; // question number is always zero

	} else if (window.CheckAnswer) {
		var f = 'CheckAnswer';
		var a = getFuncArgs(f, true);
		if (a[0]=='ShowHint') {
			if (a[1]=='QNum') {
				// JQuiz v3, v5-v6[HP5]
				x = 'if(ShowHint)hpClick(1,QNum);';
			} else {
				// JQuiz v4
				x = 'if(ShowHint)hpClick(1,QNum-1);'; // QNum is a global variable
			}
		} else if (a[0]=='CheckType') {
			// JMix v5-v6
			x = 'if(CheckType==1)hpClick(1,0);'; // question number is always 0;
		}
	}
	// add the e(x)tra code, if any, to the start of the hint (f)unction
	if (x) {
		var s = getFuncCode(f, x, '', true);
        hpNewFunction(f, a, s);
	}
}
function hpInterceptClues() {
	// modify the function which shows clues (or ShowAnswers in JQuiz)
	//	JBC:    none
	//	JCloze  v3-v6: ShowClue(ItemNum)
	//	JCross  v3-v4: ShowClue(ClueNum), v5-v6: ShowClue(ClueNum,x,y)
	//	JMatch  none
	//	JMix    none
	//	JQuiz   ShowAnswers(QNum)
	var x = ''; // extra code, if any
	if (window.ShowClue) {
		var f = 'ShowClue';
		var a = getFuncArgs(f, true);
		if (a[0]=='ItemNum') {
			// JCloze (v3-v6)
			x = 'if(!window.Locked)hpClick(2,ItemNum);'; // v6 [HP6] uses window.Locked
		} else if (a[0]=='ClueNum') {
			if (a[1]=='x' && a[2]=='y') {
				if (window.A && window.D) {
					// JCross v5-v6 [HP5]
					x = 'if(A[ClueNum]||D[ClueNum])hpClick(2,ClueNum);';
				} else if (document.getElementById) {
					// JCross v6 [HP6]
					x = "if(document.getElementById('clue_' + ClueNum)||document.getElementById('Clue_D_' + ClueNum))hpClick(2,ClueNum);";
				}
			} else {
				if (window.AClues && window.DClues) {
					// JCross v3-v4
					x = 'if(AClues[ClueNum]||DClues[ClueNum])hpClick(2,ClueNum);';
				}
			}
		}
	}
	// JQuiz: there is no "ShowClue" function but there is a "ShowAnswer" function
	if (window.ShowAnswers) {
		var f = 'ShowAnswers';
		var a = getFuncArgs(f, true);
		if (window.State) {
			if (window.ShowMessage) {
				// JQuiz v6 [HP6]
				x = 'if(State[QNum][0]<1)hpClick(2,QNum);';
			} else if (window.WriteFeedback) {
				// JQuiz v3-v4
				x = 'if(State[QNum-1][0]<1)hpClick(2,QNum-1);';
			}
		} else if (window.Status) {
			// JQuiz v5-v6 [HP5]
			x = 'if(Status[QNum][0]<0)hpClick(5,QNum);';
		}
	}
	// add the e(x)tra code, if any, to the start of the clue (f)unction
	if (x) {
		var s = getFuncCode(f, x, '', true);
		var s = getFuncCode(f, '', '', true);
        hpNewFunction(f, a, s);
	}
}
function hpInterceptChecks() {
	// modify the function which handles checks
	//	JBC:    none
	//	JCloze  CheckAnswers()
	//		NB: Rottmeier Find-It 3a: CheckText(GapState,GapId)
	//	JCross  none
	//	JMatch  HP5 v3, v5, v6: CheckAnswer(), HP5 v4: CheckResults(), HP6: CheckAnswers()
	//	JMix    CheckAnswer(CheckType)
	//	JQuiz
	//		HP5: CheckAnswer(ShowHint, QNum)
	//		HP6: CheckMCAnswer, CheckMultiSelAnswer, CheckShortAnswer
	//	Rhubarb  CheckWord(InputWord)
	//	Sequitur CheckAnswer(Chosen, Btn)
	// HP6 JQuiz has three "Check Answer" functions
	var f = new Array('CheckMCAnswer', 'CheckMultiSelAnswer', 'CheckShortAnswer');
	for (var i=0; i<f.length; i++) {
		if (eval('window.' + f[i])) {
			var a = getFuncArgs(f[i], true);
			var x = "";
			if (f[i]=='CheckMCAnswer') {
				x += "var args=new Array(QNum,I[QNum][3][ANum][0]);";
			} else if (f[i]=='CheckShortAnswer') {
				x += ""
				+ "var obj=document.getElementById('Q_'+QNum+'_Guess');"
				+ "var args=new Array(QNum,obj.value);"
				;
			} else if (f[i]=='CheckMultiSelAnswer') {
				x += ""
				+ "var g='';"
				+ "for (var ANum=0; ANum<I[QNum][3].length; ANum++){"
				+ 	"var obj=document.getElementById('Q_'+QNum+'_'+ANum+'_Chk');"
				+ 	"if (obj.checked)g+=(g?'&#43;':'')+I[QNum][3][ANum][0];"
				+ "}"
				+ "var args=new Array(QNum,g);"
				;
			}
			if (x) {
				x = "if(!Finished&&State[QNum].length&&State[QNum][0]<0){" + x + "hpClick(3,args)}";
				var s = getFuncCode(f[i], x, '', true);
                hpNewFunction(f[i], a, s);
			}
		}
	}
	var f = ''; // function name
	var x = ''; // extra code, if any
	if (window.CheckAnswer) {
		f = 'CheckAnswer';
		var a = getFuncArgs(f, true);
		if (a[0]=='ShowHint') {
			if (a[1]=='QNum') {
				// JQuiz v3, v5-v6[HP5]
				x = 'if(!ShowHint&&Status[QNum][0]<1)hpClick(3,QNum);';
			} else {
				// JQuiz v4
				x = 'if(!ShowHint&&State[QNum-1][0]<1)hpClick(3,QNum-1);'; // QNum is a global variable
			}
		} else if (a[0]=='CheckType') {
			// JMix v5-v6
			x = 'if(CheckType==0)hpClick(3,0);'; // question number is always 0;
		} else if (a[0]=='Chosen') {
			// Sequitur
			x = 'if (!(CurrentNumber==TotalSegments||AllDone||Btn.innerHTML==IncorrectIndicator))hpClick(3,Chosen);';
		}
	} else if (window.CheckWord) {
		f = 'CheckWord';
		var a = getFuncArgs(f, true);
		if (a[0]=='InputWord') {
			// Rhubarb
			x = 'if(!window.AllDone)hpClick(3,InputWord);';
		}
	} else if (window.CheckText && !window.CheckAnswers) { // Rottmeier Find-It (3a)
		f = 'CheckText';
		var a = getFuncArgs(f, true);
		if ((a[0]=='bool' && a[1]=='item') || (a[0]=='GapState' && a[1]=='GapId')) {
			x = 'if(!window.Finished&&'+a[0]+')hpClick(3,'+a[1]+');';
		}
	}
	if (f) {
		var s = getFuncCode(f, x, '', true);
        hpNewFunction(f, a, s);
	}
	// JMatch has three possible check functions, depending on the version
	// (NB: other quiz types also have these functions)
	var f = new Array('CheckAnswers', 'CheckAnswer', 'CheckResults');
	for (var i=0; i<f.length; i++) {
		if (eval('window.' + f[i])) {
			var a = getFuncArgs(f[i], true);
			if (a.length==0) {
				var s = getFuncCode(f[i], "hpClick(3);", '', true);
                hpNewFunction(f[i], a, s);
				break; // out of the loop
			}
		}
	}
}
// ***************
//  fix IE5 and NS6
// ***************
// add Array.push if required (allows v6 quizzes to run on ie5win)
if (Array.prototype && Array.prototype.push==null) {
	Array.prototype.push = new Function("x", "this[this.length]=x");
}
// add attachEvent function, if required (allows HP5 v6 quizzes to run on ie5mac)
// 	NOTE: to allow v6 quizzes on ie5mac, the following code
// 	needs to be inserted BEFORE the Hot Potatoes javascript
if (window.attachEvent==null) {
	window.attachEvent = new Function('evt', 'fn', 'eval("window."+evt+"="+fn)');
}
if (document.attachEvent==null) {
	document.attachEvent = new Function('evt', 'fn', 'eval("document."+evt+"="+fn)');
}
// fix the ShowMessage function for NS6
// by removing calls to a button's "focus()" method
if (navigator.userAgent.indexOf("Netscape6")>=0 && window.ShowMessage) {
	var s = ShowMessage.toString();
	var r = new RegExp('document\\.getElementById\\((\'|")FeedbackOKButton(\'|")\\)\\.focus\\(\\);', 'gi');
	s = s.substring(s.indexOf('{')+1, s.lastIndexOf('}')).replace(r, '');
	window.ShowMessage = new Function('Feedback', s);
}
// ns6.0 (in JMix at least) has an error in the FocusAButton function too
// this could be fixed as follows ...
//if (window.FocusAButton) {
//	window.FocusAButton = new Function('return true');
//}
// however, ns6.0 then crashes completely when the mouse moves over a link, so don't bother
// Hot Potatoes quiz sniffing
// === v3 ===
// JBC uses "QuizForm", which contains elements called "Q*_**" (* and ** start at 1)
// JCloze uses "Cloze" form
// JCross uses "Crossword" form
// JMatch uses "QuizForm", which contains elements called "1,2,3..x" and "x+1,x+2...",
// 	and "CheckForm" form, which contains an element called "ScoreBox"
// 	it is also the only HP quiz type to use an array called "CorrectAnswers"
// JQuiz uses "QForm*" forms (* starts at 1), which each contain an element called "Guess"
// === v4 ===
// JBC uses "QForm" form in "QuestionDiv", which contains elements called "FB* (* starts at 0)"
// JCloze uses "Cloze" form in "QuestionDiv"
// JCross uses "Crossword" form in "CWDiv"
// JMatch uses "ExCheck" form in "TitleDiv"
// (no JMix in hp4)
// JQuiz uses "QForm" form in "QuestionDiv", which contains an element called "Answer"
// === v5 ===
// JBC uses "QForm" form, which contains elements called "FB_*_**" (* and ** start at 0)
// JCloze uses "Cloze" form
// JCross writes out "AnswerForm" from a variable called "GetAnswerOpener"
//	HP5.3: uses "AnswerForm" in "BottomFrame"
//	HP5.5: uses "AnswerForm" in "TopFrame", but it is only there when an answer is being input
// JMatch uses "QForm" form, which contains elements called "sel*" (which disappear by the time the quiz is finished)
// JMix uses "ButtonForm"
// JQuiz uses "QForm*" and "Buttons*" (one for each question)
// === v6 ===
// JBC uses "QForm" form (elements have no name or id)
// JCloze uses "Cloze" form (elements have no name or id)
// JCross does not use any forms,
//	HP5: has "GridDiv" in "MainDiv"
//	HP6: has "Clues" table in "MainDiv"
// JMatch has "MatchDiv" in "MainDiv"
//	HP5: uses "QForm" form, which contains elements called "sel*"
// 	HP6: uses "QForm" form, which contains elements called "s*_**"
// JMix does not use any forms, but has "SegmentDiv" in "MainDiv"
// JQuiz
//	HP5: uses "QForm" form, which contains an element called "Guess"
//	HP6: has "Questions" ordered list in "MainDiv"
// === v6+ ===
// JMatch has DIVs called "D*" and  "F*" (* starts at 0)
// JMix has DIVs called "D*" and  "Drop*" (* starts at 0)
// useful sniffing tools (Cut and Paste to browser address box)
//javascript:var s="";var x=new quiz_obj();for(X in x)s+=","+X+"="+x[X];alert(s.substring(1));
//javascript:var s="";var x=document.layers;for(var i=0;i<x.length;i++)s+=","+x[i].name;alert(s.substring(1))
//javascript:var s="";var x=document.forms;for(var i=0;i<x.length;i++)s+=","+x[i].id;alert(s.substring(1))
//javascript:var s="";var x=document.forms;for(var i=0;i<x.length;i++)s+=","+x[i].name;alert(s.substring(1))
//javascript:var s="";var x=document.forms.QForm.elements;for(var i=0;i<x.length;i++)s+=","+x[i].id;alert(s.substring(1))
//javascript:var s="";var x=document.forms.QForm.elements;for(var i=0;i<x.length;i++)s+=","+x[i].name;alert(s.substring(1))
function hpDetectQuiz() {
	// "sniff" (=detect) the quiz's type and intended browser version
	// and cache the values in a global variable called "quiz"
	// Hot Potatoes version
	//	5 : HP5.3 (mac) or HP5.5 (win)
	//	6 : HP6.0 (mac) or HP6.0 (win)
	// intended browser version
	//	3   : ns3, ie3 (frames)
	//	4   : ns4, ie4 (cross browser dhtml)
	//	5   : ie5 (frames, send results via CGI)
	//	6   : ie6, op7, gecko (w3 standards)
	//	6.1 : "drag and drop" versions of JMatch and JMix v6
	// quiz type
	// 	0 : unknown
	//	1 : jbc
	//	2 : jcloze
	//	3 : jcross
	//	4 : jmatch
	//	5 : jmix
	//	6 : jquiz
	//	7 : rhubarb (TexToys)
	//	8 : Sequitur (TexToys)
	// rottmeier quiz type
	//	1 : drop-down (JCloze)
	//	2 : find-it (JCloze)
	// shortcut to window object
	var w = window;
	// create the global "quiz" object, if necessary
	if (!w.quiz) w.quiz = new Object();
	// Hot Potatoes version
	// 	HP6 v6:    Client()
	// 	HP5 v4-v5: BrowserCheck()
	//	    v3:    WinStringToMac() [JCloze, JCross, JQuiz]
	//	    v3:    winrightchar     [JBC, JMatch]
	//	    v3:    DownTime()       [JBC, JCloze, JQuiz]
	if (!quiz.hp) {
		quiz.hp = (w.Client) ? 6 : (w.BrowserCheck) ? 5 : (w.WinStringToMac || w.winrightchar) ? 5 : -1;
	}
	// check the version and type are not already set
	if (!quiz.v || !quiz.t) {
		// initialize version and type
		var v = 0;
		var t = 0;
		// set shortcuts to DOM objects
		var d = document;
		var f = d.forms;
		if (f.QuizForm && f.CheckForm && w.CorrectAnswers) {
			v = 3;
			t = 4; // jmatch
		} else if (w.FeedbackFrame && w.CodeFrame) {
 			v = 3;
			f = CodeFrame.document.forms;
			t = (f.QuizForm) ? 1 : (f.Cloze) ? 2 : (f.Crossword) ? 3 : (f.QForm1) ? 6 : 0;
		} else if (w.DynLayer) {
			v = 4;
			if (d.layers) {
				// for NS4, adjust "f" to point to a forms object in a layer
				var lyr = d.QuestionDiv || d.CWDiv || d.TitleDiv || null;
				if (lyr) f = lyr.document.forms;
			}
			t = (f.QForm && f.QForm.FB0) ? 1 : (f.Cloze) ? 2 : (f.Crossword) ? 3 : (f.ExCheck) ? 4 : (f.QForm && f.QForm.Answer) ? 6 : 0;
		} else if (w.TopFrame && w.BottomFrame) {
			v = 5;
			f = BottomFrame.document.forms;
			t = (f.QForm && f.QForm.elements[0].name.substring(0,3)=='FB_') ? 1 : (f.Cloze) ? 2 : (w.GetAnswerOpener && GetAnswerOpener.indexOf('AnswerForm')>=0) ? 3 : (f.QForm && w.RItems) ? 4 : (f.ButtonForm) ? 5 : (f.QForm0 && f.Buttons0) ? 6 : 0;
		} else if (hpObj(d, 'MainDiv')) {
			v = 6;
			var obj = (f.QForm) ? f.QForm.elements : null;
			t = (obj && obj.length>0 && obj[0].id=='') ? 1 : (f.Cloze) ? 2 : (hpObj(d, 'GridDiv') || hpObj(d, 'Clues')) ? 3 : hpObj(d, 'MatchDiv') ? 4 : hpObj(d, 'SegmentDiv') ? 5 : ((f.QForm && f.QForm.Guess) || hpObj(d, 'Questions')) ? 6 : 0;
		} else if (hpObj(d, 'D0')) {
			v = 6.1; // drag and drop (HP5 and HP6)
			t = (hpObj(d, 'F0')) ? 4 : (hpObj(d, 'Drop0')) ? 5 : 0;
		} else if (w.Words && f.Rhubarb) {
			v = 6;
			t = 7; // rhubarb (TexToys)
		} else if (w.Segments && hpObj(d, 'Story')) {
			v = 6;
			t = 8; // sequitur (TexToys)
		}
		quiz.v = v; // intended browser version
		quiz.t = t; // quiz type
	}
}
function hpRottmeier() {
	hpDetectQuiz();
	if (typeof(quiz.r)=='undefined') { // first-time only
		quiz.r = 0;
		if (quiz.t==2) { // JCloze
			if (quiz.hp==5) { // HP5
				// ??
			} else if (quiz.hp==6) { // HP6
				if (window.Create_StateArray) { // Rottmeier
					var obj = new Create_StateArray();
					if (typeof(obj.GapLocked)=='boolean') {
						quiz.r = 1; // drop-down (v2.4)
					} else if (typeof(obj.ErrorFound)=='boolean') {
						if (typeof(obj.GapSolved)!='boolean') {
							quiz.r = 2.1; // find-it (v3.1a)
						} else {
							quiz.r = 2.2; // find-it (v3.1b)
						}
					}
					obj = null; // prevents memory leakage on some versions of IE
				}
			}
		}
	}
	return quiz.r;
}
function hpVersion() {
	hpDetectQuiz();
	return quiz.hp;
}
function hpQuizType() {
	hpDetectQuiz();
	return quiz.t;
}
function hpQuizVersion() {
	hpDetectQuiz();
	return quiz.v;
}
function hpScoreEngine(score_i, a, s, aa, ss, count_c, count_i) {
	// calculate the score for the quiz so far
	// score_i : amount by which to increment "score"
	// a  : outer array
	// s  : condition, if any, on outer array (=a)
	//      if true, the score will be incremented by "score_i"
	// aa : inner array, if any
	// ss : condition, if any, on inner array (=aa)
	// count_c : condition, if any, on which "count" is to be incremented
	// count_i : amount by which to increment "count"
	// "a" and "aa" may be passed as arrays or strings containing the name of an array
	// "s" and "ss" are strings containing an expression to be eval(uated)
	// "score_i", "count_i" and "count_c" strings containing an expression to be eval(uated)
	var score = 0;
	var count = 0;
	// set default condition to increment "count", and amount by which to increment the count
	if (count_c==null) count_c = "true";
	if (count_i==null) count_i = "1";
	// set length of outer array. if any
	var l = (typeof(a)=="string") ? eval(a + ".length") : a ? a.length : 0;
	// loop through outer array
	for (var i=0; i<l; i++) {
		if (s==null && ss==null) {
			score += eval(score_i);
			if (eval(count_c)) count += eval(count_i);
		} else if (s) {
			score += eval(s) ? eval(score_i) : 0;
			if (eval(count_c)) count += eval(count_i);
		} else if (ss) {
			// set length of inner array, if any
			var ll = (typeof(aa)=="string") ? eval(aa + ".length") : aa ? aa.length : 0;
			// loop through inner array. checking inner condition
			for (var ii=0; ii<ll; ii++) {
				score += eval(ss) ? eval(score_i) : 0;
				if (eval(count_c)) count += eval(count_i);
			}
		}
	}
	if (count) {
		// get p(enalties) for JCross and JMatch (and JMix ?)
		if (window.Penalties) {
			score -= (Penalties - (hpFinished() ? 0 : 1));
		}
		// adjust count for Find-It 3a and 3b
		if (window.TotWrongChoices) {
			if (window.CheckText && !window.CheckAnswers) { // Find-It 3a
				// this seems a little odd, but will replicate behavior of CalculateScore()
				count = score + TotWrongChoices;
			} else {
				count += TotWrongChoices;
			}
		}
		score = Math.floor(100*score/count);
		if (score<0) { // just in case
			score = 0;
		}
	}
	return score;
}
function hpScore() {
	var x = ''; // score
	var hp = hpVersion();
	var t = hpQuizType();
	var v = hpQuizVersion();
	if (t==1) { // jbc
		if (v==3) x = hpScoreEngine(1, DoneStatus, "i>0 && a[i]=='0'"); // doesn't work
		else if (v==4) x = hpScoreEngine(1, DoneStatus, "a[i]==0");    // doesn't work
		else if (v==5 || v==6) x = hpScoreEngine("a[i][3]", Status, "a[i][3]");
	} else if (t==2) { // jcloze
		if (v==3 || v==4) x = hpScoreEngine("a[i]", Scores);
		else if (hp==5) x = hpScoreEngine("a[i][3]", State); // v==5 && v==6
		else if (hp==6) {
			var r = hpRottmeier();
			if (r==0) x = x = hpScoreEngine("a[i].ItemScore", State);
			else if (r==1) x = hpScoreEngine("a[i][1].Score", GapList, "a[i][1].GapLocked"); // dropdown
			else if (r==2.1) x = hpScoreEngine(1, GapList, "a[i][1].ErrorFound"); // Find-It 3a
			else if (r==2.2) x = hpScoreEngine("a[i][1].Score", GapList, "a[i][1].GapSolved"); // Find-It 3b
		}
	} else if (t==3) { // jcross
		if (v==3) x = hpScoreEngine(1, CorrectAnswers, "document.QuizForm.elements[i*2].selectedIndex==a[i]");
		else if (v==4) x = hpScoreEngine(1, WinLetters, "ConvertCase(GetBoxValue(i),1).charAt(0)==a[i].charAt(0)");
		else if (v==5 || v==6) {
            if (window.CaseSensitive) { // HP 6.2
                x = hpScoreEngine(1, L, "", "L[i]", "L[i][ii] && L[i][ii]==G[i][ii]", "L[i][ii]");
            } else {
                x = hpScoreEngine(1, L, "", "L[i]", "L[i][ii] && L[i][ii].toUpperCase()==G[i][ii].toUpperCase()", "L[i][ii]");
            }
        }
	} else if (t==4) { // jmatch
		if (v==3) x = hpScoreEngine(1, CorrectAnswers, "document.QuizForm.elements[i*2].selectedIndex==a[i]");
		else if (v==4) x = hpScoreEngine(1, Draggables, "a[i].correct=='1'");
		else if (v==5) x = hpScoreEngine(1, I, "I[i][2]<1 && I[i][0].length>0 && Status[i][0]==1");
		else if (v==6) x = hpScoreEngine(1, Status, "Status[i][0]==1");
		else if (v==5.1 || v==6.1) x = hpScoreEngine(1, D, "D[i][2]==D[i][1] && D[i][2]>0", "", "", "i<F.length");
	} else if (t==5) { // jmix
		// there was no v3 or v4 of JMix
		if (v==5 || v==6 || v==6.1) x = Math.floor(100*(Segments.length-Penalties)/Segments.length);
	} else if (t==6) { // jquiz
		if (hp==5) {
			if (v==3 || v==4) x = hpScoreEngine("a[i][4]/10", State, "a[i][0]==1");
			else if (v==5 || v==6) x = hpScoreEngine("a[i][4]/10", Status, "a[i][0]==1", "", "", "true", "1");
		} else if (hp==6) {
			if (v==6) x = hpScoreEngine("I[i][0]*a[i][0]", State, "a[i]&&a[i][0]>=0", "", "", "a[i]", "I[i][0]");
		}
	} else if (t==7) { // rhubarb
		if (v==6) {
			x = Math.floor(100*Correct/TotalWords);
		}
	} else if (t==8) { // sequitur
		if (v==6) {
			var myTotalPoints = TotalPoints - (hpFinished() ? 0 : (OptionsThisQ-1));
			x = Math.floor(100*ScoredPoints/myTotalPoints);
		}
	}
	return x; // result
}
function hpFinishedEngine(a, s, aa, ss) {
	// determine whether or not all quistions in a quiz are finished
	// a  : outer array
	// s  : condition, if any, on outer array
	//      if true for any element in "a", the quiz is NOT finished
	// aa : inner array, if any
	// ss : condition, if any, on inner array
	//      if true for any element in "aa", the quiz is NOT finished
	// the arrays "a" and "aa" may be passed as arrays or strings to be eval(uated)
	// the conditions "s" and "ss" are specified as strings to be eval(uated)
	// assume a positive result
	var x = true;
	// set length of outer array. if any
	var l = (typeof(a)=="string") ? eval(a + ".length") : a ? a.length : 0;
	// loop through outer array
	for (var i=0; i<l; i++) {
		// do outer condition, if any
		if (s && eval(s)) x = false;
		// set length of inner array, if any
		var ll = (typeof(aa)=="string") ? eval(aa + ".length") : aa ? aa.length : 0;
		// loop through inner array. checking inner condition
		for (var ii=0; ii<ll; ii++) {
			if (ss && eval(ss)) x = false;
		}
	}
	return x;
}
function hpTimedOut() {
	// v5 uses "min" and "sec"
	// v6 uses Seconds
	return (typeof(self.Seconds)=='number' && Seconds==0) || (typeof(self.min)=='number' && min==0 && typeof(self.sec)=='number' && sec==0);
}
function hpFinished() {
	// assume false result
	var x = false;
	var hp = hpVersion();
	var t = hpQuizType();
	var v = hpQuizVersion();
	if (t==1) { // jbc
		if (v==3) x = hpFinishedEngine(DoneStatus, "i>0 && a[i]=='0'");
		else if (v==4) x = hpFinishedEngine(DoneStatus, "a[i]==0");
		else if (v==5 || v==6) x = hpFinishedEngine(Status, "a[i][0]==0");
	} else if (t==2) { // jcloze
		var r = hpRottmeier();
		if (r==1) x = hpFinishedEngine(GapList, "a[i][1].GapLocked==false"); // drop-down
		else if (r==2.1) x = hpFinishedEngine(GapList, "a[i][1].ErrorFound==false"); // find-it 3a
		else if (r==2.2) x = hpFinishedEngine(GapList, "a[i][1].GapSolved==false"); // find-it 3b
		else if (v==3 || v==4 || v==5 || v==6) x = hpFinishedEngine(I, "CheckAnswer(i)==-1");
		// also:   else if (v==5 || v==6) x = hpFinishedEngine(State, "a[i][4]!=1")
	} else if (t==3) { // jcross
		if (v==3) x = hpFinishedEngine(document.Crossword.elements, "ConvertCase(is.mac?unescape(MacStringToWin(a[i].value)):a[i].value,1)!=Letters[i]");
		else if (v==4) x = hpFinishedEngine(WinLetters, "ConvertCase(GetBoxValue(i),1).charAt(0) != a[i].charAt(0)");
		else if (v==5 || v==6) {
            if (window.CaseSensitive) { // 6.2
                x = hpFinishedEngine(L, "", "L[i]", "L[i][ii] && L[i][ii]!=G[i][ii]");
            } else {
                x = hpFinishedEngine(L, "", "L[i]", "L[i][ii] && L[i][ii].toUpperCase()!=G[i][ii].toUpperCase()");
            }
		}
	} else if (t==4) { // jmatch
		if (v==3) x = hpFinishedEngine(CorrectAnswers, "document.QuizForm.elements[i*2].selectedIndex != a[i]");
		else if (v==4) x = hpFinishedEngine(Draggables, "a[i].correct!='1'");
		else if (v==5) x = hpFinishedEngine(I, "I[i][2]<1 && I[i][0].length>0 && Status[i][0]<1 && GetAnswer(i)!=I[i][3]");
		else if (v==6) x = hpFinishedEngine(Status, "Status[i][0]<1");
		else if (v==5.1 || v==6.1) x = hpFinishedEngine(D, "", F, "F[ii][1]==D[i][1]&&D[i][1]!=D[i][2]");
	} else if (t==5) { // jmix
		// there was no v3 or v4 of JMix
		if (v==5 || v==6 || v==6.1) x = !hpFinishedEngine(Answers, "a[i].join(',')=='" + GuessSequence.join(',') + "'");
	} else if (t==6) { // jquiz
		if (v==3 || v==4) x = hpFinishedEngine(State, "a[i][0]==0");
		else if (v==5 || v==6) {
			if (hp==5) x = hpFinishedEngine(Status, "a[i][0]<1");
			else if (hp==6) x = hpFinishedEngine(State, "a[i] && a[i][0]<0");
		}
	} else if (t==7) { // rhubarb
		if (v==6) x = hpFinishedEngine(DoneList, "a[i]==1");
	} else if (t==8) { // sequitur
		if (v==6) x = (CurrentNumber==TotalSegments || AllDone);
	}
	return x;
}
function hpObj(d, id) {
	return d.getElementById ? d.getElementById(id) : d.all ? d.all[id] : d[id];
}
function GetViewportHeight() {
	if (window.innerHeight) {
		return innerHeight;
	} else {
		if (hpIsStrict()) {
			return document.documentElement.clientHeight;
		} else {
			return document.body.clientHeight;
		}
	}
}
function hpIsStrict() {
	if (!window.hpStrictIsSet) {
		window.hpStrictIsSet = true;
		window.hpStrict = false;
		var s = document.compatMode;
		if (s && s=="CSS1Compat") { // ie6
			window.hpStrict = true;
		} else {
			var obj = document.doctype;
			if (obj) {
				var s = obj.systemId || obj.name; // n6 || ie5mac
				if (s && s.indexOf("strict.dtd") >= 0) {
					window.hpStrict = true;
				}
			}
		}
	}
	return window.hpStrict;
}
// **************
//  initialization
// **************
hpInterceptFeedback();
hpInterceptHints();
hpInterceptClues();
hpInterceptChecks();
function hpFindForm(formname, w) {
	if (w==null) w = self;
    var f = w.document.getElementById(formname);
    if (f) {
        return f;
    }
    var f = w.document.forms[formname];
    if (f) {
        return f;
    }
	if (w.frames) {
		for (var i=0; i<w.frames.length; i++) {
				var f = hpFindForm(formname, w.frames[i]);
				if (f) {
                    return f;
                }
		}
	}
	return null;
}
function Finish(quizstatus) {
	var mark = hpScore();
	window.hpForm = hpFindForm('store');
	if (hpForm) { // LMS
		hpForm.starttime.value = getTime(Start_Time);
		hpForm.endtime.value = getTime();
		hpForm.mark.value = mark;
		hpForm.detail.value = '<?xml version="1.0"?><hpjsresult><fields>'+GetQuestionDetails()+'</fields></hpjsresult>';
		if (hpForm.status) {
			if (!quizstatus) {
				// 4=completed, 3=abandoned, 2=timed-out or 1=in-progress
				quizstatus = hpFinished() ? 4 : hpTimedOut() ? 2 : 1;
			}
			hpForm.status.value = quizstatus;
		}
		if (!window.hpQuizResultsSent) {
			if (hpForm.status && quizstatus==4) {
				window.hpQuizResultsSent = true;
			}
			if (quizstatus==4) { // completed
				// wait 2 seconds for student to see feedback
				setTimeout("hpForm.submit();", 2000);
			} else {
				hpForm.submit();
			}
		}
	} else if (hpFinished()) {
		SendAllResults(mark);
	}
}
// create form to send results
if (DB[7] && DB[8] && !is_LMS()) {
	ResultForm = ''
		+ '<html><body>'
		+ '<form name="Results" action="" method="post" enctype="x-www-form-encoded">'
		+ 	hpHiddenField('recipient', '')
		+ 	hpHiddenField('subject', '')
		+ 	hpHiddenField('Exercise', '')
		+ 	hpHiddenField('realname', '')
		+ 	hpHiddenField('Score', '')
		+ 	hpHiddenField('Start_Time', '')
		+ 	hpHiddenField('End_Time', '')
		+ 	hpHiddenField('title', 'Thanks!')
		+ '</form>'
		+ '</body></html>'
	;
}
// reassign the StartUp function
var p = getPrompt(window.GetUserName || window.StartUp);
var c = getStartUpCode(window.StartUp);
if (p && c) {
    if (window.C && C.safari) {
        eval('window.StartUp=function(){QuizLogin("' + p + '")}');
        eval('window.StartQuiz=function(){' + c + '}');
    } else {
    	window.StartUp = new Function('QuizLogin("' + p + '")');
    	window.StartQuiz = new Function(c);
    }
	// "QuizLogin" finshes by calling "StartQuiz"
}
// reassign the SendResults function
window.SendResults = SendAllResults;
// set start time
var Start_Time = new Date();
//-->
