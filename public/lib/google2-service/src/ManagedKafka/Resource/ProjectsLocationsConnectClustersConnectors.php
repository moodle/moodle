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

namespace Google\Service\ManagedKafka\Resource;

use Google\Service\ManagedKafka\Connector;
use Google\Service\ManagedKafka\ListConnectorsResponse;
use Google\Service\ManagedKafka\ManagedkafkaEmpty;
use Google\Service\ManagedKafka\PauseConnectorRequest;
use Google\Service\ManagedKafka\PauseConnectorResponse;
use Google\Service\ManagedKafka\RestartConnectorRequest;
use Google\Service\ManagedKafka\RestartConnectorResponse;
use Google\Service\ManagedKafka\ResumeConnectorRequest;
use Google\Service\ManagedKafka\ResumeConnectorResponse;
use Google\Service\ManagedKafka\StopConnectorRequest;
use Google\Service\ManagedKafka\StopConnectorResponse;

/**
 * The "connectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $connectors = $managedkafkaService->projects_locations_connectClusters_connectors;
 *  </code>
 */
class ProjectsLocationsConnectClustersConnectors extends \Google\Service\Resource
{
  /**
   * Creates a new connector in a given Connect cluster. (connectors.create)
   *
   * @param string $parent Required. The parent Connect cluster in which to create
   * the connector. Structured like `projects/{project}/locations/{location}/conne
   * ctClusters/{connect_cluster_id}`.
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectorId Required. The ID to use for the connector,
   * which will become the final component of the connector's name. The ID must be
   * 1-63 characters long, and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` to comply with RFC 1035. This value is
   * structured like: `my-connector-id`.
   * @return Connector
   * @throws \Google\Service\Exception
   */
  public function create($parent, Connector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Connector::class);
  }
  /**
   * Deletes a connector. (connectors.delete)
   *
   * @param string $name Required. The name of the connector to delete. Structured
   * like: projects/{project}/locations/{location}/connectClusters/{connectCluster
   * }/connectors/{connector}
   * @param array $optParams Optional parameters.
   * @return ManagedkafkaEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ManagedkafkaEmpty::class);
  }
  /**
   * Returns the properties of a single connector. (connectors.get)
   *
   * @param string $name Required. The name of the connector whose configuration
   * to return. Structured like: projects/{project}/locations/{location}/connectCl
   * usters/{connectCluster}/connectors/{connector}
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
   * Lists the connectors in a given Connect cluster.
   * (connectors.listProjectsLocationsConnectClustersConnectors)
   *
   * @param string $parent Required. The parent Connect cluster whose connectors
   * are to be listed. Structured like `projects/{project}/locations/{location}/co
   * nnectClusters/{connect_cluster_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of connectors to return.
   * The service may return fewer than this value. If unspecified, server will
   * pick an appropriate default.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListConnectors` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListConnectors` must match the
   * call that provided the page token.
   * @return ListConnectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectClustersConnectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConnectorsResponse::class);
  }
  /**
   * Updates the properties of a connector. (connectors.patch)
   *
   * @param string $name Identifier. The name of the connector. Structured like: p
   * rojects/{project}/locations/{location}/connectClusters/{connect_cluster}/conn
   * ectors/{connector}
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the cluster resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The mask is
   * required and a value of * will update all fields.
   * @return Connector
   * @throws \Google\Service\Exception
   */
  public function patch($name, Connector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Connector::class);
  }
  /**
   * Pauses the connector and its tasks. (connectors.pause)
   *
   * @param string $name Required. The name of the connector to pause. Structured
   * like: projects/{project}/locations/{location}/connectClusters/{connectCluster
   * }/connectors/{connector}
   * @param PauseConnectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return PauseConnectorResponse
   * @throws \Google\Service\Exception
   */
  public function pause($name, PauseConnectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], PauseConnectorResponse::class);
  }
  /**
   * Restarts the connector. (connectors.restart)
   *
   * @param string $name Required. The name of the connector to restart.
   * Structured like: projects/{project}/locations/{location}/connectClusters/{con
   * nectCluster}/connectors/{connector}
   * @param RestartConnectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RestartConnectorResponse
   * @throws \Google\Service\Exception
   */
  public function restart($name, RestartConnectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], RestartConnectorResponse::class);
  }
  /**
   * Resumes the connector and its tasks. (connectors.resume)
   *
   * @param string $name Required. The name of the connector to pause. Structured
   * like: projects/{project}/locations/{location}/connectClusters/{connectCluster
   * }/connectors/{connector}
   * @param ResumeConnectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ResumeConnectorResponse
   * @throws \Google\Service\Exception
   */
  public function resume($name, ResumeConnectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], ResumeConnectorResponse::class);
  }
  /**
   * Stops the connector. (connectors.stop)
   *
   * @param string $name Required. The name of the connector to stop. Structured
   * like: projects/{project}/locations/{location}/connectClusters/{connectCluster
   * }/connectors/{connector}
   * @param StopConnectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return StopConnectorResponse
   * @throws \Google\Service\Exception
   */
  public function stop($name, StopConnectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], StopConnectorResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectClustersConnectors::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsConnectClustersConnectors');
