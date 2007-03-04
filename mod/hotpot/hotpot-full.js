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
	JCloze[4] = true;	// show number of hints or penalties
	JCloze[5] = true;	// show if clue was asked for or not
	JCloze[6] = true;	// show clue
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
	JCross[3] = true;	// show answers
	JCross[4] = true;	// show clues
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
	JMatch[1] = true;	// show number of attempts for each match
	JMatch[2] = true;	// show LHS texts
	JMatch[3] = true;	// show RHS texts
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
	JMix[1] = true;		// show number of wrong guesses
	JMix[2] = true;		// show right answer
	JMix[3] = true;		// show wrong answer, if any
	JMix[4] = false;	// show answer as text (false) or number (true)
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
	JQuiz[3] = true;	// show wrong and ignored answer(s)
	JQuiz[4] = true;	// show number of hints requested
	JQuiz[5] = true;	// show number of checks of incorrect answers

	// HP6 v6 quizzes only
	JQuiz[6] = false;	// show answer value (false) or A,B,C... index (true)
	JQuiz[7] = false;	// show all students answers
	JQuiz[8] = true;	// show student's wrong answers
	JQuiz[9] = true;	// show ignored answers (not relevant for multi-select questions)
	JQuiz[10] = true;	// show score weightings
	JQuiz[11] = true;	// show question type
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
	Rhubarb[0] = true; // show correct words
	Rhubarb[1] = true; // show incorrect words
}

// **********
//  Sequitur
// **********

if (window.Sequitur==null) {
	Sequitur = new Array();
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
	if ((Login[0] || Login[1] || Login[2] || Login[3]) && !is_LMS()) {
		var html = ''
			+ '<HTML>'
			+ '<HEAD></HEAD>'
			+ '<BODY bgColor="#cccccc" onLoad="opener.setFocus(self)">'
			+ '<FORM onSubmit="'
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
		html += '<TABLE>'
			+ 	'<CAPTION>' + LoginPrompt + '</CAPTION>';
		;
		if (Login[0]) { // user name
			var v = getCookie(self, 'UserName');
			html += '<TR>'
				+	'<TH align=right nowrap>' + MSG[0] + ' :</TH>'
				+	'<TD>'
			;

			if (typeof(Login[0])=='boolean') { // text box
				html += '<INPUT type=text name=UserName value="' + v + '">';

			} else { // drop down menu of names

				// pattern to match commas and white space
				var comma = (window.RegExp) ? new RegExp('\\s*,\\s*') : ',';

				// convert list of names to array, if necessary
				if (typeof(Login[0])=='string') {
					Login[0] = Login[0].split(comma);
				}

				html += '<SELECT name=UserName size=1>'
					+ '<OPTION value=""></OPTION>'
				;
				for(var i=0; i<Login[0].length; i++) {
					// convert name details to array if necesssary
					if (typeof(Login[0][i])=='string') {
						Login[0][i] = Login[0][i].split(comma);
					}
					html += makeOption(Login[0][i][0], v, Login[0][i][1]);
				}
				html += '</SELECT>';
			}
			html += 	'</TD>'
				+ '</TR>'
			;
		}
		if (Login[1]) { // user ID
			var v = getCookie(self, 'UserID');
			html += '<TR><TH align=right nowrap>' + MSG[1] + ' :</TH><TD><INPUT type=text name=UserID value="' + v + '"></TD></TR>';
		}
		if (Login[2]) { // user email
			var v = getCookie(self, 'UserEmail');
			html += '<TR><TH align=right nowrap>' + MSG[2] +' :</TH><TD><INPUT type=text name=UserEmail value="' + v + '"></TD></TR>';
		}
		if (Login[3]) { // quiz password
			var v = getCookie(self, 'Password');
			html += '<TR><TH align=right nowrap>' + MSG[3] + ' :</TH><TD><INPUT type=password name=Password value="' + v + '"></TD></TR>';
		}
		if (Login[4]) { // cookie lifespan
			var v = getCookie(self, 'CookieExpiry');
			html += '<TR>'
				+ 	'<TH align=right nowrap>' + MSG[4] + ' :</TH>'
				+ 	'<TD>'
				+		'<SELECT name="CookieExpiry" size=1>'
				+ 			makeOption('session', v, MSG[7])
				+ 			makeOption('day', v, MSG[8])
				+ 			makeOption('month', v, MSG[9])
				+ 			makeOption('never', v, MSG[10])
				+ 		'</SELECT>'
				+ 	'</TD>'
				+ '</TR>'
			;
		}
		html += 	'<TR>'
			+		'<TH>&nbsp;</TH>'
			+		'<TD nowrap>'
			+			'<INPUT type=submit value="' + MSG[5] + '"> '
			+ 			'<INPUT type=button value="' + MSG[6] + '" onClick="opener.goBack();self.close();">'
			+		'</TD>'
			+	'</TR>'
			+ '</TABLE></FORM></BODY></HTML>'
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
	return '<OPTION value="' + value + '"' + (value==v ? ' SELECTED' : '') + '>' + (txt ? txt : value) + '</OPTION>';
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
		if (n=='Email' && window.RegExp) {
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

	// workaround for "Access is denied" errors in IE when offline
	// based on an idea seen at http://www.devshed.com/Client_Side/JavaScript/Mini_FAQ
	var ie_offline = (document.all && location.protocol=='file:');

	// open the window
	var w = window.open((ie_offline ? '' : url), name, attributes);

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
		} else {
			if (ie_offline && url) w.location = url;
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
		var w = openWindow('', '', 500, 400, 'RESIZABLE,SCROLLBARS,HOTPOT_LOCATION', ResultForm);

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
		// sDetails += makeHiddenField('Name', window.UserName);
	}
	if (Login[1]) { // user ID
		sDetails += makeHiddenField('ID', window.UserID);
	}
	if (Login[2]) { // user email
		sDetails += makeHiddenField('email', window.UserEmail);
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
		sDetails += makeHiddenField('Password', window.Password);
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

		dbDetails += makeHiddenField('append_db', folder + file + ext);
		dbDetails += makeHiddenField('db_fields', DB[4]);
		dbDetails += makeHiddenField('db_delimiter', ''); // NS6+ requires this be set later
		if (DB[6]) dbDetails += makeHiddenField('env_report', DB[6]);

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
				s += makeHiddenField(ServerFields[i][0], ServerFields[i][1]);
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
	var t = get_quiz_type();
	var v = get_quiz_version();

	return	(t==1) ? GetJbcQuestionDetails(v) : 
		(t==2) ? GetJClozeQuestionDetails(v) : 
		(t==3) ? GetJCrossQuestionDetails(v) : 
		(t==4) ? GetJMatchQuestionDetails(v) : 
		(t==5) ? GetJMixQuestionDetails(v) : 
		(t==6) ? GetJQuizQuestionDetails(v) :
		(t==7) ? GetRhubarbDetails(v) :
		(t==8) ? GetSequiturDetails(v) : '';
}
function GetJbcQuestionDetails(v) {
	qDetails = '';

	// check the quiz version
	if (v==5 || v==6) {

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
				qDetails += makeHiddenField(Q+'attempts', Status[q][2] + (Status[q][0]==1 ? 1 : 0));
			}
			if (JBC[2]) { // question text
				qDetails += makeHiddenField(Q+'text', I[q][0]);
			}
			if (JBC[3] && (DB[0] || aDetails[0].length>0)) { // right
				qDetails += makeHiddenField(Q+'right', aDetails[0]);
			}
			if (JBC[4] && (DB[0] || aDetails[1].length>0)) { // wrong
				qDetails += makeHiddenField(Q+'wrong', aDetails[1]);
			}
			if (JBC[5] && (DB[0] || aDetails[2].length>0)) { // ignored
				qDetails += makeHiddenField(Q+'ignored', aDetails[2]);
			}
			// calculate score for this question, if required (for HP version < 5.5)
			if (isNaN(Status[q][3])) {
				var a1 = Status[q][1].length; // answers
				var a2 = Status[q][2]; // attempts
				Status[q][3] =  (a1<1 || a1<(a2-1)) ? 0 : ((a1 - (a2-1)) / a1);
			}
			// add 'score' for this question
			qDetails += makeHiddenField(Q+'score', Math.floor(Status[q][3]*100)+'%');
		} // end for
	}
	return qDetails;
}
function GetJClozeQuestionDetails(v) {
	var qDetails = '';

	// check the quiz version
	if (v==5 || v==6) {

		var hp5 = (State[0].Guesses==null);

		// get details for each question
		for (var q=0; q<State.length; q++) {

			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JCloze', q);

			// add separator, if required
			if (JCloze[0]) qDetails += makeSeparator(Q);

			// score (as %)
			var x = (hp5 ? State[q][3] : State[q].ItemScore);
			qDetails += makeHiddenField(Q+'score', Math.floor(x*100)+'%');

			// shortcut to students correct answer
			var correct = (hp5 ? State[q][5] : State[q].Guesses[State[q].Guesses.length-1]);

			if (JCloze[1]) { // student's correct answer
				qDetails += makeHiddenField(Q+'correct', correct);
			}
			if (JCloze[2]) { // other correct answers
				var ignored = new Array();
				for (var i=0, ii=0; i<I[q][1].length; i++) {
					if (I[q][1][i][0] && (I[q][1][i][0].toUpperCase() != correct.toUpperCase())) {
						ignored[ii++] = I[q][1][i][0];
					}
				}
				if (DB[0] || ignored.length>0) qDetails += makeHiddenField(Q+'ignored', ignored);
			}
			if (JCloze[3] && State[q].Guesses) {
				var wrong = new Array();
				for (var i=0, ii=0; i<State[q].Guesses.length-1; i++) {
					wrong[ii++] = State[q].Guesses[i];
				}
				if (DB[0] || ii>0) qDetails += makeHiddenField(Q+'wrong', wrong);
			}
			if (JCloze[4]) { // number of penalties
				var x = (hp5 ? State[q][1] : State[q].HintsAndChecks);
				qDetails += makeHiddenField(Q+'penalties', x);
			}
			if (JCloze[5]) { // clue shown?
				var x = (hp5 ? State[q][0] : State[q].ClueGiven);
				qDetails += makeHiddenField(Q+'clue_shown', (x ? 'HOTPOT_YES' : 'HOTPOT_NO'));
			}
			if (JCloze[6]) { // clue text
				qDetails += makeHiddenField(Q+'clue_text', I[q][2]);
			}
		} // end for
	}
	return qDetails;
}
function GetJCrossQuestionDetails(v) {
	var qDetails = '';

	// check the quiz version
	if (v==5 || v==6) {

		// inialize letter count
		var letters = 0;

		// get details for each question
		for (var row=0; row<L.length; row++) {
			for (var col=0; col<L[row].length; col++) {

				// increment letter count, if required
				if (L[row][col]) letters++; 

				// show answers and clues, if required
				var q = (v==5) ? C[row][col] : CL[row][col];
				if (q) {
					// format 'Q' (a padded, two-digit version of 'q')
					var Q = getQ('JCross', q);

					var clue_A = (v==5) ? A[q] : GetJCrossClue('Clue_A_' + q);
					var clue_D = (v==5) ? D[q] : GetJCrossClue('Clue_D_' + q);

					// add separator, if required
					if (JCross[0] && (clue_A || clue_D)) {
						qDetails += makeSeparator(Q);
					}

					if (clue_A) { // across question
						if (JCross[3]) qDetails += makeHiddenField(Q+'across', GetJCrossWord(G, row, col));
						if (JCross[4]) qDetails += makeHiddenField(Q+'across_clue', clue_A);
					}
					if (clue_D) { // down question
						if (JCross[3]) qDetails += makeHiddenField(Q+'down', GetJCrossWord(G, row, col, true));
						if (JCross[4]) qDetails += makeHiddenField(Q+'down_clue', clue_D);
					}
				} // end if q
			} // end for col
		} // end for row

		if (JCross[2]) { // show number of letters
			qDetails = makeHiddenField('JCross_letters', letters) + qDetails;
		}
		if (JCross[1]) { // show penalties
			qDetails = makeHiddenField('JCross_penalties', window.Penalties) + qDetails;
		}

	}
	return qDetails;
}
function GetJCrossClue(id) {
	var obj = (document.getElementById) ? document.getElementById(id) : null;
	return (obj) ? GetChildNodesText(obj, 'Clue') : '';
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
function GetJMatchQuestionDetails(v) {
	var qDetails = '';

	// HP5.5 uses "I" for v5 and v6 JMatch quizzes
	var hp5 = (window.I) ? true : false;

	// check the quiz version
	if (hp5 || v==6 || v==6.1) {

		if (JMatch[1] && v==6.1) { // attempts
			qDetails += makeHiddenField('JMatch_attempts', Penalties+1);
		}

		// get number of questions
		var max_q = (hp5 || v==6) ? Status.length : F.length;

		// get details for each question
		for (var q=0; q<max_q; q++) {

			// format 'Q' (a padded, two-digit version of 'q')
			var Q = getQ('JMatch', q);

			// add separator, if required
			if (JMatch[0] && (JMatch[1] || JMatch[2] || JMatch[3])) {
				qDetails += makeSeparator(Q);
			}
			if (JMatch[1] && (hp5 || v==6)) { // attempts
				qDetails += makeHiddenField(Q+'attempts', Status[q][1]);
			}
			if (JMatch[2]) { // LHS text
				var x = (hp5) ? I[q][0] : (v==6) ? GetJMatchText(q, 'LeftItem') : F[q][0];
				qDetails += makeHiddenField(Q+'lhs', x);
			}
			if (JMatch[3]) { // RHS text
				var x = (hp5) ? I[q][1] : (v==6) ? GetJMatchText(q, 'RightItem') : GetJMatchRHS(q);
				qDetails += makeHiddenField(Q+'rhs', x);
			}
		} // end for
	}
	return qDetails;
}
function GetJMatchText(q, className) {
	var obj = (document.getElementById) ? document.getElementById('Questions') : null;
	return (obj) ? GetChildNodesText(obj.childNodes[q], className) : '';
}
function GetJMatchRHS(q) { // Drag-and-drop only (v==6.1)
	var max_i = (window.F && window.D) ? F.length : 0;
	for (var i=0; i<max_i; i++) {
		if (D[i][2]==F[q][1]) break;
	}
	return (i<max_i) ? D[i][0] : '';
}
function GetJMixQuestionDetails(v) {
	qDetails = '';

	// check the quiz version
	if (v==5 || v==6 || v==6.1) {

		var A = Answers.length;
		for (var a=0; a<A; a++) {
			var G = Answers[a].length;
			for (var g=0; g<G; g++) {
				if (Answers[a][g] != GuessSequence[g]) break;
			}
			if (g>=G) break; 
		}
		var isWrong = (a>=A);

		// format 'Q' (a padded, two-digit version of 'q')
		var Q = getQ('JMix', 0);

		// add separator, if required
		if (JMix[0]) qDetails += makeSeparator(Q);

		// add 'score' for this question
		var score = isWrong ? 0 : ((Segments.length-Penalties)/Segments.length);
		qDetails += makeHiddenField(Q+'score', Math.floor(score*100)+'%');

		if (JMix[1]) { // number of wrong guesses
			qDetails += makeHiddenField(Q+'wrongGuesses', Penalties);
		}
		if (JMix[2]) { // right answer
			qDetails += makeHiddenField(Q+'right', GetJMixSequence(Answers[isWrong ? 0 : a]));
		}
		if (JMix[3] && isWrong) { // wrong answer
			qDetails += makeHiddenField(Q+'wrong', GetJMixSequence(GuessSequence));
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
function GetJQuizQuestionDetails(v) {
	var qDetails = '';

	// HP5.5 uses "Status" for v5 and v6 JMatch quizzes (HP6 uses "State")
	var hp =  (window.Status) ? 5 : (window.State) ? 6 : 0;

	// check the quiz version
	if (hp) {

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
				qDetails += makeHiddenField(Q+'type', x);
			}

			// score (as %)
			var x = (hp==5) ? Status[q][4]*10 : I[q][0]*State[q][0];
			qDetails += makeHiddenField(Q+'score', Math.floor(x)+'%');

			if (hp==6 && JQuiz[10]) { // weighting
				qDetails += makeHiddenField(Q+'weighting', I[q][0]);
			}
			if (JQuiz[1]) { // question text
				var x = (hp==5) ? I[q][0] : (document.getElementById) ? GetChildNodesText(document.getElementById('Q_'+q), 'QuestionText') : '';
				qDetails += makeHiddenField(Q+'question', x);
			}
			if (JQuiz[2]) { // student's correct answers
				var x = (hp==5) ? Status[q][3] : GetJQuizAnswerDetails(q, 2);
				qDetails += makeHiddenField(Q+'correct', x);
			}
			if (JQuiz[3]) { // ignored and wrong answers
				var x = (hp==5) ? '' : GetJQuizAnswerDetails(q, 1);
				if (hp==5) {
					for (var i=0; i<I[q][1].length; i++) {
						if (I[q][1][i][0] && (I[q][1][i][0].toUpperCase() != Status[q][3].toUpperCase())) {
							x += ((x ? ',' : '') + I[q][1][i][0]);
						}
					}
				}
				if (DB[0] || x) qDetails += makeHiddenField(Q+'other', x);
			}
			if (hp==6 && JQuiz[7]) { // all selected answers
				var x = GetJQuizAnswerDetails(q, 0);
				qDetails += makeHiddenField(Q+'selected', x);
			}
			if (hp==6 && JQuiz[8]) { // wrong answers
				var x = GetJQuizAnswerDetails(q, 3);
				qDetails += makeHiddenField(Q+'wrong', x);
			}
			if (hp==6 && JQuiz[9]) { // ignored answers
				var x = GetJQuizAnswerDetails(q, 4);
				qDetails += makeHiddenField(Q+'ignored', x);
			}
			if (JQuiz[4]) { // number of hints
				var x = (hp==5) ? Status[q][2] : State[q][4];
				qDetails += makeHiddenField(Q+'hints', x);
			}
			if (JQuiz[5]) { // number of checks of incorrect answers
				var x = (hp==5) ? Status[q][1] : (State[q][2]-1);
				qDetails += makeHiddenField(Q+'checks', x);
			}
		} // end for
	} // end if

	return qDetails;
}
function GetChildNodesText(obj, className) {
	// search this node (obj) and its child nodes and 
	// return all text under node with required classname
	var txt = '';
	if (obj) {
		if (className && obj.className==className) {
			className = '';
		}
		if (className=='' && obj.nodeType==3) { // text node
			txt = obj.nodeValue + ' '; // html entities
		}
		if (obj.childNodes) {
			for (var i=0; i<obj.childNodes.length; i++) {
				txt += GetChildNodesText(obj.childNodes[i], className);
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

	var x = State[q][5];

	if (I[q][2]=='3') { // multi-select

		if (flag==4) {
			var x = new Array();
		} else {
			// get required part of 'x' and convert to array
			var i = x.lastIndexOf('|');
			var x = x.substring((flag==2 ? (i+1) : 1), ((flag==0 || flag==2) ? x.length : i)).split('|');
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

		// remove trailing comma and convert to array
		x = x.substring(0, x.length-1).split(',');

		if (flag) {
			var a = new Array();
			if (flag==1 || flag==2 || flag==3) {
				for (var i=0; i<x.length; i++) {
					var ii = I[q][3][(x[i].charCodeAt(0)-65)][2];
					if(((flag==1 || flag==2) && ii==1) || (flag==3 && ii==0)) a.push(x[i]);
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
				var ii = x[i].charCodeAt(0) - 65;
				x[i] = I[q][3][ii][0];
			}
		}
	}
	return x;
}
function GetRhubarbDetails(v) {
	qDetails = '';
	if (v==6) {
		var Q = getQ('Rhubarb', 0);
		if (document.title) { // use quiz title as question name
			qDetails += makeHiddenField(Q+'name', document.title);
		}
		if (Rhubarb[0]) { // correct words
			qDetails += makeHiddenField(Q+'correct', Words.length+' words');
		}
		if (Rhubarb[1]) { // incorrect words
			// remove leading 'Wrong guesses: ' from Detail
			var x = Detail.substring(15).split(' ');
			qDetails += makeHiddenField(Q+'wrong', x);
		}
	}
	return qDetails;
}
function GetSequiturDetails(v) {
	qDetails = '';
	// there is no information available ... at the moment
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
	return 	is_LMS() ? '' : makeHiddenField(Q.substring(0, Q.length-1), '---------------------------------');
}
function makeHiddenField(name, value) {
	var field = '';
	var t = typeof(value);
	if (t=='string') {
		value = encode_entities(value);
	} else if (t=='object') {
		var values = value;
		var i_max = values.length;
		value = '';
		for (var i=0; i<i_max; i++) {
			values[i] = trim(values[i]);
			if (values[i]!='') {
				value += (i==0 ? '' : ',') +  encode_entities(values[i]);
			}
		}
	}
	if (is_LMS()) {
		if (value && value.indexOf && value.indexOf('<')>=0 && value.indexOf('>')>=0) {
			value = '<![CDATA[' + value + ']]>';
		}
		field = '<field><fieldname>' + name + '</fieldname><fielddata>' + value + '</fielddata></field>';
	} else {
		field = '<INPUT type=hidden name="' + name + '" value="' + value + '">';
	}
	return field;
}
function trim(s) {
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
		// 44 : comma ..............[,]
		// 60 : left angle bracket .[<] &lt;
		// 62 : right angle bracket [>] &gt;
		// >=128 multibyte character
		s_out += (c<128) ?  s_in.charAt(i) : ('&#x' + pad(c.toString(16), 4) + ';');
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

function getFunction(fn) {
	if (typeof(fn)=='string') {
		fn = eval('window.' + fn);
	}
	return (typeof(fn)=='function') ? fn : null;
}
function getFunctionCode(fn, extra) {
	var s = '';
	var obj = getFunction(fn);
	if (obj) {
		s = obj.toString();
		var i1 = s.indexOf('{')+1;
		var i2 = s.lastIndexOf('}');
		if (i1>0 && i1<i2) {
			s = s.substring(i1, i2);
		}
	}
	return s + (extra ? extra : '');
}
function getFunctionArgs(fn) {
	var a = new Array();
	var obj = getFunction(fn);
	if (obj) {
		var s = obj.toString();
		var i1 = s.indexOf('(')+1;
		var i2 = s.indexOf(')');
		if (i1>0 && i1<i2) {
			a = s.substring(i1, i2).split(',');
		}
	}
	return (a.length) ? ('"'+ a.join('","') + '",') : '';
}
function getPrompt(fn) {
	// the LoginPrompt is the text string in the first prompt(...) statement
	//	v5 : in the StartUp function
	//	v6 : in the GetUserName function
	// Note: netscape uses double-quote as delimiter, others use single quote
	var s = getFunctionCode(fn);
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
	var s = getFunctionCode(fn);
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
	return document.forms['store'] ? true : false;
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

function sniff_quiz() {
	// "sniff" (=detect) the quiz's type and intended browser version
	// and cache the values in a global variable caleld "quiz"

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

	// intended browser version
	//	3   : ns3, ie3 (frames)
	//	4   : ns4, ie4 (cross browser dhtml)
	//	5   : ie5 (frames, send results via CGI)
	//	6   : ie6, op7, gecko (w3 standards)
	//	6.1 : "drag and drop" versions of JMatch and JMix v6

	// create the global "quiz" object, if necessary
	if (!window.quiz) window.quiz = new Object();

	// check the version and type are not already set
	if (!quiz.v || !quiz.t) {

		// initialize version and type
		var v = 0;
		var t = 0; 

		// set shortcuts to DOM objects
		var d = document;
		var f = d.forms;

		if (f.QuizForm && f.CheckForm && self.CorrectAnswers) {
			v = 3;
			t = 4; // jmatch

		} else if (self.FeedbackFrame && self.CodeFrame) {
			v = 3;
			f = CodeFrame.document.forms;
			t = (f.QuizForm) ? 1 : (f.Cloze) ? 2 : (f.Crossword) ? 3 : (f.QForm1) ? 6 : 0;

		} else if (self.DynLayer) {
			v = 4;
			if (d.layers) {
				// for NS4, adjust "f" to point to a forms object in a layer
				var lyr = d.QuestionDiv || d.CWDiv || d.TitleDiv || null;
				if (lyr) f = lyr.document.forms;
			}
			t = (f.QForm && f.QForm.FB0) ? 1 : (f.Cloze) ? 2 : (f.Crossword) ? 3 : (f.ExCheck) ? 4 : (f.QForm && f.QForm.Answer) ? 6 : 0;

		} else if (self.TopFrame && self.BottomFrame) {
			v = 5;
			f = BottomFrame.document.forms;
			t = (f.QForm && f.QForm.elements[0].name.substring(0,3)=='FB_') ? 1 : (f.Cloze) ? 2 : (self.GetAnswerOpener && GetAnswerOpener.indexOf('AnswerForm')>=0) ? 3 : (f.QForm && self.RItems) ? 4 : (f.ButtonForm) ? 5 : (f.QForm0 && f.Buttons0) ? 6 : 0;

		} else if (GetObj(d, 'MainDiv')) {
			v = 6;
			var obj = (f.QForm) ? f.QForm.elements : null;
			t = (obj && obj.length>0 && obj[0].id=='') ? 1 : (f.Cloze) ? 2 : (GetObj(d, 'GridDiv') || GetObj(d, 'Clues')) ? 3 : GetObj(d, 'MatchDiv') ? 4 : GetObj(d, 'SegmentDiv') ? 5 : ((f.QForm && f.QForm.Guess) || GetObj(d, 'Questions')) ? 6 : 0;

		} else if (GetObj(d, 'D0')) {
			v = 6.1; // drag and drop (HP5 and HP6)
			t = (GetObj(d, 'F0')) ? 4 : (GetObj(d, 'Drop0')) ? 5 : 0;

		} else if (window.Words && f.Rhubarb) {
			v = 6;
			t = 7; // rhubarb (TexToys)

		} else if (window.Segments && GetObj(d, 'Story')) {
			v = 6;
			t = 8; // sequitur (TexToys)

		}

		if (v) quiz.v = v; // intended browser version
		if (t) quiz.t = t; // quiz type
	}
}
function get_quiz_type() {
	sniff_quiz();
	return quiz.t;
}
function get_quiz_version() {
	sniff_quiz();
	return quiz.v;
}

function all_finished(a, s, aa, ss) {
	// determine whether or not all quistions in a quiz are finished

	// a  : outer array
	// s  : condition, if any, on outer array
	// aa : inner array, if any
	// ss : condition, if any, on inner array

	// the arrays "a" and "aa" may be passed as arrays or strings to be eval(uated)
	// the conditions "s" and "ss" are specified as strings to be eval(uated)

	// assume a positive result
	var r = true;

	// set length of outer array. if any
	var l = (typeof(a)=="string") ? eval(a + ".length") : a ? a.length : 0;

	// loop through outer array
	for (var i=0; i<l; i++) {

		// do outer condition, if any
		if (s && eval(s)) r = false;

		// set length of inner array, if any
		var ll = (typeof(aa)=="string") ? eval(aa + ".length") : aa ? aa.length : 0;

		// loop through inner array. checking inner condition
		for (var ii=0; ii<ll; ii++) {
			if (ss && eval(ss)) r = false;
		}
	}
	return r;
}

function is_finished() {

	// assume false result
	var r = false; 

	var t = get_quiz_type();
	var v = get_quiz_version();

	if (t==1) { // jbc

		if (v==3) r = all_finished(DoneStatus, "i>0 && a[i]=='0'");
		else if (v==4) r = all_finished(DoneStatus, "a[i]==0");
		else if (v==5 || v==6) r = all_finished(Status, "a[i][0]==0");


	} else if (t==2) { // jcloze

		if (v==3 || v==4 || v==5 || v==6) r = all_finished(I, "CheckAnswer(i)==-1");
		// also:   else if (v==5 || v==6) r = all_finished(State, "a[i][4]!=1")

	} else if (t==3) { // jcross

		if (v==3) r = all_finished(document.Crossword.elements, "ConvertCase(is.mac?unescape(MacStringToWin(a[i].value)):a[i].value,1)!=Letters[i]");
		else if (v==4) r = all_finished(WinLetters, "ConvertCase(GetBoxValue(i),1).charAt(0) != a[i].charAt(0)");
		else if (v==5) r = all_finished(L, "", "L[i]", "L[i][ii] && L[i][ii]!=G[i][ii]");

	} else if (t==4) { // jmatch

		if (v==3) r = all_finished(CorrectAnswers, "document.QuizForm.elements[i*2].selectedIndex != a[i]");
		else if (v==4) r = all_finished(Draggables, "a[i].correct!='1'");
		else if (v==5) r = all_finished(I, "I[i][2]<1 && I[i][0].length>0 && Status[i][0]<1 && GetAnswer(i)!=I[i][3]");
		else if (v==6) r = all_finished(D, "D[i][2]==0 || D[i][2]!=D[i][1]");

	} else if (t==5) { // jmix

		// there was no v3 or v4 of JMix
		if (v==5 || v==6) r = !all_finished(Answers, "a[i].join(',')=='" + GuessSequence.join(',') + "'");

	} else if (t==6) { // jquiz

		if (v==3 || v==4) r = all_finished(State, "a[i][0]==0");
		else if (v==5 || v==6) r = all_finished(State, "a[i] && a[i][0]<0");

	} else if (t==7) { // rhubarb
		if (v==6) r = all_finished(DoneList, "a[i]==1");

	} else if (t==8) { // sequitur
		if (v==6) r = (CurrentNumber==TotalSegments || AllDone);
	}

	return r; // result
}

function GetObj(d, id) {
	return d.getElementById ? d.getElementById(id) : d.all ? d.all[id] : d[id];
}

// **************
//  initialization
// **************

if (window.Finish==null) { // v3, v4 and v5
	// modify the function which writes feedback to call Finish() if the quiz is finished
	// 	usually this is the WriteFeedback()
	// 	but v3 of JMatch uses CheckAnswer()
	var f = window.WriteFeedback ? 'WriteFeedback' : 'CheckAnswer';
	var s = getFunctionCode(f, 'if(is_finished())Finish();');
	var a = getFunctionArgs(f);
	eval('window.' + f + '=new Function(' + a + 's)');
}

// the standard Finish() function
// for v6, this overwrites the original function
function Finish(){
	var f = document.store;
	if (f) {
		// hotpot use "Score", TexToys use "FinalScore"
		var mark = (window.Score ? Score : window.FinalScore ? FinalScore : 0);
		f.starttime.value = getTime(Start_Time);
		f.endtime.value = getTime();
		f.mark.value = mark;
		f.detail.value = '<?xml version="1.0"?><hpjsresult><fields>'+GetQuestionDetails()+'</fields></hpjsresult>';
		f.submit();
	}
}

// create form to send results
if (DB[7] && DB[8] && !is_LMS()) { 
	ResultForm = ''
		+ '<html><body>'
		+ '<form name="Results" action="" method="post" enctype="x-www-form-encoded">'
		+ 	makeHiddenField('recipient', '')
		+ 	makeHiddenField('subject', '')
		+ 	makeHiddenField('Exercise', '')
		+ 	makeHiddenField('realname', '')
		+ 	makeHiddenField('Score', '')
		+ 	makeHiddenField('Start_Time', '')
		+ 	makeHiddenField('End_Time', '')
		+ 	makeHiddenField('title', 'Thanks!')
		+ '</form>'
		+ '</body></html>'
	;
}
// reassign the StartUp function
var p = getPrompt(window.GetUserName || window.StartUp);
var c = getStartUpCode(window.StartUp);
if (p && c) {
	window.StartUp = new Function('QuizLogin("' + p + '")');
	window.StartQuiz = new Function('if(!is_LMS()){' + c + '}');
}

// reassign the SendResults function
window.SendResults = SendAllResults;

// set start time
var Start_Time = new Date();

//-->
