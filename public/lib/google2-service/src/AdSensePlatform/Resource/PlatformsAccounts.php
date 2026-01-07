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

namespace Google\Service\AdSensePlatform\Resource;

use Google\Service\AdSensePlatform\Account;
use Google\Service\AdSensePlatform\CloseAccountRequest;
use Google\Service\AdSensePlatform\CloseAccountResponse;
use Google\Service\AdSensePlatform\ListAccountsResponse;
use Google\Service\AdSensePlatform\LookupAccountResponse;

/**
 * The "accounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adsenseplatformService = new Google\Service\AdSensePlatform(...);
 *   $accounts = $adsenseplatformService->platforms_accounts;
 *  </code>
 */
class PlatformsAccounts extends \Google\Service\Resource
{
  /**
   * Closes a sub-account. (accounts.close)
   *
   * @param string $name Required. Account to close. Format:
   * platforms/{platform}/accounts/{account_id}
   * @param CloseAccountRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CloseAccountResponse
   * @throws \Google\Service\Exception
   */
  public function close($name, CloseAccountRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('close', [$params], CloseAccountResponse::class);
  }
  /**
   * Creates a sub-account. (accounts.create)
   *
   * @param string $parent Required. Platform to create an account for. Format:
   * platforms/{platform}
   * @param Account $postBody
   * @param array $optParams Optional parameters.
   * @return Account
   * @throws \Google\Service\Exception
   */
  public function create($parent, Account $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Account::class);
  }
  /**
   * Gets information about the selected sub-account. (accounts.get)
   *
   * @param string $name Required. Account to get information about. Format:
   * platforms/{platform}/accounts/{account_id}
   * @param array $optParams Optional parameters.
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
   * Lists a partial view of sub-accounts for a specific parent account.
   * (accounts.listPlatformsAccounts)
   *
   * @param string $parent Required. Platform who parents the accounts. Format:
   * platforms/{platform}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of accounts to include
   * in the response, used for paging. If unspecified, at most 10000 accounts will
   * be returned. The maximum value is 10000; values above 10000 will be coerced
   * to 10000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAccounts` call. Provide this to retrieve the subsequent page.
   * @return ListAccountsResponse
   * @throws \Google\Service\Exception
   */
  public function listPlatformsAccounts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccountsResponse::class);
  }
  /**
   * Looks up information about a sub-account for a specified creation_request_id.
   * If no account exists for the given creation_request_id, returns 404.
   * (accounts.lookup)
   *
   * @param string $parent Required. Platform who parents the account. Format:
   * platforms/{platform}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string creationRequestId Optional. The creation_request_id
   * provided when calling createAccount.
   * @return LookupAccountResponse
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], LookupAccountResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformsAccounts::class, 'Google_Service_AdSensePlatform_Resource_PlatformsAccounts');
