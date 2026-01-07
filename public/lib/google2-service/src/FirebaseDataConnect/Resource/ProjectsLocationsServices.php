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

use Google\Service\FirebaseDataConnect\GraphqlRequest;
use Google\Service\FirebaseDataConnect\GraphqlResponse;
use Google\Service\FirebaseDataConnect\ListServicesResponse;
use Google\Service\FirebaseDataConnect\Operation;
use Google\Service\FirebaseDataConnect\Service;

/**
 * The "services" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasedataconnectService = new Google\Service\FirebaseDataConnect(...);
 *   $services = $firebasedataconnectService->projects_locations_services;
 *  </code>
 */
class ProjectsLocationsServices extends \Google\Service\Resource
{
  /**
   * Creates a new Service in a given project and location. (services.create)
   *
   * @param string $parent Required. Value of parent.
   * @param Service $postBody
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
   * @opt_param string serviceId Required. The ID to use for the service, which
   * will become the final component of the service's resource name.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Service, but do not actually create it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Service $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Service. (services.delete)
   *
   * @param string $name Required. The name of the service to delete, in the
   * format: ``` projects/{project}/locations/{location}/services/{service} ```
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Service is not found,
   * the request will succeed but no action will be taken on the server.
   * @opt_param string etag Optional. The etag of the Service. If this is
   * provided, it must match the server's etag.
   * @opt_param bool force Optional. If set to true, any child resources (i.e.
   * Schema, SchemaRevisions, Connectors, and ConnectorRevisions) will also be
   * deleted. Otherwise, the request will only work if the Service has no child
   * resources.
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
   * preview the Service, but do not actually delete it.
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
   * Execute any GraphQL query and mutation against the Firebase Data Connect's
   * generated GraphQL schema. Grants full read and write access to the connected
   * data sources. Note: Use introspection query to explore the generated GraphQL
   * schema. (services.executeGraphql)
   *
   * @param string $name Required. The relative resource name of Firebase Data
   * Connect service, in the format: ```
   * projects/{project}/locations/{location}/services/{service} ```
   * @param GraphqlRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GraphqlResponse
   * @throws \Google\Service\Exception
   */
  public function executeGraphql($name, GraphqlRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeGraphql', [$params], GraphqlResponse::class);
  }
  /**
   * Execute any GraphQL query against the Firebase Data Connect's generated
   * GraphQL schema. Grants full read to the connected data sources.
   * `ExecuteGraphqlRead` is identical to `ExecuteGraphql` except it only accepts
   * read-only query. (services.executeGraphqlRead)
   *
   * @param string $name Required. The relative resource name of Firebase Data
   * Connect service, in the format: ```
   * projects/{project}/locations/{location}/services/{service} ```
   * @param GraphqlRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GraphqlResponse
   * @throws \Google\Service\Exception
   */
  public function executeGraphqlRead($name, GraphqlRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeGraphqlRead', [$params], GraphqlResponse::class);
  }
  /**
   * Gets details of a single Service. (services.get)
   *
   * @param string $name Required. The name of the service to retrieve, in the
   * format: ``` projects/{project}/locations/{location}/services/{service} ```
   * @param array $optParams Optional parameters.
   * @return Service
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Service::class);
  }
  /**
   * Execute introspection query against the Firebase Data Connect's generated
   * GraphQL schema. GraphQL introspection query provides metadata such as what
   * tables the schema have, what queries and mutations can be performed on the
   * schema, and so on. Read more at https://graphql.org/learn/introspection.
   * IntrospectGraphql can read schema metadata but cannot read rows from Cloud
   * SQL instance, which can be done via ExecuteGraphqlRead.
   * (services.introspectGraphql)
   *
   * @param string $name Required. The relative resource name of Firebase Data
   * Connect service, in the format: ```
   * projects/{project}/locations/{location}/services/{service} ```
   * @param GraphqlRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GraphqlResponse
   * @throws \Google\Service\Exception
   */
  public function introspectGraphql($name, GraphqlRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('introspectGraphql', [$params], GraphqlResponse::class);
  }
  /**
   * Lists Services in a given project and location.
   * (services.listProjectsLocationsServices)
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
   * `ListServices` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListServices` must match the
   * call that provided the page token.
   * @return ListServicesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListServicesResponse::class);
  }
  /**
   * Updates the parameters of a single Service. (services.patch)
   *
   * @param string $name Identifier. The relative resource name of the Firebase
   * Data Connect service, in the format: ```
   * projects/{project}/locations/{location}/services/{service} ``` Note that the
   * service ID is specific to Firebase Data Connect and does not correspond to
   * any of the instance IDs of the underlying data source connections.
   * @param Service $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Service is not found,
   * a new Service will be created. In this case, `update_mask` is ignored.
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
   * fields to be overwritten in the Service resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Service, but do not actually update it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Service $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServices::class, 'Google_Service_FirebaseDataConnect_Resource_ProjectsLocationsServices');
