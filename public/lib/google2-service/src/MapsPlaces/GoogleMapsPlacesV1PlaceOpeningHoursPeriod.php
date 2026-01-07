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

class GoogleMapsPlacesV1PlaceOpeningHoursPeriod extends \Google\Model
{
  protected $closeType = GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint::class;
  protected $closeDataType = '';
  protected $openType = GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint::class;
  protected $openDataType = '';

  /**
   * The time that the place starts to be closed.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint $close
   */
  public function setClose(GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint $close)
  {
    $this->close = $close;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint
   */
  public function getClose()
  {
    return $this->close;
  }
  /**
   * The time that the place starts to be open.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint $open
   */
  public function setOpen(GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint $open)
  {
    $this->open = $open;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint
   */
  public function getOpen()
  {
    return $this->open;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceOpeningHoursPeriod::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceOpeningHoursPeriod');
