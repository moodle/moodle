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

namespace Google\Service\CloudSearch;

class MapInfo extends \Google\Collection
{
  protected $collection_key = 'mapTile';
  /**
   * Latitude in degrees
   *
   * @var 
   */
  public $lat;
  protected $locationUrlType = SafeUrlProto::class;
  protected $locationUrlDataType = '';
  /**
   * Longitude in degrees
   *
   * @var 
   */
  public $long;
  protected $mapTileType = MapTile::class;
  protected $mapTileDataType = 'array';
  /**
   * The zoom level of the map. A constant zoom value of 18 is used for now to
   * match the zoom of the map shown on a Moma Teams Profile page
   *
   * @var int
   */
  public $zoom;

  public function setLat($lat)
  {
    $this->lat = $lat;
  }
  public function getLat()
  {
    return $this->lat;
  }
  /**
   * URL to a view of a map centered on the user's work location in Campus Maps
   * (for google.com) or Google Maps (external).
   *
   * @param SafeUrlProto $locationUrl
   */
  public function setLocationUrl(SafeUrlProto $locationUrl)
  {
    $this->locationUrl = $locationUrl;
  }
  /**
   * @return SafeUrlProto
   */
  public function getLocationUrl()
  {
    return $this->locationUrl;
  }
  public function setLong($long)
  {
    $this->long = $long;
  }
  public function getLong()
  {
    return $this->long;
  }
  /**
   * MapTiles for the area around a user's work location
   *
   * @param MapTile[] $mapTile
   */
  public function setMapTile($mapTile)
  {
    $this->mapTile = $mapTile;
  }
  /**
   * @return MapTile[]
   */
  public function getMapTile()
  {
    return $this->mapTile;
  }
  /**
   * The zoom level of the map. A constant zoom value of 18 is used for now to
   * match the zoom of the map shown on a Moma Teams Profile page
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
class_alias(MapInfo::class, 'Google_Service_CloudSearch_MapInfo');
