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

namespace Google\Service\CloudTalentSolution;

class CommuteFilter extends \Google\Model
{
  /**
   * Commute method isn't specified.
   */
  public const COMMUTE_METHOD_COMMUTE_METHOD_UNSPECIFIED = 'COMMUTE_METHOD_UNSPECIFIED';
  /**
   * Commute time is calculated based on driving time.
   */
  public const COMMUTE_METHOD_DRIVING = 'DRIVING';
  /**
   * Commute time is calculated based on public transit including bus, metro,
   * subway, and so on.
   */
  public const COMMUTE_METHOD_TRANSIT = 'TRANSIT';
  /**
   * Commute time is calculated based on walking time.
   */
  public const COMMUTE_METHOD_WALKING = 'WALKING';
  /**
   * Commute time is calculated based on biking time.
   */
  public const COMMUTE_METHOD_CYCLING = 'CYCLING';
  /**
   * Commute time is calculated based on public transit that is wheelchair
   * accessible.
   */
  public const COMMUTE_METHOD_TRANSIT_ACCESSIBLE = 'TRANSIT_ACCESSIBLE';
  /**
   * Road traffic situation isn't specified.
   */
  public const ROAD_TRAFFIC_ROAD_TRAFFIC_UNSPECIFIED = 'ROAD_TRAFFIC_UNSPECIFIED';
  /**
   * Optimal commute time without considering any traffic impact.
   */
  public const ROAD_TRAFFIC_TRAFFIC_FREE = 'TRAFFIC_FREE';
  /**
   * Commute time calculation takes in account the peak traffic impact.
   */
  public const ROAD_TRAFFIC_BUSY_HOUR = 'BUSY_HOUR';
  /**
   * If `true`, jobs without street level addresses may also be returned. For
   * city level addresses, the city center is used. For state and coarser level
   * addresses, text matching is used. If this field is set to `false` or isn't
   * specified, only jobs that include street level addresses will be returned
   * by commute search.
   *
   * @var bool
   */
  public $allowImpreciseAddresses;
  /**
   * Required. The method of transportation to calculate the commute time for.
   *
   * @var string
   */
  public $commuteMethod;
  protected $departureTimeType = TimeOfDay::class;
  protected $departureTimeDataType = '';
  /**
   * Specifies the traffic density to use when calculating commute time.
   *
   * @var string
   */
  public $roadTraffic;
  protected $startCoordinatesType = LatLng::class;
  protected $startCoordinatesDataType = '';
  /**
   * Required. The maximum travel time in seconds. The maximum allowed value is
   * `3600s` (one hour). Format is `123s`.
   *
   * @var string
   */
  public $travelDuration;

  /**
   * If `true`, jobs without street level addresses may also be returned. For
   * city level addresses, the city center is used. For state and coarser level
   * addresses, text matching is used. If this field is set to `false` or isn't
   * specified, only jobs that include street level addresses will be returned
   * by commute search.
   *
   * @param bool $allowImpreciseAddresses
   */
  public function setAllowImpreciseAddresses($allowImpreciseAddresses)
  {
    $this->allowImpreciseAddresses = $allowImpreciseAddresses;
  }
  /**
   * @return bool
   */
  public function getAllowImpreciseAddresses()
  {
    return $this->allowImpreciseAddresses;
  }
  /**
   * Required. The method of transportation to calculate the commute time for.
   *
   * Accepted values: COMMUTE_METHOD_UNSPECIFIED, DRIVING, TRANSIT, WALKING,
   * CYCLING, TRANSIT_ACCESSIBLE
   *
   * @param self::COMMUTE_METHOD_* $commuteMethod
   */
  public function setCommuteMethod($commuteMethod)
  {
    $this->commuteMethod = $commuteMethod;
  }
  /**
   * @return self::COMMUTE_METHOD_*
   */
  public function getCommuteMethod()
  {
    return $this->commuteMethod;
  }
  /**
   * The departure time used to calculate traffic impact, represented as
   * google.type.TimeOfDay in local time zone. Currently traffic model is
   * restricted to hour level resolution.
   *
   * @param TimeOfDay $departureTime
   */
  public function setDepartureTime(TimeOfDay $departureTime)
  {
    $this->departureTime = $departureTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getDepartureTime()
  {
    return $this->departureTime;
  }
  /**
   * Specifies the traffic density to use when calculating commute time.
   *
   * Accepted values: ROAD_TRAFFIC_UNSPECIFIED, TRAFFIC_FREE, BUSY_HOUR
   *
   * @param self::ROAD_TRAFFIC_* $roadTraffic
   */
  public function setRoadTraffic($roadTraffic)
  {
    $this->roadTraffic = $roadTraffic;
  }
  /**
   * @return self::ROAD_TRAFFIC_*
   */
  public function getRoadTraffic()
  {
    return $this->roadTraffic;
  }
  /**
   * Required. The latitude and longitude of the location to calculate the
   * commute time from.
   *
   * @param LatLng $startCoordinates
   */
  public function setStartCoordinates(LatLng $startCoordinates)
  {
    $this->startCoordinates = $startCoordinates;
  }
  /**
   * @return LatLng
   */
  public function getStartCoordinates()
  {
    return $this->startCoordinates;
  }
  /**
   * Required. The maximum travel time in seconds. The maximum allowed value is
   * `3600s` (one hour). Format is `123s`.
   *
   * @param string $travelDuration
   */
  public function setTravelDuration($travelDuration)
  {
    $this->travelDuration = $travelDuration;
  }
  /**
   * @return string
   */
  public function getTravelDuration()
  {
    return $this->travelDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommuteFilter::class, 'Google_Service_CloudTalentSolution_CommuteFilter');
