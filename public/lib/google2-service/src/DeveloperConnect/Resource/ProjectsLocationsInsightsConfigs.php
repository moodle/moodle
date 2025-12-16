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

namespace Google\Service\DeveloperConnect\Resource;

use Google\Service\DeveloperConnect\InsightsConfig;
use Google\Service\DeveloperConnect\ListInsightsConfigsResponse;
use Google\Service\DeveloperConnect\Operation;

/**
 * The "insightsConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $developerconnectService = new Google\Service\DeveloperConnect(...);
 *   $insightsConfigs = $developerconnectService->projects_locations_insightsConfigs;
 *  </code>
 */
class ProjectsLocationsInsightsConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new InsightsConfig in a given project and location.
   * (insightsConfigs.create)
   *
   * @param string $parent Required. Value for parent.
   * @param InsightsConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string insightsConfigId Required. ID of the requesting
   * InsightsConfig.
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, InsightsConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Insight. (insightsConfigs.delete)
   *
   * @param string $name Required. Value for parent.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. This checksum is computed by the server
   * based on the value of other fields, and may be sent on update and delete
   * requests to ensure the client has an up-to-date value before proceeding.
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
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
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
   * Gets details of a single Insight. (insightsConfigs.get)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   * @return InsightsConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InsightsConfig::class);
  }
  /**
   * Lists InsightsConfigs in a given project and location.
   * (insightsConfigs.listProjectsLocationsInsightsConfigs)
   *
   * @param string $parent Required. Parent value for ListInsightsConfigsRequest.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results. See
   * https://google.aip.dev/160 for more details. Filter string, adhering to the
   * rules in https://google.aip.dev/160. List only InsightsConfigs matching the
   * filter. If filter is empty, all InsightsConfigs are listed.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListInsightsConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInsightsConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInsightsConfigsResponse::class);
  }
  /**
   * Updates the parameters of a single InsightsConfig. (insightsConfigs.patch)
   *
   * @param string $name Identifier. The name of the InsightsConfig. Format:
   * projects/{project}/locations/{location}/insightsConfigs/{insightsConfig}
   * @param InsightsConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the insightsConfig
   * is not found a new insightsConfig will be created. In this situation
   * `update_mask` is ignored. The creation will succeed only if the input
   * insightsConfig has all the necessary information (e.g a github_config with
   * both user_oauth_token and installation_id properties).
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
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, InsightsConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInsightsConfigs::class, 'Google_Service_DeveloperConnect_Resource_ProjectsLocationsInsightsConfigs');
