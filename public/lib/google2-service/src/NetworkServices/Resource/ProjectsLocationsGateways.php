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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\Gateway;
use Google\Service\NetworkServices\ListGatewaysResponse;
use Google\Service\NetworkServices\Operation;

/**
 * The "gateways" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $gateways = $networkservicesService->projects_locations_gateways;
 *  </code>
 */
class ProjectsLocationsGateways extends \Google\Service\Resource
{
  /**
   * Creates a new Gateway in a given project and location. (gateways.create)
   *
   * @param string $parent Required. The parent resource of the Gateway. Must be
   * in the format `projects/locations`.
   * @param Gateway $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string gatewayId Required. Short name of the Gateway resource to
   * be created.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Gateway $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Gateway. (gateways.delete)
   *
   * @param string $name Required. A name of the Gateway to delete. Must be in the
   * format `projects/locations/gateways`.
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
   * Gets details of a single Gateway. (gateways.get)
   *
   * @param string $name Required. A name of the Gateway to get. Must be in the
   * format `projects/locations/gateways`.
   * @param array $optParams Optional parameters.
   * @return Gateway
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Gateway::class);
  }
  /**
   * Lists Gateways in a given project and location.
   * (gateways.listProjectsLocationsGateways)
   *
   * @param string $parent Required. The project and location from which the
   * Gateways should be listed, specified in the format `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of Gateways to return per call.
   * @opt_param string pageToken The value returned by the last
   * `ListGatewaysResponse` Indicates that this is a continuation of a prior
   * `ListGateways` call, and that the system should return the next page of data.
   * @return ListGatewaysResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGateways($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGatewaysResponse::class);
  }
  /**
   * Updates the parameters of a single Gateway. (gateways.patch)
   *
   * @param string $name Identifier. Name of the Gateway resource. It matches
   * pattern `projects/locations/gateways/`.
   * @param Gateway $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Gateway resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Gateway $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGateways::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsGateways');
