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

class GoogleCloudAiplatformV1PreferenceOptimizationDataStats extends \Google\Collection
{
  protected $collection_key = 'userDatasetExamples';
  /**
   * Output only. A partial sample of the indices (starting from 1) of the
   * dropped examples.
   *
   * @var string[]
   */
  public $droppedExampleIndices;
  /**
   * Output only. For each index in `dropped_example_indices`, the user-facing
   * reason why the example was dropped.
   *
   * @var string[]
   */
  public $droppedExampleReasons;
  protected $scoreVariancePerExampleDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $scoreVariancePerExampleDistributionDataType = '';
  protected $scoresDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $scoresDistributionDataType = '';
  /**
   * Output only. Number of billable tokens in the tuning dataset.
   *
   * @var string
   */
  public $totalBillableTokenCount;
  /**
   * Output only. Number of examples in the tuning dataset.
   *
   * @var string
   */
  public $tuningDatasetExampleCount;
  /**
   * Output only. Number of tuning steps for this Tuning Job.
   *
   * @var string
   */
  public $tuningStepCount;
  protected $userDatasetExamplesType = GoogleCloudAiplatformV1GeminiPreferenceExample::class;
  protected $userDatasetExamplesDataType = 'array';
  protected $userInputTokenDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $userInputTokenDistributionDataType = '';
  protected $userOutputTokenDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $userOutputTokenDistributionDataType = '';

  /**
   * Output only. A partial sample of the indices (starting from 1) of the
   * dropped examples.
   *
   * @param string[] $droppedExampleIndices
   */
  public function setDroppedExampleIndices($droppedExampleIndices)
  {
    $this->droppedExampleIndices = $droppedExampleIndices;
  }
  /**
   * @return string[]
   */
  public function getDroppedExampleIndices()
  {
    return $this->droppedExampleIndices;
  }
  /**
   * Output only. For each index in `dropped_example_indices`, the user-facing
   * reason why the example was dropped.
   *
   * @param string[] $droppedExampleReasons
   */
  public function setDroppedExampleReasons($droppedExampleReasons)
  {
    $this->droppedExampleReasons = $droppedExampleReasons;
  }
  /**
   * @return string[]
   */
  public function getDroppedExampleReasons()
  {
    return $this->droppedExampleReasons;
  }
  /**
   * Output only. Dataset distributions for scores variance per example.
   *
   * @param GoogleCloudAiplatformV1DatasetDistribution $scoreVariancePerExampleDistribution
   */
  public function setScoreVariancePerExampleDistribution(GoogleCloudAiplatformV1DatasetDistribution $scoreVariancePerExampleDistribution)
  {
    $this->scoreVariancePerExampleDistribution = $scoreVariancePerExampleDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1DatasetDistribution
   */
  public function getScoreVariancePerExampleDistribution()
  {
    return $this->scoreVariancePerExampleDistribution;
  }
  /**
   * Output only. Dataset distributions for scores.
   *
   * @param GoogleCloudAiplatformV1DatasetDistribution $scoresDistribution
   */
  public function setScoresDistribution(GoogleCloudAiplatformV1DatasetDistribution $scoresDistribution)
  {
    $this->scoresDistribution = $scoresDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1DatasetDistribution
   */
  public function getScoresDistribution()
  {
    return $this->scoresDistribution;
  }
  /**
   * Output only. Number of billable tokens in the tuning dataset.
   *
   * @param string $totalBillableTokenCount
   */
  public function setTotalBillableTokenCount($totalBillableTokenCount)
  {
    $this->totalBillableTokenCount = $totalBillableTokenCount;
  }
  /**
   * @return string
   */
  public function getTotalBillableTokenCount()
  {
    return $this->totalBillableTokenCount;
  }
  /**
   * Output only. Number of examples in the tuning dataset.
   *
   * @param string $tuningDatasetExampleCount
   */
  public function setTuningDatasetExampleCount($tuningDatasetExampleCount)
  {
    $this->tuningDatasetExampleCount = $tuningDatasetExampleCount;
  }
  /**
   * @return string
   */
  public function getTuningDatasetExampleCount()
  {
    return $this->tuningDatasetExampleCount;
  }
  /**
   * Output only. Number of tuning steps for this Tuning Job.
   *
   * @param string $tuningStepCount
   */
  public function setTuningStepCount($tuningStepCount)
  {
    $this->tuningStepCount = $tuningStepCount;
  }
  /**
   * @return string
   */
  public function getTuningStepCount()
  {
    return $this->tuningStepCount;
  }
  /**
   * Output only. Sample user examples in the training dataset.
   *
   * @param GoogleCloudAiplatformV1GeminiPreferenceExample[] $userDatasetExamples
   */
  public function setUserDatasetExamples($userDatasetExamples)
  {
    $this->userDatasetExamples = $userDatasetExamples;
  }
  /**
   * @return GoogleCloudAiplatformV1GeminiPreferenceExample[]
   */
  public function getUserDatasetExamples()
  {
    return $this->userDatasetExamples;
  }
  /**
   * Output only. Dataset distributions for the user input tokens.
   *
   * @param GoogleCloudAiplatformV1DatasetDistribution $userInputTokenDistribution
   */
  public function setUserInputTokenDistribution(GoogleCloudAiplatformV1DatasetDistribution $userInputTokenDistribution)
  {
    $this->userInputTokenDistribution = $userInputTokenDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1DatasetDistribution
   */
  public function getUserInputTokenDistribution()
  {
    return $this->userInputTokenDistribution;
  }
  /**
   * Output only. Dataset distributions for the user output tokens.
   *
   * @param GoogleCloudAiplatformV1DatasetDistribution $userOutputTokenDistribution
   */
  public function setUserOutputTokenDistribution(GoogleCloudAiplatformV1DatasetDistribution $userOutputTokenDistribution)
  {
    $this->userOutputTokenDistribution = $userOutputTokenDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1DatasetDistribution
   */
  public function getUserOutputTokenDistribution()
  {
    return $this->userOutputTokenDistribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PreferenceOptimizationDataStats::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PreferenceOptimizationDataStats');
