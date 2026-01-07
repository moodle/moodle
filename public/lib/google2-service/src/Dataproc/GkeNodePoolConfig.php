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

namespace Google\Service\Dataproc;

class GkeNodePoolConfig extends \Google\Collection
{
  protected $collection_key = 'locations';
  protected $autoscalingType = GkeNodePoolAutoscalingConfig::class;
  protected $autoscalingDataType = '';
  protected $configType = GkeNodeConfig::class;
  protected $configDataType = '';
  /**
   * Optional. The list of Compute Engine zones
   * (https://cloud.google.com/compute/docs/zones#available) where node pool
   * nodes associated with a Dataproc on GKE virtual cluster will be
   * located.Note: All node pools associated with a virtual cluster must be
   * located in the same region as the virtual cluster, and they must be located
   * in the same zone within that region.If a location is not specified during
   * node pool creation, Dataproc on GKE will choose the zone.
   *
   * @var string[]
   */
  public $locations;

  /**
   * Optional. The autoscaler configuration for this node pool. The autoscaler
   * is enabled only when a valid configuration is present.
   *
   * @param GkeNodePoolAutoscalingConfig $autoscaling
   */
  public function setAutoscaling(GkeNodePoolAutoscalingConfig $autoscaling)
  {
    $this->autoscaling = $autoscaling;
  }
  /**
   * @return GkeNodePoolAutoscalingConfig
   */
  public function getAutoscaling()
  {
    return $this->autoscaling;
  }
  /**
   * Optional. The node pool configuration.
   *
   * @param GkeNodeConfig $config
   */
  public function setConfig(GkeNodeConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GkeNodeConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Optional. The list of Compute Engine zones
   * (https://cloud.google.com/compute/docs/zones#available) where node pool
   * nodes associated with a Dataproc on GKE virtual cluster will be
   * located.Note: All node pools associated with a virtual cluster must be
   * located in the same region as the virtual cluster, and they must be located
   * in the same zone within that region.If a location is not specified during
   * node pool creation, Dataproc on GKE will choose the zone.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeNodePoolConfig::class, 'Google_Service_Dataproc_GkeNodePoolConfig');
