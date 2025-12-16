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

namespace Google\Service\ManagedKafka;

class Connector extends \Google\Model
{
  /**
   * A state was not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connector is not assigned to any tasks, usually transient.
   */
  public const STATE_UNASSIGNED = 'UNASSIGNED';
  /**
   * The connector is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The connector has been paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The connector has failed. See logs for why.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The connector is restarting.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * The connector has been stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Optional. Connector config as keys/values. The keys of the map are
   * connector property names, for example: `connector.class`, `tasks.max`,
   * `key.converter`.
   *
   * @var string[]
   */
  public $configs;
  /**
   * Identifier. The name of the connector. Structured like: projects/{project}/
   * locations/{location}/connectClusters/{connect_cluster}/connectors/{connecto
   * r}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the connector.
   *
   * @var string
   */
  public $state;
  protected $taskRestartPolicyType = TaskRetryPolicy::class;
  protected $taskRestartPolicyDataType = '';

  /**
   * Optional. Connector config as keys/values. The keys of the map are
   * connector property names, for example: `connector.class`, `tasks.max`,
   * `key.converter`.
   *
   * @param string[] $configs
   */
  public function setConfigs($configs)
  {
    $this->configs = $configs;
  }
  /**
   * @return string[]
   */
  public function getConfigs()
  {
    return $this->configs;
  }
  /**
   * Identifier. The name of the connector. Structured like: projects/{project}/
   * locations/{location}/connectClusters/{connect_cluster}/connectors/{connecto
   * r}
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
   * Output only. The current state of the connector.
   *
   * Accepted values: STATE_UNSPECIFIED, UNASSIGNED, RUNNING, PAUSED, FAILED,
   * RESTARTING, STOPPED
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
   * Optional. Restarts the individual tasks of a Connector.
   *
   * @param TaskRetryPolicy $taskRestartPolicy
   */
  public function setTaskRestartPolicy(TaskRetryPolicy $taskRestartPolicy)
  {
    $this->taskRestartPolicy = $taskRestartPolicy;
  }
  /**
   * @return TaskRetryPolicy
   */
  public function getTaskRestartPolicy()
  {
    return $this->taskRestartPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connector::class, 'Google_Service_ManagedKafka_Connector');
