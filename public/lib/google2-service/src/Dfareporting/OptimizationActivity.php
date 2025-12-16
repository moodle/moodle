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

class OptimizationActivity extends \Google\Model
{
  /**
   * Floodlight activity ID of this optimization activity. This is a required
   * field.
   *
   * @var string
   */
  public $floodlightActivityId;
  protected $floodlightActivityIdDimensionValueType = DimensionValue::class;
  protected $floodlightActivityIdDimensionValueDataType = '';
  /**
   * Weight associated with this optimization. The weight assigned will be
   * understood in proportion to the weights assigned to the other optimization
   * activities. Value must be greater than or equal to 1.
   *
   * @var int
   */
  public $weight;

  /**
   * Floodlight activity ID of this optimization activity. This is a required
   * field.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Dimension value for the ID of the floodlight activity. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $floodlightActivityIdDimensionValue
   */
  public function setFloodlightActivityIdDimensionValue(DimensionValue $floodlightActivityIdDimensionValue)
  {
    $this->floodlightActivityIdDimensionValue = $floodlightActivityIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getFloodlightActivityIdDimensionValue()
  {
    return $this->floodlightActivityIdDimensionValue;
  }
  /**
   * Weight associated with this optimization. The weight assigned will be
   * understood in proportion to the weights assigned to the other optimization
   * activities. Value must be greater than or equal to 1.
   *
   * @param int $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return int
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OptimizationActivity::class, 'Google_Service_Dfareporting_OptimizationActivity');
