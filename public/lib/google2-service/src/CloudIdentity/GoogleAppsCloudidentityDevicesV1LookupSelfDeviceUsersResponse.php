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

class GoogleAppsCloudidentityDevicesV1LookupSelfDeviceUsersResponse extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * The customer resource name that may be passed back to other Devices API
   * methods such as List, Get, etc.
   *
   * @var string
   */
  public $customer;
  /**
   * [Resource names](https://cloud.google.com/apis/design/resource_names) of
   * the DeviceUsers in the format:
   * `devices/{device}/deviceUsers/{user_resource}`, where device is the unique
   * ID assigned to a Device and user_resource is the unique user ID
   *
   * @var string[]
   */
  public $names;
  /**
   * Token to retrieve the next page of results. Empty if there are no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The customer resource name that may be passed back to other Devices API
   * methods such as List, Get, etc.
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
   * [Resource names](https://cloud.google.com/apis/design/resource_names) of
   * the DeviceUsers in the format:
   * `devices/{device}/deviceUsers/{user_resource}`, where device is the unique
   * ID assigned to a Device and user_resource is the unique user ID
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
  /**
   * Token to retrieve the next page of results. Empty if there are no more
   * results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1LookupSelfDeviceUsersResponse::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1LookupSelfDeviceUsersResponse');
