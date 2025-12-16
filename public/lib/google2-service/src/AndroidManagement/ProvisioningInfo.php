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

namespace Google\Service\AndroidManagement;

class ProvisioningInfo extends \Google\Model
{
  /**
   * This value is disallowed.
   */
  public const MANAGEMENT_MODE_MANAGEMENT_MODE_UNSPECIFIED = 'MANAGEMENT_MODE_UNSPECIFIED';
  /**
   * Device owner. Android Device Policy has full control over the device.
   */
  public const MANAGEMENT_MODE_DEVICE_OWNER = 'DEVICE_OWNER';
  /**
   * Profile owner. Android Device Policy has control over a managed profile on
   * the device.
   */
  public const MANAGEMENT_MODE_PROFILE_OWNER = 'PROFILE_OWNER';
  /**
   * Ownership is unspecified.
   */
  public const OWNERSHIP_OWNERSHIP_UNSPECIFIED = 'OWNERSHIP_UNSPECIFIED';
  /**
   * Device is company-owned.
   */
  public const OWNERSHIP_COMPANY_OWNED = 'COMPANY_OWNED';
  /**
   * Device is personally-owned.
   */
  public const OWNERSHIP_PERSONALLY_OWNED = 'PERSONALLY_OWNED';
  /**
   * The API level of the Android platform version running on the device.
   *
   * @var int
   */
  public $apiLevel;
  /**
   * The email address of the authenticated user (only present for Google
   * Account provisioning method).
   *
   * @var string
   */
  public $authenticatedUserEmail;
  /**
   * The brand of the device. For example, Google.
   *
   * @var string
   */
  public $brand;
  /**
   * The name of the enterprise in the form enterprises/{enterprise}.
   *
   * @var string
   */
  public $enterprise;
  /**
   * For corporate-owned devices, IMEI number of the GSM device. For example,
   * A1000031212.
   *
   * @var string
   */
  public $imei;
  /**
   * The management mode of the device or profile.
   *
   * @var string
   */
  public $managementMode;
  /**
   * For corporate-owned devices, MEID number of the CDMA device. For example,
   * A00000292788E1.
   *
   * @var string
   */
  public $meid;
  /**
   * The model of the device. For example, Asus Nexus 7.
   *
   * @var string
   */
  public $model;
  /**
   * The name of this resource in the form provisioningInfo/{provisioning_info}.
   *
   * @var string
   */
  public $name;
  /**
   * Ownership of the managed device.
   *
   * @var string
   */
  public $ownership;
  /**
   * For corporate-owned devices, The device serial number.
   *
   * @var string
   */
  public $serialNumber;

  /**
   * The API level of the Android platform version running on the device.
   *
   * @param int $apiLevel
   */
  public function setApiLevel($apiLevel)
  {
    $this->apiLevel = $apiLevel;
  }
  /**
   * @return int
   */
  public function getApiLevel()
  {
    return $this->apiLevel;
  }
  /**
   * The email address of the authenticated user (only present for Google
   * Account provisioning method).
   *
   * @param string $authenticatedUserEmail
   */
  public function setAuthenticatedUserEmail($authenticatedUserEmail)
  {
    $this->authenticatedUserEmail = $authenticatedUserEmail;
  }
  /**
   * @return string
   */
  public function getAuthenticatedUserEmail()
  {
    return $this->authenticatedUserEmail;
  }
  /**
   * The brand of the device. For example, Google.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * The name of the enterprise in the form enterprises/{enterprise}.
   *
   * @param string $enterprise
   */
  public function setEnterprise($enterprise)
  {
    $this->enterprise = $enterprise;
  }
  /**
   * @return string
   */
  public function getEnterprise()
  {
    return $this->enterprise;
  }
  /**
   * For corporate-owned devices, IMEI number of the GSM device. For example,
   * A1000031212.
   *
   * @param string $imei
   */
  public function setImei($imei)
  {
    $this->imei = $imei;
  }
  /**
   * @return string
   */
  public function getImei()
  {
    return $this->imei;
  }
  /**
   * The management mode of the device or profile.
   *
   * Accepted values: MANAGEMENT_MODE_UNSPECIFIED, DEVICE_OWNER, PROFILE_OWNER
   *
   * @param self::MANAGEMENT_MODE_* $managementMode
   */
  public function setManagementMode($managementMode)
  {
    $this->managementMode = $managementMode;
  }
  /**
   * @return self::MANAGEMENT_MODE_*
   */
  public function getManagementMode()
  {
    return $this->managementMode;
  }
  /**
   * For corporate-owned devices, MEID number of the CDMA device. For example,
   * A00000292788E1.
   *
   * @param string $meid
   */
  public function setMeid($meid)
  {
    $this->meid = $meid;
  }
  /**
   * @return string
   */
  public function getMeid()
  {
    return $this->meid;
  }
  /**
   * The model of the device. For example, Asus Nexus 7.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * The name of this resource in the form provisioningInfo/{provisioning_info}.
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
   * Ownership of the managed device.
   *
   * Accepted values: OWNERSHIP_UNSPECIFIED, COMPANY_OWNED, PERSONALLY_OWNED
   *
   * @param self::OWNERSHIP_* $ownership
   */
  public function setOwnership($ownership)
  {
    $this->ownership = $ownership;
  }
  /**
   * @return self::OWNERSHIP_*
   */
  public function getOwnership()
  {
    return $this->ownership;
  }
  /**
   * For corporate-owned devices, The device serial number.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningInfo::class, 'Google_Service_AndroidManagement_ProvisioningInfo');
