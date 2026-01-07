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
use Google\Service\APIhub\GoogleCloudApihubV1Attribute;
use Google\Service\APIhub\GoogleCloudApihubV1ListAttributesResponse;

/**
 * The "attributes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $attributes = $apihubService->projects_locations_attributes;
 *  </code>
 */
class ProjectsLocationsAttributes extends \Google\Service\Resource
{
  /**
   * Create a user defined attribute. Certain pre defined attributes are already
   * created by the API hub. These attributes will have type as `SYSTEM_DEFINED`
   * and can be listed via ListAttributes method. Allowed values for the same can
   * be updated via UpdateAttribute method. (attributes.create)
   *
   * @param string $parent Required. The parent resource for Attribute. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1Attribute $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string attributeId Optional. The ID to use for the attribute,
   * which will become the final component of the attribute's resource name. This
   * field is optional. * If provided, the same will be used. The service will
   * throw an error if the specified id is already used by another attribute
   * resource in the API hub. * If not provided, a system generated id will be
   * used. This value should be 4-500 characters, and valid characters are
   * /a-z[0-9]-_/.
   * @return GoogleCloudApihubV1Attribute
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1Attribute $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApihubV1Attribute::class);
  }
  /**
   * Delete an attribute. Note: System defined attributes cannot be deleted. All
   * associations of the attribute being deleted with any API hub resource will
   * also get deleted. (attributes.delete)
   *
   * @param string $name Required. The name of the attribute to delete. Format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`
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
   * Get details about the attribute. (attributes.get)
   *
   * @param string $name Required. The name of the attribute to retrieve. Format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1Attribute
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1Attribute::class);
  }
  /**
   * List all attributes. (attributes.listProjectsLocationsAttributes)
   *
   * @param string $parent Required. The parent resource for Attribute. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * Attributes. A filter expression consists of a field name, a comparison
   * operator, and a value for filtering. The value must be a string or a boolean.
   * The comparison operator must be one of: `<`, `>` or `=`. Filters are not case
   * sensitive. The following fields in the `Attribute` are eligible for
   * filtering: * `display_name` - The display name of the Attribute. Allowed
   * comparison operators: `=`. * `definition_type` - The definition type of the
   * attribute. Allowed comparison operators: `=`. * `scope` - The scope of the
   * attribute. Allowed comparison operators: `=`. * `data_type` - The type of the
   * data of the attribute. Allowed comparison operators: `=`. * `mandatory` -
   * Denotes whether the attribute is mandatory or not. Allowed comparison
   * operators: `=`. * `create_time` - The time at which the Attribute was
   * created. The value should be in the
   * (RFC3339)[https://tools.ietf.org/html/rfc3339] format. Allowed comparison
   * operators: `>` and `<`. Expressions are combined with either `AND` logic
   * operator or `OR` logical operator but not both of them together i.e. only one
   * of the `AND` or `OR` operator can be used throughout the filter string and
   * both the operators cannot be used together. No other logical operators are
   * supported. At most three filter fields are allowed in the filter string and
   * if provided more than that then `INVALID_ARGUMENT` error is returned by the
   * API. Here are a few examples: * `display_name = production` - - The display
   * name of the attribute is _production_. * `(display_name = production) AND
   * (create_time < \"2021-08-15T14:50:00Z\") AND (create_time >
   * \"2021-08-10T12:00:00Z\")` - The display name of the attribute is
   * _production_ and the attribute was created before _2021-08-15 14:50:00 UTC_
   * and after _2021-08-10 12:00:00 UTC_. * `display_name = production OR scope =
   * api` - The attribute where the display name is _production_ or the scope is
   * _api_.
   * @opt_param int pageSize Optional. The maximum number of attribute resources
   * to return. The service may return fewer than this value. If unspecified, at
   * most 50 attributes will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAttributes` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAttributes` must match the
   * call that provided the page token.
   * @return GoogleCloudApihubV1ListAttributesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAttributes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListAttributesResponse::class);
  }
  /**
   * Update the attribute. The following fields in the Attribute resource can be
   * updated: * display_name The display name can be updated for user defined
   * attributes only. * description The description can be updated for user
   * defined attributes only. * allowed_values To update the list of allowed
   * values, clients need to use the fetched list of allowed values and add or
   * remove values to or from the same list. The mutable allowed values can be
   * updated for both user defined and System defined attributes. The immutable
   * allowed values cannot be updated or deleted. The updated list of allowed
   * values cannot be empty. If an allowed value that is already used by some
   * resource's attribute is deleted, then the association between the resource
   * and the attribute value will also be deleted. * cardinality The cardinality
   * can be updated for user defined attributes only. Cardinality can only be
   * increased during an update. The update_mask should be used to specify the
   * fields being updated. (attributes.patch)
   *
   * @param string $name Identifier. The name of the attribute in the API Hub.
   * Format: `projects/{project}/locations/{location}/attributes/{attribute}`
   * @param GoogleCloudApihubV1Attribute $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApihubV1Attribute
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApihubV1Attribute $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApihubV1Attribute::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAttributes::class, 'Google_Service_APIhub_Resource_ProjectsLocationsAttributes');
