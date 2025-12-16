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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SampleConfig extends \Google\Model
{
  /**
   * Default will be treated as UNCERTAINTY.
   */
  public const SAMPLE_STRATEGY_SAMPLE_STRATEGY_UNSPECIFIED = 'SAMPLE_STRATEGY_UNSPECIFIED';
  /**
   * Sample the most uncertain data to label.
   */
  public const SAMPLE_STRATEGY_UNCERTAINTY = 'UNCERTAINTY';
  /**
   * The percentage of data needed to be labeled in each following batch (except
   * the first batch).
   *
   * @var int
   */
  public $followingBatchSamplePercentage;
  /**
   * The percentage of data needed to be labeled in the first batch.
   *
   * @var int
   */
  public $initialBatchSamplePercentage;
  /**
   * Field to choose sampling strategy. Sampling strategy will decide which data
   * should be selected for human labeling in every batch.
   *
   * @var string
   */
  public $sampleStrategy;

  /**
   * The percentage of data needed to be labeled in each following batch (except
   * the first batch).
   *
   * @param int $followingBatchSamplePercentage
   */
  public function setFollowingBatchSamplePercentage($followingBatchSamplePercentage)
  {
    $this->followingBatchSamplePercentage = $followingBatchSamplePercentage;
  }
  /**
   * @return int
   */
  public function getFollowingBatchSamplePercentage()
  {
    return $this->followingBatchSamplePercentage;
  }
  /**
   * The percentage of data needed to be labeled in the first batch.
   *
   * @param int $initialBatchSamplePercentage
   */
  public function setInitialBatchSamplePercentage($initialBatchSamplePercentage)
  {
    $this->initialBatchSamplePercentage = $initialBatchSamplePercentage;
  }
  /**
   * @return int
   */
  public function getInitialBatchSamplePercentage()
  {
    return $this->initialBatchSamplePercentage;
  }
  /**
   * Field to choose sampling strategy. Sampling strategy will decide which data
   * should be selected for human labeling in every batch.
   *
   * Accepted values: SAMPLE_STRATEGY_UNSPECIFIED, UNCERTAINTY
   *
   * @param self::SAMPLE_STRATEGY_* $sampleStrategy
   */
  public function setSampleStrategy($sampleStrategy)
  {
    $this->sampleStrategy = $sampleStrategy;
  }
  /**
   * @return self::SAMPLE_STRATEGY_*
   */
  public function getSampleStrategy()
  {
    return $this->sampleStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SampleConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SampleConfig');
