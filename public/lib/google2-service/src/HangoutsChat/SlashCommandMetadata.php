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

class SlashCommandMetadata extends \Google\Model
{
  /**
   * Default value for the enum. Don't use.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Add Chat app to space.
   */
  public const TYPE_ADD = 'ADD';
  /**
   * Invoke slash command in space.
   */
  public const TYPE_INVOKE = 'INVOKE';
  protected $botType = User::class;
  protected $botDataType = '';
  /**
   * The command ID of the invoked slash command.
   *
   * @var string
   */
  public $commandId;
  /**
   * The name of the invoked slash command.
   *
   * @var string
   */
  public $commandName;
  /**
   * Indicates whether the slash command is for a dialog.
   *
   * @var bool
   */
  public $triggersDialog;
  /**
   * The type of slash command.
   *
   * @var string
   */
  public $type;

  /**
   * The Chat app whose command was invoked.
   *
   * @param User $bot
   */
  public function setBot(User $bot)
  {
    $this->bot = $bot;
  }
  /**
   * @return User
   */
  public function getBot()
  {
    return $this->bot;
  }
  /**
   * The command ID of the invoked slash command.
   *
   * @param string $commandId
   */
  public function setCommandId($commandId)
  {
    $this->commandId = $commandId;
  }
  /**
   * @return string
   */
  public function getCommandId()
  {
    return $this->commandId;
  }
  /**
   * The name of the invoked slash command.
   *
   * @param string $commandName
   */
  public function setCommandName($commandName)
  {
    $this->commandName = $commandName;
  }
  /**
   * @return string
   */
  public function getCommandName()
  {
    return $this->commandName;
  }
  /**
   * Indicates whether the slash command is for a dialog.
   *
   * @param bool $triggersDialog
   */
  public function setTriggersDialog($triggersDialog)
  {
    $this->triggersDialog = $triggersDialog;
  }
  /**
   * @return bool
   */
  public function getTriggersDialog()
  {
    return $this->triggersDialog;
  }
  /**
   * The type of slash command.
   *
   * Accepted values: TYPE_UNSPECIFIED, ADD, INVOKE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlashCommandMetadata::class, 'Google_Service_HangoutsChat_SlashCommandMetadata');
