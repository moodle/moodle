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

class GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig extends \Google\Model
{
  /**
   * Unspecified sampling method.
   */
  public const SAMPLING_METHOD_SAMPLING_METHOD_UNSPECIFIED = 'SAMPLING_METHOD_UNSPECIFIED';
  /**
   * Random sampling.
   */
  public const SAMPLING_METHOD_RANDOM = 'RANDOM';
  /**
   * Optional. The total number of logged data to import. If available data is
   * less than the sampling count, all data will be imported. Default is 100.
   *
   * @var int
   */
  public $samplingCount;
  /**
   * Optional. How long to wait before sampling data from the BigQuery table. If
   * not specified, defaults to 0.
   *
   * @var string
   */
  public $samplingDuration;
  /**
   * Optional. The sampling method to use.
   *
   * @var string
   */
  public $samplingMethod;

  /**
   * Optional. The total number of logged data to import. If available data is
   * less than the sampling count, all data will be imported. Default is 100.
   *
   * @param int $samplingCount
   */
  public function setSamplingCount($samplingCount)
  {
    $this->samplingCount = $samplingCount;
  }
  /**
   * @return int
   */
  public function getSamplingCount()
  {
    return $this->samplingCount;
  }
  /**
   * Optional. How long to wait before sampling data from the BigQuery table. If
   * not specified, defaults to 0.
   *
   * @param string $samplingDuration
   */
  public function setSamplingDuration($samplingDuration)
  {
    $this->samplingDuration = $samplingDuration;
  }
  /**
   * @return string
   */
  public function getSamplingDuration()
  {
    return $this->samplingDuration;
  }
  /**
   * Optional. The sampling method to use.
   *
   * Accepted values: SAMPLING_METHOD_UNSPECIFIED, RANDOM
   *
   * @param self::SAMPLING_METHOD_* $samplingMethod
   */
  public function setSamplingMethod($samplingMethod)
  {
    $this->samplingMethod = $samplingMethod;
  }
  /**
   * @return self::SAMPLING_METHOD_*
   */
  public function getSamplingMethod()
  {
    return $this->samplingMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BigQueryRequestSetSamplingConfig');
