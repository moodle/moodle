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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\DnsBindPermission;

/**
 * The "global" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $global = $vmwareengineService->projects_locations_global;
 *  </code>
 */
class ProjectsLocationsVmwareengineGlobal extends \Google\Service\Resource
{
  /**
   * Gets all the principals having bind permission on the intranet VPC associated
   * with the consumer project granted by the Grant API.
   * (global.getDnsBindPermission)
   *
   * @param string $name Required. The name of the resource which stores the
   * users/service accounts having the permission to bind to the corresponding
   * intranet VPC of the consumer project. DnsBindPermission is a global resource.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/dnsBindPermission`
   * @param array $optParams Optional parameters.
   * @return DnsBindPermission
   */
  public function getDnsBindPermission($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDnsBindPermission', [$params], DnsBindPermission::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVmwareengineGlobal::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsVmwareengineGlobal');
