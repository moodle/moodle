<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\PagespeedInsights;

class LighthouseAuditResultV5 extends \Google\Model
{
  /**
   * The description of the audit.
   *
   * @var string
   */
  public $description;
  /**
   * Freeform details section of the audit.
   *
   * @var array[]
   */
  public $details;
  /**
   * The value that should be displayed on the UI for this audit.
   *
   * @var string
   */
  public $displayValue;
  /**
   * An error message from a thrown error inside the audit.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * An explanation of the errors in the audit.
   *
   * @var string
   */
  public $explanation;
  /**
   * The audit's id.
   *
   * @var string
   */
  public $id;
  protected $metricSavingsType = MetricSavings::class;
  protected $metricSavingsDataType = '';
  /**
   * The unit of the numeric_value field. Used to format the numeric value for
   * display.
   *
   * @var string
   */
  public $numericUnit;
  /**
   * A numeric value that has a meaning specific to the audit, e.g. the number
   * of nodes in the DOM or the timestamp of a specific load event. More
   * information can be found in the audit details, if present.
   *
   * @var 
   */
  public $numericValue;
  /**
   * The score of the audit, can be null.
   *
   * @var array
   */
  public $score;
  /**
   * The enumerated score display mode.
   *
   * @var string
   */
  public $scoreDisplayMode;
  /**
   * The human readable title.
   *
   * @var string
   */
  public $title;
  /**
   * Possible warnings that occurred in the audit, can be null.
   *
   * @var array
   */
  public $warnings;

  /**
   * The description of the audit.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Freeform details section of the audit.
   *
   * @param array[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return array[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The value that should be displayed on the UI for this audit.
   *
   * @param string $displayValue
   */
  public function setDisplayValue($displayValue)
  {
    $this->displayValue = $displayValue;
  }
  /**
   * @return string
   */
  public function getDisplayValue()
  {
    return $this->displayValue;
  }
  /**
   * An error message from a thrown error inside the audit.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * An explanation of the errors in the audit.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * The audit's id.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The metric savings of the audit.
   *
   * @param MetricSavings $metricSavings
   */
  public function setMetricSavings(MetricSavings $metricSavings)
  {
    $this->metricSavings = $metricSavings;
  }
  /**
   * @return MetricSavings
   */
  public function getMetricSavings()
  {
    return $this->metricSavings;
  }
  /**
   * The unit of the numeric_value field. Used to format the numeric value for
   * display.
   *
   * @param string $numericUnit
   */
  public function setNumericUnit($numericUnit)
  {
    $this->numericUnit = $numericUnit;
  }
  /**
   * @return string
   */
  public function getNumericUnit()
  {
    return $this->numericUnit;
  }
  public function setNumericValue($numericValue)
  {
    $this->numericValue = $numericValue;
  }
  public function getNumericValue()
  {
    return $this->numericValue;
  }
  /**
   * The score of the audit, can be null.
   *
   * @param array $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return array
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The enumerated score display mode.
   *
   * @param string $scoreDisplayMode
   */
  public function setScoreDisplayMode($scoreDisplayMode)
  {
    $this->scoreDisplayMode = $scoreDisplayMode;
  }
  /**
   * @return string
   */
  public function getScoreDisplayMode()
  {
    return $this->scoreDisplayMode;
  }
  /**
   * The human readable title.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Possible warnings that occurred in the audit, can be null.
   *
   * @param array $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return array
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LighthouseAuditResultV5::class, 'Google_Service_PagespeedInsights_LighthouseAuditResultV5');
