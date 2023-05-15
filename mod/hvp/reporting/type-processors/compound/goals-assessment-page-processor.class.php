<?php

/**
 * Class GoalsAssessmentPageProcessor
 * Processes and generates HTML report for Goals Assessment Page in Documentation Tool
 */
class GoalsAssessmentPageProcessor extends TypeProcessor {


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
    $report = "<div class='h5p-documentation-tool-header'>{$description}</div>";
    if (!isset($extras->children) || empty($extras->children)) {
      return $report . '<div class="h5p-no-goals">No defined goals</div>';
    }

    $report .= $this->getGoalAssessmentAlternatives($extras->children[0]);

    $list = '';
    foreach ($extras->children as $index => $choice) {
      $list .= $this->getGoalAssessment($index, $choice);
    }
    $report .= "<table class='h5p-goal-table'><tbody>{$list}</tbody></table>";

    return $report;
  }


  /**
   * HTML for how well a goal was achieved
   * @param object $choice
   *
   * @return string HTML for rendering alternatives
   */
  private function getGoalAssessmentAlternatives($choice) {
    if (!isset($choice->additionals)) {
      return '';
    }

    $additionals = json_decode($choice->additionals);
    if (!isset($additionals->choices)) {
      return '';
    }

    $assessmentAlternatives = "";
    foreach ($additionals->choices as $index => $c) {
      if (!isset($c->description)) {
        continue;
      }

      $classes = "class='h5p-goal-assessment-choice h5p-goal-assessment-choice-{$index}'";
      $assessmentAlternatives .= "<div {$classes}>{$c->description->{'en-US'}}</div>";
    }

    return "<div class='h5p-goal-assessment-alt-container'>{$assessmentAlternatives}</div>";
  }


  /**
   * HTML for rendering a row in the goal assessment table.
   *
   * @param int $index Index of the goal that was assessed
   * @param object $choice The assessment data
   *
   * @return string HTML for row in table
   */
  private function getGoalAssessment($index, $choice) {
    if (!isset($choice->response) || !isset($choice->description)) {
      return '';
    }
    $goalCount = $index + 1;
    $goalCounter = "<td class='h5p-goal-index'>{$goalCount}.</td>";
    $goal = "<td class='h5p-goal-assessment-text'>{$choice->description}</td>";
    $icon = "<td class='h5p-goal-assessment-choice h5p-goal-assessment-choice-{$choice->response}' />";

    return "<tr>{$goalCounter}{$goal}{$icon}</tr>";
  }
}
