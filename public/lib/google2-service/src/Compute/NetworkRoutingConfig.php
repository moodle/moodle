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

class NetworkRoutingConfig extends \Google\Model
{
  public const BGP_BEST_PATH_SELECTION_MODE_LEGACY = 'LEGACY';
  public const BGP_BEST_PATH_SELECTION_MODE_STANDARD = 'STANDARD';
  public const BGP_INTER_REGION_COST_ADD_COST_TO_MED = 'ADD_COST_TO_MED';
  public const BGP_INTER_REGION_COST_DEFAULT = 'DEFAULT';
  public const EFFECTIVE_BGP_INTER_REGION_COST_ADD_COST_TO_MED = 'ADD_COST_TO_MED';
  public const EFFECTIVE_BGP_INTER_REGION_COST_DEFAULT = 'DEFAULT';
  public const ROUTING_MODE_GLOBAL = 'GLOBAL';
  public const ROUTING_MODE_REGIONAL = 'REGIONAL';
  /**
   * Enable comparison of Multi-Exit Discriminators (MED) across routes with
   * different neighbor ASNs when using the STANDARD BGP best path selection
   * algorithm.
   *
   * @var bool
   */
  public $bgpAlwaysCompareMed;
  /**
   * The BGP best path selection algorithm to be employed within this network
   * for dynamic routes learned by Cloud Routers. Can be LEGACY (default) or
   * STANDARD.
   *
   * @var string
   */
  public $bgpBestPathSelectionMode;
  /**
   * Allows to define a preferred approach for handling inter-region cost in the
   * selection process when using the STANDARD BGP best path selection
   * algorithm. Can be DEFAULT orADD_COST_TO_MED.
   *
   * @var string
   */
  public $bgpInterRegionCost;
  /**
   * Output only. [Output Only] Effective value of the bgp_always_compare_med
   * field.
   *
   * @var bool
   */
  public $effectiveBgpAlwaysCompareMed;
  /**
   * Output only. [Output Only] Effective value of the bgp_inter_region_cost
   * field.
   *
   * @var string
   */
  public $effectiveBgpInterRegionCost;
  /**
   * The network-wide routing mode to use. If set to REGIONAL, this network's
   * Cloud Routers will only advertise routes with subnets of this network in
   * the same region as the router. If set toGLOBAL, this network's Cloud
   * Routers will advertise routes with all subnets of this network, across
   * regions.
   *
   * @var string
   */
  public $routingMode;

  /**
   * Enable comparison of Multi-Exit Discriminators (MED) across routes with
   * different neighbor ASNs when using the STANDARD BGP best path selection
   * algorithm.
   *
   * @param bool $bgpAlwaysCompareMed
   */
  public function setBgpAlwaysCompareMed($bgpAlwaysCompareMed)
  {
    $this->bgpAlwaysCompareMed = $bgpAlwaysCompareMed;
  }
  /**
   * @return bool
   */
  public function getBgpAlwaysCompareMed()
  {
    return $this->bgpAlwaysCompareMed;
  }
  /**
   * The BGP best path selection algorithm to be employed within this network
   * for dynamic routes learned by Cloud Routers. Can be LEGACY (default) or
   * STANDARD.
   *
   * Accepted values: LEGACY, STANDARD
   *
   * @param self::BGP_BEST_PATH_SELECTION_MODE_* $bgpBestPathSelectionMode
   */
  public function setBgpBestPathSelectionMode($bgpBestPathSelectionMode)
  {
    $this->bgpBestPathSelectionMode = $bgpBestPathSelectionMode;
  }
  /**
   * @return self::BGP_BEST_PATH_SELECTION_MODE_*
   */
  public function getBgpBestPathSelectionMode()
  {
    return $this->bgpBestPathSelectionMode;
  }
  /**
   * Allows to define a preferred approach for handling inter-region cost in the
   * selection process when using the STANDARD BGP best path selection
   * algorithm. Can be DEFAULT orADD_COST_TO_MED.
   *
   * Accepted values: ADD_COST_TO_MED, DEFAULT
   *
   * @param self::BGP_INTER_REGION_COST_* $bgpInterRegionCost
   */
  public function setBgpInterRegionCost($bgpInterRegionCost)
  {
    $this->bgpInterRegionCost = $bgpInterRegionCost;
  }
  /**
   * @return self::BGP_INTER_REGION_COST_*
   */
  public function getBgpInterRegionCost()
  {
    return $this->bgpInterRegionCost;
  }
  /**
   * Output only. [Output Only] Effective value of the bgp_always_compare_med
   * field.
   *
   * @param bool $effectiveBgpAlwaysCompareMed
   */
  public function setEffectiveBgpAlwaysCompareMed($effectiveBgpAlwaysCompareMed)
  {
    $this->effectiveBgpAlwaysCompareMed = $effectiveBgpAlwaysCompareMed;
  }
  /**
   * @return bool
   */
  public function getEffectiveBgpAlwaysCompareMed()
  {
    return $this->effectiveBgpAlwaysCompareMed;
  }
  /**
   * Output only. [Output Only] Effective value of the bgp_inter_region_cost
   * field.
   *
   * Accepted values: ADD_COST_TO_MED, DEFAULT
   *
   * @param self::EFFECTIVE_BGP_INTER_REGION_COST_* $effectiveBgpInterRegionCost
   */
  public function setEffectiveBgpInterRegionCost($effectiveBgpInterRegionCost)
  {
    $this->effectiveBgpInterRegionCost = $effectiveBgpInterRegionCost;
  }
  /**
   * @return self::EFFECTIVE_BGP_INTER_REGION_COST_*
   */
  public function getEffectiveBgpInterRegionCost()
  {
    return $this->effectiveBgpInterRegionCost;
  }
  /**
   * The network-wide routing mode to use. If set to REGIONAL, this network's
   * Cloud Routers will only advertise routes with subnets of this network in
   * the same region as the router. If set toGLOBAL, this network's Cloud
   * Routers will advertise routes with all subnets of this network, across
   * regions.
   *
   * Accepted values: GLOBAL, REGIONAL
   *
   * @param self::ROUTING_MODE_* $routingMode
   */
  public function setRoutingMode($routingMode)
  {
    $this->routingMode = $routingMode;
  }
  /**
   * @return self::ROUTING_MODE_*
   */
  public function getRoutingMode()
  {
    return $this->routingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkRoutingConfig::class, 'Google_Service_Compute_NetworkRoutingConfig');
