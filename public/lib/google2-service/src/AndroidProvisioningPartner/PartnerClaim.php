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

namespace Google\Service\AndroidProvisioningPartner;

class PartnerClaim extends \Google\Model
{
  /**
   * Unspecified section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_UNSPECIFIED = 'SECTION_TYPE_UNSPECIFIED';
  /**
   * SIM-lock section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_SIM_LOCK = 'SECTION_TYPE_SIM_LOCK';
  /**
   * Zero-touch enrollment section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_ZERO_TOUCH = 'SECTION_TYPE_ZERO_TOUCH';
  /**
   * Optional. The ID of the configuration applied to the device section.
   *
   * @var string
   */
  public $configurationId;
  /**
   * The ID of the customer for whom the device is being claimed.
   *
   * @var string
   */
  public $customerId;
  protected $deviceIdentifierType = DeviceIdentifier::class;
  protected $deviceIdentifierDataType = '';
  protected $deviceMetadataType = DeviceMetadata::class;
  protected $deviceMetadataDataType = '';
  /**
   * The Google Workspace customer ID.
   *
   * @var string
   */
  public $googleWorkspaceCustomerId;
  /**
   * Optional. Must and can only be set for Chrome OS devices.
   *
   * @var string
   */
  public $preProvisioningToken;
  /**
   * Required. The section type of the device's provisioning record.
   *
   * @var string
   */
  public $sectionType;
  /**
   * Optional. Must and can only be set when DeviceProvisioningSectionType is
   * SECTION_TYPE_SIM_LOCK. The unique identifier of the SimLock profile.
   *
   * @var string
   */
  public $simlockProfileId;

  /**
   * Optional. The ID of the configuration applied to the device section.
   *
   * @param string $configurationId
   */
  public function setConfigurationId($configurationId)
  {
    $this->configurationId = $configurationId;
  }
  /**
   * @return string
   */
  public function getConfigurationId()
  {
    return $this->configurationId;
  }
  /**
   * The ID of the customer for whom the device is being claimed.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Required. Required. Device identifier of the device.
   *
   * @param DeviceIdentifier $deviceIdentifier
   */
  public function setDeviceIdentifier(DeviceIdentifier $deviceIdentifier)
  {
    $this->deviceIdentifier = $deviceIdentifier;
  }
  /**
   * @return DeviceIdentifier
   */
  public function getDeviceIdentifier()
  {
    return $this->deviceIdentifier;
  }
  /**
   * Required. The metadata to attach to the device at claim.
   *
   * @param DeviceMetadata $deviceMetadata
   */
  public function setDeviceMetadata(DeviceMetadata $deviceMetadata)
  {
    $this->deviceMetadata = $deviceMetadata;
  }
  /**
   * @return DeviceMetadata
   */
  public function getDeviceMetadata()
  {
    return $this->deviceMetadata;
  }
  /**
   * The Google Workspace customer ID.
   *
   * @param string $googleWorkspaceCustomerId
   */
  public function setGoogleWorkspaceCustomerId($googleWorkspaceCustomerId)
  {
    $this->googleWorkspaceCustomerId = $googleWorkspaceCustomerId;
  }
  /**
   * @return string
   */
  public function getGoogleWorkspaceCustomerId()
  {
    return $this->googleWorkspaceCustomerId;
  }
  /**
   * Optional. Must and can only be set for Chrome OS devices.
   *
   * @param string $preProvisioningToken
   */
  public function setPreProvisioningToken($preProvisioningToken)
  {
    $this->preProvisioningToken = $preProvisioningToken;
  }
  /**
   * @return string
   */
  public function getPreProvisioningToken()
  {
    return $this->preProvisioningToken;
  }
  /**
   * Required. The section type of the device's provisioning record.
   *
   * Accepted values: SECTION_TYPE_UNSPECIFIED, SECTION_TYPE_SIM_LOCK,
   * SECTION_TYPE_ZERO_TOUCH
   *
   * @param self::SECTION_TYPE_* $sectionType
   */
  public function setSectionType($sectionType)
  {
    $this->sectionType = $sectionType;
  }
  /**
   * @return self::SECTION_TYPE_*
   */
  public function getSectionType()
  {
    return $this->sectionType;
  }
  /**
   * Optional. Must and can only be set when DeviceProvisioningSectionType is
   * SECTION_TYPE_SIM_LOCK. The unique identifier of the SimLock profile.
   *
   * @param string $simlockProfileId
   */
  public function setSimlockProfileId($simlockProfileId)
  {
    $this->simlockProfileId = $simlockProfileId;
  }
  /**
   * @return string
   */
  public function getSimlockProfileId()
  {
    return $this->simlockProfileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnerClaim::class, 'Google_Service_AndroidProvisioningPartner_PartnerClaim');
