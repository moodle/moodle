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

namespace Google\Service\GKEHub;

class ClusterUpgradeFleetSpec extends \Google\Collection
{
  protected $collection_key = 'upstreamFleets';
  protected $gkeUpgradeOverridesType = ClusterUpgradeGKEUpgradeOverride::class;
  protected $gkeUpgradeOverridesDataType = 'array';
  protected $postConditionsType = ClusterUpgradePostConditions::class;
  protected $postConditionsDataType = '';
  /**
   * @var string[]
   */
  public $upstreamFleets;

  /**
   * @param ClusterUpgradeGKEUpgradeOverride[]
   */
  public function setGkeUpgradeOverrides($gkeUpgradeOverrides)
  {
    $this->gkeUpgradeOverrides = $gkeUpgradeOverrides;
  }
  /**
   * @return ClusterUpgradeGKEUpgradeOverride[]
   */
  public function getGkeUpgradeOverrides()
  {
    return $this->gkeUpgradeOverrides;
  }
  /**
   * @param ClusterUpgradePostConditions
   */
  public function setPostConditions(ClusterUpgradePostConditions $postConditions)
  {
    $this->postConditions = $postConditions;
  }
  /**
   * @return ClusterUpgradePostConditions
   */
  public function getPostConditions()
  {
    return $this->postConditions;
  }
  /**
   * @param string[]
   */
  public function setUpstreamFleets($upstreamFleets)
  {
    $this->upstreamFleets = $upstreamFleets;
  }
  /**
   * @return string[]
   */
  public function getUpstreamFleets()
  {
    return $this->upstreamFleets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeFleetSpec::class, 'Google_Service_GKEHub_ClusterUpgradeFleetSpec');
