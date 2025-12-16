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

namespace Google\Service\CloudTalentSolution;

class CompensationFilter extends \Google\Collection
{
  /**
   * Filter type unspecified. Position holder, INVALID, should never be used.
   */
  public const TYPE_FILTER_TYPE_UNSPECIFIED = 'FILTER_TYPE_UNSPECIFIED';
  /**
   * Filter by `base compensation entry's` unit. A job is a match if and only if
   * the job contains a base CompensationEntry and the base CompensationEntry's
   * unit matches provided units. Populate one or more units. See
   * CompensationInfo.CompensationEntry for definition of base compensation
   * entry.
   */
  public const TYPE_UNIT_ONLY = 'UNIT_ONLY';
  /**
   * Filter by `base compensation entry's` unit and amount / range. A job is a
   * match if and only if the job contains a base CompensationEntry, and the
   * base entry's unit matches provided CompensationUnit and amount or range
   * overlaps with provided CompensationRange. See
   * CompensationInfo.CompensationEntry for definition of base compensation
   * entry. Set exactly one units and populate range.
   */
  public const TYPE_UNIT_AND_AMOUNT = 'UNIT_AND_AMOUNT';
  /**
   * Filter by annualized base compensation amount and `base compensation
   * entry's` unit. Populate range and zero or more units.
   */
  public const TYPE_ANNUALIZED_BASE_AMOUNT = 'ANNUALIZED_BASE_AMOUNT';
  /**
   * Filter by annualized total compensation amount and `base compensation
   * entry's` unit . Populate range and zero or more units.
   */
  public const TYPE_ANNUALIZED_TOTAL_AMOUNT = 'ANNUALIZED_TOTAL_AMOUNT';
  protected $collection_key = 'units';
  /**
   * If set to true, jobs with unspecified compensation range fields are
   * included.
   *
   * @var bool
   */
  public $includeJobsWithUnspecifiedCompensationRange;
  protected $rangeType = CompensationRange::class;
  protected $rangeDataType = '';
  /**
   * Required. Type of filter.
   *
   * @var string
   */
  public $type;
  /**
   * Required. Specify desired `base compensation entry's`
   * CompensationInfo.CompensationUnit.
   *
   * @var string[]
   */
  public $units;

  /**
   * If set to true, jobs with unspecified compensation range fields are
   * included.
   *
   * @param bool $includeJobsWithUnspecifiedCompensationRange
   */
  public function setIncludeJobsWithUnspecifiedCompensationRange($includeJobsWithUnspecifiedCompensationRange)
  {
    $this->includeJobsWithUnspecifiedCompensationRange = $includeJobsWithUnspecifiedCompensationRange;
  }
  /**
   * @return bool
   */
  public function getIncludeJobsWithUnspecifiedCompensationRange()
  {
    return $this->includeJobsWithUnspecifiedCompensationRange;
  }
  /**
   * Compensation range.
   *
   * @param CompensationRange $range
   */
  public function setRange(CompensationRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return CompensationRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * Required. Type of filter.
   *
   * Accepted values: FILTER_TYPE_UNSPECIFIED, UNIT_ONLY, UNIT_AND_AMOUNT,
   * ANNUALIZED_BASE_AMOUNT, ANNUALIZED_TOTAL_AMOUNT
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
  /**
   * Required. Specify desired `base compensation entry's`
   * CompensationInfo.CompensationUnit.
   *
   * @param string[] $units
   */
  public function setUnits($units)
  {
    $this->units = $units;
  }
  /**
   * @return string[]
   */
  public function getUnits()
  {
    return $this->units;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompensationFilter::class, 'Google_Service_CloudTalentSolution_CompensationFilter');
