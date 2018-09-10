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
 * mod/hotpot/attempt/hp/6/jcloze/findit.a.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JClozeFindItA
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JClozeFindItA(sendallclicks, ajax) {
    this.quiztype = 'JCloze';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        this.questions[i].name = parseInt(i)+1; // gap number
        this.questions[i].type = 2;             // 2 = JCloze
        this.questions[i].text = I[i][1][0][0]; // the correct word
        this.questions[i].guesses = new Array();
    }

    /**
     * onclickCheck
     *
     * @param xxx iscorrect
     * @param xxx i
     * @param xxx g
     */
    this.onclickCheck = function (iscorrect,i,g) {
        if (window.Finished) {
            return; // quiz is already finished
        }

        if (i>=GapList.length) {
            i = GapList.length - 1;
        }

        if (iscorrect && GapList[i][1].ErrorFound) {
            return; // gap is already correct
        }


        if (iscorrect) {
            g = GapList[i][1].WrongGapValue;
        }

        // shortcut to this question
        var question = this.questions[i];

        // increment check count (even if gap content has not changed)
        question.checks++;

        var g_max = question.guesses.length;
        if (g_max && g==question.guesses[g_max-1]) {
            // gap content has not changed
            return;
        }
        question.guesses[g_max] = g;

        // create shortcut ot array of correct or wrong responses
        if (iscorrect) {
            var responses = question.correct;
        } else {
            var responses = question.wrong;
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

    /**
     * setScoreAndPenalties
     *
     * @param xxx forceRecalculate
     */
    this.setScoreAndPenalties = function (forceRecalculate) {
        if (forceRecalculate) {
            window.Score = 0;
            var TotGaps = GapList.length;
            if (TotGaps){
                var TotCorrectChoices = 0;
                for (var i=0; i<TotGaps; i++){
                    if (GapList[i][1].ErrorFound){
                        TotCorrectChoices++;
                    }
                }
                if (TotCorrectChoices > TotWrongChoices){
                    window.Score = Math.floor(100 * (TotCorrectChoices - TotWrongChoices) / TotGaps);
                }
            }
        }
        this.score = window.Score || 0;
        this.penalties = window.Penalties || 0;
    }

    this.init(I.length, sendallclicks, ajax);
}
JClozeFindItA.prototype = new hpQuizAttempt();
