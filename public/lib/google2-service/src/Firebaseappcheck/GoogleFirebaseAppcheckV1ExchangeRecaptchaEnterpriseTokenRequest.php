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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1ExchangeRecaptchaEnterpriseTokenRequest extends \Google\Model
{
  /**
   * Specifies whether this attestation is for use in a *limited use* (`true`)
   * or *session based* (`false`) context. To enable this attestation to be used
   * with the *replay protection* feature, set this to `true`. The default value
   * is `false`.
   *
   * @var bool
   */
  public $limitedUse;
  /**
   * Required. The reCAPTCHA token as returned by the [reCAPTCHA Enterprise
   * JavaScript API](https://cloud.google.com/recaptcha-
   * enterprise/docs/instrument-web-pages).
   *
   * @var string
   */
  public $recaptchaEnterpriseToken;

  /**
   * Specifies whether this attestation is for use in a *limited use* (`true`)
   * or *session based* (`false`) context. To enable this attestation to be used
   * with the *replay protection* feature, set this to `true`. The default value
   * is `false`.
   *
   * @param bool $limitedUse
   */
  public function setLimitedUse($limitedUse)
  {
    $this->limitedUse = $limitedUse;
  }
  /**
   * @return bool
   */
  public function getLimitedUse()
  {
    return $this->limitedUse;
  }
  /**
   * Required. The reCAPTCHA token as returned by the [reCAPTCHA Enterprise
   * JavaScript API](https://cloud.google.com/recaptcha-
   * enterprise/docs/instrument-web-pages).
   *
   * @param string $recaptchaEnterpriseToken
   */
  public function setRecaptchaEnterpriseToken($recaptchaEnterpriseToken)
  {
    $this->recaptchaEnterpriseToken = $recaptchaEnterpriseToken;
  }
  /**
   * @return string
   */
  public function getRecaptchaEnterpriseToken()
  {
    return $this->recaptchaEnterpriseToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1ExchangeRecaptchaEnterpriseTokenRequest::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1ExchangeRecaptchaEnterpriseTokenRequest');
