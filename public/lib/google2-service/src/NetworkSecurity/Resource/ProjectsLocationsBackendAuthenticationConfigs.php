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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\BackendAuthenticationConfig;
use Google\Service\NetworkSecurity\ListBackendAuthenticationConfigsResponse;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "backendAuthenticationConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $backendAuthenticationConfigs = $networksecurityService->projects_locations_backendAuthenticationConfigs;
 *  </code>
 */
class ProjectsLocationsBackendAuthenticationConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new BackendAuthenticationConfig in a given project and location.
   * (backendAuthenticationConfigs.create)
   *
   * @param string $parent Required. The parent resource of the
   * BackendAuthenticationConfig. Must be in the format
   * `projects/locations/{location}`.
   * @param BackendAuthenticationConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backendAuthenticationConfigId Required. Short name of the
   * BackendAuthenticationConfig resource to be created. This value should be 1-63
   * characters long, containing only letters, numbers, hyphens, and underscores,
   * and should not start with a number. E.g. "backend-auth-config".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackendAuthenticationConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single BackendAuthenticationConfig to BackendAuthenticationConfig.
   * (backendAuthenticationConfigs.delete)
   *
   * @param string $name Required. A name of the BackendAuthenticationConfig to
   * delete. Must be in the format
   * `projects/locations/{location}/backendAuthenticationConfigs`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. Etag of the resource. If this is provided,
   * it must match the server's etag.
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
   * Gets details of a single BackendAuthenticationConfig to
   * BackendAuthenticationConfig. (backendAuthenticationConfigs.get)
   *
   * @param string $name Required. A name of the BackendAuthenticationConfig to
   * get. Must be in the format
   * `projects/locations/{location}/backendAuthenticationConfigs`.
   * @param array $optParams Optional parameters.
   * @return BackendAuthenticationConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackendAuthenticationConfig::class);
  }
  /**
   * Lists BackendAuthenticationConfigs in a given project and location. (backendA
   * uthenticationConfigs.listProjectsLocationsBackendAuthenticationConfigs)
   *
   * @param string $parent Required. The project and location from which the
   * BackendAuthenticationConfigs should be listed, specified in the format
   * `projects/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of BackendAuthenticationConfigs to
   * return per call.
   * @opt_param string pageToken The value returned by the last
   * `ListBackendAuthenticationConfigsResponse` Indicates that this is a
   * continuation of a prior `ListBackendAuthenticationConfigs` call, and that the
   * system should return the next page of data.
   * @return ListBackendAuthenticationConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackendAuthenticationConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackendAuthenticationConfigsResponse::class);
  }
  /**
   * Updates the parameters of a single BackendAuthenticationConfig to
   * BackendAuthenticationConfig. (backendAuthenticationConfigs.patch)
   *
   * @param string $name Required. Name of the BackendAuthenticationConfig
   * resource. It matches the pattern `projects/locations/{location}/backendAuthen
   * ticationConfigs/{backend_authentication_config}`
   * @param BackendAuthenticationConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the BackendAuthenticationConfig resource by the
   * update. The fields specified in the update_mask are relative to the resource,
   * not the full request. A field will be overwritten if it is in the mask. If
   * the user does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackendAuthenticationConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackendAuthenticationConfigs::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsBackendAuthenticationConfigs');
