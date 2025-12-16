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

namespace Google\Service\CloudIdentity\Resource;

use Google\Service\CloudIdentity\ListPoliciesResponse;
use Google\Service\CloudIdentity\Policy;

/**
 * The "policies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudidentityService = new Google\Service\CloudIdentity(...);
 *   $policies = $cloudidentityService->policies;
 *  </code>
 */
class Policies extends \Google\Service\Resource
{
  /**
   * Get a policy. (policies.get)
   *
   * @param string $name Required. The name of the policy to retrieve. Format:
   * `policies/{policy}`.
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Policy::class);
  }
  /**
   * List policies. (policies.listPolicies)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A CEL expression for filtering the
   * results. Policies can be filtered by application with this expression:
   * setting.type.matches('^settings/gmail\\..*$') Policies can be filtered by
   * setting type with this expression:
   * setting.type.matches('^.*\\.service_status$') A maximum of one of the above
   * setting.type clauses can be used. Policies can be filtered by customer with
   * this expression: customer == "customers/{customer}" Where `customer` is the
   * `id` from the [Admin SDK `Customer`
   * resource](https://developers.google.com/admin-
   * sdk/directory/reference/rest/v1/customers). You may use
   * `customers/my_customer` to specify your own organization. When no customer is
   * mentioned it will be default to customers/my_customer. A maximum of one
   * customer clause can be used. The above clauses can only be combined together
   * in a single filter expression with the `&&` operator.
   * @opt_param int pageSize Optional. The maximum number of results to return.
   * The service can return fewer than this number. If omitted or set to 0, the
   * default is 50 results per page. The maximum allowed value is 100. `page_size`
   * values greater than 100 default to 100.
   * @opt_param string pageToken Optional. The pagination token received from a
   * prior call to PoliciesService.ListPolicies to retrieve the next page of
   * results. When paginating, all other parameters provided to
   * `ListPoliciesRequest` must match the call that provided the page token.
   * @return ListPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listPolicies($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPoliciesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policies::class, 'Google_Service_CloudIdentity_Resource_Policies');
