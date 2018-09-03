var H5P = window.H5P = window.H5P || {};

/**
 * The external event dispatcher. Others, outside of H5P may register and
 * listen for H5P Events here.
 *
 * @type {H5P.EventDispatcher}
 */
H5P.externalDispatcher = new H5P.EventDispatcher();

// EventDispatcher extensions

/**
 * Helper function for triggering xAPI added to the EventDispatcher.
 *
 * @param {string} verb
 *   The short id of the verb we want to trigger
 * @param {Oject} [extra]
 *   Extra properties for the xAPI statement
 */
H5P.EventDispatcher.prototype.triggerXAPI = function (verb, extra) {
  this.trigger(this.createXAPIEventTemplate(verb, extra));
};

/**
 * Helper function to create event templates added to the EventDispatcher.
 *
 * Will in the future be used to add representations of the questions to the
 * statements.
 *
 * @param {string} verb
 *   Verb id in short form
 * @param {Object} [extra]
 *   Extra values to be added to the statement
 * @returns {H5P.XAPIEvent}
 *   Instance
 */
H5P.EventDispatcher.prototype.createXAPIEventTemplate = function (verb, extra) {
  var event = new H5P.XAPIEvent();

  event.setActor();
  event.setVerb(verb);
  if (extra !== undefined) {
    for (var i in extra) {
      event.data.statement[i] = extra[i];
    }
  }
  if (!('object' in event.data.statement)) {
    event.setObject(this);
  }
  if (!('context' in event.data.statement)) {
    event.setContext(this);
  }
  return event;
};

/**
 * Helper function to create xAPI completed events
 *
 * DEPRECATED - USE triggerXAPIScored instead
 *
 * @deprecated
 *   since 1.5, use triggerXAPIScored instead.
 * @param {number} score
 *   Will be set as the 'raw' value of the score object
 * @param {number} maxScore
 *   will be set as the "max" value of the score object
 * @param {boolean} success
 *   will be set as the "success" value of the result object
 */
H5P.EventDispatcher.prototype.triggerXAPICompleted = function (score, maxScore, success) {
  this.triggerXAPIScored(score, maxScore, 'completed', true, success);
};

/**
 * Helper function to create scored xAPI events
 *
 * @param {number} score
 *   Will be set as the 'raw' value of the score object
 * @param {number} maxScore
 *   Will be set as the "max" value of the score object
 * @param {string} verb
 *   Short form of adl verb
 * @param {boolean} completion
 *   Is this a statement from a completed activity?
 * @param {boolean} success
 *   Is this a statement from an activity that was done successfully?
 */
H5P.EventDispatcher.prototype.triggerXAPIScored = function (score, maxScore, verb, completion, success) {
  var event = this.createXAPIEventTemplate(verb);
  event.setScoredResult(score, maxScore, this, completion, success);
  this.trigger(event);
};

H5P.EventDispatcher.prototype.setActivityStarted = function() {
  if (this.activityStartTime === undefined) {
    // Don't trigger xAPI events in the editor
    if (this.contentId !== undefined &&
        H5PIntegration.contents !== undefined &&
        H5PIntegration.contents['cid-' + this.contentId] !== undefined) {
      this.triggerXAPI('attempted');
    }
    this.activityStartTime = Date.now();
  }
};

/**
 * Internal H5P function listening for xAPI completed events and stores scores
 *
 * @param {H5P.XAPIEvent} event
 */
H5P.xAPICompletedListener = function (event) {
  if ((event.getVerb() === 'completed' || event.getVerb() === 'answered') && !event.getVerifiedStatementValue(['context', 'contextActivities', 'parent'])) {
    var score = event.getScore();
    var maxScore = event.getMaxScore();
    var contentId = event.getVerifiedStatementValue(['object', 'definition', 'extensions', 'http://h5p.org/x-api/h5p-local-content-id']);
    H5P.setFinished(contentId, score, maxScore);
  }
};
