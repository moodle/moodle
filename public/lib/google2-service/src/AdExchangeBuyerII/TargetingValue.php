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

namespace Google\Service\AdExchangeBuyerII;

class TargetingValue extends \Google\Model
{
  protected $creativeSizeValueType = CreativeSize::class;
  protected $creativeSizeValueDataType = '';
  protected $dayPartTargetingValueType = DayPartTargeting::class;
  protected $dayPartTargetingValueDataType = '';
  /**
   * The long value to include/exclude.
   *
   * @var string
   */
  public $longValue;
  /**
   * The string value to include/exclude.
   *
   * @var string
   */
  public $stringValue;

  /**
   * The creative size value to include/exclude. Filled in when key =
   * GOOG_CREATIVE_SIZE
   *
   * @param CreativeSize $creativeSizeValue
   */
  public function setCreativeSizeValue(CreativeSize $creativeSizeValue)
  {
    $this->creativeSizeValue = $creativeSizeValue;
  }
  /**
   * @return CreativeSize
   */
  public function getCreativeSizeValue()
  {
    return $this->creativeSizeValue;
  }
  /**
   * The daypart targeting to include / exclude. Filled in when the key is
   * GOOG_DAYPART_TARGETING. The definition of this targeting is derived from
   * the structure used by Ad Manager.
   *
   * @param DayPartTargeting $dayPartTargetingValue
   */
  public function setDayPartTargetingValue(DayPartTargeting $dayPartTargetingValue)
  {
    $this->dayPartTargetingValue = $dayPartTargetingValue;
  }
  /**
   * @return DayPartTargeting
   */
  public function getDayPartTargetingValue()
  {
    return $this->dayPartTargetingValue;
  }
  /**
   * The long value to include/exclude.
   *
   * @param string $longValue
   */
  public function setLongValue($longValue)
  {
    $this->longValue = $longValue;
  }
  /**
   * @return string
   */
  public function getLongValue()
  {
    return $this->longValue;
  }
  /**
   * The string value to include/exclude.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetingValue::class, 'Google_Service_AdExchangeBuyerII_TargetingValue');
