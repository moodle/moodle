<?php

/**
 * Class GoalsPageProcessor
 * Processes and generates HTML report for Goals Page in Documentation Tool
 */
class GoalsPageProcessor extends TypeProcessor {

  /**
   * Pattern for separating between different answers in correct responses
   * pattern and in user response.
   */
  const RESPONSES_SEPARATOR = '[,]';


  /**
   * Processes xAPI data and returns a human readable HTML report
   *
   * @param string $description Description
   * @param array $crp Correct responses pattern
   * @param string $response User given answer
   * @param object $extras Additional data
   * @param object $scoreSettings Score settings
   *
   * @return string HTML for the report
   */
  function generateHTML($description, $crp, $response, $extras, $scoreSettings) {
    $processedResponse = $this->processResponse($response);
    $report = "<div class='h5p-documentation-tool-header'>{$description}</div>";

    if (empty($response) || empty($processedResponse)) {
      return $report . '<div class="h5p-no-goals">No defined goals</div>';
    }

    $list = '';
    foreach ($processedResponse as $i => $res) {
      $goalCount = $i + 1;
      $index = "<td class='h5p-goal-index'>{$goalCount}.</td>";
      $goal = "<td class='h5p-goal-input'>{$res}</td>";
      $list.= "<tr>{$index}{$goal}</tr>";
    }
    $report .= "<table class='h5p-goal-table'><tbody>{$list}</tbody></table>";
    return $report;
  }

  /**
   * Process response by dividing it into an array on response separators.
   *
   * @param string $response User response
   *
   * @return array List of user responses for the different fill-ins
   */
  private function processResponse($response) {
    return explode(self::RESPONSES_SEPARATOR, $response);
  }
}
