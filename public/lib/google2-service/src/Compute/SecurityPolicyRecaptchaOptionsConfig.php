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

namespace Google\Service\Compute;

class SecurityPolicyRecaptchaOptionsConfig extends \Google\Model
{
  /**
   * An optional field to supply a reCAPTCHA site key to be used for all the
   * rules using the redirect action with the type of GOOGLE_RECAPTCHA under the
   * security policy. The specified site key needs to be created from the
   * reCAPTCHA API. The user is responsible for the validity of the specified
   * site key. If not specified, a Google-managed site key is used. This field
   * is only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @var string
   */
  public $redirectSiteKey;

  /**
   * An optional field to supply a reCAPTCHA site key to be used for all the
   * rules using the redirect action with the type of GOOGLE_RECAPTCHA under the
   * security policy. The specified site key needs to be created from the
   * reCAPTCHA API. The user is responsible for the validity of the specified
   * site key. If not specified, a Google-managed site key is used. This field
   * is only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @param string $redirectSiteKey
   */
  public function setRedirectSiteKey($redirectSiteKey)
  {
    $this->redirectSiteKey = $redirectSiteKey;
  }
  /**
   * @return string
   */
  public function getRedirectSiteKey()
  {
    return $this->redirectSiteKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRecaptchaOptionsConfig::class, 'Google_Service_Compute_SecurityPolicyRecaptchaOptionsConfig');
