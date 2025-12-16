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

namespace Google\Service\DisplayVideo;

class AgeRangeAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when age range is not specified in this version. This enum is
   * a placeholder for default value and does not represent a real age range
   * option.
   */
  public const AGE_RANGE_AGE_RANGE_UNSPECIFIED = 'AGE_RANGE_UNSPECIFIED';
  /**
   * The age range of the audience is 18 to 24.
   */
  public const AGE_RANGE_AGE_RANGE_18_24 = 'AGE_RANGE_18_24';
  /**
   * The age range of the audience is 25 to 34.
   */
  public const AGE_RANGE_AGE_RANGE_25_34 = 'AGE_RANGE_25_34';
  /**
   * The age range of the audience is 35 to 44.
   */
  public const AGE_RANGE_AGE_RANGE_35_44 = 'AGE_RANGE_35_44';
  /**
   * The age range of the audience is 45 to 54.
   */
  public const AGE_RANGE_AGE_RANGE_45_54 = 'AGE_RANGE_45_54';
  /**
   * The age range of the audience is 55 to 64.
   */
  public const AGE_RANGE_AGE_RANGE_55_64 = 'AGE_RANGE_55_64';
  /**
   * The age range of the audience is 65 and up.
   */
  public const AGE_RANGE_AGE_RANGE_65_PLUS = 'AGE_RANGE_65_PLUS';
  /**
   * The age range of the audience is unknown.
   */
  public const AGE_RANGE_AGE_RANGE_UNKNOWN = 'AGE_RANGE_UNKNOWN';
  /**
   * The age range of the audience is 18 to 20, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_18_20 = 'AGE_RANGE_18_20';
  /**
   * The age range of the audience is 21 to 24, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_21_24 = 'AGE_RANGE_21_24';
  /**
   * The age range of the audience is 25 to 29, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_25_29 = 'AGE_RANGE_25_29';
  /**
   * The age range of the audience is 30 to 34, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_30_34 = 'AGE_RANGE_30_34';
  /**
   * The age range of the audience is 35 to 39, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_35_39 = 'AGE_RANGE_35_39';
  /**
   * The age range of the audience is 40 to 44, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_40_44 = 'AGE_RANGE_40_44';
  /**
   * The age range of the audience is 45 to 49, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_45_49 = 'AGE_RANGE_45_49';
  /**
   * The age range of the audience is 50 to 54, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_50_54 = 'AGE_RANGE_50_54';
  /**
   * The age range of the audience is 55 to 59, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_55_59 = 'AGE_RANGE_55_59';
  /**
   * The age range of the audience is 60 to 64, only supported for the AdGroup
   * of YouTube Programmatic Reservation line item.
   */
  public const AGE_RANGE_AGE_RANGE_60_64 = 'AGE_RANGE_60_64';
  /**
   * Required. The age range of an audience. We only support targeting a
   * continuous age range of an audience. Thus, the age range represented in
   * this field can be 1) targeted solely, or, 2) part of a larger continuous
   * age range. The reach of a continuous age range targeting can be expanded by
   * also targeting an audience of an unknown age.
   *
   * @var string
   */
  public $ageRange;

  /**
   * Required. The age range of an audience. We only support targeting a
   * continuous age range of an audience. Thus, the age range represented in
   * this field can be 1) targeted solely, or, 2) part of a larger continuous
   * age range. The reach of a continuous age range targeting can be expanded by
   * also targeting an audience of an unknown age.
   *
   * Accepted values: AGE_RANGE_UNSPECIFIED, AGE_RANGE_18_24, AGE_RANGE_25_34,
   * AGE_RANGE_35_44, AGE_RANGE_45_54, AGE_RANGE_55_64, AGE_RANGE_65_PLUS,
   * AGE_RANGE_UNKNOWN, AGE_RANGE_18_20, AGE_RANGE_21_24, AGE_RANGE_25_29,
   * AGE_RANGE_30_34, AGE_RANGE_35_39, AGE_RANGE_40_44, AGE_RANGE_45_49,
   * AGE_RANGE_50_54, AGE_RANGE_55_59, AGE_RANGE_60_64
   *
   * @param self::AGE_RANGE_* $ageRange
   */
  public function setAgeRange($ageRange)
  {
    $this->ageRange = $ageRange;
  }
  /**
   * @return self::AGE_RANGE_*
   */
  public function getAgeRange()
  {
    return $this->ageRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgeRangeAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_AgeRangeAssignedTargetingOptionDetails');
