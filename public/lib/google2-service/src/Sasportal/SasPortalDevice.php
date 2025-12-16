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

namespace Google\Service\Sasportal;

class SasPortalDevice extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_DEVICE_STATE_UNSPECIFIED = 'DEVICE_STATE_UNSPECIFIED';
  /**
   * Device created in the SAS Portal, however, not yet registered with SAS.
   */
  public const STATE_RESERVED = 'RESERVED';
  /**
   * Device registered with SAS.
   */
  public const STATE_REGISTERED = 'REGISTERED';
  /**
   * Device de-registered with SAS.
   */
  public const STATE_DEREGISTERED = 'DEREGISTERED';
  protected $collection_key = 'grants';
  protected $activeConfigType = SasPortalDeviceConfig::class;
  protected $activeConfigDataType = '';
  protected $currentChannelsType = SasPortalChannelWithScore::class;
  protected $currentChannelsDataType = 'array';
  protected $deviceMetadataType = SasPortalDeviceMetadata::class;
  protected $deviceMetadataDataType = '';
  /**
   * Device display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The FCC identifier of the device. Refer to https://www.fcc.gov/oet/ea/fccid
   * for FccID format. Accept underscores and periods because some test-SAS
   * customers use them.
   *
   * @var string
   */
  public $fccId;
  protected $grantRangeAllowlistsType = SasPortalFrequencyRange::class;
  protected $grantRangeAllowlistsDataType = 'array';
  protected $grantsType = SasPortalDeviceGrant::class;
  protected $grantsDataType = 'array';
  /**
   * Output only. The resource path name.
   *
   * @var string
   */
  public $name;
  protected $preloadedConfigType = SasPortalDeviceConfig::class;
  protected $preloadedConfigDataType = '';
  /**
   * A serial number assigned to the device by the device manufacturer.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Output only. Device state.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Current configuration of the device as registered to the SAS.
   *
   * @param SasPortalDeviceConfig $activeConfig
   */
  public function setActiveConfig(SasPortalDeviceConfig $activeConfig)
  {
    $this->activeConfig = $activeConfig;
  }
  /**
   * @return SasPortalDeviceConfig
   */
  public function getActiveConfig()
  {
    return $this->activeConfig;
  }
  /**
   * Output only. Current channels with scores.
   *
   * @deprecated
   * @param SasPortalChannelWithScore[] $currentChannels
   */
  public function setCurrentChannels($currentChannels)
  {
    $this->currentChannels = $currentChannels;
  }
  /**
   * @deprecated
   * @return SasPortalChannelWithScore[]
   */
  public function getCurrentChannels()
  {
    return $this->currentChannels;
  }
  /**
   * Device parameters that can be overridden by both SAS Portal and SAS
   * registration requests.
   *
   * @param SasPortalDeviceMetadata $deviceMetadata
   */
  public function setDeviceMetadata(SasPortalDeviceMetadata $deviceMetadata)
  {
    $this->deviceMetadata = $deviceMetadata;
  }
  /**
   * @return SasPortalDeviceMetadata
   */
  public function getDeviceMetadata()
  {
    return $this->deviceMetadata;
  }
  /**
   * Device display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The FCC identifier of the device. Refer to https://www.fcc.gov/oet/ea/fccid
   * for FccID format. Accept underscores and periods because some test-SAS
   * customers use them.
   *
   * @param string $fccId
   */
  public function setFccId($fccId)
  {
    $this->fccId = $fccId;
  }
  /**
   * @return string
   */
  public function getFccId()
  {
    return $this->fccId;
  }
  /**
   * Only ranges that are within the allowlists are available for new grants.
   *
   * @param SasPortalFrequencyRange[] $grantRangeAllowlists
   */
  public function setGrantRangeAllowlists($grantRangeAllowlists)
  {
    $this->grantRangeAllowlists = $grantRangeAllowlists;
  }
  /**
   * @return SasPortalFrequencyRange[]
   */
  public function getGrantRangeAllowlists()
  {
    return $this->grantRangeAllowlists;
  }
  /**
   * Output only. Grants held by the device.
   *
   * @param SasPortalDeviceGrant[] $grants
   */
  public function setGrants($grants)
  {
    $this->grants = $grants;
  }
  /**
   * @return SasPortalDeviceGrant[]
   */
  public function getGrants()
  {
    return $this->grants;
  }
  /**
   * Output only. The resource path name.
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
   * Configuration of the device, as specified via SAS Portal API.
   *
   * @param SasPortalDeviceConfig $preloadedConfig
   */
  public function setPreloadedConfig(SasPortalDeviceConfig $preloadedConfig)
  {
    $this->preloadedConfig = $preloadedConfig;
  }
  /**
   * @return SasPortalDeviceConfig
   */
  public function getPreloadedConfig()
  {
    return $this->preloadedConfig;
  }
  /**
   * A serial number assigned to the device by the device manufacturer.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Output only. Device state.
   *
   * Accepted values: DEVICE_STATE_UNSPECIFIED, RESERVED, REGISTERED,
   * DEREGISTERED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalDevice::class, 'Google_Service_Sasportal_SasPortalDevice');
