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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1CustomTuningModel extends \Google\Model
{
  /**
   * Default value.
   */
  public const MODEL_STATE_MODEL_STATE_UNSPECIFIED = 'MODEL_STATE_UNSPECIFIED';
  /**
   * The model is in a paused training state.
   */
  public const MODEL_STATE_TRAINING_PAUSED = 'TRAINING_PAUSED';
  /**
   * The model is currently training.
   */
  public const MODEL_STATE_TRAINING = 'TRAINING';
  /**
   * The model has successfully completed training.
   */
  public const MODEL_STATE_TRAINING_COMPLETE = 'TRAINING_COMPLETE';
  /**
   * The model is ready for serving.
   */
  public const MODEL_STATE_READY_FOR_SERVING = 'READY_FOR_SERVING';
  /**
   * The model training failed.
   */
  public const MODEL_STATE_TRAINING_FAILED = 'TRAINING_FAILED';
  /**
   * The model training finished successfully but metrics did not improve.
   */
  public const MODEL_STATE_NO_IMPROVEMENT = 'NO_IMPROVEMENT';
  /**
   * Input data validation failed. Model training didn't start.
   */
  public const MODEL_STATE_INPUT_VALIDATION_FAILED = 'INPUT_VALIDATION_FAILED';
  /**
   * Deprecated: Timestamp the Model was created at.
   *
   * @deprecated
   * @var string
   */
  public $createTime;
  /**
   * The display name of the model.
   *
   * @var string
   */
  public $displayName;
  /**
   * Currently this is only populated if the model state is
   * `INPUT_VALIDATION_FAILED`.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The metrics of the trained model.
   *
   * @var []
   */
  public $metrics;
  /**
   * The state that the model is in (e.g.`TRAINING` or `TRAINING_FAILED`).
   *
   * @var string
   */
  public $modelState;
  /**
   * The version of the model.
   *
   * @var string
   */
  public $modelVersion;
  /**
   * Required. The fully qualified resource name of the model. Format: `projects
   * /{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/customTuningModels/{custom_tuning_model}`. Model must be an alpha-
   * numerical string with limit of 40 characters.
   *
   * @var string
   */
  public $name;
  /**
   * Timestamp the model training was initiated.
   *
   * @var string
   */
  public $trainingStartTime;

  /**
   * Deprecated: Timestamp the Model was created at.
   *
   * @deprecated
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The display name of the model.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Currently this is only populated if the model state is
   * `INPUT_VALIDATION_FAILED`.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The state that the model is in (e.g.`TRAINING` or `TRAINING_FAILED`).
   *
   * Accepted values: MODEL_STATE_UNSPECIFIED, TRAINING_PAUSED, TRAINING,
   * TRAINING_COMPLETE, READY_FOR_SERVING, TRAINING_FAILED, NO_IMPROVEMENT,
   * INPUT_VALIDATION_FAILED
   *
   * @param self::MODEL_STATE_* $modelState
   */
  public function setModelState($modelState)
  {
    $this->modelState = $modelState;
  }
  /**
   * @return self::MODEL_STATE_*
   */
  public function getModelState()
  {
    return $this->modelState;
  }
  /**
   * The version of the model.
   *
   * @param string $modelVersion
   */
  public function setModelVersion($modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return string
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
  /**
   * Required. The fully qualified resource name of the model. Format: `projects
   * /{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/customTuningModels/{custom_tuning_model}`. Model must be an alpha-
   * numerical string with limit of 40 characters.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Timestamp the model training was initiated.
   *
   * @param string $trainingStartTime
   */
  public function setTrainingStartTime($trainingStartTime)
  {
    $this->trainingStartTime = $trainingStartTime;
  }
  /**
   * @return string
   */
  public function getTrainingStartTime()
  {
    return $this->trainingStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CustomTuningModel::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CustomTuningModel');
