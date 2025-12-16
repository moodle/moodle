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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2PinControlMetadata extends \Google\Model
{
  protected $allMatchedPinsType = GoogleCloudRetailV2PinControlMetadataProductPins::class;
  protected $allMatchedPinsDataType = 'map';
  protected $droppedPinsType = GoogleCloudRetailV2PinControlMetadataProductPins::class;
  protected $droppedPinsDataType = 'map';

  /**
   * Map of all matched pins, keyed by pin position.
   *
   * @param GoogleCloudRetailV2PinControlMetadataProductPins[] $allMatchedPins
   */
  public function setAllMatchedPins($allMatchedPins)
  {
    $this->allMatchedPins = $allMatchedPins;
  }
  /**
   * @return GoogleCloudRetailV2PinControlMetadataProductPins[]
   */
  public function getAllMatchedPins()
  {
    return $this->allMatchedPins;
  }
  /**
   * Map of pins that were dropped due to overlap with other matching pins,
   * keyed by pin position.
   *
   * @param GoogleCloudRetailV2PinControlMetadataProductPins[] $droppedPins
   */
  public function setDroppedPins($droppedPins)
  {
    $this->droppedPins = $droppedPins;
  }
  /**
   * @return GoogleCloudRetailV2PinControlMetadataProductPins[]
   */
  public function getDroppedPins()
  {
    return $this->droppedPins;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PinControlMetadata::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PinControlMetadata');
