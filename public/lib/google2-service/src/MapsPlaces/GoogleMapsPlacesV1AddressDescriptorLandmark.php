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

class GoogleMapsPlacesV1AddressDescriptorLandmark extends \Google\Collection
{
  /**
   * This is the default relationship when nothing more specific below applies.
   */
  public const SPATIAL_RELATIONSHIP_NEAR = 'NEAR';
  /**
   * The landmark has a spatial geometry and the target is within its bounds.
   */
  public const SPATIAL_RELATIONSHIP_WITHIN = 'WITHIN';
  /**
   * The target is directly adjacent to the landmark.
   */
  public const SPATIAL_RELATIONSHIP_BESIDE = 'BESIDE';
  /**
   * The target is directly opposite the landmark on the other side of the road.
   */
  public const SPATIAL_RELATIONSHIP_ACROSS_THE_ROAD = 'ACROSS_THE_ROAD';
  /**
   * On the same route as the landmark but not besides or across.
   */
  public const SPATIAL_RELATIONSHIP_DOWN_THE_ROAD = 'DOWN_THE_ROAD';
  /**
   * Not on the same route as the landmark but a single turn away.
   */
  public const SPATIAL_RELATIONSHIP_AROUND_THE_CORNER = 'AROUND_THE_CORNER';
  /**
   * Close to the landmark's structure but further away from its street
   * entrances.
   */
  public const SPATIAL_RELATIONSHIP_BEHIND = 'BEHIND';
  protected $collection_key = 'types';
  protected $displayNameType = GoogleTypeLocalizedText::class;
  protected $displayNameDataType = '';
  /**
   * The landmark's resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The landmark's place id.
   *
   * @var string
   */
  public $placeId;
  /**
   * Defines the spatial relationship between the target location and the
   * landmark.
   *
   * @var string
   */
  public $spatialRelationship;
  /**
   * The straight line distance, in meters, between the center point of the
   * target and the center point of the landmark. In some situations, this value
   * can be longer than `travel_distance_meters`.
   *
   * @var float
   */
  public $straightLineDistanceMeters;
  /**
   * The travel distance, in meters, along the road network from the target to
   * the landmark, if known. This value does not take into account the mode of
   * transportation, such as walking, driving, or biking.
   *
   * @var float
   */
  public $travelDistanceMeters;
  /**
   * A set of type tags for this landmark. For a complete list of possible
   * values, see https://developers.google.com/maps/documentation/places/web-
   * service/place-types.
   *
   * @var string[]
   */
  public $types;

  /**
   * The landmark's display name.
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
   * The landmark's resource name.
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
   * The landmark's place id.
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
  /**
   * Defines the spatial relationship between the target location and the
   * landmark.
   *
   * Accepted values: NEAR, WITHIN, BESIDE, ACROSS_THE_ROAD, DOWN_THE_ROAD,
   * AROUND_THE_CORNER, BEHIND
   *
   * @param self::SPATIAL_RELATIONSHIP_* $spatialRelationship
   */
  public function setSpatialRelationship($spatialRelationship)
  {
    $this->spatialRelationship = $spatialRelationship;
  }
  /**
   * @return self::SPATIAL_RELATIONSHIP_*
   */
  public function getSpatialRelationship()
  {
    return $this->spatialRelationship;
  }
  /**
   * The straight line distance, in meters, between the center point of the
   * target and the center point of the landmark. In some situations, this value
   * can be longer than `travel_distance_meters`.
   *
   * @param float $straightLineDistanceMeters
   */
  public function setStraightLineDistanceMeters($straightLineDistanceMeters)
  {
    $this->straightLineDistanceMeters = $straightLineDistanceMeters;
  }
  /**
   * @return float
   */
  public function getStraightLineDistanceMeters()
  {
    return $this->straightLineDistanceMeters;
  }
  /**
   * The travel distance, in meters, along the road network from the target to
   * the landmark, if known. This value does not take into account the mode of
   * transportation, such as walking, driving, or biking.
   *
   * @param float $travelDistanceMeters
   */
  public function setTravelDistanceMeters($travelDistanceMeters)
  {
    $this->travelDistanceMeters = $travelDistanceMeters;
  }
  /**
   * @return float
   */
  public function getTravelDistanceMeters()
  {
    return $this->travelDistanceMeters;
  }
  /**
   * A set of type tags for this landmark. For a complete list of possible
   * values, see https://developers.google.com/maps/documentation/places/web-
   * service/place-types.
   *
   * @param string[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return string[]
   */
  public function getTypes()
  {
    return $this->types;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AddressDescriptorLandmark::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AddressDescriptorLandmark');
