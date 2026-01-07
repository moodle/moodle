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

namespace Google\Service\AndroidManagement\Resource;

use Google\Service\AndroidManagement\AndroidmanagementEmpty;
use Google\Service\AndroidManagement\Command;
use Google\Service\AndroidManagement\Device;
use Google\Service\AndroidManagement\ListDevicesResponse;
use Google\Service\AndroidManagement\Operation;

/**
 * The "devices" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidmanagementService = new Google\Service\AndroidManagement(...);
 *   $devices = $androidmanagementService->enterprises_devices;
 *  </code>
 */
class EnterprisesDevices extends \Google\Service\Resource
{
  /**
   * Deletes a device. This operation attempts to wipe the device but this is not
   * guaranteed to succeed if the device is offline for an extended period.
   * Deleted devices do not show up in enterprises.devices.list calls and a 404 is
   * returned from enterprises.devices.get. (devices.delete)
   *
   * @param string $name The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string wipeDataFlags Optional flags that control the device wiping
   * behavior.
   * @opt_param string wipeReasonMessage Optional. A short message displayed to
   * the user before wiping the work profile on personal devices. This has no
   * effect on company owned devices. The maximum message length is 200
   * characters.
   * @return AndroidmanagementEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], AndroidmanagementEmpty::class);
  }
  /**
   * Gets a device. Deleted devices will respond with a 404 error. (devices.get)
   *
   * @param string $name The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
   * @param array $optParams Optional parameters.
   * @return Device
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Device::class);
  }
  /**
   * Issues a command to a device. The Operation resource returned contains a
   * Command in its metadata field. Use the get operation method to get the status
   * of the command. (devices.issueCommand)
   *
   * @param string $name The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
   * @param Command $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function issueCommand($name, Command $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('issueCommand', [$params], Operation::class);
  }
  /**
   * Lists devices for a given enterprise. Deleted devices are not returned in the
   * response. (devices.listEnterprisesDevices)
   *
   * @param string $parent The name of the enterprise in the form
   * enterprises/{enterpriseId}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The requested page size. If unspecified, at most 10
   * devices will be returned. The maximum value is 100; values above 100 will be
   * coerced to 100. The limits can change over time.
   * @opt_param string pageToken A token identifying a page of results returned by
   * the server.
   * @return ListDevicesResponse
   * @throws \Google\Service\Exception
   */
  public function listEnterprisesDevices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDevicesResponse::class);
  }
  /**
   * Updates a device. (devices.patch)
   *
   * @param string $name The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
   * @param Device $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The field mask indicating the fields to update.
   * If not set, all modifiable fields will be modified.
   * @return Device
   * @throws \Google\Service\Exception
   */
  public function patch($name, Device $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Device::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterprisesDevices::class, 'Google_Service_AndroidManagement_Resource_EnterprisesDevices');
