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

use Google\Service\ChromeManagement\GoogleChromeManagementV1ListTelemetryEventsResponse;

/**
 * The "events" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $events = $chromemanagementService->customers_telemetry_events;
 *  </code>
 */
class CustomersTelemetryEvents extends \Google\Service\Resource
{
  /**
   * List telemetry events. (events.listCustomersTelemetryEvents)
   *
   * @param string $parent Required. Customer id or "my_customer" to use the
   * customer associated to the account making the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Only include resources that match the
   * filter. Although this parameter is currently optional, this parameter will be
   * required- please specify at least 1 event type. Supported filter fields: -
   * device_id - user_id - device_org_unit_id - user_org_unit_id - timestamp -
   * event_type The "timestamp" filter accepts either the Unix Epoch milliseconds
   * format or the RFC3339 UTC "Zulu" format with nanosecond resolution and up to
   * nine fractional digits. Both formats should be surrounded by simple double
   * quotes. Examples: "2014-10-02T15:01:23Z", "2014-10-02T15:01:23.045123456Z",
   * "1679283943823".
   * @opt_param int pageSize Optional. Maximum number of results to return.
   * Default value is 100. Maximum value is 1000.
   * @opt_param string pageToken Optional. Token to specify next page in the list.
   * @opt_param string readMask Required. Read mask to specify which fields to
   * return. Although currently required, this field will become optional, while
   * the filter parameter with an event type will be come required. Supported
   * read_mask paths are: - device - user - audio_severe_underrun_event -
   * usb_peripherals_event - https_latency_change_event -
   * network_state_change_event - wifi_signal_strength_event -
   * vpn_connection_state_change_event - app_install_event - app_uninstall_event -
   * app_launch_event - os_crash_event - external_displays_event
   * @return GoogleChromeManagementV1ListTelemetryEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersTelemetryEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChromeManagementV1ListTelemetryEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersTelemetryEvents::class, 'Google_Service_ChromeManagement_Resource_CustomersTelemetryEvents');
