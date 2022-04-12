/**
 * User tour control library.
 *
 * @module     tool_usertours/usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
import BootstrapTour from './tour';
import Templates from 'core/templates';
import log from 'core/log';
import notification from 'core/notification';
import * as tourRepository from './repository';
import Pending from 'core/pending';
import {eventTypes} from './events';

let currentTour = null;
let tourId = null;

/**
 * Find the first matching tour.
 *
 * @param {object[]} tourDetails
 * @param {object[]} filters
 * @returns {null|object}
 */
const findMatchingTour = (tourDetails, filters) => {
    return tourDetails.find(tour => filters.some(filter => {
        if (filter && filter.filterMatches) {
            return filter.filterMatches(tour);
        }

        return true;
    }));
};

/**
 * Initialise the user tour for the current page.
 *
 * @method  init
 * @param   {Array}    tourDetails      The matching tours for this page.
 * @param   {Array}    filters          The names of all client side filters.
 */
export const init = async(tourDetails, filters) => {
    const requirements = [];
    filters.forEach(filter => {
        requirements.push(import(`tool_usertours/filter_${filter}`));
    });

    const filterPlugins = await Promise.all(requirements);

    const matchingTour = findMatchingTour(tourDetails, filterPlugins);
    if (!matchingTour) {
        return;
    }

    // Only one tour per page is allowed.
    tourId = matchingTour.tourId;

    let startTour = matchingTour.startTour;
    if (typeof startTour === 'undefined') {
        startTour = true;
    }

    if (startTour) {
        // Fetch the tour configuration.
        fetchTour(tourId);
    }

    addResetLink();

    // Watch for the reset link.
    document.querySelector('body').addEventListener('click', e => {
        const resetLink = e.target.closest('#resetpagetour');
        if (resetLink) {
            e.preventDefault();
            resetTourState(tourId);
        }
    });
};

/**
 * Fetch the configuration specified tour, and start the tour when it has been fetched.
 *
 * @method  fetchTour
 * @param   {Number}    tourId      The ID of the tour to start.
 */
const fetchTour = async tourId => {
    const pendingPromise = new Pending(`admin_usertour_fetchTour:${tourId}`);

    try {
        // If we don't have any tour config (because it doesn't need showing for the current user), return early.
        const response = await tourRepository.fetchTour(tourId);
        if (response.hasOwnProperty('tourconfig')) {
            const {html} = await Templates.renderForPromise('tool_usertours/tourstep', response.tourconfig);
            startBootstrapTour(tourId, html, response.tourconfig);
        }
        pendingPromise.resolve();
    } catch (error) {
        pendingPromise.resolve();
        notification.exception(error);
    }
};

const getPreferredResetLocation = () => {
    let location = document.querySelector('.tool_usertours-resettourcontainer');
    if (location) {
        return location;
    }

    location = document.querySelector('.logininfo');
    if (location) {
        return location;
    }

    location = document.querySelector('footer');
    if (location) {
        return location;
    }

    return document.body;
};

/**
 * Add a reset link to the page.
 *
 * @method  addResetLink
 */
const addResetLink = () => {
    const pendingPromise = new Pending('admin_usertour_addResetLink');

    Templates.render('tool_usertours/resettour', {})
    .then(function(html, js) {
        // Append the link to the most suitable place on the page with fallback to legacy selectors and finally the body if
        // there is no better place.
        Templates.appendNodeContents(getPreferredResetLocation(), html, js);

        return;
    })
    .catch()
    .then(pendingPromise.resolve)
    .catch();
};

/**
 * Start the specified tour.
 *
 * @method  startBootstrapTour
 * @param   {Number}    tourId      The ID of the tour to start.
 * @param   {String}    template    The template to use.
 * @param   {Object}    tourConfig  The tour configuration.
 * @return  {Object}
 */
const startBootstrapTour = (tourId, template, tourConfig) => {
    if (currentTour && currentTour.tourRunning) {
        // End the current tour.
        currentTour.endTour();
        currentTour = null;
    }

    document.addEventListener(eventTypes.tourEnded, markTourComplete);
    document.addEventListener(eventTypes.stepRenderer, markStepShown);

    // Sort out the tour name.
    tourConfig.tourName = tourConfig.name;
    delete tourConfig.name;

    // Add the template to the configuration.
    // This enables translations of the buttons.
    tourConfig.template = template;

    tourConfig.steps = tourConfig.steps.map(function(step) {
        if (typeof step.element !== 'undefined') {
            step.target = step.element;
            delete step.element;
        }

        if (typeof step.reflex !== 'undefined') {
            step.moveOnClick = !!step.reflex;
            delete step.reflex;
        }

        if (typeof step.content !== 'undefined') {
            step.body = step.content;
            delete step.content;
        }

        return step;
    });

    currentTour = new BootstrapTour(tourConfig);
    return currentTour.startTour();
};

/**
 * Mark the specified step as being shownd by the user.
 *
 * @method  markStepShown
 * @param   {Event} e
 */
const markStepShown = e => {
    const tour = e.detail.tour;
    const stepConfig = tour.getStepConfig(tour.getCurrentStepNumber());
    tourRepository.markStepShown(
        stepConfig.stepid,
        tourId,
        tour.getCurrentStepNumber()
    ).catch(log.error);
};

/**
 * Mark the specified tour as being completed by the user.
 *
 * @method  markTourComplete
 * @param   {Event} e
 * @listens tool_usertours/stepRendered
 */
const markTourComplete = e => {
    document.removeEventListener(eventTypes.tourEnded, markTourComplete);
    document.removeEventListener(eventTypes.stepRenderer, markStepShown);

    const tour = e.detail.tour;
    const stepConfig = tour.getStepConfig(tour.getCurrentStepNumber());
    tourRepository.markTourComplete(
        stepConfig.stepid,
        tourId,
        tour.getCurrentStepNumber()
    ).catch(log.error);
};

/**
 * Reset the state, and restart the the tour on the current page.
 *
 * @method  resetTourState
 * @param   {Number}    tourId      The ID of the tour to start.
 * @returns {Promise}
 */
export const resetTourState = tourId => tourRepository.resetTourState(tourId)
.then(response => {
    if (response.startTour) {
        fetchTour(response.startTour);
    }
    return;
}).catch(notification.exception);
