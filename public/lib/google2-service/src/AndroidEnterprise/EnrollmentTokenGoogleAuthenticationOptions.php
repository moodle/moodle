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

namespace Google\Service\AndroidEnterprise;

class EnrollmentTokenGoogleAuthenticationOptions extends \Google\Model
{
  /**
   * The value is unused.
   */
  public const AUTHENTICATION_REQUIREMENT_authenticationRequirementUnspecified = 'authenticationRequirementUnspecified';
  /**
   * Google authentication is optional for the user. This means the user can
   * choose to skip Google authentication during enrollment.
   */
  public const AUTHENTICATION_REQUIREMENT_optional = 'optional';
  /**
   * Google authentication is required for the user. This means the user must
   * authenticate with a Google account to proceed.
   */
  public const AUTHENTICATION_REQUIREMENT_required = 'required';
  /**
   * [Optional] Specifies whether user should authenticate with Google during
   * enrollment. This setting, if specified,`GoogleAuthenticationSettings`
   * specified for the enterprise resource is ignored for devices enrolled with
   * this token.
   *
   * @var string
   */
  public $authenticationRequirement;
  /**
   * [Optional] Specifies the managed Google account that the user must use
   * during enrollment.`AuthenticationRequirement` must be set to`REQUIRED` if
   * this field is set.
   *
   * @var string
   */
  public $requiredAccountEmail;

  /**
   * [Optional] Specifies whether user should authenticate with Google during
   * enrollment. This setting, if specified,`GoogleAuthenticationSettings`
   * specified for the enterprise resource is ignored for devices enrolled with
   * this token.
   *
   * Accepted values: authenticationRequirementUnspecified, optional, required
   *
   * @param self::AUTHENTICATION_REQUIREMENT_* $authenticationRequirement
   */
  public function setAuthenticationRequirement($authenticationRequirement)
  {
    $this->authenticationRequirement = $authenticationRequirement;
  }
  /**
   * @return self::AUTHENTICATION_REQUIREMENT_*
   */
  public function getAuthenticationRequirement()
  {
    return $this->authenticationRequirement;
  }
  /**
   * [Optional] Specifies the managed Google account that the user must use
   * during enrollment.`AuthenticationRequirement` must be set to`REQUIRED` if
   * this field is set.
   *
   * @param string $requiredAccountEmail
   */
  public function setRequiredAccountEmail($requiredAccountEmail)
  {
    $this->requiredAccountEmail = $requiredAccountEmail;
  }
  /**
   * @return string
   */
  public function getRequiredAccountEmail()
  {
    return $this->requiredAccountEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnrollmentTokenGoogleAuthenticationOptions::class, 'Google_Service_AndroidEnterprise_EnrollmentTokenGoogleAuthenticationOptions');
