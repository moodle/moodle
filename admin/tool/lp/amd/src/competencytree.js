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
 * Handle selection changes on the competency tree.
 *
 * @module     tool_lp/competencyselect
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 define(['core/ajax', 'core/notification', 'jquery'], function(ajax, notification, $) {
     // Private variables and functions.
      /** @var {Object[]} competencies - Cached list of competencies */
     var competencies = [];

     /** @var {Number} competencyFrameworkId - The current framework id */
     var competencyFrameworkId = 0;

     /**
      * Load the list of competencies via ajax. Competencies are filtered by the searchtext.
      * @param {String} searchtext The text to filter on.
      * @return {promise}
      */
     var loadCompetencies = function(searchtext) {
         var deferred = $.Deferred();
         searchtext = '';
         var promises = ajax.call([{
             methodname: 'tool_lp_search_competencies',
             args: {
                 searchtext: searchtext,
                 competencyframeworkid: competencyFrameworkId
             }
         }]);
         promises[0].done(function(result) {
             competencies = [];
             var i = 0;
             for (i = 0; i < result.length; i++) {
                 competencies[result[i].id] = result[i];
             }
             deferred.resolve(competencies);
         }).fail(function(exception) {
             deferred.reject(exception);
         });

         return deferred.promise();
     };


     return /** @alias module:tool_lp/competencytree */ {
        // Public variables and functions.
        /**
         * Initialise the tree.
         *
         * @param {Number} id The competency id.
         */
        init: function(id) {
            competencyFrameworkId = id;
            loadCompetencies('').fail(notification.exception);
        },

        /**
         * Get the competency framework id this model was initiliased with.
         *
         * @return {Number}
         */
        getCompetencyFrameworkId: function() {
            return competencyFrameworkId;
        },

        /**
         * Get a competency by id
         *
         * @param {Number} id The competency id
         * @return {Object}
         */
        getCompetency: function(id) {
            return competencies[id];
        },

        /**
         * Get all competencies for this framework.
         *
         * @return {Object[]}
         */
        listCompetencies: function() {
            return competencies;
        },

        /**
         * Reload the list of competencies, filtered by the search text.
         *
         * @param {String} searchtext The text to filter by.
         * @return {Object[]} The filtered competency list.
         */
        applySearch: function(searchtext) {
            return loadCompetencies(searchtext);
        }
     };
 });
