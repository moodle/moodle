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
 * mod/hotpot/attempt/hp/6/jcloze/findit.b.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JClozeFindItB
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JClozeFindItB(sendallclicks, ajax) {
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

        if (typeof(i)=='undefined') {
            // we are being called from CheckAnswers()
            for (var i=0; i<GapList.length; i++) {
                if (GapList[i][1].ErrorFound && ! GapList[i][1].GapSolved) {
                    if (CheckAnswer(i)>=0) {
                        iscorrect = true;
                    } else {
                        iscorrect = false;
                    }
                    this.onclickCheck(iscorrect, i, GetGapValue(i));
                }
            }
            return;
        }

        if (typeof(g)=='undefined') {
            // user has clicked the target word but not entered anything yet
            return;
        }

        if (iscorrect && GapList[i][1].GapSolved) {
            // gap is already correct
            return;
        }
        // if (iscorrect==false), the user has clicked on an incorrect word
        // so we want to continue and add it to the array of wrong guesses

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
            if (TotGaps) {
                var TotScore = 0;
                for (var x=0; x<TotGaps; x++){
                    TotScore += GapList[x][1].Score;
                }
                if (TotScore || TotWrongChoices) {
                    window.Score = Math.floor((TotScore * 100)/(TotScore + TotWrongChoices));
                }
            }
        }
        this.score = window.Score || 0;
        this.penalties = window.Penalties || 0;
    }

    this.init(I.length, sendallclicks, ajax);
}
JClozeFindItB.prototype = new hpQuizAttempt();
