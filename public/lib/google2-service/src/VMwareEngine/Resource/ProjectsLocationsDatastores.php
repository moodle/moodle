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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\Datastore;
use Google\Service\VMwareEngine\ListDatastoresResponse;
use Google\Service\VMwareEngine\Operation;

/**
 * The "datastores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $datastores = $vmwareengineService->projects_locations_datastores;
 *  </code>
 */
class ProjectsLocationsDatastores extends \Google\Service\Resource
{
  /**
   * Creates a new `Datastore` resource in a given project and location.
   * Datastores are regional resources (datastores.create)
   *
   * @param string $parent Required. The resource name of the location to create
   * the new datastore in. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1`
   * @param Datastore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string datastoreId Required. The user-provided identifier of the
   * datastore to be created. This identifier must be unique among each
   * `Datastore` within the parent and becomes the final token in the name URI.
   * The identifier must meet the following requirements: * Only contains 1-63
   * alphanumeric characters and hyphens * Begins with an alphabetical character *
   * Ends with a non-hyphen character * Not formatted as a UUID * Complies with
   * [RFC 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Datastore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a `Datastore` resource. You can only delete a Datastore after all
   * resources that refer to it are deleted. For example, multiple clusters of the
   * same private cloud or different private clouds can refer to the same
   * datastore. (datastores.delete)
   *
   * @param string $name Required. The resource name of the Datastore to be
   * deleted. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastore/my-datastore`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. Checksum used to ensure that the user-
   * provided value is up to date before the server processes the request. The
   * server compares provided checksum with the current checksum of the resource.
   * If the user-provided value is out of date, this request returns an `ABORTED`
   * error.
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Retrieves a `Datastore` resource by its resource name. The resource contains
   * details of the Datastore, such as its description, subnets, type, and more.
   * (datastores.get)
   *
   * @param string $name Required. The resource name of the Datastore to retrieve.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/my-datastore`
   * @param array $optParams Optional parameters.
   * @return Datastore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Datastore::class);
  }
  /**
   * Lists `Datastore` resources in a given project and location.
   * (datastores.listProjectsLocationsDatastores)
   *
   * @param string $parent Required. The resource name of the location to query
   * for Datastores. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter expression that matches resources
   * returned in the response. The expression must specify the field name, a
   * comparison operator, and the value that you want to use for filtering. The
   * value must be a string, a number, or a boolean. The comparison operator must
   * be `=`, `!=`, `>`, or `<`. For example, if you are filtering a list of
   * datastores, you can exclude the ones named `example-datastore` by specifying
   * `name != "example-datastore"`. To filter on multiple expressions, provide
   * each separate expression within parentheses. For example: ``` (name =
   * "example-datastore") (createTime > "2021-04-12T08:15:10.40Z") ``` By default,
   * each expression is an `AND` expression. However, you can include `AND` and
   * `OR` expressions explicitly. For example: ``` (name = "example-datastore-1")
   * AND (createTime > "2021-04-12T08:15:10.40Z") OR (name = "example-
   * datastore-2") ```
   * @opt_param string orderBy Optional. Sorts list results by a certain order. By
   * default, returned results are ordered by `name` in ascending order. You can
   * also sort results in descending order based on the `name` value using
   * `orderBy="name desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize Optional. The maximum number of results to return in
   * one page. The maximum value is coerced to 1000. The default value of this
   * field is 500.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDatastores` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListDatastores` must match the
   * call that provided the page token.
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return ListDatastoresResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatastores($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDatastoresResponse::class);
  }
  /**
   * Modifies a Datastore resource. Only the following fields can be updated:
   * `description`. Only fields specified in `updateMask` are applied.
   * (datastores.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * datastore. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/datastore`
   * @param Datastore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Datastore resource by the update. The fields
   * specified in the `update_mask` are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten. Only the following
   * fields can be updated: `description`.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Datastore $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatastores::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsDatastores');
