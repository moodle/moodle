<?php

require(__DIR__ . '/../html-purifier/HtmlReportPurifier.php');

/**
 * Class TypeProcessor
 */
abstract class TypeProcessor {

  private $style;

  private $script;

  protected $xapiData;

  protected $disableScoring;

  /**
   * Generate HTML for report
   *
   * @param object $xapiData
   * @param bool $disableScoring Disables scoring
   *
   * @return string HTML as string
   */
  public function generateReport($xapiData, $disableScoring = false, $allowStyles = false) {
    $this->xapiData       = $xapiData;
    $this->disableScoring = $disableScoring;

    // Grab description
    $description = $this->getDescription($xapiData);

    // Grab correct response pattern
    $crp = $this->getCRP($xapiData);

    // Grab extras
    $extras        = $this->getExtras($xapiData);
    $scoreSettings = $this->getScoreSettings($xapiData);

    return HtmlReportPurifier::filter_xss($this->generateHTML(
      $description,
      $crp,
      $this->getResponse($xapiData),
      $extras,
      $scoreSettings
    ), array(
        'a', 'b', 'button', 'br', 'code', 'col', 'colgroup', 'dd', 'div', 'dl',
        'dt', 'em', 'figcaption', 'figure', 'footer', 'h1', 'h2', 'h3',
        'h4', 'h5', 'h6', 'header', 'hgroup', 'i', 'img', 'ins', 'li',
        'menu', 'meter', 'nav', 'ol', 'p', 's', 'section', 'span', 'strong',
        'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th',
        'thead', 'time', 'tr', 'tt', 'u', 'ul')
    , $allowStyles);
  }

  /**
   * Get score settings
   *
   * @param object $xapiData
   *
   * @return object Score settings
   */
  protected function getScoreSettings($xapiData) {
    $scoreSettings = (object) [];

    if (!isset($xapiData->raw_score) || !isset($xapiData->max_score)) {
      return $scoreSettings;
    }

    // Grab scores and score labels
    $scoreSettings->rawScore = $xapiData->raw_score;
    $scoreSettings->maxScore = $xapiData->max_score;

    $scoreSettings->scoreLabel = 'Score:';
    if (isset($xapiData->score_label)) {
      $scoreSettings->scoreLabel = $xapiData->score_label;
    }

    $scoreSettings->scoreDelimiter = 'out of';
    if (isset($xapiData->score_delimiter)) {
      $scoreSettings->scoreDelimiter = $xapiData->score_delimiter;
    }

    $scoreSettings->scaledScoreDelimiter = ',';
    if (isset($xapiData->scaled_score_delimiter)) {
      $scoreSettings->scaledScoreDelimiter = $xapiData->scaled_score_delimiter;
    }

    // Scaled score
    if (isset($xapiData->score_scale)) {
      $scoreSettings->scoreScale = $xapiData->score_scale;

      $scoreSettings->scaledScoreLabel = 'Scaled score:';
      if (isset($xapiData->score_label)) {
        $scoreSettings->scaledScoreLabel = $xapiData->scaled_score_label;
      }

      // Send data on scaled scores and parent max score for dynamic grading
      if (isset($xapiData->scaled_score_per_score)) {
        $scoreSettings->scaledScorePerScore = $xapiData->scaled_score_per_score;
      }

      if (isset($xapiData->parent_max_score)) {
        $scoreSettings->parentMaxScore = $xapiData->parent_max_score;
      }
    }

    $scoreSettings->questionsRemainingLabel = 'questions remaining to grade';
    if (isset($xapiData->questions_remaining_label)) {
      $scoreSettings->questionsRemainingLabel = $xapiData->questions_remaining_label;
    }

    $scoreSettings->submitButtonLabel = 'Submit grade';
    if (isset($xapiData->submit_button_label)) {
      $scoreSettings->submitButtonLabel = $xapiData->submit_button_label;
    }

    $scoreSettings->reportingScoreLabel = 'Score';
    if (isset($xapiData->reportingScoreLabel)) {
      $scoreSettings->reportingScoreLabel = $xapiData->reportingScoreLabel;
    }

    $scoreSettings->IVOpenEndedQuestionTitle = 'Free Text Question';
    if (isset($xapiData->IVOpenEndedQuestionTitle)) {
      $scoreSettings->IVOpenEndedQuestionTitle = $xapiData->IVOpenEndedQuestionTitle;
    }

    return $scoreSettings;
  }

  /**
   * Generate score html
   *
   * @param object $scoreSettings Score settings
   *
   * @return string Score html
   */
  protected function generateScoreHtml($scoreSettings) {
    $showScores = isset($scoreSettings->rawScore)
                  && isset($scoreSettings->maxScore)
                  && !$this->disableScoring;

    if (!$showScores) {
      return '';
    }

    // Generate html for score
    $scoreLabel     = $scoreSettings->scoreLabel;
    $scoreDelimiter = $scoreSettings->scoreDelimiter;
    $scaleDelimiter = '';

    // Generate html for scaled score
    $scaledHtml = "";
    if (isset($scoreSettings->scoreScale)) {
      $scaleDelimiter = $scoreSettings->scaledScoreDelimiter;
      $scaledHtml =
        "<div class='h5p-reporting-scaled-container'>" .
          "<span class='h5p-reporting-scaled-label'>{$scoreSettings->scaledScoreLabel}</span>" .
          "<span class='h5p-reporting-scaled-score'>{$scoreSettings->scoreScale}</span>" .
        "</div>";
    }

    $scoreHtml =
      "<div class='h5p-reporting-score-container'>" .
        "<span class='h5p-reporting-score-label'>{$scoreLabel}</span>" .
        "<span class='h5p-reporting-score'>" .
          "<span class='h5p-reporting-raw-score'>" .
            $scoreSettings->rawScore .
          "</span>" .
          " " . $scoreDelimiter . " " .
          $scoreSettings->maxScore . $scaleDelimiter .
        "</span>" .
      "</div>";

    $html = "<div class='h5p-reporting-score-wrapper'>{$scoreHtml}{$scaledHtml}</div>";

    return $html;
  }

  /**
   * Decode extras from xAPI data.
   *
   * @param stdClass $xapiData
   *
   * @return stdClass
   */
  protected function getExtras($xapiData) {
    $extras = ($xapiData->additionals === '' ? new stdClass() : json_decode($xapiData->additionals));
    if (isset($xapiData->children)) {
      $extras->children = $xapiData->children;
    }

    // Send the content id to the view for dynamic grading
    if (isset($xapiData->id)) {
      $extras->subcontent_id = $xapiData->id;
    }

    return $extras;
  }

  /**
   * Decode and retrieve 'en-US' description from xAPI data.
   *
   * @param stdClass $xapiData
   *
   * @return string Description as a string
   */
  protected function getDescription($xapiData) {
    return $xapiData->description;
  }

  /**
   * Decode and retrieve Correct Responses Pattern from xAPI data.
   *
   * @param stdClass $xapiData
   *
   * @return array Correct responses pattern as an array
   */
  protected function getCRP($xapiData) {
    return json_decode($xapiData->correct_responses_pattern, true);
  }

  /**
   * Decode and retrieve user response from xAPI data.
   *
   * @param stdClass $xapiData
   *
   * @return string User response
   */
  protected function getResponse($xapiData) {
    return $xapiData->response;
  }

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
  abstract function generateHTML($description, $crp, $response, $extras, $scoreSettings);

  /**
   * Set style used by the processor.
   *
   * @param string $style Path to style
   */
  protected function setStyle($style) {
    $this->style = $style;
  }

  /**
   * Get style used by processor if used.
   *
   * @return string Library relative path to CSS
   */
  public function getStyle() {
    return $this->style;
  }

  /**
   * Set script used by the processor.
   *
   * @param string $script Path to script
   */
  protected function setScript($script) {
    $this->script = $script;
  }

  /**
   * Get script used by processor.
   *
   * @return string|null Path to script
   */
  public function getScript() {
    return $this->script;
  }
}
