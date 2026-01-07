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

use Google\Service\ServiceNetworking\DnsRecordSet;

/**
 * The "dnsRecordSet" collection of methods.
 * Typical usage is:
 *  <code>
 *   $servicenetworkingService = new Google\Service\ServiceNetworking(...);
 *   $dnsRecordSet = $servicenetworkingService->services_dnsRecordSet;
 *  </code>
 */
class ServicesDnsRecordSet extends \Google\Service\Resource
{
  /**
   * Producers can use this method to retrieve information about the DNS record
   * set added to the private zone inside the shared tenant host project
   * associated with a consumer network. (dnsRecordSet.get)
   *
   * @param string $parent Required. Parent resource identifying the connection
   * which owns this collection of DNS zones in the format services/{service}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string consumerNetwork Required. The consumer network containing
   * the record set. Must be in the form of
   * projects/{project}/global/networks/{network}
   * @opt_param string domain Required. The domain name of the zone containing the
   * recordset.
   * @opt_param string type Required. RecordSet Type eg. type='A'. See the list of
   * [Supported DNS Types](https://dns.corp.google.com/docs/overview).
   * @opt_param string zone Required. The name of the zone containing the record
   * set.
   * @return DnsRecordSet
   */
  public function get($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DnsRecordSet::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServicesDnsRecordSet::class, 'Google_Service_ServiceNetworking_Resource_ServicesDnsRecordSet');
