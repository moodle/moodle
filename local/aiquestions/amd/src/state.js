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
 * Code for checking questions generation state.
 *
 * @package
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/templates', 'core/str'], function($, Ajax, Templates, str) {
    // Load the state of the questions generation every 20 seconds.
    var intervalId = setInterval(function() {
        checkState(intervalId);
    }, 20000);

    /**
     * Check the state of the questions generation.
     * @param {int} intervalId The interval id.
     * @return {void}
     * @example
     *  checkState(intervalId);
     */
    function checkState(intervalId) {
        var userid = $("#local_aiquestions_userid")[0].outerText;
        var uniqid = $("#local_aiquestions_uniqid")[0].outerText;
        var promises = Ajax.call([{
            methodname: 'local_aiquestions_check_state',
            args: {
                userid: userid,
                uniqid: uniqid
            }
        }]);
        promises[0].then(function(showSuccess) {
            // If Questions are ready, show success message.
            if (showSuccess[0].success != '') {
                var successmessage = JSON.parse(showSuccess[0].success);
                if (Object.keys(successmessage).length == 1) {
                    var single = true;
                } else {
                    var single = false;
                }
                if (showSuccess[0].success == "0") { //Error (probably question not created after n tries).
                    var error = showSuccess[0].tries;
                } else {
                    var error = '';
                }
                Templates.render('local_aiquestions/success', { success: successmessage,
                                                                wwwroot: M.cfg.wwwroot,
                                                                error: error,
                                                                single: single }).then(function(html) {
                    $("#local_aiquestions_success").html(html);
                });
                // Stop checking the state while questions are ready.
                clearInterval(intervalId);
            }
            // Show info if exists.
            if (showSuccess[0].tries !== null) {
                // If the questions are ready, show 100%.
                if (showSuccess[0].success != '') {
                    var percent = 100;
                } else {
                    var percent = Math.round((showSuccess[0].tries / showSuccess[0].numoftries) * 100);
                }
                Templates.render('local_aiquestions/info', { tries: showSuccess[0].tries,
                                                             numoftries: showSuccess[0].numoftries,
                                                             percent: percent }).then(function(html) {
                    $("#local_aiquestions_info").html(html);
                });
            }
        });
    }
});
