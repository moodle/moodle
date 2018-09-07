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
 * mod/hotpot/attempt/hp/6/rhubarb/rhubarb.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rhubarb
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function Rhubarb(sendallclicks, ajax) {
    this.quiztype = 'Rhubarb';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        this.questions[i].name = i+1; // since there is only one question, this is always "1"
        this.questions[i].type = 7;   // 7 = Rhubarb
        this.questions[i].text = '';  // always empty for Rhubarb
    }

    /**
     * onclickCheck
     *
     * @param xxx g
     */
    this.onclickCheck = function (g) {
        var G = g.toUpperCase();
        var i_max = Words.length;
        for (var i=0; i<i_max; i++) {
            if (G==Words[i].toUpperCase()) {
                // a correct word
                break;
            }
        }
        if (i<i_max) {
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
        if (forceRecalculate) {
        }
        this.score = Math.floor(100*Correct/TotalWords);
    }

    this.init(1, sendallclicks, ajax);
}
Rhubarb.prototype = new hpQuizAttempt();
