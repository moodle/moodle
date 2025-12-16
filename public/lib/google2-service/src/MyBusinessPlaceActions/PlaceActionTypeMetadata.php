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

namespace Google\Service\MyBusinessPlaceActions;

class PlaceActionTypeMetadata extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PLACE_ACTION_TYPE_PLACE_ACTION_TYPE_UNSPECIFIED = 'PLACE_ACTION_TYPE_UNSPECIFIED';
  /**
   * The action type is booking an appointment.
   */
  public const PLACE_ACTION_TYPE_APPOINTMENT = 'APPOINTMENT';
  /**
   * The action type is booking an online appointment.
   */
  public const PLACE_ACTION_TYPE_ONLINE_APPOINTMENT = 'ONLINE_APPOINTMENT';
  /**
   * The action type is making a dining reservation.
   */
  public const PLACE_ACTION_TYPE_DINING_RESERVATION = 'DINING_RESERVATION';
  /**
   * The action type is ordering food for delivery and/or takeout.
   */
  public const PLACE_ACTION_TYPE_FOOD_ORDERING = 'FOOD_ORDERING';
  /**
   * The action type is ordering food for delivery.
   */
  public const PLACE_ACTION_TYPE_FOOD_DELIVERY = 'FOOD_DELIVERY';
  /**
   * The action type is ordering food for takeout.
   */
  public const PLACE_ACTION_TYPE_FOOD_TAKEOUT = 'FOOD_TAKEOUT';
  /**
   * The action type is shopping, that can be delivery and/or pickup.
   */
  public const PLACE_ACTION_TYPE_SHOP_ONLINE = 'SHOP_ONLINE';
  /**
   * The action type is booking an appointment with a Solopneuer partner.
   */
  public const PLACE_ACTION_TYPE_SOLOPRENEUR_APPOINTMENT = 'SOLOPRENEUR_APPOINTMENT';
  /**
   * The localized display name for the attribute, if available; otherwise, the
   * English display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The place action type.
   *
   * @var string
   */
  public $placeActionType;

  /**
   * The localized display name for the attribute, if available; otherwise, the
   * English display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The place action type.
   *
   * Accepted values: PLACE_ACTION_TYPE_UNSPECIFIED, APPOINTMENT,
   * ONLINE_APPOINTMENT, DINING_RESERVATION, FOOD_ORDERING, FOOD_DELIVERY,
   * FOOD_TAKEOUT, SHOP_ONLINE, SOLOPRENEUR_APPOINTMENT
   *
   * @param self::PLACE_ACTION_TYPE_* $placeActionType
   */
  public function setPlaceActionType($placeActionType)
  {
    $this->placeActionType = $placeActionType;
  }
  /**
   * @return self::PLACE_ACTION_TYPE_*
   */
  public function getPlaceActionType()
  {
    return $this->placeActionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaceActionTypeMetadata::class, 'Google_Service_MyBusinessPlaceActions_PlaceActionTypeMetadata');
