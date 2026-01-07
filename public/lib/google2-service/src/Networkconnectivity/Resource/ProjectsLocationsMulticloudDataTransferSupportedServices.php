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

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\ListMulticloudDataTransferSupportedServicesResponse;
use Google\Service\Networkconnectivity\MulticloudDataTransferSupportedService;

/**
 * The "multicloudDataTransferSupportedServices" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $multicloudDataTransferSupportedServices = $networkconnectivityService->projects_locations_multicloudDataTransferSupportedServices;
 *  </code>
 */
class ProjectsLocationsMulticloudDataTransferSupportedServices extends \Google\Service\Resource
{
  /**
   * Gets the details of a service that is supported for Data Transfer Essentials.
   * (multicloudDataTransferSupportedServices.get)
   *
   * @param string $name Required. The name of the service.
   * @param array $optParams Optional parameters.
   * @return MulticloudDataTransferSupportedService
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MulticloudDataTransferSupportedService::class);
  }
  /**
   * Lists the services in the project for a region that are supported for Data
   * Transfer Essentials. (multicloudDataTransferSupportedServices.listProjectsLoc
   * ationsMulticloudDataTransferSupportedServices)
   *
   * @param string $parent Required. The name of the parent resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results listed per
   * page.
   * @opt_param string pageToken Optional. The page token.
   * @return ListMulticloudDataTransferSupportedServicesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMulticloudDataTransferSupportedServices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMulticloudDataTransferSupportedServicesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMulticloudDataTransferSupportedServices::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsMulticloudDataTransferSupportedServices');
