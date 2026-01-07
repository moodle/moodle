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

class RelevantLocation extends \Google\Model
{
  /**
   * Type unspecified.
   */
  public const RELATION_TYPE_RELATION_TYPE_UNSPECIFIED = 'RELATION_TYPE_UNSPECIFIED';
  /**
   * This represents a relation between 2 locations which share one physical
   * area, same brand/upper management/organization, but with different key
   * attributes like store hours or phone numbers. For example, Costco Pharmacy
   * is a department in Costco Wholesale.
   */
  public const RELATION_TYPE_DEPARTMENT_OF = 'DEPARTMENT_OF';
  /**
   * This represents the cases where 2 locations are co-located in the same
   * physical location, but from different companies (e.g. Starbucks in a
   * Safeway, shops in a mall).
   */
  public const RELATION_TYPE_INDEPENDENT_ESTABLISHMENT_IN = 'INDEPENDENT_ESTABLISHMENT_IN';
  /**
   * Required. Specify the location that is on the other side of the relation by
   * its placeID.
   *
   * @var string
   */
  public $placeId;
  /**
   * Required. The type of the relationship.
   *
   * @var string
   */
  public $relationType;

  /**
   * Required. Specify the location that is on the other side of the relation by
   * its placeID.
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
   * Required. The type of the relationship.
   *
   * Accepted values: RELATION_TYPE_UNSPECIFIED, DEPARTMENT_OF,
   * INDEPENDENT_ESTABLISHMENT_IN
   *
   * @param self::RELATION_TYPE_* $relationType
   */
  public function setRelationType($relationType)
  {
    $this->relationType = $relationType;
  }
  /**
   * @return self::RELATION_TYPE_*
   */
  public function getRelationType()
  {
    return $this->relationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RelevantLocation::class, 'Google_Service_MyBusinessBusinessInformation_RelevantLocation');
