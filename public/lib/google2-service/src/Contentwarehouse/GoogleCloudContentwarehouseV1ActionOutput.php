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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1ActionOutput extends \Google\Model
{
  /**
   * The unknown state.
   */
  public const ACTION_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * State indicating action executed successfully.
   */
  public const ACTION_STATE_ACTION_SUCCEEDED = 'ACTION_SUCCEEDED';
  /**
   * State indicating action failed.
   */
  public const ACTION_STATE_ACTION_FAILED = 'ACTION_FAILED';
  /**
   * State indicating action timed out.
   */
  public const ACTION_STATE_ACTION_TIMED_OUT = 'ACTION_TIMED_OUT';
  /**
   * State indicating action is pending.
   */
  public const ACTION_STATE_ACTION_PENDING = 'ACTION_PENDING';
  /**
   * ID of the action.
   *
   * @var string
   */
  public $actionId;
  /**
   * State of an action.
   *
   * @var string
   */
  public $actionState;
  /**
   * Action execution output message.
   *
   * @var string
   */
  public $outputMessage;

  /**
   * ID of the action.
   *
   * @param string $actionId
   */
  public function setActionId($actionId)
  {
    $this->actionId = $actionId;
  }
  /**
   * @return string
   */
  public function getActionId()
  {
    return $this->actionId;
  }
  /**
   * State of an action.
   *
   * Accepted values: UNKNOWN, ACTION_SUCCEEDED, ACTION_FAILED,
   * ACTION_TIMED_OUT, ACTION_PENDING
   *
   * @param self::ACTION_STATE_* $actionState
   */
  public function setActionState($actionState)
  {
    $this->actionState = $actionState;
  }
  /**
   * @return self::ACTION_STATE_*
   */
  public function getActionState()
  {
    return $this->actionState;
  }
  /**
   * Action execution output message.
   *
   * @param string $outputMessage
   */
  public function setOutputMessage($outputMessage)
  {
    $this->outputMessage = $outputMessage;
  }
  /**
   * @return string
   */
  public function getOutputMessage()
  {
    return $this->outputMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1ActionOutput::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1ActionOutput');
