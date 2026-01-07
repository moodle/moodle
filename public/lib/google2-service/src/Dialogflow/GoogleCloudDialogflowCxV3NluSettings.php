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

class GoogleCloudDialogflowCxV3NluSettings extends \Google\Model
{
  /**
   * Not specified. `MODEL_TRAINING_MODE_AUTOMATIC` will be used.
   */
  public const MODEL_TRAINING_MODE_MODEL_TRAINING_MODE_UNSPECIFIED = 'MODEL_TRAINING_MODE_UNSPECIFIED';
  /**
   * NLU model training is automatically triggered when a flow gets modified.
   * User can also manually trigger model training in this mode.
   */
  public const MODEL_TRAINING_MODE_MODEL_TRAINING_MODE_AUTOMATIC = 'MODEL_TRAINING_MODE_AUTOMATIC';
  /**
   * User needs to manually trigger NLU model training. Best for large flows
   * whose models take long time to train.
   */
  public const MODEL_TRAINING_MODE_MODEL_TRAINING_MODE_MANUAL = 'MODEL_TRAINING_MODE_MANUAL';
  /**
   * Not specified. `MODEL_TYPE_STANDARD` will be used.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * Use standard NLU model.
   */
  public const MODEL_TYPE_MODEL_TYPE_STANDARD = 'MODEL_TYPE_STANDARD';
  /**
   * Use advanced NLU model.
   */
  public const MODEL_TYPE_MODEL_TYPE_ADVANCED = 'MODEL_TYPE_ADVANCED';
  /**
   * To filter out false positive results and still get variety in matched
   * natural language inputs for your agent, you can tune the machine learning
   * classification threshold. If the returned score value is less than the
   * threshold value, then a no-match event will be triggered. The score values
   * range from 0.0 (completely uncertain) to 1.0 (completely certain). If set
   * to 0.0, the default of 0.3 is used. You can set a separate classification
   * threshold for the flow in each language enabled for the agent.
   *
   * @var float
   */
  public $classificationThreshold;
  /**
   * Indicates NLU model training mode.
   *
   * @var string
   */
  public $modelTrainingMode;
  /**
   * Indicates the type of NLU model.
   *
   * @var string
   */
  public $modelType;

  /**
   * To filter out false positive results and still get variety in matched
   * natural language inputs for your agent, you can tune the machine learning
   * classification threshold. If the returned score value is less than the
   * threshold value, then a no-match event will be triggered. The score values
   * range from 0.0 (completely uncertain) to 1.0 (completely certain). If set
   * to 0.0, the default of 0.3 is used. You can set a separate classification
   * threshold for the flow in each language enabled for the agent.
   *
   * @param float $classificationThreshold
   */
  public function setClassificationThreshold($classificationThreshold)
  {
    $this->classificationThreshold = $classificationThreshold;
  }
  /**
   * @return float
   */
  public function getClassificationThreshold()
  {
    return $this->classificationThreshold;
  }
  /**
   * Indicates NLU model training mode.
   *
   * Accepted values: MODEL_TRAINING_MODE_UNSPECIFIED,
   * MODEL_TRAINING_MODE_AUTOMATIC, MODEL_TRAINING_MODE_MANUAL
   *
   * @param self::MODEL_TRAINING_MODE_* $modelTrainingMode
   */
  public function setModelTrainingMode($modelTrainingMode)
  {
    $this->modelTrainingMode = $modelTrainingMode;
  }
  /**
   * @return self::MODEL_TRAINING_MODE_*
   */
  public function getModelTrainingMode()
  {
    return $this->modelTrainingMode;
  }
  /**
   * Indicates the type of NLU model.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, MODEL_TYPE_STANDARD,
   * MODEL_TYPE_ADVANCED
   *
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3NluSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3NluSettings');
