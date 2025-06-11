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
 * Backward compatibility file for the old popover.js
 *
 * @module     qtype_multianswer/feedback
 * @copyright  2023 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import 'theme_boost/popover';
import $ from 'jquery';

/** @property {object} Contains the list of selectors for this module. */
const SELECTORS = {
    FEEDBACK_TRIGGER: '.feedbacktrigger[data-toggle="popover"]',
};

/** @property {boolean} Flag to indicate whether the feedback popovers have been already initialised. */
let feedbackInitialised = false;

/**
 * Function to initialise the feedback popovers.
 */
const initPopovers = () => {
    if (!feedbackInitialised) {
        $(SELECTORS.FEEDBACK_TRIGGER).popover();

        document.addEventListener('click', (e) => {
            if (e.target.closest(SELECTORS.FEEDBACK_TRIGGER)) {
                e.preventDefault();
            }
        });
        feedbackInitialised = true;
    }
};

export default {
    initPopovers: initPopovers,
};
