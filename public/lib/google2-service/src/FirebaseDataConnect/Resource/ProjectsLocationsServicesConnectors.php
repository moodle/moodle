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

use Google\Service\FirebaseDataConnect\Connector;
use Google\Service\FirebaseDataConnect\ExecuteMutationRequest;
use Google\Service\FirebaseDataConnect\ExecuteMutationResponse;
use Google\Service\FirebaseDataConnect\ExecuteQueryRequest;
use Google\Service\FirebaseDataConnect\ExecuteQueryResponse;
use Google\Service\FirebaseDataConnect\GraphqlResponse;
use Google\Service\FirebaseDataConnect\ImpersonateRequest;
use Google\Service\FirebaseDataConnect\ListConnectorsResponse;
use Google\Service\FirebaseDataConnect\Operation;

/**
 * The "connectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebasedataconnectService = new Google\Service\FirebaseDataConnect(...);
 *   $connectors = $firebasedataconnectService->projects_locations_services_connectors;
 *  </code>
 */
class ProjectsLocationsServicesConnectors extends \Google\Service\Resource
{
  /**
   * Creates a new Connector in a given project and location. The operations are
   * validated against and must be compatible with the active schema. If the
   * operations and schema are not compatible or if the schema is not present,
   * this will result in an error. (connectors.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectorId Required. The ID to use for the connector,
   * which will become the final component of the connector's resource name.
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
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Connector, but do not actually create it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Connector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Connector. (connectors.delete)
   *
   * @param string $name Required. The name of the connector to delete, in the
   * format: ``` projects/{project}/locations/{location}/services/{service}/connec
   * tors/{connector} ```
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Connector is not
   * found, the request will succeed but no action will be taken on the server.
   * @opt_param string etag Optional. The etag of the Connector. If this is
   * provided, it must match the server's etag.
   * @opt_param bool force Optional. If set to true, any child resources (i.e.
   * ConnectorRevisions) will also be deleted. Otherwise, the request will only
   * work if the Connector has no child resources.
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
   * preview the Connector, but do not actually delete it.
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
   * Execute a predefined mutation in a Connector. (connectors.executeMutation)
   *
   * @param string $name Required. The resource name of the connector to find the
   * predefined mutation, in the format: ``` projects/{project}/locations/{locatio
   * n}/services/{service}/connectors/{connector} ```
   * @param ExecuteMutationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExecuteMutationResponse
   * @throws \Google\Service\Exception
   */
  public function executeMutation($name, ExecuteMutationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeMutation', [$params], ExecuteMutationResponse::class);
  }
  /**
   * Execute a predefined query in a Connector. (connectors.executeQuery)
   *
   * @param string $name Required. The resource name of the connector to find the
   * predefined query, in the format: ``` projects/{project}/locations/{location}/
   * services/{service}/connectors/{connector} ```
   * @param ExecuteQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExecuteQueryResponse
   * @throws \Google\Service\Exception
   */
  public function executeQuery($name, ExecuteQueryRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeQuery', [$params], ExecuteQueryResponse::class);
  }
  /**
   * Gets details of a single Connector. (connectors.get)
   *
   * @param string $name Required. The name of the connector to retrieve, in the
   * format: ``` projects/{project}/locations/{location}/services/{service}/connec
   * tors/{connector} ```
   * @param array $optParams Optional parameters.
   * @return Connector
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Connector::class);
  }
  /**
   * Impersonate a mutation defined on a Firebase Data Connect connector. It
   * grants the admin SDK access to mutations defined in the given connector. The
   * caller can choose to impersonate a particular Firebase Auth user, or skip
   * @auth completely. (connectors.impersonateMutation)
   *
   * @param string $name Required. The resource name of the connector to find the
   * predefined query/mutation, in the format: ``` projects/{project}/locations/{l
   * ocation}/services/{service}/connectors/{connector} ```
   * @param ImpersonateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GraphqlResponse
   * @throws \Google\Service\Exception
   */
  public function impersonateMutation($name, ImpersonateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('impersonateMutation', [$params], GraphqlResponse::class);
  }
  /**
   * Impersonate a query defined on a Firebase Data Connect connector. It grants
   * the admin SDK access to queries defined in the given connector. The caller
   * can choose to impersonate a particular Firebase Auth user, or skip @auth
   * completely. (connectors.impersonateQuery)
   *
   * @param string $name Required. The resource name of the connector to find the
   * predefined query/mutation, in the format: ``` projects/{project}/locations/{l
   * ocation}/services/{service}/connectors/{connector} ```
   * @param ImpersonateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GraphqlResponse
   * @throws \Google\Service\Exception
   */
  public function impersonateQuery($name, ImpersonateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('impersonateQuery', [$params], GraphqlResponse::class);
  }
  /**
   * Lists Connectors in a given project and location.
   * (connectors.listProjectsLocationsServicesConnectors)
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
   * `ListConnectors` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListConnectors` must match the
   * call that provided the page token.
   * @return ListConnectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServicesConnectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConnectorsResponse::class);
  }
  /**
   * Updates the parameters of a single Connector, and creates a new
   * ConnectorRevision with the updated Connector. The operations are validated
   * against and must be compatible with the live schema. If the operations and
   * schema are not compatible or if the schema is not present, this will result
   * in an error. (connectors.patch)
   *
   * @param string $name Identifier. The relative resource name of the connector,
   * in the format: ``` projects/{project}/locations/{location}/services/{service}
   * /connectors/{connector} ```
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the Connector is not
   * found, a new Connector will be created. In this case, `update_mask` is
   * ignored.
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
   * fields to be overwritten in the Connector resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the Connector, but do not actually update it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Connector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServicesConnectors::class, 'Google_Service_FirebaseDataConnect_Resource_ProjectsLocationsServicesConnectors');
