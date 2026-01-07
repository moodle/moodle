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

class License extends \Google\Collection
{
  protected $collection_key = 'requiredCoattachedLicenses';
  /**
   * Specifies licenseCodes of licenses that can replace this license. Note:
   * such replacements are allowed even if removable_from_disk is false.
   *
   * @var string[]
   */
  public $allowedReplacementLicenses;
  /**
   * If true, this license can be appended to an existing disk's set of
   * licenses.
   *
   * @var bool
   */
  public $appendableToDisk;
  /**
   * [Output Only] Deprecated. This field no longer reflects whether a license
   * charges a usage fee.
   *
   * @var bool
   */
  public $chargesUseFee;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional textual description of the resource; provided by the client
   * when the resource is created.
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
   * Specifies licenseCodes of licenses that are incompatible with this license.
   * If a license is incompatible with this license, it cannot be attached to
   * the same disk or image.
   *
   * @var string[]
   */
  public $incompatibleLicenses;
  /**
   * Output only. [Output Only] Type of resource. Always compute#license for
   * licenses.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] The unique code used to attach this license to images,
   * snapshots, and disks.
   *
   * @var string
   */
  public $licenseCode;
  protected $minimumRetentionType = Duration::class;
  protected $minimumRetentionDataType = '';
  /**
   * If true, this license can only be used on VMs on multi tenant nodes.
   *
   * @var bool
   */
  public $multiTenantOnly;
  /**
   * Name of the resource. The name must be 1-63 characters long and comply
   * withRFC1035.
   *
   * @var string
   */
  public $name;
  /**
   * If true, indicates this is an OS license. Only one OS license can be
   * attached to a disk or image at a time.
   *
   * @var bool
   */
  public $osLicense;
  /**
   * If true, this license can be removed from a disk's set of licenses, with no
   * replacement license needed.
   *
   * @var bool
   */
  public $removableFromDisk;
  /**
   * Specifies the set of permissible coattached licenseCodes of licenses that
   * satisfy the coattachment requirement of this license. At least one license
   * from the set must be attached to the same disk or image as this license.
   *
   * @var string[]
   */
  public $requiredCoattachedLicenses;
  protected $resourceRequirementsType = LicenseResourceRequirements::class;
  protected $resourceRequirementsDataType = '';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * If true, this license can only be used on VMs on sole tenant nodes.
   *
   * @var bool
   */
  public $soleTenantOnly;
  /**
   * If false, licenses will not be copied from the source resource when
   * creating an image from a disk, disk from snapshot, or snapshot from disk.
   *
   * @var bool
   */
  public $transferable;
  /**
   * Output only. [Output Only] Last update timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $updateTimestamp;

  /**
   * Specifies licenseCodes of licenses that can replace this license. Note:
   * such replacements are allowed even if removable_from_disk is false.
   *
   * @param string[] $allowedReplacementLicenses
   */
  public function setAllowedReplacementLicenses($allowedReplacementLicenses)
  {
    $this->allowedReplacementLicenses = $allowedReplacementLicenses;
  }
  /**
   * @return string[]
   */
  public function getAllowedReplacementLicenses()
  {
    return $this->allowedReplacementLicenses;
  }
  /**
   * If true, this license can be appended to an existing disk's set of
   * licenses.
   *
   * @param bool $appendableToDisk
   */
  public function setAppendableToDisk($appendableToDisk)
  {
    $this->appendableToDisk = $appendableToDisk;
  }
  /**
   * @return bool
   */
  public function getAppendableToDisk()
  {
    return $this->appendableToDisk;
  }
  /**
   * [Output Only] Deprecated. This field no longer reflects whether a license
   * charges a usage fee.
   *
   * @param bool $chargesUseFee
   */
  public function setChargesUseFee($chargesUseFee)
  {
    $this->chargesUseFee = $chargesUseFee;
  }
  /**
   * @return bool
   */
  public function getChargesUseFee()
  {
    return $this->chargesUseFee;
  }
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
   * An optional textual description of the resource; provided by the client
   * when the resource is created.
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
   * Specifies licenseCodes of licenses that are incompatible with this license.
   * If a license is incompatible with this license, it cannot be attached to
   * the same disk or image.
   *
   * @param string[] $incompatibleLicenses
   */
  public function setIncompatibleLicenses($incompatibleLicenses)
  {
    $this->incompatibleLicenses = $incompatibleLicenses;
  }
  /**
   * @return string[]
   */
  public function getIncompatibleLicenses()
  {
    return $this->incompatibleLicenses;
  }
  /**
   * Output only. [Output Only] Type of resource. Always compute#license for
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
   * [Output Only] The unique code used to attach this license to images,
   * snapshots, and disks.
   *
   * @param string $licenseCode
   */
  public function setLicenseCode($licenseCode)
  {
    $this->licenseCode = $licenseCode;
  }
  /**
   * @return string
   */
  public function getLicenseCode()
  {
    return $this->licenseCode;
  }
  /**
   * If set, this license will be unable to be removed or replaced once attached
   * to a disk until the minimum_retention period has passed.
   *
   * @param Duration $minimumRetention
   */
  public function setMinimumRetention(Duration $minimumRetention)
  {
    $this->minimumRetention = $minimumRetention;
  }
  /**
   * @return Duration
   */
  public function getMinimumRetention()
  {
    return $this->minimumRetention;
  }
  /**
   * If true, this license can only be used on VMs on multi tenant nodes.
   *
   * @param bool $multiTenantOnly
   */
  public function setMultiTenantOnly($multiTenantOnly)
  {
    $this->multiTenantOnly = $multiTenantOnly;
  }
  /**
   * @return bool
   */
  public function getMultiTenantOnly()
  {
    return $this->multiTenantOnly;
  }
  /**
   * Name of the resource. The name must be 1-63 characters long and comply
   * withRFC1035.
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
   * If true, indicates this is an OS license. Only one OS license can be
   * attached to a disk or image at a time.
   *
   * @param bool $osLicense
   */
  public function setOsLicense($osLicense)
  {
    $this->osLicense = $osLicense;
  }
  /**
   * @return bool
   */
  public function getOsLicense()
  {
    return $this->osLicense;
  }
  /**
   * If true, this license can be removed from a disk's set of licenses, with no
   * replacement license needed.
   *
   * @param bool $removableFromDisk
   */
  public function setRemovableFromDisk($removableFromDisk)
  {
    $this->removableFromDisk = $removableFromDisk;
  }
  /**
   * @return bool
   */
  public function getRemovableFromDisk()
  {
    return $this->removableFromDisk;
  }
  /**
   * Specifies the set of permissible coattached licenseCodes of licenses that
   * satisfy the coattachment requirement of this license. At least one license
   * from the set must be attached to the same disk or image as this license.
   *
   * @param string[] $requiredCoattachedLicenses
   */
  public function setRequiredCoattachedLicenses($requiredCoattachedLicenses)
  {
    $this->requiredCoattachedLicenses = $requiredCoattachedLicenses;
  }
  /**
   * @return string[]
   */
  public function getRequiredCoattachedLicenses()
  {
    return $this->requiredCoattachedLicenses;
  }
  /**
   * [Input Only] Deprecated.
   *
   * @param LicenseResourceRequirements $resourceRequirements
   */
  public function setResourceRequirements(LicenseResourceRequirements $resourceRequirements)
  {
    $this->resourceRequirements = $resourceRequirements;
  }
  /**
   * @return LicenseResourceRequirements
   */
  public function getResourceRequirements()
  {
    return $this->resourceRequirements;
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
   * If true, this license can only be used on VMs on sole tenant nodes.
   *
   * @param bool $soleTenantOnly
   */
  public function setSoleTenantOnly($soleTenantOnly)
  {
    $this->soleTenantOnly = $soleTenantOnly;
  }
  /**
   * @return bool
   */
  public function getSoleTenantOnly()
  {
    return $this->soleTenantOnly;
  }
  /**
   * If false, licenses will not be copied from the source resource when
   * creating an image from a disk, disk from snapshot, or snapshot from disk.
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
  /**
   * Output only. [Output Only] Last update timestamp inRFC3339 text format.
   *
   * @param string $updateTimestamp
   */
  public function setUpdateTimestamp($updateTimestamp)
  {
    $this->updateTimestamp = $updateTimestamp;
  }
  /**
   * @return string
   */
  public function getUpdateTimestamp()
  {
    return $this->updateTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(License::class, 'Google_Service_Compute_License');
