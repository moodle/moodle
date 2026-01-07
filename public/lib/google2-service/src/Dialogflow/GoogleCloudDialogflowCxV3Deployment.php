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

class GoogleCloudDialogflowCxV3Deployment extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The deployment succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The deployment failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * End time of this deployment.
   *
   * @var string
   */
  public $endTime;
  /**
   * The name of the flow version for this deployment. Format:
   * projects//locations//agents//flows//versions/.
   *
   * @var string
   */
  public $flowVersion;
  /**
   * The name of the deployment. Format:
   * projects//locations//agents//environments//deployments/.
   *
   * @var string
   */
  public $name;
  protected $resultType = GoogleCloudDialogflowCxV3DeploymentResult::class;
  protected $resultDataType = '';
  /**
   * Start time of this deployment.
   *
   * @var string
   */
  public $startTime;
  /**
   * The current state of the deployment.
   *
   * @var string
   */
  public $state;

  /**
   * End time of this deployment.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The name of the flow version for this deployment. Format:
   * projects//locations//agents//flows//versions/.
   *
   * @param string $flowVersion
   */
  public function setFlowVersion($flowVersion)
  {
    $this->flowVersion = $flowVersion;
  }
  /**
   * @return string
   */
  public function getFlowVersion()
  {
    return $this->flowVersion;
  }
  /**
   * The name of the deployment. Format:
   * projects//locations//agents//environments//deployments/.
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
   * Result of the deployment.
   *
   * @param GoogleCloudDialogflowCxV3DeploymentResult $result
   */
  public function setResult(GoogleCloudDialogflowCxV3DeploymentResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DeploymentResult
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Start time of this deployment.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The current state of the deployment.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Deployment::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Deployment');
