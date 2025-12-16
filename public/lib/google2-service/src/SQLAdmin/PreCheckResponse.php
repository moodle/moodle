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

namespace Google\Service\SQLAdmin;

class PreCheckResponse extends \Google\Collection
{
  /**
   * Default unspecified value to prevent unintended behavior changes.
   */
  public const MESSAGE_TYPE_MESSAGE_TYPE_UNSPECIFIED = 'MESSAGE_TYPE_UNSPECIFIED';
  /**
   * General informational messages that don't require action.
   */
  public const MESSAGE_TYPE_INFO = 'INFO';
  /**
   * Warnings that might impact the upgrade but don't block it.
   */
  public const MESSAGE_TYPE_WARNING = 'WARNING';
  /**
   * Errors that a user must resolve before proceeding with the upgrade.
   */
  public const MESSAGE_TYPE_ERROR = 'ERROR';
  protected $collection_key = 'actionsRequired';
  /**
   * The actions that the user needs to take. Use repeated for multiple actions.
   *
   * @var string[]
   */
  public $actionsRequired;
  /**
   * The message to be displayed to the user.
   *
   * @var string
   */
  public $message;
  /**
   * The type of message whether it is an info, warning, or error.
   *
   * @var string
   */
  public $messageType;

  /**
   * The actions that the user needs to take. Use repeated for multiple actions.
   *
   * @param string[] $actionsRequired
   */
  public function setActionsRequired($actionsRequired)
  {
    $this->actionsRequired = $actionsRequired;
  }
  /**
   * @return string[]
   */
  public function getActionsRequired()
  {
    return $this->actionsRequired;
  }
  /**
   * The message to be displayed to the user.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The type of message whether it is an info, warning, or error.
   *
   * Accepted values: MESSAGE_TYPE_UNSPECIFIED, INFO, WARNING, ERROR
   *
   * @param self::MESSAGE_TYPE_* $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return self::MESSAGE_TYPE_*
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreCheckResponse::class, 'Google_Service_SQLAdmin_PreCheckResponse');
