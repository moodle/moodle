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

class SqlserverValidation extends \Google\Collection
{
  protected $collection_key = 'validationDetails';
  /**
   * Optional. The agent version collected this data point
   *
   * @var string
   */
  public $agentVersion;
  /**
   * Required. The instance_name of the instance that the Insight data comes
   * from. According to https://linter.aip.dev/122/name-suffix: field names
   * should not use the _name suffix unless the field would be ambiguous without
   * it.
   *
   * @var string
   */
  public $instance;
  /**
   * Required. The project_id of the cloud project that the Insight data comes
   * from.
   *
   * @var string
   */
  public $projectId;
  protected $validationDetailsType = SqlserverValidationValidationDetail::class;
  protected $validationDetailsDataType = 'array';

  /**
   * Optional. The agent version collected this data point
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
   * Required. The instance_name of the instance that the Insight data comes
   * from. According to https://linter.aip.dev/122/name-suffix: field names
   * should not use the _name suffix unless the field would be ambiguous without
   * it.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. The project_id of the cloud project that the Insight data comes
   * from.
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
   * Optional. A list of SqlServer validation metrics data.
   *
   * @param SqlserverValidationValidationDetail[] $validationDetails
   */
  public function setValidationDetails($validationDetails)
  {
    $this->validationDetails = $validationDetails;
  }
  /**
   * @return SqlserverValidationValidationDetail[]
   */
  public function getValidationDetails()
  {
    return $this->validationDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlserverValidation::class, 'Google_Service_WorkloadManager_SqlserverValidation');
