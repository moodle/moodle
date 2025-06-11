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
 * Script to update stored_progress progress bars on the screen.
 *
 * @module     core/stored_progress
 * @copyright  2023 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */

/* global updateProgressBar */

import * as Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * @var bool This AMD script is loaded multiple times, for each progress bar on a page.
 * So this stops it running multiple times.
 * */
var STORED_PROGRESS_LOADED = false;

/**
 * Poll a given stored progress record.
 *
 * @param {array} ids
 * @param {integer} timeout
 */
function poll(ids, timeout) {

    // Call AJAX request.
    let promise = Ajax.call([{
        methodname: 'core_output_poll_stored_progress', args: {'ids': ids}
    }]);

    let repollids = [];

    // When AJAX request returns, handle the results.
    promise[0].then(function(results) {

        results.forEach(function(data) {

            // Update the progress bar percentage and message using the core method from the javascript-static.js.
            updateProgressBar(data.uniqueid, data.progress, data.message, data.estimated, data.error);

            // Add the bar for re-polling if it's not completed.
            if (data.progress < 100 && !data.error) {
                repollids.push(data.id);
            }

            // If a different timeout came back from the script, use that instead.
            if (data.timeout && data.timeout > 0) {
                timeout = data.timeout;
            }

        });

        // If we still want to poll any of them, do it again.
        if (repollids.length > 0) {
            return setTimeout(() => poll(repollids, timeout), timeout * 1000);
        }

        return false;

    }).catch(Notification.exception);

}

/**
 * Initialise the polling process.
 *
 * @param {integer} timeout Timeout to use (seconds).
 */
export const init = (timeout) => {

    if (STORED_PROGRESS_LOADED === false) {

        let ids = [];

        // Find any stored progress bars we want to poll.
        document.querySelectorAll('.stored-progress-bar').forEach(el => {

            // Get its id and add to array.
            let id = el.dataset.recordid;
            ids.push(id);

        });

        // Poll for updates from these IDs.
        poll(ids, timeout);

        // Script has run, we don't want it to run again.
        STORED_PROGRESS_LOADED = true;

    }

};