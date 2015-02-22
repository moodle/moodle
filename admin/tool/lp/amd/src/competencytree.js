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
     var competencies = [];

     var competencyFrameworkId = 0;

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


     return {
        // Public variables and functions.
        init: function(id) {
            competencyFrameworkId = id;
            loadCompetencies('').fail(notification.exception);
        },

        getCompetencyFrameworkId: function() {
            return competencyFrameworkId;
        },
        // Public variables and functions.
        getCompetency: function(id) {
            return competencies[id];
        },

        listCompetencies: function() {
            return competencies;
        },

        applySearch: function(searchtext) {
            return loadCompetencies(searchtext);
        }
     };
 });
