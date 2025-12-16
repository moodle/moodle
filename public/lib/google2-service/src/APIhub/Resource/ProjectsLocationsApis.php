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
use Google\Service\APIhub\GoogleCloudApihubV1Api;
use Google\Service\APIhub\GoogleCloudApihubV1ListApisResponse;

/**
 * The "apis" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $apis = $apihubService->projects_locations_apis;
 *  </code>
 */
class ProjectsLocationsApis extends \Google\Service\Resource
{
  /**
   * Create an API resource in the API hub. Once an API resource is created,
   * versions can be added to it. (apis.create)
   *
   * @param string $parent Required. The parent resource for the API resource.
   * Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1Api $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string apiId Optional. The ID to use for the API resource, which
   * will become the final component of the API's resource name. This field is
   * optional. * If provided, the same will be used. The service will throw an
   * error if the specified id is already used by another API resource in the API
   * hub. * If not provided, a system generated id will be used. This value should
   * be 4-500 characters, and valid characters are /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Api
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Api $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Api::class);
  }
  /**
   * Delete an API resource in the API hub. API can only be deleted if all
   * underlying versions are deleted. (apis.delete)
   *
   * @param string $name Required. The name of the API resource to delete. Format:
   * `projects/{project}/locations/{location}/apis/{api}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, any versions from this API
   * will also be deleted. Otherwise, the request will only work if the API has no
   * versions.
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
   * Get API resource details including the API versions contained in it.
   * (apis.get)
   *
   * @param string $name Required. The name of the API resource to retrieve.
   * Format: `projects/{project}/locations/{location}/apis/{api}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Api
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Api::class);
  }
  /**
   * List API resources in the API hub. (apis.listProjectsLocationsApis)
   *
   * @param string $parent Required. The parent, which owns this collection of API
   * resources. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * ApiResources. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string. The
   * comparison operator must be one of: `<`, `>`, `:` or `=`. Filters are not
   * case sensitive. The following fields in the `ApiResource` are eligible for
   * filtering: * `owner.email` - The email of the team which owns the
   * ApiResource. Allowed comparison operators: `=`. * `create_time` - The time at
   * which the ApiResource was created. The value should be in the
   * (RFC3339)[https://tools.ietf.org/html/rfc3339] format. Allowed comparison
   * operators: `>` and `<`. * `display_name` - The display name of the
   * ApiResource. Allowed comparison operators: `=`. *
   * `target_user.enum_values.values.id` - The allowed value id of the target
   * users attribute associated with the ApiResource. Allowed comparison operator
   * is `:`. * `target_user.enum_values.values.display_name` - The allowed value
   * display name of the target users attribute associated with the ApiResource.
   * Allowed comparison operator is `:`. * `team.enum_values.values.id` - The
   * allowed value id of the team attribute associated with the ApiResource.
   * Allowed comparison operator is `:`. * `team.enum_values.values.display_name`
   * - The allowed value display name of the team attribute associated with the
   * ApiResource. Allowed comparison operator is `:`. *
   * `business_unit.enum_values.values.id` - The allowed value id of the business
   * unit attribute associated with the ApiResource. Allowed comparison operator
   * is `:`. * `business_unit.enum_values.values.display_name` - The allowed value
   * display name of the business unit attribute associated with the ApiResource.
   * Allowed comparison operator is `:`. * `maturity_level.enum_values.values.id`
   * - The allowed value id of the maturity level attribute associated with the
   * ApiResource. Allowed comparison operator is `:`. *
   * `maturity_level.enum_values.values.display_name` - The allowed value display
   * name of the maturity level attribute associated with the ApiResource. Allowed
   * comparison operator is `:`. * `api_style.enum_values.values.id` - The allowed
   * value id of the api style attribute associated with the ApiResource. Allowed
   * comparison operator is `:`. * `api_style.enum_values.values.display_name` -
   * The allowed value display name of the api style attribute associated with the
   * ApiResource. Allowed comparison operator is `:`. * `attributes.projects/test-
   * project-id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.enum_values.values.id` - The allowed value id of the user defined enum
   * attribute associated with the Resource. Allowed comparison operator is `:`.
   * Here user-defined-attribute-enum-id is a placeholder that can be replaced
   * with any user defined enum attribute name. * `attributes.projects/test-
   * project-id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.enum_values.values.display_name` - The allowed value display name of the
   * user defined enum attribute associated with the Resource. Allowed comparison
   * operator is `:`. Here user-defined-attribute-enum-display-name is a
   * placeholder that can be replaced with any user defined enum attribute enum
   * name. * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.string_values.values` - The allowed
   * value of the user defined string attribute associated with the Resource.
   * Allowed comparison operator is `:`. Here user-defined-attribute-string is a
   * placeholder that can be replaced with any user defined string attribute name.
   * * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.json_values.values` - The allowed value
   * of the user defined JSON attribute associated with the Resource. Allowed
   * comparison operator is `:`. Here user-defined-attribute-json is a placeholder
   * that can be replaced with any user defined JSON attribute name. A filter
   * function is also supported in the filter string. The filter function is
   * `id(name)`. The `id(name)` function returns the id of the resource name. For
   * example, `id(name) = \"api-1\"` is equivalent to `name = \"projects/test-
   * project-id/locations/test-location-id/apis/api-1\"` provided the parent is
   * `projects/test-project-id/locations/test-location-id`. Another supported
   * filter function is `plugins(source_metadata)`. This function filters for
   * resources that are associated with a specific plugin. For example,
   * `plugins(source_metadata) : "projects/test-project-id/locations/test-
   * location-id/plugins/test-plugin-id"` will return resources sourced from the
   * given plugin. Expressions are combined with either `AND` logic operator or
   * `OR` logical operator but not both of them together i.e. only one of the
   * `AND` or `OR` operator can be used throughout the filter string and both the
   * operators cannot be used together. No other logical operators are supported.
   * At most three filter fields are allowed in the filter string and if provided
   * more than that then `INVALID_ARGUMENT` error is returned by the API. Here are
   * a few examples: * `owner.email = \"apihub@google.com\"` - - The owner team
   * email is _apihub@google.com_. * `owner.email = \"apihub@google.com\" AND
   * create_time < \"2021-08-15T14:50:00Z\" AND create_time >
   * \"2021-08-10T12:00:00Z\"` - The owner team email is _apihub@google.com_ and
   * the api was created before _2021-08-15 14:50:00 UTC_ and after _2021-08-10
   * 12:00:00 UTC_. * `owner.email = \"apihub@google.com\" OR
   * team.enum_values.values.id: apihub-team-id` - The filter string specifies the
   * APIs where the owner team email is _apihub@google.com_ or the id of the
   * allowed value associated with the team attribute is _apihub-team-id_. *
   * `owner.email = \"apihub@google.com\" OR team.enum_values.values.display_name:
   * ApiHub Team` - The filter string specifies the APIs where the owner team
   * email is _apihub@google.com_ or the display name of the allowed value
   * associated with the team attribute is `ApiHub Team`. * `owner.email =
   * \"apihub@google.com\" AND attributes.projects/test-project-id/locations/test-
   * location-id/
   * attributes/17650f90-4a29-4971-b3c0-d5532da3764b.enum_values.values.id:
   * test_enum_id AND attributes.projects/test-project-id/locations/test-location-
   * id/ attributes/1765\0f90-4a29-5431-b3d0-d5532da3764c.string_values.values:
   * test_string_value` - The filter string specifies the APIs where the owner
   * team email is _apihub@google.com_ and the id of the allowed value associated
   * with the user defined attribute of type enum is _test_enum_id_ and the value
   * of the user defined attribute of type string is _test_..
   * @opt_param int pageSize Optional. The maximum number of API resources to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 Apis will be returned. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApis` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to `ListApis`
   * must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListApisResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApis($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListApisResponse::class);
  }
  /**
   * Update an API resource in the API hub. The following fields in the API can be
   * updated: * display_name * description * owner * documentation * target_user *
   * team * business_unit * maturity_level * api_style * attributes * fingerprint
   * The update_mask should be used to specify the fields being updated. Updating
   * the owner field requires complete owner message and updates both owner and
   * email fields. (apis.patch)
   *
   * @param string $name Identifier. The name of the API resource in the API Hub.
   * Format: `projects/{project}/locations/{location}/apis/{api}`
   * @param GoogleCloudApihubV1Api $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Api
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Api $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Api::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApis::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApis');
