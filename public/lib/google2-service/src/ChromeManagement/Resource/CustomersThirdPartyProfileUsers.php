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

use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse;

/**
 * The "thirdPartyProfileUsers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $thirdPartyProfileUsers = $chromemanagementService->customers_thirdPartyProfileUsers;
 *  </code>
 */
class CustomersThirdPartyProfileUsers extends \Google\Service\Resource
{
  /**
   * Moves a third party chrome profile user to a destination OU. All profiles
   * associated to that user will be moved to the destination OU.
   * (thirdPartyProfileUsers.move)
   *
   * @param string $name Required. Format:
   * customers/{customer_id}/thirdPartyProfileUsers/{third_party_profile_user_id}
   * @param GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse
   * @throws \Google\Service\Exception
   */
  public function move($name, GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('move', [$params], GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersThirdPartyProfileUsers::class, 'Google_Service_ChromeManagement_Resource_CustomersThirdPartyProfileUsers');
