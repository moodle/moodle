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

namespace Google\Service\Merchant\Resource;

use Google\Service\Merchant\SearchRequest;
use Google\Service\Merchant\SearchResponse;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $merchantapiService = new Google\Service\Merchant(...);
 *   $reports = $merchantapiService->accounts_reports;
 *  </code>
 */
class AccountsReports extends \Google\Service\Resource
{
  /**
   * Retrieves a report defined by a search query. The response might contain
   * fewer rows than specified by `page_size`. Rely on `next_page_token` to
   * determine if there are more rows to be requested. (reports.search)
   *
   * @param string $parent Required. Id of the account making the call. Must be a
   * standalone account or an MCA subaccount. Format: accounts/{account}
   * @param SearchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SearchResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, SearchRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsReports::class, 'Google_Service_Merchant_Resource_AccountsReports');
