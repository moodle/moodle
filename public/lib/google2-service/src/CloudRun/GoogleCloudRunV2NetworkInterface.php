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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2NetworkInterface extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Optional. The VPC network that the Cloud Run resource will be able to send
   * traffic to. At least one of network or subnetwork must be specified. If
   * both network and subnetwork are specified, the given VPC subnetwork must
   * belong to the given VPC network. If network is not specified, it will be
   * looked up from the subnetwork.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The VPC subnetwork that the Cloud Run resource will get IPs from.
   * At least one of network or subnetwork must be specified. If both network
   * and subnetwork are specified, the given VPC subnetwork must belong to the
   * given VPC network. If subnetwork is not specified, the subnetwork with the
   * same name with the network will be used.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Optional. Network tags applied to this Cloud Run resource.
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. The VPC network that the Cloud Run resource will be able to send
   * traffic to. At least one of network or subnetwork must be specified. If
   * both network and subnetwork are specified, the given VPC subnetwork must
   * belong to the given VPC network. If network is not specified, it will be
   * looked up from the subnetwork.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The VPC subnetwork that the Cloud Run resource will get IPs from.
   * At least one of network or subnetwork must be specified. If both network
   * and subnetwork are specified, the given VPC subnetwork must belong to the
   * given VPC network. If subnetwork is not specified, the subnetwork with the
   * same name with the network will be used.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Optional. Network tags applied to this Cloud Run resource.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2NetworkInterface::class, 'Google_Service_CloudRun_GoogleCloudRunV2NetworkInterface');
