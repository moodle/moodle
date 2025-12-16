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

namespace Google\Service\Container;

class AdvancedDatapathObservabilityConfig extends \Google\Model
{
  /**
   * Default value. This shouldn't be used.
   */
  public const RELAY_MODE_RELAY_MODE_UNSPECIFIED = 'RELAY_MODE_UNSPECIFIED';
  /**
   * disabled
   */
  public const RELAY_MODE_DISABLED = 'DISABLED';
  /**
   * exposed via internal load balancer
   */
  public const RELAY_MODE_INTERNAL_VPC_LB = 'INTERNAL_VPC_LB';
  /**
   * exposed via external load balancer
   */
  public const RELAY_MODE_EXTERNAL_LB = 'EXTERNAL_LB';
  /**
   * Expose flow metrics on nodes
   *
   * @var bool
   */
  public $enableMetrics;
  /**
   * Enable Relay component
   *
   * @var bool
   */
  public $enableRelay;
  /**
   * Method used to make Relay available
   *
   * @var string
   */
  public $relayMode;

  /**
   * Expose flow metrics on nodes
   *
   * @param bool $enableMetrics
   */
  public function setEnableMetrics($enableMetrics)
  {
    $this->enableMetrics = $enableMetrics;
  }
  /**
   * @return bool
   */
  public function getEnableMetrics()
  {
    return $this->enableMetrics;
  }
  /**
   * Enable Relay component
   *
   * @param bool $enableRelay
   */
  public function setEnableRelay($enableRelay)
  {
    $this->enableRelay = $enableRelay;
  }
  /**
   * @return bool
   */
  public function getEnableRelay()
  {
    return $this->enableRelay;
  }
  /**
   * Method used to make Relay available
   *
   * Accepted values: RELAY_MODE_UNSPECIFIED, DISABLED, INTERNAL_VPC_LB,
   * EXTERNAL_LB
   *
   * @param self::RELAY_MODE_* $relayMode
   */
  public function setRelayMode($relayMode)
  {
    $this->relayMode = $relayMode;
  }
  /**
   * @return self::RELAY_MODE_*
   */
  public function getRelayMode()
  {
    return $this->relayMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvancedDatapathObservabilityConfig::class, 'Google_Service_Container_AdvancedDatapathObservabilityConfig');
