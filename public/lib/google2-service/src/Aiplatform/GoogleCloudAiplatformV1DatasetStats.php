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

class GoogleCloudAiplatformV1DatasetStats extends \Google\Collection
{
  protected $collection_key = 'userDatasetExamples';
  /**
   * @var string
   */
  public $totalBillableCharacterCount;
  /**
   * @var string
   */
  public $totalTuningCharacterCount;
  /**
   * @var string
   */
  public $tuningDatasetExampleCount;
  /**
   * @var string
   */
  public $tuningStepCount;
  protected $userDatasetExamplesType = GoogleCloudAiplatformV1Content::class;
  protected $userDatasetExamplesDataType = 'array';
  protected $userInputTokenDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $userInputTokenDistributionDataType = '';
  protected $userMessagePerExampleDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $userMessagePerExampleDistributionDataType = '';
  protected $userOutputTokenDistributionType = GoogleCloudAiplatformV1DatasetDistribution::class;
  protected $userOutputTokenDistributionDataType = '';

  /**
   * @param string
   */
  public function setTotalBillableCharacterCount($totalBillableCharacterCount)
  {
    $this->totalBillableCharacterCount = $totalBillableCharacterCount;
  }
  /**
   * @return string
   */
  public function getTotalBillableCharacterCount()
  {
    return $this->totalBillableCharacterCount;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
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
   * @param GoogleCloudAiplatformV1Content[]
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
   * @param GoogleCloudAiplatformV1DatasetDistribution
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
   * @param GoogleCloudAiplatformV1DatasetDistribution
   */
  public function setUserMessagePerExampleDistribution(GoogleCloudAiplatformV1DatasetDistribution $userMessagePerExampleDistribution)
  {
    $this->userMessagePerExampleDistribution = $userMessagePerExampleDistribution;
  }
  /**
   * @return GoogleCloudAiplatformV1DatasetDistribution
   */
  public function getUserMessagePerExampleDistribution()
  {
    return $this->userMessagePerExampleDistribution;
  }
  /**
   * @param GoogleCloudAiplatformV1DatasetDistribution
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
class_alias(GoogleCloudAiplatformV1DatasetStats::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DatasetStats');
