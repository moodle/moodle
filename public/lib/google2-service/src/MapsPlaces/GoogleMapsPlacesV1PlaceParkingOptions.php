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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1PlaceParkingOptions extends \Google\Model
{
  /**
   * Place offers free garage parking.
   *
   * @var bool
   */
  public $freeGarageParking;
  /**
   * Place offers free parking lots.
   *
   * @var bool
   */
  public $freeParkingLot;
  /**
   * Place offers free street parking.
   *
   * @var bool
   */
  public $freeStreetParking;
  /**
   * Place offers paid garage parking.
   *
   * @var bool
   */
  public $paidGarageParking;
  /**
   * Place offers paid parking lots.
   *
   * @var bool
   */
  public $paidParkingLot;
  /**
   * Place offers paid street parking.
   *
   * @var bool
   */
  public $paidStreetParking;
  /**
   * Place offers valet parking.
   *
   * @var bool
   */
  public $valetParking;

  /**
   * Place offers free garage parking.
   *
   * @param bool $freeGarageParking
   */
  public function setFreeGarageParking($freeGarageParking)
  {
    $this->freeGarageParking = $freeGarageParking;
  }
  /**
   * @return bool
   */
  public function getFreeGarageParking()
  {
    return $this->freeGarageParking;
  }
  /**
   * Place offers free parking lots.
   *
   * @param bool $freeParkingLot
   */
  public function setFreeParkingLot($freeParkingLot)
  {
    $this->freeParkingLot = $freeParkingLot;
  }
  /**
   * @return bool
   */
  public function getFreeParkingLot()
  {
    return $this->freeParkingLot;
  }
  /**
   * Place offers free street parking.
   *
   * @param bool $freeStreetParking
   */
  public function setFreeStreetParking($freeStreetParking)
  {
    $this->freeStreetParking = $freeStreetParking;
  }
  /**
   * @return bool
   */
  public function getFreeStreetParking()
  {
    return $this->freeStreetParking;
  }
  /**
   * Place offers paid garage parking.
   *
   * @param bool $paidGarageParking
   */
  public function setPaidGarageParking($paidGarageParking)
  {
    $this->paidGarageParking = $paidGarageParking;
  }
  /**
   * @return bool
   */
  public function getPaidGarageParking()
  {
    return $this->paidGarageParking;
  }
  /**
   * Place offers paid parking lots.
   *
   * @param bool $paidParkingLot
   */
  public function setPaidParkingLot($paidParkingLot)
  {
    $this->paidParkingLot = $paidParkingLot;
  }
  /**
   * @return bool
   */
  public function getPaidParkingLot()
  {
    return $this->paidParkingLot;
  }
  /**
   * Place offers paid street parking.
   *
   * @param bool $paidStreetParking
   */
  public function setPaidStreetParking($paidStreetParking)
  {
    $this->paidStreetParking = $paidStreetParking;
  }
  /**
   * @return bool
   */
  public function getPaidStreetParking()
  {
    return $this->paidStreetParking;
  }
  /**
   * Place offers valet parking.
   *
   * @param bool $valetParking
   */
  public function setValetParking($valetParking)
  {
    $this->valetParking = $valetParking;
  }
  /**
   * @return bool
   */
  public function getValetParking()
  {
    return $this->valetParking;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceParkingOptions::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceParkingOptions');
