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

class GoogleCloudApigeeV1RuntimeTraceSamplingConfig extends \Google\Model
{
  /**
   * Sampler unspecified.
   */
  public const SAMPLER_SAMPLER_UNSPECIFIED = 'SAMPLER_UNSPECIFIED';
  /**
   * OFF means distributed trace is disabled, or the sampling probability is 0.
   */
  public const SAMPLER_OFF = 'OFF';
  /**
   * PROBABILITY means traces are captured on a probability that defined by
   * sampling_rate. The sampling rate is limited to 0 to 0.5 when this is set.
   */
  public const SAMPLER_PROBABILITY = 'PROBABILITY';
  /**
   * Sampler of distributed tracing. OFF is the default value.
   *
   * @var string
   */
  public $sampler;
  /**
   * Field sampling rate. This value is only applicable when using the
   * PROBABILITY sampler. The supported values are > 0 and <= 0.5.
   *
   * @var float
   */
  public $samplingRate;

  /**
   * Sampler of distributed tracing. OFF is the default value.
   *
   * Accepted values: SAMPLER_UNSPECIFIED, OFF, PROBABILITY
   *
   * @param self::SAMPLER_* $sampler
   */
  public function setSampler($sampler)
  {
    $this->sampler = $sampler;
  }
  /**
   * @return self::SAMPLER_*
   */
  public function getSampler()
  {
    return $this->sampler;
  }
  /**
   * Field sampling rate. This value is only applicable when using the
   * PROBABILITY sampler. The supported values are > 0 and <= 0.5.
   *
   * @param float $samplingRate
   */
  public function setSamplingRate($samplingRate)
  {
    $this->samplingRate = $samplingRate;
  }
  /**
   * @return float
   */
  public function getSamplingRate()
  {
    return $this->samplingRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RuntimeTraceSamplingConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RuntimeTraceSamplingConfig');
