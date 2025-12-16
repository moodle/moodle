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

namespace Google\Service\Sheets;

class ConditionValue extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const RELATIVE_DATE_RELATIVE_DATE_UNSPECIFIED = 'RELATIVE_DATE_UNSPECIFIED';
  /**
   * The value is one year before today.
   */
  public const RELATIVE_DATE_PAST_YEAR = 'PAST_YEAR';
  /**
   * The value is one month before today.
   */
  public const RELATIVE_DATE_PAST_MONTH = 'PAST_MONTH';
  /**
   * The value is one week before today.
   */
  public const RELATIVE_DATE_PAST_WEEK = 'PAST_WEEK';
  /**
   * The value is yesterday.
   */
  public const RELATIVE_DATE_YESTERDAY = 'YESTERDAY';
  /**
   * The value is today.
   */
  public const RELATIVE_DATE_TODAY = 'TODAY';
  /**
   * The value is tomorrow.
   */
  public const RELATIVE_DATE_TOMORROW = 'TOMORROW';
  /**
   * A relative date (based on the current date). Valid only if the type is
   * DATE_BEFORE, DATE_AFTER, DATE_ON_OR_BEFORE or DATE_ON_OR_AFTER. Relative
   * dates are not supported in data validation. They are supported only in
   * conditional formatting and conditional filters.
   *
   * @var string
   */
  public $relativeDate;
  /**
   * A value the condition is based on. The value is parsed as if the user typed
   * into a cell. Formulas are supported (and must begin with an `=` or a '+').
   *
   * @var string
   */
  public $userEnteredValue;

  /**
   * A relative date (based on the current date). Valid only if the type is
   * DATE_BEFORE, DATE_AFTER, DATE_ON_OR_BEFORE or DATE_ON_OR_AFTER. Relative
   * dates are not supported in data validation. They are supported only in
   * conditional formatting and conditional filters.
   *
   * Accepted values: RELATIVE_DATE_UNSPECIFIED, PAST_YEAR, PAST_MONTH,
   * PAST_WEEK, YESTERDAY, TODAY, TOMORROW
   *
   * @param self::RELATIVE_DATE_* $relativeDate
   */
  public function setRelativeDate($relativeDate)
  {
    $this->relativeDate = $relativeDate;
  }
  /**
   * @return self::RELATIVE_DATE_*
   */
  public function getRelativeDate()
  {
    return $this->relativeDate;
  }
  /**
   * A value the condition is based on. The value is parsed as if the user typed
   * into a cell. Formulas are supported (and must begin with an `=` or a '+').
   *
   * @param string $userEnteredValue
   */
  public function setUserEnteredValue($userEnteredValue)
  {
    $this->userEnteredValue = $userEnteredValue;
  }
  /**
   * @return string
   */
  public function getUserEnteredValue()
  {
    return $this->userEnteredValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConditionValue::class, 'Google_Service_Sheets_ConditionValue');
