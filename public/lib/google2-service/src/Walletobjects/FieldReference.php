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

namespace Google\Service\Walletobjects;

class FieldReference extends \Google\Model
{
  /**
   * Default option when no format is specified, when selected, no formatting
   * will be applied.
   */
  public const DATE_FORMAT_DATE_FORMAT_UNSPECIFIED = 'DATE_FORMAT_UNSPECIFIED';
  /**
   * Renders `2018-12-14T13:00:00` as `Dec 14, 1:00 PM` in `en_US`.
   */
  public const DATE_FORMAT_DATE_TIME = 'DATE_TIME';
  /**
   * Legacy alias for `DATE_TIME`. Deprecated.
   *
   * @deprecated
   */
  public const DATE_FORMAT_dateTime = 'dateTime';
  /**
   * Renders `2018-12-14T13:00:00` as `Dec 14` in `en_US`.
   */
  public const DATE_FORMAT_DATE_ONLY = 'DATE_ONLY';
  /**
   * Legacy alias for `DATE_ONLY`. Deprecated.
   *
   * @deprecated
   */
  public const DATE_FORMAT_dateOnly = 'dateOnly';
  /**
   * Renders `2018-12-14T13:00:00` as `1:00 PM` in `en_US`.
   */
  public const DATE_FORMAT_TIME_ONLY = 'TIME_ONLY';
  /**
   * Legacy alias for `TIME_ONLY`. Deprecated.
   *
   * @deprecated
   */
  public const DATE_FORMAT_timeOnly = 'timeOnly';
  /**
   * Renders `2018-12-14T13:00:00` as `Dec 14, 2018, 1:00 PM` in `en_US`.
   */
  public const DATE_FORMAT_DATE_TIME_YEAR = 'DATE_TIME_YEAR';
  /**
   * Legacy alias for `DATE_TIME_YEAR`. Deprecated.
   *
   * @deprecated
   */
  public const DATE_FORMAT_dateTimeYear = 'dateTimeYear';
  /**
   * Renders `2018-12-14T13:00:00` as `Dec 14, 2018` in `en_US`.
   */
  public const DATE_FORMAT_DATE_YEAR = 'DATE_YEAR';
  /**
   * Legacy alias for `DATE_YEAR`. Deprecated.
   *
   * @deprecated
   */
  public const DATE_FORMAT_dateYear = 'dateYear';
  /**
   * Renders `2018-12-14T13:00:00` as `2018-12`.
   */
  public const DATE_FORMAT_YEAR_MONTH = 'YEAR_MONTH';
  /**
   * Renders `2018-12-14T13:00:00` as `2018-12-14`.
   */
  public const DATE_FORMAT_YEAR_MONTH_DAY = 'YEAR_MONTH_DAY';
  /**
   * Only valid if the `fieldPath` references a date field. Chooses how the date
   * field will be formatted and displayed in the UI.
   *
   * @var string
   */
  public $dateFormat;
  /**
   * Path to the field being referenced, prefixed with "object" or "class" and
   * separated with dots. For example, it may be the string
   * "object.purchaseDetails.purchasePrice".
   *
   * @var string
   */
  public $fieldPath;

  /**
   * Only valid if the `fieldPath` references a date field. Chooses how the date
   * field will be formatted and displayed in the UI.
   *
   * Accepted values: DATE_FORMAT_UNSPECIFIED, DATE_TIME, dateTime, DATE_ONLY,
   * dateOnly, TIME_ONLY, timeOnly, DATE_TIME_YEAR, dateTimeYear, DATE_YEAR,
   * dateYear, YEAR_MONTH, YEAR_MONTH_DAY
   *
   * @param self::DATE_FORMAT_* $dateFormat
   */
  public function setDateFormat($dateFormat)
  {
    $this->dateFormat = $dateFormat;
  }
  /**
   * @return self::DATE_FORMAT_*
   */
  public function getDateFormat()
  {
    return $this->dateFormat;
  }
  /**
   * Path to the field being referenced, prefixed with "object" or "class" and
   * separated with dots. For example, it may be the string
   * "object.purchaseDetails.purchasePrice".
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldReference::class, 'Google_Service_Walletobjects_FieldReference');
