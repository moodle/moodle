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

namespace Google\Service\PostmasterTools;

class DeliveryError extends \Google\Model
{
  /**
   * The default value which should never be used explicitly.
   */
  public const ERROR_CLASS_DELIVERY_ERROR_CLASS_UNSPECIFIED = 'DELIVERY_ERROR_CLASS_UNSPECIFIED';
  /**
   * Delivery of message has been rejected.
   */
  public const ERROR_CLASS_PERMANENT_ERROR = 'PERMANENT_ERROR';
  /**
   * Temporary failure of message delivery to the recipient.
   */
  public const ERROR_CLASS_TEMPORARY_ERROR = 'TEMPORARY_ERROR';
  /**
   * The default value which should never be used explicitly.
   */
  public const ERROR_TYPE_DELIVERY_ERROR_TYPE_UNSPECIFIED = 'DELIVERY_ERROR_TYPE_UNSPECIFIED';
  /**
   * The Domain or IP is sending traffic at a suspiciously high rate, due to
   * which temporary rate limits have been imposed. The limit will be lifted
   * when Gmail is confident enough of the nature of the traffic.
   */
  public const ERROR_TYPE_RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';
  /**
   * The traffic is suspected to be spam, by Gmail, for various reasons.
   */
  public const ERROR_TYPE_SUSPECTED_SPAM = 'SUSPECTED_SPAM';
  /**
   * The traffic is suspected to be spammy, specific to the content.
   */
  public const ERROR_TYPE_CONTENT_SPAMMY = 'CONTENT_SPAMMY';
  /**
   * Traffic contains attachments not supported by Gmail.
   */
  public const ERROR_TYPE_BAD_ATTACHMENT = 'BAD_ATTACHMENT';
  /**
   * The sender domain has set up a DMARC rejection policy.
   */
  public const ERROR_TYPE_BAD_DMARC_POLICY = 'BAD_DMARC_POLICY';
  /**
   * The IP reputation of the sending IP is very low.
   */
  public const ERROR_TYPE_LOW_IP_REPUTATION = 'LOW_IP_REPUTATION';
  /**
   * The Domain reputation of the sending domain is very low.
   */
  public const ERROR_TYPE_LOW_DOMAIN_REPUTATION = 'LOW_DOMAIN_REPUTATION';
  /**
   * The IP is listed in one or more public [Real-time Blackhole
   * Lists](http://en.wikipedia.org/wiki/DNSBL). Work with the RBL to get your
   * IP delisted.
   */
  public const ERROR_TYPE_IP_IN_RBL = 'IP_IN_RBL';
  /**
   * The Domain is listed in one or more public [Real-time Blackhole
   * Lists](http://en.wikipedia.org/wiki/DNSBL). Work with the RBL to get your
   * domain delisted.
   */
  public const ERROR_TYPE_DOMAIN_IN_RBL = 'DOMAIN_IN_RBL';
  /**
   * The sending IP is missing a [PTR
   * record](https://support.google.com/domains/answer/3251147#ptr).
   */
  public const ERROR_TYPE_BAD_PTR_RECORD = 'BAD_PTR_RECORD';
  /**
   * The class of delivery error.
   *
   * @var string
   */
  public $errorClass;
  /**
   * The ratio of messages where the error occurred vs all authenticated
   * traffic.
   *
   * @var 
   */
  public $errorRatio;
  /**
   * The type of delivery error.
   *
   * @var string
   */
  public $errorType;

  /**
   * The class of delivery error.
   *
   * Accepted values: DELIVERY_ERROR_CLASS_UNSPECIFIED, PERMANENT_ERROR,
   * TEMPORARY_ERROR
   *
   * @param self::ERROR_CLASS_* $errorClass
   */
  public function setErrorClass($errorClass)
  {
    $this->errorClass = $errorClass;
  }
  /**
   * @return self::ERROR_CLASS_*
   */
  public function getErrorClass()
  {
    return $this->errorClass;
  }
  public function setErrorRatio($errorRatio)
  {
    $this->errorRatio = $errorRatio;
  }
  public function getErrorRatio()
  {
    return $this->errorRatio;
  }
  /**
   * The type of delivery error.
   *
   * Accepted values: DELIVERY_ERROR_TYPE_UNSPECIFIED, RATE_LIMIT_EXCEEDED,
   * SUSPECTED_SPAM, CONTENT_SPAMMY, BAD_ATTACHMENT, BAD_DMARC_POLICY,
   * LOW_IP_REPUTATION, LOW_DOMAIN_REPUTATION, IP_IN_RBL, DOMAIN_IN_RBL,
   * BAD_PTR_RECORD
   *
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryError::class, 'Google_Service_PostmasterTools_DeliveryError');
