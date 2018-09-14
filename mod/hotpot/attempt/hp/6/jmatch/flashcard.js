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
 * mod/hotpot/attempt/hp/6/jmatch/flashcard.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JMatchFlashcard
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JMatchFlashcard(sendallclicks, ajax) {
    this.quiztype = 'JMatch';

    /**
     * initQuestion
     *
     * @param xxx i
     */
    this.initQuestion = function (i) {
        this.questions[i].name = GetTextFromNode(document.getElementById('L_' + i));
        this.questions[i].type = 4;  // 4 = JMatch
        this.questions[i].text = ''; // always empty for JMatch
    }

    /**
     * onclickCheck
     *
     * @param xxx CurrItem
     */
    this.onclickCheck = function (CurrItem) {
        if (CurrItem && CurrItem.id && CurrItem.id.match(new RegExp('^I_\\d+$'))) {
            var i = parseInt(CurrItem.id.substring(2));
            if (Stage==1) {
                this.questions[i].checks++;
            } else {
                this.questions[i].correct[0] = GetTextFromNode(document.getElementById('R_' + i));
            }
        }
    }

    /**
     * setScoreAndPenalties
     *
     * @param xxx forceRecalculate
     */
    this.setScoreAndPenalties = function (forceRecalculate) {
        // do nothing
    }

    if (window.QList) {
        this.init(QList.length, sendallclicks, ajax);
    }
}
JMatchFlashcard.prototype = new hpQuizAttempt();
