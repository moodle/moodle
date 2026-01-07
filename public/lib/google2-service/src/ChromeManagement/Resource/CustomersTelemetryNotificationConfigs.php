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

use Google\Service\ChromeManagement\GoogleChromeManagementV1ListTelemetryNotificationConfigsResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1TelemetryNotificationConfig;
use Google\Service\ChromeManagement\GoogleProtobufEmpty;

/**
 * The "notificationConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $notificationConfigs = $chromemanagementService->customers_telemetry_notificationConfigs;
 *  </code>
 */
class CustomersTelemetryNotificationConfigs extends \Google\Service\Resource
{
  /**
   * Create a telemetry notification config. (notificationConfigs.create)
   *
   * @param string $parent Required. The parent resource where this notification
   * config will be created. Format: `customers/{customer}`
   * @param GoogleChromeManagementV1TelemetryNotificationConfig $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementV1TelemetryNotificationConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleChromeManagementV1TelemetryNotificationConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleChromeManagementV1TelemetryNotificationConfig::class);
  }
  /**
   * Delete a telemetry notification config. (notificationConfigs.delete)
   *
   * @param string $name Required. The name of the notification config to delete.
   * Format:
   * `customers/{customer}/telemetry/notificationConfigs/{notification_config}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * List all telemetry notification configs.
   * (notificationConfigs.listCustomersTelemetryNotificationConfigs)
   *
   * @param string $parent Required. The parent which owns the notification
   * configs.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of notification configs to return.
   * The service may return fewer than this value. If unspecified, at most 100
   * notification configs will be returned. The maximum value is 100; values above
   * 100 will be coerced to 100.
   * @opt_param string pageToken A page token, received from a previous
   * `ListTelemetryNotificationConfigs` call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListTelemetryNotificationConfigs` must match the call that provided the page
   * token.
   * @return GoogleChromeManagementV1ListTelemetryNotificationConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersTelemetryNotificationConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChromeManagementV1ListTelemetryNotificationConfigsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersTelemetryNotificationConfigs::class, 'Google_Service_ChromeManagement_Resource_CustomersTelemetryNotificationConfigs');
