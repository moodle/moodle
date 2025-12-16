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

namespace Google\Service\MyBusinessAccountManagement;

class ListAccountsResponse extends \Google\Collection
{
  protected $collection_key = 'accounts';
  protected $accountsType = Account::class;
  protected $accountsDataType = 'array';
  /**
   * If the number of accounts exceeds the requested page size, this field is
   * populated with a token to fetch the next page of accounts on a subsequent
   * call to `accounts.list`. If there are no more accounts, this field is not
   * present in the response.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A collection of accounts to which the user has access. The personal account
   * of the user doing the query will always be the first item of the result,
   * unless it is filtered out.
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
   * If the number of accounts exceeds the requested page size, this field is
   * populated with a token to fetch the next page of accounts on a subsequent
   * call to `accounts.list`. If there are no more accounts, this field is not
   * present in the response.
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
class_alias(ListAccountsResponse::class, 'Google_Service_MyBusinessAccountManagement_ListAccountsResponse');
