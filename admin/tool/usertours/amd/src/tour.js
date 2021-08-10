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
 * Manage user tours in Moodle.
 *
 * @module tool_usertours/tour
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as Aria from 'core/aria';
import Popper from 'core/popper';

/**
 * A Tour.
 *
 * @class
 */
export default class Tour {
    /**
     * @param   {object}    config  The configuration object.
     */
    constructor(config) {
        this.init(config);
    }

    /**
     * Initialise the tour.
     *
     * @method  init
     * @param   {Object}    config  The configuration object.
     * @chainable
     * @return {Object} this.
     */
    init(config) {
        // Unset all handlers.
        this.eventHandlers = {};

        // Reset the current tour states.
        this.reset();

        // Store the initial configuration.
        this.originalConfiguration = config || {};

        // Apply configuration.
        this.configure.apply(this, arguments);

        try {
            this.storage = window.sessionStorage;
            this.storageKey = 'tourstate_' + this.tourName;
        } catch (e) {
            this.storage = false;
            this.storageKey = '';
        }

        return this;
    }

    /**
     * Reset the current tour state.
     *
     * @method  reset
     * @chainable
     * @return {Object} this.
     */
    reset() {
        // Hide the current step.
        this.hide();

        // Unset all handlers.
        this.eventHandlers = [];

        // Unset all listeners.
        this.resetStepListeners();

        // Unset the original configuration.
        this.originalConfiguration = {};

        // Reset the current step number and list of steps.
        this.steps = [];

        // Reset the current step number.
        this.currentStepNumber = 0;

        return this;
    }

    /**
     * Prepare tour configuration.
     *
     * @method  configure
     * @param {Object} config The configuration object.
     * @chainable
     * @return {Object} this.
     */
    configure(config) {
        if (typeof config === 'object') {
            // Tour name.
            if (typeof config.tourName !== 'undefined') {
                this.tourName = config.tourName;
            }

            // Set up eventHandlers.
            if (config.eventHandlers) {
                for (let eventName in config.eventHandlers) {
                    config.eventHandlers[eventName].forEach(function(handler) {
                        this.addEventHandler(eventName, handler);
                    }, this);
                }
            }

            // Reset the step configuration.
            this.resetStepDefaults(true);

            // Configure the steps.
            if (typeof config.steps === 'object') {
                this.steps = config.steps;
            }

            if (typeof config.template !== 'undefined') {
                this.templateContent = config.template;
            }
        }

        // Check that we have enough to start the tour.
        this.checkMinimumRequirements();

        return this;
    }

    /**
     * Check that the configuration meets the minimum requirements.
     *
     * @method  checkMinimumRequirements
     */
    checkMinimumRequirements() {
        // Need a tourName.
        if (!this.tourName) {
            throw new Error("Tour Name required");
        }

        // Need a minimum of one step.
        if (!this.steps || !this.steps.length) {
            throw new Error("Steps must be specified");
        }
    }

    /**
     * Reset step default configuration.
     *
     * @method  resetStepDefaults
     * @param   {Boolean}   loadOriginalConfiguration   Whether to load the original configuration supplied with the Tour.
     * @chainable
     * @return {Object} this.
     */
    resetStepDefaults(loadOriginalConfiguration) {
        if (typeof loadOriginalConfiguration === 'undefined') {
            loadOriginalConfiguration = true;
        }

        this.stepDefaults = {};
        if (!loadOriginalConfiguration || typeof this.originalConfiguration.stepDefaults === 'undefined') {
            this.setStepDefaults({});
        } else {
            this.setStepDefaults(this.originalConfiguration.stepDefaults);
        }

        return this;
    }

    /**
     * Set the step defaults.
     *
     * @method  setStepDefaults
     * @param   {Object}    stepDefaults                The step defaults to apply to all steps
     * @chainable
     * @return {Object} this.
     */
    setStepDefaults(stepDefaults) {
        if (!this.stepDefaults) {
            this.stepDefaults = {};
        }
        $.extend(
            this.stepDefaults,
            {
                element:        '',
                placement:      'top',
                delay:          0,
                moveOnClick:    false,
                moveAfterTime:  0,
                orphan:         false,
                direction:      1,
            },
            stepDefaults
        );

        return this;
    }

    /**
     * Retrieve the current step number.
     *
     * @method  getCurrentStepNumber
     * @return  {Integer}                   The current step number
     */
    getCurrentStepNumber() {
        return parseInt(this.currentStepNumber, 10);
    }

    /**
     * Store the current step number.
     *
     * @method  setCurrentStepNumber
     * @param   {Integer}   stepNumber      The current step number
     * @chainable
     */
    setCurrentStepNumber(stepNumber) {
        this.currentStepNumber = stepNumber;
        if (this.storage) {
            try {
                this.storage.setItem(this.storageKey, stepNumber);
            } catch (e) {
                if (e.code === DOMException.QUOTA_EXCEEDED_ERR) {
                    this.storage.removeItem(this.storageKey);
                }
            }
        }
    }

    /**
     * Get the next step number after the currently displayed step.
     *
     * @method  getNextStepNumber
     * @param   {Integer}   stepNumber      The current step number
     * @return  {Integer}    The next step number to display
     */
    getNextStepNumber(stepNumber) {
        if (typeof stepNumber === 'undefined') {
            stepNumber = this.getCurrentStepNumber();
        }
        let nextStepNumber = stepNumber + 1;

        // Keep checking the remaining steps.
        while (nextStepNumber <= this.steps.length) {
            if (this.isStepPotentiallyVisible(this.getStepConfig(nextStepNumber))) {
                return nextStepNumber;
            }
            nextStepNumber++;
        }

        return null;
    }

    /**
     * Get the previous step number before the currently displayed step.
     *
     * @method  getPreviousStepNumber
     * @param   {Integer}   stepNumber      The current step number
     * @return  {Integer}    The previous step number to display
     */
    getPreviousStepNumber(stepNumber) {
        if (typeof stepNumber === 'undefined') {
            stepNumber = this.getCurrentStepNumber();
        }
        let previousStepNumber = stepNumber - 1;

        // Keep checking the remaining steps.
        while (previousStepNumber >= 0) {
            if (this.isStepPotentiallyVisible(this.getStepConfig(previousStepNumber))) {
                return previousStepNumber;
            }
            previousStepNumber--;
        }

        return null;
    }

    /**
     * Is the step the final step number?
     *
     * @method  isLastStep
     * @param   {Integer}   stepNumber  Step number to test
     * @return  {Boolean}               Whether the step is the final step
     */
    isLastStep(stepNumber) {
        let nextStepNumber = this.getNextStepNumber(stepNumber);

        return nextStepNumber === null;
    }

    /**
     * Is the step the first step number?
     *
     * @method  isFirstStep
     * @param   {Integer}   stepNumber  Step number to test
     * @return  {Boolean}               Whether the step is the first step
     */
    isFirstStep(stepNumber) {
        let previousStepNumber = this.getPreviousStepNumber(stepNumber);

        return previousStepNumber === null;
    }

    /**
     * Is this step potentially visible?
     *
     * @method  isStepPotentiallyVisible
     * @param   {Object}    stepConfig      The step configuration to normalise
     * @return  {Boolean}               Whether the step is the potentially visible
     */
    isStepPotentiallyVisible(stepConfig) {
        if (!stepConfig) {
            // Without step config, there can be no step.
            return false;
        }

        if (this.isStepActuallyVisible(stepConfig)) {
            // If it is actually visible, it is already potentially visible.
            return true;
        }

        if (typeof stepConfig.orphan !== 'undefined' && stepConfig.orphan) {
            // Orphan steps have no target. They are always visible.
            return true;
        }

        if (typeof stepConfig.delay !== 'undefined' && stepConfig.delay) {
            // Only return true if the activated has not been used yet.
            return true;
        }

        // Not theoretically, or actually visible.
        return false;
    }

    /**
     * Is this step actually visible?
     *
     * @method  isStepActuallyVisible
     * @param   {Object}    stepConfig      The step configuration to normalise
     * @return  {Boolean}               Whether the step is actually visible
     */
    isStepActuallyVisible(stepConfig) {
        if (!stepConfig) {
            // Without step config, there can be no step.
            return false;
        }

        let target = this.getStepTarget(stepConfig);
        if (target && target.length && target.is(':visible')) {
            // Without a target, there can be no step.
            return !!target.length;
        }

        return false;
    }

    /**
     * Go to the next step in the tour.
     *
     * @method  next
     * @chainable
     * @return {Object} this.
     */
    next() {
        return this.gotoStep(this.getNextStepNumber());
    }

    /**
     * Go to the previous step in the tour.
     *
     * @method  previous
     * @chainable
     * @return {Object} this.
     */
    previous() {
        return this.gotoStep(this.getPreviousStepNumber(), -1);
    }

    /**
     * Go to the specified step in the tour.
     *
     * @method  gotoStep
     * @param   {Integer}   stepNumber     The step number to display
     * @param   {Integer}   direction      Next or previous step
     * @chainable
     * @return {Object} this.
     */
    gotoStep(stepNumber, direction) {
        if (stepNumber < 0) {
            return this.endTour();
        }

        let stepConfig = this.getStepConfig(stepNumber);
        if (stepConfig === null) {
            return this.endTour();
        }

        return this._gotoStep(stepConfig, direction);
    }

    _gotoStep(stepConfig, direction) {
        if (!stepConfig) {
            return this.endTour();
        }

        if (typeof stepConfig.delay !== 'undefined' && stepConfig.delay && !stepConfig.delayed) {
            stepConfig.delayed = true;
            window.setTimeout(this._gotoStep.bind(this), stepConfig.delay, stepConfig, direction);

            return this;
        } else if (!stepConfig.orphan && !this.isStepActuallyVisible(stepConfig)) {
            let fn = direction == -1 ? 'getPreviousStepNumber' : 'getNextStepNumber';
            return this.gotoStep(this[fn](stepConfig.stepNumber), direction);
        }

        this.hide();

        this.fireEventHandlers('beforeRender', stepConfig);
        this.renderStep(stepConfig);
        this.fireEventHandlers('afterRender', stepConfig);

        return this;
    }

    /**
     * Fetch the normalised step configuration for the specified step number.
     *
     * @method  getStepConfig
     * @param   {Integer}   stepNumber      The step number to fetch configuration for
     * @return  {Object}                    The step configuration
     */
    getStepConfig(stepNumber) {
        if (stepNumber === null || stepNumber < 0 || stepNumber >= this.steps.length) {
            return null;
        }

        // Normalise the step configuration.
        let stepConfig = this.normalizeStepConfig(this.steps[stepNumber]);

        // Add the stepNumber to the stepConfig.
        stepConfig = $.extend(stepConfig, {stepNumber: stepNumber});

        return stepConfig;
    }

    /**
     * Normalise the supplied step configuration.
     *
     * @method  normalizeStepConfig
     * @param   {Object}    stepConfig      The step configuration to normalise
     * @return  {Object}                    The normalised step configuration
     */
    normalizeStepConfig(stepConfig) {

        if (typeof stepConfig.reflex !== 'undefined' && typeof stepConfig.moveAfterClick === 'undefined') {
            stepConfig.moveAfterClick = stepConfig.reflex;
        }

        if (typeof stepConfig.element !== 'undefined' && typeof stepConfig.target === 'undefined') {
            stepConfig.target = stepConfig.element;
        }

        if (typeof stepConfig.content !== 'undefined' && typeof stepConfig.body === 'undefined') {
            stepConfig.body = stepConfig.content;
        }

        stepConfig = $.extend({}, this.stepDefaults, stepConfig);

        stepConfig = $.extend({}, {
            attachTo: stepConfig.target,
            attachPoint: 'after',
        }, stepConfig);

        if (stepConfig.attachTo) {
            stepConfig.attachTo = $(stepConfig.attachTo).first();
        }

        return stepConfig;
    }

    /**
     * Fetch the actual step target from the selector.
     *
     * This should not be called until after any delay has completed.
     *
     * @method  getStepTarget
     * @param   {Object}    stepConfig      The step configuration
     * @return  {$}
     */
    getStepTarget(stepConfig) {
        if (stepConfig.target) {
            return $(stepConfig.target);
        }

        return null;
    }

    /**
     * Fire any event handlers for the specified event.
     *
     * @param   {String}    eventName       The name of the event to handle
     * @param   {Object}    data            Any data to pass to the event
     * @chainable
     * @return {Object} this.
     */
    fireEventHandlers(eventName, data) {
        if (typeof this.eventHandlers[eventName] === 'undefined') {
            return this;
        }

        this.eventHandlers[eventName].forEach(function(thisEvent) {
            thisEvent.call(this, data);
        }, this);

        return this;
    }

    /**
     * @method addEventHandler
     * @param  {string}      eventName       The name of the event to listen for
     * @param  {function}    handler         The event handler to call
     * @return {Object} this.
     */
    addEventHandler(eventName, handler) {
        if (typeof this.eventHandlers[eventName] === 'undefined') {
            this.eventHandlers[eventName] = [];
        }

        this.eventHandlers[eventName].push(handler);

        return this;
    }

    /**
     * Process listeners for the step being shown.
     *
     * @method  processStepListeners
     * @param   {object}    stepConfig      The configuration for the step
     * @chainable
     * @return {Object} this.
     */
    processStepListeners(stepConfig) {
        this.listeners.push(
        // Next/Previous buttons.
        {
            node: this.currentStepNode,
            args: ['click', '[data-role="next"]', $.proxy(this.next, this)]
        }, {
            node: this.currentStepNode,
            args: ['click', '[data-role="previous"]', $.proxy(this.previous, this)]
        },

        // Close and end tour buttons.
        {
            node: this.currentStepNode,
            args: ['click', '[data-role="end"]', $.proxy(this.endTour, this)]
        },

        // Click backdrop and hide tour.
        {
            node: $('[data-flexitour="backdrop"]'),
            args: ['click', $.proxy(this.hide, this)]
        },

        // Keypresses.
        {
            node: $('body'),
            args: ['keydown', $.proxy(this.handleKeyDown, this)]
        });

        if (stepConfig.moveOnClick) {
            var targetNode = this.getStepTarget(stepConfig);
            this.listeners.push({
                node: targetNode,
                args: ['click', $.proxy(function(e) {
                    if ($(e.target).parents('[data-flexitour="container"]').length === 0) {
                        // Ignore clicks when they are in the flexitour.
                        window.setTimeout($.proxy(this.next, this), 500);
                    }
                }, this)]
            });
        }

        this.listeners.forEach(function(listener) {
            listener.node.on.apply(listener.node, listener.args);
        });

        return this;
    }

    /**
     * Reset step listeners.
     *
     * @method  resetStepListeners
     * @chainable
     * @return {Object} this.
     */
    resetStepListeners() {
        // Stop listening to all external handlers.
        if (this.listeners) {
            this.listeners.forEach(function(listener) {
                listener.node.off.apply(listener.node, listener.args);
            });
        }
        this.listeners = [];

        return this;
    }

    /**
     * The standard step renderer.
     *
     * @method  renderStep
     * @param   {Object}    stepConfig      The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    renderStep(stepConfig) {
        // Store the current step configuration for later.
        this.currentStepConfig = stepConfig;
        this.setCurrentStepNumber(stepConfig.stepNumber);

        // Fetch the template and convert it to a $ object.
        let template = $(this.getTemplateContent());

        // Title.
        template.find('[data-placeholder="title"]')
            .html(stepConfig.title);

        // Body.
        template.find('[data-placeholder="body"]')
            .html(stepConfig.body);

        // Is this the first step?
        if (this.isFirstStep(stepConfig.stepNumber)) {
            template.find('[data-role="previous"]').hide();
        } else {
            template.find('[data-role="previous"]').prop('disabled', false);
        }

        // Is this the final step?
        if (this.isLastStep(stepConfig.stepNumber)) {
            template.find('[data-role="next"]').hide();
            template.find('[data-role="end"]').removeClass("btn-secondary").addClass("btn-primary");
        } else {
            template.find('[data-role="next"]').prop('disabled', false);
        }

        template.find('[data-role="previous"]').attr('role', 'button');
        template.find('[data-role="next"]').attr('role', 'button');
        template.find('[data-role="end"]').attr('role', 'button');

        // Replace the template with the updated version.
        stepConfig.template = template;

        // Add to the page.
        this.addStepToPage(stepConfig);

        // Process step listeners after adding to the page.
        // This uses the currentNode.
        this.processStepListeners(stepConfig);

        return this;
    }

    /**
     * Getter for the template content.
     *
     * @method  getTemplateContent
     * @return  {$}
     */
    getTemplateContent() {
        return $(this.templateContent).clone();
    }

    /**
     * Helper to add a step to the page.
     *
     * @method  addStepToPage
     * @param   {Object}    stepConfig      The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    addStepToPage(stepConfig) {
        // Create the stepNode from the template data.
        let currentStepNode = $('<span data-flexitour="container"></span>')
            .html(stepConfig.template)
            .hide();

        // The scroll animation occurs on the body or html.
        let animationTarget = $('body, html')
            .stop(true, true);

        if (this.isStepActuallyVisible(stepConfig)) {
            let targetNode = this.getStepTarget(stepConfig);

            targetNode.data('flexitour', 'target');

            let zIndex = this.calculateZIndex(targetNode);
            if (zIndex) {
                stepConfig.zIndex = zIndex + 1;
            }

            if (stepConfig.zIndex) {
                currentStepNode.css('zIndex', stepConfig.zIndex + 1);
            }

            // Add the backdrop.
            this.positionBackdrop(stepConfig);

            $(document.body).append(currentStepNode);
            this.currentStepNode = currentStepNode;

            // Ensure that the step node is positioned.
            // Some situations mean that the value is not properly calculated without this step.
            this.currentStepNode.css({
                top: 0,
                left: 0,
            });

            animationTarget
                .animate({
                    scrollTop: this.calculateScrollTop(stepConfig),
                }).promise().then(function() {
                        this.positionStep(stepConfig);
                        this.revealStep(stepConfig);
                        return;
                    }.bind(this))
                    .catch(function() {
                        // Silently fail.
                    });

        } else if (stepConfig.orphan) {
            stepConfig.isOrphan = true;

            // This will be appended to the body instead.
            stepConfig.attachTo = $('body').first();
            stepConfig.attachPoint = 'append';

            // Add the backdrop.
            this.positionBackdrop(stepConfig);

            // This is an orphaned step.
            currentStepNode.addClass('orphan');

            // It lives in the body.
            $(document.body).append(currentStepNode);
            this.currentStepNode = currentStepNode;

            this.currentStepNode.offset(this.calculateStepPositionInPage());
            this.currentStepNode.css('position', 'fixed');

            this.currentStepPopper = new Popper(
                $('body'),
                this.currentStepNode[0], {
                    removeOnDestroy: true,
                    placement: stepConfig.placement + '-start',
                    arrowElement: '[data-role="arrow"]',
                    // Empty the modifiers. We've already placed the step and don't want it moved.
                    modifiers: {
                        hide: {
                            enabled: false,
                        },
                        applyStyle: {
                            onLoad: null,
                            enabled: false,
                        },
                    }
                }
            );

            this.revealStep(stepConfig);
        }

        return this;
    }

    /**
     * Make the given step visible.
     *
     * @method revealStep
     * @param {Object} stepConfig The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    revealStep(stepConfig) {
        // Fade the step in.
        this.currentStepNode.fadeIn('', $.proxy(function() {
                // Announce via ARIA.
                this.announceStep(stepConfig);

                // Focus on the current step Node.
                this.currentStepNode.focus();
                window.setTimeout($.proxy(function() {
                    // After a brief delay, focus again.
                    // There seems to be an issue with Jaws where it only reads the dialogue title initially.
                    // This second focus helps it to read the full dialogue.
                    if (this.currentStepNode) {
                        this.currentStepNode.focus();
                    }
                }, this), 100);

            }, this));

        return this;
    }

    /**
     * Helper to announce the step on the page.
     *
     * @method  announceStep
     * @param   {Object}    stepConfig      The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    announceStep(stepConfig) {
        // Setup the step Dialogue as per:
        // * https://www.w3.org/TR/wai-aria-practices/#dialog_nonmodal
        // * https://www.w3.org/TR/wai-aria-practices/#dialog_modal

        // Generate an ID for the current step node.
        let stepId = 'tour-step-' + this.tourName + '-' + stepConfig.stepNumber;
        this.currentStepNode.attr('id', stepId);

        let bodyRegion = this.currentStepNode.find('[data-placeholder="body"]').first();
        bodyRegion.attr('id', stepId + '-body');
        bodyRegion.attr('role', 'document');

        let headerRegion = this.currentStepNode.find('[data-placeholder="title"]').first();
        headerRegion.attr('id', stepId + '-title');
        headerRegion.attr('aria-labelledby', stepId + '-body');

        // Generally, a modal dialog has a role of dialog.
        this.currentStepNode.attr('role', 'dialog');
        this.currentStepNode.attr('tabindex', 0);
        this.currentStepNode.attr('aria-labelledby', stepId + '-title');
        this.currentStepNode.attr('aria-describedby', stepId + '-body');

        // Configure ARIA attributes on the target.
        let target = this.getStepTarget(stepConfig);
        if (target) {
            if (!target.attr('tabindex')) {
                target.attr('tabindex', 0);
            }

            target
                .data('original-describedby', target.attr('aria-describedby'))
                .attr('aria-describedby', stepId + '-body')
                ;
        }

        this.accessibilityShow(stepConfig);

        return this;
    }

    /**
     * Handle key down events.
     *
     * @method  handleKeyDown
     * @param   {EventFacade} e
     */
    handleKeyDown(e) {
        let tabbableSelector = 'a[href], link[href], [draggable=true], [contenteditable=true], ';
        tabbableSelector += ':input:enabled, [tabindex], button:enabled';
        switch (e.keyCode) {
            case 27:
                this.endTour();
                break;

            // 9 == Tab - trap focus for items with a backdrop.
            case 9:
                // Tab must be handled on key up only in this instance.
                (function() {
                    if (!this.currentStepConfig.hasBackdrop) {
                        // Trapping tab focus is only handled for those steps with a backdrop.
                        return;
                    }

                    // Find all tabbable locations.
                    let activeElement = $(document.activeElement);
                    let stepTarget = this.getStepTarget(this.currentStepConfig);
                    let tabbableNodes = $(tabbableSelector);
                    let dialogContainer = $('span[data-flexitour="container"]');
                    let currentIndex;
                    // Filter out element which is not belong to target section or dialogue.
                    if (stepTarget) {
                        tabbableNodes = tabbableNodes.filter(function(index, element) {
                            return stepTarget !== null
                                && (stepTarget.has(element).length
                                    || dialogContainer.has(element).length
                                    || stepTarget.is(element)
                                    || dialogContainer.is(element));
                        });
                    }

                    // Find index of focusing element.
                    tabbableNodes.each(function(index, element) {
                        if (activeElement.is(element)) {
                            currentIndex = index;
                            return false;
                        }
                        // Keep looping.
                        return true;
                    });

                    let nextIndex;
                    let nextNode;
                    let focusRelevant;
                    if (currentIndex != void 0) {
                        let direction = 1;
                        if (e.shiftKey) {
                            direction = -1;
                        }
                        nextIndex = currentIndex;
                        do {
                            nextIndex += direction;
                            nextNode = $(tabbableNodes[nextIndex]);
                        } while (nextNode.length && nextNode.is(':disabled') || nextNode.is(':hidden'));
                        if (nextNode.length) {
                            // A new f
                            focusRelevant = nextNode.closest(stepTarget).length;
                            focusRelevant = focusRelevant || nextNode.closest(this.currentStepNode).length;
                        } else {
                            // Unable to find the target somehow.
                            focusRelevant = false;
                        }
                    }

                    if (focusRelevant) {
                        nextNode.focus();
                    } else {
                        if (e.shiftKey) {
                            // Focus on the last tabbable node in the step.
                            this.currentStepNode.find(tabbableSelector).last().focus();
                        } else {
                            if (this.currentStepConfig.isOrphan) {
                                // Focus on the step - there is no target.
                                this.currentStepNode.focus();
                            } else {
                                // Focus on the step target.
                                stepTarget.focus();
                            }
                        }
                    }
                    e.preventDefault();
                }).call(this);
                break;
        }
    }

    /**
     * Start the current tour.
     *
     * @method  startTour
     * @param   {Integer}   startAt     Which step number to start at. If not specified, starts at the last point.
     * @chainable
     * @return {Object} this.
     */
    startTour(startAt) {
        if (this.storage && typeof startAt === 'undefined') {
            let storageStartValue = this.storage.getItem(this.storageKey);
            if (storageStartValue) {
                let storageStartAt = parseInt(storageStartValue, 10);
                if (storageStartAt <= this.steps.length) {
                    startAt = storageStartAt;
                }
            }
        }

        if (typeof startAt === 'undefined') {
            startAt = this.getCurrentStepNumber();
        }

        this.fireEventHandlers('beforeStart', startAt);
        this.gotoStep(startAt);
        this.fireEventHandlers('afterStart', startAt);

        return this;
    }

    /**
     * Restart the tour from the beginning, resetting the completionlag.
     *
     * @method  restartTour
     * @chainable
     * @return {Object} this.
     */
    restartTour() {
        return this.startTour(0);
    }

    /**
     * End the current tour.
     *
     * @method  endTour
     * @chainable
     * @return {Object} this.
     */
    endTour() {
        this.fireEventHandlers('beforeEnd');

        if (this.currentStepConfig) {
            let previousTarget = this.getStepTarget(this.currentStepConfig);
            if (previousTarget) {
                if (!previousTarget.attr('tabindex')) {
                    previousTarget.attr('tabindex', '-1');
                }
                previousTarget.focus();
            }
        }

        this.hide(true);

        this.fireEventHandlers('afterEnd');

        return this;
    }

    /**
     * Hide any currently visible steps.
     *
     * @method hide
     * @param {Bool} transition Animate the visibility change
     * @chainable
     * @return {Object} this.
     */
    hide(transition) {
        this.fireEventHandlers('beforeHide');

        if (this.currentStepNode && this.currentStepNode.length) {
            this.currentStepNode.hide();
            if (this.currentStepPopper) {
                this.currentStepPopper.destroy();
            }
        }

        // Restore original target configuration.
        if (this.currentStepConfig) {
            let target = this.getStepTarget(this.currentStepConfig);
            if (target) {
                if (target.data('original-labelledby')) {
                    target.attr('aria-labelledby', target.data('original-labelledby'));
                }

                if (target.data('original-describedby')) {
                    target.attr('aria-describedby', target.data('original-describedby'));
                }

                if (target.data('original-tabindex')) {
                    target.attr('tabindex', target.data('tabindex'));
                }
            }

            // Clear the step configuration.
            this.currentStepConfig = null;
        }

        let fadeTime = 0;
        if (transition) {
            fadeTime = 400;
        }

        // Remove the backdrop features.
        $('[data-flexitour="step-background"]').remove();
        $('[data-flexitour="step-backdrop"]').removeAttr('data-flexitour');
        $('[data-flexitour="backdrop"]').fadeOut(fadeTime, function() {
            $(this).remove();
        });

        // Remove aria-describedby and tabindex attributes.
        if (this.currentStepNode && this.currentStepNode.length) {
            let stepId = this.currentStepNode.attr('id');
            if (stepId) {
                let currentStepElement = '[aria-describedby="' + stepId + '-body"]';
                $(currentStepElement).removeAttr('tabindex');
                $(currentStepElement).removeAttr('aria-describedby');
            }
        }

        // Reset the listeners.
        this.resetStepListeners();

        this.accessibilityHide();

        this.fireEventHandlers('afterHide');

        this.currentStepNode = null;
        this.currentStepPopper = null;
        return this;
    }

    /**
     * Show the current steps.
     *
     * @method show
     * @chainable
     * @return {Object} this.
     */
    show() {
        // Show the current step.
        let startAt = this.getCurrentStepNumber();

        return this.gotoStep(startAt);
    }

    /**
     * Return the current step node.
     *
     * @method  getStepContainer
     * @return  {jQuery}
     */
    getStepContainer() {
        return $(this.currentStepNode);
    }

    /**
     * Calculate scrollTop.
     *
     * @method  calculateScrollTop
     * @param   {Object}    stepConfig      The step configuration of the step
     * @return  {Number}
     */
    calculateScrollTop(stepConfig) {
        let scrollTop = $(window).scrollTop();
        let viewportHeight = $(window).height();
        let targetNode = this.getStepTarget(stepConfig);

        if (stepConfig.placement === 'top') {
            // If the placement is top, center scroll at the top of the target.
            scrollTop = targetNode.offset().top - (viewportHeight / 2);
        } else if (stepConfig.placement === 'bottom') {
            // If the placement is bottom, center scroll at the bottom of the target.
            scrollTop = targetNode.offset().top + targetNode.height() - (viewportHeight / 2);
        } else if (targetNode.height() <= (viewportHeight * 0.8)) {
            // If the placement is left/right, and the target fits in the viewport, centre screen on the target
            scrollTop = targetNode.offset().top - ((viewportHeight - targetNode.height()) / 2);
        } else {
            // If the placement is left/right, and the target is bigger than the viewport, set scrollTop to target.top + buffer
            // and change step attachmentTarget to top+.
            scrollTop = targetNode.offset().top - (viewportHeight * 0.2);
        }

        // Never scroll over the top.
        scrollTop = Math.max(0, scrollTop);

        // Never scroll beyond the bottom.
        scrollTop = Math.min($(document).height() - viewportHeight, scrollTop);

        return Math.ceil(scrollTop);
    }

    /**
     * Calculate dialogue position for page middle.
     *
     * @method  calculateScrollTop
     * @return  {Number}
     */
    calculateStepPositionInPage() {
        let viewportHeight = $(window).height();
        let stepHeight = this.currentStepNode.height();

        let viewportWidth = $(window).width();
        let stepWidth = this.currentStepNode.width();

        return {
            top: Math.ceil((viewportHeight - stepHeight) / 2),
            left: Math.ceil((viewportWidth - stepWidth) / 2)
        };
    }

    /**
     * Position the step on the page.
     *
     * @method  positionStep
     * @param   {Object}    stepConfig      The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    positionStep(stepConfig) {
        let content = this.currentStepNode;
        if (!content || !content.length) {
            // Unable to find the step node.
            return this;
        }

        stepConfig.placement = this.recalculatePlacement(stepConfig);
        let flipBehavior;
        switch (stepConfig.placement) {
            case 'left':
                flipBehavior = ['left', 'right', 'top', 'bottom'];
                break;
            case 'right':
                flipBehavior = ['right', 'left', 'top', 'bottom'];
                break;
            case 'top':
                flipBehavior = ['top', 'bottom', 'right', 'left'];
                break;
            case 'bottom':
                flipBehavior = ['bottom', 'top', 'right', 'left'];
                break;
            default:
                flipBehavior = 'flip';
                break;
        }

        let target = this.getStepTarget(stepConfig);
        var config = {
            placement: stepConfig.placement + '-start',
            removeOnDestroy: true,
            modifiers: {
                flip: {
                    behaviour: flipBehavior,
                },
                arrow: {
                    element: '[data-role="arrow"]',
                },
            },
            onCreate: function(data) {
                recalculateArrowPosition(data);
            },
            onUpdate: function(data) {
                recalculateArrowPosition(data);
            },
        };

        let recalculateArrowPosition = function(data) {
            let placement = data.placement.split('-')[0];
            const isVertical = ['left', 'right'].indexOf(placement) !== -1;
            const arrowElement = data.instance.popper.querySelector('[data-role="arrow"]');
            const stepElement = $(data.instance.popper.querySelector('[data-role="flexitour-step"]'));
            if (isVertical) {
                let arrowHeight = parseFloat(window.getComputedStyle(arrowElement).height);
                let arrowOffset = parseFloat(window.getComputedStyle(arrowElement).top);
                let popperHeight = parseFloat(window.getComputedStyle(data.instance.popper).height);
                let popperOffset = parseFloat(window.getComputedStyle(data.instance.popper).top);
                let popperBorderWidth = parseFloat(stepElement.css('borderTopWidth'));
                let popperBorderRadiusWidth = parseFloat(stepElement.css('borderTopLeftRadius')) * 2;
                let arrowPos = arrowOffset + (arrowHeight / 2);
                let maxPos = popperHeight + popperOffset - popperBorderWidth - popperBorderRadiusWidth;
                let minPos = popperOffset + popperBorderWidth + popperBorderRadiusWidth;
                if (arrowPos >= maxPos || arrowPos <= minPos) {
                    let newArrowPos = 0;
                    if (arrowPos > (popperHeight / 2)) {
                        newArrowPos = maxPos - arrowHeight;
                    } else {
                        newArrowPos = minPos + arrowHeight;
                    }
                    $(arrowElement).css('top', newArrowPos);
                }
            } else {
                let arrowWidth = parseFloat(window.getComputedStyle(arrowElement).width);
                let arrowOffset = parseFloat(window.getComputedStyle(arrowElement).left);
                let popperWidth = parseFloat(window.getComputedStyle(data.instance.popper).width);
                let popperOffset = parseFloat(window.getComputedStyle(data.instance.popper).left);
                let popperBorderWidth = parseFloat(stepElement.css('borderTopWidth'));
                let popperBorderRadiusWidth = parseFloat(stepElement.css('borderTopLeftRadius')) * 2;
                let arrowPos = arrowOffset + (arrowWidth / 2);
                let maxPos = popperWidth + popperOffset - popperBorderWidth - popperBorderRadiusWidth;
                let minPos = popperOffset + popperBorderWidth + popperBorderRadiusWidth;
                if (arrowPos >= maxPos || arrowPos <= minPos) {
                    let newArrowPos = 0;
                    if (arrowPos > (popperWidth / 2)) {
                        newArrowPos = maxPos - arrowWidth;
                    } else {
                        newArrowPos = minPos + arrowWidth;
                    }
                    $(arrowElement).css('left', newArrowPos);
                }
            }
        };

        let background = $('[data-flexitour="step-background"]');
        if (background.length) {
            target = background;
        }
        this.currentStepPopper = new Popper(target, content[0], config);

        return this;
    }

    /**
     * For left/right placement, checks that there is room for the step at current window size.
     *
     * If there is not enough room, changes placement to 'top'.
     *
     * @method  recalculatePlacement
     * @param   {Object}    stepConfig      The step configuration of the step
     * @return  {String}                    The placement after recalculate
     */
    recalculatePlacement(stepConfig) {
        const buffer = 10;
        const arrowWidth = 16;
        let target = this.getStepTarget(stepConfig);
        let widthContent = this.currentStepNode.width() + arrowWidth;
        let targetOffsetLeft = target.offset().left - buffer;
        let targetOffsetRight = target.offset().left + target.width() + buffer;
        let placement = stepConfig.placement;

        if (['left', 'right'].indexOf(placement) !== -1) {
            if ((targetOffsetLeft < (widthContent + buffer)) &&
                ((targetOffsetRight + widthContent + buffer) > document.documentElement.clientWidth)) {
                placement = 'top';
            }
        }
        return placement;
    }

    /**
     * Add the backdrop.
     *
     * @method  positionBackdrop
     * @param   {Object}    stepConfig      The step configuration of the step
     * @chainable
     * @return {Object} this.
     */
    positionBackdrop(stepConfig) {
        if (stepConfig.backdrop) {
            this.currentStepConfig.hasBackdrop = true;
            let backdrop = $('<div data-flexitour="backdrop"></div>');

            if (stepConfig.zIndex) {
                if (stepConfig.attachPoint === 'append') {
                    stepConfig.attachTo.append(backdrop);
                } else {
                    backdrop.insertAfter(stepConfig.attachTo);
                }
            } else {
                $('body').append(backdrop);
            }

            if (this.isStepActuallyVisible(stepConfig)) {
                // The step has a visible target.
                // Punch a hole through the backdrop.
                let background = $('<div data-flexitour="step-background"></div>');

                let targetNode = this.getStepTarget(stepConfig);

                let buffer = 10;

                let colorNode = targetNode;
                if (buffer) {
                    colorNode = $('body');
                }

                background.css({
                    width: targetNode.outerWidth() + buffer + buffer,
                    height: targetNode.outerHeight() + buffer + buffer,
                    left: targetNode.offset().left - buffer,
                    top: targetNode.offset().top - buffer,
                    backgroundColor: this.calculateInherittedBackgroundColor(colorNode),
                });

                if (targetNode.offset().left < buffer) {
                    background.css({
                        width: targetNode.outerWidth() + targetNode.offset().left + buffer,
                        left: targetNode.offset().left,
                    });
                }

                if (targetNode.offset().top < buffer) {
                    background.css({
                        height: targetNode.outerHeight() + targetNode.offset().top + buffer,
                        top: targetNode.offset().top,
                    });
                }

                let targetRadius = targetNode.css('borderRadius');
                if (targetRadius && targetRadius !== $('body').css('borderRadius')) {
                    background.css('borderRadius', targetRadius);
                }

                let targetPosition = this.calculatePosition(targetNode);
                if (targetPosition === 'fixed') {
                    background.css('top', 0);
                } else if (targetPosition === 'absolute') {
                    background.css('position', 'fixed');
                }

                let fader = background.clone();
                fader.css({
                    backgroundColor: backdrop.css('backgroundColor'),
                    opacity: backdrop.css('opacity'),
                });
                fader.attr('data-flexitour', 'step-background-fader');

                if (stepConfig.zIndex) {
                    if (stepConfig.attachPoint === 'append') {
                        stepConfig.attachTo.append(background);
                    } else {
                        fader.insertAfter(stepConfig.attachTo);
                        background.insertAfter(stepConfig.attachTo);
                    }
                } else {
                    $('body').append(fader);
                    $('body').append(background);
                }

                // Add the backdrop data to the actual target.
                // This is the part which actually does the work.
                targetNode.attr('data-flexitour', 'step-backdrop');

                if (stepConfig.zIndex) {
                    backdrop.css('zIndex', stepConfig.zIndex);
                    background.css('zIndex', stepConfig.zIndex + 1);
                    targetNode.css('zIndex', stepConfig.zIndex + 2);
                }

                fader.fadeOut('2000', function() {
                    $(this).remove();
                });
            }
        }
        return this;
    }

    /**
     * Calculate the inheritted z-index.
     *
     * @method  calculateZIndex
     * @param   {jQuery}    elem                        The element to calculate z-index for
     * @return  {Number}                                Calculated z-index
     */
    calculateZIndex(elem) {
        elem = $(elem);
        while (elem.length && elem[0] !== document) {
            // Ignore z-index if position is set to a value where z-index is ignored by the browser
            // This makes behavior of this function consistent across browsers
            // WebKit always returns auto if the element is positioned.
            let position = elem.css("position");
            if (position === "absolute" || position === "relative" || position === "fixed") {
                // IE returns 0 when zIndex is not specified
                // other browsers return a string
                // we ignore the case of nested elements with an explicit value of 0
                // <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
                let value = parseInt(elem.css("zIndex"), 10);
                if (!isNaN(value) && value !== 0) {
                    return value;
                }
            }
            elem = elem.parent();
        }

        return 0;
    }

    /**
     * Calculate the inheritted background colour.
     *
     * @method  calculateInherittedBackgroundColor
     * @param   {jQuery}    elem                        The element to calculate colour for
     * @return  {String}                                Calculated background colour
     */
    calculateInherittedBackgroundColor(elem) {
        // Use a fake node to compare each element against.
        let fakeNode = $('<div>').hide();
        $('body').append(fakeNode);
        let fakeElemColor = fakeNode.css('backgroundColor');
        fakeNode.remove();

        elem = $(elem);
        while (elem.length && elem[0] !== document) {
            let color = elem.css('backgroundColor');
            if (color !== fakeElemColor) {
                return color;
            }
            elem = elem.parent();
        }

        return null;
    }

    /**
     * Calculate the inheritted position.
     *
     * @method  calculatePosition
     * @param   {jQuery}    elem                        The element to calculate position for
     * @return  {String}                                Calculated position
     */
    calculatePosition(elem) {
        elem = $(elem);
        while (elem.length && elem[0] !== document) {
            let position = elem.css('position');
            if (position !== 'static') {
                return position;
            }
            elem = elem.parent();
        }

        return null;
    }

    /**
     * Perform accessibility changes for step shown.
     *
     * This will add aria-hidden="true" to all siblings and parent siblings.
     *
     * @method  accessibilityShow
     */
    accessibilityShow() {
        let stateHolder = 'data-has-hidden';
        let attrName = 'aria-hidden';
        let hideFunction = function(child) {
            let flexitourRole = child.data('flexitour');
            if (flexitourRole) {
                switch (flexitourRole) {
                    case 'container':
                    case 'target':
                        return;
                }
            }

            let hidden = child.attr(attrName);
            if (!hidden) {
                child.attr(stateHolder, true);
                Aria.hide(child);
            }
        };

        this.currentStepNode.siblings().each(function(index, node) {
            hideFunction($(node));
        });
        this.currentStepNode.parentsUntil('body').siblings().each(function(index, node) {
            hideFunction($(node));
        });
    }

    /**
     * Perform accessibility changes for step hidden.
     *
     * This will remove any newly added aria-hidden="true".
     *
     * @method  accessibilityHide
     */
    accessibilityHide() {
        let stateHolder = 'data-has-hidden';
        let showFunction = function(child) {
            let hidden = child.attr(stateHolder);
            if (typeof hidden !== 'undefined') {
                child.removeAttr(stateHolder);
                Aria.unhide(child);
            }
        };

        $('[' + stateHolder + ']').each(function(index, node) {
            showFunction($(node));
        });
    }
}
