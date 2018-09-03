<?php

/**
 * Class DocumentationToolProcessor
 * Processes and generates HTML report for Documentation Tool H5Ps
 */
class DocumentationToolProcessor extends TypeProcessor {

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
    $this->setStyle('styles/documentation-tool.css');

    $H5PReport = H5PReport::getInstance();
    $reports   = '';

    if (isset($extras->children)) {
      foreach ($extras->children as $childData) {
        $reports .= $H5PReport->generateReport($childData, null, $this->disableScoring);
      }
    }

    // Do not display description when children is empty
    if (!empty($reports) && !empty($description)) {
      $reports =
        '<p class="h5p-reporting-description h5p-compound-task-description">' .
        $description .
        '</p>' .
        $reports;
    }

    return '<div class="h5p-reporting-container h5p-compound-container">' .
           '<div class="h5p-result">' .
           $reports .
           '</div>' .
           '</div>';
  }
}
