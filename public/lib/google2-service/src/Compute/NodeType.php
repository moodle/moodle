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

class NodeType extends \Google\Model
{
  /**
   * [Output Only] The CPU platform used by this node type.
   *
   * @var string
   */
  public $cpuPlatform;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deprecatedType = DeprecationStatus::class;
  protected $deprecatedDataType = '';
  /**
   * [Output Only] An optional textual description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The number of virtual CPUs that are available to the node
   * type.
   *
   * @var int
   */
  public $guestCpus;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The type of the resource. Alwayscompute#nodeType
   * for node types.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Local SSD available to the node type, defined in GB.
   *
   * @var int
   */
  public $localSsdGb;
  /**
   * Output only. [Output Only] Maximum number of VMs that can be created for
   * this node type.
   *
   * @var int
   */
  public $maxVms;
  /**
   * [Output Only] The amount of physical memory available to the node type,
   * defined in MB.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] The name of the zone where the node type
   * resides, such as us-central1-a.
   *
   * @var string
   */
  public $zone;

  /**
   * [Output Only] The CPU platform used by this node type.
   *
   * @param string $cpuPlatform
   */
  public function setCpuPlatform($cpuPlatform)
  {
    $this->cpuPlatform = $cpuPlatform;
  }
  /**
   * @return string
   */
  public function getCpuPlatform()
  {
    return $this->cpuPlatform;
  }
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * [Output Only] The deprecation status associated with this node type.
   *
   * @param DeprecationStatus $deprecated
   */
  public function setDeprecated(DeprecationStatus $deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return DeprecationStatus
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * [Output Only] An optional textual description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * [Output Only] The number of virtual CPUs that are available to the node
   * type.
   *
   * @param int $guestCpus
   */
  public function setGuestCpus($guestCpus)
  {
    $this->guestCpus = $guestCpus;
  }
  /**
   * @return int
   */
  public function getGuestCpus()
  {
    return $this->guestCpus;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] The type of the resource. Alwayscompute#nodeType
   * for node types.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * [Output Only] Local SSD available to the node type, defined in GB.
   *
   * @param int $localSsdGb
   */
  public function setLocalSsdGb($localSsdGb)
  {
    $this->localSsdGb = $localSsdGb;
  }
  /**
   * @return int
   */
  public function getLocalSsdGb()
  {
    return $this->localSsdGb;
  }
  /**
   * Output only. [Output Only] Maximum number of VMs that can be created for
   * this node type.
   *
   * @param int $maxVms
   */
  public function setMaxVms($maxVms)
  {
    $this->maxVms = $maxVms;
  }
  /**
   * @return int
   */
  public function getMaxVms()
  {
    return $this->maxVms;
  }
  /**
   * [Output Only] The amount of physical memory available to the node type,
   * defined in MB.
   *
   * @param int $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * [Output Only] Name of the resource.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] The name of the zone where the node type
   * resides, such as us-central1-a.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeType::class, 'Google_Service_Compute_NodeType');
