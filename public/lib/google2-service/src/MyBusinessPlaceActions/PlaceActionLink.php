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

class PlaceActionLink extends \Google\Model
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
   * Not specified.
   */
  public const PROVIDER_TYPE_PROVIDER_TYPE_UNSPECIFIED = 'PROVIDER_TYPE_UNSPECIFIED';
  /**
   * A 1P provider such as a merchant, or an agency on behalf of a merchant.
   */
  public const PROVIDER_TYPE_MERCHANT = 'MERCHANT';
  /**
   * A 3P aggregator, such as a `Reserve with Google` partner.
   */
  public const PROVIDER_TYPE_AGGREGATOR_3P = 'AGGREGATOR_3P';
  /**
   * Output only. The time when the place action link was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Indicates whether this link can be edited by the client.
   *
   * @var bool
   */
  public $isEditable;
  /**
   * Optional. Whether this link is preferred by the merchant. Only one link can
   * be marked as preferred per place action type at a location. If a future
   * request marks a different link as preferred for the same place action type,
   * then the current preferred link (if any exists) will lose its preference.
   *
   * @var bool
   */
  public $isPreferred;
  /**
   * Optional. The resource name, in the format
   * `locations/{location_id}/placeActionLinks/{place_action_link_id}`. The name
   * field will only be considered in UpdatePlaceActionLink and
   * DeletePlaceActionLink requests for updating and deleting links
   * respectively. However, it will be ignored in CreatePlaceActionLink request,
   * where `place_action_link_id` will be assigned by the server on successful
   * creation of a new link and returned as part of the response.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The type of place action that can be performed using this link.
   *
   * @var string
   */
  public $placeActionType;
  /**
   * Output only. Specifies the provider type.
   *
   * @var string
   */
  public $providerType;
  /**
   * Output only. The time when the place action link was last modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. The link uri. The same uri can be reused for different action
   * types across different locations. However, only one place action link is
   * allowed for each unique combination of (uri, place action type, location).
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The time when the place action link was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Indicates whether this link can be edited by the client.
   *
   * @param bool $isEditable
   */
  public function setIsEditable($isEditable)
  {
    $this->isEditable = $isEditable;
  }
  /**
   * @return bool
   */
  public function getIsEditable()
  {
    return $this->isEditable;
  }
  /**
   * Optional. Whether this link is preferred by the merchant. Only one link can
   * be marked as preferred per place action type at a location. If a future
   * request marks a different link as preferred for the same place action type,
   * then the current preferred link (if any exists) will lose its preference.
   *
   * @param bool $isPreferred
   */
  public function setIsPreferred($isPreferred)
  {
    $this->isPreferred = $isPreferred;
  }
  /**
   * @return bool
   */
  public function getIsPreferred()
  {
    return $this->isPreferred;
  }
  /**
   * Optional. The resource name, in the format
   * `locations/{location_id}/placeActionLinks/{place_action_link_id}`. The name
   * field will only be considered in UpdatePlaceActionLink and
   * DeletePlaceActionLink requests for updating and deleting links
   * respectively. However, it will be ignored in CreatePlaceActionLink request,
   * where `place_action_link_id` will be assigned by the server on successful
   * creation of a new link and returned as part of the response.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The type of place action that can be performed using this link.
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
  /**
   * Output only. Specifies the provider type.
   *
   * Accepted values: PROVIDER_TYPE_UNSPECIFIED, MERCHANT, AGGREGATOR_3P
   *
   * @param self::PROVIDER_TYPE_* $providerType
   */
  public function setProviderType($providerType)
  {
    $this->providerType = $providerType;
  }
  /**
   * @return self::PROVIDER_TYPE_*
   */
  public function getProviderType()
  {
    return $this->providerType;
  }
  /**
   * Output only. The time when the place action link was last modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Required. The link uri. The same uri can be reused for different action
   * types across different locations. However, only one place action link is
   * allowed for each unique combination of (uri, place action type, location).
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaceActionLink::class, 'Google_Service_MyBusinessPlaceActions_PlaceActionLink');
