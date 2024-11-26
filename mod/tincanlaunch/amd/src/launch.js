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
 * Handles display of the launch attempt table (registrations).
 *
 * @package
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    David Pesce  <david.pesce@exputo.com>
 * @module    mod_tincanlaunch/launch
 */

import $ from 'jquery';
import * as Str from 'core/str';

let id = '';
let cid = '';
let simplifiedlaunch = false;

let SELECTORS = {
    ATTEMPT_PROGRESS: '#tincanlaunch_attemptprogress',
    ATTEMPT_TABLE: '#tincanlaunch_attempttable',
    COMPLETION_CHECK: '#tincanlaunch_completioncheck',
    EXIT: '#tincanlaunch_exit',
    LAUNCH_FORM: '#launchform',
    MAINCONTENT: '#maincontent',
    NEW_ATTEMPT: '#tincanlaunch_newattempt',
    SIMPLIFIED: '#tincanlaunch_simplified',
    SIMPLIFIED_LINK: '[id^=tincanlaunch_simplifiedlink-]',
    NEW_ATTEMPT_LINK: '[id^=tincanlaunch_newattemptlink-]',
    REATTEMPT: '[id^=tincanrelaunch_attempt-]',
    REGISTRATION: '#launchform_registration',
    STATUSDIV: '#tincanlaunch_status',
    STATUSPARA: '#tincanlaunch_status_para'
};

export const init = (courseid) => {

    // Retrieve id and n URL parameters
    let urlparams = new URLSearchParams(window.location.search);
    id = urlparams.get('id');
    cid = courseid;

    // This is a simplified navigation launch
    if ($(SELECTORS.SIMPLIFIED).length) {
        simplifiedlaunch = true;
        let simplifiedid = $(SELECTORS.SIMPLIFIED_LINK).attr('id').substring(28);
        launchExperience(simplifiedid);
    } else {
        // Iterate over existing registrations and add necessary values.
        $(SELECTORS.REATTEMPT).each(function() {
            let registrationid = $(this).attr('id').substring(23);

            // Listen for keyUp event.
            $(this).on("keyup", function(e) {
                keyTest(e.key, registrationid);
            });

            // Listen for click event.
            $(this).on("click", function() {
                launchExperience(registrationid);
            });

            // Add tabindex and cursor.
            $(this).attr('tabindex', '0');
            $(this).attr('class', 'btn btn-primary');
        });

        // Add details to new attempt link.
        let newregistrationid = $(SELECTORS.NEW_ATTEMPT_LINK).attr('id').substring(28);
        $(SELECTORS.NEW_ATTEMPT_LINK).attr('tabindex', '0');

        $(SELECTORS.NEW_ATTEMPT_LINK).on("click", function() {
            launchExperience(newregistrationid);
        });

        $(SELECTORS.NEW_ATTEMPT_LINK).on("keyup", function(e) {
            keyTest(e.key, newregistrationid);
        });
    }

    // Periodically check completion
    setInterval(function() {
        $(SELECTORS.COMPLETION_CHECK).load('completion_check.php?id=' + id);
    }, 30000); // TODO: make this interval a configuration setting.
};

const keyTest = (keycode, registrationid) => {
    if (keycode === 'Enter' || keycode === ' ') {
        launchExperience(registrationid);
    }
};

const launchExperience = (registrationid) => {
    // Add status para.
    let statuspara = $("<p></p>").attr("id", "tincanlaunch_status_para");

    // Add completion span.
    let completionspan = $("<span>").attr("id", "tincanlaunch_completioncheck");
    $(SELECTORS.STATUSDIV).append(statuspara, completionspan);

    const spawnedWindow = window.open('launch.php?launchform_registration=' + registrationid + '&id=' + id);

    // Check every second to see if the spawned window was closed.
    const checkWindow = setInterval(() => {
        if (spawnedWindow.closed) {
            window.console.log('xAPI content window was closed.');
            clearInterval(checkWindow); // Stop checking for window closure

            // Perform a final completion check.
            $(SELECTORS.COMPLETION_CHECK).load('completion_check.php?id=' + id);

            // Redirect to the course page.
            if (simplifiedlaunch) {
                window.location.href = "/course/view.php?id=" + cid;
            } else {
                window.location.href = "view.php?id=" + id;
            }
        }
    }, 1000);

    let stringsToRetrieve = [
        {
            key: 'tincanlaunch_progress',
            component: 'tincanlaunch'
        },
        {
            key: 'returntocourse',
            component: 'tincanlaunch'
        },
        {
            key: 'returntoregistrations',
            component: 'tincanlaunch'
        }
    ];

    $(SELECTORS.NEW_ATTEMPT).remove();
    $(SELECTORS.ATTEMPT_TABLE).remove();

    Str.get_strings(stringsToRetrieve)
        .done(function(s) {
            // Attempt in progress.
            $(SELECTORS.STATUSPARA).text(s[0]);

            // Return to course or registrations table.
            let exitpara = $("<p></p>").attr("id", SELECTORS.EXIT);
            if (simplifiedlaunch) {
                exitpara.html("<a href='/course/view.php?id=" + cid + "'>" + s[1] + "</a>");
            } else {
                exitpara.html("<a href='/course/view.php?id=" + cid + "'>" + s[2] + "</a>");
            }

            $(SELECTORS.STATUSPARA).after(exitpara);
    });
};