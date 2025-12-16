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

namespace Google\Service\Walletobjects\Resource;

use Google\Service\Walletobjects\Permissions as PermissionsModel;

/**
 * The "permissions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $walletobjectsService = new Google\Service\Walletobjects(...);
 *   $permissions = $walletobjectsService->permissions;
 *  </code>
 */
class Permissions extends \Google\Service\Resource
{
  /**
   * Returns the permissions for the given issuer id. (permissions.get)
   *
   * @param string $resourceId The unique identifier for an issuer. This ID must
   * be unique across all issuers.
   * @param array $optParams Optional parameters.
   * @return PermissionsModel
   * @throws \Google\Service\Exception
   */
  public function get($resourceId, $optParams = [])
  {
    $params = ['resourceId' => $resourceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PermissionsModel::class);
  }
  /**
   * Updates the permissions for the given issuer. (permissions.update)
   *
   * @param string $resourceId The unique identifier for an issuer. This ID must
   * be unique across all issuers.
   * @param PermissionsModel $postBody
   * @param array $optParams Optional parameters.
   * @return PermissionsModel
   * @throws \Google\Service\Exception
   */
  public function update($resourceId, PermissionsModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], PermissionsModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Permissions::class, 'Google_Service_Walletobjects_Resource_Permissions');
