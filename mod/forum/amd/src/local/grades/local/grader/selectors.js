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
 * Define all of the selectors we will be using on the grading interface.
 *
 * @module     mod_forum/local/grades/local/grader/selectors
 * @package    mod_forum
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const getDataSelector = (name, value) => {
    return `[data-${name}="${value}"]`;
};

export default {
    buttons: {
        toggleFullscreen: getDataSelector('action', 'togglefullscreen'),
        closeGrader: getDataSelector('action', 'closegrader'),
    },
    regions: {
        moduleReplace: getDataSelector('region', 'module_content'),
        pickerRegion: getDataSelector('region', 'user_picker'),
    },
};

