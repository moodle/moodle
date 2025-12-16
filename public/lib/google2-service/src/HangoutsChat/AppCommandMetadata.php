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

class AppCommandMetadata extends \Google\Model
{
  /**
   * Default value. Unspecified.
   */
  public const APP_COMMAND_TYPE_APP_COMMAND_TYPE_UNSPECIFIED = 'APP_COMMAND_TYPE_UNSPECIFIED';
  /**
   * A slash command. The user sends the command in a Chat message.
   */
  public const APP_COMMAND_TYPE_SLASH_COMMAND = 'SLASH_COMMAND';
  /**
   * A quick command. The user selects the command from the Chat menu in the
   * message reply area.
   */
  public const APP_COMMAND_TYPE_QUICK_COMMAND = 'QUICK_COMMAND';
  /**
   * The ID for the command specified in the Chat API configuration.
   *
   * @var int
   */
  public $appCommandId;
  /**
   * The type of Chat app command.
   *
   * @var string
   */
  public $appCommandType;

  /**
   * The ID for the command specified in the Chat API configuration.
   *
   * @param int $appCommandId
   */
  public function setAppCommandId($appCommandId)
  {
    $this->appCommandId = $appCommandId;
  }
  /**
   * @return int
   */
  public function getAppCommandId()
  {
    return $this->appCommandId;
  }
  /**
   * The type of Chat app command.
   *
   * Accepted values: APP_COMMAND_TYPE_UNSPECIFIED, SLASH_COMMAND, QUICK_COMMAND
   *
   * @param self::APP_COMMAND_TYPE_* $appCommandType
   */
  public function setAppCommandType($appCommandType)
  {
    $this->appCommandType = $appCommandType;
  }
  /**
   * @return self::APP_COMMAND_TYPE_*
   */
  public function getAppCommandType()
  {
    return $this->appCommandType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppCommandMetadata::class, 'Google_Service_HangoutsChat_AppCommandMetadata');
