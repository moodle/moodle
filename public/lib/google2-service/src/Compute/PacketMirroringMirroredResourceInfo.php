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

class PacketMirroringMirroredResourceInfo extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $instancesType = PacketMirroringMirroredResourceInfoInstanceInfo::class;
  protected $instancesDataType = 'array';
  protected $subnetworksType = PacketMirroringMirroredResourceInfoSubnetInfo::class;
  protected $subnetworksDataType = 'array';
  /**
   * A set of mirrored tags. Traffic from/to all VM instances that have one or
   * more of these tags will be mirrored.
   *
   * @var string[]
   */
  public $tags;

  /**
   * A set of virtual machine instances that are being mirrored. They must live
   * in zones contained in the same region as this packetMirroring.
   *
   * Note that this config will apply only to those network interfaces of the
   * Instances that belong to the network specified in this packetMirroring.
   *
   * You may specify a maximum of 50 Instances.
   *
   * @param PacketMirroringMirroredResourceInfoInstanceInfo[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return PacketMirroringMirroredResourceInfoInstanceInfo[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * A set of subnetworks for which traffic from/to all VM instances will be
   * mirrored. They must live in the same region as this packetMirroring.
   *
   * You may specify a maximum of 5 subnetworks.
   *
   * @param PacketMirroringMirroredResourceInfoSubnetInfo[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return PacketMirroringMirroredResourceInfoSubnetInfo[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
  /**
   * A set of mirrored tags. Traffic from/to all VM instances that have one or
   * more of these tags will be mirrored.
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
class_alias(PacketMirroringMirroredResourceInfo::class, 'Google_Service_Compute_PacketMirroringMirroredResourceInfo');
