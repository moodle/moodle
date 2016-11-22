// jshint ignore: start
(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery","./popper"], function (a0,b1) {
      return (root['Tour'] = factory(a0,b1));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"),require("popper.js"));
  } else {
    root['Tour'] = factory($,Popper);
  }
}(this, function ($, Popper) {

"use strict";

/**
 * A Tour.
 *
 * @class   Tour
 * @param   {object}    config  The configuration object.
 */

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

function Tour(config) {
    this.init(config);
}

/**
 * The name of the tour.
 *
 * @property    {String}    tourName
 */
Tour.prototype.tourName;

/**
 * The name of the tour storage key.
 *
 * @property    {String}    storageKey
 */
Tour.prototype.storageKey;

/**
 * The session storage object
 *
 * @property    {Storage}   storage
 */
Tour.prototype.storage;

/**
 * The original configuration as passed into the constructor.
 *
 * @property    {Object}    originalConfiguration
 */
Tour.prototype.originalConfiguration;

/**
 * The list of step listeners.
 *
 * @property    {Array}     listeners
 */
Tour.prototype.listeners;

/**
 * The list of event handlers.
 *
 * @property    {Object}    eventHandlers
 */
Tour.prototype.eventHandlers;

/**
 * The list of steps.
 *
 * @property    {Object[]}      steps
 */
Tour.prototype.steps;

/**
 * The current step node.
 *
 * @property    {jQuery}        currentStepNode
 */
Tour.prototype.currentStepNode;

/**
 * The current step number.
 *
 * @property    {Number}        currentStepNumber
 */
Tour.prototype.currentStepNumber;

/**
 * The popper for the current step.
 *
 * @property    {Popper}        currentStepPopper
 */
Tour.prototype.currentStepPopper;

/**
 * The config for the current step.
 *
 * @property    {Object}        currentStepConfig
 */
Tour.prototype.currentStepConfig;

/**
 * The template content.
 *
 * @property    {String}        templateContent
 */
Tour.prototype.templateContent;

/**
 * Initialise the tour.
 *
 * @method  init
 * @param   {Object}    config  The configuration object.
 * @chainable
 */
Tour.prototype.init = function (config) {
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
};

/**
 * Reset the current tour state.
 *
 * @method  reset
 * @chainable
 */
Tour.prototype.reset = function () {
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
};

/**
 * Prepare tour configuration.
 *
 * @method  configure
 * @chainable
 */
Tour.prototype.configure = function (config) {
    var _this = this;

    if ((typeof config === 'undefined' ? 'undefined' : _typeof(config)) === 'object') {
        // Tour name.
        if (typeof config.tourName !== 'undefined') {
            this.tourName = config.tourName;
        }

        // Set up eventHandlers.
        if (config.eventHandlers) {
            (function () {
                var eventName = void 0;
                for (eventName in config.eventHandlers) {
                    config.eventHandlers[eventName].forEach(function (handler) {
                        this.addEventHandler(eventName, handler);
                    }, _this);
                }
            })();
        }

        // Reset the step configuration.
        this.resetStepDefaults(true);

        // Configure the steps.
        if (_typeof(config.steps) === 'object') {
            this.steps = config.steps;
        }

        if (typeof config.template !== 'undefined') {
            this.templateContent = config.template;
        }
    }

    // Check that we have enough to start the tour.
    this.checkMinimumRequirements();

    return this;
};

/**
 * Check that the configuration meets the minimum requirements.
 *
 * @method  checkMinimumRequirements
 * @chainable
 */
Tour.prototype.checkMinimumRequirements = function () {
    // Need a tourName.
    if (!this.tourName) {
        throw new Error("Tour Name required");
    }

    // Need a minimum of one step.
    if (!this.steps || !this.steps.length) {
        throw new Error("Steps must be specified");
    }
};

/**
 * Reset step default configuration.
 *
 * @method  resetStepDefaults
 * @param   {Boolean}   loadOriginalConfiguration   Whether to load the original configuration supplied with the Tour.
 * @chainable
 */
Tour.prototype.resetStepDefaults = function (loadOriginalConfiguration) {
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
};

/**
 * Set the step defaults.
 *
 * @method  setStepDefaults
 * @param   {Object}    stepDefaults                The step defaults to apply to all steps
 * @chainable
 */
Tour.prototype.setStepDefaults = function (stepDefaults) {
    if (!this.stepDefaults) {
        this.stepDefaults = {};
    }
    $.extend(this.stepDefaults, {
        element: '',
        placement: 'top',
        delay: 0,
        moveOnClick: false,
        moveAfterTime: 0,
        orphan: false,
        direction: 1
    }, stepDefaults);

    return this;
};

/**
 * Retrieve the current step number.
 *
 * @method  getCurrentStepNumber
 * @return  {Integer}                   The current step number
 */
Tour.prototype.getCurrentStepNumber = function () {
    return parseInt(this.currentStepNumber, 10);
};

/**
 * Store the current step number.
 *
 * @method  setCurrentStepNumber
 * @param   {Integer}   stepNumber      The current step number
 * @chainable
 */
Tour.prototype.setCurrentStepNumber = function (stepNumber) {
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
};

/**
 * Get the next step number after the currently displayed step.
 *
 * @method  getNextStepNumber
 * @return  {Integer}    The next step number to display
 */
Tour.prototype.getNextStepNumber = function (stepNumber) {
    if (typeof stepNumber === 'undefined') {
        stepNumber = this.getCurrentStepNumber();
    }
    var nextStepNumber = stepNumber + 1;

    // Keep checking the remaining steps.
    while (nextStepNumber <= this.steps.length) {
        if (this.isStepPotentiallyVisible(this.getStepConfig(nextStepNumber))) {
            return nextStepNumber;
        }
        nextStepNumber++;
    }

    return null;
};

/**
 * Get the previous step number before the currently displayed step.
 *
 * @method  getPreviousStepNumber
 * @return  {Integer}    The previous step number to display
 */
Tour.prototype.getPreviousStepNumber = function (stepNumber) {
    if (typeof stepNumber === 'undefined') {
        stepNumber = this.getCurrentStepNumber();
    }
    var previousStepNumber = stepNumber - 1;

    // Keep checking the remaining steps.
    while (previousStepNumber >= 0) {
        if (this.isStepPotentiallyVisible(this.getStepConfig(previousStepNumber))) {
            return previousStepNumber;
        }
        previousStepNumber--;
    }

    return null;
};

/**
 * Is the step the final step number?
 *
 * @method  isLastStep
 * @param   {Integer}   stepNumber  Step number to test
 * @return  {Boolean}               Whether the step is the final step
 */
Tour.prototype.isLastStep = function (stepNumber) {
    var nextStepNumber = this.getNextStepNumber(stepNumber);

    return nextStepNumber === null;
};

/**
 * Is the step the first step number?
 *
 * @method  isFirstStep
 * @param   {Integer}   stepNumber  Step number to test
 * @return  {Boolean}               Whether the step is the first step
 */
Tour.prototype.isFirstStep = function (stepNumber) {
    var previousStepNumber = this.getPreviousStepNumber(stepNumber);

    return previousStepNumber === null;
};

/**
 * Is this step potentially visible?
 *
 * @method  isStepPotentiallyVisible
 * @param   {Integer}   stepNumber  Step number to test
 * @return  {Boolean}               Whether the step is the potentially visible
 */
Tour.prototype.isStepPotentiallyVisible = function (stepConfig) {
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
};

/**
 * Is this step actually visible?
 *
 * @method  isStepActuallyVisible
 * @param   {Integer}   stepNumber  Step number to test
 * @return  {Boolean}               Whether the step is actually visible
 */
Tour.prototype.isStepActuallyVisible = function (stepConfig) {
    if (!stepConfig) {
        // Without step config, there can be no step.
        return false;
    }

    var target = this.getStepTarget(stepConfig);
    if (target && target.length && target.is(':visible')) {
        // Without a target, there can be no step.
        return !!target.length;
    }

    return false;
};

/**
 * Go to the next step in the tour.
 *
 * @method  next
 * @chainable
 */
Tour.prototype.next = function () {
    return this.gotoStep(this.getNextStepNumber());
};

/**
 * Go to the previous step in the tour.
 *
 * @method  previous
 * @chainable
 */
Tour.prototype.previous = function () {
    return this.gotoStep(this.getPreviousStepNumber(), -1);
};

/**
 * Go to the specified step in the tour.
 *
 * @method  gotoStep
 * @param   {Integer}   stepNumber      The step number to display
 * @chainable
 */
Tour.prototype.gotoStep = function (stepNumber, direction) {
    if (stepNumber < 0) {
        return this.endTour();
    }

    var stepConfig = this.getStepConfig(stepNumber);
    if (stepConfig === null) {
        return this.endTour();
    }

    return this._gotoStep(stepConfig, direction);
};

Tour.prototype._gotoStep = function (stepConfig, direction) {
    if (!stepConfig) {
        return this.endTour();
    }

    if (typeof stepConfig.delay !== 'undefined' && stepConfig.delay && !stepConfig.delayed) {
        stepConfig.delayed = true;
        window.setTimeout(this._gotoStep.bind(this), stepConfig.delay, stepConfig, direction);

        return this;
    } else if (!stepConfig.orphan && !this.isStepActuallyVisible(stepConfig)) {
        var fn = direction == -1 ? 'getPreviousStepNumber' : 'getNextStepNumber';
        return this.gotoStep(this[fn](stepConfig.stepNumber), direction);
    }

    this.hide();

    this.fireEventHandlers('beforeRender', stepConfig);
    this.renderStep(stepConfig);
    this.fireEventHandlers('afterRender', stepConfig);

    return this;
};

/**
 * Fetch the normalised step configuration for the specified step number.
 *
 * @method  getStepConfig
 * @param   {Integer}   stepNumber      The step number to fetch configuration for
 * @return  {Object}                    The step configuration
 */
Tour.prototype.getStepConfig = function (stepNumber) {
    if (stepNumber === null || stepNumber < 0 || stepNumber >= this.steps.length) {
        return null;
    }

    // Normalise the step configuration.
    var stepConfig = this.normalizeStepConfig(this.steps[stepNumber]);

    // Add the stepNumber to the stepConfig.
    stepConfig = $.extend(stepConfig, { stepNumber: stepNumber });

    return stepConfig;
};

/**
 * Normalise the supplied step configuration.
 *
 * @method  normalizeStepConfig
 * @param   {Object}    stepConfig      The step configuration to normalise
 * @return  {Object}                    The normalised step configuration
 */
Tour.prototype.normalizeStepConfig = function (stepConfig) {

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
        attachPoint: 'after'
    }, stepConfig);

    if (stepConfig.attachTo) {
        stepConfig.attachTo = $(stepConfig.attachTo).first();
    }

    return stepConfig;
};

/**
 * Fetch the actual step target from the selector.
 *
 * This should not be called until after any delay has completed.
 *
 * @method  getStepTarget
 * @param   {Object}    stepConfig      The step configuration
 * @return  {$}
 */
Tour.prototype.getStepTarget = function (stepConfig) {
    if (stepConfig.target) {
        return $(stepConfig.target);
    }

    return null;
};

/**
 * Fire any event handlers for the specified event.
 *
 * @param   {String}    eventName       The name of the event to handle
 * @param   {Object}    data            Any data to pass to the event
 * @chainable
 */
Tour.prototype.fireEventHandlers = function (eventName, data) {
    if (typeof this.eventHandlers[eventName] === 'undefined') {
        return this;
    }

    this.eventHandlers[eventName].forEach(function (thisEvent) {
        thisEvent.call(this, data);
    }, this);

    return this;
};

/**
 * @method  addEventHandler
 * @param   string      eventName       The name of the event to listen for
 * @param   function    handler         The event handler to call
 */
Tour.prototype.addEventHandler = function (eventName, handler) {
    if (typeof this.eventHandlers[eventName] === 'undefined') {
        this.eventHandlers[eventName] = [];
    }

    this.eventHandlers[eventName].push(handler);

    return this;
};

/**
 * Process listeners for the step being shown.
 *
 * @method  processStepListeners
 * @param   {object}    stepConfig      The configuration for the step
 * @chainable
 */
Tour.prototype.processStepListeners = function (stepConfig) {
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

    // Keypresses.
    {
        node: $('body'),
        args: ['keydown', $.proxy(this.handleKeyDown, this)]
    });

    if (stepConfig.moveOnClick) {
        var targetNode = this.getStepTarget(stepConfig);
        this.listeners.push({
            node: targetNode,
            args: ['click', $.proxy(function (e) {
                if ($(e.target).parents('[data-flexitour="container"]').length === 0) {
                    // Ignore clicks when they are in the flexitour.
                    window.setTimeout($.proxy(this.next, this), 500);
                }
            }, this)]
        });
    }

    this.listeners.forEach(function (listener) {
        listener.node.on.apply(listener.node, listener.args);
    });

    return this;
};

/**
 * Reset step listeners.
 *
 * @method  resetStepListeners
 * @chainable
 */
Tour.prototype.resetStepListeners = function () {
    // Stop listening to all external handlers.
    if (this.listeners) {
        this.listeners.forEach(function (listener) {
            listener.node.off.apply(listener.node, listener.args);
        });
    }
    this.listeners = [];

    return this;
};

/**
 * The standard step renderer.
 *
 * @method  renderStep
 * @param   {Object}    stepConfig      The step configuration of the step
 * @chainable
 */
Tour.prototype.renderStep = function (stepConfig) {
    // Store the current step configuration for later.
    this.currentStepConfig = stepConfig;
    this.setCurrentStepNumber(stepConfig.stepNumber);

    // Fetch the template and convert it to a $ object.
    var template = $(this.getTemplateContent());

    // Title.
    template.find('[data-placeholder="title"]').html(stepConfig.title);

    // Body.
    template.find('[data-placeholder="body"]').html(stepConfig.body);

    // Is this the first step?
    if (this.isFirstStep(stepConfig.stepNumber)) {
        template.find('[data-role="previous"]').prop('disabled', true);
    } else {
        template.find('[data-role="previous"]').prop('disabled', false);
    }

    // Is this the final step?
    if (this.isLastStep(stepConfig.stepNumber)) {
        template.find('[data-role="next"]').prop('disabled', true);
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
};

/**
 * Getter for the template content.
 *
 * @method  getTemplateContent
 * @return  {$}
 */
Tour.prototype.getTemplateContent = function () {
    return $(this.templateContent).clone();
};

/**
 * Helper to add a step to the page.
 *
 * @method  addStepToPage
 * @param   {Object}    stepConfig      The step configuration of the step
 * @chainable
 */
Tour.prototype.addStepToPage = function (stepConfig) {
    var stepContent = stepConfig.template;

    // Create the stepNode from the template data.
    var currentStepNode = $('<span data-flexitour="container"></span>').html(stepConfig.template).hide();

    // The scroll animation occurs on the body or html.
    var animationTarget = $('body, html').stop(true, true);

    if (this.isStepActuallyVisible(stepConfig)) {
        var targetNode = this.getStepTarget(stepConfig);

        targetNode.data('flexitour', 'target');

        var zIndex = this.calculateZIndex(targetNode);
        if (zIndex) {
            stepConfig.zIndex = zIndex + 1;
        }

        if (stepConfig.zIndex) {
            currentStepNode.css('zIndex', stepConfig.zIndex + 1);
        }

        // Add the backdrop.
        this.positionBackdrop(stepConfig);

        if (stepConfig.attachPoint === 'append') {
            stepConfig.attachTo.append(currentStepNode);
            this.currentStepNode = currentStepNode;
        } else {
            this.currentStepNode = currentStepNode.insertAfter(stepConfig.attachTo);
        }

        // Ensure that the step node is positioned.
        // Some situations mean that the value is not properly calculated without this step.
        this.currentStepNode.css({
            top: 0,
            left: 0
        });

        animationTarget.animate({
            scrollTop: this.calculateScrollTop(stepConfig)
        }).promise().then(function () {
            this.positionStep(stepConfig);
            this.revealStep(stepConfig);
        }.bind(this));
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
        stepConfig.attachTo.append(currentStepNode);
        this.currentStepNode = currentStepNode;

        this.currentStepNode.offset(this.calculateStepPositionInPage());
        this.currentStepNode.css('position', 'fixed');

        this.currentStepPopper = new Popper($('body'), this.currentStepNode[0], {
            removeOnDestroy: true,
            placement: stepConfig.placement + '-start',
            arrowElement: '[data-role="arrow"]',
            // Empty the modifiers. We've already placed the step and don't want it moved.
            modifiers: {
                hide: {
                    enabled: false
                },
                applyStyle: {
                    onLoad: null,
                    enabled: false
                }
            }
        });

        this.revealStep(stepConfig);
    }

    return this;
};

Tour.prototype.revealStep = function (stepConfig) {
    // Fade the step in.
    this.currentStepNode.fadeIn('', $.proxy(function () {
        // Announce via ARIA.
        this.announceStep(stepConfig);

        // Focus on the current step Node.
        this.currentStepNode.focus();
        window.setTimeout($.proxy(function () {
            // After a brief delay, focus again.
            // There seems to be an issue with Jaws where it only reads the dialogue title initially.
            // This second focus helps it to read the full dialogue.
            if (this.currentStepNode) {
                this.currentStepNode.focus();
            }
        }, this), 100);
    }, this));

    return this;
};

/**
 * Helper to announce the step on the page.
 *
 * @method  announceStep
 * @param   {Object}    stepConfig      The step configuration of the step
 * @chainable
 */
Tour.prototype.announceStep = function (stepConfig) {
    // Setup the step Dialogue as per:
    // * https://www.w3.org/TR/wai-aria-practices/#dialog_nonmodal
    // * https://www.w3.org/TR/wai-aria-practices/#dialog_modal

    // Generate an ID for the current step node.
    var stepId = 'tour-step-' + this.tourName + '-' + stepConfig.stepNumber;
    this.currentStepNode.attr('id', stepId);

    var bodyRegion = this.currentStepNode.find('[data-placeholder="body"]').first();
    bodyRegion.attr('id', stepId + '-body');
    bodyRegion.attr('role', 'document');

    var headerRegion = this.currentStepNode.find('[data-placeholder="title"]').first();
    headerRegion.attr('id', stepId + '-title');
    headerRegion.attr('aria-labelledby', stepId + '-body');

    // Generally, a modal dialog has a role of dialog.
    this.currentStepNode.attr('role', 'dialog');
    this.currentStepNode.attr('tabindex', 0);
    this.currentStepNode.attr('aria-labelledby', stepId + '-title');
    this.currentStepNode.attr('aria-describedby', stepId + '-body');

    // Configure ARIA attributes on the target.
    var target = this.getStepTarget(stepConfig);
    if (target) {
        if (!target.attr('tabindex')) {
            target.attr('tabindex', 0);
        }

        target.data('original-describedby', target.attr('aria-describedby')).attr('aria-describedby', stepId + '-body');
    }

    this.accessibilityShow(stepConfig);

    return this;
};

/**
 * Handle key down events.
 *
 * @method  handleKeyDown
 * @param   {EventFacade} e
 */
Tour.prototype.handleKeyDown = function (e) {
    var tabbableSelector = 'a[href], link[href], [draggable=true], [contenteditable=true], :input:enabled, [tabindex], button';
    switch (e.keyCode) {
        case 27:
            this.endTour();
            break;

        // 9 == Tab - trap focus for items with a backdrop.
        case 9:
            // Tab must be handled on key up only in this instance.
            (function () {
                if (!this.currentStepConfig.hasBackdrop) {
                    // Trapping tab focus is only handled for those steps with a backdrop.
                    return;
                }

                // Find all tabbable locations.
                var activeElement = $(document.activeElement);
                var stepTarget = this.getStepTarget(this.currentStepConfig);
                var tabbableNodes = $(tabbableSelector);
                var currentIndex = void 0;
                tabbableNodes.filter(function (index, element) {
                    if (activeElement.is(element)) {
                        currentIndex = index;
                        return false;
                    }
                });

                var nextIndex = void 0;
                var nextNode = void 0;
                var focusRelevant = void 0;
                if (currentIndex) {
                    var direction = 1;
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
};

/**
 * Start the current tour.
 *
 * @method  startTour
 * @param   {Integer}   startAt     Which step number to start at. If not specified, starts at the last point.
 * @chainable
 */
Tour.prototype.startTour = function (startAt) {
    if (this.storage && typeof startAt === 'undefined') {
        var storageStartValue = this.storage.getItem(this.storageKey);
        if (storageStartValue) {
            var storageStartAt = parseInt(storageStartValue, 10);
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
};

/**
 * Restart the tour from the beginning, resetting the completionlag.
 *
 * @method  restartTour
 * @chainable
 */
Tour.prototype.restartTour = function () {
    return this.startTour(0);
};

/**
 * End the current tour.
 *
 * @method  endTour
 * @chainable
 */
Tour.prototype.endTour = function () {
    this.fireEventHandlers('beforeEnd');

    if (this.currentStepConfig) {
        var previousTarget = this.getStepTarget(this.currentStepConfig);
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
};

/**
 * Hide any currently visible steps.
 *
 * @method hide
 * @chainable
 */
Tour.prototype.hide = function (transition) {
    this.fireEventHandlers('beforeHide');

    if (this.currentStepNode && this.currentStepNode.length) {
        this.currentStepNode.hide();
        if (this.currentStepPopper) {
            this.currentStepPopper.destroy();
        }
    }

    // Restore original target configuration.
    if (this.currentStepConfig) {
        var target = this.getStepTarget(this.currentStepConfig);
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

    var fadeTime = 0;
    if (transition) {
        fadeTime = 400;
    }

    // Remove the backdrop features.
    $('[data-flexitour="step-background"]').remove();
    $('[data-flexitour="step-backdrop"]').removeAttr('data-flexitour');
    $('[data-flexitour="backdrop"]').fadeOut(fadeTime, function () {
        $(this).remove();
    });

    // Reset the listeners.
    this.resetStepListeners();

    this.accessibilityHide();

    this.fireEventHandlers('afterHide');

    this.currentStepNode = null;
    this.currentStepPopper = null;
    return this;
};

/**
 * Show the current steps.
 *
 * @method show
 * @chainable
 */
Tour.prototype.show = function () {
    // Show the current step.
    var startAt = this.getCurrentStepNumber();

    return this.gotoStep(startAt);
};

/**
 * Return the current step node.
 *
 * @method  getStepContainer
 * @return  {jQuery}
 */
Tour.prototype.getStepContainer = function () {
    return $(this.currentStepNode);
};

/**
 * Calculate scrollTop.
 *
 * @method  calculateScrollTop
 * @param   {Object}    stepConfig      The step configuration of the step
 * @return  {Number}
 */
Tour.prototype.calculateScrollTop = function (stepConfig) {
    var scrollTop = $(window).scrollTop();
    var viewportHeight = $(window).height();
    var targetNode = this.getStepTarget(stepConfig);

    if (stepConfig.placement === 'top') {
        // If the placement is top, center scroll at the top of the target.
        scrollTop = targetNode.offset().top - viewportHeight / 2;
    } else if (stepConfig.placement === 'bottom') {
        // If the placement is bottom, center scroll at the bottom of the target.
        scrollTop = targetNode.offset().top + targetNode.height() - viewportHeight / 2;
    } else if (targetNode.height() <= viewportHeight * 0.8) {
        // If the placement is left/right, and the target fits in the viewport, centre screen on the target
        scrollTop = targetNode.offset().top - (viewportHeight - targetNode.height()) / 2;
    } else {
        // If the placement is left/right, and the target is bigger than the viewport, set scrollTop to target.top + buffer
        // and change step attachmentTarget to top+.
        scrollTop = targetNode.offset().top - viewportHeight * 0.2;
    }

    // Never scroll over the top.
    scrollTop = Math.max(0, scrollTop);

    // Never scroll beyond the bottom.
    scrollTop = Math.min($(document).height() - viewportHeight, scrollTop);

    return Math.ceil(scrollTop);
};

/**
 * Calculate dialogue position for page middle.
 *
 * @method  calculateScrollTop
 * @return  {Number}
 */
Tour.prototype.calculateStepPositionInPage = function () {
    var viewportHeight = $(window).height();
    var stepHeight = this.currentStepNode.height();

    var viewportWidth = $(window).width();
    var stepWidth = this.currentStepNode.width();

    return {
        top: Math.ceil((viewportHeight - stepHeight) / 2),
        left: Math.ceil((viewportWidth - stepWidth) / 2)
    };
};

/**
 * Position the step on the page.
 *
 * @method  positionStep
 * @param   {Object}    stepConfig      The step configuration of the step
 * @chainable
 */
Tour.prototype.positionStep = function (stepConfig) {
    var content = this.currentStepNode;
    if (!content || !content.length) {
        // Unable to find the step node.
        return this;
    }

    var flipBehavior = void 0;
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

    var target = this.getStepTarget(stepConfig);
    var config = {
        placement: stepConfig.placement + '-start',
        removeOnDestroy: true,
        modifiers: {
            flip: {
                behaviour: flipBehavior
            },
            arrow: {
                element: '[data-role="arrow"]'
            }
        }
    };

    var boundaryElement = target.closest('section');
    if (boundaryElement.length) {
        config.boundariesElement = boundaryElement[0];
    }

    var background = $('[data-flexitour="step-background"]');
    if (background.length) {
        target = background;
    }
    this.currentStepPopper = new Popper(target, content[0], config);

    return this;
};

/**
 * Add the backdrop.
 *
 * @method  positionBackdrop
 * @param   {Object}    stepConfig      The step configuration of the step
 * @chainable
 */
Tour.prototype.positionBackdrop = function (stepConfig) {
    if (stepConfig.backdrop) {
        this.currentStepConfig.hasBackdrop = true;
        var backdrop = $('<div data-flexitour="backdrop"></div>');

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
            var background = $('<div data-flexitour="step-background"></div>');

            var targetNode = this.getStepTarget(stepConfig);

            var buffer = 10;

            var colorNode = targetNode;
            if (buffer) {
                colorNode = $('body');
            }

            background.css({
                width: targetNode.outerWidth() + buffer + buffer,
                height: targetNode.outerHeight() + buffer + buffer,
                left: targetNode.offset().left - buffer,
                top: targetNode.offset().top - buffer,
                backgroundColor: this.calculateInherittedBackgroundColor(colorNode)
            });

            if (targetNode.offset().left < buffer) {
                background.css({
                    width: targetNode.outerWidth() + targetNode.offset().left + buffer,
                    left: targetNode.offset().left
                });
            }

            if (targetNode.offset().top < buffer) {
                background.css({
                    height: targetNode.outerHeight() + targetNode.offset().top + buffer,
                    top: targetNode.offset().top
                });
            }

            var targetRadius = targetNode.css('borderRadius');
            if (targetRadius && targetRadius !== $('body').css('borderRadius')) {
                background.css('borderRadius', targetRadius);
            }

            var targetPosition = this.calculatePosition(targetNode);
            if (targetPosition === 'fixed') {
                background.css('top', 0);
            }

            var fader = background.clone();
            fader.css({
                backgroundColor: backdrop.css('backgroundColor'),
                opacity: backdrop.css('opacity')
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

            fader.fadeOut('2000', function () {
                $(this).remove();
            });
        }
    }
    return this;
};

/**
 * Calculate the inheritted z-index.
 *
 * @method  calculateZIndex
 * @param   {jQuery}    elem                        The element to calculate z-index for
 * @return  {Number}                                Calculated z-index
 */
Tour.prototype.calculateZIndex = function (elem) {
    elem = $(elem);
    while (elem.length && elem[0] !== document) {
        // Ignore z-index if position is set to a value where z-index is ignored by the browser
        // This makes behavior of this function consistent across browsers
        // WebKit always returns auto if the element is positioned.
        var position = elem.css("position");
        if (position === "absolute" || position === "relative" || position === "fixed") {
            // IE returns 0 when zIndex is not specified
            // other browsers return a string
            // we ignore the case of nested elements with an explicit value of 0
            // <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
            var value = parseInt(elem.css("zIndex"), 10);
            if (!isNaN(value) && value !== 0) {
                return value;
            }
        }
        elem = elem.parent();
    }

    return 0;
};

/**
 * Calculate the inheritted background colour.
 *
 * @method  calculateInherittedBackgroundColor
 * @param   {jQuery}    elem                        The element to calculate colour for
 * @return  {String}                                Calculated background colour
 */
Tour.prototype.calculateInherittedBackgroundColor = function (elem) {
    // Use a fake node to compare each element against.
    var fakeNode = $('<div>').hide();
    $('body').append(fakeNode);
    var fakeElemColor = fakeNode.css('backgroundColor');
    fakeNode.remove();

    elem = $(elem);
    while (elem.length && elem[0] !== document) {
        var color = elem.css('backgroundColor');
        if (color !== fakeElemColor) {
            return color;
        }
        elem = elem.parent();
    }

    return null;
};

/**
 * Calculate the inheritted position.
 *
 * @method  calculatePosition
 * @param   {jQuery}    elem                        The element to calculate position for
 * @return  {String}                                Calculated position
 */
Tour.prototype.calculatePosition = function (elem) {
    elem = $(elem);
    while (elem.length && elem[0] !== document) {
        var position = elem.css('position');
        if (position !== 'static') {
            return position;
        }
        elem = elem.parent();
    }

    return null;
};

/**
 * Perform accessibility changes for step shown.
 *
 * This will add aria-hidden="true" to all siblings and parent siblings.
 *
 * @method  accessibilityShow
 */
Tour.prototype.accessibilityShow = function () {
    var stateHolder = 'data-has-hidden';
    var attrName = 'aria-hidden';
    var hideFunction = function hideFunction(child) {
        var flexitourRole = child.data('flexitour');
        if (flexitourRole) {
            switch (flexitourRole) {
                case 'container':
                case 'target':
                    return;
            }
        }

        var hidden = child.attr(attrName);
        if (!hidden) {
            child.attr(stateHolder, true);
            child.attr(attrName, true);
        }
    };

    this.currentStepNode.siblings().each(function (index, node) {
        hideFunction($(node));
    });
    this.currentStepNode.parentsUntil('body').siblings().each(function (index, node) {
        hideFunction($(node));
    });
};

/**
 * Perform accessibility changes for step hidden.
 *
 * This will remove any newly added aria-hidden="true".
 *
 * @method  accessibilityHide
 */
Tour.prototype.accessibilityHide = function () {
    var stateHolder = 'data-has-hidden';
    var attrName = 'aria-hidden';
    var showFunction = function showFunction(child) {
        var hidden = child.attr(stateHolder);
        if (typeof hidden !== 'undefined') {
            child.removeAttr(stateHolder);
            child.removeAttr(attrName);
        }
    };

    $('[' + stateHolder + ']').each(function (index, node) {
        showFunction($(node));
    });
};

if ((typeof exports === 'undefined' ? 'undefined' : _typeof(exports)) === 'object') {
    module.exports = Tour;
}

return Tour;

}));
