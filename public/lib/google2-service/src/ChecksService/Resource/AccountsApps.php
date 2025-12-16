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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\GoogleChecksAccountV1alphaApp;
use Google\Service\ChecksService\GoogleChecksAccountV1alphaListAppsResponse;

/**
 * The "apps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $apps = $checksService->accounts_apps;
 *  </code>
 */
class AccountsApps extends \Google\Service\Resource
{
  /**
   * Gets an app. (apps.get)
   *
   * @param string $name Required. Resource name of the app. Example:
   * `accounts/123/apps/456`
   * @param array $optParams Optional parameters.
   * @return GoogleChecksAccountV1alphaApp
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChecksAccountV1alphaApp::class);
  }
  /**
   * Lists the apps under the given account. (apps.listAccountsApps)
   *
   * @param string $parent Required. The parent account. Example: `accounts/123`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return.
   * The server may further constrain the maximum number of results returned in a
   * single page. If unspecified, the server will decide the number of results to
   * be returned.
   * @opt_param string pageToken Optional. A page token received from a previous
   * `ListApps` call. Provide this to retrieve the subsequent page.
   * @return GoogleChecksAccountV1alphaListAppsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsApps($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChecksAccountV1alphaListAppsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsApps::class, 'Google_Service_ChecksService_Resource_AccountsApps');
