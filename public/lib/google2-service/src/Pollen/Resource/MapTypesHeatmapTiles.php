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

namespace Google\Service\Pollen\Resource;

use Google\Service\Pollen\HttpBody;

/**
 * The "heatmapTiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $pollenService = new Google\Service\Pollen(...);
 *   $heatmapTiles = $pollenService->mapTypes_heatmapTiles;
 *  </code>
 */
class MapTypesHeatmapTiles extends \Google\Service\Resource
{
  /**
   * Returns a byte array containing the data of the tile PNG image.
   * (heatmapTiles.lookupHeatmapTile)
   *
   * @param string $mapType Required. The type of the pollen heatmap. Defines the
   * combination of pollen type and index that the map will graphically represent.
   * @param int $zoom Required. The map's zoom level. Defines how large or small
   * the contents of a map appear in a map view. * Zoom level 0 is the entire
   * world in a single tile. * Zoom level 1 is the entire world in 4 tiles. * Zoom
   * level 2 is the entire world in 16 tiles. * Zoom level 16 is the entire world
   * in 65,536 tiles. Allowed values: 0-16
   * @param int $x Required. Defines the east-west point in the requested tile.
   * @param int $y Required. Defines the north-south point in the requested tile.
   * @param array $optParams Optional parameters.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function lookupHeatmapTile($mapType, $zoom, $x, $y, $optParams = [])
  {
    $params = ['mapType' => $mapType, 'zoom' => $zoom, 'x' => $x, 'y' => $y];
    $params = array_merge($params, $optParams);
    return $this->call('lookupHeatmapTile', [$params], HttpBody::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MapTypesHeatmapTiles::class, 'Google_Service_Pollen_Resource_MapTypesHeatmapTiles');
