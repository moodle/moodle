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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AsyncQuery extends \Google\Model
{
  /**
   * Creation time of the query.
   *
   * @var string
   */
  public $created;
  /**
   * Hostname is available only when query is executed at host level.
   *
   * @var string
   */
  public $envgroupHostname;
  /**
   * Error is set when query fails.
   *
   * @var string
   */
  public $error;
  /**
   * ExecutionTime is available only after the query is completed.
   *
   * @var string
   */
  public $executionTime;
  /**
   * Asynchronous Query Name.
   *
   * @var string
   */
  public $name;
  protected $queryParamsType = GoogleCloudApigeeV1QueryMetadata::class;
  protected $queryParamsDataType = '';
  /**
   * Asynchronous Report ID.
   *
   * @var string
   */
  public $reportDefinitionId;
  protected $resultType = GoogleCloudApigeeV1AsyncQueryResult::class;
  protected $resultDataType = '';
  /**
   * ResultFileSize is available only after the query is completed.
   *
   * @var string
   */
  public $resultFileSize;
  /**
   * ResultRows is available only after the query is completed.
   *
   * @var string
   */
  public $resultRows;
  /**
   * Self link of the query. Example: `/organizations/myorg/environments/myenv/q
   * ueries/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd` or following format if query
   * is running at host level:
   * `/organizations/myorg/hostQueries/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd`
   *
   * @var string
   */
  public $self;
  /**
   * Query state could be "enqueued", "running", "completed", "failed".
   *
   * @var string
   */
  public $state;
  /**
   * Last updated timestamp for the query.
   *
   * @var string
   */
  public $updated;

  /**
   * Creation time of the query.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Hostname is available only when query is executed at host level.
   *
   * @param string $envgroupHostname
   */
  public function setEnvgroupHostname($envgroupHostname)
  {
    $this->envgroupHostname = $envgroupHostname;
  }
  /**
   * @return string
   */
  public function getEnvgroupHostname()
  {
    return $this->envgroupHostname;
  }
  /**
   * Error is set when query fails.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * ExecutionTime is available only after the query is completed.
   *
   * @param string $executionTime
   */
  public function setExecutionTime($executionTime)
  {
    $this->executionTime = $executionTime;
  }
  /**
   * @return string
   */
  public function getExecutionTime()
  {
    return $this->executionTime;
  }
  /**
   * Asynchronous Query Name.
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
   * Contains information like metrics, dimenstions etc of the AsyncQuery.
   *
   * @param GoogleCloudApigeeV1QueryMetadata $queryParams
   */
  public function setQueryParams(GoogleCloudApigeeV1QueryMetadata $queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return GoogleCloudApigeeV1QueryMetadata
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
  /**
   * Asynchronous Report ID.
   *
   * @param string $reportDefinitionId
   */
  public function setReportDefinitionId($reportDefinitionId)
  {
    $this->reportDefinitionId = $reportDefinitionId;
  }
  /**
   * @return string
   */
  public function getReportDefinitionId()
  {
    return $this->reportDefinitionId;
  }
  /**
   * Result is available only after the query is completed.
   *
   * @param GoogleCloudApigeeV1AsyncQueryResult $result
   */
  public function setResult(GoogleCloudApigeeV1AsyncQueryResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return GoogleCloudApigeeV1AsyncQueryResult
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * ResultFileSize is available only after the query is completed.
   *
   * @param string $resultFileSize
   */
  public function setResultFileSize($resultFileSize)
  {
    $this->resultFileSize = $resultFileSize;
  }
  /**
   * @return string
   */
  public function getResultFileSize()
  {
    return $this->resultFileSize;
  }
  /**
   * ResultRows is available only after the query is completed.
   *
   * @param string $resultRows
   */
  public function setResultRows($resultRows)
  {
    $this->resultRows = $resultRows;
  }
  /**
   * @return string
   */
  public function getResultRows()
  {
    return $this->resultRows;
  }
  /**
   * Self link of the query. Example: `/organizations/myorg/environments/myenv/q
   * ueries/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd` or following format if query
   * is running at host level:
   * `/organizations/myorg/hostQueries/9cfc0d85-0f30-46d6-ae6f-318d0cb961bd`
   *
   * @param string $self
   */
  public function setSelf($self)
  {
    $this->self = $self;
  }
  /**
   * @return string
   */
  public function getSelf()
  {
    return $this->self;
  }
  /**
   * Query state could be "enqueued", "running", "completed", "failed".
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Last updated timestamp for the query.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AsyncQuery::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AsyncQuery');
