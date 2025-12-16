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
use Google\Service\APIhub\GoogleCloudApihubV1FetchAdditionalSpecContentResponse;
use Google\Service\APIhub\GoogleCloudApihubV1LintSpecRequest;
use Google\Service\APIhub\GoogleCloudApihubV1ListSpecsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1Spec;
use Google\Service\APIhub\GoogleCloudApihubV1SpecContents;

/**
 * The "specs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $specs = $apihubService->projects_locations_apis_versions_specs;
 *  </code>
 */
class ProjectsLocationsApisVersionsSpecs extends \Google\Service\Resource
{
  /**
   * Add a spec to an API version in the API hub. Multiple specs can be added to
   * an API version. Note, while adding a spec, at least one of `contents` or
   * `source_uri` must be provided. If `contents` is provided, then `spec_type`
   * must also be provided. On adding a spec with contents to the version, the
   * operations present in it will be added to the version.Note that the file
   * contents in the spec should be of the same type as defined in the
   * `projects/{project}/locations/{location}/attributes/system-spec-type`
   * attribute associated with spec resource. Note that specs of various types can
   * be uploaded, however parsing of details is supported for OpenAPI spec
   * currently. In order to access the information parsed from the spec, use the
   * GetSpec method. In order to access the raw contents for a particular spec,
   * use the GetSpecContents method. In order to access the operations parsed from
   * the spec, use the ListAPIOperations method. (specs.create)
   *
   * @param string $parent Required. The parent resource for Spec. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param GoogleCloudApihubV1Spec $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string specId Optional. The ID to use for the spec, which will
   * become the final component of the spec's resource name. This field is
   * optional. * If provided, the same will be used. The service will throw an
   * error if the specified id is already used by another spec in the API
   * resource. * If not provided, a system generated id will be used. This value
   * should be 4-500 characters, overall resource name which will be of format `pr
   * ojects/{project}/locations/{location}/apis/{api}/versions/{version}/specs/{sp
   * ec}`, its length is limited to 1000 characters and valid characters are
   * /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Spec
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Spec $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Spec::class);
  }
  /**
   * Delete a spec. Deleting a spec will also delete the associated operations
   * from the version. (specs.delete)
   *
   * @param string $name Required. The name of the spec to delete. Format: `projec
   * ts/{project}/locations/{location}/apis/{api}/versions/{version}/specs/{spec}`
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
   * Fetch additional spec content. (specs.fetchAdditionalSpecContent)
   *
   * @param string $name Required. The name of the spec whose contents need to be
   * retrieved. Format: `projects/{project}/locations/{location}/apis/{api}/versio
   * ns/{version}/specs/{spec}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string specContentType Optional. The type of the spec contents to
   * be retrieved.
   * @return GoogleCloudApihubV1FetchAdditionalSpecContentResponse
   * @throws \Google\Service\Exception
   */
  public function fetchAdditionalSpecContent($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('fetchAdditionalSpecContent', [$params], GoogleCloudApihubV1FetchAdditionalSpecContentResponse::class);
  }
  /**
   * Get details about the information parsed from a spec. Note that this method
   * does not return the raw spec contents. Use GetSpecContents method to retrieve
   * the same. (specs.get)
   *
   * @param string $name Required. The name of the spec to retrieve. Format: `proj
   * ects/{project}/locations/{location}/apis/{api}/versions/{version}/specs/{spec
   * }`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Spec
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Spec::class);
  }
  /**
   * Get spec contents. (specs.getContents)
   *
   * @param string $name Required. The name of the spec whose contents need to be
   * retrieved. Format: `projects/{project}/locations/{location}/apis/{api}/versio
   * ns/{version}/specs/{spec}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1SpecContents
   * @throws \Google\Service\Exception
   */
  public function getContents($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getContents', [$params], GoogleCloudApihubV1SpecContents::class);
  }
  /**
   * Lints the requested spec and updates the corresponding API Spec with the lint
   * response. This lint response will be available in all subsequent Get and List
   * Spec calls to Core service. (specs.lint)
   *
   * @param string $name Required. The name of the spec to be linted. Format: `pro
   * jects/{project}/locations/{location}/apis/{api}/versions/{version}/specs/{spe
   * c}`
   * @param GoogleCloudApihubV1LintSpecRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ApihubEmpty
   * @throws \Google\Service\Exception
   */
  public function lint($name, GoogleCloudApihubV1LintSpecRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lint', [$params], ApihubEmpty::class);
  }
  /**
   * List specs corresponding to a particular API resource.
   * (specs.listProjectsLocationsApisVersionsSpecs)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * specs. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * Specs. A filter expression consists of a field name, a comparison operator,
   * and a value for filtering. The value must be a string. The comparison
   * operator must be one of: `<`, `>`, `:` or `=`. Filters are not case
   * sensitive. The following fields in the `Spec` are eligible for filtering: *
   * `display_name` - The display name of the Spec. Allowed comparison operators:
   * `=`. * `create_time` - The time at which the Spec was created. The value
   * should be in the (RFC3339)[https://tools.ietf.org/html/rfc3339] format.
   * Allowed comparison operators: `>` and `<`. *
   * `spec_type.enum_values.values.id` - The allowed value id of the spec_type
   * attribute associated with the Spec. Allowed comparison operators: `:`. *
   * `spec_type.enum_values.values.display_name` - The allowed value display name
   * of the spec_type attribute associated with the Spec. Allowed comparison
   * operators: `:`. * `lint_response.json_values.values` - The json value of the
   * lint_response attribute associated with the Spec. Allowed comparison
   * operators: `:`. * `mime_type` - The MIME type of the Spec. Allowed comparison
   * operators: `=`. * `attributes.projects/test-project-id/locations/test-
   * location-id/ attributes/user-defined-attribute-id.enum_values.values.id` -
   * The allowed value id of the user defined enum attribute associated with the
   * Resource. Allowed comparison operator is `:`. Here user-defined-attribute-
   * enum-id is a placeholder that can be replaced with any user defined enum
   * attribute name. * `attributes.projects/test-project-id/locations/test-
   * location-id/ attributes/user-defined-attribute-
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
   * `spec_type.enum_values.values.id: rest-id` - The filter string specifies that
   * the id of the allowed value associated with the spec_type attribute is _rest-
   * id_. * `spec_type.enum_values.values.display_name: \"Rest Display Name\"` -
   * The filter string specifies that the display name of the allowed value
   * associated with the spec_type attribute is `Rest Display Name`. *
   * `spec_type.enum_values.values.id: grpc-id AND create_time <
   * \"2021-08-15T14:50:00Z\" AND create_time > \"2021-08-10T12:00:00Z\"` - The id
   * of the allowed value associated with the spec_type attribute is _grpc-id_ and
   * the spec was created before _2021-08-15 14:50:00 UTC_ and after _2021-08-10
   * 12:00:00 UTC_. * `spec_type.enum_values.values.id: rest-id OR
   * spec_type.enum_values.values.id: grpc-id` - The id of the allowed value
   * associated with the spec_type attribute is _rest-id_ or _grpc-id_. *
   * `spec_type.enum_values.values.id: rest-id AND attributes.projects/test-
   * project-id/locations/test-location-id/
   * attributes/17650f90-4a29-4971-b3c0-d5532da3764b.enum_values.values.id: test`
   * - The filter string specifies that the id of the allowed value associated
   * with the spec_type attribute is _rest-id_ and the id of the allowed value
   * associated with the user defined attribute of type enum is _test_.
   * @opt_param int pageSize Optional. The maximum number of specs to return. The
   * service may return fewer than this value. If unspecified, at most 50 specs
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListSpecs` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListSpecs` must match the call
   * that provided the page token.
   * @return GoogleCloudApihubV1ListSpecsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApisVersionsSpecs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListSpecsResponse::class);
  }
  /**
   * Update spec. The following fields in the spec can be updated: * display_name
   * * source_uri * lint_response * attributes * contents * spec_type In case of
   * an OAS spec, updating spec contents can lead to: 1. Creation, deletion and
   * update of operations. 2. Creation, deletion and update of definitions. 3.
   * Update of other info parsed out from the new spec. In case of contents or
   * source_uri being present in update mask, spec_type must also be present.
   * Also, spec_type can not be present in update mask if contents or source_uri
   * is not present. The update_mask should be used to specify the fields being
   * updated. (specs.patch)
   *
   * @param string $name Identifier. The name of the spec. Format: `projects/{proj
   * ect}/locations/{location}/apis/{api}/versions/{version}/specs/{spec}`
   * @param GoogleCloudApihubV1Spec $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Spec
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Spec $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Spec::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApisVersionsSpecs::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApisVersionsSpecs');
