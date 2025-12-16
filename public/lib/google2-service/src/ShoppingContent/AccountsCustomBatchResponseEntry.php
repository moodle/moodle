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

class AccountsCustomBatchResponseEntry extends \Google\Model
{
  protected $accountType = Account::class;
  protected $accountDataType = '';
  /**
   * The ID of the request entry this entry responds to.
   *
   * @var string
   */
  public $batchId;
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountsCustomBatchResponseEntry`"
   *
   * @var string
   */
  public $kind;

  /**
   * The retrieved, created, or updated account. Not defined if the method was
   * `delete`, `claimwebsite` or `link`.
   *
   * @param Account $account
   */
  public function setAccount(Account $account)
  {
    $this->account = $account;
  }
  /**
   * @return Account
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * The ID of the request entry this entry responds to.
   *
   * @param string $batchId
   */
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  /**
   * @return string
   */
  public function getBatchId()
  {
    return $this->batchId;
  }
  /**
   * A list of errors for failed custombatch entries. *Note:* Schema errors fail
   * the whole request.
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountsCustomBatchResponseEntry`"
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
class_alias(AccountsCustomBatchResponseEntry::class, 'Google_Service_ShoppingContent_AccountsCustomBatchResponseEntry');
