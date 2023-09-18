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
 * Custom auto-complete adapter to load users from the assignment list_participants webservice.
 *
 * @module     mod_assign/participant_selector
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax', 'jquery', 'core/templates'], function(ajax, $, templates) {


    return /** @alias module:mod_assign/participants_selector */ {

        // Public variables and functions.
        /**
         * Process the results returned from transport (convert to value + label)
         *
         * @method processResults
         * @param {String} selector
         * @param {Array} data
         * @return {Array}
         */
        processResults: function(selector, data) {
            return data;
        },

        /**
         * Fetch results based on the current query. This also renders each result from a template before returning them.
         *
         * @method transport
         * @param {String} selector Selector for the original select element
         * @param {String} query Current search string
         * @param {Function} success Success handler
         * @param {Function} failure Failure handler
         */
        transport: function(selector, query, success, failure) {
            var assignmentid = $(selector).attr('data-assignmentid');
            var groupid = $(selector).attr('data-groupid');
            var filters = $('[data-region="configure-filters"] input[type="checkbox"]');
            var filterstrings = [];

            filters.each(function(index, element) {
                filterstrings[$(element).attr('name')] = $(element).prop('checked');
            });

            ajax.call([{
                methodname: 'mod_assign_list_participants',
                args: {
                    assignid: assignmentid,
                    groupid: groupid,
                    filter: query,
                    limit: 30,
                    includeenrolments: false,
                    tablesort: true
                }
            }])[0].then(function(results) {
                var promises = [];
                var identityfields = $('[data-showuseridentity]').data('showuseridentity').split(',');

                // We got the results, now we loop over them and render each one from a template.
                $.each(results, function(index, user) {
                    var ctx = user,
                        identity = [],
                        show = true;

                    if (filterstrings.filter_submitted && !user.submitted) {
                        show = false;
                    }
                    if (filterstrings.filter_notsubmitted && user.submitted) {
                        show = false;
                    }
                    if (filterstrings.filter_requiregrading && !user.requiregrading) {
                        show = false;
                    }
                    if (filterstrings.filter_grantedextension && !user.grantedextension) {
                        show = false;
                    }
                    if (show) {
                        $.each(identityfields, function(i, k) {
                            if (typeof user[k] !== 'undefined' && user[k] !== '') {
                                ctx.hasidentity = true;
                                identity.push(user[k]);
                            }
                        });
                        ctx.identity = identity.join(', ');
                        promises.push(templates.render('mod_assign/list_participant_user_summary', ctx).then(function(html) {
                            return {value: user.id, label: html};
                        }));
                    }
                });
                // Do the dance for $.when()
                return $.when.apply($, promises);
            }).then(function() {
                var users = [];

                // Determine if we've been passed any arguments..
                if (arguments[0]) {
                    // Undo the $.when() dance from arguments object into an array..
                    users = Array.prototype.slice.call(arguments);
                }

                success(users);
                return;
            }).catch(failure);
        }
    };
});
