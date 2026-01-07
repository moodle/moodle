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

namespace Google\Service\Cloudbilling\Resource;

use Google\Service\Cloudbilling\BillingAccount;
use Google\Service\Cloudbilling\ListBillingAccountsResponse;

/**
 * The "subAccounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudbillingService = new Google\Service\Cloudbilling(...);
 *   $subAccounts = $cloudbillingService->billingAccounts_subAccounts;
 *  </code>
 */
class BillingAccountsSubAccounts extends \Google\Service\Resource
{
  /**
   * This method creates [billing
   * subaccounts](https://cloud.google.com/billing/docs/concepts#subaccounts).
   * Google Cloud resellers should use the Channel Services APIs, [accounts.custom
   * ers.create](https://cloud.google.com/channel/docs/reference/rest/v1/accounts.
   * customers/create) and [accounts.customers.entitlements.create](https://cloud.
   * google.com/channel/docs/reference/rest/v1/accounts.customers.entitlements/cre
   * ate). When creating a subaccount, the current authenticated user must have
   * the `billing.accounts.update` IAM permission on the parent account, which is
   * typically given to billing account
   * [administrators](https://cloud.google.com/billing/docs/how-to/billing-
   * access). This method will return an error if the parent account has not been
   * provisioned for subaccounts. (subAccounts.create)
   *
   * @param string $parent Optional. The parent to create a billing account from.
   * Format: - `billingAccounts/{billing_account_id}`, for example,
   * `billingAccounts/012345-567890-ABCDEF`
   * @param BillingAccount $postBody
   * @param array $optParams Optional parameters.
   * @return BillingAccount
   * @throws \Google\Service\Exception
   */
  public function create($parent, BillingAccount $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], BillingAccount::class);
  }
  /**
   * Lists the billing accounts that the current authenticated user has permission
   * to [view](https://cloud.google.com/billing/docs/how-to/billing-access).
   * (subAccounts.listBillingAccountsSubAccounts)
   *
   * @param string $parent Optional. The parent resource to list billing accounts
   * from. Format: - `organizations/{organization_id}`, for example,
   * `organizations/12345678` - `billingAccounts/{billing_account_id}`, for
   * example, `billingAccounts/012345-567890-ABCDEF`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Options for how to filter the returned billing
   * accounts. This only supports filtering for
   * [subaccounts](https://cloud.google.com/billing/docs/concepts) under a single
   * provided parent billing account. (for example,
   * `master_billing_account=billingAccounts/012345-678901-ABCDEF`). Boolean
   * algebra and other fields are not currently supported.
   * @opt_param int pageSize Requested page size. The maximum page size is 100;
   * this is also the default.
   * @opt_param string pageToken A token identifying a page of results to return.
   * This should be a `next_page_token` value returned from a previous
   * `ListBillingAccounts` call. If unspecified, the first page of results is
   * returned.
   * @return ListBillingAccountsResponse
   * @throws \Google\Service\Exception
   */
  public function listBillingAccountsSubAccounts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBillingAccountsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingAccountsSubAccounts::class, 'Google_Service_Cloudbilling_Resource_BillingAccountsSubAccounts');
