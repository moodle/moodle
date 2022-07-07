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
 * Define all of the selectors we will be using within MoodleNet plugin.
 *
 * @module     tool_moodlenet/selectors
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    return {
        action: {
            browse: '[data-action="browse"]',
            submit: '[data-action="submit"]',
            showMoodleNet: '[data-action="show-moodlenet"]',
            closeOption: '[data-action="close-chooser-option-summary"]',
        },
        region: {
            clearIcon: '[data-region="clear-icon"]',
            courses: '[data-region="mnet-courses"]',
            instancePage: '[data-region="moodle-net"]',
            searchInput: '[data-region="search-input"]',
            searchIcon: '[data-region="search-icon"]',
            selectPage: '[data-region="moodle-net-select"]',
            spinner: '[data-region="spinner"]',
            validationArea: '[data-region="validation-area"]',
            carousel: '[data-region="carousel"]',
            moodleNet: '[data-region="pluginCarousel"]',
        },
    };
});
