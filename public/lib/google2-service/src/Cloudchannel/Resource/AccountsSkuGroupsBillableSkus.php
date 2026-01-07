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

namespace Google\Service\Cloudchannel\Resource;

use Google\Service\Cloudchannel\GoogleCloudChannelV1ListSkuGroupBillableSkusResponse;

/**
 * The "billableSkus" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudchannelService = new Google\Service\Cloudchannel(...);
 *   $billableSkus = $cloudchannelService->accounts_skuGroups_billableSkus;
 *  </code>
 */
class AccountsSkuGroupsBillableSkus extends \Google\Service\Resource
{
  /**
   * Lists the Billable SKUs in a given SKU group. Possible error codes:
   * PERMISSION_DENIED: If the account making the request and the account being
   * queried for are different, or the account doesn't exist. INVALID_ARGUMENT:
   * Missing or invalid required parameters in the request. INTERNAL: Any non-user
   * error related to technical issue in the backend. In this case, contact cloud
   * channel support. Return Value: If successful, the BillableSku resources. The
   * data for each resource is displayed in the ascending order of: *
   * BillableSku.service_display_name * BillableSku.sku_display_name If
   * unsuccessful, returns an error.
   * (billableSkus.listAccountsSkuGroupsBillableSkus)
   *
   * @param string $parent Required. Resource name of the SKU group. Format:
   * accounts/{account}/skuGroups/{sku_group}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of SKUs to return. The
   * service may return fewer than this value. If unspecified, returns a maximum
   * of 100000 SKUs. The maximum value is 100000; values above 100000 will be
   * coerced to 100000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * beyond the first page. Obtained through
   * ListSkuGroupBillableSkusResponse.next_page_token of the previous
   * CloudChannelService.ListSkuGroupBillableSkus call.
   * @return GoogleCloudChannelV1ListSkuGroupBillableSkusResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsSkuGroupsBillableSkus($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudChannelV1ListSkuGroupBillableSkusResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsSkuGroupsBillableSkus::class, 'Google_Service_Cloudchannel_Resource_AccountsSkuGroupsBillableSkus');
