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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1DateTimePicker extends \Google\Model
{
  /**
   * Users input a date and time.
   */
  public const TYPE_DATE_AND_TIME = 'DATE_AND_TIME';
  /**
   * Users input a date.
   */
  public const TYPE_DATE_ONLY = 'DATE_ONLY';
  /**
   * Users input a time.
   */
  public const TYPE_TIME_ONLY = 'TIME_ONLY';
  protected $hostAppDataSourceType = HostAppDataSourceMarkup::class;
  protected $hostAppDataSourceDataType = '';
  /**
   * The text that prompts users to input a date, a time, or a date and time.
   * For example, if users are scheduling an appointment, use a label such as
   * `Appointment date` or `Appointment date and time`.
   *
   * @var string
   */
  public $label;
  /**
   * The name by which the `DateTimePicker` is identified in a form input event.
   * For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $name;
  protected $onChangeActionType = GoogleAppsCardV1Action::class;
  protected $onChangeActionDataType = '';
  /**
   * The number representing the time zone offset from UTC, in minutes. If set,
   * the `value_ms_epoch` is displayed in the specified time zone. If unset, the
   * value defaults to the user's time zone setting.
   *
   * @var int
   */
  public $timezoneOffsetDate;
  /**
   * Whether the widget supports inputting a date, a time, or the date and time.
   *
   * @var string
   */
  public $type;
  /**
   * The default value displayed in the widget, in milliseconds since [Unix
   * epoch time](https://en.wikipedia.org/wiki/Unix_time). Specify the value
   * based on the type of picker (`DateTimePickerType`): * `DATE_AND_TIME`: a
   * calendar date and time in UTC. For example, to represent January 1, 2023 at
   * 12:00 PM UTC, use `1672574400000`. * `DATE_ONLY`: a calendar date at
   * 00:00:00 UTC. For example, to represent January 1, 2023, use
   * `1672531200000`. * `TIME_ONLY`: a time in UTC. For example, to represent
   * 12:00 PM, use `43200000` (or `12 * 60 * 60 * 1000`).
   *
   * @var string
   */
  public $valueMsEpoch;

  /**
   * A data source that's unique to a Google Workspace host application, such as
   * Gmail emails, Google Calendar events, or Google Chat messages. Available
   * for Google Workspace add-ons that extend Google Workspace Studio.
   * Unavailable for Google Chat apps.
   *
   * @param HostAppDataSourceMarkup $hostAppDataSource
   */
  public function setHostAppDataSource(HostAppDataSourceMarkup $hostAppDataSource)
  {
    $this->hostAppDataSource = $hostAppDataSource;
  }
  /**
   * @return HostAppDataSourceMarkup
   */
  public function getHostAppDataSource()
  {
    return $this->hostAppDataSource;
  }
  /**
   * The text that prompts users to input a date, a time, or a date and time.
   * For example, if users are scheduling an appointment, use a label such as
   * `Appointment date` or `Appointment date and time`.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The name by which the `DateTimePicker` is identified in a form input event.
   * For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Triggered when the user clicks **Save** or **Clear** from the
   * `DateTimePicker` interface.
   *
   * @param GoogleAppsCardV1Action $onChangeAction
   */
  public function setOnChangeAction(GoogleAppsCardV1Action $onChangeAction)
  {
    $this->onChangeAction = $onChangeAction;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getOnChangeAction()
  {
    return $this->onChangeAction;
  }
  /**
   * The number representing the time zone offset from UTC, in minutes. If set,
   * the `value_ms_epoch` is displayed in the specified time zone. If unset, the
   * value defaults to the user's time zone setting.
   *
   * @param int $timezoneOffsetDate
   */
  public function setTimezoneOffsetDate($timezoneOffsetDate)
  {
    $this->timezoneOffsetDate = $timezoneOffsetDate;
  }
  /**
   * @return int
   */
  public function getTimezoneOffsetDate()
  {
    return $this->timezoneOffsetDate;
  }
  /**
   * Whether the widget supports inputting a date, a time, or the date and time.
   *
   * Accepted values: DATE_AND_TIME, DATE_ONLY, TIME_ONLY
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
   * The default value displayed in the widget, in milliseconds since [Unix
   * epoch time](https://en.wikipedia.org/wiki/Unix_time). Specify the value
   * based on the type of picker (`DateTimePickerType`): * `DATE_AND_TIME`: a
   * calendar date and time in UTC. For example, to represent January 1, 2023 at
   * 12:00 PM UTC, use `1672574400000`. * `DATE_ONLY`: a calendar date at
   * 00:00:00 UTC. For example, to represent January 1, 2023, use
   * `1672531200000`. * `TIME_ONLY`: a time in UTC. For example, to represent
   * 12:00 PM, use `43200000` (or `12 * 60 * 60 * 1000`).
   *
   * @param string $valueMsEpoch
   */
  public function setValueMsEpoch($valueMsEpoch)
  {
    $this->valueMsEpoch = $valueMsEpoch;
  }
  /**
   * @return string
   */
  public function getValueMsEpoch()
  {
    return $this->valueMsEpoch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1DateTimePicker::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1DateTimePicker');
