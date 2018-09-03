<?php

/**
 * Class StandardPageProcessor
 * Processes and generates HTML report for Stanrd Page in Documentation Tool
 */
class StandardPageProcessor extends TypeProcessor {


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
    if (!isset($extras->children) || empty($extras->children)) {
      return '';
    }

    $report = "<div class='h5p-documentation-tool-header'>{$description}</div>";

    foreach ($extras->children as $child) {
      if (!isset($child->description) || !isset($child->response)) {
        continue;
      }

      $title = "<div class='h5p-standard-page-title'>{$child->description}</div>";

      $answer = empty($child->response) ? '-' : $child->response;

      $response = "<div class='h5p-standard-page-response'>{$answer}</div>";

      $report .= "<div class='h5p-standard-page-container'>{$title}{$response}</div>";
    }

    return "<div class='h5p-standard-page'>{$report}</div>";
  }
}
