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

class GoogleCloudAiplatformV1SupervisedTuningDataStats extends \Google\Collection
{
  protected $collection_key = 'userDatasetExamples';
  /**
   * Output only. For each index in `truncated_example_indices`, the user-facing
   * reason why the example was dropped.
   *
   * @var string[]
   */
  public $droppedExampleReasons;
  /**
   * Output only. Number of billable characters in the tuning dataset.
   *
   * @deprecated
   * @var string
   */
  public $totalBillableCharacterCount;
  /**
   * Output only. Number of billable tokens in the tuning dataset.
   *
   * @var string
   */
  public $totalBillableTokenCount;
  /**
   * Output only. The number of examples in the dataset that have been dropped.
   * An example can be dropped for reasons including: too many tokens, contains
   * an invalid image, contains too many images, etc.
   *
   * @var string
   */
  public $totalTruncatedExampleCount;
  /**
   * Output only. Number of tuning characters in the tuning dataset.
   *
   * @var string
   */
  public $totalTuningCharacterCount;
  /**
   * Output only. A partial sample of the indices (starting from 1) of the
   * dropped examples.
   *
   * @var string[]
   */
  public $truncatedExampleIndices;
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
  protected $userDatasetExamplesType = GoogleCloudAiplatformV1Content::class;
  protected $userDatasetExamplesDataType = 'array';
  protected $userInputTokenDistributionType = GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution::class;
  protected $userInputTokenDistributionDataType = '';
  protected $userMessagePerExampleDistributionType = GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution::class;
  protected $userMessagePerExampleDistributionDataType = '';
  protected $userOutputTokenDistributionType = GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution::class;
  protected $userOutputTokenDistributionDataType = '';

  /**
   * Output only. For each index in `truncated_example_indices`, the user-facing
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
   * Output only. Number of billable characters in the tuning dataset.
   *
   * @deprecated
   * @param string $totalBillableCharacterCount
   */
  public function setTotalBillableCharacterCount($totalBillableCharacterCount)
  {
    $this->totalBillableCharacterCount = $totalBillableCharacterCount;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTotalBillableCharacterCount()
  {
    return $this->totalBillableCharacterCount;
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
   * Output only. The number of examples in the dataset that have been dropped.
   * An example can be dropped for reasons including: too many tokens, contains
   * an invalid image, contains too many images, etc.
   *
   * @param string $totalTruncatedExampleCount
   */
  public function setTotalTruncatedExampleCount($totalTruncatedExampleCount)
  {
    $this->totalTruncatedExampleCount = $totalTruncatedExampleCount;
  }
  /**
   * @return string
   */
  public function getTotalTruncatedExampleCount()
  {
    return $this->totalTruncatedExampleCount;
  }
  /**
   * Output only. Number of tuning characters in the tuning dataset.
   *
   * @param string $totalTuningCharacterCount
   */
  public function setTotalTuningCharacterCount($totalTuningCharacterCount)
  {
    $this->totalTuningCharacterCount = $totalTuningCharacterCount;
  }
  /**
   * @return string
   */
  public function getTotalTuningCharacterCount()
  {
    return $this->totalTuningCharacterCount;
  }
  /**
   * Output only. A partial sample of the indices (starting from 1) of the
   * dropped examples.
   *
   * @param string[] $truncatedExampleIndices
   */
  public function setTruncatedExampleIndices($truncatedExampleIndices)
  {
    $this->truncatedExampleIndices = $truncatedExampleIndices;
  }
  /**
   * @return string[]
   */
  public function getTruncatedExampleIndices()
  {
    return $this->truncatedExampleIndices;
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
   * Output only. Sample user messages in the training dataset uri.
   *
   * @param GoogleCloudAiplatformV1Content[] $userDatasetExamples
   */
  public function setUserDatasetExamples($userDatasetExamples)
  {
    $this->userDatasetExamples = $userDatasetExamples;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getUserDatasetExamples()
  {
    return $this->userDatasetExamples;
  }
  /**
   * Output only. Dataset distributions for the user input tokens.
   *
   * @param GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userInputTokenDistribution
   */
  public function setUserInputTokenDistribution(GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userInputTokenDistribution)
  {
    $this->userInputTokenDistribution = $userInputTokenDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution
   */
  public function getUserInputTokenDistribution()
  {
    return $this->userInputTokenDistribution;
  }
  /**
   * Output only. Dataset distributions for the messages per example.
   *
   * @param GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userMessagePerExampleDistribution
   */
  public function setUserMessagePerExampleDistribution(GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userMessagePerExampleDistribution)
  {
    $this->userMessagePerExampleDistribution = $userMessagePerExampleDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution
   */
  public function getUserMessagePerExampleDistribution()
  {
    return $this->userMessagePerExampleDistribution;
  }
  /**
   * Output only. Dataset distributions for the user output tokens.
   *
   * @param GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userOutputTokenDistribution
   */
  public function setUserOutputTokenDistribution(GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution $userOutputTokenDistribution)
  {
    $this->userOutputTokenDistribution = $userOutputTokenDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution
   */
  public function getUserOutputTokenDistribution()
  {
    return $this->userOutputTokenDistribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SupervisedTuningDataStats::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SupervisedTuningDataStats');
