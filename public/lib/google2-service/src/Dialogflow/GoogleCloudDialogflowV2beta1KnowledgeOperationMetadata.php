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

class GoogleCloudDialogflowV2beta1KnowledgeOperationMetadata extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The operation has been created.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The operation is currently running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The operation is done, either cancelled or completed.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The time when the operation finished.
   *
   * @var string
   */
  public $doneTime;
  protected $exportOperationMetadataType = GoogleCloudDialogflowV2beta1ExportOperationMetadata::class;
  protected $exportOperationMetadataDataType = '';
  /**
   * The name of the knowledge base interacted with during the operation.
   *
   * @var string
   */
  public $knowledgeBase;
  /**
   * Required. Output only. The current state of this operation.
   *
   * @var string
   */
  public $state;

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
   * Metadata for the Export Data Operation such as the destination of export.
   *
   * @param GoogleCloudDialogflowV2beta1ExportOperationMetadata $exportOperationMetadata
   */
  public function setExportOperationMetadata(GoogleCloudDialogflowV2beta1ExportOperationMetadata $exportOperationMetadata)
  {
    $this->exportOperationMetadata = $exportOperationMetadata;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ExportOperationMetadata
   */
  public function getExportOperationMetadata()
  {
    return $this->exportOperationMetadata;
  }
  /**
   * The name of the knowledge base interacted with during the operation.
   *
   * @param string $knowledgeBase
   */
  public function setKnowledgeBase($knowledgeBase)
  {
    $this->knowledgeBase = $knowledgeBase;
  }
  /**
   * @return string
   */
  public function getKnowledgeBase()
  {
    return $this->knowledgeBase;
  }
  /**
   * Required. Output only. The current state of this operation.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, DONE
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
class_alias(GoogleCloudDialogflowV2beta1KnowledgeOperationMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1KnowledgeOperationMetadata');
