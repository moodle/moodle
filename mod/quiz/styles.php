body#mod-quiz-report table#itemanalysis {
  margin: 20px auto;
}
body#mod-quiz-report table#itemanalysis .header,
body#mod-quiz-report table#itemanalysis .cell
{
  padding: 4px;
}
body#mod-quiz-report table#itemanalysis .header .commands {
  display: inline;
}
body#mod-quiz-report table#itemanalysis td {
  border-width: 1px;
  border-style: solid;
}
body#mod-quiz-report table#itemanalysis .header {
  text-align: left;
}
body#mod-quiz-report table#itemanalysis .numcol {
  text-align: center;
  vertical-align : middle !important;
}

body#mod-quiz-report table#itemanalysis .uncorrect {
  color: red;
}

body#mod-quiz-report table#itemanalysis .correct {
  color: blue;
  font-weight : bold;
}

body#mod-quiz-report table#itemanalysis .partialcorrect {
  color: green !important;
}

body#mod-quiz-report table#itemanalysis .qname {
  color: green !important;
}

/* manual grading */
body#mod-quiz-grading table#grading
{
  width: 80%;
  margin: auto;
}
body#mod-quiz-grading table#grading
{
  margin: 20px auto;
}
body#mod-quiz-grading table#grading .header,
body#mod-quiz-grading table#grading .cell
{
  padding: 4px;
}
body#mod-quiz-grading table#grading .header .commands 
{
  display: inline;
}
body#mod-quiz-grading table#grading .picture 
{
  width: 40px;
}
body#mod-quiz-grading table#grading td 
{
  border-left-width: 1px;
  border-right-width: 1px;
  border-left-style: solid;
  border-right-style: solid;
  vertical-align: bottom;
}
.mod-quiz .quiz-report-title {
  text-align: center;
  font-weight : bold;
}
.mod-quiz .grade {
  font-size: small;
  margin-top: 10pt
}
.mod-quiz .gradingdetails {
  font-size: small;
}
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
  /* width: 5%; */
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

#mod-quiz-attempt #timer .generalbox {
  width:150px
}

#mod-quiz-attempt #timer {
  position:absolute;
  /*top:100px; is set by js*/
  left:10px
}



