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

namespace Google\Service\BigtableAdmin;

class MultiClusterRoutingUseAny extends \Google\Collection
{
  protected $collection_key = 'clusterIds';
  /**
   * The set of clusters to route to. The order is ignored; clusters will be
   * tried in order of distance. If left empty, all clusters are eligible.
   *
   * @var string[]
   */
  public $clusterIds;
  protected $rowAffinityType = RowAffinity::class;
  protected $rowAffinityDataType = '';

  /**
   * The set of clusters to route to. The order is ignored; clusters will be
   * tried in order of distance. If left empty, all clusters are eligible.
   *
   * @param string[] $clusterIds
   */
  public function setClusterIds($clusterIds)
  {
    $this->clusterIds = $clusterIds;
  }
  /**
   * @return string[]
   */
  public function getClusterIds()
  {
    return $this->clusterIds;
  }
  /**
   * Row affinity sticky routing based on the row key of the request. Requests
   * that span multiple rows are routed non-deterministically.
   *
   * @param RowAffinity $rowAffinity
   */
  public function setRowAffinity(RowAffinity $rowAffinity)
  {
    $this->rowAffinity = $rowAffinity;
  }
  /**
   * @return RowAffinity
   */
  public function getRowAffinity()
  {
    return $this->rowAffinity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiClusterRoutingUseAny::class, 'Google_Service_BigtableAdmin_MultiClusterRoutingUseAny');
