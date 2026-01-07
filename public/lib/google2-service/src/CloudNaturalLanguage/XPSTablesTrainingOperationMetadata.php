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

namespace Google\Service\CloudNaturalLanguage;

class XPSTablesTrainingOperationMetadata extends \Google\Collection
{
  /**
   * Unspecified stage.
   */
  public const CREATE_MODEL_STAGE_CREATE_MODEL_STAGE_UNSPECIFIED = 'CREATE_MODEL_STAGE_UNSPECIFIED';
  /**
   * Prepare the model training pipeline and run data processing.
   */
  public const CREATE_MODEL_STAGE_DATA_PREPROCESSING = 'DATA_PREPROCESSING';
  /**
   * Training model.
   */
  public const CREATE_MODEL_STAGE_TRAINING = 'TRAINING';
  /**
   * Run evaluation.
   */
  public const CREATE_MODEL_STAGE_EVALUATING = 'EVALUATING';
  /**
   * Finalizing model training pipeline.
   */
  public const CREATE_MODEL_STAGE_MODEL_POST_PROCESSING = 'MODEL_POST_PROCESSING';
  protected $collection_key = 'trainingObjectivePoints';
  /**
   * Current stage of creating model.
   *
   * @var string
   */
  public $createModelStage;
  /**
   * The optimization objective for model.
   *
   * @var string
   */
  public $optimizationObjective;
  protected $topTrialsType = XPSTuningTrial::class;
  protected $topTrialsDataType = 'array';
  /**
   * Creating model budget.
   *
   * @var string
   */
  public $trainBudgetMilliNodeHours;
  protected $trainingObjectivePointsType = XPSTrainingObjectivePoint::class;
  protected $trainingObjectivePointsDataType = 'array';
  /**
   * Timestamp when training process starts.
   *
   * @var string
   */
  public $trainingStartTime;

  /**
   * Current stage of creating model.
   *
   * Accepted values: CREATE_MODEL_STAGE_UNSPECIFIED, DATA_PREPROCESSING,
   * TRAINING, EVALUATING, MODEL_POST_PROCESSING
   *
   * @param self::CREATE_MODEL_STAGE_* $createModelStage
   */
  public function setCreateModelStage($createModelStage)
  {
    $this->createModelStage = $createModelStage;
  }
  /**
   * @return self::CREATE_MODEL_STAGE_*
   */
  public function getCreateModelStage()
  {
    return $this->createModelStage;
  }
  /**
   * The optimization objective for model.
   *
   * @param string $optimizationObjective
   */
  public function setOptimizationObjective($optimizationObjective)
  {
    $this->optimizationObjective = $optimizationObjective;
  }
  /**
   * @return string
   */
  public function getOptimizationObjective()
  {
    return $this->optimizationObjective;
  }
  /**
   * This field is for training. When the operation is terminated successfully,
   * AutoML Backend post this field to operation metadata in spanner. If the
   * metadata has no trials returned, the training operation is supposed to be a
   * failure.
   *
   * @param XPSTuningTrial[] $topTrials
   */
  public function setTopTrials($topTrials)
  {
    $this->topTrials = $topTrials;
  }
  /**
   * @return XPSTuningTrial[]
   */
  public function getTopTrials()
  {
    return $this->topTrials;
  }
  /**
   * Creating model budget.
   *
   * @param string $trainBudgetMilliNodeHours
   */
  public function setTrainBudgetMilliNodeHours($trainBudgetMilliNodeHours)
  {
    $this->trainBudgetMilliNodeHours = $trainBudgetMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getTrainBudgetMilliNodeHours()
  {
    return $this->trainBudgetMilliNodeHours;
  }
  /**
   * This field records the training objective value with respect to time,
   * giving insight into how the model architecture search is performing as
   * training time elapses.
   *
   * @param XPSTrainingObjectivePoint[] $trainingObjectivePoints
   */
  public function setTrainingObjectivePoints($trainingObjectivePoints)
  {
    $this->trainingObjectivePoints = $trainingObjectivePoints;
  }
  /**
   * @return XPSTrainingObjectivePoint[]
   */
  public function getTrainingObjectivePoints()
  {
    return $this->trainingObjectivePoints;
  }
  /**
   * Timestamp when training process starts.
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
class_alias(XPSTablesTrainingOperationMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSTablesTrainingOperationMetadata');
