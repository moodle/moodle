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

class GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification extends \Google\Model
{
  /**
   * If a place provides delivery.
   *
   * @var bool
   */
  public $delivery;
  /**
   * If a place provides dine-in.
   *
   * @var bool
   */
  public $dineIn;
  /**
   * If a place provides takeout.
   *
   * @var bool
   */
  public $takeout;

  /**
   * If a place provides delivery.
   *
   * @param bool $delivery
   */
  public function setDelivery($delivery)
  {
    $this->delivery = $delivery;
  }
  /**
   * @return bool
   */
  public function getDelivery()
  {
    return $this->delivery;
  }
  /**
   * If a place provides dine-in.
   *
   * @param bool $dineIn
   */
  public function setDineIn($dineIn)
  {
    $this->dineIn = $dineIn;
  }
  /**
   * @return bool
   */
  public function getDineIn()
  {
    return $this->dineIn;
  }
  /**
   * If a place provides takeout.
   *
   * @param bool $takeout
   */
  public function setTakeout($takeout)
  {
    $this->takeout = $takeout;
  }
  /**
   * @return bool
   */
  public function getTakeout()
  {
    return $this->takeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification');
