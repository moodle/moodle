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

class SecurityPolicyRuleRedirectOptions extends \Google\Model
{
  public const TYPE_EXTERNAL_302 = 'EXTERNAL_302';
  public const TYPE_GOOGLE_RECAPTCHA = 'GOOGLE_RECAPTCHA';
  /**
   * Target for the redirect action. This is required if the type is
   * EXTERNAL_302 and cannot be specified for GOOGLE_RECAPTCHA.
   *
   * @var string
   */
  public $target;
  /**
   * Type of the redirect action. Possible values are:        -
   * GOOGLE_RECAPTCHA: redirect to reCAPTCHA for manual    challenge assessment.
   * - EXTERNAL_302: redirect to a different URL via a 302    response.
   *
   * @var string
   */
  public $type;

  /**
   * Target for the redirect action. This is required if the type is
   * EXTERNAL_302 and cannot be specified for GOOGLE_RECAPTCHA.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Type of the redirect action. Possible values are:        -
   * GOOGLE_RECAPTCHA: redirect to reCAPTCHA for manual    challenge assessment.
   * - EXTERNAL_302: redirect to a different URL via a 302    response.
   *
   * Accepted values: EXTERNAL_302, GOOGLE_RECAPTCHA
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleRedirectOptions::class, 'Google_Service_Compute_SecurityPolicyRuleRedirectOptions');
