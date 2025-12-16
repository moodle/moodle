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

namespace Google\Service\Dataflow;

class PipelineDescription extends \Google\Collection
{
  protected $collection_key = 'originalPipelineTransform';
  protected $displayDataType = DisplayData::class;
  protected $displayDataDataType = 'array';
  protected $executionPipelineStageType = ExecutionStageSummary::class;
  protected $executionPipelineStageDataType = 'array';
  protected $originalPipelineTransformType = TransformSummary::class;
  protected $originalPipelineTransformDataType = 'array';
  /**
   * A hash value of the submitted pipeline portable graph step names if exists.
   *
   * @var string
   */
  public $stepNamesHash;

  /**
   * Pipeline level display data.
   *
   * @param DisplayData[] $displayData
   */
  public function setDisplayData($displayData)
  {
    $this->displayData = $displayData;
  }
  /**
   * @return DisplayData[]
   */
  public function getDisplayData()
  {
    return $this->displayData;
  }
  /**
   * Description of each stage of execution of the pipeline.
   *
   * @param ExecutionStageSummary[] $executionPipelineStage
   */
  public function setExecutionPipelineStage($executionPipelineStage)
  {
    $this->executionPipelineStage = $executionPipelineStage;
  }
  /**
   * @return ExecutionStageSummary[]
   */
  public function getExecutionPipelineStage()
  {
    return $this->executionPipelineStage;
  }
  /**
   * Description of each transform in the pipeline and collections between them.
   *
   * @param TransformSummary[] $originalPipelineTransform
   */
  public function setOriginalPipelineTransform($originalPipelineTransform)
  {
    $this->originalPipelineTransform = $originalPipelineTransform;
  }
  /**
   * @return TransformSummary[]
   */
  public function getOriginalPipelineTransform()
  {
    return $this->originalPipelineTransform;
  }
  /**
   * A hash value of the submitted pipeline portable graph step names if exists.
   *
   * @param string $stepNamesHash
   */
  public function setStepNamesHash($stepNamesHash)
  {
    $this->stepNamesHash = $stepNamesHash;
  }
  /**
   * @return string
   */
  public function getStepNamesHash()
  {
    return $this->stepNamesHash;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineDescription::class, 'Google_Service_Dataflow_PipelineDescription');
