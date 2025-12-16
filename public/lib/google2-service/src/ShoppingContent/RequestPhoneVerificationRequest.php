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

class RequestPhoneVerificationRequest extends \Google\Model
{
  /**
   * Unknown method.
   */
  public const PHONE_VERIFICATION_METHOD_PHONE_VERIFICATION_METHOD_UNSPECIFIED = 'PHONE_VERIFICATION_METHOD_UNSPECIFIED';
  /**
   * Receive verification code by SMS.
   */
  public const PHONE_VERIFICATION_METHOD_SMS = 'SMS';
  /**
   * Receive verification code by phone call.
   */
  public const PHONE_VERIFICATION_METHOD_PHONE_CALL = 'PHONE_CALL';
  /**
   * Language code [IETF BCP 47 syntax](https://tools.ietf.org/html/bcp47) (for
   * example, en-US). Language code is used to provide localized `SMS` and
   * `PHONE_CALL`. Default language used is en-US if not provided.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Phone number to be verified.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Required. Two letter country code for the phone number, for example `CA`
   * for Canadian numbers. See the [ISO 3166-1 alpha-
   * 2](https://wikipedia.org/wiki/ISO_3166-1_alpha-
   * 2#Officially_assigned_code_elements) officially assigned codes.
   *
   * @var string
   */
  public $phoneRegionCode;
  /**
   * Verification method to receive verification code.
   *
   * @var string
   */
  public $phoneVerificationMethod;

  /**
   * Language code [IETF BCP 47 syntax](https://tools.ietf.org/html/bcp47) (for
   * example, en-US). Language code is used to provide localized `SMS` and
   * `PHONE_CALL`. Default language used is en-US if not provided.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Phone number to be verified.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * Required. Two letter country code for the phone number, for example `CA`
   * for Canadian numbers. See the [ISO 3166-1 alpha-
   * 2](https://wikipedia.org/wiki/ISO_3166-1_alpha-
   * 2#Officially_assigned_code_elements) officially assigned codes.
   *
   * @param string $phoneRegionCode
   */
  public function setPhoneRegionCode($phoneRegionCode)
  {
    $this->phoneRegionCode = $phoneRegionCode;
  }
  /**
   * @return string
   */
  public function getPhoneRegionCode()
  {
    return $this->phoneRegionCode;
  }
  /**
   * Verification method to receive verification code.
   *
   * Accepted values: PHONE_VERIFICATION_METHOD_UNSPECIFIED, SMS, PHONE_CALL
   *
   * @param self::PHONE_VERIFICATION_METHOD_* $phoneVerificationMethod
   */
  public function setPhoneVerificationMethod($phoneVerificationMethod)
  {
    $this->phoneVerificationMethod = $phoneVerificationMethod;
  }
  /**
   * @return self::PHONE_VERIFICATION_METHOD_*
   */
  public function getPhoneVerificationMethod()
  {
    return $this->phoneVerificationMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestPhoneVerificationRequest::class, 'Google_Service_ShoppingContent_RequestPhoneVerificationRequest');
