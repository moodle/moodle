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

namespace Google\Service\GKEOnPrem;

class ListVmwareClustersResponse extends \Google\Collection
{
  protected $collection_key = 'vmwareClusters';
  /**
   * A token identifying a page of results the server should return. If the
   * token is not empty this means that more results are available and should be
   * retrieved by repeating the request with the provided page token.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;
  protected $vmwareClustersType = VmwareCluster::class;
  protected $vmwareClustersDataType = 'array';

  /**
   * A token identifying a page of results the server should return. If the
   * token is not empty this means that more results are available and should be
   * retrieved by repeating the request with the provided page token.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
  /**
   * The list of VMware Cluster.
   *
   * @param VmwareCluster[] $vmwareClusters
   */
  public function setVmwareClusters($vmwareClusters)
  {
    $this->vmwareClusters = $vmwareClusters;
  }
  /**
   * @return VmwareCluster[]
   */
  public function getVmwareClusters()
  {
    return $this->vmwareClusters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListVmwareClustersResponse::class, 'Google_Service_GKEOnPrem_ListVmwareClustersResponse');
