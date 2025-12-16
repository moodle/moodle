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

class Zone extends \Google\Collection
{
  public const STATUS_DOWN = 'DOWN';
  public const STATUS_UP = 'UP';
  protected $collection_key = 'availableCpuPlatforms';
  /**
   * [Output Only] Available cpu/platform selections for the zone.
   *
   * @var string[]
   */
  public $availableCpuPlatforms;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deprecatedType = DeprecationStatus::class;
  protected $deprecatedDataType = '';
  /**
   * [Output Only] Textual description of the resource.
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
   * Output only. [Output Only] Type of the resource. Always compute#zone for
   * zones.
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
   * [Output Only] Full URL reference to the region which hosts the zone.
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
   * [Output Only] Status of the zone, either UP orDOWN.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $supportsPzs;

  /**
   * [Output Only] Available cpu/platform selections for the zone.
   *
   * @param string[] $availableCpuPlatforms
   */
  public function setAvailableCpuPlatforms($availableCpuPlatforms)
  {
    $this->availableCpuPlatforms = $availableCpuPlatforms;
  }
  /**
   * @return string[]
   */
  public function getAvailableCpuPlatforms()
  {
    return $this->availableCpuPlatforms;
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
   * [Output Only] The deprecation status associated with this zone.
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
   * [Output Only] Textual description of the resource.
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
   * Output only. [Output Only] Type of the resource. Always compute#zone for
   * zones.
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
   * [Output Only] Full URL reference to the region which hosts the zone.
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
   * [Output Only] Status of the zone, either UP orDOWN.
   *
   * Accepted values: DOWN, UP
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $supportsPzs
   */
  public function setSupportsPzs($supportsPzs)
  {
    $this->supportsPzs = $supportsPzs;
  }
  /**
   * @return bool
   */
  public function getSupportsPzs()
  {
    return $this->supportsPzs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Zone::class, 'Google_Service_Compute_Zone');
