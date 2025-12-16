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

class LocationPolicyLocation extends \Google\Model
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
  protected $constraintsType = LocationPolicyLocationConstraints::class;
  protected $constraintsDataType = '';
  /**
   * Preference for a given location. Set to either ALLOW orDENY.
   *
   * @var string
   */
  public $preference;

  /**
   * Constraints that the caller requires on the result distribution in this
   * zone.
   *
   * @param LocationPolicyLocationConstraints $constraints
   */
  public function setConstraints(LocationPolicyLocationConstraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return LocationPolicyLocationConstraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * Preference for a given location. Set to either ALLOW orDENY.
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
class_alias(LocationPolicyLocation::class, 'Google_Service_Compute_LocationPolicyLocation');
