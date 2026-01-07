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

namespace Google\Service\OracleDatabase;

class ExascaleDbStorageVaultProperties extends \Google\Collection
{
  /**
   * The state of the ExascaleDbStorageVault is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The ExascaleDbStorageVault is being provisioned.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The ExascaleDbStorageVault is available.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * The ExascaleDbStorageVault is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The ExascaleDbStorageVault is being deleted.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * The ExascaleDbStorageVault has been deleted.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * The ExascaleDbStorageVault has failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'vmClusterIds';
  /**
   * Optional. The size of additional flash cache in percentage of high capacity
   * database storage.
   *
   * @var int
   */
  public $additionalFlashCachePercent;
  /**
   * Output only. The shape attributes of the VM clusters attached to the
   * ExascaleDbStorageVault.
   *
   * @var string[]
   */
  public $attachedShapeAttributes;
  /**
   * Output only. The shape attributes available for the VM clusters to be
   * attached to the ExascaleDbStorageVault.
   *
   * @var string[]
   */
  public $availableShapeAttributes;
  /**
   * Optional. The description of the ExascaleDbStorageVault.
   *
   * @var string
   */
  public $description;
  protected $exascaleDbStorageDetailsType = ExascaleDbStorageDetails::class;
  protected $exascaleDbStorageDetailsDataType = '';
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @var string
   */
  public $ociUri;
  /**
   * Output only. The OCID for the ExascaleDbStorageVault.
   *
   * @var string
   */
  public $ocid;
  /**
   * Output only. The state of the ExascaleDbStorageVault.
   *
   * @var string
   */
  public $state;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';
  /**
   * Output only. The number of VM clusters associated with the
   * ExascaleDbStorageVault.
   *
   * @var int
   */
  public $vmClusterCount;
  /**
   * Output only. The list of VM cluster OCIDs associated with the
   * ExascaleDbStorageVault.
   *
   * @var string[]
   */
  public $vmClusterIds;

  /**
   * Optional. The size of additional flash cache in percentage of high capacity
   * database storage.
   *
   * @param int $additionalFlashCachePercent
   */
  public function setAdditionalFlashCachePercent($additionalFlashCachePercent)
  {
    $this->additionalFlashCachePercent = $additionalFlashCachePercent;
  }
  /**
   * @return int
   */
  public function getAdditionalFlashCachePercent()
  {
    return $this->additionalFlashCachePercent;
  }
  /**
   * Output only. The shape attributes of the VM clusters attached to the
   * ExascaleDbStorageVault.
   *
   * @param string[] $attachedShapeAttributes
   */
  public function setAttachedShapeAttributes($attachedShapeAttributes)
  {
    $this->attachedShapeAttributes = $attachedShapeAttributes;
  }
  /**
   * @return string[]
   */
  public function getAttachedShapeAttributes()
  {
    return $this->attachedShapeAttributes;
  }
  /**
   * Output only. The shape attributes available for the VM clusters to be
   * attached to the ExascaleDbStorageVault.
   *
   * @param string[] $availableShapeAttributes
   */
  public function setAvailableShapeAttributes($availableShapeAttributes)
  {
    $this->availableShapeAttributes = $availableShapeAttributes;
  }
  /**
   * @return string[]
   */
  public function getAvailableShapeAttributes()
  {
    return $this->availableShapeAttributes;
  }
  /**
   * Optional. The description of the ExascaleDbStorageVault.
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
   * Required. The storage details of the ExascaleDbStorageVault.
   *
   * @param ExascaleDbStorageDetails $exascaleDbStorageDetails
   */
  public function setExascaleDbStorageDetails(ExascaleDbStorageDetails $exascaleDbStorageDetails)
  {
    $this->exascaleDbStorageDetails = $exascaleDbStorageDetails;
  }
  /**
   * @return ExascaleDbStorageDetails
   */
  public function getExascaleDbStorageDetails()
  {
    return $this->exascaleDbStorageDetails;
  }
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @param string $ociUri
   */
  public function setOciUri($ociUri)
  {
    $this->ociUri = $ociUri;
  }
  /**
   * @return string
   */
  public function getOciUri()
  {
    return $this->ociUri;
  }
  /**
   * Output only. The OCID for the ExascaleDbStorageVault.
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Output only. The state of the ExascaleDbStorageVault.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, UPDATING,
   * TERMINATING, TERMINATED, FAILED
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
   * Output only. The time zone of the ExascaleDbStorageVault.
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Output only. The number of VM clusters associated with the
   * ExascaleDbStorageVault.
   *
   * @param int $vmClusterCount
   */
  public function setVmClusterCount($vmClusterCount)
  {
    $this->vmClusterCount = $vmClusterCount;
  }
  /**
   * @return int
   */
  public function getVmClusterCount()
  {
    return $this->vmClusterCount;
  }
  /**
   * Output only. The list of VM cluster OCIDs associated with the
   * ExascaleDbStorageVault.
   *
   * @param string[] $vmClusterIds
   */
  public function setVmClusterIds($vmClusterIds)
  {
    $this->vmClusterIds = $vmClusterIds;
  }
  /**
   * @return string[]
   */
  public function getVmClusterIds()
  {
    return $this->vmClusterIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExascaleDbStorageVaultProperties::class, 'Google_Service_OracleDatabase_ExascaleDbStorageVaultProperties');
