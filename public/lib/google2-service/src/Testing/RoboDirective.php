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

namespace Google\Service\Testing;

class RoboDirective extends \Google\Model
{
  /**
   * DO NOT USE. For proto versioning only.
   */
  public const ACTION_TYPE_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * Direct Robo to click on the specified element. No-op if specified element
   * is not clickable.
   */
  public const ACTION_TYPE_SINGLE_CLICK = 'SINGLE_CLICK';
  /**
   * Direct Robo to enter text on the specified element. No-op if specified
   * element is not enabled or does not allow text entry.
   */
  public const ACTION_TYPE_ENTER_TEXT = 'ENTER_TEXT';
  /**
   * Direct Robo to ignore interactions with a specific element.
   */
  public const ACTION_TYPE_IGNORE = 'IGNORE';
  /**
   * Required. The type of action that Robo should perform on the specified
   * element.
   *
   * @var string
   */
  public $actionType;
  /**
   * The text that Robo is directed to set. If left empty, the directive will be
   * treated as a CLICK on the element matching the resource_name.
   *
   * @var string
   */
  public $inputText;
  /**
   * Required. The android resource name of the target UI element. For example,
   * in Java: R.string.foo in xml: @string/foo Only the "foo" part is needed.
   * Reference doc:
   * https://developer.android.com/guide/topics/resources/accessing-
   * resources.html
   *
   * @var string
   */
  public $resourceName;

  /**
   * Required. The type of action that Robo should perform on the specified
   * element.
   *
   * Accepted values: ACTION_TYPE_UNSPECIFIED, SINGLE_CLICK, ENTER_TEXT, IGNORE
   *
   * @param self::ACTION_TYPE_* $actionType
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return self::ACTION_TYPE_*
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * The text that Robo is directed to set. If left empty, the directive will be
   * treated as a CLICK on the element matching the resource_name.
   *
   * @param string $inputText
   */
  public function setInputText($inputText)
  {
    $this->inputText = $inputText;
  }
  /**
   * @return string
   */
  public function getInputText()
  {
    return $this->inputText;
  }
  /**
   * Required. The android resource name of the target UI element. For example,
   * in Java: R.string.foo in xml: @string/foo Only the "foo" part is needed.
   * Reference doc:
   * https://developer.android.com/guide/topics/resources/accessing-
   * resources.html
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoboDirective::class, 'Google_Service_Testing_RoboDirective');
