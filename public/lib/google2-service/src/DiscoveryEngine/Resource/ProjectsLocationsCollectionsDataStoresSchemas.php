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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListSchemasResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Schema;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "schemas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $schemas = $discoveryengineService->projects_locations_collections_dataStores_schemas;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresSchemas extends \Google\Service\Resource
{
  /**
   * Creates a Schema. (schemas.create)
   *
   * @param string $parent Required. The parent data store resource name, in the
   * format of `projects/{project}/locations/{location}/collections/{collection}/d
   * ataStores/{data_store}`.
   * @param GoogleCloudDiscoveryengineV1Schema $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string schemaId Required. The ID to use for the Schema, which
   * becomes the final component of the Schema.name. This field should conform to
   * [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a length limit
   * of 63 characters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Schema $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Schema. (schemas.delete)
   *
   * @param string $name Required. The full resource name of the schema, in the
   * format of `projects/{project}/locations/{location}/collections/{collection}/d
   * ataStores/{data_store}/schemas/{schema}`.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a Schema. (schemas.get)
   *
   * @param string $name Required. The full resource name of the schema, in the
   * format of `projects/{project}/locations/{location}/collections/{collection}/d
   * ataStores/{data_store}/schemas/{schema}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Schema
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Schema::class);
  }
  /**
   * Gets a list of Schemas.
   * (schemas.listProjectsLocationsCollectionsDataStoresSchemas)
   *
   * @param string $parent Required. The parent data store resource name, in the
   * format of `projects/{project}/locations/{location}/collections/{collection}/d
   * ataStores/{data_store}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of Schemas to return. The service
   * may return fewer than this value. If unspecified, at most 100 Schemas are
   * returned. The maximum value is 1000; values above 1000 are set to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * SchemaService.ListSchemas call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to SchemaService.ListSchemas
   * must match the call that provided the page token.
   * @return GoogleCloudDiscoveryengineV1ListSchemasResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStoresSchemas($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListSchemasResponse::class);
  }
  /**
   * Updates a Schema. (schemas.patch)
   *
   * @param string $name Immutable. The full resource name of the schema, in the
   * format of `projects/{project}/locations/{location}/collections/{collection}/d
   * ataStores/{data_store}/schemas/{schema}`. This field must be a UTF-8 encoded
   * string with a length limit of 1024 characters.
   * @param GoogleCloudDiscoveryengineV1Schema $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the Schema is not found, a
   * new Schema is created. In this situation, `update_mask` is ignored.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Schema $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresSchemas::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresSchemas');
