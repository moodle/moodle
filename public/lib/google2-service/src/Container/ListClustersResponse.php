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

class ListClustersResponse extends \Google\Collection
{
  protected $collection_key = 'missingZones';
  protected $clustersType = Cluster::class;
  protected $clustersDataType = 'array';
  /**
   * If any zones are listed here, the list of clusters returned may be missing
   * those zones.
   *
   * @var string[]
   */
  public $missingZones;

  /**
   * A list of clusters in the project in the specified zone, or across all
   * ones.
   *
   * @param Cluster[] $clusters
   */
  public function setClusters($clusters)
  {
    $this->clusters = $clusters;
  }
  /**
   * @return Cluster[]
   */
  public function getClusters()
  {
    return $this->clusters;
  }
  /**
   * If any zones are listed here, the list of clusters returned may be missing
   * those zones.
   *
   * @param string[] $missingZones
   */
  public function setMissingZones($missingZones)
  {
    $this->missingZones = $missingZones;
  }
  /**
   * @return string[]
   */
  public function getMissingZones()
  {
    return $this->missingZones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListClustersResponse::class, 'Google_Service_Container_ListClustersResponse');
