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
 * mod/hotpot/attempt/hp/6/jcloze/dropdown.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JClozeDropDown
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JClozeDropDown(sendallclicks, ajax) {
    this.quiztype = 'JCloze';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        this.questions[i].name = parseInt(i)+1; // gap number
        this.questions[i].type = 2;             // 2 = JCloze
        this.questions[i].text = I[i][2];       // clue text
        this.questions[i].guesses = new Array();
    }

    /**
     * onclickCheck
     *
     * @param xxx setScores
     */
    this.onclickCheck = function (setScores) {
        var i_max = this.questions.length;
        for (var i=0; i<i_max; i++) {

            if (GapList[i][1].GapLocked) {
                // already correct
                continue;
            }

            var ii = Get_SelectedDropValue(i);
            if (isNaN(ii) || ii<0) {
                // nothing selected yet
                continue;
            }

            if (window.MakeIndividualDropdowns) {
                var is_wrong = (ii!=0);
                var g = I[i][1][ii][0];
                var MaxNumOfTrials = I[i][1].length;
            } else {
                var is_wrong = (ii!=i);
                var g = I[ii][1][0][0];
                var MaxNumOfTrials = GapList.length;
            }
            if (! g) {
                // no gap content - shouldn't happen
                continue;
            }
            if (setScores) {
                if (MaxNumOfTrials && MaxNumOfTrials > GapList[i][1].NumOfTrials){
                    GapList[i][1].Score = 1 - (GapList[i][1].NumOfTrials / MaxNumOfTrials);
                } else {
                    GapList[i][1].Score = 0;
                }
                if (GapList[i][1].ClueAskedFor){
                    GapList[i][1].Score /= 2;
                }
            }

            // shortcut to this question
            var question = this.questions[i];

            // increment check count (even if gap content has not changed)
            question.checks++;

            var g_max = question.guesses.length;
            if (g_max && g==question.guesses[g_max-1]) {
                // gap content has not changed
                continue;
            }

            // create shortcut ot array of correct or wrong responses
            if (is_wrong) {
                var responses = question.wrong;
            } else {
                var responses = question.correct;
            }
            var r_max = responses.length;
            for (var r=0; r<r_max; r++) {
                if (responses[r]==g) {
                    // this guess has been entered before
                    break;
                }
            }
            if (r==r_max) {
                // if this is a new g(uess), i.e. it has not been entered before
                // append g(uess) to the end of the array of responses
                responses[r] = g;
            }
        } // end for loop

        if (setScores) {
            var TotalScore = 0;
            for (var i=0; i<i_max; i++) {
                TotalScore += GapList[i][1].Score;
            }
            window.Score = Math.floor((TotalScore * 100) / i_max);
        }
    } // end function

    /**
     * setQuestionScore
     *
     * @param xxx q
     */
    this.setQuestionScore = function (q) {
        if (GapList[q]) {
            this.questions[q].score = Math.max(0, 100 * GapList[q][1].Score) + '%';
        }
    }

    this.init(I.length, sendallclicks, ajax);
}
JClozeDropDown.prototype = new hpQuizAttempt();
