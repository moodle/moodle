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

class SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions extends \Google\Collection
{
  protected $collection_key = 'sessionTokenSiteKeys';
  /**
   * A list of site keys to be used during the validation of reCAPTCHA action-
   * tokens. The provided site keys need to be created from reCAPTCHA API under
   * the same project where the security policy is created.
   *
   * @var string[]
   */
  public $actionTokenSiteKeys;
  /**
   * A list of site keys to be used during the validation of reCAPTCHA session-
   * tokens. The provided site keys need to be created from reCAPTCHA API under
   * the same project where the security policy is created.
   *
   * @var string[]
   */
  public $sessionTokenSiteKeys;

  /**
   * A list of site keys to be used during the validation of reCAPTCHA action-
   * tokens. The provided site keys need to be created from reCAPTCHA API under
   * the same project where the security policy is created.
   *
   * @param string[] $actionTokenSiteKeys
   */
  public function setActionTokenSiteKeys($actionTokenSiteKeys)
  {
    $this->actionTokenSiteKeys = $actionTokenSiteKeys;
  }
  /**
   * @return string[]
   */
  public function getActionTokenSiteKeys()
  {
    return $this->actionTokenSiteKeys;
  }
  /**
   * A list of site keys to be used during the validation of reCAPTCHA session-
   * tokens. The provided site keys need to be created from reCAPTCHA API under
   * the same project where the security policy is created.
   *
   * @param string[] $sessionTokenSiteKeys
   */
  public function setSessionTokenSiteKeys($sessionTokenSiteKeys)
  {
    $this->sessionTokenSiteKeys = $sessionTokenSiteKeys;
  }
  /**
   * @return string[]
   */
  public function getSessionTokenSiteKeys()
  {
    return $this->sessionTokenSiteKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions::class, 'Google_Service_Compute_SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions');
