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

namespace Google\Service\Books;

class GeolayerdataGeo extends \Google\Collection
{
  protected $collection_key = 'boundary';
  /**
   * The boundary of the location as a set of loops containing pairs of
   * latitude, longitude coordinates.
   *
   * @var string[]
   */
  public $boundary;
  /**
   * The cache policy active for this data. EX: UNRESTRICTED, RESTRICTED, NEVER
   *
   * @var string
   */
  public $cachePolicy;
  /**
   * The country code of the location.
   *
   * @var string
   */
  public $countryCode;
  /**
   * The latitude of the location.
   *
   * @var 
   */
  public $latitude;
  /**
   * The longitude of the location.
   *
   * @var 
   */
  public $longitude;
  /**
   * The type of map that should be used for this location. EX: HYBRID, ROADMAP,
   * SATELLITE, TERRAIN
   *
   * @var string
   */
  public $mapType;
  protected $viewportType = GeolayerdataGeoViewport::class;
  protected $viewportDataType = '';
  /**
   * The Zoom level to use for the map. Zoom levels between 0 (the lowest zoom
   * level, in which the entire world can be seen on one map) to 21+ (down to
   * individual buildings). See: https:
   * //developers.google.com/maps/documentation/staticmaps/#Zoomlevels
   *
   * @var int
   */
  public $zoom;

  /**
   * The boundary of the location as a set of loops containing pairs of
   * latitude, longitude coordinates.
   *
   * @param string[] $boundary
   */
  public function setBoundary($boundary)
  {
    $this->boundary = $boundary;
  }
  /**
   * @return string[]
   */
  public function getBoundary()
  {
    return $this->boundary;
  }
  /**
   * The cache policy active for this data. EX: UNRESTRICTED, RESTRICTED, NEVER
   *
   * @param string $cachePolicy
   */
  public function setCachePolicy($cachePolicy)
  {
    $this->cachePolicy = $cachePolicy;
  }
  /**
   * @return string
   */
  public function getCachePolicy()
  {
    return $this->cachePolicy;
  }
  /**
   * The country code of the location.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }
  public function getLatitude()
  {
    return $this->latitude;
  }
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }
  public function getLongitude()
  {
    return $this->longitude;
  }
  /**
   * The type of map that should be used for this location. EX: HYBRID, ROADMAP,
   * SATELLITE, TERRAIN
   *
   * @param string $mapType
   */
  public function setMapType($mapType)
  {
    $this->mapType = $mapType;
  }
  /**
   * @return string
   */
  public function getMapType()
  {
    return $this->mapType;
  }
  /**
   * The viewport for showing this location. This is a latitude, longitude
   * rectangle.
   *
   * @param GeolayerdataGeoViewport $viewport
   */
  public function setViewport(GeolayerdataGeoViewport $viewport)
  {
    $this->viewport = $viewport;
  }
  /**
   * @return GeolayerdataGeoViewport
   */
  public function getViewport()
  {
    return $this->viewport;
  }
  /**
   * The Zoom level to use for the map. Zoom levels between 0 (the lowest zoom
   * level, in which the entire world can be seen on one map) to 21+ (down to
   * individual buildings). See: https:
   * //developers.google.com/maps/documentation/staticmaps/#Zoomlevels
   *
   * @param int $zoom
   */
  public function setZoom($zoom)
  {
    $this->zoom = $zoom;
  }
  /**
   * @return int
   */
  public function getZoom()
  {
    return $this->zoom;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeolayerdataGeo::class, 'Google_Service_Books_GeolayerdataGeo');
