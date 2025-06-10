<?php

/**
 * Class FillInProcessor
 * Processes and generates HTML report for 'fill-in' interaction type.
 */
class FillInProcessor extends TypeProcessor {

  /**
   * Placeholder for answers in the description.
   * 10 underscores.
   */
  const FILL_IN_PLACEHOLDER = '__________';

  /**
   * Pattern for separating between different answers in correct responses
   * pattern and in user response.
   */
  const RESPONSES_SEPARATOR = '[,]';

  /**
   * String separator that will be applied between correct responses pattern
   * words
   */
  const CRP_REPORT_SEPARATOR = ' / ';

  /**
   * Support for the Alternatives extension in the x-api event.
   */
  const CONTENT_TYPE_ALTERNATIVES = 'https://h5p.org/x-api/alternatives';

  /**
   * Support the Case Sensitivity extension in the x-api event.
   */
  const CONTENT_TYPE_CASE_SENSITIVITY = 'https://h5p.org/x-api/case-sensitivity';

  /**
   * Determines options for interaction and generates a human readable HTML
   * report.
   *
   * @inheritdoc
   */
  public function generateHTML($description, $crp, $response, $extras, $scoreSettings = NULL) {
    // We need some style for our report
    $this->setStyle('styles/fill-in.css');

    if (isset($extras->extensions->{self::CONTENT_TYPE_CASE_SENSITIVITY}) &&
        isset($extras->extensions->{self::CONTENT_TYPE_ALTERNATIVES})) {
      $caseMatters    = array('caseSensitive' => $extras->extensions->{self::CONTENT_TYPE_CASE_SENSITIVITY});
      $processedCRPs  = $extras->extensions->{self::CONTENT_TYPE_ALTERNATIVES};
    } else {
      // Generate interaction options
      $caseMatters      = $this->determineCaseMatters(empty($crp[0]) ?  '' : $crp[0]);
      
      // Process correct responses
      $processedCRPs    = $this->processCRPs($crp, $caseMatters['nextIndex']);
    }

    // Process user responses patterns
    $processedResponse  = $this->processResponse($response);

    // Build report from description, correct responses and user responses
    $report = $this->buildReportOutput($description,
      $processedCRPs,
      $processedResponse,
      $caseMatters['caseSensitive']
    );

    $header = $this->generateHeader($scoreSettings);
    $longFillIn = property_exists($extras, 'longfillin') ? ' h5p-long-fill-in' : '';
    $container =
      '<div class="h5p-reporting-container h5p-fill-in-container' . $longFillIn . '">' .
        $header . $report .
      '</div>';

    // Footer only required if there is a correct responses pattern
    $footer = (isset($crp)) ? $this->generateFooter() : '';

    return $container . $footer;
  }

  /**
   * Generate header element
   *
   * @param $scoreSettings
   *
   * @return string
   */
  private function generateHeader($scoreSettings) {
    $scoreHtml = $this->generateScoreHtml($scoreSettings);

    return
      "<div class='h5p-fill-in-header'>" .
        $scoreHtml .
      "</div>";
  }

  /**
   * Generate footer
   *
   * @return string
   */
  function generateFooter() {
    return
      '<div class="h5p-fill-in-footer">' .
        '<span class="h5p-fill-in-correct-responses-pattern">Correct Answer</span>' .
        '<span class="h5p-fill-in-user-response-correct">Your correct answer</span>' .
        '<span class="h5p-fill-in-user-response-wrong">Your incorrect answer</span>' .
      '</div>';
  }

  /**
   * Massages correct responses patterns data.
   * The result is a two dimensional array sorted on placeholder order.
   *
   * @param array $crp Correct responses pattern
   * @param number $strStartIndex Start index of actual response pattern.
   * Any data before this index is options applied to the tasks, and should not
   * be processed as part of the correct responses pattern.
   *
   * @return array Two dimensional array.
   *  The first array dimensions is sorted on placeholder order, and the second
   *  separates between correct answer alternatives.
   */
  private function processCRPs($crp, $strStartIndex) {

    // CRPs sorted by placeholder order
    $sortedCRP = array();

    if (!is_array($crp)) {
      return $sortedCRP;
    }

    foreach ($crp as $crpString) {

      // Remove options
      $pattern = substr($crpString, $strStartIndex);

      // Process correct responses pattern into array
      $answers = explode(self::RESPONSES_SEPARATOR, $pattern);
      foreach ($answers as $index => $value) {

        // Create array of correct alternatives at placeholder index
        if (!isset($sortedCRP[$index])) {
          $sortedCRP[$index] = array();
        }

        // Add alternative to placeholder index
        if (!in_array($value, $sortedCRP[$index])) {
          $sortedCRP[$index][] = $value;
        }
      }
    }
    return $sortedCRP;
  }

  /**
   * Determine if interaction answer is case sensitive
   *
   * @param string $singleCRP A correct responses pattern with encoded option
   *
   * @return array Case sensitivity data
   */
  private function determineCaseMatters($singleCRP) {
    $html          = '';
    $nextIndex     = 0;
    $caseSensitive = NULL;

    // Check if interaction has case sensitivity option as first option
    if (strtolower(substr($singleCRP, 1, 13)) === 'case_matters=') {
      if (strtolower(substr($singleCRP, 14, 5)) === 'false') {
        $html          = 'caseSensitive = false';
        $nextIndex     = 20;
        $caseSensitive = FALSE;
      }
      else if (strtolower(substr($singleCRP, 14, 4)) === 'true') {
        $html          = 'caseSensitive = true';
        $nextIndex     = 19;
        $caseSensitive = TRUE;
      }
    }

    return array(
      'html'          => $html,
      'nextIndex'     => $nextIndex,
      'caseSensitive' => $caseSensitive
    );
  }

  /**
   * Build report.
   * Creates a stylable HTML report from description user responses and correct
   * responses.
   *
   * @param string $description
   * @param array $crp
   * @param array $response
   * @param boolean $caseSensitive
   *
   * @return string HTML
   */
  private function buildReportOutput(
    $description, $crp,
    $response, $caseSensitive
  ) {

    // Get placeholder replacements and replace them
    $placeholderReplacements = $this->getPlaceholderReplacements($crp,
      $response,
      $caseSensitive
    );
    return $this->replacePlaceholders($description, $placeholderReplacements);
  }

  /**
   * Process correct responses patterns and user responses and format them to
   * replace placeholders in description.
   *
   * @param array $crp Correct responses patterns
   * @param array $response User responses
   * @param boolean $caseSensitive Case sensitivity of interaction
   *
   * @return array Placeholder replacements
   */
  private function getPlaceholderReplacements($crp, $response, $caseSensitive) {
    $placeholderReplacements = array();

    // Return response without markup if answers are neither right nor wrong
    if (sizeof($crp) === 0) {
      foreach($response as $answer) {
        $placeholderReplacements[] =
          '<span class="h5p-fill-in-user-response h5p-fill-in-user-response-correct h5p-fill-in-no-correct">' .
          nl2br($answer) .
          '</span>';
      }
    }

    foreach ($crp as $index => $value) {

      $currentResponse = isset($response[$index]) ? $response[$index] : '';

      // Determine user response styling
      $isCorrect = $this->isResponseCorrect($currentResponse,
        $value,
        $caseSensitive
      );
      $responseClass = $isCorrect ?
        'h5p-fill-in-user-response-correct' :
        'h5p-fill-in-user-response-wrong';

      // Format the placeholder replacements
      $userResponse =
        '<span class="h5p-fill-in-user-response ' . $responseClass . '">' .
        $currentResponse .
        '</span>';

      $CRPhtml = $this->getCRPHtml($value, $currentResponse, $caseSensitive);

      $correctResponsePattern = '';
      if (strlen($CRPhtml) > 0) {
        $correctResponsePattern .=
          '<span class="h5p-fill-in-correct-responses-pattern">' .
            $CRPhtml .
          '</span>';
      }

      $placeholderReplacements[] = $userResponse . $correctResponsePattern;
    }

    return $placeholderReplacements;
  }

  /**
   * Generate HTML from a single correct response pattern
   *
   * @param array $singleCRP
   * @param string $response User response
   * @param boolean $caseSensitive
   *
   * @return string
   */
  private function getCRPHtml($singleCRP, $response, $caseSensitive) {
    $html = array ();

    foreach ($singleCRP as $index => $value) {

      // Compare lower cases if not case sensitive
      $comparisonCRP = $value;
      $comparisonResponse = $response;
      if (isset($caseSensitive) && $caseSensitive === false) {
        $comparisonCRP = strtolower($value);
        $comparisonResponse = strtolower($response);
      }

      // Skip showing answers that user gave
      if ($comparisonCRP === $comparisonResponse) {
        continue;
      }

      $html[] = $value;
    }

    return join(self::CRP_REPORT_SEPARATOR, $html);
  }

  /**
   * Determine if a user response is correct by matching it with the correct
   * responses pattern.
   *
   * @param string $response User response
   * @param string $crp Correct responses pattern
   * @param boolean $caseSensitive Case sensitivity
   *
   * @return bool True if user response is correct
   */
  private function isResponseCorrect($response, $crp, $caseSensitive) {
    $userResponse    = $response;
    $matchingPattern = $crp;

    // Make user response and matching pattern lower case if case insensitive
    if (isset($caseSensitive) && $caseSensitive === FALSE) {
      $userResponse    = strtolower($response);
      $matchingPattern = array_map('strtolower', $crp);
    }

    return in_array($userResponse, $matchingPattern);
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

  /**
   * Fill in description placeholders with replacements.
   *
   * @param string $description Description
   * @param array $placeholderReplacements Replacements for placeholders in
   * description
   *
   * @return string Description with replaced placeholders
   */
  private function replacePlaceholders($description, $placeholderReplacements) {
    $replacedDescription = $description;

    // Determine position of next placeholder and the corresponding
    // replacement index
    $index   = 0;
    $nextPos = strpos($replacedDescription, self::FILL_IN_PLACEHOLDER, 0);

    while ($nextPos !== FALSE) {
      // Fill in placeholder in description with replacement
      $replacedDescription = substr_replace(
        $replacedDescription,
        $placeholderReplacements[$index],
        $nextPos,
        strlen(self::FILL_IN_PLACEHOLDER)
      );

      // Determine position of next placeholder and the corresponding
      // replacement index
      $nextPos = strpos($replacedDescription, self::FILL_IN_PLACEHOLDER,
        $nextPos + strlen($placeholderReplacements[$index]));
      $index += 1;
    }

    return $replacedDescription;
  }
}
