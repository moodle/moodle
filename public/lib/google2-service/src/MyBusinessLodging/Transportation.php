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

class Transportation extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const AIRPORT_SHUTTLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const AIRPORT_SHUTTLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const AIRPORT_SHUTTLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const AIRPORT_SHUTTLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CAR_RENTAL_ON_PROPERTY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CAR_RENTAL_ON_PROPERTY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CAR_RENTAL_ON_PROPERTY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CAR_RENTAL_ON_PROPERTY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_AIRPORT_SHUTTLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_AIRPORT_SHUTTLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_AIRPORT_SHUTTLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_AIRPORT_SHUTTLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_PRIVATE_CAR_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_PRIVATE_CAR_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_PRIVATE_CAR_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_PRIVATE_CAR_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LOCAL_SHUTTLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LOCAL_SHUTTLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LOCAL_SHUTTLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LOCAL_SHUTTLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PRIVATE_CAR_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PRIVATE_CAR_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PRIVATE_CAR_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PRIVATE_CAR_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TRANSFER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TRANSFER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TRANSFER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TRANSFER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Airport shuttle. The hotel provides guests with a chauffeured van or bus to
   * and from the airport. Can be free or for a fee. Guests may share the
   * vehicle with other guests unknown to them. Applies if the hotel has a
   * third-party shuttle service (office/desk etc.) within the hotel. As long as
   * hotel provides this service, it doesn't matter if it's directly with them
   * or a third party they work with. Does not apply if guest has to coordinate
   * with an entity outside/other than the hotel.
   *
   * @var bool
   */
  public $airportShuttle;
  /**
   * Airport shuttle exception.
   *
   * @var string
   */
  public $airportShuttleException;
  /**
   * Car rental on property. A branch of a rental car company with a processing
   * desk in the hotel. Available cars for rent may be awaiting at the hotel or
   * in a nearby lot.
   *
   * @var bool
   */
  public $carRentalOnProperty;
  /**
   * Car rental on property exception.
   *
   * @var string
   */
  public $carRentalOnPropertyException;
  /**
   * Free airport shuttle. Airport shuttle is free to guests. Must be free to
   * all guests without any conditions.
   *
   * @var bool
   */
  public $freeAirportShuttle;
  /**
   * Free airport shuttle exception.
   *
   * @var string
   */
  public $freeAirportShuttleException;
  /**
   * Free private car service. Private chauffeured car service is free to
   * guests.
   *
   * @var bool
   */
  public $freePrivateCarService;
  /**
   * Free private car service exception.
   *
   * @var string
   */
  public $freePrivateCarServiceException;
  /**
   * Local shuttle. A car, van or bus provided by the hotel to transport guests
   * to destinations within a specified range of distance around the hotel.
   * Usually shopping and/or convention centers, downtown districts, or beaches.
   * Can be free or for a fee.
   *
   * @var bool
   */
  public $localShuttle;
  /**
   * Local shuttle exception.
   *
   * @var string
   */
  public $localShuttleException;
  /**
   * Private car service. Hotel provides a private chauffeured car to transport
   * guests to destinations. Passengers in the car are either alone or are known
   * to one another and have requested the car together. Service can be free or
   * for a fee and travel distance is usually limited to a specific range. Not a
   * taxi.
   *
   * @var bool
   */
  public $privateCarService;
  /**
   * Private car service exception.
   *
   * @var string
   */
  public $privateCarServiceException;
  /**
   * Transfer. Hotel provides a shuttle service or car service to take guests to
   * and from the nearest airport or train station. Can be free or for a fee.
   * Guests may share the vehicle with other guests unknown to them.
   *
   * @var bool
   */
  public $transfer;
  /**
   * Transfer exception.
   *
   * @var string
   */
  public $transferException;

  /**
   * Airport shuttle. The hotel provides guests with a chauffeured van or bus to
   * and from the airport. Can be free or for a fee. Guests may share the
   * vehicle with other guests unknown to them. Applies if the hotel has a
   * third-party shuttle service (office/desk etc.) within the hotel. As long as
   * hotel provides this service, it doesn't matter if it's directly with them
   * or a third party they work with. Does not apply if guest has to coordinate
   * with an entity outside/other than the hotel.
   *
   * @param bool $airportShuttle
   */
  public function setAirportShuttle($airportShuttle)
  {
    $this->airportShuttle = $airportShuttle;
  }
  /**
   * @return bool
   */
  public function getAirportShuttle()
  {
    return $this->airportShuttle;
  }
  /**
   * Airport shuttle exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::AIRPORT_SHUTTLE_EXCEPTION_* $airportShuttleException
   */
  public function setAirportShuttleException($airportShuttleException)
  {
    $this->airportShuttleException = $airportShuttleException;
  }
  /**
   * @return self::AIRPORT_SHUTTLE_EXCEPTION_*
   */
  public function getAirportShuttleException()
  {
    return $this->airportShuttleException;
  }
  /**
   * Car rental on property. A branch of a rental car company with a processing
   * desk in the hotel. Available cars for rent may be awaiting at the hotel or
   * in a nearby lot.
   *
   * @param bool $carRentalOnProperty
   */
  public function setCarRentalOnProperty($carRentalOnProperty)
  {
    $this->carRentalOnProperty = $carRentalOnProperty;
  }
  /**
   * @return bool
   */
  public function getCarRentalOnProperty()
  {
    return $this->carRentalOnProperty;
  }
  /**
   * Car rental on property exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CAR_RENTAL_ON_PROPERTY_EXCEPTION_* $carRentalOnPropertyException
   */
  public function setCarRentalOnPropertyException($carRentalOnPropertyException)
  {
    $this->carRentalOnPropertyException = $carRentalOnPropertyException;
  }
  /**
   * @return self::CAR_RENTAL_ON_PROPERTY_EXCEPTION_*
   */
  public function getCarRentalOnPropertyException()
  {
    return $this->carRentalOnPropertyException;
  }
  /**
   * Free airport shuttle. Airport shuttle is free to guests. Must be free to
   * all guests without any conditions.
   *
   * @param bool $freeAirportShuttle
   */
  public function setFreeAirportShuttle($freeAirportShuttle)
  {
    $this->freeAirportShuttle = $freeAirportShuttle;
  }
  /**
   * @return bool
   */
  public function getFreeAirportShuttle()
  {
    return $this->freeAirportShuttle;
  }
  /**
   * Free airport shuttle exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_AIRPORT_SHUTTLE_EXCEPTION_* $freeAirportShuttleException
   */
  public function setFreeAirportShuttleException($freeAirportShuttleException)
  {
    $this->freeAirportShuttleException = $freeAirportShuttleException;
  }
  /**
   * @return self::FREE_AIRPORT_SHUTTLE_EXCEPTION_*
   */
  public function getFreeAirportShuttleException()
  {
    return $this->freeAirportShuttleException;
  }
  /**
   * Free private car service. Private chauffeured car service is free to
   * guests.
   *
   * @param bool $freePrivateCarService
   */
  public function setFreePrivateCarService($freePrivateCarService)
  {
    $this->freePrivateCarService = $freePrivateCarService;
  }
  /**
   * @return bool
   */
  public function getFreePrivateCarService()
  {
    return $this->freePrivateCarService;
  }
  /**
   * Free private car service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_PRIVATE_CAR_SERVICE_EXCEPTION_* $freePrivateCarServiceException
   */
  public function setFreePrivateCarServiceException($freePrivateCarServiceException)
  {
    $this->freePrivateCarServiceException = $freePrivateCarServiceException;
  }
  /**
   * @return self::FREE_PRIVATE_CAR_SERVICE_EXCEPTION_*
   */
  public function getFreePrivateCarServiceException()
  {
    return $this->freePrivateCarServiceException;
  }
  /**
   * Local shuttle. A car, van or bus provided by the hotel to transport guests
   * to destinations within a specified range of distance around the hotel.
   * Usually shopping and/or convention centers, downtown districts, or beaches.
   * Can be free or for a fee.
   *
   * @param bool $localShuttle
   */
  public function setLocalShuttle($localShuttle)
  {
    $this->localShuttle = $localShuttle;
  }
  /**
   * @return bool
   */
  public function getLocalShuttle()
  {
    return $this->localShuttle;
  }
  /**
   * Local shuttle exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LOCAL_SHUTTLE_EXCEPTION_* $localShuttleException
   */
  public function setLocalShuttleException($localShuttleException)
  {
    $this->localShuttleException = $localShuttleException;
  }
  /**
   * @return self::LOCAL_SHUTTLE_EXCEPTION_*
   */
  public function getLocalShuttleException()
  {
    return $this->localShuttleException;
  }
  /**
   * Private car service. Hotel provides a private chauffeured car to transport
   * guests to destinations. Passengers in the car are either alone or are known
   * to one another and have requested the car together. Service can be free or
   * for a fee and travel distance is usually limited to a specific range. Not a
   * taxi.
   *
   * @param bool $privateCarService
   */
  public function setPrivateCarService($privateCarService)
  {
    $this->privateCarService = $privateCarService;
  }
  /**
   * @return bool
   */
  public function getPrivateCarService()
  {
    return $this->privateCarService;
  }
  /**
   * Private car service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PRIVATE_CAR_SERVICE_EXCEPTION_* $privateCarServiceException
   */
  public function setPrivateCarServiceException($privateCarServiceException)
  {
    $this->privateCarServiceException = $privateCarServiceException;
  }
  /**
   * @return self::PRIVATE_CAR_SERVICE_EXCEPTION_*
   */
  public function getPrivateCarServiceException()
  {
    return $this->privateCarServiceException;
  }
  /**
   * Transfer. Hotel provides a shuttle service or car service to take guests to
   * and from the nearest airport or train station. Can be free or for a fee.
   * Guests may share the vehicle with other guests unknown to them.
   *
   * @param bool $transfer
   */
  public function setTransfer($transfer)
  {
    $this->transfer = $transfer;
  }
  /**
   * @return bool
   */
  public function getTransfer()
  {
    return $this->transfer;
  }
  /**
   * Transfer exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TRANSFER_EXCEPTION_* $transferException
   */
  public function setTransferException($transferException)
  {
    $this->transferException = $transferException;
  }
  /**
   * @return self::TRANSFER_EXCEPTION_*
   */
  public function getTransferException()
  {
    return $this->transferException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Transportation::class, 'Google_Service_MyBusinessLodging_Transportation');
