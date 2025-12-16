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

namespace Google\Service\CloudDeploy;

class AssociatedEntities extends \Google\Collection
{
  protected $collection_key = 'gkeClusters';
  protected $anthosClustersType = AnthosCluster::class;
  protected $anthosClustersDataType = 'array';
  protected $gkeClustersType = GkeCluster::class;
  protected $gkeClustersDataType = 'array';

  /**
   * Optional. Information specifying Anthos clusters as associated entities.
   *
   * @param AnthosCluster[] $anthosClusters
   */
  public function setAnthosClusters($anthosClusters)
  {
    $this->anthosClusters = $anthosClusters;
  }
  /**
   * @return AnthosCluster[]
   */
  public function getAnthosClusters()
  {
    return $this->anthosClusters;
  }
  /**
   * Optional. Information specifying GKE clusters as associated entities.
   *
   * @param GkeCluster[] $gkeClusters
   */
  public function setGkeClusters($gkeClusters)
  {
    $this->gkeClusters = $gkeClusters;
  }
  /**
   * @return GkeCluster[]
   */
  public function getGkeClusters()
  {
    return $this->gkeClusters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssociatedEntities::class, 'Google_Service_CloudDeploy_AssociatedEntities');
