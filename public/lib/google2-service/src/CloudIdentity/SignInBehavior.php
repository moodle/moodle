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

namespace Google\Service\CloudIdentity;

class SignInBehavior extends \Google\Model
{
  /**
   * Default and means "always"
   */
  public const REDIRECT_CONDITION_REDIRECT_CONDITION_UNSPECIFIED = 'REDIRECT_CONDITION_UNSPECIFIED';
  /**
   * Sign-in flows where the user is prompted for their identity will not
   * redirect to the IdP (so the user will most likely be prompted by Google for
   * a password), but special flows like IdP-initiated SAML and sign-in
   * following automatic redirection to the IdP by domain-specific service URLs
   * will accept the IdP's assertion of the user's identity.
   */
  public const REDIRECT_CONDITION_NEVER = 'NEVER';
  /**
   * When to redirect sign-ins to the IdP.
   *
   * @var string
   */
  public $redirectCondition;

  /**
   * When to redirect sign-ins to the IdP.
   *
   * Accepted values: REDIRECT_CONDITION_UNSPECIFIED, NEVER
   *
   * @param self::REDIRECT_CONDITION_* $redirectCondition
   */
  public function setRedirectCondition($redirectCondition)
  {
    $this->redirectCondition = $redirectCondition;
  }
  /**
   * @return self::REDIRECT_CONDITION_*
   */
  public function getRedirectCondition()
  {
    return $this->redirectCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignInBehavior::class, 'Google_Service_CloudIdentity_SignInBehavior');
