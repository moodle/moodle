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

class Accessibility extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MOBILITY_ACCESSIBLE_POOL_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MOBILITY_ACCESSIBLE_POOL_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MOBILITY_ACCESSIBLE_POOL_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MOBILITY_ACCESSIBLE_POOL_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Mobility accessible. Throughout the property there are physical adaptations
   * to ease the stay of a person in a wheelchair, such as auto-opening doors,
   * wide elevators, wide bathrooms or ramps.
   *
   * @var bool
   */
  public $mobilityAccessible;
  /**
   * Mobility accessible elevator. A lift that transports people from one level
   * to another and is built to accommodate a wheelchair-using passenger owing
   * to the width of its doors and placement of call buttons.
   *
   * @var bool
   */
  public $mobilityAccessibleElevator;
  /**
   * Mobility accessible elevator exception.
   *
   * @var string
   */
  public $mobilityAccessibleElevatorException;
  /**
   * Mobility accessible exception.
   *
   * @var string
   */
  public $mobilityAccessibleException;
  /**
   * Mobility accessible parking. The presence of a marked, designated area of
   * prescribed size in which only registered, labeled vehicles transporting a
   * person with physical challenges may park.
   *
   * @var bool
   */
  public $mobilityAccessibleParking;
  /**
   * Mobility accessible parking exception.
   *
   * @var string
   */
  public $mobilityAccessibleParkingException;
  /**
   * Mobility accessible pool. A swimming pool equipped with a mechanical chair
   * that can be lowered and raised for the purpose of moving physically
   * challenged guests into and out of the pool. May be powered by electricity
   * or water. Also known as pool lift.
   *
   * @var bool
   */
  public $mobilityAccessiblePool;
  /**
   * Mobility accessible pool exception.
   *
   * @var string
   */
  public $mobilityAccessiblePoolException;

  /**
   * Mobility accessible. Throughout the property there are physical adaptations
   * to ease the stay of a person in a wheelchair, such as auto-opening doors,
   * wide elevators, wide bathrooms or ramps.
   *
   * @param bool $mobilityAccessible
   */
  public function setMobilityAccessible($mobilityAccessible)
  {
    $this->mobilityAccessible = $mobilityAccessible;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessible()
  {
    return $this->mobilityAccessible;
  }
  /**
   * Mobility accessible elevator. A lift that transports people from one level
   * to another and is built to accommodate a wheelchair-using passenger owing
   * to the width of its doors and placement of call buttons.
   *
   * @param bool $mobilityAccessibleElevator
   */
  public function setMobilityAccessibleElevator($mobilityAccessibleElevator)
  {
    $this->mobilityAccessibleElevator = $mobilityAccessibleElevator;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleElevator()
  {
    return $this->mobilityAccessibleElevator;
  }
  /**
   * Mobility accessible elevator exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_* $mobilityAccessibleElevatorException
   */
  public function setMobilityAccessibleElevatorException($mobilityAccessibleElevatorException)
  {
    $this->mobilityAccessibleElevatorException = $mobilityAccessibleElevatorException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_ELEVATOR_EXCEPTION_*
   */
  public function getMobilityAccessibleElevatorException()
  {
    return $this->mobilityAccessibleElevatorException;
  }
  /**
   * Mobility accessible exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_EXCEPTION_* $mobilityAccessibleException
   */
  public function setMobilityAccessibleException($mobilityAccessibleException)
  {
    $this->mobilityAccessibleException = $mobilityAccessibleException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_EXCEPTION_*
   */
  public function getMobilityAccessibleException()
  {
    return $this->mobilityAccessibleException;
  }
  /**
   * Mobility accessible parking. The presence of a marked, designated area of
   * prescribed size in which only registered, labeled vehicles transporting a
   * person with physical challenges may park.
   *
   * @param bool $mobilityAccessibleParking
   */
  public function setMobilityAccessibleParking($mobilityAccessibleParking)
  {
    $this->mobilityAccessibleParking = $mobilityAccessibleParking;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessibleParking()
  {
    return $this->mobilityAccessibleParking;
  }
  /**
   * Mobility accessible parking exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_* $mobilityAccessibleParkingException
   */
  public function setMobilityAccessibleParkingException($mobilityAccessibleParkingException)
  {
    $this->mobilityAccessibleParkingException = $mobilityAccessibleParkingException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_PARKING_EXCEPTION_*
   */
  public function getMobilityAccessibleParkingException()
  {
    return $this->mobilityAccessibleParkingException;
  }
  /**
   * Mobility accessible pool. A swimming pool equipped with a mechanical chair
   * that can be lowered and raised for the purpose of moving physically
   * challenged guests into and out of the pool. May be powered by electricity
   * or water. Also known as pool lift.
   *
   * @param bool $mobilityAccessiblePool
   */
  public function setMobilityAccessiblePool($mobilityAccessiblePool)
  {
    $this->mobilityAccessiblePool = $mobilityAccessiblePool;
  }
  /**
   * @return bool
   */
  public function getMobilityAccessiblePool()
  {
    return $this->mobilityAccessiblePool;
  }
  /**
   * Mobility accessible pool exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MOBILITY_ACCESSIBLE_POOL_EXCEPTION_* $mobilityAccessiblePoolException
   */
  public function setMobilityAccessiblePoolException($mobilityAccessiblePoolException)
  {
    $this->mobilityAccessiblePoolException = $mobilityAccessiblePoolException;
  }
  /**
   * @return self::MOBILITY_ACCESSIBLE_POOL_EXCEPTION_*
   */
  public function getMobilityAccessiblePoolException()
  {
    return $this->mobilityAccessiblePoolException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accessibility::class, 'Google_Service_MyBusinessLodging_Accessibility');
