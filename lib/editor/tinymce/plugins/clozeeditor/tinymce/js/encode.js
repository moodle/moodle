// JavaScript Document

// Determine whether the actual test is of type "numerical", or any other
function isNumerical() {
    if (getQuizTypeElement().value == 'NUMERICAL') {
        return true;
    } else {
        return false;
    }
}

var i = 0;

// Create answers array where all answers will be saved
var answers = new Array();
var outputCache, throttle_value;

// Ultimate encoding function which does all the calculations upon hitting the "encode" button
function encode() {
    // create new answers object in the array for all filled inputs
    for (var i = 1; i <= countFilledInputs(); i++) {
        answers[i] = new Object();
        // fill array, and use getXxxElement function to address it
        answers[i]['answer']    = getAnswerElement(i).value;
        answers[i]['percent']   = getPercentElement(i).value;
        if (isNumerical()) {
            // add ':' in front of throttle values (Moodle's Cloze Quiz Coding Style)
            answers[i]['throttle']  = ':' + getThrottleElement(i).value;
        } else {
            answers[i]['throttle']  = '';
        }

        answers[i]['feedback']  = getFeedbackElement(i).value;
    }

    // reset  before filling it again
    // document.Formular.output.value = "";
    outputCache = "";

    // create output for all filled input fields
    for (i = 1; i <= countFilledInputs(); i++) {
        // Why did we add "add_correct"?
        // var add_correct = '';

        // trim all texts to avoid vacuous white spaces
        answers[i]['answer']    = trim(answers[i]['answer']);
        answers[i]['percent']   = trim(answers[i]['percent']);

        answers[i]['feedback']  = trim(answers[i]['feedback']);

        // if it's not the first answer, then add '~' as delimeter between answers
        if (outputCache != '') {
            outputCache = outputCache + '~';
        }

        // var el = getPercentElement(i);
        // Check whether a percent value is above 100%, which should never happen
        if (typeof(getPercentElement(i).value) != 'undefined') {
            if (getPercentElement(i).value > 100) {
                getPercentElement(i).value = 100;
            }
        }

        outputCache = outputCache +
                    '%' +
                    getPercentElement(i).value +
                    '%' +
                    // add_correct +     // looks like this was vacuous
                    answers[i]['answer'] +
                    answers[i]['throttle'] +
                    '#' +
                    answers[i]['feedback'];
    }

    // Finally build the complete question

    outputCache = '{' +
                  document.Formular.weighting.value +
                  ':' +
                  getQuizTypeFromInput() +
                  ':' +
                  stripslashes(outputCache) +
                  '}';

    return outputCache;
}
