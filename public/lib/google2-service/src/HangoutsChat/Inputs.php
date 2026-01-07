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

class Inputs extends \Google\Model
{
  protected $dateInputType = DateInput::class;
  protected $dateInputDataType = '';
  protected $dateTimeInputType = DateTimeInput::class;
  protected $dateTimeInputDataType = '';
  protected $stringInputsType = StringInputs::class;
  protected $stringInputsDataType = '';
  protected $timeInputType = TimeInput::class;
  protected $timeInputDataType = '';

  /**
   * Date input values from a [`DateTimePicker`](https://developers.google.com/c
   * hat/api/reference/rest/v1/cards#DateTimePicker) widget that only accepts
   * date values.
   *
   * @param DateInput $dateInput
   */
  public function setDateInput(DateInput $dateInput)
  {
    $this->dateInput = $dateInput;
  }
  /**
   * @return DateInput
   */
  public function getDateInput()
  {
    return $this->dateInput;
  }
  /**
   * Date and time input values from a [`DateTimePicker`](https://developers.goo
   * gle.com/chat/api/reference/rest/v1/cards#DateTimePicker) widget that
   * accepts both a date and time.
   *
   * @param DateTimeInput $dateTimeInput
   */
  public function setDateTimeInput(DateTimeInput $dateTimeInput)
  {
    $this->dateTimeInput = $dateTimeInput;
  }
  /**
   * @return DateTimeInput
   */
  public function getDateTimeInput()
  {
    return $this->dateTimeInput;
  }
  /**
   * A list of strings that represent the values that the user inputs in a
   * widget. If the widget only accepts one value, such as a [`TextInput`](https
   * ://developers.google.com/chat/api/reference/rest/v1/cards#TextInput)
   * widget, the list contains one string object. If the widget accepts multiple
   * values, such as a [`SelectionInput`](https://developers.google.com/chat/api
   * /reference/rest/v1/cards#selectioninput) widget of checkboxes, the list
   * contains a string object for each value that the user inputs or selects.
   *
   * @param StringInputs $stringInputs
   */
  public function setStringInputs(StringInputs $stringInputs)
  {
    $this->stringInputs = $stringInputs;
  }
  /**
   * @return StringInputs
   */
  public function getStringInputs()
  {
    return $this->stringInputs;
  }
  /**
   * Time input values from a [`DateTimePicker`](https://developers.google.com/c
   * hat/api/reference/rest/v1/cards#DateTimePicker) widget that only accepts
   * time values.
   *
   * @param TimeInput $timeInput
   */
  public function setTimeInput(TimeInput $timeInput)
  {
    $this->timeInput = $timeInput;
  }
  /**
   * @return TimeInput
   */
  public function getTimeInput()
  {
    return $this->timeInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Inputs::class, 'Google_Service_HangoutsChat_Inputs');
