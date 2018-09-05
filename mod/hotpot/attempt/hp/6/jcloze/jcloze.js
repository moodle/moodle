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
 * mod/hotpot/attempt/hp/6/jcloze/jcloze.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JCloze
 *
 * @param xxx sendallclicks
 * @param xxx ajax
 */
function JCloze(sendallclicks, ajax) {
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
        var TotalScore = 0;

        var i_max = this.questions.length;
        for (var i=0; i<i_max; i++) {

            if (State[i].AnsweredCorrectly) {
                TotalScore += State[i].ItemScore;
                continue;
            }

            var g = GetGapValue(i);
            if (! g) {
                // no gap content
                continue;
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

            question.guesses[g_max] = g;
            var G = g.toUpperCase();

            // try to match g(uess) to one of the items
            // in the array of correct answers, I[i][1]
            var TotalChars = 0;
            var MaxMatchingChars = 0;
            var TotalMatchingChars = 0;

            var ii_max = I[i][1].length;
            for (var ii=0; ii<ii_max; ii++) {

                var a = I[i][1][ii][0];
                var A = a.toUpperCase();
                TotalChars = a.length;

                if (window.CaseSensitive) {
                    if (g==a) {
                        // case sensitive match found
                        break;
                    }
                } else {
                    if (G==A) {
                        // case INsensitive match found
                        break;
                    }
                }

                // wrong answer, so count how many chars are correct
                var MatchingChars = 0;
                for (var iii=0; iii<Math.min(TotalChars, g.length); iii++) {
                    if (window.CaseSensitive) {
                        if (g.charAt(iii) != a.charAt(iii)) {
                            break;
                        }
                    } else {
                        if (G.charAt(iii) != A.charAt(iii)) {
                            break;
                        }
                    }
                    MatchingChars++;
                }
                if (MatchingChars > MaxMatchingChars) {
                    MaxMatchingChars = MatchingChars;
                    TotalMatchingChars = Math.max(TotalChars, g.length);
                }
            } // end for ii

            // create shortcut to array of correct or wrong responses
            if (ii==ii_max) {
                // the end of the loop was reached and no match was found
                // i.e. this g(uess) is a WRONG response
                var responses = question.wrong;
                if (setScores && TotalMatchingChars) {
                    State[i].ItemScore = (MaxMatchingChars - State[i].HintsAndChecks) / TotalMatchingChars;
                }
            } else {
                // the loop was aborted early because a match was found
                // i.e. this g(uess) is a CORRECT response
                var responses = question.correct;
                if (setScores && TotalChars) {
                    SetCorrectAnswer(i, g);
                    State[i].AnsweredCorrectly = true;
                    State[i].ItemScore = (TotalChars - State[i].HintsAndChecks) / TotalChars;
                }
            }
            if (setScores && State[i].ItemScore) {
                if (State[i].ItemScore < 0 ) {
                    State[i].ItemScore = 0;
                } else if (State[i].ClueGiven) {
                    State[i].ItemScore /= 2;
                }
                TotalScore += State[i].ItemScore;
                if (State[i].Guesses==null) {
                    State[i].Guesses = new Array();
                }
                State[i].Guesses.push(g);
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

        // set total score for this quiz
        if (setScores && i_max) {
            window.Score = Math.floor((TotalScore * 100) / i_max);
        }
    } // end function

    /**
     * setQuestionScore
     *
     * @param xxx q
     */
    this.setQuestionScore = function (q) {
        if (State[q]) {
            this.questions[q].score = Math.max(0, 100 * State[q].ItemScore) + '%';
        }
    }

    this.init(I.length, sendallclicks, ajax);
}
JCloze.prototype = new hpQuizAttempt();
