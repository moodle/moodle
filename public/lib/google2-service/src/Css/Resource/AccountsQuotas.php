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

use Google\Service\Css\ListQuotaGroupsResponse;

/**
 * The "quotas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $quotas = $cssService->accounts_quotas;
 *  </code>
 */
class AccountsQuotas extends \Google\Service\Resource
{
  /**
   * Lists the daily call quota and usage per group for your CSS Center account.
   * (quotas.listAccountsQuotas)
   *
   * @param string $parent Required. The CSS account that owns the collection of
   * method quotas and resources. In most cases, this is the CSS domain. Format:
   * accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of quotas to return in
   * the response, used for paging. Defaults to 500; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. Token (if provided) to retrieve the
   * subsequent page. All other parameters must match the original call that
   * provided the page token.
   * @return ListQuotaGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsQuotas($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListQuotaGroupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsQuotas::class, 'Google_Service_Css_Resource_AccountsQuotas');
