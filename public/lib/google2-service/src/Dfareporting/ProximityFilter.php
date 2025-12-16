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

class ProximityFilter extends \Google\Model
{
  /**
   * The radius bucket type is unknown.
   */
  public const RADIUS_BUCKET_TYPE_RADIUS_BUCKET_TYPE_UNKNOWN = 'RADIUS_BUCKET_TYPE_UNKNOWN';
  /**
   * The radius bucket type is small.
   */
  public const RADIUS_BUCKET_TYPE_SMALL = 'SMALL';
  /**
   * The radius bucket type is medium.
   */
  public const RADIUS_BUCKET_TYPE_MEDIUM = 'MEDIUM';
  /**
   * The radius bucket type is large.
   */
  public const RADIUS_BUCKET_TYPE_LARGE = 'LARGE';
  /**
   * The radius bucket type is multi-regional.
   */
  public const RADIUS_BUCKET_TYPE_MULTI_REGIONAL = 'MULTI_REGIONAL';
  /**
   * The radius bucket type is national.
   */
  public const RADIUS_BUCKET_TYPE_NATIONAL = 'NATIONAL';
  /**
   * The units of the radius value are unknown. This value is unused.
   */
  public const RADIUS_UNIT_TYPE_RADIUS_UNIT_TYPE_UNKNOWN = 'RADIUS_UNIT_TYPE_UNKNOWN';
  /**
   * The units of the radius value are kilometers.
   */
  public const RADIUS_UNIT_TYPE_KILOMETERS = 'KILOMETERS';
  /**
   * The units of the radius value are miles.
   */
  public const RADIUS_UNIT_TYPE_MILES = 'MILES';
  /**
   * Optional. Field ID in the element.
   *
   * @var int
   */
  public $fieldId;
  /**
   * Optional. The radius bucket type of the proximity filter
   *
   * @var string
   */
  public $radiusBucketType;
  /**
   * Optional. The units of the radius value
   *
   * @var string
   */
  public $radiusUnitType;
  /**
   * Optional. Radius length in units defined by radius_units.
   *
   * @var int
   */
  public $radiusValue;

  /**
   * Optional. Field ID in the element.
   *
   * @param int $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return int
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Optional. The radius bucket type of the proximity filter
   *
   * Accepted values: RADIUS_BUCKET_TYPE_UNKNOWN, SMALL, MEDIUM, LARGE,
   * MULTI_REGIONAL, NATIONAL
   *
   * @param self::RADIUS_BUCKET_TYPE_* $radiusBucketType
   */
  public function setRadiusBucketType($radiusBucketType)
  {
    $this->radiusBucketType = $radiusBucketType;
  }
  /**
   * @return self::RADIUS_BUCKET_TYPE_*
   */
  public function getRadiusBucketType()
  {
    return $this->radiusBucketType;
  }
  /**
   * Optional. The units of the radius value
   *
   * Accepted values: RADIUS_UNIT_TYPE_UNKNOWN, KILOMETERS, MILES
   *
   * @param self::RADIUS_UNIT_TYPE_* $radiusUnitType
   */
  public function setRadiusUnitType($radiusUnitType)
  {
    $this->radiusUnitType = $radiusUnitType;
  }
  /**
   * @return self::RADIUS_UNIT_TYPE_*
   */
  public function getRadiusUnitType()
  {
    return $this->radiusUnitType;
  }
  /**
   * Optional. Radius length in units defined by radius_units.
   *
   * @param int $radiusValue
   */
  public function setRadiusValue($radiusValue)
  {
    $this->radiusValue = $radiusValue;
  }
  /**
   * @return int
   */
  public function getRadiusValue()
  {
    return $this->radiusValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProximityFilter::class, 'Google_Service_Dfareporting_ProximityFilter');
