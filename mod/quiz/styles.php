

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


body#mod-quiz-grading table#grading .header {
  text-align: left;
}
body#mod-quiz-grading table#grading .picture {
  text-align: center !important;
}
body#mod-quiz-grading .controls {
  text-align: center;
}

body#mod-quiz-grading table#grading td {
  border-color: #dddddd;
}
body#mod-quiz-grading table#grading .r1 {
  background-color: #eeeeee;
}
/* grading */



body#mod-quiz-report table#responses {
  margin: 20px auto;
}
body#mod-quiz-report table#responses .header,
body#mod-quiz-report table#responses .cell
{
  padding: 4px;
}
body#mod-quiz-report table#responses .header .commands {
  display: inline;
}
body#mod-quiz-report table#responses td {
  border-width: 1px;
  border-style: solid;
}
body#mod-quiz-report table#responses .header {
  text-align: left;
}
body#mod-quiz-report table#responses .numcol {
  text-align: center;
  vertical-align : middle !important;
}

body#mod-quiz-report table#responses .uncorrect {
  color: red;
}

body#mod-quiz-report table#responses .correct {
  color: green;
}

body#mod-quiz-report table#responses .partialcorrect {
  color: orange;
}

#mod-quiz-attempt #timer .generalbox {
  width:150px
}
#mod-quiz-attempt #timer {
  position:fixed !important;
  top:100px !important;
  left:10px !important
}
* html #mod-quiz-attempt #timer {
  position:absolute !important
}