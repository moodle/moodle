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

namespace Google\Service\CloudCommercePartnerProcurementService\Resource;

use Google\Service\CloudCommercePartnerProcurementService\Account;
use Google\Service\CloudCommercePartnerProcurementService\ApproveAccountRequest;
use Google\Service\CloudCommercePartnerProcurementService\CloudcommerceprocurementEmpty;
use Google\Service\CloudCommercePartnerProcurementService\ListAccountsResponse;
use Google\Service\CloudCommercePartnerProcurementService\RejectAccountRequest;
use Google\Service\CloudCommercePartnerProcurementService\ResetAccountRequest;

/**
 * The "accounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcommerceprocurementService = new Google\Service\CloudCommercePartnerProcurementService(...);
 *   $accounts = $cloudcommerceprocurementService->providers_accounts;
 *  </code>
 */
class ProvidersAccounts extends \Google\Service\Resource
{
  /**
   * Grants an approval on an Account. (accounts.approve)
   *
   * @param string $name Required. The resource name of the account, with the
   * format `providers/{providerId}/accounts/{accountId}`.
   * @param ApproveAccountRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function approve($name, ApproveAccountRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('approve', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Gets a requested Account resource. (accounts.get)
   *
   * @param string $name Required. The name of the account to retrieve.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. What information to include in the response.
   * @return Account
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Account::class);
  }
  /**
   * Lists Accounts that the provider has access to.
   * (accounts.listProvidersAccounts)
   *
   * @param string $parent Required. The parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of entries that are requested. The
   * default page size is 25 and the maximum page size is 200.
   * @opt_param string pageToken The token for fetching the next page.
   * @return ListAccountsResponse
   * @throws \Google\Service\Exception
   */
  public function listProvidersAccounts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccountsResponse::class);
  }
  /**
   * Rejects an approval on an Account. (accounts.reject)
   *
   * @param string $name Required. The resource name of the account.
   * @param RejectAccountRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function reject($name, RejectAccountRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reject', [$params], CloudcommerceprocurementEmpty::class);
  }
  /**
   * Resets an Account and cancels all associated Entitlements. Partner can only
   * reset accounts they own rather than customer accounts. (accounts.reset)
   *
   * @param string $name Required. The resource name of the account.
   * @param ResetAccountRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloudcommerceprocurementEmpty
   * @throws \Google\Service\Exception
   */
  public function reset($name, ResetAccountRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reset', [$params], CloudcommerceprocurementEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvidersAccounts::class, 'Google_Service_CloudCommercePartnerProcurementService_Resource_ProvidersAccounts');
