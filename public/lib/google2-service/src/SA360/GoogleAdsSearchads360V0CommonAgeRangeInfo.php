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

class GoogleAdsSearchads360V0CommonAgeRangeInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Between 18 and 24 years old.
   */
  public const TYPE_AGE_RANGE_18_24 = 'AGE_RANGE_18_24';
  /**
   * Between 25 and 34 years old.
   */
  public const TYPE_AGE_RANGE_25_34 = 'AGE_RANGE_25_34';
  /**
   * Between 35 and 44 years old.
   */
  public const TYPE_AGE_RANGE_35_44 = 'AGE_RANGE_35_44';
  /**
   * Between 45 and 54 years old.
   */
  public const TYPE_AGE_RANGE_45_54 = 'AGE_RANGE_45_54';
  /**
   * Between 55 and 64 years old.
   */
  public const TYPE_AGE_RANGE_55_64 = 'AGE_RANGE_55_64';
  /**
   * 65 years old and beyond.
   */
  public const TYPE_AGE_RANGE_65_UP = 'AGE_RANGE_65_UP';
  /**
   * Undetermined age range.
   */
  public const TYPE_AGE_RANGE_UNDETERMINED = 'AGE_RANGE_UNDETERMINED';
  /**
   * Type of the age range.
   *
   * @var string
   */
  public $type;

  /**
   * Type of the age range.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AGE_RANGE_18_24, AGE_RANGE_25_34,
   * AGE_RANGE_35_44, AGE_RANGE_45_54, AGE_RANGE_55_64, AGE_RANGE_65_UP,
   * AGE_RANGE_UNDETERMINED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonAgeRangeInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonAgeRangeInfo');
