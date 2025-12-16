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

class GoogleMapsPlacesV1AuthorAttribution extends \Google\Model
{
  /**
   * Name of the author of the Photo or Review.
   *
   * @var string
   */
  public $displayName;
  /**
   * Profile photo URI of the author of the Photo or Review.
   *
   * @var string
   */
  public $photoUri;
  /**
   * URI of the author of the Photo or Review.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of the author of the Photo or Review.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Profile photo URI of the author of the Photo or Review.
   *
   * @param string $photoUri
   */
  public function setPhotoUri($photoUri)
  {
    $this->photoUri = $photoUri;
  }
  /**
   * @return string
   */
  public function getPhotoUri()
  {
    return $this->photoUri;
  }
  /**
   * URI of the author of the Photo or Review.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AuthorAttribution::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AuthorAttribution');
