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

class GoogleCloudDialogflowCxV3Experiment extends \Google\Collection
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The experiment is created but not started yet.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The experiment is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The experiment is done.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The experiment with auto-rollout enabled has failed.
   */
  public const STATE_ROLLOUT_FAILED = 'ROLLOUT_FAILED';
  protected $collection_key = 'variantsHistory';
  /**
   * Creation time of this experiment.
   *
   * @var string
   */
  public $createTime;
  protected $definitionType = GoogleCloudDialogflowCxV3ExperimentDefinition::class;
  protected $definitionDataType = '';
  /**
   * The human-readable description of the experiment.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the experiment (unique in an
   * environment). Limit of 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * End time of this experiment.
   *
   * @var string
   */
  public $endTime;
  /**
   * Maximum number of days to run the experiment/rollout. If auto-rollout is
   * not enabled, default value and maximum will be 30 days. If auto-rollout is
   * enabled, default value and maximum will be 6 days.
   *
   * @var string
   */
  public $experimentLength;
  /**
   * Last update time of this experiment.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * The name of the experiment. Format:
   * projects//locations//agents//environments//experiments/.
   *
   * @var string
   */
  public $name;
  protected $resultType = GoogleCloudDialogflowCxV3ExperimentResult::class;
  protected $resultDataType = '';
  protected $rolloutConfigType = GoogleCloudDialogflowCxV3RolloutConfig::class;
  protected $rolloutConfigDataType = '';
  /**
   * The reason why rollout has failed. Should only be set when state is
   * ROLLOUT_FAILED.
   *
   * @var string
   */
  public $rolloutFailureReason;
  protected $rolloutStateType = GoogleCloudDialogflowCxV3RolloutState::class;
  protected $rolloutStateDataType = '';
  /**
   * Start time of this experiment.
   *
   * @var string
   */
  public $startTime;
  /**
   * The current state of the experiment. Transition triggered by
   * Experiments.StartExperiment: DRAFT->RUNNING. Transition triggered by
   * Experiments.CancelExperiment: DRAFT->DONE or RUNNING->DONE.
   *
   * @var string
   */
  public $state;
  protected $variantsHistoryType = GoogleCloudDialogflowCxV3VariantsHistory::class;
  protected $variantsHistoryDataType = 'array';

  /**
   * Creation time of this experiment.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The definition of the experiment.
   *
   * @param GoogleCloudDialogflowCxV3ExperimentDefinition $definition
   */
  public function setDefinition(GoogleCloudDialogflowCxV3ExperimentDefinition $definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ExperimentDefinition
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * The human-readable description of the experiment.
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
   * Required. The human-readable name of the experiment (unique in an
   * environment). Limit of 64 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * End time of this experiment.
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
   * Maximum number of days to run the experiment/rollout. If auto-rollout is
   * not enabled, default value and maximum will be 30 days. If auto-rollout is
   * enabled, default value and maximum will be 6 days.
   *
   * @param string $experimentLength
   */
  public function setExperimentLength($experimentLength)
  {
    $this->experimentLength = $experimentLength;
  }
  /**
   * @return string
   */
  public function getExperimentLength()
  {
    return $this->experimentLength;
  }
  /**
   * Last update time of this experiment.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * The name of the experiment. Format:
   * projects//locations//agents//environments//experiments/.
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
   * Inference result of the experiment.
   *
   * @param GoogleCloudDialogflowCxV3ExperimentResult $result
   */
  public function setResult(GoogleCloudDialogflowCxV3ExperimentResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ExperimentResult
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * The configuration for auto rollout. If set, there should be exactly two
   * variants in the experiment (control variant being the default version of
   * the flow), the traffic allocation for the non-control variant will
   * gradually increase to 100% when conditions are met, and eventually replace
   * the control variant to become the default version of the flow.
   *
   * @param GoogleCloudDialogflowCxV3RolloutConfig $rolloutConfig
   */
  public function setRolloutConfig(GoogleCloudDialogflowCxV3RolloutConfig $rolloutConfig)
  {
    $this->rolloutConfig = $rolloutConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3RolloutConfig
   */
  public function getRolloutConfig()
  {
    return $this->rolloutConfig;
  }
  /**
   * The reason why rollout has failed. Should only be set when state is
   * ROLLOUT_FAILED.
   *
   * @param string $rolloutFailureReason
   */
  public function setRolloutFailureReason($rolloutFailureReason)
  {
    $this->rolloutFailureReason = $rolloutFailureReason;
  }
  /**
   * @return string
   */
  public function getRolloutFailureReason()
  {
    return $this->rolloutFailureReason;
  }
  /**
   * State of the auto rollout process.
   *
   * @param GoogleCloudDialogflowCxV3RolloutState $rolloutState
   */
  public function setRolloutState(GoogleCloudDialogflowCxV3RolloutState $rolloutState)
  {
    $this->rolloutState = $rolloutState;
  }
  /**
   * @return GoogleCloudDialogflowCxV3RolloutState
   */
  public function getRolloutState()
  {
    return $this->rolloutState;
  }
  /**
   * Start time of this experiment.
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
   * The current state of the experiment. Transition triggered by
   * Experiments.StartExperiment: DRAFT->RUNNING. Transition triggered by
   * Experiments.CancelExperiment: DRAFT->DONE or RUNNING->DONE.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, RUNNING, DONE, ROLLOUT_FAILED
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
   * The history of updates to the experiment variants.
   *
   * @param GoogleCloudDialogflowCxV3VariantsHistory[] $variantsHistory
   */
  public function setVariantsHistory($variantsHistory)
  {
    $this->variantsHistory = $variantsHistory;
  }
  /**
   * @return GoogleCloudDialogflowCxV3VariantsHistory[]
   */
  public function getVariantsHistory()
  {
    return $this->variantsHistory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Experiment::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Experiment');
