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

namespace Google\Service\SQLAdmin;

class PasswordValidationPolicy extends \Google\Model
{
  /**
   * Complexity check is not specified.
   */
  public const COMPLEXITY_COMPLEXITY_UNSPECIFIED = 'COMPLEXITY_UNSPECIFIED';
  /**
   * A combination of lowercase, uppercase, numeric, and non-alphanumeric
   * characters.
   */
  public const COMPLEXITY_COMPLEXITY_DEFAULT = 'COMPLEXITY_DEFAULT';
  /**
   * The complexity of the password.
   *
   * @var string
   */
  public $complexity;
  /**
   * This field is deprecated and will be removed in a future version of the
   * API.
   *
   * @deprecated
   * @var bool
   */
  public $disallowCompromisedCredentials;
  /**
   * Disallow username as a part of the password.
   *
   * @var bool
   */
  public $disallowUsernameSubstring;
  /**
   * Whether to enable the password policy or not. When enabled, passwords must
   * meet complexity requirements. Keep this policy enabled to help prevent
   * unauthorized access. Disabling this policy allows weak passwords.
   *
   * @var bool
   */
  public $enablePasswordPolicy;
  /**
   * Minimum number of characters allowed.
   *
   * @var int
   */
  public $minLength;
  /**
   * Minimum interval after which the password can be changed. This flag is only
   * supported for PostgreSQL.
   *
   * @var string
   */
  public $passwordChangeInterval;
  /**
   * Number of previous passwords that cannot be reused.
   *
   * @var int
   */
  public $reuseInterval;

  /**
   * The complexity of the password.
   *
   * Accepted values: COMPLEXITY_UNSPECIFIED, COMPLEXITY_DEFAULT
   *
   * @param self::COMPLEXITY_* $complexity
   */
  public function setComplexity($complexity)
  {
    $this->complexity = $complexity;
  }
  /**
   * @return self::COMPLEXITY_*
   */
  public function getComplexity()
  {
    return $this->complexity;
  }
  /**
   * This field is deprecated and will be removed in a future version of the
   * API.
   *
   * @deprecated
   * @param bool $disallowCompromisedCredentials
   */
  public function setDisallowCompromisedCredentials($disallowCompromisedCredentials)
  {
    $this->disallowCompromisedCredentials = $disallowCompromisedCredentials;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDisallowCompromisedCredentials()
  {
    return $this->disallowCompromisedCredentials;
  }
  /**
   * Disallow username as a part of the password.
   *
   * @param bool $disallowUsernameSubstring
   */
  public function setDisallowUsernameSubstring($disallowUsernameSubstring)
  {
    $this->disallowUsernameSubstring = $disallowUsernameSubstring;
  }
  /**
   * @return bool
   */
  public function getDisallowUsernameSubstring()
  {
    return $this->disallowUsernameSubstring;
  }
  /**
   * Whether to enable the password policy or not. When enabled, passwords must
   * meet complexity requirements. Keep this policy enabled to help prevent
   * unauthorized access. Disabling this policy allows weak passwords.
   *
   * @param bool $enablePasswordPolicy
   */
  public function setEnablePasswordPolicy($enablePasswordPolicy)
  {
    $this->enablePasswordPolicy = $enablePasswordPolicy;
  }
  /**
   * @return bool
   */
  public function getEnablePasswordPolicy()
  {
    return $this->enablePasswordPolicy;
  }
  /**
   * Minimum number of characters allowed.
   *
   * @param int $minLength
   */
  public function setMinLength($minLength)
  {
    $this->minLength = $minLength;
  }
  /**
   * @return int
   */
  public function getMinLength()
  {
    return $this->minLength;
  }
  /**
   * Minimum interval after which the password can be changed. This flag is only
   * supported for PostgreSQL.
   *
   * @param string $passwordChangeInterval
   */
  public function setPasswordChangeInterval($passwordChangeInterval)
  {
    $this->passwordChangeInterval = $passwordChangeInterval;
  }
  /**
   * @return string
   */
  public function getPasswordChangeInterval()
  {
    return $this->passwordChangeInterval;
  }
  /**
   * Number of previous passwords that cannot be reused.
   *
   * @param int $reuseInterval
   */
  public function setReuseInterval($reuseInterval)
  {
    $this->reuseInterval = $reuseInterval;
  }
  /**
   * @return int
   */
  public function getReuseInterval()
  {
    return $this->reuseInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PasswordValidationPolicy::class, 'Google_Service_SQLAdmin_PasswordValidationPolicy');
