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

class GoogleCloudAiplatformV1SupervisedHyperParameters extends \Google\Model
{
  /**
   * Adapter size is unspecified.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_UNSPECIFIED = 'ADAPTER_SIZE_UNSPECIFIED';
  /**
   * Adapter size 1.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_ONE = 'ADAPTER_SIZE_ONE';
  /**
   * Adapter size 2.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_TWO = 'ADAPTER_SIZE_TWO';
  /**
   * Adapter size 4.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_FOUR = 'ADAPTER_SIZE_FOUR';
  /**
   * Adapter size 8.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_EIGHT = 'ADAPTER_SIZE_EIGHT';
  /**
   * Adapter size 16.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_SIXTEEN = 'ADAPTER_SIZE_SIXTEEN';
  /**
   * Adapter size 32.
   */
  public const ADAPTER_SIZE_ADAPTER_SIZE_THIRTY_TWO = 'ADAPTER_SIZE_THIRTY_TWO';
  /**
   * Optional. Adapter size for tuning.
   *
   * @var string
   */
  public $adapterSize;
  /**
   * Optional. Number of complete passes the model makes over the entire
   * training dataset during training.
   *
   * @var string
   */
  public $epochCount;
  /**
   * Optional. Multiplier for adjusting the default learning rate. Mutually
   * exclusive with `learning_rate`. This feature is only available for 1P
   * models.
   *
   * @var 
   */
  public $learningRateMultiplier;

  /**
   * Optional. Adapter size for tuning.
   *
   * Accepted values: ADAPTER_SIZE_UNSPECIFIED, ADAPTER_SIZE_ONE,
   * ADAPTER_SIZE_TWO, ADAPTER_SIZE_FOUR, ADAPTER_SIZE_EIGHT,
   * ADAPTER_SIZE_SIXTEEN, ADAPTER_SIZE_THIRTY_TWO
   *
   * @param self::ADAPTER_SIZE_* $adapterSize
   */
  public function setAdapterSize($adapterSize)
  {
    $this->adapterSize = $adapterSize;
  }
  /**
   * @return self::ADAPTER_SIZE_*
   */
  public function getAdapterSize()
  {
    return $this->adapterSize;
  }
  /**
   * Optional. Number of complete passes the model makes over the entire
   * training dataset during training.
   *
   * @param string $epochCount
   */
  public function setEpochCount($epochCount)
  {
    $this->epochCount = $epochCount;
  }
  /**
   * @return string
   */
  public function getEpochCount()
  {
    return $this->epochCount;
  }
  public function setLearningRateMultiplier($learningRateMultiplier)
  {
    $this->learningRateMultiplier = $learningRateMultiplier;
  }
  public function getLearningRateMultiplier()
  {
    return $this->learningRateMultiplier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SupervisedHyperParameters::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SupervisedHyperParameters');
