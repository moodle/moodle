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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultScoreSource extends \Google\Collection
{
  /**
   * Source type is unspecified.
   */
  public const SOURCE_TYPE_SOURCE_TYPE_UNSPECIFIED = 'SOURCE_TYPE_UNSPECIFIED';
  /**
   * Score is derived only from system-generated answers.
   */
  public const SOURCE_TYPE_SYSTEM_GENERATED_ONLY = 'SYSTEM_GENERATED_ONLY';
  /**
   * Score is derived from both system-generated answers, and includes any
   * manual edits if they exist.
   */
  public const SOURCE_TYPE_INCLUDES_MANUAL_EDITS = 'INCLUDES_MANUAL_EDITS';
  protected $collection_key = 'qaTagResults';
  /**
   * The normalized score, which is the score divided by the potential score.
   *
   * @var 
   */
  public $normalizedScore;
  /**
   * The maximum potential overall score of the scorecard. Any questions
   * answered using `na_value` are excluded from this calculation.
   *
   * @var 
   */
  public $potentialScore;
  protected $qaTagResultsType = GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultQaTagResult::class;
  protected $qaTagResultsDataType = 'array';
  /**
   * The overall numerical score of the result.
   *
   * @var 
   */
  public $score;
  /**
   * What created the score.
   *
   * @var string
   */
  public $sourceType;

  public function setNormalizedScore($normalizedScore)
  {
    $this->normalizedScore = $normalizedScore;
  }
  public function getNormalizedScore()
  {
    return $this->normalizedScore;
  }
  public function setPotentialScore($potentialScore)
  {
    $this->potentialScore = $potentialScore;
  }
  public function getPotentialScore()
  {
    return $this->potentialScore;
  }
  /**
   * Collection of tags and their scores.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultQaTagResult[] $qaTagResults
   */
  public function setQaTagResults($qaTagResults)
  {
    $this->qaTagResults = $qaTagResults;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultQaTagResult[]
   */
  public function getQaTagResults()
  {
    return $this->qaTagResults;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * What created the score.
   *
   * Accepted values: SOURCE_TYPE_UNSPECIFIED, SYSTEM_GENERATED_ONLY,
   * INCLUDES_MANUAL_EDITS
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultScoreSource::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1QaScorecardResultScoreSource');
