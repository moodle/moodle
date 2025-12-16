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
use Google\Service\APIhub\GoogleCloudApihubV1Curation;
use Google\Service\APIhub\GoogleCloudApihubV1ListCurationsResponse;

/**
 * The "curations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $curations = $apihubService->projects_locations_curations;
 *  </code>
 */
class ProjectsLocationsCurations extends \Google\Service\Resource
{
  /**
   * Create a curation resource in the API hub. Once a curation resource is
   * created, plugin instances can start using it. (curations.create)
   *
   * @param string $parent Required. The parent resource for the curation
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1Curation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string curationId Optional. The ID to use for the curation
   * resource, which will become the final component of the curations's resource
   * name. This field is optional. * If provided, the same will be used. The
   * service will throw an error if the specified ID is already used by another
   * curation resource in the API hub. * If not provided, a system generated ID
   * will be used. This value should be 4-500 characters, and valid characters are
   * /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Curation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Curation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Curation::class);
  }
  /**
   * Delete a curation resource in the API hub. A curation can only be deleted if
   * it's not being used by any plugin instance. (curations.delete)
   *
   * @param string $name Required. The name of the curation resource to delete.
   * Format: `projects/{project}/locations/{location}/curations/{curation}`
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
   * Get curation resource details. (curations.get)
   *
   * @param string $name Required. The name of the curation resource to retrieve.
   * Format: `projects/{project}/locations/{location}/curations/{curation}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Curation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Curation::class);
  }
  /**
   * List curation resources in the API hub.
   * (curations.listProjectsLocationsCurations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * curation resources. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * curation resources. A filter expression consists of a field name, a
   * comparison operator, and a value for filtering. The value must be a string.
   * The comparison operator must be one of: `<`, `>`, `:` or `=`. Filters are
   * case insensitive. The following fields in the `curation resource` are
   * eligible for filtering: * `create_time` - The time at which the curation was
   * created. The value should be in the
   * (RFC3339)[https://tools.ietf.org/html/rfc3339] format. Allowed comparison
   * operators: `>` and `<`. * `display_name` - The display name of the curation.
   * Allowed comparison operators: `=`. * `state` - The state of the curation.
   * Allowed comparison operators: `=`. Expressions are combined with either `AND`
   * logic operator or `OR` logical operator but not both of them together i.e.
   * only one of the `AND` or `OR` operator can be used throughout the filter
   * string and both the operators cannot be used together. No other logical
   * operators are supported. At most three filter fields are allowed in the
   * filter string and if provided more than that then `INVALID_ARGUMENT` error is
   * returned by the API. Here are a few examples: * `create_time <
   * \"2021-08-15T14:50:00Z\" AND create_time > \"2021-08-10T12:00:00Z\"` - The
   * curation resource was created before _2021-08-15 14:50:00 UTC_ and after
   * _2021-08-10 12:00:00 UTC_.
   * @opt_param int pageSize Optional. The maximum number of curation resources to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 curations will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListCurations` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to
   * `ListCurations` must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListCurationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCurations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListCurationsResponse::class);
  }
  /**
   * Update a curation resource in the API hub. The following fields in the
   * curation can be updated: * display_name * description The update_mask should
   * be used to specify the fields being updated. (curations.patch)
   *
   * @param string $name Identifier. The name of the curation. Format:
   * `projects/{project}/locations/{location}/curations/{curation}`
   * @param GoogleCloudApihubV1Curation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudApihubV1Curation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Curation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Curation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCurations::class, 'Google_Service_APIhub_Resource_ProjectsLocationsCurations');
