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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1AppGroupAppKey;
use Google\Service\Apigee\GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest;

/**
 * The "keys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $keys = $apigeeService->organizations_appgroups_apps_keys;
 *  </code>
 */
class OrganizationsAppgroupsAppsKeys extends \Google\Service\Resource
{
  /**
   * Creates a custom consumer key and secret for a AppGroup app. This is
   * particularly useful if you want to migrate existing consumer keys and secrets
   * to Apigee from another system. Consumer keys and secrets can contain letters,
   * numbers, underscores, and hyphens. No other special characters are allowed.
   * To avoid service disruptions, a consumer key and secret should not exceed 2
   * KBs each. **Note**: When creating the consumer key and secret, an association
   * to API products will not be made. Therefore, you should not specify the
   * associated API products in your request. Instead, use the
   * UpdateAppGroupAppKey API to make the association after the consumer key and
   * secret are created. If a consumer key and secret already exist, you can keep
   * them or delete them using the DeleteAppGroupAppKey API. (keys.create)
   *
   * @param string $parent Required. Parent of the AppGroup app key. Use the
   * following structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}/keys`
   * @param GoogleCloudApigeeV1AppGroupAppKey $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupAppKey
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1AppGroupAppKey $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1AppGroupAppKey::class);
  }
  /**
   * Deletes an app's consumer key and removes all API products associated with
   * the app. After the consumer key is deleted, it cannot be used to access any
   * APIs. (keys.delete)
   *
   * @param string $name Required. Name of the AppGroup app key. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}/keys/{key}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupAppKey
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1AppGroupAppKey::class);
  }
  /**
   * Gets details for a consumer key for a AppGroup app, including the key and
   * secret value, associated API products, and other information. (keys.get)
   *
   * @param string $name Required. Name of the AppGroup app key. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}/keys/{key}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupAppKey
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1AppGroupAppKey::class);
  }
  /**
   * Adds an API product to an AppGroupAppKey, enabling the app that holds the key
   * to access the API resources bundled in the API product. In addition, you can
   * add attributes and scopes to the AppGroupAppKey. This API replaces the
   * existing attributes with those specified in the request. Include or exclude
   * any existing attributes that you want to retain or delete, respectively. You
   * can use the same key to access all API products associated with the app.
   * (keys.updateAppGroupAppKey)
   *
   * @param string $name Required. Name of the AppGroup app key. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}/keys/{key}`
   * @param GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupAppKey
   * @throws \Google\Service\Exception
   */
  public function updateAppGroupAppKey($name, GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateAppGroupAppKey', [$params], GoogleCloudApigeeV1AppGroupAppKey::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsAppgroupsAppsKeys::class, 'Google_Service_Apigee_Resource_OrganizationsAppgroupsAppsKeys');
