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

namespace Google\Service\Css\Resource;

use Google\Service\Css\Account;
use Google\Service\Css\ListChildAccountsResponse;
use Google\Service\Css\UpdateAccountLabelsRequest;

/**
 * The "accounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $accounts = $cssService->accounts;
 *  </code>
 */
class Accounts extends \Google\Service\Resource
{
  /**
   * Retrieves a single CSS/MC account by ID. (accounts.get)
   *
   * @param string $name Required. The name of the managed CSS/MC account. Format:
   * accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string parent Optional. Only required when retrieving MC account
   * information. The CSS domain that is the parent resource of the MC account.
   * Format: accounts/{account}
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
   * Lists all the accounts under the specified CSS account ID, and optionally
   * filters by label ID and account name. (accounts.listChildAccounts)
   *
   * @param string $parent Required. The parent account. Must be a CSS group or
   * domain. Format: accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fullName If set, only the MC accounts with the given name
   * (case sensitive) will be returned.
   * @opt_param string labelId If set, only the MC accounts with the given label
   * ID will be returned.
   * @opt_param int pageSize Optional. The maximum number of accounts to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * accounts will be returned. The maximum value is 100; values above 100 will be
   * coerced to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListChildAccounts` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListChildAccounts` must match
   * the call that provided the page token.
   * @return ListChildAccountsResponse
   * @throws \Google\Service\Exception
   */
  public function listChildAccounts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listChildAccounts', [$params], ListChildAccountsResponse::class);
  }
  /**
   * Updates labels assigned to CSS/MC accounts by a CSS domain.
   * (accounts.updateLabels)
   *
   * @param string $name Required. The label resource name. Format:
   * accounts/{account}
   * @param UpdateAccountLabelsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Account
   * @throws \Google\Service\Exception
   */
  public function updateLabels($name, UpdateAccountLabelsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateLabels', [$params], Account::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accounts::class, 'Google_Service_Css_Resource_Accounts');
