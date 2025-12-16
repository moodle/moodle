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

class GoogleMapsPlacesV1PlaceAreaSummary extends \Google\Collection
{
  protected $collection_key = 'contentBlocks';
  protected $contentBlocksType = GoogleMapsPlacesV1ContentBlock::class;
  protected $contentBlocksDataType = 'array';
  /**
   * @var string
   */
  public $flagContentUri;

  /**
   * @param GoogleMapsPlacesV1ContentBlock[]
   */
  public function setContentBlocks($contentBlocks)
  {
    $this->contentBlocks = $contentBlocks;
  }
  /**
   * @return GoogleMapsPlacesV1ContentBlock[]
   */
  public function getContentBlocks()
  {
    return $this->contentBlocks;
  }
  /**
   * @param string
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceAreaSummary::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceAreaSummary');
