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
 * Bulk rating.
 *
 * @module     report_lpmonitoring/bulkrating
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 */

define(['jquery',
    'core/ajax',
    'core/notification'],
    function($, ajax, notification) {

        /**
         * Default scales values object.
         * @param {Number} templateid The template id
         */
        var BulkRating = function(templateid) {
            this.templateid = templateid;
            // Clear form inputs.
            $(document).ready(function() {
                $('#savescalesvalues').trigger('reset');
            });
            $(document).on('submit', '#savescalesvalues', this.saveHandler.bind(this));
            $(document).on('change', '.donotapplybulk input[type=checkbox]', function() {
                var compid = $(this).data('compid');
                $('#rating_table_comp' + compid).toggleClass("enabled disabled");
                if ($(this).prop('checked')) {
                    $('#rating_table_comp' + compid).find('input').prop('disabled', true);
                } else {
                    $('#rating_table_comp' + compid).find('input').prop('disabled', false);
                }
            });
        };

        /** @var {Number} The template id. */
        BulkRating.prototype.templateid = null;

        /**
         * Triggered when form is submitted.
         *
         * @name   saveHandler
         * @return {Void}
         * @function
         */
        BulkRating.prototype.saveHandler = function() {
            var scalesvalues = [];
            var forcerating = false;
            var self = this,
                requests;

            $('#savescalesvalues .enabled input[type=radio]:checked').each(function () {
                var compid = $(this).data('compid');
                scalesvalues.push({compid : compid, value : $(this).val()});
            });
            if ($('#savescalesvalues input[name="forcerating"]').prop('checked')) {
                forcerating = true;
            }
            scalesvalues = JSON.stringify(scalesvalues);
            requests = ajax.call([{
                methodname: 'report_lpmonitoring_add_rating_task',
                args: {
                    templateid: self.templateid,
                    defaultscalesvalues: scalesvalues,
                    forcerating: forcerating
                }
            }]);

            requests[0].done(function(context) {
                if (context) {
                    $('#savescalesvalues input[type=submit]').attr('disabled', true);
                    $('#msg-success-cmrating').show();
                }
            }).fail(notification.exception);

            return false;
        };

        return {
            /**
             * Main initialisation.
             *
             * @param {Number} templateid The template id.
             * @return {BulkRating} A new instance of BulkRating.
             * @method init
             */
            init: function(templateid) {
                return new BulkRating(templateid);
            }
        };

    });
