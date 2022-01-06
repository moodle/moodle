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
 * Competency rule config.
 *
 * @module     tool_lp/competency_outcomes
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/str'],
        function($, Str) {

    var OUTCOME_NONE = 0,
        OUTCOME_EVIDENCE = 1,
        OUTCOME_COMPLETE = 2,
        OUTCOME_RECOMMEND = 3;

    return {

        NONE: OUTCOME_NONE,
        EVIDENCE: OUTCOME_EVIDENCE,
        COMPLETE: OUTCOME_COMPLETE,
        RECOMMEND: OUTCOME_RECOMMEND,

        /**
         * Get all the outcomes.
         *
         * @return {Object} Indexed by outcome code, contains code and name.
         * @method getAll
         */
        getAll: function() {
            var self = this;
            return Str.get_strings([
                {key: 'competencyoutcome_none', component: 'tool_lp'},
                {key: 'competencyoutcome_evidence', component: 'tool_lp'},
                {key: 'competencyoutcome_recommend', component: 'tool_lp'},
                {key: 'competencyoutcome_complete', component: 'tool_lp'},
            ]).then(function(strings) {
                var outcomes = {};
                outcomes[self.NONE] = {code: self.NONE, name: strings[0]};
                outcomes[self.EVIDENCE] = {code: self.EVIDENCE, name: strings[1]};
                outcomes[self.RECOMMEND] = {code: self.RECOMMEND, name: strings[2]};
                outcomes[self.COMPLETE] = {code: self.COMPLETE, name: strings[3]};
                return outcomes;
            });
        },

        /**
         * Get the string for an outcome.
         *
         * @param  {Number} id The outcome code.
         * @return {Promise} Resolved with the string.
         * @method getString
         */
        getString: function(id) {
            var self = this,
                all = self.getAll();

            return all.then(function(outcomes) {
                if (typeof outcomes[id] === 'undefined') {
                    return $.Deferred().reject().promise();
                }
                return outcomes[id].name;
            });
        }
    };
});
