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
 * mod/hotpot/attempt/hp/6/jquiz/jquiz.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JQuiz
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JQuiz(sendallclicks, ajax) {
    this.quiztype = 'JQuiz';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        var txt = GetTextFromNodeN(document.getElementById('Q_'+i), 'QuestionText');
        this.questions[i].name = txt; // the question (not always unique!)
        this.questions[i].type = 6;   // 6 = JQuiz
        this.questions[i].text = '';  // always empty for JQuiz
        this.questions[i].weighting = I[i][0];
    }

    /**
     * onclickCheck
     *
     * @param xxx args
     */
    this.onclickCheck = function (args) {
        if (! args) {
            // no args - shouldn't happen !!
            return;
        }
        var q = args[0]; // clue/question number
        var g = args[1]; // student's g(uess) at the correct response

        if (! g.length) {
            // no response
            return;
        }
        var G = g.toUpperCase(); // used for shortanswer only
        var correct_answer = ''; // used for multiselect only

        // set index of answer array in I (the question array)
        var i_max = I[q][3].length;
        for (var i=0; i<i_max; i++) {

            if (! I[q][3][i][2]) {
                // not a correct answer
                continue;
            }

            if (I[q][2]==3) {
                // multiselect
                correct_answer += (correct_answer  ? '+' : '') + I[q][3][i][0];
            } else {
                // multichoice, shortanswer
                if (window.CaseSensitive) {
                    if (g==I[q][3][i][0]) {
                        // case sensitive match found
                        break;
                    }
                } else {
                    if (G==I[q][3][i][0].toUpperCase()) {
                        // case INsensitive match found
                        break;
                    }
                }
            }
        } // end for loop

        if (i<i_max || g==correct_answer) {
            var responses = this.questions[q].correct;
        } else {
            var responses = this.questions[q].wrong;
        }

        var r_max = responses.length;
        for (var r=0; r<r_max; r++) {
            if (g==responses[r]) {
                // this g(uess) is already in the array of responses
                break;
            }
        }

        if (r==r_max) {
            // this is new response
            responses[r] = g;
        }

        // increment check count
        this.questions[q].checks++;
    }

    /**
     * setQuestionScore
     *
     * @param xxx q
     */
    this.setQuestionScore = function (q) {
        // questions that were not displayed have State[q] == null
        if (State[q]) {
            this.questions[q].score = Math.max(0, I[q][0] * State[q][0]) + '%';
        }
    }

    /**
     * setScoreAndPenalties
     *
     * @param xxx forceRecalculate
     */
    this.setScoreAndPenalties = function (forceRecalculate) {
        if (forceRecalculate) {
            // based on JQuiz calculateOverallScore()
            // but calculates score for ALL questions
            // not just those that have been attempted
            var TotalWeighting = 0;
            var TotalScore = 0;
            var TotalCount = 0;
            for (var QNum=0; QNum<State.length; QNum++){
                if (State[QNum]){
                    // question was displayed
                    TotalWeighting += I[QNum][0];
                    if (State[QNum][0] > -1){
                        // question was attempted
                        TotalScore += (I[QNum][0] * State[QNum][0]);
                        TotalCount ++;
                    }
                }
            }
            if (TotalWeighting > 0){
                window.Score = Math.floor((TotalScore/TotalWeighting)*100);
            } else if (TotalCount) {
                window.Score = 100;
            } else {
                // no questions attempted
                window.Score = 0;
            }
        }
        this.score = window.Score || 0;
        this.penalties = window.Penalties || 0;
    }

    this.init(State.length, sendallclicks, ajax);
}
JQuiz.prototype = new hpQuizAttempt();
