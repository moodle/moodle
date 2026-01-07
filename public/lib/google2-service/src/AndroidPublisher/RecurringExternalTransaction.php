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

class RecurringExternalTransaction extends \Google\Model
{
  /**
   * Unspecified transaction program. Not used.
   */
  public const MIGRATED_TRANSACTION_PROGRAM_EXTERNAL_TRANSACTION_PROGRAM_UNSPECIFIED = 'EXTERNAL_TRANSACTION_PROGRAM_UNSPECIFIED';
  /**
   * User choice billing, where a user may choose between Google Play Billing
   * developer-managed billing.
   */
  public const MIGRATED_TRANSACTION_PROGRAM_USER_CHOICE_BILLING = 'USER_CHOICE_BILLING';
  /**
   * Alternative billing only, where users may only use developer-manager
   * billing.
   */
  public const MIGRATED_TRANSACTION_PROGRAM_ALTERNATIVE_BILLING_ONLY = 'ALTERNATIVE_BILLING_ONLY';
  protected $externalSubscriptionType = ExternalSubscription::class;
  protected $externalSubscriptionDataType = '';
  /**
   * Input only. Provided during the call to Create. Retrieved from the client
   * when the alternative billing flow is launched. Required only for the
   * initial purchase.
   *
   * @var string
   */
  public $externalTransactionToken;
  /**
   * The external transaction id of the first transaction of this recurring
   * series of transactions. For example, for a subscription this would be the
   * transaction id of the first payment. Required when creating recurring
   * external transactions.
   *
   * @var string
   */
  public $initialExternalTransactionId;
  /**
   * Input only. Provided during the call to Create. Must only be used when
   * migrating a subscription from manual monthly reporting to automated
   * reporting.
   *
   * @var string
   */
  public $migratedTransactionProgram;
  protected $otherRecurringProductType = OtherRecurringProduct::class;
  protected $otherRecurringProductDataType = '';

  /**
   * Details of an external subscription.
   *
   * @param ExternalSubscription $externalSubscription
   */
  public function setExternalSubscription(ExternalSubscription $externalSubscription)
  {
    $this->externalSubscription = $externalSubscription;
  }
  /**
   * @return ExternalSubscription
   */
  public function getExternalSubscription()
  {
    return $this->externalSubscription;
  }
  /**
   * Input only. Provided during the call to Create. Retrieved from the client
   * when the alternative billing flow is launched. Required only for the
   * initial purchase.
   *
   * @param string $externalTransactionToken
   */
  public function setExternalTransactionToken($externalTransactionToken)
  {
    $this->externalTransactionToken = $externalTransactionToken;
  }
  /**
   * @return string
   */
  public function getExternalTransactionToken()
  {
    return $this->externalTransactionToken;
  }
  /**
   * The external transaction id of the first transaction of this recurring
   * series of transactions. For example, for a subscription this would be the
   * transaction id of the first payment. Required when creating recurring
   * external transactions.
   *
   * @param string $initialExternalTransactionId
   */
  public function setInitialExternalTransactionId($initialExternalTransactionId)
  {
    $this->initialExternalTransactionId = $initialExternalTransactionId;
  }
  /**
   * @return string
   */
  public function getInitialExternalTransactionId()
  {
    return $this->initialExternalTransactionId;
  }
  /**
   * Input only. Provided during the call to Create. Must only be used when
   * migrating a subscription from manual monthly reporting to automated
   * reporting.
   *
   * Accepted values: EXTERNAL_TRANSACTION_PROGRAM_UNSPECIFIED,
   * USER_CHOICE_BILLING, ALTERNATIVE_BILLING_ONLY
   *
   * @param self::MIGRATED_TRANSACTION_PROGRAM_* $migratedTransactionProgram
   */
  public function setMigratedTransactionProgram($migratedTransactionProgram)
  {
    $this->migratedTransactionProgram = $migratedTransactionProgram;
  }
  /**
   * @return self::MIGRATED_TRANSACTION_PROGRAM_*
   */
  public function getMigratedTransactionProgram()
  {
    return $this->migratedTransactionProgram;
  }
  /**
   * Details of a recurring external transaction product which doesn't belong to
   * any other specific category.
   *
   * @param OtherRecurringProduct $otherRecurringProduct
   */
  public function setOtherRecurringProduct(OtherRecurringProduct $otherRecurringProduct)
  {
    $this->otherRecurringProduct = $otherRecurringProduct;
  }
  /**
   * @return OtherRecurringProduct
   */
  public function getOtherRecurringProduct()
  {
    return $this->otherRecurringProduct;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecurringExternalTransaction::class, 'Google_Service_AndroidPublisher_RecurringExternalTransaction');
