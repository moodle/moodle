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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaReplayExecutionResponse extends \Google\Model
{
  /**
   * Next ID: 4 The id of the execution corresponding to this run of the
   * integration.
   *
   * @var string
   */
  public $executionId;
  /**
   * OUTPUT parameters in format of Map. Where Key is the name of the parameter.
   * The parameters would only be present in case of synchrounous execution.
   * Note: Name of the system generated parameters are wrapped by backtick(`) to
   * distinguish them from the user defined parameters.
   *
   * @var array[]
   */
  public $outputParameters;
  /**
   * The execution id which is replayed.
   *
   * @var string
   */
  public $replayedExecutionId;

  /**
   * Next ID: 4 The id of the execution corresponding to this run of the
   * integration.
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
   * OUTPUT parameters in format of Map. Where Key is the name of the parameter.
   * The parameters would only be present in case of synchrounous execution.
   * Note: Name of the system generated parameters are wrapped by backtick(`) to
   * distinguish them from the user defined parameters.
   *
   * @param array[] $outputParameters
   */
  public function setOutputParameters($outputParameters)
  {
    $this->outputParameters = $outputParameters;
  }
  /**
   * @return array[]
   */
  public function getOutputParameters()
  {
    return $this->outputParameters;
  }
  /**
   * The execution id which is replayed.
   *
   * @param string $replayedExecutionId
   */
  public function setReplayedExecutionId($replayedExecutionId)
  {
    $this->replayedExecutionId = $replayedExecutionId;
  }
  /**
   * @return string
   */
  public function getReplayedExecutionId()
  {
    return $this->replayedExecutionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaReplayExecutionResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaReplayExecutionResponse');
