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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1TransactionData extends \Google\Collection
{
  protected $collection_key = 'merchants';
  protected $billingAddressType = GoogleCloudRecaptchaenterpriseV1TransactionDataAddress::class;
  protected $billingAddressDataType = '';
  /**
   * Optional. The Bank Identification Number - generally the first 6 or 8
   * digits of the card.
   *
   * @var string
   */
  public $cardBin;
  /**
   * Optional. The last four digits of the card.
   *
   * @var string
   */
  public $cardLastFour;
  /**
   * Optional. The currency code in ISO-4217 format.
   *
   * @var string
   */
  public $currencyCode;
  protected $gatewayInfoType = GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo::class;
  protected $gatewayInfoDataType = '';
  protected $itemsType = GoogleCloudRecaptchaenterpriseV1TransactionDataItem::class;
  protected $itemsDataType = 'array';
  protected $merchantsType = GoogleCloudRecaptchaenterpriseV1TransactionDataUser::class;
  protected $merchantsDataType = 'array';
  /**
   * Optional. The payment method for the transaction. The allowed values are: *
   * credit-card * debit-card * gift-card * processor-{name} (If a third-party
   * is used, for example, processor-paypal) * custom-{name} (If an alternative
   * method is used, for example, custom-crypto)
   *
   * @var string
   */
  public $paymentMethod;
  protected $shippingAddressType = GoogleCloudRecaptchaenterpriseV1TransactionDataAddress::class;
  protected $shippingAddressDataType = '';
  /**
   * Optional. The value of shipping in the specified currency. 0 for free or no
   * shipping.
   *
   * @var 
   */
  public $shippingValue;
  /**
   * Unique identifier for the transaction. This custom identifier can be used
   * to reference this transaction in the future, for example, labeling a refund
   * or chargeback event. Two attempts at the same transaction should use the
   * same transaction id.
   *
   * @var string
   */
  public $transactionId;
  protected $userType = GoogleCloudRecaptchaenterpriseV1TransactionDataUser::class;
  protected $userDataType = '';
  /**
   * Optional. The decimal value of the transaction in the specified currency.
   *
   * @var 
   */
  public $value;

  /**
   * Optional. Address associated with the payment method when applicable.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataAddress $billingAddress
   */
  public function setBillingAddress(GoogleCloudRecaptchaenterpriseV1TransactionDataAddress $billingAddress)
  {
    $this->billingAddress = $billingAddress;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataAddress
   */
  public function getBillingAddress()
  {
    return $this->billingAddress;
  }
  /**
   * Optional. The Bank Identification Number - generally the first 6 or 8
   * digits of the card.
   *
   * @param string $cardBin
   */
  public function setCardBin($cardBin)
  {
    $this->cardBin = $cardBin;
  }
  /**
   * @return string
   */
  public function getCardBin()
  {
    return $this->cardBin;
  }
  /**
   * Optional. The last four digits of the card.
   *
   * @param string $cardLastFour
   */
  public function setCardLastFour($cardLastFour)
  {
    $this->cardLastFour = $cardLastFour;
  }
  /**
   * @return string
   */
  public function getCardLastFour()
  {
    return $this->cardLastFour;
  }
  /**
   * Optional. The currency code in ISO-4217 format.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Optional. Information about the payment gateway's response to the
   * transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo $gatewayInfo
   */
  public function setGatewayInfo(GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo $gatewayInfo)
  {
    $this->gatewayInfo = $gatewayInfo;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo
   */
  public function getGatewayInfo()
  {
    return $this->gatewayInfo;
  }
  /**
   * Optional. Items purchased in this transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataItem[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Optional. Information about the user or users fulfilling the transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataUser[] $merchants
   */
  public function setMerchants($merchants)
  {
    $this->merchants = $merchants;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataUser[]
   */
  public function getMerchants()
  {
    return $this->merchants;
  }
  /**
   * Optional. The payment method for the transaction. The allowed values are: *
   * credit-card * debit-card * gift-card * processor-{name} (If a third-party
   * is used, for example, processor-paypal) * custom-{name} (If an alternative
   * method is used, for example, custom-crypto)
   *
   * @param string $paymentMethod
   */
  public function setPaymentMethod($paymentMethod)
  {
    $this->paymentMethod = $paymentMethod;
  }
  /**
   * @return string
   */
  public function getPaymentMethod()
  {
    return $this->paymentMethod;
  }
  /**
   * Optional. Destination address if this transaction involves shipping a
   * physical item.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataAddress $shippingAddress
   */
  public function setShippingAddress(GoogleCloudRecaptchaenterpriseV1TransactionDataAddress $shippingAddress)
  {
    $this->shippingAddress = $shippingAddress;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataAddress
   */
  public function getShippingAddress()
  {
    return $this->shippingAddress;
  }
  public function setShippingValue($shippingValue)
  {
    $this->shippingValue = $shippingValue;
  }
  public function getShippingValue()
  {
    return $this->shippingValue;
  }
  /**
   * Unique identifier for the transaction. This custom identifier can be used
   * to reference this transaction in the future, for example, labeling a refund
   * or chargeback event. Two attempts at the same transaction should use the
   * same transaction id.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
  /**
   * Optional. Information about the user paying/initiating the transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionDataUser $user
   */
  public function setUser(GoogleCloudRecaptchaenterpriseV1TransactionDataUser $user)
  {
    $this->user = $user;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionDataUser
   */
  public function getUser()
  {
    return $this->user;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionData::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionData');
