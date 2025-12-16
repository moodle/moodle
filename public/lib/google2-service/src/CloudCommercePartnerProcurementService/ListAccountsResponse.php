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

namespace Google\Service\CloudCommercePartnerProcurementService;

class ListAccountsResponse extends \Google\Collection
{
  protected $collection_key = 'accounts';
  protected $accountsType = Account::class;
  protected $accountsDataType = 'array';
  /**
   * The token for fetching the next page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of accounts in this response.
   *
   * @param Account[] $accounts
   */
  public function setAccounts($accounts)
  {
    $this->accounts = $accounts;
  }
  /**
   * @return Account[]
   */
  public function getAccounts()
  {
    return $this->accounts;
  }
  /**
   * The token for fetching the next page.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAccountsResponse::class, 'Google_Service_CloudCommercePartnerProcurementService_ListAccountsResponse');
