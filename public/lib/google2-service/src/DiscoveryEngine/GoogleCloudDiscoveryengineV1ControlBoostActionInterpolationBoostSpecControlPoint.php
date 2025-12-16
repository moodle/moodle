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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ControlBoostActionInterpolationBoostSpecControlPoint extends \Google\Model
{
  /**
   * Optional. Can be one of: 1. The numerical field value. 2. The duration spec
   * for freshness: The value must be formatted as an XSD `dayTimeDuration`
   * value (a restricted subset of an ISO 8601 duration value). The pattern for
   * this is: `nDnM]`.
   *
   * @var string
   */
  public $attributeValue;
  /**
   * Optional. The value between -1 to 1 by which to boost the score if the
   * attribute_value evaluates to the value specified above.
   *
   * @var float
   */
  public $boostAmount;

  /**
   * Optional. Can be one of: 1. The numerical field value. 2. The duration spec
   * for freshness: The value must be formatted as an XSD `dayTimeDuration`
   * value (a restricted subset of an ISO 8601 duration value). The pattern for
   * this is: `nDnM]`.
   *
   * @param string $attributeValue
   */
  public function setAttributeValue($attributeValue)
  {
    $this->attributeValue = $attributeValue;
  }
  /**
   * @return string
   */
  public function getAttributeValue()
  {
    return $this->attributeValue;
  }
  /**
   * Optional. The value between -1 to 1 by which to boost the score if the
   * attribute_value evaluates to the value specified above.
   *
   * @param float $boostAmount
   */
  public function setBoostAmount($boostAmount)
  {
    $this->boostAmount = $boostAmount;
  }
  /**
   * @return float
   */
  public function getBoostAmount()
  {
    return $this->boostAmount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ControlBoostActionInterpolationBoostSpecControlPoint::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ControlBoostActionInterpolationBoostSpecControlPoint');
