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

namespace Google\Service\ParameterManager\Resource;

use Google\Service\ParameterManager\ListParameterVersionsResponse;
use Google\Service\ParameterManager\ParameterVersion;
use Google\Service\ParameterManager\ParametermanagerEmpty;
use Google\Service\ParameterManager\RenderParameterVersionResponse;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $parametermanagerService = new Google\Service\ParameterManager(...);
 *   $versions = $parametermanagerService->projects_locations_parameters_versions;
 *  </code>
 */
class ProjectsLocationsParametersVersions extends \Google\Service\Resource
{
  /**
   * Creates a new ParameterVersion in a given project, location, and parameter.
   * (versions.create)
   *
   * @param string $parent Required. Value for parent in the format
   * `projects/locations/parameters`.
   * @param ParameterVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parameterVersionId Required. Id of the ParameterVersion
   * resource
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return ParameterVersion
   * @throws \Google\Service\Exception
   */
  public function create($parent, ParameterVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], ParameterVersion::class);
  }
  /**
   * Deletes a single ParameterVersion. (versions.delete)
   *
   * @param string $name Required. Name of the resource in the format
   * `projects/locations/parameters/versions`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return ParametermanagerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ParametermanagerEmpty::class);
  }
  /**
   * Gets details of a single ParameterVersion. (versions.get)
   *
   * @param string $name Required. Name of the resource in the format
   * `projects/locations/parameters/versions`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. View of the ParameterVersion. In the default
   * FULL view, all metadata & payload associated with the ParameterVersion will
   * be returned.
   * @return ParameterVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ParameterVersion::class);
  }
  /**
   * Lists ParameterVersions in a given project, location, and parameter.
   * (versions.listProjectsLocationsParametersVersions)
   *
   * @param string $parent Required. Parent value for ListParameterVersionsRequest
   * in the format `projects/locations/parameters`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListParameterVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsParametersVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListParameterVersionsResponse::class);
  }
  /**
   * Updates a single ParameterVersion. (versions.patch)
   *
   * @param string $name Identifier. [Output only] The resource name of the
   * ParameterVersion in the format `projects/locations/parameters/versions`.
   * @param ParameterVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the ParameterVersion resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A mutable field will be overwritten if it is in the mask. If
   * the user does not provide a mask then all mutable fields present in the
   * request will be overwritten.
   * @return ParameterVersion
   * @throws \Google\Service\Exception
   */
  public function patch($name, ParameterVersion $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ParameterVersion::class);
  }
  /**
   * Gets rendered version of a ParameterVersion. (versions.render)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return RenderParameterVersionResponse
   * @throws \Google\Service\Exception
   */
  public function render($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('render', [$params], RenderParameterVersionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsParametersVersions::class, 'Google_Service_ParameterManager_Resource_ProjectsLocationsParametersVersions');
