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

namespace Google\Service\Docs;

class DateElementProperties extends \Google\Model
{
  /**
   * The date format is unspecified.
   */
  public const DATE_FORMAT_DATE_FORMAT_UNSPECIFIED = 'DATE_FORMAT_UNSPECIFIED';
  /**
   * Output only. The date format is imported from an external source.
   */
  public const DATE_FORMAT_DATE_FORMAT_CUSTOM = 'DATE_FORMAT_CUSTOM';
  /**
   * The date format is an abbreviated month followed by the day. For example,
   * "Jan 1".
   */
  public const DATE_FORMAT_DATE_FORMAT_MONTH_DAY_ABBREVIATED = 'DATE_FORMAT_MONTH_DAY_ABBREVIATED';
  /**
   * The date format is a month followed by the day. For example, "January 01".
   */
  public const DATE_FORMAT_DATE_FORMAT_MONTH_DAY_FULL = 'DATE_FORMAT_MONTH_DAY_FULL';
  /**
   * The date format is an abbreviated month followed by the day and the year.
   * For example, "Jan 1, 1970".
   */
  public const DATE_FORMAT_DATE_FORMAT_MONTH_DAY_YEAR_ABBREVIATED = 'DATE_FORMAT_MONTH_DAY_YEAR_ABBREVIATED';
  /**
   * The date format is in ISO 8601 format. For example, "1970-01-01".
   */
  public const DATE_FORMAT_DATE_FORMAT_ISO8601 = 'DATE_FORMAT_ISO8601';
  /**
   * The time format is unspecified.
   */
  public const TIME_FORMAT_TIME_FORMAT_UNSPECIFIED = 'TIME_FORMAT_UNSPECIFIED';
  /**
   * Indicates that the date does not have a time.
   */
  public const TIME_FORMAT_TIME_FORMAT_DISABLED = 'TIME_FORMAT_DISABLED';
  /**
   * The time format shows the hour and minute. For example, "Jan 1, 1970 12:00
   * PM".
   */
  public const TIME_FORMAT_TIME_FORMAT_HOUR_MINUTE = 'TIME_FORMAT_HOUR_MINUTE';
  /**
   * The time format shows the hour, minute, and timezone. For example, "Jan 1,
   * 1970 12:00 PM UTC".
   */
  public const TIME_FORMAT_TIME_FORMAT_HOUR_MINUTE_TIMEZONE = 'TIME_FORMAT_HOUR_MINUTE_TIMEZONE';
  /**
   * Determines how the date part of the DateElement will be displayed in the
   * document. If unset, the default value is
   * DATE_FORMAT_MONTH_DAY_YEAR_ABBREVIATED, indicating the DateElement will be
   * formatted as `MMM d, y` in `en_US`, or locale specific equivalent.
   *
   * @var string
   */
  public $dateFormat;
  /**
   * Output only. Indicates how the DateElement is displayed in the document.
   *
   * @var string
   */
  public $displayText;
  /**
   * The locale of the document, as defined by the Unicode Common Locale Data
   * Repository (CLDR) project. For example, `en_US`. If unset, the default
   * locale is `en_US`.
   *
   * @var string
   */
  public $locale;
  /**
   * Determines how the time part of the DateElement will be displayed in the
   * document. If unset, the default value is TIME_FORMAT_DISABLED, indicating
   * no time should be shown.
   *
   * @var string
   */
  public $timeFormat;
  /**
   * The time zone of the DateElement, as defined by the Unicode Common Locale
   * Data Repository (CLDR) project. For example, `America/New York`. If unset,
   * the default time zone is `etc/UTC`.
   *
   * @var string
   */
  public $timeZoneId;
  /**
   * The point in time to represent, in seconds and nanoseconds since Unix
   * epoch: January 1, 1970 at midnight UTC. Timestamp is expected to be in UTC.
   * If time_zone_id is set, the timestamp is adjusted according to the time
   * zone. For example, a timestamp of `18000` with a date format of
   * `DATE_FORMAT_ISO8601` and time format of `TIME_FORMAT_HOUR_MINUTE` would be
   * displayed as `1970-01-01 5:00 AM`. A timestamp of `18000` with date format
   * of `DATE_FORMAT_8SO8601`, time format of `TIME_FORMAT_HOUR_MINUTE`, and
   * time zone set to `America/New_York` will instead be `1970-01-01 12:00 AM`.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Determines how the date part of the DateElement will be displayed in the
   * document. If unset, the default value is
   * DATE_FORMAT_MONTH_DAY_YEAR_ABBREVIATED, indicating the DateElement will be
   * formatted as `MMM d, y` in `en_US`, or locale specific equivalent.
   *
   * Accepted values: DATE_FORMAT_UNSPECIFIED, DATE_FORMAT_CUSTOM,
   * DATE_FORMAT_MONTH_DAY_ABBREVIATED, DATE_FORMAT_MONTH_DAY_FULL,
   * DATE_FORMAT_MONTH_DAY_YEAR_ABBREVIATED, DATE_FORMAT_ISO8601
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
   * Output only. Indicates how the DateElement is displayed in the document.
   *
   * @param string $displayText
   */
  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  /**
   * @return string
   */
  public function getDisplayText()
  {
    return $this->displayText;
  }
  /**
   * The locale of the document, as defined by the Unicode Common Locale Data
   * Repository (CLDR) project. For example, `en_US`. If unset, the default
   * locale is `en_US`.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Determines how the time part of the DateElement will be displayed in the
   * document. If unset, the default value is TIME_FORMAT_DISABLED, indicating
   * no time should be shown.
   *
   * Accepted values: TIME_FORMAT_UNSPECIFIED, TIME_FORMAT_DISABLED,
   * TIME_FORMAT_HOUR_MINUTE, TIME_FORMAT_HOUR_MINUTE_TIMEZONE
   *
   * @param self::TIME_FORMAT_* $timeFormat
   */
  public function setTimeFormat($timeFormat)
  {
    $this->timeFormat = $timeFormat;
  }
  /**
   * @return self::TIME_FORMAT_*
   */
  public function getTimeFormat()
  {
    return $this->timeFormat;
  }
  /**
   * The time zone of the DateElement, as defined by the Unicode Common Locale
   * Data Repository (CLDR) project. For example, `America/New York`. If unset,
   * the default time zone is `etc/UTC`.
   *
   * @param string $timeZoneId
   */
  public function setTimeZoneId($timeZoneId)
  {
    $this->timeZoneId = $timeZoneId;
  }
  /**
   * @return string
   */
  public function getTimeZoneId()
  {
    return $this->timeZoneId;
  }
  /**
   * The point in time to represent, in seconds and nanoseconds since Unix
   * epoch: January 1, 1970 at midnight UTC. Timestamp is expected to be in UTC.
   * If time_zone_id is set, the timestamp is adjusted according to the time
   * zone. For example, a timestamp of `18000` with a date format of
   * `DATE_FORMAT_ISO8601` and time format of `TIME_FORMAT_HOUR_MINUTE` would be
   * displayed as `1970-01-01 5:00 AM`. A timestamp of `18000` with date format
   * of `DATE_FORMAT_8SO8601`, time format of `TIME_FORMAT_HOUR_MINUTE`, and
   * time zone set to `America/New_York` will instead be `1970-01-01 12:00 AM`.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DateElementProperties::class, 'Google_Service_Docs_DateElementProperties');
