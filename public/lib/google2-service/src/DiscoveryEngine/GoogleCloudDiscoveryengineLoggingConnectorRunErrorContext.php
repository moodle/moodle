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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineLoggingConnectorRunErrorContext extends \Google\Model
{
  /**
   * The full resource name of the Connector Run. Format:
   * `projects/locations/collections/dataConnector/connectorRuns`. The
   * `connector_run_id` is system-generated.
   *
   * @var string
   */
  public $connectorRun;
  /**
   * The full resource name of the DataConnector. Format:
   * `projects/locations/collections/dataConnector`.
   *
   * @var string
   */
  public $dataConnector;
  /**
   * The time when the connector run ended.
   *
   * @var string
   */
  public $endTime;
  /**
   * The entity to sync for the connector run.
   *
   * @var string
   */
  public $entity;
  /**
   * The operation resource name of the LRO to sync the connector.
   *
   * @var string
   */
  public $operation;
  /**
   * The time when the connector run started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The type of sync run. Can be one of the following: * `FULL` * `INCREMENTAL`
   *
   * @var string
   */
  public $syncType;

  /**
   * The full resource name of the Connector Run. Format:
   * `projects/locations/collections/dataConnector/connectorRuns`. The
   * `connector_run_id` is system-generated.
   *
   * @param string $connectorRun
   */
  public function setConnectorRun($connectorRun)
  {
    $this->connectorRun = $connectorRun;
  }
  /**
   * @return string
   */
  public function getConnectorRun()
  {
    return $this->connectorRun;
  }
  /**
   * The full resource name of the DataConnector. Format:
   * `projects/locations/collections/dataConnector`.
   *
   * @param string $dataConnector
   */
  public function setDataConnector($dataConnector)
  {
    $this->dataConnector = $dataConnector;
  }
  /**
   * @return string
   */
  public function getDataConnector()
  {
    return $this->dataConnector;
  }
  /**
   * The time when the connector run ended.
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
   * The entity to sync for the connector run.
   *
   * @param string $entity
   */
  public function setEntity($entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The operation resource name of the LRO to sync the connector.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The time when the connector run started.
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
   * The type of sync run. Can be one of the following: * `FULL` * `INCREMENTAL`
   *
   * @param string $syncType
   */
  public function setSyncType($syncType)
  {
    $this->syncType = $syncType;
  }
  /**
   * @return string
   */
  public function getSyncType()
  {
    return $this->syncType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineLoggingConnectorRunErrorContext::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineLoggingConnectorRunErrorContext');
