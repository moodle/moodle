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

namespace Google\Service\MyBusinessLodging;

class GuestUnitFeatures extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BUNGALOW_OR_VILLA_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BUNGALOW_OR_VILLA_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BUNGALOW_OR_VILLA_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BUNGALOW_OR_VILLA_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CONNECTING_UNIT_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CONNECTING_UNIT_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CONNECTING_UNIT_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CONNECTING_UNIT_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const EXECUTIVE_FLOOR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const EXECUTIVE_FLOOR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const EXECUTIVE_FLOOR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const EXECUTIVE_FLOOR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MAX_OCCUPANTS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MAX_OCCUPANTS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MAX_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MAX_OCCUPANTS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PRIVATE_HOME_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PRIVATE_HOME_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PRIVATE_HOME_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PRIVATE_HOME_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SUITE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SUITE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SUITE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SUITE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default tier. Equivalent to STANDARD. Prefer using STANDARD directly.
   */
  public const TIER_UNIT_TIER_UNSPECIFIED = 'UNIT_TIER_UNSPECIFIED';
  /**
   * Standard unit. The predominant and most basic guestroom type available at
   * the hotel. All other guestroom types include the features/amenities of this
   * room, as well as additional features/amenities.
   */
  public const TIER_STANDARD_UNIT = 'STANDARD_UNIT';
  /**
   * Deluxe unit. A guestroom type that builds on the features of the standard
   * guestroom by offering additional amenities and/or more space, and/or views.
   * The room rate is higher than that of the standard room type. Also known as
   * Superior. Only allowed if another unit type is a standard tier.
   */
  public const TIER_DELUXE_UNIT = 'DELUXE_UNIT';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TIER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TIER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TIER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TIER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Bungalow or villa. An independent structure that is part of a hotel or
   * resort that is rented to one party for a vacation stay. The hotel or resort
   * may be completely comprised of bungalows or villas, or they may be one of
   * several guestroom options. Guests in the bungalows or villas most often
   * have the same, if not more, amenities and services offered to guests in
   * other guestroom types.
   *
   * @var bool
   */
  public $bungalowOrVilla;
  /**
   * Bungalow or villa exception.
   *
   * @var string
   */
  public $bungalowOrVillaException;
  /**
   * Connecting unit available. A guestroom type that features access to an
   * adjacent guestroom for the purpose of booking both rooms. Most often used
   * by families who need more than one room to accommodate the number of people
   * in their group.
   *
   * @var bool
   */
  public $connectingUnitAvailable;
  /**
   * Connecting unit available exception.
   *
   * @var string
   */
  public $connectingUnitAvailableException;
  /**
   * Executive floor. A floor of the hotel where the guestrooms are only
   * bookable by members of the hotel's frequent guest membership program.
   * Benefits of this room class include access to a designated lounge which may
   * or may not feature free breakfast, cocktails or other perks specific to
   * members of the program.
   *
   * @var bool
   */
  public $executiveFloor;
  /**
   * Executive floor exception.
   *
   * @var string
   */
  public $executiveFloorException;
  /**
   * Max adult occupants count. The total number of adult guests allowed to stay
   * overnight in the guestroom.
   *
   * @var int
   */
  public $maxAdultOccupantsCount;
  /**
   * Max adult occupants count exception.
   *
   * @var string
   */
  public $maxAdultOccupantsCountException;
  /**
   * Max child occupants count. The total number of children allowed to stay
   * overnight in the room.
   *
   * @var int
   */
  public $maxChildOccupantsCount;
  /**
   * Max child occupants count exception.
   *
   * @var string
   */
  public $maxChildOccupantsCountException;
  /**
   * Max occupants count. The total number of guests allowed to stay overnight
   * in the guestroom.
   *
   * @var int
   */
  public $maxOccupantsCount;
  /**
   * Max occupants count exception.
   *
   * @var string
   */
  public $maxOccupantsCountException;
  /**
   * Private home. A privately owned home (house, townhouse, apartment, cabin,
   * bungalow etc) that may or not serve as the owner's residence, but is rented
   * out in its entirety or by the room(s) to paying guest(s) for vacation
   * stays. Not for lease-based, long-term residency.
   *
   * @var bool
   */
  public $privateHome;
  /**
   * Private home exception.
   *
   * @var string
   */
  public $privateHomeException;
  /**
   * Suite. A guestroom category that implies both a bedroom area and a separate
   * living area. There may or may not be full walls and doors separating the
   * two areas, but regardless, they are very distinct. Does not mean a couch or
   * chair in a bedroom.
   *
   * @var bool
   */
  public $suite;
  /**
   * Suite exception.
   *
   * @var string
   */
  public $suiteException;
  /**
   * Tier. Classification of the unit based on available features/amenities. A
   * non-standard tier is only permitted if at least one other unit type falls
   * under the standard tier.
   *
   * @var string
   */
  public $tier;
  /**
   * Tier exception.
   *
   * @var string
   */
  public $tierException;
  protected $totalLivingAreasType = LivingArea::class;
  protected $totalLivingAreasDataType = '';
  protected $viewsType = ViewsFromUnit::class;
  protected $viewsDataType = '';

  /**
   * Bungalow or villa. An independent structure that is part of a hotel or
   * resort that is rented to one party for a vacation stay. The hotel or resort
   * may be completely comprised of bungalows or villas, or they may be one of
   * several guestroom options. Guests in the bungalows or villas most often
   * have the same, if not more, amenities and services offered to guests in
   * other guestroom types.
   *
   * @param bool $bungalowOrVilla
   */
  public function setBungalowOrVilla($bungalowOrVilla)
  {
    $this->bungalowOrVilla = $bungalowOrVilla;
  }
  /**
   * @return bool
   */
  public function getBungalowOrVilla()
  {
    return $this->bungalowOrVilla;
  }
  /**
   * Bungalow or villa exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BUNGALOW_OR_VILLA_EXCEPTION_* $bungalowOrVillaException
   */
  public function setBungalowOrVillaException($bungalowOrVillaException)
  {
    $this->bungalowOrVillaException = $bungalowOrVillaException;
  }
  /**
   * @return self::BUNGALOW_OR_VILLA_EXCEPTION_*
   */
  public function getBungalowOrVillaException()
  {
    return $this->bungalowOrVillaException;
  }
  /**
   * Connecting unit available. A guestroom type that features access to an
   * adjacent guestroom for the purpose of booking both rooms. Most often used
   * by families who need more than one room to accommodate the number of people
   * in their group.
   *
   * @param bool $connectingUnitAvailable
   */
  public function setConnectingUnitAvailable($connectingUnitAvailable)
  {
    $this->connectingUnitAvailable = $connectingUnitAvailable;
  }
  /**
   * @return bool
   */
  public function getConnectingUnitAvailable()
  {
    return $this->connectingUnitAvailable;
  }
  /**
   * Connecting unit available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CONNECTING_UNIT_AVAILABLE_EXCEPTION_* $connectingUnitAvailableException
   */
  public function setConnectingUnitAvailableException($connectingUnitAvailableException)
  {
    $this->connectingUnitAvailableException = $connectingUnitAvailableException;
  }
  /**
   * @return self::CONNECTING_UNIT_AVAILABLE_EXCEPTION_*
   */
  public function getConnectingUnitAvailableException()
  {
    return $this->connectingUnitAvailableException;
  }
  /**
   * Executive floor. A floor of the hotel where the guestrooms are only
   * bookable by members of the hotel's frequent guest membership program.
   * Benefits of this room class include access to a designated lounge which may
   * or may not feature free breakfast, cocktails or other perks specific to
   * members of the program.
   *
   * @param bool $executiveFloor
   */
  public function setExecutiveFloor($executiveFloor)
  {
    $this->executiveFloor = $executiveFloor;
  }
  /**
   * @return bool
   */
  public function getExecutiveFloor()
  {
    return $this->executiveFloor;
  }
  /**
   * Executive floor exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::EXECUTIVE_FLOOR_EXCEPTION_* $executiveFloorException
   */
  public function setExecutiveFloorException($executiveFloorException)
  {
    $this->executiveFloorException = $executiveFloorException;
  }
  /**
   * @return self::EXECUTIVE_FLOOR_EXCEPTION_*
   */
  public function getExecutiveFloorException()
  {
    return $this->executiveFloorException;
  }
  /**
   * Max adult occupants count. The total number of adult guests allowed to stay
   * overnight in the guestroom.
   *
   * @param int $maxAdultOccupantsCount
   */
  public function setMaxAdultOccupantsCount($maxAdultOccupantsCount)
  {
    $this->maxAdultOccupantsCount = $maxAdultOccupantsCount;
  }
  /**
   * @return int
   */
  public function getMaxAdultOccupantsCount()
  {
    return $this->maxAdultOccupantsCount;
  }
  /**
   * Max adult occupants count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_* $maxAdultOccupantsCountException
   */
  public function setMaxAdultOccupantsCountException($maxAdultOccupantsCountException)
  {
    $this->maxAdultOccupantsCountException = $maxAdultOccupantsCountException;
  }
  /**
   * @return self::MAX_ADULT_OCCUPANTS_COUNT_EXCEPTION_*
   */
  public function getMaxAdultOccupantsCountException()
  {
    return $this->maxAdultOccupantsCountException;
  }
  /**
   * Max child occupants count. The total number of children allowed to stay
   * overnight in the room.
   *
   * @param int $maxChildOccupantsCount
   */
  public function setMaxChildOccupantsCount($maxChildOccupantsCount)
  {
    $this->maxChildOccupantsCount = $maxChildOccupantsCount;
  }
  /**
   * @return int
   */
  public function getMaxChildOccupantsCount()
  {
    return $this->maxChildOccupantsCount;
  }
  /**
   * Max child occupants count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_* $maxChildOccupantsCountException
   */
  public function setMaxChildOccupantsCountException($maxChildOccupantsCountException)
  {
    $this->maxChildOccupantsCountException = $maxChildOccupantsCountException;
  }
  /**
   * @return self::MAX_CHILD_OCCUPANTS_COUNT_EXCEPTION_*
   */
  public function getMaxChildOccupantsCountException()
  {
    return $this->maxChildOccupantsCountException;
  }
  /**
   * Max occupants count. The total number of guests allowed to stay overnight
   * in the guestroom.
   *
   * @param int $maxOccupantsCount
   */
  public function setMaxOccupantsCount($maxOccupantsCount)
  {
    $this->maxOccupantsCount = $maxOccupantsCount;
  }
  /**
   * @return int
   */
  public function getMaxOccupantsCount()
  {
    return $this->maxOccupantsCount;
  }
  /**
   * Max occupants count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MAX_OCCUPANTS_COUNT_EXCEPTION_* $maxOccupantsCountException
   */
  public function setMaxOccupantsCountException($maxOccupantsCountException)
  {
    $this->maxOccupantsCountException = $maxOccupantsCountException;
  }
  /**
   * @return self::MAX_OCCUPANTS_COUNT_EXCEPTION_*
   */
  public function getMaxOccupantsCountException()
  {
    return $this->maxOccupantsCountException;
  }
  /**
   * Private home. A privately owned home (house, townhouse, apartment, cabin,
   * bungalow etc) that may or not serve as the owner's residence, but is rented
   * out in its entirety or by the room(s) to paying guest(s) for vacation
   * stays. Not for lease-based, long-term residency.
   *
   * @param bool $privateHome
   */
  public function setPrivateHome($privateHome)
  {
    $this->privateHome = $privateHome;
  }
  /**
   * @return bool
   */
  public function getPrivateHome()
  {
    return $this->privateHome;
  }
  /**
   * Private home exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PRIVATE_HOME_EXCEPTION_* $privateHomeException
   */
  public function setPrivateHomeException($privateHomeException)
  {
    $this->privateHomeException = $privateHomeException;
  }
  /**
   * @return self::PRIVATE_HOME_EXCEPTION_*
   */
  public function getPrivateHomeException()
  {
    return $this->privateHomeException;
  }
  /**
   * Suite. A guestroom category that implies both a bedroom area and a separate
   * living area. There may or may not be full walls and doors separating the
   * two areas, but regardless, they are very distinct. Does not mean a couch or
   * chair in a bedroom.
   *
   * @param bool $suite
   */
  public function setSuite($suite)
  {
    $this->suite = $suite;
  }
  /**
   * @return bool
   */
  public function getSuite()
  {
    return $this->suite;
  }
  /**
   * Suite exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SUITE_EXCEPTION_* $suiteException
   */
  public function setSuiteException($suiteException)
  {
    $this->suiteException = $suiteException;
  }
  /**
   * @return self::SUITE_EXCEPTION_*
   */
  public function getSuiteException()
  {
    return $this->suiteException;
  }
  /**
   * Tier. Classification of the unit based on available features/amenities. A
   * non-standard tier is only permitted if at least one other unit type falls
   * under the standard tier.
   *
   * Accepted values: UNIT_TIER_UNSPECIFIED, STANDARD_UNIT, DELUXE_UNIT
   *
   * @param self::TIER_* $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return self::TIER_*
   */
  public function getTier()
  {
    return $this->tier;
  }
  /**
   * Tier exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TIER_EXCEPTION_* $tierException
   */
  public function setTierException($tierException)
  {
    $this->tierException = $tierException;
  }
  /**
   * @return self::TIER_EXCEPTION_*
   */
  public function getTierException()
  {
    return $this->tierException;
  }
  /**
   * Features available in the living areas in the guest unit.
   *
   * @param LivingArea $totalLivingAreas
   */
  public function setTotalLivingAreas(LivingArea $totalLivingAreas)
  {
    $this->totalLivingAreas = $totalLivingAreas;
  }
  /**
   * @return LivingArea
   */
  public function getTotalLivingAreas()
  {
    return $this->totalLivingAreas;
  }
  /**
   * Views available from the guest unit itself.
   *
   * @param ViewsFromUnit $views
   */
  public function setViews(ViewsFromUnit $views)
  {
    $this->views = $views;
  }
  /**
   * @return ViewsFromUnit
   */
  public function getViews()
  {
    return $this->views;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestUnitFeatures::class, 'Google_Service_MyBusinessLodging_GuestUnitFeatures');
