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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

define(['jquery'], function($) {
    return {
        trackActivityLabelClicks: function(ajaxUrl) {
            $('.intelliboardLabelTracking').on('submit', function(e) {
                if (!$(this).data('allowsubmit')) {
                    e.preventDefault();

                    var cmId = $(this).parents('li.activity').attr('id').replace('module-', '');
                    var form = $(this);

                    if (cmId) {
                        $.ajax(ajaxUrl, {
                            data: {
                                page: 'module',
                                param: cmId,
                                time: 1
                            },
                            complete: function() {
                                form.attr('data-allowsubmit', 1);
                                form.submit();
                            }
                        });
                    }
                }
            });
        }
    };
});