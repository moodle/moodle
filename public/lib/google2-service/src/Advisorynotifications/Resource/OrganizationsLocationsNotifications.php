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

namespace Google\Service\Advisorynotifications\Resource;

use Google\Service\Advisorynotifications\GoogleCloudAdvisorynotificationsV1ListNotificationsResponse;
use Google\Service\Advisorynotifications\GoogleCloudAdvisorynotificationsV1Notification;

/**
 * The "notifications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $advisorynotificationsService = new Google\Service\Advisorynotifications(...);
 *   $notifications = $advisorynotificationsService->organizations_locations_notifications;
 *  </code>
 */
class OrganizationsLocationsNotifications extends \Google\Service\Resource
{
  /**
   * Gets a notification. (notifications.get)
   *
   * @param string $name Required. A name of the notification to retrieve. Format:
   * organizations/{organization}/locations/{location}/notifications/{notification
   * } or projects/{projects}/locations/{location}/notifications/{notification}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode ISO code for requested localization language.
   * If unset, will be interpereted as "en". If the requested language is valid,
   * but not supported for this notification, English will be returned with an
   * "Not applicable" LocalizationState. If the ISO code is invalid (i.e. not a
   * real language), this RPC will throw an error.
   * @return GoogleCloudAdvisorynotificationsV1Notification
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAdvisorynotificationsV1Notification::class);
  }
  /**
   * Lists notifications under a given parent.
   * (notifications.listOrganizationsLocationsNotifications)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * notifications. Must be of the form
   * "organizations/{organization}/locations/{location}" or
   * "projects/{project}/locations/{location}".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode ISO code for requested localization language.
   * If unset, will be interpereted as "en". If the requested language is valid,
   * but not supported for this notification, English will be returned with an
   * "Not applicable" LocalizationState. If the ISO code is invalid (i.e. not a
   * real language), this RPC will throw an error.
   * @opt_param int pageSize The maximum number of notifications to return. The
   * service may return fewer than this value. If unspecified or equal to 0, at
   * most 50 notifications will be returned. The maximum value is 50; values above
   * 50 will be coerced to 50.
   * @opt_param string pageToken A page token returned from a previous request.
   * When paginating, all other parameters provided in the request must match the
   * call that returned the page token.
   * @opt_param string view Specifies which parts of the notification resource
   * should be returned in the response.
   * @return GoogleCloudAdvisorynotificationsV1ListNotificationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsNotifications($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAdvisorynotificationsV1ListNotificationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsNotifications::class, 'Google_Service_Advisorynotifications_Resource_OrganizationsLocationsNotifications');
