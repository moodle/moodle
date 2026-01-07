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

class LicenseCode extends \Google\Collection
{
  /**
   * Machines are not allowed to attach boot disks with this License Code.
   * Requests to create new resources with this license will be rejected.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Use is allowed for anyone with USE_READ_ONLY access to this License Code.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Use of this license is limited to a project whitelist.
   */
  public const STATE_RESTRICTED = 'RESTRICTED';
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Reserved state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  protected $collection_key = 'licenseAlias';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Output only. [Output Only] Description of this License Code.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of resource. Always compute#licenseCode for
   * licenses.
   *
   * @var string
   */
  public $kind;
  protected $licenseAliasType = LicenseCodeLicenseAlias::class;
  protected $licenseAliasDataType = 'array';
  /**
   * Output only. [Output Only] Name of the resource. The name is 1-20
   * characters long and must be a valid 64 bit integer.
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
   * Output only. [Output Only] Current state of this License Code.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output Only] If true, the license will remain attached when
   * creating images or snapshots from disks. Otherwise, the license is not
   * transferred.
   *
   * @var bool
   */
  public $transferable;

  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
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
   * Output only. [Output Only] Description of this License Code.
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
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output Only] Type of resource. Always compute#licenseCode for
   * licenses.
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
   * [Output Only] URL and description aliases of Licenses with the same License
   * Code.
   *
   * @param LicenseCodeLicenseAlias[] $licenseAlias
   */
  public function setLicenseAlias($licenseAlias)
  {
    $this->licenseAlias = $licenseAlias;
  }
  /**
   * @return LicenseCodeLicenseAlias[]
   */
  public function getLicenseAlias()
  {
    return $this->licenseAlias;
  }
  /**
   * Output only. [Output Only] Name of the resource. The name is 1-20
   * characters long and must be a valid 64 bit integer.
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
   * Output only. [Output Only] Current state of this License Code.
   *
   * Accepted values: DISABLED, ENABLED, RESTRICTED, STATE_UNSPECIFIED,
   * TERMINATED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. [Output Only] If true, the license will remain attached when
   * creating images or snapshots from disks. Otherwise, the license is not
   * transferred.
   *
   * @param bool $transferable
   */
  public function setTransferable($transferable)
  {
    $this->transferable = $transferable;
  }
  /**
   * @return bool
   */
  public function getTransferable()
  {
    return $this->transferable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LicenseCode::class, 'Google_Service_Compute_LicenseCode');
