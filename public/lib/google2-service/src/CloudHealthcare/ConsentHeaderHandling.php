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

namespace Google\Service\CloudHealthcare;

class ConsentHeaderHandling extends \Google\Model
{
  /**
   * If not specified, the default value `PERMIT_EMPTY_SCOPE` is used.
   */
  public const PROFILE_SCOPE_PROFILE_UNSPECIFIED = 'SCOPE_PROFILE_UNSPECIFIED';
  /**
   * When no consent scopes are provided (for example, if there's an empty or
   * missing header), then consent check is disabled, similar to when
   * `access_enforced` is `false`. You can use audit logs to differentiate these
   * two cases by looking at the value of `protopayload.metadata.consentMode`.
   * If consents scopes are present, they must be valid and within the allowed
   * limits, otherwise the request will be rejected with a `4xx` code.
   */
  public const PROFILE_PERMIT_EMPTY_SCOPE = 'PERMIT_EMPTY_SCOPE';
  /**
   * The consent header must be non-empty when performing read and search
   * operations, otherwise the request is rejected with a `4xx` code.
   * Additionally, invalid consent scopes or scopes exceeding the allowed limits
   * are rejected.
   */
  public const PROFILE_REQUIRED_ON_READ = 'REQUIRED_ON_READ';
  /**
   * Optional. Specifies the default server behavior when the header is empty.
   * If not specified, the `ScopeProfile.PERMIT_EMPTY_SCOPE` option is used.
   *
   * @var string
   */
  public $profile;

  /**
   * Optional. Specifies the default server behavior when the header is empty.
   * If not specified, the `ScopeProfile.PERMIT_EMPTY_SCOPE` option is used.
   *
   * Accepted values: SCOPE_PROFILE_UNSPECIFIED, PERMIT_EMPTY_SCOPE,
   * REQUIRED_ON_READ
   *
   * @param self::PROFILE_* $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return self::PROFILE_*
   */
  public function getProfile()
  {
    return $this->profile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsentHeaderHandling::class, 'Google_Service_CloudHealthcare_ConsentHeaderHandling');
