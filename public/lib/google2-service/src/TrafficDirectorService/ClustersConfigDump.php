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

namespace Google\Service\TrafficDirectorService;

class ClustersConfigDump extends \Google\Collection
{
  protected $collection_key = 'staticClusters';
  protected $dynamicActiveClustersType = DynamicCluster::class;
  protected $dynamicActiveClustersDataType = 'array';
  protected $dynamicWarmingClustersType = DynamicCluster::class;
  protected $dynamicWarmingClustersDataType = 'array';
  protected $staticClustersType = StaticCluster::class;
  protected $staticClustersDataType = 'array';
  /**
   * This is the :ref:`version_info ` in the last processed CDS discovery
   * response. If there are only static bootstrap clusters, this field will be
   * "".
   *
   * @var string
   */
  public $versionInfo;

  /**
   * The dynamically loaded active clusters. These are clusters that are
   * available to service data plane traffic.
   *
   * @param DynamicCluster[] $dynamicActiveClusters
   */
  public function setDynamicActiveClusters($dynamicActiveClusters)
  {
    $this->dynamicActiveClusters = $dynamicActiveClusters;
  }
  /**
   * @return DynamicCluster[]
   */
  public function getDynamicActiveClusters()
  {
    return $this->dynamicActiveClusters;
  }
  /**
   * The dynamically loaded warming clusters. These are clusters that are
   * currently undergoing warming in preparation to service data plane traffic.
   * Note that if attempting to recreate an Envoy configuration from a
   * configuration dump, the warming clusters should generally be discarded.
   *
   * @param DynamicCluster[] $dynamicWarmingClusters
   */
  public function setDynamicWarmingClusters($dynamicWarmingClusters)
  {
    $this->dynamicWarmingClusters = $dynamicWarmingClusters;
  }
  /**
   * @return DynamicCluster[]
   */
  public function getDynamicWarmingClusters()
  {
    return $this->dynamicWarmingClusters;
  }
  /**
   * The statically loaded cluster configs.
   *
   * @param StaticCluster[] $staticClusters
   */
  public function setStaticClusters($staticClusters)
  {
    $this->staticClusters = $staticClusters;
  }
  /**
   * @return StaticCluster[]
   */
  public function getStaticClusters()
  {
    return $this->staticClusters;
  }
  /**
   * This is the :ref:`version_info ` in the last processed CDS discovery
   * response. If there are only static bootstrap clusters, this field will be
   * "".
   *
   * @param string $versionInfo
   */
  public function setVersionInfo($versionInfo)
  {
    $this->versionInfo = $versionInfo;
  }
  /**
   * @return string
   */
  public function getVersionInfo()
  {
    return $this->versionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClustersConfigDump::class, 'Google_Service_TrafficDirectorService_ClustersConfigDump');
