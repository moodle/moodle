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

class XPSImageSegmentationTrainResponse extends \Google\Collection
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
  protected $collection_key = 'colorMaps';
  protected $colorMapsType = XPSColorMap::class;
  protected $colorMapsDataType = 'array';
  protected $exportModelSpecType = XPSImageExportModelSpec::class;
  protected $exportModelSpecDataType = '';
  protected $modelArtifactSpecType = XPSImageModelArtifactSpec::class;
  protected $modelArtifactSpecDataType = '';
  protected $modelServingSpecType = XPSImageModelServingSpec::class;
  protected $modelServingSpecDataType = '';
  /**
   * Stop reason for training job, e.g. 'TRAIN_BUDGET_REACHED',
   * 'MODEL_CONVERGED'.
   *
   * @var string
   */
  public $stopReason;
  /**
   * The actual train cost of creating this model, expressed in node seconds,
   * i.e. 3,600 value in this field means 1 node hour.
   *
   * @var string
   */
  public $trainCostNodeSeconds;

  /**
   * Color map of the model.
   *
   * @param XPSColorMap[] $colorMaps
   */
  public function setColorMaps($colorMaps)
  {
    $this->colorMaps = $colorMaps;
  }
  /**
   * @return XPSColorMap[]
   */
  public function getColorMaps()
  {
    return $this->colorMaps;
  }
  /**
   * NOTE: These fields are not used/needed in EAP but will be set later.
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
   * ## The fields below are only populated under uCAIP request scope. Model
   * artifact spec stores and model gcs pathes and related metadata
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
   * 'MODEL_CONVERGED'.
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
   * The actual train cost of creating this model, expressed in node seconds,
   * i.e. 3,600 value in this field means 1 node hour.
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
class_alias(XPSImageSegmentationTrainResponse::class, 'Google_Service_CloudNaturalLanguage_XPSImageSegmentationTrainResponse');
