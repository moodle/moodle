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

namespace Google\Service\AnalyticsData;

class DimensionCompatibility extends \Google\Model
{
  /**
   * Unspecified compatibility.
   */
  public const COMPATIBILITY_COMPATIBILITY_UNSPECIFIED = 'COMPATIBILITY_UNSPECIFIED';
  /**
   * The dimension or metric is compatible. This dimension or metric can be
   * successfully added to a report.
   */
  public const COMPATIBILITY_COMPATIBLE = 'COMPATIBLE';
  /**
   * The dimension or metric is incompatible. This dimension or metric cannot be
   * successfully added to a report.
   */
  public const COMPATIBILITY_INCOMPATIBLE = 'INCOMPATIBLE';
  /**
   * The compatibility of this dimension. If the compatibility is COMPATIBLE,
   * this dimension can be successfully added to the report.
   *
   * @var string
   */
  public $compatibility;
  protected $dimensionMetadataType = DimensionMetadata::class;
  protected $dimensionMetadataDataType = '';

  /**
   * The compatibility of this dimension. If the compatibility is COMPATIBLE,
   * this dimension can be successfully added to the report.
   *
   * Accepted values: COMPATIBILITY_UNSPECIFIED, COMPATIBLE, INCOMPATIBLE
   *
   * @param self::COMPATIBILITY_* $compatibility
   */
  public function setCompatibility($compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return self::COMPATIBILITY_*
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * The dimension metadata contains the API name for this compatibility
   * information. The dimension metadata also contains other helpful information
   * like the UI name and description.
   *
   * @param DimensionMetadata $dimensionMetadata
   */
  public function setDimensionMetadata(DimensionMetadata $dimensionMetadata)
  {
    $this->dimensionMetadata = $dimensionMetadata;
  }
  /**
   * @return DimensionMetadata
   */
  public function getDimensionMetadata()
  {
    return $this->dimensionMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionCompatibility::class, 'Google_Service_AnalyticsData_DimensionCompatibility');
