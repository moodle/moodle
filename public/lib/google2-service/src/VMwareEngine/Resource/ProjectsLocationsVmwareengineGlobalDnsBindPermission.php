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

use Google\Service\VMwareEngine\GrantDnsBindPermissionRequest;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\RevokeDnsBindPermissionRequest;

/**
 * The "dnsBindPermission" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $dnsBindPermission = $vmwareengineService->projects_locations_global_dnsBindPermission;
 *  </code>
 */
class ProjectsLocationsVmwareengineGlobalDnsBindPermission extends \Google\Service\Resource
{
  /**
   * Grants the bind permission to the customer provided principal(user / service
   * account) to bind their DNS zone with the intranet VPC associated with the
   * project. (dnsBindPermission.grant)
   *
   * @param string $name Required. The name of the resource which stores the
   * users/service accounts having the permission to bind to the corresponding
   * intranet VPC of the consumer project. DnsBindPermission is a global resource.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/dnsBindPermission`
   * @param GrantDnsBindPermissionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   */
  public function grant($name, GrantDnsBindPermissionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('grant', [$params], Operation::class);
  }
  /**
   * Revokes the bind permission from the customer provided principal(user /
   * service account) on the intranet VPC associated with the consumer project.
   * (dnsBindPermission.revoke)
   *
   * @param string $name Required. The name of the resource which stores the
   * users/service accounts having the permission to bind to the corresponding
   * intranet VPC of the consumer project. DnsBindPermission is a global resource.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/dnsBindPermission`
   * @param RevokeDnsBindPermissionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   */
  public function revoke($name, RevokeDnsBindPermissionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('revoke', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVmwareengineGlobalDnsBindPermission::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsVmwareengineGlobalDnsBindPermission');
