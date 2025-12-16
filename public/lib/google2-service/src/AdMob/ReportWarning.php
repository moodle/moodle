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

namespace Google\Service\AdMob;

class ReportWarning extends \Google\Model
{
  /**
   * Default value for an unset field. Do not use.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Some data in this report is aggregated based on a time zone different from
   * the requested time zone. This could happen if a local time-zone report has
   * the start time before the last time this time zone changed. The description
   * field will contain the date of the last time zone change.
   */
  public const TYPE_DATA_BEFORE_ACCOUNT_TIMEZONE_CHANGE = 'DATA_BEFORE_ACCOUNT_TIMEZONE_CHANGE';
  /**
   * There is an unusual delay in processing the source data for the requested
   * date range. The report results might be less up to date than usual. AdMob
   * is aware of the issue and is actively working to resolve it.
   */
  public const TYPE_DATA_DELAYED = 'DATA_DELAYED';
  /**
   * Warnings that are exposed without a specific type. Useful when new warning
   * types are added but the API is not changed yet.
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * The currency being requested is not the account currency. The earning
   * metrics will be based on the requested currency, and thus not a good
   * estimation of the final payment anymore, due to the currency rate
   * fluctuation.
   */
  public const TYPE_REPORT_CURRENCY_NOT_ACCOUNT_CURRENCY = 'REPORT_CURRENCY_NOT_ACCOUNT_CURRENCY';
  /**
   * Describes the details of the warning message, in English.
   *
   * @var string
   */
  public $description;
  /**
   * Type of the warning.
   *
   * @var string
   */
  public $type;

  /**
   * Describes the details of the warning message, in English.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Type of the warning.
   *
   * Accepted values: TYPE_UNSPECIFIED, DATA_BEFORE_ACCOUNT_TIMEZONE_CHANGE,
   * DATA_DELAYED, OTHER, REPORT_CURRENCY_NOT_ACCOUNT_CURRENCY
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
class_alias(ReportWarning::class, 'Google_Service_AdMob_ReportWarning');
