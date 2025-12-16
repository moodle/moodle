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

namespace Google\Service\Dataproc;

class NativeSqlExecutionUiData extends \Google\Collection
{
  protected $collection_key = 'fallbackNodeToReason';
  /**
   * Optional. Description of the execution.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Execution ID of the Native SQL Execution.
   *
   * @var string
   */
  public $executionId;
  /**
   * Optional. Description of the fallback.
   *
   * @var string
   */
  public $fallbackDescription;
  protected $fallbackNodeToReasonType = FallbackReason::class;
  protected $fallbackNodeToReasonDataType = 'array';
  /**
   * Optional. Number of nodes fallen back to Spark.
   *
   * @var int
   */
  public $numFallbackNodes;
  /**
   * Optional. Number of nodes in Native.
   *
   * @var int
   */
  public $numNativeNodes;

  /**
   * Optional. Description of the execution.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Execution ID of the Native SQL Execution.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * Optional. Description of the fallback.
   *
   * @param string $fallbackDescription
   */
  public function setFallbackDescription($fallbackDescription)
  {
    $this->fallbackDescription = $fallbackDescription;
  }
  /**
   * @return string
   */
  public function getFallbackDescription()
  {
    return $this->fallbackDescription;
  }
  /**
   * Optional. Fallback node to reason.
   *
   * @param FallbackReason[] $fallbackNodeToReason
   */
  public function setFallbackNodeToReason($fallbackNodeToReason)
  {
    $this->fallbackNodeToReason = $fallbackNodeToReason;
  }
  /**
   * @return FallbackReason[]
   */
  public function getFallbackNodeToReason()
  {
    return $this->fallbackNodeToReason;
  }
  /**
   * Optional. Number of nodes fallen back to Spark.
   *
   * @param int $numFallbackNodes
   */
  public function setNumFallbackNodes($numFallbackNodes)
  {
    $this->numFallbackNodes = $numFallbackNodes;
  }
  /**
   * @return int
   */
  public function getNumFallbackNodes()
  {
    return $this->numFallbackNodes;
  }
  /**
   * Optional. Number of nodes in Native.
   *
   * @param int $numNativeNodes
   */
  public function setNumNativeNodes($numNativeNodes)
  {
    $this->numNativeNodes = $numNativeNodes;
  }
  /**
   * @return int
   */
  public function getNumNativeNodes()
  {
    return $this->numNativeNodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NativeSqlExecutionUiData::class, 'Google_Service_Dataproc_NativeSqlExecutionUiData');
