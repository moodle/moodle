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

class SecurityPolicyRuleMatcherExprOptions extends \Google\Model
{
  protected $recaptchaOptionsType = SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions::class;
  protected $recaptchaOptionsDataType = '';

  /**
   * reCAPTCHA configuration options to be applied for the rule. If the rule
   * does not evaluate reCAPTCHA tokens, this field has no effect.
   *
   * @param SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions $recaptchaOptions
   */
  public function setRecaptchaOptions(SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions $recaptchaOptions)
  {
    $this->recaptchaOptions = $recaptchaOptions;
  }
  /**
   * @return SecurityPolicyRuleMatcherExprOptionsRecaptchaOptions
   */
  public function getRecaptchaOptions()
  {
    return $this->recaptchaOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleMatcherExprOptions::class, 'Google_Service_Compute_SecurityPolicyRuleMatcherExprOptions');
