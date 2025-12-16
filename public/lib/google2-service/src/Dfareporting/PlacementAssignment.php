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

namespace Google\Service\Dfareporting;

class PlacementAssignment extends \Google\Model
{
  /**
   * Whether this placement assignment is active. When true, the placement will
   * be included in the ad's rotation.
   *
   * @var bool
   */
  public $active;
  /**
   * ID of the placement to be assigned. This is a required field.
   *
   * @var string
   */
  public $placementId;
  protected $placementIdDimensionValueType = DimensionValue::class;
  protected $placementIdDimensionValueDataType = '';
  /**
   * Whether the placement to be assigned requires SSL. This is a read-only
   * field that is auto-generated when the ad is inserted or updated.
   *
   * @var bool
   */
  public $sslRequired;

  /**
   * Whether this placement assignment is active. When true, the placement will
   * be included in the ad's rotation.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * ID of the placement to be assigned. This is a required field.
   *
   * @param string $placementId
   */
  public function setPlacementId($placementId)
  {
    $this->placementId = $placementId;
  }
  /**
   * @return string
   */
  public function getPlacementId()
  {
    return $this->placementId;
  }
  /**
   * Dimension value for the ID of the placement. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $placementIdDimensionValue
   */
  public function setPlacementIdDimensionValue(DimensionValue $placementIdDimensionValue)
  {
    $this->placementIdDimensionValue = $placementIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getPlacementIdDimensionValue()
  {
    return $this->placementIdDimensionValue;
  }
  /**
   * Whether the placement to be assigned requires SSL. This is a read-only
   * field that is auto-generated when the ad is inserted or updated.
   *
   * @param bool $sslRequired
   */
  public function setSslRequired($sslRequired)
  {
    $this->sslRequired = $sslRequired;
  }
  /**
   * @return bool
   */
  public function getSslRequired()
  {
    return $this->sslRequired;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlacementAssignment::class, 'Google_Service_Dfareporting_PlacementAssignment');
