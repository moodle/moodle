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

class GetRecaptchaParamResponse extends \Google\Model
{
  /**
   * The fixed string "identitytoolkit#GetRecaptchaParamResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * Site key registered at recaptcha.
   *
   * @var string
   */
  public $recaptchaSiteKey;
  /**
   * The stoken field for the recaptcha widget, used to request captcha
   * challenge.
   *
   * @var string
   */
  public $recaptchaStoken;

  /**
   * The fixed string "identitytoolkit#GetRecaptchaParamResponse".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Site key registered at recaptcha.
   *
   * @param string $recaptchaSiteKey
   */
  public function setRecaptchaSiteKey($recaptchaSiteKey)
  {
    $this->recaptchaSiteKey = $recaptchaSiteKey;
  }
  /**
   * @return string
   */
  public function getRecaptchaSiteKey()
  {
    return $this->recaptchaSiteKey;
  }
  /**
   * The stoken field for the recaptcha widget, used to request captcha
   * challenge.
   *
   * @param string $recaptchaStoken
   */
  public function setRecaptchaStoken($recaptchaStoken)
  {
    $this->recaptchaStoken = $recaptchaStoken;
  }
  /**
   * @return string
   */
  public function getRecaptchaStoken()
  {
    return $this->recaptchaStoken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetRecaptchaParamResponse::class, 'Google_Service_IdentityToolkit_GetRecaptchaParamResponse');
