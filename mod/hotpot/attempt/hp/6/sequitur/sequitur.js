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
 * mod/hotpot/attempt/hp/6/sequitur/sequitur.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sequitur
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function Sequitur(sendallclicks, ajax) {
    this.quiztype = 'Sequitur';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        this.questions[i].name = i+1; // since there is only one question, this is always "1"
        this.questions[i].type = 8;   // 8 = Sequitur
        this.questions[i].text = '';  // always empty for Sequitur
    }

    /**
     * onclickCheck
     *
     * @param xxx Chosen
     */
    this.onclickCheck = function (Chosen) {
        if (Chosen==0) {
            return; // stop button
        }
        var g = GetTextFromNode(document.getElementById('Choice'+Chosen));
        if (! g) {
            return; // shouldn't happen
        }
        if (Chosen==CurrentCorrect) {
            var responses = this.questions[0].correct;
        } else {
            var responses = this.questions[0].wrong;
        }
        var r_max = responses.length;
        for (var r=0; r<r_max; r++) {
            if (responses[r]==g) {
                // this g(uess) has been entered before
                break;
            }
        }
        if (r==r_max) {
            // if this is a new g(uess), i.e. it has not been entered before,
            // append g(uess) to the end of the array of responses
            responses[r] = g;
            this.questions[0].checks++;
        }
    }

    /**
     * setScoreAndPenalties
     *
     * @param xxx forceRecalculate
     */
    this.setScoreAndPenalties = function (forceRecalculate) {
        if (window.TotalPointsAvailable) {
            this.score = Math.floor(100 * ScoredPoints / TotalPointsAvailable);
        } else if (Finished) {
            this.score = Math.floor(100 * ScoredPoints / TotalPoints);
        } else {
            this.score = Math.floor(100 * ScoredPoints / (TotalPoints - OptionsThisQ + 1));
        }
    }

    this.init(1, sendallclicks, ajax);
}
Sequitur.prototype = new hpQuizAttempt();
