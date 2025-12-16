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

namespace Google\Service\ServiceNetworking\Resource;

use Google\Service\ServiceNetworking\ListDnsRecordSetsResponse;

/**
 * The "dnsRecordSet" collection of methods.
 * Typical usage is:
 *  <code>
 *   $servicenetworkingService = new Google\Service\ServiceNetworking(...);
 *   $dnsRecordSet = $servicenetworkingService->services_projects_global_networks_zones_dnsRecordSet;
 *  </code>
 */
class ServicesProjectsServicenetworkingGlobalNetworksZonesDnsRecordSet extends \Google\Service\Resource
{
  /**
   * Producers can use this method to retrieve a list of available DNS RecordSets
   * available inside the private zone on the tenant host project accessible from
   * their network. (dnsRecordSet.lISTServicesProjectsServicenetworkingGlobalNetwo
   * rksZonesDnsRecordSet)
   *
   * @param string $parent Required. The service that is managing peering
   * connectivity for a service producer's organization. For Google services that
   * support this functionality, this value is
   * `services/servicenetworking.googleapis.com`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string consumerNetwork Required. The network that the consumer is
   * using to connect with services. Must be in the form of
   * projects/{project}/global/networks/{network} {project} is the project number,
   * as in '12345' {network} is the network name.
   * @opt_param string zone Required. The name of the private DNS zone in the
   * shared producer host project from which the record set will be removed.
   * @return ListDnsRecordSetsResponse
   */
  public function lISTServicesProjectsServicenetworkingGlobalNetworksZonesDnsRecordSet($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('lIST', [$params], ListDnsRecordSetsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServicesProjectsServicenetworkingGlobalNetworksZonesDnsRecordSet::class, 'Google_Service_ServiceNetworking_Resource_ServicesProjectsServicenetworkingGlobalNetworksZonesDnsRecordSet');
