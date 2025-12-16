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

class GoogleMapsPlacesV1AddressDescriptorArea extends \Google\Model
{
  /**
   * The containment is unspecified.
   */
  public const CONTAINMENT_CONTAINMENT_UNSPECIFIED = 'CONTAINMENT_UNSPECIFIED';
  /**
   * The target location is within the area region, close to the center.
   */
  public const CONTAINMENT_WITHIN = 'WITHIN';
  /**
   * The target location is within the area region, close to the edge.
   */
  public const CONTAINMENT_OUTSKIRTS = 'OUTSKIRTS';
  /**
   * The target location is outside the area region, but close by.
   */
  public const CONTAINMENT_NEAR = 'NEAR';
  /**
   * Defines the spatial relationship between the target location and the area.
   *
   * @var string
   */
  public $containment;
  protected $displayNameType = GoogleTypeLocalizedText::class;
  protected $displayNameDataType = '';
  /**
   * The area's resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The area's place id.
   *
   * @var string
   */
  public $placeId;

  /**
   * Defines the spatial relationship between the target location and the area.
   *
   * Accepted values: CONTAINMENT_UNSPECIFIED, WITHIN, OUTSKIRTS, NEAR
   *
   * @param self::CONTAINMENT_* $containment
   */
  public function setContainment($containment)
  {
    $this->containment = $containment;
  }
  /**
   * @return self::CONTAINMENT_*
   */
  public function getContainment()
  {
    return $this->containment;
  }
  /**
   * The area's display name.
   *
   * @param GoogleTypeLocalizedText $displayName
   */
  public function setDisplayName(GoogleTypeLocalizedText $displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The area's resource name.
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
   * The area's place id.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AddressDescriptorArea::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AddressDescriptorArea');
