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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1DateRange extends \Google\Model
{
  protected $invoiceEndDateType = GoogleTypeDate::class;
  protected $invoiceEndDateDataType = '';
  protected $invoiceStartDateType = GoogleTypeDate::class;
  protected $invoiceStartDateDataType = '';
  protected $usageEndDateTimeType = GoogleTypeDateTime::class;
  protected $usageEndDateTimeDataType = '';
  protected $usageStartDateTimeType = GoogleTypeDateTime::class;
  protected $usageStartDateTimeDataType = '';

  /**
   * The latest invoice date (inclusive). If this value is not the last day of a
   * month, this will move it forward to the last day of the given month.
   *
   * @param GoogleTypeDate $invoiceEndDate
   */
  public function setInvoiceEndDate(GoogleTypeDate $invoiceEndDate)
  {
    $this->invoiceEndDate = $invoiceEndDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getInvoiceEndDate()
  {
    return $this->invoiceEndDate;
  }
  /**
   * The earliest invoice date (inclusive). If this value is not the first day
   * of a month, this will move it back to the first day of the given month.
   *
   * @param GoogleTypeDate $invoiceStartDate
   */
  public function setInvoiceStartDate(GoogleTypeDate $invoiceStartDate)
  {
    $this->invoiceStartDate = $invoiceStartDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getInvoiceStartDate()
  {
    return $this->invoiceStartDate;
  }
  /**
   * The latest usage date time (exclusive). If you use time groupings (daily,
   * weekly, etc), each group uses midnight to midnight (Pacific time). The
   * usage end date is rounded down to include all usage from the specified
   * date. We recommend that clients pass `usage_start_date_time` in Pacific
   * time.
   *
   * @param GoogleTypeDateTime $usageEndDateTime
   */
  public function setUsageEndDateTime(GoogleTypeDateTime $usageEndDateTime)
  {
    $this->usageEndDateTime = $usageEndDateTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getUsageEndDateTime()
  {
    return $this->usageEndDateTime;
  }
  /**
   * The earliest usage date time (inclusive). If you use time groupings (daily,
   * weekly, etc), each group uses midnight to midnight (Pacific time). The
   * usage start date is rounded down to include all usage from the specified
   * date. We recommend that clients pass `usage_start_date_time` in Pacific
   * time.
   *
   * @param GoogleTypeDateTime $usageStartDateTime
   */
  public function setUsageStartDateTime(GoogleTypeDateTime $usageStartDateTime)
  {
    $this->usageStartDateTime = $usageStartDateTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getUsageStartDateTime()
  {
    return $this->usageStartDateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1DateRange::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1DateRange');
