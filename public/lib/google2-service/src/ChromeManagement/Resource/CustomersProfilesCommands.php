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

use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ListChromeBrowserProfileCommandsResponse;

/**
 * The "commands" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $commands = $chromemanagementService->customers_profiles_commands;
 *  </code>
 */
class CustomersProfilesCommands extends \Google\Service\Resource
{
  /**
   * Creates a Chrome browser profile remote command. (commands.create)
   *
   * @param string $parent Required. Format:
   * customers/{customer_id}/profiles/{profile_permanent_id}
   * @param GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand::class);
  }
  /**
   * Gets a Chrome browser profile remote command. (commands.get)
   *
   * @param string $name Required. Format:
   * customers/{customer_id}/profiles/{profile_permanent_id}/commands/{command_id}
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand::class);
  }
  /**
   * Lists remote commands of a Chrome browser profile.
   * (commands.listCustomersProfilesCommands)
   *
   * @param string $parent Required. Format:
   * customers/{customer_id}/profiles/{profile_permanent_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of commands to return.
   * The default page size is 100 if page_size is unspecified, and the maximum
   * page size allowed is 100.
   * @opt_param string pageToken Optional. The page token used to retrieve a
   * specific page of the listing request.
   * @return GoogleChromeManagementVersionsV1ListChromeBrowserProfileCommandsResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersProfilesCommands($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChromeManagementVersionsV1ListChromeBrowserProfileCommandsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersProfilesCommands::class, 'Google_Service_ChromeManagement_Resource_CustomersProfilesCommands');
