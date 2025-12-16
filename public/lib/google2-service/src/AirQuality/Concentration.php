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

namespace Google\Service\AirQuality;

class Concentration extends \Google\Model
{
  /**
   * Unspecified concentration unit.
   */
  public const UNITS_UNIT_UNSPECIFIED = 'UNIT_UNSPECIFIED';
  /**
   * The ppb (parts per billion) concentration unit.
   */
  public const UNITS_PARTS_PER_BILLION = 'PARTS_PER_BILLION';
  /**
   * The "µg/m^3" (micrograms per cubic meter) concentration unit.
   */
  public const UNITS_MICROGRAMS_PER_CUBIC_METER = 'MICROGRAMS_PER_CUBIC_METER';
  /**
   * Units for measuring this pollutant concentration.
   *
   * @var string
   */
  public $units;
  /**
   * Value of the pollutant concentration.
   *
   * @var float
   */
  public $value;

  /**
   * Units for measuring this pollutant concentration.
   *
   * Accepted values: UNIT_UNSPECIFIED, PARTS_PER_BILLION,
   * MICROGRAMS_PER_CUBIC_METER
   *
   * @param self::UNITS_* $units
   */
  public function setUnits($units)
  {
    $this->units = $units;
  }
  /**
   * @return self::UNITS_*
   */
  public function getUnits()
  {
    return $this->units;
  }
  /**
   * Value of the pollutant concentration.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Concentration::class, 'Google_Service_AirQuality_Concentration');
