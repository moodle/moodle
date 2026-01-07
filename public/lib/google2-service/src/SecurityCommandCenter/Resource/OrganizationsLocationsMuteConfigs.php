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

namespace Google\Service\SecurityCommandCenter\Resource;

use Google\Service\SecurityCommandCenter\GoogleCloudSecuritycenterV1MuteConfig;
use Google\Service\SecurityCommandCenter\SecuritycenterEmpty;

/**
 * The "muteConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $muteConfigs = $securitycenterService->organizations_locations_muteConfigs;
 *  </code>
 */
class OrganizationsLocationsMuteConfigs extends \Google\Service\Resource
{
  /**
   * Deletes an existing mute config. (muteConfigs.delete)
   *
   * @param string $name Required. Name of the mute config to delete. Its format
   * is `organizations/{organization}/muteConfigs/{config_id}`,
   * `folders/{folder}/muteConfigs/{config_id}`,
   * `projects/{project}/muteConfigs/{config_id}`,
   * `organizations/{organization}/locations/global/muteConfigs/{config_id}`,
   * `folders/{folder}/locations/global/muteConfigs/{config_id}`, or
   * `projects/{project}/locations/global/muteConfigs/{config_id}`.
   * @param array $optParams Optional parameters.
   * @return SecuritycenterEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SecuritycenterEmpty::class);
  }
  /**
   * Gets a mute config. (muteConfigs.get)
   *
   * @param string $name Required. Name of the mute config to retrieve. Its format
   * is `organizations/{organization}/muteConfigs/{config_id}`,
   * `folders/{folder}/muteConfigs/{config_id}`,
   * `projects/{project}/muteConfigs/{config_id}`,
   * `organizations/{organization}/locations/global/muteConfigs/{config_id}`,
   * `folders/{folder}/locations/global/muteConfigs/{config_id}`, or
   * `projects/{project}/locations/global/muteConfigs/{config_id}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudSecuritycenterV1MuteConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudSecuritycenterV1MuteConfig::class);
  }
  /**
   * Updates a mute config. (muteConfigs.patch)
   *
   * @param string $name This field will be ignored if provided on config
   * creation. Format `organizations/{organization}/muteConfigs/{mute_config}`
   * `folders/{folder}/muteConfigs/{mute_config}`
   * `projects/{project}/muteConfigs/{mute_config}`
   * `organizations/{organization}/locations/global/muteConfigs/{mute_config}`
   * `folders/{folder}/locations/global/muteConfigs/{mute_config}`
   * `projects/{project}/locations/global/muteConfigs/{mute_config}`
   * @param GoogleCloudSecuritycenterV1MuteConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated. If empty all
   * mutable fields will be updated.
   * @return GoogleCloudSecuritycenterV1MuteConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudSecuritycenterV1MuteConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudSecuritycenterV1MuteConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsMuteConfigs::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsLocationsMuteConfigs');
