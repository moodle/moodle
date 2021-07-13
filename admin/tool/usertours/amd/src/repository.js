/**
 * Step management code.
 *
 * @module     tool_usertours/managesteps
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
import {call as fetchMany} from 'core/ajax';
import moodleConfig from 'core/config';

/**
 * Reset the tour state of the specified tour.
 *
 * @param {number} tourid
 * @return {Promise}
 */
export const resetTourState = tourid => fetchMany([{
    methodname: 'tool_usertours_reset_tour',
    args: {
        tourid,
        context: moodleConfig.contextid,
        pageurl: window.location.href,
    }
}])[0];

/**
 * Mark the specified tour as complete.
 *
 * @param {number} stepid
 * @param {number} tourid
 * @param {number} stepindex
 * @return {Promise}
 */
export const markTourComplete = (stepid, tourid, stepindex) => fetchMany([{
    methodname: 'tool_usertours_complete_tour',
    args: {
        stepid,
        stepindex: stepindex,
        tourid,
        context: moodleConfig.contextid,
        pageurl: window.location.href,
    }
}])[0];

/**
 * Fetch the specified tour.
 *
 * @param {number} tourid
 * @return {Promise}
 */
export const fetchTour = tourid => fetchMany([{
    methodname: 'tool_usertours_fetch_and_start_tour',
    args: {
        tourid,
        context: moodleConfig.contextid,
        pageurl: window.location.href,
    }
}])[0];

/**
 * Mark the specified step as having been shown.
 *
 * @param {number} stepid
 * @param {number} tourid
 * @param {number} stepindex
 * @return {Promise}
 */
export const markStepShown = (stepid, tourid, stepindex) => fetchMany([{
    methodname: 'tool_usertours_step_shown',
    args: {
        tourid,
        stepid,
        stepindex,
        context: moodleConfig.contextid,
        pageurl: window.location.href,
    }
}])[0];
