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

namespace Google\Service\DriveLabels\Resource;

use Google\Service\DriveLabels\GoogleAppsDriveLabelsV2UserCapabilities;

/**
 * The "users" collection of methods.
 * Typical usage is:
 *  <code>
 *   $drivelabelsService = new Google\Service\DriveLabels(...);
 *   $users = $drivelabelsService->users;
 *  </code>
 */
class Users extends \Google\Service\Resource
{
  /**
   * Gets the user capabilities. (users.getCapabilities)
   *
   * @param string $name Required. The resource name of the user. Only
   * "users/me/capabilities" is supported.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customer The customer to scope this request to. For
   * example: `customers/abcd1234`. If unset, it will return settings within the
   * current customer.
   * @return GoogleAppsDriveLabelsV2UserCapabilities
   * @throws \Google\Service\Exception
   */
  public function getCapabilities($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getCapabilities', [$params], GoogleAppsDriveLabelsV2UserCapabilities::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Users::class, 'Google_Service_DriveLabels_Resource_Users');
