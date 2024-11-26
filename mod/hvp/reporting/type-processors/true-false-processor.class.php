<?php

/**
 * Class FillInProcessor
 * Processes and generates HTML report for 'fill-in' interaction type.
 */
class TrueFalseProcessor extends TypeProcessor {

  /**
   * Determines options for interaction and generates a human readable HTML
   * report.
   *
   * @inheritdoc
   */
  public function generateHTML($description, $crp, $response, $extras = NULL, $scoreSettings = NULL) {
    // We need some style for our report
    $this->setStyle('styles/true-false.css');

    return $this->getContent($description, $crp, $response, $scoreSettings) .
           $this->generateFooter();
  }

  /**
   * Get report content
   *
   * @param string $description
   * @param array $crp
   * @param string $response
   * @param object $scoreSettings
   *
   * @return string
   */
  private function getContent($description, $crp, $response, $scoreSettings) {
    $isCorrectClass = $response === $crp[0] ?
      'h5p-true-false-user-response-correct' :
      'h5p-true-false-user-response-wrong';

    $header = $this->generateHeader($description, $scoreSettings);

    return
      '<div class="h5p-reporting-container h5p-true-false-container">' .
        $header .
        '<p class="h5p-true-false-task">' .
          '<span class="h5p-true-false-correct-responses-pattern">' . $crp[0] . '</span>' .
          '<span class="' . $isCorrectClass . '">' . $response . '</span>' .
        '</p>' .
      '</div>';
  }

  /**
   * Generate header element
   *
   * @param $description
   * @param $scoreSettings
   *
   * @return string
   */
  private function generateHeader($description, $scoreSettings) {
    $descriptionHtml =
      "<p class='h5p-reporting-description h5p-true-false-task-description'>" .
        "{$description}" .
      "</p>";
    $scoreHtml = $this->generateScoreHtml($scoreSettings);

    return
      "<div class='h5p-choices-header'>" .
        "{$descriptionHtml}{$scoreHtml}" .
      "</div>";
  }

  /**
   * Generate footer,
   *
   * @return string
   */
  function generateFooter() {
    return
      '<div class="h5p-true-false-footer">' .
        '<span class="h5p-true-false-correct-responses-pattern">Correct Answer</span>' .
        '<span class="h5p-true-false-user-response-correct">Your correct answer</span>' .
        '<span class="h5p-true-false-user-response-wrong">Your incorrect answer</span>' .
      '</div>';
  }
}
