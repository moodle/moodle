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

class DiskType extends \Google\Model
{
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * [Output Only] Server-defined default disk size in GB.
   *
   * @var string
   */
  public $defaultDiskSizeGb;
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
   * Output only. [Output Only] Type of the resource. Always compute#diskType
   * for disk types.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * [Output Only] URL of the region where the disk type resides. Only
   * applicable for regional resources. You must specify this field as part of
   * the HTTP request URL. It is not settable as a field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] An optional textual description of the valid disk size, such
   * as "10GB-10TB".
   *
   * @var string
   */
  public $validDiskSize;
  /**
   * [Output Only] URL of the zone where the disk type resides. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
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
   * [Output Only] Server-defined default disk size in GB.
   *
   * @param string $defaultDiskSizeGb
   */
  public function setDefaultDiskSizeGb($defaultDiskSizeGb)
  {
    $this->defaultDiskSizeGb = $defaultDiskSizeGb;
  }
  /**
   * @return string
   */
  public function getDefaultDiskSizeGb()
  {
    return $this->defaultDiskSizeGb;
  }
  /**
   * [Output Only] The deprecation status associated with this disk type.
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
   * Output only. [Output Only] Type of the resource. Always compute#diskType
   * for disk types.
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
   * [Output Only] URL of the region where the disk type resides. Only
   * applicable for regional resources. You must specify this field as part of
   * the HTTP request URL. It is not settable as a field in the request body.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
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
   * [Output Only] An optional textual description of the valid disk size, such
   * as "10GB-10TB".
   *
   * @param string $validDiskSize
   */
  public function setValidDiskSize($validDiskSize)
  {
    $this->validDiskSize = $validDiskSize;
  }
  /**
   * @return string
   */
  public function getValidDiskSize()
  {
    return $this->validDiskSize;
  }
  /**
   * [Output Only] URL of the zone where the disk type resides. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
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
class_alias(DiskType::class, 'Google_Service_Compute_DiskType');
