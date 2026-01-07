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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1WipeDeviceRequest extends \Google\Model
{
  /**
   * Optional. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the customer.
   * If you're using this API for your own organization, use
   * `customers/my_customer` If you're using this API to manage another
   * organization, use `customers/{customer}`, where customer is the customer to
   * whom the device belongs.
   *
   * @var string
   */
  public $customer;
  /**
   * Optional. Specifies if a user is able to factory reset a device after a
   * Device Wipe. On iOS, this is called "Activation Lock", while on Android,
   * this is known as "Factory Reset Protection". If true, this protection will
   * be removed from the device, so that a user can successfully factory reset.
   * If false, the setting is untouched on the device.
   *
   * @var bool
   */
  public $removeResetLock;

  /**
   * Optional. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the customer.
   * If you're using this API for your own organization, use
   * `customers/my_customer` If you're using this API to manage another
   * organization, use `customers/{customer}`, where customer is the customer to
   * whom the device belongs.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Optional. Specifies if a user is able to factory reset a device after a
   * Device Wipe. On iOS, this is called "Activation Lock", while on Android,
   * this is known as "Factory Reset Protection". If true, this protection will
   * be removed from the device, so that a user can successfully factory reset.
   * If false, the setting is untouched on the device.
   *
   * @param bool $removeResetLock
   */
  public function setRemoveResetLock($removeResetLock)
  {
    $this->removeResetLock = $removeResetLock;
  }
  /**
   * @return bool
   */
  public function getRemoveResetLock()
  {
    return $this->removeResetLock;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1WipeDeviceRequest::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1WipeDeviceRequest');
