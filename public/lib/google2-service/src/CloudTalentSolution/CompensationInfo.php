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

class CompensationInfo extends \Google\Collection
{
  protected $collection_key = 'entries';
  protected $annualizedBaseCompensationRangeType = CompensationRange::class;
  protected $annualizedBaseCompensationRangeDataType = '';
  protected $annualizedTotalCompensationRangeType = CompensationRange::class;
  protected $annualizedTotalCompensationRangeDataType = '';
  protected $entriesType = CompensationEntry::class;
  protected $entriesDataType = 'array';

  /**
   * Output only. Annualized base compensation range. Computed as base
   * compensation entry's CompensationEntry.amount times
   * CompensationEntry.expected_units_per_year. See CompensationEntry for
   * explanation on compensation annualization.
   *
   * @param CompensationRange $annualizedBaseCompensationRange
   */
  public function setAnnualizedBaseCompensationRange(CompensationRange $annualizedBaseCompensationRange)
  {
    $this->annualizedBaseCompensationRange = $annualizedBaseCompensationRange;
  }
  /**
   * @return CompensationRange
   */
  public function getAnnualizedBaseCompensationRange()
  {
    return $this->annualizedBaseCompensationRange;
  }
  /**
   * Output only. Annualized total compensation range. Computed as all
   * compensation entries' CompensationEntry.amount times
   * CompensationEntry.expected_units_per_year. See CompensationEntry for
   * explanation on compensation annualization.
   *
   * @param CompensationRange $annualizedTotalCompensationRange
   */
  public function setAnnualizedTotalCompensationRange(CompensationRange $annualizedTotalCompensationRange)
  {
    $this->annualizedTotalCompensationRange = $annualizedTotalCompensationRange;
  }
  /**
   * @return CompensationRange
   */
  public function getAnnualizedTotalCompensationRange()
  {
    return $this->annualizedTotalCompensationRange;
  }
  /**
   * Job compensation information. At most one entry can be of type
   * CompensationInfo.CompensationType.BASE, which is referred as **base
   * compensation entry** for the job.
   *
   * @param CompensationEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return CompensationEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompensationInfo::class, 'Google_Service_CloudTalentSolution_CompensationInfo');
