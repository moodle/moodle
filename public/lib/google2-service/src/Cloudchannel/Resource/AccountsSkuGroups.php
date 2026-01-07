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

use Google\Service\Cloudchannel\GoogleCloudChannelV1ListSkuGroupsResponse;

/**
 * The "skuGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudchannelService = new Google\Service\Cloudchannel(...);
 *   $skuGroups = $cloudchannelService->accounts_skuGroups;
 *  </code>
 */
class AccountsSkuGroups extends \Google\Service\Resource
{
  /**
   * Lists the Rebilling supported SKU groups the account is authorized to sell.
   * Reference: https://cloud.google.com/skus/sku-groups Possible Error Codes: *
   * PERMISSION_DENIED: If the account making the request and the account being
   * queried are different, or the account doesn't exist. * INTERNAL: Any non-user
   * error related to technical issues in the backend. In this case, contact Cloud
   * Channel support. Return Value: If successful, the SkuGroup resources. The
   * data for each resource is displayed in the alphabetical order of SKU group
   * display name. The data for each resource is displayed in the ascending order
   * of SkuGroup.display_name If unsuccessful, returns an error.
   * (skuGroups.listAccountsSkuGroups)
   *
   * @param string $parent Required. The resource name of the account from which
   * to list SKU groups. Parent uses the format: accounts/{account}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of SKU groups to return.
   * The service may return fewer than this value. If unspecified, returns a
   * maximum of 1000 SKU groups. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * beyond the first page. Obtained through ListSkuGroupsResponse.next_page_token
   * of the previous CloudChannelService.ListSkuGroups call.
   * @return GoogleCloudChannelV1ListSkuGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsSkuGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudChannelV1ListSkuGroupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsSkuGroups::class, 'Google_Service_Cloudchannel_Resource_AccountsSkuGroups');
