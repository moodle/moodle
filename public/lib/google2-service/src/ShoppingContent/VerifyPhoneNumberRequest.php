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

class VerifyPhoneNumberRequest extends \Google\Model
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
   * Verification method used to receive verification code.
   *
   * @var string
   */
  public $phoneVerificationMethod;
  /**
   * The verification code that was sent to the phone number for validation.
   *
   * @var string
   */
  public $verificationCode;
  /**
   * The verification ID returned by `requestphoneverification`.
   *
   * @var string
   */
  public $verificationId;

  /**
   * Verification method used to receive verification code.
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
  /**
   * The verification code that was sent to the phone number for validation.
   *
   * @param string $verificationCode
   */
  public function setVerificationCode($verificationCode)
  {
    $this->verificationCode = $verificationCode;
  }
  /**
   * @return string
   */
  public function getVerificationCode()
  {
    return $this->verificationCode;
  }
  /**
   * The verification ID returned by `requestphoneverification`.
   *
   * @param string $verificationId
   */
  public function setVerificationId($verificationId)
  {
    $this->verificationId = $verificationId;
  }
  /**
   * @return string
   */
  public function getVerificationId()
  {
    return $this->verificationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyPhoneNumberRequest::class, 'Google_Service_ShoppingContent_VerifyPhoneNumberRequest');
