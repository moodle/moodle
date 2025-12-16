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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType extends \Google\Model
{
  /**
   * Not specified.
   */
  public const LEVEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const LEVEL_UNKNOWN = 'UNKNOWN';
  /**
   * Level 1.
   */
  public const LEVEL_LEVEL1 = 'LEVEL1';
  /**
   * Level 2.
   */
  public const LEVEL_LEVEL2 = 'LEVEL2';
  /**
   * Level 3.
   */
  public const LEVEL_LEVEL3 = 'LEVEL3';
  /**
   * Level 4.
   */
  public const LEVEL_LEVEL4 = 'LEVEL4';
  /**
   * Level 5.
   */
  public const LEVEL_LEVEL5 = 'LEVEL5';
  /**
   * Level of the type.
   *
   * @var string
   */
  public $level;
  /**
   * Value of the type.
   *
   * @var string
   */
  public $value;

  /**
   * Level of the type.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, LEVEL1, LEVEL2, LEVEL3, LEVEL4,
   * LEVEL5
   *
   * @param self::LEVEL_* $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return self::LEVEL_*
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * Value of the type.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductType');
