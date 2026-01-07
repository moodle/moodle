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

class GoogleCloudDialogflowV2CreateConversationModelEvaluationOperationMetadata extends \Google\Model
{
  /**
   * Operation status not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The operation is being prepared.
   */
  public const STATE_INITIALIZING = 'INITIALIZING';
  /**
   * The operation is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The operation is cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The operation has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The operation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The resource name of the conversation model. Format:
   * `projects//locations//conversationModels/`
   *
   * @var string
   */
  public $conversationModel;
  /**
   * The resource name of the conversation model. Format:
   * `projects//locations//conversationModels//evaluations/`
   *
   * @var string
   */
  public $conversationModelEvaluation;
  /**
   * Timestamp when the request to create conversation model was submitted. The
   * time is measured on server side.
   *
   * @var string
   */
  public $createTime;
  /**
   * State of CreateConversationModel operation.
   *
   * @var string
   */
  public $state;

  /**
   * The resource name of the conversation model. Format:
   * `projects//locations//conversationModels/`
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
   * The resource name of the conversation model. Format:
   * `projects//locations//conversationModels//evaluations/`
   *
   * @param string $conversationModelEvaluation
   */
  public function setConversationModelEvaluation($conversationModelEvaluation)
  {
    $this->conversationModelEvaluation = $conversationModelEvaluation;
  }
  /**
   * @return string
   */
  public function getConversationModelEvaluation()
  {
    return $this->conversationModelEvaluation;
  }
  /**
   * Timestamp when the request to create conversation model was submitted. The
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
   * State of CreateConversationModel operation.
   *
   * Accepted values: STATE_UNSPECIFIED, INITIALIZING, RUNNING, CANCELLED,
   * SUCCEEDED, FAILED
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
class_alias(GoogleCloudDialogflowV2CreateConversationModelEvaluationOperationMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2CreateConversationModelEvaluationOperationMetadata');
