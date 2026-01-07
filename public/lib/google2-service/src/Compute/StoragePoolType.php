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

class StoragePoolType extends \Google\Collection
{
  protected $collection_key = 'supportedDiskTypes';
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deprecatedType = DeprecationStatus::class;
  protected $deprecatedDataType = '';
  /**
   * [Output Only] An optional description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#storagePoolType for storage pool types.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Maximum storage pool size in GB.
   *
   * @var string
   */
  public $maxPoolProvisionedCapacityGb;
  /**
   * [Output Only] Maximum provisioned IOPS.
   *
   * @var string
   */
  public $maxPoolProvisionedIops;
  /**
   * [Output Only] Maximum provisioned throughput.
   *
   * @var string
   */
  public $maxPoolProvisionedThroughput;
  /**
   * [Output Only] Minimum storage pool size in GB.
   *
   * @var string
   */
  public $minPoolProvisionedCapacityGb;
  /**
   * [Output Only] Minimum provisioned IOPS.
   *
   * @var string
   */
  public $minPoolProvisionedIops;
  /**
   * [Output Only] Minimum provisioned throughput.
   *
   * @var string
   */
  public $minPoolProvisionedThroughput;
  /**
   * [Deprecated] This field is deprecated. Use minPoolProvisionedCapacityGb
   * instead.
   *
   * @var string
   */
  public $minSizeGb;
  /**
   * [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * [Output Only] The list of disk types supported in this storage pool type.
   *
   * @var string[]
   */
  public $supportedDiskTypes;
  /**
   * [Output Only] URL of the zone where the storage pool type resides. You must
   * specify this field as part of the HTTP request URL. It is not settable as a
   * field in the request body.
   *
   * @var string
   */
  public $zone;

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
   * [Output Only] The deprecation status associated with this storage pool
   * type.
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
   * [Output Only] An optional description of this resource.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#storagePoolType for storage pool types.
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
   * [Output Only] Maximum storage pool size in GB.
   *
   * @param string $maxPoolProvisionedCapacityGb
   */
  public function setMaxPoolProvisionedCapacityGb($maxPoolProvisionedCapacityGb)
  {
    $this->maxPoolProvisionedCapacityGb = $maxPoolProvisionedCapacityGb;
  }
  /**
   * @return string
   */
  public function getMaxPoolProvisionedCapacityGb()
  {
    return $this->maxPoolProvisionedCapacityGb;
  }
  /**
   * [Output Only] Maximum provisioned IOPS.
   *
   * @param string $maxPoolProvisionedIops
   */
  public function setMaxPoolProvisionedIops($maxPoolProvisionedIops)
  {
    $this->maxPoolProvisionedIops = $maxPoolProvisionedIops;
  }
  /**
   * @return string
   */
  public function getMaxPoolProvisionedIops()
  {
    return $this->maxPoolProvisionedIops;
  }
  /**
   * [Output Only] Maximum provisioned throughput.
   *
   * @param string $maxPoolProvisionedThroughput
   */
  public function setMaxPoolProvisionedThroughput($maxPoolProvisionedThroughput)
  {
    $this->maxPoolProvisionedThroughput = $maxPoolProvisionedThroughput;
  }
  /**
   * @return string
   */
  public function getMaxPoolProvisionedThroughput()
  {
    return $this->maxPoolProvisionedThroughput;
  }
  /**
   * [Output Only] Minimum storage pool size in GB.
   *
   * @param string $minPoolProvisionedCapacityGb
   */
  public function setMinPoolProvisionedCapacityGb($minPoolProvisionedCapacityGb)
  {
    $this->minPoolProvisionedCapacityGb = $minPoolProvisionedCapacityGb;
  }
  /**
   * @return string
   */
  public function getMinPoolProvisionedCapacityGb()
  {
    return $this->minPoolProvisionedCapacityGb;
  }
  /**
   * [Output Only] Minimum provisioned IOPS.
   *
   * @param string $minPoolProvisionedIops
   */
  public function setMinPoolProvisionedIops($minPoolProvisionedIops)
  {
    $this->minPoolProvisionedIops = $minPoolProvisionedIops;
  }
  /**
   * @return string
   */
  public function getMinPoolProvisionedIops()
  {
    return $this->minPoolProvisionedIops;
  }
  /**
   * [Output Only] Minimum provisioned throughput.
   *
   * @param string $minPoolProvisionedThroughput
   */
  public function setMinPoolProvisionedThroughput($minPoolProvisionedThroughput)
  {
    $this->minPoolProvisionedThroughput = $minPoolProvisionedThroughput;
  }
  /**
   * @return string
   */
  public function getMinPoolProvisionedThroughput()
  {
    return $this->minPoolProvisionedThroughput;
  }
  /**
   * [Deprecated] This field is deprecated. Use minPoolProvisionedCapacityGb
   * instead.
   *
   * @param string $minSizeGb
   */
  public function setMinSizeGb($minSizeGb)
  {
    $this->minSizeGb = $minSizeGb;
  }
  /**
   * @return string
   */
  public function getMinSizeGb()
  {
    return $this->minSizeGb;
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
   * [Output Only] Server-defined URL for the resource.
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
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * [Output Only] The list of disk types supported in this storage pool type.
   *
   * @param string[] $supportedDiskTypes
   */
  public function setSupportedDiskTypes($supportedDiskTypes)
  {
    $this->supportedDiskTypes = $supportedDiskTypes;
  }
  /**
   * @return string[]
   */
  public function getSupportedDiskTypes()
  {
    return $this->supportedDiskTypes;
  }
  /**
   * [Output Only] URL of the zone where the storage pool type resides. You must
   * specify this field as part of the HTTP request URL. It is not settable as a
   * field in the request body.
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
class_alias(StoragePoolType::class, 'Google_Service_Compute_StoragePoolType');
