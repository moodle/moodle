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

namespace Google\Service\Adsense\Resource;

use Google\Service\Adsense\ListPolicyIssuesResponse;
use Google\Service\Adsense\PolicyIssue;

/**
 * The "policyIssues" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adsenseService = new Google\Service\Adsense(...);
 *   $policyIssues = $adsenseService->accounts_policyIssues;
 *  </code>
 */
class AccountsPolicyIssues extends \Google\Service\Resource
{
  /**
   * Gets information about the selected policy issue. (policyIssues.get)
   *
   * @param string $name Required. Name of the policy issue. Format:
   * accounts/{account}/policyIssues/{policy_issue}
   * @param array $optParams Optional parameters.
   * @return PolicyIssue
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PolicyIssue::class);
  }
  /**
   * Lists all the policy issues where the specified account is involved, both
   * directly and through any AFP child accounts.
   * (policyIssues.listAccountsPolicyIssues)
   *
   * @param string $parent Required. The account for which policy issues are being
   * retrieved. Format: accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of policy issues to include in the
   * response, used for paging. If unspecified, at most 10000 policy issues will
   * be returned. The maximum value is 10000; values above 10000 will be coerced
   * to 10000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListPolicyIssues` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListPolicyIssues` must match
   * the call that provided the page token.
   * @return ListPolicyIssuesResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsPolicyIssues($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPolicyIssuesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsPolicyIssues::class, 'Google_Service_Adsense_Resource_AccountsPolicyIssues');
