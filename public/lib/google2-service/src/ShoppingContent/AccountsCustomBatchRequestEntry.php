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

class AccountsCustomBatchRequestEntry extends \Google\Collection
{
  protected $collection_key = 'labelIds';
  protected $accountType = Account::class;
  protected $accountDataType = '';
  /**
   * The ID of the targeted account. Only defined if the method is not `insert`.
   *
   * @var string
   */
  public $accountId;
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  /**
   * Whether the account should be deleted if the account has offers. Only
   * applicable if the method is `delete`.
   *
   * @var bool
   */
  public $force;
  /**
   * Label IDs for the 'updatelabels' request.
   *
   * @var string[]
   */
  public $labelIds;
  protected $linkRequestType = AccountsCustomBatchRequestEntryLinkRequest::class;
  protected $linkRequestDataType = '';
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`claimWebsite`" -
   * "`delete`" - "`get`" - "`insert`" - "`link`" - "`update`"
   *
   * @var string
   */
  public $method;
  /**
   * Only applicable if the method is `claimwebsite`. Indicates whether or not
   * to take the claim from another account in case there is a conflict.
   *
   * @var bool
   */
  public $overwrite;
  /**
   * Controls which fields are visible. Only applicable if the method is 'get'.
   *
   * @var string
   */
  public $view;

  /**
   * The account to create or update. Only defined if the method is `insert` or
   * `update`.
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
   * The ID of the targeted account. Only defined if the method is not `insert`.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * An entry ID, unique within the batch request.
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
   * Whether the account should be deleted if the account has offers. Only
   * applicable if the method is `delete`.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Label IDs for the 'updatelabels' request.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * Details about the `link` request.
   *
   * @param AccountsCustomBatchRequestEntryLinkRequest $linkRequest
   */
  public function setLinkRequest(AccountsCustomBatchRequestEntryLinkRequest $linkRequest)
  {
    $this->linkRequest = $linkRequest;
  }
  /**
   * @return AccountsCustomBatchRequestEntryLinkRequest
   */
  public function getLinkRequest()
  {
    return $this->linkRequest;
  }
  /**
   * The ID of the managing account.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * The method of the batch entry. Acceptable values are: - "`claimWebsite`" -
   * "`delete`" - "`get`" - "`insert`" - "`link`" - "`update`"
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Only applicable if the method is `claimwebsite`. Indicates whether or not
   * to take the claim from another account in case there is a conflict.
   *
   * @param bool $overwrite
   */
  public function setOverwrite($overwrite)
  {
    $this->overwrite = $overwrite;
  }
  /**
   * @return bool
   */
  public function getOverwrite()
  {
    return $this->overwrite;
  }
  /**
   * Controls which fields are visible. Only applicable if the method is 'get'.
   *
   * @param string $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_AccountsCustomBatchRequestEntry');
