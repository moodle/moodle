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

class FutureResourcesSpecLocationPolicyLocation extends \Google\Model
{
  /**
   * Location is allowed for use.
   */
  public const PREFERENCE_ALLOW = 'ALLOW';
  /**
   * Location is prohibited.
   */
  public const PREFERENCE_DENY = 'DENY';
  /**
   * Default value, unused.
   */
  public const PREFERENCE_PREFERENCE_UNSPECIFIED = 'PREFERENCE_UNSPECIFIED';
  /**
   * Preference for this location.
   *
   * @var string
   */
  public $preference;

  /**
   * Preference for this location.
   *
   * Accepted values: ALLOW, DENY, PREFERENCE_UNSPECIFIED
   *
   * @param self::PREFERENCE_* $preference
   */
  public function setPreference($preference)
  {
    $this->preference = $preference;
  }
  /**
   * @return self::PREFERENCE_*
   */
  public function getPreference()
  {
    return $this->preference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpecLocationPolicyLocation::class, 'Google_Service_Compute_FutureResourcesSpecLocationPolicyLocation');
