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

namespace Google\Service\FirebaseDataConnect\Resource;

use Google\Service\FirebaseDataConnect\ListSchemasResponse;
use Google\Service\FirebaseDataConnect\Operation;
use Google\Service\FirebaseDataConnect\Schema;

/**
 * The "schemas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasedataconnectService = new Google\Service\FirebaseDataConnect(...);
 *   $schemas = $firebasedataconnectService->projects_locations_services_schemas;
 *  </code>
 */
class ProjectsLocationsServicesSchemas extends \Google\Service\Resource
{
  /**
   * Creates a new Schema in a given project and location. Only creation of
   * `schemas/main` is supported and calling create with any other schema ID will
   * result in an error. (schemas.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Schema $postBody
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
   * @opt_param string schemaId Required. The ID to use for the schema, which will
   * become the final component of the schema's resource name. Currently, only
   * `main` is supported and any other schema ID will result in an error.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Schema, but do not actually update it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Schema $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Schema. Because the schema and connectors must be compatible
   * at all times, if this is called while any connectors are active, this will
   * result in an error. (schemas.delete)
   *
   * @param string $name Required. The name of the schema to delete, in the
   * format: ```
   * projects/{project}/locations/{location}/services/{service}/schemas/{schema}
   * ```
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Schema is not found,
   * the request will succeed but no action will be taken on the server.
   * @opt_param string etag Optional. The etag of the Schema. If this is provided,
   * it must match the server's etag.
   * @opt_param bool force Optional. If set to true, any child resources (i.e.
   * SchemaRevisions) will also be deleted.
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
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Schema, but do not actually delete it.
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
   * Gets details of a single Schema. (schemas.get)
   *
   * @param string $name Required. The name of the schema to retrieve, in the
   * format: ```
   * projects/{project}/locations/{location}/services/{service}/schemas/{schema}
   * ```
   * @param array $optParams Optional parameters.
   * @return Schema
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Schema::class);
  }
  /**
   * Lists Schemas in a given project and location.
   * (schemas.listProjectsLocationsServicesSchemas)
   *
   * @param string $parent Required. Value of parent.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListSchemas` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListSchemas` must match the
   * call that provided the page token.
   * @return ListSchemasResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServicesSchemas($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSchemasResponse::class);
  }
  /**
   * Updates the parameters of a single Schema, and creates a new SchemaRevision
   * with the updated Schema. (schemas.patch)
   *
   * @param string $name Identifier. The relative resource name of the schema, in
   * the format: ```
   * projects/{project}/locations/{location}/services/{service}/schemas/{schema}
   * ``` Right now, the only supported schema is "main".
   * @param Schema $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Schema is not found, a
   * new Schema will be created. In this case, `update_mask` is ignored.
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
   * fields to be overwritten in the Schema resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Schema, but do not actually update it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Schema $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServicesSchemas::class, 'Google_Service_FirebaseDataConnect_Resource_ProjectsLocationsServicesSchemas');
