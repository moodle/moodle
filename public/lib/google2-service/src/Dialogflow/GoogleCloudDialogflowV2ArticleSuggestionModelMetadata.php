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

class GoogleCloudDialogflowV2ArticleSuggestionModelMetadata extends \Google\Model
{
  /**
   * ModelType unspecified.
   */
  public const TRAINING_MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * ModelType smart reply dual encoder model.
   */
  public const TRAINING_MODEL_TYPE_SMART_REPLY_DUAL_ENCODER_MODEL = 'SMART_REPLY_DUAL_ENCODER_MODEL';
  /**
   * ModelType smart reply bert model.
   */
  public const TRAINING_MODEL_TYPE_SMART_REPLY_BERT_MODEL = 'SMART_REPLY_BERT_MODEL';
  /**
   * Optional. Type of the article suggestion model. If not provided, model_type
   * is used.
   *
   * @var string
   */
  public $trainingModelType;

  /**
   * Optional. Type of the article suggestion model. If not provided, model_type
   * is used.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, SMART_REPLY_DUAL_ENCODER_MODEL,
   * SMART_REPLY_BERT_MODEL
   *
   * @param self::TRAINING_MODEL_TYPE_* $trainingModelType
   */
  public function setTrainingModelType($trainingModelType)
  {
    $this->trainingModelType = $trainingModelType;
  }
  /**
   * @return self::TRAINING_MODEL_TYPE_*
   */
  public function getTrainingModelType()
  {
    return $this->trainingModelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2ArticleSuggestionModelMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2ArticleSuggestionModelMetadata');
