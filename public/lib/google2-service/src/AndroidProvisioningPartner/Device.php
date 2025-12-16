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

class Device extends \Google\Collection
{
  protected $collection_key = 'claims';
  protected $claimsType = DeviceClaim::class;
  protected $claimsDataType = 'array';
  /**
   * Not available to resellers.
   *
   * @var string
   */
  public $configuration;
  /**
   * Output only. The ID of the device. Assigned by the server.
   *
   * @var string
   */
  public $deviceId;
  protected $deviceIdentifierType = DeviceIdentifier::class;
  protected $deviceIdentifierDataType = '';
  protected $deviceMetadataType = DeviceMetadata::class;
  protected $deviceMetadataDataType = '';
  /**
   * Output only. The API resource name in the format
   * `partners/[PARTNER_ID]/devices/[DEVICE_ID]`. Assigned by the server.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The provisioning claims for a device. Devices claimed for
   * zero-touch enrollment have a claim with the type `SECTION_TYPE_ZERO_TOUCH`.
   * Call `partners.devices.unclaim` or `partners.devices.unclaimAsync` to
   * remove the device from zero-touch enrollment.
   *
   * @param DeviceClaim[] $claims
   */
  public function setClaims($claims)
  {
    $this->claims = $claims;
  }
  /**
   * @return DeviceClaim[]
   */
  public function getClaims()
  {
    return $this->claims;
  }
  /**
   * Not available to resellers.
   *
   * @param string $configuration
   */
  public function setConfiguration($configuration)
  {
    $this->configuration = $configuration;
  }
  /**
   * @return string
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
  /**
   * Output only. The ID of the device. Assigned by the server.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * The hardware IDs that identify a manufactured device. To learn more, read
   * [Identifiers](https://developers.google.com/zero-touch/guides/identifiers).
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
   * The metadata attached to the device. Structured as key-value pairs. To
   * learn more, read [Device metadata](https://developers.google.com/zero-
   * touch/guides/metadata).
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
   * Output only. The API resource name in the format
   * `partners/[PARTNER_ID]/devices/[DEVICE_ID]`. Assigned by the server.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Device::class, 'Google_Service_AndroidProvisioningPartner_Device');
