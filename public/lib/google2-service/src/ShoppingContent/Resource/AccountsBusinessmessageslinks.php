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

namespace Google\Service\ShoppingContent\Resource;

use Google\Service\ShoppingContent\BusinessMessagesLink;
use Google\Service\ShoppingContent\ListBusinessMessagesLinksResponse;

/**
 * The "businessmessageslinks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $businessmessageslinks = $contentService->accounts_businessmessageslinks;
 *  </code>
 */
class AccountsBusinessmessageslinks extends \Google\Service\Resource
{
  /**
   * Creates a `BusinessMessagesLink` in Merchant Center account.
   * (businessmessageslinks.create)
   *
   * @param string $accountId Required. The ID of the Merchant Center account.
   * @param BusinessMessagesLink $postBody
   * @param array $optParams Optional parameters.
   * @return BusinessMessagesLink
   */
  public function create($accountId, BusinessMessagesLink $postBody, $optParams = [])
  {
    $params = ['accountId' => $accountId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], BusinessMessagesLink::class);
  }
  /**
   * Deletes the specified `BusinessMessagesLink` resource from Merchant Center
   * account. (businessmessageslinks.delete)
   *
   * @param string $accountId Required. The ID of the Merchant Center account.
   * @param string $businessMessagesLinkId Required. The identifier for the
   * Business Messages Link.
   * @param array $optParams Optional parameters.
   */
  public function delete($accountId, $businessMessagesLinkId, $optParams = [])
  {
    $params = ['accountId' => $accountId, 'businessMessagesLinkId' => $businessMessagesLinkId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves `BusinessMessagesLink` in Merchant Center account.
   * (businessmessageslinks.get)
   *
   * @param string $accountId Required. The ID of the Merchant Center account.
   * @param string $businessMessagesLinkId Required. The identifier for the
   * Business Messages Link.
   * @param array $optParams Optional parameters.
   * @return BusinessMessagesLink
   */
  public function get($accountId, $businessMessagesLinkId, $optParams = [])
  {
    $params = ['accountId' => $accountId, 'businessMessagesLinkId' => $businessMessagesLinkId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BusinessMessagesLink::class);
  }
  /**
   * Lists the `BusinessMessagesLink` resources for Merchant Center account.
   * (businessmessageslinks.listAccountsBusinessmessageslinks)
   *
   * @param string $accountId Required. The ID of the account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of
   * `BusinessMessagesLink` resources for the Merchant Center account to return.
   * Defaults to 50; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListBusinessMessagesLinks` call. Provide the page token to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListBusinessMessagesLinks` must match the call that provided the page token.
   * @return ListBusinessMessagesLinksResponse
   */
  public function listAccountsBusinessmessageslinks($accountId, $optParams = [])
  {
    $params = ['accountId' => $accountId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBusinessMessagesLinksResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsBusinessmessageslinks::class, 'Google_Service_ShoppingContent_Resource_AccountsBusinessmessageslinks');
