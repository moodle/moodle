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

class GoogleMapsPlacesV1Polyline extends \Google\Model
{
  /**
   * An [encoded polyline](https://developers.google.com/maps/documentation/util
   * ities/polylinealgorithm), as returned by the [Routes API by default](https:
   * //developers.google.com/maps/documentation/routes/reference/rest/v2/TopLeve
   * l/computeRoutes#polylineencoding). See the [encoder](https://developers.goo
   * gle.com/maps/documentation/utilities/polylineutility) and [decoder](https:/
   * /developers.google.com/maps/documentation/routes/polylinedecoder) tools.
   *
   * @var string
   */
  public $encodedPolyline;

  /**
   * An [encoded polyline](https://developers.google.com/maps/documentation/util
   * ities/polylinealgorithm), as returned by the [Routes API by default](https:
   * //developers.google.com/maps/documentation/routes/reference/rest/v2/TopLeve
   * l/computeRoutes#polylineencoding). See the [encoder](https://developers.goo
   * gle.com/maps/documentation/utilities/polylineutility) and [decoder](https:/
   * /developers.google.com/maps/documentation/routes/polylinedecoder) tools.
   *
   * @param string $encodedPolyline
   */
  public function setEncodedPolyline($encodedPolyline)
  {
    $this->encodedPolyline = $encodedPolyline;
  }
  /**
   * @return string
   */
  public function getEncodedPolyline()
  {
    return $this->encodedPolyline;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1Polyline::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1Polyline');
