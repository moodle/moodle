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

class GoogleAppsCardV1SwitchControl extends \Google\Model
{
  /**
   * A toggle-style switch.
   */
  public const CONTROL_TYPE_SWITCH = 'SWITCH';
  /**
   * Deprecated in favor of `CHECK_BOX`.
   */
  public const CONTROL_TYPE_CHECKBOX = 'CHECKBOX';
  /**
   * A checkbox.
   */
  public const CONTROL_TYPE_CHECK_BOX = 'CHECK_BOX';
  /**
   * How the switch appears in the user interface. [Google Workspace add-ons and
   * Chat apps](https://developers.google.com/workspace/extend):
   *
   * @var string
   */
  public $controlType;
  /**
   * The name by which the switch widget is identified in a form input event.
   * For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $name;
  protected $onChangeActionType = GoogleAppsCardV1Action::class;
  protected $onChangeActionDataType = '';
  /**
   * When `true`, the switch is selected.
   *
   * @var bool
   */
  public $selected;
  /**
   * The value entered by a user, returned as part of a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $value;

  /**
   * How the switch appears in the user interface. [Google Workspace add-ons and
   * Chat apps](https://developers.google.com/workspace/extend):
   *
   * Accepted values: SWITCH, CHECKBOX, CHECK_BOX
   *
   * @param self::CONTROL_TYPE_* $controlType
   */
  public function setControlType($controlType)
  {
    $this->controlType = $controlType;
  }
  /**
   * @return self::CONTROL_TYPE_*
   */
  public function getControlType()
  {
    return $this->controlType;
  }
  /**
   * The name by which the switch widget is identified in a form input event.
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
   * The action to perform when the switch state is changed, such as what
   * function to run.
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
   * When `true`, the switch is selected.
   *
   * @param bool $selected
   */
  public function setSelected($selected)
  {
    $this->selected = $selected;
  }
  /**
   * @return bool
   */
  public function getSelected()
  {
    return $this->selected;
  }
  /**
   * The value entered by a user, returned as part of a form input event. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1SwitchControl::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1SwitchControl');
