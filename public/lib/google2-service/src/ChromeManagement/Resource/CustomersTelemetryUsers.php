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

namespace Google\Service\ChromeManagement\Resource;

use Google\Service\ChromeManagement\GoogleChromeManagementV1ListTelemetryUsersResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1TelemetryUser;

/**
 * The "users" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $users = $chromemanagementService->customers_telemetry_users;
 *  </code>
 */
class CustomersTelemetryUsers extends \Google\Service\Resource
{
  /**
   * Get telemetry user. (users.get)
   *
   * @param string $name Required. Name of the `TelemetryUser` to return.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string readMask Read mask to specify which fields to return.
   * Supported read_mask paths are: - name - org_unit_id - user_id - user_email -
   * user_device.device_id - user_device.audio_status_report -
   * user_device.device_activity_report - user_device.network_bandwidth_report -
   * user_device.peripherals_report - user_device.app_report
   * @return GoogleChromeManagementV1TelemetryUser
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChromeManagementV1TelemetryUser::class);
  }
  /**
   * List all telemetry users. (users.listCustomersTelemetryUsers)
   *
   * @param string $parent Required. Customer id or "my_customer" to use the
   * customer associated to the account making the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Only include resources that match the filter.
   * Supported filter fields: - user_id - user_org_unit_id
   * @opt_param int pageSize Maximum number of results to return. Default value is
   * 100. Maximum value is 1000.
   * @opt_param string pageToken Token to specify next page in the list.
   * @opt_param string readMask Read mask to specify which fields to return.
   * Supported read_mask paths are: - name - org_unit_id - user_id - user_email -
   * user_device.device_id - user_device.audio_status_report -
   * user_device.device_activity_report - user_device.network_bandwidth_report -
   * user_device.peripherals_report - user_device.app_report
   * @return GoogleChromeManagementV1ListTelemetryUsersResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersTelemetryUsers($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChromeManagementV1ListTelemetryUsersResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersTelemetryUsers::class, 'Google_Service_ChromeManagement_Resource_CustomersTelemetryUsers');
