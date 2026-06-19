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
 * Define all of the selectors we will be using on the AI Course assistant.
 *
 * @module     aiplacement_courseassist/selectors
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const courseAssistControls = '.course-assist-controls';

export default {
    ELEMENTS: {
        AIDRAWER: '#ai-drawer',
        AIDRAWER_BODY: '#ai-drawer .ai-drawer-body',
        PAGE: '#page',
        MAIN_REGION: '[role="main"]',
        AIDRAWER_CLOSE: '#ai-drawer-close',
        RESPONSE: '.course-assist-response',
        COURSE_ASSIST_CONTROLS: courseAssistControls,
        JUMPTO: `${courseAssistControls} [data-region="jumpto"]`,
        ACTION: `${courseAssistControls} [data-input-type="action"]`,
    },
    ACTIONS: {
        SUMMARY: `${courseAssistControls} [data-action="summarise_text"]`,
        EXPLAIN: `${courseAssistControls} [data-action="explain_text"]`,
        RETRY: `${courseAssistControls} [data-action="retry"]`,
        DECLINE: '.ai-policy-block [data-action="decline"]',
        ACCEPT: '.ai-policy-block [data-action="accept"]',
        REGENERATE: `${courseAssistControls} [data-action="regenerate"]`,
        CANCEL: `${courseAssistControls} [data-action="cancel"]`,
    }
};
