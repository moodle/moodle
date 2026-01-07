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

namespace Google\Service\MyBusinessBusinessInformation;

class GoogleUpdatedLocation extends \Google\Model
{
  /**
   * The fields that Google updated.
   *
   * @var string
   */
  public $diffMask;
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * The fields that have pending edits that haven't yet been pushed to Maps and
   * Search.
   *
   * @var string
   */
  public $pendingMask;

  /**
   * The fields that Google updated.
   *
   * @param string $diffMask
   */
  public function setDiffMask($diffMask)
  {
    $this->diffMask = $diffMask;
  }
  /**
   * @return string
   */
  public function getDiffMask()
  {
    return $this->diffMask;
  }
  /**
   * The Google-updated version of this location.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The fields that have pending edits that haven't yet been pushed to Maps and
   * Search.
   *
   * @param string $pendingMask
   */
  public function setPendingMask($pendingMask)
  {
    $this->pendingMask = $pendingMask;
  }
  /**
   * @return string
   */
  public function getPendingMask()
  {
    return $this->pendingMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleUpdatedLocation::class, 'Google_Service_MyBusinessBusinessInformation_GoogleUpdatedLocation');
