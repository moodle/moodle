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

class XPSVideoObjectTrackingTrainResponse extends \Google\Model
{
  protected $exportModelSpecType = XPSVideoExportModelSpec::class;
  protected $exportModelSpecDataType = '';
  protected $modelArtifactSpecType = XPSVideoModelArtifactSpec::class;
  protected $modelArtifactSpecDataType = '';
  /**
   * The actual train cost of creating this model, expressed in node seconds,
   * i.e. 3,600 value in this field means 1 node hour.
   *
   * @var string
   */
  public $trainCostNodeSeconds;

  /**
   * Populated for AutoML request only.
   *
   * @param XPSVideoExportModelSpec $exportModelSpec
   */
  public function setExportModelSpec(XPSVideoExportModelSpec $exportModelSpec)
  {
    $this->exportModelSpec = $exportModelSpec;
  }
  /**
   * @return XPSVideoExportModelSpec
   */
  public function getExportModelSpec()
  {
    return $this->exportModelSpec;
  }
  /**
   * ## The fields below are only populated under uCAIP request scope.
   *
   * @param XPSVideoModelArtifactSpec $modelArtifactSpec
   */
  public function setModelArtifactSpec(XPSVideoModelArtifactSpec $modelArtifactSpec)
  {
    $this->modelArtifactSpec = $modelArtifactSpec;
  }
  /**
   * @return XPSVideoModelArtifactSpec
   */
  public function getModelArtifactSpec()
  {
    return $this->modelArtifactSpec;
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
class_alias(XPSVideoObjectTrackingTrainResponse::class, 'Google_Service_CloudNaturalLanguage_XPSVideoObjectTrackingTrainResponse');
