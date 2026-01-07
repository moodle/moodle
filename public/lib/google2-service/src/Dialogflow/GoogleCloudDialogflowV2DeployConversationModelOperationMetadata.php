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

class GoogleCloudDialogflowV2DeployConversationModelOperationMetadata extends \Google\Model
{
  /**
   * The resource name of the conversation model. Format:
   * `projects//conversationModels/`
   *
   * @var string
   */
  public $conversationModel;
  /**
   * Timestamp when request to deploy conversation model was submitted. The time
   * is measured on server side.
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
   * Timestamp when request to deploy conversation model was submitted. The time
   * is measured on server side.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2DeployConversationModelOperationMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2DeployConversationModelOperationMetadata');
