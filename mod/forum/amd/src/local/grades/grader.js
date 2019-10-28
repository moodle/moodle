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
 * This module will tie together all of the different calls the gradable module will make.
 *
 * @module     mod_forum/local/grades/grader
 * @package    mod_forum
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Templates from 'core/templates';
import Selectors from './local/grader/selectors';
import getUserPicker from './local/grader/user_picker';
import {createLayout as createFullScreenWindow} from 'mod_forum/local/layout/fullscreen';
import getGradingPanelFunctions from './local/grader/gradingpanel';
import {add as addToast} from 'core/toast';
import {get_string as getString} from 'core/str';
import {failedUpdate} from 'core_grades/grades/grader/gradingpanel/normalise';

const templateNames = {
    grader: {
        app: 'mod_forum/local/grades/grader',
        gradingPanel: {
            error: 'mod_forum/local/grades/local/grader/gradingpanel/error',
        },
    },
};

/**
 * Helper function that replaces the user picker placeholder with what we get back from the user picker class.
 *
 * @param {HTMLElement} root
 * @param {String} html
 */
const displayUserPicker = (root, html) => {
    const pickerRegion = root.querySelector(Selectors.regions.pickerRegion);
    Templates.replaceNodeContents(pickerRegion, html, '');
};

/**
 * To be removed, this is now done as a part of Templates.renderForPromise()
 *
 * @param {String} html
 * @param {String} js
 * @return {[*, *]}
 */
const fetchContentFromRender = (html, js) => {
    return [html, js];
};

/**
 * Here we build the function that is passed to the user picker that'll handle updating the user content area
 * of the grading interface.
 *
 * @param {HTMLElement} root
 * @param {Function} getContentForUser
 * @param {Function} getGradeForUser
 * @return {Function}
 */
const getUpdateUserContentFunction = (root, getContentForUser, getGradeForUser) => {
    return async(user) => {
        const [
            [html, js],
            userGrade,
        ] = await Promise.all([
            getContentForUser(user.id).then(fetchContentFromRender),
            getGradeForUser(user.id),
        ]);
        Templates.replaceNodeContents(root.querySelector(Selectors.regions.moduleReplace), html, js);

        const [
            gradingPanelHtml,
            gradingPanelJS
        ] = await Templates.render(userGrade.templatename, userGrade.grade).then(fetchContentFromRender);
        Templates.replaceNodeContents(root.querySelector(Selectors.regions.gradingPanel), gradingPanelHtml, gradingPanelJS);
    };
};

/**
 * Add click handlers to the buttons in the header of the grading interface.
 *
 * @param {HTMLElement} graderLayout
 * @param {Object} userPicker
 * @param {Function} saveGradeFunction
 */
const registerEventListeners = (graderLayout, userPicker, saveGradeFunction) => {
    const graderContainer = graderLayout.getContainer();
    graderContainer.addEventListener('click', (e) => {
        if (e.target.closest(Selectors.buttons.toggleFullscreen)) {
            e.stopImmediatePropagation();
            e.preventDefault();
            graderLayout.toggleFullscreen();

            return;
        }

        if (e.target.closest(Selectors.buttons.closeGrader)) {
            e.stopImmediatePropagation();
            e.preventDefault();

            graderLayout.close();

            return;
        }

        if (e.target.closest(Selectors.buttons.saveGrade)) {
            saveGradeFunction(userPicker.currentUser);
        }
    });
};

/**
 * Get the function used to save a user grade.
 *
 * @param {HTMLElement} root The container for the grader
 * @param {Function} setGradeForUser The function that will be called.
 * @return {Function}
 */
const getSaveUserGradeFunction = (root, setGradeForUser) => {
    return async(user) => {
        try {
            root.querySelector(Selectors.regions.gradingPanelErrors).innerHTML = '';
            const result = await setGradeForUser(user.id, root.querySelector(Selectors.regions.gradingPanel));
            if (result.success) {
                addToast(await getString('grades:gradesavedfor', 'mod_forum', user));
            }
            if (result.failed) {
                displayGradingError(root, user, result.error);
            }

            return result;
        } catch (err) {
            displayGradingError(root, user, err);

            return failedUpdate(err);
        }
    };
};

/**
 * Display a grading error, typically from a failed save.
 *
 * @param {HTMLElement} root The container for the grader
 * @param {Object} user The user who was errored
 * @param {Object} err The details of the error
 */
const displayGradingError = async(root, user, err) => {
    const [
        {html, js},
        errorString
    ] = await Promise.all([
        Templates.renderForPromise(templateNames.grader.gradingPanel.error, {error: err}),
        await getString('grades:gradesavefailed', 'mod_forum', {error: err.message, ...user}),
    ]);

    Templates.replaceNodeContents(root.querySelector(Selectors.regions.gradingPanelErrors), html, js);
    addToast(errorString);
};

/**
 * Launch the grader interface with the specified parameters.
 *
 * @param {Function} getListOfUsers A function to get the list of users
 * @param {Function} getContentForUser A function to get the content for a specific user
 * @param {Function} getGradeForUser A function get the grade details for a specific user
 * @param {Function} setGradeForUser A function to set the grade for a specific user
 */
export const launch = async(getListOfUsers, getContentForUser, getGradeForUser, setGradeForUser, {
    initialUserId = null, moduleName
} = {}) => {

    // We need all of these functions to be executed in series, if one step runs before another the interface
    // will not work.
    const [
        graderLayout,
        graderHTML,
        userList,
    ] = await Promise.all([
        createFullScreenWindow({fullscreen: false, showLoader: false}),
        Templates.render(templateNames.grader.app, {moduleName: moduleName}),
        getListOfUsers(),
    ]);
    const graderContainer = graderLayout.getContainer();

    const saveGradeFunction = getSaveUserGradeFunction(graderContainer, setGradeForUser);

    Templates.replaceNodeContents(graderContainer, graderHTML, '');
    const updateUserContent = getUpdateUserContentFunction(graderContainer, getContentForUser, getGradeForUser);

    // Fetch the userpicker for display.
    const userPicker = await getUserPicker(
        userList,
        updateUserContent,
        saveGradeFunction,
        {
            initialUserId,
        },
    );

    // Register all event listeners.
    registerEventListeners(graderLayout, userPicker, saveGradeFunction);

    // Display the newly created user picker.
    displayUserPicker(graderContainer, userPicker.rootNode);
};

export {getGradingPanelFunctions};
