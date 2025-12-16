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

namespace Google\Service\DatabaseMigrationService;

class SourceNumericFilter extends \Google\Model
{
  /**
   * Numeric filter option unspecified
   */
  public const NUMERIC_FILTER_OPTION_NUMERIC_FILTER_OPTION_UNSPECIFIED = 'NUMERIC_FILTER_OPTION_UNSPECIFIED';
  /**
   * Numeric filter option that matches all numeric columns.
   */
  public const NUMERIC_FILTER_OPTION_NUMERIC_FILTER_OPTION_ALL = 'NUMERIC_FILTER_OPTION_ALL';
  /**
   * Numeric filter option that matches columns having numeric datatypes with
   * specified precision and scale within the limited range of filter.
   */
  public const NUMERIC_FILTER_OPTION_NUMERIC_FILTER_OPTION_LIMIT = 'NUMERIC_FILTER_OPTION_LIMIT';
  /**
   * Numeric filter option that matches only the numeric columns with no
   * precision and scale specified.
   */
  public const NUMERIC_FILTER_OPTION_NUMERIC_FILTER_OPTION_LIMITLESS = 'NUMERIC_FILTER_OPTION_LIMITLESS';
  /**
   * Required. Enum to set the option defining the datatypes numeric filter has
   * to be applied to
   *
   * @var string
   */
  public $numericFilterOption;
  /**
   * Optional. The filter will match columns with precision smaller than or
   * equal to this number.
   *
   * @var int
   */
  public $sourceMaxPrecisionFilter;
  /**
   * Optional. The filter will match columns with scale smaller than or equal to
   * this number.
   *
   * @var int
   */
  public $sourceMaxScaleFilter;
  /**
   * Optional. The filter will match columns with precision greater than or
   * equal to this number.
   *
   * @var int
   */
  public $sourceMinPrecisionFilter;
  /**
   * Optional. The filter will match columns with scale greater than or equal to
   * this number.
   *
   * @var int
   */
  public $sourceMinScaleFilter;

  /**
   * Required. Enum to set the option defining the datatypes numeric filter has
   * to be applied to
   *
   * Accepted values: NUMERIC_FILTER_OPTION_UNSPECIFIED,
   * NUMERIC_FILTER_OPTION_ALL, NUMERIC_FILTER_OPTION_LIMIT,
   * NUMERIC_FILTER_OPTION_LIMITLESS
   *
   * @param self::NUMERIC_FILTER_OPTION_* $numericFilterOption
   */
  public function setNumericFilterOption($numericFilterOption)
  {
    $this->numericFilterOption = $numericFilterOption;
  }
  /**
   * @return self::NUMERIC_FILTER_OPTION_*
   */
  public function getNumericFilterOption()
  {
    return $this->numericFilterOption;
  }
  /**
   * Optional. The filter will match columns with precision smaller than or
   * equal to this number.
   *
   * @param int $sourceMaxPrecisionFilter
   */
  public function setSourceMaxPrecisionFilter($sourceMaxPrecisionFilter)
  {
    $this->sourceMaxPrecisionFilter = $sourceMaxPrecisionFilter;
  }
  /**
   * @return int
   */
  public function getSourceMaxPrecisionFilter()
  {
    return $this->sourceMaxPrecisionFilter;
  }
  /**
   * Optional. The filter will match columns with scale smaller than or equal to
   * this number.
   *
   * @param int $sourceMaxScaleFilter
   */
  public function setSourceMaxScaleFilter($sourceMaxScaleFilter)
  {
    $this->sourceMaxScaleFilter = $sourceMaxScaleFilter;
  }
  /**
   * @return int
   */
  public function getSourceMaxScaleFilter()
  {
    return $this->sourceMaxScaleFilter;
  }
  /**
   * Optional. The filter will match columns with precision greater than or
   * equal to this number.
   *
   * @param int $sourceMinPrecisionFilter
   */
  public function setSourceMinPrecisionFilter($sourceMinPrecisionFilter)
  {
    $this->sourceMinPrecisionFilter = $sourceMinPrecisionFilter;
  }
  /**
   * @return int
   */
  public function getSourceMinPrecisionFilter()
  {
    return $this->sourceMinPrecisionFilter;
  }
  /**
   * Optional. The filter will match columns with scale greater than or equal to
   * this number.
   *
   * @param int $sourceMinScaleFilter
   */
  public function setSourceMinScaleFilter($sourceMinScaleFilter)
  {
    $this->sourceMinScaleFilter = $sourceMinScaleFilter;
  }
  /**
   * @return int
   */
  public function getSourceMinScaleFilter()
  {
    return $this->sourceMinScaleFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceNumericFilter::class, 'Google_Service_DatabaseMigrationService_SourceNumericFilter');
