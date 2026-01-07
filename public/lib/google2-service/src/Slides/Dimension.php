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

namespace Google\Service\Slides;

class Dimension extends \Google\Model
{
  /**
   * The units are unknown.
   */
  public const UNIT_UNIT_UNSPECIFIED = 'UNIT_UNSPECIFIED';
  /**
   * An English Metric Unit (EMU) is defined as 1/360,000 of a centimeter and
   * thus there are 914,400 EMUs per inch, and 12,700 EMUs per point.
   */
  public const UNIT_EMU = 'EMU';
  /**
   * A point, 1/72 of an inch.
   */
  public const UNIT_PT = 'PT';
  /**
   * The magnitude.
   *
   * @var 
   */
  public $magnitude;
  /**
   * The units for magnitude.
   *
   * @var string
   */
  public $unit;

  public function setMagnitude($magnitude)
  {
    $this->magnitude = $magnitude;
  }
  public function getMagnitude()
  {
    return $this->magnitude;
  }
  /**
   * The units for magnitude.
   *
   * Accepted values: UNIT_UNSPECIFIED, EMU, PT
   *
   * @param self::UNIT_* $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return self::UNIT_*
   */
  public function getUnit()
  {
    return $this->unit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dimension::class, 'Google_Service_Slides_Dimension');
