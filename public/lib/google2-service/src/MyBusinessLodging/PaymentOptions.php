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

namespace Google\Service\MyBusinessLodging;

class PaymentOptions extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CASH_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CASH_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CASH_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CASH_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CHEQUE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CHEQUE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CHEQUE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CHEQUE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CREDIT_CARD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CREDIT_CARD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CREDIT_CARD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CREDIT_CARD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DEBIT_CARD_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DEBIT_CARD_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DEBIT_CARD_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DEBIT_CARD_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILE_NFC_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILE_NFC_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILE_NFC_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILE_NFC_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Cash. The hotel accepts payment by paper/coin currency.
   *
   * @var bool
   */
  public $cash;
  /**
   * Cash exception.
   *
   * @var string
   */
  public $cashException;
  /**
   * Cheque. The hotel accepts a printed document issued by the guest's bank in
   * the guest's name as a form of payment.
   *
   * @var bool
   */
  public $cheque;
  /**
   * Cheque exception.
   *
   * @var string
   */
  public $chequeException;
  /**
   * Credit card. The hotel accepts payment by a card issued by a bank or credit
   * card company. Also known as charge card, debit card, bank card, or charge
   * plate.
   *
   * @var bool
   */
  public $creditCard;
  /**
   * Credit card exception.
   *
   * @var string
   */
  public $creditCardException;
  /**
   * Debit card. The hotel accepts a bank-issued card that immediately deducts
   * the charged funds from the guest's bank account upon processing.
   *
   * @var bool
   */
  public $debitCard;
  /**
   * Debit card exception.
   *
   * @var string
   */
  public $debitCardException;
  /**
   * Mobile nfc. The hotel has the compatible computer hardware terminal that
   * reads and charges a payment app on the guest's smartphone without requiring
   * the two devices to make physical contact. Also known as Apple Pay, Google
   * Pay, Samsung Pay.
   *
   * @var bool
   */
  public $mobileNfc;
  /**
   * Mobile nfc exception.
   *
   * @var string
   */
  public $mobileNfcException;

  /**
   * Cash. The hotel accepts payment by paper/coin currency.
   *
   * @param bool $cash
   */
  public function setCash($cash)
  {
    $this->cash = $cash;
  }
  /**
   * @return bool
   */
  public function getCash()
  {
    return $this->cash;
  }
  /**
   * Cash exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CASH_EXCEPTION_* $cashException
   */
  public function setCashException($cashException)
  {
    $this->cashException = $cashException;
  }
  /**
   * @return self::CASH_EXCEPTION_*
   */
  public function getCashException()
  {
    return $this->cashException;
  }
  /**
   * Cheque. The hotel accepts a printed document issued by the guest's bank in
   * the guest's name as a form of payment.
   *
   * @param bool $cheque
   */
  public function setCheque($cheque)
  {
    $this->cheque = $cheque;
  }
  /**
   * @return bool
   */
  public function getCheque()
  {
    return $this->cheque;
  }
  /**
   * Cheque exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CHEQUE_EXCEPTION_* $chequeException
   */
  public function setChequeException($chequeException)
  {
    $this->chequeException = $chequeException;
  }
  /**
   * @return self::CHEQUE_EXCEPTION_*
   */
  public function getChequeException()
  {
    return $this->chequeException;
  }
  /**
   * Credit card. The hotel accepts payment by a card issued by a bank or credit
   * card company. Also known as charge card, debit card, bank card, or charge
   * plate.
   *
   * @param bool $creditCard
   */
  public function setCreditCard($creditCard)
  {
    $this->creditCard = $creditCard;
  }
  /**
   * @return bool
   */
  public function getCreditCard()
  {
    return $this->creditCard;
  }
  /**
   * Credit card exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CREDIT_CARD_EXCEPTION_* $creditCardException
   */
  public function setCreditCardException($creditCardException)
  {
    $this->creditCardException = $creditCardException;
  }
  /**
   * @return self::CREDIT_CARD_EXCEPTION_*
   */
  public function getCreditCardException()
  {
    return $this->creditCardException;
  }
  /**
   * Debit card. The hotel accepts a bank-issued card that immediately deducts
   * the charged funds from the guest's bank account upon processing.
   *
   * @param bool $debitCard
   */
  public function setDebitCard($debitCard)
  {
    $this->debitCard = $debitCard;
  }
  /**
   * @return bool
   */
  public function getDebitCard()
  {
    return $this->debitCard;
  }
  /**
   * Debit card exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DEBIT_CARD_EXCEPTION_* $debitCardException
   */
  public function setDebitCardException($debitCardException)
  {
    $this->debitCardException = $debitCardException;
  }
  /**
   * @return self::DEBIT_CARD_EXCEPTION_*
   */
  public function getDebitCardException()
  {
    return $this->debitCardException;
  }
  /**
   * Mobile nfc. The hotel has the compatible computer hardware terminal that
   * reads and charges a payment app on the guest's smartphone without requiring
   * the two devices to make physical contact. Also known as Apple Pay, Google
   * Pay, Samsung Pay.
   *
   * @param bool $mobileNfc
   */
  public function setMobileNfc($mobileNfc)
  {
    $this->mobileNfc = $mobileNfc;
  }
  /**
   * @return bool
   */
  public function getMobileNfc()
  {
    return $this->mobileNfc;
  }
  /**
   * Mobile nfc exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILE_NFC_EXCEPTION_* $mobileNfcException
   */
  public function setMobileNfcException($mobileNfcException)
  {
    $this->mobileNfcException = $mobileNfcException;
  }
  /**
   * @return self::MOBILE_NFC_EXCEPTION_*
   */
  public function getMobileNfcException()
  {
    return $this->mobileNfcException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PaymentOptions::class, 'Google_Service_MyBusinessLodging_PaymentOptions');
