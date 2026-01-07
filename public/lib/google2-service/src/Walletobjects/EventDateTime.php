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

class EventDateTime extends \Google\Model
{
  public const DOORS_OPEN_LABEL_DOORS_OPEN_LABEL_UNSPECIFIED = 'DOORS_OPEN_LABEL_UNSPECIFIED';
  public const DOORS_OPEN_LABEL_DOORS_OPEN = 'DOORS_OPEN';
  /**
   * Legacy alias for `DOORS_OPEN`. Deprecated.
   *
   * @deprecated
   */
  public const DOORS_OPEN_LABEL_doorsOpen = 'doorsOpen';
  public const DOORS_OPEN_LABEL_GATES_OPEN = 'GATES_OPEN';
  /**
   * Legacy alias for `GATES_OPEN`. Deprecated.
   *
   * @deprecated
   */
  public const DOORS_OPEN_LABEL_gatesOpen = 'gatesOpen';
  protected $customDoorsOpenLabelType = LocalizedString::class;
  protected $customDoorsOpenLabelDataType = '';
  /**
   * The date/time when the doors open at the venue. This is an ISO 8601
   * extended format date/time, with or without an offset. Time may be specified
   * up to nanosecond precision. Offsets may be specified with seconds precision
   * (even though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @var string
   */
  public $doorsOpen;
  /**
   * The label to use for the doors open value (`doorsOpen`) on the card detail
   * view. Each available option maps to a set of localized strings, so that
   * translations are shown to the user based on their locale. Both
   * `doorsOpenLabel` and `customDoorsOpenLabel` may not be set. If neither is
   * set, the label will default to "Doors Open", localized. If the doors open
   * field is unset, this label will not be used.
   *
   * @var string
   */
  public $doorsOpenLabel;
  /**
   * The date/time when the event ends. If the event spans multiple days, it
   * should be the end date/time on the last day. This is an ISO 8601 extended
   * format date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @var string
   */
  public $end;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventDateTime"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * The date/time when the event starts. If the event spans multiple days, it
   * should be the start date/time on the first day. This is an ISO 8601
   * extended format date/time, with or without an offset. Time may be specified
   * up to nanosecond precision. Offsets may be specified with seconds precision
   * (even though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @var string
   */
  public $start;

  /**
   * A custom label to use for the doors open value (`doorsOpen`) on the card
   * detail view. This should only be used if the default "Doors Open" label or
   * one of the `doorsOpenLabel` options is not sufficient. Both
   * `doorsOpenLabel` and `customDoorsOpenLabel` may not be set. If neither is
   * set, the label will default to "Doors Open", localized. If the doors open
   * field is unset, this label will not be used.
   *
   * @param LocalizedString $customDoorsOpenLabel
   */
  public function setCustomDoorsOpenLabel(LocalizedString $customDoorsOpenLabel)
  {
    $this->customDoorsOpenLabel = $customDoorsOpenLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomDoorsOpenLabel()
  {
    return $this->customDoorsOpenLabel;
  }
  /**
   * The date/time when the doors open at the venue. This is an ISO 8601
   * extended format date/time, with or without an offset. Time may be specified
   * up to nanosecond precision. Offsets may be specified with seconds precision
   * (even though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @param string $doorsOpen
   */
  public function setDoorsOpen($doorsOpen)
  {
    $this->doorsOpen = $doorsOpen;
  }
  /**
   * @return string
   */
  public function getDoorsOpen()
  {
    return $this->doorsOpen;
  }
  /**
   * The label to use for the doors open value (`doorsOpen`) on the card detail
   * view. Each available option maps to a set of localized strings, so that
   * translations are shown to the user based on their locale. Both
   * `doorsOpenLabel` and `customDoorsOpenLabel` may not be set. If neither is
   * set, the label will default to "Doors Open", localized. If the doors open
   * field is unset, this label will not be used.
   *
   * Accepted values: DOORS_OPEN_LABEL_UNSPECIFIED, DOORS_OPEN, doorsOpen,
   * GATES_OPEN, gatesOpen
   *
   * @param self::DOORS_OPEN_LABEL_* $doorsOpenLabel
   */
  public function setDoorsOpenLabel($doorsOpenLabel)
  {
    $this->doorsOpenLabel = $doorsOpenLabel;
  }
  /**
   * @return self::DOORS_OPEN_LABEL_*
   */
  public function getDoorsOpenLabel()
  {
    return $this->doorsOpenLabel;
  }
  /**
   * The date/time when the event ends. If the event spans multiple days, it
   * should be the end date/time on the last day. This is an ISO 8601 extended
   * format date/time, with or without an offset. Time may be specified up to
   * nanosecond precision. Offsets may be specified with seconds precision (even
   * though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventDateTime"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The date/time when the event starts. If the event spans multiple days, it
   * should be the start date/time on the first day. This is an ISO 8601
   * extended format date/time, with or without an offset. Time may be specified
   * up to nanosecond precision. Offsets may be specified with seconds precision
   * (even though offset seconds is not part of ISO 8601). For example:
   * `1985-04-12T23:20:50.52Z` would be 20 minutes and 50.52 seconds after the
   * 23rd hour of April 12th, 1985 in UTC. `1985-04-12T19:20:50.52-04:00` would
   * be 20 minutes and 50.52 seconds after the 19th hour of April 12th, 1985, 4
   * hours before UTC (same instant in time as the above example). If the event
   * were in New York, this would be the equivalent of Eastern Daylight Time
   * (EDT). Remember that offset varies in regions that observe Daylight Saving
   * Time (or Summer Time), depending on the time of the year.
   * `1985-04-12T19:20:50.52` would be 20 minutes and 50.52 seconds after the
   * 19th hour of April 12th, 1985 with no offset information. The portion of
   * the date/time without the offset is considered the "local date/time". This
   * should be the local date/time at the venue. For example, if the event
   * occurs at the 20th hour of June 5th, 2018 at the venue, the local date/time
   * portion should be `2018-06-05T20:00:00`. If the local date/time at the
   * venue is 4 hours before UTC, an offset of `-04:00` may be appended. Without
   * offset information, some rich features may not be available.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventDateTime::class, 'Google_Service_Walletobjects_EventDateTime');
