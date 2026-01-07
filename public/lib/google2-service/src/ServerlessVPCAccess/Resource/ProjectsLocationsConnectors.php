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

namespace Google\Service\ServerlessVPCAccess\Resource;

use Google\Service\ServerlessVPCAccess\Connector;
use Google\Service\ServerlessVPCAccess\ListConnectorsResponse;
use Google\Service\ServerlessVPCAccess\Operation;

/**
 * The "connectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vpcaccessService = new Google\Service\ServerlessVPCAccess(...);
 *   $connectors = $vpcaccessService->projects_locations_connectors;
 *  </code>
 */
class ProjectsLocationsConnectors extends \Google\Service\Resource
{
  /**
   * Creates a Serverless VPC Access connector, returns an operation.
   * (connectors.create)
   *
   * @param string $parent Required. The project ID and location in which the
   * configuration should be created, specified in the format
   * `projects/locations`.
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectorId Required. The ID to use for this connector.
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
   * Deletes a Serverless VPC Access connector. Returns NOT_FOUND if the resource
   * does not exist. (connectors.delete)
   *
   * @param string $name Required. Name of a Serverless VPC Access connector to
   * delete.
   * @param array $optParams Optional parameters.
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
   * Gets a Serverless VPC Access connector. Returns NOT_FOUND if the resource
   * does not exist. (connectors.get)
   *
   * @param string $name Required. Name of a Serverless VPC Access connector to
   * get.
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
   * Lists Serverless VPC Access connectors.
   * (connectors.listProjectsLocationsConnectors)
   *
   * @param string $parent Required. The project and location from which the
   * routes should be listed.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of functions to return per call.
   * @opt_param string pageToken Continuation token.
   * @return ListConnectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConnectorsResponse::class);
  }
  /**
   * Updates a Serverless VPC Access connector, returns an operation.
   * (connectors.patch)
   *
   * @param string $name The resource name in the format
   * `projects/locations/connectors`.
   * @param Connector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The fields to update on the entry group. If
   * absent or empty, all modifiable fields are updated.
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
class_alias(ProjectsLocationsConnectors::class, 'Google_Service_ServerlessVPCAccess_Resource_ProjectsLocationsConnectors');
