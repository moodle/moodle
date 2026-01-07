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

namespace Google\Service\AndroidPublisher;

class ExternalTransaction extends \Google\Model
{
  /**
   * Unspecified transaction state. Not used.
   */
  public const TRANSACTION_STATE_TRANSACTION_STATE_UNSPECIFIED = 'TRANSACTION_STATE_UNSPECIFIED';
  /**
   * The transaction has been successfully reported to Google.
   */
  public const TRANSACTION_STATE_TRANSACTION_REPORTED = 'TRANSACTION_REPORTED';
  /**
   * The transaction has been fully refunded.
   */
  public const TRANSACTION_STATE_TRANSACTION_CANCELED = 'TRANSACTION_CANCELED';
  /**
   * Output only. The time when this transaction was created. This is the time
   * when Google was notified of the transaction.
   *
   * @var string
   */
  public $createTime;
  protected $currentPreTaxAmountType = Price::class;
  protected $currentPreTaxAmountDataType = '';
  protected $currentTaxAmountType = Price::class;
  protected $currentTaxAmountDataType = '';
  protected $externalOfferDetailsType = ExternalOfferDetails::class;
  protected $externalOfferDetailsDataType = '';
  /**
   * Output only. The id of this transaction. All transaction ids under the same
   * package name must be unique. Set when creating the external transaction.
   *
   * @var string
   */
  public $externalTransactionId;
  protected $oneTimeTransactionType = OneTimeExternalTransaction::class;
  protected $oneTimeTransactionDataType = '';
  protected $originalPreTaxAmountType = Price::class;
  protected $originalPreTaxAmountDataType = '';
  protected $originalTaxAmountType = Price::class;
  protected $originalTaxAmountDataType = '';
  /**
   * Output only. The resource name of the external transaction. The package
   * name of the application the inapp products were sold (for example,
   * 'com.some.app').
   *
   * @var string
   */
  public $packageName;
  protected $recurringTransactionType = RecurringExternalTransaction::class;
  protected $recurringTransactionDataType = '';
  protected $testPurchaseType = ExternalTransactionTestPurchase::class;
  protected $testPurchaseDataType = '';
  /**
   * Optional. The transaction program code, used to help determine service fee
   * for eligible apps participating in partner programs. Developers
   * participating in the Play Media Experience Program
   * (https://play.google.com/console/about/programs/mediaprogram/) must provide
   * the program code when reporting alternative billing transactions. If you
   * are an eligible developer, please contact your BDM for more information on
   * how to set this field. Note: this field can not be used for external offers
   * transactions.
   *
   * @var int
   */
  public $transactionProgramCode;
  /**
   * Output only. The current state of the transaction.
   *
   * @var string
   */
  public $transactionState;
  /**
   * Required. The time when the transaction was completed.
   *
   * @var string
   */
  public $transactionTime;
  protected $userTaxAddressType = ExternalTransactionAddress::class;
  protected $userTaxAddressDataType = '';

  /**
   * Output only. The time when this transaction was created. This is the time
   * when Google was notified of the transaction.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The current transaction amount before tax. This represents the
   * current pre-tax amount including any refunds that may have been applied to
   * this transaction.
   *
   * @param Price $currentPreTaxAmount
   */
  public function setCurrentPreTaxAmount(Price $currentPreTaxAmount)
  {
    $this->currentPreTaxAmount = $currentPreTaxAmount;
  }
  /**
   * @return Price
   */
  public function getCurrentPreTaxAmount()
  {
    return $this->currentPreTaxAmount;
  }
  /**
   * Output only. The current tax amount. This represents the current tax amount
   * including any refunds that may have been applied to this transaction.
   *
   * @param Price $currentTaxAmount
   */
  public function setCurrentTaxAmount(Price $currentTaxAmount)
  {
    $this->currentTaxAmount = $currentTaxAmount;
  }
  /**
   * @return Price
   */
  public function getCurrentTaxAmount()
  {
    return $this->currentTaxAmount;
  }
  /**
   * Optional. Details necessary to accurately report external offers
   * transactions.
   *
   * @param ExternalOfferDetails $externalOfferDetails
   */
  public function setExternalOfferDetails(ExternalOfferDetails $externalOfferDetails)
  {
    $this->externalOfferDetails = $externalOfferDetails;
  }
  /**
   * @return ExternalOfferDetails
   */
  public function getExternalOfferDetails()
  {
    return $this->externalOfferDetails;
  }
  /**
   * Output only. The id of this transaction. All transaction ids under the same
   * package name must be unique. Set when creating the external transaction.
   *
   * @param string $externalTransactionId
   */
  public function setExternalTransactionId($externalTransactionId)
  {
    $this->externalTransactionId = $externalTransactionId;
  }
  /**
   * @return string
   */
  public function getExternalTransactionId()
  {
    return $this->externalTransactionId;
  }
  /**
   * This is a one-time transaction and not part of a subscription.
   *
   * @param OneTimeExternalTransaction $oneTimeTransaction
   */
  public function setOneTimeTransaction(OneTimeExternalTransaction $oneTimeTransaction)
  {
    $this->oneTimeTransaction = $oneTimeTransaction;
  }
  /**
   * @return OneTimeExternalTransaction
   */
  public function getOneTimeTransaction()
  {
    return $this->oneTimeTransaction;
  }
  /**
   * Required. The original transaction amount before taxes. This represents the
   * pre-tax amount originally notified to Google before any refunds were
   * applied.
   *
   * @param Price $originalPreTaxAmount
   */
  public function setOriginalPreTaxAmount(Price $originalPreTaxAmount)
  {
    $this->originalPreTaxAmount = $originalPreTaxAmount;
  }
  /**
   * @return Price
   */
  public function getOriginalPreTaxAmount()
  {
    return $this->originalPreTaxAmount;
  }
  /**
   * Required. The original tax amount. This represents the tax amount
   * originally notified to Google before any refunds were applied.
   *
   * @param Price $originalTaxAmount
   */
  public function setOriginalTaxAmount(Price $originalTaxAmount)
  {
    $this->originalTaxAmount = $originalTaxAmount;
  }
  /**
   * @return Price
   */
  public function getOriginalTaxAmount()
  {
    return $this->originalTaxAmount;
  }
  /**
   * Output only. The resource name of the external transaction. The package
   * name of the application the inapp products were sold (for example,
   * 'com.some.app').
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * This transaction is part of a recurring series of transactions.
   *
   * @param RecurringExternalTransaction $recurringTransaction
   */
  public function setRecurringTransaction(RecurringExternalTransaction $recurringTransaction)
  {
    $this->recurringTransaction = $recurringTransaction;
  }
  /**
   * @return RecurringExternalTransaction
   */
  public function getRecurringTransaction()
  {
    return $this->recurringTransaction;
  }
  /**
   * Output only. If set, this transaction was a test purchase. Google will not
   * charge for a test transaction.
   *
   * @param ExternalTransactionTestPurchase $testPurchase
   */
  public function setTestPurchase(ExternalTransactionTestPurchase $testPurchase)
  {
    $this->testPurchase = $testPurchase;
  }
  /**
   * @return ExternalTransactionTestPurchase
   */
  public function getTestPurchase()
  {
    return $this->testPurchase;
  }
  /**
   * Optional. The transaction program code, used to help determine service fee
   * for eligible apps participating in partner programs. Developers
   * participating in the Play Media Experience Program
   * (https://play.google.com/console/about/programs/mediaprogram/) must provide
   * the program code when reporting alternative billing transactions. If you
   * are an eligible developer, please contact your BDM for more information on
   * how to set this field. Note: this field can not be used for external offers
   * transactions.
   *
   * @param int $transactionProgramCode
   */
  public function setTransactionProgramCode($transactionProgramCode)
  {
    $this->transactionProgramCode = $transactionProgramCode;
  }
  /**
   * @return int
   */
  public function getTransactionProgramCode()
  {
    return $this->transactionProgramCode;
  }
  /**
   * Output only. The current state of the transaction.
   *
   * Accepted values: TRANSACTION_STATE_UNSPECIFIED, TRANSACTION_REPORTED,
   * TRANSACTION_CANCELED
   *
   * @param self::TRANSACTION_STATE_* $transactionState
   */
  public function setTransactionState($transactionState)
  {
    $this->transactionState = $transactionState;
  }
  /**
   * @return self::TRANSACTION_STATE_*
   */
  public function getTransactionState()
  {
    return $this->transactionState;
  }
  /**
   * Required. The time when the transaction was completed.
   *
   * @param string $transactionTime
   */
  public function setTransactionTime($transactionTime)
  {
    $this->transactionTime = $transactionTime;
  }
  /**
   * @return string
   */
  public function getTransactionTime()
  {
    return $this->transactionTime;
  }
  /**
   * Required. User address for tax computation.
   *
   * @param ExternalTransactionAddress $userTaxAddress
   */
  public function setUserTaxAddress(ExternalTransactionAddress $userTaxAddress)
  {
    $this->userTaxAddress = $userTaxAddress;
  }
  /**
   * @return ExternalTransactionAddress
   */
  public function getUserTaxAddress()
  {
    return $this->userTaxAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalTransaction::class, 'Google_Service_AndroidPublisher_ExternalTransaction');
