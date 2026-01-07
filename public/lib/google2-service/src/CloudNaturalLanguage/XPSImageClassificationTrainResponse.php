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

class XPSImageClassificationTrainResponse extends \Google\Model
{
  public const STOP_REASON_TRAIN_STOP_REASON_UNSPECIFIED = 'TRAIN_STOP_REASON_UNSPECIFIED';
  public const STOP_REASON_TRAIN_STOP_REASON_BUDGET_REACHED = 'TRAIN_STOP_REASON_BUDGET_REACHED';
  /**
   * Model fully converged, can not be resumbed training.
   */
  public const STOP_REASON_TRAIN_STOP_REASON_MODEL_CONVERGED = 'TRAIN_STOP_REASON_MODEL_CONVERGED';
  /**
   * Model early converged, can be further trained till full convergency.
   */
  public const STOP_REASON_TRAIN_STOP_REASON_MODEL_EARLY_STOPPED = 'TRAIN_STOP_REASON_MODEL_EARLY_STOPPED';
  /**
   * Total number of classes.
   *
   * @var string
   */
  public $classCount;
  protected $exportModelSpecType = XPSImageExportModelSpec::class;
  protected $exportModelSpecDataType = '';
  protected $modelArtifactSpecType = XPSImageModelArtifactSpec::class;
  protected $modelArtifactSpecDataType = '';
  protected $modelServingSpecType = XPSImageModelServingSpec::class;
  protected $modelServingSpecDataType = '';
  /**
   * Stop reason for training job, e.g. 'TRAIN_BUDGET_REACHED',
   * 'MODEL_CONVERGED', 'MODEL_EARLY_STOPPED'.
   *
   * @var string
   */
  public $stopReason;
  /**
   * The actual cost to create this model. - For edge type model, the cost is
   * expressed in node hour. - For cloud type model,the cost is expressed in
   * compute hour. - Populated for models created before GA. To be deprecated
   * after GA.
   *
   * @var string
   */
  public $trainCostInNodeTime;
  /**
   * The actual training cost, expressed in node seconds. Populated for models
   * trained in node time.
   *
   * @var string
   */
  public $trainCostNodeSeconds;

  /**
   * Total number of classes.
   *
   * @param string $classCount
   */
  public function setClassCount($classCount)
  {
    $this->classCount = $classCount;
  }
  /**
   * @return string
   */
  public function getClassCount()
  {
    return $this->classCount;
  }
  /**
   * Information of downloadable models that are pre-generated as part of
   * training flow and will be persisted in AutoMl backend. Populated for AutoMl
   * requests.
   *
   * @param XPSImageExportModelSpec $exportModelSpec
   */
  public function setExportModelSpec(XPSImageExportModelSpec $exportModelSpec)
  {
    $this->exportModelSpec = $exportModelSpec;
  }
  /**
   * @return XPSImageExportModelSpec
   */
  public function getExportModelSpec()
  {
    return $this->exportModelSpec;
  }
  /**
   * ## The fields below are only populated under uCAIP request scope.
   *
   * @param XPSImageModelArtifactSpec $modelArtifactSpec
   */
  public function setModelArtifactSpec(XPSImageModelArtifactSpec $modelArtifactSpec)
  {
    $this->modelArtifactSpec = $modelArtifactSpec;
  }
  /**
   * @return XPSImageModelArtifactSpec
   */
  public function getModelArtifactSpec()
  {
    return $this->modelArtifactSpec;
  }
  /**
   * @param XPSImageModelServingSpec $modelServingSpec
   */
  public function setModelServingSpec(XPSImageModelServingSpec $modelServingSpec)
  {
    $this->modelServingSpec = $modelServingSpec;
  }
  /**
   * @return XPSImageModelServingSpec
   */
  public function getModelServingSpec()
  {
    return $this->modelServingSpec;
  }
  /**
   * Stop reason for training job, e.g. 'TRAIN_BUDGET_REACHED',
   * 'MODEL_CONVERGED', 'MODEL_EARLY_STOPPED'.
   *
   * Accepted values: TRAIN_STOP_REASON_UNSPECIFIED,
   * TRAIN_STOP_REASON_BUDGET_REACHED, TRAIN_STOP_REASON_MODEL_CONVERGED,
   * TRAIN_STOP_REASON_MODEL_EARLY_STOPPED
   *
   * @param self::STOP_REASON_* $stopReason
   */
  public function setStopReason($stopReason)
  {
    $this->stopReason = $stopReason;
  }
  /**
   * @return self::STOP_REASON_*
   */
  public function getStopReason()
  {
    return $this->stopReason;
  }
  /**
   * The actual cost to create this model. - For edge type model, the cost is
   * expressed in node hour. - For cloud type model,the cost is expressed in
   * compute hour. - Populated for models created before GA. To be deprecated
   * after GA.
   *
   * @param string $trainCostInNodeTime
   */
  public function setTrainCostInNodeTime($trainCostInNodeTime)
  {
    $this->trainCostInNodeTime = $trainCostInNodeTime;
  }
  /**
   * @return string
   */
  public function getTrainCostInNodeTime()
  {
    return $this->trainCostInNodeTime;
  }
  /**
   * The actual training cost, expressed in node seconds. Populated for models
   * trained in node time.
   *
   * @param string $trainCostNodeSeconds
   */
  public function setTrainCostNodeSeconds($trainCostNodeSeconds)
  {
    $this->trainCostNodeSeconds = $trainCostNodeSeconds;
  }
  /**
   * @return string
   */
  public function getTrainCostNodeSeconds()
  {
    return $this->trainCostNodeSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageClassificationTrainResponse::class, 'Google_Service_CloudNaturalLanguage_XPSImageClassificationTrainResponse');
