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

class GoogleMapsPlacesV1Photo extends \Google\Collection
{
  protected $collection_key = 'authorAttributions';
  protected $authorAttributionsType = GoogleMapsPlacesV1AuthorAttribution::class;
  protected $authorAttributionsDataType = 'array';
  /**
   * A link where users can flag a problem with the photo.
   *
   * @var string
   */
  public $flagContentUri;
  /**
   * A link to show the photo on Google Maps.
   *
   * @var string
   */
  public $googleMapsUri;
  /**
   * The maximum available height, in pixels.
   *
   * @var int
   */
  public $heightPx;
  /**
   * Identifier. A reference representing this place photo which may be used to
   * look up this place photo again (also called the API "resource" name:
   * `places/{place_id}/photos/{photo}`).
   *
   * @var string
   */
  public $name;
  /**
   * The maximum available width, in pixels.
   *
   * @var int
   */
  public $widthPx;

  /**
   * This photo's authors.
   *
   * @param GoogleMapsPlacesV1AuthorAttribution[] $authorAttributions
   */
  public function setAuthorAttributions($authorAttributions)
  {
    $this->authorAttributions = $authorAttributions;
  }
  /**
   * @return GoogleMapsPlacesV1AuthorAttribution[]
   */
  public function getAuthorAttributions()
  {
    return $this->authorAttributions;
  }
  /**
   * A link where users can flag a problem with the photo.
   *
   * @param string $flagContentUri
   */
  public function setFlagContentUri($flagContentUri)
  {
    $this->flagContentUri = $flagContentUri;
  }
  /**
   * @return string
   */
  public function getFlagContentUri()
  {
    return $this->flagContentUri;
  }
  /**
   * A link to show the photo on Google Maps.
   *
   * @param string $googleMapsUri
   */
  public function setGoogleMapsUri($googleMapsUri)
  {
    $this->googleMapsUri = $googleMapsUri;
  }
  /**
   * @return string
   */
  public function getGoogleMapsUri()
  {
    return $this->googleMapsUri;
  }
  /**
   * The maximum available height, in pixels.
   *
   * @param int $heightPx
   */
  public function setHeightPx($heightPx)
  {
    $this->heightPx = $heightPx;
  }
  /**
   * @return int
   */
  public function getHeightPx()
  {
    return $this->heightPx;
  }
  /**
   * Identifier. A reference representing this place photo which may be used to
   * look up this place photo again (also called the API "resource" name:
   * `places/{place_id}/photos/{photo}`).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The maximum available width, in pixels.
   *
   * @param int $widthPx
   */
  public function setWidthPx($widthPx)
  {
    $this->widthPx = $widthPx;
  }
  /**
   * @return int
   */
  public function getWidthPx()
  {
    return $this->widthPx;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1Photo::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1Photo');
