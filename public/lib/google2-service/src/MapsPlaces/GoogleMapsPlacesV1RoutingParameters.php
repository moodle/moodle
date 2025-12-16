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

class GoogleMapsPlacesV1RoutingParameters extends \Google\Model
{
  /**
   * No routing preference specified. Default to `TRAFFIC_UNAWARE`.
   */
  public const ROUTING_PREFERENCE_ROUTING_PREFERENCE_UNSPECIFIED = 'ROUTING_PREFERENCE_UNSPECIFIED';
  /**
   * Computes routes without taking live traffic conditions into consideration.
   * Suitable when traffic conditions don't matter or are not applicable. Using
   * this value produces the lowest latency. Note: For `TravelMode` `DRIVE` and
   * `TWO_WHEELER`, the route and duration chosen are based on road network and
   * average time-independent traffic conditions, not current road conditions.
   * Consequently, routes may include roads that are temporarily closed. Results
   * for a given request may vary over time due to changes in the road network,
   * updated average traffic conditions, and the distributed nature of the
   * service. Results may also vary between nearly-equivalent routes at any time
   * or frequency.
   */
  public const ROUTING_PREFERENCE_TRAFFIC_UNAWARE = 'TRAFFIC_UNAWARE';
  /**
   * Calculates routes taking live traffic conditions into consideration. In
   * contrast to `TRAFFIC_AWARE_OPTIMAL`, some optimizations are applied to
   * significantly reduce latency.
   */
  public const ROUTING_PREFERENCE_TRAFFIC_AWARE = 'TRAFFIC_AWARE';
  /**
   * Calculates the routes taking live traffic conditions into consideration,
   * without applying most performance optimizations. Using this value produces
   * the highest latency.
   */
  public const ROUTING_PREFERENCE_TRAFFIC_AWARE_OPTIMAL = 'TRAFFIC_AWARE_OPTIMAL';
  /**
   * No travel mode specified. Defaults to `DRIVE`.
   */
  public const TRAVEL_MODE_TRAVEL_MODE_UNSPECIFIED = 'TRAVEL_MODE_UNSPECIFIED';
  /**
   * Travel by passenger car.
   */
  public const TRAVEL_MODE_DRIVE = 'DRIVE';
  /**
   * Travel by bicycle. Not supported with `search_along_route_parameters`.
   */
  public const TRAVEL_MODE_BICYCLE = 'BICYCLE';
  /**
   * Travel by walking. Not supported with `search_along_route_parameters`.
   */
  public const TRAVEL_MODE_WALK = 'WALK';
  /**
   * Motorized two wheeled vehicles of all kinds such as scooters and
   * motorcycles. Note that this is distinct from the `BICYCLE` travel mode
   * which covers human-powered transport. Not supported with
   * `search_along_route_parameters`. Only supported in those countries listed
   * at [Countries and regions supported for two-wheeled
   * vehicles](https://developers.google.com/maps/documentation/routes/coverage-
   * two-wheeled).
   */
  public const TRAVEL_MODE_TWO_WHEELER = 'TWO_WHEELER';
  protected $originType = GoogleTypeLatLng::class;
  protected $originDataType = '';
  protected $routeModifiersType = GoogleMapsPlacesV1RouteModifiers::class;
  protected $routeModifiersDataType = '';
  /**
   * Optional. Specifies how to compute the routing summaries. The server
   * attempts to use the selected routing preference to compute the route. The
   * traffic aware routing preference is only available for the `DRIVE` or
   * `TWO_WHEELER` `travelMode`.
   *
   * @var string
   */
  public $routingPreference;
  /**
   * Optional. The travel mode.
   *
   * @var string
   */
  public $travelMode;

  /**
   * Optional. An explicit routing origin that overrides the origin defined in
   * the polyline. By default, the polyline origin is used.
   *
   * @param GoogleTypeLatLng $origin
   */
  public function setOrigin(GoogleTypeLatLng $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Optional. The route modifiers.
   *
   * @param GoogleMapsPlacesV1RouteModifiers $routeModifiers
   */
  public function setRouteModifiers(GoogleMapsPlacesV1RouteModifiers $routeModifiers)
  {
    $this->routeModifiers = $routeModifiers;
  }
  /**
   * @return GoogleMapsPlacesV1RouteModifiers
   */
  public function getRouteModifiers()
  {
    return $this->routeModifiers;
  }
  /**
   * Optional. Specifies how to compute the routing summaries. The server
   * attempts to use the selected routing preference to compute the route. The
   * traffic aware routing preference is only available for the `DRIVE` or
   * `TWO_WHEELER` `travelMode`.
   *
   * Accepted values: ROUTING_PREFERENCE_UNSPECIFIED, TRAFFIC_UNAWARE,
   * TRAFFIC_AWARE, TRAFFIC_AWARE_OPTIMAL
   *
   * @param self::ROUTING_PREFERENCE_* $routingPreference
   */
  public function setRoutingPreference($routingPreference)
  {
    $this->routingPreference = $routingPreference;
  }
  /**
   * @return self::ROUTING_PREFERENCE_*
   */
  public function getRoutingPreference()
  {
    return $this->routingPreference;
  }
  /**
   * Optional. The travel mode.
   *
   * Accepted values: TRAVEL_MODE_UNSPECIFIED, DRIVE, BICYCLE, WALK, TWO_WHEELER
   *
   * @param self::TRAVEL_MODE_* $travelMode
   */
  public function setTravelMode($travelMode)
  {
    $this->travelMode = $travelMode;
  }
  /**
   * @return self::TRAVEL_MODE_*
   */
  public function getTravelMode()
  {
    return $this->travelMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1RoutingParameters::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1RoutingParameters');
