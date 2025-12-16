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

class Configuration extends \Google\Model
{
  /**
   * Required. The name of the organization. Zero-touch enrollment shows this
   * organization name to device users during device provisioning.
   *
   * @var string
   */
  public $companyName;
  /**
   * Output only. The ID of the configuration. Assigned by the server.
   *
   * @var string
   */
  public $configurationId;
  /**
   * Required. A short name that describes the configuration's purpose. For
   * example, _Sales team_ or _Temporary employees_. The zero-touch enrollment
   * portal displays this name to IT admins.
   *
   * @var string
   */
  public $configurationName;
  /**
   * Required. The email address that device users can contact to get help.
   * Zero-touch enrollment shows this email address to device users before
   * device provisioning. The value is validated on input.
   *
   * @var string
   */
  public $contactEmail;
  /**
   * Required. The telephone number that device users can call, using another
   * device, to get help. Zero-touch enrollment shows this number to device
   * users before device provisioning. Accepts numerals, spaces, the plus sign,
   * hyphens, and parentheses.
   *
   * @var string
   */
  public $contactPhone;
  /**
   * A message, containing one or two sentences, to help device users get help
   * or give them more details about what’s happening to their device. Zero-
   * touch enrollment shows this message before the device is provisioned.
   *
   * @var string
   */
  public $customMessage;
  /**
   * The JSON-formatted EMM provisioning extras that are passed to the DPC.
   *
   * @var string
   */
  public $dpcExtras;
  /**
   * Required. The resource name of the selected DPC (device policy controller)
   * in the format `customers/[CUSTOMER_ID]/dpcs`. To list the supported DPCs,
   * call `customers.dpcs.list`.
   *
   * @var string
   */
  public $dpcResourcePath;
  /**
   * Optional. The timeout before forcing factory reset the device if the device
   * doesn't go through provisioning in the setup wizard, usually due to lack of
   * network connectivity during setup wizard. Ranges from 0-6 hours, with 2
   * hours being the default if unset.
   *
   * @var string
   */
  public $forcedResetTime;
  /**
   * Required. Whether this is the default configuration that zero-touch
   * enrollment applies to any new devices the organization purchases in the
   * future. Only one customer configuration can be the default. Setting this
   * value to `true`, changes the previous default configuration's `isDefault`
   * value to `false`.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Output only. The API resource name in the format
   * `customers/[CUSTOMER_ID]/configurations/[CONFIGURATION_ID]`. Assigned by
   * the server.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The name of the organization. Zero-touch enrollment shows this
   * organization name to device users during device provisioning.
   *
   * @param string $companyName
   */
  public function setCompanyName($companyName)
  {
    $this->companyName = $companyName;
  }
  /**
   * @return string
   */
  public function getCompanyName()
  {
    return $this->companyName;
  }
  /**
   * Output only. The ID of the configuration. Assigned by the server.
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
   * Required. A short name that describes the configuration's purpose. For
   * example, _Sales team_ or _Temporary employees_. The zero-touch enrollment
   * portal displays this name to IT admins.
   *
   * @param string $configurationName
   */
  public function setConfigurationName($configurationName)
  {
    $this->configurationName = $configurationName;
  }
  /**
   * @return string
   */
  public function getConfigurationName()
  {
    return $this->configurationName;
  }
  /**
   * Required. The email address that device users can contact to get help.
   * Zero-touch enrollment shows this email address to device users before
   * device provisioning. The value is validated on input.
   *
   * @param string $contactEmail
   */
  public function setContactEmail($contactEmail)
  {
    $this->contactEmail = $contactEmail;
  }
  /**
   * @return string
   */
  public function getContactEmail()
  {
    return $this->contactEmail;
  }
  /**
   * Required. The telephone number that device users can call, using another
   * device, to get help. Zero-touch enrollment shows this number to device
   * users before device provisioning. Accepts numerals, spaces, the plus sign,
   * hyphens, and parentheses.
   *
   * @param string $contactPhone
   */
  public function setContactPhone($contactPhone)
  {
    $this->contactPhone = $contactPhone;
  }
  /**
   * @return string
   */
  public function getContactPhone()
  {
    return $this->contactPhone;
  }
  /**
   * A message, containing one or two sentences, to help device users get help
   * or give them more details about what’s happening to their device. Zero-
   * touch enrollment shows this message before the device is provisioned.
   *
   * @param string $customMessage
   */
  public function setCustomMessage($customMessage)
  {
    $this->customMessage = $customMessage;
  }
  /**
   * @return string
   */
  public function getCustomMessage()
  {
    return $this->customMessage;
  }
  /**
   * The JSON-formatted EMM provisioning extras that are passed to the DPC.
   *
   * @param string $dpcExtras
   */
  public function setDpcExtras($dpcExtras)
  {
    $this->dpcExtras = $dpcExtras;
  }
  /**
   * @return string
   */
  public function getDpcExtras()
  {
    return $this->dpcExtras;
  }
  /**
   * Required. The resource name of the selected DPC (device policy controller)
   * in the format `customers/[CUSTOMER_ID]/dpcs`. To list the supported DPCs,
   * call `customers.dpcs.list`.
   *
   * @param string $dpcResourcePath
   */
  public function setDpcResourcePath($dpcResourcePath)
  {
    $this->dpcResourcePath = $dpcResourcePath;
  }
  /**
   * @return string
   */
  public function getDpcResourcePath()
  {
    return $this->dpcResourcePath;
  }
  /**
   * Optional. The timeout before forcing factory reset the device if the device
   * doesn't go through provisioning in the setup wizard, usually due to lack of
   * network connectivity during setup wizard. Ranges from 0-6 hours, with 2
   * hours being the default if unset.
   *
   * @param string $forcedResetTime
   */
  public function setForcedResetTime($forcedResetTime)
  {
    $this->forcedResetTime = $forcedResetTime;
  }
  /**
   * @return string
   */
  public function getForcedResetTime()
  {
    return $this->forcedResetTime;
  }
  /**
   * Required. Whether this is the default configuration that zero-touch
   * enrollment applies to any new devices the organization purchases in the
   * future. Only one customer configuration can be the default. Setting this
   * value to `true`, changes the previous default configuration's `isDefault`
   * value to `false`.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Output only. The API resource name in the format
   * `customers/[CUSTOMER_ID]/configurations/[CONFIGURATION_ID]`. Assigned by
   * the server.
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
class_alias(Configuration::class, 'Google_Service_AndroidProvisioningPartner_Configuration');
