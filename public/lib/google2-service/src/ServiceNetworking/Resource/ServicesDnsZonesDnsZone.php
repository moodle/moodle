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

use Google\Service\ServiceNetworking\GetDnsZoneResponse;

/**
 * The "dnsZone" collection of methods.
 * Typical usage is:
 *  <code>
 *   $servicenetworkingService = new Google\Service\ServiceNetworking(...);
 *   $dnsZone = $servicenetworkingService->services_dnsZones_dnsZone;
 *  </code>
 */
class ServicesDnsZonesDnsZone extends \Google\Service\Resource
{
  /**
   * Service producers can use this method to retrieve a DNS zone in the shared
   * producer host project and the matching peering zones in consumer project
   * (dnsZone.get)
   *
   * @param string $name Required. The network that the consumer is using to
   * connect with services. Must be in the form of services/{service}/projects/{pr
   * oject}/global/networks/{network}/zones/{zoneName} Where {service} is the
   * peering service that is managing connectivity for the service producer's
   * organization. For Google services that support this {project} is the project
   * number, as in '12345' {network} is the network name. {zoneName} is the DNS
   * zone name
   * @param array $optParams Optional parameters.
   * @return GetDnsZoneResponse
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GetDnsZoneResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServicesDnsZonesDnsZone::class, 'Google_Service_ServiceNetworking_Resource_ServicesDnsZonesDnsZone');
