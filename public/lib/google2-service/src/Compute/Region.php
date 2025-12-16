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

class Region extends \Google\Collection
{
  public const STATUS_DOWN = 'DOWN';
  public const STATUS_UP = 'UP';
  protected $collection_key = 'zones';
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
   * Output only. [Output Only] Type of the resource. Always compute#region for
   * regions.
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
  protected $quotaStatusWarningType = RegionQuotaStatusWarning::class;
  protected $quotaStatusWarningDataType = '';
  protected $quotasType = Quota::class;
  protected $quotasDataType = 'array';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] Status of the region, either UP orDOWN.
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
   * [Output Only] A list of zones available in this region, in the form of
   * resource URLs.
   *
   * @var string[]
   */
  public $zones;

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
   * [Output Only] The deprecation status associated with this region.
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
   * Output only. [Output Only] Type of the resource. Always compute#region for
   * regions.
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
   * Output only. [Output Only] Warning of fetching the `quotas` field for this
   * region. This field is populated only if fetching of the `quotas` field
   * fails.
   *
   * @param RegionQuotaStatusWarning $quotaStatusWarning
   */
  public function setQuotaStatusWarning(RegionQuotaStatusWarning $quotaStatusWarning)
  {
    $this->quotaStatusWarning = $quotaStatusWarning;
  }
  /**
   * @return RegionQuotaStatusWarning
   */
  public function getQuotaStatusWarning()
  {
    return $this->quotaStatusWarning;
  }
  /**
   * [Output Only] Quotas assigned to this region.
   *
   * @param Quota[] $quotas
   */
  public function setQuotas($quotas)
  {
    $this->quotas = $quotas;
  }
  /**
   * @return Quota[]
   */
  public function getQuotas()
  {
    return $this->quotas;
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
   * [Output Only] Status of the region, either UP orDOWN.
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
  /**
   * [Output Only] A list of zones available in this region, in the form of
   * resource URLs.
   *
   * @param string[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return string[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Region::class, 'Google_Service_Compute_Region');
