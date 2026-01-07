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

namespace Google\Service\NetworkManagement;

class Host extends \Google\Collection
{
  protected $collection_key = 'cloudVirtualNetworkIds';
  /**
   * @var string
   */
  public $cloudInstanceId;
  /**
   * @var string
   */
  public $cloudProjectId;
  /**
   * @var string
   */
  public $cloudProvider;
  /**
   * @var string
   */
  public $cloudRegion;
  /**
   * @var string[]
   */
  public $cloudVirtualNetworkIds;
  /**
   * @var string
   */
  public $cloudVpcId;
  /**
   * @var string
   */
  public $cloudZone;
  /**
   * @var string
   */
  public $os;

  /**
   * @param string
   */
  public function setCloudInstanceId($cloudInstanceId)
  {
    $this->cloudInstanceId = $cloudInstanceId;
  }
  /**
   * @return string
   */
  public function getCloudInstanceId()
  {
    return $this->cloudInstanceId;
  }
  /**
   * @param string
   */
  public function setCloudProjectId($cloudProjectId)
  {
    $this->cloudProjectId = $cloudProjectId;
  }
  /**
   * @return string
   */
  public function getCloudProjectId()
  {
    return $this->cloudProjectId;
  }
  /**
   * @param string
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return string
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * @param string
   */
  public function setCloudRegion($cloudRegion)
  {
    $this->cloudRegion = $cloudRegion;
  }
  /**
   * @return string
   */
  public function getCloudRegion()
  {
    return $this->cloudRegion;
  }
  /**
   * @param string[]
   */
  public function setCloudVirtualNetworkIds($cloudVirtualNetworkIds)
  {
    $this->cloudVirtualNetworkIds = $cloudVirtualNetworkIds;
  }
  /**
   * @return string[]
   */
  public function getCloudVirtualNetworkIds()
  {
    return $this->cloudVirtualNetworkIds;
  }
  /**
   * @param string
   */
  public function setCloudVpcId($cloudVpcId)
  {
    $this->cloudVpcId = $cloudVpcId;
  }
  /**
   * @return string
   */
  public function getCloudVpcId()
  {
    return $this->cloudVpcId;
  }
  /**
   * @param string
   */
  public function setCloudZone($cloudZone)
  {
    $this->cloudZone = $cloudZone;
  }
  /**
   * @return string
   */
  public function getCloudZone()
  {
    return $this->cloudZone;
  }
  /**
   * @param string
   */
  public function setOs($os)
  {
    $this->os = $os;
  }
  /**
   * @return string
   */
  public function getOs()
  {
    return $this->os;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Host::class, 'Google_Service_NetworkManagement_Host');
