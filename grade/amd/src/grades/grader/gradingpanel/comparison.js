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
 * Compare a given form's values and its previously set data attributes.
 *
 * @module     core_grades/grades/grader/gradingpanel/comparison
 * @package    core_grades
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const fillInitialValues = (form) => {
    Array.prototype.forEach.call(form.elements, (input) => {
        if (input.type === 'submit' || input.type === 'button') {
            return;
        } else if (input.type === 'radio' || input.type === 'checkbox') {
            input.dataset.initialValue = JSON.stringify(input.checked);
        } else if (typeof input.value !== 'undefined') {
            input.dataset.initialValue = JSON.stringify(input.value);
        } else if (input.type === 'select-one') {
            Array.prototype.forEach.call(input.options, (option) => {
                option.dataset.initialValue = JSON.stringify(option.selected);
            });
        }
   });
};


/**
 * Compare the form data with the initial form data from when the form was set up.
 *
 * If values have changed, return a truthy value.
 *
 * @param {HTMLElement} form
 * @return {Boolean}
 */
export const compareData = (form) => {
    const result = Array.prototype.some.call(form.elements, (input) => {
        if (input.type === 'submit' || input.type === 'button') {
            return false;
        } else if (input.type === 'radio' || input.type === 'checkbox') {
            if (typeof input.dataset.initialValue !== 'undefined') {
                return input.dataset.initialValue !== JSON.stringify(input.checked);
            }
        } else if (typeof input.value !== 'undefined') {
            if (typeof input.dataset.initialValue !== 'undefined') {
                return input.dataset.initialValue !== JSON.stringify(input.value);
            }
        } else if (input.type === 'select-one') {
            return Array.prototype.some.call(input.options, (option) => {
                if (typeof option.dataset.initialValue !== 'undefined') {
                    return option.dataset.initialValue !== JSON.stringify(option.selected);
                }

                return false;
            });
        }

        // No value found to check. Assume that there were changes.
        return true;
    });

    // Fill the initial values again as the form may not be reloaded.
    fillInitialValues(form);

    return result;
};
