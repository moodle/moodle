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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2CreateConversationModelOperationMetadata extends \Google\Model
{
  /**
   * Invalid.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Request is submitted, but training has not started yet. The model may
   * remain in this state until there is enough capacity to start training.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The training has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The training has succeeded.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The training has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The training is in cancelling state.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * Custom model is training.
   */
  public const STATE_TRAINING = 'TRAINING';
  /**
   * The resource name of the conversation model. Format:
   * `projects//conversationModels/`
   *
   * @var string
   */
  public $conversationModel;
  /**
   * Timestamp when the request to create conversation model is submitted. The
   * time is measured on server side.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time when the operation finished.
   *
   * @var string
   */
  public $doneTime;
  /**
   * State of CreateConversationModel operation.
   *
   * @var string
   */
  public $state;

  /**
   * The resource name of the conversation model. Format:
   * `projects//conversationModels/`
   *
   * @param string $conversationModel
   */
  public function setConversationModel($conversationModel)
  {
    $this->conversationModel = $conversationModel;
  }
  /**
   * @return string
   */
  public function getConversationModel()
  {
    return $this->conversationModel;
  }
  /**
   * Timestamp when the request to create conversation model is submitted. The
   * time is measured on server side.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time when the operation finished.
   *
   * @param string $doneTime
   */
  public function setDoneTime($doneTime)
  {
    $this->doneTime = $doneTime;
  }
  /**
   * @return string
   */
  public function getDoneTime()
  {
    return $this->doneTime;
  }
  /**
   * State of CreateConversationModel operation.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, SUCCEEDED, FAILED, CANCELLED,
   * CANCELLING, TRAINING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2CreateConversationModelOperationMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2CreateConversationModelOperationMetadata');
