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

namespace Google\Service\CloudControlsPartnerService\Resource;

use Google\Service\CloudControlsPartnerService\CloudcontrolspartnerEmpty;
use Google\Service\CloudControlsPartnerService\Customer;
use Google\Service\CloudControlsPartnerService\ListCustomersResponse;

/**
 * The "customers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcontrolspartnerService = new Google\Service\CloudControlsPartnerService(...);
 *   $customers = $cloudcontrolspartnerService->organizations_locations_customers;
 *  </code>
 */
class OrganizationsLocationsCustomers extends \Google\Service\Resource
{
  /**
   * Creates a new customer. (customers.create)
   *
   * @param string $parent Required. Parent resource Format:
   * `organizations/{organization}/locations/{location}`
   * @param Customer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customerId Required. The customer id to use for the
   * customer, which will become the final component of the customer's resource
   * name. The specified value must be a valid Google cloud organization id.
   * @return Customer
   * @throws \Google\Service\Exception
   */
  public function create($parent, Customer $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Customer::class);
  }
  /**
   * Delete details of a single customer (customers.delete)
   *
   * @param string $name Required. name of the resource to be deleted format:
   * name=organizations/locations/customers
   * @param array $optParams Optional parameters.
   * @return CloudcontrolspartnerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], CloudcontrolspartnerEmpty::class);
  }
  /**
   * Gets details of a single customer (customers.get)
   *
   * @param string $name Required. Format:
   * `organizations/{organization}/locations/{location}/customers/{customer}`
   * @param array $optParams Optional parameters.
   * @return Customer
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Customer::class);
  }
  /**
   * Lists customers of a partner identified by its Google Cloud organization ID
   * (customers.listOrganizationsLocationsCustomers)
   *
   * @param string $parent Required. Parent resource Format:
   * `organizations/{organization}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize The maximum number of Customers to return. The
   * service may return fewer than this value. If unspecified, at most 500
   * Customers will be returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListCustomers` call. Provide this to retrieve the subsequent page.
   * @return ListCustomersResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsCustomers($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCustomersResponse::class);
  }
  /**
   * Update details of a single customer (customers.patch)
   *
   * @param string $name Identifier. Format:
   * `organizations/{organization}/locations/{location}/customers/{customer}`
   * @param Customer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update
   * @return Customer
   * @throws \Google\Service\Exception
   */
  public function patch($name, Customer $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Customer::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsCustomers::class, 'Google_Service_CloudControlsPartnerService_Resource_OrganizationsLocationsCustomers');
