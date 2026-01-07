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

class MapTile extends \Google\Model
{
  protected $imageUrlType = SafeUrlProto::class;
  protected $imageUrlDataType = '';
  /**
   * Map tile x coordinate
   *
   * @var 
   */
  public $tileX;
  /**
   * Map tile y coordinate
   *
   * @var 
   */
  public $tileY;

  /**
   * URL to an image file containing an office layout of the user's location for
   * their organization, if one is available. For google.com, this image is from
   * Corp Campus Maps.
   *
   * @param SafeUrlProto $imageUrl
   */
  public function setImageUrl(SafeUrlProto $imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return SafeUrlProto
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  public function setTileX($tileX)
  {
    $this->tileX = $tileX;
  }
  public function getTileX()
  {
    return $this->tileX;
  }
  public function setTileY($tileY)
  {
    $this->tileY = $tileY;
  }
  public function getTileY()
  {
    return $this->tileY;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MapTile::class, 'Google_Service_CloudSearch_MapTile');
