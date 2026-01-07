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

namespace Google\Service\TagManager\Resource;

use Google\Service\TagManager\GtagConfig;
use Google\Service\TagManager\ListGtagConfigResponse;

/**
 * The "gtag_config" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tagmanagerService = new Google\Service\TagManager(...);
 *   $gtag_config = $tagmanagerService->accounts_containers_workspaces_gtag_config;
 *  </code>
 */
class AccountsContainersWorkspacesGtagConfig extends \Google\Service\Resource
{
  /**
   * Creates a Google tag config. (gtag_config.create)
   *
   * @param string $parent Workspace's API relative path.
   * @param GtagConfig $postBody
   * @param array $optParams Optional parameters.
   * @return GtagConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, GtagConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GtagConfig::class);
  }
  /**
   * Deletes a Google tag config. (gtag_config.delete)
   *
   * @param string $path Google tag config's API relative path.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets a Google tag config. (gtag_config.get)
   *
   * @param string $path Google tag config's API relative path.
   * @param array $optParams Optional parameters.
   * @return GtagConfig
   * @throws \Google\Service\Exception
   */
  public function get($path, $optParams = [])
  {
    $params = ['path' => $path];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GtagConfig::class);
  }
  /**
   * Lists all Google tag configs in a Container.
   * (gtag_config.listAccountsContainersWorkspacesGtagConfig)
   *
   * @param string $parent Workspace's API relative path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken Continuation token for fetching the next page of
   * results.
   * @return ListGtagConfigResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsContainersWorkspacesGtagConfig($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGtagConfigResponse::class);
  }
  /**
   * Updates a Google tag config. (gtag_config.update)
   *
   * @param string $path Google tag config's API relative path.
   * @param GtagConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fingerprint When provided, this fingerprint must match the
   * fingerprint of the config in storage.
   * @return GtagConfig
   * @throws \Google\Service\Exception
   */
  public function update($path, GtagConfig $postBody, $optParams = [])
  {
    $params = ['path' => $path, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GtagConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsContainersWorkspacesGtagConfig::class, 'Google_Service_TagManager_Resource_AccountsContainersWorkspacesGtagConfig');
