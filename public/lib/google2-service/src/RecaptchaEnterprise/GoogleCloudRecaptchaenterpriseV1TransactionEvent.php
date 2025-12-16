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

class GoogleCloudRecaptchaenterpriseV1TransactionEvent extends \Google\Model
{
  /**
   * Default, unspecified event type.
   */
  public const EVENT_TYPE_TRANSACTION_EVENT_TYPE_UNSPECIFIED = 'TRANSACTION_EVENT_TYPE_UNSPECIFIED';
  /**
   * Indicates that the transaction is approved by the merchant. The
   * accompanying reasons can include terms such as 'INHOUSE', 'ACCERTIFY',
   * 'CYBERSOURCE', or 'MANUAL_REVIEW'.
   */
  public const EVENT_TYPE_MERCHANT_APPROVE = 'MERCHANT_APPROVE';
  /**
   * Indicates that the transaction is denied and concluded due to risks
   * detected by the merchant. The accompanying reasons can include terms such
   * as 'INHOUSE', 'ACCERTIFY', 'CYBERSOURCE', or 'MANUAL_REVIEW'.
   */
  public const EVENT_TYPE_MERCHANT_DENY = 'MERCHANT_DENY';
  /**
   * Indicates that the transaction is being evaluated by a human, due to
   * suspicion or risk.
   */
  public const EVENT_TYPE_MANUAL_REVIEW = 'MANUAL_REVIEW';
  /**
   * Indicates that the authorization attempt with the card issuer succeeded.
   */
  public const EVENT_TYPE_AUTHORIZATION = 'AUTHORIZATION';
  /**
   * Indicates that the authorization attempt with the card issuer failed. The
   * accompanying reasons can include Visa's '54' indicating that the card is
   * expired, or '82' indicating that the CVV is incorrect.
   */
  public const EVENT_TYPE_AUTHORIZATION_DECLINE = 'AUTHORIZATION_DECLINE';
  /**
   * Indicates that the transaction is completed because the funds were settled.
   */
  public const EVENT_TYPE_PAYMENT_CAPTURE = 'PAYMENT_CAPTURE';
  /**
   * Indicates that the transaction could not be completed because the funds
   * were not settled.
   */
  public const EVENT_TYPE_PAYMENT_CAPTURE_DECLINE = 'PAYMENT_CAPTURE_DECLINE';
  /**
   * Indicates that the transaction has been canceled. Specify the reason for
   * the cancellation. For example, 'INSUFFICIENT_INVENTORY'.
   */
  public const EVENT_TYPE_CANCEL = 'CANCEL';
  /**
   * Indicates that the merchant has received a chargeback inquiry due to fraud
   * for the transaction, requesting additional information before a fraud
   * chargeback is officially issued and a formal chargeback notification is
   * sent.
   */
  public const EVENT_TYPE_CHARGEBACK_INQUIRY = 'CHARGEBACK_INQUIRY';
  /**
   * Indicates that the merchant has received a chargeback alert due to fraud
   * for the transaction. The process of resolving the dispute without involving
   * the payment network is started.
   */
  public const EVENT_TYPE_CHARGEBACK_ALERT = 'CHARGEBACK_ALERT';
  /**
   * Indicates that a fraud notification is issued for the transaction, sent by
   * the payment instrument's issuing bank because the transaction appears to be
   * fraudulent. We recommend including TC40 or SAFE data in the `reason` field
   * for this event type. For partial chargebacks, we recommend that you include
   * an amount in the `value` field.
   */
  public const EVENT_TYPE_FRAUD_NOTIFICATION = 'FRAUD_NOTIFICATION';
  /**
   * Indicates that the merchant is informed by the payment network that the
   * transaction has entered the chargeback process due to fraud. Reason code
   * examples include Discover's '6005' and '6041'. For partial chargebacks, we
   * recommend that you include an amount in the `value` field.
   */
  public const EVENT_TYPE_CHARGEBACK = 'CHARGEBACK';
  /**
   * Indicates that the transaction has entered the chargeback process due to
   * fraud, and that the merchant has chosen to enter representment. Reason
   * examples include Discover's '6005' and '6041'. For partial chargebacks, we
   * recommend that you include an amount in the `value` field.
   */
  public const EVENT_TYPE_CHARGEBACK_REPRESENTMENT = 'CHARGEBACK_REPRESENTMENT';
  /**
   * Indicates that the transaction has had a fraud chargeback which was
   * illegitimate and was reversed as a result. For partial chargebacks, we
   * recommend that you include an amount in the `value` field.
   */
  public const EVENT_TYPE_CHARGEBACK_REVERSE = 'CHARGEBACK_REVERSE';
  /**
   * Indicates that the merchant has received a refund for a completed
   * transaction. For partial refunds, we recommend that you include an amount
   * in the `value` field. Reason example: 'TAX_EXEMPT' (partial refund of
   * exempt tax)
   */
  public const EVENT_TYPE_REFUND_REQUEST = 'REFUND_REQUEST';
  /**
   * Indicates that the merchant has received a refund request for this
   * transaction, but that they have declined it. For partial refunds, we
   * recommend that you include an amount in the `value` field. Reason example:
   * 'TAX_EXEMPT' (partial refund of exempt tax)
   */
  public const EVENT_TYPE_REFUND_DECLINE = 'REFUND_DECLINE';
  /**
   * Indicates that the completed transaction was refunded by the merchant. For
   * partial refunds, we recommend that you include an amount in the `value`
   * field. Reason example: 'TAX_EXEMPT' (partial refund of exempt tax)
   */
  public const EVENT_TYPE_REFUND = 'REFUND';
  /**
   * Indicates that the completed transaction was refunded by the merchant, and
   * that this refund was reversed. For partial refunds, we recommend that you
   * include an amount in the `value` field.
   */
  public const EVENT_TYPE_REFUND_REVERSE = 'REFUND_REVERSE';
  /**
   * Optional. Timestamp when this transaction event occurred; otherwise assumed
   * to be the time of the API call.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Optional. The type of this transaction event.
   *
   * @var string
   */
  public $eventType;
  /**
   * Optional. The reason or standardized code that corresponds with this
   * transaction event, if one exists. For example, a CHARGEBACK event with code
   * 6005.
   *
   * @var string
   */
  public $reason;
  /**
   * Optional. The value that corresponds with this transaction event, if one
   * exists. For example, a refund event where $5.00 was refunded. Currency is
   * obtained from the original transaction data.
   *
   * @var 
   */
  public $value;

  /**
   * Optional. Timestamp when this transaction event occurred; otherwise assumed
   * to be the time of the API call.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Optional. The type of this transaction event.
   *
   * Accepted values: TRANSACTION_EVENT_TYPE_UNSPECIFIED, MERCHANT_APPROVE,
   * MERCHANT_DENY, MANUAL_REVIEW, AUTHORIZATION, AUTHORIZATION_DECLINE,
   * PAYMENT_CAPTURE, PAYMENT_CAPTURE_DECLINE, CANCEL, CHARGEBACK_INQUIRY,
   * CHARGEBACK_ALERT, FRAUD_NOTIFICATION, CHARGEBACK, CHARGEBACK_REPRESENTMENT,
   * CHARGEBACK_REVERSE, REFUND_REQUEST, REFUND_DECLINE, REFUND, REFUND_REVERSE
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Optional. The reason or standardized code that corresponds with this
   * transaction event, if one exists. For example, a CHARGEBACK event with code
   * 6005.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
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
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionEvent::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionEvent');
