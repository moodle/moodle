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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\ApihubEmpty;
use Google\Service\APIhub\GoogleCloudApihubV1ExternalApi;
use Google\Service\APIhub\GoogleCloudApihubV1ListExternalApisResponse;

/**
 * The "externalApis" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $externalApis = $apihubService->projects_locations_externalApis;
 *  </code>
 */
class ProjectsLocationsExternalApis extends \Google\Service\Resource
{
  /**
   * Create an External API resource in the API hub. (externalApis.create)
   *
   * @param string $parent Required. The parent resource for the External API
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1ExternalApi $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string externalApiId Optional. The ID to use for the External API
   * resource, which will become the final component of the External API's
   * resource name. This field is optional. * If provided, the same will be used.
   * The service will throw an error if the specified id is already used by
   * another External API resource in the API hub. * If not provided, a system
   * generated id will be used. This value should be 4-500 characters, and valid
   * characters are /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1ExternalApi
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1ExternalApi $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1ExternalApi::class);
  }
  /**
   * Delete an External API resource in the API hub. (externalApis.delete)
   *
   * @param string $name Required. The name of the External API resource to
   * delete. Format:
   * `projects/{project}/locations/{location}/externalApis/{externalApi}`
   * @param array $optParams Optional parameters.
   * @return ApihubEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ApihubEmpty::class);
  }
  /**
   * Get details about an External API resource in the API hub. (externalApis.get)
   *
   * @param string $name Required. The name of the External API resource to
   * retrieve. Format:
   * `projects/{project}/locations/{location}/externalApis/{externalApi}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1ExternalApi
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1ExternalApi::class);
  }
  /**
   * List External API resources in the API hub.
   * (externalApis.listProjectsLocationsExternalApis)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * External API resources. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of External API
   * resources to return. The service may return fewer than this value. If
   * unspecified, at most 50 ExternalApis will be returned. The maximum value is
   * 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListExternalApis` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to
   * `ListExternalApis` must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListExternalApisResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsExternalApis($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListExternalApisResponse::class);
  }
  /**
   * Update an External API resource in the API hub. The following fields can be
   * updated: * display_name * description * documentation * endpoints * paths The
   * update_mask should be used to specify the fields being updated.
   * (externalApis.patch)
   *
   * @param string $name Identifier. Format:
   * `projects/{project}/locations/{location}/externalApi/{externalApi}`.
   * @param GoogleCloudApihubV1ExternalApi $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1ExternalApi
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1ExternalApi $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1ExternalApi::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsExternalApis::class, 'Google_Service_APIhub_Resource_ProjectsLocationsExternalApis');
