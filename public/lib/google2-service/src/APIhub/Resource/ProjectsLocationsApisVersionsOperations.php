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
use Google\Service\APIhub\GoogleCloudApihubV1ApiOperation;
use Google\Service\APIhub\GoogleCloudApihubV1ListApiOperationsResponse;

/**
 * The "operations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $operations = $apihubService->projects_locations_apis_versions_operations;
 *  </code>
 */
class ProjectsLocationsApisVersionsOperations extends \Google\Service\Resource
{
  /**
   * Create an apiOperation in an API version. An apiOperation can be created only
   * if the version has no apiOperations which were created by parsing a spec.
   * (operations.create)
   *
   * @param string $parent Required. The parent resource for the operation
   * resource. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param GoogleCloudApihubV1ApiOperation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string apiOperationId Optional. The ID to use for the operation
   * resource, which will become the final component of the operation's resource
   * name. This field is optional. * If provided, the same will be used. The
   * service will throw an error if the specified id is already used by another
   * operation resource in the API hub. * If not provided, a system generated id
   * will be used. This value should be 4-500 characters, overall resource name
   * which will be of format `projects/{project}/locations/{location}/apis/{api}/v
   * ersions/{version}/operations/{operation}`, its length is limited to 700
   * characters, and valid characters are /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1ApiOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1ApiOperation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1ApiOperation::class);
  }
  /**
   * Delete an operation in an API version and we can delete only the operations
   * created via create API. If the operation was created by parsing the spec,
   * then it can be deleted by editing or deleting the spec. (operations.delete)
   *
   * @param string $name Required. The name of the operation resource to delete.
   * Format: `projects/{project}/locations/{location}/apis/{api}/versions/{version
   * }/operations/{operation}`
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
   * Get details about a particular operation in API version. (operations.get)
   *
   * @param string $name Required. The name of the operation to retrieve. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}/operat
   * ions/{operation}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1ApiOperation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1ApiOperation::class);
  }
  /**
   * List operations in an API version.
   * (operations.listProjectsLocationsApisVersionsOperations)
   *
   * @param string $parent Required. The parent which owns this collection of
   * operations i.e., the API version. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * ApiOperations. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string or a boolean.
   * The comparison operator must be one of: `<`, `>` or `=`. Filters are not case
   * sensitive. The following fields in the `ApiOperation` are eligible for
   * filtering: * `name` - The ApiOperation resource name. Allowed comparison
   * operators: `=`. * `details.http_operation.path.path` - The http operation's
   * complete path relative to server endpoint. Allowed comparison operators: `=`.
   * * `details.http_operation.method` - The http operation method type. Allowed
   * comparison operators: `=`. * `details.deprecated` - Indicates if the
   * ApiOperation is deprecated. Allowed values are True / False indicating the
   * deprycation status of the ApiOperation. Allowed comparison operators: `=`. *
   * `create_time` - The time at which the ApiOperation was created. The value
   * should be in the (RFC3339)[https://tools.ietf.org/html/rfc3339] format.
   * Allowed comparison operators: `>` and `<`. * `attributes.projects/test-
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
   * that can be replaced with any user defined JSON attribute name. Expressions
   * are combined with either `AND` logic operator or `OR` logical operator but
   * not both of them together i.e. only one of the `AND` or `OR` operator can be
   * used throughout the filter string and both the operators cannot be used
   * together. No other logical operators are supported. At most three filter
   * fields are allowed in the filter string and if provided more than that then
   * `INVALID_ARGUMENT` error is returned by the API. Here are a few examples: *
   * `details.deprecated = True` - The ApiOperation is deprecated. *
   * `details.http_operation.method = GET AND create_time <
   * \"2021-08-15T14:50:00Z\" AND create_time > \"2021-08-10T12:00:00Z\"` - The
   * method of the http operation of the ApiOperation is _GET_ and the spec was
   * created before _2021-08-15 14:50:00 UTC_ and after _2021-08-10 12:00:00 UTC_.
   * * `details.http_operation.method = GET OR details.http_operation.method =
   * POST`. - The http operation of the method of ApiOperation is _GET_ or _POST_.
   * * `details.deprecated = True AND attributes.projects/test-project-
   * id/locations/test-location-id/
   * attributes/17650f90-4a29-4971-b3c0-d5532da3764b.string_values.values: test` -
   * The filter string specifies that the ApiOperation is deprecated and the value
   * of the user defined attribute of type string is _test_.
   * @opt_param int pageSize Optional. The maximum number of operations to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * operations will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApiOperations` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters (except page_size) provided to
   * `ListApiOperations` must match the call that provided the page token.
   * @return GoogleCloudApihubV1ListApiOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApisVersionsOperations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListApiOperationsResponse::class);
  }
  /**
   * Update an operation in an API version. The following fields in the
   * ApiOperation resource can be updated: * details.description *
   * details.documentation * details.http_operation.path *
   * details.http_operation.method * details.deprecated * attributes *
   * details.mcp_tool.title * details.mcp_tool.description *
   * details.mcp_tool.input_schema * details.mcp_tool.output_schema *
   * details.input_schema * details.output_schema *
   * details.mcp_tool.annotations.title *
   * details.mcp_tool.annotations.read_only_hint *
   * details.mcp_tool.annotations.destructive_hint *
   * details.mcp_tool.annotations.idempotent_hint *
   * details.mcp_tool.annotations.open_world_hint *
   * details.mcp_tool.annotations.additional_hints The update_mask should be used
   * to specify the fields being updated. An operation can be updated only if the
   * operation was created via CreateApiOperation API. If the operation was
   * created by parsing the spec, then it can be edited by updating the spec.
   * (operations.patch)
   *
   * @param string $name Identifier. The name of the operation. Format: `projects/
   * {project}/locations/{location}/apis/{api}/versions/{version}/operations/{oper
   * ation}`
   * @param GoogleCloudApihubV1ApiOperation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1ApiOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1ApiOperation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1ApiOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApisVersionsOperations::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApisVersionsOperations');
