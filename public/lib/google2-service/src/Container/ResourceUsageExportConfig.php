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

class ResourceUsageExportConfig extends \Google\Model
{
  protected $bigqueryDestinationType = BigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  protected $consumptionMeteringConfigType = ConsumptionMeteringConfig::class;
  protected $consumptionMeteringConfigDataType = '';
  /**
   * Whether to enable network egress metering for this cluster. If enabled, a
   * daemonset will be created in the cluster to meter network egress traffic.
   *
   * @var bool
   */
  public $enableNetworkEgressMetering;

  /**
   * Configuration to use BigQuery as usage export destination.
   *
   * @param BigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(BigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return BigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * Configuration to enable resource consumption metering.
   *
   * @param ConsumptionMeteringConfig $consumptionMeteringConfig
   */
  public function setConsumptionMeteringConfig(ConsumptionMeteringConfig $consumptionMeteringConfig)
  {
    $this->consumptionMeteringConfig = $consumptionMeteringConfig;
  }
  /**
   * @return ConsumptionMeteringConfig
   */
  public function getConsumptionMeteringConfig()
  {
    return $this->consumptionMeteringConfig;
  }
  /**
   * Whether to enable network egress metering for this cluster. If enabled, a
   * daemonset will be created in the cluster to meter network egress traffic.
   *
   * @param bool $enableNetworkEgressMetering
   */
  public function setEnableNetworkEgressMetering($enableNetworkEgressMetering)
  {
    $this->enableNetworkEgressMetering = $enableNetworkEgressMetering;
  }
  /**
   * @return bool
   */
  public function getEnableNetworkEgressMetering()
  {
    return $this->enableNetworkEgressMetering;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceUsageExportConfig::class, 'Google_Service_Container_ResourceUsageExportConfig');
