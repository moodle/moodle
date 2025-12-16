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

namespace Google\Service\AccessContextManager\Resource;

use Google\Service\AccessContextManager\ListSupportedServicesResponse;
use Google\Service\AccessContextManager\SupportedService;

/**
 * The "services" collection of methods.
 * Typical usage is:
 *  <code>
 *   $accesscontextmanagerService = new Google\Service\AccessContextManager(...);
 *   $services = $accesscontextmanagerService->services;
 *  </code>
 */
class Services extends \Google\Service\Resource
{
  /**
   * Returns a VPC-SC supported service based on the service name. (services.get)
   *
   * @param string $name The name of the service to get information about. The
   * names must be in the same format as used in defining a service perimeter, for
   * example, `storage.googleapis.com`.
   * @param array $optParams Optional parameters.
   * @return SupportedService
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SupportedService::class);
  }
  /**
   * Lists all VPC-SC supported services. (services.listServices)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize This flag specifies the maximum number of services to
   * return per page. Default is 100.
   * @opt_param string pageToken Token to start on a later page. Default is the
   * first page.
   * @return ListSupportedServicesResponse
   * @throws \Google\Service\Exception
   */
  public function listServices($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSupportedServicesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Services::class, 'Google_Service_AccessContextManager_Resource_Services');
