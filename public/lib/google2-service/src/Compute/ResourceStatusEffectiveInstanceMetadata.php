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

class ResourceStatusEffectiveInstanceMetadata extends \Google\Model
{
  /**
   * Effective block-project-ssh-keys value at Instance level.
   *
   * @var bool
   */
  public $blockProjectSshKeysMetadataValue;
  /**
   * Effective enable-guest-attributes value at Instance level.
   *
   * @var bool
   */
  public $enableGuestAttributesMetadataValue;
  /**
   * Effective enable-os-inventory value at Instance level.
   *
   * @var bool
   */
  public $enableOsInventoryMetadataValue;
  /**
   * Effective enable-osconfig value at Instance level.
   *
   * @var bool
   */
  public $enableOsconfigMetadataValue;
  /**
   * Effective enable-oslogin value at Instance level.
   *
   * @var bool
   */
  public $enableOsloginMetadataValue;
  /**
   * Effective serial-port-enable value at Instance level.
   *
   * @var bool
   */
  public $serialPortEnableMetadataValue;
  /**
   * Effective serial-port-logging-enable value at Instance level.
   *
   * @var bool
   */
  public $serialPortLoggingEnableMetadataValue;
  /**
   * Effective VM DNS setting at Instance level.
   *
   * @var string
   */
  public $vmDnsSettingMetadataValue;

  /**
   * Effective block-project-ssh-keys value at Instance level.
   *
   * @param bool $blockProjectSshKeysMetadataValue
   */
  public function setBlockProjectSshKeysMetadataValue($blockProjectSshKeysMetadataValue)
  {
    $this->blockProjectSshKeysMetadataValue = $blockProjectSshKeysMetadataValue;
  }
  /**
   * @return bool
   */
  public function getBlockProjectSshKeysMetadataValue()
  {
    return $this->blockProjectSshKeysMetadataValue;
  }
  /**
   * Effective enable-guest-attributes value at Instance level.
   *
   * @param bool $enableGuestAttributesMetadataValue
   */
  public function setEnableGuestAttributesMetadataValue($enableGuestAttributesMetadataValue)
  {
    $this->enableGuestAttributesMetadataValue = $enableGuestAttributesMetadataValue;
  }
  /**
   * @return bool
   */
  public function getEnableGuestAttributesMetadataValue()
  {
    return $this->enableGuestAttributesMetadataValue;
  }
  /**
   * Effective enable-os-inventory value at Instance level.
   *
   * @param bool $enableOsInventoryMetadataValue
   */
  public function setEnableOsInventoryMetadataValue($enableOsInventoryMetadataValue)
  {
    $this->enableOsInventoryMetadataValue = $enableOsInventoryMetadataValue;
  }
  /**
   * @return bool
   */
  public function getEnableOsInventoryMetadataValue()
  {
    return $this->enableOsInventoryMetadataValue;
  }
  /**
   * Effective enable-osconfig value at Instance level.
   *
   * @param bool $enableOsconfigMetadataValue
   */
  public function setEnableOsconfigMetadataValue($enableOsconfigMetadataValue)
  {
    $this->enableOsconfigMetadataValue = $enableOsconfigMetadataValue;
  }
  /**
   * @return bool
   */
  public function getEnableOsconfigMetadataValue()
  {
    return $this->enableOsconfigMetadataValue;
  }
  /**
   * Effective enable-oslogin value at Instance level.
   *
   * @param bool $enableOsloginMetadataValue
   */
  public function setEnableOsloginMetadataValue($enableOsloginMetadataValue)
  {
    $this->enableOsloginMetadataValue = $enableOsloginMetadataValue;
  }
  /**
   * @return bool
   */
  public function getEnableOsloginMetadataValue()
  {
    return $this->enableOsloginMetadataValue;
  }
  /**
   * Effective serial-port-enable value at Instance level.
   *
   * @param bool $serialPortEnableMetadataValue
   */
  public function setSerialPortEnableMetadataValue($serialPortEnableMetadataValue)
  {
    $this->serialPortEnableMetadataValue = $serialPortEnableMetadataValue;
  }
  /**
   * @return bool
   */
  public function getSerialPortEnableMetadataValue()
  {
    return $this->serialPortEnableMetadataValue;
  }
  /**
   * Effective serial-port-logging-enable value at Instance level.
   *
   * @param bool $serialPortLoggingEnableMetadataValue
   */
  public function setSerialPortLoggingEnableMetadataValue($serialPortLoggingEnableMetadataValue)
  {
    $this->serialPortLoggingEnableMetadataValue = $serialPortLoggingEnableMetadataValue;
  }
  /**
   * @return bool
   */
  public function getSerialPortLoggingEnableMetadataValue()
  {
    return $this->serialPortLoggingEnableMetadataValue;
  }
  /**
   * Effective VM DNS setting at Instance level.
   *
   * @param string $vmDnsSettingMetadataValue
   */
  public function setVmDnsSettingMetadataValue($vmDnsSettingMetadataValue)
  {
    $this->vmDnsSettingMetadataValue = $vmDnsSettingMetadataValue;
  }
  /**
   * @return string
   */
  public function getVmDnsSettingMetadataValue()
  {
    return $this->vmDnsSettingMetadataValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceStatusEffectiveInstanceMetadata::class, 'Google_Service_Compute_ResourceStatusEffectiveInstanceMetadata');
