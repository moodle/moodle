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

namespace Google\Service\IdentityToolkit;

class IdentitytoolkitRelyingpartySendVerificationCodeRequest extends \Google\Model
{
  /**
   * Receipt of successful app token validation with APNS.
   *
   * @var string
   */
  public $iosReceipt;
  /**
   * Secret delivered to iOS app via APNS.
   *
   * @var string
   */
  public $iosSecret;
  /**
   * The phone number to send the verification code to in E.164 format.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Recaptcha solution.
   *
   * @var string
   */
  public $recaptchaToken;

  /**
   * Receipt of successful app token validation with APNS.
   *
   * @param string $iosReceipt
   */
  public function setIosReceipt($iosReceipt)
  {
    $this->iosReceipt = $iosReceipt;
  }
  /**
   * @return string
   */
  public function getIosReceipt()
  {
    return $this->iosReceipt;
  }
  /**
   * Secret delivered to iOS app via APNS.
   *
   * @param string $iosSecret
   */
  public function setIosSecret($iosSecret)
  {
    $this->iosSecret = $iosSecret;
  }
  /**
   * @return string
   */
  public function getIosSecret()
  {
    return $this->iosSecret;
  }
  /**
   * The phone number to send the verification code to in E.164 format.
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
   * Recaptcha solution.
   *
   * @param string $recaptchaToken
   */
  public function setRecaptchaToken($recaptchaToken)
  {
    $this->recaptchaToken = $recaptchaToken;
  }
  /**
   * @return string
   */
  public function getRecaptchaToken()
  {
    return $this->recaptchaToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartySendVerificationCodeRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartySendVerificationCodeRequest');
