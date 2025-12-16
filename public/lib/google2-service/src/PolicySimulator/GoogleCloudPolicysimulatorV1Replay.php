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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1Replay extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `Replay` has not started yet.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The `Replay` is currently running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The `Replay` has successfully completed.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The `Replay` has finished with an error.
   */
  public const STATE_FAILED = 'FAILED';
  protected $configType = GoogleCloudPolicysimulatorV1ReplayConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The resource name of the `Replay`, which has the following
   * format: `{projects|folders|organizations}/{resource-
   * id}/locations/global/replays/{replay-id}`, where `{resource-id}` is the ID
   * of the project, folder, or organization that owns the Replay. Example:
   * `projects/my-example-
   * project/locations/global/replays/506a5f7f-38ce-4d7d-8e03-479ce1833c36`
   *
   * @var string
   */
  public $name;
  protected $resultsSummaryType = GoogleCloudPolicysimulatorV1ReplayResultsSummary::class;
  protected $resultsSummaryDataType = '';
  /**
   * Output only. The current state of the `Replay`.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The configuration used for the `Replay`.
   *
   * @param GoogleCloudPolicysimulatorV1ReplayConfig $config
   */
  public function setConfig(GoogleCloudPolicysimulatorV1ReplayConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ReplayConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The resource name of the `Replay`, which has the following
   * format: `{projects|folders|organizations}/{resource-
   * id}/locations/global/replays/{replay-id}`, where `{resource-id}` is the ID
   * of the project, folder, or organization that owns the Replay. Example:
   * `projects/my-example-
   * project/locations/global/replays/506a5f7f-38ce-4d7d-8e03-479ce1833c36`
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
   * Output only. Summary statistics about the replayed log entries.
   *
   * @param GoogleCloudPolicysimulatorV1ReplayResultsSummary $resultsSummary
   */
  public function setResultsSummary(GoogleCloudPolicysimulatorV1ReplayResultsSummary $resultsSummary)
  {
    $this->resultsSummary = $resultsSummary;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ReplayResultsSummary
   */
  public function getResultsSummary()
  {
    return $this->resultsSummary;
  }
  /**
   * Output only. The current state of the `Replay`.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED, FAILED
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
class_alias(GoogleCloudPolicysimulatorV1Replay::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1Replay');
