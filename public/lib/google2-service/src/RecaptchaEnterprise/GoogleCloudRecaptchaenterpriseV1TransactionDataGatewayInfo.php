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

class GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo extends \Google\Model
{
  /**
   * Optional. AVS response code from the gateway (available only when reCAPTCHA
   * Enterprise is called after authorization).
   *
   * @var string
   */
  public $avsResponseCode;
  /**
   * Optional. CVV response code from the gateway (available only when reCAPTCHA
   * Enterprise is called after authorization).
   *
   * @var string
   */
  public $cvvResponseCode;
  /**
   * Optional. Gateway response code describing the state of the transaction.
   *
   * @var string
   */
  public $gatewayResponseCode;
  /**
   * Optional. Name of the gateway service (for example, stripe, square,
   * paypal).
   *
   * @var string
   */
  public $name;

  /**
   * Optional. AVS response code from the gateway (available only when reCAPTCHA
   * Enterprise is called after authorization).
   *
   * @param string $avsResponseCode
   */
  public function setAvsResponseCode($avsResponseCode)
  {
    $this->avsResponseCode = $avsResponseCode;
  }
  /**
   * @return string
   */
  public function getAvsResponseCode()
  {
    return $this->avsResponseCode;
  }
  /**
   * Optional. CVV response code from the gateway (available only when reCAPTCHA
   * Enterprise is called after authorization).
   *
   * @param string $cvvResponseCode
   */
  public function setCvvResponseCode($cvvResponseCode)
  {
    $this->cvvResponseCode = $cvvResponseCode;
  }
  /**
   * @return string
   */
  public function getCvvResponseCode()
  {
    return $this->cvvResponseCode;
  }
  /**
   * Optional. Gateway response code describing the state of the transaction.
   *
   * @param string $gatewayResponseCode
   */
  public function setGatewayResponseCode($gatewayResponseCode)
  {
    $this->gatewayResponseCode = $gatewayResponseCode;
  }
  /**
   * @return string
   */
  public function getGatewayResponseCode()
  {
    return $this->gatewayResponseCode;
  }
  /**
   * Optional. Name of the gateway service (for example, stripe, square,
   * paypal).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionDataGatewayInfo');
