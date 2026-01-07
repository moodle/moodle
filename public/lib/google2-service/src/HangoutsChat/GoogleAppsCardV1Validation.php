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

class GoogleAppsCardV1Validation extends \Google\Model
{
  /**
   * Unspecified type. Do not use.
   */
  public const INPUT_TYPE_INPUT_TYPE_UNSPECIFIED = 'INPUT_TYPE_UNSPECIFIED';
  /**
   * Regular text that accepts all characters.
   */
  public const INPUT_TYPE_TEXT = 'TEXT';
  /**
   * An integer value.
   */
  public const INPUT_TYPE_INTEGER = 'INTEGER';
  /**
   * A float value.
   */
  public const INPUT_TYPE_FLOAT = 'FLOAT';
  /**
   * An email address.
   */
  public const INPUT_TYPE_EMAIL = 'EMAIL';
  /**
   * A emoji selected from system-provided emoji picker.
   */
  public const INPUT_TYPE_EMOJI_PICKER = 'EMOJI_PICKER';
  /**
   * Specify the character limit for text input widgets. Note that this is only
   * used for text input and is ignored for other widgets. [Google Workspace
   * add-ons and Chat apps](https://developers.google.com/workspace/extend):
   *
   * @var int
   */
  public $characterLimit;
  /**
   * Specify the type of the input widgets. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @var string
   */
  public $inputType;

  /**
   * Specify the character limit for text input widgets. Note that this is only
   * used for text input and is ignored for other widgets. [Google Workspace
   * add-ons and Chat apps](https://developers.google.com/workspace/extend):
   *
   * @param int $characterLimit
   */
  public function setCharacterLimit($characterLimit)
  {
    $this->characterLimit = $characterLimit;
  }
  /**
   * @return int
   */
  public function getCharacterLimit()
  {
    return $this->characterLimit;
  }
  /**
   * Specify the type of the input widgets. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * Accepted values: INPUT_TYPE_UNSPECIFIED, TEXT, INTEGER, FLOAT, EMAIL,
   * EMOJI_PICKER
   *
   * @param self::INPUT_TYPE_* $inputType
   */
  public function setInputType($inputType)
  {
    $this->inputType = $inputType;
  }
  /**
   * @return self::INPUT_TYPE_*
   */
  public function getInputType()
  {
    return $this->inputType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Validation::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Validation');
