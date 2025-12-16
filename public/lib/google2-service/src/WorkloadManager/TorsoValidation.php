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

namespace Google\Service\WorkloadManager;

class TorsoValidation extends \Google\Model
{
  /**
   * Unspecified workload type.
   */
  public const WORKLOAD_TYPE_WORKLOAD_TYPE_UNSPECIFIED = 'WORKLOAD_TYPE_UNSPECIFIED';
  /**
   * MySQL workload.
   */
  public const WORKLOAD_TYPE_MYSQL = 'MYSQL';
  /**
   * Oracle workload.
   */
  public const WORKLOAD_TYPE_ORACLE = 'ORACLE';
  /**
   * Redis workload.
   */
  public const WORKLOAD_TYPE_REDIS = 'REDIS';
  /**
   * Required. agent_version lists the version of the agent that collected this
   * data.
   *
   * @var string
   */
  public $agentVersion;
  /**
   * Optional. instance_name lists the human readable name of the instance that
   * the data comes from.
   *
   * @var string
   */
  public $instanceName;
  /**
   * Required. project_id lists the human readable cloud project that the data
   * comes from.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. validation_details contains the pairs of validation data: field
   * name & field value.
   *
   * @var string[]
   */
  public $validationDetails;
  /**
   * Required. workload_type specifies the type of torso workload.
   *
   * @var string
   */
  public $workloadType;

  /**
   * Required. agent_version lists the version of the agent that collected this
   * data.
   *
   * @param string $agentVersion
   */
  public function setAgentVersion($agentVersion)
  {
    $this->agentVersion = $agentVersion;
  }
  /**
   * @return string
   */
  public function getAgentVersion()
  {
    return $this->agentVersion;
  }
  /**
   * Optional. instance_name lists the human readable name of the instance that
   * the data comes from.
   *
   * @param string $instanceName
   */
  public function setInstanceName($instanceName)
  {
    $this->instanceName = $instanceName;
  }
  /**
   * @return string
   */
  public function getInstanceName()
  {
    return $this->instanceName;
  }
  /**
   * Required. project_id lists the human readable cloud project that the data
   * comes from.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. validation_details contains the pairs of validation data: field
   * name & field value.
   *
   * @param string[] $validationDetails
   */
  public function setValidationDetails($validationDetails)
  {
    $this->validationDetails = $validationDetails;
  }
  /**
   * @return string[]
   */
  public function getValidationDetails()
  {
    return $this->validationDetails;
  }
  /**
   * Required. workload_type specifies the type of torso workload.
   *
   * Accepted values: WORKLOAD_TYPE_UNSPECIFIED, MYSQL, ORACLE, REDIS
   *
   * @param self::WORKLOAD_TYPE_* $workloadType
   */
  public function setWorkloadType($workloadType)
  {
    $this->workloadType = $workloadType;
  }
  /**
   * @return self::WORKLOAD_TYPE_*
   */
  public function getWorkloadType()
  {
    return $this->workloadType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TorsoValidation::class, 'Google_Service_WorkloadManager_TorsoValidation');
