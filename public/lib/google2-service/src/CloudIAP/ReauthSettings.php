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

namespace Google\Service\CloudIAP;

class ReauthSettings extends \Google\Model
{
  /**
   * Reauthentication disabled.
   */
  public const METHOD_METHOD_UNSPECIFIED = 'METHOD_UNSPECIFIED';
  /**
   * Prompts the user to log in again.
   */
  public const METHOD_LOGIN = 'LOGIN';
  /**
   * @deprecated
   */
  public const METHOD_PASSWORD = 'PASSWORD';
  /**
   * User must use their secure key 2nd factor device.
   */
  public const METHOD_SECURE_KEY = 'SECURE_KEY';
  /**
   * User can use any enabled 2nd factor.
   */
  public const METHOD_ENROLLED_SECOND_FACTORS = 'ENROLLED_SECOND_FACTORS';
  /**
   * Default value. This value is unused.
   */
  public const POLICY_TYPE_POLICY_TYPE_UNSPECIFIED = 'POLICY_TYPE_UNSPECIFIED';
  /**
   * This policy acts as a minimum to other policies, lower in the hierarchy.
   * Effective policy may only be the same or stricter.
   */
  public const POLICY_TYPE_MINIMUM = 'MINIMUM';
  /**
   * This policy acts as a default if no other reauth policy is set.
   */
  public const POLICY_TYPE_DEFAULT = 'DEFAULT';
  /**
   * Optional. Reauth session lifetime, how long before a user has to
   * reauthenticate again.
   *
   * @var string
   */
  public $maxAge;
  /**
   * Optional. Reauth method requested.
   *
   * @var string
   */
  public $method;
  /**
   * Optional. How IAP determines the effective policy in cases of hierarchical
   * policies. Policies are merged from higher in the hierarchy to lower in the
   * hierarchy.
   *
   * @var string
   */
  public $policyType;

  /**
   * Optional. Reauth session lifetime, how long before a user has to
   * reauthenticate again.
   *
   * @param string $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
  }
  /**
   * @return string
   */
  public function getMaxAge()
  {
    return $this->maxAge;
  }
  /**
   * Optional. Reauth method requested.
   *
   * Accepted values: METHOD_UNSPECIFIED, LOGIN, PASSWORD, SECURE_KEY,
   * ENROLLED_SECOND_FACTORS
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Optional. How IAP determines the effective policy in cases of hierarchical
   * policies. Policies are merged from higher in the hierarchy to lower in the
   * hierarchy.
   *
   * Accepted values: POLICY_TYPE_UNSPECIFIED, MINIMUM, DEFAULT
   *
   * @param self::POLICY_TYPE_* $policyType
   */
  public function setPolicyType($policyType)
  {
    $this->policyType = $policyType;
  }
  /**
   * @return self::POLICY_TYPE_*
   */
  public function getPolicyType()
  {
    return $this->policyType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReauthSettings::class, 'Google_Service_CloudIAP_ReauthSettings');
