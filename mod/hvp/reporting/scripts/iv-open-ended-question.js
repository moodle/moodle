document.addEventListener("DOMContentLoaded", function() {
  var container = document.getElementById("gradable-container");
  var questions = [];
  var questionElements = container.querySelectorAll('.h5p-iv-open-ended-reporting-container');
  var inputs = [];
  var maxScores = {};
  var index = 0;
  var currentQuestion = questionElements[0];

  // Translatable labels
  var IVOpenEndedQuestionTitle;
  var scoreLabel;
  var scoreDelimiter;
  var questionsRemainingLabel;
  var submitButtonLabel;

  // Render each of the question containers
  for (var i = 0; i < questionElements.length; i++) {
    IVOpenEndedQuestionTitle = questionElements[i].getAttribute('data-report-iv-open-ended-question-title');
    scoreLabel = questionElements[i].getAttribute('data-report-score-label');
    scoreDelimiter = questionElements[i].getAttribute('data-report-score-delimiter');
    questionsRemainingLabel = questionElements[i].getAttribute('data-report-questions-remaining-label');
    submitButtonLabel = questionElements[i].getAttribute('data-report-submit-button-label');

    // Add the title to an existing div in the the header
    addTitle(i);

    // Add other elements to the header
    var header = document.getElementById('h5p-iv-open-ended-reporting-header-' + i);

    var gradeInputWrapper = document.createElement('div');
    gradeInputWrapper.classList.add('h5p-iv-open-ended-reporting-grade-input-wrapper');

    var inputDiv = createInputDiv(questionElements[i], i);
    gradeInputWrapper.append(inputDiv);

    var submitButtonWrapper = createSubmitButtonWrapper(i);
    gradeInputWrapper.append(submitButtonWrapper);

    header.append(gradeInputWrapper);

    // Add the header to the question container
    questionElements[i].prepend(header);

    // Keep track of the elements created and general data for later use
    questions[i] = {};
    questions[i] = {
      'element': questionElements[i],
      'inputDiv': inputDiv,
      'submitButton': submitButtonWrapper,
      'gradebookContainer': questionElements[i].querySelectorAll('.h5p-iv-open-ended-reporting-scores')[0]
    };
  }

  updateMainGradeBookContainer();

  /**
   * Add a title
   *
   * @param {number} index
   * @return {null}
   */
  function addTitle(index) {
    var titleCounter = document.createElement('div');
    titleCounter.classList.add('h5p-iv-open-ended-title-counter');
    titleCounter.innerHTML = IVOpenEndedQuestionTitle + ' ' + '<span>' + (index + 1) + ' ' + scoreDelimiter + ' ' + questionElements.length + '</span>';

    var titleWrapper = document.getElementById('h5p-iv-open-ended-reporting-title-wrapper-' + index);
    titleWrapper.prepend(titleCounter);
  }

  /**
   * Create submit button wrapper
   * @param {number} index
   * @return {HTMLElement}
   */
  function createSubmitButtonWrapper(index) {
    var submitButtonWrapper = document.createElement('div');
    submitButtonWrapper.classList.add('h5p-iv-open-ended-reporting-submit-button-wrapper');

    var submitButton = document.createElement('button');
    submitButton.classList.add('h5p-iv-open-ended-reporting-submit-button');
    submitButton.id = 'h5p-iv-open-ended-reporting-submit-button-' + index;
    submitButton.innerHTML = submitButtonLabel;

    submitButtonWrapper.append(submitButton);
    return submitButtonWrapper;
  }

  /**
   * Create input wrapper
   * @param {HTMLElement} wrapper
   * @param {number} index
   * @return {HTMLElement}
   */
  function createInputDiv(wrapper, index) {
    var inputDiv = document.createElement('div');
    inputDiv.classList.add('h5p-iv-open-ended-input-div');

    var scoreText = document.createElement('span');
    scoreText.innerHTML = scoreLabel + ': ';
    inputDiv.append(scoreText);

    var input = document.createElement('input');
    input.setAttribute('type', 'number');
    input.id = 'h5p-grade-input-' + index;
    input.subcontentID = wrapper.getAttribute('data-report-id');
    input.scaleFactor = wrapper.getAttribute('data-report-scale');
    input.maxScore = wrapper.getAttribute('data-report-max');
    inputDiv.append(input);

    var maxScoreText = document.createElement('span');
    maxScoreText.id = 'h5p-max-score-text-' + index;
    inputDiv.append(maxScoreText);

    inputs.push(input);
    return inputDiv;
  }

  // Add logic to the inputs
  inputs.forEach(function(input, index) {
    // Populate the inputs with existing questionElements
    populateInputDiv(input.subcontentID, index);

    input.addEventListener('focus', function() {
      var submitButton = document.getElementById('h5p-iv-open-ended-reporting-submit-button-' + index);
      submitButton.disabled = false;
    });

    // Validate on blur
    input.addEventListener('blur', function() {
      if (this.value == '' || parseInt(this.value) < 0) {
        this.value = 0;
      }

      if (parseInt(this.value) > parseInt(maxScores[index])) {
        this.value = maxScores[index];
      }
    });

    // Add logic for the corresponding submit button
    var submitButton = document.getElementById('h5p-iv-open-ended-reporting-submit-button-' + index);
    submitButton.addEventListener('click', function() {

      // Validate on submit again since blur doesn't always work
      if (this.value == '' || parseInt(this.value) < 0) {
        this.value = 0;
      }

      if (parseInt(this.value) > parseInt(maxScores[index])) {
        this.value = maxScores[index];
      }

      H5P.jQuery.post(data_for_page.setSubContentEndpoint, {
        subcontent_id: input.subcontentID,
        score: input.value,
        maxScore: input.maxScore
      }, function(response) {
        renderAfterSubmit(input, index, response.data.totalUngraded)
      });
    });
  });

  /**
   * rerenders elements with new data
   * @param {HTMLElement} input
   * @param {number} index
   * @param {number} totalUngraded
   */
  function renderAfterSubmit(input, index, totalUngraded) {
    hideInputs(index);

    // Update the gradebook score for this question and for the main content type
    updateGradeBookContainer(index, input.value, input.scaleFactor);
    updateMainGradeBookContainer();
    updateQuestionCounter(totalUngraded);
  }

  /**
   * hide inputs
   *
   * @param {number} index
   */
  function hideInputs(index) {
    questions[index].submitButton.classList.add('h5p-iv-open-ended-reporting-hidden');
    questions[index].inputDiv.classList.add('h5p-iv-open-ended-reporting-hidden');
  }


  /**
   * Updates the gradebook scores for a particular question
   *
   * @param {number} index
   * @param {number} inputValue
   * @param  {float} scaleFactor
   */
  function updateGradeBookContainer(index, inputValue, scaleFactor) {
    var scoreContainer = document.getElementById('h5p-iv-open-ended-reporting-score-' + index);
    scoreContainer.classList.remove('h5p-iv-open-ended-reporting-hidden');

    var scaledScoreElement = scoreContainer.querySelectorAll('.h5p-reporting-scaled-score')[0];
    scaledScoreElement.innerHTML = Math.round((scaleFactor * inputValue) * 100 + Number.EPSILON) / 100;

    var rawScoreElement = scoreContainer.querySelectorAll('.h5p-reporting-raw-score')[0];
    rawScoreElement.innerHTML = inputValue;
  }


  /**
   * Updates the values of the main gradebook container for the containing content type
   */
  function updateMainGradeBookContainer() {
    // Only look within the same report
    var thisReport = findAncestor(container, '.h5p-reporting-main-container');
    var thisReportView = findAncestor(container, '.h5p-report-view');
    var rawScores = thisReport.querySelectorAll('.h5p-reporting-raw-score');
    rawScores = Array.prototype.slice.call(rawScores).map(function(rawScoreElement) {
      return parseInt(rawScoreElement.innerHTML);
    });
    var rawScore = rawScores.reduce(function(a,b) {
      return a + b;
    });
    var mainRawScoreElement = thisReport.querySelectorAll('.h5p-reporting-main-score-raw-score')[0];
    mainRawScoreElement.innerHTML = rawScore;

    // Update the gradebook score
    var scaledScores = thisReportView.querySelectorAll('.h5p-reporting-scaled-score');
    scaledScores = Array.prototype.slice.call(scaledScores).map(function(scaledScoreElement) {
      return parseFloat(scaledScoreElement.innerHTML);
    });

    var scaledScore = scaledScores.reduce(function(a,b) {
      return a + b;
    });

    var scaledScoreElement = thisReport.querySelectorAll('.h5p-reporting-main-score-scaled-score')[0];
    scaledScoreElement.innerHTML = Number((scaledScore).toFixed(2));
  }


  /**
   * Renders the input div
   *
   * @param {number} subcontentID
   * @param {number} index
   */
  function populateInputDiv(subcontentID, index) {
    H5P.jQuery.get(data_for_page.getSubContentEndpoint, {subcontent_id : subcontentID}, function(response) {
      var loadedInput = document.getElementById('h5p-grade-input-' + index);
      loadedInput.value = response.data.score;

      var loadedMaxScore = document.getElementById('h5p-max-score-text-' + index);
      loadedMaxScore.innerHTML = scoreDelimiter + ' ' + response.data.maxScore;
      maxScores[index] = response.data.maxScore;

      // Disable buttons if the content type hasn't been graded yet
      if (response.data.score === null) {
        var submitButton = document.getElementById('h5p-iv-open-ended-reporting-submit-button-' + index);
        submitButton.disabled = true;
      }
      else {
        // Hide input div and show grade container if it already has been graded
        hideInputs(index);
        updateGradeBookContainer(index, loadedInput.value, loadedInput.scaleFactor);
      }

      updateQuestionCounter(response.data.totalUngraded);
    });
  }


  /**
   * Updates the remanining question counter
   *
   * @param {number} totalUngraded
   */
  function updateQuestionCounter(totalUngraded) {
    if (totalUngraded > 0) {
      container.querySelectorAll('.h5p-iv-open-ended-reporting-question-counter').forEach(function(questionCounter) {
        questionCounter.innerHTML = '<span>' + totalUngraded + ' ' + questionsRemainingLabel + '</span>';
      });
    } else {
      container.querySelectorAll('.h5p-iv-open-ended-reporting-question-counter').forEach(function(questionCounter) {
        questionCounter.innerHTML = '<span>All questions have been graded</span>';
        questionCounter.classList.add('reporting-completed');
      });
    }
  }

  // Initialize buttons
  container.querySelectorAll('.h5p-iv-open-ended-previous').forEach(function(button, index) {
    button.addEventListener("click", showPreviousQuestion);

    // Disable the first previous button
    if (index === 0) {
      button.disabled = true;
    }
  });

  container.querySelectorAll('.h5p-iv-open-ended-next').forEach(function(button, index) {
    button.addEventListener("click", showNextQuestion);

    // Disable the last next button
    if (index === container.querySelectorAll('.h5p-iv-open-ended-next').length - 1) {
      button.disabled = true;
    }
  });

  // Add logic to the 'Change grade' button to show the input again
  container.querySelectorAll('.h5p-iv-open-ended-reporting-change-grade').forEach(function(button) {
    var index = button.getAttribute('data-report-id');
    button.addEventListener('click', function() {
      questions[index].inputDiv.classList.remove('h5p-iv-open-ended-reporting-hidden');
      questions[index].submitButton.classList.remove('h5p-iv-open-ended-reporting-hidden');
      questions[index].gradebookContainer.classList.add('h5p-iv-open-ended-reporting-hidden');
    });
  });

  /**
   * showPreviousQuestion
   */
  function showPreviousQuestion() {
    // If we are on the first question don't do anything
    if (index === 0) {
      return;
    }
    currentQuestion.classList.add('h5p-iv-open-ended-reporting-hidden');

    index -= 1;
    currentQuestion = questionElements[index];
    currentQuestion.classList.remove('h5p-iv-open-ended-reporting-hidden');
  }

  /**
   * showNextQuestion
   */
  function showNextQuestion() {
    if (index == questionElements.length - 1) {
      return;
    }
    currentQuestion.classList.add('h5p-iv-open-ended-reporting-hidden');

    index += 1;
    currentQuestion = questionElements[index];
    currentQuestion.classList.remove('h5p-iv-open-ended-reporting-hidden');
  }

  /**
   * Finds an ancestor that matches a selector
   *
   * @param  {type} el
   * @param  {type} sel
   * @return {HTMLElement} element to be returned
   */
  function findAncestor (el, sel) {
    while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el,sel)));
    return el;
  }
});
