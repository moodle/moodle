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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\AuthzExtension;
use Google\Service\NetworkServices\ListAuthzExtensionsResponse;
use Google\Service\NetworkServices\Operation;

/**
 * The "authzExtensions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $authzExtensions = $networkservicesService->projects_locations_authzExtensions;
 *  </code>
 */
class ProjectsLocationsAuthzExtensions extends \Google\Service\Resource
{
  /**
   * Creates a new `AuthzExtension` resource in a given project and location.
   * (authzExtensions.create)
   *
   * @param string $parent Required. The parent resource of the `AuthzExtension`
   * resource. Must be in the format `projects/{project}/locations/{location}`.
   * @param AuthzExtension $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string authzExtensionId Required. User-provided ID of the
   * `AuthzExtension` resource to be created.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, AuthzExtension $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the specified `AuthzExtension` resource. (authzExtensions.delete)
   *
   * @param string $name Required. The name of the `AuthzExtension` resource to
   * delete. Must be in the format
   * `projects/{project}/locations/{location}/authzExtensions/{authz_extension}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes after the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of the specified `AuthzExtension` resource.
   * (authzExtensions.get)
   *
   * @param string $name Required. A name of the `AuthzExtension` resource to get.
   * Must be in the format
   * `projects/{project}/locations/{location}/authzExtensions/{authz_extension}`.
   * @param array $optParams Optional parameters.
   * @return AuthzExtension
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AuthzExtension::class);
  }
  /**
   * Lists `AuthzExtension` resources in a given project and location.
   * (authzExtensions.listProjectsLocationsAuthzExtensions)
   *
   * @param string $parent Required. The project and location from which the
   * `AuthzExtension` resources are listed. These values are specified in the
   * following format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint about how to order the results.
   * @opt_param int pageSize Optional. Requested page size. The server might
   * return fewer items than requested. If unspecified, the server picks an
   * appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * that the server returns.
   * @return ListAuthzExtensionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAuthzExtensions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAuthzExtensionsResponse::class);
  }
  /**
   * Updates the parameters of the specified `AuthzExtension` resource.
   * (authzExtensions.patch)
   *
   * @param string $name Required. Identifier. Name of the `AuthzExtension`
   * resource in the following format:
   * `projects/{project}/locations/{location}/authzExtensions/{authz_extension}`.
   * @param AuthzExtension $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Used to specify the fields to be
   * overwritten in the `AuthzExtension` resource by the update. The fields
   * specified in the `update_mask` are relative to the resource, not the full
   * request. A field is overwritten if it is in the mask. If the user does not
   * specify a mask, then all fields are overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, AuthzExtension $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAuthzExtensions::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsAuthzExtensions');
