<?php

/**
 * Class H5PReport
 * @property  fillInProcessor
 */
class H5PReport {

  private static $version = '1.1.0';

  private static $processorMap = array(
    'compound' => 'CompoundProcessor',
    'fill-in' => 'FillInProcessor',
    'long-fill-in' => 'FillInProcessor',
    'true-false' => 'TrueFalseProcessor',
    'matching' => 'MatchingProcessor',
    'choice' => 'ChoiceProcessor',
    'long-choice' => 'LongChoiceProcessor',
  );

  private static $versionExtension = 'https://h5p.org/x-api/h5p-reporting-version';
  private static $contentTypeExtension = 'https://h5p.org/x-api/h5p-machine-name';

  public static $contentTypeProcessors = array(
    'H5P.DocumentationTool' => 'DocumentationToolProcessor',
    'H5P.GoalsPage' => 'GoalsPageProcessor',
    'H5P.GoalsAssessmentPage' => 'GoalsAssessmentPageProcessor',
    'H5P.StandardPage' => 'StandardPageProcessor',
    'H5P.FreeTextQuestion' => 'IVOpenEndedQuestionProcessor',
  );

  private $processors = array();

  /**
   * Generate the proper report depending on xAPI data.
   *
   * @param object $xapiData
   * @param string $forcedProcessor Force a processor type
   * @param bool $disableScoring Disables scoring for the report
   *
   * @return string A report
   */
  public function generateReport($xapiData, $forcedProcessor = null, $disableScoring = false) {
    $interactionType = $xapiData->interaction_type;
    if (!self::isSupportedVersion($xapiData)) {
      return self::renderUnsupportedVersionPage($xapiData);
    }

    $contentTypeProcessor = self::getContentTypeProcessor($xapiData);
    if (isset($contentTypeProcessor)) {
      $interactionType = $contentTypeProcessor;
    }

    if (isset($forcedProcessor)) {
      $interactionType = $forcedProcessor;
    }

    if (!isset(self::$processorMap[$interactionType]) && !isset(self::$contentTypeProcessors[$interactionType])) {
      return ''; // No processor found
    }

    if (!isset($this->processors[$interactionType])) {
      // Not used before. Initialize new processor
      if (array_key_exists($interactionType, self::$contentTypeProcessors)) {
        $this->processors[$interactionType] = new self::$contentTypeProcessors[$interactionType]();
      }
      else {
        $this->processors[$interactionType] = new self::$processorMap[$interactionType]();
      }
    }

    // Generate and return report from xAPI data
    // Allow compound content types to have styles in case they are rendering gradable containers
    return $this->processors[$interactionType]
      ->generateReport($xapiData, $disableScoring, ($interactionType == "compound" ? true : false));
  }

  /**
   * Generate the proper report for dynamically gradable content types depending on xAPI data.
   *
   * @param object $xapiData
   * @return string A report
   */
  public function generateGradableReports($xapiData) {
    $results = array();

    foreach ($xapiData as $childData) {
     $interactionType = self::getContentTypeProcessor($childData);

     if (!isset($this->processors[$interactionType])) {
       // Not used before. Initialize new processor
       if (array_key_exists($interactionType, self::$contentTypeProcessors)) {
         $this->processors[$interactionType] = new self::$contentTypeProcessors[$interactionType]();
       }
     }

     if ($interactionType == 'H5P.FreeTextQuestion') {
       array_push($results, $childData);
     }
    }

    if (count($results) > 0) {
      return self::buildContainer($results);
    }

    // Return nothing if there are no reports
    return ' ';
  }

  /**
   * Generate the wrapping element for a grading container
   *
   * @param object $results
   *
   * @return string HTML of the container and within it, gradable elements
   */
  private function buildContainer($results) {
    $container = '<div id="gradable-container" class="h5p-iv-open-ended-grading-container">';

    foreach ($results as $index=>$child) {
      $container .= self::buildChild($child, $index);
    }

    $container .= '</div>';

    return $container;
  }

  /**
   * Generate each of the gradable elements
   *
   * @param object $data
   * @param int $index
   *
   * @return string HTML of a gradable element
   */
  private function buildChild($data, $index) {
    // Generate and return report from xAPI data
    $interactionType = self::getContentTypeProcessor($data);
    return $this->processors[$interactionType]
      ->generateReport($data, false, true);
  }

  /**
   * Removes gradable children from xAPI data
   *
   * @return array
   */
  public function stripGradableChildren($xapiData) {
    return array_filter($xapiData, function ($data) {
      $contentTypeProcessor = H5PReport::getContentTypeProcessor($data);
      $interactionType = $contentTypeProcessor;
      return $interactionType !== 'H5P.FreeTextQuestion';
    });
  }

  /**
   * List of CSS stylesheets used by the processors when rendering the report.
   *
   * @return array
   */
  public function getStylesUsed() {
    $styles = array(
      'styles/shared-styles.css'
    );

    // Fetch style used by each report processor
    foreach ($this->processors as $processor) {
      $style = $processor->getStyle();
      if (!empty($style)) {
        $styles[] = $style;
      }
    }

    return $styles;
  }


  /**
   * List of JS scripts to be used by the processors when rendering the report.
   *
   * @return array
   */
  public function getScriptsUsed() {
    $scripts = [];

    // Fetch scripts used by each report processor
    foreach ($this->processors as $processor) {
      $script = $processor->getScript();
      if (!empty($script)) {
        $scripts[] = $script;
      }
    }

    return $scripts;
  }

  /**
   * Caches instance of report generator.
   * @return \H5PReport
   */
  public static function getInstance() {
    static $instance;

    if (!$instance) {
      $instance = new H5PReport();
    }

    return $instance;
  }

  /**
   * Attempts to retrieve content type processor from xapi data
   * @param object $xapiData
   *
   * @return string|null Content type processor
   */
  public static function getContentTypeProcessor($xapiData) {
    if (!isset($xapiData->additionals)) {
      return null;
    }

    $extras = json_decode($xapiData->additionals);

    if (!isset($extras->extensions) || !isset($extras->extensions->{self::$contentTypeExtension})) {
      return null;
    }

    $processor = $extras->extensions->{self::$contentTypeExtension};
    if (!array_key_exists($processor, self::$contentTypeProcessors)) {
      return null;
    }

    return $processor;
  }

    /**
     * Get required reporting module version from statement
     *
     * @param $xapiData
     *
     * @return string Defaults to 1.0.0
     */
  public static function getVersion($xapiData) {
    if (!isset($xapiData->additionals)) {
      return '1.0.0';
    }

    $additionals = json_decode($xapiData->additionals);
    if (!isset($additionals->contextExtensions->{self::$versionExtension})) {
      return '1.0.0';
    }

    return $additionals->contextExtensions->{self::$versionExtension};
  }

    /**
     * Check is render report from statement is supported
     *
     * @param $xapiData
     *
     * @return bool
     */
  public static function isSupportedVersion($xapiData) {
    $reportingVersion = array_map('intval', explode('.', self::$version));
    $statementVersion = array_map('intval', explode('.', self::getVersion($xapiData)));

    // Sanitation
    if (!count($statementVersion) === 3) {
      return false;
    }

    // Check major version
    if ($reportingVersion[0] < $statementVersion[0]) {
      return false;
    }

    // Check minor version
    $hasOutdatedMinorVersion = $reportingVersion[0] === $statementVersion[0]
                               && $reportingVersion[1] < $statementVersion[1];
    if ($hasOutdatedMinorVersion) {
      return false;
    }

    // Patch versions are assumed to be compatible
    return true;
  }

    /**
     * Display message saying that report could not be rendered
     *
     * @param $xapiData
     *
     * @return string
     */
  public static function renderUnsupportedVersionPage($xapiData) {
    $requiredVersion = self::getVersion($xapiData);
    $installedVersion = self::$version;
    return "<div>Version {$requiredVersion} of the reporting module is required to render this report. Currently installed: {$installedVersion}</div>";
  }
}
