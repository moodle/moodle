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

namespace Google\Service\Walletobjects;

class BoardingAndSeatingPolicy extends \Google\Model
{
  public const BOARDING_POLICY_BOARDING_POLICY_UNSPECIFIED = 'BOARDING_POLICY_UNSPECIFIED';
  public const BOARDING_POLICY_ZONE_BASED = 'ZONE_BASED';
  /**
   * Legacy alias for `ZONE_BASED`. Deprecated.
   *
   * @deprecated
   */
  public const BOARDING_POLICY_zoneBased = 'zoneBased';
  public const BOARDING_POLICY_GROUP_BASED = 'GROUP_BASED';
  /**
   * Legacy alias for `GROUP_BASED`. Deprecated.
   *
   * @deprecated
   */
  public const BOARDING_POLICY_groupBased = 'groupBased';
  public const BOARDING_POLICY_BOARDING_POLICY_OTHER = 'BOARDING_POLICY_OTHER';
  /**
   * Legacy alias for `BOARDING_POLICY_OTHER`. Deprecated.
   *
   * @deprecated
   */
  public const BOARDING_POLICY_boardingPolicyOther = 'boardingPolicyOther';
  public const SEAT_CLASS_POLICY_SEAT_CLASS_POLICY_UNSPECIFIED = 'SEAT_CLASS_POLICY_UNSPECIFIED';
  public const SEAT_CLASS_POLICY_CABIN_BASED = 'CABIN_BASED';
  /**
   * Legacy alias for `CABIN_BASED`. Deprecated.
   *
   * @deprecated
   */
  public const SEAT_CLASS_POLICY_cabinBased = 'cabinBased';
  public const SEAT_CLASS_POLICY_CLASS_BASED = 'CLASS_BASED';
  /**
   * Legacy alias for `CLASS_BASED`. Deprecated.
   *
   * @deprecated
   */
  public const SEAT_CLASS_POLICY_classBased = 'classBased';
  public const SEAT_CLASS_POLICY_TIER_BASED = 'TIER_BASED';
  /**
   * Legacy alias for `TIER_BASED`. Deprecated.
   *
   * @deprecated
   */
  public const SEAT_CLASS_POLICY_tierBased = 'tierBased';
  public const SEAT_CLASS_POLICY_SEAT_CLASS_POLICY_OTHER = 'SEAT_CLASS_POLICY_OTHER';
  /**
   * Legacy alias for `SEAT_CLASS_POLICY_OTHER`. Deprecated.
   *
   * @deprecated
   */
  public const SEAT_CLASS_POLICY_seatClassPolicyOther = 'seatClassPolicyOther';
  /**
   * Indicates the policy the airline uses for boarding. If unset, Google will
   * default to `zoneBased`.
   *
   * @var string
   */
  public $boardingPolicy;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#boardingAndSeatingPolicy"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * Seating policy which dictates how we display the seat class. If unset,
   * Google will default to `cabinBased`.
   *
   * @var string
   */
  public $seatClassPolicy;

  /**
   * Indicates the policy the airline uses for boarding. If unset, Google will
   * default to `zoneBased`.
   *
   * Accepted values: BOARDING_POLICY_UNSPECIFIED, ZONE_BASED, zoneBased,
   * GROUP_BASED, groupBased, BOARDING_POLICY_OTHER, boardingPolicyOther
   *
   * @param self::BOARDING_POLICY_* $boardingPolicy
   */
  public function setBoardingPolicy($boardingPolicy)
  {
    $this->boardingPolicy = $boardingPolicy;
  }
  /**
   * @return self::BOARDING_POLICY_*
   */
  public function getBoardingPolicy()
  {
    return $this->boardingPolicy;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#boardingAndSeatingPolicy"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Seating policy which dictates how we display the seat class. If unset,
   * Google will default to `cabinBased`.
   *
   * Accepted values: SEAT_CLASS_POLICY_UNSPECIFIED, CABIN_BASED, cabinBased,
   * CLASS_BASED, classBased, TIER_BASED, tierBased, SEAT_CLASS_POLICY_OTHER,
   * seatClassPolicyOther
   *
   * @param self::SEAT_CLASS_POLICY_* $seatClassPolicy
   */
  public function setSeatClassPolicy($seatClassPolicy)
  {
    $this->seatClassPolicy = $seatClassPolicy;
  }
  /**
   * @return self::SEAT_CLASS_POLICY_*
   */
  public function getSeatClassPolicy()
  {
    return $this->seatClassPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BoardingAndSeatingPolicy::class, 'Google_Service_Walletobjects_BoardingAndSeatingPolicy');
