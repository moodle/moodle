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

class Parking extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_PARKING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_PARKING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_PARKING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_PARKING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_SELF_PARKING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_SELF_PARKING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_SELF_PARKING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_SELF_PARKING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FREE_VALET_PARKING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FREE_VALET_PARKING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FREE_VALET_PARKING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FREE_VALET_PARKING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PARKING_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PARKING_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SELF_PARKING_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SELF_PARKING_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SELF_PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SELF_PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const VALET_PARKING_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const VALET_PARKING_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const VALET_PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const VALET_PARKING_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Electric car charging stations. Electric power stations, usually located
   * outdoors, into which guests plug their electric cars to receive a charge.
   *
   * @var bool
   */
  public $electricCarChargingStations;
  /**
   * Electric car charging stations exception.
   *
   * @var string
   */
  public $electricCarChargingStationsException;
  /**
   * Free parking. The hotel allows the cars of guests to be parked for free.
   * Parking facility may be an outdoor lot or an indoor garage, but must be
   * onsite. Nearby parking does not apply. Parking may be performed by the
   * guest or by hotel staff. Free parking must be available to all guests
   * (limited conditions does not apply).
   *
   * @var bool
   */
  public $freeParking;
  /**
   * Free parking exception.
   *
   * @var string
   */
  public $freeParkingException;
  /**
   * Free self parking. Guests park their own cars for free. Parking facility
   * may be an outdoor lot or an indoor garage, but must be onsite. Nearby
   * parking does not apply.
   *
   * @var bool
   */
  public $freeSelfParking;
  /**
   * Free self parking exception.
   *
   * @var string
   */
  public $freeSelfParkingException;
  /**
   * Free valet parking. Hotel staff member parks the cars of guests. Parking
   * with this service is free.
   *
   * @var bool
   */
  public $freeValetParking;
  /**
   * Free valet parking exception.
   *
   * @var string
   */
  public $freeValetParkingException;
  /**
   * Parking available. The hotel allows the cars of guests to be parked. Can be
   * free or for a fee. Parking facility may be an outdoor lot or an indoor
   * garage, but must be onsite. Nearby parking does not apply. Parking may be
   * performed by the guest or by hotel staff.
   *
   * @var bool
   */
  public $parkingAvailable;
  /**
   * Parking available exception.
   *
   * @var string
   */
  public $parkingAvailableException;
  /**
   * Self parking available. Guests park their own cars. Parking facility may be
   * an outdoor lot or an indoor garage, but must be onsite. Nearby parking does
   * not apply. Can be free or for a fee.
   *
   * @var bool
   */
  public $selfParkingAvailable;
  /**
   * Self parking available exception.
   *
   * @var string
   */
  public $selfParkingAvailableException;
  /**
   * Valet parking available. Hotel staff member parks the cars of guests.
   * Parking with this service can be free or for a fee.
   *
   * @var bool
   */
  public $valetParkingAvailable;
  /**
   * Valet parking available exception.
   *
   * @var string
   */
  public $valetParkingAvailableException;

  /**
   * Electric car charging stations. Electric power stations, usually located
   * outdoors, into which guests plug their electric cars to receive a charge.
   *
   * @param bool $electricCarChargingStations
   */
  public function setElectricCarChargingStations($electricCarChargingStations)
  {
    $this->electricCarChargingStations = $electricCarChargingStations;
  }
  /**
   * @return bool
   */
  public function getElectricCarChargingStations()
  {
    return $this->electricCarChargingStations;
  }
  /**
   * Electric car charging stations exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_* $electricCarChargingStationsException
   */
  public function setElectricCarChargingStationsException($electricCarChargingStationsException)
  {
    $this->electricCarChargingStationsException = $electricCarChargingStationsException;
  }
  /**
   * @return self::ELECTRIC_CAR_CHARGING_STATIONS_EXCEPTION_*
   */
  public function getElectricCarChargingStationsException()
  {
    return $this->electricCarChargingStationsException;
  }
  /**
   * Free parking. The hotel allows the cars of guests to be parked for free.
   * Parking facility may be an outdoor lot or an indoor garage, but must be
   * onsite. Nearby parking does not apply. Parking may be performed by the
   * guest or by hotel staff. Free parking must be available to all guests
   * (limited conditions does not apply).
   *
   * @param bool $freeParking
   */
  public function setFreeParking($freeParking)
  {
    $this->freeParking = $freeParking;
  }
  /**
   * @return bool
   */
  public function getFreeParking()
  {
    return $this->freeParking;
  }
  /**
   * Free parking exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_PARKING_EXCEPTION_* $freeParkingException
   */
  public function setFreeParkingException($freeParkingException)
  {
    $this->freeParkingException = $freeParkingException;
  }
  /**
   * @return self::FREE_PARKING_EXCEPTION_*
   */
  public function getFreeParkingException()
  {
    return $this->freeParkingException;
  }
  /**
   * Free self parking. Guests park their own cars for free. Parking facility
   * may be an outdoor lot or an indoor garage, but must be onsite. Nearby
   * parking does not apply.
   *
   * @param bool $freeSelfParking
   */
  public function setFreeSelfParking($freeSelfParking)
  {
    $this->freeSelfParking = $freeSelfParking;
  }
  /**
   * @return bool
   */
  public function getFreeSelfParking()
  {
    return $this->freeSelfParking;
  }
  /**
   * Free self parking exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_SELF_PARKING_EXCEPTION_* $freeSelfParkingException
   */
  public function setFreeSelfParkingException($freeSelfParkingException)
  {
    $this->freeSelfParkingException = $freeSelfParkingException;
  }
  /**
   * @return self::FREE_SELF_PARKING_EXCEPTION_*
   */
  public function getFreeSelfParkingException()
  {
    return $this->freeSelfParkingException;
  }
  /**
   * Free valet parking. Hotel staff member parks the cars of guests. Parking
   * with this service is free.
   *
   * @param bool $freeValetParking
   */
  public function setFreeValetParking($freeValetParking)
  {
    $this->freeValetParking = $freeValetParking;
  }
  /**
   * @return bool
   */
  public function getFreeValetParking()
  {
    return $this->freeValetParking;
  }
  /**
   * Free valet parking exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FREE_VALET_PARKING_EXCEPTION_* $freeValetParkingException
   */
  public function setFreeValetParkingException($freeValetParkingException)
  {
    $this->freeValetParkingException = $freeValetParkingException;
  }
  /**
   * @return self::FREE_VALET_PARKING_EXCEPTION_*
   */
  public function getFreeValetParkingException()
  {
    return $this->freeValetParkingException;
  }
  /**
   * Parking available. The hotel allows the cars of guests to be parked. Can be
   * free or for a fee. Parking facility may be an outdoor lot or an indoor
   * garage, but must be onsite. Nearby parking does not apply. Parking may be
   * performed by the guest or by hotel staff.
   *
   * @param bool $parkingAvailable
   */
  public function setParkingAvailable($parkingAvailable)
  {
    $this->parkingAvailable = $parkingAvailable;
  }
  /**
   * @return bool
   */
  public function getParkingAvailable()
  {
    return $this->parkingAvailable;
  }
  /**
   * Parking available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PARKING_AVAILABLE_EXCEPTION_* $parkingAvailableException
   */
  public function setParkingAvailableException($parkingAvailableException)
  {
    $this->parkingAvailableException = $parkingAvailableException;
  }
  /**
   * @return self::PARKING_AVAILABLE_EXCEPTION_*
   */
  public function getParkingAvailableException()
  {
    return $this->parkingAvailableException;
  }
  /**
   * Self parking available. Guests park their own cars. Parking facility may be
   * an outdoor lot or an indoor garage, but must be onsite. Nearby parking does
   * not apply. Can be free or for a fee.
   *
   * @param bool $selfParkingAvailable
   */
  public function setSelfParkingAvailable($selfParkingAvailable)
  {
    $this->selfParkingAvailable = $selfParkingAvailable;
  }
  /**
   * @return bool
   */
  public function getSelfParkingAvailable()
  {
    return $this->selfParkingAvailable;
  }
  /**
   * Self parking available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SELF_PARKING_AVAILABLE_EXCEPTION_* $selfParkingAvailableException
   */
  public function setSelfParkingAvailableException($selfParkingAvailableException)
  {
    $this->selfParkingAvailableException = $selfParkingAvailableException;
  }
  /**
   * @return self::SELF_PARKING_AVAILABLE_EXCEPTION_*
   */
  public function getSelfParkingAvailableException()
  {
    return $this->selfParkingAvailableException;
  }
  /**
   * Valet parking available. Hotel staff member parks the cars of guests.
   * Parking with this service can be free or for a fee.
   *
   * @param bool $valetParkingAvailable
   */
  public function setValetParkingAvailable($valetParkingAvailable)
  {
    $this->valetParkingAvailable = $valetParkingAvailable;
  }
  /**
   * @return bool
   */
  public function getValetParkingAvailable()
  {
    return $this->valetParkingAvailable;
  }
  /**
   * Valet parking available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::VALET_PARKING_AVAILABLE_EXCEPTION_* $valetParkingAvailableException
   */
  public function setValetParkingAvailableException($valetParkingAvailableException)
  {
    $this->valetParkingAvailableException = $valetParkingAvailableException;
  }
  /**
   * @return self::VALET_PARKING_AVAILABLE_EXCEPTION_*
   */
  public function getValetParkingAvailableException()
  {
    return $this->valetParkingAvailableException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Parking::class, 'Google_Service_MyBusinessLodging_Parking');
