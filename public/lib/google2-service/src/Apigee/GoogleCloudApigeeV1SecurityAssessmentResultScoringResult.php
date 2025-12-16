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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityAssessmentResultScoringResult extends \Google\Model
{
  /**
   * Severity is not defined.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Severity is low.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Severity is medium.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Severity is high.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Severity is minimal
   */
  public const SEVERITY_MINIMAL = 'MINIMAL';
  protected $assessmentRecommendationsType = GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation::class;
  protected $assessmentRecommendationsDataType = 'map';
  /**
   * The time when resource data was last fetched for this resource. This time
   * may be different than when the resource was actually updated due to lag in
   * data collection.
   *
   * @var string
   */
  public $dataUpdateTime;
  /**
   * The number of failed assessments grouped by its weight. Keys are one of the
   * following: "MAJOR", "MODERATE", "MINOR".
   *
   * @var int[]
   */
  public $failedAssessmentPerWeight;
  /**
   * The security score of the assessment.
   *
   * @var int
   */
  public $score;
  /**
   * @var string
   */
  public $severity;

  /**
   * The recommendations of the assessment. The key is the "name" of the
   * assessment (not display_name), and the value are the recommendations.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation[] $assessmentRecommendations
   */
  public function setAssessmentRecommendations($assessmentRecommendations)
  {
    $this->assessmentRecommendations = $assessmentRecommendations;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation[]
   */
  public function getAssessmentRecommendations()
  {
    return $this->assessmentRecommendations;
  }
  /**
   * The time when resource data was last fetched for this resource. This time
   * may be different than when the resource was actually updated due to lag in
   * data collection.
   *
   * @param string $dataUpdateTime
   */
  public function setDataUpdateTime($dataUpdateTime)
  {
    $this->dataUpdateTime = $dataUpdateTime;
  }
  /**
   * @return string
   */
  public function getDataUpdateTime()
  {
    return $this->dataUpdateTime;
  }
  /**
   * The number of failed assessments grouped by its weight. Keys are one of the
   * following: "MAJOR", "MODERATE", "MINOR".
   *
   * @param int[] $failedAssessmentPerWeight
   */
  public function setFailedAssessmentPerWeight($failedAssessmentPerWeight)
  {
    $this->failedAssessmentPerWeight = $failedAssessmentPerWeight;
  }
  /**
   * @return int[]
   */
  public function getFailedAssessmentPerWeight()
  {
    return $this->failedAssessmentPerWeight;
  }
  /**
   * The security score of the assessment.
   *
   * @param int $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return int
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityAssessmentResultScoringResult::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAssessmentResultScoringResult');
