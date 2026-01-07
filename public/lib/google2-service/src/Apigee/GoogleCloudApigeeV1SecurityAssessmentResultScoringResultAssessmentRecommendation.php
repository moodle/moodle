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

class GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation extends \Google\Collection
{
  /**
   * The verdict is unspecified.
   */
  public const VERDICT_VERDICT_UNSPECIFIED = 'VERDICT_UNSPECIFIED';
  /**
   * The assessment has passed.
   */
  public const VERDICT_PASS = 'PASS';
  /**
   * The assessment has failed.
   */
  public const VERDICT_FAIL = 'FAIL';
  /**
   * The verdict is not applicable.
   */
  public const VERDICT_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * The weight is unspecified.
   */
  public const WEIGHT_WEIGHT_UNSPECIFIED = 'WEIGHT_UNSPECIFIED';
  /**
   * The weight is minor.
   */
  public const WEIGHT_MINOR = 'MINOR';
  /**
   * The weight is moderate.
   */
  public const WEIGHT_MODERATE = 'MODERATE';
  /**
   * The weight is major.
   */
  public const WEIGHT_MAJOR = 'MAJOR';
  protected $collection_key = 'recommendations';
  /**
   * The display name of the assessment.
   *
   * @var string
   */
  public $displayName;
  protected $recommendationsType = GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendationRecommendation::class;
  protected $recommendationsDataType = 'array';
  /**
   * Score impact indicates the impact on the overall score if the assessment
   * were to pass.
   *
   * @var int
   */
  public $scoreImpact;
  /**
   * Verdict indicates the assessment result.
   *
   * @var string
   */
  public $verdict;
  /**
   * The weight of the assessment which was set in the profile.
   *
   * @var string
   */
  public $weight;

  /**
   * The display name of the assessment.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The recommended steps of the assessment.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendationRecommendation[] $recommendations
   */
  public function setRecommendations($recommendations)
  {
    $this->recommendations = $recommendations;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendationRecommendation[]
   */
  public function getRecommendations()
  {
    return $this->recommendations;
  }
  /**
   * Score impact indicates the impact on the overall score if the assessment
   * were to pass.
   *
   * @param int $scoreImpact
   */
  public function setScoreImpact($scoreImpact)
  {
    $this->scoreImpact = $scoreImpact;
  }
  /**
   * @return int
   */
  public function getScoreImpact()
  {
    return $this->scoreImpact;
  }
  /**
   * Verdict indicates the assessment result.
   *
   * Accepted values: VERDICT_UNSPECIFIED, PASS, FAIL, NOT_APPLICABLE
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
  /**
   * The weight of the assessment which was set in the profile.
   *
   * Accepted values: WEIGHT_UNSPECIFIED, MINOR, MODERATE, MAJOR
   *
   * @param self::WEIGHT_* $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return self::WEIGHT_*
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAssessmentResultScoringResultAssessmentRecommendation');
