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
export default {
    ELEMENTS: {
        AIDRAWER: '#ai-drawer',
        AIDRAWER_BODY: '#ai-drawer .ai-drawer-body',
        PAGE: '#page',
        MAIN_REGION: '[role="main"]',
    },
    ACTIONS: {
        SUMMARY: '[data-action="course-summarise"]',
        RETRY: '[data-action="course-summarise-retry"]',
        DECLINE: '.ai-policy-block [data-action="decline"]',
        ACCEPT: '.ai-policy-block [data-action="accept"]',
        REGENERATE: '[data-action="course-summarise-regenerate"]',
        CANCEL: '[data-action="course-summarise-cancel"]',
    }
};
