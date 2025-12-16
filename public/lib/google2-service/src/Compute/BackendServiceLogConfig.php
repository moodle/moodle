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

namespace Google\Service\Compute;

class BackendServiceLogConfig extends \Google\Collection
{
  /**
   * A subset of optional fields.
   */
  public const OPTIONAL_MODE_CUSTOM = 'CUSTOM';
  /**
   * None optional fields.
   */
  public const OPTIONAL_MODE_EXCLUDE_ALL_OPTIONAL = 'EXCLUDE_ALL_OPTIONAL';
  /**
   * All optional fields.
   */
  public const OPTIONAL_MODE_INCLUDE_ALL_OPTIONAL = 'INCLUDE_ALL_OPTIONAL';
  protected $collection_key = 'optionalFields';
  /**
   * Denotes whether to enable logging for the load balancer traffic served by
   * this backend service. The default value is false.
   *
   * @var bool
   */
  public $enable;
  /**
   * This field can only be specified if logging is enabled for this backend
   * service and "logConfig.optionalMode" was set to CUSTOM. Contains a list of
   * optional fields you want to include in the logs. For example:
   * serverInstance, serverGkeDetails.cluster, serverGkeDetails.pod.podNamespace
   *
   * @var string[]
   */
  public $optionalFields;
  /**
   * This field can only be specified if logging is enabled for this backend
   * service. Configures whether all, none or a subset of optional fields should
   * be added to the reported logs. One of [INCLUDE_ALL_OPTIONAL,
   * EXCLUDE_ALL_OPTIONAL, CUSTOM]. Default is EXCLUDE_ALL_OPTIONAL.
   *
   * @var string
   */
  public $optionalMode;
  /**
   * This field can only be specified if logging is enabled for this backend
   * service. The value of the field must be in [0, 1]. This configures the
   * sampling rate of requests to the load balancer where 1.0 means all logged
   * requests are reported and 0.0 means no logged requests are reported. The
   * default value is 1.0.
   *
   * @var float
   */
  public $sampleRate;

  /**
   * Denotes whether to enable logging for the load balancer traffic served by
   * this backend service. The default value is false.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * This field can only be specified if logging is enabled for this backend
   * service and "logConfig.optionalMode" was set to CUSTOM. Contains a list of
   * optional fields you want to include in the logs. For example:
   * serverInstance, serverGkeDetails.cluster, serverGkeDetails.pod.podNamespace
   *
   * @param string[] $optionalFields
   */
  public function setOptionalFields($optionalFields)
  {
    $this->optionalFields = $optionalFields;
  }
  /**
   * @return string[]
   */
  public function getOptionalFields()
  {
    return $this->optionalFields;
  }
  /**
   * This field can only be specified if logging is enabled for this backend
   * service. Configures whether all, none or a subset of optional fields should
   * be added to the reported logs. One of [INCLUDE_ALL_OPTIONAL,
   * EXCLUDE_ALL_OPTIONAL, CUSTOM]. Default is EXCLUDE_ALL_OPTIONAL.
   *
   * Accepted values: CUSTOM, EXCLUDE_ALL_OPTIONAL, INCLUDE_ALL_OPTIONAL
   *
   * @param self::OPTIONAL_MODE_* $optionalMode
   */
  public function setOptionalMode($optionalMode)
  {
    $this->optionalMode = $optionalMode;
  }
  /**
   * @return self::OPTIONAL_MODE_*
   */
  public function getOptionalMode()
  {
    return $this->optionalMode;
  }
  /**
   * This field can only be specified if logging is enabled for this backend
   * service. The value of the field must be in [0, 1]. This configures the
   * sampling rate of requests to the load balancer where 1.0 means all logged
   * requests are reported and 0.0 means no logged requests are reported. The
   * default value is 1.0.
   *
   * @param float $sampleRate
   */
  public function setSampleRate($sampleRate)
  {
    $this->sampleRate = $sampleRate;
  }
  /**
   * @return float
   */
  public function getSampleRate()
  {
    return $this->sampleRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceLogConfig::class, 'Google_Service_Compute_BackendServiceLogConfig');
