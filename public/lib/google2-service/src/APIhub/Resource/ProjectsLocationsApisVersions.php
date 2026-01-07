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
use Google\Service\APIhub\GoogleCloudApihubV1ListVersionsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1Version;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $versions = $apihubService->projects_locations_apis_versions;
 *  </code>
 */
class ProjectsLocationsApisVersions extends \Google\Service\Resource
{
  /**
   * Create an API version for an API resource in the API hub. (versions.create)
   *
   * @param string $parent Required. The parent resource for API version. Format:
   * `projects/{project}/locations/{location}/apis/{api}`
   * @param GoogleCloudApihubV1Version $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string versionId Optional. The ID to use for the API version,
   * which will become the final component of the version's resource name. This
   * field is optional. * If provided, the same will be used. The service will
   * throw an error if the specified id is already used by another version in the
   * API resource. * If not provided, a system generated id will be used. This
   * value should be 4-500 characters, overall resource name which will be of
   * format
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`, its
   * length is limited to 700 characters and valid characters are /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Version
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Version $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Version::class);
  }
  /**
   * Delete an API version. Version can only be deleted if all underlying specs,
   * operations, definitions and linked deployments are deleted. (versions.delete)
   *
   * @param string $name Required. The name of the version to delete. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, any specs from this version
   * will also be deleted. Otherwise, the request will only work if the version
   * has no specs.
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
   * Get details about the API version of an API resource. This will include
   * information about the specs and operations present in the API version as well
   * as the deployments linked to it. (versions.get)
   *
   * @param string $name Required. The name of the API version to retrieve.
   * Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Version
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Version::class);
  }
  /**
   * List API versions of an API resource in the API hub.
   * (versions.listProjectsLocationsApisVersions)
   *
   * @param string $parent Required. The parent which owns this collection of API
   * versions i.e., the API resource Format:
   * `projects/{project}/locations/{location}/apis/{api}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * Versions. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string, a number, or
   * a boolean. The comparison operator must be one of: `<`, `>` or `=`. Filters
   * are not case sensitive. The following fields in the `Version` are eligible
   * for filtering: * `display_name` - The display name of the Version. Allowed
   * comparison operators: `=`. * `create_time` - The time at which the Version
   * was created. The value should be in the
   * (RFC3339)[https://tools.ietf.org/html/rfc3339] format. Allowed comparison
   * operators: `>` and `<`. * `lifecycle.enum_values.values.id` - The allowed
   * value id of the lifecycle attribute associated with the Version. Allowed
   * comparison operators: `:`. * `lifecycle.enum_values.values.display_name` -
   * The allowed value display name of the lifecycle attribute associated with the
   * Version. Allowed comparison operators: `:`. *
   * `compliance.enum_values.values.id` - The allowed value id of the compliances
   * attribute associated with the Version. Allowed comparison operators: `:`. *
   * `compliance.enum_values.values.display_name` - The allowed value display name
   * of the compliances attribute associated with the Version. Allowed comparison
   * operators: `:`. * `accreditation.enum_values.values.id` - The allowed value
   * id of the accreditations attribute associated with the Version. Allowed
   * comparison operators: `:`. * `accreditation.enum_values.values.display_name`
   * - The allowed value display name of the accreditations attribute associated
   * with the Version. Allowed comparison operators: `:`. *
   * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.enum_values.values.id` - The allowed
   * value id of the user defined enum attribute associated with the Resource.
   * Allowed comparison operator is `:`. Here user-defined-attribute-enum-id is a
   * placeholder that can be replaced with any user defined enum attribute name. *
   * `attributes.projects/test-project-id/locations/test-location-id/
   * attributes/user-defined-attribute-id.enum_values.values.display_name` - The
   * allowed value display name of the user defined enum attribute associated with
   * the Resource. Allowed comparison operator is `:`. Here user-defined-
   * attribute-enum-display-name is a placeholder that can be replaced with any
   * user defined enum attribute enum name. * `attributes.projects/test-project-
   * id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.string_values.values` - The allowed value of the user defined string
   * attribute associated with the Resource. Allowed comparison operator is `:`.
   * Here user-defined-attribute-string is a placeholder that can be replaced with
   * any user defined string attribute name. * `attributes.projects/test-project-
   * id/locations/test-location-id/ attributes/user-defined-attribute-
   * id.json_values.values` - The allowed value of the user defined JSON attribute
   * associated with the Resource. Allowed comparison operator is `:`. Here user-
   * defined-attribute-json is a placeholder that can be replaced with any user
   * defined JSON attribute name. Expressions are combined with either `AND` logic
   * operator or `OR` logical operator but not both of them together i.e. only one
   * of the `AND` or `OR` operator can be used throughout the filter string and
   * both the operators cannot be used together. No other logical operators are
   * supported. At most three filter fields are allowed in the filter string and
   * if provided more than that then `INVALID_ARGUMENT` error is returned by the
   * API. Here are a few examples: * `lifecycle.enum_values.values.id: preview-id`
   * - The filter string specifies that the id of the allowed value associated
   * with the lifecycle attribute of the Version is _preview-id_. *
   * `lifecycle.enum_values.values.display_name: \"Preview Display Name\"` - The
   * filter string specifies that the display name of the allowed value associated
   * with the lifecycle attribute of the Version is `Preview Display Name`. *
   * `lifecycle.enum_values.values.id: preview-id AND create_time <
   * \"2021-08-15T14:50:00Z\" AND create_time > \"2021-08-10T12:00:00Z\"` - The id
   * of the allowed value associated with the lifecycle attribute of the Version
   * is _preview-id_ and it was created before _2021-08-15 14:50:00 UTC_ and after
   * _2021-08-10 12:00:00 UTC_. * `compliance.enum_values.values.id: gdpr-id OR
   * compliance.enum_values.values.id: pci-dss-id` - The id of the allowed value
   * associated with the compliance attribute is _gdpr-id_ or _pci-dss-id_. *
   * `lifecycle.enum_values.values.id: preview-id AND attributes.projects/test-
   * project-id/locations/test-location-id/
   * attributes/17650f90-4a29-4971-b3c0-d5532da3764b.string_values.values: test` -
   * The filter string specifies that the id of the allowed value associated with
   * the lifecycle attribute of the Version is _preview-id_ and the value of the
   * user defined attribute of type string is _test_.
   * @opt_param int pageSize Optional. The maximum number of versions to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * versions will be returned. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListVersions` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to
   * `ListVersions` must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApisVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListVersionsResponse::class);
  }
  /**
   * Update API version. The following fields in the version can be updated
   * currently: * display_name * description * documentation * deployments *
   * lifecycle * compliance * accreditation * attributes The update_mask should be
   * used to specify the fields being updated. (versions.patch)
   *
   * @param string $name Identifier. The name of the version. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param GoogleCloudApihubV1Version $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Version
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Version $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Version::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApisVersions::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApisVersions');
