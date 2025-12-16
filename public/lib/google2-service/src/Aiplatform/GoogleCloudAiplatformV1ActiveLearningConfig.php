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

class GoogleCloudAiplatformV1ActiveLearningConfig extends \Google\Model
{
  /**
   * Max number of human labeled DataItems.
   *
   * @var string
   */
  public $maxDataItemCount;
  /**
   * Max percent of total DataItems for human labeling.
   *
   * @var int
   */
  public $maxDataItemPercentage;
  protected $sampleConfigType = GoogleCloudAiplatformV1SampleConfig::class;
  protected $sampleConfigDataType = '';
  protected $trainingConfigType = GoogleCloudAiplatformV1TrainingConfig::class;
  protected $trainingConfigDataType = '';

  /**
   * Max number of human labeled DataItems.
   *
   * @param string $maxDataItemCount
   */
  public function setMaxDataItemCount($maxDataItemCount)
  {
    $this->maxDataItemCount = $maxDataItemCount;
  }
  /**
   * @return string
   */
  public function getMaxDataItemCount()
  {
    return $this->maxDataItemCount;
  }
  /**
   * Max percent of total DataItems for human labeling.
   *
   * @param int $maxDataItemPercentage
   */
  public function setMaxDataItemPercentage($maxDataItemPercentage)
  {
    $this->maxDataItemPercentage = $maxDataItemPercentage;
  }
  /**
   * @return int
   */
  public function getMaxDataItemPercentage()
  {
    return $this->maxDataItemPercentage;
  }
  /**
   * Active learning data sampling config. For every active learning labeling
   * iteration, it will select a batch of data based on the sampling strategy.
   *
   * @param GoogleCloudAiplatformV1SampleConfig $sampleConfig
   */
  public function setSampleConfig(GoogleCloudAiplatformV1SampleConfig $sampleConfig)
  {
    $this->sampleConfig = $sampleConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SampleConfig
   */
  public function getSampleConfig()
  {
    return $this->sampleConfig;
  }
  /**
   * CMLE training config. For every active learning labeling iteration, system
   * will train a machine learning model on CMLE. The trained model will be used
   * by data sampling algorithm to select DataItems.
   *
   * @param GoogleCloudAiplatformV1TrainingConfig $trainingConfig
   */
  public function setTrainingConfig(GoogleCloudAiplatformV1TrainingConfig $trainingConfig)
  {
    $this->trainingConfig = $trainingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1TrainingConfig
   */
  public function getTrainingConfig()
  {
    return $this->trainingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ActiveLearningConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ActiveLearningConfig');
