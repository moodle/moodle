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

use Google\Service\Apigee\GoogleCloudApigeeV1AppGroupApp;
use Google\Service\Apigee\GoogleCloudApigeeV1ListAppGroupAppsResponse;

/**
 * The "apps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $apps = $apigeeService->organizations_appgroups_apps;
 *  </code>
 */
class OrganizationsAppgroupsApps extends \Google\Service\Resource
{
  /**
   * Creates an app and associates it with an AppGroup. This API associates the
   * AppGroup app with the specified API product and auto-generates an API key for
   * the app to use in calls to API proxies inside that API product. The `name` is
   * the unique ID of the app that you can use in API calls. (apps.create)
   *
   * @param string $parent Required. Name of the AppGroup. Use the following
   * structure in your request: `organizations/{org}/appgroups/{app_group_name}`
   * @param GoogleCloudApigeeV1AppGroupApp $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupApp
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1AppGroupApp $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1AppGroupApp::class);
  }
  /**
   * Deletes an AppGroup app. **Note**: The delete operation is asynchronous. The
   * AppGroup app is deleted immediately, but its associated resources, such as
   * app keys or access tokens, may take anywhere from a few seconds to a few
   * minutes to be deleted. (apps.delete)
   *
   * @param string $name Required. Name of the AppGroup app. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupApp
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1AppGroupApp::class);
  }
  /**
   * Returns the details for an AppGroup app. (apps.get)
   *
   * @param string $name Required. Name of the AppGroup app. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1AppGroupApp
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1AppGroupApp::class);
  }
  /**
   * Lists all apps created by an AppGroup in an Apigee organization. Optionally,
   * you can request an expanded view of the AppGroup apps. Lists all AppGroupApps
   * in an AppGroup. A maximum of 1000 AppGroup apps are returned in the response
   * if PageSize is not specified, or if the PageSize is greater than 1000.
   * (apps.listOrganizationsAppgroupsApps)
   *
   * @param string $parent Required. Name of the AppGroup. Use the following
   * structure in your request: `organizations/{org}/appgroups/{app_group_name}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number entries to return. If
   * unspecified, at most 1000 entries will be returned.
   * @opt_param string pageToken Optional. Page token. If provides, must be a
   * valid AppGroup app returned from a previous call that can be used to retrieve
   * the next page.
   * @return GoogleCloudApigeeV1ListAppGroupAppsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsAppgroupsApps($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListAppGroupAppsResponse::class);
  }
  /**
   * Updates the details for an AppGroup app. In addition, you can add an API
   * product to an AppGroup app and automatically generate an API key for the app
   * to use when calling APIs in the API product. If you want to use an existing
   * API key for the API product, add the API product to the API key using the
   * UpdateAppGroupAppKey API. Using this API, you cannot update the app name, as
   * it is the primary key used to identify the app and cannot be changed. This
   * API replaces the existing attributes with those specified in the request.
   * Include or exclude any existing attributes that you want to retain or delete,
   * respectively. (apps.update)
   *
   * @param string $name Required. Name of the AppGroup app. Use the following
   * structure in your request:
   * `organizations/{org}/appgroups/{app_group_name}/apps/{app}`
   * @param GoogleCloudApigeeV1AppGroupApp $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string action Approve or revoke the consumer key by setting this
   * value to `approve` or `revoke`. The `Content-Type` header must be set to
   * `application/octet-stream`, with empty body.
   * @return GoogleCloudApigeeV1AppGroupApp
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleCloudApigeeV1AppGroupApp $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleCloudApigeeV1AppGroupApp::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsAppgroupsApps::class, 'Google_Service_Apigee_Resource_OrganizationsAppgroupsApps');
