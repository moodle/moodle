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

class PreservedState extends \Google\Model
{
  protected $disksType = PreservedStatePreservedDisk::class;
  protected $disksDataType = 'map';
  protected $externalIPsType = PreservedStatePreservedNetworkIp::class;
  protected $externalIPsDataType = 'map';
  protected $internalIPsType = PreservedStatePreservedNetworkIp::class;
  protected $internalIPsDataType = 'map';
  /**
   * Preserved metadata defined for this instance.
   *
   * @var string[]
   */
  public $metadata;

  /**
   * Preserved disks defined for this instance. This map is keyed with the
   * device names of the disks.
   *
   * @param PreservedStatePreservedDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return PreservedStatePreservedDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Preserved external IPs defined for this instance. This map is keyed with
   * the name of the network interface.
   *
   * @param PreservedStatePreservedNetworkIp[] $externalIPs
   */
  public function setExternalIPs($externalIPs)
  {
    $this->externalIPs = $externalIPs;
  }
  /**
   * @return PreservedStatePreservedNetworkIp[]
   */
  public function getExternalIPs()
  {
    return $this->externalIPs;
  }
  /**
   * Preserved internal IPs defined for this instance. This map is keyed with
   * the name of the network interface.
   *
   * @param PreservedStatePreservedNetworkIp[] $internalIPs
   */
  public function setInternalIPs($internalIPs)
  {
    $this->internalIPs = $internalIPs;
  }
  /**
   * @return PreservedStatePreservedNetworkIp[]
   */
  public function getInternalIPs()
  {
    return $this->internalIPs;
  }
  /**
   * Preserved metadata defined for this instance.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreservedState::class, 'Google_Service_Compute_PreservedState');
