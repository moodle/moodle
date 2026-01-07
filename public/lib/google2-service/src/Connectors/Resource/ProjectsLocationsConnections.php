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

namespace Google\Service\Connectors\Resource;

use Google\Service\Connectors\CheckReadinessResponse;
use Google\Service\Connectors\CheckStatusResponse;
use Google\Service\Connectors\ExchangeAuthCodeRequest;
use Google\Service\Connectors\ExchangeAuthCodeResponse;
use Google\Service\Connectors\ExecuteSqlQueryRequest;
use Google\Service\Connectors\ExecuteSqlQueryResponse;
use Google\Service\Connectors\RefreshAccessTokenRequest;
use Google\Service\Connectors\RefreshAccessTokenResponse;

/**
 * The "connections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $connectorsService = new Google\Service\Connectors(...);
 *   $connections = $connectorsService->projects_locations_connections;
 *  </code>
 */
class ProjectsLocationsConnections extends \Google\Service\Resource
{
  /**
   * Reports readiness status of the connector. Similar logic to GetStatus but
   * modified for kubernetes health check to understand.
   * (connections.checkReadiness)
   *
   * @param string $name
   * @param array $optParams Optional parameters.
   * @return CheckReadinessResponse
   * @throws \Google\Service\Exception
   */
  public function checkReadiness($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('checkReadiness', [$params], CheckReadinessResponse::class);
  }
  /**
   * Reports the status of the connection. Note that when the connection is in a
   * state that is not ACTIVE, the implementation of this RPC method must return a
   * Status with the corresponding State instead of returning a gRPC status code
   * that is not "OK", which indicates that ConnectionStatus itself, not the
   * connection, failed. (connections.checkStatus)
   *
   * @param string $name
   * @param array $optParams Optional parameters.
   * @return CheckStatusResponse
   * @throws \Google\Service\Exception
   */
  public function checkStatus($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('checkStatus', [$params], CheckStatusResponse::class);
  }
  /**
   * ExchangeAuthCode exchanges the OAuth authorization code (and other necessary
   * data) for an access token (and associated credentials).
   * (connections.exchangeAuthCode)
   *
   * @param string $name
   * @param ExchangeAuthCodeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExchangeAuthCodeResponse
   * @throws \Google\Service\Exception
   */
  public function exchangeAuthCode($name, ExchangeAuthCodeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exchangeAuthCode', [$params], ExchangeAuthCodeResponse::class);
  }
  /**
   * Executes a SQL statement specified in the body of the request. An example of
   * this SQL statement in the case of Salesforce connector would be 'select *
   * from Account a, Order o where a.Id = o.AccountId'.
   * (connections.executeSqlQuery)
   *
   * @param string $connection Required. Resource name of the Connection. Format:
   * projects/{project}/locations/{location}/connections/{connection}
   * @param ExecuteSqlQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExecuteSqlQueryResponse
   * @throws \Google\Service\Exception
   */
  public function executeSqlQuery($connection, ExecuteSqlQueryRequest $postBody, $optParams = [])
  {
    $params = ['connection' => $connection, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeSqlQuery', [$params], ExecuteSqlQueryResponse::class);
  }
  /**
   * RefreshAccessToken exchanges the OAuth refresh token (and other necessary
   * data) for a new access token (and new associated credentials).
   * (connections.refreshAccessToken)
   *
   * @param string $name
   * @param RefreshAccessTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RefreshAccessTokenResponse
   * @throws \Google\Service\Exception
   */
  public function refreshAccessToken($name, RefreshAccessTokenRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('refreshAccessToken', [$params], RefreshAccessTokenResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnections::class, 'Google_Service_Connectors_Resource_ProjectsLocationsConnections');
