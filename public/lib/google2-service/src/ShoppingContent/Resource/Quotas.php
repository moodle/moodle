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

use Google\Service\ShoppingContent\ListMethodQuotasResponse;

/**
 * The "quotas" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $quotas = $contentService->quotas;
 *  </code>
 */
class Quotas extends \Google\Service\Resource
{
  /**
   * Lists the daily call quota and usage per method for your Merchant Center
   * account. (quotas.listQuotas)
   *
   * @param string $merchantId Required. The ID of the account that has quota.
   * This account must be an admin.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of quotas to return in the
   * response, used for paging. Defaults to 500; values above 1000 will be coerced
   * to 1000.
   * @opt_param string pageToken Token (if provided) to retrieve the subsequent
   * page. All other parameters must match the original call that provided the
   * page token.
   * @return ListMethodQuotasResponse
   * @throws \Google\Service\Exception
   */
  public function listQuotas($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMethodQuotasResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Quotas::class, 'Google_Service_ShoppingContent_Resource_Quotas');
