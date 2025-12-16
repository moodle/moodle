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

namespace Google\Service\Reseller;

class RenewalSettings extends \Google\Model
{
  /**
   * Identifies the resource as a subscription renewal setting. Value:
   * `subscriptions#renewalSettings`
   *
   * @var string
   */
  public $kind;
  /**
   * Renewal settings for the annual commitment plan. For more detailed
   * information, see renewal options in the administrator help center. When
   * renewing a subscription, the `renewalType` is a required property.
   *
   * @var string
   */
  public $renewalType;

  /**
   * Identifies the resource as a subscription renewal setting. Value:
   * `subscriptions#renewalSettings`
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Renewal settings for the annual commitment plan. For more detailed
   * information, see renewal options in the administrator help center. When
   * renewing a subscription, the `renewalType` is a required property.
   *
   * @param string $renewalType
   */
  public function setRenewalType($renewalType)
  {
    $this->renewalType = $renewalType;
  }
  /**
   * @return string
   */
  public function getRenewalType()
  {
    return $this->renewalType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenewalSettings::class, 'Google_Service_Reseller_RenewalSettings');
