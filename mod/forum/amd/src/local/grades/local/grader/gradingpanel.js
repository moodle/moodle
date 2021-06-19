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
 * Grading panel functions.
 *
 * @module     mod_forum/local/grades/local/grader/gradingpanel
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get the grade panel setter and getter for the current component.
 * This function dynamically pulls the relevant gradingpanel JS file defined in the grading method.
 * We do this because we do not know until execution time what the grading type is and we do not want to import unused files.
 *
 * @method
 * @param {String} component The component being graded
 * @param {Number} context The contextid of the thing being graded
 * @param {String} gradingComponent The thing providing the grading type
 * @param {String} gradingSubtype The subtype fo the grading component
 * @param {String} itemName The name of the thing being graded
 * @return {Object}
 */
export default async(component, context, gradingComponent, gradingSubtype, itemName) => {
    let gradingMethodHandler = `${gradingComponent}/grades/grader/gradingpanel`;
    if (gradingSubtype) {
        gradingMethodHandler += `/${gradingSubtype}`;
    }

    const GradingMethod = await import(gradingMethodHandler);

    return {
        getter: (userId) => GradingMethod.fetchCurrentGrade(component, context, itemName, userId),
        setter: (userId, notifyStudent, formData) => GradingMethod.storeCurrentGrade(
            component, context, itemName, userId, notifyStudent, formData),
    };
};

