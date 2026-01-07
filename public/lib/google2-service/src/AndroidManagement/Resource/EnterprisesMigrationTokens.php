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

namespace Google\Service\AndroidManagement\Resource;

use Google\Service\AndroidManagement\ListMigrationTokensResponse;
use Google\Service\AndroidManagement\MigrationToken;

/**
 * The "migrationTokens" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidmanagementService = new Google\Service\AndroidManagement(...);
 *   $migrationTokens = $androidmanagementService->enterprises_migrationTokens;
 *  </code>
 */
class EnterprisesMigrationTokens extends \Google\Service\Resource
{
  /**
   * Creates a migration token, to migrate an existing device from being managed
   * by the EMM's Device Policy Controller (DPC) to being managed by the Android
   * Management API. See the guide
   * (https://developers.google.com/android/management/dpc-migration) for more
   * details. (migrationTokens.create)
   *
   * @param string $parent Required. The enterprise in which this migration token
   * is created. This must be the same enterprise which already manages the device
   * in the Play EMM API. Format: enterprises/{enterprise}
   * @param MigrationToken $postBody
   * @param array $optParams Optional parameters.
   * @return MigrationToken
   * @throws \Google\Service\Exception
   */
  public function create($parent, MigrationToken $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], MigrationToken::class);
  }
  /**
   * Gets a migration token. (migrationTokens.get)
   *
   * @param string $name Required. The name of the migration token to retrieve.
   * Format: enterprises/{enterprise}/migrationTokens/{migration_token}
   * @param array $optParams Optional parameters.
   * @return MigrationToken
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MigrationToken::class);
  }
  /**
   * Lists migration tokens. (migrationTokens.listEnterprisesMigrationTokens)
   *
   * @param string $parent Required. The enterprise which the migration tokens
   * belong to. Format: enterprises/{enterprise}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of migration tokens to return.
   * Fewer migration tokens may be returned. If unspecified, at most 100 migration
   * tokens will be returned. The maximum value is 100; values above 100 will be
   * coerced to 100.
   * @opt_param string pageToken A page token, received from a previous
   * ListMigrationTokens call. Provide this to retrieve the subsequent page.When
   * paginating, all other parameters provided to ListMigrationTokens must match
   * the call that provided the page token.
   * @return ListMigrationTokensResponse
   * @throws \Google\Service\Exception
   */
  public function listEnterprisesMigrationTokens($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMigrationTokensResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterprisesMigrationTokens::class, 'Google_Service_AndroidManagement_Resource_EnterprisesMigrationTokens');
