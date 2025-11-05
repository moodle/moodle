/**
 * This function is used to display the EULA for Turnitin quizzes.
 * This should only be called if the user has not accepted the EULA.
 * Hides the quiz start button until the user has accepted the EULA.
 *
 * @copyright Turnitin
 * @author 2025 Jack Milgate <jmilgate@turnitin.com>
 * @module plagiarism_turnitin/quiz_eula
 */

define(['jquery',
        'core/modal',
        'plagiarism_turnitin/quiz_eula'],
function($, Modal, QuizEula) {
  return {
      quizEula: function() {
        // As an additional check to see if we're on the right page and about to start a quiz,
        // we check for the presence of the quiz start button.
        if ($(".quizstartbuttondiv").length == 0) {
          return;
        }

        // Hide the quiz button
        // On eula acceptance the page will be refreshed without this function being called, so the start button will be displayed again
        $(".quizstartbuttondiv").hide();
        $(".quizstartbuttondiv").parent().append($(".pp_turnitin_eula"));
      }
  };
});
