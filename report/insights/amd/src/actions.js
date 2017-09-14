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
 * Module to manage report insights actions that are executed using AJAX.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This module manages prediction actions that require AJAX requests.
 *
 * @module report_insights/actions
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    return {

        /**
         * Attach on click handlers to hide predictions.
         *
         * @param {Number} predictionId The prediction id.
         * @access public
         */
        init: function(predictionId) {

            // Select the prediction with the provided id ensuring that an external function is set as method name.
            $('a[data-prediction-methodname][data-prediction-id=' + predictionId + ']').on('click', function(e) {
                e.preventDefault();
                var action = $(e.currentTarget);
                var methodname = action.attr('data-prediction-methodname');
                var predictionContainers = action.closest('tr');

                if (predictionContainers.length > 0) {
                    var promise = Ajax.call([
                        {
                            methodname: methodname,
                            args: {predictionid: predictionId}
                        }
                    ])[0];
                    promise.done(function() {
                        predictionContainers[0].remove();

                        // Move back if no remaining predictions.
                        if ($('.insights-list tr').length < 2) {
                            if (document.referrer) {
                                window.location.assign(document.referrer);
                            } else {
                                window.location.reload(true);
                            }
                        }
                    }).fail(Notification.exception);
                }
            });
        }
    };
});
