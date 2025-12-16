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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1DnsZone;
use Google\Service\Apigee\GoogleCloudApigeeV1ListDnsZonesResponse;
use Google\Service\Apigee\GoogleLongrunningOperation;

/**
 * The "dnsZones" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $dnsZones = $apigeeService->organizations_dnsZones;
 *  </code>
 */
class OrganizationsDnsZones extends \Google\Service\Resource
{
  /**
   * Creates a new DNS zone. (dnsZones.create)
   *
   * @param string $parent Required. Organization where the DNS zone will be
   * created.
   * @param GoogleCloudApigeeV1DnsZone $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dnsZoneId Required. User assigned ID for this resource.
   * Must be unique within the organization. The name must be 1-63 characters
   * long, must begin with a letter, end with a letter or digit, and only contain
   * lowercase letters, digits or dashes.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1DnsZone $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a previously created DNS zone. (dnsZones.delete)
   *
   * @param string $name Required. Name of the DNS zone to delete. Use the
   * following structure in your request:
   * `organizations/{org}/dnsZones/{dns_zone}`.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Fetches the representation of an existing DNS zone. (dnsZones.get)
   *
   * @param string $name Required. Name of the DNS zone to fetch. Use the
   * following structure in your request:
   * `organizations/{org}/dnsZones/{dns_zone}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1DnsZone
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1DnsZone::class);
  }
  /**
   * Enumerates DNS zones that have been created but not yet deleted.
   * (dnsZones.listOrganizationsDnsZones)
   *
   * @param string $parent Required. Name of the organization for which to list
   * the DNS zones. Use the following structure in your request:
   * `organizations/{org}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of DNS zones to return. If
   * unspecified, at most 25 DNS zones will be returned.
   * @opt_param string pageToken Optional. Page token, returned from a previous
   * `ListDnsZones` call, that you can use to retrieve the next page.
   * @return GoogleCloudApigeeV1ListDnsZonesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsDnsZones($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListDnsZonesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsDnsZones::class, 'Google_Service_Apigee_Resource_OrganizationsDnsZones');
