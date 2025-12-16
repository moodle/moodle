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

namespace Google\Service\ShoppingContent;

class AccountsCustomBatchResponse extends \Google\Collection
{
  protected $collection_key = 'entries';
  protected $entriesType = AccountsCustomBatchResponseEntry::class;
  protected $entriesDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountsCustomBatchResponse`".
   *
   * @var string
   */
  public $kind;

  /**
   * The result of the execution of the batch requests.
   *
   * @param AccountsCustomBatchResponseEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return AccountsCustomBatchResponseEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountsCustomBatchResponse`".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsCustomBatchResponse::class, 'Google_Service_ShoppingContent_AccountsCustomBatchResponse');
