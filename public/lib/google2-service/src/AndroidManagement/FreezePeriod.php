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

namespace Google\Service\AndroidManagement;

class FreezePeriod extends \Google\Model
{
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  protected $startDateType = Date::class;
  protected $startDateDataType = '';

  /**
   * The end date (inclusive) of the freeze period. Must be no later than 90
   * days from the start date. If the end date is earlier than the start date,
   * the freeze period is considered wrapping year-end. Note: day and month must
   * be set. year should not be set as it is not used. For example, {"month":
   * 1,"date": 30}.
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * The start date (inclusive) of the freeze period. Note: day and month must
   * be set. year should not be set as it is not used. For example, {"month":
   * 1,"date": 30}.
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreezePeriod::class, 'Google_Service_AndroidManagement_FreezePeriod');
