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

class GoogleCloudDiscoveryengineV1alphaConnectorRun extends \Google\Collection
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The data sync is ongoing.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The data sync is finished.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The data sync is failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Data sync has been running longer than expected and is still running at the
   * time the next run is supposed to start.
   */
  public const STATE_OVERRUN = 'OVERRUN';
  /**
   * Data sync was scheduled but has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Data sync is about to start.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The data sync completed with non-fatal errors.
   */
  public const STATE_WARNING = 'WARNING';
  /**
   * An ongoing connector run has been running longer than expected, causing
   * this run to be skipped.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Default value.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * ConnectorRun triggered by scheduler if connector has PERIODIC sync mode.
   */
  public const TRIGGER_SCHEDULER = 'SCHEDULER';
  /**
   * ConnectorRun auto triggered by connector initialization.
   */
  public const TRIGGER_INITIALIZATION = 'INITIALIZATION';
  /**
   * ConnectorRun auto triggered by resuming connector.
   */
  public const TRIGGER_RESUME = 'RESUME';
  /**
   * ConnectorRun triggered by user manually.
   */
  public const TRIGGER_MANUAL = 'MANUAL';
  protected $collection_key = 'errors';
  /**
   * Output only. The time when the connector run ended.
   *
   * @var string
   */
  public $endTime;
  protected $entityRunsType = GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun::class;
  protected $entityRunsDataType = 'array';
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. The time when the connector run was most recently paused.
   *
   * @var string
   */
  public $latestPauseTime;
  /**
   * Output only. The full resource name of the Connector Run. Format:
   * `projects/locations/collections/dataConnector/connectorRuns`. The
   * `connector_run_id` is system-generated.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the connector run started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The state of the sync run.
   *
   * @var string
   */
  public $state;
  /**
   * Timestamp at which the connector run sync state was last updated.
   *
   * @var string
   */
  public $stateUpdateTime;
  /**
   * Output only. The trigger for this ConnectorRun.
   *
   * @var string
   */
  public $trigger;

  /**
   * Output only. The time when the connector run ended.
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
   * Output only. The details of the entities synced at the ConnectorRun. Each
   * ConnectorRun consists of syncing one or more entities.
   *
   * @param GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun[] $entityRuns
   */
  public function setEntityRuns($entityRuns)
  {
    $this->entityRuns = $entityRuns;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun[]
   */
  public function getEntityRuns()
  {
    return $this->entityRuns;
  }
  /**
   * Contains info about errors incurred during the sync. Only exist if running
   * into an error state. Contains error code and error message. Use with the
   * `state` field.
   *
   * @param GoogleRpcStatus[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. The time when the connector run was most recently paused.
   *
   * @param string $latestPauseTime
   */
  public function setLatestPauseTime($latestPauseTime)
  {
    $this->latestPauseTime = $latestPauseTime;
  }
  /**
   * @return string
   */
  public function getLatestPauseTime()
  {
    return $this->latestPauseTime;
  }
  /**
   * Output only. The full resource name of the Connector Run. Format:
   * `projects/locations/collections/dataConnector/connectorRuns`. The
   * `connector_run_id` is system-generated.
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
   * Output only. The time when the connector run started.
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
   * Output only. The state of the sync run.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, FAILED, OVERRUN,
   * CANCELLED, PENDING, WARNING, SKIPPED
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
  /**
   * Timestamp at which the connector run sync state was last updated.
   *
   * @param string $stateUpdateTime
   */
  public function setStateUpdateTime($stateUpdateTime)
  {
    $this->stateUpdateTime = $stateUpdateTime;
  }
  /**
   * @return string
   */
  public function getStateUpdateTime()
  {
    return $this->stateUpdateTime;
  }
  /**
   * Output only. The trigger for this ConnectorRun.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, SCHEDULER, INITIALIZATION, RESUME,
   * MANUAL
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaConnectorRun::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaConnectorRun');
