#mod-quiz-attempt #page {
	text-align: center;
}
#mod-quiz-attempt .question {
	width: 90%;
	text-align: left;
	margin: 10px auto 15px auto;
	border: 1px solid #DDD;
	clear: both;
}
#mod-quiz-attempt .question .info {
	float: left;
	margin: 5px;
	width: 8%;
}
#mod-quiz-attempt .question .no {
	font-size: 1.2em;
	font-weight: bold;
}
#mod-quiz-attempt .question .grade {
	margin-top: 0.5em;
	font-size: 0.8em;
}
#mod-quiz-attempt .question .content {
	float: right;
	margin: 5px;
	width: 88%;
}
#mod-quiz-attempt .question .qtext {
	margin-bottom: 1.5em;
}
#mod-quiz-attempt .question .ablock {
	margin: 0.7em 0 0.3em 0;
}
#mod-quiz-attempt .question .prompt {
	float: left;
	width: 15%;
	height: 2em;
	padding-top: 0.3em;
	/* font-size: 0.8em; */
}
#mod-quiz-attempt .question .answer {
	float: right;
	width: 83%;
	margin-bottom: 0.5em;
}
#mod-quiz-attempt .question .submit {
	position: relative;
	clear: both;
	float: left;
}
/* MSIE Hack */
* html #mod-quiz-attempt .question .submit {
	float: none;
}
#mod-quiz-attempt .question .c0,
#mod-quiz-attempt .question .c1 {
	padding: 0.3em 0 0.3em 0.3em;
	vertical-align: top;
}
#mod-quiz-attempt .question .r0 {
	background-color: #F5F5F5;
}
#mod-quiz-attempt .question .r1 {
	background-color: #EEE;
}
#mod-quiz-attempt .multichoice .c0 {
  width: 5%;
	vertical-align: top;
	padding-top: 0.4em;
}
#mod-quiz-attempt .shortanswer .answer,
#mod-quiz-attempt .truefalse .answer {
	background-color: #EEE;
	padding: 0.3em 0 0.3em 0.3em;
}
#mod-quiz-attempt .shortanswer .answer input {
	width: 85%;
}
#mod-quiz-attempt .shortanswer .feedback,
#mod-quiz-attempt .truefalse .feedback {
  clear: both;
	float: right;
	width: 83%;
	/* margin-left: 15%; */
	padding: 0 0 0.3em 0.3em;
	/* background-color: #EEE; */
  border: 1px solid #DDD;
}	
#mod-quiz-attempt .question .grading, 
#mod-quiz-attempt .question .history {
	float: right;
	margin: 5px;
	width: 88%;
}
.clearfix:after {
  content: "."; 
  display: block; 
  height: 0; 
  clear: both; 
  visibility: hidden;
}
/* Hides from IE-mac \*/
* html .clearfix {height: 1%;}
/* End hide from IE-mac */



