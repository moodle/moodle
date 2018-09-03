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
 * Edit items in feedback module
 *
 * @module     mod_feedback/edit
 * @package    mod_feedback
 * @copyright  2016 Marina Glancy
 */
define(['jquery', 'core/ajax', 'core/str', 'core/notification'],
function($, ajax, str, notification) {
    var manager = {
        deleteItem: function(e) {
            e.preventDefault();

            str.get_strings([
                {
                    key:        'confirmation',
                    component:  'admin'
                },
                {
                    key:        'confirmdeleteitem',
                    component:  'mod_feedback'
                },
                {
                    key:        'yes',
                    component:  'moodle'
                },
                {
                    key:        'no',
                    component:  'moodle'
                }
            ]).done(function(s) {
                notification.confirm(s[0], s[1], s[2], s[3], $.proxy(function() {
                    window.location = $(this).attr('href');
                }, e.currentTarget));
            });
        },

        setup: function() {
            $('body').delegate('[data-action="delete"]', 'click', manager.deleteItem);
        }
    };

    return {
        setup: manager.setup
    };
});
